<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\CpiaAssessment;
use App\Models\DocumentSignature;
use App\Models\Pro_QaInspection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SignatureController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ── Dedicated signature page ────────────────────────────────────────────────

    /**
     * Show the signature page for a given document.
     * URL: GET /sign/{documentType}/{documentId}
     */
    public function showPage(string $documentType, int $documentId)
    {
        $allowed = ['qa_unit_report', 'cpia_assessment'];
        abort_if(!in_array($documentType, $allowed), 404);

        $docInfo       = $this->loadDocumentInfo($documentType, $documentId);
        $signatures    = DocumentSignature::getForDocument($documentType, $documentId)->keyBy('role_in_document');
        $requiredRoles = $this->requiredRoles($documentType);
        $user          = Auth::user();
        $userRole      = $this->resolveUserRole($documentType, $documentId, $user);

        // super_admin can sign as any role not yet signed
        $canChooseRole = ($user->role === 'super_admin') && $userRole === null;
        $availableRoles = $canChooseRole
            ? array_values(array_filter($requiredRoles, fn($r) => !$signatures->has($r)))
            : [];

        // If super_admin has already signed in one role, detect it
        if ($user->role === 'super_admin') {
            foreach ($requiredRoles as $r) {
                $sig = $signatures->get($r);
                if ($sig && $sig->user_id === $user->id) {
                    $userRole = $r;
                    break;
                }
            }
        }

        $alreadySigned = $userRole && $signatures->has($userRole);

        return view('sign.document', compact(
            'documentType', 'documentId', 'docInfo', 'signatures',
            'requiredRoles', 'userRole', 'alreadySigned', 'canChooseRole', 'availableRoles'
        ));
    }

    // ── AJAX endpoints ──────────────────────────────────────────────────────────

    /** Return signatures for a document */
    public function getSignatures(Request $request)
    {
        $request->validate([
            'document_type' => 'required|string',
            'document_id'   => 'required|integer',
        ]);

        $sigs = DocumentSignature::getForDocument(
            $request->document_type,
            $request->document_id
        )->map(fn($s) => [
            'id'               => $s->id,
            'signer_name'      => $s->signer_name,
            'role_in_document' => $s->role_in_document,
            'signed_at'        => $s->signed_at?->format('d/m/Y H:i'),
            'signature_data'   => $s->signature_data,
        ]);

        return response()->json(['signatures' => $sigs]);
    }

    /** Save a new signature */
    public function save(Request $request)
    {
        $v = Validator::make($request->all(), [
            'document_type'    => 'required|string|max:60',
            'document_id'      => 'required|integer',
            'role_in_document' => 'required|string|max:60',
            'signature_data'   => 'required|string',  // base64 PNG
            'signer_name'      => 'nullable|string|max:255',
        ]);

        if ($v->fails()) {
            return response()->json(['success' => false, 'message' => implode(', ', $v->errors()->all())], 422);
        }

        $user       = auth()->user();
        $signerName = $request->signer_name
            ?: ($user->personnel ? trim($user->personnel->prenom . ' ' . $user->personnel->nom) : $user->name);

        // Prevent double-signing same role
        if (DocumentSignature::hasRole($request->document_type, $request->document_id, $request->role_in_document)) {
            return response()->json(['success' => false, 'message' => 'This role has already signed this document.'], 409);
        }

        $sig = DocumentSignature::create([
            'user_id'          => $user->id,
            'signer_name'      => $signerName,
            'document_type'    => $request->document_type,
            'document_id'      => $request->document_id,
            'role_in_document' => $request->role_in_document,
            'signature_data'   => $request->signature_data,
            'ip_address'       => $request->ip(),
            'signed_at'        => now(),
        ]);

        // Trigger next-step notifications if applicable
        $this->triggerPostSignatureNotifications($request->document_type, $request->document_id, $request->role_in_document);

        return response()->json([
            'success'     => true,
            'message'     => 'Document signed successfully.',
            'signed_at'   => $sig->signed_at->format('d/m/Y H:i'),
            'signer_name' => $sig->signer_name,
        ]);
    }

    // ── Helpers ─────────────────────────────────────────────────────────────────

    private function loadDocumentInfo(string $documentType, int $documentId): array
    {
        if ($documentType === 'qa_unit_report') {
            $inspection = Pro_QaInspection::with(['project', 'inspector'])->findOrFail($documentId);
            return [
                'title'    => 'QA Unit Report',
                'subtitle' => ($inspection->type_inspection ?? 'Inspection')
                              . ' — '
                              . ($inspection->project?->project_code ?? ''),
                'date'     => $inspection->date_performed
                                  ? \Carbon\Carbon::parse($inspection->date_performed)->format('d/m/Y')
                                  : null,
                'view_url' => route('checklist.report', $documentId),
                'model'    => $inspection,
            ];
        }

        if ($documentType === 'cpia_assessment') {
            $assessment = CpiaAssessment::with(['project'])->findOrFail($documentId);
            return [
                'title'    => 'Critical Phase Impact Assessment',
                'subtitle' => 'Project: ' . ($assessment->project?->project_code ?? ''),
                'date'     => $assessment->completed_at?->format('d/m/Y'),
                'view_url' => route('cpia.print', $assessment->project_id),
                'model'    => $assessment,
            ];
        }

        abort(404);
    }

    private function requiredRoles(string $documentType): array
    {
        return match ($documentType) {
            'qa_unit_report'  => ['study_director', 'qa_inspector', 'qa_manager', 'facility_manager'],
            'cpia_assessment' => ['qa_manager', 'study_director', 'facility_manager'],
            default           => [],
        };
    }

    /**
     * Determine what role_in_document the currently authenticated user holds
     * for the given document. Returns null if the user has no signing role.
     */
    private function resolveUserRole(string $documentType, int $documentId, User $user): ?string
    {
        // For inspections: check if this user is the assigned QA Inspector (via personnel link)
        if ($documentType === 'qa_unit_report') {
            $inspection = Pro_QaInspection::with('inspector')->find($documentId);
            if ($inspection && $inspection->inspector?->user_id === $user->id) {
                return 'qa_inspector';
            }
        }

        return match ($user->role) {
            'qa_manager'       => 'qa_manager',
            'facility_manager' => 'facility_manager',
            'study_director'   => 'study_director',
            default            => null,
        };
    }

    private function triggerPostSignatureNotifications(string $type, int $documentId, string $role): void
    {
        if ($type !== 'qa_unit_report') {
            return;
        }

        $inspection = Pro_QaInspection::find($documentId);
        if (!$inspection) {
            return;
        }

        $project  = $inspection->project;
        $signUrl  = route('sign.document', [$type, $documentId]);

        // After SD signs → notify QA Manager
        if ($role === 'study_director') {
            $qaManagers = User::where('role', 'qa_manager')->get();
            foreach ($qaManagers as $u) {
                AppNotification::send(
                    $u->id,
                    'signature_requested',
                    'QA Unit Report ready for your signature',
                    "The Study Director has signed the QA Unit Report for project {$project?->project_code}. Your signature is required.",
                    $signUrl,
                    'bi-pen-fill'
                );
            }
        }

        // After QA Manager signs → notify Facility Manager
        if ($role === 'qa_manager') {
            $fms = User::where('role', 'facility_manager')->get();
            foreach ($fms as $u) {
                AppNotification::send(
                    $u->id,
                    'report_signed',
                    'QA Unit Report fully signed',
                    "The QA Unit Report for project {$project?->project_code} has been signed by both the Study Director and QA Manager.",
                    $signUrl,
                    'bi-check2-circle'
                );
            }
        }
    }
}

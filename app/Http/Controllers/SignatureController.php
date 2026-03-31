<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\DocumentSignature;
use App\Models\Pro_QaInspection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SignatureController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

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

        $user  = auth()->user();
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

    private function triggerPostSignatureNotifications(string $type, int $documentId, string $role): void
    {
        if ($type !== 'qa_unit_report') {
            return;
        }

        $inspection = Pro_QaInspection::find($documentId);
        if (!$inspection) {
            return;
        }

        $project = $inspection->project;

        // After SD signs → notify QA Manager
        if ($role === 'study_director') {
            $qaManagers = User::where('role', 'qa_manager')->get();
            foreach ($qaManagers as $u) {
                AppNotification::send(
                    $u->id,
                    'signature_requested',
                    'QA Unit Report ready for your signature',
                    "The Study Director has signed the QA Unit Report for project {$project?->project_code}. Your signature is required.",
                    route('checklist.report', $inspection->id),
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
                    route('checklist.report', $inspection->id),
                    'bi-check2-circle'
                );
            }
        }
    }
}

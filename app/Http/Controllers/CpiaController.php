<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\CpiaAssessment;
use App\Models\CpiaItem;
use App\Models\CpiaResponse;
use App\Models\CpiaSection;
use App\Models\Pro_KeyFacilityPersonnel;
use App\Models\Pro_Personnel;
use App\Models\Pro_Project;
use App\Models\Pro_StudyDirectorAppointmentForm;
use App\Models\Pro_StudyQualityAssuranceMeeting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CpiaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the CPIA form for a project.
     * The QA Manager selects which sections to fill and assigns impact scores.
     */
    public function index(int $project_id)
    {
        $project = Pro_Project::with([
            'studyDirectorAppointmentForm.studyDirector',
        ])->findOrFail($project_id);

        // Verify prerequisites: meeting scheduled + at least 1 critical phase
        $meeting = Pro_StudyQualityAssuranceMeeting::where('project_id', $project_id)
            ->where('meeting_type', 'study_initiation_meeting')
            ->first();
        abort_unless($meeting, 403, 'The Study Initiation Meeting must be scheduled first.');

        $criticalCount = $project->allActivitiesProject()
            ->where('phase_critique', true)->count();
        abort_unless($criticalCount > 0, 403, 'At least one critical phase must be identified first.');

        // Load or create assessment
        $assessment = CpiaAssessment::firstOrCreate(
            ['project_id' => $project_id],
            [
                'project_code'       => $project->project_code,
                'study_director_name' => $this->sdName($project),
                'study_title'        => $project->project_title,
                'created_by'         => Auth::id(),
            ]
        );

        // All active sections with their items
        $sections = CpiaSection::where('is_active', true)
            ->orderBy('sort_order')
            ->with(['activeItems'])
            ->get();

        // Existing responses keyed by item_id
        $responses = CpiaResponse::where('assessment_id', $assessment->id)
            ->get()
            ->keyBy('item_id');

        // Which sections have any filled response (for progress indicator)
        $filledSectionIds = $assessment->filledSectionIds();

        return view('cpia.index', compact(
            'project', 'assessment', 'sections', 'responses', 'filledSectionIds'
        ));
    }

    /**
     * Save responses for one or all sections (AJAX POST).
     */
    public function save(Request $request, int $project_id)
    {
        $project    = Pro_Project::findOrFail($project_id);
        $assessment = CpiaAssessment::where('project_id', $project_id)->firstOrFail();

        // Block edits if assessment is completed
        if ($assessment->isCompleted()) {
            return response()->json(['success' => false, 'message' => 'Assessment is completed and locked. Revert to draft first.'], 403);
        }

        $request->validate([
            'responses'              => 'required|array',
            'responses.*.item_id'    => 'required|integer|exists:pro_cpia_items,id',
            'responses.*.impact_score' => 'nullable|integer|min:0|max:10',
            'responses.*.is_selected' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($request, $assessment) {
            foreach ($request->responses as $row) {
                $item = CpiaItem::find($row['item_id']);
                if (!$item) continue;

                CpiaResponse::updateOrCreate(
                    [
                        'assessment_id' => $assessment->id,
                        'item_id'       => $row['item_id'],
                    ],
                    [
                        'section_id'         => $item->section_id,
                        'impact_score'       => isset($row['impact_score']) && $row['impact_score'] !== '' ? (int)$row['impact_score'] : null,
                        'is_selected'        => (bool)($row['is_selected'] ?? false),
                        'item_text_snapshot' => $item->text,
                        'created_by'         => Auth::id(),
                    ]
                );
            }
        });

        // Update header fields if sent
        $update = [];
        if ($request->filled('study_director_name')) $update['study_director_name'] = $request->study_director_name;
        if ($request->filled('study_title'))         $update['study_title']          = $request->study_title;
        if (!empty($update)) {
            $update['updated_by'] = Auth::id();
            $assessment->update($update);
        }

        return response()->json(['success' => true, 'message' => 'Assessment saved.']);
    }

    /**
     * Mark the assessment as completed and notify signatories.
     */
    public function complete(Request $request, int $project_id)
    {
        $project    = Pro_Project::findOrFail($project_id);
        $assessment = CpiaAssessment::where('project_id', $project_id)->firstOrFail();

        if ($assessment->isCompleted()) {
            return response()->json(['success' => false, 'message' => 'Assessment is already marked as completed.'], 409);
        }

        $filledCount = CpiaResponse::where('assessment_id', $assessment->id)
            ->whereNotNull('impact_score')->count();

        if ($filledCount === 0) {
            return response()->json(['success' => false, 'message' => 'Please fill at least one section before marking as completed.'], 422);
        }

        $assessment->update([
            'status'       => 'completed',
            'completed_at' => now(),
            'completed_by' => Auth::id(),
        ]);

        $this->notifySignatories($project, $assessment);

        return response()->json([
            'success'  => true,
            'message'  => 'Assessment marked as completed. Signatories have been notified.',
        ]);
    }

    /**
     * Revert the assessment back to draft so it can be edited again.
     * Only allowed if the project is not archived.
     */
    public function revertToDraft(Request $request, int $project_id)
    {
        $project    = Pro_Project::findOrFail($project_id);
        $assessment = CpiaAssessment::where('project_id', $project_id)->firstOrFail();

        if ($project->archived_at) {
            return response()->json(['success' => false, 'message' => 'Project is archived. The assessment cannot be modified.'], 403);
        }

        if (!$assessment->isCompleted()) {
            return response()->json(['success' => false, 'message' => 'Assessment is already in draft.'], 409);
        }

        $assessment->update([
            'status'       => 'draft',
            'completed_at' => null,
            'completed_by' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Assessment reverted to draft. You can now edit it.',
        ]);
    }

    /**
     * Print the CPIA — only sections that have at least one filled response.
     */
    public function print(int $project_id)
    {
        $project    = Pro_Project::with('studyDirectorAppointmentForm.studyDirector')->findOrFail($project_id);
        $assessment = CpiaAssessment::where('project_id', $project_id)->firstOrFail();

        // Only sections with filled responses
        $filledSectionIds = $assessment->filledSectionIds();
        abort_if(empty($filledSectionIds), 404, 'No sections have been filled yet.');

        $sections = CpiaSection::whereIn('id', $filledSectionIds)
            ->orderBy('sort_order')
            ->with(['activeItems'])
            ->get();

        $responses = CpiaResponse::where('assessment_id', $assessment->id)
            ->get()
            ->keyBy('item_id');

        $keyPersonnels = $this->keyPersonnels();

        $signatures = \App\Models\DocumentSignature::getForDocument('cpia_assessment', $assessment->id)
            ->keyBy('role_in_document');

        return view('cpia.print', compact(
            'project', 'assessment', 'sections', 'responses', 'keyPersonnels', 'signatures'
        ));
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    private function sdName(Pro_Project $project): string
    {
        $appt = $project->studyDirectorAppointmentForm;
        $sd   = $appt?->studyDirector;
        if (!$sd) return '';
        return trim(($sd->titre_personnel ?? '') . ' ' . $sd->prenom . ' ' . $sd->nom);
    }

    private function notifySignatories(Pro_Project $project, CpiaAssessment $assessment): void
    {
        $printUrl = route('sign.document', ['cpia_assessment', $assessment->id]);
        $title    = "CPIA ready for signature — {$project->project_code}";
        $body     = "The Critical Phase Impact Assessment for project \"{$project->project_title}\" has been completed and requires your signature.";

        $notifiedUserIds = [];

        // QA Managers
        User::where('role', 'qa_manager')->each(function ($u) use ($title, $body, $printUrl, &$notifiedUserIds) {
            AppNotification::send($u->id, 'signature_requested', $title, $body, $printUrl, 'bi-clipboard2-pulse');
            $notifiedUserIds[] = $u->id;
        });

        // Facility Managers
        User::where('role', 'facility_manager')->each(function ($u) use ($title, $body, $printUrl, &$notifiedUserIds) {
            if (!in_array($u->id, $notifiedUserIds)) {
                AppNotification::send($u->id, 'signature_requested', $title, $body, $printUrl, 'bi-clipboard2-pulse');
                $notifiedUserIds[] = $u->id;
            }
        });

        // Study Director (via appointment form)
        $appt = $project->studyDirectorAppointmentForm;
        if ($appt && $appt->studyDirector?->user_id) {
            $sdUserId = $appt->studyDirector->user_id;
            if (!in_array($sdUserId, $notifiedUserIds)) {
                AppNotification::send($sdUserId, 'signature_requested', $title, $body, $printUrl, 'bi-clipboard2-pulse');
            }
        }
    }

    private function keyPersonnels(): array
    {
        $rows   = Pro_KeyFacilityPersonnel::where('active', 1)->get();
        $result = [];
        foreach ($rows as $row) {
            $person = Pro_Personnel::find($row->personnel_id);
            if ($person) {
                $key          = strtolower(str_replace(' ', '_', $row->staff_role));
                $result[$key] = $person;
            }
        }
        return $result;
    }
}

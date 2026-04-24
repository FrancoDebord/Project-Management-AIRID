<?php

namespace App\Http\Controllers;

use App\Models\CpiaAssessment;
use App\Models\Pro_KeyFacilityPersonnel;
use App\Models\Pro_Project;
use App\Models\Pro_QaInspection;
use App\Models\Pro_QaInspectionFinding;
use App\Models\Pro_StudyActivities;
use App\Models\Pro_StudyQualityAssuranceMeeting;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FrontendController extends Controller
{
    function __construct() {
        $this->middleware('auth');
    }

    function indexPage(Request $request)
    {
        // ── Stage filter from query string ─────────────────────────────
        $stageFilter = $request->get('stage', 'all');
        $dbStage = match($stageFilter) {
            'in_progress' => 'in progress',
            'not_started' => 'not_started',
            'suspended'   => 'suspended',
            'completed'   => 'completed',
            'archived'    => 'archived',
            default       => null,
        };

        // ── Restrict visible projects based on role ────────────────────
        $user        = auth()->user();
        $personnelId = $user->personnel?->id;

        $baseQuery = Pro_Project::query();

        if ($user->hasRole('study_director') && $personnelId) {
            // Only projects where this person is the active study director
            $baseQuery->whereHas('studyDirectorAppointmentForm', function ($q) use ($personnelId) {
                $q->where('study_director', $personnelId);
            });
        } elseif ($user->hasRole('project_manager') && $personnelId) {
            // Only projects where this person is in the project team
            $baseQuery->whereHas('keyPersonnelProject', function ($q) use ($personnelId) {
                $q->where('personnels.id', $personnelId);
            });
        }
        // All other roles see all projects

        // ── KPIs — always across ALL projects ──────────────────────────
        $totalProjects = (clone $baseQuery)->count();
        $kpiInProgress = (clone $baseQuery)->where('project_stage', 'in progress')->count();

        $glpProjectIds = (clone $baseQuery)->where('is_glp', true)->pluck('id')->toArray();

        // Open NCs with project detail
        $openNcRows = empty($glpProjectIds) ? collect() : DB::table('pro_qa_inspections_findings as f')
            ->join('pro_projects as p', 'f.project_id', '=', 'p.id')
            ->whereIn('f.project_id', $glpProjectIds)
            ->where('f.is_conformity', 0)
            ->where('f.status', '!=', 'complete')
            ->select('p.id as project_id', 'p.project_code', 'p.project_title', DB::raw('COUNT(*) as nc_count'))
            ->groupBy('p.id', 'p.project_code', 'p.project_title')
            ->orderByDesc('nc_count')
            ->get();
        $kpiOpenNc = $openNcRows->sum('nc_count');

        // Pending inspections with project detail
        $pendingInspectionRows = empty($glpProjectIds) ? collect() : DB::table('pro_qa_inspections as qi')
            ->join('pro_projects as p', 'qi.project_id', '=', 'p.id')
            ->whereIn('qi.project_id', $glpProjectIds)
            ->whereNull('qi.date_performed')
            ->select('p.id as project_id', 'p.project_code', 'p.project_title', DB::raw('COUNT(*) as insp_count'))
            ->groupBy('p.id', 'p.project_code', 'p.project_title')
            ->orderByDesc('insp_count')
            ->get();
        $kpiPendingInspections = $pendingInspectionRows->sum('insp_count');

        // Attach Study Directors to each detail row
        $allProjectsForKpi = empty($glpProjectIds) ? collect() : Pro_Project::with([
            'studyDirectorAppointmentForm.studyDirector',
            'projectManager',
        ])->whereIn('id', $glpProjectIds)->get()->keyBy('id');

        foreach ($openNcRows as $row) {
            $proj = $allProjectsForKpi->get($row->project_id);
            $sd   = $proj?->studyDirectorAppointmentForm?->studyDirector;
            $row->sd_name = $sd ? trim(($sd->titre_personnel ?? '') . ' ' . $sd->prenom . ' ' . $sd->nom) : '—';
            $row->pm_name = $proj?->projectManager
                ? trim(($proj->projectManager->titre_personnel ?? '') . ' ' . $proj->projectManager->prenom . ' ' . $proj->projectManager->nom)
                : '—';
        }
        foreach ($pendingInspectionRows as $row) {
            $proj = $allProjectsForKpi->get($row->project_id);
            $sd   = $proj?->studyDirectorAppointmentForm?->studyDirector;
            $row->sd_name = $sd ? trim(($sd->titre_personnel ?? '') . ' ' . $sd->prenom . ' ' . $sd->nom) : '—';
            $row->pm_name = $proj?->projectManager
                ? trim(($proj->projectManager->titre_personnel ?? '') . ' ' . $proj->projectManager->prenom . ' ' . $proj->projectManager->nom)
                : '—';
        }

        $kpiByStage = [
            'in_progress' => $kpiInProgress,
            'not_started' => (clone $baseQuery)->where('project_stage', 'not_started')->count(),
            'suspended'   => (clone $baseQuery)->where('project_stage', 'suspended')->count(),
            'completed'   => (clone $baseQuery)->where('project_stage', 'completed')->count(),
            'archived'    => (clone $baseQuery)->where('project_stage', 'archived')->count(),
        ];

        // Avg completion needs scores for visible projects
        $allProjects   = (clone $baseQuery)->orderBy("date_debut_effective", "desc")->get();
        $allScores     = $this->computeProjectScores($allProjects);
        $projectsNeedingInspection = $allScores['needingInspection'];
        $allScores     = $allScores['scores'];

        $kpiAvgCompletion = $totalProjects > 0
            ? (int) round(collect($allScores)->avg('overall'))
            : 0;

        // ── Paginated projects for display (6 per page) ────────────────
        $search = trim($request->get('q', ''));
        $query = (clone $baseQuery)->orderBy("date_debut_effective", "desc");
        if ($dbStage) {
            $query->where('project_stage', $dbStage);
        }
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('project_code',  'like', '%' . $search . '%')
                  ->orWhere('project_title', 'like', '%' . $search . '%');
            });
        }
        $all_projects  = $query->paginate(12)->withQueryString();
        $projectScores = $allScores; // scores pre-computed for all, view filters by id

        return view("accueil", compact(
            "all_projects", "totalProjects", "stageFilter", "search",
            "kpiInProgress", "kpiOpenNc", "kpiPendingInspections", "kpiAvgCompletion", "kpiByStage",
            'projectsNeedingInspection', 'projectScores',
            'openNcRows', 'pendingInspectionRows'
        ));
    }

    /**
     * Detailed overview page for a single project
     */
    public function projectOverview(int $id)
    {
        $project = Pro_Project::with([
            'studyDirector',
            'projectManager',
            'studyDirectorAppointmentForm.studyDirector',
            'keyPersonnelProject',
            'studyTypesApplied',
            'productTypesEvaluated',
            'labTestsConcerned',
            'protocolDeveloppementActivitiesProject.protocolDevDocuments',
            'protocolDeveloppementActivitiesProject.protocolDevActivity',
            'otherBasicDocuments',
            'allActivitiesProject.category',
            'allActivitiesProject.personneResponsable',
            'allActivitiesProject.executedBy',
            'reportPhaseDocuments',
            'archivingDocuments',
            'dmDatabases',
            'dmPcAssignments',
            'dmSoftwareValidations.files',
            'dmDataloggerValidations.files',
            'dmDoubleEntries.database',
        ])->findOrFail($id);

        // QA Inspections for this project with findings
        $inspections = Pro_QaInspection::with(['inspector', 'findings.assignedTo'])
            ->where('project_id', $id)
            ->orderBy('date_scheduled')
            ->get();

        // QA Statement
        $qaStatement = \App\Models\Pro_QaStatement::where('project_id', $id)->first();

        // ── Document download permissions ──────────────────────────────
        $user        = auth()->user();
        $personnelId = $user->personnel?->id;
        $sdForm      = $project->studyDirectorAppointmentForm;
        $isProjectSd = $personnelId && $sdForm && (int)$sdForm->study_director === (int)$personnelId;

        $canDownloadAll     = $user->hasRole(['super_admin', 'facility_manager']);
        $canDownloadQA      = $user->hasRole(['super_admin', 'facility_manager', 'qa_manager']);
        $canDownloadProject = $canDownloadAll || ($user->hasRole('study_director') && $isProjectSd);

        // IDs of critical activities that have a done inspection
        $inspectedCriticalIds = Pro_QaInspection::whereNotNull('activity_id')
            ->whereNotNull('date_performed')
            ->pluck('activity_id')
            ->toArray();

        // Activities grouped by category
        $activitiesByCategory = $project->allActivitiesProject
            ->groupBy(fn($a) => $a->category->name ?? 'Uncategorized');

        // Score for this project
        $scores = $this->computeProjectScores(collect([$project]));
        $score  = $scores['scores'][$id] ?? [
            'overall' => 0, 'actScore' => 0, 'critScore' => 0,
            'findScore' => 0, 'reportScore' => 0, 'archiveScore' => 0,
        ];

        // Unresolved findings across all project inspections
        $allFindings = $inspections->flatMap(fn($i) => $i->findings)
            ->where('is_conformity', 0);

        return view('project-overview', compact(
            'project', 'inspections', 'inspectedCriticalIds',
            'activitiesByCategory', 'score', 'allFindings',
            'qaStatement', 'canDownloadAll', 'canDownloadQA', 'canDownloadProject'
        ));
    }

    public function qaActivitiesChecklist(int $id)
    {
        $project = Pro_Project::with([
            'studyDirectorAppointmentForm.studyDirector',
            'protocolDeveloppementActivitiesProject.protocolDevDocuments',
            'reportPhaseDocuments',
            'archivingDocuments',
        ])->findOrFail($id);

        abort_unless($project->is_glp, 403, 'Not a GLP project.');

        $sdForm  = $project->studyDirectorAppointmentForm;
        $sd      = $sdForm?->studyDirector;
        $sdName  = $sd ? trim(($sd->titre_personnel ?? '') . ' ' . $sd->prenom . ' ' . $sd->nom) : '';

        $saved      = \App\Models\Pro_QaActivitiesChecklist::where('project_id', $id)->get()->keyBy('item_number');
        $activities = \App\Models\Pro_QaActivitiesChecklist::activities();
        $prefill    = $this->qaChecklistPrefill($project);
        $isQaMgr    = auth()->user()->hasRole(['super_admin', 'facility_manager', 'qa_manager']);
        $headerImagePath = public_path('storage/assets/logo/airid.jpg');
        $globalSettings  = \App\Models\AppSetting::allAsMap();

        return view('qa-activities-checklist', compact(
            'project', 'sdName', 'saved', 'activities', 'prefill', 'isQaMgr',
            'headerImagePath', 'globalSettings'
        ));
    }

    private function qaChecklistPrefill(Pro_Project $project): array
    {
        $pf = [];

        $firstProtoDoc = $project->protocolDeveloppementActivitiesProject
            ->flatMap(fn($a) => $a->protocolDevDocuments ?? collect())
            ->sortBy('date_upload')->first();
        if ($firstProtoDoc?->date_upload) {
            $pf[1] = ['date' => $firstProtoDoc->date_upload, 'mov' => 'Protocol development document'];
        }

        $level3 = $project->protocolDeveloppementActivitiesProject->firstWhere('level_activite', 3);
        if ($level3?->real_date_performed) {
            $pf[5] = ['date' => $level3->real_date_performed, 'mov' => 'Signed protocol (level 3)'];
        }

        $protoInsp = Pro_QaInspection::where('project_id', $project->id)
            ->where('type_inspection', 'Study Inspection')
            ->orderBy('date_performed')->first();
        if ($protoInsp?->date_performed) {
            $pf[2] = ['date' => $protoInsp->date_performed, 'mov' => $protoInsp->inspection_name ?? 'Study Inspection'];
        }

        $firstInsp = Pro_QaInspection::where('project_id', $project->id)
            ->orderBy('date_scheduled')->first();
        if ($firstInsp?->date_scheduled) {
            $pf[7] = ['date' => $firstInsp->date_scheduled, 'mov' => 'First QA inspection scheduled'];
        }

        $critInsp = Pro_QaInspection::where('project_id', $project->id)
            ->where('type_inspection', 'Critical Phase Inspection')
            ->whereNotNull('date_performed')
            ->orderByDesc('date_performed')->first();
        if ($critInsp?->date_performed) {
            $pf[8] = ['date' => $critInsp->date_performed, 'mov' => 'Critical Phase Inspection completed'];
        }

        $dqInsp = Pro_QaInspection::where('project_id', $project->id)
            ->where('type_inspection', 'Data Quality Inspection')
            ->whereNotNull('date_performed')
            ->orderByDesc('date_performed')->first();
        if ($dqInsp?->date_performed) {
            $pf[9] = ['date' => $dqInsp->date_performed, 'mov' => 'Data Quality Inspection completed'];
        }

        $draftReport = $project->reportPhaseDocuments
            ->where('document_type', 'draft_report')
            ->whereNotNull('submission_date')
            ->sortByDesc('submission_date')->first();
        if ($draftReport?->submission_date) {
            $pf[13] = ['date' => $draftReport->submission_date, 'mov' => 'Draft study report – ' . ($draftReport->title ?? '')];
        }

        $finalReport = $project->reportPhaseDocuments
            ->where('document_type', 'final_report')
            ->whereNotNull('submission_date')
            ->sortByDesc('submission_date')->first();
        if ($finalReport?->submission_date) {
            $pf[16] = ['date' => $finalReport->submission_date, 'mov' => 'Final study report – ' . ($finalReport->title ?? '')];
        }

        $qaStatement = \App\Models\Pro_QaStatement::where('project_id', $project->id)
            ->whereNotNull('date_signed')->first();
        if ($qaStatement?->date_signed) {
            $pf[19] = ['date' => $qaStatement->date_signed, 'mov' => 'QA Statement – ' . ($qaStatement->report_number ?? '')];
        }

        $archDoc = $project->archivingDocuments
            ->whereNotNull('date_submitted')
            ->sortBy('date_submitted')->first();
        if ($archDoc?->date_submitted) {
            $pf[20] = ['date' => $archDoc->date_submitted, 'mov' => 'Archiving document submitted'];
        }

        return $pf;
    }

    // ─────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────

    private function computeProjectScores($projects): array
    {
        $ids = $projects->pluck('id')->toArray();
        if (empty($ids)) {
            return ['scores' => [], 'needingInspection' => []];
        }

        // 1. Activities per project
        $actStats = DB::table('pro_studies_activities')
            ->select('project_id',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status='completed' THEN 1 ELSE 0 END) as completed"))
            ->whereIn('project_id', $ids)
            ->groupBy('project_id')
            ->get()->keyBy('project_id');

        // 2. QA Inspections (done = date_performed not null)
        $inspStats = DB::table('pro_qa_inspections')
            ->select('project_id',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN date_performed IS NOT NULL THEN 1 ELSE 0 END) as completed'))
            ->whereIn('project_id', $ids)
            ->groupBy('project_id')
            ->get()->keyBy('project_id');

        // 3. NC Findings (is_conformity=0, resolved = status='complete')
        $findStats = DB::table('pro_qa_inspections_findings')
            ->select('project_id',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status='complete' THEN 1 ELSE 0 END) as resolved"))
            ->where('is_conformity', 0)
            ->whereIn('project_id', $ids)
            ->groupBy('project_id')
            ->get()->keyBy('project_id');

        // 4. Protocol Dev documents (applicable, complete=true; level 5 = Amendment/Deviation is optional, excluded like ProjectController)
        $protoStats = DB::table('pro_protocols_devs_activities_projects')
            ->select('project_id',
                DB::raw('SUM(CASE WHEN applicable=1 AND level_activite <> 5 THEN 1 ELSE 0 END) as total'),
                DB::raw('SUM(CASE WHEN applicable=1 AND level_activite <> 5 AND complete=1 THEN 1 ELSE 0 END) as completed'))
            ->whereIn('project_id', $ids)
            ->groupBy('project_id')
            ->get()->keyBy('project_id');

        // 5. Report milestone (≥1 submitted or published doc)
        $reportDoneIds = DB::table('pro_report_phase_documents')
            ->whereIn('project_id', $ids)
            ->whereIn('status', ['submitted', 'published'])
            ->pluck('project_id')->unique()->toArray();

        // "Needing inspection" alert: critical activity completed but not yet inspected (GLP projects only)
        $glpIds = $projects->where('is_glp', true)->pluck('id')->toArray();
        $criticalRows = empty($glpIds) ? collect() : DB::table('pro_studies_activities')
            ->select('id', 'project_id', 'status')
            ->where('phase_critique', 1)
            ->whereIn('project_id', $glpIds)
            ->get();

        $inspectedActivityIds = $criticalRows->isEmpty() ? [] : DB::table('pro_qa_inspections')
            ->whereNotNull('activity_id')
            ->whereNotNull('date_performed')
            ->pluck('activity_id')
            ->toArray();

        $needingInspection = $criticalRows
            ->where('status', 'completed')
            ->whereNotIn('id', $inspectedActivityIds)
            ->pluck('project_id')->unique()->toArray();

        $scores = [];
        foreach ($projects as $p) {
            $pid             = $p->id;
            $phasesCompleted = $p->phases_completed ?? [];

            $acts      = $actStats[$pid]  ?? null;
            $totalAct  = $acts  ? (int) $acts->total     : 0;
            $doneAct   = $acts  ? (int) $acts->completed : 0;

            // Inspections and NC findings only count for GLP projects
            $insps     = $p->is_glp ? ($inspStats[$pid] ?? null) : null;
            $totalInsp = $insps ? (int) $insps->total     : 0;
            $doneInsp  = $insps ? (int) $insps->completed : 0;

            $finds     = $p->is_glp ? ($findStats[$pid] ?? null) : null;
            $totalNc   = $finds ? (int) $finds->total    : 0;
            $doneNc    = $finds ? (int) $finds->resolved : 0;

            $proto      = $protoStats[$pid] ?? null;
            $totalProto = $proto ? (int) $proto->total     : 0;
            $doneProto  = $proto ? (int) $proto->completed : 0;

            $reportMilestone  = in_array($pid, $reportDoneIds) ? 1 : 0;
            $archiveMilestone = ($p->archived_at || in_array('archiving', $phasesCompleted)) ? 1 : 0;

            // Equal-weight per-item overall (same algorithm as ProjectController)
            $totalItems     = $totalAct + $totalProto + $totalInsp + $totalNc + 1 + 1;
            $completedItems = $doneAct  + $doneProto  + $doneInsp  + $doneNc  + $reportMilestone + $archiveMilestone;
            $overall = $totalItems > 0 ? (int) round($completedItems / $totalItems * 100) : 0;

            // Per-dimension % kept for backward compat (project-overview.blade.php)
            $actScore    = $totalAct  > 0 ? (int) round($doneAct  / $totalAct  * 100) : 100;
            $critScore   = $totalInsp > 0 ? (int) round($doneInsp / $totalInsp * 100) : 100;
            $findScore   = $totalNc   > 0 ? (int) round($doneNc   / $totalNc   * 100) : 100;
            $reportScore  = $reportMilestone  * 100;
            $archiveScore = $archiveMilestone * 100;

            $protoScore  = $totalProto > 0 ? (int) round($doneProto / $totalProto * 100) : 100;

            $scores[$pid] = compact(
                'overall',
                'actScore', 'critScore', 'findScore', 'reportScore', 'archiveScore', 'protoScore',
                'totalAct', 'doneAct', 'totalInsp', 'doneInsp',
                'totalNc',  'doneNc',  'totalProto', 'doneProto', 'reportMilestone', 'archiveMilestone'
            ) + ['phasesCount' => count($phasesCompleted)];
        }

        return ['scores' => $scores, 'needingInspection' => $needingInspection];
    }

    public function projectsList(Request $request)
    {
        $search = $request->input('search', '');
        $status = $request->input('status', '');

        $query = Pro_Project::with([
            'studyDirectorAppointmentForm.studyDirector',
            'studyDirectorReplacementHistory',
        ]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('project_code',  'like', "%{$search}%")
                  ->orWhere('project_title','like', "%{$search}%")
                  ->orWhere('protocol_code','like', "%{$search}%")
                  ->orWhere('sponsor_name', 'like', "%{$search}%")
                  ->orWhere('manufacturer_name', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('project_stage', $status);
        }

        $projects = $query->orderBy('created_at', 'desc')->get();

        $statuses = [
            'not_started' => 'Not Started',
            'in progress' => 'In Progress',
            'suspended'   => 'Suspended',
            'completed'   => 'Completed',
            'archived'    => 'Archived',
        ];

        return view('projects-list', compact('projects', 'search', 'status', 'statuses'));
    }

    /**
     * Generate and stream the Projects List as a PDF with AIRID header.
     */
    public function projectsListPdf(Request $request)
    {
        $search = $request->input('search', '');
        $status = $request->input('status', '');

        $query = Pro_Project::with([
            'studyDirectorAppointmentForm.studyDirector',
            'studyDirectorReplacementHistory',
            'projectManager',
        ]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('project_code',      'like', "%{$search}%")
                  ->orWhere('project_title',   'like', "%{$search}%")
                  ->orWhere('protocol_code',   'like', "%{$search}%")
                  ->orWhere('sponsor_name',    'like', "%{$search}%")
                  ->orWhere('manufacturer_name','like',"%{$search}%");
            });
        }

        if ($status) {
            $query->where('project_stage', $status);
        }

        $projects = $query->orderBy('created_at', 'desc')->get();

        $statuses = [
            'not_started' => 'Not Started',
            'in progress' => 'In Progress',
            'suspended'   => 'Suspended',
            'completed'   => 'Completed',
            'archived'    => 'Archived',
        ];

        $pdf = Pdf::loadView('pdf.projects-list', compact('projects', 'search', 'status', 'statuses'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('AIRID-Projects-List-' . now()->format('Ymd') . '.pdf');
    }

    /**
     * Generate and stream the Study Director Appointment Form as PDF.
     */
    public function sdAppointmentFormPdf(Request $request)
    {
        $projectId = $request->get('project_id');
        $project   = Pro_Project::with([
            'studyDirectorAppointmentForm.studyDirector',
        ])->findOrFail($projectId);

        $form = $project->studyDirectorAppointmentForm;
        $sd   = $form?->studyDirector;

        $pdf = Pdf::loadView('pdf.sd-appointment-form', compact('project', 'form', 'sd'))
            ->setPaper('a4', 'portrait');

        $safeCode = str_replace(['/', '\\', ' '], '-', $project->project_code ?? $projectId);
        $filename = 'SD-Appointment-' . $safeCode . '.pdf';

        return $pdf->download($filename);
    }

    // ────────────────────────────────────────────────────────────────────────
    //  MEETING REPORT PDF — Critical Phase Agreement Meeting Minutes
    // ────────────────────────────────────────────────────────────────────────

    public function meetingReportPdf(Request $request)
    {
        $projectId = $request->get('project_id');

        $project = Pro_Project::with([
            'studyDirectorAppointmentForm.studyDirector',
            'projectManager',
            'keyPersonnelProject',
            'studyTypesApplied',
            'allActivitiesProject.personneResponsable',
        ])->findOrFail($projectId);

        // Meeting
        $meeting = Pro_StudyQualityAssuranceMeeting::with('participants')
            ->where('project_id', $projectId)
            ->where('meeting_type', 'study_initiation_meeting')
            ->firstOrFail();

        // Study Director name
        $sda    = $project->studyDirectorAppointmentForm;
        $sd     = $sda?->studyDirector;
        $sdName = $sd
            ? trim(($sd->titre_personnel ?? '') . ' ' . $sd->prenom . ' ' . $sd->nom)
            : '—';

        // SD signed_at (if applicable)
        $sdSignedAt = $sda?->sd_signed_at ?? null;

        // Project Manager name
        $pm     = $project->projectManager;
        $pmName = $pm ? trim($pm->prenom . ' ' . $pm->nom) : null;

        // Sponsor
        $sponsor = $project->sponsor_name ?: null;

        // Key Personnel (load with pivot role if available)
        $keyPersonnel = $project->keyPersonnelProject ?? collect();

        // Participants
        $participants = $meeting->participants ?? collect();

        // Critical phases
        $criticalPhases = $project->allActivitiesProject
            ->where('phase_critique', true)
            ->values();

        // All activities count
        $allActivitiesCount = $project->allActivitiesProject->count();

        // QA inspections keyed by activity_id
        $cpInspections = Pro_QaInspection::with('inspector')
            ->where('project_id', $projectId)
            ->whereNotNull('activity_id')
            ->get()
            ->keyBy('activity_id');

        // Determine QA inspector name from the first relevant inspection
        $qaInspector = $cpInspections->first()?->inspector;
        if (!$qaInspector) {
            // Fall back to first any inspection for this project
            $qaInspector = Pro_QaInspection::where('project_id', $projectId)
                ->with('inspector')
                ->first()?->inspector;
        }
        $qaInspectorName = $qaInspector
            ? trim(($qaInspector->titre_personnel ?? '') . ' ' . $qaInspector->prenom . ' ' . $qaInspector->nom)
            : '—';

        // CPIA assessment
        $cpia = CpiaAssessment::where('project_id', $projectId)->first();

        // Header image
        $headerImagePath = 'file://' . str_replace('\\', '/', public_path('storage/assets/header/entete_airid.png'));

        $pdf = Pdf::loadView('pdf.meeting-report', compact(
            'project', 'meeting', 'sdName', 'sdSignedAt', 'pmName', 'sponsor',
            'keyPersonnel', 'participants', 'criticalPhases', 'allActivitiesCount',
            'cpInspections', 'qaInspectorName', 'cpia', 'headerImagePath'
        ))->setPaper('a4', 'portrait');

        $safeCode = str_replace(['/', '\\', ' '], '-', $project->project_code ?? $projectId);
        $filename  = 'Meeting-Report-' . $safeCode . '.pdf';

        return $pdf->download($filename);
    }
}

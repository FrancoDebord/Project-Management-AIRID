<?php

namespace App\Http\Controllers;

use App\Models\Pro_Project;
use App\Models\Pro_QaInspection;
use App\Models\Pro_QaInspectionFinding;
use App\Models\Pro_StudyActivities;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FrontendController extends Controller
{
    function __construct() {
        $this->middleware('auth');
    }

    function indexPage(Request $request)
    {
        $projectsCount   = Pro_Project::count();
        $activeUsers     = User::where('active', true)->count();
        $totalBudget     = 0;
        $tasksInProgress = Pro_Project::where('project_stage', 'in progress')->count();

        $months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sept', 'Oct', 'Nov', 'Déc'];
        $budgetsByMonth = array_fill(0, 12, 0);

        $all_projects = Pro_Project::orderBy("date_debut_effective", "desc")->get();

        $projectScores           = $this->computeProjectScores($all_projects);
        $projectsNeedingInspection = $projectScores['needingInspection'];
        $projectScores           = $projectScores['scores'];

        return view("accueil", compact(
            "all_projects", 'projectsCount', 'activeUsers', 'totalBudget',
            'tasksInProgress', 'months', 'budgetsByMonth',
            'projectsNeedingInspection', 'projectScores'
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
            'allActivitiesProject.category',
            'allActivitiesProject.personneResponsable',
            'allActivitiesProject.executedBy',
            'reportPhaseDocuments',
            'archivingDocuments',
        ])->findOrFail($id);

        // QA Inspections for this project with findings
        $inspections = Pro_QaInspection::with(['inspector', 'findings.assignedTo'])
            ->where('project_id', $id)
            ->orderBy('date_scheduled')
            ->get();

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
            'activitiesByCategory', 'score', 'allFindings'
        ));
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

        // Activity stats per project
        $actStats = DB::table('pro_studies_activities')
            ->select('project_id',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status='completed' THEN 1 ELSE 0 END) as completed"))
            ->whereIn('project_id', $ids)
            ->groupBy('project_id')
            ->get()->keyBy('project_id');

        // Critical activities per project (id + project_id)
        $criticalRows = DB::table('pro_studies_activities')
            ->select('id', 'project_id', 'status')
            ->where('phase_critique', 1)
            ->whereIn('project_id', $ids)
            ->get();

        // Completed inspections linked to activities
        $inspectedActivityIds = Pro_QaInspection::whereNotNull('activity_id')
            ->whereNotNull('date_performed')
            ->pluck('activity_id')
            ->toArray();

        // Projects needing inspection: critical activity completed but no inspection
        $needingInspection = $criticalRows
            ->where('status', 'completed')
            ->whereNotIn('id', $inspectedActivityIds)
            ->pluck('project_id')->unique()->toArray();

        // Group critical rows by project
        $critByProject = $criticalRows->groupBy('project_id');

        // Finding stats per project (non-conformities)
        $findStats = DB::table('pro_qa_inspections_findings as f')
            ->join('pro_qa_inspections as i', 'i.id', '=', 'f.inspection_id')
            ->select('i.project_id',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN f.status='pending' THEN 1 ELSE 0 END) as pending"))
            ->where('f.is_conformity', 0)
            ->whereIn('i.project_id', $ids)
            ->groupBy('i.project_id')
            ->get()->keyBy('project_id');

        // Projects that have at least one report document
        $withReport = DB::table('pro_report_phase_documents')
            ->whereIn('project_id', $ids)
            ->pluck('project_id')->unique()->toArray();

        $scores = [];
        foreach ($projects as $p) {
            $pid = $p->id;

            $acts       = $actStats[$pid]     ?? null;
            $actScore   = (!$acts || $acts->total == 0) ? 100
                          : (int) round($acts->completed / $acts->total * 100);

            $crits      = $critByProject[$pid] ?? collect();
            $totalCrit  = $crits->count();
            $doneCrit   = $crits->whereIn('id', $inspectedActivityIds)->count();
            $critScore  = $totalCrit == 0 ? 100 : (int) round($doneCrit / $totalCrit * 100);

            $finds      = $findStats[$pid] ?? null;
            $findScore  = (!$finds || $finds->total == 0) ? 100
                          : (int) round(($finds->total - $finds->pending) / $finds->total * 100);

            $reportScore   = in_array($pid, $withReport)  ? 100 : 0;
            $archiveScore  = $p->archived_at              ? 100 : 0;

            $overall = (int) round(
                $actScore    * 0.40 +
                $critScore   * 0.25 +
                $findScore   * 0.20 +
                $reportScore * 0.10 +
                $archiveScore* 0.05
            );

            $scores[$pid] = compact('overall','actScore','critScore','findScore','reportScore','archiveScore');
        }

        return ['scores' => $scores, 'needingInspection' => $needingInspection];
    }
}

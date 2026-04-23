<?php

namespace App\Http\Controllers;

use App\Models\Pro_LabTest;
use App\Models\Pro_Personnel;
use App\Models\Pro_ProductType;
use App\Models\Pro_Project;
use App\Models\Pro_StudyActivities;
use App\Models\Pro_StudyType;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FeaturesController extends Controller
{
    // ────────────────────────────────────────────────────────────────────────
    //  SEARCH ENGINE
    // ────────────────────────────────────────────────────────────────────────

    public function search(Request $request)
    {
        $hasSearch = $request->anyFilled([
            'q', 'status', 'is_glp', 'nature', 'sponsor', 'manufacturer', 'test_system',
            'sd', 'pm', 'kp',
            'study_types', 'lab_tests', 'product_types',
            'year_from', 'year_to', 'date_from', 'date_to',
            'qa_date_from', 'qa_date_to',
            'report_date_from', 'report_date_to',
            'archive_date_from', 'archive_date_to',
            'date_first_entry_from', 'date_first_entry_to',
            'date_second_entry_from', 'date_second_entry_to',
        ]);

        $projects = collect();
        $total    = 0;

        if ($hasSearch) {
            $q = Pro_Project::with([
                'studyDirectorAppointmentForm.studyDirector',
                'projectManager',
                'keyPersonnelProject',
                'studyTypesApplied',
                'productTypesEvaluated',
                'labTestsConcerned',
            ]);

            // ── Keyword ─────────────────────────────────────────────────────
            if ($kw = $request->input('q')) {
                $q->where(function ($sub) use ($kw) {
                    $sub->where('project_code',        'like', "%{$kw}%")
                        ->orWhere('project_title',     'like', "%{$kw}%")
                        ->orWhere('protocol_code',     'like', "%{$kw}%")
                        ->orWhere('sponsor_name',      'like', "%{$kw}%")
                        ->orWhere('manufacturer_name', 'like', "%{$kw}%")
                        ->orWhere('test_system',       'like', "%{$kw}%")
                        ->orWhere('project_nature',    'like', "%{$kw}%")
                        ->orWhere('description_project','like', "%{$kw}%");
                });
            }

            // ── Status ───────────────────────────────────────────────────────
            if ($status = $request->input('status')) {
                $q->where('project_stage', $status);
            }

            // ── GLP ──────────────────────────────────────────────────────────
            if ($request->filled('is_glp') && $request->input('is_glp') !== '') {
                $q->where('is_glp', (bool) $request->input('is_glp'));
            }

            // ── Nature ───────────────────────────────────────────────────────
            if ($nature = $request->input('nature')) {
                $q->where('project_nature', 'like', "%{$nature}%");
            }

            // ── Test system ──────────────────────────────────────────────────
            if ($ts = $request->input('test_system')) {
                $q->where('test_system', 'like', "%{$ts}%");
            }

            // ── Sponsor / Manufacturer ───────────────────────────────────────
            if ($sp = $request->input('sponsor')) {
                $q->where('sponsor_name', 'like', "%{$sp}%");
            }
            if ($mn = $request->input('manufacturer')) {
                $q->where('manufacturer_name', 'like', "%{$mn}%");
            }

            // ── Study Director (by personnel ID) ────────────────────────────
            if ($sdId = $request->input('sd')) {
                $q->whereHas('studyDirectorAppointmentForm', fn($sub) => $sub->where('study_director', $sdId));
            }

            // ── Project Manager (by personnel ID) ───────────────────────────
            if ($pmId = $request->input('pm')) {
                $q->where('project_manager', $pmId);
            }

            // ── Key Personnel (array of personnel IDs) ───────────────────────
            if ($kpIds = $request->input('kp')) {
                $kpIds = array_filter((array) $kpIds);
                if (!empty($kpIds)) {
                    $q->whereHas('keyPersonnelProject', fn($sub) => $sub->whereIn('personnels.id', $kpIds));
                }
            }

            // ── Year range ───────────────────────────────────────────────────
            if ($yFrom = $request->input('year_from')) {
                $q->where(function ($sub) use ($yFrom) {
                    $sub->whereYear('date_debut_previsionnelle', '>=', $yFrom)
                        ->orWhereYear('created_at', '>=', $yFrom);
                });
            }
            if ($yTo = $request->input('year_to')) {
                $q->where(function ($sub) use ($yTo) {
                    $sub->whereYear('date_debut_previsionnelle', '<=', $yTo)
                        ->orWhereYear('created_at', '<=', $yTo);
                });
            }

            // ── Start date range ─────────────────────────────────────────────
            if ($dFrom = $request->input('date_from')) {
                $q->where('date_debut_previsionnelle', '>=', $dFrom);
            }
            if ($dTo = $request->input('date_to')) {
                $q->where('date_debut_previsionnelle', '<=', $dTo);
            }

            // ── Study Types ──────────────────────────────────────────────────
            if ($stIds = $request->input('study_types')) {
                $stIds = array_filter((array) $stIds);
                if (!empty($stIds)) {
                    $q->whereHas('studyTypesApplied', fn($sub) => $sub->whereIn('pro_studies_types.id', $stIds));
                }
            }

            // ── Lab Tests ────────────────────────────────────────────────────
            if ($ltIds = $request->input('lab_tests')) {
                $ltIds = array_filter((array) $ltIds);
                if (!empty($ltIds)) {
                    $q->whereHas('labTestsConcerned', fn($sub) => $sub->whereIn('pro_lab_tests.id', $ltIds));
                }
            }

            // ── Product Types ────────────────────────────────────────────────
            if ($ptIds = $request->input('product_types')) {
                $ptIds = array_filter((array) $ptIds);
                if (!empty($ptIds)) {
                    $q->whereHas('productTypesEvaluated', fn($sub) => $sub->whereIn('pro_products_types.id', $ptIds));
                }
            }

            // ── QA Inspection dates (scheduled) ─────────────────────────────
            if ($qaFrom = $request->input('qa_date_from')) {
                $q->whereHas('qaInspections', fn($sub) => $sub->where('date_scheduled', '>=', $qaFrom));
            }
            if ($qaTo = $request->input('qa_date_to')) {
                $q->whereHas('qaInspections', fn($sub) => $sub->where('date_scheduled', '<=', $qaTo));
            }

            // ── Report Phase submission dates ────────────────────────────────
            if ($rFrom = $request->input('report_date_from')) {
                $q->whereHas('reportPhaseDocuments', fn($sub) => $sub->where('submission_date', '>=', $rFrom));
            }
            if ($rTo = $request->input('report_date_to')) {
                $q->whereHas('reportPhaseDocuments', fn($sub) => $sub->where('submission_date', '<=', $rTo));
            }

            // ── Archiving date ───────────────────────────────────────────────
            if ($aFrom = $request->input('archive_date_from')) {
                $q->where('archived_at', '>=', $aFrom);
            }
            if ($aTo = $request->input('archive_date_to')) {
                $q->where('archived_at', '<=', $aTo);
            }

            // ── Double Entry — 1st entry ─────────────────────────────────────
            if ($de1From = $request->input('date_first_entry_from')) {
                $q->whereHas('dmDoubleEntries', fn($sub) => $sub->where('first_entry_date', '>=', $de1From));
            }
            if ($de1To = $request->input('date_first_entry_to')) {
                $q->whereHas('dmDoubleEntries', fn($sub) => $sub->where('first_entry_date', '<=', $de1To));
            }

            // ── Double Entry — 2nd entry ─────────────────────────────────────
            if ($de2From = $request->input('date_second_entry_from')) {
                $q->whereHas('dmDoubleEntries', fn($sub) => $sub->where('second_entry_date', '>=', $de2From));
            }
            if ($de2To = $request->input('date_second_entry_to')) {
                $q->whereHas('dmDoubleEntries', fn($sub) => $sub->where('second_entry_date', '<=', $de2To));
            }

            $projects = $q->orderBy('created_at', 'desc')->get();
            $total    = $projects->count();
        }

        // ── Filter option data ───────────────────────────────────────────────
        $studyTypes   = Pro_StudyType::orderBy('level_type')->get();
        $labTests     = Pro_LabTest::orderBy('level_test')->get();
        $productTypes = Pro_ProductType::orderBy('level_product')->get();

        $statuses = [
            'not_started' => 'Not Started',
            'in progress' => 'In Progress',
            'suspended'   => 'Suspended',
            'completed'   => 'Completed',
            'archived'    => 'Archived',
        ];

        $natures     = Pro_Project::whereNotNull('project_nature')->where('project_nature', '!=', '')->distinct()->pluck('project_nature')->sort()->values();
        $testSystems = Pro_Project::whereNotNull('test_system')->where('test_system', '!=', '')->distinct()->pluck('test_system')->sort()->values();

        // ── Autocomplete data ────────────────────────────────────────────────
        // SD: only designated Study Directors (all, not just active — old projects may have past SDs)
        $sdPersonnel = \App\Models\Pro_StudyDirector::with('personnel')
            ->get()
            ->map(fn($sd) => $sd->personnel)
            ->filter()
            ->sortBy('nom')
            ->values();

        // PM: personnel with an active contract
        $pmPersonnel = Pro_Personnel::where('sous_contrat', 1)
            ->orderBy('nom')
            ->get(['id', 'titre_personnel', 'nom', 'prenom']);

        // KP: all personnel (past team members findable too)
        $allPersonnel = Pro_Personnel::orderBy('nom')
            ->get(['id', 'titre_personnel', 'nom', 'prenom']);

        $allSponsors      = Pro_Project::whereNotNull('sponsor_name')->where('sponsor_name', '!=', '')->distinct()->pluck('sponsor_name')->sort()->values();
        $allManufacturers = Pro_Project::whereNotNull('manufacturer_name')->where('manufacturer_name', '!=', '')->distinct()->pluck('manufacturer_name')->sort()->values();

        return view('features.search', compact(
            'projects', 'total', 'hasSearch',
            'statuses', 'studyTypes', 'labTests', 'productTypes',
            'natures', 'testSystems',
            'sdPersonnel', 'pmPersonnel', 'allPersonnel',
            'allSponsors', 'allManufacturers'
        ));
    }

    // ────────────────────────────────────────────────────────────────────────
    //  PROJECT DIAGNOSTICS
    // ────────────────────────────────────────────────────────────────────────

    public function diagnostics()
    {
        $today      = Carbon::today();
        $projects   = Pro_Project::with([
            'studyDirectorAppointmentForm.studyDirector',
            'allActivitiesProject',
            'reportPhaseDocuments',
            'archivingDocuments',
        ])->whereNull('archived_at')->get();

        $diagnostics = $projects->map(function ($p) use ($today) {
            // ── Overdue activities ────────────────────────────────────
            $overdueActivities = $p->allActivitiesProject->filter(function ($a) use ($today) {
                return $a->status !== 'completed'
                    && is_null($a->actual_activity_date)
                    && $a->estimated_activity_end_date
                    && Carbon::parse($a->estimated_activity_end_date)->lt($today);
            });

            // ── Final report date ─────────────────────────────────────
            $finalReport = $p->reportPhaseDocuments
                ->where('document_type', 'final_report')
                ->whereNotNull('submission_date')
                ->sortByDesc('submission_date')
                ->first();
            $reportDate = $finalReport?->submission_date
                ? Carbon::parse($finalReport->submission_date)
                : null;

            // ── Archiving deadline (max 3 months after final report) ──
            $archiveDeadline   = $reportDate?->copy()->addMonths(3);
            $archiveOverdue    = $archiveDeadline && $archiveDeadline->lt($today) && !$p->archived_at;
            $daysToArchive     = $archiveDeadline ? $today->diffInDays($archiveDeadline, false) : null;

            // ── Report overdue (no final report + last experiment > 6 months ago) ──
            $doneActivities  = $p->allActivitiesProject->whereNotNull('actual_activity_date');
            $lastExperiment  = $doneActivities->sortByDesc('actual_activity_date')->first();
            $lastExpDate     = $lastExperiment?->actual_activity_date
                ? Carbon::parse($lastExperiment->actual_activity_date)
                : null;
            $reportOverdue   = !$finalReport && $lastExpDate && $lastExpDate->diffInMonths($today) >= 6;

            // ── Overall status ────────────────────────────────────────
            $issues = [];
            if ($overdueActivities->isNotEmpty()) {
                $issues[] = ['type' => 'activities', 'count' => $overdueActivities->count(),
                             'label' => $overdueActivities->count() . ' activité(s) en retard',
                             'severity' => 'danger'];
            }
            if ($archiveOverdue) {
                $issues[] = ['type' => 'archiving', 'count' => 1,
                             'label' => "Archivage en retard (délai dépassé depuis {$today->diffInDays($archiveDeadline)} j.)",
                             'severity' => 'danger'];
            }
            if ($reportOverdue) {
                $issues[] = ['type' => 'report', 'count' => 1,
                             'label' => 'Rapport final non soumis (> 6 mois après dernier test)',
                             'severity' => 'warning'];
            }
            if ($archiveDeadline && !$archiveOverdue && $daysToArchive !== null && $daysToArchive <= 30) {
                $issues[] = ['type' => 'archive_soon', 'count' => 1,
                             'label' => "Archivage à faire dans {$daysToArchive} j.",
                             'severity' => 'warning'];
            }

            $status = empty($issues) ? 'ok' : (
                collect($issues)->contains('severity', 'danger') ? 'danger' : 'warning'
            );

            return [
                'project'           => $p,
                'status'            => $status,
                'issues'            => $issues,
                'overdueActivities' => $overdueActivities,
                'reportDate'        => $reportDate,
                'archiveDeadline'   => $archiveDeadline,
                'daysToArchive'     => $daysToArchive,
                'lastExpDate'       => $lastExpDate,
            ];
        });

        $countOk      = $diagnostics->where('status', 'ok')->count();
        $countWarning = $diagnostics->where('status', 'warning')->count();
        $countDanger  = $diagnostics->where('status', 'danger')->count();

        // Sort: danger first, then warning, then ok
        $diagnostics = $diagnostics->sortBy(fn($d) => match($d['status']) {
            'danger'  => 0,
            'warning' => 1,
            default   => 2,
        })->values();

        return view('features.diagnostics', compact(
            'diagnostics', 'countOk', 'countWarning', 'countDanger', 'today'
        ));
    }
}

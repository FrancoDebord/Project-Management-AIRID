<?php

namespace App\Http\Controllers;

use App\Models\Pro_Cl_FacilityInspection;
use App\Models\Pro_Cl_ProcessInspection;
use App\Models\Pro_KeyFacilityPersonnel;
use App\Models\Pro_Personnel;
use App\Models\Pro_Project;
use App\Models\Pro_QaInspection;
use App\Models\Pro_QaInspectionFinding;
use App\Models\Pro_StudyActivities;
use Illuminate\Http\Request;

class QaDashboardController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $all_projects = Pro_Project::orderBy('project_code')->get();

        $inspectionsQuery = Pro_QaInspection::with(['inspector', 'project'])
            ->withCount('findings')
            ->latest('date_scheduled');

        $filterProject = $request->get('project_id');
        $filterType    = $request->get('type_inspection');
        // Default to 'scheduled' when no explicit status param is present
        $filterStatus  = $request->has('status') ? $request->get('status') : 'scheduled';

        if ($filterProject) {
            $inspectionsQuery->where('project_id', $filterProject);
        }
        if ($filterType) {
            $inspectionsQuery->where('type_inspection', $filterType);
        }
        if ($filterStatus === 'scheduled') {
            $inspectionsQuery->whereNull('date_performed');
        } elseif ($filterStatus === 'done') {
            $inspectionsQuery->whereNotNull('date_performed');
        }

        $all_inspections = $inspectionsQuery->get();

        // All inspections for the calendar (no filter)
        $calendar_inspections = Pro_QaInspection::with(['project', 'inspector'])
            ->select('id', 'inspection_name', 'type_inspection', 'date_scheduled', 'date_performed', 'project_id', 'qa_inspector_id')
            ->get()
            ->map(fn($i) => [
                'id'             => $i->id,
                'title'          => $i->inspection_name ?? $i->type_inspection,
                'start'          => $i->date_scheduled,
                'type'           => $i->type_inspection,
                'done'           => !is_null($i->date_performed),
                'project_code'   => $i->project?->project_code ?? '',
                'inspector_name' => $i->inspector ? $i->inspector->prenom . ' ' . $i->inspector->nom : '',
            ]);

        // Stats
        $totalInspections    = Pro_QaInspection::count();
        $scheduledCount      = Pro_QaInspection::whereNull('date_performed')->count();
        $doneCount           = Pro_QaInspection::whereNotNull('date_performed')->count();
        $unresolvedFindings  = Pro_QaInspectionFinding::where('status', 'pending')
                                  ->where('is_conformity', 0)->count();

        $all_personnels = Pro_Personnel::orderBy('prenom')->get();

        $qaManagerDefaultId = \DB::table('pro_key_facility_personnels')
            ->where('staff_role', 'Quality Assurance')
            ->where('active', 1)
            ->value('personnel_id');

        $inspectionTypes = [
            'Facility Inspection',
            'Process Inspection',
            'Study Inspection',
            'Study Protocol Inspection',
            'Study Report Inspection',
            'Data Quality Inspection',
            'Critical Phase Inspection',
            'Study Protocol Amendment/Deviation Inspection',
            'Study Report Amendment Inspection',
        ];

        // Determine which facility inspections have already started (any section filled)
        // and which are fully complete (all sections filled)
        $facilityInspections = $all_inspections
            ->where('type_inspection', 'Facility Inspection')
            ->whereNull('date_performed');

        $facilityIds = $facilityInspections->pluck('id');

        $facilityStartedIds = collect();
        $facilityReadyIds   = collect(); // all sections filled → can be marked done

        if ($facilityIds->isNotEmpty()) {
            // Cotonou records
            $cotonouRecords = Pro_Cl_FacilityInspection::whereIn('inspection_id', $facilityIds)
                ->get(['inspection_id', 'sections_done']);
            // Covè records
            $coveRecords = \App\Models\Pro_Cl_FacilityInspectionCove::whereIn('inspection_id', $facilityIds)
                ->get(['inspection_id', 'sections_done']);

            $allRecords = $cotonouRecords->merge($coveRecords);
            $facilityStartedIds = $allRecords->pluck('inspection_id')->unique();

            // A facility inspection is "ready" when sections_done count = expected total
            foreach ($facilityInspections as $fi) {
                $expectedTotal = ($fi->facility_location === 'cove') ? 9 : 15;
                $record = $allRecords->firstWhere('inspection_id', $fi->id);
                if ($record) {
                    $done = is_array($record->sections_done) ? $record->sections_done : (json_decode($record->sections_done ?? '[]', true) ?? []);
                    if (count($done) >= $expectedTotal) {
                        $facilityReadyIds->push($fi->id);
                    }
                }
            }
        }

        // Determine which process inspections have already started and which are fully complete
        $processInspections = $all_inspections
            ->where('type_inspection', 'Process Inspection')
            ->whereNull('date_performed');

        $processIds       = $processInspections->pluck('id');
        $processStartedIds = collect();
        $processReadyIds   = collect();

        if ($processIds->isNotEmpty()) {
            $processRecords = Pro_Cl_ProcessInspection::whereIn('inspection_id', $processIds)
                ->get(['inspection_id', 'sections_done']);

            $processStartedIds = $processRecords->pluck('inspection_id')->unique();

            foreach ($processInspections as $pi) {
                $record = $processRecords->firstWhere('inspection_id', $pi->id);
                if ($record) {
                    $done = is_array($record->sections_done) ? $record->sections_done : (json_decode($record->sections_done ?? '[]', true) ?? []);
                    if (count($done) >= 5) {
                        $processReadyIds->push($pi->id);
                    }
                }
            }
        }

        return view('qa-dashboard', compact(
            'all_projects',
            'all_inspections',
            'calendar_inspections',
            'all_personnels',
            'totalInspections',
            'scheduledCount',
            'doneCount',
            'unresolvedFindings',
            'qaManagerDefaultId',
            'inspectionTypes',
            'filterProject',
            'filterType',
            'filterStatus',
            'facilityStartedIds',
            'facilityReadyIds',
            'processStartedIds',
            'processReadyIds'
        ));
    }

    /**
     * Return critical activities for a project (for the "link to activity" dropdown)
     */
    public function criticalActivities(Request $request)
    {
        $project_id = $request->project_id;
        if (!$project_id) {
            return response()->json(['activities' => []]);
        }
        $activities = Pro_StudyActivities::where('project_id', $project_id)
            ->where('phase_critique', 1)
            ->orderBy('estimated_activity_date')
            ->get(['id', 'study_activity_name', 'status', 'estimated_activity_date']);

        return response()->json(['activities' => $activities]);
    }
}

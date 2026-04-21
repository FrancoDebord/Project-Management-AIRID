<?php

namespace App\Http\Controllers;

use App\Models\Pro_Personnel;
use App\Models\Pro_Project;
use App\Models\Pro_Project_Team;
use App\Models\Pro_StudyDirector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{


    function __construct() {

        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $all_projects = Pro_Project::orderBy("date_debut_effective", "desc")->get();

        return view("index-project", compact("all_projects"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $project = new Pro_Project();

        // Only active contractors for PM / key-personnel selects
        $all_personnels = Pro_Personnel::where('sous_contrat', 1)->orderBy('prenom')->get();
        // Only designated Study Directors for the SD select
        $sd_personnels = Pro_StudyDirector::where('active', true)
            ->with('personnel')->get()->pluck('personnel')->filter()->sortBy('nom');
        $all_projects = Pro_Project::all();

        $project_id =  $request->project_id;

        if ($project_id) {
            $project = Pro_Project::find($project_id);
        }

        $columns = Schema::getColumnListing('pro_projects');

        $total_filled_percentage_projects = $this->computeCompleteness('pro_projects', $project->id ?? 0);
        $total_filled_percentage_study_director_appointment = $this->computeCompleteness('pro_study_director_appointment_forms', $project->studyDirectorAppointmentForm->id ?? 0, ['id', 'replacement_date', 'created_at', 'updated_at',"study_director_signature","quality_assurance_signature","fm_signature","comments","sd_appointment_file"]);

        $execution_rate = 0;
        $phase_metrics  = [];
        $project_phase  = 'study_creation';

        if ($project->id) {
            $pid = $project->id;

            // 1. Activities
            $totalActivities     = DB::table('pro_studies_activities')->where('project_id', $pid)->count();
            $completedActivities = DB::table('pro_studies_activities')->where('project_id', $pid)->where('status', 'completed')->count();

            // 2. QA Inspections (completed = date_performed set) — GLP only
            $totalInspections     = 0;
            $completedInspections = 0;
            if ($project->is_glp) {
                $totalInspections     = DB::table('pro_qa_inspections')->where('project_id', $pid)->count();
                $completedInspections = DB::table('pro_qa_inspections')->where('project_id', $pid)->whereNotNull('date_performed')->count();
            }

            // 3. NC Findings (is_conformity = 0 → non-conformity, resolved = status 'complete') — GLP only
            $totalNc    = 0;
            $resolvedNc = 0;
            if ($project->is_glp) {
                $totalNc    = DB::table('pro_qa_inspections_findings')->where('project_id', $pid)->where('is_conformity', 0)->count();
                $resolvedNc = DB::table('pro_qa_inspections_findings')->where('project_id', $pid)->where('is_conformity', 0)->where('status', 'complete')->count();
            }

            // 4. Protocol Dev documents (applicable activities, complete = true; level 5 = Amendment/Deviation is optional)
            $totalProtocolDev     = DB::table('pro_protocols_devs_activities_projects')->where('project_id', $pid)->where('applicable', true)->where('level_activite', '<>', 5)->count();
            $completedProtocolDev = DB::table('pro_protocols_devs_activities_projects')->where('project_id', $pid)->where('applicable', true)->where('level_activite', '<>', 5)->where('complete', true)->count();

            // 5. Report phase — single milestone (done when ≥1 submitted/published doc exists)
            $completedReportDocs = DB::table('pro_report_phase_documents')->where('project_id', $pid)->whereIn('status', ['submitted', 'published'])->count();
            $totalReportDocs     = $completedReportDocs; // for phase_metrics display only
            $reportMilestone     = $completedReportDocs > 0 ? 1 : 0;

            // 6. Archiving milestone (1 point, done when archived_at is set OR phase manually marked completed)
            $isArchived = ($project->archived_at || in_array('archiving', $project->phases_completed ?? [])) ? 1 : 0;

            // Composite rate: activities + protocol dev docs + inspections + NC findings + 1 reporting + 1 archiving
            $totalItems     = $totalActivities + $totalProtocolDev + $totalInspections + $totalNc + 1 + 1;
            $completedItems = $completedActivities + $completedProtocolDev + $completedInspections + $resolvedNc + $reportMilestone + $isArchived;
            $execution_rate = $totalItems > 0 ? round(($completedItems / $totalItems) * 100, 1) : 0;

            // Phase metrics passed to view
            $phase_metrics = [
                'activities'   => ['total' => $totalActivities,     'done' => $completedActivities],
                'protocol_dev' => ['total' => $totalProtocolDev,    'done' => $completedProtocolDev],
                'inspections'  => ['total' => $totalInspections,    'done' => $completedInspections],
                'nc_findings'  => ['total' => $totalNc,             'done' => $resolvedNc],
                'report_docs'  => ['total' => $totalReportDocs,     'done' => $completedReportDocs],
                'archiving'    => ['total' => 1,                    'done' => $isArchived],
            ];

            // Compute phase statuses first — needed to auto-detect current phase
            $phaseStatuses = $this->computePhaseStatuses($project);

            // Current phase: first phase that is neither manually completed
            // nor fully done by data criteria
            $phasesCompleted = $project->phases_completed ?? [];
            $phaseOrder = $project->is_glp
                ? ['study_creation', 'protocol_details', 'protocol_development', 'planning', 'experimental', 'quality_assurance', 'reporting', 'archiving']
                : ['study_creation', 'protocol_details', 'protocol_development', 'experimental', 'reporting', 'archiving'];
            $project_phase = 'study_creation';
            foreach ($phaseOrder as $p) {
                $doneManually = in_array($p, $phasesCompleted);
                $doneCriteria = $phaseStatuses[$p]['can_complete'] ?? false;
                if (!$doneManually && !$doneCriteria) {
                    $project_phase = $p;
                    break;
                }
                $project_phase = 'all_done';
            }
        } else {
            $phaseStatuses = $this->computePhaseStatuses($project);
        }

        return view("study_management_design", compact("project", "all_personnels", "sd_personnels", "all_projects", "total_filled_percentage_projects", "total_filled_percentage_study_director_appointment", "execution_rate", "phase_metrics", "project_phase", "phaseStatuses"));
    }

    // Compute per-phase completion status
    private function computePhaseStatuses($project): array
    {
        $statuses = [];

        if (!$project->id) {
            return $statuses;
        }

        // ── Legacy projects: all phases auto-validated ──────────────────────
        if ($project->is_legacy) {
            $phases = ['study_creation','protocol_details','protocol_development','planning','experimental','quality_assurance','reporting','archiving'];
            foreach ($phases as $phase) {
                $statuses[$phase] = [
                    'can_complete' => true,
                    'items' => [
                        ['label' => 'Legacy project — phase pre-validated', 'done' => true],
                    ],
                    'next' => 'Legacy project — all phases are pre-validated.',
                ];
            }
            return $statuses;
        }

        $pid = $project->id;

        // ── Study Creation ──────────────────────────────────────────────────
        // Only truly required fields: project_code + project_title
        // study_director and protocol_code are optional in the form
        $codeOk      = !empty($project->project_code);
        $titleOk     = !empty($project->project_title);
        // study_director: accept from either the project record OR the appointment form
        $apptForm    = $project->studyDirectorAppointmentForm;
        $sdOk        = !empty($project->study_director)
                       || ($apptForm && !empty($apptForm->study_director));
        $basicOk     = $codeOk && $titleOk && $sdOk;
        $apptOk      = $apptForm && $apptForm->study_director && $apptForm->sd_appointment_date;
        $statuses['study_creation'] = [
            'can_complete' => (bool)$basicOk && (bool)$apptOk,
            'items' => [
                ['label' => 'Basic project information filled (code, title, study director)', 'done' => (bool)$basicOk],
                ['label' => 'Study Director Appointment form filled',                         'done' => (bool)$apptOk],
            ],
            'next' => !(bool)$codeOk
                ? 'Fill in the project code.'
                : (!(bool)$titleOk
                    ? 'Fill in the project title.'
                    : (!(bool)$sdOk
                        ? 'Select a Study Director in the basic information form or in the Appointment form.'
                        : (!(bool)$apptOk
                            ? 'Fill in the Study Director Appointment form (director + appointment date).'
                            : 'All criteria met — ready to mark as completed.'))),
        ];

        // ── Protocol Details ────────────────────────────────────────────────
        $statuses['protocol_details'] = [
            'can_complete' => true,
            'items' => [
                ['label' => 'Protocol details defined (no automatic validation)', 'done' => true],
            ],
            'next' => 'Ensure all protocol details and activities are defined before marking as completed.',
        ];

        // ── Protocol Development ────────────────────────────────────────────
        // Level 5 (Amendment/Deviation) is optional — excluded from completion check
        $totalProtDev     = DB::table('pro_protocols_devs_activities_projects')->where('project_id', $pid)->where('applicable', true)->where('level_activite', '<>', 5)->count();
        $completedProtDev = DB::table('pro_protocols_devs_activities_projects')->where('project_id', $pid)->where('applicable', true)->where('level_activite', '<>', 5)->where('complete', true)->count();
        $protDevOk        = $totalProtDev > 0 && $completedProtDev === $totalProtDev;
        $statuses['protocol_development'] = [
            'can_complete' => $protDevOk,
            'items' => [
                ['label' => "Protocol development documents ({$completedProtDev}/{$totalProtDev} completed)", 'done' => $protDevOk],
            ],
            'next' => $protDevOk
                ? 'All protocol development documents provided — ready to mark as completed.'
                : ($totalProtDev === 0
                    ? 'Generate and complete protocol development activities first.'
                    : "Complete remaining protocol development activities ({$completedProtDev}/{$totalProtDev} done)."),
        ];

        // ── Planning ────────────────────────────────────────────────────────
        $meetingDone   = DB::table('pro_studies_initiation_meetings')
            ->where('project_id', $pid)
            ->where('status', '!=', 'cancelled')
            ->where('date_scheduled', '<=', now()->toDateString())
            ->count() > 0;
        $criticalCount = DB::table('pro_studies_activities')->where('project_id', $pid)->where('phase_critique', 1)->count();
        $cpiaAssessment = \App\Models\CpiaAssessment::where('project_id', $pid)->first();
        $cpiaCompleted  = $cpiaAssessment && $cpiaAssessment->status === 'completed';
        $planningOk     = $meetingDone && $criticalCount > 0 && $cpiaCompleted;
        $statuses['planning'] = [
            'can_complete' => $planningOk,
            'items' => [
                ['label' => 'Study Initiation Meeting scheduled and date passed',              'done' => $meetingDone],
                ['label' => "Critical phases identified ({$criticalCount} identified)",        'done' => $criticalCount > 0],
                ['label' => 'Critical Phase Impact Assessment completed',                      'done' => $cpiaCompleted],
            ],
            'next' => !$meetingDone
                ? 'Schedule the Study Initiation Meeting and wait for its date to pass.'
                : ($criticalCount === 0
                    ? 'Identify at least one critical phase in the activities.'
                    : (!$cpiaCompleted
                        ? 'Complete the Critical Phase Impact Assessment.'
                        : 'All criteria met — ready to mark as completed.')),
        ];

        // ── Experimental ────────────────────────────────────────────────────
        $totalActs     = DB::table('pro_studies_activities')->where('project_id', $pid)->count();
        $completedActs = DB::table('pro_studies_activities')->where('project_id', $pid)->where('status', 'completed')->count();
        $expOk         = $totalActs > 0 && $completedActs === $totalActs;

        $nextActivity  = !$expOk ? DB::table('pro_studies_activities')
            ->where('project_id', $pid)
            ->whereIn('status', ['pending', 'in_progress', 'delayed'])
            ->orderByRaw("CASE WHEN status = 'in_progress' THEN 0 ELSE 1 END")
            ->orderBy('estimated_activity_date')
            ->first() : null;

        $nextActivityLabel = '';
        if ($nextActivity) {
            $dateStr = $nextActivity->estimated_activity_date
                ? ' — scheduled ' . \Carbon\Carbon::parse($nextActivity->estimated_activity_date)->format('d M Y')
                : '';
            $statusTag = $nextActivity->status === 'in_progress' ? ' [In progress]' : ($nextActivity->status === 'delayed' ? ' [Delayed]' : '');
            $nextActivityLabel = "Next activity: \"{$nextActivity->study_activity_name}\"{$statusTag}{$dateStr}";
        }

        $statuses['experimental'] = [
            'can_complete' => $expOk,
            'items' => [
                ['label' => "Activities executed ({$completedActs}/{$totalActs})", 'done' => $expOk],
            ],
            'next' => $expOk
                ? 'All activities completed — ready to mark as completed.'
                : ($totalActs === 0
                    ? 'No activities have been scheduled yet.'
                    : ($nextActivityLabel ?: "Execute remaining activities ({$completedActs}/{$totalActs} done).")),
        ];

        // ── Quality Assurance ───────────────────────────────────────────────
        $totalInsp     = DB::table('pro_qa_inspections')->where('project_id', $pid)->count();
        $completedInsp = DB::table('pro_qa_inspections')->where('project_id', $pid)->whereNotNull('date_performed')->count();
        $totalNc       = DB::table('pro_qa_inspections_findings')->where('project_id', $pid)->where('is_conformity', 0)->count();
        $resolvedNc    = DB::table('pro_qa_inspections_findings')->where('project_id', $pid)->where('is_conformity', 0)->where('status', 'complete')->count();
        $inspAllDone   = $totalInsp > 0 && $completedInsp === $totalInsp;
        $ncAllResolved = $totalNc === 0 || $resolvedNc === $totalNc;
        $qaOk          = $inspAllDone && $ncAllResolved;
        $statuses['quality_assurance'] = [
            'can_complete' => $qaOk,
            'items' => [
                ['label' => "All inspections completed ({$completedInsp}/{$totalInsp})",    'done' => $inspAllDone],
                ['label' => "NC findings resolved ({$resolvedNc}/{$totalNc})",              'done' => $ncAllResolved],
            ],
            'next' => !$inspAllDone
                ? "Complete all scheduled inspections ({$completedInsp}/{$totalInsp} done)."
                : (!$ncAllResolved
                    ? "Resolve all non-conformity findings ({$resolvedNc}/{$totalNc} resolved)."
                    : 'All criteria met — ready to mark as completed.'),
        ];

        // ── Data Management ─────────────────────────────────────────────────────
        $dbCount = DB::table('pro_dm_databases')->where('project_id', $pid)->count();
        $pcCount = DB::table('pro_dm_pc_assignments')->where('project_id', $pid)->count();
        $deCount = DB::table('pro_dm_double_entries')->where('project_id', $pid)->count();
        $dmOk    = $dbCount > 0 && $pcCount > 0 && $deCount > 0;
        $statuses['data_management'] = [
            'can_complete' => $dmOk,
            'items' => [
                ['label' => "Bases de données enregistrées ({$dbCount})",          'done' => $dbCount > 0],
                ['label' => "PC de saisie attribué ({$pcCount})",                  'done' => $pcCount > 0],
                ['label' => "Sessions de double saisie enregistrées ({$deCount})", 'done' => $deCount > 0],
            ],
            'next' => $dbCount === 0
                ? 'Enregistrez au moins une base de données.'
                : ($pcCount === 0
                    ? 'Attribuez au moins un PC de saisie.'
                    : ($deCount === 0
                        ? 'Enregistrez au moins une session de double saisie.'
                        : 'Tous les critères sont remplis — prêt à marquer comme complété.')),
        ];

        // ── Reporting ───────────────────────────────────────────────────────
        $reportCount = DB::table('pro_report_phase_documents')->where('project_id', $pid)->count();
        $reportOk    = $reportCount > 0;
        $statuses['reporting'] = [
            'can_complete' => $reportOk,
            'items' => [
                ['label' => "At least one report document added ({$reportCount} document(s))", 'done' => $reportOk],
            ],
            'next' => $reportOk
                ? 'Report documents recorded — ready to mark as completed.'
                : 'Add at least one report document.',
        ];

        // ── Archiving ───────────────────────────────────────────────────────
        $savedChecklist    = $project->archive_checklist ?? [];
        $checklistItemKeys = ['physical_docs_archived', 'raw_data_secured', 'electronic_backup', 'samples_stored', 'personnel_notified'];
        $checkedCount      = count(array_filter($savedChecklist));
        $totalCheckItems   = count($checklistItemKeys);
        $checklistOk       = $checkedCount === $totalCheckItems;
        $statuses['archiving'] = [
            'can_complete' => $checklistOk,
            'items' => [
                ['label' => "Archiving checklist completed ({$checkedCount}/{$totalCheckItems} items checked)", 'done' => $checklistOk],
            ],
            'next' => $checklistOk
                ? 'Archiving checklist complete — ready to mark as completed.'
                : "Complete the archiving checklist ({$checkedCount}/{$totalCheckItems} items checked).",
        ];

        return $statuses;
    }

    // Calculer le pourcentage de complétude d'une table
    private function computeCompleteness($table, $id, $exclude = ['id', 'created_at', 'updated_at'])
    {
        // Liste des colonnes
        $columns = Schema::getColumnListing($table);

        // Exclure les colonnes techniques
        $columns = array_diff($columns, $exclude);


        // Récupérer la ligne
        $record = DB::table($table)->where('id', $id)->first();


        if (!$record) {
            return 0; // pas trouvé
        }

        $filled = 0;
        $total = count($columns);

        foreach ($columns as $col) {
            if (!empty($record->$col)) {
                $filled++;
            }
        }

        // Pourcentage
        return $total > 0 ? round(($filled / $total) * 100, 2) : 0;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


        // $rules = [
        //     "code" => "required|string|max:50|unique:pro_projects,project_code",
        //     "title" => "required|string|max:255",
        //     "is_glp" => "required|boolean",
        // ];

        // $validator = Validator::make($request->all(), $rules);

        // if ($validator->fails()) {
        //     return response()->json(['errors' => $validator->errors(), "code_erreur" => 1], 422);
        // }

        // $project =  Pro_Project::create([
        //     'project_code' => $request->code,
        //     'project_title' => $request->title,
        //     'is_glp' => (bool) $request->is_glp
        // ]);

        // if ($request->ajax()) {

        //     $data = [
        //         'project_id' => $project->id,
        //         'project_code' => $project->project_code,
        //         'project_title' => $project->project_title,
        //         'is_glp' => $project->is_glp
        //     ];
        //     return response()->json(['message' => 'Project successfully created.', 'data' => $data, "code_erreur" => 0], 201);
        // } else {

        //     return redirect()->route('project.create')->with('success', 'Project successfully created.');
        // }
    }

    /**
     * Display the specified resource.
     */
    public function show(Pro_Project $pro_Project)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($project_id)
    {
        $project = Pro_Project::find($project_id);

        $all_personnels = Pro_Personnel::where('sous_contrat', 1)->orderBy('prenom')->get();
        $sd_personnels = Pro_StudyDirector::where('active', true)
            ->with('personnel')->get()->pluck('personnel')->filter()->sortBy('nom');
        return view("create-project", compact("project", "all_personnels", "sd_personnels"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $project_id)
    {
        $rules = [
            "project_code" => "required|string",
            "project_title" => "string|required",
            "project_status" => "string|required",
            "project_nature" => "string|required",
            "test_system" => "string|required",
            "key_personnel" => "array|nullable",
            "protocol_code" => "string|required",
            "study_director" => "integer|nullable|exists:personnels,id",
            "project_manager" => "integer|nullable|exists:personnels,id",
            "date_debut_previsionnelle" => "date|nullable",
            "date_debut_effective" => "date|nullable",
            "date_fin_previsionnelle" => "date|nullable",
            "date_fin_effective" => "date|nullable",
            "project_stage" => "string|required",
        ];

        $message = "";

        $request->validate($rules);

        $project = Pro_Project::findOrFail($project_id);

        $update = $project->update($request->except(["_method", "_token", "key_personnel"]));

        if ($update) {

            if ($request->key_personnel) {

                //Supprimer les anciens Key Personnel
                $delete_olds_key_personnels = Pro_Project_Team::where("project_id", $project_id)->delete();

                foreach ($request->key_personnel as $key => $staff_id) {

                    $project_team = Pro_Project_Team::create([
                        "project_id" => $project->id,
                        "staff_id" => $staff_id,
                        "role" => "",
                    ]);
                }
            }
        }

        $message = "The new Project is updated successfully...";
        $all_personnels = Pro_Personnel::where('sous_contrat', 1)->orderBy('prenom')->get();
        $sd_personnels = Pro_StudyDirector::where('active', true)
            ->with('personnel')->get()->pluck('personnel')->filter()->sortBy('nom');

        return view("create-project", compact("project", "all_personnels", "sd_personnels"))->with("message", $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pro_Project $pro_Project)
    {
        //
    }
    
    
}

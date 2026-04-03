<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Pro_Project;
use App\Models\Pro_Project_Phase;
use App\Models\Pro_Project_StudyPhaseCompleted;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProjectActivityScheduleController extends Controller
{
    //

    public function masterSchedule(Request $request)
    {
        $projects = Pro_Project::with([
            'keyPersonnelProject',
            'studyDirectorAppointmentForm.studyDirector',
            'protocolDeveloppementActivitiesProject',
            'allActivitiesProject',
            'reportPhaseDocuments',
            'archivingDocuments',
        ])->orderByDesc('project_code')->get();

        $manageUrl = fn(int $id, string $step) =>
            route('project.create', ['project_id' => $id]) . '#' . $step;

        $scheduleData = $projects->map(function ($project) use ($manageUrl) {
            
        $project->project_status = $project->is_glp ? "GLP":"NON-GLP";
        // Study Start phase
            $sdForm      = $project->studyDirectorAppointmentForm;
            $startDate   = $sdForm?->sd_appointment_date;
            $level1      = $project->protocolDeveloppementActivitiesProject
                                   ->firstWhere('level_activite', 1);
            $studyStartEnd = $level1?->real_date_performed;

            // Planning phase
            $level3       = $project->protocolDeveloppementActivitiesProject
                                    ->firstWhere('level_activite', 3);
            $planningStart = $level3?->real_date_performed;

            $doneActivities = $project->allActivitiesProject
                                      ->filter(fn($a) => !is_null($a->actual_activity_date))
                                      ->sortBy('actual_activity_date');
            $firstActivity = $doneActivities->first();
            $lastActivity  = $doneActivities->last();

            $expStart    = $firstActivity?->actual_activity_date;
            $expEnd      = $lastActivity?->actual_activity_date;
            $planningEnd = $expStart;

            // Report phase
            $reportStart = $expEnd;
            $finalReport = $project->reportPhaseDocuments
                                   ->where('document_type', 'final_report')
                                   ->filter(fn($d) => !is_null($d->submission_date))
                                   ->sortByDesc('submission_date')
                                   ->first();
            $reportEnd = $finalReport?->submission_date;

            // Archiving phase
            $archiveStart = $reportEnd;
            $archiveDoc   = $project->archivingDocuments
                                    ->filter(fn($d) => !is_null($d->archive_date))
                                    ->sortBy('archive_date')
                                    ->first();
            $archiveEnd   = $archiveDoc?->archive_date
                         ?? ($project->archived_at?->format('Y-m-d'));

            $pid = $project->id;

            return [
                'project'      => $project,
                'study_start'  => [
                    'start' => ['date' => $startDate,    'tab' => 'step1', 'tooltip' => 'SD Appointment Date',                   'nr_url' => $manageUrl($pid, 'step1')],
                    'end'   => ['date' => $studyStartEnd,'tab' => 'step3', 'tooltip' => 'SD uploads Draft Protocol',              'nr_url' => $manageUrl($pid, 'step3')],
                ],
                'planning'     => [
                    'start' => ['date' => $planningStart,'tab' => 'step3', 'tooltip' => 'Final Approved Protocol (signed)',       'nr_url' => $manageUrl($pid, 'step3')],
                    'end'   => ['date' => $planningEnd,  'tab' => 'step5', 'tooltip' => 'First experimental activity performed',  'nr_url' => $manageUrl($pid, 'step5')],
                ],
                'experimental' => [
                    'start' => ['date' => $expStart,     'tab' => 'step5', 'tooltip' => 'First experimental activity performed',  'nr_url' => $manageUrl($pid, 'step5')],
                    'end'   => ['date' => $expEnd,       'tab' => 'step5', 'tooltip' => 'Last experimental activity performed',   'nr_url' => $manageUrl($pid, 'step5')],
                ],
                'report'       => [
                    'start' => ['date' => $reportStart,  'tab' => 'step5', 'tooltip' => 'Last experimental activity performed',   'nr_url' => $manageUrl($pid, 'step5')],
                    'end'   => ['date' => $reportEnd,    'tab' => 'step7', 'tooltip' => 'Final Report submitted (signed by SD)',   'nr_url' => $manageUrl($pid, 'step7')],
                ],
                'archiving'    => [
                    'start' => ['date' => $archiveStart, 'tab' => 'step7', 'tooltip' => 'Final Report signed by all parties',     'nr_url' => $manageUrl($pid, 'step7')],
                    'end'   => ['date' => $archiveEnd,   'tab' => 'step8', 'tooltip' => 'Study documents submitted to archivist', 'nr_url' => $manageUrl($pid, 'step8')],
                ],
            ];
        });

        return view('master-schedule', compact('scheduleData'));
    }

    /** Generate Master Schedule as PDF (A3 landscape) */
    public function masterSchedulePdf()
    {
        $projects = Pro_Project::with([
            'keyPersonnelProject',
            'studyDirectorAppointmentForm.studyDirector',
            'protocolDeveloppementActivitiesProject',
            'allActivitiesProject',
            'reportPhaseDocuments',
            'archivingDocuments',
        ])->orderBy('project_code')->get();

        $manageUrl = fn(int $id, string $step) =>
            route('project.create', ['project_id' => $id]) . '#' . $step;

        $scheduleData = $projects->map(function ($project) use ($manageUrl) {
            $project->project_status = $project->is_glp ? 'GLP' : 'NON-GLP';
            $sdForm        = $project->studyDirectorAppointmentForm;
            $startDate     = $sdForm?->sd_appointment_date;
            $level1        = $project->protocolDeveloppementActivitiesProject->firstWhere('level_activite', 1);
            $studyStartEnd = $level1?->real_date_performed;
            $level3        = $project->protocolDeveloppementActivitiesProject->firstWhere('level_activite', 3);
            $planningStart = $level3?->real_date_performed;
            $doneActivities = $project->allActivitiesProject->filter(fn($a) => !is_null($a->actual_activity_date))->sortBy('actual_activity_date');
            $expStart    = $doneActivities->first()?->actual_activity_date;
            $expEnd      = $doneActivities->last()?->actual_activity_date;
            $planningEnd = $expStart;
            $reportStart = $expEnd;
            $reportEnd   = $project->reportPhaseDocuments->where('document_type', 'final_report')->filter(fn($d) => !is_null($d->submission_date))->sortByDesc('submission_date')->first()?->submission_date;
            $archiveStart = $reportEnd;
            $archiveEnd   = $project->archivingDocuments->filter(fn($d) => !is_null($d->archive_date))->sortBy('archive_date')->first()?->archive_date ?? $project->archived_at?->format('Y-m-d');
            $pid = $project->id;

            return [
                'project'      => $project,
                'study_start'  => ['start' => ['date' => $startDate,    'nr_url' => $manageUrl($pid, 'step1')], 'end' => ['date' => $studyStartEnd, 'nr_url' => $manageUrl($pid, 'step3')]],
                'planning'     => ['start' => ['date' => $planningStart, 'nr_url' => $manageUrl($pid, 'step3')], 'end' => ['date' => $planningEnd,  'nr_url' => $manageUrl($pid, 'step5')]],
                'experimental' => ['start' => ['date' => $expStart,      'nr_url' => $manageUrl($pid, 'step5')], 'end' => ['date' => $expEnd,       'nr_url' => $manageUrl($pid, 'step5')]],
                'report'       => ['start' => ['date' => $reportStart,   'nr_url' => $manageUrl($pid, 'step5')], 'end' => ['date' => $reportEnd,    'nr_url' => $manageUrl($pid, 'step7')]],
                'archiving'    => ['start' => ['date' => $archiveStart,  'nr_url' => $manageUrl($pid, 'step7')], 'end' => ['date' => $archiveEnd,   'nr_url' => $manageUrl($pid, 'step8')]],
            ];
        });

        $globalSettings   = AppSetting::allAsMap();
        $headerImagePath  = public_path('storage/assets/header/entete_airid.png');

        $pdf = Pdf::loadView('master-schedule-pdf', compact('scheduleData', 'globalSettings', 'headerImagePath'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('master-schedule-' . now()->format('Y-m-d') . '.pdf');
    }


    /** Generate per-project activities as PDF (A4 landscape) */
    public function projectActivitiesPdf(int $projectId)
    {
        $project = Pro_Project::with([
            'studyDirectorAppointmentForm.studyDirector',
            'allActivitiesProject.category',
            'allActivitiesProject.personneResponsable',
        ])->findOrFail($projectId);

        $activitiesByCategory = $project->allActivitiesProject
            ->groupBy(fn($a) => $a->category->name ?? 'Uncategorized');

        $sdForm  = $project->studyDirectorAppointmentForm;
        $sd      = $sdForm?->studyDirector;
        $sdName  = $sd ? trim(($sd->titre_personnel ?? '') . ' ' . $sd->prenom . ' ' . $sd->nom) : null;

        $headerImagePath = public_path('storage/assets/header/entete_airid.png');

        $pdf = Pdf::loadView('project-activities-pdf', compact('project', 'activitiesByCategory', 'sdName', 'headerImagePath'))
            ->setPaper('a4', 'landscape');

        // $safeCode = str_replace(['/', '\\'], '-', $project->project_code);
        $safeCode = str_replace(['/', '-'], '-', $project->project_code);
        return $pdf->download('activities-' . $safeCode . '-' . now()->format('Y-m-d') . '.pdf');
    }

    public function projectTrackingSheet(Request $request)
    {


        $all_projects = Pro_Project::orderBy("date_debut_effective", "desc")->get();

        $all_phases = Pro_Project_Phase::orderBy("level", "asc")->get();

        return view("project-tracking-sheet", compact("all_projects", "all_phases"));
    }


    /**
     * Save the project tracking sheet data.
     */
    public function saveProjectTrackingSheet(Request $request)
    {
        //any validation rules that make required when date start is provided


        $rules = [
            'project_id' => 'required|exists:pro_projects,id',
            'study_phase_id' => 'required|exists:pro_study_phases,id',
            'date_start' => 'nullable|date',
            'date_end' => 'nullable|date|after_or_equal:date_start',
        ];

        $date_update_start = null;
        $date_update_end = null;

        if ($request->input('date_start')) {
            $rules['file_evidence1'] = 'required|file';

            $date_update_start = now();
        } else {
            $rules['file_evidence1'] = 'nullable|file';
        }

        if ($request->input('date_end')) {
            $rules['file_evidence2'] = 'required|file';
            $date_update_end = now();
        } else {
            $rules['file_evidence2'] = 'nullable|file';
        }

        // Logic to save the project tracking sheet data
        // This is a placeholder; actual implementation will depend on your application logic
        $request->validate($rules);



        //declaring variables for evidence files
        $evidence1_file = null;
        $evidence2_file = null;

        //saving first the evidence files if they exist
        if ($request->hasFile('file_evidence1')) {
            // $request->file('file_evidence1')->store('evidence_files');

            // move uploaded file to the storage
            $request->file('file_evidence1')->move(public_path('storage/evidence_files'), $request->file('file_evidence1')->getClientOriginalName());

            $evidence1_file = $request->file('file_evidence1')->getClientOriginalName();
        }

        if ($request->hasFile('file_evidence2')) {
            // $request->file('file_evidence2')->store('evidence_files');

            //move uploaded file to the storage
            $request->file('file_evidence2')->move(public_path('storage/evidence_files'), $request->file('file_evidence2')->getClientOriginalName());

            $evidence2_file = $request->file('file_evidence2')->getClientOriginalName();
        } else {
            $evidence2_file = null;
        }

        //using the name of evidence files to save the project tracking sheet data
        // Assuming you have a model Pro_Project_StudyPhaseCompleted to save the data

        $project_info = Pro_Project::find($request->input('project_id'));

        //check if this record already exists in the database

        $project_tracking_sheet_exists = Pro_Project_StudyPhaseCompleted::where("project_id", $request->input("project_id"))
            ->where("study_phase_id", $request->input("study_phase_id"))
            ->first();


        if (!$project_tracking_sheet_exists) {
            $project_tracking_sheet_data = Pro_Project_StudyPhaseCompleted::create([
                'project_id' => $request->input('project_id'),
                'project_code' => $project_info->project_code,
                'study_phase_id' => $request->input('study_phase_id'),
                'evidence1_file' => $evidence1_file,
                'evidence2_file' => $evidence2_file,
                'url_evidence1_file' => "storage/evidence_files/" . $evidence1_file,
                'url_evidence2_file' => "storage/evidence_files/" . $evidence2_file,
                'date_update_start' => $date_update_start,
                'date_update_end' => $date_update_end,
                'date_start' => $request->input('date_start'),
                'date_end' => $request->input('date_end'),
                // Add more fields as needed
            ]);
        } else {

            $project_tracking_sheet_data = $project_tracking_sheet_exists->update([
                'project_id' => $request->input('project_id'),
                'project_code' => $project_info->project_code,
                'study_phase_id' => $request->input('study_phase_id'),
                'evidence1_file' => $evidence1_file,
                'evidence2_file' => $evidence2_file,
                'url_evidence1_file' => "storage/evidence_files/" . $evidence1_file,
                'url_evidence2_file' => "storage/evidence_files/" . $evidence2_file,
                'date_update_start' => $date_update_start,
                'date_update_end' => $date_update_end,
                'date_start' => $request->input('date_start'),
                'date_end' => $request->input('date_end'),
                // Add more fields as needed
            ]);
        }

        //check if the data was saved successfully
        if (!$project_tracking_sheet_data) {

            return redirect()->route("projectTrackingSheet")->with([
                'status' => 'error',
                'message' => 'Failed to save project tracking sheet data.',
            ]);
        } else {
            // If you want to return the saved data

            return redirect()->route("projectTrackingSheet")->with([
                'status' => 'success',
                'message' => 'Project tracking sheet saved successfully.',
                'data' => $project_tracking_sheet_data,
            ]);
        }
    }

}

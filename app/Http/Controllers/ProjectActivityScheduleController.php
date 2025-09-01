<?php

namespace App\Http\Controllers;

use App\Models\Pro_Project;
use App\Models\Pro_Project_Phase;
use App\Models\Pro_Project_StudyPhaseCompleted;
use Illuminate\Http\Request;

class ProjectActivityScheduleController extends Controller
{
    //

    public function masterSchedule(Request $request)
    {


        $all_projects = Pro_Project::orderBy("date_debut_effective", "desc")->get();

        $all_phases = Pro_Project_Phase::orderBy("level", "asc")->get();

        return view("master-schedule", compact("all_projects", "all_phases"));
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

<?php

namespace App\Http\Controllers;

use App\Models\Pro_Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectAjaxController extends Controller
{
    //

    function __construct()
    {
        $this->middleware('auth');
    }

    function storeProject(Request $request)
    {

        $rules = [
            "code" => "required|string|max:50",
            "title" => "required|string|max:255",
            "is_glp" => "required|boolean",
        ];

        $checkUnique = Pro_Project::where("project_code", $request->code)->first();
        if ($checkUnique) {
            return response()->json(['message' => 'The project code has already been taken.', "code_erreur" => 1], 201);
        }

        $project =  Pro_Project::create([
            'project_code' => $request->code,
            'project_title' => $request->title,
            'is_glp' => (bool) $request->is_glp
        ]);


        $data = [
            'project_id' => $project->id,
            'project_code' => $project->project_code,
            'project_title' => $project->project_title,
            'is_glp' => $project->is_glp
        ];
        return response()->json(['message' => 'Project successfully created.', 'data' => $data, "code_erreur" => 0], 201);
    }

    function saveOtherBasicInformationOnProject(Request $request)
    {


        $project_id = $request->input('project_id');

        $rules = [
            "project_id" => "required|exists:pro_projects,id",
            "project_code" => "required|string|max:255",
            "project_title" => "required|string|max:255",
            "protocol_code" => "nullable|string|max:255",
            "is_glp" => "required|boolean",
            "project_nature" => "nullable|string|max:255",
            "test_system" => "nullable|string|max:255",
            "study_director" => "nullable|numeric|exists:pro_personnels,id",
            "project_manager" => "nullable|numeric|exists:pro_personnels,id",
            "project_stage" => "nullable|string|max:255",
            "date_debut_previsionnelle" => "nullable|date",
            "date_debut_effective" => "nullable|date",
            "date_fin_previsionnelle" => "nullable|date",
            "date_fin_effective" => "nullable|date",
            "description_project" => "nullable|string",
        ];

        // $validator = Validator::make($request->all(), $rules);

        // if ($validator->fails()) {
        //     return response()->json(['errors' => $validator->errors(), "code_erreur" => 1], 200);
        // }

        $project = Pro_Project::find($project_id);

        if (!$project) {
            return response()->json(['message' => 'Project not found.', "code_erreur" => 1], 200);
        }

        // Update project with provided data
        $project->project_code = $request->input('project_code');
        $project->project_title = $request->input('project_title');
        $project->is_glp = (bool)$request->input('is_glp');
        $project->project_nature = $request->input('project_nature');
        $project->protocol_code = $request->input('protocol_code');
        $project->test_system = $request->input('test_system');
        $project->study_director = $request->input('study_director');
        $project->project_manager = $request->input('project_manager');
        $project->project_stage = $request->input('project_stage');
        $project->date_debut_previsionnelle = $request->input('date_debut_previsionnelle');
        $project->date_debut_effective = $request->input('date_debut_effective');
        $project->date_fin_previsionnelle = $request->input('date_fin_previsionnelle');
        $project->date_fin_effective = $request->input('date_fin_effective');
        $project->description_project = $request->input('description_project');
        $project->save();
        session()->flash('success', 'Project information updated successfully.');
        return response()->json(['message' => 'Project information updated successfully.', "code_erreur" => 0], 200);
    }


    public function saveStudyDirectorAppointmentForm(Request $request)
    {
        $rules = [
            "project_id" => "required|exists:pro_projects,id",
            "study_director" => "required|exists:personnels,id",
            "project_manager" => "nullable|exists:personnels,id",
            "sd_appointment_date" => "nullable|date",
            "estimated_start_date" => "nullable|date",
            "estimated_end_date" => "nullable|date",
            "sd_appointment_file" => "nullable|file|mimes:pdf",
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), "code_erreur" => 1], 200);
        }

        $project = Pro_Project::find($request->input('project_id'));

        if (!$project) {
            return response()->json(['message' => 'Project not found.', "code_erreur" => 1], 200);
        }

        $dataToUpdate = [
            'study_director' => $request->input('study_director'),
            'project_manager' => $request->input('project_manager'),
            'sd_appointment_date' => $request->input('sd_appointment_date'),
            'estimated_start_date' => $request->input('estimated_start_date'),
            'estimated_end_date' => $request->input('estimated_end_date'),
        ];

        if ($request->hasFile('sd_appointment_file')) {
            $path = $request->file('sd_appointment_file')->store('uploads', 'public');
            $dataToUpdate['sd_appointment_file'] = $path;
        }

        // Check if an appointment form already exists for this project
        $existingForm = $project->studyDirectorAppointmentForm;

        if ($existingForm) {
            // Update existing form
            $existingForm->update($dataToUpdate);
        } else {
            // Create new form
            $dataToUpdate['project_id'] = $project->id;
            \App\Models\Pro_StudyDirectorAppointmentForm::create($dataToUpdate);
        }

        session()->flash('success', 'Study Director Appointment Form saved successfully.');
        return response()->json(['message' => 'Study Director Appointment Form saved successfully.', "code_erreur" => 0], 200);
    }
}

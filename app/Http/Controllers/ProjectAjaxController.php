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

    function saveOtherBasicInformationOnProject(Request $request){

        $project_id = $request->input('project_id');

        $rules = [
            "project_id" => "required|exists:pro_projects,id",
            "project_code" => "required|string|max:255",
            "project_title" => "required|string|max:255",
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
            "description" => "nullable|string",
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), "code_erreur" => 1], 422);
        }

        $project = Pro_Project::find($project_id);

        if (!$project) {
            return response()->json(['message' => 'Project not found.', "code_erreur" => 1], 404);
        }

        // Update project with provided data
        $project->sponsor = $request->input('sponsor');
        $project->study_type = $request->input('study_type');
        $project->therapeutic_area = $request->input('therapeutic_area');
        $project->indication = $request->input('indication');
        $project->phase = $request->input('phase');
        $project->date_debut_prevue = $request->input('date_debut_prevue');
        $project->date_fin_prevue = $request->input('date_fin_prevue');
        $project->date_debut_effective = $request->input('date_debut_effective');
        $project->date_fin_effective = $request->input('date_fin_effective');
        $project->budget = $request->input('budget');
        $project->currency = $request->input('currency');
        $project->status = $request->input('status');
        $project->description = $request->input('description');
        $project->save();
        return response()->json(['message' => 'Project information updated successfully.', "code_erreur" => 0], 200);
    }

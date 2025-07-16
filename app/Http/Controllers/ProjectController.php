<?php

namespace App\Http\Controllers;

use App\Models\Pro_Personnel;
use App\Models\Pro_Project;
use App\Models\Pro_Project_Team;
use Illuminate\Http\Request;

class ProjectController extends Controller
{


    function __construct() {}
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

        $all_personnels = Pro_Personnel::all();
        return view("create-project", compact("project", "all_personnels"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            "project_code" => "required|string|unique:pro_projects,project_code",
            "project_title" => "string|required",
            "project_status" => "string|required",
            "project_nature" => "string|required",
            "test_system" => "string|required",
            "key_personnel" => "array|nullable",
            "project_title" => "string|required",
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


        $project = Pro_Project::create($request->except(["_method", "_token", "key_personnel"]));

        if ($project) {

            if ($request->key_personnel) {

                foreach ($request->key_personnel as $key => $staff_id) {
                    # code...

                    $project_team = Pro_Project_Team::create([
                        "project_id" => $project->id,
                        "staff_id" => $staff_id,
                        "role" => "",
                    ]);
                }
            }
        }
        $message = "The new Project is created successfully...";
        $all_personnels = Pro_Personnel::all();


        return view("create-project", compact("project", "all_personnels"))->with("message", $message);
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

        $all_personnels = Pro_Personnel::all();
        return view("create-project", compact("project", "all_personnels"));
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
                $delete_olds_key_personnels = Pro_Project_Team::where("project_id",$project_id)->delete();

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
        $all_personnels = Pro_Personnel::all();

        return view("create-project", compact("project", "all_personnels"))->with("message", $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pro_Project $pro_Project)
    {
        //
    }
}

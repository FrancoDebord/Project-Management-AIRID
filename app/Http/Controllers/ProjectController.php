<?php

namespace App\Http\Controllers;

use App\Models\Pro_Personnel;
use App\Models\Pro_Project;
use App\Models\Pro_Project_Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

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
        $all_projects = Pro_Project::all();

        $project_id =  $request->project_id;

        if ($project_id) {
            $project = Pro_Project::find($project_id);
        }

        $columns = Schema::getColumnListing('pro_projects');

        $total_filled_percentage_projects = $this->computeCompleteness('pro_projects', $project->id ?? 0);
        $total_filled_percentage_study_director_appointment = $this->computeCompleteness('pro_study_director_appointment_forms', $project->studyDirectorAppointmentForm->id ?? 0, ['id', 'replacement_date', 'created_at', 'updated_at',"study_director_signature","quality_assurance_signature","fm_signature","comments","sd_appointment_file"]);

        return view("study_management_design", compact("project", "all_personnels", "all_projects", "total_filled_percentage_projects", "total_filled_percentage_study_director_appointment"));
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

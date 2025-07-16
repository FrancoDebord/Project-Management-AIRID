<?php

namespace App\Http\Controllers;

use App\Models\Pro_Project;
use App\Models\Pro_Project_Phase;
use Illuminate\Http\Request;

class ProjectActivityScheduleController extends Controller
{
    //

    public function activityPage(Request $request){


        $all_projects = Pro_Project::orderBy("date_debut_effective", "desc")->get();

        $all_phases = Pro_Project_Phase::orderBy("level","asc")->get();

        return view("master-schedule",compact("all_projects","all_phases"));
    }


    public function scheduleActivityForProject(Request $request){


         $all_projects = Pro_Project::orderBy("date_debut_effective", "desc")->get();

        $all_phases = Pro_Project_Phase::orderBy("level","asc")->get();

        return view("project-tracking-sheet",compact("all_projects","all_phases"));
    }
}

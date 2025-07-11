<?php

namespace App\Http\Controllers;

use App\Models\Pro_Project;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    //

    function __construct()
    {
        
    }

    function indexPage(Request $request){

        $all_projects = Pro_Project::orderBy("date_debut_effective","desc")->get();

        return view("accueil",compact("all_projects"));

    }
}

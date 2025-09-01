<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProjectManagementController extends Controller
{
    //

    public function __construct() {

        // $this->middleware('auth');

}


function afficherManageProjectPage(Request $request)
    {
        // Logic to display the manage project page
        return view('manage-project');
    }

 
    function creerProjet(Request $request)
    {
        return view('creer-projet');
    }

}

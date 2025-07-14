<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProjectActivityScheduleController extends Controller
{
    //

    public function activityPage(Request $request){


        return view("index-project-activity-schedule");
    }


    public function scheduleActivityForProject(Request $request){


        return view("schedule-for-project");
    }
}

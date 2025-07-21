<?php

use App\Http\Controllers\FrontendController;
use App\Http\Controllers\ProjectActivityScheduleController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontendController::class,"indexPage"])->name("indexPage");
Route::get('/master-schedule', [ProjectActivityScheduleController::class,"activityPage"])->name("activityPage");
Route::get('/project-activity-schedule/{project_id?}', [ProjectActivityScheduleController::class,"scheduleActivityForProject"])->name("scheduleActivityForProject");

Route::resource("project",ProjectController::class);

require_once("route_ajax.php");
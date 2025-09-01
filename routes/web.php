<?php

use App\Http\Controllers\FrontendController;
use App\Http\Controllers\ProjectActivityScheduleController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectManagementController;
use App\Http\Controllers\WizardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontendController::class,"indexPage"])->name("indexPage");
Route::get('/master-schedule', [ProjectActivityScheduleController::class,"masterSchedule"])->name("masterSchedule");
Route::get('/project-activity-schedule/{project_id?}', [ProjectActivityScheduleController::class,"projectTrackingSheet"])->name("projectTrackingSheet");

Route::resource("project",ProjectController::class);

Route::post("/save-project-tracking-sheet",[ProjectActivityScheduleController::class,"saveProjectTrackingSheet"])->name("saveProjectTrackingSheet");

Route::get('/manage-project', [WizardController::class, 'index'])->name('index');
Route::get('/manage-project2', [ProjectManagementController::class, 'afficherManageProjectPage'])->name('afficherManageProjectPage');


// Route::get('/wizard', function(){ return view('study_management_design'); });
// Route::post('/wizard/submit', [WizardController::class, 'submit'])->name('wizard.submit');

require_once("route_ajax.php");
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

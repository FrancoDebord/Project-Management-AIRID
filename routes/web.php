<?php

use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\ProjectActivityScheduleController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectManagementController;
use App\Http\Controllers\QaDashboardController;
use App\Http\Controllers\WizardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontendController::class,"indexPage"])->name("indexPage");
Route::get('/master-schedule', [ProjectActivityScheduleController::class,"masterSchedule"])->name("masterSchedule");
Route::get('/project-activity-schedule/{project_id?}', [ProjectActivityScheduleController::class,"projectTrackingSheet"])->name("projectTrackingSheet");

Route::resource("project",ProjectController::class);

Route::post("/save-project-tracking-sheet",[ProjectActivityScheduleController::class,"saveProjectTrackingSheet"])->name("saveProjectTrackingSheet");

Route::get('/project/{id}/overview', [FrontendController::class, 'projectOverview'])->name('projectOverview');

Route::get('/manage-project', [WizardController::class, 'index'])->name('index');
Route::get('/manage-project2', [ProjectManagementController::class, 'afficherManageProjectPage'])->name('afficherManageProjectPage');


// Route::get('/wizard', function(){ return view('study_management_design'); });
// Route::post('/wizard/submit', [WizardController::class, 'submit'])->name('wizard.submit');

// ── QA Dashboard ──
Route::get('/qa-dashboard', [QaDashboardController::class, 'index'])->name('qaDashboard');
Route::get('/qa-dashboard/critical-activities', [QaDashboardController::class, 'criticalActivities'])->name('qaDashboard.criticalActivities');

// ── Checklist routes ──
Route::get('/checklist/{inspection_id}',        [ChecklistController::class, 'index'])->name('checklist.index');
Route::get('/checklist/{inspection_id}/report',        [ChecklistController::class, 'report'])->name('checklist.report');
Route::get('/checklist/{inspection_id}/followup',       [ChecklistController::class, 'followup'])->name('checklist.followup');
Route::get('/checklist/{inspection_id}/facility-print', [ChecklistController::class, 'facilityPrint'])->name('checklist.facilityPrint');
Route::get('/checklist/{inspection_id}/process-print', [ChecklistController::class, 'processPrint'])->name('checklist.processPrint');
Route::get('/checklist/{inspection_id}/study-protocol-print', [ChecklistController::class, 'studyProtocolPrint'])->name('checklist.studyProtocolPrint');
Route::get('/checklist/{inspection_id}/amendment-print', [ChecklistController::class, 'amendmentPrint'])->name('checklist.amendmentPrint');
Route::get('/checklist/{inspection_id}/{slug}', [ChecklistController::class, 'show'])->name('checklist.show');
Route::post('/checklist/{inspection_id}/{slug}',[ChecklistController::class, 'save'])->name('checklist.save');
Route::get('/ajax/get-checklist-statuses',      [ChecklistController::class, 'statuses'])->name('getChecklistStatuses');

require_once("route_ajax.php");
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

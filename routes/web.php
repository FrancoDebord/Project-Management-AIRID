<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\FmQaReviewController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\ProjectActivityScheduleController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectManagementController;
use App\Http\Controllers\QaDashboardController;
use App\Http\Controllers\SignatureController;
use App\Http\Controllers\WizardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontendController::class,"indexPage"])->name("indexPage");
Route::get('/master-schedule',      [ProjectActivityScheduleController::class, 'masterSchedule'])->name('masterSchedule');
Route::get('/master-schedule/pdf',  [ProjectActivityScheduleController::class, 'masterSchedulePdf'])->name('masterSchedule.pdf');
Route::get('/project-activity-schedule/{project_id?}', [ProjectActivityScheduleController::class,"projectTrackingSheet"])->name("projectTrackingSheet");

Route::resource("project",ProjectController::class);

Route::post("/save-project-tracking-sheet",[ProjectActivityScheduleController::class,"saveProjectTrackingSheet"])->name("saveProjectTrackingSheet");

Route::get('/project/{id}/overview', [FrontendController::class, 'projectOverview'])->name('projectOverview');
Route::get('/project/{projectId}/activities/pdf', [ProjectActivityScheduleController::class, 'projectActivitiesPdf'])->name('project.activities.pdf');
Route::get('/project/{id}/qa-activities-checklist', [FrontendController::class, 'qaActivitiesChecklist'])->name('project.qa-checklist');

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
Route::get('/checklist/{inspection_id}/study-report-print', [ChecklistController::class, 'studyReportPrint'])->name('checklist.studyReportPrint');
Route::get('/checklist/{inspection_id}/amendment-print', [ChecklistController::class, 'amendmentPrint'])->name('checklist.amendmentPrint');
Route::get('/checklist/{inspection_id}/data-quality-print', [ChecklistController::class, 'dataQualityPrint'])->name('checklist.dataQualityPrint');
Route::get('/checklist/{inspection_id}/{slug}', [ChecklistController::class, 'show'])->name('checklist.show');
Route::post('/checklist/{inspection_id}/{slug}',[ChecklistController::class, 'save'])->name('checklist.save');
Route::get('/ajax/get-checklist-statuses',      [ChecklistController::class, 'statuses'])->name('getChecklistStatuses');

// ── Notifications ──
Route::middleware('auth')->prefix('notifications')->group(function () {
    Route::get('/',           [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/unread',     [NotificationController::class, 'unreadCount'])->name('notifications.unread');
    Route::get('/latest',     [NotificationController::class, 'latest'])->name('notifications.latest');
    Route::post('/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/read-all',  [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
});

// ── Signatures ──
Route::middleware('auth')->prefix('signatures')->group(function () {
    Route::get('/',  [SignatureController::class, 'getSignatures'])->name('signatures.get');
    Route::post('/', [SignatureController::class, 'save'])->name('signatures.save');
});

// ── Facility Manager — QA Review ──
Route::prefix('fm')->middleware(['auth', 'role:super_admin,facility_manager'])->name('fm.')->group(function () {
    Route::get('/qa-review',              [FmQaReviewController::class, 'index'])->name('qa-review.index');
    Route::post('/qa-review',             [FmQaReviewController::class, 'store'])->name('qa-review.store');
    Route::get('/qa-review/{id}',         [FmQaReviewController::class, 'show'])->name('qa-review.show');
    Route::post('/qa-review/{id}/save',   [FmQaReviewController::class, 'saveResponses'])->name('qa-review.save');
    Route::post('/qa-review/{id}/complete',[FmQaReviewController::class, 'complete'])->name('qa-review.complete');
    Route::get('/qa-review/{id}/print',   [FmQaReviewController::class, 'print'])->name('qa-review.print');
    Route::delete('/qa-review/{id}',      [FmQaReviewController::class, 'destroy'])->name('qa-review.destroy');
});

// ── Admin ──
Route::prefix('admin')->middleware(['auth', 'role:super_admin'])->group(function () {
    Route::get('/users',              [AdminController::class, 'users'])->name('admin.users');
    Route::put('/users/{user}/role',  [AdminController::class, 'updateRole'])->name('admin.users.updateRole');
});

// ── Settings ──
Route::prefix('settings')->middleware(['auth', 'role:super_admin,facility_manager'])->group(function () {
    Route::get('/',    [SettingsController::class, 'index'])->name('admin.settings');
    Route::post('/',   [SettingsController::class, 'update'])->name('admin.settings.update');
});

require_once("route_ajax.php");
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

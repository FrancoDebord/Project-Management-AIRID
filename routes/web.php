<?php

use App\Http\Controllers\AdminChecklistController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminCpiaController;
use App\Http\Controllers\CpiaController;
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

Route::middleware('auth')->group(function () {

Route::get('/', [FrontendController::class,"indexPage"])->name("indexPage");
Route::get('/master-schedule',      [ProjectActivityScheduleController::class, 'masterSchedule'])->name('masterSchedule');
Route::get('/master-schedule/pdf',  [ProjectActivityScheduleController::class, 'masterSchedulePdf'])->name('masterSchedule.pdf');
Route::get('/project-activity-schedule/{project_id?}', [ProjectActivityScheduleController::class,"projectTrackingSheet"])->name("projectTrackingSheet");

Route::resource("project",ProjectController::class);

Route::post("/save-project-tracking-sheet",[ProjectActivityScheduleController::class,"saveProjectTrackingSheet"])->name("saveProjectTrackingSheet");

Route::get('/project/{id}/overview', [FrontendController::class, 'projectOverview'])->name('projectOverview');
Route::get('/projects/list',         [FrontendController::class, 'projectsList'])->name('projects.list');
Route::get('/projects/list/pdf',     [FrontendController::class, 'projectsListPdf'])->name('projects.list.pdf');
Route::get('/project/{projectId}/activities/pdf', [ProjectActivityScheduleController::class, 'projectActivitiesPdf'])->name('project.activities.pdf');
Route::get('/project/{id}/qa-activities-checklist', [FrontendController::class, 'qaActivitiesChecklist'])->name('project.qa-checklist');

Route::get('/manage-project', [WizardController::class, 'index'])->name('index');
Route::get('/manage-project2', [ProjectManagementController::class, 'afficherManageProjectPage'])->name('afficherManageProjectPage');

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

}); // end auth middleware

// ── Notifications ──
Route::middleware('auth')->prefix('notifications')->group(function () {
    Route::get('/',           [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/unread',     [NotificationController::class, 'unreadCount'])->name('notifications.unread');
    Route::get('/latest',     [NotificationController::class, 'latest'])->name('notifications.latest');
    Route::post('/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/read-all',  [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
});

// ── Signatures ──
Route::middleware('auth')->group(function () {
    Route::get('/sign/{documentType}/{documentId}', [SignatureController::class, 'showPage'])->name('sign.document');
    Route::prefix('signatures')->group(function () {
        Route::get('/',  [SignatureController::class, 'getSignatures'])->name('signatures.get');
        Route::post('/', [SignatureController::class, 'save'])->name('signatures.save');
    });
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

    // Study Director designation (distinct from the user role)
    Route::post('/study-directors/promote', [AdminController::class, 'promoteStudyDirector'])->name('admin.sd.promote');
    Route::post('/study-directors/demote',  [AdminController::class, 'demoteStudyDirector'])->name('admin.sd.demote');

    // CPIA Section/Item Management
    Route::get('/cpia',                                   [AdminCpiaController::class, 'index'])->name('admin.cpia.index');
    Route::get('/cpia/sections/{section}',                [AdminCpiaController::class, 'show'])->name('admin.cpia.show');
    Route::post('/cpia/sections/{section}/items',         [AdminCpiaController::class, 'storeItem'])->name('admin.cpia.items.store');
    Route::put('/cpia/items/{item}',                      [AdminCpiaController::class, 'updateItem'])->name('admin.cpia.items.update');
    Route::post('/cpia/items/{item}/duplicate',           [AdminCpiaController::class, 'duplicateItem'])->name('admin.cpia.items.duplicate');
    Route::post('/cpia/items/{item}/toggle',              [AdminCpiaController::class, 'toggleItem'])->name('admin.cpia.items.toggle');
    Route::delete('/cpia/items/{item}',                   [AdminCpiaController::class, 'destroyItem'])->name('admin.cpia.items.destroy');

    // Checklist Question Management
    Route::get('/checklists',                                   [AdminChecklistController::class, 'index'])->name('admin.checklists.index');
    Route::get('/checklists/{template}',                        [AdminChecklistController::class, 'show'])->name('admin.checklists.show');
    Route::post('/checklists/sections/{section}/questions',     [AdminChecklistController::class, 'storeQuestion'])->name('admin.checklists.questions.store');
    Route::put('/checklists/questions/{question}',              [AdminChecklistController::class, 'updateQuestion'])->name('admin.checklists.questions.update');
    Route::post('/checklists/questions/{question}/duplicate',   [AdminChecklistController::class, 'duplicateQuestion'])->name('admin.checklists.questions.duplicate');
    Route::post('/checklists/questions/{question}/toggle',      [AdminChecklistController::class, 'toggleQuestion'])->name('admin.checklists.questions.toggle');
    Route::delete('/checklists/questions/{question}',           [AdminChecklistController::class, 'destroyQuestion'])->name('admin.checklists.questions.destroy');
});

// ── CPIA ──
Route::middleware('auth')->group(function () {
    Route::get('/project/{project_id}/cpia',              [CpiaController::class, 'index'])->name('cpia.index');
    Route::post('/project/{project_id}/cpia/save',        [CpiaController::class, 'save'])->name('cpia.save');
    Route::post('/project/{project_id}/cpia/complete',      [CpiaController::class, 'complete'])->name('cpia.complete');
    Route::post('/project/{project_id}/cpia/revert-draft', [CpiaController::class, 'revertToDraft'])->name('cpia.revertToDraft');
    Route::get('/project/{project_id}/cpia/print',         [CpiaController::class, 'print'])->name('cpia.print');
});

// ── Settings ──
// General settings (FM + super_admin only)
Route::prefix('settings')->middleware(['auth', 'role:super_admin,facility_manager'])->group(function () {
    Route::get('/',    [SettingsController::class, 'index'])->name('admin.settings');
    Route::post('/',   [SettingsController::class, 'update'])->name('admin.settings.update');
});
// User settings (all authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/settings/user',  [SettingsController::class, 'userSettings'])->name('user.settings');
    Route::post('/settings/user', [SettingsController::class, 'updateUserSettings'])->name('user.settings.update');
});

// ── Features ──
Route::middleware('auth')->prefix('features')->name('features.')->group(function () {
    Route::get('/search',      [\App\Http\Controllers\FeaturesController::class, 'search'])->name('search');
    Route::get('/diagnostics', [\App\Http\Controllers\FeaturesController::class, 'diagnostics'])->name('diagnostics');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// ── PDF export ───────────────────────────────────────────────────────────────
Route::get('/pdf/sd-appointment-form', [FrontendController::class, 'sdAppointmentFormPdf'])
    ->middleware('auth')
    ->name('pdf.sd-appointment-form');

Route::get('/pdf/meeting-report', [FrontendController::class, 'meetingReportPdf'])
    ->middleware('auth')
    ->name('pdf.meeting-report');

Route::get('/pdf/dm/software-validation/{id}', [\App\Http\Controllers\DataManagementController::class, 'softwareValidationPdf'])
    ->middleware('auth')
    ->name('pdf.dm.software-validation');

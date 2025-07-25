<?php

use App\Http\Controllers\RequeteAjaxController;
use Illuminate\Support\Facades\Route;


Route::prefix('ajax')->group(function () {
    // Define other AJAX routes here
    Route::get('/check-project-study-phase', [RequeteAjaxController::class,"checkStudyPhaseCompleted"])->name("checkStudyPhaseCompleted");
});

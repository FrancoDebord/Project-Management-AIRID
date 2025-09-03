<?php

use App\Http\Controllers\ProjectAjaxController;
use App\Http\Controllers\RequeteAjaxController;
use Illuminate\Support\Facades\Route;


Route::prefix('ajax')->group(function () {
    // Define other AJAX routes here
    Route::get('/check-project-study-phase', [RequeteAjaxController::class,"checkStudyPhaseCompleted"])->name("checkStudyPhaseCompleted");
    Route::post('/store-project', [ProjectAjaxController::class,"storeProject"])->name("storeProject");
    Route::post('/store-detailed-information-project', [ProjectAjaxController::class,"saveOtherBasicInformationOnProject"])->name("saveOtherBasicInformationOnProject");
    Route::post('/store-study-director-appointment-form', [ProjectAjaxController::class,"saveStudyDirectorAppointmentForm"])->name("saveStudyDirectorAppointmentForm");
    Route::post('/store-study-director-replacement-form', [ProjectAjaxController::class,"saveStudyDirectorReplacementForm"])->name("saveStudyDirectorReplacementForm");
    Route::post('/store-other-basic-documents', [ProjectAjaxController::class,"saveOtherBasicDocuments"])->name("saveOtherBasicDocuments");
});

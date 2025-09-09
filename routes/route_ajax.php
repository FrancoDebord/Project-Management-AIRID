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
    Route::post('/store-activity-project', [ProjectAjaxController::class,"saveActivityProject"])->name("saveActivityProject");
    Route::post('/delete-activity-project', [ProjectAjaxController::class,"supprimerActivite"])->name("supprimerActivite");
    Route::post('/generate-protocol-dev-activity-project', [ProjectAjaxController::class,"generateProtocolDevActivitiesForProject"])->name("generateProtocolDevActivitiesForProject");
    Route::post('/update-protocol-dev-activity-project', [ProjectAjaxController::class,"saveProtocolDevelopmentActivityCompleted"])->name("saveProtocolDevelopmentActivityCompleted");



    Route::get('/get-study-type/{id}', [ProjectAjaxController::class,"getStudyTypeById"])->name("getStudyTypeById");
    Route::get('/get-all-chidren-activities', [ProjectAjaxController::class,"childrenActivity"])->name("childrenActivity");
});

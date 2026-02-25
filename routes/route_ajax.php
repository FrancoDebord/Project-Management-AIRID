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
    Route::post('/execute-activity', [ProjectAjaxController::class,"executeActivity"])->name("executeActivity");
    Route::post('/reset-activity', [ProjectAjaxController::class,"resetActivityExecution"])->name("resetActivityExecution");
    
    Route::post('/schedule-meeting', [ProjectAjaxController::class,"scheduleStudyInitiationMeeting"])->name("scheduleStudyInitiationMeeting");


    Route::get('/get-study-type/{id}', [ProjectAjaxController::class,"getStudyTypeById"])->name("getStudyTypeById");
    Route::get('/get-all-chidren-activities', [ProjectAjaxController::class,"childrenActivity"])->name("childrenActivity");
    
    Route::get('/get-meeting-info', [ProjectAjaxController::class,"getMeetingInfoById"])->name("getMeetingInfoById");
    Route::post('/delete-meeting', [ProjectAjaxController::class,"deleteQAMeeting"])->name("deleteQAMeeting");
    Route::post('/marquer-critique', [ProjectAjaxController::class,"marquerActivitePhaseCritique"])->name("marquerActivitePhaseCritique");
    Route::post('/marquer-non-critique', [ProjectAjaxController::class,"marquerActiviteNonPhaseCritique"])->name("marquerActiviteNonPhaseCritique");
    Route::post('/schedule-qa-inspection', [ProjectAjaxController::class,"scheduleQaInspection"])->name("scheduleQaInspection");
    Route::get('/get-inspection-findings', [ProjectAjaxController::class,"getInspectionFindings"])->name("getInspectionFindings");
    Route::post('/save-qa-finding', [ProjectAjaxController::class,"saveQaFinding"])->name("saveQaFinding");
    Route::post('/resolve-qa-finding', [ProjectAjaxController::class,"resolveQaFinding"])->name("resolveQaFinding");
    Route::post('/delete-qa-inspection', [ProjectAjaxController::class,"deleteQaInspection"])->name("deleteQaInspection");
    Route::post('/delete-qa-finding', [ProjectAjaxController::class,"deleteQaFinding"])->name("deleteQaFinding");
    Route::post('/delete-corrective-action', [ProjectAjaxController::class,"deleteCorrectiveAction"])->name("deleteCorrectiveAction");
});

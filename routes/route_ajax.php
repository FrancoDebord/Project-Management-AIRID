<?php

use App\Http\Controllers\ProjectAjaxController;
use App\Http\Controllers\RequeteAjaxController;
use App\Http\Controllers\SignatureController;
use Illuminate\Support\Facades\Route;


Route::prefix('ajax')->group(function () {
    // Define other AJAX routes here
    Route::get('/check-project-study-phase', [RequeteAjaxController::class,"checkStudyPhaseCompleted"])->name("checkStudyPhaseCompleted");
    Route::post('/store-project', [ProjectAjaxController::class,"storeProject"])->name("storeProject");
    Route::post('/store-detailed-information-project', [ProjectAjaxController::class,"saveOtherBasicInformationOnProject"])->name("saveOtherBasicInformationOnProject");
    Route::post('/store-study-director-appointment-form', [ProjectAjaxController::class,"saveStudyDirectorAppointmentForm"])->name("saveStudyDirectorAppointmentForm");
    Route::post('/store-study-director-replacement-form', [ProjectAjaxController::class,"saveStudyDirectorReplacementForm"])->name("saveStudyDirectorReplacementForm");
    Route::post('/store-other-basic-documents', [ProjectAjaxController::class,"saveOtherBasicDocuments"])->name("saveOtherBasicDocuments");
    Route::post('/save-key-personnel',   [ProjectAjaxController::class, 'saveKeyPersonnel'])->name('saveKeyPersonnel');
    Route::post('/add-key-personnel',    [ProjectAjaxController::class, 'addKeyPersonnelMember'])->name('addKeyPersonnelMember');
    Route::post('/remove-key-personnel', [ProjectAjaxController::class, 'removeKeyPersonnelMember'])->name('removeKeyPersonnelMember');
    Route::post('/store-activity-project', [ProjectAjaxController::class,"saveActivityProject"])->name("saveActivityProject");
    Route::post('/delete-activity-project', [ProjectAjaxController::class,"supprimerActivite"])->name("supprimerActivite");
    Route::post('/generate-protocol-dev-activity-project', [ProjectAjaxController::class,"generateProtocolDevActivitiesForProject"])->name("generateProtocolDevActivitiesForProject");
    Route::post('/update-protocol-dev-activity-project', [ProjectAjaxController::class,"saveProtocolDevelopmentActivityCompleted"])->name("saveProtocolDevelopmentActivityCompleted");
    Route::post('/delete-protocol-dev-document',         [ProjectAjaxController::class,"deleteProtocolDevDocument"])->name("deleteProtocolDevDocument");
    Route::post('/delete-protocol-dev-document-entry',  [ProjectAjaxController::class,"deleteProtocolDevDocumentEntry"])->name("deleteProtocolDevDocumentEntry");
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
    Route::post('/update-qa-inspection', [ProjectAjaxController::class,"updateQaInspection"])->name("updateQaInspection");
    Route::get('/get-inspection-findings', [ProjectAjaxController::class,"getInspectionFindings"])->name("getInspectionFindings");
    Route::post('/save-qa-finding', [ProjectAjaxController::class,"saveQaFinding"])->name("saveQaFinding");
    Route::post('/resolve-qa-finding', [ProjectAjaxController::class,"resolveQaFinding"])->name("resolveQaFinding");
    Route::post('/delete-qa-inspection', [ProjectAjaxController::class,"deleteQaInspection"])->name("deleteQaInspection");
    Route::post('/mark-inspection-done', [ProjectAjaxController::class,"markInspectionDone"])->name("markInspectionDone");
    Route::post('/toggle-inspection-completed', [ProjectAjaxController::class,"toggleInspectionCompleted"])->name("toggleInspectionCompleted");
    Route::post('/toggle-phase-completed', [ProjectAjaxController::class,"togglePhaseCompleted"])->name("togglePhaseCompleted");
    Route::post('/delete-qa-finding', [ProjectAjaxController::class,"deleteQaFinding"])->name("deleteQaFinding");
    Route::post('/update-qa-finding', [ProjectAjaxController::class,"updateQaFinding"])->name("updateQaFinding");
    Route::post('/delete-corrective-action', [ProjectAjaxController::class,"deleteCorrectiveAction"])->name("deleteCorrectiveAction");

    // Report Phase
    Route::post('/save-report-document',   [ProjectAjaxController::class, 'saveReportDocument'])->name('saveReportDocument');
    Route::post('/update-report-document', [ProjectAjaxController::class, 'updateReportDocument'])->name('updateReportDocument');
    Route::post('/delete-report-document', [ProjectAjaxController::class, 'deleteReportDocument'])->name('deleteReportDocument');

    // Project stage
    Route::patch('/project/{project}/stage',  [ProjectAjaxController::class, 'updateProjectStage'])->name('project.updateStage');

    // Archiving Phase
    Route::post('/archive-project',           [ProjectAjaxController::class, 'archiveProject'])->name('archiveProject');
    Route::post('/unarchive-project',         [ProjectAjaxController::class, 'unarchiveProject'])->name('unarchiveProject');
    Route::post('/save-archive-checklist',    [ProjectAjaxController::class, 'saveArchiveChecklist'])->name('saveArchiveChecklist');
    Route::post('/save-archiving-document',   [ProjectAjaxController::class, 'saveArchivingDocument'])->name('saveArchivingDocument');
    Route::post('/delete-archiving-document',  [ProjectAjaxController::class, 'deleteArchivingDocument'])->name('deleteArchivingDocument');
    Route::post('/save-archive-submission',    [ProjectAjaxController::class, 'saveArchiveSubmission'])->name('saveArchiveSubmission');

    // QA Statement
    Route::post('/save-qa-statement',   [ProjectAjaxController::class, 'saveQaStatement'])->name('saveQaStatement');
    Route::get('/qa-statement/print',   [ProjectAjaxController::class, 'printQaStatement'])->name('printQaStatement');

    // QA Activities Checklist
    Route::post('/save-qa-activities-checklist',  [ProjectAjaxController::class, 'saveQaActivitiesChecklist'])->name('saveQaActivitiesChecklist');
    Route::get('/qa-activities-checklist/print',  [ProjectAjaxController::class, 'printQaActivitiesChecklist'])->name('printQaActivitiesChecklist');

    // Electronic signatures
    Route::get('/signatures',           [SignatureController::class, 'getSignatures'])->name('ajax.signatures.get');
    Route::post('/save-signature',      [SignatureController::class, 'save'])->name('ajax.signatures.save');
});

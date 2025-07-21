<?php

namespace App\Http\Controllers;

use App\Models\Pro_Project_Phase;
use App\Models\Pro_Project_StudyPhaseCompleted;
use Illuminate\Http\Request;

class RequeteAjaxController extends Controller
{
    //

    function checkStudyPhaseCompleted(Request $request)
    {
        $projectId = $request->input('project_id');
        $studyPhaseId = $request->input('study_phase_id');

        // Logic to check if the study phase is completed for the given project
        // This is a placeholder; actual implementation will depend on your application logic


        $studyPhaseInfo = Pro_Project_Phase::find($studyPhaseId);
            

        $infos_study_phase_completed = \App\Models\Pro_Project_StudyPhaseCompleted::where('project_id', $projectId)
            ->where('study_phase_id', $studyPhaseId)
            ->first();

        if (!$infos_study_phase_completed) {
            return response()->json([
                'status' => 'success',
                'message' => 'Study phase completion status checked successfully.',
                'data' => [
                    'project_id' => $projectId,
                    'study_phase_id' => $studyPhaseId,
                    'evidence1_file' => $infos_study_phase_completed->evidence1_file ?? null,
                    'evidence2_file' => $infos_study_phase_completed->evidence2_file ?? null,
                    'date_start' => $infos_study_phase_completed->date_start ?? null,
                    'date_end' => $infos_study_phase_completed->date_end ?? null,
                    'date_update_start' => $infos_study_phase_completed->date_update_start ?? null,
                    'date_update_end' => $infos_study_phase_completed->date_update_end ?? null,
                    'studyPhaseInfo' => $studyPhaseInfo,
                    // Add more data as needed
                ]
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Study phase completion not found for the given project and study phase.',
            ], 404);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Pro_OtherBasicDocument;
use App\Models\Pro_Project;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Validator;

class ProjectAjaxController extends Controller
{
    //

    function __construct()
    {
        $this->middleware('auth');
    }

    function storeProject(Request $request)
    {

        $rules = [
            "code" => "required|string|max:50",
            "title" => "required|string|max:255",
            "is_glp" => "required|boolean",
        ];

        $checkUnique = Pro_Project::where("project_code", $request->code)->first();
        if ($checkUnique) {
            return response()->json(['message' => 'The project code has already been taken.', "code_erreur" => 1], 201);
        }

        $project =  Pro_Project::create([
            'project_code' => $request->code,
            'project_title' => $request->title,
            'is_glp' => (bool) $request->is_glp
        ]);


        $data = [
            'project_id' => $project->id,
            'project_code' => $project->project_code,
            'project_title' => $project->project_title,
            'is_glp' => $project->is_glp
        ];
        return response()->json(['message' => 'Project successfully created.', 'data' => $data, "code_erreur" => 0], 201);
    }

    function saveOtherBasicInformationOnProject(Request $request)
    {


        $project_id = $request->input('project_id');

        $rules = [
            "project_id" => "required|exists:pro_projects,id",
            "project_code" => "required|string|max:255",
            "project_title" => "required|string|max:255",
            "protocol_code" => "nullable|string|max:255",
            "is_glp" => "required|boolean",
            "project_nature" => "nullable|string|max:255",
            "test_system" => "nullable|string|max:255",
            "study_director" => "nullable|numeric|exists:pro_personnels,id",
            "project_manager" => "nullable|numeric|exists:pro_personnels,id",
            "project_stage" => "nullable|string|max:255",
            "date_debut_previsionnelle" => "nullable|date",
            "date_debut_effective" => "nullable|date",
            "date_fin_previsionnelle" => "nullable|date",
            "date_fin_effective" => "nullable|date",
            "description_project" => "nullable|string",
        ];


        if (!$request->input('project_code')) {
            return response()->json(['message' => 'The project code is required.', "code_erreur" => 1], 200);
        }
        $checkUnique = Pro_Project::where("project_code", $request->input('project_code'))
            ->where("id", "<>", $project_id)
            ->first();
        if ($checkUnique) {
            return response()->json(['message' => 'The project code has already been taken.', "code_erreur" => 1], 200);
        }

        if (!$request->input('project_title')) {
            return response()->json(['message' => 'The project title is required.', "code_erreur" => 1], 200);
        }
        if ($request->input('date_debut_previsionnelle') && $request->input('date_debut_effective') && $request->input('date_debut_previsionnelle') > $request->input('date_debut_effective')) {
            return response()->json(['message' => 'The planned start date cannot be later than the actual start date.', "code_erreur" => 1], 200);
        }
        if ($request->input('date_fin_previsionnelle') && $request->input('date_fin_effective') && $request->input('date_fin_previsionnelle') > $request->input('date_fin_effective')) {
            return response()->json(['message' => 'The planned end date cannot be later than the actual end date.', "code_erreur" => 1], 200);
        }
        if ($request->input('date_debut_previsionnelle') && $request->input('date_fin_previsionnelle') && $request->input('date_debut_previsionnelle') > $request->input('date_fin_previsionnelle')) {
            return response()->json(['message' => 'The planned start date cannot be later than the planned end date.', "code_erreur" => 1], 200);
        }
        if ($request->input('date_debut_effective') && $request->input('date_fin_effective') && $request->input('date_debut_effective') > $request->input('date_fin_effective')) {
            return response()->json(['message' => 'The actual start date cannot be later than the actual end date.', "code_erreur" => 1], 200);
        }
        if ($request->input('study_director') && $request->input('project_manager') && $request->input('study_director') == $request->input('project_manager')) {
            return response()->json(['message' => 'The study director must be different from the project manager.', "code_erreur" => 1], 200);
        }


        $project = Pro_Project::find($project_id);

        if (!$project) {
            return response()->json(['message' => 'Project not found.', "code_erreur" => 1], 200);
        }

        // Update project with provided data
        $project->project_code = $request->input('project_code');
        $project->project_title = $request->input('project_title');
        $project->is_glp = (bool)$request->input('is_glp');
        $project->project_nature = $request->input('project_nature');
        $project->protocol_code = $request->input('protocol_code');
        $project->test_system = $request->input('test_system');
        $project->study_director = $request->input('study_director');
        $project->project_manager = $request->input('project_manager');
        $project->project_stage = $request->input('project_stage');
        $project->date_debut_previsionnelle = $request->input('date_debut_previsionnelle');
        $project->date_debut_effective = $request->input('date_debut_effective');
        $project->date_fin_previsionnelle = $request->input('date_fin_previsionnelle');
        $project->date_fin_effective = $request->input('date_fin_effective');
        $project->description_project = $request->input('description_project');
        $project->save();
        session()->flash('success', 'Project information updated successfully.');
        return response()->json(['message' => 'Project information updated successfully.', "code_erreur" => 0], 200);
    }

    /**
     * Save Study Director Appointment Form
     */

    public function saveStudyDirectorAppointmentForm(Request $request)
    {
        $rules = [
            "project_id" => "required|exists:pro_projects,id",
            "study_director" => "required|exists:personnels,id",
            "project_manager" => "nullable|exists:personnels,id",
            "sd_appointment_date" => "nullable|date",
            "estimated_start_date" => "nullable|date",
            "estimated_end_date" => "nullable|date",
            "sd_appointment_file" => "nullable|file|mimes:pdf",
        ];




        if (!$request->input('study_director')) {
            return response()->json(['message' => 'The study director is required.', "code_erreur" => 1], 200);
        }

        if (!$request->input('sd_appointment_date')) {
            return response()->json(['message' => 'The study director appointment date is required.', "code_erreur" => 1], 200);
        }


        if ($request->hasFile('sd_appointment_file')) {
            $file = $request->file('sd_appointment_file');
            if (!$file->isValid()) {
                return response()->json(['message' => 'The uploaded file is not valid.', "code_erreur" => 1], 200);
            }
        } else {
            return response()->json(['message' => 'The signed Study Director Appointment Form (PDF) is required.', "code_erreur" => 1], 200);
        }


        if ($request->input('estimated_start_date') && $request->input('estimated_end_date') && $request->input('estimated_start_date') > $request->input('estimated_end_date')) {
            return response()->json(['message' => 'The estimated start date cannot be later than the estimated end date.', "code_erreur" => 1], 200);
        }


        if ($request->input('study_director') && $request->input('project_manager') && $request->input('study_director') == $request->input('project_manager')) {
            return response()->json(['message' => 'The study director must be different from the project manager.', "code_erreur" => 1], 200);
        }

        $project = Pro_Project::find($request->input('project_id'));

        if (!$project) {
            return response()->json(['message' => 'Project not found.', "code_erreur" => 1], 200);
        }

        $dataToUpdate = [
            'study_director' => $request->input('study_director'),
            'project_manager' => $request->input('project_manager'),
            'sd_appointment_date' => $request->input('sd_appointment_date'),
            'estimated_start_date' => $request->input('estimated_start_date'),
            'estimated_end_date' => $request->input('estimated_end_date'),
        ];

        if ($request->hasFile('sd_appointment_file')) {
            $path = $request->file('sd_appointment_file')->store('uploads', 'public');
            $dataToUpdate['sd_appointment_file'] = $path;
        }

        // Check if an appointment form already exists for this project
        $existingForm = $project->studyDirectorAppointmentForm;

        if ($existingForm) {
            // Update existing form
            $existingForm->update($dataToUpdate);
        } else {
            // Create new form
            $dataToUpdate['project_id'] = $project->id;
            \App\Models\Pro_StudyDirectorAppointmentForm::create($dataToUpdate);
        }

        session()->flash('success', 'Study Director Appointment Form saved successfully.');
        return response()->json(['message' => 'Study Director Appointment Form saved successfully.', "code_erreur" => 0], 200);
    }


    /**
     * Save Study Director Replacement Form
     */

    public function saveStudyDirectorReplacementForm(Request $request)
    {
        $rules = [
            "project_id" => "required|exists:pro_projects,id",
            "study_director" => "required|exists:personnels,id",
            "project_manager" => "nullable|exists:personnels,id",
            "replacement_reason" => "nullable|string",
            "replacement_date" => "nullable|date",
            "sd_appointment_file" => "nullable|file|mimes:pdf",
        ];

        // $validator = Validator::make($request->all(), $rules);

        // if ($validator->fails()) {
        //     return response()->json(['errors' => $validator->errors(), "code_erreur" => 1], 200);
        // }
        if (!$request->input('study_director')) {
            return response()->json(['message' => 'The new study director is required.', "code_erreur" => 1], 200);
        }


        $project = Pro_Project::find($request->input('project_id'));

        if (!$project) {
            return response()->json(['message' => 'Project not found.', "code_erreur" => 1], 200);
        }

        if ($request->input('study_director') && $request->input('project_manager') && $request->input('study_director') == $request->input('project_manager')) {
            return response()->json(['message' => 'The study director must be different from the project manager.', "code_erreur" => 1], 200);
        }
        if ($request->hasFile('sd_appointment_file')) {
            $file = $request->file('sd_appointment_file');
            if (!$file->isValid()) {
                return response()->json(['message' => 'The uploaded file is not valid.', "code_erreur" => 1], 200);
            }
        } else {
            return response()->json(['message' => 'The signed Study Director Replacement Form (PDF) is required.', "code_erreur" => 1], 200);
        }


        // Check if an appointment form already exists for this project
        $existingForm = $project->studyDirectorAppointmentForm;
        if (!$existingForm) {
            return response()->json(['message' => 'No existing Study Director Appointment Form found for this project.', "code_erreur" => 1], 200);
        }

        $oldStudyDirectorId = $existingForm->study_director;
        if ($oldStudyDirectorId == $request->input('study_director')) {
            return response()->json(['message' => 'The new Study Director must be different from the current one.', "code_erreur" => 1], 200);
        }

        $oldSdAppointmentDate = $existingForm->sd_appointment_date;
        if ($oldSdAppointmentDate && $request->input('replacement_date') && $request->input('replacement_date') < $oldSdAppointmentDate) {
            return response()->json(['message' => 'The replacement date cannot be earlier than the original appointment date.', "code_erreur" => 1], 200);
        }
        if ($request->input('replacement_date') && $existingForm->estimated_end_date && $request->input('replacement_date') > $existingForm->estimated_end_date) {
            return response()->json(['message' => 'The replacement date cannot be later than the estimated end date of the study.', "code_erreur" => 1], 200);
        }
        if (!$request->input('replacement_date')) {
            return response()->json(['message' => 'The replacement date is required.', "code_erreur" => 1], 200);
        }
        if (!$request->input('replacement_reason')) {
            return response()->json(['message' => 'The replacement reason is required.', "code_erreur" => 1], 200);
        }


        // Update project with new study director
        $dataToUpdate = [
            'study_director' => $request->input('study_director'),
            'project_manager' => $request->input('project_manager'),
            'sd_appointment_date' => $request->input('replacement_date'), // Using replacement date as new appointment date of the new SD
        ];

        if ($request->hasFile('sd_appointment_file')) {
            $path = $request->file('sd_appointment_file')->store('uploads', 'public');
            $dataToUpdate['sd_appointment_file'] = $path;
        }


        if ($existingForm) {
            // Update existing form
            $existingForm->update(['active' => false, "replacement_date" => $request->input('replacement_date'), "replacement_reason" => $request->input('replacement_reason')]); // Mark existing as inactive

            $dataToUpdate['estimated_start_date'] = $existingForm->estimated_start_date;
            $dataToUpdate['estimated_end_date'] = $existingForm->estimated_end_date;
            $dataToUpdate['active'] = true;

            // Create new form
            $dataToUpdate['project_id'] = $project->id;
            \App\Models\Pro_StudyDirectorAppointmentForm::create($dataToUpdate);
        }

        session()->flash('success', 'Study Director Replacement Form saved successfully.');
        return response()->json(['message' => 'Study Director Replacement Form saved successfully.', "code_erreur" => 0], 200);
    }


    /**
     * Save Other Basic Documents for a Project
     */

    function saveOtherBasicDocuments(Request $request)
    {


        $rules = [
            "project_id" => "required|exists:pro_projects,id",
            "titre_document" => "required|string|max:255",
            "description_document" => "nullable|string",
            "document_file" => "required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png",
            "upload_date" => "nullable|date",
            "uploaded_by" => "nullable|string|max:255",
            "document_type" => "nullable|string|max:255",
        ];

        if (!$request->input('titre_document')) {
            return response()->json(['message' => 'The document title is required.', "code_erreur" => 1], 200);
        }

        if (!$request->hasFile('document_file')) {
            return response()->json(['message' => 'The document file is required.', "code_erreur" => 1], 200);
        }

        $project = Pro_Project::find($request->input('project_id'));

        if (!$project) {
            return response()->json(['message' => 'Project not found.', "code_erreur" => 1], 200);
        }

        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            if (!$file->isValid()) {
                return response()->json(['message' => 'The uploaded file is not valid.', "code_erreur" => 1], 200);
            }
            $path = $file->store('other_basic_documents', 'public');
        } else {
            return response()->json(['message' => 'The document file is required.', "code_erreur" => 1], 200);
        }

        $documentData = [
            'project_id' => $project->id,
            'titre_document' => $request->input('titre_document'),
            'description_document' => $request->input('description_document'),
            'document_file_path' => $path,
            'upload_date' => now(),
            'uploaded_by' => FacadesAuth::user() ? FacadesAuth::user()->id : null,
            'document_type' => "other basic",
        ];

        Pro_OtherBasicDocument::create($documentData);

        session()->flash('success', 'Other Basic Document saved successfully.');
        return response()->json(['message' => 'Other Basic Document saved successfully.', "code_erreur" => 0], 200);
    }

    function getStudyTypeById(Request $request)
    {
        $study_type_id = $request->input('study_type_id');
        $project_id = $request->input('project_id');

        if (!$study_type_id) {
            return response()->json(['message' => 'The study type ID is required.', "code_erreur" => 1], 200);
        }
        if (!$project_id) {
            return response()->json(['message' => 'The project ID is required.', "code_erreur" => 1], 200);
        }

        $study_type = \App\Models\Pro_StudyType::find($study_type_id);
        if (!$study_type) {
            return response()->json(['message' => 'Study type not found.', "code_erreur" => 1], 200);
        }

        $all_sub_categories = $study_type->allSubCategories;

        $project = Pro_Project::find($project_id);
        if (!$project) {
            return response()->json(['message' => 'Project not found.', "code_erreur" => 1], 200);
        }

        $all_activities = $project->allActivitiesProject($study_type_id)->get();

        return response()->json(['message' => 'Study type retrieved successfully.', 'study_type' => $study_type, 'sub_categories' => $all_sub_categories, 'all_activities' => $all_activities, "code_erreur" => 0], 200);
    }



    /**
     * Save the activity for a project
     */
    function saveActivityProject(Request $request)
    {

        $rules = [
            "project_id" => "required|exists:pro_projects,id",
            "study_type_id" => "required|exists:pro_studies_types,id",
            "study_sub_category_id" => "nullable|exists:pro_studies_types_sub_categories,id",
            "parent_activity_id" => "nullable|exists:pro_study_activities,id",
            "study_activity_name" => "required|string|max:255",
            "description" => "nullable|string",
            "estimated_activity_date" => "nullable|date",
            "should_be_performed_by" => "nullable|exists:personnels,id",
        ];



        if (!$request->input('estimated_activity_date')) {
            return response()->json(['message' => 'The estimated activity date is required.', "code_erreur" => 1], 200);
        }


        if (!$request->input('project_id')) {
            return response()->json(['message' => 'The project ID for the activity is required.', "code_erreur" => 1], 200);
        }

        if (!$request->input('study_type_id')) {
            return response()->json(['message' => 'The study type for the activity is required.', "code_erreur" => 1], 200);
        }

        if (!$request->input('study_sub_category_id')) {
            return response()->json(['message' => 'The study sub-category for the activity is required.', "code_erreur" => 1], 200);
        }

        if (!$request->input('study_activity_name')) {
            return response()->json(['message' => 'The activity name is required.', "code_erreur" => 1], 200);
        }

        if ($request->input('parent_activity_id')) {
            $parent_activity = \App\Models\Pro_StudyActivities::find($request->input('parent_activity_id'));
            if ($parent_activity) {

                if ($parent_activity->activity_name == $request->input('activity_name')) {
                    return response()->json(['message' => 'The activity cannot be its own parent.', "code_erreur" => 1], 200);
                }

                if (Carbon::parse($request->estimated_activity_date) >= $parent_activity->estimated_activity_date) {
                    return response()->json(['message' => "The Due date of the new activity shall be inferior or equal to its parent activity's", "code_erreur" => 1], 200);
                }
            }
        }

        $project = Pro_Project::find($request->input('project_id'));

        if (!$project) {
            return response()->json(['message' => 'Project not found.', "code_erreur" => 1], 200);
        }

        $study_type = \App\Models\Pro_StudyType::find($request->input('study_type_id'));
        if (!$study_type) {
            return response()->json(['message' => 'Study type not found.', "code_erreur" => 1], 200);
        }

        $study_sub_category = \App\Models\Pro_StudyTypeSubCategory::find($request->input('study_sub_category_id'));
        if (!$study_sub_category) {
            return response()->json(['message' => 'Study sub-category not found.', "code_erreur" => 1], 200);
        }

        $activityData = [
            'project_id' => $project->id,
            'study_type_id' => $request->input('study_type_id'),
            'study_sub_category_id' => $request->input('study_sub_category_id'),
            'parent_activity_id' => $request->input('parent_activity_id') ?? null,
            'study_activity_name' => $request->input('study_activity_name'),
            'activity_description' => $request->input('activity_description'),
            'estimated_activity_date' => $request->input('estimated_activity_date'),
            'should_be_performed_by' => $request->input('should_be_performed_by') ?? null,
            'created_by' => FacadesAuth::id(),
            'status' => 'pending',
        ];

        \App\Models\Pro_StudyActivities::create($activityData);

        session()->flash('success', 'Activity added to project successfully.');
        return response()->json(['message' => 'Activity added to project successfully.', "code_erreur" => 0], 200);
    }
}

<?php

namespace App\Http\Controllers;

use App\Mail\MeetingMail;
use App\Models\Pro_KeyFacilityPersonnel;
use App\Models\Pro_OtherBasicDocument;
use App\Models\Pro_Personnel;
use App\Models\Pro_Project;
use App\Models\Pro_ProjectRelatedLabTest;
use App\Models\Pro_ProjectRelatedProductType;
use App\Models\Pro_ProjectRelatedStudyType;
use App\Models\Pro_ProtocolDevActivity;
use App\Models\Pro_ProtocolDevActivityProject;
use App\Models\Pro_StudyActivities;
use App\Models\Pro_QaInspection;
use App\Models\Pro_QaInspectionFinding;
use App\Models\Pro_StudyQualityAssuranceMeeting;
use App\Models\Pro_StudyQualityAssuranceMeetingParticipant;
use Carbon\Carbon;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Mail;
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
            "study_type_id" => "required|array",
            "product_type_id" => "required|array",
            "lab_test_id" => "required|array",
        ];

        $checkUnique = Pro_Project::where("project_code", $request->code)->first();
        if ($checkUnique) {
            return response()->json(['message' => 'The project code has already been taken.', "code_erreur" => 1], 201);
        }

        $checkUnique = Pro_Project::where("project_title", $request->title)->first();
        if ($checkUnique) {
            return response()->json(['message' => 'The project title has already been taken.', "code_erreur" => 1], 201);
        }

        if (!$request->input('study_type_id')) {
            return response()->json(['message' => 'The Study Type is required.', "code_erreur" => 1], 200);
        }
        if (!$request->input('product_type_id')) {
            return response()->json(['message' => 'The Evaluation Product Type is required.', "code_erreur" => 1], 200);
        }

        if (!$request->input('lab_test_id')) {
            return response()->json(['message' => 'The Lab Test appliable is required.', "code_erreur" => 1], 200);
        }

        $project =  Pro_Project::create([
            'project_code' => $request->code,
            'project_title' => $request->title,
            'is_glp' => (bool) $request->is_glp
        ]);

        if ($project) {

            $all_study_types = $request->input('study_type_id');
            $all_products_types = $request->input('product_type_id');
            $all_lab_tests = $request->input('lab_test_id');

            foreach ($all_study_types as $key => $study_type_id) {

                $project_related =  Pro_ProjectRelatedStudyType::create([
                    'project_id' => $project->id,
                    'study_type_id' => $study_type_id,
                ]);
            }

            foreach ($all_products_types as $key => $product_type_id) {

                $project_related =  Pro_ProjectRelatedProductType::create([
                    'project_id' => $project->id,
                    'product_type_id' => $product_type_id,
                ]);
            }

            foreach ($all_lab_tests as $key => $lab_test_id) {

                $project_related =  Pro_ProjectRelatedLabTest::create([
                    'project_id' => $project->id,
                    'lab_test_id' => $lab_test_id,
                ]);
            }
        }

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
        // if ($request->input('study_director') && $request->input('project_manager') && $request->input('study_director') == $request->input('project_manager')) {
        //     return response()->json(['message' => 'The study director must be different from the project manager.', "code_erreur" => 1], 200);
        // }

        if (!$request->input('study_type_id')) {
            return response()->json(['message' => 'The Study Type is required.', "code_erreur" => 1], 200);
        }
        if (!$request->input('product_type_id')) {
            return response()->json(['message' => 'The Evaluation Product Type is required.', "code_erreur" => 1], 200);
        }

        if (!$request->input('lab_test_id')) {
            return response()->json(['message' => 'The Lab Test appliable is required.', "code_erreur" => 1], 200);
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


        //suppression des anciennes données 
        $delete_olds_study_types = Pro_ProjectRelatedStudyType::where("project_id", $project->id)->delete();
        $delete_olds_product_types = Pro_ProjectRelatedProductType::where("project_id", $project->id)->delete();
        $delete_olds_lab_tests = Pro_ProjectRelatedLabTest::where("project_id", $project->id)->delete();

        $all_study_types = $request->input('study_type_id');
        $all_products_types = $request->input('product_type_id');
        $all_lab_tests = $request->input('lab_test_id');

        foreach ($all_study_types as $key => $study_type_id) {

            $project_related =  Pro_ProjectRelatedStudyType::create([
                'project_id' => $project->id,
                'study_type_id' => $study_type_id,
            ]);
        }

        foreach ($all_products_types as $key => $product_type_id) {

            $project_related =  Pro_ProjectRelatedProductType::create([
                'project_id' => $project->id,
                'product_type_id' => $product_type_id,
            ]);
        }

        foreach ($all_lab_tests as $key => $lab_test_id) {

            $project_related =  Pro_ProjectRelatedLabTest::create([
                'project_id' => $project->id,
                'lab_test_id' => $lab_test_id,
            ]);
        }

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
            'uploaded_by' => FacadesAuth::user() ? FacadesAuth::user()->personnel->id : null,
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

        $all_personnels = Pro_Personnel::orderBy("prenom", "asc")->get();

        return response()->json(['message' => 'Study type retrieved successfully.', 'study_type' => $study_type, 'sub_categories' => $all_sub_categories, 'all_activities' => $all_activities, "all_personnels" => $all_personnels,  "code_erreur" => 0], 200);
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
            "estimated_activity_end_date" => "nullable|date",
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


        if ($request->input('estimated_activity_date') && $request->input('estimated_activity_end_date')) {


            if (Carbon::parse($request->estimated_activity_date) > Carbon::parse($request->estimated_activity_end_date)) {
                return response()->json(['message' => "The Due date of the new activity shall be inferior or equal to the end date", "code_erreur" => 1], 200);
            }
        }

        if ($request->input('parent_activity_id')) {
            $parent_activity = \App\Models\Pro_StudyActivities::find($request->input('parent_activity_id'));
            if ($parent_activity) {

                if ($parent_activity->study_activity_name == $request->input('study_activity_name')) {
                    return response()->json(['message' => 'The activity cannot be its own parent.', "code_erreur" => 1], 200);
                }

                if (Carbon::parse($request->estimated_activity_date) > $parent_activity->estimated_activity_date) {
                    return response()->json(['message' => "The Due date of the new activity shall be inferior or equal to its parent activity's. (" . $parent_activity->estimated_activity_date . ")", "code_erreur" => 1], 200);
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
            'estimated_activity_end_date' => $request->input('estimated_activity_end_date'),
            'should_be_performed_by' => $request->input('should_be_performed_by') ?? null,
            'created_by' => FacadesAuth::user()->personnel->id,
            'status' => 'pending',
        ];

        if ($request->input('id')) {

            $activity_to_update = Pro_StudyActivities::find($request->input('id'));
            if (!$activity_to_update) {
                return response()->json(['message' => 'Study Activity not found.', "code_erreur" => 1], 200);
            } else {
                // Interdire la modification d'une activité déjà exécutée
                if (!is_null($activity_to_update->actual_activity_date)) {
                    return response()->json(['message' => 'You cannot edit an activity that has already been executed.', "code_erreur" => 1], 200);
                }
                $activity_to_update->update($activityData);
            }
        } else {

            \App\Models\Pro_StudyActivities::create($activityData);
        }

        session()->flash('success', 'Activity added to project successfully.');
        return response()->json(['message' => 'Activity added to project successfully.', "code_erreur" => 0], 200);
    }


    /**
     * Supprimer une activité
     */
    function supprimerActivite(Request $request)
    {

        $delete_cascade = $request->input('delete_cascade');
        $activity_id_to_delete = $request->input('activity_id');

        $activity_to_delete = Pro_StudyActivities::find($activity_id_to_delete);
        if (!$activity_to_delete) {
            return response()->json(['message' => 'Study Activity not found.', "code_erreur" => 1], 200);
        } else {

            // Interdire la suppression d'une activité déjà exécutée
            if (!is_null($activity_to_delete->actual_activity_date)) {
                return response()->json(['message' => 'You cannot delete an activity that has already been executed.', "code_erreur" => 1], 200);
            }


            if ($delete_cascade == 1) { // Supprimer avec les enfants

                $supprimer_enfants = Pro_StudyActivities::where("parent_activity_id", $activity_id_to_delete)->delete();
            }

            $activity_to_delete->delete(); //Suppression de la tâche principale

            session()->flash('success', 'Study Activity deleted successfully.');
            return response()->json(['message' => 'Study Activity deleted successfully.', "code_erreur" => 0], 200);
        }
    }

    /**
     * 
     */
    function childrenActivity(Request $request)
    {


        $all_children_activities = [];

        $activity_to_delete = Pro_StudyActivities::find($request->input('activity_id'));
        if (!$activity_to_delete) {
            return response()->json(['message' => 'Study Activity not found.', "code_erreur" => 1], 200);
        } else {


            // $all_children_activities = Pro_StudyActivities::where("parent_activity_id",$request->input('activity_id'))->orderBy("estimated_activity_date")->get();
            $all_children_activities = $activity_to_delete->allChildrenActivities;
        }

        return response()->json(['all_children_activities' => $all_children_activities, "code_erreur" => 0], 200);
    }


    /**
     * Generate Protocol Dev activities for Project
     */

    function generateProtocolDevActivitiesForProject(Request $request)
    {

        $project_id = $request->input("project_id");

        if (!$request->input('project_id')) {
            return response()->json(['message' => 'The project ID for the activity is required.', "code_erreur" => 1], 200);
        }

        $project = Pro_Project::find($project_id);
        if (!$project) {
            return response()->json(['message' => 'Project not found.', "code_erreur" => 1], 200);
        }

        $all_protocol_dev_activities = Pro_ProtocolDevActivity::all();

        foreach ($all_protocol_dev_activities as $key => $protocol_dev_activity) {
            # code...

            $check_exist = Pro_ProtocolDevActivityProject::where("project_id", $project_id)
                ->where("protocol_dev_activity_id", $protocol_dev_activity->id)->first();

            $assignedTo = null;
            $staff_role = "Other";

            $studyDirectorAppointmentForm = $project->studyDirectorAppointmentForm;
            if ($protocol_dev_activity->staff_role_perform == "Study Director") {

                $assignedTo = $studyDirectorAppointmentForm ? $studyDirectorAppointmentForm->study_director : null;

                $staff_role = "Study Director";
            } elseif ($protocol_dev_activity->staff_role_perform == "Project Manager") {

                $assignedTo = $studyDirectorAppointmentForm ? $studyDirectorAppointmentForm->project_manager : null;

                $staff_role = "Project Manager";
            } elseif ($protocol_dev_activity->staff_role_perform == "Facility Manager") {

                $fm = Pro_KeyFacilityPersonnel::where("staff_role", "Facility Manager")->first();

                $assignedTo = $fm ? $fm->personnel_id : null;

                $staff_role = "Facility Manager";
            } elseif ($protocol_dev_activity->staff_role_perform == "Quality Assurance") {

                $qa = Pro_KeyFacilityPersonnel::where("staff_role", "Quality Assurance")->first();

                $assignedTo = $qa ? $qa->personnel_id : null;
                $staff_role = "Quality Assurance";
            } elseif ($protocol_dev_activity->staff_role_perform == "Archivist") {

                $archivist = Pro_KeyFacilityPersonnel::where("staff_role", "Archivist")->first();

                $assignedTo = $archivist ? $archivist->personnel_id : null;
                $staff_role = "Archivist";
            }

            if (!$check_exist) {


                $act_create = Pro_ProtocolDevActivityProject::create([
                    "project_id" => $project_id,
                    "protocol_dev_activity_id" => $protocol_dev_activity->id,
                    "level_activite" => $protocol_dev_activity->level_activite,
                    "staff_id_assigned" => $assignedTo,
                    "staff_role" => $staff_role,
                    "applicable" => true,
                    "complete" => false,

                ]);
            } else {

                $check_exist->update([
                    "project_id" => $project_id,
                    "protocol_dev_activity_id" => $protocol_dev_activity->id,
                    "level_activite" => $protocol_dev_activity->level_activite,
                    "staff_id_assigned" => $assignedTo,
                    "staff_role" => $staff_role,
                    "applicable" => true,

                ]);
            }
        }

        session()->flash('success', 'Protocol Dev Activities created for the Project successfully.');
        return response()->json(['message' => 'Protocol Dev Activities created for the Project successfully.', "code_erreur" => 0], 200);
    }

    /**
     * Enregistrer activite Protocol
     */

    function saveProtocolDevelopmentActivityCompleted(Request $request)
    {


        if (!$request->input('protocol_dev_activity_project_id')) {
            return response()->json(['message' => 'The  activity ID is required.', "code_erreur" => 1], 200);
        }

        $record_activity = Pro_ProtocolDevActivityProject::find($request->input('protocol_dev_activity_project_id'));
        if (!$record_activity) {
            return response()->json(['message' => 'Record to update not found.', "code_erreur" => 1], 200);
        }

        if (!$request->input('date_performed')) {
            return response()->json(['message' => 'The Activity Date performed is required.', "code_erreur" => 1], 200);
        }

        if (Carbon::parse($request->date_performed) > now()) {
            return response()->json(['message' => "The Date performed of the activity shall be inferior or equal to today ", "code_erreur" => 1], 200);
        }

        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            if (!$file->isValid()) {
                return response()->json(['message' => 'The uploaded file is not valid.', "code_erreur" => 1], 200);
            }
            $path = $file->store('protocol_dev', 'public');
        } else {
            return response()->json(['message' => 'The document file is required.', "code_erreur" => 1], 200);
        }


        $documentData = [
            'id' => $request->input('protocol_dev_activity_project_id'),
            'date_performed' => $request->input('date_performed'),
            'document_file_path' => $path,
            'complete' => true,
            'real_date_performed' => now(),
            'staff_id_performed' => FacadesAuth::user() ? FacadesAuth::user()->personnel->id : null,
        ];

        $record_activity->update($documentData);

        $activity = $record_activity->protocolDevActivity;
        $message_success = $activity ? $activity->nom_activite . " document successfully uploaded or updated " : "Protocol Dev Activities updated for the Project successfully.";

        session()->flash('success', $message_success);
        return response()->json(['message' => $message_success, "code_erreur" => 0], 200);
    }


    /**
     * schedule meeting
     */

    function scheduleStudyInitiationMeeting(Request $request)
    {



        if (!$request->input('meeting_type')) {
            return response()->json(['message' => 'The  Meeting Type is required.', "code_erreur" => 1], 200);
        }
        if (!$request->input('meeting_date')) {
            return response()->json(['message' => 'The  Meeting Date is required.', "code_erreur" => 1], 200);
        }
        if (!$request->input('meeting_time')) {
            return response()->json(['message' => 'The  Meeting Time is required.', "code_erreur" => 1], 200);
        }
        if (!$request->input('participants')) {
            return response()->json(['message' => 'At least, one participant is required for the meeting.', "code_erreur" => 1], 200);
        }

        if ($request->input('meeting_id')) {

            $existing_meeting = Pro_StudyQualityAssuranceMeeting::find($request->input('meeting_id'));

            if (!$existing_meeting) {
                return response()->json(['message' => 'Record to update not found.', "code_erreur" => 1], 200);
            }
        }


        $path = null;

        if ($request->hasFile('meeting_file')) {
            $file = $request->file('meeting_file');
            if (!$file->isValid()) {
                return response()->json(['message' => 'The uploaded file is not valid.', "code_erreur" => 1], 200);
            }
            $path = $file->store('qa_meetings', 'public');
        }



        $documentData = [
            'project_id' => $request->input('project_id'),
            'meeting_type' => $request->input('meeting_type'),
            'date_scheduled' => $request->input('meeting_date'),
            'time_scheduled' => $request->input('meeting_time'),
            'breve_description' => $request->input('breve_description'),
            'meeting_link' => $request->input('meeting_link'),
            'meeting_file' => $path,
            'status' => "pending",
            'organizer_id' => FacadesAuth::user() ? FacadesAuth::user()->personnel->id : null,
        ];

        $meeting = Pro_StudyQualityAssuranceMeeting::find($request->input('meeting_id'));


        if ($meeting) {

            $meeting->update($documentData);

            //delete olds participants
            Pro_StudyQualityAssuranceMeetingParticipant::where("initiation_meeting_id", $request->input('meeting_id'))->delete();
        } else {

            $meeting = Pro_StudyQualityAssuranceMeeting::create($documentData);
        }

        $participants = $request->input('participants');
        $participants[] = $documentData["organizer_id"];

        if ($meeting) {

            foreach ($participants as $key => $participant_id) {
                # code...
                $participant  = Pro_StudyQualityAssuranceMeetingParticipant::create([
                    'initiation_meeting_id' =>  $meeting->id,
                    'participant_id' => $participant_id,
                ]);
            }

            $all_participants = Pro_Personnel::whereIn("id", $participants)->pluck("email_professionnel")->toArray();


            // Envoi par email
            // Mail::to($all_participants)->send(new MeetingMail($meeting));
        }


        session()->flash('success', "Meeting scheduled or updated successfully");
        return response()->json(['message' => "Meeting scheduled or updated successfully", "code_erreur" => 0], 200);
    }


    /**
     * Get Meeting Info By Id
     */

    function getMeetingInfoById(Request $request)
    {


        $meeting_id = $request->meeting_id;

        $meeting_info = Pro_StudyQualityAssuranceMeeting::find($meeting_id);
        $participants = [];

        if ($meeting_info) {

            $participants = $meeting_info->participants()->pluck("participant_id")->toArray();
        }

        return response()->json(['meeting_info' => $meeting_info, 'participants' => $participants, "code_erreur" => 0], 200);
    }

    function deleteQAMeeting(Request $request)
    {

        $meeting_id = $request->meeting_id;

        $meeting_to_delete = Pro_StudyQualityAssuranceMeeting::find($meeting_id);


        if (!$meeting_to_delete) {

            session()->flash('success', "You record you want to delete does not exist");
            return response()->json(['message' => "You record you want to delete does not exist", "code_erreur" => 1], 200);
        }

        $delete = $meeting_to_delete->delete();

        if ($delete) {


            //supprimer les participants

            $delete_participants = Pro_StudyQualityAssuranceMeetingParticipant::where("initiation_meeting_id", $meeting_id)->delete();

            session()->flash('success', "Meeting unscheduled successfully");
            return response()->json(['message' => "Meeting unscheduled successfully", "code_erreur" => 0], 200);
        }
    }


    /**
     * Marquer une activité comme une phase critique
     */
    function marquerActivitePhaseCritique(Request $request)
    {

        $activity_id = $request->activity_id;
        $meeting_id = $request->meeting_id;

        $activity = Pro_StudyActivities::find($activity_id);


        if (!$activity) {

            session()->flash('success', "You activity record does not exist");
            return response()->json(['message' => "You activity record does not exist", "code_erreur" => 1], 200);
        }


        $activity->phase_critique = true;
        $activity->meeting_id = $meeting_id;
        $activity->save();

        session()->flash('success', "Activity successfully marked as critical");
        return response()->json(['message' => "Activity successfully marked as critical", "code_erreur" => 0], 200);
    }

    function marquerActiviteNonPhaseCritique(Request $request)
    {


        $activity_id = $request->activity_id;
        $meeting_id = $request->meeting_id;

        $activity = Pro_StudyActivities::find($activity_id);


        if (!$activity) {

            session()->flash('success', "You activity record does not exist");
            return response()->json(['message' => "You activity record does not exist", "code_erreur" => 1], 200);
        }


        $activity->phase_critique = false;
        $activity->meeting_id = $meeting_id;
        $activity->save();

        session()->flash('success', "Activity successfully marked  as non critical");
        return response()->json(['message' => "Activity successfully marked non as critical", "code_erreur" => 0], 200);
    }

    function executeActivity(Request $request)
    {
        $rules = [
            'activity_id' => 'required|integer|exists:pro_studies_activities,id',
            'actual_activity_date' => 'required|date|before_or_equal:today',
            'performed_by' => 'required|integer|exists:personnels,id',
            'project_id' => 'required|integer|exists:pro_projects,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed: ' . implode(', ', $validator->errors()->all())], 422);
        }

        try {
            $activity = Pro_StudyActivities::find($request->activity_id);

            if (!$activity) {
                return response()->json(['success' => false, 'message' => 'Activity not found'], 404);
            }

            // Mettre à jour l'activité avec les informations d'exécution
            $activity->actual_activity_date = $request->actual_activity_date;
            $activity->performed_by = $request->performed_by;
            $activity->status = 'completed';

            // Si des commentaires sont fournis, on les stocke dans l'attribut commentaire
            if ($request->comments) {
                $activity->commentaire = ($activity->commentaire ?? '');
                
                $activity->commentaire = trim($activity->commentaire . "\n\n" . $request->comments);
            }

            $activity->save();

            return response()->json([
                'success' => true,
                'message' => 'Activity successfully recorded',
                'activity' => $activity
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Réinitialiser une activité en mode pending
     */
    function resetActivityExecution(Request $request)
    {
        $rules = [
            'activity_id' => 'required|integer|exists:pro_studies_activities,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed: ' . implode(', ', $validator->errors()->all())], 422);
        }

        try {
            $activity = Pro_StudyActivities::find($request->activity_id);

            if (!$activity) {
                return response()->json(['success' => false, 'message' => 'Activity not found'], 404);
            }

            // Remettre l'activité en pending et nettoyer les infos d'exécution
            $activity->status = 'pending';
            $activity->actual_activity_date = null;
            $activity->performed_by = null;
            // On conserve les commentaires historiques dans commentaire

            $activity->save();

            return response()->json([
                'success' => true,
                'message' => 'Activity successfully reset to pending',
                'activity' => $activity
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Programmer une inspection QA
     */
    function scheduleQaInspection(Request $request)
    {
        $rules = [
            'project_id'       => 'required|integer|exists:pro_projects,id',
            'qa_inspector_id'  => 'required|integer|exists:personnels,id',
            'date_scheduled'   => 'required|date',
            'type_inspection'  => 'required|string|in:Facility Inspection,Process Inspection,Study Inspection,Critical Phase Inspection',
            'activity_id'      => 'nullable|integer|exists:pro_studies_activities,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $validator->errors()->all())
            ], 422);
        }

        try {
            $inspectionNumber = Pro_QaInspection::where('project_id', $request->project_id)->count() + 1;
            $inspectionName   = $request->type_inspection . ' #' . $inspectionNumber;

            $inspection = Pro_QaInspection::create([
                'project_id'      => $request->project_id,
                'activity_id'     => $request->activity_id ?? null,
                'checklist_slug'  => $request->checklist_slug ?? null,
                'qa_inspector_id' => $request->qa_inspector_id,
                'date_scheduled'  => $request->date_scheduled,
                'type_inspection' => $request->type_inspection,
                'inspection_name' => $inspectionName,
            ]);

            return response()->json([
                'success'    => true,
                'message'    => 'QA Inspection scheduled successfully',
                'inspection' => $inspection
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les findings d'une inspection
     */
    function getInspectionFindings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'inspection_id' => 'required|integer|exists:pro_qa_inspections,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $findings = Pro_QaInspectionFinding::where('inspection_id', $request->inspection_id)
            ->with(['assignedTo', 'parentFinding'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($f) {
                return [
                    'id'               => $f->id,
                    'finding_text'     => $f->finding_text,
                    'action_point'     => $f->action_point,
                    'deadline_date'    => $f->deadline_date,
                    'status'           => $f->status,
                    'parent_finding_id'=> $f->parent_finding_id,
                    'assigned_to_name' => $f->assignedTo
                        ? $f->assignedTo->prenom . ' ' . $f->assignedTo->nom
                        : null,
                    'parent_finding_text' => $f->parentFinding
                        ? mb_strimwidth($f->parentFinding->finding_text, 0, 60, '…')
                        : null,
                    'created_at' => $f->created_at?->format('d/m/Y'),
                ];
            });

        return response()->json(['success' => true, 'findings' => $findings], 200);
    }

    /**
     * Enregistrer un finding d'inspection QA
     */
    function saveQaFinding(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'inspection_id'    => 'required|integer|exists:pro_qa_inspections,id',
            'project_id'       => 'required|integer|exists:pro_projects,id',
            'finding_text'     => 'required|string|max:2000',
            'assigned_to'      => 'required|integer|exists:personnels,id',
            'deadline_date'    => 'nullable|date',
            'parent_finding_id'=> 'nullable|integer|exists:pro_qa_inspections_findings,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => implode(', ', $validator->errors()->all())
            ], 422);
        }

        try {
            $finding = Pro_QaInspectionFinding::create([
                'inspection_id'    => $request->inspection_id,
                'project_id'       => $request->project_id,
                'finding_text'     => $request->finding_text,
                'assigned_to'      => $request->assigned_to,
                'deadline_date'    => $request->deadline_date,
                'parent_finding_id'=> $request->parent_finding_id ?: null,
                'status'           => 'pending',
            ]);

            // Marquer l'inspection comme réalisée dès le premier finding enregistré
            $inspection = Pro_QaInspection::find($request->inspection_id);
            if ($inspection && !$inspection->date_performed) {
                $inspection->date_performed = now()->toDateString();
                $inspection->save();
            }

            $finding->load('assignedTo');

            return response()->json([
                'success' => true,
                'message' => 'Finding enregistré avec succès',
                'finding' => [
                    'id'               => $finding->id,
                    'finding_text'     => $finding->finding_text,
                    'action_point'     => null,
                    'deadline_date'    => $finding->deadline_date,
                    'status'           => $finding->status,
                    'parent_finding_id'=> $finding->parent_finding_id,
                    'assigned_to_name' => $finding->assignedTo
                        ? $finding->assignedTo->prenom . ' ' . $finding->assignedTo->nom
                        : null,
                    'created_at' => $finding->created_at?->format('d/m/Y'),
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Résoudre un finding (Corrective Action par le Study Director)
     */
    function resolveQaFinding(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'finding_id'   => 'required|integer|exists:pro_qa_inspections_findings,id',
            'action_point' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => implode(', ', $validator->errors()->all())
            ], 422);
        }

        try {
            $finding = Pro_QaInspectionFinding::find($request->finding_id);

            if (!$finding) {
                return response()->json(['success' => false, 'message' => 'Finding introuvable'], 404);
            }

            $finding->action_point = $request->action_point;
            $finding->status       = 'complete';
            $finding->save();

            return response()->json([
                'success' => true,
                'message' => 'Finding résolu avec succès',
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Supprimer une inspection QA et tous ses findings (+ actions correctives)
     */
    function deleteQaInspection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'inspection_id' => 'required|integer|exists:pro_qa_inspections,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => implode(', ', $validator->errors()->all())
            ], 422);
        }

        try {
            $inspection = Pro_QaInspection::findOrFail($request->inspection_id);

            // Supprimer d'abord les findings enfants, puis les findings, puis l'inspection
            foreach ($inspection->findings as $finding) {
                $finding->childFindings()->delete();
            }
            $inspection->findings()->delete();
            $inspection->delete();

            return response()->json([
                'success' => true,
                'message' => 'Inspection supprimée avec succès.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Supprimer un finding individuel (et ses findings enfants)
     */
    function deleteQaFinding(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'finding_id' => 'required|integer|exists:pro_qa_inspections_findings,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => implode(', ', $validator->errors()->all())
            ], 422);
        }

        try {
            $finding = Pro_QaInspectionFinding::findOrFail($request->finding_id);
            $finding->childFindings()->delete();
            $finding->delete();

            return response()->json([
                'success' => true,
                'message' => 'Finding supprimé avec succès.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Supprimer une corrective action (remet le finding en statut "pending")
     */
    function deleteCorrectiveAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'finding_id' => 'required|integer|exists:pro_qa_inspections_findings,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => implode(', ', $validator->errors()->all())
            ], 422);
        }

        try {
            $finding = Pro_QaInspectionFinding::findOrFail($request->finding_id);
            $finding->action_point = null;
            $finding->status       = 'pending';
            $finding->save();

            return response()->json([
                'success' => true,
                'message' => 'Corrective action supprimée. Finding remis en attente.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}

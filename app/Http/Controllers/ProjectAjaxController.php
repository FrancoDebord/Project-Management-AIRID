<?php

namespace App\Http\Controllers;

use App\Mail\FindingsResolvedMail;
use App\Mail\MeetingMail;
use App\Mail\StudyDirectorAssignedMail;
use App\Models\AppNotification;
use App\Models\AppSetting;
use App\Models\Pro_ArchivingDocument;
use App\Models\Pro_KeyFacilityPersonnel;
use App\Models\Pro_ReportPhaseDocument;
use App\Models\Pro_OtherBasicDocument;
use App\Models\Pro_Personnel;
use App\Models\Pro_Project;
use App\Models\Pro_ProjectRelatedLabTest;
use App\Models\Pro_ProjectRelatedProductType;
use App\Models\Pro_Project_Team;
use App\Models\Pro_ProjectRelatedStudyType;
use App\Models\Pro_ProtocolDevActivity;
use App\Models\Pro_ProtocolDevActivityProject;
use App\Models\Pro_ProtocolDevDocument;
use App\Models\Pro_QaStatement;
use App\Models\Pro_QaActivitiesChecklist;
use App\Models\User;
use App\Models\Pro_StudyDirectorAppointmentForm;
use App\Models\Pro_StudyActivities;
use App\Models\Pro_QaInspection;
use App\Models\Pro_QaInspectionFinding;
use App\Models\Pro_StudyQualityAssuranceMeeting;
use App\Models\Pro_StudyQualityAssuranceMeetingParticipant;
use Carbon\Carbon;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\DB;
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
            "code"          => "required|string|max:50",
            "title"         => "required|string|max:255",
            "is_glp"        => "required|boolean",
            "study_type_id" => "nullable|array",
        ];

        $checkUnique = Pro_Project::where("project_code", $request->code)->first();
        if ($checkUnique) {
            return response()->json(['message' => 'The project code has already been taken.', "code_erreur" => 1], 201);
        }

        $checkUnique = Pro_Project::where("project_title", $request->title)->first();
        if ($checkUnique) {
            return response()->json(['message' => 'The project title has already been taken.', "code_erreur" => 1], 201);
        }

        $project = Pro_Project::create([
            'project_code'   => $request->code,
            'project_title'  => $request->title,
            'is_glp'         => (bool) $request->is_glp,
            'study_director' => $request->study_director_id ?: null,
        ]);

        if ($project) {
            foreach ($request->input('study_type_id', []) as $study_type_id) {
                Pro_ProjectRelatedStudyType::create(['project_id' => $project->id, 'study_type_id' => $study_type_id]);
            }

            // ── Notify Study Director if assigned ─────────────────────
            if ($request->study_director_id) {
                $personnel = Pro_Personnel::find($request->study_director_id);
                $sdUser    = $personnel?->user_id ? User::find($personnel->user_id) : null;
                $projectUrl = route('projectOverview', ['id' => $project->id]);
                $directorName = $personnel ? trim(($personnel->prenom ?? '') . ' ' . ($personnel->nom ?? '')) : 'Study Director';

                if ($sdUser) {
                    AppNotification::send(
                        $sdUser->id,
                        'project_assigned',
                        "You have been appointed Study Director — {$project->project_code}",
                        "You have been assigned as Study Director for study \"{$project->project_title}\". Please fill in the SD Appointment Form and review the study details.",
                        $projectUrl,
                        'bi-person-badge-fill'
                    );

                    $email = $personnel->email_professionnel ?? $personnel->email_personnel ?? $sdUser->email;
                    if ($email) {
                        try {
                            Mail::to($email)->send(new StudyDirectorAssignedMail(
                                directorName: $directorName,
                                projectCode:  $project->project_code,
                                projectTitle: $project->project_title,
                                projectUrl:   $projectUrl,
                            ));
                        } catch (\Throwable) { /* fail silently */ }
                    }
                }
            }
        }

        $data = [
            'project_id'   => $project->id,
            'project_code' => $project->project_code,
            'project_title'=> $project->project_title,
            'is_glp'       => $project->is_glp,
        ];
        return response()->json(['message' => 'Project successfully created.', 'data' => $data, 'code_erreur' => 0], 201);
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
     * Save Key Personnel for a project (pro_projects_team)
     */
    public function saveKeyPersonnel(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:pro_projects,id',
            'staff_ids'  => 'nullable|array',
            'staff_ids.*'=> 'exists:personnels,id',
        ]);

        $projectId = $request->input('project_id');

        Pro_Project_Team::where('project_id', $projectId)->delete();

        foreach ((array) $request->input('staff_ids', []) as $staffId) {
            Pro_Project_Team::create([
                'project_id' => $projectId,
                'staff_id'   => $staffId,
                'role'       => '',
            ]);
        }

        return response()->json(['message' => 'Key personnel updated successfully.', 'code_erreur' => 0], 200);
    }

    public function addKeyPersonnelMember(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:pro_projects,id',
            'staff_id'   => 'required|exists:personnels,id',
        ]);

        $exists = Pro_Project_Team::where('project_id', $request->project_id)
                                  ->where('staff_id', $request->staff_id)
                                  ->exists();
        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Person already in team.'], 422);
        }

        Pro_Project_Team::create([
            'project_id' => $request->project_id,
            'staff_id'   => $request->staff_id,
            'role'       => '',
        ]);

        $person = Pro_Personnel::find($request->staff_id);
        return response()->json([
            'success' => true,
            'person'  => [
                'id'             => $person->id,
                'full_name'      => trim(($person->titre_personnel ?? '') . ' ' . $person->prenom . ' ' . $person->nom),
                'titre_personnel'=> $person->titre_personnel ?? '',
                'role'           => $person->role ?? '',
            ],
        ]);
    }

    public function removeKeyPersonnelMember(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:pro_projects,id',
            'staff_id'   => 'required|exists:personnels,id',
        ]);

        Pro_Project_Team::where('project_id', $request->project_id)
                        ->where('staff_id', $request->staff_id)
                        ->delete();

        return response()->json(['success' => true]);
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


        // File is required only when creating (no existing form for this project)
        $project = Pro_Project::find($request->input('project_id'));
        $existingFormCheck = $project ? $project->studyDirectorAppointmentForm : null;

        if ($request->hasFile('sd_appointment_file')) {
            $file = $request->file('sd_appointment_file');
            if (!$file->isValid()) {
                return response()->json(['message' => 'The uploaded file is not valid.', "code_erreur" => 1], 200);
            }
        } elseif (!$existingFormCheck || !$existingFormCheck->sd_appointment_file) {
            return response()->json(['message' => 'The signed Study Director Appointment Form (PDF) is required.', "code_erreur" => 1], 200);
        }


        if ($request->input('estimated_start_date') && $request->input('estimated_end_date') && $request->input('estimated_start_date') > $request->input('estimated_end_date')) {
            return response()->json(['message' => 'The estimated start date cannot be later than the estimated end date.', "code_erreur" => 1], 200);
        }


        if ($request->input('study_director') && $request->input('project_manager') && $request->input('study_director') == $request->input('project_manager')) {
            return response()->json(['message' => 'The study director must be different from the project manager.', "code_erreur" => 1], 200);
        }

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
        if ($existingFormCheck) {
            // Update existing form
            $existingFormCheck->update($dataToUpdate);
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

                // If activity is a critical phase, sync the linked inspection's scheduled date
                if ($activity_to_update->phase_critique && $request->input('estimated_activity_date')) {
                    Pro_QaInspection::where('activity_id', $activity_to_update->id)
                        ->whereNull('date_performed')
                        ->update(['date_scheduled' => $request->input('estimated_activity_date')]);
                }
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
            return response()->json(['message' => 'The activity ID is required.', 'code_erreur' => 1], 200);
        }

        $record_activity = Pro_ProtocolDevActivityProject::find($request->input('protocol_dev_activity_project_id'));
        if (!$record_activity) {
            return response()->json(['message' => 'Record to update not found.', 'code_erreur' => 1], 200);
        }

        if (!$request->input('date_performed')) {
            return response()->json(['message' => 'The Activity Date performed is required.', 'code_erreur' => 1], 200);
        }

        if (Carbon::parse($request->date_performed) > now()) {
            return response()->json(['message' => 'The Date performed shall be today or in the past.', 'code_erreur' => 1], 200);
        }

        $activity     = $record_activity->protocolDevActivity;
        $level        = (int) $record_activity->level_activite;
        // The DB column has a typo: "multipicite" instead of "multiplicite"
        $multiplicite = $activity?->multipicite ?? 'une_fois';

        // Chronological check: must be >= previous level's date
        $prevActivity = Pro_ProtocolDevActivityProject::where('project_id', $record_activity->project_id)
            ->where('level_activite', $level - 1)
            ->where('complete', true)
            ->first();
        if ($prevActivity && $prevActivity->date_performed) {
            if (Carbon::parse($request->date_performed) < Carbon::parse($prevActivity->date_performed)) {
                return response()->json([
                    'message'     => 'The date cannot be before the previous activity\'s date (' . $prevActivity->date_performed . ').',
                    'code_erreur' => 1,
                ], 200);
            }
        }

        // For une_fois: must be <= next level's date
        if ($multiplicite === 'une_fois') {
            $nextActivity = Pro_ProtocolDevActivityProject::where('project_id', $record_activity->project_id)
                ->where('level_activite', $level + 1)
                ->where('complete', true)
                ->first();
            if ($nextActivity && $nextActivity->date_performed) {
                if (Carbon::parse($request->date_performed) > Carbon::parse($nextActivity->date_performed)) {
                    return response()->json([
                        'message'     => 'The date cannot be after the next activity\'s date (' . $nextActivity->date_performed . ').',
                        'code_erreur' => 1,
                    ], 200);
                }
            }
        }

        // ── Resolve existing document ──
        $staffId = FacadesAuth::user() ? FacadesAuth::user()->personnel?->id : null;

        // If a specific doc_id is provided (edit button on any row), target that document
        $existingDoc = null;
        if ($request->filled('doc_id')) {
            $existingDoc = Pro_ProtocolDevDocument::where('id', $request->doc_id)
                ->where('activity_project_id', $record_activity->id)
                ->first();
        } elseif ($multiplicite === 'une_fois') {
            // For une_fois without explicit doc_id: find the single existing doc
            $existingDoc = Pro_ProtocolDevDocument::where('activity_project_id', $record_activity->id)->first();
        }
        // For plusieurs_fois without doc_id: always create a new document

        // File required for first submission (no existing doc)
        if (!$existingDoc && !$request->hasFile('document_file')) {
            return response()->json(['message' => 'A document file is required for the first submission.', 'code_erreur' => 1], 200);
        }

        $path = $existingDoc?->document_file_path;
        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            if (!$file->isValid()) {
                return response()->json(['message' => 'The uploaded file is not valid.', 'code_erreur' => 1], 200);
            }
            if ($path) {
                \Storage::disk('public')->delete($path);
            }
            $path = $file->store('protocol_dev', 'public');
        }

        // ── Auto-inspection (GLP projects only) ──
        $inspectionTypeMap = [
            1 => 'Study Protocol Inspection',
            3 => 'Study Protocol Inspection',
            5 => 'Study Protocol Amendment/Deviation Inspection',
        ];

        $project   = Pro_Project::find($record_activity->project_id);
        $isGlp     = $project && $project->is_glp;

        $inspection = null;
        if ($isGlp && isset($inspectionTypeMap[$level])) {
            $inspType    = $inspectionTypeMap[$level];
            $inspName    = ($activity?->nom_activite ?? 'Protocol Dev') . ' – QA Inspection';
            $scheduledAt = $request->input('date_performed');
            $projectId   = $record_activity->project_id;

            if ($existingDoc?->qa_inspection_id) {
                $inspection = Pro_QaInspection::find($existingDoc->qa_inspection_id);
                $inspection?->update(['date_scheduled' => $scheduledAt, 'date_start' => $scheduledAt]);
            } else {
                $inspection = Pro_QaInspection::create([
                    'project_id'      => $projectId,
                    'type_inspection' => $inspType,
                    'inspection_name' => $inspName,
                    'date_scheduled'  => $scheduledAt,
                    'date_start'      => $scheduledAt,
                    'qa_inspector_id' => $this->getQaManagerId(),
                ]);
            }
        }

        // ── Save / update the document record ──
        $docData = [
            'activity_project_id' => $record_activity->id,
            'project_id'          => $record_activity->project_id,
            'document_file_path'  => $path,
            'date_performed'      => $request->input('date_performed'),
            'date_upload'         => now()->toDateString(),
            'staff_id_performed'  => $staffId,
            'qa_inspection_id'    => $inspection?->id,
        ];

        if ($existingDoc) {
            $existingDoc->update($docData);
        } else {
            Pro_ProtocolDevDocument::create($docData);
        }

        // ── Mark the activity as complete ──
        $record_activity->update([
            'complete'            => true,
            'date_performed'      => $request->input('date_performed'),
            'real_date_performed' => now(),
            'staff_id_performed'  => $staffId,
        ]);

        $message_success = ($activity?->nom_activite ?? 'Protocol Dev') . ' document successfully uploaded.';
        session()->flash('success', $message_success);
        return response()->json(['message' => $message_success, 'code_erreur' => 0], 200);
    }

    /**
     * Delete ALL documents for an activity and reset it (used when activity has no more docs)
     */
    public function deleteProtocolDevDocument(Request $request)
    {
        $record = Pro_ProtocolDevActivityProject::find($request->record_id);
        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Record not found.'], 404);
        }

        // Delete all document files from storage
        foreach ($record->protocolDevDocuments as $doc) {
            if ($doc->document_file_path) {
                \Storage::disk('public')->delete($doc->document_file_path);
            }
            $doc->delete();
        }

        $record->update([
            'complete'            => false,
            'date_performed'      => null,
            'real_date_performed' => null,
            'staff_id_performed'  => null,
        ]);

        return response()->json(['success' => true, 'message' => 'Documents supprimés, activité réinitialisée.']);
    }

    /**
     * Delete a single document entry from pro_protocol_dev_documents.
     * If it was the last one, also resets the activity.
     */
    public function deleteProtocolDevDocumentEntry(Request $request)
    {
        $doc = Pro_ProtocolDevDocument::find($request->doc_id);
        if (!$doc) {
            return response()->json(['success' => false, 'message' => 'Document not found.'], 404);
        }

        $activityProject = $doc->activityProject;

        if ($doc->document_file_path) {
            \Storage::disk('public')->delete($doc->document_file_path);
        }
        $doc->delete();

        // If no more documents remain, reset the activity
        if ($activityProject && $activityProject->protocolDevDocuments()->count() === 0) {
            $activityProject->update([
                'complete'            => false,
                'date_performed'      => null,
                'real_date_performed' => null,
                'staff_id_performed'  => null,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Document supprimé.']);
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

        // Auto-create a Critical Phase Inspection if none exists for this activity
        $existingInspection = Pro_QaInspection::where('activity_id', $activity->id)->first();
        if (!$existingInspection) {
            Pro_QaInspection::create([
                'project_id'      => $activity->project_id,
                'activity_id'     => $activity->id,
                'type_inspection' => 'Critical Phase Inspection',
                'inspection_name' => 'Critical Phase: ' . $activity->study_activity_name,
                'date_scheduled'  => $activity->estimated_activity_date,
                'qa_inspector_id' => $this->getQaManagerId(),
                'checklist_slug'  => null,
            ]);
        }

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

        // Block unmarking if the linked inspection has already been performed
        $performedInspection = Pro_QaInspection::where('activity_id', $activity->id)
            ->whereNotNull('date_performed')
            ->first();
        if ($performedInspection) {
            return response()->json([
                'message'     => 'Cannot unmark: the Critical Phase Inspection for this activity has already been performed.',
                'code_erreur' => 1,
            ], 200);
        }

        $activity->phase_critique = false;
        $activity->meeting_id = $meeting_id;
        $activity->save();

        // Delete the linked Critical Phase Inspection if not yet performed
        Pro_QaInspection::where('activity_id', $activity->id)
            ->whereNull('date_performed')
            ->delete();

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

            // Vérifier que le projet n'est pas archivé
            $project = Pro_Project::find($request->project_id);
            if ($project && $project->archived_at) {
                return response()->json(['success' => false, 'message' => 'This project is archived. No modifications are allowed.'], 403);
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

            // Block if experimental phase is already marked as completed
            if ($activity->project_id) {
                $project = Pro_Project::find($activity->project_id);
                if ($project && in_array('experimental', $project->phases_completed ?? [])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'La phase expérimentale est déjà marquée comme complétée. Vous ne pouvez plus remettre une activité en attente.',
                    ], 422);
                }
            }

            // Block if the activity is a critical phase and already has an inspection performed
            if ($activity->phase_critique) {
                $inspectionDone = DB::table('pro_qa_inspections')
                    ->where('activity_id', $activity->id)
                    ->whereNotNull('date_performed')
                    ->exists();
                if ($inspectionDone) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Une inspection QA a déjà été réalisée pour cette phase critique. Vous ne pouvez plus remettre l\'activité en attente.',
                    ], 422);
                }
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
        $isCritical = $request->type_inspection === 'Critical Phase Inspection';
        $rules = [
            'project_id'       => $isCritical ? 'required|integer|exists:pro_projects,id' : 'nullable|integer|exists:pro_projects,id',
            'qa_inspector_id'  => 'required|integer|exists:personnels,id',
            'date_scheduled'   => 'required|date',
            'type_inspection'  => 'required|string|max:100',
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
            $inspectionName   = $request->filled('inspection_name')
                ? $request->inspection_name
                : $request->type_inspection . ' #' . $inspectionNumber;

            $inspection = Pro_QaInspection::create([
                'project_id'        => $request->project_id,
                'activity_id'       => $request->activity_id ?? null,
                'checklist_slug'    => $request->checklist_slug ?? null,
                'qa_inspector_id'   => $request->qa_inspector_id,
                'date_scheduled'    => $request->date_scheduled,
                'type_inspection'   => $request->type_inspection,
                'inspection_name'   => $inspectionName,
                'facility_location' => $request->facility_location ?? null,
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
     * Mettre à jour les informations d'une inspection (avant démarrage des formulaires)
     */
    function updateQaInspection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'inspection_id'    => 'required|integer|exists:pro_qa_inspections,id',
            'inspection_name'  => 'nullable|string|max:200',
            'date_scheduled'   => 'nullable|date',
            'date_start'       => 'nullable|date',
            'date_end'         => 'nullable|date|after_or_equal:date_start',
            'date_report_fm'   => 'nullable|date',
            'date_report_sd'   => 'nullable|date',
            'qa_inspector_id'  => 'nullable|integer|exists:personnels,id',
            'facility_location'=> 'nullable|string|in:cotonou,cove',
            'checklist_slug'   => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $inspection = Pro_QaInspection::findOrFail($request->inspection_id);

        // Block editing if inspection has been marked as done
        if ($inspection->date_performed) {
            return response()->json(['success' => false, 'message' => 'Cette inspection est déjà finalisée. Modification impossible.'], 403);
        }

        // Block editing if any facility forms have been started
        if ($inspection->type_inspection === 'Facility Inspection') {
            $started = false;
            if ($inspection->facility_location === 'cove') {
                $started = \App\Models\Pro_Cl_FacilityInspectionCove::where('inspection_id', $inspection->id)->exists();
            } else {
                $started = \App\Models\Pro_Cl_FacilityInspection::where('inspection_id', $inspection->id)->exists();
            }
            if ($started) {
                return response()->json(['success' => false, 'message' => 'Des formulaires ont déjà été remplis pour cette inspection. Modification impossible.'], 403);
            }
        }

        // Block editing if any process inspection forms have been started
        if ($inspection->type_inspection === 'Process Inspection') {
            $started = \App\Models\Pro_Cl_ProcessInspection::where('inspection_id', $inspection->id)->exists();
            if ($started) {
                return response()->json(['success' => false, 'message' => 'Des formulaires ont déjà été remplis pour cette inspection. Modification impossible.'], 403);
            }
        }

        if ($request->filled('date_scheduled')) $inspection->date_scheduled = $request->date_scheduled;
        if ($request->filled('date_start'))     $inspection->date_start     = $request->date_start;
        if ($request->filled('date_end'))       $inspection->date_end       = $request->date_end;
        if ($request->filled('date_report_fm')) $inspection->date_report_fm = $request->date_report_fm;
        if ($request->filled('date_report_sd')) $inspection->date_report_sd = $request->date_report_sd;
        if ($request->filled('qa_inspector_id'))    $inspection->qa_inspector_id  = $request->qa_inspector_id;
        if ($request->filled('inspection_name'))    $inspection->inspection_name  = $request->inspection_name;
        if ($inspection->type_inspection === 'Facility Inspection' && $request->filled('facility_location')) {
            $inspection->facility_location = $request->facility_location;
        }
        if ($request->filled('checklist_slug'))     $inspection->checklist_slug   = $request->checklist_slug;
        $inspection->save();

        return response()->json(['success' => true, 'message' => 'Inspection mise à jour.']);
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

        $inspection = Pro_QaInspection::find($request->inspection_id);

        $findings = Pro_QaInspectionFinding::where('inspection_id', $request->inspection_id)
            ->with(['assignedTo', 'parentFinding'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($f) {
                return [
                    'id'               => $f->id,
                    'finding_text'     => $f->finding_text,
                    'is_conformity'    => (bool) $f->is_conformity,
                    'action_point'          => $f->action_point,
                    'means_of_verification' => $f->means_of_verification,
                    'resolved_by_name'      => $f->resolved_by_name,
                    'meeting_date'          => $f->meeting_date,
                    'deadline_date'         => $f->deadline_date,
                    'status'                => $f->status,
                    'facility_section'      => $f->facility_section,
                    'parent_finding_id'     => $f->parent_finding_id,
                    'assigned_to_id' => $f->assigned_to,
                    'assigned_to_name' => $f->assignedTo
                        ? $f->assignedTo->prenom . ' ' . $f->assignedTo->nom
                        : null,
                    'parent_finding_text' => $f->parentFinding
                        ? mb_strimwidth($f->parentFinding->finding_text, 0, 60, '…')
                        : null,
                    'created_at' => $f->created_at?->format('d/m/Y'),
                ];
            });

        $response = ['success' => true, 'findings' => $findings];

        // Helper: build section_conformities map from a record
        $sectionConformities = function ($record, array $sections): array {
            if (!$record) return [];
            $map = [];
            foreach ($sections as $s) {
                $val = $record->{"{$s}_is_conforming"};
                $map[$s] = $val === null ? null : (bool) $val;
            }
            return $map;
        };

        // For Facility Inspections, also return location and sections_done
        if ($inspection && $inspection->type_inspection === 'Facility Inspection') {
            $response['is_facility']       = true;
            $response['facility_location'] = $inspection->facility_location;

            if ($inspection->facility_location === 'cove') {
                $clRecord = \App\Models\Pro_Cl_FacilityInspectionCove::where('inspection_id', $inspection->id)->first();
                $response['section_conformities'] = $sectionConformities($clRecord, ['a','b','c','d','e','f','g','h','i']);
            } else {
                $clRecord = \App\Models\Pro_Cl_FacilityInspection::where('inspection_id', $inspection->id)->first();
                $response['section_conformities'] = $sectionConformities($clRecord, ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o']);
            }
            $response['sections_done'] = $clRecord ? ($clRecord->sections_done ?? []) : [];
        }

        // For Process Inspections, also return sections_done
        if ($inspection && $inspection->type_inspection === 'Process Inspection') {
            $response['is_process'] = true;
            $clRecord = \App\Models\Pro_Cl_ProcessInspection::where('inspection_id', $inspection->id)->first();
            $response['sections_done'] = $clRecord ? ($clRecord->sections_done ?? []) : [];
            $response['section_conformities'] = $sectionConformities($clRecord, ['a','b','c','d','e']);
        }

        // Study Protocol Inspection
        if ($inspection && $inspection->type_inspection === 'Study Protocol Inspection') {
            $response['is_study_protocol'] = true;
            $clRecord = \App\Models\Pro_Cl_StudyProtocolInspection::where('inspection_id', $inspection->id)->first();
            $response['sections_done'] = $clRecord ? ($clRecord->sections_done ?? []) : [];
            $response['section_conformities'] = $sectionConformities($clRecord, ['a','b','c','d','e','f']);
        }

        // Study Report Inspection
        if ($inspection && $inspection->type_inspection === 'Study Report Inspection') {
            $response['is_study_report'] = true;
            $clRecord = \App\Models\Pro_Cl_StudyReportInspection::where('inspection_id', $inspection->id)->first();
            $response['sections_done'] = $clRecord ? ($clRecord->sections_done ?? []) : [];
            $response['section_conformities'] = $sectionConformities($clRecord, ['a','b','c','d','e']);
        }

        // Data Quality Inspection
        if ($inspection && $inspection->type_inspection === 'Data Quality Inspection') {
            $response['is_data_quality'] = true;
            $clRecord = \App\Models\Pro_Cl_DataQualityInspection::where('inspection_id', $inspection->id)->first();
            $response['sections_done'] = $clRecord ? ($clRecord->sections_done ?? []) : [];
            $response['section_conformities'] = $sectionConformities($clRecord, ['a','b','c','d','e']);
        }

        return response()->json($response, 200);
    }

    /**
     * Enregistrer un finding d'inspection QA
     */
    function saveQaFinding(Request $request)
    {
        $isConformity = $request->boolean('is_conformity', false);

        // Determine if this is a facility or process inspection (no project_id required)
        $inspection = Pro_QaInspection::find($request->inspection_id);
        $isFacility = $inspection && in_array($inspection->type_inspection, ['Facility Inspection', 'Process Inspection']);

        $validator = Validator::make($request->all(), [
            'inspection_id'    => 'required|integer|exists:pro_qa_inspections,id',
            'project_id'       => $isFacility ? 'nullable|integer|exists:pro_projects,id' : 'required|integer|exists:pro_projects,id',
            'finding_text'     => 'required|string|max:2000',
            'is_conformity'    => 'nullable|boolean',
            'assigned_to'      => $isConformity ? 'nullable|integer|exists:personnels,id' : 'required|integer|exists:personnels,id',
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
            // Block if inspection is marked as completed
            if ($inspection && $inspection->completed_at) {
                return response()->json(['success' => false, 'message' => 'This inspection has been marked as completed. No new findings can be added.'], 403);
            }

            // Vérifier que le projet n'est pas archivé (seulement si project_id fourni)
            if ($request->project_id) {
                $projectLock = Pro_Project::find($request->project_id);
                if ($projectLock && $projectLock->archived_at) {
                    return response()->json(['success' => false, 'message' => 'This project is archived. No modifications are allowed.'], 403);
                }
            }

            $finding = Pro_QaInspectionFinding::create([
                'inspection_id'    => $request->inspection_id,
                'facility_section' => $request->facility_section ?: null,
                'project_id'       => $request->project_id ?: null,
                'finding_text'     => $request->finding_text,
                'is_conformity'    => $request->boolean('is_conformity', false),
                'assigned_to'      => $request->assigned_to,
                'deadline_date'    => $request->deadline_date,
                'parent_finding_id'=> $request->parent_finding_id ?: null,
                'status'           => 'pending',
            ]);

            $finding->load('assignedTo');

            return response()->json([
                'success' => true,
                'message' => 'Finding enregistré avec succès',
                'finding' => [
                    'id'               => $finding->id,
                    'finding_text'     => $finding->finding_text,
                    'is_conformity'    => (bool) $finding->is_conformity,
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

    function updateQaFinding(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'finding_id'    => 'required|integer|exists:pro_qa_inspections_findings,id',
            'finding_text'  => 'required|string|max:2000',
            'assigned_to'   => 'nullable|integer|exists:personnels,id',
            'deadline_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => implode(', ', $validator->errors()->all())], 422);
        }

        try {
            $finding = Pro_QaInspectionFinding::findOrFail($request->finding_id);
            $finding->finding_text  = $request->finding_text;
            $finding->assigned_to   = $request->assigned_to ?: null;
            $finding->deadline_date = $request->deadline_date ?: null;
            $finding->save();
            $finding->load('assignedTo');

            return response()->json([
                'success' => true,
                'finding' => [
                    'id'               => $finding->id,
                    'finding_text'     => $finding->finding_text,
                    'is_conformity'    => (bool) $finding->is_conformity,
                    'action_point'     => $finding->action_point,
                    'deadline_date'    => $finding->deadline_date,
                    'status'           => $finding->status,
                    'facility_section' => $finding->facility_section,
                    'assigned_to_id'   => $finding->assigned_to,
                    'assigned_to_name' => $finding->assignedTo
                        ? $finding->assignedTo->prenom . ' ' . $finding->assignedTo->nom
                        : null,
                ],
            ]);
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
            'finding_id'           => 'required|integer|exists:pro_qa_inspections_findings,id',
            'action_point'         => 'required|string|max:2000',
            'means_of_verification'=> 'nullable|string|max:2000',
            'resolved_date'        => 'required|date',
            'resolved_by_name'     => 'nullable|string|max:255',
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

            $finding->action_point          = $request->action_point;
            $finding->means_of_verification = $request->means_of_verification ?? null;
            $finding->resolved_by_name      = $request->resolved_by_name ?: null;
            $finding->meeting_date          = $request->resolved_date;
            $finding->status                = 'complete';
            $finding->save();

            // ── Check if all non-conformity findings are now resolved ──
            $inspection = Pro_QaInspection::find($finding->inspection_id);
            if ($inspection) {
                $pending = Pro_QaInspectionFinding::where('inspection_id', $inspection->id)
                    ->where('is_conformity', 0)
                    ->where('status', '!=', 'complete')
                    ->count();

                if ($pending === 0) {
                    $this->notifyAllFindingsResolved($inspection);
                }
            }

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

            if ($inspection->completed_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette inspection est marquée comme terminée et ne peut pas être supprimée.',
                ], 403);
            }

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
     * Mark a QA inspection as done (sets date_performed to today)
     */
    public function markInspectionDone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'inspection_id' => 'required|integer|exists:pro_qa_inspections,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => implode(', ', $validator->errors()->all())], 422);
        }

        try {
            $inspection = Pro_QaInspection::findOrFail($request->inspection_id);

            if ($inspection->date_performed) {
                return response()->json(['success' => false, 'message' => 'This inspection is already marked as done.'], 409);
            }

            // Block if project is archived
            if ($inspection->project_id) {
                $project = Pro_Project::find($inspection->project_id);
                if ($project && $project->archived_at) {
                    return response()->json(['success' => false, 'message' => 'This project is archived. No modifications are allowed.'], 403);
                }
            }

            // Block Facility Inspection if not all sections are completed
            if ($inspection->type_inspection === 'Facility Inspection') {
                if ($inspection->facility_location === 'cove') {
                    $facilityRecord = \App\Models\Pro_Cl_FacilityInspectionCove::where('inspection_id', $inspection->id)->first();
                    $allSections    = ['a','b','c','d','e','f','g','h','i'];
                } else {
                    $facilityRecord = \App\Models\Pro_Cl_FacilityInspection::where('inspection_id', $inspection->id)->first();
                    $allSections    = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o'];
                }
                $sectionsDone = $facilityRecord ? (array)($facilityRecord->sections_done ?? []) : [];
                $total        = count($allSections);
                if (!empty(array_diff($allSections, $sectionsDone))) {
                    $count = count($sectionsDone);
                    return response()->json([
                        'success' => false,
                        'message' => "Impossible de finaliser : {$count}/{$total} sections complétées. Veuillez remplir toutes les sections du Facility Inspection Checklist.",
                    ], 422);
                }
            }

            // Block Process Inspection if not all sections are completed
            if ($inspection->type_inspection === 'Process Inspection') {
                $processRecord = \App\Models\Pro_Cl_ProcessInspection::where('inspection_id', $inspection->id)->first();
                $allSections   = ['a','b','c','d','e'];
                $sectionsDone  = $processRecord ? (array)($processRecord->sections_done ?? []) : [];
                $total         = count($allSections);
                if (!empty(array_diff($allSections, $sectionsDone))) {
                    $count = count($sectionsDone);
                    return response()->json([
                        'success' => false,
                        'message' => "Impossible de finaliser : {$count}/{$total} sections complétées. Veuillez remplir toutes les sections du Process Inspection Checklist.",
                    ], 422);
                }
            }

            // Block Study Protocol Inspection if not all sections are completed
            if ($inspection->type_inspection === 'Study Protocol Inspection') {
                $spRecord    = \App\Models\Pro_Cl_StudyProtocolInspection::where('inspection_id', $inspection->id)->first();
                $allSections = ['a','b','c','d','e','f'];
                $sectionsDone = $spRecord ? (array)($spRecord->sections_done ?? []) : [];
                $total        = count($allSections);
                if (!empty(array_diff($allSections, $sectionsDone))) {
                    $count = count($sectionsDone);
                    return response()->json([
                        'success' => false,
                        'message' => "Impossible de finaliser : {$count}/{$total} sections complétées. Veuillez remplir toutes les sections du Study Protocol Inspection Checklist.",
                    ], 422);
                }
            }

            // Block Study Report Inspection if not all sections are completed
            if ($inspection->type_inspection === 'Study Report Inspection') {
                $srRecord    = \App\Models\Pro_Cl_StudyReportInspection::where('inspection_id', $inspection->id)->first();
                $allSections = ['a','b','c','d','e'];
                $sectionsDone = $srRecord ? (array)($srRecord->sections_done ?? []) : [];
                $total        = count($allSections);
                if (!empty(array_diff($allSections, $sectionsDone))) {
                    $count = count($sectionsDone);
                    return response()->json([
                        'success' => false,
                        'message' => "Impossible de finaliser : {$count}/{$total} sections complétées. Veuillez remplir toutes les sections du Study Report Inspection Checklist.",
                    ], 422);
                }
            }

            // Block Data Quality Inspection if not all sections are completed
            if ($inspection->type_inspection === 'Data Quality Inspection') {
                $dqRecord    = \App\Models\Pro_Cl_DataQualityInspection::where('inspection_id', $inspection->id)->first();
                $allSections = ['a','b','c','d','e'];
                $sectionsDone = $dqRecord ? (array)($dqRecord->sections_done ?? []) : [];
                $total        = count($allSections);
                if (!empty(array_diff($allSections, $sectionsDone))) {
                    $count = count($sectionsDone);
                    return response()->json([
                        'success' => false,
                        'message' => "Impossible de finaliser : {$count}/{$total} sections complétées. Veuillez remplir toutes les sections du Data Quality Inspection Checklist.",
                    ], 422);
                }
            }

            $inspection->date_performed = now()->toDateString();
            $inspection->save();

            return response()->json(['success' => true, 'message' => 'Inspection marked as done.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Toggle a QA inspection's "completed" state.
     * Requires date_performed to be set and at least one finding to exist.
     */
    public function toggleInspectionCompleted(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'inspection_id' => 'required|integer|exists:pro_qa_inspections,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => implode(', ', $validator->errors()->all())], 422);
        }

        try {
            $inspection = Pro_QaInspection::findOrFail($request->inspection_id);

            if ($inspection->completed_at) {
                // Reopen
                $inspection->completed_at = null;
                $inspection->save();
                return response()->json(['success' => true, 'completed' => false, 'message' => 'Inspection reopened.']);
            }

            // Mark as completed — enforce prerequisites
            if (!$inspection->date_performed) {
                return response()->json(['success' => false, 'message' => 'The inspection form must be filled before marking as completed.'], 422);
            }
            if ($inspection->findings()->count() === 0) {
                return response()->json(['success' => false, 'message' => 'Findings must be documented before marking as completed.'], 422);
            }

            $inspection->completed_at = now();
            $inspection->save();

            return response()->json(['success' => true, 'completed' => true, 'message' => 'Inspection marked as completed.']);
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

    /**
     * Enregistrer un document de la Report Phase
     */
    public function saveReportDocument(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id'      => 'required|integer|exists:pro_projects,id',
            'document_type'   => 'required|string|in:final_report,scientific_article,publication_link,shared_data,report_amendment,other',
            'title'           => 'required|string|max:255',
            'description'     => 'nullable|string|max:2000',
            'url'             => 'nullable|url|max:500',
            'doi'             => 'nullable|string|max:255',
            'signature_date'  => 'nullable|date',
            'status'          => 'required|string|in:draft,submitted,published',
            'file'            => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png|max:20480',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => implode(', ', $validator->errors()->all()),
            ], 422);
        }

        try {
            $filePath = null;
            if ($request->hasFile('file')) {
                $filePath = $request->file('file')->store('report_phase_documents', 'public');
            }

            $doc = Pro_ReportPhaseDocument::create([
                'project_id'      => $request->project_id,
                'document_type'   => $request->document_type,
                'title'           => $request->title,
                'description'     => $request->description,
                'file_path'       => $filePath,
                'url'             => $request->url,
                'doi'             => $request->doi,
                'submission_date' => now()->toDateString(),
                'signature_date'  => $request->signature_date,
                'status'          => $request->status,
                'submitted_by'    => auth()->id(),
            ]);

            // Auto-inspection pour rapports finaux et amendements
            $inspectionTypeMap = [
                'final_report'     => 'Study Report Inspection',
                'report_amendment' => 'Study Report Amendment Inspection',
            ];
            if (isset($inspectionTypeMap[$doc->document_type])) {
                $inspection = Pro_QaInspection::create([
                    'project_id'      => $doc->project_id,
                    'type_inspection' => $inspectionTypeMap[$doc->document_type],
                    'inspection_name' => $doc->title . ' – QA Inspection',
                    'date_scheduled'  => $doc->signature_date ?? $doc->submission_date,
                    'qa_inspector_id' => $this->getQaManagerId(),
                ]);
                $doc->update(['qa_inspection_id' => $inspection->id]);
            }

            return response()->json([
                'success'  => true,
                'message'  => 'Document enregistré avec succès.',
                'document' => [
                    'id'              => $doc->id,
                    'document_type'   => $doc->document_type,
                    'title'           => $doc->title,
                    'description'     => $doc->description,
                    'file_path'       => $doc->file_path ? asset('storage/' . $doc->file_path) : null,
                    'url'             => $doc->url,
                    'doi'             => $doc->doi,
                    'submission_date' => $doc->submission_date,
                    'signature_date'  => $doc->signature_date,
                    'status'          => $doc->status,
                    'created_at'      => $doc->created_at->format('d/m/Y'),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    /**
     * Supprimer un document de la Report Phase
     */
    public function deleteReportDocument(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'document_id' => 'required|integer|exists:pro_report_phase_documents,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => implode(', ', $validator->errors()->all())], 422);
        }

        try {
            $doc = Pro_ReportPhaseDocument::findOrFail($request->document_id);
            if ($doc->file_path) {
                \Storage::disk('public')->delete($doc->file_path);
            }
            $doc->delete();

            return response()->json(['success' => true, 'message' => 'Document supprimé.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mettre à jour un document de la Report Phase
     */
    public function updateReportDocument(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'document_id'     => 'required|integer|exists:pro_report_phase_documents,id',
            'document_type'   => 'required|string|in:final_report,scientific_article,publication_link,shared_data,report_amendment,other',
            'title'           => 'required|string|max:255',
            'description'     => 'nullable|string|max:2000',
            'url'             => 'nullable|url|max:500',
            'doi'             => 'nullable|string|max:255',
            'signature_date'  => 'nullable|date',
            'status'          => 'required|string|in:draft,submitted,published',
            'file'            => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png|max:20480',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => implode(', ', $validator->errors()->all()),
            ], 422);
        }

        try {
            $doc = Pro_ReportPhaseDocument::findOrFail($request->document_id);

            if ($request->hasFile('file')) {
                if ($doc->file_path) {
                    \Storage::disk('public')->delete($doc->file_path);
                }
                $doc->file_path = $request->file('file')->store('report_phase_documents', 'public');
            }

            $doc->document_type  = $request->document_type;
            $doc->title          = $request->title;
            $doc->description    = $request->description;
            $doc->url            = $request->url;
            $doc->doi            = $request->doi;
            $doc->signature_date = $request->signature_date;
            $doc->status         = $request->status;
            $doc->save();

            // Mettre à jour l'inspection liée si elle existe, sinon créer
            $inspectionTypeMap = [
                'final_report'     => 'Study Report Inspection',
                'report_amendment' => 'Study Report Amendment Inspection',
            ];
            if (isset($inspectionTypeMap[$doc->document_type])) {
                $scheduledAt = $doc->signature_date ?? $doc->submission_date;
                if ($doc->qa_inspection_id) {
                    Pro_QaInspection::where('id', $doc->qa_inspection_id)
                        ->update(['date_scheduled' => $scheduledAt, 'inspection_name' => $doc->title . ' – QA Inspection']);
                } else {
                    $inspection = Pro_QaInspection::create([
                        'project_id'      => $doc->project_id,
                        'type_inspection' => $inspectionTypeMap[$doc->document_type],
                        'inspection_name' => $doc->title . ' – QA Inspection',
                        'date_scheduled'  => $scheduledAt,
                        'qa_inspector_id' => $this->getQaManagerId(),
                    ]);
                    $doc->update(['qa_inspection_id' => $inspection->id]);
                }
            }

            return response()->json([
                'success'  => true,
                'message'  => 'Document mis à jour.',
                'document' => [
                    'id'              => $doc->id,
                    'document_type'   => $doc->document_type,
                    'title'           => $doc->title,
                    'description'     => $doc->description,
                    'file_path'       => $doc->file_path ? asset('storage/' . $doc->file_path) : null,
                    'url'             => $doc->url,
                    'doi'             => $doc->doi,
                    'submission_date' => $doc->submission_date,
                    'signature_date'  => $doc->signature_date,
                    'status'          => $doc->status,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    // ─────────────────────────────────────────────
    // Archiving Phase
    // ─────────────────────────────────────────────

    /**
     * Archive a project (lock it)
     */
    public function archiveProject(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|integer|exists:pro_projects,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => implode(', ', $validator->errors()->all())], 422);
        }

        try {
            $project = Pro_Project::findOrFail($request->project_id);
            $project->archived_at    = now();
            $project->archived_by    = auth()->id();
            $project->project_stage  = 'archived';
            $project->save();

            return response()->json(['success' => true, 'message' => 'Projet archivé avec succès.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    /**
     * Unarchive a project (unlock it)
     */
    public function unarchiveProject(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|integer|exists:pro_projects,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => implode(', ', $validator->errors()->all())], 422);
        }

        try {
            $project = Pro_Project::findOrFail($request->project_id);
            $project->archived_at   = null;
            $project->archived_by   = null;
            $project->project_stage = 'in progress';
            $project->save();

            return response()->json(['success' => true, 'message' => 'Projet désarchivé.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    /**
     * Save archive checklist (JSON of manual confirmations)
     */
    public function saveArchiveChecklist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|integer|exists:pro_projects,id',
            'checklist'  => 'required|array',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => implode(', ', $validator->errors()->all())], 422);
        }

        try {
            $project = Pro_Project::findOrFail($request->project_id);
            $project->archive_checklist = $request->checklist;
            $project->save();

            return response()->json(['success' => true, 'message' => 'Checklist enregistrée.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    /**
     * Upload an archiving document
     */
    public function saveArchivingDocument(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id'        => 'required|integer|exists:pro_projects,id',
            'title'             => 'required|string|max:255',
            'description'       => 'nullable|string|max:2000',
            'document_type'     => 'nullable|string|max:100',
            'physical_location' => 'nullable|string|max:255',
            'archive_date'      => 'nullable|date',
            'file'              => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip|max:30720',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => implode(', ', $validator->errors()->all())], 422);
        }

        try {
            $filePath = null;
            if ($request->hasFile('file')) {
                $filePath = $request->file('file')->store('archiving_documents', 'public');
            }

            $doc = Pro_ArchivingDocument::create([
                'project_id'        => $request->project_id,
                'title'             => $request->title,
                'description'       => $request->description,
                'document_type'     => $request->document_type,
                'physical_location' => $request->physical_location,
                'archive_date'      => $request->archive_date,
                'file_path'         => $filePath,
                'uploaded_by'       => auth()->id(),
            ]);

            return response()->json([
                'success'  => true,
                'message'  => 'Document d\'archivage enregistré.',
                'document' => [
                    'id'                => $doc->id,
                    'title'             => $doc->title,
                    'document_type'     => $doc->document_type,
                    'physical_location' => $doc->physical_location,
                    'archive_date'      => $doc->archive_date,
                    'file_path'         => $doc->file_path ? asset('storage/' . $doc->file_path) : null,
                    'created_at'        => $doc->created_at->format('d/m/Y'),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete an archiving document
     */
    public function deleteArchivingDocument(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'document_id' => 'required|integer|exists:pro_archiving_documents,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => implode(', ', $validator->errors()->all())], 422);
        }

        try {
            $doc = Pro_ArchivingDocument::findOrFail($request->document_id);
            if ($doc->file_path) {
                \Storage::disk('public')->delete($doc->file_path);
            }
            $doc->delete();

            return response()->json(['success' => true, 'message' => 'Document supprimé.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    /**
     * Save archive submission date and/or deposition form file
     */
    public function saveArchiveSubmission(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id'              => 'required|integer|exists:pro_projects,id',
            'archive_submission_date' => 'nullable|date',
            'file'                    => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip|max:30720',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => implode(', ', $validator->errors()->all())], 422);
        }

        try {
            $project = Pro_Project::findOrFail($request->project_id);

            if ($request->has('archive_submission_date')) {
                $project->archive_submission_date = $request->archive_submission_date ?: null;
            }

            if ($request->hasFile('file')) {
                if ($project->archive_deposition_form_path) {
                    \Storage::disk('public')->delete($project->archive_deposition_form_path);
                }
                $project->archive_deposition_form_path = $request->file('file')->store('archive_deposition_forms', 'public');
            }

            $project->save();

            return response()->json([
                'success'                  => true,
                'message'                  => 'Informations d\'archivage enregistrées.',
                'archive_submission_date'  => $project->archive_submission_date,
                'archive_deposition_form_path' => $project->archive_deposition_form_path
                    ? asset('storage/' . $project->archive_deposition_form_path)
                    : null,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    /**
     * Toggle a project phase as completed / not completed.
     */
    public function togglePhaseCompleted(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|integer|exists:pro_projects,id',
            'phase'      => 'required|string|in:study_creation,protocol_details,protocol_development,planning,experimental,quality_assurance,reporting,archiving',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => implode(', ', $validator->errors()->all())], 422);
        }

        $project = Pro_Project::findOrFail($request->project_id);
        $phases  = $project->phases_completed ?? [];
        $phase   = $request->phase;
        $pid     = $project->id;

        $isMarking = !in_array($phase, $phases); // true = marking as done, false = unmarking

        // ── Server-side gate when marking as done ──────────────────────────
        if ($isMarking) {
            $error = null;

            switch ($phase) {
                case 'study_creation':
                    // study_director accepted from either the project record OR the appointment form
                    $sdFromAppt = DB::table('pro_study_director_appointment_forms')
                                    ->where('project_id', $pid)->where('active', true)
                                    ->whereNotNull('study_director')->value('study_director');
                    $sdOk    = !empty($project->study_director) || !empty($sdFromAppt);
                    $basicOk = !empty($project->project_code) && !empty($project->project_title) && $sdOk;
                    $apptOk  = DB::table('pro_study_director_appointment_forms')
                                 ->where('project_id', $pid)->where('active', true)
                                 ->whereNotNull('study_director')->whereNotNull('sd_appointment_date')->exists();
                    if (!$basicOk) $error = 'Basic project information is incomplete (project code, title, and study director are required).';
                    elseif (!$apptOk) $error = 'Study Director Appointment form is not filled.';
                    break;

                case 'protocol_development':
                    // Level 5 (Amendment/Deviation) is optional — exclude from mandatory count
                    $total     = DB::table('pro_protocols_devs_activities_projects')->where('project_id', $pid)->where('applicable', true)->where('level_activite', '<>', 5)->count();
                    $completed = DB::table('pro_protocols_devs_activities_projects')->where('project_id', $pid)->where('applicable', true)->where('level_activite', '<>', 5)->where('complete', true)->count();
                    if ($total === 0 || $completed < $total)
                        $error = "Not all protocol development activities are completed ({$completed}/{$total}).";
                    break;

                case 'planning':
                    $meetingDone   = DB::table('pro_studies_initiation_meetings')
                        ->where('project_id', $pid)
                        ->where('status', '!=', 'cancelled')
                        ->where('date_scheduled', '<=', now()->toDateString())
                        ->count() > 0;
                    $criticalCount = DB::table('pro_studies_activities')->where('project_id', $pid)->where('phase_critique', 1)->count();
                    if (!$meetingDone)    $error = 'The Study Initiation Meeting has not been completed.';
                    elseif ($criticalCount === 0) $error = 'No critical phases have been identified.';
                    break;

                case 'experimental':
                    $total     = DB::table('pro_studies_activities')->where('project_id', $pid)->count();
                    $completed = DB::table('pro_studies_activities')->where('project_id', $pid)->where('status', 'completed')->count();
                    if ($total === 0 || $completed < $total)
                        $error = "Not all activities are completed ({$completed}/{$total}).";
                    break;

                case 'quality_assurance':
                    $totalInsp     = DB::table('pro_qa_inspections')->where('project_id', $pid)->count();
                    $completedInsp = DB::table('pro_qa_inspections')->where('project_id', $pid)->whereNotNull('date_performed')->count();
                    $totalNc       = DB::table('pro_qa_inspections_findings')->where('project_id', $pid)->where('is_conformity', 0)->count();
                    $resolvedNc    = DB::table('pro_qa_inspections_findings')->where('project_id', $pid)->where('is_conformity', 0)->where('status', 'complete')->count();
                    if ($totalInsp === 0 || $completedInsp < $totalInsp)
                        $error = "Not all inspections are completed ({$completedInsp}/{$totalInsp}).";
                    elseif ($totalNc > 0 && $resolvedNc < $totalNc)
                        $error = "Not all NC findings are resolved ({$resolvedNc}/{$totalNc}).";
                    break;

                case 'reporting':
                    $count = DB::table('pro_report_phase_documents')->where('project_id', $pid)->count();
                    if ($count === 0) $error = 'No report documents have been added yet.';
                    break;

                case 'archiving':
                    $checklist = $project->archive_checklist ?? [];
                    $total     = 5; // physical_docs_archived, raw_data_secured, electronic_backup, samples_stored, personnel_notified
                    $checked   = count(array_filter($checklist));
                    if ($checked < $total) $error = "Archiving checklist is incomplete ({$checked}/{$total} items checked).";
                    break;
            }

            if ($error) {
                return response()->json(['success' => false, 'message' => $error], 422);
            }
        }

        if (in_array($phase, $phases)) {
            $phases = array_values(array_filter($phases, fn($p) => $p !== $phase));
            $done   = false;
        } else {
            $phases[] = $phase;
            $done     = true;
        }

        $project->phases_completed = $phases;
        $project->save();

        return response()->json(['success' => true, 'done' => $done, 'phases' => $phases]);
    }

    /**
     * Retourne l'ID du QA Manager depuis pro_key_facility_personnels (staff_role = 'Quality Assurance')
     */
    private function getQaManagerId(): ?int
    {
        $kfp = Pro_KeyFacilityPersonnel::where('staff_role', 'Quality Assurance')
                                       ->where('active', true)
                                       ->first();
        return $kfp?->personnel_id;
    }

    // ─────────────────────────────────────────────────────────────
    //  QA STATEMENT
    // ─────────────────────────────────────────────────────────────

    /**
     * Save (create or update) the QA Statement for a project.
     */
    public function saveQaStatement(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id'      => 'required|integer|exists:pro_projects,id',
            'status'          => 'required|in:draft,final',
            'date_signed'     => 'nullable|date',
            'qa_manager_id'   => 'nullable|integer|exists:personnels,id',
            'intro_text'      => 'nullable|string',
            'report_number'   => 'nullable|string|max:100',
            'doc_ref'         => 'nullable|string|max:100',
            'doc_issue_date'  => 'nullable|date',
            'doc_next_review' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $project = Pro_Project::findOrFail($request->project_id);

        // Block editing if already final
        $existing = \App\Models\Pro_QaStatement::where('project_id', $project->id)->first();
        if ($existing && $existing->status === 'final' && $request->status === 'draft') {
            return response()->json(['success' => false, 'message' => 'Un QA Statement finalisé ne peut pas être repassé en brouillon.'], 403);
        }

        $data = [
            'project_id'      => $project->id,
            'status'          => $request->status,
            'date_signed'     => $request->date_signed ?: null,
            'qa_manager_id'   => $request->qa_manager_id ?: $this->getQaManagerId(),
            'intro_text'      => $request->intro_text,
            'report_number'   => $request->report_number ?: $project->project_code,
            'doc_ref'         => $request->doc_ref         ?: 'QA-PR-L-001/09',
            'doc_issue_date'  => $request->doc_issue_date  ?: null,
            'doc_next_review' => $request->doc_next_review ?: null,
        ];

        if ($existing) {
            $existing->update($data);
            $statement = $existing->fresh();
        } else {
            $statement = \App\Models\Pro_QaStatement::create($data);
        }

        return response()->json(['success' => true, 'message' => 'QA Statement enregistré.', 'statement' => $statement]);
    }

    /**
     * Render the printable QA Statement for a project.
     */
    public function printQaStatement(Request $request)
    {
        $project_id = $request->project_id;
        $project    = Pro_Project::findOrFail($project_id);

        // Only GLP projects
        if (!$project->is_glp) {
            abort(403, 'QA Statement is only available for GLP projects.');
        }

        $statement = \App\Models\Pro_QaStatement::where('project_id', $project_id)->first();

        // Study Directors (all records for this project from appointment forms)
        $studyDirectors = \App\Models\Pro_StudyDirectorAppointmentForm::where('project_id', $project_id)
            ->with('studyDirector')
            ->orderBy('sd_appointment_date')
            ->get();

        // QA Manager
        $qaManagerId = $statement?->qa_manager_id ?? $this->getQaManagerId();
        $qaManager   = $qaManagerId ? Pro_Personnel::find($qaManagerId) : null;

        // Year of the statement (used to filter facility inspections)
        $statementYear = $statement?->date_signed
            ? \Carbon\Carbon::parse($statement->date_signed)->year
            : now()->year;

        // All non-facility inspections for this project
        $inspections = Pro_QaInspection::where('project_id', $project_id)
            ->where('type_inspection', '!=', 'Facility Inspection')
            ->with('inspector')
            ->orderBy('date_start')
            ->orderBy('date_scheduled')
            ->get();

        // Facility inspections filtered to the statement year only
        $facilityInspections = Pro_QaInspection::where('project_id', $project_id)
            ->where('type_inspection', 'Facility Inspection')
            ->where(function ($q) use ($statementYear) {
                $q->whereYear('date_start', $statementYear)
                  ->orWhere(function ($q2) use ($statementYear) {
                      $q2->whereNull('date_start')
                         ->whereYear('date_scheduled', $statementYear);
                  });
            })
            ->with('inspector')
            ->orderBy('facility_location')   // Cotonou before Cové alphabetically
            ->orderBy('date_start')
            ->orderBy('date_scheduled')
            ->get();

        // Split amendments and deviations from protocol A/D inspections
        $amendmentDevInspections = $inspections->where('type_inspection', 'Study Protocol Amendment/Deviation Inspection');
        $amendments = $amendmentDevInspections->filter(
            fn($i) => stripos($i->inspection_name ?? '', 'amendment') !== false
                   || stripos($i->inspection_name ?? '', 'amendement') !== false
        )->values();
        $deviations = $amendmentDevInspections->filter(
            fn($i) => stripos($i->inspection_name ?? '', 'deviation') !== false
                   || stripos($i->inspection_name ?? '', 'déviation') !== false
        )->values();
        // Any that match neither tag: put in amendments as fallback
        $untagged = $amendmentDevInspections->filter(
            fn($i) => $amendments->doesntContain('id', $i->id)
                   && $deviations->doesntContain('id', $i->id)
        )->values();
        $amendments = $amendments->merge($untagged);

        $groupedInspections = [
            'protocol'       => $inspections->where('type_inspection', 'Study Protocol Inspection')->values(),
            'amendments'     => $amendments,
            'deviations'     => $deviations,
            'critical_phases'=> $inspections->where('type_inspection', 'Critical Phase Inspection')->values(),
            'data_quality'   => $inspections->where('type_inspection', 'Data Quality Inspection')->values(),
            'report'         => $inspections->whereIn('type_inspection', [
                'Study Report Inspection',
                'Study Report Amendment Inspection',
            ])->values(),
            'facility'       => $facilityInspections,
        ];

        // Global document settings (fallback if not set on the statement itself)
        $globalSettings = AppSetting::allAsMap();

        return view('partials.qa-statement-print', compact(
            'project', 'statement', 'studyDirectors', 'qaManager',
            'inspections', 'groupedInspections', 'statementYear',
            'globalSettings'
        ));
    }

    // ── Private helpers ─────────────────────────────────────────────

    private function notifyAllFindingsResolved(Pro_QaInspection $inspection): void
    {
        $project  = $inspection->project;
        $reportUrl = route('checklist.report', $inspection->id);
        $msg       = "All non-conformity findings for inspection \"{$inspection->inspection_name}\" (project {$project?->project_code}) have been resolved. Please review and sign the QA Unit Report.";

        // Collect user IDs to notify: SD, QA Manager, QA Inspector, Facility Manager
        $userIds = collect();

        // Study Director
        $sdPersonnel = $project?->studyDirectorAppointmentForm?->studyDirector;
        if ($sdPersonnel?->user_id) {
            $userIds->push($sdPersonnel->user_id);
        }

        // QA Inspector
        $inspectorPersonnel = $inspection->inspector;
        if ($inspectorPersonnel?->user_id) {
            $userIds->push($inspectorPersonnel->user_id);
        }

        // All QA Managers and Facility Managers
        User::whereIn('role', ['qa_manager', 'facility_manager'])->each(function ($u) use ($userIds) {
            $userIds->push($u->id);
        });

        $userIds = $userIds->unique()->filter();

        foreach ($userIds as $uid) {
            AppNotification::send(
                $uid,
                'findings_resolved',
                "All findings resolved — {$inspection->inspection_name}",
                $msg,
                $reportUrl,
                'bi-check2-circle'
            );
        }

        // Send emails
        $recipients = collect();
        if ($sdPersonnel) {
            $email = $sdPersonnel->email_professionnel ?? $sdPersonnel->email_personnel;
            $name  = trim(($sdPersonnel->prenom ?? '') . ' ' . ($sdPersonnel->nom ?? ''));
            if ($email) $recipients->push(['email' => $email, 'name' => $name]);
        }
        if ($inspectorPersonnel && $inspectorPersonnel->id !== $sdPersonnel?->id) {
            $email = $inspectorPersonnel->email_professionnel ?? $inspectorPersonnel->email_personnel;
            $name  = trim(($inspectorPersonnel->prenom ?? '') . ' ' . ($inspectorPersonnel->nom ?? ''));
            if ($email) $recipients->push(['email' => $email, 'name' => $name]);
        }
        User::whereIn('role', ['qa_manager', 'facility_manager'])->with('personnel')->get()->each(function ($u) use ($recipients) {
            $email = $u->personnel?->email_professionnel ?? $u->personnel?->email_personnel ?? $u->email;
            $name  = $u->personnel ? trim(($u->personnel->prenom ?? '') . ' ' . ($u->personnel->nom ?? '')) : $u->name;
            if ($email) $recipients->push(['email' => $email, 'name' => $name]);
        });

        foreach ($recipients->unique('email') as $r) {
            try {
                Mail::to($r['email'])->send(new FindingsResolvedMail(
                    recipientName:  $r['name'],
                    projectCode:    $project?->project_code ?? '',
                    inspectionName: $inspection->inspection_name ?? '',
                    reportUrl:      $reportUrl,
                ));
            } catch (\Throwable) { /* fail silently */ }
        }
    }

    // ── QA Activities Checklist ───────────────────────────────────────

    /**
     * Save one or all rows of the QA Activities Checklist for a project.
     * Accepts JSON: { project_id, items: [ { item_number, date_performed, means_of_verification, is_checked } ] }
     */
    public function saveQaActivitiesChecklist(Request $request)
    {
        $request->validate([
            'project_id'            => 'required|exists:pro_projects,id',
            'items'                 => 'required|array|min:1',
            'items.*.item_number'   => 'required|integer|between:1,20',
            'items.*.date_performed'=> 'nullable|date',
            'items.*.means_of_verification' => 'nullable|string|max:500',
            'items.*.is_checked'    => 'nullable|boolean',
        ]);

        $pid    = $request->project_id;
        $userId = FacadesAuth::id();

        foreach ($request->items as $item) {
            Pro_QaActivitiesChecklist::updateOrCreate(
                ['project_id' => $pid, 'item_number' => $item['item_number']],
                [
                    'date_performed'        => $item['date_performed']        ?? null,
                    'means_of_verification' => $item['means_of_verification'] ?? null,
                    'is_checked'            => (bool) ($item['is_checked']    ?? false),
                    'updated_by'            => $userId,
                ]
            );
        }

        return response()->json(['success' => true, 'message' => 'Checklist saved.']);
    }

    /**
     * Print the QA Activities Checklist as an HTML page (printable).
     */
    public function printQaActivitiesChecklist(Request $request)
    {
        $projectId = $request->integer('project_id');

        $project = Pro_Project::with([
            'studyDirectorAppointmentForm.studyDirector',
            'protocolDeveloppementActivitiesProject',
            'reportPhaseDocuments',
            'archivingDocuments',
        ])->findOrFail($projectId);

        $sdForm  = $project->studyDirectorAppointmentForm;
        $sd      = $sdForm?->studyDirector;
        $sdName  = $sd ? trim(($sd->titre_personnel ?? '') . ' ' . $sd->prenom . ' ' . $sd->nom) : '';

        // Saved checklist rows keyed by item_number
        $saved = Pro_QaActivitiesChecklist::where('project_id', $projectId)
            ->get()->keyBy('item_number');

        // Pre-fill logic: derive dates from existing project data where possible
        $prefill = $this->qaChecklistPrefill($project);

        $activities = Pro_QaActivitiesChecklist::activities();
        $globalSettings = AppSetting::allAsMap();
        $headerImagePath = public_path('storage/assets/logo/airid.jpg');

        return view('partials.qa-activities-checklist-print', compact(
            'project', 'sdName', 'saved', 'prefill',
            'activities', 'globalSettings', 'headerImagePath'
        ));
    }

    /** Derive pre-fill dates for checklist items from project data. */
    private function qaChecklistPrefill(Pro_Project $project): array
    {
        $pf = [];

        // #1 – Protocol received from SD: first protocol dev doc upload date
        $firstProtoDoc = $project->protocolDeveloppementActivitiesProject
            ->flatMap(fn($a) => $a->protocolDevDocuments ?? collect())
            ->sortBy('date_upload')->first();
        if ($firstProtoDoc?->date_upload) {
            $pf[1] = ['date' => $firstProtoDoc->date_upload, 'mov' => 'Protocol development document'];
        }

        // #5 – Copy of approved protocol: last proto dev activity completed (level 3)
        $level3 = $project->protocolDeveloppementActivitiesProject->firstWhere('level_activite', 3);
        if ($level3?->real_date_performed) {
            $pf[5] = ['date' => $level3->real_date_performed, 'mov' => 'Signed protocol (level 3)'];
        }

        // #2,3,4 – Protocol inspections: QA inspection of type Study Inspection
        $protoInsp = \App\Models\Pro_QaInspection::where('project_id', $project->id)
            ->where('type_inspection', 'Study Inspection')
            ->orderBy('date_performed')->first();
        if ($protoInsp?->date_performed) {
            $pf[2] = ['date' => $protoInsp->date_performed, 'mov' => $protoInsp->inspection_name ?? 'Study Inspection'];
        }

        // #7 – Inspection programme established: first inspection scheduled date
        $firstInsp = \App\Models\Pro_QaInspection::where('project_id', $project->id)
            ->orderBy('date_scheduled')->first();
        if ($firstInsp?->date_scheduled) {
            $pf[7] = ['date' => $firstInsp->date_scheduled, 'mov' => 'First QA inspection scheduled'];
        }

        // #8 – Critical phase inspections: last critical phase inspection done
        $critInsp = \App\Models\Pro_QaInspection::where('project_id', $project->id)
            ->where('type_inspection', 'Critical Phase Inspection')
            ->whereNotNull('date_performed')
            ->orderByDesc('date_performed')->first();
        if ($critInsp?->date_performed) {
            $pf[8] = ['date' => $critInsp->date_performed, 'mov' => 'Critical Phase Inspection completed'];
        }

        // #9 – Data Quality: data quality inspection done
        $dqInsp = \App\Models\Pro_QaInspection::where('project_id', $project->id)
            ->where('type_inspection', 'Data Quality Inspection')
            ->whereNotNull('date_performed')
            ->orderByDesc('date_performed')->first();
        if ($dqInsp?->date_performed) {
            $pf[9] = ['date' => $dqInsp->date_performed, 'mov' => 'Data Quality Inspection completed'];
        }

        // #13 – Draft report received: draft report document submission date
        $draftReport = $project->reportPhaseDocuments
            ->where('document_type', 'draft_report')
            ->whereNotNull('submission_date')
            ->sortByDesc('submission_date')->first();
        if ($draftReport?->submission_date) {
            $pf[13] = ['date' => $draftReport->submission_date, 'mov' => 'Draft study report – ' . ($draftReport->title ?? '')];
        }

        // #16 – Final report received: final report document submission date
        $finalReport = $project->reportPhaseDocuments
            ->where('document_type', 'final_report')
            ->whereNotNull('submission_date')
            ->sortByDesc('submission_date')->first();
        if ($finalReport?->submission_date) {
            $pf[16] = ['date' => $finalReport->submission_date, 'mov' => 'Final study report – ' . ($finalReport->title ?? '')];
        }

        // #19 – QA Statement signed
        $qaStatement = \App\Models\Pro_QaStatement::where('project_id', $project->id)
            ->whereNotNull('date_signed')->first();
        if ($qaStatement?->date_signed) {
            $pf[19] = ['date' => $qaStatement->date_signed, 'mov' => 'QA Statement – ' . ($qaStatement->report_number ?? '')];
        }

        // #20 – Archiving: first archiving document date
        $archDoc = $project->archivingDocuments
            ->whereNotNull('archive_date')
            ->sortBy('archive_date')->first();
        if ($archDoc?->archive_date) {
            $pf[20] = ['date' => $archDoc->archive_date, 'mov' => 'QA file archived'];
        }

        return $pf;
    }
}

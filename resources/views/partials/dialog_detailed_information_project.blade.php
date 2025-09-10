<div class="modal fade" id="detailedInformationProjectModal" data-bs-backdrop="static" data-bs-keyboard="false"
    tabindex="-1" aria-labelledby="detailedInformationProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background: #c20102; ">
                <h1 class="modal-title fs-5" style="color : white" id="detailedInformationProjectModalLabel">Basic
                    Information on Project</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                {{-- Affichage des messages d'erreur --}}
                <div id="error-messages-detailed-information-project"></div>

                {{-- Formulaire --}}
                @php
                    $project_id = request()->get('project_id');
                    $project = null;
                    $all_study_types_project = [];
                    $all_products_types_project = [];
                    $all_lab_tests_project = [];

                    if ($project_id) {
                        $project = \App\Models\Pro_Project::find($project_id);

                        $all_study_types_project = $project->studyTypesApplied()->pluck('study_type_id')->toArray();
                        $all_products_types_project = $project
                            ->productTypesEvaluated()
                            ->pluck('product_type_id')
                            ->toArray();
                        $all_lab_tests_project = $project->labTestsConcerned()->pluck('lab_test_id')->toArray();
                    }

                @endphp
                <form action="{{ route('saveOtherBasicInformationOnProject') }}" method="POST"
                    id="formDetailedInformationProject">
                    {{-- CSRF Token --}}
                    @csrf

                    <input type="hidden" name="project_id" id="project_id_basic_information" value="{{ $project_id }}">

                    <div class="row">
                        <div class="col form-group-sm mt-2">
                            <label for="project_code" class="form-label"> <strong>Project Code</strong> <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="project_code" readonly name="project_code"
                                value="{{ $project->project_code ?? '' }}">
                        </div>

                        <div class="col form-group-sm mt-2">
                            <label for="project_title" class="form-label"> <strong>Project Title </strong> <span
                                    class="text-danger">*</span> </label>
                            <input type="text" class="form-control" id="project_title" name="project_title"
                                value="{{ $project->project_title ?? '' }}">
                        </div>

                        <div class="col form-group-sm mt-2">
                            <label for="protocol_code" class="form-label"> <strong>Protocol Code</strong> </label>
                            <input type="text" class="form-control" id="protocol_code" name="protocol_code"
                                value="{{ $project->protocol_code ?? '' }}">
                        </div>



                        {{-- <div class="col-12 col-sm-6 form-group-sm mt-2">
                            <label for="project_nature" class="form-label">Project Nature</label>
                            <select id="project_nature" name="project_nature"
                                class="form-select form-control selectpicker show-tick" data-live-search="true">
                                <option value="Evaluation_Phase_1"
                                    {{ isset($project) && $project->project_nature == 'Evaluation_Phase_1' ? 'selected' : '' }}>
                                    Evaluation Phase 1</option>
                                <option value="Evaluation_Phase_2"
                                    {{ isset($project) && $project->project_nature == 'Evaluation_Phase_2' ? 'selected' : '' }}>
                                    Evaluation Phase 2</option>
                                <option value="Evaluation_Phase_1_et_2"
                                    {{ isset($project) && $project->project_nature == 'Evaluation_Phase_1_et_2' ? 'selected' : '' }}>
                                    Evaluation Phase 1 et 2</option>
                                <option value="Community_Study"
                                    {{ isset($project) && $project->project_nature == 'Community_Study' ? 'selected' : '' }}>
                                    Community Study</option>
                            </select>
                        </div> --}}





                    </div>

                    @php
                        $all_study_types = App\Models\Pro_StudyType::orderBy('level_type')->get();
                        $all_product_types = App\Models\Pro_ProductType::orderBy('level_product')->get();
                        $all_lab_tests = App\Models\Pro_LabTest::orderBy('level_test')->get();
                    @endphp


                    <div class="row">

                        <div class="col form-group-sm mt-2">
                            <label for="is_glp" class="form-label"> <strong>Is it a GLP Project ?</strong> <span
                                    class="text-danger">*</span> </label>
                            <select id="is_glp" name="is_glp" class="form-select">
                                <option value="0" {{ isset($project) && $project->is_glp == 0 ? 'selected' : '' }}>
                                    No
                                </option>
                                <option value="1" {{ isset($project) && $project->is_glp == 1 ? 'selected' : '' }}>
                                    Yes
                                </option>
                            </select>
                        </div>

                        <div class="col mb-3 mt-2">
                            <label for="study_type_id_form_basic_info" class="form-label"> 
                                <strong>Select all appliable Study Types </strong>
                                <span class="text-danger">*</span> </label>

                            <select name="study_type_id[]" id="study_type_id_form_basic_info"
                                class=" form-control selectpicker " multiple data-live-search="true">

                                @forelse ($all_study_types??[] as $study_type)
                                    @if (in_array($study_type->id, $all_study_types_project))
                                        <option value="{{ $study_type->id }}" selected>
                                            {{ $study_type->study_type_name }}
                                        </option>
                                    @else
                                        <option value="{{ $study_type->id }}">{{ $study_type->study_type_name }}
                                        </option>
                                    @endif

                                @empty
                                @endforelse
                            </select>
                        </div>

                        <div class="col mb-3 mt-2">
                            <label for="product_type_id_basic_info" class="form-label"> <strong>Specify Evaluation Product</strong> <span
                                    class="text-danger">*</span> </label>

                            <select name="product_type_id[]" id="product_type_id_basic_info"
                                class=" form-control selectpicker " multiple data-live-search="true">

                                @forelse ($all_product_types??[] as $product_type)
                                    @if (in_array($product_type->id, $all_products_types_project))
                                        <option value="{{ $product_type->id }}" selected>
                                            {{ $product_type->product_type_name }}
                                        </option>
                                    @else
                                        <option value="{{ $product_type->id }}">
                                            {{ $product_type->product_type_name }}
                                        </option>
                                    @endif


                                @empty
                                @endforelse
                            </select>
                        </div>






                    </div>

                    <div class="row">

                        <div class="col mb-3  mt-2">
                            <label for="lab_test_id_basic_info" class="form-label"> <strong>All Lab Test appliable</strong> <span
                                    class="text-danger">*</span> </label>

                            <select name="lab_test_id[]" id="lab_test_id_basic_info" class=" form-control selectpicker "
                                multiple data-live-search="true">

                                @forelse ($all_lab_tests??[] as $lab_test)
                                    @if (in_array($lab_test->id, $all_lab_tests_project))
                                        <option value="{{ $lab_test->id }}" selected>
                                            {{ $lab_test->lab_test_name }}
                                        </option>
                                    @else
                                        <option value="{{ $lab_test->id }}">{{ $lab_test->lab_test_name }}
                                        </option>
                                    @endif

                                @empty
                                @endforelse
                            </select>
                        </div>


                        <div class="col mb-3 mt-2">
                            <label for="test_system" class="form-label"> <strong>Test System</strong> </label>
                            <select id="test_system" name="test_system" class=" form-control selectpicker show-tick"
                                data-live-search="true">
                                <option value="lab_mosquitoes"
                                    {{ isset($project) && $project->test_system == 'lab_mosquitoes' ? 'selected' : '' }}>
                                    Lab Mosquitoes</option>
                                <option value="field_mosquitoes"
                                    {{ isset($project) && $project->test_system == 'field_mosquitoes' ? 'selected' : '' }}>
                                    Field Mosquitoes</option>
                                <option value="lab_and_field_mosquitoes"
                                    {{ isset($project) && $project->test_system == 'lab_and_field_mosquitoes' ? 'selected' : '' }}>
                                    Lab and Field Mosquitoes</option>
                            </select>
                        </div>

                        <div class="col mb-3 mt-2">
                            <label for="project_stage" class="form-label"> <strong>Project Stage</strong> </label>
                            <select id="project_stage" name="project_stage"
                                class="form-select form-control selectpicker show-tick" data-live-search="true">
                                <option value="not_started"
                                    {{ isset($project) && $project->project_stage == 'not_started' ? 'selected' : '' }}>
                                    Not Started</option>
                                <option value="in progress"
                                    {{ isset($project) && $project->project_stage == 'in progress' ? 'selected' : '' }}>
                                    In Progress</option>
                                <option value="suspended"
                                    {{ isset($project) && $project->project_stage == 'suspended' ? 'selected' : '' }}>
                                    Suspended</option>
                                <option value="completed"
                                    {{ isset($project) && $project->project_stage == 'completed' ? 'selected' : '' }}>
                                    Completed</option>
                                <option value="archived"
                                    {{ isset($project) && $project->project_stage == 'archived' ? 'selected' : '' }}>
                                    Archived</option>
                                <option value="NA"
                                    {{ isset($project) && $project->project_stage == 'NA' ? 'selected' : '' }}>
                                    N/A</option>
                            </select>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-12 form-group-sm mt-2">
                            <label for="description_project" class="form-label"> <strong>Description of the Project</strong> </label>
                            <textarea class="form-control" id="description_project" name="description_project" rows="3">{{ $project->description_project ?? '' }}</textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-outline-danger " style="float: right">Save
                                Changes</button>
                        </div>
                    </div>




                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

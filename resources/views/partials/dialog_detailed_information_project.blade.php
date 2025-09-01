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

                @php
                    $project_id = request()->get('project_id');
                    $project = null;
                    if ($project_id) {
                        $project = \App\Models\Pro_Project::find($project_id);
                    }

                @endphp
                <form action="#" method="POST" id="formDetailedInformationProject">
                    {{-- CSRF Token --}}
                    @csrf

                    <input type="hidden" name="project_id" value="{{ $project_id }}">

                    <div class="row">
                        <div class="col-12 col-sm-6 form-group-sm mt-2">
                            <label for="project_code" class="form-label">Project Code</label>
                            <input type="text" class="form-control" id="project_code" readonly name="project_code"
                                value="{{ $project->project_code ?? '' }}">
                        </div>

                        <div class="col-12 col-sm-6 form-group-sm mt-2">
                            <label for="project_title" class="form-label">Project Title</label>
                            <input type="text" class="form-control" id="project_title" name="project_title"
                                value="{{ $project->project_title ?? '' }}">
                        </div>

                        <div class="col-12 col-sm-6 form-group-sm mt-2">
                            <label for="is_glp" class="form-label">Is it a GLP Project ?</label>
                            <select id="is_glp" name="is_glp" class="form-select">
                                <option value="0" {{ isset($project) && $project->is_glp == 0 ? 'selected' : '' }}>
                                    No
                                </option>
                                <option value="1" {{ isset($project) && $project->is_glp == 1 ? 'selected' : '' }}>
                                    Yes
                                </option>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 form-group-sm mt-2">
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
                        </div>

                        <div class="col-12 col-sm-6 form-group-sm mt-2">
                            <label for="test_system" class="form-label">Test System</label>
                            <select id="test_system" name="test_system"
                                class="form-select form-control selectpicker show-tick" data-live-search="true">
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

                        <div class="col-12 col-sm-6 form-group-sm mt-2">
                            <label for="study_director" class="form-label">Study Director</label>
                            <select id="study_director" name="study_director"
                                class="form-select form-control selectpicker show-tick" data-live-search="true">

                                @foreach ($all_personnels as $personnel)
                                    <option value="{{ $personnel->id }}"
                                        {{ isset($project) && $project->study_director == $personnel->id ? 'selected' : '' }}>
                                        {{ $personnel->titre }} {{ $personnel->prenom }} {{ $personnel->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 form-group-sm mt-2">
                            <label for="project_manager" class="form-label">Project Manager</label>
                            <select id="project_manager" name="project_manager"
                                class="form-select form-control selectpicker show-tick" data-live-search="true">

                                @foreach ($all_personnels as $personnel)
                                    <option value="{{ $personnel->id }}"
                                        {{ isset($project) && $project->project_manager == $personnel->id ? 'selected' : '' }}>
                                        {{ $personnel->titre }} {{ $personnel->prenom }} {{ $personnel->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 form-group-sm mt-2">
                            <label for="project_stage" class="form-label">Project Stage</label>
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
                            <label for="description_project" class="form-label">Description of the Project</label>
                            <textarea class="form-control" id="description_project" name="description_project" rows="3">{{ $project->description_project ?? '' }}</textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-outline-danger " style="float: right">Save Changes</button>
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

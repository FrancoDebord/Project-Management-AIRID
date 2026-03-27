<div class="modal fade" id="detailedInformationProjectModal" data-bs-backdrop="static" data-bs-keyboard="false"
    tabindex="-1" aria-labelledby="detailedInformationProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header" style="background:#c20102;color:#fff;">
                <h5 class="modal-title" id="detailedInformationProjectModalLabel">
                    <i class="bi bi-folder2-open me-2"></i>Basic Information on Project
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-4 py-3">

                <div id="error-messages-detailed-information-project" class="mb-3"></div>

                @php
                    $project_id = request()->get('project_id');
                    $project    = null;
                    $all_study_types_project    = [];
                    $all_products_types_project = [];
                    $all_lab_tests_project      = [];

                    if ($project_id) {
                        $project = \App\Models\Pro_Project::find($project_id);
                        if ($project) {
                            $all_study_types_project    = $project->studyTypesApplied()->pluck('study_type_id')->toArray();
                            $all_products_types_project = $project->productTypesEvaluated()->pluck('product_type_id')->toArray();
                            $all_lab_tests_project      = $project->labTestsConcerned()->pluck('lab_test_id')->toArray();
                        }
                    }

                    $all_study_types   = App\Models\Pro_StudyType::orderBy('level_type')->get();
                    $all_product_types = App\Models\Pro_ProductType::orderBy('level_product')->get();
                    $all_lab_tests     = App\Models\Pro_LabTest::orderBy('level_test')->get();
                @endphp

                <form action="{{ route('saveOtherBasicInformationOnProject') }}" method="POST"
                      id="formDetailedInformationProject">
                    @csrf
                    <input type="hidden" name="project_id" id="project_id_basic_information" value="{{ $project_id }}">

                    {{-- ── Identification ─────────────────────────────────── --}}
                    <p class="fw-bold text-uppercase small text-muted mb-2" style="letter-spacing:.06em;">
                        <i class="bi bi-tag me-1"></i>Identification
                    </p>
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">
                                Project Code <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control bg-light" id="project_code" name="project_code"
                                       readonly value="{{ $project->project_code ?? '' }}">
                                <button type="button" class="btn btn-outline-secondary" id="btn-unlock-code"
                                        title="Unlock to edit project code"
                                        onclick="toggleProjectCodeEdit()">
                                    <i class="bi bi-lock-fill" id="icon-lock-code"></i>
                                </button>
                            </div>
                            <div class="form-text text-warning d-none" id="code-edit-warning">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>Changing the code will update all references.
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">
                                Project Title <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="project_title" name="project_title"
                                   value="{{ $project->project_title ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Protocol Code</label>
                            <input type="text" class="form-control" id="protocol_code" name="protocol_code"
                                   value="{{ $project->protocol_code ?? '' }}">
                        </div>
                    </div>

                    {{-- ── Classification ─────────────────────────────────── --}}
                    <p class="fw-bold text-uppercase small text-muted mb-2" style="letter-spacing:.06em;">
                        <i class="bi bi-tags me-1"></i>Classification
                    </p>
                    <div class="row g-3 mb-3">
                        <div class="col-md-2">
                            <label class="form-label fw-semibold small">
                                GLP Project ? <span class="text-danger">*</span>
                            </label>
                            <select id="is_glp" name="is_glp" class="form-select">
                                <option value="0" {{ isset($project) && !$project->is_glp ? 'selected' : '' }}>No</option>
                                <option value="1" {{ isset($project) && $project->is_glp  ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Project Nature</label>
                            <select id="project_nature" name="project_nature" class="form-select">
                                <option value="">— Select —</option>
                                <option value="Evaluation_Phase_1"        {{ isset($project) && $project->project_nature == 'Evaluation_Phase_1'        ? 'selected' : '' }}>Evaluation Phase 1</option>
                                <option value="Evaluation_Phase_2"        {{ isset($project) && $project->project_nature == 'Evaluation_Phase_2'        ? 'selected' : '' }}>Evaluation Phase 2</option>
                                <option value="Evaluation_Phase_1_et_2"   {{ isset($project) && $project->project_nature == 'Evaluation_Phase_1_et_2'   ? 'selected' : '' }}>Evaluation Phase 1 & 2</option>
                                <option value="Community_Study"           {{ isset($project) && $project->project_nature == 'Community_Study'           ? 'selected' : '' }}>Community Study</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Test System</label>
                            <select id="test_system" name="test_system" class="form-select">
                                <option value="">— Select —</option>
                                <option value="lab_mosquitoes"            {{ isset($project) && $project->test_system == 'lab_mosquitoes'            ? 'selected' : '' }}>Lab Mosquitoes</option>
                                <option value="field_mosquitoes"          {{ isset($project) && $project->test_system == 'field_mosquitoes'          ? 'selected' : '' }}>Field Mosquitoes</option>
                                <option value="lab_and_field_mosquitoes"  {{ isset($project) && $project->test_system == 'lab_and_field_mosquitoes'  ? 'selected' : '' }}>Lab and Field Mosquitoes</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Project Stage</label>
                            <select id="project_stage" name="project_stage" class="form-select">
                                <option value="">— Select —</option>
                                <option value="not_started"  {{ isset($project) && $project->project_stage == 'not_started'  ? 'selected' : '' }}>Not Started</option>
                                <option value="in progress"  {{ isset($project) && $project->project_stage == 'in progress'  ? 'selected' : '' }}>In Progress</option>
                                <option value="suspended"    {{ isset($project) && $project->project_stage == 'suspended'    ? 'selected' : '' }}>Suspended</option>
                                <option value="completed"    {{ isset($project) && $project->project_stage == 'completed'    ? 'selected' : '' }}>Completed</option>
                                <option value="archived"     {{ isset($project) && $project->project_stage == 'archived'     ? 'selected' : '' }}>Archived</option>
                                <option value="NA"           {{ isset($project) && $project->project_stage == 'NA'           ? 'selected' : '' }}>N/A</option>
                            </select>
                        </div>
                    </div>

                    {{-- ── Dates ───────────────────────────────────────────── --}}
                    <p class="fw-bold text-uppercase small text-muted mb-2" style="letter-spacing:.06em;">
                        <i class="bi bi-calendar3 me-1"></i>Dates
                    </p>
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Planned Start Date</label>
                            <input type="date" class="form-control" name="date_debut_previsionnelle"
                                   id="date_debut_previsionnelle"
                                   value="{{ $project->date_debut_previsionnelle ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Planned End Date</label>
                            <input type="date" class="form-control" name="date_fin_previsionnelle"
                                   id="date_fin_previsionnelle"
                                   value="{{ $project->date_fin_previsionnelle ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Actual Start Date</label>
                            <input type="date" class="form-control" name="date_debut_effective"
                                   id="date_debut_effective"
                                   value="{{ $project->date_debut_effective ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Actual End Date</label>
                            <input type="date" class="form-control" name="date_fin_effective"
                                   id="date_fin_effective"
                                   value="{{ $project->date_fin_effective ?? '' }}">
                        </div>
                    </div>

                    {{-- ── Study Classifications ───────────────────────────── --}}
                    <p class="fw-bold text-uppercase small text-muted mb-2" style="letter-spacing:.06em;">
                        <i class="bi bi-journals me-1"></i>Study Classifications
                        <span class="text-danger">*</span>
                    </p>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Study Types</label>
                            <select name="study_type_id[]" id="study_type_id_form_basic_info"
                                    class="form-select" multiple size="5">
                                @foreach ($all_study_types as $st)
                                    <option value="{{ $st->id }}"
                                        {{ in_array($st->id, $all_study_types_project) ? 'selected' : '' }}>
                                        {{ $st->study_type_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Evaluation Products</label>
                            <select name="product_type_id[]" id="product_type_id_basic_info"
                                    class="form-select" multiple size="5">
                                @foreach ($all_product_types as $pt)
                                    <option value="{{ $pt->id }}"
                                        {{ in_array($pt->id, $all_products_types_project) ? 'selected' : '' }}>
                                        {{ $pt->product_type_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Lab Tests</label>
                            <select name="lab_test_id[]" id="lab_test_id_basic_info"
                                    class="form-select" multiple size="5">
                                @foreach ($all_lab_tests as $lt)
                                    <option value="{{ $lt->id }}"
                                        {{ in_array($lt->id, $all_lab_tests_project) ? 'selected' : '' }}>
                                        {{ $lt->lab_test_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <p class="text-muted small mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Hold <kbd>Ctrl</kbd> (or <kbd>Cmd</kbd> on Mac) to select multiple items.
                    </p>

                    {{-- ── Description ─────────────────────────────────────── --}}
                    <div class="mb-2">
                        <label class="form-label fw-semibold small">
                            <i class="bi bi-card-text me-1"></i>Description of the Project
                        </label>
                        <textarea class="form-control" id="description_project" name="description_project"
                                  rows="3" placeholder="Optional description…">{{ $project->description_project ?? '' }}</textarea>
                    </div>

                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="formDetailedInformationProject"
                        class="btn btn-danger fw-semibold px-4">
                    <i class="bi bi-save me-1"></i>Save Changes
                </button>
            </div>

        </div>
    </div>
</div>

<script>
document.getElementById('detailedInformationProjectModal').addEventListener('show.bs.modal', function () {
    var err = document.getElementById('error-messages-detailed-information-project');
    if (err) { err.innerHTML = ''; err.className = 'mb-3'; }
    // Re-lock project code on modal open
    var input   = document.getElementById('project_code');
    var icon    = document.getElementById('icon-lock-code');
    var warning = document.getElementById('code-edit-warning');
    var btn     = document.getElementById('btn-unlock-code');
    if (input)   { input.readOnly = true; input.classList.add('bg-light'); }
    if (icon)    { icon.className = 'bi bi-lock-fill'; }
    if (warning) { warning.classList.add('d-none'); }
    if (btn)     { btn.classList.remove('btn-warning'); btn.classList.add('btn-outline-secondary'); }
});

function toggleProjectCodeEdit() {
    var input   = document.getElementById('project_code');
    var icon    = document.getElementById('icon-lock-code');
    var warning = document.getElementById('code-edit-warning');
    var btn     = document.getElementById('btn-unlock-code');
    var locked  = input.readOnly;

    input.readOnly = !locked;
    input.classList.toggle('bg-light', locked);
    icon.className = locked ? 'bi bi-unlock-fill' : 'bi bi-lock-fill';
    warning.classList.toggle('d-none', locked);
    btn.classList.toggle('btn-warning', locked);
    btn.classList.toggle('btn-outline-secondary', !locked);
    if (!locked) { input.focus(); input.select(); }
}
</script>

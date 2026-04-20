<!-- Modal Add / Edit Activity -->
<div class="modal fade" id="ModalAddActivity" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            @php
                $project_id = request()->get('project_id');
            @endphp

            <div class="modal-header" style="background:#c20102;color:#fff;">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Add / Edit Activity</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-4 py-3">
                <div id="div-messages-error" class="mb-2"></div>

                <form id="formAddActivity" action="{{ route('saveActivityProject') }}" method="POST">
                    @csrf
                    <input type="hidden" name="project_id"     id="project_id_add_activity"   value="{{ $project_id }}">
                    <input type="hidden" name="study_type_id"  id="study_type_id_add_activity" value="">
                    <input type="hidden" name="id"             id="activity_id"               value="">
                    {{-- Sub-category transmis silencieusement par le JS --}}
                    <input type="hidden" name="study_sub_category_id" id="study_sub_category_id">

                    {{-- Nom de l'activité --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Activity Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="study_activity_name" name="study_activity_name" required
                               placeholder="Describe the activity…">
                    </div>

                    {{-- Description --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Description</label>
                        <textarea class="form-control" id="activity_description" name="activity_description"
                                  rows="2" placeholder="Optional…"></textarea>
                    </div>

                    {{-- Parent Activity --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Parent Activity</label>
                        <select name="parent_activity_id" id="parent_activity_id" class="form-select form-select-sm">
                        </select>
                    </div>

                    {{-- Dates --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Start Due Date <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="estimated_activity_date"
                                   name="estimated_activity_date" required placeholder="YYYY-MM-DD">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">End Due Date <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="estimated_activity_end_date"
                                   name="estimated_activity_end_date" required placeholder="YYYY-MM-DD">
                        </div>
                    </div>

                    {{-- Responsible Person --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Responsible Person <span class="text-danger">*</span></label>
                        <select name="should_be_performed_by" id="should_be_performed_by" class="form-select">
                        </select>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-danger fw-semibold px-4">
                            <i class="bi bi-save me-1"></i>Save Activity
                        </button>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
// Tom Select for responsible person — reinitialise each time the modal opens
// (jQuery populates the <select> options just before .modal('show') is called)
document.getElementById('ModalAddActivity').addEventListener('show.bs.modal', function () {
    var el = document.getElementById('should_be_performed_by');
    if (el && el.tomselect) { el.tomselect.destroy(); }
    if (el) {
        new TomSelect(el, {
            allowEmptyOption: true,
            placeholder: '— Select responsible person —',
            sortField: { field: 'text', direction: 'asc' }
        });
    }
});
// Tom Select for parent activity — reinitialise each time the modal opens
document.getElementById('ModalAddActivity').addEventListener('show.bs.modal', function () {
    var el2 = document.getElementById('parent_activity_id');
    if (el2 && el2.tomselect) { el2.tomselect.destroy(); }
    if (el2) {
        new TomSelect(el2, {
            allowEmptyOption: true,
            placeholder: '— None —',
            sortField: { field: 'text', direction: 'asc' }
        });
    }
});

// Submit handled by javascript_ajax.js (#formAddActivity jQuery handler)
</script>

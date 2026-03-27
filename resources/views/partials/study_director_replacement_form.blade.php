@php
    $project_id  = request()->get('project_id');
    $project     = $project_id ? \App\Models\Pro_Project::find($project_id) : null;
    $all_personnels_repl = \App\Models\Pro_Personnel::orderBy('nom')->get();
@endphp

<div class="modal fade" id="replacementModal" tabindex="-1" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 overflow-hidden">

            <div class="modal-header" style="background:linear-gradient(135deg,#7b1fa2,#9c27b0);color:#fff;">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-person-fill-gear me-2"></i>Study Director Replacement
                    @if($project) — <span class="fw-normal opacity-75">{{ $project->project_code }}</span>@endif
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body px-4 py-4">
                <div id="error-messages-study-director-replacement" class="mb-3"></div>

                <form action="{{ route('saveStudyDirectorReplacementForm') }}" method="POST"
                      enctype="multipart/form-data" id="form_study_director_replacement">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project_id }}">

                    <div class="row g-3">

                        {{-- New Study Director --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">New Study Director Appointed <span class="text-danger">*</span></label>
                            <select id="repl_study_director" name="study_director" class="form-select">
                                <option value="">— Select Study Director —</option>
                                @foreach($all_personnels_repl as $p)
                                    <option value="{{ $p->id }}">{{ trim(($p->titre ?? '') . ' ' . $p->prenom . ' ' . $p->nom) }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Project Manager --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Project Manager</label>
                            <select id="repl_project_manager" name="project_manager" class="form-select">
                                <option value="">— Select Project Manager —</option>
                                @foreach($all_personnels_repl as $p)
                                    <option value="{{ $p->id }}"
                                        {{ $project && $project->project_manager == $p->id ? 'selected' : '' }}>
                                        {{ trim(($p->titre ?? '') . ' ' . $p->prenom . ' ' . $p->nom) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Date of Replacement --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Date of Replacement <span class="text-danger">*</span></label>
                            <input type="date" id="replacement_date" name="replacement_date" class="form-control">
                        </div>

                        {{-- File Upload --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Signed Replacement Form (PDF)</label>
                            <input type="file" id="sd_appointment_file" name="sd_appointment_file"
                                   class="form-control" accept="application/pdf">
                            <div class="form-text">PDF only</div>
                        </div>

                        {{-- Reason --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Replacement Reason</label>
                            <textarea id="replacement_reason" name="replacement_reason"
                                      class="form-control" rows="3"
                                      placeholder="Describe the reason for this replacement…"></textarea>
                        </div>

                        <div class="col-12 text-end mt-2">
                            <button type="submit" class="btn fw-semibold px-4"
                                    style="background:linear-gradient(135deg,#7b1fa2,#9c27b0);color:#fff;">
                                <i class="bi bi-save me-1"></i>Submit Replacement
                            </button>
                        </div>

                    </div>
                </form>
            </div>

            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    function initReplTomSelects() {
        ['repl_study_director', 'repl_project_manager'].forEach(function(id) {
            var el = document.getElementById(id);
            if (!el) return;
            if (el.tomselect) el.tomselect.destroy();
            new TomSelect(el, {
                placeholder: el.options[0]?.text || '— Select —',
                allowEmptyOption: true,
                sortField: { field: 'text', direction: 'asc' }
            });
        });
    }
    document.getElementById('replacementModal').addEventListener('show.bs.modal', initReplTomSelects);
})();
</script>

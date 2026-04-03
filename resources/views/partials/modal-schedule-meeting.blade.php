@php
    $meeting_project_id = request('project_id');
    $meeting_project    = App\Models\Pro_Project::with(['studyDirector','projectManager'])->find($meeting_project_id);

    // Compute default participant IDs to pre-select
    $defaultParticipantIds = [];
    if ($meeting_project) {
        if ($meeting_project->study_director)
            $defaultParticipantIds[] = $meeting_project->study_director;
        if ($meeting_project->project_manager)
            $defaultParticipantIds[] = $meeting_project->project_manager;
    }
    // QA Manager from key facility personnel
    $keyPersonnelRows = App\Models\Pro_KeyFacilityPersonnel::where('active', 1)->get();
    foreach ($keyPersonnelRows as $kp) {
        $defaultParticipantIds[] = $kp->personnel_id;
    }
    $defaultParticipantIds = array_values(array_unique(array_filter($defaultParticipantIds)));
@endphp

<div class="modal fade" id="meetingModal" tabindex="-1" aria-labelledby="meetingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">

            {{-- Header --}}
            <div class="modal-header border-0 py-3 px-4"
                 style="background:linear-gradient(135deg,#1a3a6b 0%,#c41230 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div style="background:rgba(255,255,255,.15);border-radius:10px;width:40px;height:40px;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-calendar-event-fill text-white fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0" id="meetingModalLabel">
                            Study Initiation Meeting
                        </h5>
                        <p class="text-white-50 small mb-0">Schedule or update meeting details</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body px-4 py-3">
                <div id="div-messages-error7"></div>

                <form id="meetingForm" action="{{ route('scheduleStudyInitiationMeeting') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="project_id"   id="project_id_qa_meeting" value="{{ $meeting_project_id }}">
                    <input type="hidden" name="meeting_id"   id="qa_meeting_id" value="">
                    <input type="hidden" name="meeting_type" id="qa_meeting_type" value="study_initiation_meeting">
                    <input type="hidden" id="default_participant_ids" value="{{ json_encode($defaultParticipantIds) }}">

                    {{-- Date / Time / Link --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold mb-1">
                                <i class="bi bi-calendar3 me-1 text-danger"></i>Date <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-sm"
                                   id="meeting_date" name="meeting_date"
                                   placeholder="dd/mm/yyyy" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold mb-1">
                                <i class="bi bi-clock me-1 text-danger"></i>Time <span class="text-danger">*</span>
                            </label>
                            <input type="time" class="form-control form-control-sm"
                                   name="meeting_time" id="meeting_time" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold mb-1">
                                <i class="bi bi-link-45deg me-1 text-danger"></i>Meeting Link
                                <span class="text-muted fw-normal">(optional)</span>
                            </label>
                            <input type="url" class="form-control form-control-sm"
                                   name="meeting_link" id="meeting_link"
                                   placeholder="https://meet.google.com/...">
                        </div>
                    </div>

                    {{-- Participants --}}
                    <div class="mb-3">
                        <label class="form-label small fw-semibold mb-1">
                            <i class="bi bi-people-fill me-1 text-danger"></i>Participants <span class="text-danger">*</span>
                        </label>
                        <div class="p-2 rounded-2 mb-1" style="background:#fff7f7;border:1px solid #f0d0d0;font-size:.75rem;color:#888;">
                            <i class="bi bi-info-circle me-1"></i>
                            Study Director, QA Manager, Project Manager and Key Personnel are pre-selected. Add or remove as needed.
                        </div>
                        <select id="meeting_participants" name="participants[]"
                                class="form-select form-select-sm" multiple style="height:auto;">
                            @foreach ($all_personnels as $personnel)
                                <option value="{{ $personnel->id }}">
                                    {{ trim(($personnel->titre_personnel ?? $personnel->titre ?? '') . ' ' . $personnel->prenom . ' ' . $personnel->nom) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Description + Agenda --}}
                    <div class="row g-3 mb-2">
                        <div class="col-md-7">
                            <label class="form-label small fw-semibold mb-1">
                                <i class="bi bi-card-text me-1 text-danger"></i>Brief description
                            </label>
                            <textarea name="breve_description" class="form-control form-control-sm"
                                      id="breve_description" rows="3"
                                      placeholder="Purpose, objectives of the meeting…"></textarea>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold mb-1">
                                <i class="bi bi-file-earmark-text me-1 text-danger"></i>Agenda
                                <span class="text-muted fw-normal">(optional)</span>
                            </label>
                            <input type="file" class="form-control form-control-sm"
                                   name="meeting_file" id="meeting_file"
                                   accept=".pdf,.doc,.docx,.xls,.xlsx">
                            <div class="form-text" style="font-size:.7rem;">PDF, Word, Excel</div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer border-0 px-4 pb-4 pt-2 gap-2" style="background:#f8f9fa;">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x me-1"></i>Cancel
                </button>
                <button type="submit" form="meetingForm"
                        class="btn btn-sm fw-semibold text-white"
                        style="background:linear-gradient(90deg,#1a3a6b,#c41230);border:none;min-width:140px;">
                    <i class="bi bi-calendar-check me-1"></i>Save Meeting
                </button>
            </div>

        </div>
    </div>
</div>

<script>
(function () {
    let _meetingTomSelect = null;

    function initMeetingTomSelect() {
        if (_meetingTomSelect) return;
        if (typeof TomSelect === 'undefined') return;

        _meetingTomSelect = new TomSelect('#meeting_participants', {
            plugins: ['remove_button'],
            placeholder: 'Search and select participants…',
            maxOptions: null,
            persist: false,
            onInitialize() {
                // Pre-select default participants for a new meeting
                const ids = JSON.parse(document.getElementById('default_participant_ids').value || '[]');
                if (ids.length) this.setValue(ids.map(String));
            },
        });
    }

    // Init when modal first opens
    document.addEventListener('show.bs.modal', function (e) {
        if (e.target && e.target.id === 'meetingModal') {
            initMeetingTomSelect();
        }
    });

    // Expose a function for the legacy JS to populate on edit
    window.setMeetingParticipants = function (participantIds) {
        if (_meetingTomSelect) {
            _meetingTomSelect.clear(true);
            _meetingTomSelect.setValue(participantIds.map(String));
        } else {
            // Fallback: set after a tick
            setTimeout(function () {
                if (_meetingTomSelect) {
                    _meetingTomSelect.clear(true);
                    _meetingTomSelect.setValue(participantIds.map(String));
                }
            }, 100);
        }
    };

    // Reset participants to defaults when opening a NEW meeting
    window.resetMeetingToDefaults = function () {
        if (_meetingTomSelect) {
            const ids = JSON.parse(document.getElementById('default_participant_ids').value || '[]');
            _meetingTomSelect.clear(true);
            _meetingTomSelect.setValue(ids.map(String));
        }
    };
})();
</script>

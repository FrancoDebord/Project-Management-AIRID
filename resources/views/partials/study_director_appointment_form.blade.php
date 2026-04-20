<div class="modal fade" id="customModal" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            @php
                $project_id = request()->get('project_id');
                $project    = null;
                $sda        = null;
                if ($project_id) {
                    $project = \App\Models\Pro_Project::find($project_id);
                    if ($project) {
                        $sda = $project->studyDirectorAppointmentForm;
                    }
                }
                $isUpdate = $sda && $sda->sd_appointment_file;

                // Only personnel with an active Study Director designation
                $sd_personnels = \App\Models\Pro_StudyDirector::where('active', true)
                    ->with('personnel')
                    ->get()
                    ->pluck('personnel')
                    ->filter()
                    ->sortBy('nom');

                // Only personnel currently under contract for the Project Manager select
                $pm_personnels = \App\Models\Pro_Personnel::where('sous_contrat', 1)
                    ->orderBy('prenom')
                    ->get();
            @endphp

            <!-- Header -->
            <div class="modal-header" style="background-color:#c20102;color:#fff;">
                <h5 class="modal-title" id="customModalLabel">
                    <i class="bi bi-person-badge-fill me-2"></i>
                    Study Director Appointment Form
                    @if($project) — <span class="fw-normal">{{ $project->project_code }}</span>@endif
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body px-4 py-3">

                <div id="error-messages-study-director-appointment" class="mb-3"></div>

                <form action="{{ route('saveStudyDirectorAppointmentForm') }}"
                      method="POST"
                      enctype="multipart/form-data"
                      id="form_study_director_appointment">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project_id }}">

                    {{-- ── Personnel ──────────────────────────────────────── --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">
                                Study Director <span class="text-danger">*</span>
                            </label>
                            <select name="study_director" id="study_director" class="form-select">
                                <option value="">— Select —</option>
                                @foreach ($sd_personnels as $p)
                                    <option value="{{ $p->id }}"
                                        {{ ($sda && $sda->study_director == $p->id) || (!$sda && $project && $project->study_director == $p->id) ? 'selected' : '' }}>
                                        {{ $p->titre_personnel }} {{ $p->prenom }} {{ $p->nom }}
                                    </option>
                                @endforeach
                                @if($sd_personnels->isEmpty())
                                    <option disabled>— Aucun Study Director désigné —</option>
                                @endif
                            </select>
                            <div class="form-text text-muted" style="font-size:.72rem;">
                                <i class="bi bi-info-circle me-1"></i>
                                Seuls les personnels désignés Study Director apparaissent ici.
                                <a href="{{ route('admin.users') }}" target="_blank" class="ms-1">Gérer les désignations</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Project Manager</label>
                            <select name="project_manager" id="project_manager" class="form-select">
                                <option value="">— Select —</option>
                                @foreach ($pm_personnels as $p)
                                    <option value="{{ $p->id }}"
                                        {{ ($sda && $sda->project_manager == $p->id) || (!$sda && $project && $project->project_manager == $p->id) ? 'selected' : '' }}>
                                        {{ $p->titre_personnel }} {{ $p->prenom }} {{ $p->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- ── Dates ──────────────────────────────────────────── --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">
                                Date of Appointment <span class="text-danger">*</span>
                            </label>
                            <input type="date"
                                   id="sd_appointment_date"
                                   name="sd_appointment_date"
                                   class="form-control"
                                   value="{{ $sda?->sd_appointment_date ?? '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Estimated Start Date</label>
                            <input type="date"
                                   id="estimated_start_date"
                                   name="estimated_start_date"
                                   class="form-control"
                                   value="{{ $sda?->estimated_start_date ?? '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Estimated End Date</label>
                            <input type="date"
                                   id="estimated_end_date"
                                   name="estimated_end_date"
                                   class="form-control"
                                   value="{{ $sda?->estimated_end_date ?? '' }}">
                        </div>
                    </div>

                    {{-- ── File Upload ─────────────────────────────────────── --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">
                            Signed Study Director Appointment Form (PDF)
                            @if(!$isUpdate)<span class="text-danger">*</span>@endif
                        </label>

                        @if($isUpdate)
                        <div class="d-flex align-items-center gap-2 mb-2 p-2 rounded border"
                             style="background:#f0fff4;border-color:#198754 !important;">
                            <i class="bi bi-file-earmark-pdf-fill text-danger fs-5"></i>
                            <a href="{{ asset('storage/' . $sda->sd_appointment_file) }}"
                               target="_blank" class="text-decoration-none small fw-semibold text-success">
                                View current file
                            </a>
                            <span class="text-muted small ms-auto">Upload a new file below to replace it (optional)</span>
                        </div>
                        @endif

                        <input type="file"
                               id="sd_appointment_file"
                               name="sd_appointment_file"
                               class="form-control fileclass"
                               accept="application/pdf">
                    </div>

                    {{-- ── Comments ────────────────────────────────────────── --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Comments (if any)</label>
                        <textarea name="comments" id="comments" class="form-control" rows="3"
                                  placeholder="Optional remarks…">{{ $sda?->comments ?? '' }}</textarea>
                    </div>

                    {{-- ── Actions ─────────────────────────────────────────── --}}
                    <div class="d-flex justify-content-between align-items-center gap-2 pt-2 border-top mt-2">
                        @if($sda)
                        <a href="{{ route('pdf.sd-appointment-form', ['project_id' => $project_id]) }}"
                           target="_blank"
                           class="btn btn-outline-primary btn-sm fw-semibold"
                           data-no-lock>
                            <i class="bi bi-file-earmark-pdf me-1"></i>Download PDF
                        </a>
                        @else
                        <div></div>
                        @endif
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger fw-semibold px-4">
                                <i class="bi bi-save me-1"></i>Save
                            </button>
                        </div>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>

{{-- Submit handled by javascript_ajax.js (#form_study_director_appointment jQuery handler) --}}

@php
    $all_study_types = App\Models\Pro_StudyType::orderBy('level_type')->get();
    $all_personnel   = App\Models\Pro_Personnel::orderBy('nom')->get();
@endphp

<div class="modal fade" id="ModalformCreateNewProject" tabindex="-1"
     aria-labelledby="ModalformCreateNewProjectLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">

            {{-- Header --}}
            <div class="modal-header border-0 py-3 px-4"
                 style="background:linear-gradient(135deg,#1a3a6b 0%,#c41230 100%);">
                <div>
                    <h5 class="modal-title text-white fw-bold mb-0" id="ModalformCreateNewProjectLabel">
                        <i class="bi bi-plus-circle-fill me-2"></i>Create a New Study
                    </h5>
                    <p class="text-white-50 small mb-0">Register basic study information.</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body px-4 py-3">
                <div id="error-messages"></div>

                <form method="POST" action="{{ route('storeProject') }}" id="formCreateNewProject">
                    @csrf

                    {{-- Row 1: Code + Title --}}
                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <label class="form-label small fw-semibold mb-1">
                                Study Code <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="code" id="cp_code" class="form-control form-control-sm"
                                   placeholder="e.g. 1201" value="{{ old('code') }}">
                        </div>
                        <div class="col-8">
                            <label class="form-label small fw-semibold mb-1">
                                Study Title <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="title" id="cp_title" class="form-control form-control-sm"
                                   placeholder="Full title of the study" value="{{ old('title') }}">
                        </div>
                    </div>

                    {{-- Row 2: Study Types + GLP --}}
                    <div class="row g-2 mb-3">
                        <div class="col-8">
                            <label class="form-label small fw-semibold mb-1">
                                Study Types <span class="text-muted fw-normal">(optional)</span>
                            </label>
                            <select name="study_type_id[]" id="cp_study_type_id" class="form-select form-select-sm" multiple>
                                @foreach($all_study_types as $st)
                                    <option value="{{ $st->id }}">{{ $st->study_type_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-4">
                            <label class="form-label small fw-semibold mb-1">GLP Study?</label>
                            <select name="is_glp" id="cp_is_glp" class="form-select form-select-sm">
                                <option value="0">No — Non-GLP</option>
                                <option value="1">Yes — GLP</option>
                            </select>
                        </div>
                    </div>

                    {{-- Study Director --}}
                    <div class="mb-3">
                        <label class="form-label small fw-semibold mb-1">
                            Study Director
                            <span class="text-muted fw-normal">(optional — can be set later)</span>
                        </label>
                        <select name="study_director_id" id="cp_study_director_id" class="form-select form-select-sm">
                            <option value="">— None for now —</option>
                            @foreach($all_personnel as $p)
                                <option value="{{ $p->id }}">
                                    {{ trim(($p->titre_personnel ?? '') . ' ' . $p->prenom . ' ' . $p->nom) }}
                                    @if($p->email_professionnel ?? $p->email_personnel)
                                        ({{ $p->email_professionnel ?? $p->email_personnel }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text" style="font-size:.73rem;">
                            <i class="bi bi-info-circle me-1"></i>The Study Director will receive a notification and email granting study access.
                        </div>
                    </div>

                    {{-- Sponsor & Manufacturer --}}
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold mb-1">Sponsor Name</label>
                            <input type="text" name="sponsor_name" id="cp_sponsor_name" class="form-control form-control-sm"
                                   placeholder="Sponsor organization…">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold mb-1">Sponsor Email</label>
                            <input type="email" name="sponsor_email" id="cp_sponsor_email" class="form-control form-control-sm"
                                   placeholder="sponsor@example.com">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold mb-1">Manufacturer
                                <span class="text-muted fw-normal">(leave blank if same as Sponsor)</span>
                            </label>
                            <input type="text" name="manufacturer_name" id="cp_manufacturer_name" class="form-control form-control-sm"
                                   placeholder="Manufacturer name…">
                        </div>
                    </div>

                    {{-- Legacy toggle --}}
                    <div class="mb-3 p-3 rounded-3" style="background:#f8f9fa;border:1px solid #e5e7eb;">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch"
                                   name="is_legacy" id="cp_is_legacy" value="1">
                            <label class="form-check-label fw-semibold small" for="cp_is_legacy">
                                <i class="bi bi-archive me-1" style="color:#856404;"></i>
                                Ancien projet déjà terminé (Legacy)
                            </label>
                        </div>
                        <div class="text-muted" style="font-size:.75rem;margin-top:.3rem;margin-left:2.2rem;">
                            Cochez si cette étude est déjà terminée et que vous l'enregistrez à des fins d'archivage.
                            Les étapes seront pré-validées automatiquement. Les dates clés du Master Schedule pourront
                            être saisies depuis l'onglet <strong>Study Creation</strong> après création.
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="d-grid mt-2">
                        <button type="submit" class="btn fw-semibold text-white"
                                style="background:linear-gradient(90deg,#1a3a6b,#c41230);border:none;border-radius:8px;padding:10px;">
                            <i class="bi bi-check2-circle me-2"></i>Create Study
                        </button>
                    </div>
                </form>
            </div>

            <div class="modal-footer border-0 pt-0 px-4 pb-3" style="background:#f8f9fa;">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x me-1"></i>Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    let _initialized = false;

    function initCreateProjectSelects() {
        if (_initialized) return;
        _initialized = true;

        new TomSelect('#cp_study_type_id', {
            plugins: ['remove_button'],
            placeholder: 'Select study types…',
            maxOptions: null,
        });
        new TomSelect('#cp_study_director_id', {
            placeholder: '— Select Study Director —',
            allowEmptyOption: true,
            maxOptions: null,
            searchField: ['text'],
        });
    }

    function ensureTomSelect(callback) {
        if (typeof TomSelect !== 'undefined') {
            callback();
            return;
        }
        if (!document.querySelector('link[href*="tom-select"]')) {
            const link = document.createElement('link');
            link.rel  = 'stylesheet';
            link.href = 'https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css';
            document.head.appendChild(link);
        }
        const script  = document.createElement('script');
        script.src    = 'https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js';
        script.onload = callback;
        document.head.appendChild(script);
    }

    document.addEventListener('show.bs.modal', function (e) {
        if (e.target && e.target.id === 'ModalformCreateNewProject') {
            ensureTomSelect(initCreateProjectSelects);
        }
    });

})();
</script>

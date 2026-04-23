@extends('index-new')
@section('title', 'Moteur de recherche — AIRID')

@section('content')
<style>
/* ── Hero ── */
.search-hero { background:linear-gradient(135deg,#1a3a6b 0%,#c41230 100%); border-radius:.75rem; padding:1.4rem 2rem; margin-bottom:1.2rem; color:#fff; }

/* ── Accordion filters ── */
#searchAccordion .accordion-item   { border:1px solid #e5e7eb; border-radius:.6rem !important; margin-bottom:.5rem; overflow:hidden; }
#searchAccordion .accordion-button { font-size:.88rem; font-weight:600; background:#f8f9fa; color:#1a3a6b; padding:.65rem 1rem; }
#searchAccordion .accordion-button:not(.collapsed) { background:#1a3a6b; color:#fff; box-shadow:none; }
#searchAccordion .accordion-button:not(.collapsed)::after { filter:brightness(0) invert(1); }
#searchAccordion .accordion-button:focus { box-shadow:none; }
#searchAccordion .accordion-body  { padding:.9rem 1rem; background:#fff; }

/* Active filter badge on accordion header */
.filter-count { font-size:.68rem; padding:1px 7px; border-radius:10px; background:rgba(255,255,255,.25); color:#fff; margin-left:.5rem; font-weight:700; }
#searchAccordion .accordion-button:not(.collapsed) .filter-count { background:rgba(255,255,255,.3); color:#fff; }
#searchAccordion .accordion-button.collapsed .filter-count { background:#c41230; color:#fff; }

/* ── Sub-section label ── */
.sub-label { font-size:.71rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#6b7280; margin-bottom:.5rem; }

/* ── Checkboxes ── */
.check-grid { display:flex; flex-wrap:wrap; gap:.35rem 1.2rem; }
.check-grid .form-check { margin:0; }
.check-grid .form-check-label { font-size:.83rem; cursor:pointer; }

/* ── Select2 ── */
.select2-container--bootstrap-5 .select2-selection { border-color:#dee2e6; font-size:.85rem; }
.select2-container--bootstrap-5 .select2-selection--single { min-height:31px; }
.select2-container--bootstrap-5 .select2-selection--multiple { min-height:31px; }

/* ── Results ── */
.result-row:hover { background:#f4f6fb; }
.badge-glp  { background:#1a3a6b; color:#fff; }
.badge-nglp { background:#6c757d; color:#fff; }
.pl-status  { display:inline-block; padding:.15rem .55rem; border-radius:20px; font-size:.7rem; font-weight:600; }
.s-not_started { background:#e9ecef; color:#495057; }
.s-in_progress { background:#d1ecf1; color:#0c5460; }
.s-suspended   { background:#fff3cd; color:#856404; }
.s-completed   { background:#d4edda; color:#155724; }
.s-archived    { background:#cce5ff; color:#004085; }
.tag-sm { display:inline-block; font-size:.68rem; padding:1px 7px; border-radius:12px; font-weight:500; }
</style>

@php
/* Which accordion sections have active filters → keep them open */
$openKw       = true;
$openStatus   = request()->anyFilled(['status','is_glp']);
$openSponsor  = request()->anyFilled(['sponsor','manufacturer','nature','test_system']);
$openPersonnel= request()->anyFilled(['sd','pm','kp']);
$openTypes    = request()->anyFilled(['study_types','lab_tests','product_types']);
$openDates    = request()->anyFilled(['year_from','year_to','date_from','date_to']);
$openQA       = request()->anyFilled(['qa_date_from','qa_date_to','report_date_from','report_date_to']);
$openArchive  = request()->anyFilled(['archive_date_from','archive_date_to','date_first_entry_from','date_first_entry_to','date_second_entry_from','date_second_entry_to']);

/* Count active filters per section (for badge) */
$cntStatus   = collect(['status','is_glp'])->filter(fn($k)=>request()->filled($k))->count();
$cntSponsor  = collect(['sponsor','manufacturer','nature','test_system'])->filter(fn($k)=>request()->filled($k))->count();
$cntPersonnel= collect(['sd','pm'])->filter(fn($k)=>request()->filled($k))->count() + (request()->filled('kp') ? count((array)request('kp')) : 0);
$cntTypes    = (request()->filled('study_types') ? count((array)request('study_types')) : 0)
             + (request()->filled('lab_tests')   ? count((array)request('lab_tests'))   : 0)
             + (request()->filled('product_types')? count((array)request('product_types')): 0);
$cntDates    = collect(['year_from','year_to','date_from','date_to'])->filter(fn($k)=>request()->filled($k))->count();
$cntQA       = collect(['qa_date_from','qa_date_to','report_date_from','report_date_to'])->filter(fn($k)=>request()->filled($k))->count();
$cntArchive  = collect(['archive_date_from','archive_date_to','date_first_entry_from','date_first_entry_to','date_second_entry_from','date_second_entry_to'])->filter(fn($k)=>request()->filled($k))->count();
@endphp

{{-- ── Hero ── --}}
<div class="search-hero d-flex align-items-center gap-3">
    <i class="bi bi-search fs-2 opacity-75"></i>
    <div>
        <h4 class="fw-bold mb-0">Moteur de recherche avancée</h4>
        <p class="opacity-75 small mb-0">Combinez plusieurs critères pour retrouver n'importe quel projet.</p>
    </div>
</div>

{{-- ── Datalists for sponsor / manufacturer / nature / test system ── --}}
<datalist id="dl-sponsor">@foreach($allSponsors as $s)<option value="{{ $s }}">@endforeach</datalist>
<datalist id="dl-manufacturer">@foreach($allManufacturers as $m)<option value="{{ $m }}">@endforeach</datalist>
<datalist id="dl-nature">@foreach($natures as $n)<option value="{{ $n }}">@endforeach</datalist>
<datalist id="dl-testsys">@foreach($testSystems as $ts)<option value="{{ $ts }}">@endforeach</datalist>

<form method="GET" action="{{ route('features.search') }}" id="search-form">

{{-- ══════════════════════════════════════════════════ ACCORDION ══ --}}
<div class="accordion" id="searchAccordion">

    {{-- ─── 1. Mot-clé ─────────────────────────────────────────── --}}
    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button {{ $openKw ? '' : 'collapsed' }}" type="button"
                    data-bs-toggle="collapse" data-bs-target="#acc-kw">
                <i class="bi bi-fonts me-2"></i>Recherche textuelle libre
            </button>
        </h2>
        <div id="acc-kw" class="accordion-collapse collapse {{ $openKw ? 'show' : '' }}">
            <div class="accordion-body">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" class="form-control"
                           value="{{ request('q') }}"
                           placeholder="Code projet, titre, code protocole, sponsor, fabricant, description…">
                </div>
            </div>
        </div>
    </div>

    {{-- ─── 2. Statut & Classification ──────────────────────────── --}}
    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button {{ $openStatus ? '' : 'collapsed' }}" type="button"
                    data-bs-toggle="collapse" data-bs-target="#acc-status">
                <i class="bi bi-funnel me-2"></i>Statut &amp; Classification
                @if($cntStatus)<span class="filter-count">{{ $cntStatus }} filtre(s)</span>@endif
            </button>
        </h2>
        <div id="acc-status" class="accordion-collapse collapse {{ $openStatus ? 'show' : '' }}">
            <div class="accordion-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small mb-1">Statut du projet</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">Tous les statuts</option>
                            @foreach($statuses as $v => $l)
                                <option value="{{ $v }}" {{ request('status') === $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small mb-1">GLP / Non-GLP</label>
                        <select name="is_glp" class="form-select form-select-sm">
                            <option value="">Tous</option>
                            <option value="1" {{ request('is_glp') === '1' ? 'selected' : '' }}>GLP uniquement</option>
                            <option value="0" {{ request('is_glp') === '0' ? 'selected' : '' }}>Non-GLP uniquement</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── 3. Sponsor, Fabricant & Nature ──────────────────────── --}}
    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button {{ $openSponsor ? '' : 'collapsed' }}" type="button"
                    data-bs-toggle="collapse" data-bs-target="#acc-sponsor">
                <i class="bi bi-building me-2"></i>Sponsor, Fabricant &amp; Nature
                @if($cntSponsor)<span class="filter-count">{{ $cntSponsor }} filtre(s)</span>@endif
            </button>
        </h2>
        <div id="acc-sponsor" class="accordion-collapse collapse {{ $openSponsor ? 'show' : '' }}">
            <div class="accordion-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small mb-1">Sponsor</label>
                        <input type="text" name="sponsor" list="dl-sponsor" class="form-control form-control-sm"
                               value="{{ request('sponsor') }}" placeholder="Nom du sponsor…" autocomplete="off">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small mb-1">Fabricant</label>
                        <input type="text" name="manufacturer" list="dl-manufacturer" class="form-control form-control-sm"
                               value="{{ request('manufacturer') }}" placeholder="Nom du fabricant…" autocomplete="off">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small mb-1">Nature du projet</label>
                        <input type="text" name="nature" list="dl-nature" class="form-control form-control-sm"
                               value="{{ request('nature') }}" placeholder="Nature…" autocomplete="off">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small mb-1">Test System</label>
                        <input type="text" name="test_system" list="dl-testsys" class="form-control form-control-sm"
                               value="{{ request('test_system') }}" placeholder="Test system…" autocomplete="off">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── 4. Personnel ────────────────────────────────────────── --}}
    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button {{ $openPersonnel ? '' : 'collapsed' }}" type="button"
                    data-bs-toggle="collapse" data-bs-target="#acc-personnel">
                <i class="bi bi-people me-2"></i>Personnel
                @if($cntPersonnel)<span class="filter-count">{{ $cntPersonnel }} sélection(s)</span>@endif
            </button>
        </h2>
        <div id="acc-personnel" class="accordion-collapse collapse {{ $openPersonnel ? 'show' : '' }}">
            <div class="accordion-body">
                <div class="row g-3">
                    {{-- Study Director — only designated SDs --}}
                    <div class="col-md-4">
                        <label class="form-label small mb-1 fw-semibold">
                            <i class="bi bi-person-badge me-1 text-primary"></i>Study Director
                        </label>
                        <select name="sd" id="sd-select" class="form-select form-select-sm">
                            <option value="">— Tous —</option>
                            @foreach($sdPersonnel as $p)
                                @php $label = trim(($p->titre_personnel ?? '') . ' ' . $p->prenom . ' ' . $p->nom); @endphp
                                <option value="{{ $p->id }}" {{ request('sd') == $p->id ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Project Manager — active-contract personnel --}}
                    <div class="col-md-4">
                        <label class="form-label small mb-1 fw-semibold">
                            <i class="bi bi-person-gear me-1 text-success"></i>Project Manager
                        </label>
                        <select name="pm" id="pm-select" class="form-select form-select-sm">
                            <option value="">— Tous —</option>
                            @foreach($pmPersonnel as $p)
                                @php $label = trim(($p->titre_personnel ?? '') . ' ' . $p->prenom . ' ' . $p->nom); @endphp
                                <option value="{{ $p->id }}" {{ request('pm') == $p->id ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Key Personnel (multiple) --}}
                    <div class="col-md-4">
                        <label class="form-label small mb-1 fw-semibold">
                            <i class="bi bi-people-fill me-1 text-warning"></i>Key Personnel <span class="text-muted fw-normal small">(multiple)</span>
                        </label>
                        <select name="kp[]" id="kp-select" multiple class="form-select form-select-sm">
                            @foreach($allPersonnel as $p)
                                @php $label = trim(($p->titre_personnel ?? '') . ' ' . $p->prenom . ' ' . $p->nom); @endphp
                                <option value="{{ $p->id }}" {{ in_array($p->id, (array) request('kp', [])) ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <div class="form-text" style="font-size:.7rem;">Le projet doit comporter AU MOINS un de ces personnels.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── 5. Study Types, Lab Tests, Produits ─────────────────── --}}
    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button {{ $openTypes ? '' : 'collapsed' }}" type="button"
                    data-bs-toggle="collapse" data-bs-target="#acc-types">
                <i class="bi bi-journal-bookmark me-2"></i>Study Types, Lab Tests &amp; Produits évalués
                @if($cntTypes)<span class="filter-count">{{ $cntTypes }} sélection(s)</span>@endif
            </button>
        </h2>
        <div id="acc-types" class="accordion-collapse collapse {{ $openTypes ? 'show' : '' }}">
            <div class="accordion-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="sub-label"><i class="bi bi-journal-bookmark me-1"></i>Study Types</div>
                        <div class="check-grid">
                            @foreach($studyTypes as $st)
                            <div class="form-check">
                                <input type="checkbox" name="study_types[]" value="{{ $st->id }}"
                                       id="st{{ $st->id }}" class="form-check-input"
                                       {{ in_array($st->id, (array) request('study_types', [])) ? 'checked' : '' }}>
                                <label for="st{{ $st->id }}" class="form-check-label">{{ $st->study_type_name }}</label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="sub-label"><i class="bi bi-eyedropper me-1"></i>Lab Tests</div>
                        <div class="check-grid">
                            @foreach($labTests as $lt)
                            <div class="form-check">
                                <input type="checkbox" name="lab_tests[]" value="{{ $lt->id }}"
                                       id="lt{{ $lt->id }}" class="form-check-input"
                                       {{ in_array($lt->id, (array) request('lab_tests', [])) ? 'checked' : '' }}>
                                <label for="lt{{ $lt->id }}" class="form-check-label">{{ $lt->lab_test_name }}</label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="sub-label"><i class="bi bi-box-seam me-1"></i>Produits évalués</div>
                        <div class="check-grid">
                            @foreach($productTypes as $pt)
                            <div class="form-check">
                                <input type="checkbox" name="product_types[]" value="{{ $pt->id }}"
                                       id="pt{{ $pt->id }}" class="form-check-input"
                                       {{ in_array($pt->id, (array) request('product_types', [])) ? 'checked' : '' }}>
                                <label for="pt{{ $pt->id }}" class="form-check-label">{{ $pt->product_type_name }}</label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── 6. Dates du projet ──────────────────────────────────── --}}
    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button {{ $openDates ? '' : 'collapsed' }}" type="button"
                    data-bs-toggle="collapse" data-bs-target="#acc-dates">
                <i class="bi bi-calendar-range me-2"></i>Dates du projet
                @if($cntDates)<span class="filter-count">{{ $cntDates }} filtre(s)</span>@endif
            </button>
        </h2>
        <div id="acc-dates" class="accordion-collapse collapse {{ $openDates ? 'show' : '' }}">
            <div class="accordion-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="sub-label"><i class="bi bi-calendar-range me-1"></i>Année de démarrage</div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small mb-1">De</label>
                                <input type="number" name="year_from" class="form-control form-control-sm"
                                       value="{{ request('year_from') }}" placeholder="2020" min="2000" max="2099">
                            </div>
                            <div class="col-6">
                                <label class="form-label small mb-1">À</label>
                                <input type="number" name="year_to" class="form-control form-control-sm"
                                       value="{{ request('year_to') }}" placeholder="{{ date('Y') }}" min="2000" max="2099">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="sub-label"><i class="bi bi-calendar-event me-1"></i>Période de démarrage prévue</div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small mb-1">Du</label>
                                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label small mb-1">Au</label>
                                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── 7. QA & Rapport final ────────────────────────────────── --}}
    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button {{ $openQA ? '' : 'collapsed' }}" type="button"
                    data-bs-toggle="collapse" data-bs-target="#acc-qa">
                <i class="bi bi-shield-check me-2"></i>Assurance Qualité &amp; Rapport final
                @if($cntQA)<span class="filter-count">{{ $cntQA }} filtre(s)</span>@endif
            </button>
        </h2>
        <div id="acc-qa" class="accordion-collapse collapse {{ $openQA ? 'show' : '' }}">
            <div class="accordion-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="sub-label"><i class="bi bi-shield-check me-1"></i>Date planifiée d'inspection QA</div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small mb-1">Du</label>
                                <input type="date" name="qa_date_from" class="form-control form-control-sm" value="{{ request('qa_date_from') }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label small mb-1">Au</label>
                                <input type="date" name="qa_date_to" class="form-control form-control-sm" value="{{ request('qa_date_to') }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="sub-label"><i class="bi bi-file-earmark-text me-1"></i>Date de soumission du rapport final</div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small mb-1">Du</label>
                                <input type="date" name="report_date_from" class="form-control form-control-sm" value="{{ request('report_date_from') }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label small mb-1">Au</label>
                                <input type="date" name="report_date_to" class="form-control form-control-sm" value="{{ request('report_date_to') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── 8. Archivage & Double saisie ────────────────────────── --}}
    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button {{ $openArchive ? '' : 'collapsed' }}" type="button"
                    data-bs-toggle="collapse" data-bs-target="#acc-archive">
                <i class="bi bi-archive me-2"></i>Archivage &amp; Data Management (double saisie)
                @if($cntArchive)<span class="filter-count">{{ $cntArchive }} filtre(s)</span>@endif
            </button>
        </h2>
        <div id="acc-archive" class="accordion-collapse collapse {{ $openArchive ? 'show' : '' }}">
            <div class="accordion-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="sub-label"><i class="bi bi-archive me-1"></i>Date d'archivage</div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small mb-1">Du</label>
                                <input type="date" name="archive_date_from" class="form-control form-control-sm" value="{{ request('archive_date_from') }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label small mb-1">Au</label>
                                <input type="date" name="archive_date_to" class="form-control form-control-sm" value="{{ request('archive_date_to') }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="sub-label"><i class="bi bi-input-cursor-text me-1"></i>1ère saisie</div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small mb-1">Du</label>
                                <input type="date" name="date_first_entry_from" class="form-control form-control-sm" value="{{ request('date_first_entry_from') }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label small mb-1">Au</label>
                                <input type="date" name="date_first_entry_to" class="form-control form-control-sm" value="{{ request('date_first_entry_to') }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="sub-label"><i class="bi bi-input-cursor-text me-1"></i>2ème saisie</div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small mb-1">Du</label>
                                <input type="date" name="date_second_entry_from" class="form-control form-control-sm" value="{{ request('date_second_entry_from') }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label small mb-1">Au</label>
                                <input type="date" name="date_second_entry_to" class="form-control form-control-sm" value="{{ request('date_second_entry_to') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>{{-- end accordion --}}

{{-- ══════════════════════════════════════════════════ SUBMIT ══ --}}
<div class="d-flex align-items-center gap-2 my-3">
    <button type="submit" class="btn px-5 fw-semibold py-2" style="background:#1a3a6b;color:#fff;border:none;">
        <i class="bi bi-search me-1"></i>Rechercher
    </button>
    @if($hasSearch)
    <a href="{{ route('features.search') }}" class="btn btn-outline-secondary">
        <i class="bi bi-x-circle me-1"></i>Réinitialiser
    </a>
    <span class="text-muted small ms-2 fw-semibold">{{ $total }} résultat(s)</span>
    @endif
</div>

</form>

{{-- ══════════════════════════════════════════════════ RESULTS ══ --}}
@if($hasSearch)
<div class="card border-0 shadow-sm">
    <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between"
         style="background:#1a3a6b;color:#fff;border-radius:.5rem .5rem 0 0;">
        <span class="fw-semibold"><i class="bi bi-table me-2"></i>Résultats</span>
        <span class="badge bg-light text-dark">{{ $total }} projet(s)</span>
    </div>
    <div class="card-body p-0">
        @if($projects->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                Aucun projet trouvé avec ces critères.
                <div class="small mt-1">Essayez d'élargir votre recherche en supprimant certains filtres.</div>
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:.82rem;">
                <thead style="background:#f1f3f9;font-size:.73rem;text-transform:uppercase;letter-spacing:.04em;color:#4b5563;">
                    <tr>
                        <th class="px-3 py-3">Code / Protocole</th>
                        <th>Titre &amp; Study Types</th>
                        <th>Sponsor</th>
                        <th>Study Director / PM</th>
                        <th>Lab Tests &amp; Produits</th>
                        <th class="text-center">GLP</th>
                        <th class="text-center">Statut</th>
                        <th class="text-center px-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($projects as $p)
                @php
                    $sda        = $p->studyDirectorAppointmentForm;
                    $sd         = $sda?->studyDirector;
                    $sdName     = $sd ? trim(($sd->titre_personnel ?? '') . ' ' . $sd->prenom . ' ' . $sd->nom) : '—';
                    $stageKey   = str_replace(' ', '_', $p->project_stage ?? 'not_started');
                    $stageLabel = match($p->project_stage) {
                        'not_started' => 'Not Started',
                        'in progress' => 'In Progress',
                        'suspended'   => 'Suspended',
                        'completed'   => 'Completed',
                        'archived'    => 'Archived',
                        default       => ucfirst($p->project_stage ?? '—'),
                    };
                @endphp
                <tr class="result-row">
                    {{-- Code --}}
                    <td class="px-3">
                        <div class="fw-bold" style="color:#1a3a6b;">{{ $p->project_code }}</div>
                        @if($p->protocol_code)
                            <div class="text-muted" style="font-size:.7rem;">{{ $p->protocol_code }}</div>
                        @endif
                    </td>

                    {{-- Title + study types --}}
                    <td style="max-width:220px;">
                        <div class="text-truncate fw-semibold" title="{{ $p->project_title }}">{{ $p->project_title ?: '—' }}</div>
                        @if($p->studyTypesApplied->isNotEmpty())
                        <div class="d-flex flex-wrap gap-1 mt-1">
                            @foreach($p->studyTypesApplied as $st)
                                <span class="tag-sm" style="background:#e8eef7;color:#1a3a6b;">{{ $st->study_type_name }}</span>
                            @endforeach
                        </div>
                        @endif
                    </td>

                    {{-- Sponsor --}}
                    <td>
                        <div>{{ $p->sponsor_name ?: '—' }}</div>
                        @if($p->manufacturer_name && $p->manufacturer_name !== $p->sponsor_name)
                            <div class="text-muted" style="font-size:.7rem;">{{ $p->manufacturer_name }}</div>
                        @endif
                    </td>

                    {{-- SD / PM --}}
                    <td>
                        <div>{{ $sdName }}</div>
                        @if($p->projectManager)
                            <div class="text-muted" style="font-size:.72rem;">
                                <i class="bi bi-person-gear me-1"></i>{{ $p->projectManager->prenom }} {{ $p->projectManager->nom }}
                            </div>
                        @endif
                    </td>

                    {{-- Lab tests + products --}}
                    <td style="max-width:180px;">
                        @if($p->labTestsConcerned->isNotEmpty())
                        <div class="d-flex flex-wrap gap-1 mb-1">
                            @foreach($p->labTestsConcerned as $lt)
                                <span class="tag-sm" style="background:#fef3c7;color:#92400e;">{{ $lt->lab_test_name }}</span>
                            @endforeach
                        </div>
                        @endif
                        @if($p->productTypesEvaluated->isNotEmpty())
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($p->productTypesEvaluated as $pt)
                                <span class="tag-sm" style="background:#d1fae5;color:#065f46;">{{ $pt->product_type_name }}</span>
                            @endforeach
                        </div>
                        @endif
                        @if($p->labTestsConcerned->isEmpty() && $p->productTypesEvaluated->isEmpty())
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    {{-- GLP --}}
                    <td class="text-center">
                        @if($p->is_glp)
                            <span class="badge badge-glp rounded-pill px-2" style="font-size:.68rem;">GLP</span>
                        @else
                            <span class="badge badge-nglp rounded-pill px-2" style="font-size:.68rem;">Non-GLP</span>
                        @endif
                    </td>

                    {{-- Status --}}
                    <td class="text-center">
                        <span class="pl-status s-{{ $stageKey }}">{{ $stageLabel }}</span>
                        @if($p->archived_at)
                            <div style="font-size:.66rem;" class="text-muted mt-1">
                                <i class="bi bi-lock-fill me-1"></i>{{ \Carbon\Carbon::parse($p->archived_at)->format('d/m/Y') }}
                            </div>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="text-center px-3">
                        <div class="d-flex flex-column gap-1 align-items-center">
                            <a href="{{ route('projectOverview', $p->id) }}"
                               class="btn btn-sm fw-semibold py-0 px-3 w-100"
                               style="background:#1a3a6b;color:#fff;font-size:.74rem;">
                                <i class="bi bi-eye me-1"></i>Overview
                            </a>
                            <a href="{{ route('project.create', ['project_id' => $p->id]) }}"
                               class="btn btn-sm btn-outline-secondary fw-semibold py-0 px-3 w-100"
                               style="font-size:.74rem;">
                                <i class="bi bi-pencil-square me-1"></i>Gérer
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endif

<script>
$(function () {
    if (typeof $.fn.select2 === 'undefined') return;

    const s2opts = {
        theme: 'bootstrap-5',
        allowClear: true,
        width: '100%',
        language: {
            noResults:     function () { return 'Aucun résultat'; },
            searching:     function () { return 'Recherche…'; },
            inputTooShort: function () { return 'Tapez pour rechercher…'; },
        },
    };

    // Study Director — single, searchable
    $('#sd-select').select2(Object.assign({}, s2opts, {
        placeholder: 'Rechercher un Study Director…',
    }));

    // Project Manager — single, searchable
    $('#pm-select').select2(Object.assign({}, s2opts, {
        placeholder: 'Rechercher un Project Manager…',
    }));

    // Key Personnel — multi, searchable
    $('#kp-select').select2(Object.assign({}, s2opts, {
        placeholder: 'Rechercher du personnel clé…',
    }));

    // Re-open accordion when Select2 opens (avoid focus trap)
    $('[id$="-select"]').on('select2:open', function () {
        document.querySelector('.select2-container--open .select2-search__field')?.focus();
    });
});
</script>
@endsection

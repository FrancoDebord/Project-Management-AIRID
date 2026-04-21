@extends('index-new')
@section('title', 'Moteur de recherche — AIRID')

@section('content')
<style>
.search-hero { background:linear-gradient(135deg,#1a3a6b 0%,#c41230 100%); border-radius:.75rem; padding:1.5rem 2rem; margin-bottom:1.5rem; color:#fff; }
.filter-card { background:#fff; border:1px solid #e5e7eb; border-radius:.65rem; padding:1.2rem 1.4rem; margin-bottom:1rem; }
.filter-card .filter-title { font-size:.78rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#6b7280; margin-bottom:.75rem; }
.result-row:hover { background:#f4f6fb; }
.badge-glp  { background:#1a3a6b;color:#fff; }
.badge-nglp { background:#6c757d;color:#fff; }
.pl-status  { display:inline-block;padding:.15rem .55rem;border-radius:20px;font-size:.7rem;font-weight:600; }
.s-not_started{background:#e9ecef;color:#495057} .s-in_progress{background:#d1ecf1;color:#0c5460}
.s-suspended{background:#fff3cd;color:#856404}   .s-completed{background:#d4edda;color:#155724}
.s-archived{background:#cce5ff;color:#004085}
</style>

{{-- Hero --}}
<div class="search-hero d-flex align-items-center gap-3">
    <i class="bi bi-search fs-2 opacity-75"></i>
    <div>
        <h4 class="fw-bold mb-0">Moteur de recherche avancée</h4>
        <p class="opacity-75 small mb-0">Combinez plusieurs critères pour retrouver n'importe quel projet.</p>
    </div>
</div>

<form method="GET" action="{{ route('features.search') }}" id="search-form">

<div class="row g-3 mb-3">

    {{-- ── Keyword ── --}}
    <div class="col-12">
        <div class="filter-card">
            <div class="filter-title"><i class="bi bi-fonts me-1"></i>Recherche textuelle</div>
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="q" class="form-control"
                       value="{{ request('q') }}"
                       placeholder="Code projet, titre, code protocole, sponsor, fabricant, nature, test system…">
            </div>
        </div>
    </div>

    {{-- ── Status + GLP + Nature ── --}}
    <div class="col-md-4">
        <div class="filter-card h-100">
            <div class="filter-title"><i class="bi bi-funnel me-1"></i>Statut & Type</div>
            <div class="mb-2">
                <label class="form-label small mb-1">Statut du projet</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Tous les statuts</option>
                    @foreach($statuses as $v => $l)
                        <option value="{{ $v }}" {{ request('status') === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-2">
                <label class="form-label small mb-1">GLP / Non-GLP</label>
                <select name="is_glp" class="form-select form-select-sm">
                    <option value="">Tous</option>
                    <option value="1" {{ request('is_glp') === '1' ? 'selected' : '' }}>GLP uniquement</option>
                    <option value="0" {{ request('is_glp') === '0' ? 'selected' : '' }}>Non-GLP uniquement</option>
                </select>
            </div>
            <div>
                <label class="form-label small mb-1">Nature du projet</label>
                <select name="nature" class="form-select form-select-sm">
                    <option value="">Toutes</option>
                    @foreach($natures as $n)
                        <option value="{{ $n }}" {{ request('nature') === $n ? 'selected' : '' }}>{{ $n }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- ── Sponsor / Manufacturer / Test System ── --}}
    <div class="col-md-4">
        <div class="filter-card h-100">
            <div class="filter-title"><i class="bi bi-building me-1"></i>Sponsor & Test</div>
            <div class="mb-2">
                <label class="form-label small mb-1">Sponsor</label>
                <input type="text" name="sponsor" class="form-control form-control-sm"
                       value="{{ request('sponsor') }}" placeholder="Nom du sponsor…">
            </div>
            <div class="mb-2">
                <label class="form-label small mb-1">Fabricant</label>
                <input type="text" name="manufacturer" class="form-control form-control-sm"
                       value="{{ request('manufacturer') }}" placeholder="Nom du fabricant…">
            </div>
            <div>
                <label class="form-label small mb-1">Test System</label>
                <select name="test_system" class="form-select form-select-sm">
                    <option value="">Tous</option>
                    @foreach($testSystems as $ts)
                        <option value="{{ $ts }}" {{ request('test_system') === $ts ? 'selected' : '' }}>{{ $ts }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- ── Personnel ── --}}
    <div class="col-md-4">
        <div class="filter-card h-100">
            <div class="filter-title"><i class="bi bi-people me-1"></i>Personnel</div>
            <div class="mb-2">
                <label class="form-label small mb-1">Study Director (nom)</label>
                <input type="text" name="sd" class="form-control form-control-sm"
                       value="{{ request('sd') }}" placeholder="Nom ou prénom…">
            </div>
            <div class="mb-2">
                <label class="form-label small mb-1">Project Manager (nom)</label>
                <input type="text" name="pm" class="form-control form-control-sm"
                       value="{{ request('pm') }}" placeholder="Nom ou prénom…">
            </div>
            <div>
                <label class="form-label small mb-1">Key Personnel (nom)</label>
                <input type="text" name="kp" class="form-control form-control-sm"
                       value="{{ request('kp') }}" placeholder="Nom ou prénom…">
            </div>
        </div>
    </div>

    {{-- ── Dates ── --}}
    <div class="col-md-6">
        <div class="filter-card">
            <div class="filter-title"><i class="bi bi-calendar-range me-1"></i>Année d'obtention</div>
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
    </div>

    <div class="col-md-6">
        <div class="filter-card">
            <div class="filter-title"><i class="bi bi-calendar-event me-1"></i>Période de démarrage prévue</div>
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

    {{-- ── Double Data Entry dates ── --}}
    <div class="col-md-6">
        <div class="filter-card">
            <div class="filter-title"><i class="bi bi-input-cursor-text me-1"></i>1ère saisie (Data Management)</div>
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
    </div>

    <div class="col-md-6">
        <div class="filter-card">
            <div class="filter-title"><i class="bi bi-input-cursor-text me-1"></i>2ème saisie (Data Management)</div>
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

</div>{{-- row --}}

{{-- Submit row --}}
<div class="d-flex align-items-center gap-2 mb-4">
    <button type="submit" class="btn px-4 fw-semibold" style="background:#1a3a6b;color:#fff;border:none;">
        <i class="bi bi-search me-1"></i>Rechercher
    </button>
    @if($hasSearch)
    <a href="{{ route('features.search') }}" class="btn btn-outline-secondary">
        <i class="bi bi-x me-1"></i>Effacer
    </a>
    <span class="text-muted small ms-2">{{ $total }} résultat(s)</span>
    @endif
</div>

</form>

{{-- Results --}}
@if($hasSearch)
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($projects->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>Aucun projet trouvé avec ces critères.
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:.82rem;">
                <thead style="background:#1a3a6b;color:#fff;font-size:.74rem;text-transform:uppercase;">
                    <tr>
                        <th class="px-3 py-3">Code</th>
                        <th>Titre</th>
                        <th>Sponsor</th>
                        <th>Study Director</th>
                        <th>Nature / Test System</th>
                        <th class="text-center">GLP</th>
                        <th class="text-center">Statut</th>
                        <th class="text-end px-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($projects as $p)
                    @php
                        $sda    = $p->studyDirectorAppointmentForm;
                        $sd     = $sda?->studyDirector;
                        $sdName = $sd ? trim(($sd->titre_personnel ?? '') . ' ' . $sd->prenom . ' ' . $sd->nom) : '—';
                        $stageKey = str_replace(' ','_',$p->project_stage ?? 'not_started');
                        $stageLabel = match($p->project_stage){
                            'not_started'=>'Not Started','in progress'=>'In Progress',
                            'suspended'=>'Suspended','completed'=>'Completed','archived'=>'Archived',
                            default=>ucfirst($p->project_stage ?? '—')};
                    @endphp
                    <tr class="result-row">
                        <td class="px-3">
                            <div class="fw-bold" style="color:#1a3a6b;">{{ $p->project_code }}</div>
                            @if($p->protocol_code)<div class="text-muted" style="font-size:.7rem;">{{ $p->protocol_code }}</div>@endif
                        </td>
                        <td style="max-width:240px;">
                            <div class="text-truncate" title="{{ $p->project_title }}">{{ $p->project_title }}</div>
                        </td>
                        <td>
                            <div>{{ $p->sponsor_name ?: '—' }}</div>
                            @if($p->manufacturer_name && $p->manufacturer_name !== $p->sponsor_name)
                                <div class="text-muted" style="font-size:.7rem;">{{ $p->manufacturer_name }}</div>
                            @endif
                        </td>
                        <td>{{ $sdName }}</td>
                        <td>
                            @if($p->project_nature)<div>{{ $p->project_nature }}</div>@endif
                            @if($p->test_system)<div class="text-muted" style="font-size:.7rem;">{{ $p->test_system }}</div>@endif
                        </td>
                        <td class="text-center">
                            <span class="badge badge-{{ $p->is_glp ? 'glp' : 'nglp' }} rounded-pill px-2" style="font-size:.68rem;">
                                {{ $p->is_glp ? 'GLP' : 'Non-GLP' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="pl-status s-{{ $stageKey }}">{{ $stageLabel }}</span>
                        </td>
                        <td class="text-end px-3">
                            <a href="{{ route('projectOverview', $p->id) }}"
                               class="btn btn-sm btn-outline-primary py-0 px-2">
                                <i class="bi bi-eye me-1"></i>Voir
                            </a>
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
@endsection

@extends('index-new')

@section('breadcrumb')
    {{-- <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Sample Page</h5>
                        <p class="m-b-0">Lorem Ipsum is simply dummy text of the printing</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb-title">
                        <li class="breadcrumb-item">
                            <a href="index-2.html"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item"><a href="#!">Pages</a>
                        </li>
                        <li class="breadcrumb-item"><a href="#!">Sample Page</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div> --}}
@endsection


@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 fw-semibold mb-0">Tableau de bord</h1>
        @if(Auth::user()->canCreateProject())
        <button type="button"
                data-bs-toggle="modal" data-bs-target="#ModalformCreateNewProject"
                class="btn btn-sm fw-semibold d-flex align-items-center gap-2"
                style="background:linear-gradient(90deg,#1a3a6b,#4e2d8e);color:#fff;border:none;border-radius:9px;padding:.5rem 1.1rem;font-size:.88rem;box-shadow:0 2px 8px rgba(26,58,107,.25);">
            <i class="bi bi-plus-lg"></i>Nouveau projet
        </button>
        @endif
    </div>

    {{-- ── KPI Cards ── --}}
    <div class="row g-3 mb-4">

        {{-- KPI 1 : Projets en cours --}}
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="border-left:4px solid #198754 !important;">
                <div class="d-flex align-items-start gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:48px;height:48px;background:#d1e7dd;">
                        <i class="bi bi-play-circle-fill fs-4" style="color:#198754;"></i>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="text-muted small mb-1">Projets en cours</div>
                        <div class="fw-bold" style="font-size:1.6rem;line-height:1;color:#198754;">{{ $kpiInProgress }}</div>
                        <div class="text-muted mt-1" style="font-size:.72rem;">
                            sur {{ $totalProjects }} projet{{ $totalProjects > 1 ? 's' : '' }} au total
                            @if($kpiByStage['suspended'] > 0)
                                · <span class="text-warning">{{ $kpiByStage['suspended'] }} suspendu{{ $kpiByStage['suspended'] > 1 ? 's' : '' }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI 2 : NC ouvertes --}}
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="border-left:4px solid #dc3545 !important;">
                <div class="d-flex align-items-start gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:48px;height:48px;background:#f8d7da;">
                        <i class="bi bi-exclamation-triangle-fill fs-4" style="color:#dc3545;"></i>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="text-muted small mb-1">Non-conformités ouvertes</div>
                        <div class="fw-bold {{ $kpiOpenNc > 0 ? 'text-danger' : 'text-success' }}" style="font-size:1.6rem;line-height:1;">{{ $kpiOpenNc }}</div>
                        <div class="text-muted mt-1" style="font-size:.72rem;">
                            @if($kpiOpenNc === 0)
                                <span class="text-success"><i class="bi bi-check-circle me-1"></i>Tout est résolu</span>
                            @else
                                findings en attente de résolution
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI 3 : Inspections en attente --}}
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="border-left:4px solid #6f42c1 !important;">
                <div class="d-flex align-items-start gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:48px;height:48px;background:#e9d8fd;">
                        <i class="bi bi-shield-exclamation fs-4" style="color:#6f42c1;"></i>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="text-muted small mb-1">Inspections QA en attente</div>
                        <div class="fw-bold" style="font-size:1.6rem;line-height:1;color:#6f42c1;">{{ $kpiPendingInspections }}</div>
                        <div class="text-muted mt-1" style="font-size:.72rem;">
                            @if(count($projectsNeedingInspection) > 0)
                                <span class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>{{ count($projectsNeedingInspection) }} projet{{ count($projectsNeedingInspection) > 1 ? 's' : '' }} nécessitent une inspection</span>
                            @else
                                inspections non encore clôturées
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI 4 : Taux d'achèvement moyen --}}
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="border-left:4px solid #0d6efd !important;">
                <div class="d-flex align-items-start gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:48px;height:48px;background:#cfe2ff;">
                        <i class="bi bi-speedometer2 fs-4" style="color:#0d6efd;"></i>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="text-muted small mb-1">Achèvement moyen</div>
                        <div class="fw-bold text-primary" style="font-size:1.6rem;line-height:1;">{{ $kpiAvgCompletion }}%</div>
                        <div class="mt-1" style="font-size:.72rem;">
                            <div class="progress" style="height:4px;border-radius:2px;">
                                <div class="progress-bar bg-primary" style="width:{{ $kpiAvgCompletion }}%;"></div>
                            </div>
                            <span class="text-muted mt-1 d-block">
                                {{ $kpiByStage['completed'] + $kpiByStage['archived'] }} terminé{{ ($kpiByStage['completed'] + $kpiByStage['archived']) > 1 ? 's' : '' }}
                                / archivé{{ ($kpiByStage['completed'] + $kpiByStage['archived']) > 1 ? 's' : '' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <style>
        .proj-card {
            border-radius: .75rem;
            border: 1px solid #e8ecf0;
            transition: box-shadow .15s, transform .15s;
        }
        .proj-card:hover { box-shadow: 0 6px 18px rgba(0,0,0,.1); transform: translateY(-2px); }
        .proj-stage-bar { width: 4px; border-radius: .75rem 0 0 .75rem; flex-shrink: 0; min-height: 100%; }
        .proj-stat-cell { background: #f4f6fb; border-radius: .4rem; padding: .3rem .4rem; text-align: center; }
        .proj-stat-cell .count { font-size: .92rem; font-weight: 700; line-height: 1.1; }
        .proj-stat-cell .lbl   { font-size: .62rem; color: #888; line-height: 1.1; }
        .proj-milestone { font-size: .75rem; }
        .stage-filter-link { text-decoration: none; border-radius: .4rem; padding: .3rem .65rem; font-size: .8rem; display: inline-flex; align-items: center; gap: .3rem; opacity: .7; transition: opacity .15s, box-shadow .15s; }
        .stage-filter-link:hover, .stage-filter-link.active { opacity: 1; box-shadow: 0 2px 6px rgba(0,0,0,.15); font-weight: 700; }
    </style>

    <div class="mt-4">
        {{-- Section header + filters --}}
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-grid-3x3-gap me-2 text-danger"></i>Vue d'ensemble des projets
                @php $displayCount = $all_projects->total(); @endphp
                <span class="badge bg-secondary ms-2" style="font-size:.75rem;">{{ $displayCount }}</span>
            </h5>
            <div class="d-flex flex-wrap gap-1">
                @php
                    $filterLinks = [
                        'all'         => ['label' => 'Tous',         'count' => $totalProjects,               'bg' => '#343a40', 'color' => '#fff'],
                        'in_progress' => ['label' => 'En cours',     'count' => $kpiByStage['in_progress'],   'bg' => '#d1e7dd', 'color' => '#0a3622'],
                        'not_started' => ['label' => 'Non démarré',  'count' => $kpiByStage['not_started'],   'bg' => '#e9ecef', 'color' => '#495057'],
                        'suspended'   => ['label' => 'Suspendu',     'count' => $kpiByStage['suspended'],     'bg' => '#fff3cd', 'color' => '#664d03'],
                        'completed'   => ['label' => 'Terminé',      'count' => $kpiByStage['completed'],     'bg' => '#cfe2ff', 'color' => '#084298'],
                        'archived'    => ['label' => 'Archivé',      'count' => $kpiByStage['archived'],      'bg' => '#dee2e6', 'color' => '#212529'],
                    ];
                @endphp
                @foreach($filterLinks as $key => $fl)
                    <a href="{{ route('indexPage', array_filter(['stage' => $key === 'all' ? null : $key])) }}"
                       class="stage-filter-link {{ $stageFilter === $key || ($key === 'all' && $stageFilter === 'all') ? 'active' : '' }}"
                       style="background:{{ $fl['bg'] }};color:{{ $fl['color'] }};">
                        {{ $fl['label'] }}
                        <span class="badge ms-1" style="background:rgba(0,0,0,.18);color:#fff;font-size:.65rem;">{{ $fl['count'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Search bar --}}
        <form method="GET" action="{{ route('indexPage') }}" class="mb-3" id="proj-search-form">
            @if($stageFilter !== 'all')
                <input type="hidden" name="stage" value="{{ $stageFilter }}">
            @endif
            <div class="input-group" style="max-width:480px;">
                <span class="input-group-text bg-white border-end-0" style="border-radius:9px 0 0 9px;">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" name="q" id="proj-search-input"
                       class="form-control border-start-0 ps-0"
                       style="border-radius:0 9px 9px 0;font-size:.88rem;"
                       placeholder="Rechercher par code ou titre…"
                       value="{{ $search ?? '' }}"
                       autocomplete="off">
                @if(!empty($search))
                <a href="{{ route('indexPage', array_filter(['stage' => $stageFilter !== 'all' ? $stageFilter : null])) }}"
                   class="btn btn-outline-secondary btn-sm" title="Effacer la recherche"
                   style="border-radius:0 9px 9px 0;font-size:.8rem;">
                    <i class="bi bi-x-lg"></i>
                </a>
                @endif
            </div>
            @if(!empty($search))
            <div class="text-muted mt-1" style="font-size:.78rem;">
                <i class="bi bi-funnel me-1"></i>
                {{ $all_projects->total() }} résultat{{ $all_projects->total() !== 1 ? 's' : '' }} pour
                "<strong>{{ $search }}</strong>"
            </div>
            @endif
        </form>

        {{-- Project grid --}}
        <div class="row g-3">
            @forelse ($all_projects as $project)
                @php
                    $stage = $project->project_stage ?? '';
                    $stageLabel = match($stage) {
                        'in progress' => 'En cours',
                        'not_started' => 'Non démarré',
                        'suspended'   => 'Suspendu',
                        'completed'   => 'Terminé',
                        'archived'    => 'Archivé',
                        'NA'          => 'N/A',
                        default       => $stage ?: '—',
                    };
                    $stageBadge = match($stage) {
                        'in progress' => 'bg-success',
                        'not_started' => 'bg-secondary',
                        'suspended'   => 'bg-warning text-dark',
                        'completed'   => 'bg-primary',
                        'archived'    => 'bg-dark',
                        default       => 'bg-light text-dark border',
                    };
                    $stageColor = match($stage) {
                        'in progress' => '#198754',
                        'not_started' => '#adb5bd',
                        'suspended'   => '#ffc107',
                        'completed'   => '#0d6efd',
                        'archived'    => '#343a40',
                        default       => '#dee2e6',
                    };

                    $sc  = $projectScores[$project->id] ?? [
                        'overall'=>0,'totalAct'=>0,'doneAct'=>0,'totalInsp'=>0,'doneInsp'=>0,
                        'totalNc'=>0,'doneNc'=>0,'reportMilestone'=>0,'archiveMilestone'=>0,'phasesCount'=>0,
                    ];
                    $pct      = $sc['overall'];
                    $barColor = $pct >= 80 ? '#198754' : ($pct >= 50 ? '#ffc107' : '#dc3545');
                    $pctColor = $pct >= 80 ? 'text-success' : ($pct >= 50 ? 'text-warning' : 'text-danger');
                    $needsQa  = in_array($project->id, $projectsNeedingInspection);
                    $startDate = $project->date_debut_effective
                        ? \Carbon\Carbon::parse($project->date_debut_effective)->format('d/m/Y') : '—';
                    $endDate = $project->date_fin_effective
                        ? \Carbon\Carbon::parse($project->date_fin_effective)->format('d/m/Y') : '—';
                @endphp

                <div class="col-12 col-sm-6 col-xl-4">
                    <div class="proj-card bg-white d-flex h-100 overflow-hidden">
                        <div class="proj-stage-bar" style="background:{{ $stageColor }};"></div>
                        <div class="p-3 flex-grow-1 d-flex flex-column" style="min-width:0;">

                            {{-- Header --}}
                            <div class="d-flex align-items-start justify-content-between mb-1 gap-2">
                                <div style="min-width:0;">
                                    <div class="fw-bold text-dark" style="font-size:.95rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                        {{ $project->project_code ?? '—' }}
                                    </div>
                                    <div class="text-muted small text-truncate">{{ $project->project_title ?? '—' }}</div>
                                </div>
                                <div class="d-flex flex-column align-items-end gap-1 flex-shrink-0">
                                    <span class="badge {{ $stageBadge }}" style="font-size:.68rem;">{{ $stageLabel }}</span>
                                    @if($needsQa)
                                        <span class="badge bg-warning text-dark" title="Activités critiques sans inspection QA" style="font-size:.65rem;">
                                            <i class="bi bi-exclamation-triangle-fill"></i> QA
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Dates --}}
                            <div class="text-muted mb-2" style="font-size:.73rem;">
                                <i class="bi bi-calendar2-range me-1"></i>{{ $startDate }} → {{ $endDate }}
                            </div>

                            {{-- Progress --}}
                            <div class="mb-2">
                                <div class="d-flex justify-content-between mb-1" style="font-size:.78rem;">
                                    <span class="text-muted">Progression</span>
                                    <span class="fw-bold {{ $pctColor }}">{{ $pct }}%</span>
                                </div>
                                <div class="progress" style="height:6px; border-radius:3px; background:#e9ecef;">
                                    <div class="progress-bar" style="width:{{ $pct }}%; background:{{ $barColor }}; border-radius:3px;"></div>
                                </div>
                            </div>

                            {{-- Stats --}}
                            <div class="row g-1 mb-2">
                                <div class="col-4">
                                    <div class="proj-stat-cell">
                                        <div class="count text-primary">{{ $sc['doneAct'] }}<span class="text-muted fw-normal">/{{ $sc['totalAct'] }}</span></div>
                                        <div class="lbl">Activités</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="proj-stat-cell">
                                        <div class="count" style="color:#6f42c1;">{{ $sc['doneInsp'] }}<span class="text-muted fw-normal">/{{ $sc['totalInsp'] }}</span></div>
                                        <div class="lbl">Inspections</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="proj-stat-cell">
                                        <div class="count text-danger">{{ $sc['doneNc'] }}<span class="text-muted fw-normal">/{{ $sc['totalNc'] }}</span></div>
                                        <div class="lbl">NC résolues</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Milestones --}}
                            <div class="d-flex align-items-center flex-wrap gap-2 mb-2" style="font-size:.75rem;">
                                <span class="{{ $sc['reportMilestone'] ? 'text-success' : 'text-muted' }}">
                                    <i class="bi {{ $sc['reportMilestone'] ? 'bi-file-earmark-check-fill' : 'bi-file-earmark-x' }} me-1"></i>Rapport
                                </span>
                                <span class="{{ $sc['archiveMilestone'] ? 'text-success' : 'text-muted' }}">
                                    <i class="bi {{ $sc['archiveMilestone'] ? 'bi-archive-fill' : 'bi-archive' }} me-1"></i>Archivage
                                </span>
                                <span class="ms-auto text-muted" style="font-size:.7rem;">
                                    <i class="bi bi-check2-square me-1"></i>{{ $sc['phasesCount'] }}/8 phases
                                </span>
                            </div>

                            {{-- Actions --}}
                            <div class="d-flex gap-2 mt-auto">
                                <a href="{{ route('project.create', ['project_id' => $project->id]) }}"
                                   class="btn btn-sm btn-danger flex-grow-1" style="font-size:.78rem;">
                                    <i class="bi bi-kanban me-1"></i>Gérer
                                </a>
                                <a href="{{ route('projectOverview', $project->id) }}"
                                   class="btn btn-sm btn-outline-secondary" title="Vue d'ensemble" style="font-size:.78rem;">
                                    <i class="bi bi-bar-chart-line"></i>
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-warning text-center mt-2 mb-0">
                        <i class="bi bi-folder2-open me-2"></i>Aucun projet dans cette catégorie.
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($all_projects->hasPages())
        <div class="d-flex align-items-center justify-content-between mt-4 flex-wrap gap-2">
            <div class="text-muted small">
                Projets <strong>{{ $all_projects->firstItem() }}–{{ $all_projects->lastItem() }}</strong>
                sur <strong>{{ $all_projects->total() }}</strong>
            </div>
            <div>
                {{ $all_projects->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif

    </div>

    <script>
    (function () {
        const input = document.getElementById('proj-search-input');
        const form  = document.getElementById('proj-search-form');
        if (!input || !form) return;
        let timer;
        input.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(function () { form.submit(); }, 400);
        });
    })();
    </script>

    @if(Auth::user()->canCreateProject())
        @include('partials.dialog-create-project')

        <script>
        (function () {
            const form    = document.getElementById('formCreateNewProject');
            const errBox  = document.getElementById('error-messages');
            const btn     = form ? form.querySelector('[type=submit]') : null;
            const redirect = '{{ route('project.create') }}';

            if (!form) return;

            form.addEventListener('submit', function (e) {
                e.preventDefault();

                if (errBox) errBox.innerHTML = '';
                if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating…'; }

                fetch(form.action, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: new FormData(form),
                })
                .then(r => r.json())
                .then(data => {
                    if (data.code_erreur !== 0) {
                        if (errBox) errBox.innerHTML = `<div class="alert alert-danger py-2 px-3 small">${data.message}</div>`;
                        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-check2-circle me-2"></i>Create Study'; }
                        return;
                    }
                    window.location.href = redirect + '?project_id=' + data.data.project_id;
                })
                .catch(() => {
                    if (errBox) errBox.innerHTML = '<div class="alert alert-danger py-2 px-3 small">Erreur réseau. Veuillez réessayer.</div>';
                    if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-check2-circle me-2"></i>Create Study'; }
                });
            });
        })();
        </script>
    @endif
@endsection

@extends('index-new')
@section('title', 'Project Overview – ' . $project->project_code)

@section('content')
<style>
:root { --ov-red:#C10202; --ov-dark:#8b0001; }
.ov-header { background:linear-gradient(135deg,#c20102,#8b0001); color:#fff; border-radius:1rem; padding:1.4rem 2rem; }
.score-ring { width:70px; height:70px; }
.cat-card { border-radius:.75rem; border:1px solid #dee2e6; overflow:hidden; }
.cat-card .cat-header { padding:.65rem 1rem; font-weight:600; font-size:.9rem; cursor:pointer; display:flex; align-items:center; justify-content:space-between; }
.cat-card .cat-header:hover { filter:brightness(.95); }
.act-row { border-bottom:1px solid #f0f0f0; padding:.5rem .8rem; font-size:.85rem; }
.act-row:last-child { border-bottom:none; }
.badge-responsible { background:#fde8e8; color:#c20102; font-weight:500; font-size:.75rem; padding:2px 7px; border-radius:20px; }
.score-mini { font-size:.75rem; font-weight:600; }
.status-completed { color:#198754; }
.status-pending    { color:#6c757d; }
.status-in_progress{ color:#0d6efd; }
.inspection-card { border-left:4px solid #dee2e6; border-radius:.4rem; padding:.6rem .9rem; font-size:.85rem; background:#f9f9f9; margin-bottom:.5rem; }
.inspection-card.done    { border-left-color:#198754; background:#f0fff4; }
.inspection-card.scheduled { border-left-color:#0d6efd; background:#f0f6ff; }
.finding-pill { font-size:.75rem; padding:2px 9px; border-radius:20px; }
.find-pending  { background:#fff3cd; color:#856404; }
.find-resolved { background:#d1e7dd; color:#0a3622; }
.find-conform  { background:#e9ecef; color:#495057; }
</style>

{{-- ── Header ───────────────────────────────────────────────── --}}
<div class="ov-header d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <div class="small opacity-75 mb-1">
            <a href="{{ route('indexPage') }}" class="text-white opacity-75 text-decoration-none">Dashboard</a>
            <span class="mx-1">›</span> Project Overview
        </div>
        <h4 class="fw-bold mb-1"><i class="bi bi-folder2-open me-2"></i>{{ $project->project_code }}</h4>
        @if($project->project_title)
            <div class="small opacity-85">{{ $project->project_title }}</div>
        @endif
        <div class="d-flex flex-wrap gap-3 mt-2 small opacity-90">
            @if($project->studyDirector)
                <span><i class="bi bi-person-badge me-1"></i>Study Director: <strong>{{ $project->studyDirector->prenom }} {{ $project->studyDirector->nom }}</strong></span>
            @endif
            @if($project->projectManager)
                <span><i class="bi bi-person-gear me-1"></i>Project Manager: <strong>{{ $project->projectManager->prenom }} {{ $project->projectManager->nom }}</strong></span>
            @endif
            @if($project->date_debut_effective)
                <span><i class="bi bi-calendar-range me-1"></i>{{ date('d/m/Y', strtotime($project->date_debut_effective)) }}
                @if($project->date_fin_effective) → {{ date('d/m/Y', strtotime($project->date_fin_effective)) }}@endif</span>
            @endif
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge fs-6 fw-semibold px-3 py-2
            {{ $project->archived_at ? 'bg-dark' : ($project->project_stage === 'in progress' ? 'bg-success' : ($project->project_stage === 'completed' ? 'bg-secondary' : 'bg-warning text-dark')) }}">
            {{ ucfirst($project->project_stage ?? 'Unknown') }}
            @if($project->archived_at) <i class="bi bi-lock-fill ms-1"></i>@endif
        </span>
        <a href="{{ route('project.create', ['project_id' => $project->id]) }}" class="btn btn-light btn-sm fw-semibold">
            <i class="bi bi-pencil-square me-1"></i>Manage
        </a>
    </div>
</div>

{{-- ── Study Creation Details ───────────────────────────────── --}}
<div class="card border-0 shadow-sm mb-4 rounded-3 overflow-hidden">
    <div class="card-header fw-semibold py-2 px-3 d-flex align-items-center justify-content-between"
         style="background:linear-gradient(90deg,#c20102,#8b0001);color:#fff;">
        <span><i class="bi bi-info-circle me-2"></i>Study Details</span>
        @unless($project->archived_at)
        <a href="{{ route('project.create', ['project_id' => $project->id]) }}#step1"
           class="btn btn-sm py-0 px-2" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.4);font-size:.75rem;">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
        @endunless
    </div>
    <div class="card-body py-3 px-3">
        <div class="row g-3">
            <div class="col-md-6 col-lg-3">
                <div class="small text-muted fw-semibold mb-1" style="font-size:.72rem;text-transform:uppercase;">Study Code</div>
                <div class="fw-bold" style="color:#c20102;">{{ $project->project_code }}</div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="small text-muted fw-semibold mb-1" style="font-size:.72rem;text-transform:uppercase;">GLP Status</div>
                @if($project->is_glp)
                    <span class="badge bg-success fs-6"><i class="bi bi-patch-check me-1"></i>GLP</span>
                @else
                    <span class="badge bg-secondary fs-6">Non-GLP</span>
                @endif
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="small text-muted fw-semibold mb-1" style="font-size:.72rem;text-transform:uppercase;">Start Date</div>
                <div>{{ $project->date_debut_effective ? date('d/m/Y', strtotime($project->date_debut_effective)) : '—' }}</div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="small text-muted fw-semibold mb-1" style="font-size:.72rem;text-transform:uppercase;">End Date</div>
                <div>{{ $project->date_fin_effective ? date('d/m/Y', strtotime($project->date_fin_effective)) : '—' }}</div>
            </div>

            {{-- Study Director --}}
            @php $sdForm = $project->studyDirectorAppointmentForm; $sd = $sdForm?->studyDirector; @endphp
            <div class="col-md-6 col-lg-4">
                <div class="small text-muted fw-semibold mb-1" style="font-size:.72rem;text-transform:uppercase;">Study Director</div>
                @if($sd)
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-person-badge text-primary"></i>
                        <span class="fw-semibold">{{ trim(($sd->titre_personnel ?? '') . ' ' . $sd->prenom . ' ' . $sd->nom) }}</span>
                    </div>
                    @if($sdForm->sd_appointment_date)
                        <div class="small text-muted mt-1"><i class="bi bi-calendar3 me-1"></i>Appointed: {{ \Carbon\Carbon::parse($sdForm->sd_appointment_date)->format('d/m/Y') }}</div>
                    @endif
                    @if($sdForm->sd_appointment_file)
                        <a href="{{ asset('storage/sd_appointment_files/' . $sdForm->sd_appointment_file) }}"
                           target="_blank" class="btn btn-xs btn-outline-primary mt-1" style="font-size:.74rem;padding:2px 8px;">
                            <i class="bi bi-file-earmark-arrow-down me-1"></i>Appointment Letter
                        </a>
                    @endif
                @else
                    <span class="text-muted">Not assigned</span>
                @endif
            </div>

            {{-- Study Types --}}
            <div class="col-md-6 col-lg-4">
                <div class="small text-muted fw-semibold mb-1" style="font-size:.72rem;text-transform:uppercase;">Study Types</div>
                <div class="d-flex flex-wrap gap-1">
                    @forelse($project->studyTypesApplied as $st)
                        <span class="badge" style="background:#c20102;font-size:.72rem;">{{ $st->study_type_name }}</span>
                    @empty
                        <span class="text-muted">—</span>
                    @endforelse
                </div>
            </div>

            {{-- Products --}}
            <div class="col-md-6 col-lg-4">
                <div class="small text-muted fw-semibold mb-1" style="font-size:.72rem;text-transform:uppercase;">Products Evaluated</div>
                <div class="d-flex flex-wrap gap-1">
                    @forelse($project->productTypesEvaluated as $pt)
                        <span class="badge bg-info text-dark" style="font-size:.72rem;">{{ $pt->product_type_name }}</span>
                    @empty
                        <span class="text-muted">—</span>
                    @endforelse
                </div>
            </div>

            {{-- Key Personnel --}}
            @if($project->keyPersonnelProject->isNotEmpty())
            <div class="col-12">
                <div class="small text-muted fw-semibold mb-1" style="font-size:.72rem;text-transform:uppercase;">Key Personnel</div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($project->keyPersonnelProject as $kp)
                        <span class="badge" style="background:#fde8e8;color:#c20102;font-weight:500;font-size:.75rem;">
                            <i class="bi bi-person me-1"></i>{{ trim(($kp->titre_personnel ?? '') . ' ' . $kp->prenom . ' ' . $kp->nom) }}
                        </span>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Other submitted documents --}}
            @if($project->otherBasicDocuments->isNotEmpty())
            <div class="col-12">
                <div class="small text-muted fw-semibold mb-1" style="font-size:.72rem;text-transform:uppercase;">Submitted Documents</div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($project->otherBasicDocuments as $doc)
                        <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank"
                           class="btn btn-sm btn-outline-secondary" style="font-size:.78rem;padding:3px 10px;">
                            <i class="bi bi-file-earmark-arrow-down me-1"></i>{{ $doc->document_name ?? $doc->file_name ?? 'Document' }}
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ── Score summary row ────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    @php
        $cats = [
            ['label' => 'Overall',      'pct' => $score['overall'],      'icon' => 'bi-speedometer2',      'color' => '#c20102'],
            ['label' => 'Activities',   'pct' => $score['actScore'],     'icon' => 'bi-list-check',         'color' => '#0d6efd'],
            ['label' => 'Inspections',  'pct' => $score['critScore'],    'icon' => 'bi-shield-check',       'color' => '#6f42c1'],
            ['label' => 'Findings',     'pct' => $score['findScore'],    'icon' => 'bi-exclamation-circle', 'color' => '#C10202'],
            ['label' => 'Report',       'pct' => $score['reportScore'],  'icon' => 'bi-file-earmark-text',  'color' => '#198754'],
            ['label' => 'Archiving',    'pct' => $score['archiveScore'], 'icon' => 'bi-archive',            'color' => '#6c757d'],
        ];
    @endphp
    @foreach($cats as $cat)
    @php $barCol = $cat['pct'] >= 80 ? '#198754' : ($cat['pct'] >= 50 ? '#ffc107' : '#dc3545'); @endphp
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="mb-1" style="color:{{ $cat['color'] }}; font-size:1.6rem;"><i class="bi {{ $cat['icon'] }}"></i></div>
            <div class="fw-bold fs-5 mb-0" style="color:{{ $barCol }}">{{ $cat['pct'] }}%</div>
            <div class="small text-muted">{{ $cat['label'] }}</div>
            <div class="progress mt-2" style="height:5px;">
                <div class="progress-bar" style="width:{{ $cat['pct'] }}%; background:{{ $barCol }};"></div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ── Main content ─────────────────────────────────────────── --}}
<div class="accordion" id="ovAccordion">

    {{-- ══ 1. STUDY ACTIVITIES ════════════════════════════════ --}}
    <div class="accordion-item border-0 shadow-sm mb-3 rounded-3 overflow-hidden">
        <h2 class="accordion-header">
            <button class="accordion-button fw-semibold" type="button"
                    data-bs-toggle="collapse" data-bs-target="#colAct">
                <i class="bi bi-list-check me-2 text-primary"></i>
                Study Activities
                @php
                    $totalActs    = $project->allActivitiesProject->count();
                    $doneActs     = $project->allActivitiesProject->where('status','completed')->count();
                @endphp
                <span class="ms-2 badge bg-primary">{{ $doneActs }}/{{ $totalActs }}</span>
                <span class="ms-2 badge {{ $score['actScore'] >= 80 ? 'bg-success' : ($score['actScore'] >= 50 ? 'bg-warning text-dark' : 'bg-danger') }}">
                    {{ $score['actScore'] }}%
                </span>
                <a href="{{ route('project.activities.pdf', $project->id) }}"
                   target="_blank"
                   class="ms-auto btn btn-sm btn-outline-light"
                   style="font-size:.72rem;padding:2px 8px;white-space:nowrap;"
                   onclick="event.stopPropagation();">
                    <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                </a>
            </button>
        </h2>
        <div id="colAct" class="accordion-collapse collapse show" data-bs-parent="#ovAccordion">
            <div class="accordion-body p-0">
                @forelse($activitiesByCategory as $catName => $activities)
                    @php
                        $catTotal = $activities->count();
                        $catDone  = $activities->where('status','completed')->count();
                        $catPct   = $catTotal > 0 ? round($catDone/$catTotal*100) : 100;
                    @endphp
                    <div class="cat-card mx-3 mb-3 mt-2">
                        <div class="cat-header bg-light"
                             data-bs-toggle="collapse"
                             data-bs-target="#cat-{{ Str::slug($catName) }}">
                            <span>
                                <i class="bi bi-folder me-1 text-secondary"></i>{{ $catName }}
                                <span class="badge bg-secondary ms-1">{{ $catDone }}/{{ $catTotal }}</span>
                            </span>
                            <span class="d-flex align-items-center gap-2">
                                <span style="width:80px;">
                                    <div class="progress" style="height:6px;">
                                        <div class="progress-bar {{ $catPct >= 80 ? 'bg-success' : ($catPct >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                             style="width:{{ $catPct }}%"></div>
                                    </div>
                                </span>
                                <span class="score-mini">{{ $catPct }}%</span>
                                <i class="bi bi-chevron-down"></i>
                            </span>
                        </div>
                        <div class="collapse show" id="cat-{{ Str::slug($catName) }}">
                            @foreach($activities as $act)
                            <div class="act-row d-flex align-items-start gap-2">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        @if($act->status === 'completed')
                                            <i class="bi bi-check-circle-fill text-success"></i>
                                        @elseif($act->status === 'in_progress')
                                            <i class="bi bi-arrow-repeat text-primary"></i>
                                        @else
                                            <i class="bi bi-circle text-secondary"></i>
                                        @endif
                                        <span class="fw-semibold {{ $act->status === 'completed' ? 'text-decoration-line-through text-muted' : '' }}">
                                            {{ $act->study_activity_name }}
                                        </span>
                                        @if($act->phase_critique)
                                            <span class="badge" style="background:#C10202;font-size:.68rem;">
                                                <i class="bi bi-shield-exclamation me-1"></i>Critical
                                            </span>
                                        @endif
                                        @if($act->status === 'completed')
                                            <span class="badge bg-success-subtle text-success border border-success">Completed</span>
                                        @elseif($act->status === 'in_progress')
                                            <span class="badge bg-primary-subtle text-primary border border-primary">In progress</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary border">Pending</span>
                                        @endif
                                    </div>
                                    <div class="d-flex flex-wrap gap-3 mt-1 text-muted" style="font-size:.78rem;">
                                        @if($act->personneResponsable)
                                            <span><i class="bi bi-person-fill me-1 text-primary"></i>
                                                <strong class="text-dark">{{ $act->personneResponsable->prenom }} {{ $act->personneResponsable->nom }}</strong>
                                            </span>
                                        @endif
                                        @if($act->executedBy && $act->status === 'completed')
                                            <span><i class="bi bi-check2 me-1 text-success"></i>
                                                Executed by {{ $act->executedBy->prenom }} {{ $act->executedBy->nom }}
                                            </span>
                                        @endif
                                        @if($act->estimated_activity_date)
                                            <span><i class="bi bi-calendar3 me-1"></i>
                                                {{ date('d/m/Y', strtotime($act->estimated_activity_date)) }}
                                                @if($act->estimated_activity_end_date && $act->estimated_activity_end_date != $act->estimated_activity_date)
                                                    → {{ date('d/m/Y', strtotime($act->estimated_activity_end_date)) }}
                                                @endif
                                            </span>
                                        @endif
                                        @if($act->actual_activity_date)
                                            <span class="text-success"><i class="bi bi-calendar-check me-1"></i>
                                                Done {{ date('d/m/Y', strtotime($act->actual_activity_date)) }}
                                            </span>
                                        @endif
                                    </div>
                                    @if($act->commentaire)
                                        <div class="small text-muted fst-italic mt-1">{{ $act->commentaire }}</div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center py-4">No activities registered.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ══ ACTIVITIES TIMELINE ═══════════════════════════════ --}}
    @php
        $timelineActivities = $project->allActivitiesProject
            ->filter(fn($a) => $a->estimated_activity_date || $a->actual_activity_date)
            ->sortBy('estimated_activity_date')
            ->values();
    @endphp
    @if($timelineActivities->isNotEmpty())
    <div class="accordion-item border-0 shadow-sm mb-3 rounded-3 overflow-hidden">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed fw-semibold" type="button"
                    data-bs-toggle="collapse" data-bs-target="#colTimeline">
                <i class="bi bi-calendar3-range me-2 text-warning"></i>
                Activities Timeline
                <span class="ms-2 badge bg-warning text-dark">{{ $timelineActivities->count() }} activities</span>
            </button>
        </h2>
        <div id="colTimeline" class="accordion-collapse collapse" data-bs-parent="#ovAccordion">
            <div class="accordion-body pt-4 pb-2">
                <style>
                .tl-wrap { position:relative; padding-left:36px; }
                .tl-wrap::before { content:''; position:absolute; left:14px; top:0; bottom:0; width:2px; background:linear-gradient(to bottom,#c20102,#dee2e6); }
                .tl-item { position:relative; margin-bottom:18px; }
                .tl-dot { position:absolute; left:-29px; top:4px; width:16px; height:16px; border-radius:50%; border:2px solid #fff; box-shadow:0 0 0 2px currentColor; }
                .tl-dot.done    { background:#198754; color:#198754; }
                .tl-dot.in-prog { background:#0d6efd; color:#0d6efd; }
                .tl-dot.pending { background:#adb5bd; color:#adb5bd; }
                .tl-dot.critical{ background:#C10202; color:#C10202; box-shadow:0 0 0 3px rgba(193,2,2,.25); }
                .tl-label { font-size:.8rem; font-weight:600; line-height:1.3; }
                .tl-meta  { font-size:.72rem; color:#6c757d; margin-top:2px; }
                .tl-date  { font-size:.72rem; white-space:nowrap; }
                </style>
                <div class="tl-wrap">
                @foreach($timelineActivities as $ta)
                @php
                    $isDone  = $ta->status === 'completed';
                    $isInProg= $ta->status === 'in_progress';
                    $isCrit  = (bool)$ta->phase_critique;
                    $dotCls  = $isDone ? 'done' : ($isInProg ? 'in-prog' : 'pending');
                    if ($isCrit && !$isDone) $dotCls = 'critical';
                    $dispDate = $ta->actual_activity_date
                        ? \Carbon\Carbon::parse($ta->actual_activity_date)->format('d/m/Y')
                        : ($ta->estimated_activity_date ? \Carbon\Carbon::parse($ta->estimated_activity_date)->format('d/m/Y') . ' (est.)' : '');
                @endphp
                <div class="tl-item">
                    <span class="tl-dot {{ $dotCls }}"></span>
                    <div class="d-flex align-items-start justify-content-between gap-2 flex-wrap">
                        <div>
                            <div class="tl-label">
                                {{ $ta->study_activity_name }}
                                @if($isCrit)
                                    <span class="badge ms-1" style="background:#C10202;font-size:.65rem;vertical-align:middle;">
                                        <i class="bi bi-shield-exclamation me-1"></i>Critical
                                    </span>
                                @endif
                            </div>
                            <div class="tl-meta">
                                {{ $ta->category->name ?? '' }}
                                @if($ta->personneResponsable)
                                    &nbsp;·&nbsp; <i class="bi bi-person me-1"></i>{{ $ta->personneResponsable->prenom }} {{ $ta->personneResponsable->nom }}
                                @endif
                            </div>
                        </div>
                        <div class="text-end tl-date">
                            <span class="{{ $isDone ? 'text-success' : 'text-muted' }}">
                                <i class="bi bi-{{ $isDone ? 'calendar-check' : 'calendar3' }} me-1"></i>{{ $dispDate }}
                            </span>
                            @if($isDone)
                                <div><span class="badge bg-success-subtle text-success border border-success" style="font-size:.65rem;">Done</span></div>
                            @elseif($isInProg)
                                <div><span class="badge bg-primary-subtle text-primary border border-primary" style="font-size:.65rem;">In progress</span></div>
                            @elseif($isCrit)
                                <div><span class="badge" style="background:rgba(193,2,2,.1);color:#C10202;border:1px solid #C10202;font-size:.65rem;">Pending critical</span></div>
                            @else
                                <div><span class="badge bg-secondary-subtle text-secondary border" style="font-size:.65rem;">Pending</span></div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
                </div>

                {{-- Legend --}}
                <div class="d-flex flex-wrap gap-3 mt-2 ms-1" style="font-size:.75rem;color:#6c757d;">
                    <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#198754;margin-right:4px;"></span>Completed</span>
                    <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#0d6efd;margin-right:4px;"></span>In progress</span>
                    <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#adb5bd;margin-right:4px;"></span>Pending</span>
                    <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#C10202;margin-right:4px;"></span>Critical phase pending</span>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ══ DOCUMENTS ══════════════════════════════════════════ --}}
    @include('partials.project-documents-overview', [
        'project'            => $project,
        'qaStatement'        => $qaStatement,
        'canDownloadAll'     => $canDownloadAll,
        'canDownloadQA'      => $canDownloadQA,
        'canDownloadProject' => $canDownloadProject,
    ])

    {{-- ══ 2. QA INSPECTIONS ══════════════════════════════════ --}}
    <div class="accordion-item border-0 shadow-sm mb-3 rounded-3 overflow-hidden">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed fw-semibold" type="button"
                    data-bs-toggle="collapse" data-bs-target="#colInsp">
                <i class="bi bi-shield-check me-2" style="color:#6f42c1;"></i>
                QA Inspections
                @php
                    $totalInsp = $inspections->count();
                    $doneInsp  = $inspections->filter(fn($i) => !is_null($i->date_performed))->count();
                @endphp
                <span class="ms-2 badge bg-purple" style="background:#6f42c1;">{{ $doneInsp }}/{{ $totalInsp }}</span>
                <span class="ms-2 badge {{ $score['critScore'] >= 80 ? 'bg-success' : ($score['critScore'] >= 50 ? 'bg-warning text-dark' : 'bg-danger') }}">
                    {{ $score['critScore'] }}%
                </span>
            </button>
        </h2>
        <div id="colInsp" class="accordion-collapse collapse" data-bs-parent="#ovAccordion">
            <div class="accordion-body">
                @forelse($inspections as $insp)
                @php
                    $isDone   = !is_null($insp->date_performed);
                    $cardCls  = $isDone ? 'done' : 'scheduled';
                    $typeCol  = match($insp->type_inspection) {
                        'Facility Inspection'       => '#0d6efd',
                        'Process Inspection'        => '#6f42c1',
                        'Study Inspection'          => '#198754',
                        'Critical Phase Inspection' => '#C10202',
                        default => '#6c757d',
                    };
                    $pendingFinds  = $insp->findings->where('is_conformity', 0)->where('status', 'pending')->count();
                    $resolvedFinds = $insp->findings->where('is_conformity', 0)->where('status', 'complete')->count();
                    $conformFinds  = $insp->findings->where('is_conformity', 1)->count();
                @endphp
                <div class="inspection-card {{ $cardCls }}">
                    <div class="d-flex align-items-start justify-content-between gap-2 flex-wrap">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                @if($isDone)
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Done</span>
                                @else
                                    <span class="badge bg-primary"><i class="bi bi-calendar-event me-1"></i>Scheduled</span>
                                @endif
                                <span class="fw-semibold">{{ $insp->inspection_name ?? $insp->type_inspection }}</span>
                                <span class="badge" style="background:{{ $typeCol }}; color:#fff; font-size:.72rem;">{{ $insp->type_inspection }}</span>
                            </div>
                            <div class="d-flex flex-wrap gap-3 text-muted" style="font-size:.78rem;">
                                @if($insp->inspector)
                                    <span><i class="bi bi-person-badge me-1 text-primary"></i>
                                        <strong class="text-dark">{{ $insp->inspector->prenom }} {{ $insp->inspector->nom }}</strong>
                                        <em class="text-muted">(QA Inspector)</em>
                                    </span>
                                @endif
                                <span><i class="bi bi-calendar3 me-1"></i>Scheduled: {{ \Carbon\Carbon::parse($insp->date_scheduled)->format('d/m/Y') }}</span>
                                @if($insp->date_performed)
                                    <span class="text-success"><i class="bi bi-calendar-check me-1"></i>Performed: {{ \Carbon\Carbon::parse($insp->date_performed)->format('d/m/Y') }}</span>
                                @endif
                            </div>
                            <div class="d-flex flex-wrap gap-1 mt-1">
                                @if($pendingFinds)  <span class="finding-pill find-pending"><i class="bi bi-exclamation me-1"></i>{{ $pendingFinds }} pending</span>@endif
                                @if($resolvedFinds) <span class="finding-pill find-resolved"><i class="bi bi-check2 me-1"></i>{{ $resolvedFinds }} resolved</span>@endif
                                @if($conformFinds)  <span class="finding-pill find-conform"><i class="bi bi-check-all me-1"></i>{{ $conformFinds }} conformity</span>@endif
                                @if(!$insp->findings->count()) <span class="small text-muted">No findings yet</span>@endif
                            </div>
                        </div>
                        @if(!$isDone)
                        <div>
                            <button class="btn btn-sm btn-success fw-semibold"
                                    onclick="markInspectionDone({{ $insp->id }}, this)">
                                <i class="bi bi-check-circle me-1"></i>Mark as Done
                            </button>
                        </div>
                        @endif
                    </div>

                    {{-- Findings detail --}}
                    @if($insp->findings->isNotEmpty())
                    <div class="mt-2 ps-2 border-start border-2" style="border-color:#dee2e6 !important;">
                        @foreach($insp->findings as $f)
                        @php
                            $fCls  = $f->is_conformity ? 'find-conform' : ($f->status === 'complete' ? 'find-resolved' : 'find-pending');
                            $fIcon = $f->is_conformity ? 'bi-check-all' : ($f->status === 'complete' ? 'bi-check2-circle' : 'bi-exclamation-triangle');
                        @endphp
                        <div class="d-flex align-items-start gap-2 py-1" style="font-size:.8rem;">
                            <i class="bi {{ $fIcon }} mt-1 {{ $f->is_conformity ? 'text-secondary' : ($f->status === 'complete' ? 'text-success' : 'text-warning') }}"></i>
                            <div class="flex-grow-1">
                                <span>{{ $f->finding_text }}</span>
                                @if($f->assignedTo)
                                    <span class="badge-responsible ms-1">
                                        <i class="bi bi-person me-1"></i>{{ $f->assignedTo->prenom }} {{ $f->assignedTo->nom }}
                                    </span>
                                @endif
                                @if($f->deadline_date)
                                    <span class="text-muted ms-1"><i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($f->deadline_date)->format('d/m/Y') }}</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                @empty
                    <p class="text-muted text-center py-4">No QA inspections scheduled for this project.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ══ 3. UNRESOLVED FINDINGS ═════════════════════════════ --}}
    <div class="accordion-item border-0 shadow-sm mb-3 rounded-3 overflow-hidden">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed fw-semibold" type="button"
                    data-bs-toggle="collapse" data-bs-target="#colFind">
                <i class="bi bi-exclamation-triangle me-2 text-danger"></i>
                QA Findings
                @php $pendingCount = $allFindings->where('status','pending')->count(); @endphp
                @if($pendingCount)
                    <span class="ms-2 badge bg-danger">{{ $pendingCount }} pending</span>
                @else
                    <span class="ms-2 badge bg-success">All resolved</span>
                @endif
                <span class="ms-2 badge {{ $score['findScore'] >= 80 ? 'bg-success' : ($score['findScore'] >= 50 ? 'bg-warning text-dark' : 'bg-danger') }}">
                    {{ $score['findScore'] }}%
                </span>
            </button>
        </h2>
        <div id="colFind" class="accordion-collapse collapse" data-bs-parent="#ovAccordion">
            <div class="accordion-body">
                @forelse($allFindings as $f)
                @php $isPending = $f->status === 'pending'; @endphp
                <div class="act-row d-flex align-items-start gap-2">
                    <i class="bi {{ $isPending ? 'bi-exclamation-triangle-fill text-warning' : 'bi-check-circle-fill text-success' }} mt-1 flex-shrink-0"></i>
                    <div class="flex-grow-1">
                        <div>{{ $f->finding_text }}</div>
                        <div class="d-flex flex-wrap gap-2 mt-1 text-muted" style="font-size:.78rem;">
                            @if($f->assignedTo)
                                <span><i class="bi bi-person-fill text-primary me-1"></i>
                                    <strong class="text-dark">{{ $f->assignedTo->prenom }} {{ $f->assignedTo->nom }}</strong>
                                </span>
                            @endif
                            @if($f->deadline_date)
                                <span><i class="bi bi-calendar3 me-1"></i>Deadline: {{ \Carbon\Carbon::parse($f->deadline_date)->format('d/m/Y') }}</span>
                            @endif
                            <span class="badge {{ $isPending ? 'bg-warning text-dark' : 'bg-success' }}">{{ $isPending ? 'Pending' : 'Resolved' }}</span>
                        </div>
                        @if(!$isPending && $f->action_point)
                            <div class="small text-success mt-1"><i class="bi bi-check2-circle me-1"></i>{{ $f->action_point }}</div>
                        @endif
                    </div>
                </div>
                @empty
                    <p class="text-muted text-center py-4">No non-conformity findings.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ══ QA Activities Checklist (GLP only) ══════════════════ --}}
    @if($project->is_glp)
    @php
        $qaChecklistCount = \App\Models\Pro_QaActivitiesChecklist::where('project_id', $project->id)->where('is_checked', true)->count();
        $qaChecklistTotal = 20;
    @endphp
    <div class="card border-0 shadow-sm mb-3" style="border-radius:12px;overflow:hidden;">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-3 py-3 px-4">
            <div class="d-flex align-items-center gap-3">
                <div style="background:#fde8e8;border-radius:10px;width:44px;height:44px;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-card-checklist" style="font-size:1.25rem;color:#c20102;"></i>
                </div>
                <div>
                    <div class="fw-semibold" style="font-size:.95rem;color:#c20102;">QA Activities Checklist</div>
                    <div class="text-muted" style="font-size:.78rem;">
                        QA-PR-1-011/05 &mdash;
                        <span class="badge {{ $qaChecklistCount === $qaChecklistTotal ? 'bg-success' : 'bg-secondary' }}" style="font-size:.7rem;">
                            {{ $qaChecklistCount }}/{{ $qaChecklistTotal }} checked
                        </span>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('project.qa-checklist', $project->id) }}"
                   class="btn btn-sm fw-semibold"
                   style="background:#c20102;color:#fff;border:none;font-size:.8rem;">
                    <i class="bi bi-box-arrow-up-right me-1"></i>View Checklist
                </a>
                <a href="{{ route('printQaActivitiesChecklist', ['project_id' => $project->id]) }}"
                   target="_blank"
                   class="btn btn-sm btn-outline-secondary fw-semibold"
                   style="font-size:.8rem;">
                    <i class="bi bi-printer me-1"></i>Print
                </a>
            </div>
        </div>
    </div>
    @endif

    {{-- ══ DATA MANAGEMENT ═══════════════════════════════════ --}}
    @php
        $dmDbs  = $project->dmDatabases;
        $dmPcs  = $project->dmPcAssignments;
        $dmSvs  = $project->dmSoftwareValidations;
        $dmDlvs = $project->dmDataloggerValidations;
        $dmDes  = $project->dmDoubleEntries;
        $dmTotal = $dmDbs->count() + $dmPcs->count() + $dmDes->count();
    @endphp
    <div class="accordion-item border-0 shadow-sm mb-3 rounded-3 overflow-hidden">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed fw-semibold" type="button"
                    data-bs-toggle="collapse" data-bs-target="#colDM">
                <i class="bi bi-database me-2" style="color:#0d6efd;"></i>
                Data Management
                <span class="ms-2 badge" style="background:#0d6efd;">
                    {{ $dmDbs->count() }} DB · {{ $dmPcs->count() }} PC · {{ $dmDes->count() }} Sessions
                </span>
                @if($dmTotal > 0)
                    <span class="ms-2 badge bg-success">Active</span>
                @else
                    <span class="ms-2 badge bg-secondary">Not started</span>
                @endif
                <a href="{{ route('project.create', ['project_id' => $project->id]) }}#step7"
                   class="ms-auto btn btn-sm btn-outline-light"
                   style="font-size:.72rem;padding:2px 8px;white-space:nowrap;"
                   onclick="event.stopPropagation();">
                    <i class="bi bi-pencil me-1"></i>Edit
                </a>
            </button>
        </h2>
        <div id="colDM" class="accordion-collapse collapse" data-bs-parent="#ovAccordion">
            <div class="accordion-body pt-3 pb-2 px-3">

                {{-- ── Databases ── --}}
                <h6 class="fw-semibold mb-2" style="color:#0d6efd;font-size:.82rem;text-transform:uppercase;letter-spacing:.05em;">
                    <i class="bi bi-server me-1"></i>Bases de données ({{ $dmDbs->count() }})
                </h6>
                @if($dmDbs->isNotEmpty())
                <div class="table-responsive mb-3">
                    <table class="table table-sm table-bordered align-middle mb-0" style="font-size:.82rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Nom</th>
                                <th>Type</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($dmDbs as $db)
                            <tr>
                                <td class="fw-semibold">{{ $db->name }}</td>
                                <td><span class="badge bg-info text-dark">{{ ucfirst(str_replace('_',' ',$db->type)) }}</span></td>
                                <td class="text-muted">{{ $db->description ?: '—' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                    <p class="text-muted small mb-3">Aucune base de données enregistrée.</p>
                @endif

                {{-- ── PC Assignments ── --}}
                <h6 class="fw-semibold mb-2" style="color:#0d6efd;font-size:.82rem;text-transform:uppercase;letter-spacing:.05em;">
                    <i class="bi bi-pc-display me-1"></i>PC de saisie ({{ $dmPcs->count() }})
                </h6>
                @if($dmPcs->isNotEmpty())
                <div class="table-responsive mb-3">
                    <table class="table table-sm table-bordered align-middle mb-0" style="font-size:.82rem;">
                        <thead class="table-light">
                            <tr>
                                <th>PC</th>
                                <th>Numéro de série</th>
                                <th class="text-center">GLP</th>
                                <th>Attribué le</th>
                                <th>Retourné le</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($dmPcs as $pc)
                            <tr>
                                <td class="fw-semibold">{{ $pc->pc_name }}</td>
                                <td class="text-muted">{{ $pc->pc_serial ?: '—' }}</td>
                                <td class="text-center">
                                    @if($pc->is_glp)
                                        <span class="badge bg-success" style="font-size:.68rem;">GLP</span>
                                    @else
                                        <span class="badge bg-secondary" style="font-size:.68rem;">Non-GLP</span>
                                    @endif
                                </td>
                                <td>{{ $pc->assigned_at ? \Carbon\Carbon::parse($pc->assigned_at)->format('d/m/Y') : '—' }}</td>
                                <td>{{ $pc->returned_at ? \Carbon\Carbon::parse($pc->returned_at)->format('d/m/Y') : '—' }}</td>
                                <td>
                                    @if($pc->returned_at)
                                        <span class="badge bg-secondary" style="font-size:.68rem;">Retourné</span>
                                    @else
                                        <span class="badge bg-success" style="font-size:.68rem;">En cours</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                    <p class="text-muted small mb-3">Aucun PC attribué.</p>
                @endif

                {{-- ── Software Validations (GLP only) ── --}}
                @if($project->is_glp && $dmSvs->isNotEmpty())
                <h6 class="fw-semibold mb-2" style="color:#0d6efd;font-size:.82rem;text-transform:uppercase;letter-spacing:.05em;">
                    <i class="bi bi-shield-check me-1"></i>Validations logicielles ({{ $dmSvs->count() }})
                </h6>
                <div class="table-responsive mb-3">
                    <table class="table table-sm table-bordered align-middle mb-0" style="font-size:.82rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Logiciel</th>
                                <th>Version</th>
                                <th>Date</th>
                                <th>Validé par</th>
                                <th>Statut</th>
                                <th>PDF</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($dmSvs as $sv)
                            <tr>
                                <td class="fw-semibold">{{ $sv->software_name }}</td>
                                <td>{{ $sv->current_software_version ?: '—' }}</td>
                                <td>{{ $sv->validation_date ? \Carbon\Carbon::parse($sv->validation_date)->format('d/m/Y') : '—' }}</td>
                                <td>{{ $sv->validation_done_by ?: '—' }}</td>
                                <td>
                                    @php $svBadge = match($sv->status){ 'validated'=>'bg-success','in_progress'=>'bg-warning text-dark', default=>'bg-secondary' }; @endphp
                                    <span class="badge {{ $svBadge }}" style="font-size:.68rem;">{{ ucfirst(str_replace('_',' ',$sv->status)) }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('pdf.dm.software-validation', $sv->id) }}"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-danger py-0 px-2"
                                       style="font-size:.72rem;">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                {{-- ── Datalogger Validations (GLP only) ── --}}
                @if($project->is_glp && $dmDlvs->isNotEmpty())
                <h6 class="fw-semibold mb-2" style="color:#0d6efd;font-size:.82rem;text-transform:uppercase;letter-spacing:.05em;">
                    <i class="bi bi-thermometer me-1"></i>Validations data loggers ({{ $dmDlvs->count() }})
                </h6>
                <div class="table-responsive mb-3">
                    <table class="table table-sm table-bordered align-middle mb-0" style="font-size:.82rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Nom</th>
                                <th>N° série</th>
                                <th>Localisation</th>
                                <th>Date</th>
                                <th>Validé par</th>
                                <th>Statut</th>
                                <th>Fichiers</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($dmDlvs as $dlv)
                            <tr>
                                <td class="fw-semibold">{{ $dlv->name }}</td>
                                <td>{{ $dlv->serial_number ?: '—' }}</td>
                                <td>{{ $dlv->location ?: '—' }}</td>
                                <td>{{ $dlv->validation_date ? \Carbon\Carbon::parse($dlv->validation_date)->format('d/m/Y') : '—' }}</td>
                                <td>{{ $dlv->validated_by ?: '—' }}</td>
                                <td>
                                    @php $dlBadge = match($dlv->status){ 'validated'=>'bg-success','in_progress'=>'bg-warning text-dark', default=>'bg-secondary' }; @endphp
                                    <span class="badge {{ $dlBadge }}" style="font-size:.68rem;">{{ ucfirst(str_replace('_',' ',$dlv->status)) }}</span>
                                </td>
                                <td>
                                    @if($dlv->files->isNotEmpty())
                                        @foreach($dlv->files as $dlf)
                                            <a href="{{ asset('storage/'.$dlf->file_path) }}" target="_blank"
                                               class="d-block text-truncate text-primary" style="font-size:.72rem;max-width:120px;"
                                               title="{{ $dlf->original_name }}">
                                                <i class="bi bi-paperclip me-1"></i>{{ $dlf->original_name }}
                                            </a>
                                        @endforeach
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                {{-- ── Double Data Entry Sessions ── --}}
                <h6 class="fw-semibold mb-2" style="color:#0d6efd;font-size:.82rem;text-transform:uppercase;letter-spacing:.05em;">
                    <i class="bi bi-input-cursor-text me-1"></i>Sessions de double saisie ({{ $dmDes->count() }})
                </h6>
                @if($dmDes->isNotEmpty())
                <div class="table-responsive mb-2">
                    <table class="table table-sm table-bordered align-middle mb-0" style="font-size:.82rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Base de données</th>
                                <th>1ère saisie</th>
                                <th>Opérateur 1</th>
                                <th>2ème saisie</th>
                                <th>Opérateur 2</th>
                                <th class="text-center">Conformité</th>
                                <th>Fichier comparaison</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($dmDes as $de)
                            <tr>
                                <td>{{ $de->database?->name ?: '—' }}</td>
                                <td>{{ $de->first_entry_date ? \Carbon\Carbon::parse($de->first_entry_date)->format('d/m/Y') : '—' }}</td>
                                <td>{{ $de->first_entry_by ?: '—' }}</td>
                                <td>{{ $de->second_entry_date ? \Carbon\Carbon::parse($de->second_entry_date)->format('d/m/Y') : '—' }}</td>
                                <td>{{ $de->second_entry_by ?: '—' }}</td>
                                <td class="text-center">
                                    @if(is_null($de->is_compliant))
                                        <span class="badge bg-secondary" style="font-size:.68rem;">—</span>
                                    @elseif($de->is_compliant)
                                        <span class="badge bg-success" style="font-size:.68rem;"><i class="bi bi-check2 me-1"></i>Conforme</span>
                                    @else
                                        <span class="badge bg-danger" style="font-size:.68rem;"><i class="bi bi-x me-1"></i>Non conforme</span>
                                    @endif
                                </td>
                                <td>
                                    @if($de->comparison_file_path)
                                        <a href="{{ asset('storage/'.$de->comparison_file_path) }}" target="_blank"
                                           class="text-primary text-truncate d-inline-block" style="max-width:120px;font-size:.72rem;"
                                           title="{{ $de->comparison_file_name }}">
                                            <i class="bi bi-paperclip me-1"></i>{{ $de->comparison_file_name }}
                                        </a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                    <p class="text-muted small mb-2">Aucune session de double saisie enregistrée.</p>
                @endif

            </div>
        </div>
    </div>

    {{-- ══ 4. REPORT PHASE ════════════════════════════════════ --}}
    <div class="accordion-item border-0 shadow-sm mb-3 rounded-3 overflow-hidden">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed fw-semibold" type="button"
                    data-bs-toggle="collapse" data-bs-target="#colReport">
                <i class="bi bi-file-earmark-text me-2 text-success"></i>
                Report Phase Documents
                <span class="ms-2 badge bg-secondary">{{ $project->reportPhaseDocuments->count() }}</span>
                <span class="ms-2 badge {{ $score['reportScore'] >= 80 ? 'bg-success' : 'bg-danger' }}">{{ $score['reportScore'] }}%</span>
            </button>
        </h2>
        <div id="colReport" class="accordion-collapse collapse" data-bs-parent="#ovAccordion">
            <div class="accordion-body p-0">
                @forelse($project->reportPhaseDocuments as $doc)
                <div class="act-row d-flex align-items-start gap-2">
                    <i class="bi bi-file-earmark-check text-success mt-1 flex-shrink-0 fs-5"></i>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">{{ $doc->title }}</div>
                        <div class="d-flex flex-wrap gap-2 mt-1 text-muted" style="font-size:.78rem;">
                            @if($doc->document_type) <span class="badge bg-light text-dark border">{{ $doc->document_type }}</span>@endif
                            @if($doc->submission_date) <span><i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($doc->submission_date)->format('d/m/Y') }}</span>@endif
                            @if($doc->status) <span class="badge {{ $doc->status === 'submitted' ? 'bg-success' : 'bg-warning text-dark' }}">{{ ucfirst($doc->status) }}</span>@endif
                        </div>
                        @if($doc->description) <div class="small text-muted mt-1">{{ $doc->description }}</div>@endif
                    </div>
                </div>
                @empty
                    <p class="text-muted text-center py-4">No report documents uploaded yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ══ 5. ARCHIVING ═══════════════════════════════════════ --}}
    <div class="accordion-item border-0 shadow-sm mb-3 rounded-3 overflow-hidden">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed fw-semibold" type="button"
                    data-bs-toggle="collapse" data-bs-target="#colArch">
                <i class="bi bi-archive me-2 text-secondary"></i>
                Archiving
                @if($project->archived_at)
                    <span class="ms-2 badge bg-dark"><i class="bi bi-lock-fill me-1"></i>Archived</span>
                @else
                    <span class="ms-2 badge bg-secondary">Not archived</span>
                @endif
                <span class="ms-2 badge {{ $score['archiveScore'] >= 80 ? 'bg-success' : 'bg-secondary' }}">{{ $score['archiveScore'] }}%</span>
            </button>
        </h2>
        <div id="colArch" class="accordion-collapse collapse" data-bs-parent="#ovAccordion">
            <div class="accordion-body">
                @if($project->archived_at)
                    <div class="alert alert-dark d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-lock-fill fs-5"></i>
                        <div>
                            <strong>Archived on {{ \Carbon\Carbon::parse($project->archived_at)->format('d/m/Y') }}</strong>
                            @if($project->archived_by)
                                <span class="text-muted small ms-2">by user #{{ $project->archived_by }}</span>
                            @endif
                        </div>
                    </div>
                @endif
                @if($project->archivingDocuments->isNotEmpty())
                    <h6 class="fw-semibold mb-2">Archiving Documents</h6>
                    @foreach($project->archivingDocuments as $doc)
                    <div class="act-row d-flex align-items-start gap-2">
                        <i class="bi bi-file-earmark-zip text-secondary mt-1 flex-shrink-0 fs-5"></i>
                        <div>
                            <div class="fw-semibold">{{ $doc->title }}</div>
                            <div class="d-flex flex-wrap gap-2 mt-1 text-muted" style="font-size:.78rem;">
                                @if($doc->document_type) <span class="badge bg-light text-dark border">{{ $doc->document_type }}</span>@endif
                                @if($doc->archive_date) <span><i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($doc->archive_date)->format('d/m/Y') }}</span>@endif
                                @if($doc->physical_location) <span><i class="bi bi-geo-alt me-1"></i>{{ $doc->physical_location }}</span>@endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <p class="text-muted text-center py-3">No archiving documents uploaded.</p>
                @endif
            </div>
        </div>
    </div>

</div>{{-- end accordion --}}

<script>
(function(){
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    window.markInspectionDone = function(inspectionId, btn) {
        if (!confirm('Mark this inspection as completed today?')) return;
        btn.disabled = true;
        fetch('{{ route("markInspectionDone") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ inspection_id: inspectionId }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                btn.disabled = false;
                alert(data.message);
            }
        })
        .catch(() => { btn.disabled = false; alert('Network error.'); });
    };
})();
</script>
@endsection

@extends('index-new')
@section('title', 'Diagnostic des projets — AIRID')

@section('content')
<style>
.diag-hero { background:linear-gradient(135deg,#1a3a6b 0%,#6f42c1 100%); border-radius:.75rem; padding:1.25rem 1.75rem; margin-bottom:1.5rem; color:#fff; }
.stat-pill  { border-radius:1rem; padding:.55rem 1.2rem; font-weight:700; font-size:.95rem; }
.diag-card  { border-left:4px solid transparent; transition:box-shadow .15s; }
.diag-card:hover { box-shadow:0 4px 14px rgba(0,0,0,.1); }
.diag-ok      { border-left-color:#198754; }
.diag-warning { border-left-color:#ffc107; }
.diag-danger  { border-left-color:#dc3545; }
.issue-badge { font-size:.72rem; font-weight:600; padding:.2rem .55rem; border-radius:.35rem; }
.ib-danger  { background:#fde8e8;color:#dc3545; }
.ib-warning { background:#fff8e1;color:#856404; }
.timeline-bar { height:6px; border-radius:3px; background:#e9ecef; overflow:hidden; }
.timeline-fill { height:100%; border-radius:3px; }
</style>

{{-- Hero --}}
<div class="diag-hero d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div class="d-flex align-items-center gap-3">
        <i class="bi bi-clipboard-pulse fs-2 opacity-75"></i>
        <div>
            <h4 class="fw-bold mb-0">Diagnostic des projets</h4>
            <p class="opacity-75 small mb-0">État de santé en temps réel — activités, rapports, archivage.</p>
        </div>
    </div>
    <div class="text-end small opacity-75">Au {{ $today->format('d/m/Y') }}</div>
</div>

{{-- Summary pills --}}
<div class="d-flex gap-3 mb-4 flex-wrap">
    <div class="stat-pill" style="background:#d4edda;color:#155724;">
        <i class="bi bi-check-circle me-1"></i>{{ $countOk }} à jour
    </div>
    <div class="stat-pill" style="background:#fff3cd;color:#856404;">
        <i class="bi bi-exclamation-triangle me-1"></i>{{ $countWarning }} avertissement(s)
    </div>
    <div class="stat-pill" style="background:#f8d7da;color:#721c24;">
        <i class="bi bi-x-circle me-1"></i>{{ $countDanger }} critique(s)
    </div>
    <div class="stat-pill" style="background:#e2e3e5;color:#383d41;">
        Total : {{ $diagnostics->count() }} projets actifs
    </div>
</div>

@forelse($diagnostics as $d)
@php
    $p      = $d['project'];
    $status = $d['status'];
    $issues = $d['issues'];
    $sda    = $p->studyDirectorAppointmentForm;
    $sd     = $sda?->studyDirector;
    $sdName = $sd ? trim(($sd->titre_personnel ?? '') . ' ' . $sd->prenom . ' ' . $sd->nom) : '—';

    $cardClass = match($status){ 'danger'=>'diag-danger','warning'=>'diag-warning',default=>'diag-ok' };
    $iconHtml  = match($status){
        'danger'  => '<i class="bi bi-x-circle-fill text-danger fs-5"></i>',
        'warning' => '<i class="bi bi-exclamation-triangle-fill text-warning fs-5"></i>',
        default   => '<i class="bi bi-check-circle-fill text-success fs-5"></i>',
    };
@endphp
<div class="card border-0 shadow-sm mb-3 diag-card {{ $cardClass }}">
    <div class="card-body py-3 px-4">
        <div class="d-flex align-items-start gap-3 flex-wrap">

            {{-- Status icon --}}
            <div class="pt-1">{!! $iconHtml !!}</div>

            {{-- Project info --}}
            <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('projectOverview', $p->id) }}"
                       class="fw-bold text-decoration-none" style="color:#1a3a6b;font-size:.95rem;">
                        {{ $p->project_code }}
                    </a>
                    @if($p->is_glp)
                        <span class="badge rounded-pill px-2" style="background:#1a3a6b;color:#fff;font-size:.65rem;">GLP</span>
                    @endif
                    <span class="text-muted small">{{ Str::limit($p->project_title, 80) }}</span>
                </div>
                <div class="text-muted small mt-1">
                    <i class="bi bi-person-badge me-1"></i>{{ $sdName }}
                    &nbsp;·&nbsp;
                    <i class="bi bi-activity me-1"></i>{{ ucfirst($p->project_stage ?? '—') }}
                    @if($d['lastExpDate'])
                        &nbsp;·&nbsp;
                        <i class="bi bi-flask me-1"></i>Dernier test : {{ $d['lastExpDate']->format('d/m/Y') }}
                    @endif
                </div>

                {{-- Issues --}}
                @if($issues)
                <div class="mt-2 d-flex flex-wrap gap-2">
                    @foreach($issues as $issue)
                    <span class="issue-badge ib-{{ $issue['severity'] }}">
                        <i class="bi bi-{{ $issue['severity'] === 'danger' ? 'x-circle' : 'exclamation-triangle' }} me-1"></i>
                        {{ $issue['label'] }}
                    </span>
                    @endforeach
                </div>
                @else
                <div class="mt-1 text-success small"><i class="bi bi-check2 me-1"></i>Tout est à jour.</div>
                @endif

                {{-- Overdue activities list --}}
                @if($d['overdueActivities']->isNotEmpty())
                <div class="mt-2">
                    <button class="btn btn-sm btn-outline-danger py-0 px-2" style="font-size:.72rem;"
                            type="button" data-bs-toggle="collapse"
                            data-bs-target="#overdue-{{ $p->id }}">
                        <i class="bi bi-list-ul me-1"></i>Voir les {{ $d['overdueActivities']->count() }} activité(s)
                    </button>
                    <div class="collapse mt-2" id="overdue-{{ $p->id }}">
                        <ul class="list-group list-group-flush" style="font-size:.78rem;">
                            @foreach($d['overdueActivities']->take(15) as $act)
                            <li class="list-group-item py-1 px-2 d-flex justify-content-between align-items-center">
                                <span>{{ $act->study_activity_name }}</span>
                                <span class="text-danger small">
                                    Prévu le {{ $act->estimated_activity_end_date
                                        ? \Carbon\Carbon::parse($act->estimated_activity_end_date)->format('d/m/Y')
                                        : '—' }}
                                    ({{ $act->estimated_activity_end_date
                                        ? \Carbon\Carbon::parse($act->estimated_activity_end_date)->diffForHumans()
                                        : '' }})
                                </span>
                            </li>
                            @endforeach
                            @if($d['overdueActivities']->count() > 15)
                            <li class="list-group-item py-1 px-2 text-muted small">… et {{ $d['overdueActivities']->count() - 15 }} autre(s)</li>
                            @endif
                        </ul>
                    </div>
                </div>
                @endif

                {{-- Archive countdown bar --}}
                @if($d['archiveDeadline'] && !$p->archived_at)
                @php
                    $daysLeft  = $d['daysToArchive'];
                    $totalDays = 90; // 3 months ≈ 90 days
                    $pct       = $daysLeft !== null ? max(0, min(100, round((($totalDays - $daysLeft) / $totalDays) * 100))) : 0;
                    $barColor  = $daysLeft <= 0 ? '#dc3545' : ($daysLeft <= 14 ? '#ffc107' : '#198754');
                @endphp
                <div class="mt-2">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span><i class="bi bi-archive me-1"></i>Délai d'archivage</span>
                        <span>{{ $d['archiveDeadline']->format('d/m/Y') }}
                            @if($daysLeft !== null)
                                ({{ $daysLeft > 0 ? "dans {$daysLeft} j." : abs($daysLeft) . ' j. de retard' }})
                            @endif
                        </span>
                    </div>
                    <div class="timeline-bar">
                        <div class="timeline-fill" style="width:{{ $pct }}%;background:{{ $barColor }};"></div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Quick action --}}
            <div class="text-end">
                <a href="{{ route('projectOverview', $p->id) }}"
                   class="btn btn-sm btn-outline-primary py-1 px-3" style="font-size:.78rem;">
                    <i class="bi bi-arrow-right-circle me-1"></i>Ouvrir
                </a>
            </div>

        </div>
    </div>
</div>
@empty
<div class="text-center py-5 text-muted">
    <i class="bi bi-check2-all fs-2 d-block mb-2 text-success"></i>
    Aucun projet actif trouvé.
</div>
@endforelse

@endsection

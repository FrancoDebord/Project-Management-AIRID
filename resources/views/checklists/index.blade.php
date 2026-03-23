<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checklists — {{ $inspection->inspection_name ?? $inspection->type_inspection }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --qa-brand: #C10202;
            --qa-brand-dark: #8b0001;
        }
        body { background: #f4f5f7; font-family: 'Segoe UI', sans-serif; }

        .page-header {
            background: linear-gradient(90deg, var(--qa-brand), var(--qa-brand-dark));
            color: #fff;
            padding: 20px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,.15);
        }
        .page-header .btn-back {
            background: rgba(255,255,255,.2);
            color: #fff;
            border: 1px solid rgba(255,255,255,.4);
            border-radius: 8px;
            font-size: .85rem;
            transition: background .2s;
        }
        .page-header .btn-back:hover { background: rgba(255,255,255,.35); color: #fff; }

        .checklist-card {
            border-radius: 14px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 4px 14px rgba(0,0,0,.05);
            background: #fff;
            transition: box-shadow .2s, transform .15s;
            height: 100%;
        }
        .checklist-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,.1); transform: translateY(-2px); }

        .card-letter {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, var(--qa-brand), var(--qa-brand-dark));
            color: #fff;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; font-weight: 700;
            flex-shrink: 0;
        }
        .card-title-text {
            font-size: .92rem;
            font-weight: 600;
            color: #1a1a1a;
            line-height: 1.3;
        }
        .card-qcount {
            font-size: .75rem;
            color: #888;
        }
        .badge-filled {
            background-color: #198754; color: #fff;
            border-radius: 999px; padding: .25rem .7rem; font-size: .75rem; font-weight: 600;
        }
        .badge-todo {
            background-color: #fd7e14; color: #fff;
            border-radius: 999px; padding: .25rem .7rem; font-size: .75rem; font-weight: 600;
        }
        .btn-open {
            background: linear-gradient(90deg, var(--qa-brand), var(--qa-brand-dark));
            color: #fff; border: none; border-radius: 8px;
            font-size: .82rem; font-weight: 600; padding: .35rem .9rem;
            transition: opacity .2s;
        }
        .btn-open:hover { opacity: .88; color: #fff; }

        .inspection-meta {
            background: #fff7f7;
            border-left: 4px solid var(--qa-brand);
            border-radius: 10px;
            padding: 14px 18px;
        }
    </style>
</head>
<body>

{{-- ── Header ── --}}
<div class="page-header">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="bi bi-clipboard2-check me-2"></i>
                @if(isset($progress))Facility Inspection Checklists @else Critical Phase Inspection Checklists @endif
            </h4>
            <small class="opacity-75">
                @if(isset($progress))
                    {{ $inspection->facility_location === 'cove' ? 'QA-PR-1-001B/06' : 'QA-PR-1-001A/06' }}
                    — {{ $inspection->facility_location === 'cove' ? 'Covè' : 'Cotonou' }}
                @else
                    QA-PR-1-003/05
                @endif
                — SANAS OECD GLP COMPLIANT FACILITY N° G0028
            </small>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            @if(isset($progress))
                @php
                    $printRoute = $inspection->type_inspection === 'Process Inspection'
                        ? 'checklist.processPrint'
                        : 'checklist.facilityPrint';
                @endphp
                <a href="{{ route($printRoute, $inspection->id) }}?mode=filled"
                   class="btn btn-back" target="_blank">
                    <i class="bi bi-printer me-1"></i>Imprimer rempli
                </a>
                <a href="{{ route($printRoute, $inspection->id) }}?mode=empty"
                   class="btn btn-back" target="_blank">
                    <i class="bi bi-file-earmark me-1"></i>Imprimer vierge
                </a>
                <a href="{{ route('checklist.report', $inspection->id) }}"
                   class="btn btn-back" target="_blank">
                    <i class="bi bi-file-earmark-text me-1"></i>QA Unit Report
                </a>
                <a href="{{ route('checklist.followup', $inspection->id) }}"
                   class="btn btn-back" target="_blank">
                    <i class="bi bi-file-earmark-check me-1"></i>Follow-Up Report
                </a>
            @endif
            <a href="{{ route('qaDashboard') }}" class="btn btn-back">
                <i class="bi bi-shield-check me-1"></i>Dashboard QA
            </a>
            @if ($inspection->project_id)
                <a href="/project/{{ $inspection->project_id }}/edit?project_id={{ $inspection->project_id }}"
                   class="btn btn-back">
                    <i class="bi bi-arrow-left me-1"></i>Retour au projet
                </a>
            @endif
            @if(!isset($progress) && $inspection->project_id)
            <a href="{{ route('checklist.report', $inspection->id) }}"
               class="btn btn-back" target="_blank">
                <i class="bi bi-file-earmark-text me-1"></i>QA Unit Report
            </a>
            <a href="{{ route('checklist.followup', $inspection->id) }}"
               class="btn btn-back" target="_blank">
                <i class="bi bi-file-earmark-check me-1"></i>Follow-Up Report
            </a>
            @endif
        </div>
    </div>
</div>

<div class="container-fluid py-4 px-4">

    {{-- Flash message --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Inspection meta --}}
    <div class="inspection-meta mb-4">
        <div class="row align-items-center g-3">
            <div class="col-auto">
                <i class="bi bi-shield-check fs-3" style="color:var(--qa-brand);"></i>
            </div>
            <div class="col">
                <div class="fw-bold" style="color:var(--qa-brand);">
                    {{ $inspection->inspection_name ?? $inspection->type_inspection }}
                </div>
                <div class="text-muted small">
                    @if ($inspection->inspector)
                        <i class="bi bi-person me-1"></i>{{ $inspection->inspector->prenom }} {{ $inspection->inspector->nom }}
                        &nbsp;|&nbsp;
                    @endif
                    @if ($inspection->date_scheduled)
                        <i class="bi bi-calendar3 me-1"></i>Programmée le {{ \Carbon\Carbon::parse($inspection->date_scheduled)->format('d/m/Y') }}
                    @endif
                    @if ($inspection->project)
                        &nbsp;|&nbsp;<i class="bi bi-folder2 me-1"></i>{{ $inspection->project->project_code }}
                    @endif
                </div>
            </div>
            <div class="col-auto">
                @isset($progress)
                    {{-- Facility Inspection: show progress --}}
                    @if ($progress >= $total)
                        <span class="badge-filled" style="font-size:.8rem; padding:.35rem .9rem;">
                            <i class="bi bi-check-circle-fill me-1"></i>{{ $progress }}/{{ $total }} sections complétées
                        </span>
                    @else
                        <span class="badge-todo" style="font-size:.8rem; padding:.35rem .9rem;">
                            <i class="bi bi-hourglass-split me-1"></i>{{ $progress }}/{{ $total }} sections complétées
                        </span>
                    @endif
                @else
                    @php $filledSlug = collect($statuses)->search(true); @endphp
                    @if ($filledSlug)
                        <span class="badge-filled" style="font-size:.8rem; padding:.35rem .9rem;">
                            <i class="bi bi-check-circle-fill me-1"></i>Checklist rempli
                        </span>
                        <div class="text-muted small mt-1">{{ $forms[$filledSlug]['letter'] }}. {{ $forms[$filledSlug]['title'] }}</div>
                        @if(!$inspection->date_performed)
                            <div class="mt-2">
                                <button id="markDoneBtn" class="btn btn-sm btn-success fw-semibold"
                                        onclick="markInspectionDone({{ $inspection->id }}, this)">
                                    <i class="bi bi-check-circle me-1"></i>Marquer comme finalisée
                                </button>
                            </div>
                        @else
                            <div class="mt-2">
                                <span class="badge bg-success" style="font-size:.8rem;">
                                    <i class="bi bi-check-circle-fill me-1"></i>Finalisée le {{ \Carbon\Carbon::parse($inspection->date_performed)->format('d/m/Y') }}
                                </span>
                            </div>
                        @endif
                    @else
                        <span class="badge-todo" style="font-size:.8rem; padding:.35rem .9rem;">
                            <i class="bi bi-hand-index me-1"></i>Aucun checklist sélectionné
                        </span>
                    @endif
                @endisset
            </div>
        </div>

        {{-- Facility progress bar --}}
        @isset($progress)
            @php $pct = $total > 0 ? round($progress / $total * 100) : 0; @endphp
            <div class="mt-3">
                <div class="d-flex justify-content-between mb-1" style="font-size:.8rem;">
                    <span class="text-muted">Progression du remplissage</span>
                    <span class="fw-semibold" style="color:var(--qa-brand);">{{ $pct }}%</span>
                </div>
                <div class="progress" style="height:10px; border-radius:999px;">
                    <div class="progress-bar {{ $progress >= $total ? 'bg-success' : 'bg-warning' }}"
                         role="progressbar"
                         style="width:{{ $pct }}%"
                         aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="{{ $total }}"></div>
                </div>
            </div>
        @endisset
    </div>

    {{-- Grille meta --}}
    @php
        $findingCounts = $findingCounts ?? [];
        $isCritical    = $inspection->type_inspection === 'Critical Phase Inspection';
        $activeSlug    = $inspection->checklist_slug ?? null;
    @endphp

    {{-- Instruction --}}
    @isset($progress)
        @if ($progress < $total)
            <div class="alert alert-warning d-flex align-items-center gap-2 mb-3 rounded-3" style="font-size:.88rem;">
                <i class="bi bi-exclamation-triangle-fill fs-5 flex-shrink-0"></i>
                <span>Remplissez <strong>toutes les {{ $total }} sections</strong> pour pouvoir finaliser l'inspection Facility. Il reste <strong>{{ $total - $progress }}</strong> section(s) à compléter.</span>
            </div>
        @else
            <div class="alert alert-success d-flex align-items-center gap-2 mb-3 rounded-3" style="font-size:.88rem;">
                <i class="bi bi-check-circle-fill fs-5 flex-shrink-0"></i>
                <span>Toutes les sections sont complétées. Vous pouvez maintenant <strong>finaliser l'inspection</strong>.</span>
            </div>
        @endif
    @else
        @if($isCritical && $activeSlug)
            <div class="alert alert-primary d-flex align-items-center gap-2 mb-3 rounded-3" style="font-size:.88rem;">
                <i class="bi bi-pin-angle-fill fs-5 flex-shrink-0"></i>
                <span>Seul le formulaire <strong>{{ $forms[$activeSlug]['letter'] }}. {{ $forms[$activeSlug]['title'] }}</strong> est actif pour cette inspection. Les autres sont désactivés.</span>
            </div>
        @else
            <div class="alert alert-info d-flex align-items-center gap-2 mb-3 rounded-3" style="font-size:.88rem;">
                <i class="bi bi-info-circle-fill fs-5 flex-shrink-0"></i>
                <span>Sélectionnez <strong>un seul formulaire</strong> correspondant au type d'inspection réalisée, puis remplissez-le.</span>
            </div>
        @endif
    @endisset

    {{-- Grille des formulaires --}}
    <div class="row g-3">
        @foreach ($forms as $slug => $form)
            @php
                $done      = $statuses[$slug];
                $nFindings = $findingCounts[$slug] ?? 0;
                $isLocked  = $isCritical && $activeSlug && $slug !== $activeSlug;
            @endphp
            <div class="col-md-6 col-lg-4">
                <div class="checklist-card p-3 d-flex flex-column gap-2
                    {{ $done ? 'border-success' : '' }}
                    {{ $isLocked ? 'opacity-50' : '' }}"
                    style="{{ $done ? 'border-color:#198754 !important; border-width:2px !important;' : '' }}
                           {{ $isLocked ? 'background:#f3f3f3 !important; cursor:not-allowed;' : '' }}">
                    <div class="d-flex align-items-start gap-3">
                        <div class="card-letter" style="{{ $isLocked ? 'background:#adb5bd;' : '' }}">
                            {{ $form['letter'] }}
                        </div>
                        <div class="flex-grow-1">
                            <div class="card-title-text">{{ $form['title'] }}</div>
                            <div class="card-qcount">{{ count($form['questions']) }} questions</div>
                        </div>
                        <div class="d-flex flex-column align-items-end gap-1">
                            @if ($isLocked)
                                <span class="badge bg-secondary" style="font-size:.72rem;">
                                    <i class="bi bi-lock me-1"></i>Non sélectionné
                                </span>
                            @elseif ($done)
                                <span class="badge-filled">
                                    <i class="bi bi-check-lg me-1"></i>Rempli
                                </span>
                            @else
                                <span class="badge-todo">
                                    <i class="bi bi-clock me-1"></i>À compléter
                                </span>
                            @endif
                            @if (!$isLocked && $nFindings > 0)
                                <span class="badge rounded-pill bg-danger" style="font-size:.72rem;">
                                    <i class="bi bi-exclamation-triangle me-1"></i>{{ $nFindings }} finding{{ $nFindings > 1 ? 's' : '' }}
                                </span>
                            @elseif(!$isLocked && $done)
                                <span style="font-size:.72rem; color:#198754;">
                                    <i class="bi bi-check-circle me-1"></i>Conforme
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="mt-auto d-flex justify-content-end gap-2">
                        @if ($isLocked)
                            <span class="btn btn-sm btn-secondary disabled" style="font-size:.82rem; cursor:not-allowed;">
                                <i class="bi bi-lock me-1"></i>Désactivé
                            </span>
                        @elseif(isset($progress) && $done)
                            {{-- Section already filled: propose Modifier + Ajouter un finding --}}
                            <a href="{{ route('checklist.show', [$inspection->id, $slug]) }}#addFindingForm"
                               class="btn btn-sm"
                               style="background:#198754; color:#fff; border:none; border-radius:8px; font-size:.82rem; font-weight:600; padding:.35rem .9rem;">
                                <i class="bi bi-plus-circle me-1"></i>Ajouter un finding
                            </a>
                            <a href="{{ route('checklist.show', [$inspection->id, $slug]) }}"
                               class="btn btn-open btn-sm">
                                <i class="bi bi-pencil-square me-1"></i>Modifier
                            </a>
                        @else
                            <a href="{{ route('checklist.show', [$inspection->id, $slug]) }}"
                               class="btn btn-open btn-sm">
                                <i class="bi bi-pencil-square me-1"></i>
                                @if(isset($progress))Remplir le formulaire @else {{ $done ? 'Modifier' : 'Remplir' }} @endif
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function markInspectionDone(inspectionId, btn) {
    if (!confirm('Marquer cette inspection comme finalisée ?')) return;
    btn.disabled = true;
    fetch('{{ route("markInspectionDone") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ inspection_id: inspectionId }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) { location.reload(); }
        else { btn.disabled = false; alert(data.message || 'Erreur.'); }
    })
    .catch(() => { btn.disabled = false; alert('Erreur réseau.'); });
}
</script>
</body>
</html>

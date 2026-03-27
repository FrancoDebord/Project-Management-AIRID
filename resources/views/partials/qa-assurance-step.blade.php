@php
    $project_id           = request('project_id');
    $project              = App\Models\Pro_Project::find($project_id);
    $all_phases_critiques = $project ? $project->allPhasesCritiques : collect();
    $all_personnels       = App\Models\Pro_Personnel::orderBy('prenom', 'asc')->get();
    $qa_inspections       = App\Models\Pro_QaInspection::where('project_id', $project_id)
                                ->with(['inspector', 'activity'])
                                ->withCount('findings')
                                ->orderByRaw('COALESCE(date_start, date_scheduled) ASC')
                                ->get();
    $all_project_findings = App\Models\Pro_QaInspectionFinding::whereIn(
                                'inspection_id', $qa_inspections->pluck('id')
                            )
                            ->with(['assignedTo', 'inspection', 'inspection.inspector'])
                            ->orderBy('inspection_id')
                            ->orderBy('created_at')
                            ->get();
    // Map activity_id → inspection (first inspection found for each activity)
    $activityInspectionMap = $qa_inspections->whereNotNull('activity_id')->keyBy('activity_id');
    // Activités critiques exécutées sans inspection programmée
    $activitesSansInspection = $all_phases_critiques->filter(
        fn($a) => $a->status === 'completed' && !isset($activityInspectionMap[$a->id])
    );
    // QA Manager par défaut pour l'inspecteur
    $qaManagerDefaultId = \DB::table('pro_key_facility_personnels')
                            ->where('staff_role', 'Quality Assurance')
                            ->where('active', 1)
                            ->value('personnel_id');
@endphp

<style>
    :root {
        --qa-brand: #C10202;
        --qa-brand-dark: #8b0001;
        --qa-black: #010101;
        --qa-gray: #706D6B;
        --qa-border: #E5E5E5;
    }

    /* ── Cards ── */
    .airid-qa .card {
        border-radius: 16px;
        border: 1px solid var(--qa-border);
        box-shadow: 0 8px 20px rgba(0,0,0,.04);
    }
    .airid-qa .card-header {
        background: linear-gradient(90deg, var(--qa-brand), var(--qa-brand-dark));
        color: #fff;
        font-weight: 600;
        letter-spacing: .02em;
        border-bottom: none;
    }

    /* ── Table headers ── */
    .airid-qa .table thead th {
        background: linear-gradient(90deg, var(--qa-brand), var(--qa-brand-dark));
        color: #fff;
        border: none;
        font-weight: 600;
    }
    .airid-qa .table tbody tr { vertical-align: middle; }

    /* ── Buttons ── */
    .airid-qa .btn-qa-primary {
        background: linear-gradient(90deg, var(--qa-brand), var(--qa-brand-dark));
        color: #fff; border: none; font-weight: 600;
        border-radius: 8px; transition: opacity .2s;
    }
    .airid-qa .btn-qa-primary:hover { opacity: .88; color: #fff; }

    .airid-qa .btn-qa-inspect {
        background: linear-gradient(135deg, #6f42c1, #4e2d8e);
        color: #fff; border: none;
        border-radius: 6px; font-size: .8rem; font-weight: 600; transition: opacity .2s;
    }
    .airid-qa .btn-qa-inspect:hover { opacity: .85; color: #fff; }

    .airid-qa .btn-qa-schedule {
        background: linear-gradient(135deg, #0f7a4a, #085e38);
        color: #fff; border: none;
        border-radius: 6px; font-size: .8rem; font-weight: 600; transition: opacity .2s;
    }
    .airid-qa .btn-qa-schedule:hover { opacity: .85; color: #fff; }

    /* ── Checklist Picker Modal cards ── */
    .picker-card {
        border-radius: 14px;
        border: 1px solid #e0e0e0;
        box-shadow: 0 3px 10px rgba(0,0,0,.05);
        background: #fff;
        transition: box-shadow .2s, transform .15s;
        height: 100%;
    }
    .picker-card:hover { box-shadow: 0 8px 22px rgba(0,0,0,.1); transform: translateY(-2px); }
    .picker-letter {
        width: 42px; height: 42px;
        background: linear-gradient(135deg, #C10202, #8b0001);
        color: #fff; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.05rem; font-weight: 700; flex-shrink: 0;
    }
    .picker-title { font-size: .87rem; font-weight: 600; color: #1a1a1a; line-height: 1.3; }
    .picker-qcount { font-size: .72rem; color: #999; }
    .picker-card:hover .picker-title { color: #C10202; }
    .modal-header-checklist {
        background: linear-gradient(90deg, #C10202, #8b0001);
        color: #fff; border-bottom: none;
    }
    .modal-header-checklist .btn-close { filter: brightness(0) invert(1); }
    @keyframes spin { to { transform: rotate(360deg); } }
    .spin { display: inline-block; animation: spin .8s linear infinite; }

    .airid-qa .btn-qa-findings {
        background-color: #0d6efd; color: #fff; border: none;
        border-radius: 6px; font-size: .8rem; font-weight: 600; transition: opacity .2s;
    }
    .airid-qa .btn-qa-findings:hover { opacity: .85; color: #fff; }

    .airid-qa .btn-qa-delete {
        background-color: transparent; color: #dc3545;
        border: 1px solid #dc3545; border-radius: 6px;
        font-size: .8rem; font-weight: 600; transition: all .2s;
        padding: .25rem .5rem;
    }
    .airid-qa .btn-qa-delete:hover { background-color: #dc3545; color: #fff; }

    /* ── Modal headers ── */
    .modal-header-qa {
        background: linear-gradient(90deg, var(--qa-brand), var(--qa-brand-dark));
        color: #fff; border-bottom: none;
    }
    .modal-header-qa .btn-close { filter: brightness(0) invert(1); }

    .modal-header-findings {
        background: linear-gradient(135deg, #0d6efd, #0950c5);
        color: #fff; border-bottom: none;
    }
    .modal-header-findings .btn-close { filter: brightness(0) invert(1); }

    /* ── Btn save ── */
    .btn-qa-save {
        background: linear-gradient(90deg, var(--qa-brand), var(--qa-brand-dark));
        border: none; font-weight: 600; color: #fff; border-radius: 8px; min-width: 220px;
    }
    .btn-qa-save:hover { opacity: .88; color: #fff; }

    /* ── Activity banner in modal ── */
    .qa-activity-banner {
        background: linear-gradient(135deg, #fff0f0, #ffe5e5);
        border-left: 4px solid var(--qa-brand);
        border-radius: 10px;
        padding: 14px 18px;
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .qa-activity-banner .banner-icon {
        font-size: 2rem;
        color: var(--qa-brand);
        flex-shrink: 0;
    }
    .qa-activity-banner .banner-label {
        font-size: .72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--qa-brand);
        margin-bottom: 3px;
    }
    .qa-activity-banner .banner-text {
        font-size: 1.05rem;
        font-weight: 700;
        color: #1a0000;
        line-height: 1.3;
    }

    /* ── Badges ── */
    .airid-qa .badge-scheduled {
        background-color: #0d6efd; color: #fff;
        border-radius: 999px; padding: .25rem .6rem; font-weight: 600;
    }
    .airid-qa .badge-done {
        background-color: #198754; color: #fff;
        border-radius: 999px; padding: .25rem .6rem; font-weight: 600;
    }

    /* ── Finding cards ── */
    .finding-card {
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        padding: 14px 16px;
        margin-bottom: 10px;
        background: #fafafa;
        transition: box-shadow .2s;
    }
    .finding-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,.08); }
    .finding-card.resolved {
        border-left: 4px solid #198754;
        background: #f0fff4;
    }
    .finding-card.pending-finding {
        border-left: 4px solid #fd7e14;
        background: #fffdf5;
    }
    .finding-text-content {
        font-size: .92rem; color: #222;
        white-space: pre-wrap; word-break: break-word;
    }
    .finding-action-point {
        background: #e8f5e9; border-radius: 6px;
        padding: 8px 12px; font-size: .85rem; color: #1a5c2a; margin-top: 8px;
    }
    .resolve-form {
        margin-top: 10px; background: #fff8e1;
        border-radius: 8px; padding: 12px; display: none;
    }
    .finding-parent-ref {
        font-size: .75rem; color: #888; margin-bottom: 4px; font-style: italic;
    }
    .findings-empty {
        text-align: center; padding: 30px 20px; color: #aaa;
    }
    .findings-empty i { font-size: 2rem; display: block; margin-bottom: 8px; }
</style>

<div class="row airid-qa">

    {{-- ── ALERTE : activités critiques sans inspection ── --}}
    @if($activitesSansInspection->isNotEmpty())
    <div class="col-12 mb-3">
        <div class="alert alert-warning border-warning d-flex align-items-start gap-2 rounded-3 mb-0"
             style="border-left: 5px solid #f0ad4e !important;">
            <i class="bi bi-exclamation-triangle-fill fs-5 mt-1 text-warning flex-shrink-0"></i>
            <div>
                <strong>Inspection(s) QA requise(s) :</strong>
                les activités critiques suivantes ont été exécutées mais n'ont pas encore d'inspection programmée :
                <ul class="mb-0 mt-1">
                    @foreach($activitesSansInspection as $act)
                    <li>
                        <strong>{{ $act->study_activity_name }}</strong>
                        @if($act->actual_activity_date)
                            <span class="text-muted small">
                                — exécutée le {{ \Carbon\Carbon::parse($act->actual_activity_date)->format('d/m/Y') }}
                            </span>
                        @endif
                        &nbsp;
                        <button class="btn btn-warning btn-sm py-0 px-2"
                                style="font-size:.78rem;"
                                onclick="openQaInspectionModal(
                                    {{ $act->id }},
                                    '{{ addslashes($act->study_activity_name) }}',
                                    'Critical Phase Inspection'
                                )">
                            <i class="bi bi-calendar-plus me-1"></i>Programmer
                        </button>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    {{-- ── HEADER ── --}}
    <div class="col-12 mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h5 class="mb-0 fw-bold" style="color: var(--qa-brand);">
                <i class="bi bi-shield-check me-2"></i>Quality Assurance — Inspections
            </h5>
            <small class="text-muted">
                Programmez des inspections QA basées sur les phases critiques —
                <strong>{{ $project ? $project->project_code : '' }}</strong>
            </small>
        </div>
        <button class="btn btn-qa-primary px-4 py-2"
                onclick="openQaInspectionModal('', '', 'Facility Inspection')">
            <i class="bi bi-plus-circle me-1"></i> Programmer une inspection
        </button>
    </div>

    {{-- ── BLOC A : Phases critiques ── --}}
    <div class="col-12 mb-4">
        <div class="card rounded-4">
            <div class="card-header rounded-top-4">
                <i class="bi bi-flag-fill me-2"></i>
                Phases critiques identifiées
                <span class="badge bg-white text-danger ms-2">{{ $all_phases_critiques->count() }}</span>
            </div>
            <div class="card-body p-0">
                @if ($all_phases_critiques->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-info-circle fs-4 d-block mb-1"></i>
                        Aucune activité critique n'a été définie dans la Planning Phase.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width:45%">Activité</th>
                                    <th>Date début prévue</th>
                                    <th>Statut</th>
                                    <th class="text-center">Phase critique</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($all_phases_critiques as $activite)
                                    <tr>
                                        <td class="fw-semibold">{{ $activite->study_activity_name }}</td>
                                        <td>
                                            @if ($activite->estimated_activity_date)
                                                {{ \Carbon\Carbon::parse($activite->estimated_activity_date)->format('d/m/Y') }}
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($activite->status === 'completed')
                                                <span class="badge bg-success">Completed</span>
                                            @elseif ($activite->status === 'in_progress')
                                                <span class="badge bg-warning text-dark">In Progress</span>
                                            @elseif ($activite->status === 'delayed')
                                                <span class="badge bg-danger">Delayed</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($activite->status) }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge" style="background-color:#C10202; color:#fff;">
                                                <i class="bi bi-flag-fill me-1"></i>Critique
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @php $linkedInspection = $activityInspectionMap[$activite->id] ?? null; @endphp
                                            @if ($linkedInspection)
                                                <div class="d-flex align-items-center justify-content-center gap-1 flex-wrap">
                                                    @if ($activite->status === 'completed')
                                                        @if ($linkedInspection->checklist_slug)
                                                            <a href="{{ route('checklist.show', [$linkedInspection->id, $linkedInspection->checklist_slug]) }}"
                                                               class="btn btn-qa-inspect btn-sm">
                                                                <i class="bi bi-clipboard2-check me-1"></i>Inspecter
                                                            </a>
                                                        @else
                                                            <button class="btn btn-qa-inspect btn-sm"
                                                                    onclick="openChecklistModal(
                                                                        {{ $linkedInspection->id }},
                                                                        '{{ addslashes($linkedInspection->inspection_name ?? $linkedInspection->type_inspection) }}'
                                                                    )">
                                                                <i class="bi bi-clipboard2-check me-1"></i>Inspecter
                                                            </button>
                                                        @endif
                                                    @else
                                                        <span class="text-warning small fst-italic">
                                                            <i class="bi bi-hourglass-split me-1"></i>En attente de l'exécution de l'activité
                                                        </span>
                                                    @endif
                                                    @if (!$linkedInspection->date_performed)
                                                        <button class="btn btn-outline-secondary btn-sm"
                                                                onclick="openEditInspectionModal(
                                                                    {{ $linkedInspection->id }},
                                                                    '{{ addslashes($linkedInspection->inspection_name ?? '') }}',
                                                                    '{{ $linkedInspection->date_start ?? $linkedInspection->date_scheduled ?? '' }}',
                                                                    '{{ $linkedInspection->date_end ?? '' }}',
                                                                    '{{ $linkedInspection->date_report_fm ?? '' }}',
                                                                    '{{ $linkedInspection->date_report_sd ?? '' }}',
                                                                    {{ $linkedInspection->qa_inspector_id ?? 'null' }},
                                                                    '{{ $linkedInspection->checklist_slug ?? '' }}',
                                                                    '{{ $linkedInspection->type_inspection }}'
                                                                )"
                                                                title="Modifier l'inspection">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            @elseif ($activite->status === 'completed')
                                                <button class="btn btn-qa-schedule btn-sm"
                                                        onclick="openQaInspectionModal(
                                                            {{ $activite->id }},
                                                            '{{ addslashes($activite->study_activity_name) }}',
                                                            'Critical Phase Inspection'
                                                        )">
                                                    <i class="bi bi-calendar-plus me-1"></i>Programmer Inspection
                                                </button>
                                            @else
                                                <span class="text-warning small fst-italic">
                                                    <i class="bi bi-hourglass-split me-1"></i>En attente de l'exécution de l'activité
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── BLOC B : Inspections programmées ── --}}
    <div class="col-12 mb-4">
        <div class="card rounded-4">
            <div class="card-header rounded-top-4">
                <i class="bi bi-calendar-check me-2"></i>
                Inspections programmées
                <span class="badge bg-white text-danger ms-2">{{ $qa_inspections->count() }}</span>
            </div>
            <div class="card-body p-0">
                @if ($qa_inspections->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-calendar-x fs-4 d-block mb-1"></i>
                        Aucune inspection n'a encore été programmée pour ce projet.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Type d'inspection</th>
                                    <th>Inspecteur QA</th>
                                    <th>Date programmée</th>
                                    <th>Date réalisée</th>
                                    <th class="text-center">Statut</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($qa_inspections as $i => $inspection)
                                    <tr>
                                        <td class="text-muted small">{{ $i + 1 }}</td>
                                        <td>
                                            @php
                                                $typeColor = match($inspection->type_inspection) {
                                                    'Critical Phase Inspection'  => '#6f42c1',
                                                    'Study Inspection'           => '#0d6efd',
                                                    'Study Protocol Inspection'  => '#0d9488',
                                                    'Process Inspection'         => '#fd7e14',
                                                    default                      => '#6c757d',
                                                };
                                            @endphp
                                            @if ($inspection->inspection_name)
                                                <div class="fw-semibold" style="color:{{ $typeColor }}; font-size:.88rem;">
                                                    {{ $inspection->inspection_name }}
                                                </div>
                                            @else
                                                <span class="badge"
                                                      style="background-color:{{ $typeColor }}; color:#fff;
                                                             font-size:.8rem; padding:.3rem .6rem; border-radius:999px;">
                                                    {{ $inspection->type_inspection }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($inspection->inspector)
                                                <i class="bi bi-person-circle me-1 text-muted"></i>
                                                {{ $inspection->inspector->prenom }} {{ $inspection->inspector->nom }}
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($inspection->date_scheduled)
                                                <i class="bi bi-calendar3 me-1 text-muted"></i>
                                                {{ \Carbon\Carbon::parse($inspection->date_scheduled)->format('d/m/Y') }}
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($inspection->date_performed)
                                                <strong class="text-success">
                                                    <i class="bi bi-check-circle me-1"></i>
                                                    {{ \Carbon\Carbon::parse($inspection->date_performed)->format('d/m/Y') }}
                                                </strong>
                                            @else
                                                <span class="text-muted fst-italic">Non réalisée</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($inspection->completed_at)
                                                <span class="badge bg-success"><i class="bi bi-patch-check-fill me-1"></i>Terminée</span>
                                            @elseif ($inspection->date_performed)
                                                <span class="badge-done"><i class="bi bi-check-lg me-1"></i>Réalisée</span>
                                            @else
                                                <span class="badge-scheduled"><i class="bi bi-clock me-1"></i>Programmée</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center justify-content-center gap-1 flex-wrap">
                                                @php
                                                    $isMultiSection = in_array($inspection->type_inspection, [
                                                        'Facility Inspection',
                                                        'Process Inspection',
                                                        'Study Protocol Inspection',
                                                        'Study Report Inspection',
                                                        'Data Quality Inspection',
                                                    ]);
                                                @endphp
                                                @if ($inspection->date_performed)
                                                    @if ($isMultiSection)
                                                    <a href="{{ route('checklist.index', $inspection->id) }}"
                                                       class="btn btn-qa-findings btn-sm">
                                                        <i class="bi bi-journal-text me-1"></i>Findings
                                                        @if ($inspection->findings_count > 0)
                                                            <span class="badge bg-white text-primary ms-1">{{ $inspection->findings_count }}</span>
                                                        @endif
                                                    </a>
                                                    @else
                                                    <button class="btn btn-qa-findings btn-sm"
                                                            onclick="openFindingsModal(
                                                                {{ $inspection->id }},
                                                                '{{ addslashes($inspection->inspection_name ?? $inspection->type_inspection) }}',
                                                                '{{ $inspection->date_scheduled ? \Carbon\Carbon::parse($inspection->date_scheduled)->format("d/m/Y") : "" }}',
                                                                {{ $inspection->completed_at ? 'true' : 'false' }}
                                                            )">
                                                        <i class="bi bi-journal-text me-1"></i>Findings
                                                        @if ($inspection->findings_count > 0)
                                                            <span class="badge bg-white text-primary ms-1">{{ $inspection->findings_count }}</span>
                                                        @endif
                                                    </button>
                                                    @endif
                                                @else
                                                <button class="btn btn-secondary btn-sm" disabled
                                                        title="Remplissez d'abord le checklist d'inspection">
                                                    <i class="bi bi-lock me-1"></i>Findings
                                                </button>
                                                @endif
                                                @php
                                                    $activityNotExecuted = $inspection->activity_id
                                                        && $inspection->activity
                                                        && $inspection->activity->status !== 'completed';
                                                @endphp
                                                @if ($inspection->completed_at)
                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1"
                                                          title="Inspection terminée — formulaire verrouillé">
                                                        <i class="bi bi-lock-fill me-1"></i>Terminée
                                                    </span>
                                                @elseif ($activityNotExecuted && !$inspection->date_performed)
                                                    <span class="text-warning small fst-italic"
                                                          title="L'activité doit être exécutée avant de pouvoir remplir l'inspection">
                                                        <i class="bi bi-hourglass-split me-1"></i>En attente de l'exécution de l'activité
                                                    </span>
                                                @elseif ($inspection->checklist_slug)
                                                    <a href="{{ route('checklist.show', [$inspection->id, $inspection->checklist_slug]) }}"
                                                       class="btn btn-outline-dark btn-sm"
                                                       title="Ouvrir le formulaire d'inspection">
                                                        <i class="bi bi-clipboard2-check"></i>
                                                    </a>
                                                @elseif ($inspection->type_inspection === 'Facility Inspection')
                                                    <button class="btn btn-qa-inspect btn-sm"
                                                            onclick="openChecklistModal(
                                                                {{ $inspection->id }},
                                                                '{{ addslashes($inspection->inspection_name ?? $inspection->type_inspection) }}',
                                                                'Facility Inspection',
                                                                '{{ $inspection->facility_location ?? 'cotonou' }}'
                                                            )"
                                                            title="Remplir le Facility Inspection Checklist">
                                                        <i class="bi bi-building-check me-1"></i>Inspecter
                                                    </button>
                                                @else
                                                    <a href="{{ route('checklist.index', $inspection->id) }}"
                                                       class="btn btn-outline-dark btn-sm"
                                                       title="Checklists d'inspection">
                                                        <i class="bi bi-clipboard2-check"></i>
                                                    </a>
                                                @endif
                                                @if ($inspection->date_performed)
                                                <a href="{{ route('checklist.report', $inspection->id) }}"
                                                   target="_blank"
                                                   class="btn btn-outline-secondary btn-sm"
                                                   title="QA Unit Report">
                                                    <i class="bi bi-file-earmark-text"></i>
                                                </a>
                                                <a href="{{ route('checklist.followup', $inspection->id) }}"
                                                   target="_blank"
                                                   class="btn btn-outline-primary btn-sm"
                                                   title="QA Findings Response (Follow-Up)">
                                                    <i class="bi bi-file-earmark-ruled"></i>
                                                </a>
                                                @endif
                                                @if (!$inspection->date_performed)
                                                <button class="btn btn-outline-secondary btn-sm"
                                                        onclick="openEditInspectionModal(
                                                            {{ $inspection->id }},
                                                            '{{ addslashes($inspection->inspection_name ?? '') }}',
                                                            '{{ $inspection->date_start ?? $inspection->date_scheduled ?? '' }}',
                                                            '{{ $inspection->date_end ?? '' }}',
                                                            '{{ $inspection->date_report_fm ?? '' }}',
                                                            '{{ $inspection->date_report_sd ?? '' }}',
                                                            {{ $inspection->qa_inspector_id ?? 'null' }},
                                                            '{{ $inspection->checklist_slug ?? '' }}',
                                                            '{{ $inspection->type_inspection }}'
                                                        )"
                                                        title="Modifier l'inspection">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                @endif
                                                {{-- Mark as completed / Reopen --}}
                                                @if($inspection->completed_at)
                                                <button class="btn btn-outline-warning btn-sm"
                                                        onclick="toggleInspectionComplete({{ $inspection->id }}, this)"
                                                        title="Rouvrir cette inspection">
                                                    <i class="bi bi-arrow-counterclockwise me-1"></i>Rouvrir
                                                </button>
                                                @elseif($inspection->date_performed && $inspection->findings_count > 0)
                                                <button class="btn btn-outline-success btn-sm"
                                                        onclick="toggleInspectionComplete({{ $inspection->id }}, this)"
                                                        title="Marquer l'inspection comme terminée">
                                                    <i class="bi bi-check-all me-1"></i>Terminer
                                                </button>
                                                @endif
                                                @if (!$inspection->completed_at)
                                                <button class="btn btn-qa-delete btn-sm"
                                                        onclick="deleteQaInspection(
                                                            {{ $inspection->id }},
                                                            '{{ addslashes($inspection->inspection_name ?? $inspection->type_inspection) }}'
                                                        )"
                                                        title="Supprimer cette inspection">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                                @endif
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
    </div>

    {{-- ── BLOC C : Tableau général des findings avec filtre + impression ── --}}
    <div class="col-12 mb-4">
        <div class="card rounded-4">
            <div class="card-header rounded-top-4">
                <i class="bi bi-clipboard2-check me-2"></i>
                Tableau des findings
                <span class="badge bg-white text-danger ms-2">{{ $all_project_findings->count() }}</span>
            </div>
            <div class="card-body">

                {{-- Filtre + boutons impression --}}
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <label class="form-label mb-0 fw-semibold small">
                            <i class="bi bi-funnel me-1"></i>Filtrer par inspection :
                        </label>
                        <select class="form-select form-select-sm" id="filterByInspection"
                                onchange="filterFindings()" style="min-width:260px;">
                            <option value="">— Toutes les inspections —</option>
                            @foreach ($qa_inspections as $insp)
                                <option value="{{ $insp->id }}">
                                    {{ $insp->inspection_name ?? $insp->type_inspection }}
                                    ({{ $insp->date_scheduled
                                        ? \Carbon\Carbon::parse($insp->date_scheduled)->format('d/m/Y')
                                        : '—' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-outline-secondary btn-sm rounded-3"
                                onclick="printFindings(false)">
                            <i class="bi bi-printer me-1"></i>Imprimer sans Corrective Actions
                        </button>
                        <button class="btn btn-outline-success btn-sm rounded-3"
                                onclick="printFindings(true)">
                            <i class="bi bi-printer-fill me-1"></i>Imprimer avec Corrective Actions
                        </button>
                    </div>
                </div>

                @if ($all_project_findings->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-journal-x fs-4 d-block mb-1"></i>
                        Aucun finding enregistré pour ce projet.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="findingsGlobalTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Inspection</th>
                                    <th style="width:8%">Type</th>
                                    <th style="width:28%">Observation / Finding</th>
                                    <th>Adressé à</th>
                                    <th>Deadline</th>
                                    <th>Corrective Action</th>
                                    <th class="text-center">Statut</th>
                                    <th class="text-center">Suppr.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($all_project_findings as $fi)
                                    @php
                                        $inspColor = match($fi->inspection?->type_inspection) {
                                            'Critical Phase Inspection'  => '#6f42c1',
                                            'Study Inspection'           => '#0d6efd',
                                            'Study Protocol Inspection'  => '#0d9488',
                                            'Process Inspection'         => '#fd7e14',
                                            default                      => '#6c757d',
                                        };
                                    @endphp
                                    <tr class="finding-row" data-inspection-id="{{ $fi->inspection_id }}">
                                        <td class="text-muted small">{{ $loop->iteration }}</td>
                                        <td>
                                            {{-- Type conformité --}}
                                            @if($fi->is_conformity)
                                                <span class="badge bg-success rounded-pill" style="font-size:.72rem;">
                                                    <i class="bi bi-check-circle me-1"></i>Conf.
                                                </span>
                                            @else
                                                <span class="badge bg-danger rounded-pill" style="font-size:.72rem;">
                                                    <i class="bi bi-x-circle me-1"></i>Non-conf.
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge mb-1 d-block text-center"
                                                  style="background-color:{{ $inspColor }}; color:#fff;
                                                         font-size:.75rem; padding:.3rem .5rem; border-radius:6px;
                                                         white-space:normal; line-height:1.3;">
                                                {{ $fi->inspection?->inspection_name ?? $fi->inspection?->type_inspection ?? '—' }}
                                            </span>
                                            @if ($fi->inspection?->date_scheduled)
                                                <small class="text-muted">
                                                    {{ \Carbon\Carbon::parse($fi->inspection->date_scheduled)->format('d/m/Y') }}
                                                </small>
                                            @endif
                                            @if ($fi->inspection?->inspector)
                                                <br><small class="text-muted">
                                                    <i class="bi bi-person me-1"></i>{{ $fi->inspection->inspector->prenom }} {{ $fi->inspection->inspector->nom }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <div style="font-size:.88rem; white-space:pre-wrap; word-break:break-word;">{{ $fi->finding_text }}</div>
                                            @if ($fi->parent_finding_id)
                                                <small class="text-muted fst-italic">
                                                    <i class="bi bi-link-45deg me-1"></i>Lié au finding #{{ $fi->parent_finding_id }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($fi->assignedTo)
                                                <i class="bi bi-person-circle me-1 text-muted"></i>
                                                {{ $fi->assignedTo->prenom }} {{ $fi->assignedTo->nom }}
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($fi->deadline_date)
                                                <span class="{{ now()->gt($fi->deadline_date) && $fi->status === 'pending' ? 'text-danger fw-bold' : 'text-muted' }}">
                                                    {{ \Carbon\Carbon::parse($fi->deadline_date)->format('d/m/Y') }}
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($fi->action_point)
                                                <div class="p-2 rounded mb-1"
                                                     style="background:#e8f5e9; font-size:.83rem; color:#1a5c2a; white-space:pre-wrap; word-break:break-word;">
                                                    {{ $fi->action_point }}
                                                    @if($fi->means_of_verification)
                                                        <div class="mt-1" style="font-size:.78rem; color:#2d6a4f;">
                                                            <strong>MoV :</strong> {{ $fi->means_of_verification }}
                                                        </div>
                                                    @endif
                                                </div>
                                                @if(!$fi->inspection?->completed_at)
                                                <div class="d-flex gap-1">
                                                    <button class="btn btn-outline-secondary btn-sm"
                                                            style="font-size:.7rem; padding:.15rem .4rem;"
                                                            onclick="toggleTableEditForm({{ $fi->id }})"
                                                            title="Modifier la résolution">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger btn-sm"
                                                            style="font-size:.7rem; padding:.15rem .4rem;"
                                                            onclick="deleteCorrectiveActionFromTable({{ $fi->id }})"
                                                            title="Supprimer la résolution">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                </div>
                                                @endif
                                            @elseif($fi->is_conformity)
                                                <span class="text-muted fst-italic small">—</span>
                                            @else
                                                <span class="text-muted fst-italic small">En attente</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($fi->status === 'complete')
                                                <span class="badge bg-success rounded-pill">
                                                    <i class="bi bi-check-lg me-1"></i>Résolu
                                                </span>
                                            @elseif($fi->is_conformity)
                                                <span class="badge bg-success rounded-pill">
                                                    <i class="bi bi-check-circle me-1"></i>Conforme
                                                </span>
                                            @else
                                                <span class="badge bg-warning text-dark rounded-pill d-block mb-1">
                                                    <i class="bi bi-hourglass-split me-1"></i>En attente
                                                </span>
                                                @if(!$fi->inspection?->completed_at)
                                                <button class="btn btn-outline-success btn-sm rounded-3"
                                                        style="font-size:.72rem; white-space:nowrap;"
                                                        onclick="toggleTableResolveForm({{ $fi->id }})"
                                                        title="Saisir la corrective action">
                                                    <i class="bi bi-check2-circle me-1"></i>Résoudre
                                                </button>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-outline-danger btn-sm rounded-3"
                                                    onclick="deleteQaFindingFromTable({{ $fi->id }})"
                                                    title="Supprimer ce finding">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    {{-- Ligne d'édition de la résolution (non-conformity complete) --}}
                                    @if(!$fi->is_conformity && $fi->status === 'complete' && !$fi->inspection?->completed_at)
                                    <tr id="table-edit-row-{{ $fi->id }}" style="display:none; background:#fffbeb;">
                                        <td colspan="9" class="p-3">
                                            <div class="row g-2 align-items-end">
                                                <div class="col-md-5">
                                                    <label class="form-label fw-semibold small mb-1">
                                                        Action corrective <span class="text-danger">*</span>
                                                    </label>
                                                    <textarea id="tbl-edit-action-{{ $fi->id }}" rows="2"
                                                              class="form-control form-control-sm">{{ $fi->action_point }}</textarea>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label fw-semibold small mb-1">Moyen de vérification</label>
                                                    <input type="text" id="tbl-edit-mov-{{ $fi->id }}"
                                                           class="form-control form-control-sm"
                                                           value="{{ $fi->means_of_verification ?? '' }}">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label fw-semibold small mb-1">Date <span class="text-danger">*</span></label>
                                                    <input type="date" id="tbl-edit-date-{{ $fi->id }}"
                                                           class="form-control form-control-sm"
                                                           value="{{ $fi->meeting_date ?? now()->toDateString() }}">
                                                </div>
                                                <div class="col-md-2 d-flex gap-1">
                                                    <button class="btn btn-warning btn-sm flex-grow-1"
                                                            onclick="submitTableEdit({{ $fi->id }})">
                                                        <i class="bi bi-floppy me-1"></i>Enregistrer
                                                    </button>
                                                    <button class="btn btn-outline-secondary btn-sm"
                                                            onclick="toggleTableEditForm({{ $fi->id }})">
                                                        <i class="bi bi-x"></i>
                                                    </button>
                                                </div>
                                                <div id="tbl-edit-error-{{ $fi->id }}" class="col-12 text-danger small" style="display:none;"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endif

                                    {{-- Ligne de résolution inline (non-conformity pending) --}}
                                    @if(!$fi->is_conformity && $fi->status !== 'complete')
                                    <tr id="table-resolve-row-{{ $fi->id }}" style="display:none; background:#f0fdf4;">
                                        <td colspan="9" class="p-3">
                                            <div class="row g-2 align-items-end">
                                                <div class="col-md-5">
                                                    <label class="form-label fw-semibold small mb-1">
                                                        Action corrective <span class="text-danger">*</span>
                                                    </label>
                                                    <textarea id="tbl-resolve-action-{{ $fi->id }}" rows="2"
                                                              class="form-control form-control-sm"
                                                              placeholder="Décrivez la mesure corrective…"></textarea>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label fw-semibold small mb-1">Moyen de vérification</label>
                                                    <input type="text" id="tbl-resolve-mov-{{ $fi->id }}"
                                                           class="form-control form-control-sm"
                                                           placeholder="Ex: rapport, photo…">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label fw-semibold small mb-1">
                                                        Date <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="date" id="tbl-resolve-date-{{ $fi->id }}"
                                                           class="form-control form-control-sm"
                                                           value="{{ now()->toDateString() }}">
                                                </div>
                                                <div class="col-md-2 d-flex gap-1">
                                                    <button class="btn btn-success btn-sm flex-grow-1"
                                                            onclick="submitTableResolve({{ $fi->id }})">
                                                        <i class="bi bi-floppy me-1"></i>Valider
                                                    </button>
                                                    <button class="btn btn-outline-secondary btn-sm"
                                                            onclick="toggleTableResolveForm({{ $fi->id }})">
                                                        <i class="bi bi-x"></i>
                                                    </button>
                                                </div>
                                                <div id="tbl-resolve-error-{{ $fi->id }}" class="col-12 text-danger small" style="display:none;"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

            </div>
        </div>
    </div>

</div>{{-- /row airid-qa --}}


{{-- ═══════════════════════════════════════
     MODAL 0 : Checklist Picker (Inspecter)
═══════════════════════════════════════ --}}
<div class="modal fade" id="checklistPickerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 overflow-hidden">

            <div class="modal-header modal-header-checklist py-3 px-4">
                <div>
                    <h5 class="modal-title fw-bold mb-1">
                        <i class="bi bi-clipboard2-check me-2"></i>Formulaires d'inspection
                    </h5>
                    <small id="checklistPickerSubtitle" class="opacity-75 d-block"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">

                <p class="text-muted small mb-3" id="checklistPickerInstruction">
                    <i class="bi bi-info-circle me-1"></i>
                    Sélectionnez le formulaire à remplir pour cette inspection.
                </p>

                {{-- Facility progress bar (hidden by default) --}}
                <div id="facilityProgressArea" style="display:none;" class="mb-4">
                    <div class="d-flex justify-content-between mb-1" style="font-size:.8rem;">
                        <span class="text-muted">Sections complétées</span>
                        <span id="facilityProgressLabel" class="fw-semibold" style="color:#C10202;"></span>
                    </div>
                    <div class="progress mb-3" style="height:10px; border-radius:999px;">
                        <div id="facilityProgressBar" class="progress-bar"
                             role="progressbar" style="width:0%"></div>
                    </div>
                    <div id="facilityProgressAlert" class="alert d-flex align-items-center gap-2 py-2 px-3 mb-0" style="font-size:.82rem;"></div>
                </div>

                {{-- Cards grid --}}
                <div class="row g-3" id="checklistPickerGrid">
                    {{-- Injected by JS --}}
                </div>

            </div>

            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-outline-dark btn-sm rounded-3" data-bs-dismiss="modal">
                    Fermer
                </button>
            </div>

        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════
     MODAL 1 : Programmer une inspection
═══════════════════════════════════════ --}}
<div class="modal fade" id="scheduleQaInspectionModal" tabindex="-1"
     aria-labelledby="qaInspectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 overflow-hidden">

            <div class="modal-header modal-header-qa">
                <h5 class="modal-title fw-bold" id="qaInspectionModalLabel">
                    <i class="bi bi-shield-plus me-2"></i>Programmer une inspection QA
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-4 py-4">

                <input type="hidden" id="qaActivityIdHidden">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="qaTypeInspection" class="form-label fw-semibold">
                            Type d'inspection <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="qaTypeInspection" required>
                            <option value="">— Sélectionner un type —</option>
                            <option value="Critical Phase Inspection">Critical Phase Inspection</option>
                            <option value="Study Inspection">Study Inspection</option>
                            <option value="Study Protocol Inspection">Study Protocol Inspection</option>
                            <option value="Study Report Inspection">Study Report Inspection</option>
                            <option value="Data Quality Inspection">Data Quality Inspection</option>
                            <option value="Study Protocol Amendment/Deviation Inspection">Study Protocol Amendment/Deviation Inspection</option>
                            <option value="Study Report Amendment Inspection">Study Report Amendment Inspection</option>
                            <option value="Process Inspection">Process Inspection</option>
                            <option value="Facility Inspection">Facility Inspection</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="qaDateScheduled" class="form-label fw-semibold">
                            Date programmée <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control" id="qaDateScheduled" required>
                    </div>

                    {{-- Facility location (visible seulement pour Facility Inspection) --}}
                    <div class="col-12" id="qaFacilityLocationWrapper" style="display:none;">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-geo-alt-fill me-1 text-danger"></i>
                            Site d'inspection <span class="text-danger">*</span>
                        </label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="qaFacilityLocation"
                                       id="locCotonou" value="cotonou" checked>
                                <label class="form-check-label" for="locCotonou">
                                    <strong>Cotonou</strong> — Main Facility (QA-PR-1-001A/06)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="qaFacilityLocation"
                                       id="locCove" value="cove">
                                <label class="form-check-label" for="locCove">
                                    <strong>Covè</strong> — Field Site (QA-PR-1-001B/06)
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Dropdown activité critique (visible seulement pour Critical Phase Inspection) --}}
                    <div class="col-12" id="qaActivitySelectWrapper" style="display:none;">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-flag-fill me-1 text-danger"></i>
                            Activité critique concernée <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="qaActivitySelect">
                            <option value="">— Sélectionner l'activité —</option>
                            @foreach ($all_phases_critiques as $act)
                                @if ($act->status === 'completed')
                                    <option value="{{ $act->id }}">{{ $act->study_activity_name }}</option>
                                @endif
                            @endforeach
                        </select>
                        @if ($all_phases_critiques->where('status', 'completed')->isEmpty())
                            <small class="text-muted mt-1 d-block">
                                <i class="bi bi-info-circle me-1"></i>
                                Aucune activité critique exécutée pour ce projet.
                            </small>
                        @endif
                    </div>

                    {{-- Sélecteur de formulaire checklist (visible seulement pour Critical Phase Inspection) --}}
                    <div class="col-12" id="qaChecklistFormWrapper" style="display:none;">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-clipboard2-check me-1 text-danger"></i>
                            Formulaire de checklist à utiliser <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="qaChecklistSlugSelect">
                            <option value="">— Sélectionner le formulaire —</option>
                            <option value="cone-llin">A — Cone Bioassay with LLIN samples</option>
                            <option value="cone-irs-blocks-treatment">B — Cone Bioassay with IRS blocks (Blocks treatment)</option>
                            <option value="cone-irs-blocks-test">C — Cone Bioassay with IRS blocks (Test)</option>
                            <option value="tunnel-test">D — Tunnel Test</option>
                            <option value="llin-washing">E — Evaluation of Whole LLIN – Washing/Cutting</option>
                            <option value="llin-exp-huts">F — Evaluation of Whole LLIN in Experimental huts</option>
                            <option value="irs-treatment">G — IRS Treatment application</option>
                            <option value="irs-trial">H — IRS Trial</option>
                            <option value="cone-irs-walls">I — Cone Bioassay on IRS treated walls</option>
                            <option value="cylinder-bioassay">J — Cylinder Bioassay</option>
                            <option value="cdc-bottle-coating">K — CDC Bottle Bioassay (Coating)</option>
                            <option value="cdc-bottle-test">L — CDC Bottle Bioassay (Test)</option>
                            <option value="spatial-repellents">M — Evaluation of spatial repellents in Experimental huts</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="qaInspectionCustomName" class="form-label fw-semibold">
                            Intitulé de l'inspection
                            <small class="text-muted fw-normal">(optionnel — remplace le nom auto-généré)</small>
                        </label>
                        <input type="text" class="form-control" id="qaInspectionCustomName"
                               placeholder="Ex : Inspection LLIN — Lot B — Semaine 12">
                    </div>

                    <div class="col-12">
                        <label for="qaInspectorSelect" class="form-label fw-semibold">
                            Inspecteur QA <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="qaInspectorSelect" required>
                            <option value="">— Sélectionner un inspecteur —</option>
                            @foreach ($all_personnels as $personnel)
                                <option value="{{ $personnel->id }}">
                                    {{ $personnel->prenom }} {{ $personnel->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Bandeau activité liée ── visible quand une activité est sélectionnée --}}
                <div id="qaLinkedActivityWrapper" class="mt-3" style="display:none;">
                    <div class="qa-activity-banner">
                        <div class="banner-icon"><i class="bi bi-flag-fill"></i></div>
                        <div>
                            <div class="banner-label">Activité critique à inspecter</div>
                            <div class="banner-text" id="qaLinkedActivityName"></div>
                        </div>
                    </div>
                </div>

                <div id="qaInspectionErrorMsg" class="alert alert-danger mt-3 d-none" role="alert"></div>

            </div>

            <div class="modal-footer border-0 px-4 pb-4">
                <button type="button" class="btn btn-outline-secondary rounded-3"
                        data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Annuler
                </button>
                <button type="button" class="btn btn-qa-save rounded-3 px-4" id="btnSaveQaInspection"
                        onclick="saveQaInspection()">
                    <i class="bi bi-calendar-plus me-1"></i>Programmer l'inspection
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════
     MODAL — Modifier une inspection
     ═══════════════════════════════════════ --}}
<div class="modal fade" id="editInspectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square me-2 text-danger"></i>Modifier l'inspection
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-3">
                <input type="hidden" id="editInspectionId">
                <input type="hidden" id="editInspectionType">

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Intitulé de l'inspection</label>
                        <input type="text" class="form-control" id="editInspectionName"
                               placeholder="Ex : Critical Phase — Lot B">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Date début d'inspection <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control" id="editInspectionDate"
                               title="Date de début (aussi utilisée comme date planifiée)">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Date fin d'inspection</label>
                        <input type="date" class="form-control" id="editInspectionDateEnd">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-building me-1 text-primary"></i>Date rapport → FM
                        </label>
                        <input type="date" class="form-control" id="editInspectionDateFm">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-person me-1 text-success"></i>Date rapport → SD
                        </label>
                        <input type="date" class="form-control" id="editInspectionDateSd">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Inspecteur QA</label>
                        <select class="form-select" id="editInspectionInspector">
                            <option value="">— Sélectionner un inspecteur —</option>
                            @foreach ($all_personnels as $personnel)
                                <option value="{{ $personnel->id }}">
                                    {{ $personnel->prenom }} {{ $personnel->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12" id="editChecklistSlugWrapper">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-clipboard2-check me-1 text-danger"></i>
                            Formulaire de checklist à utiliser
                        </label>
                        <select class="form-select" id="editChecklistSlug">
                            <option value="">— Sélectionner le formulaire —</option>
                            <option value="cone-llin">A — Cone Bioassay with LLIN samples</option>
                            <option value="cone-irs-blocks-treatment">B — Cone Bioassay with IRS blocks (Blocks treatment)</option>
                            <option value="cone-irs-blocks-test">C — Cone Bioassay with IRS blocks (Test)</option>
                            <option value="tunnel-test">D — Tunnel Test</option>
                            <option value="llin-washing">E — Evaluation of Whole LLIN – Washing/Cutting</option>
                            <option value="llin-exp-huts">F — Evaluation of Whole LLIN in Experimental huts</option>
                            <option value="irs-treatment">G — IRS Treatment application</option>
                            <option value="irs-trial">H — IRS Trial</option>
                            <option value="cone-irs-walls">I — Cone Bioassay on IRS treated walls</option>
                            <option value="cylinder-bioassay">J — Cylinder Bioassay</option>
                            <option value="cdc-bottle-coating">K — CDC Bottle Bioassay (Coating)</option>
                            <option value="cdc-bottle-test">L — CDC Bottle Bioassay (Test)</option>
                            <option value="spatial-repellents">M — Evaluation of spatial repellents in Experimental huts</option>
                        </select>
                    </div>
                </div>

                <div id="editInspectionErrorMsg" class="alert alert-danger mt-3 d-none" role="alert"></div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4">
                <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Annuler
                </button>
                <button type="button" class="btn btn-qa-save rounded-3 px-4" id="btnSaveEditInspection"
                        onclick="saveEditInspection()">
                    <i class="bi bi-check2 me-1"></i>Enregistrer
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════
     MODAL 2 : Findings d'une inspection
═══════════════════════════════════════ --}}
<div class="modal fade" id="findingsModal" tabindex="-1"
     aria-labelledby="findingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 overflow-hidden">

            <div class="modal-header modal-header-findings">
                <div>
                    <h5 class="modal-title fw-bold mb-0" id="findingsModalLabel">
                        <i class="bi bi-journal-text me-2"></i>Findings de l'inspection
                    </h5>
                    <small id="findingsModalSubtitle" class="opacity-75 d-block mt-1"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">

                {{-- ── Formulaire ajout finding ── --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-light fw-semibold py-2 rounded-top-3">
                        <i class="bi bi-plus-circle-fill me-2 text-primary"></i>Ajouter un finding / observation
                    </div>
                    <div class="card-body">
                        <input type="hidden" id="findingInspectionId">
                        <input type="hidden" id="findingProjectId" value="{{ $project_id }}">
                        <input type="hidden" id="projectStudyDirectorId" value="{{ $project?->study_director ?? '' }}">

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    Observation / Constat <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="findingText" rows="3"
                                          placeholder="Décrivez l'observation ou le constat fait lors de l'inspection…"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Type de constat</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="findingConformity"
                                               id="findingNonConformity" value="0" checked
                                               onchange="toggleFindingCorrectiveFields()">
                                        <label class="form-check-label text-danger fw-semibold" for="findingNonConformity">
                                            <i class="bi bi-x-circle me-1"></i>Non-conformité
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="findingConformity"
                                               id="findingConformity" value="1"
                                               onchange="toggleFindingCorrectiveFields()">
                                        <label class="form-check-label text-success fw-semibold" for="findingConformity">
                                            <i class="bi bi-check-circle me-1"></i>Conformité
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5" id="findingAssignedToWrapper">
                                <label class="form-label fw-semibold">
                                    Adressé à (Study Director / Responsable) <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="findingAssignedTo">
                                    <option value="">— Sélectionner —</option>
                                    @foreach ($all_personnels as $personnel)
                                        <option value="{{ $personnel->id }}">
                                            {{ $personnel->prenom }} {{ $personnel->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4" id="findingDeadlineWrapper">
                                <label class="form-label fw-semibold">Date limite de résolution</label>
                                <input type="date" class="form-control" id="findingDeadline">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Lié à un finding antérieur</label>
                                <select class="form-select" id="findingParentId">
                                    <option value="">— Aucun —</option>
                                </select>
                            </div>
                        </div>

                        <div id="findingErrorMsg" class="alert alert-danger mt-3 d-none"></div>

                        <div class="mt-3 text-end">
                            <button type="button" class="btn btn-primary px-4 rounded-3"
                                    id="btnSaveFinding" onclick="saveFinding()">
                                <i class="bi bi-floppy me-1"></i>Enregistrer le finding
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ── Liste findings existants ── --}}
                <h6 class="fw-bold mb-3" style="color:#0d6efd;">
                    <i class="bi bi-list-check me-2"></i>Findings enregistrés
                </h6>
                <div id="findingsList">
                    <div class="findings-empty">
                        <i class="bi bi-hourglass-split"></i>
                        Chargement des findings…
                    </div>
                </div>

            </div>

            <div class="modal-footer border-0 px-4 pb-4">
                <button type="button" class="btn btn-outline-secondary rounded-3"
                        data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Fermer
                </button>
            </div>
        </div>
    </div>
</div>


{{-- ── Toasts ── --}}
<div aria-live="polite" aria-atomic="true" class="position-relative">
    <div class="toast-container position-fixed top-0 end-0 p-3" id="qaToastContainer"></div>
</div>


<script>
// ─────────────────────────────────────────────
//  UTILS
// ─────────────────────────────────────────────
function showQaToast(message, type) {
    const container = document.getElementById('qaToastContainer');
    if (!container) return;
    const bg = type === 'success' ? 'bg-success' : (type === 'error' ? 'bg-danger' : 'bg-info');
    const el = document.createElement('div');
    el.className = `toast align-items-center text-white ${bg} border-0`;
    el.setAttribute('role', 'alert');
    el.setAttribute('aria-live', 'assertive');
    el.setAttribute('aria-atomic', 'true');
    el.innerHTML = `<div class="d-flex">
        <div class="toast-body">${message}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto"
                data-bs-dismiss="toast" aria-label="Close"></button>
    </div>`;
    container.appendChild(el);
    const bsToast = new bootstrap.Toast(el, { delay: 4000 });
    bsToast.show();
    el.addEventListener('hidden.bs.toast', () => el.remove());
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;')
              .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

const QA_MANAGER_DEFAULT_ID = {{ $qaManagerDefaultId ?? 'null' }};

// ─────────────────────────────────────────────
//  MODAL 1 — Programmer une inspection
// ─────────────────────────────────────────────
function openQaInspectionModal(activityId, activityName, defaultType) {
    document.getElementById('qaTypeInspection').value        = defaultType || '';
    document.getElementById('qaDateScheduled').value         = '';
    document.getElementById('qaInspectorSelect').value       = QA_MANAGER_DEFAULT_ID || '';
    document.getElementById('qaActivityIdHidden').value      = activityId || '';
    document.getElementById('qaChecklistSlugSelect').value   = '';

    const errDiv = document.getElementById('qaInspectionErrorMsg');
    errDiv.classList.add('d-none');
    errDiv.textContent = '';

    // Afficher/masquer les champs spécifiques à Critical Phase / Facility
    const isCritical = defaultType === 'Critical Phase Inspection';
    const isFacility = defaultType === 'Facility Inspection';
    document.getElementById('qaActivitySelectWrapper').style.display    = isCritical ? '' : 'none';
    document.getElementById('qaChecklistFormWrapper').style.display     = isCritical ? '' : 'none';
    document.getElementById('qaFacilityLocationWrapper').style.display  = isFacility ? '' : 'none';

    // Pré-sélectionner l'activité dans le dropdown si fournie
    const actSelect = document.getElementById('qaActivitySelect');
    actSelect.value = activityId ? String(activityId) : '';

    // Bandeau activité
    const banner = document.getElementById('qaLinkedActivityWrapper');
    if (activityId && activityName) {
        document.getElementById('qaLinkedActivityName').textContent = activityName;
        banner.style.display = 'block';
    } else {
        banner.style.display = 'none';
    }

    new bootstrap.Modal(document.getElementById('scheduleQaInspectionModal'), {}).show();
}

function saveQaInspection() {
    const project_id      = "{{ request('project_id') }}";
    const typeInspection  = document.getElementById('qaTypeInspection').value;
    const dateScheduled   = document.getElementById('qaDateScheduled').value;
    const inspectorId     = document.getElementById('qaInspectorSelect').value;
    const activityId      = document.getElementById('qaActivityIdHidden').value;
    const checklistSlug   = document.getElementById('qaChecklistSlugSelect').value;
    const customName      = document.getElementById('qaInspectionCustomName').value.trim();
    const errDiv          = document.getElementById('qaInspectionErrorMsg');
    const btn             = document.getElementById('btnSaveQaInspection');

    errDiv.classList.add('d-none');
    errDiv.textContent = '';

    const isCritical = typeInspection === 'Critical Phase Inspection';

    if (!typeInspection || !dateScheduled || !inspectorId) {
        errDiv.textContent = 'Veuillez remplir tous les champs obligatoires.';
        errDiv.classList.remove('d-none');
        return;
    }
    if (isCritical && !checklistSlug) {
        errDiv.textContent = 'Veuillez sélectionner le formulaire de checklist à utiliser.';
        errDiv.classList.remove('d-none');
        return;
    }

    const fd = new FormData();
    fd.append('project_id',      project_id);
    fd.append('qa_inspector_id', inspectorId);
    fd.append('date_scheduled',  dateScheduled);
    fd.append('type_inspection', typeInspection);
    if (activityId)    fd.append('activity_id',     activityId);
    if (checklistSlug) fd.append('checklist_slug',  checklistSlug);
    if (customName)    fd.append('inspection_name', customName);
    if (typeInspection === 'Facility Inspection') {
        const locEl = document.querySelector('input[name="qaFacilityLocation"]:checked');
        fd.append('facility_location', locEl ? locEl.value : 'cotonou');
    }
    fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enregistrement…';

    fetch('/ajax/schedule-qa-inspection', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-calendar-plus me-1"></i>Programmer l\'inspection';
            if (data.success) {
                showQaToast('Inspection programmée avec succès !', 'success');
                bootstrap.Modal.getInstance(document.getElementById('scheduleQaInspectionModal')).hide();
                setTimeout(() => location.reload(), 900);
            } else {
                errDiv.textContent = data.message || 'Une erreur est survenue.';
                errDiv.classList.remove('d-none');
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-calendar-plus me-1"></i>Programmer l\'inspection';
            showQaToast('Erreur réseau.', 'error');
        });
}

// ─────────────────────────────────────────────
//  MODAL 2 — Findings
// ─────────────────────────────────────────────
function openFindingsModal(inspectionId, inspectionType, inspectionDate, isCompleted = false) {
    document.getElementById('findingInspectionId').value = inspectionId;
    document.getElementById('findingsModalSubtitle').textContent =
        inspectionType + (inspectionDate ? ' — ' + inspectionDate : '');

    // Réinitialiser formulaire
    document.getElementById('findingText').value       = '';
    document.getElementById('findingAssignedTo').value =
        document.getElementById('projectStudyDirectorId').value || '';
    document.getElementById('findingDeadline').value   = '';
    document.getElementById('findingParentId').innerHTML = '<option value="">— Aucun —</option>';
    document.getElementById('findingNonConformity').checked = true;
    toggleFindingCorrectiveFields();

    // Hide "Add Finding" form when inspection is completed
    const addFindingCard = document.querySelector('#findingsModal .card.border-0');
    if (addFindingCard) addFindingCard.style.display = isCompleted ? 'none' : '';

    const errDiv = document.getElementById('findingErrorMsg');
    errDiv.classList.add('d-none');
    errDiv.textContent = '';

    new bootstrap.Modal(document.getElementById('findingsModal'), {}).show();
    loadFindings(inspectionId);
}

function loadFindings(inspectionId) {
    const list = document.getElementById('findingsList');
    list.innerHTML = `<div class="findings-empty">
        <span class="spinner-border spinner-border-sm text-primary me-2"></span>Chargement…
    </div>`;

    fetch(`/ajax/get-inspection-findings?inspection_id=${inspectionId}`)
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                list.innerHTML = `<div class="findings-empty">
                    <i class="bi bi-exclamation-triangle"></i>${data.message}
                </div>`;
                return;
            }

            // Mettre à jour le select "Lié à un finding antérieur"
            const parentSelect = document.getElementById('findingParentId');
            parentSelect.innerHTML = '<option value="">— Aucun —</option>';
            data.findings.forEach(f => {
                const opt = document.createElement('option');
                opt.value = f.id;
                opt.textContent = '#' + f.id + ' — ' + f.finding_text.substring(0, 60)
                    + (f.finding_text.length > 60 ? '…' : '');
                parentSelect.appendChild(opt);
            });

            if (data.findings.length === 0) {
                list.innerHTML = `<div class="findings-empty">
                    <i class="bi bi-journal-x"></i>
                    Aucun finding enregistré pour cette inspection.
                </div>`;
                return;
            }

            list.innerHTML = '';
            data.findings.forEach(f => renderFindingCard(f, list));
        })
        .catch(() => {
            list.innerHTML = `<div class="findings-empty">
                <i class="bi bi-wifi-off"></i>Erreur réseau.
            </div>`;
        });
}

function renderFindingCard(f, container) {
    const isResolved   = f.status === 'complete';
    const isConformity = f.is_conformity === true;
    const div = document.createElement('div');
    div.id = 'finding-card-' + f.id;
    div.className = 'finding-card ' + (isResolved || isConformity ? 'resolved' : 'pending-finding');

    const deadlineHtml = f.deadline_date
        ? `<span class="text-muted small ms-3"><i class="bi bi-calendar-event me-1"></i>Deadline : <strong>${f.deadline_date}</strong></span>` : '';
    const assignedHtml = f.assigned_to_name
        ? `<span class="text-muted small ms-3"><i class="bi bi-person me-1"></i>${escapeHtml(f.assigned_to_name)}</span>` : '';
    const dateHtml = f.created_at
        ? `<span class="text-muted small ms-3"><i class="bi bi-clock me-1"></i>${f.created_at}</span>` : '';
    const parentHtml = f.parent_finding_text
        ? `<div class="finding-parent-ref"><i class="bi bi-link-45deg me-1"></i>Lié au finding : ${escapeHtml(f.parent_finding_text)}</div>` : '';
    const conformityBadge = isConformity
        ? `<span class="badge bg-success rounded-pill"><i class="bi bi-check-circle me-1"></i>Conformité</span>`
        : `<span class="badge bg-danger rounded-pill" style="font-size:.75rem;"><i class="bi bi-x-circle me-1"></i>Non-conformité</span>`;
    const statusBadge = isResolved
        ? `<span class="badge bg-success rounded-pill"><i class="bi bi-check-lg me-1"></i>Résolu</span>`
        : isConformity
            ? `<span class="badge bg-success rounded-pill"><i class="bi bi-check-circle me-1"></i>Conforme</span>`
            : `<span class="badge bg-warning text-dark rounded-pill"><i class="bi bi-hourglass-split me-1"></i>En attente de résolution</span>`;
    const actionPointHtml = isResolved && f.action_point
        ? `<div class="finding-action-point">
               <div class="d-flex justify-content-between align-items-center mb-1">
                   <strong><i class="bi bi-shield-check me-1"></i>Corrective Action :</strong>
                   <button class="btn btn-outline-danger rounded-2"
                           style="font-size:.7rem; padding:.15rem .45rem; line-height:1.2;"
                           onclick="deleteCorrectiveAction(${f.id})"
                           title="Supprimer cette corrective action">
                       <i class="bi bi-x-circle me-1"></i>Supprimer
                   </button>
               </div>
               <div>${escapeHtml(f.action_point)}</div>
               ${f.means_of_verification ? `<div class="mt-1" style="font-size:.82rem;color:#1a5c2a;"><strong>Means of verification :</strong> ${escapeHtml(f.means_of_verification)}</div>` : ''}
           </div>` : '';
    const resolveBtn = (!isResolved && !isConformity)
        ? `<button class="btn btn-sm btn-outline-success mt-2 rounded-3"
                   onclick="toggleResolveForm(${f.id})">
               <i class="bi bi-check2-circle me-1"></i>Résoudre — Saisir la Corrective Action
           </button>
           <div id="resolve-form-${f.id}" class="resolve-form">
               <label class="form-label fw-semibold small mb-1">
                   Corrective Action du Study Director <span class="text-danger">*</span>
               </label>
               <textarea class="form-control form-control-sm mb-2" id="resolve-text-${f.id}"
                         rows="3" placeholder="Décrivez la mesure corrective apportée…"></textarea>
               <label class="form-label fw-semibold small mb-1 mt-1">
                   Means of verification for implementation of corrective actions
               </label>
               <textarea class="form-control form-control-sm mb-2" id="resolve-mov-${f.id}"
                         rows="2" placeholder="Ex: See test item reception logbook…"></textarea>
               <label class="form-label fw-semibold small mb-1 mt-1">
                   Date de résolution <span class="text-danger">*</span>
               </label>
               <input type="date" class="form-control form-control-sm mb-2" id="resolve-date-${f.id}"
                      value="${new Date().toISOString().split('T')[0]}">
               <button class="btn btn-success btn-sm rounded-3 px-3" onclick="submitResolve(${f.id})">
                   <i class="bi bi-check-circle me-1"></i>Valider la résolution
               </button>
               <button class="btn btn-link btn-sm text-muted" onclick="toggleResolveForm(${f.id})">Annuler</button>
           </div>` : '';

    div.innerHTML = `
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-1 mb-2">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                ${conformityBadge}
                ${statusBadge}
                <span class="text-muted small fw-semibold">#${f.id}</span>
                ${dateHtml}${assignedHtml}${deadlineHtml}
            </div>
            <button class="btn btn-outline-danger rounded-2 ms-auto"
                    style="font-size:.72rem; padding:.2rem .45rem; line-height:1.2;"
                    onclick="deleteQaFinding(${f.id})"
                    title="Supprimer ce finding">
                <i class="bi bi-trash3"></i>
            </button>
        </div>
        ${parentHtml}
        <div class="finding-text-content">${escapeHtml(f.finding_text)}</div>
        ${actionPointHtml}
        ${resolveBtn}
    `;
    container.appendChild(div);
}

function toggleResolveForm(findingId) {
    const form = document.getElementById('resolve-form-' + findingId);
    form.style.display = (form.style.display === 'block') ? 'none' : 'block';
}

function submitResolve(findingId) {
    const actionText  = document.getElementById('resolve-text-' + findingId).value.trim();
    const resolveDate = document.getElementById('resolve-date-' + findingId).value;
    if (!actionText) {
        showQaToast('La Corrective Action est obligatoire.', 'error');
        return;
    }
    if (!resolveDate) {
        showQaToast('La date de résolution est obligatoire.', 'error');
        return;
    }
    const movText = document.getElementById('resolve-mov-' + findingId).value.trim();
    const fd = new FormData();
    fd.append('finding_id',            findingId);
    fd.append('action_point',          actionText);
    fd.append('means_of_verification', movText);
    fd.append('resolved_date',         resolveDate);
    fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);

    fetch('/ajax/resolve-qa-finding', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showQaToast('Finding résolu avec succès !', 'success');
                loadFindings(document.getElementById('findingInspectionId').value);
            } else {
                showQaToast(data.message || 'Erreur.', 'error');
            }
        })
        .catch(() => showQaToast('Erreur réseau.', 'error'));
}

function toggleFindingCorrectiveFields() {
    const isConformity = document.getElementById('findingConformity').checked;
    const assignedWrapper = document.getElementById('findingAssignedToWrapper');
    const deadlineWrapper = document.getElementById('findingDeadlineWrapper');
    assignedWrapper.style.display = isConformity ? 'none' : '';
    deadlineWrapper.style.display = isConformity ? 'none' : '';
    if (isConformity) {
        document.getElementById('findingAssignedTo').value = '';
        document.getElementById('findingDeadline').value   = '';
    }
}

function saveFinding() {
    const inspectionId  = document.getElementById('findingInspectionId').value;
    const projectId     = document.getElementById('findingProjectId').value;
    const text          = document.getElementById('findingText').value.trim();
    const assignedTo    = document.getElementById('findingAssignedTo').value;
    const deadline      = document.getElementById('findingDeadline').value;
    const parentId      = document.getElementById('findingParentId').value;
    const isConformity  = document.querySelector('input[name="findingConformity"]:checked')?.value ?? '0';
    const errDiv        = document.getElementById('findingErrorMsg');
    const btn           = document.getElementById('btnSaveFinding');

    errDiv.classList.add('d-none');
    errDiv.textContent = '';

    if (!text || (!isConformity || isConformity === '0') && !assignedTo) {
        errDiv.textContent = isConformity === '1' ? 'L\'observation est obligatoire.' : 'L\'observation et le responsable sont obligatoires.';
        errDiv.classList.remove('d-none');
        return;
    }

    const fd = new FormData();
    fd.append('inspection_id', inspectionId);
    fd.append('project_id',    projectId);
    fd.append('finding_text',  text);
    fd.append('is_conformity', isConformity);
    fd.append('assigned_to',   assignedTo);
    if (deadline) fd.append('deadline_date', deadline);
    if (parentId) fd.append('parent_finding_id', parentId);
    fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enregistrement…';

    fetch('/ajax/save-qa-finding', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-floppy me-1"></i>Enregistrer le finding';
            if (data.success) {
                showQaToast('Finding enregistré !', 'success');
                document.getElementById('findingText').value       = '';
                document.getElementById('findingAssignedTo').value = '';
                document.getElementById('findingDeadline').value   = '';
                document.getElementById('findingParentId').value   = '';
                document.getElementById('findingNonConformity').checked = true;
                loadFindings(inspectionId);
            } else {
                errDiv.textContent = data.message || 'Erreur.';
                errDiv.classList.remove('d-none');
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-floppy me-1"></i>Enregistrer le finding';
            showQaToast('Erreur réseau.', 'error');
        });
}

// ─────────────────────────────────────────────
//  SUPPRESSIONS
// ─────────────────────────────────────────────

// ─────────────────────────────────────────────
//  Toggle inspection completed / reopen
// ─────────────────────────────────────────────
function toggleInspectionComplete(inspectionId, btn) {
    const isCompleted = btn.classList.contains('btn-outline-warning'); // warning = currently completed → reopen
    const label = isCompleted ? 'rouvrir' : 'terminer';
    if (!confirm('Voulez-vous ' + label + ' cette inspection ?')) return;

    btn.disabled = true;
    fetch('{{ route("toggleInspectionCompleted") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ inspection_id: inspectionId }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Erreur.');
            btn.disabled = false;
        }
    })
    .catch(() => { alert('Network error.'); btn.disabled = false; });
}

function deleteQaInspection(inspectionId, inspectionName) {
    if (!confirm('Supprimer l\'inspection "' + inspectionName + '" ainsi que tous ses findings et actions correctives ?\n\nCette action est irréversible.')) return;

    const fd = new FormData();
    fd.append('inspection_id', inspectionId);
    fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);

    fetch('/ajax/delete-qa-inspection', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showQaToast(data.message, 'success');
                setTimeout(() => location.reload(), 900);
            } else {
                showQaToast(data.message || 'Erreur lors de la suppression.', 'error');
            }
        })
        .catch(() => showQaToast('Erreur réseau.', 'error'));
}

function deleteQaFinding(findingId) {
    if (!confirm('Supprimer ce finding et tous les findings liés ?\n\nCette action est irréversible.')) return;

    const fd = new FormData();
    fd.append('finding_id', findingId);
    fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);

    fetch('/ajax/delete-qa-finding', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showQaToast(data.message, 'success');
                const inspectionId = document.getElementById('findingInspectionId').value;
                if (inspectionId) {
                    loadFindings(inspectionId);
                } else {
                    location.reload();
                }
            } else {
                showQaToast(data.message || 'Erreur lors de la suppression.', 'error');
            }
        })
        .catch(() => showQaToast('Erreur réseau.', 'error'));
}

function toggleTableResolveForm(findingId) {
    const row = document.getElementById('table-resolve-row-' + findingId);
    row.style.display = (row.style.display === 'none' || row.style.display === '') ? '' : 'none';
}

function submitTableResolve(findingId) {
    const action  = document.getElementById('tbl-resolve-action-' + findingId).value.trim();
    const mov     = document.getElementById('tbl-resolve-mov-' + findingId).value.trim();
    const date    = document.getElementById('tbl-resolve-date-' + findingId).value;
    const errDiv  = document.getElementById('tbl-resolve-error-' + findingId);

    if (!action || !date) {
        errDiv.textContent = 'L\'action corrective et la date sont requises.';
        errDiv.style.display = '';
        return;
    }
    errDiv.style.display = 'none';

    const fd = new FormData();
    fd.append('finding_id',            findingId);
    fd.append('action_point',          action);
    fd.append('means_of_verification', mov);
    fd.append('resolved_date',         date);
    fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);

    fetch('/ajax/resolve-qa-finding', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showQaToast('Finding résolu avec succès !', 'success');
                setTimeout(() => location.reload(), 900);
            } else {
                errDiv.textContent = data.message || 'Erreur.';
                errDiv.style.display = '';
            }
        })
        .catch(() => { errDiv.textContent = 'Erreur réseau.'; errDiv.style.display = ''; });
}

function toggleTableEditForm(findingId) {
    const row = document.getElementById('table-edit-row-' + findingId);
    row.style.display = (row.style.display === 'none' || row.style.display === '') ? '' : 'none';
}

function submitTableEdit(findingId) {
    const action  = document.getElementById('tbl-edit-action-' + findingId).value.trim();
    const mov     = document.getElementById('tbl-edit-mov-' + findingId).value.trim();
    const date    = document.getElementById('tbl-edit-date-' + findingId).value;
    const errDiv  = document.getElementById('tbl-edit-error-' + findingId);

    if (!action || !date) {
        errDiv.textContent = 'L\'action corrective et la date sont requises.';
        errDiv.style.display = '';
        return;
    }
    errDiv.style.display = 'none';

    const fd = new FormData();
    fd.append('finding_id',            findingId);
    fd.append('action_point',          action);
    fd.append('means_of_verification', mov);
    fd.append('resolved_date',         date);
    fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);

    fetch('/ajax/resolve-qa-finding', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showQaToast('Résolution mise à jour !', 'success');
                setTimeout(() => location.reload(), 900);
            } else {
                errDiv.textContent = data.message || 'Erreur.';
                errDiv.style.display = '';
            }
        })
        .catch(() => { errDiv.textContent = 'Erreur réseau.'; errDiv.style.display = ''; });
}

function deleteCorrectiveActionFromTable(findingId) {
    if (!confirm('Supprimer cette résolution ?\n\nLe finding sera remis en statut "En attente".')) return;

    const fd = new FormData();
    fd.append('finding_id', findingId);
    fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);

    fetch('/ajax/delete-corrective-action', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showQaToast('Résolution supprimée. Finding remis en attente.', 'success');
                setTimeout(() => location.reload(), 900);
            } else {
                showQaToast(data.message || 'Erreur.', 'error');
            }
        })
        .catch(() => showQaToast('Erreur réseau.', 'error'));
}

function deleteQaFindingFromTable(findingId) {
    if (!confirm('Supprimer ce finding et tous les findings liés ?\n\nCette action est irréversible.')) return;

    const fd = new FormData();
    fd.append('finding_id', findingId);
    fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);

    fetch('/ajax/delete-qa-finding', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showQaToast(data.message, 'success');
                setTimeout(() => location.reload(), 900);
            } else {
                showQaToast(data.message || 'Erreur lors de la suppression.', 'error');
            }
        })
        .catch(() => showQaToast('Erreur réseau.', 'error'));
}

function deleteCorrectiveAction(findingId) {
    if (!confirm('Supprimer cette Corrective Action ?\n\nLe finding sera remis en statut "En attente".')) return;

    const fd = new FormData();
    fd.append('finding_id', findingId);
    fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);

    fetch('/ajax/delete-corrective-action', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showQaToast(data.message, 'success');
                const inspectionId = document.getElementById('findingInspectionId').value;
                loadFindings(inspectionId);
            } else {
                showQaToast(data.message || 'Erreur lors de la suppression.', 'error');
            }
        })
        .catch(() => showQaToast('Erreur réseau.', 'error'));
}

// ─────────────────────────────────────────────
//  MODAL 0 — Checklist Picker
// ─────────────────────────────────────────────

const CHECKLIST_FORMS_META = [
    { slug: 'cone-llin',                  letter: 'A', title: 'Cone Bioassay with LLIN samples',                              count: 13 },
    { slug: 'cone-irs-blocks-treatment',  letter: 'B', title: 'Cone Bioassay with IRS blocks (Blocks treatment)',             count: 10 },
    { slug: 'cone-irs-blocks-test',       letter: 'C', title: 'Cone Bioassay with IRS blocks (Test)',                         count: 11 },
    { slug: 'tunnel-test',                letter: 'D', title: 'Tunnel Test',                                                  count: 16 },
    { slug: 'llin-washing',               letter: 'E', title: 'Evaluation of Whole LLIN – Washing/Cutting',                   count: 10 },
    { slug: 'llin-exp-huts',              letter: 'F', title: 'Evaluation of Whole LLIN in Experimental huts',                count: 20 },
    { slug: 'irs-treatment',              letter: 'G', title: 'IRS Treatment application',                                    count: 13 },
    { slug: 'irs-trial',                  letter: 'H', title: 'IRS Trial',                                                    count: 14 },
    { slug: 'cone-irs-walls',             letter: 'I', title: 'Cone Bioassay on IRS treated walls',                           count: 12 },
    { slug: 'cylinder-bioassay',          letter: 'J', title: 'Cylinder Bioassay',                                            count:  8 },
    { slug: 'cdc-bottle-coating',         letter: 'K', title: 'CDC Bottle Bioassay (Coating)',                                count:  8 },
    { slug: 'cdc-bottle-test',            letter: 'L', title: 'CDC Bottle Bioassay (Test)',                                   count: 10 },
    { slug: 'spatial-repellents',         letter: 'M', title: 'Evaluation of spatial repellents in Experimental huts',        count: 20 },
];

const FACILITY_SECTIONS_META = {
    cotonou: [
        { slug: 'facility-a', letter: 'A', title: 'Administration',                           count: 26 },
        { slug: 'facility-b', letter: 'B', title: 'Document Control',                         count: 15 },
        { slug: 'facility-c', letter: 'C', title: 'Bioassay Laboratory',                      count: 11 },
        { slug: 'facility-d', letter: 'D', title: 'Biomolecular Room',                        count:  5 },
        { slug: 'facility-e', letter: 'E', title: 'Shaker-Bath room and LLIN Washing area',   count:  4 },
        { slug: 'facility-f', letter: 'F', title: 'Chemical & Potter tower Room',             count: 19 },
        { slug: 'facility-g', letter: 'G', title: 'Safety (changing) room',                   count:  7 },
        { slug: 'facility-h', letter: 'H', title: 'Storage and untreated block rooms',        count:  6 },
        { slug: 'facility-i', letter: 'I', title: 'Net storage room and expired products Room', count: 4 },
        { slug: 'facility-j', letter: 'J', title: 'Equipment',                                count: 15 },
        { slug: 'facility-k', letter: 'K', title: 'Staff Offices & Buildings',                count: 13 },
        { slug: 'facility-l', letter: 'L', title: 'Data Management',                          count: 25 },
        { slug: 'facility-m', letter: 'M', title: 'Archive',                                  count: 16 },
        { slug: 'facility-n', letter: 'N', title: 'Insectary and Annex',                      count: 25 },
        { slug: 'facility-o', letter: 'O', title: 'Animal House',                             count: 12 },
    ],
    cove: [
        { slug: 'facility-cove-a', letter: 'A', title: 'General',                             count:  1 },
        { slug: 'facility-cove-b', letter: 'B', title: 'Staff Offices & Buildings',           count: 24 },
        { slug: 'facility-cove-c', letter: 'C', title: 'Bioassay Laboratory Field site',      count: 22 },
        { slug: 'facility-cove-d', letter: 'D', title: 'Chemical Room & Non-treated material Room', count: 16 },
        { slug: 'facility-cove-e', letter: 'E', title: 'Experimental Huts – SITE 1',          count: 13 },
        { slug: 'facility-cove-f', letter: 'F', title: 'Experimental Huts – SITE 2',          count: 13 },
        { slug: 'facility-cove-g', letter: 'G', title: 'Experimental Huts – SITE 3',          count: 13 },
        { slug: 'facility-cove-h', letter: 'H', title: 'Insectary',                           count: 25 },
        { slug: 'facility-cove-i', letter: 'I', title: 'Animal House',                        count: 12 },
    ],
};

function openChecklistModal(inspectionId, inspectionName, inspectionType, facilityLocation) {
    document.getElementById('checklistPickerSubtitle').textContent = inspectionName;

    const grid        = document.getElementById('checklistPickerGrid');
    const progressArea = document.getElementById('facilityProgressArea');
    const instruction  = document.getElementById('checklistPickerInstruction');

    if (inspectionType === 'Facility Inspection') {
        instruction.style.display = 'none';
        progressArea.style.display = 'block';
        grid.innerHTML = '<div class="col-12 text-center text-muted py-3"><i class="bi bi-arrow-repeat spin me-2"></i>Chargement…</div>';

        const facilityMeta = FACILITY_SECTIONS_META[facilityLocation || 'cotonou'] || FACILITY_SECTIONS_META.cotonou;
        fetch(`/ajax/get-checklist-statuses?inspection_id=${inspectionId}`)
            .then(r => r.json())
            .then(data => {
                const statuses = data.statuses || {};
                const done     = statuses['facility_progress'] ?? 0;
                const total    = facilityMeta.length;
                const pct      = total > 0 ? Math.round(done / total * 100) : 0;

                // Update progress bar
                const bar   = document.getElementById('facilityProgressBar');
                const label = document.getElementById('facilityProgressLabel');
                const alert = document.getElementById('facilityProgressAlert');
                bar.style.width = pct + '%';
                bar.className   = 'progress-bar ' + (done >= total ? 'bg-success' : 'bg-warning');
                label.textContent = `${done}/${total} (${pct}%)`;
                if (done >= total) {
                    alert.className   = 'alert alert-success d-flex align-items-center gap-2 py-2 px-3 mb-0';
                    alert.innerHTML   = '<i class="bi bi-check-circle-fill flex-shrink-0"></i><span>Toutes les sections sont complétées.</span>';
                } else {
                    alert.className   = 'alert alert-warning d-flex align-items-center gap-2 py-2 px-3 mb-0';
                    alert.innerHTML   = `<i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i><span>Il reste <strong>${total - done}</strong> section(s) à compléter pour pouvoir finaliser.</span>`;
                }

                // Build section cards
                grid.innerHTML = facilityMeta.map(f => {
                    const filled  = statuses[f.slug] === true;
                    const badge   = filled
                        ? '<span class="badge rounded-pill bg-success" style="font-size:.7rem;">Rempli</span>'
                        : '<span class="badge rounded-pill bg-warning text-dark" style="font-size:.7rem;">À compléter</span>';
                    const border  = filled ? 'border-color:#198754 !important; border-width:2px !important;' : '';
                    return `
                        <div class="col-md-6 col-lg-4">
                            <a href="/checklist/${inspectionId}/${f.slug}" class="text-decoration-none">
                                <div class="picker-card p-3 d-flex align-items-center gap-3" style="${border}">
                                    <div class="picker-letter">${f.letter}</div>
                                    <div class="flex-grow-1">
                                        <div class="picker-title">${escapeHtml(f.title)}</div>
                                        <div class="picker-qcount">${f.count} questions</div>
                                    </div>
                                    <div class="d-flex flex-column align-items-end gap-1">
                                        ${badge}
                                        <i class="bi bi-chevron-right text-muted" style="font-size:.8rem;"></i>
                                    </div>
                                </div>
                            </a>
                        </div>`;
                }).join('');
            })
            .catch(() => {
                grid.innerHTML = '<div class="col-12 text-center text-danger py-3">Erreur lors du chargement.</div>';
            });
    } else {
        instruction.style.display = '';
        progressArea.style.display = 'none';

        grid.innerHTML = CHECKLIST_FORMS_META.map(f => `
            <div class="col-md-6 col-lg-4">
                <a href="/checklist/${inspectionId}/${f.slug}" class="text-decoration-none">
                    <div class="picker-card p-3 d-flex align-items-center gap-3">
                        <div class="picker-letter">${f.letter}</div>
                        <div class="flex-grow-1">
                            <div class="picker-title">${escapeHtml(f.title)}</div>
                            <div class="picker-qcount">${f.count} questions</div>
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </div>
                </a>
            </div>
        `).join('');
    }

    new bootstrap.Modal(document.getElementById('checklistPickerModal')).show();
}

// ─────────────────────────────────────────────
//  Listeners dynamiques — Modal inspection QA
// ─────────────────────────────────────────────
(function () {
    const typeSelect    = document.getElementById('qaTypeInspection');
    const actWrapper    = document.getElementById('qaActivitySelectWrapper');
    const clFormWrapper = document.getElementById('qaChecklistFormWrapper');
    const actSelect     = document.getElementById('qaActivitySelect');
    const banner        = document.getElementById('qaLinkedActivityWrapper');
    const bannerText    = document.getElementById('qaLinkedActivityName');
    const hiddenId      = document.getElementById('qaActivityIdHidden');
    const slugSelect    = document.getElementById('qaChecklistSlugSelect');

    // Quand le type change : afficher/masquer les champs Critical Phase
    if (typeSelect) {
        typeSelect.addEventListener('change', function () {
            const isCritical = this.value === 'Critical Phase Inspection';
            const isFacility = this.value === 'Facility Inspection';
            actWrapper.style.display    = isCritical ? '' : 'none';
            clFormWrapper.style.display = isCritical ? '' : 'none';
            document.getElementById('qaFacilityLocationWrapper').style.display = isFacility ? '' : 'none';
            if (!isCritical) {
                actSelect.value      = '';
                hiddenId.value       = '';
                slugSelect.value     = '';
                banner.style.display = 'none';
            }
        });
    }

    // Quand une activité est sélectionnée : mettre à jour le hidden + le bandeau
    if (actSelect) {
        actSelect.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            if (opt.value) {
                hiddenId.value         = opt.value;
                bannerText.textContent = opt.text;
                banner.style.display   = 'block';
            } else {
                hiddenId.value       = '';
                banner.style.display = 'none';
            }
        });
    }
})();

// ─────────────────────────────────────────────
//  BLOC C — Filtre + Impression
// ─────────────────────────────────────────────

function filterFindings() {
    const selectedId = document.getElementById('filterByInspection').value;
    const rows = document.querySelectorAll('#findingsGlobalTable .finding-row');
    rows.forEach(row => {
        if (!selectedId || row.dataset.inspectionId === selectedId) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function printFindings(withCorrective) {
    const selectedId  = document.getElementById('filterByInspection').value;
    const projectCode = "{{ $project ? $project->project_code : '' }}";
    const filterLabel = selectedId
        ? document.getElementById('filterByInspection').selectedOptions[0]?.textContent.trim()
        : 'Toutes les inspections';

    // Collecter les lignes visibles
    const rows = document.querySelectorAll('#findingsGlobalTable .finding-row');
    const visibleRows = Array.from(rows).filter(r =>
        !selectedId || r.dataset.inspectionId === selectedId
    );

    if (visibleRows.length === 0) {
        showQaToast('Aucun finding à imprimer pour cette sélection.', 'error');
        return;
    }

    // Construire le tableau HTML imprimable
    let tableRows = '';
    visibleRows.forEach((row, idx) => {
        const cells = row.querySelectorAll('td');
        const inspection  = cells[1]?.innerText.trim() || '—';
        const finding     = cells[2]?.innerText.trim() || '—';
        const assignedTo  = cells[3]?.innerText.trim() || '—';
        const deadline    = cells[4]?.innerText.trim() || '—';
        const corrective  = cells[5]?.innerText.trim() || '—';
        const status      = cells[6]?.innerText.trim() || '—';

        const caRow = withCorrective
            ? `<tr>
                   <td colspan="2" style="padding:4px 8px; background:#e8f5e9; color:#1a5c2a; font-style:italic; font-size:11px;">
                       <strong>Corrective Action :</strong> ${corrective !== 'En attente' ? corrective : '<em style="color:#999;">En attente</em>'}
                   </td>
               </tr>`
            : '';

        tableRows += `
            <tr style="border-top:2px solid #eee;">
                <td style="padding:8px; font-weight:bold; color:#888; vertical-align:top; font-size:12px;">${idx + 1}</td>
                <td style="padding:8px; vertical-align:top; font-size:11px; color:#444;">${inspection}</td>
                <td style="padding:8px; vertical-align:top; font-size:12px;">${finding}</td>
                <td style="padding:8px; vertical-align:top; font-size:11px; color:#444;">${assignedTo}</td>
                <td style="padding:8px; vertical-align:top; font-size:11px; color:#444;">${deadline}</td>
                <td style="padding:8px; text-align:center; font-size:11px;">
                    <span style="background:${status.includes('Résolu') ? '#198754' : '#fd7e14'}; color:#fff;
                                 border-radius:999px; padding:2px 8px; font-size:10px;">${status}</span>
                </td>
            </tr>
            ${caRow}
        `;
    });

    const caHeaderCell = withCorrective ? '' : '';
    const title = withCorrective
        ? 'Rapport de Findings QA — Avec Corrective Actions'
        : 'Rapport de Findings QA — Sans Corrective Actions';

    const html = `<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>${title}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #222; padding: 24px; }
        .print-header { border-bottom: 3px solid #C10202; padding-bottom: 14px; margin-bottom: 20px; }
        .print-header h1 { font-size: 17px; color: #C10202; margin-bottom: 4px; }
        .print-header .meta { font-size: 11px; color: #666; }
        table { width: 100%; border-collapse: collapse; }
        thead th { background: #C10202; color: #fff; padding: 8px; font-size: 11px; text-align: left; }
        tbody tr:nth-child(odd) { background: #fafafa; }
        tbody td { padding: 6px 8px; vertical-align: top; border-bottom: 1px solid #eee; font-size: 11px; }
        .footer { margin-top: 20px; font-size: 10px; color: #aaa; text-align: right; }
        @media print {
            body { padding: 10px; }
            button { display: none; }
        }
    </style>
</head>
<body>
    <div class="print-header">
        <h1>${title}</h1>
        <div class="meta">
            <strong>Projet :</strong> ${projectCode} &nbsp;|&nbsp;
            <strong>Inspection :</strong> ${filterLabel} &nbsp;|&nbsp;
            <strong>Date d'impression :</strong> ${new Date().toLocaleDateString('fr-FR')} &nbsp;|&nbsp;
            <strong>Nombre de findings :</strong> ${visibleRows.length}
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width:3%">#</th>
                <th style="width:16%">Inspection</th>
                <th style="width:33%">Observation / Finding</th>
                <th style="width:15%">Adressé à</th>
                <th style="width:11%">Deadline</th>
                <th style="width:10%">Statut</th>
            </tr>
        </thead>
        <tbody>
            ${tableRows}
        </tbody>
    </table>
    <div class="footer">Généré par Project Management — CREC</div>
    <script>window.onload = function(){ window.print(); }<\/script>
</body>
</html>`;

    const win = window.open('', '_blank');
    win.document.write(html);
    win.document.close();
}

// ─────────────────────────────────────────────
//  MODAL — Modifier une inspection
// ─────────────────────────────────────────────
function openEditInspectionModal(inspectionId, name, dateStart, dateEnd, dateFm, dateSd, inspectorId, checklistSlug, typeInspection) {
    document.getElementById('editInspectionId').value        = inspectionId;
    document.getElementById('editInspectionType').value      = typeInspection || '';
    document.getElementById('editInspectionName').value      = name || '';
    document.getElementById('editInspectionDate').value      = dateStart || '';
    document.getElementById('editInspectionDateEnd').value   = dateEnd   || '';
    document.getElementById('editInspectionDateFm').value    = dateFm    || '';
    document.getElementById('editInspectionDateSd').value    = dateSd    || '';
    document.getElementById('editInspectionInspector').value = inspectorId ? String(inspectorId) : '';
    document.getElementById('editChecklistSlug').value       = checklistSlug || '';

    const isCritical = (typeInspection || '') === 'Critical Phase Inspection';
    document.getElementById('editChecklistSlugWrapper').style.display = isCritical ? '' : 'none';

    const errDiv = document.getElementById('editInspectionErrorMsg');
    errDiv.classList.add('d-none');
    errDiv.textContent = '';

    new bootstrap.Modal(document.getElementById('editInspectionModal'), {}).show();
}

function saveEditInspection() {
    const inspectionId   = document.getElementById('editInspectionId').value;
    const typeInspection = document.getElementById('editInspectionType').value;
    const name           = document.getElementById('editInspectionName').value.trim();
    const dateStart      = document.getElementById('editInspectionDate').value;
    const dateEnd        = document.getElementById('editInspectionDateEnd').value;
    const dateFm         = document.getElementById('editInspectionDateFm').value;
    const dateSd         = document.getElementById('editInspectionDateSd').value;
    const inspectorId    = document.getElementById('editInspectionInspector').value;
    const checklistSlug  = document.getElementById('editChecklistSlug').value;
    const errDiv         = document.getElementById('editInspectionErrorMsg');
    const btn            = document.getElementById('btnSaveEditInspection');

    errDiv.classList.add('d-none');
    errDiv.textContent = '';

    if (!dateStart) {
        errDiv.textContent = 'La date de début d\'inspection est obligatoire.';
        errDiv.classList.remove('d-none');
        return;
    }

    const fd = new FormData();
    fd.append('inspection_id',   inspectionId);
    fd.append('date_scheduled',  dateStart);
    fd.append('date_start',      dateStart);
    if (dateEnd)      fd.append('date_end',       dateEnd);
    if (dateFm)       fd.append('date_report_fm', dateFm);
    if (dateSd)       fd.append('date_report_sd', dateSd);
    if (inspectorId)  fd.append('qa_inspector_id', inspectorId);
    if (name)         fd.append('inspection_name', name);
    if (checklistSlug) fd.append('checklist_slug', checklistSlug);
    fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enregistrement…';

    fetch('/ajax/update-qa-inspection', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check2 me-1"></i>Enregistrer';
            if (data.success) {
                showQaToast('Inspection mise à jour.', 'success');
                bootstrap.Modal.getInstance(document.getElementById('editInspectionModal')).hide();
                setTimeout(() => location.reload(), 900);
            } else {
                errDiv.textContent = data.message || 'Une erreur est survenue.';
                errDiv.classList.remove('d-none');
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check2 me-1"></i>Enregistrer';
            showQaToast('Erreur réseau.', 'error');
        });
}
</script>

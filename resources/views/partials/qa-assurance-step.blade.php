@php
    $project_id           = request('project_id');
    $project              = App\Models\Pro_Project::find($project_id);
    $all_phases_critiques = $project ? $project->allPhasesCritiques : collect();
    $all_personnels       = App\Models\Pro_Personnel::orderBy('prenom', 'asc')->get();
    $qa_inspections       = App\Models\Pro_QaInspection::where('project_id', $project_id)
                                ->with('inspector')
                                ->withCount('findings')
                                ->latest()
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
                                            @if ($activite->status === 'completed')
                                                @if (isset($activityInspectionMap[$activite->id]))
                                                    @php $linkedInspection = $activityInspectionMap[$activite->id]; @endphp
                                                    @if ($linkedInspection->checklist_slug)
                                                        {{-- Inspection avec formulaire associé → lien direct --}}
                                                        <a href="{{ route('checklist.show', [$linkedInspection->id, $linkedInspection->checklist_slug]) }}"
                                                           class="btn btn-qa-inspect btn-sm">
                                                            <i class="bi bi-clipboard2-check me-1"></i>Inspecter
                                                        </a>
                                                    @else
                                                        {{-- Inspection sans formulaire → picker modal --}}
                                                        <button class="btn btn-qa-inspect btn-sm"
                                                                onclick="openChecklistModal(
                                                                    {{ $linkedInspection->id }},
                                                                    '{{ addslashes($linkedInspection->inspection_name ?? $linkedInspection->type_inspection) }}'
                                                                )">
                                                            <i class="bi bi-clipboard2-check me-1"></i>Inspecter
                                                        </button>
                                                    @endif
                                                @else
                                                    <button class="btn btn-qa-schedule btn-sm"
                                                            onclick="openQaInspectionModal(
                                                                {{ $activite->id }},
                                                                '{{ addslashes($activite->study_activity_name) }}',
                                                                'Critical Phase Inspection'
                                                            )">
                                                        <i class="bi bi-calendar-plus me-1"></i>Programmer Inspection
                                                    </button>
                                                @endif
                                            @else
                                                <span class="text-muted small fst-italic">
                                                    <i class="bi bi-lock me-1"></i>Non exécutée
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
                                                    'Critical Phase Inspection' => '#6f42c1',
                                                    'Study Inspection'          => '#0d6efd',
                                                    'Process Inspection'        => '#fd7e14',
                                                    default                     => '#6c757d',
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
                                            @if ($inspection->date_performed)
                                                <span class="badge-done"><i class="bi bi-check-lg me-1"></i>Réalisée</span>
                                            @else
                                                <span class="badge-scheduled"><i class="bi bi-clock me-1"></i>Programmée</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center justify-content-center gap-1 flex-wrap">
                                                <button class="btn btn-qa-findings btn-sm"
                                                        onclick="openFindingsModal(
                                                            {{ $inspection->id }},
                                                            '{{ addslashes($inspection->inspection_name ?? $inspection->type_inspection) }}',
                                                            '{{ $inspection->date_scheduled ? \Carbon\Carbon::parse($inspection->date_scheduled)->format("d/m/Y") : "" }}'
                                                        )">
                                                    <i class="bi bi-journal-text me-1"></i>Findings
                                                    @if ($inspection->findings_count > 0)
                                                        <span class="badge bg-white text-primary ms-1">{{ $inspection->findings_count }}</span>
                                                    @endif
                                                </button>
                                                @if ($inspection->checklist_slug)
                                                    <a href="{{ route('checklist.show', [$inspection->id, $inspection->checklist_slug]) }}"
                                                       class="btn btn-outline-dark btn-sm"
                                                       title="Ouvrir le formulaire d'inspection">
                                                        <i class="bi bi-clipboard2-check"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('checklist.index', $inspection->id) }}"
                                                       class="btn btn-outline-dark btn-sm"
                                                       title="Checklists d'inspection">
                                                        <i class="bi bi-clipboard2-check"></i>
                                                    </a>
                                                @endif
                                                <button class="btn btn-qa-delete btn-sm"
                                                        onclick="deleteQaInspection(
                                                            {{ $inspection->id }},
                                                            '{{ addslashes($inspection->inspection_name ?? $inspection->type_inspection) }}'
                                                        )"
                                                        title="Supprimer cette inspection">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
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
                                    #{{ $insp->id }} — {{ $insp->type_inspection }}
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
                                    <th style="width:30%">Observation / Finding</th>
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
                                            'Critical Phase Inspection' => '#6f42c1',
                                            'Study Inspection'          => '#0d6efd',
                                            'Process Inspection'        => '#fd7e14',
                                            default                     => '#6c757d',
                                        };
                                    @endphp
                                    <tr class="finding-row" data-inspection-id="{{ $fi->inspection_id }}">
                                        <td class="text-muted small">{{ $loop->iteration }}</td>
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
                                                <div class="p-2 rounded"
                                                     style="background:#e8f5e9; font-size:.83rem; color:#1a5c2a; white-space:pre-wrap; word-break:break-word;">
                                                    {{ $fi->action_point }}
                                                </div>
                                            @else
                                                <span class="text-muted fst-italic small">En attente</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($fi->status === 'complete')
                                                <span class="badge bg-success rounded-pill">
                                                    <i class="bi bi-check-lg me-1"></i>Résolu
                                                </span>
                                            @else
                                                <span class="badge bg-warning text-dark rounded-pill">
                                                    <i class="bi bi-hourglass-split me-1"></i>En attente
                                                </span>
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

                <p class="text-muted small mb-3">
                    <i class="bi bi-info-circle me-1"></i>
                    Sélectionnez le formulaire à remplir pour cette inspection.
                </p>

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

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    Observation / Constat <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="findingText" rows="3"
                                          placeholder="Décrivez l'observation ou le constat fait lors de l'inspection…"></textarea>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-semibold">
                                    Adressé à (Study Director / Responsable) <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="findingAssignedTo" required>
                                    <option value="">— Sélectionner —</option>
                                    @foreach ($all_personnels as $personnel)
                                        <option value="{{ $personnel->id }}">
                                            {{ $personnel->prenom }} {{ $personnel->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
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

// ─────────────────────────────────────────────
//  MODAL 1 — Programmer une inspection
// ─────────────────────────────────────────────
function openQaInspectionModal(activityId, activityName, defaultType) {
    document.getElementById('qaTypeInspection').value        = defaultType || '';
    document.getElementById('qaDateScheduled').value         = '';
    document.getElementById('qaInspectorSelect').value       = '';
    document.getElementById('qaActivityIdHidden').value      = activityId || '';
    document.getElementById('qaChecklistSlugSelect').value   = '';

    const errDiv = document.getElementById('qaInspectionErrorMsg');
    errDiv.classList.add('d-none');
    errDiv.textContent = '';

    // Afficher/masquer les champs spécifiques à Critical Phase
    const isCritical = defaultType === 'Critical Phase Inspection';
    document.getElementById('qaActivitySelectWrapper').style.display    = isCritical ? '' : 'none';
    document.getElementById('qaChecklistFormWrapper').style.display     = isCritical ? '' : 'none';

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
    if (activityId)    fd.append('activity_id',    activityId);
    if (checklistSlug) fd.append('checklist_slug', checklistSlug);
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
function openFindingsModal(inspectionId, inspectionType, inspectionDate) {
    document.getElementById('findingInspectionId').value = inspectionId;
    document.getElementById('findingsModalSubtitle').textContent =
        inspectionType + (inspectionDate ? ' — ' + inspectionDate : '');

    // Réinitialiser formulaire
    document.getElementById('findingText').value       = '';
    document.getElementById('findingAssignedTo').value = '';
    document.getElementById('findingDeadline').value   = '';
    document.getElementById('findingParentId').innerHTML = '<option value="">— Aucun —</option>';

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
    const isResolved = f.status === 'complete';
    const div = document.createElement('div');
    div.id = 'finding-card-' + f.id;
    div.className = 'finding-card ' + (isResolved ? 'resolved' : 'pending-finding');

    const deadlineHtml = f.deadline_date
        ? `<span class="text-muted small ms-3"><i class="bi bi-calendar-event me-1"></i>Deadline : <strong>${f.deadline_date}</strong></span>` : '';
    const assignedHtml = f.assigned_to_name
        ? `<span class="text-muted small ms-3"><i class="bi bi-person me-1"></i>${escapeHtml(f.assigned_to_name)}</span>` : '';
    const dateHtml = f.created_at
        ? `<span class="text-muted small ms-3"><i class="bi bi-clock me-1"></i>${f.created_at}</span>` : '';
    const parentHtml = f.parent_finding_text
        ? `<div class="finding-parent-ref"><i class="bi bi-link-45deg me-1"></i>Lié au finding : ${escapeHtml(f.parent_finding_text)}</div>` : '';
    const statusBadge = isResolved
        ? `<span class="badge bg-success rounded-pill"><i class="bi bi-check-lg me-1"></i>Résolu</span>`
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
           </div>` : '';
    const resolveBtn = !isResolved
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
               <button class="btn btn-success btn-sm rounded-3 px-3" onclick="submitResolve(${f.id})">
                   <i class="bi bi-check-circle me-1"></i>Valider la résolution
               </button>
               <button class="btn btn-link btn-sm text-muted" onclick="toggleResolveForm(${f.id})">Annuler</button>
           </div>` : '';

    div.innerHTML = `
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-1 mb-2">
            <div class="d-flex align-items-center gap-2 flex-wrap">
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
    const actionText = document.getElementById('resolve-text-' + findingId).value.trim();
    if (!actionText) {
        showQaToast('La Corrective Action est obligatoire.', 'error');
        return;
    }
    const fd = new FormData();
    fd.append('finding_id',   findingId);
    fd.append('action_point', actionText);
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

function saveFinding() {
    const inspectionId = document.getElementById('findingInspectionId').value;
    const projectId    = document.getElementById('findingProjectId').value;
    const text         = document.getElementById('findingText').value.trim();
    const assignedTo   = document.getElementById('findingAssignedTo').value;
    const deadline     = document.getElementById('findingDeadline').value;
    const parentId     = document.getElementById('findingParentId').value;
    const errDiv       = document.getElementById('findingErrorMsg');
    const btn          = document.getElementById('btnSaveFinding');

    errDiv.classList.add('d-none');
    errDiv.textContent = '';

    if (!text || !assignedTo) {
        errDiv.textContent = 'L\'observation et le responsable sont obligatoires.';
        errDiv.classList.remove('d-none');
        return;
    }

    const fd = new FormData();
    fd.append('inspection_id', inspectionId);
    fd.append('project_id',    projectId);
    fd.append('finding_text',  text);
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

function openChecklistModal(inspectionId, inspectionName) {
    document.getElementById('checklistPickerSubtitle').textContent = inspectionName;

    const grid = document.getElementById('checklistPickerGrid');
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
            actWrapper.style.display    = isCritical ? '' : 'none';
            clFormWrapper.style.display = isCritical ? '' : 'none';
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
</script>

@extends('index-new')
@section('title', 'Quality Assurance Dashboard')

@section('content')
<style>
    :root {
        --qa-brand: #C10202;
        --qa-brand-dark: #8b0001;
    }
    .qa-dash .stat-card {
        border-radius: 14px;
        border: none;
        color: #fff;
        padding: 1.1rem 1.4rem;
        position: relative;
        overflow: hidden;
    }
    .qa-dash .stat-card .stat-icon {
        font-size: 2.8rem;
        opacity: .18;
        position: absolute;
        right: 1rem;
        bottom: .4rem;
    }
    .qa-dash .stat-card .stat-value { font-size: 2.2rem; font-weight: 700; line-height: 1; }
    .qa-dash .stat-card .stat-label { font-size: .82rem; opacity: .85; margin-top: .25rem; }
    .qa-dash .card-header-qa {
        background: linear-gradient(90deg, var(--qa-brand), var(--qa-brand-dark));
        color: #fff;
        font-weight: 600;
        border-radius: .75rem .75rem 0 0;
        padding: .65rem 1rem;
    }
    .qa-dash .btn-qa-primary { background: var(--qa-brand); color: #fff; border: none; }
    .qa-dash .btn-qa-primary:hover { background: var(--qa-brand-dark); color: #fff; }
    .qa-dash .type-badge {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 20px;
        font-size: .75rem;
        font-weight: 600;
        color: #fff;
    }
    .qa-dash .type-facility    { background: #0d6efd; }
    .qa-dash .type-process     { background: #6f42c1; }
    .qa-dash .type-study       { background: #198754; }
    .qa-dash .type-critical    { background: var(--qa-brand); }
    .qa-dash .insp-row:hover   { background: #fdf3f3; cursor: pointer; }
    .qa-dash .insp-row.selected { background: #fce8e8; }

    /* Findings panel */
    .qa-dash .findings-panel {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: .75rem;
    }
    .qa-dash .finding-card {
        border-left: 4px solid #dee2e6;
        border-radius: .4rem;
        padding: .6rem .8rem;
        font-size: .87rem;
        background: #f9f9f9;
    }
    .qa-dash .finding-card.pending  { border-left-color: #fd7e14; background: #fff9f4; }
    .qa-dash .finding-card.resolved { border-left-color: #198754; background: #f0fff4; }
    .qa-dash .finding-card.conform  { border-left-color: #6c757d; background: #f8f9fa; }
    .qa-dash .page-header {
        background: linear-gradient(135deg, var(--qa-brand) 0%, var(--qa-brand-dark) 100%);
        color: #fff;
        border-radius: 1rem;
        padding: 1.4rem 2rem;
    }
</style>

<div class="qa-dash">

    {{-- ── Page Header ──────────────────────────────────── --}}
    <div class="page-header d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-0 fw-bold"><i class="bi bi-shield-check me-2"></i>Quality Assurance Dashboard</h4>
            <div class="small opacity-75 mt-1">All QA inspections across all projects</div>
        </div>
        <button class="btn btn-light fw-semibold" onclick="openScheduleModal()">
            <i class="bi bi-plus-circle me-1"></i>Schedule Inspection
        </button>
    </div>

    {{-- ── Stats ────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card" style="background:linear-gradient(135deg,#1a3a6b,#2a5aaa);">
                <div class="stat-value">{{ $totalInspections }}</div>
                <div class="stat-label">Total Inspections</div>
                <i class="bi bi-clipboard2-check stat-icon"></i>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card" style="background:linear-gradient(135deg,#0d6efd,#0a58ca);">
                <div class="stat-value">{{ $scheduledCount }}</div>
                <div class="stat-label">Scheduled (pending)</div>
                <i class="bi bi-calendar-event stat-icon"></i>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card" style="background:linear-gradient(135deg,#198754,#146c43);">
                <div class="stat-value">{{ $doneCount }}</div>
                <div class="stat-label">Completed</div>
                <i class="bi bi-check-circle stat-icon"></i>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card" style="background:linear-gradient(135deg,#C10202,#8b0001);">
                <div class="stat-value">{{ $unresolvedFindings }}</div>
                <div class="stat-label">Unresolved Findings</div>
                <i class="bi bi-exclamation-triangle stat-icon"></i>
            </div>
        </div>
    </div>

    {{-- ── Filters ──────────────────────────────────────── --}}
    <form method="GET" action="{{ route('qaDashboard') }}" class="card border-0 shadow-sm mb-4">
        <div class="card-body py-2">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold mb-1">Project</label>
                    <select name="project_id" id="filter-project" class="form-select form-select-sm">
                        <option value="">All projects</option>
                        @foreach($all_projects as $proj)
                            <option value="{{ $proj->id }}" {{ $filterProject == $proj->id ? 'selected' : '' }}>
                                {{ $proj->project_code }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold mb-1">Inspection Type</label>
                    <select name="type_inspection" id="filter-type" class="form-select form-select-sm">
                        <option value="">All types</option>
                        @foreach($inspectionTypes as $t)
                            <option value="{{ $t }}" {{ $filterType == $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold mb-1">Status</label>
                    <select name="status" id="filter-status" class="form-select form-select-sm">
                        <option value="" {{ $filterStatus === '' ? 'selected' : '' }}>All</option>
                        <option value="scheduled" {{ $filterStatus === 'scheduled' ? 'selected' : '' }}>Scheduled (en cours)</option>
                        <option value="done"      {{ $filterStatus === 'done'      ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-qa-primary btn-sm w-100">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('qaDashboard') }}" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="bi bi-x-circle me-1"></i>Reset
                    </a>
                </div>
            </div>
        </div>
    </form>

    {{-- ── Main content: inspections table + findings panel ── --}}
    <div class="row g-4">

        {{-- Inspections Table --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header-qa d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-table me-2"></i>Inspections ({{ $all_inspections->count() }})</span>
                </div>
                <div class="card-body p-0">
                    @if($all_inspections->isEmpty())
                        <p class="text-muted text-center py-5 mb-0">No inspections found.</p>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th>Project</th>
                                    <th>Inspection</th>
                                    <th>Type</th>
                                    <th>Inspector</th>
                                    <th>Date</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Findings</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($all_inspections as $insp)
                                @php
                                    $typeClass = match($insp->type_inspection) {
                                        'Facility Inspection'       => 'type-facility',
                                        'Process Inspection'        => 'type-process',
                                        'Study Inspection'          => 'type-study',
                                        'Critical Phase Inspection' => 'type-critical',
                                        default                     => 'type-study',
                                    };
                                    $isDone = !is_null($insp->date_performed);
                                @endphp
                                <tr class="insp-row"
                                    data-id="{{ $insp->id }}"
                                    data-name="{{ addslashes($insp->inspection_name ?? $insp->type_inspection) }}"
                                    data-project="{{ $insp->project->project_code ?? '—' }}"
                                    data-project-id="{{ $insp->project_id }}"
                                    data-type="{{ $insp->type_inspection }}"
                                    data-facility-location="{{ $insp->facility_location ?? '' }}"
                                    data-completed="{{ $insp->completed_at ? '1' : '0' }}"
                                    onclick="selectInspection({{ $insp->id }}, '{{ addslashes($insp->inspection_name ?? $insp->type_inspection) }}', {{ $insp->project_id ?? 'null' }}, '{{ $insp->type_inspection }}', '{{ $insp->facility_location ?? '' }}', {{ $insp->completed_at ? 'true' : 'false' }})">
                                    <td class="fw-semibold">{{ $insp->project->project_code ?? '—' }}</td>
                                    <td>{{ $insp->inspection_name ?? $insp->type_inspection }}</td>
                                    <td><span class="type-badge {{ $typeClass }}">{{ $insp->type_inspection }}</span></td>
                                    <td>
                                        @if($insp->inspector)
                                            {{ $insp->inspector->prenom }} {{ $insp->inspector->nom }}
                                        @else <span class="text-muted">—</span>@endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($insp->date_scheduled)->format('d/m/Y') }}</td>
                                    <td class="text-center">
                                        @if($insp->completed_at)
                                            <span class="badge bg-success"><i class="bi bi-patch-check-fill me-1"></i>Completed</span>
                                        @elseif($isDone)
                                            <span class="badge bg-success">Done</span>
                                        @else
                                            <span class="badge bg-primary">Scheduled</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $insp->findings_count }}</span>
                                    </td>
                                    <td class="text-nowrap" onclick="event.stopPropagation()">
                                        @if($isDone)
                                        @if ($insp->type_inspection === 'Facility Inspection')
                                        <a href="{{ route('checklist.facilityPrint', $insp->id) }}?mode=filled" target="_blank"
                                           class="btn btn-xs btn-outline-secondary btn-sm py-0 px-1" title="Imprimer Facility Checklist">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                        @elseif ($insp->type_inspection === 'Process Inspection')
                                        <a href="{{ route('checklist.processPrint', $insp->id) }}?mode=filled" target="_blank"
                                           class="btn btn-xs btn-outline-secondary btn-sm py-0 px-1" title="Imprimer Process Checklist">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                        @endif
                                        <a href="{{ route('checklist.report', $insp->id) }}" target="_blank"
                                           class="btn btn-xs btn-outline-primary btn-sm py-0 px-1" title="QA Unit Report">
                                            <i class="bi bi-file-earmark-text"></i>
                                        </a>
                                        <a href="{{ route('checklist.followup', $insp->id) }}" target="_blank"
                                           class="btn btn-xs btn-outline-success btn-sm py-0 px-1" title="Follow-Up Report">
                                            <i class="bi bi-file-earmark-check"></i>
                                        </a>
                                        {{-- Mark as completed / Reopen --}}
                                        @if($insp->completed_at)
                                        <button class="btn btn-xs btn-outline-warning btn-sm py-0 px-1 dash-reopen-btn"
                                                data-inspection-id="{{ $insp->id }}"
                                                title="Rouvrir cette inspection"
                                                onclick="dashToggleComplete({{ $insp->id }}, this)">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                        @elseif($insp->findings_count > 0)
                                        <button class="btn btn-xs btn-outline-success btn-sm py-0 px-1 dash-complete-btn fw-semibold"
                                                data-inspection-id="{{ $insp->id }}"
                                                title="Marquer comme terminée"
                                                onclick="dashToggleComplete({{ $insp->id }}, this)">
                                            <i class="bi bi-check-all"></i>
                                        </button>
                                        @endif
                                        @else
                                        @php
                                            $canEdit = !($facilityStartedIds ?? collect())->contains($insp->id)
                                                    && !($processStartedIds ?? collect())->contains($insp->id);
                                        @endphp
                                        @if ($canEdit)
                                        <button class="btn btn-xs btn-outline-warning btn-sm py-0 px-1"
                                                title="Modifier les informations de l'inspection"
                                                onclick="openEditInspectionModal({{ $insp->id }}, '{{ addslashes($insp->inspection_name ?? '') }}', '{{ $insp->date_scheduled }}', {{ $insp->qa_inspector_id ?? 'null' }}, '{{ $insp->type_inspection }}', '{{ $insp->facility_location ?? '' }}')">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        @endif
                                        @if ($insp->type_inspection === 'Facility Inspection')
                                        <button class="btn btn-xs btn-outline-primary btn-sm py-0 px-1 fw-semibold"
                                                title="Remplir le Facility Inspection Checklist"
                                                onclick="openDashboardChecklistModal({{ $insp->id }}, '{{ addslashes($insp->inspection_name ?? $insp->type_inspection) }}', 'Facility Inspection', '{{ $insp->facility_location ?? 'cotonou' }}')">
                                            <i class="bi bi-building-check"></i>
                                        </button>
                                        @elseif ($insp->type_inspection === 'Process Inspection')
                                        <button class="btn btn-xs btn-outline-primary btn-sm py-0 px-1 fw-semibold"
                                                title="Remplir le Process Inspection Checklist"
                                                onclick="openDashboardChecklistModal({{ $insp->id }}, '{{ addslashes($insp->inspection_name ?? $insp->type_inspection) }}', 'Process Inspection', '')">
                                            <i class="bi bi-gear-wide-connected"></i>
                                        </button>
                                        @elseif ($insp->checklist_slug)
                                        <a href="{{ route('checklist.show', [$insp->id, $insp->checklist_slug]) }}"
                                           class="btn btn-xs btn-outline-primary btn-sm py-0 px-1 fw-semibold"
                                           title="Remplir le formulaire d'inspection">
                                            <i class="bi bi-clipboard2-check"></i>
                                        </a>
                                        @else
                                        <a href="{{ route('checklist.index', $insp->id) }}"
                                           class="btn btn-xs btn-outline-primary btn-sm py-0 px-1 fw-semibold"
                                           title="Remplir le formulaire d'inspection">
                                            <i class="bi bi-clipboard2-check"></i>
                                        </a>
                                        @endif
                                        @php
                                            $canMarkDone = match($insp->type_inspection) {
                                                'Facility Inspection' => ($facilityReadyIds ?? collect())->contains($insp->id),
                                                'Process Inspection'  => ($processReadyIds ?? collect())->contains($insp->id),
                                                default               => true,
                                            };
                                        @endphp
                                        @if($canMarkDone)
                                        <button class="btn btn-xs btn-outline-success btn-sm py-0 px-1 fw-semibold"
                                                title="Marquer comme finalisé"
                                                onclick="markInspectionDone({{ $insp->id }}, this)">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                        @endif
                                        @endif
                                        <button class="btn btn-xs btn-outline-danger btn-sm py-0 px-1"
                                                title="Delete"
                                                onclick="deleteInspection({{ $insp->id }}, this)">
                                            <i class="bi bi-trash"></i>
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

        {{-- Findings Panel --}}
        <div class="col-lg-5">
            <div class="findings-panel p-3">
                <div id="findingsPanelHeader" class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-list-check me-2 text-danger"></i>
                        <span id="findingsPanelTitle">Select an inspection to see its findings</span>
                        <span id="conformityBadge" class="ms-2"></span>
                    </h6>
                    <div id="findingsPanelActions" class="d-none">
                        <button class="btn btn-qa-primary btn-sm" onclick="openAddFindingModal()">
                            <i class="bi bi-plus me-1"></i>Add Finding
                        </button>
                    </div>
                </div>
                <div id="findingsPanelBody">
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-arrow-left-circle fs-2 d-block mb-2 opacity-50"></i>
                        Click on an inspection row to view its findings.
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Calendar ─────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mt-4 mb-4">
        <div class="card-header-qa d-flex align-items-center justify-content-between">
            <span><i class="bi bi-calendar3 me-2"></i>Calendrier des Inspections</span>
            <div class="d-flex gap-2 align-items-center flex-wrap" style="font-size:.78rem;">
                <span><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#0d6efd;margin-right:4px;"></span>Facility</span>
                <span><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#6f42c1;margin-right:4px;"></span>Process</span>
                <span><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#198754;margin-right:4px;"></span>Study</span>
                <span><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#C10202;margin-right:4px;"></span>Critical Phase</span>
                <span><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#adb5bd;margin-right:4px;"></span>Complétée</span>
            </div>
        </div>
        <div class="card-body p-3">
            <div id="qa-calendar"></div>
        </div>
    </div>

</div>

{{-- ── Calendar tooltip ── --}}
<div id="cal-tooltip" style="display:none;position:fixed;z-index:9999;background:#fff;border:1px solid #dee2e6;border-radius:8px;padding:10px 14px;font-size:.83rem;box-shadow:0 4px 16px rgba(0,0,0,.15);max-width:260px;pointer-events:none;"></div>

{{-- ══════════════════════════════════════════════════════
     SCHEDULE INSPECTION MODAL
══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="scheduleInspectionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(90deg,var(--qa-brand),var(--qa-brand-dark));color:#fff;">
                <h5 class="modal-title fw-bold"><i class="bi bi-calendar-plus me-2"></i>Schedule QA Inspection</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="scheduleErr" class="alert alert-danger d-none small py-2 mb-3"></div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Inspection Type <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="si-type" onchange="onTypeChange()">
                            @foreach($inspectionTypes as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6" id="si-project-row" style="display:none;">
                        <label class="form-label fw-semibold small">Project <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="si-project" onchange="loadCriticalActivities()">
                            <option value="">— Select project —</option>
                            @foreach($all_projects as $proj)
                            <option value="{{ $proj->id }}"
                                    {{ !$proj->archived_at ? '' : 'disabled' }}
                                    data-archived="{{ $proj->archived_at ? '1' : '0' }}">
                                {{ $proj->project_code }}{{ $proj->archived_at ? ' (archived)' : '' }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12" id="si-activity-row" style="display:none;">
                        <label class="form-label fw-semibold small">Link to Critical Activity (optional)</label>
                        <select class="form-select form-select-sm" id="si-activity">
                            <option value="">— None —</option>
                        </select>
                    </div>
                    <div class="col-md-12" id="si-facility-location-row" style="display:none;">
                        <label class="form-label fw-semibold small">Facility Site <span class="text-danger">*</span></label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="siFacilityLocation"
                                       id="siLocCotonou" value="cotonou" checked>
                                <label class="form-check-label small" for="siLocCotonou">
                                    <strong>Cotonou</strong> — Main Facility
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="siFacilityLocation"
                                       id="siLocCove" value="cove">
                                <label class="form-check-label small" for="siLocCove">
                                    <strong>Covè</strong> — Field Site
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">QA Inspector <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="si-inspector">
                            <option value="">— Select —</option>
                            @foreach($all_personnels as $p)
                            <option value="{{ $p->id }}" {{ $p->id == $qaManagerDefaultId ? 'selected' : '' }}>
                                {{ $p->prenom }} {{ $p->nom }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Scheduled Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-sm" id="si-date">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-semibold small">Inspection Name (optional)</label>
                        <input type="text" class="form-control form-control-sm" id="si-name"
                               placeholder="Auto-generated if left blank">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-qa-primary btn-sm fw-semibold" onclick="submitSchedule()" id="siSaveBtn">
                    <i class="bi bi-calendar-check me-1"></i>Schedule
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     ADD FINDING MODAL
══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="addFindingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(90deg,var(--qa-brand),var(--qa-brand-dark));color:#fff;">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>Add QA Finding</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="findingErr" class="alert alert-danger d-none small py-2 mb-3"></div>
                <div class="mb-3">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="af-is-conformity"
                               onchange="toggleConformityFields()">
                        <label class="form-check-label fw-semibold small" for="af-is-conformity">
                            This is a conformity (no issue)
                        </label>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-semibold small">Observation / Finding <span class="text-danger">*</span></label>
                    <textarea class="form-control form-control-sm" id="af-text" rows="3" maxlength="2000"></textarea>
                </div>
                <div id="af-nonconformity-fields">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Assigned To <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="af-assigned">
                                <option value="">— Select person —</option>
                                @foreach($all_personnels as $p)
                                <option value="{{ $p->id }}">{{ $p->prenom }} {{ $p->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Deadline</label>
                            <input type="date" class="form-control form-control-sm" id="af-deadline">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-qa-primary btn-sm fw-semibold" onclick="submitFinding()" id="afSaveBtn">
                    <i class="bi bi-save me-1"></i>Save Finding
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     RESOLVE FINDING MODAL
══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="resolveFindingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(90deg,#198754,#146c43);color:#fff;">
                <h5 class="modal-title fw-bold"><i class="bi bi-check2-circle me-2"></i>Resolve Finding</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="resolveErr" class="alert alert-danger d-none small py-2 mb-3"></div>
                <div class="mb-2">
                    <label class="form-label fw-semibold small">Action Point / Corrective Action <span class="text-danger">*</span></label>
                    <textarea class="form-control form-control-sm" id="resolve-action" rows="3" maxlength="2000"></textarea>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-semibold small">Means of Verification</label>
                    <textarea class="form-control form-control-sm" id="resolve-mov" rows="2" maxlength="2000"
                              placeholder="How will you verify the corrective action was implemented?"></textarea>
                </div>
                <div class="row g-2 mt-1">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Date de résolution <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-sm" id="resolve-date">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Résolu par (optionnel)</label>
                        <input type="text" class="form-control form-control-sm" id="resolve-by-name"
                               placeholder="Nom de la personne">
                    </div>
                </div>
                <input type="hidden" id="resolve-finding-id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success btn-sm fw-semibold" onclick="submitResolve()" id="resolveSaveBtn">
                    <i class="bi bi-check-circle me-1"></i>Mark as Resolved
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     EDIT FINDING MODAL
══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="editFindingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(90deg,#0d6efd,#0a58ca);color:#fff;">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Finding</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="editFindingErr" class="alert alert-danger d-none small py-2 mb-3"></div>
                <input type="hidden" id="ef-finding-id">
                <div class="mb-2">
                    <label class="form-label fw-semibold small">Observation / Finding <span class="text-danger">*</span></label>
                    <textarea class="form-control form-control-sm" id="ef-finding-text" rows="3" maxlength="2000"></textarea>
                </div>
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Assigned To</label>
                        <select class="form-select form-select-sm" id="ef-assigned-to">
                            <option value="">— Select person —</option>
                            @foreach($all_personnels as $p)
                            <option value="{{ $p->id }}">{{ $p->prenom }} {{ $p->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Deadline</label>
                        <input type="date" class="form-control form-control-sm" id="ef-deadline">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm fw-semibold" onclick="submitEditFinding()" id="efSaveBtn">
                    <i class="bi bi-save me-1"></i>Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function(){
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const QA_MANAGER_ID = {{ $qaManagerDefaultId ?? 'null' }};

    // ── Select2 init (deferred until jQuery is ready) ────────
    function initSelect2(selector, opts) {
        $(selector).select2(Object.assign({
            theme: 'bootstrap-5',
            width: '100%',
            allowClear: true,
        }, opts || {}));
    }

    function bootSelect2() {
        // Filter bar
        initSelect2('#filter-project',  { placeholder: 'All projects', minimumResultsForSearch: 4 });
        initSelect2('#filter-type',     { placeholder: 'All types',    minimumResultsForSearch: 0 });
        initSelect2('#filter-status',   { placeholder: 'All',          minimumResultsForSearch: Infinity });

        // Schedule modal
        initSelect2('#si-type',      { placeholder: 'Select type',       dropdownParent: $('#scheduleInspectionModal'), minimumResultsForSearch: Infinity });
        initSelect2('#si-project',   { placeholder: '— Select project —', dropdownParent: $('#scheduleInspectionModal') });
        initSelect2('#si-activity',  { placeholder: '— None —',           dropdownParent: $('#scheduleInspectionModal') });
        initSelect2('#si-inspector', { placeholder: '— Select —',         dropdownParent: $('#scheduleInspectionModal') });

        // Add Finding modal
        initSelect2('#af-assigned', { placeholder: '— Select person —', dropdownParent: $('#addFindingModal') });

        // Edit Finding modal
        initSelect2('#ef-assigned-to', { placeholder: '— Select person —', dropdownParent: $('#editFindingModal') });

        // Sync Select2 change events to original handlers
        $('#si-type').on('change', function() { window.onTypeChange(); });
        $('#si-project').on('change', function() { window.loadCriticalActivities(); });
    }

    // jQuery may load after @yield('content') — poll until available
    (function waitForJQuery() {
        if (typeof window.jQuery !== 'undefined') {
            jQuery(document).ready(bootSelect2);
        } else {
            setTimeout(waitForJQuery, 30);
        }
    })();

    let selectedInspectionId        = null;
    let selectedInspectionProjectId = null;
    let selectedInspectionType      = null;
    let selectedFacilityLocation    = null;
    let selectedInspectionCompleted = false;
    let currentFacilitySection      = null;

    // ── Select inspection row ────────────────────────────────
    window.selectInspection = function(id, name, projectId, type, facilityLocation, isCompleted = false) {
        document.querySelectorAll('.insp-row').forEach(r => r.classList.remove('selected'));
        const row = document.querySelector(`.insp-row[data-id="${id}"]`);
        if (row) row.classList.add('selected');

        selectedInspectionId        = id;
        selectedInspectionProjectId = projectId;
        selectedInspectionType      = type;
        selectedFacilityLocation    = facilityLocation || null;
        selectedInspectionCompleted = isCompleted;

        document.getElementById('findingsPanelTitle').textContent = name;

        // For facility/process inspections, hide the global "Add Finding" button (each section has its own)
        // Also hide if the inspection is marked as completed
        const actionsEl = document.getElementById('findingsPanelActions');
        if (type === 'Facility Inspection' || type === 'Process Inspection' || isCompleted) {
            actionsEl.classList.add('d-none');
        } else {
            actionsEl.classList.remove('d-none');
        }

        loadFindings(id);
    };

    // ── Toggle inspection completed ───────────────────────────
    window.dashToggleComplete = function(inspectionId, btn) {
        const isReopening = btn.classList.contains('dash-reopen-btn');
        const label = isReopening ? 'reopen' : 'mark as completed';
        if (!confirm('Are you sure you want to ' + label + ' this inspection?')) return;

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
                alert(data.message || 'Error.');
                btn.disabled = false;
            }
        })
        .catch(() => { alert('Network error.'); btn.disabled = false; });
    };

    // ── Load findings ────────────────────────────────────────
    function loadFindings(inspectionId) {
        const body = document.getElementById('findingsPanelBody');
        body.innerHTML = '<div class="text-center py-4"><div class="spinner-border spinner-border-sm text-danger"></div></div>';

        fetch(`{{ url('/ajax/get-inspection-findings') }}?inspection_id=${inspectionId}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) { body.innerHTML = `<p class="text-danger small">${data.message}</p>`; return; }
            const findings = data.findings;
            if (data.is_facility) {
                renderSectionedFindings(findings, FACILITY_SECTIONS_META[data.facility_location] || FACILITY_SECTIONS_META.cotonou, data.sections_done || []);
            } else if (data.is_process) {
                renderSectionedFindings(findings, PROCESS_SECTIONS_META, data.sections_done || []);
            } else {
                renderFindings(findings);
            }
            // Conformity indicator
            const allNonConform = findings.filter(f => !f.is_conformity);
            const isConformant = allNonConform.length === 0 || allNonConform.every(f => f.status === 'complete');
            const conformBadge = document.getElementById('conformityBadge');
            if (conformBadge) {
                if (isConformant) {
                    conformBadge.innerHTML = '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Conforme</span>';
                } else {
                    const pending = allNonConform.filter(f => f.status !== 'complete').length;
                    conformBadge.innerHTML = `<span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle me-1"></i>${pending} non-conformité(s) en attente</span>`;
                }
            }
        })
        .catch(() => { body.innerHTML = '<p class="text-danger small">Network error.</p>'; });
    }

    function renderFindings(findings) {
        const body = document.getElementById('findingsPanelBody');
        if (!findings.length) {
            body.innerHTML = '<p class="text-muted small text-center py-4">No findings recorded yet. Click "Add Finding" to start.</p>';
            return;
        }

        let html = '<div class="d-flex flex-column gap-2">';
        findings.forEach(f => { html += buildFindingCardHtml(f); });
        html += '</div>';
        body.innerHTML = html;
    }

    function renderSectionedFindings(findings, meta, sectionsDone) {
        const body = document.getElementById('findingsPanelBody');

        // Group findings by facility_section slug
        const bySection = {};
        findings.forEach(f => {
            const s = f.facility_section || '__none__';
            if (!bySection[s]) bySection[s] = [];
            bySection[s].push(f);
        });

        let html = '<div class="accordion" id="facilityFindingsAccordion">';

        meta.forEach((sec, idx) => {
            // sections_done stores the letter only ('a','b'…), slug is 'process-a','facility-a','facility-cove-a'
            const sectionLetter = sec.slug.split('-').pop();
            const isDone    = Array.isArray(sectionsDone) && sectionsDone.includes(sectionLetter);
            const secFinds  = bySection[sec.slug] || [];
            const pendingN  = secFinds.filter(f => !f.is_conformity && f.status !== 'complete').length;
            const collapseId = `fac-sec-${idx}`;

            let statusBadge = '';
            if (!isDone) {
                statusBadge = '<span class="badge bg-secondary ms-2" style="font-size:.7rem;">Non inspectée</span>';
            } else if (pendingN > 0) {
                statusBadge = `<span class="badge bg-danger ms-2" style="font-size:.7rem;">${pendingN} finding${pendingN>1?'s':''}</span>`;
            } else {
                statusBadge = '<span class="badge bg-success ms-2" style="font-size:.7rem;">Conforme</span>';
            }

            const sectionUrl = `/checklist/${selectedInspectionId}/${sec.slug}`;

            html += `
            <div class="accordion-item border mb-2 rounded-3 overflow-hidden">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed py-2 px-3" type="button"
                            data-bs-toggle="collapse" data-bs-target="#${collapseId}" style="font-size:.88rem;">
                        <span class="badge rounded-pill me-2" style="background:var(--qa-brand); font-size:.78rem;">${sec.letter}</span>
                        <span class="fw-semibold">${escHtml(sec.title)}</span>
                        ${statusBadge}
                    </button>
                </h2>
                <div id="${collapseId}" class="accordion-collapse collapse">
                    <div class="accordion-body p-2">`;

            if (!isDone) {
                html += `
                <div class="text-center py-2">
                    <p class="text-muted small mb-2"><i class="bi bi-clock me-1"></i>Cette unité n'a pas encore été inspectée.</p>
                    <a href="${sectionUrl}" class="btn btn-sm btn-outline-primary rounded-3" style="font-size:.78rem;" target="_blank">
                        <i class="bi bi-pencil-square me-1"></i>Remplir le formulaire
                    </a>
                </div>`;
            } else {
                // Action buttons for filled section
                html += `<div class="d-flex gap-2 justify-content-end mb-2">
                    <a href="${sectionUrl}" class="btn btn-sm btn-outline-secondary rounded-3" style="font-size:.78rem;" target="_blank">
                        <i class="bi bi-pencil-square me-1"></i>Modifier
                    </a>
                    <button class="btn btn-sm btn-outline-danger rounded-3" style="font-size:.78rem;"
                            onclick="openAddFindingForSection('${sec.slug}')">
                        <i class="bi bi-plus me-1"></i>Ajouter un finding
                    </button>
                </div>`;

                if (!secFinds.length) {
                    html += '<p class="text-muted small text-center py-2 mb-0"><i class="bi bi-check-circle me-1 text-success"></i>Aucun finding pour cette section.</p>';
                } else {
                    html += '<div class="d-flex flex-column gap-2">';
                    secFinds.forEach(f => { html += buildFindingCardHtml(f); });
                    html += '</div>';
                }
            }

            html += `   </div>
                </div>
            </div>`;
        });

        // Findings without a section (edge case)
        const orphans = bySection['__none__'] || [];
        if (orphans.length) {
            html += '<div class="mt-2"><p class="small text-muted fw-semibold mb-1">Autres findings</p><div class="d-flex flex-column gap-2">';
            orphans.forEach(f => { html += buildFindingCardHtml(f); });
            html += '</div></div>';
        }

        html += '</div>';
        body.innerHTML = html;
    }

    function buildFindingCardHtml(f) {
        const isConform  = f.is_conformity;
        const isResolved = f.status === 'complete';
        const cls = isConform ? 'conform' : (isResolved ? 'resolved' : 'pending');

        const badge = isConform
            ? '<span class="badge bg-secondary ms-1">Conformity</span>'
            : (isResolved
                ? '<span class="badge bg-success ms-1">Resolved</span>'
                : '<span class="badge bg-warning text-dark ms-1">Pending</span>');

        let actionHtml = '';
        if (!isConform && !isResolved) {
            actionHtml += `<button class="btn btn-xs btn-outline-success btn-sm py-0 px-1 ms-1" onclick="openResolveModal(${f.id})" title="Resolve"><i class="bi bi-check2"></i></button>`;
        }
        actionHtml += `<button class="btn btn-xs btn-outline-primary btn-sm py-0 px-1 ms-1" onclick="openEditFindingModal(${f.id})" title="Edit"><i class="bi bi-pencil"></i></button>`;
        actionHtml += `<button class="btn btn-xs btn-outline-danger btn-sm py-0 px-1 ms-1" onclick="deleteFinding(${f.id}, this)" title="Delete"><i class="bi bi-trash"></i></button>`;

        return `
    <div class="finding-card ${cls}" id="fc-${f.id}"
         data-finding-text="${escHtml(f.finding_text)}"
         data-assigned-to-id="${f.assigned_to_id || ''}"
         data-deadline="${f.deadline_date || ''}">
        <div class="d-flex align-items-start justify-content-between gap-2">
            <div class="flex-grow-1">
                <div>${escHtml(f.finding_text)}${badge}</div>
                ${f.assigned_to_name ? `<div class="text-muted small mt-1"><i class="bi bi-person me-1"></i>${escHtml(f.assigned_to_name)}${f.deadline_date ? ' · <i class="bi bi-calendar3"></i> '+f.deadline_date : ''}</div>` : ''}
                ${isResolved && f.action_point ? `<div class="text-success small mt-1"><i class="bi bi-check2-circle me-1"></i>${escHtml(f.action_point)}</div>` : ''}
                ${isResolved ? `<div class="text-muted small mt-1" style="font-size:.78rem;">${f.meeting_date ? '<i class="bi bi-calendar-check me-1"></i>'+f.meeting_date : ''}${f.resolved_by_name ? (f.meeting_date ? ' · ' : '') + '<i class="bi bi-person-check me-1"></i>'+escHtml(f.resolved_by_name) : ''}</div>` : ''}
            </div>
            <div class="text-nowrap">${actionHtml}</div>
        </div>
    </div>`;
    }

    window.openAddFindingForSection = function(sectionSlug) {
        currentFacilitySection = sectionSlug;
        openAddFindingModal();
    };

    function escHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    // ── Schedule Inspection Modal ────────────────────────────
    window.openScheduleModal = function() {
        document.getElementById('scheduleErr').classList.add('d-none');
        $('#si-project').val('').trigger('change');
        $('#si-type').val('{{ $inspectionTypes[0] ?? "Facility Inspection" }}').trigger('change.select2');
        document.getElementById('si-date').value = '';
        document.getElementById('si-name').value = '';
        $('#si-activity').empty().append('<option value="">— None —</option>').trigger('change.select2');
        document.getElementById('si-project-row').style.display  = 'none';
        document.getElementById('si-activity-row').style.display = 'none';
        if (QA_MANAGER_ID) $('#si-inspector').val(QA_MANAGER_ID).trigger('change.select2');
        new bootstrap.Modal(document.getElementById('scheduleInspectionModal')).show();
    };

    window.onTypeChange = function() {
        const type       = $('#si-type').val();
        const isCritical = type === 'Critical Phase Inspection';
        const isFacility = type === 'Facility Inspection';
        document.getElementById('si-project-row').style.display           = isCritical ? '' : 'none';
        document.getElementById('si-activity-row').style.display          = isCritical ? '' : 'none';
        document.getElementById('si-facility-location-row').style.display = isFacility ? '' : 'none';
        if (!isCritical) {
            $('#si-project').val('').trigger('change.select2');
            $('#si-activity').empty().append('<option value="">— None —</option>').trigger('change.select2');
        }
    };

    window.loadCriticalActivities = function() {
        const projectId = $('#si-project').val();
        const $sel = $('#si-activity');
        $sel.empty().append('<option value="">— None —</option>');
        if (!projectId) return;

        fetch(`{{ route('qaDashboard.criticalActivities') }}?project_id=${projectId}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            (data.activities || []).forEach(a => {
                $sel.append(new Option(
                    a.study_activity_name + (a.estimated_activity_date ? ' (' + a.estimated_activity_date + ')' : '') + (a.status === 'completed' ? ' ✓' : ''),
                    a.id
                ));
            });
            $sel.trigger('change.select2');
        });
    };

    window.submitSchedule = function() {
        const errDiv  = document.getElementById('scheduleErr');
        const project = $('#si-project').val();
        const type    = $('#si-type').val();
        const insp    = $('#si-inspector').val();
        const date    = document.getElementById('si-date').value;

        const isCritical = type === 'Critical Phase Inspection';
        if ((isCritical && !project) || !type || !insp || !date) {
            errDiv.textContent = isCritical
                ? 'Project, type, inspector and date are required.'
                : 'Type, inspector and date are required.';
            errDiv.classList.remove('d-none');
            return;
        }
        errDiv.classList.add('d-none');

        const btn = document.getElementById('siSaveBtn');
        btn.disabled = true;

        const locEl = document.querySelector('input[name="siFacilityLocation"]:checked');
        const body = {
            project_id:        project,
            type_inspection:   type,
            qa_inspector_id:   insp,
            date_scheduled:    date,
            inspection_name:   document.getElementById('si-name').value.trim() || null,
            activity_id:       document.getElementById('si-activity').value || null,
            facility_location: type === 'Facility Inspection' ? (locEl ? locEl.value : 'cotonou') : null,
        };

        fetch('{{ route("scheduleQaInspection") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify(body),
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('scheduleInspectionModal')).hide();
                location.reload();
            } else {
                errDiv.textContent = data.message;
                errDiv.classList.remove('d-none');
            }
        })
        .catch(() => { btn.disabled = false; errDiv.textContent = 'Network error.'; errDiv.classList.remove('d-none'); });
    };

    // ── Add Finding Modal ────────────────────────────────────
    window.openAddFindingModal = function() {
        if (!selectedInspectionId) return;
        document.getElementById('findingErr').classList.add('d-none');
        document.getElementById('af-text').value  = '';
        $('#af-assigned').val('').trigger('change.select2');
        document.getElementById('af-deadline').value = '';
        document.getElementById('af-is-conformity').checked = false;
        document.getElementById('af-nonconformity-fields').style.display = '';
        const modal = new bootstrap.Modal(document.getElementById('addFindingModal'));
        document.getElementById('addFindingModal').addEventListener('hidden.bs.modal', () => {
            currentFacilitySection = null;
        }, { once: true });
        modal.show();
    };

    window.toggleConformityFields = function() {
        const isConform = document.getElementById('af-is-conformity').checked;
        document.getElementById('af-nonconformity-fields').style.display = isConform ? 'none' : '';
    };

    window.submitFinding = function() {
        const errDiv = document.getElementById('findingErr');
        const text   = document.getElementById('af-text').value.trim();
        const isConform = document.getElementById('af-is-conformity').checked;
        const assigned  = $('#af-assigned').val();

        if (!text) {
            errDiv.textContent = 'Observation text is required.';
            errDiv.classList.remove('d-none');
            return;
        }
        if (!isConform && !assigned) {
            errDiv.textContent = 'Assigned person is required for non-conformities.';
            errDiv.classList.remove('d-none');
            return;
        }
        errDiv.classList.add('d-none');

        const btn = document.getElementById('afSaveBtn');
        btn.disabled = true;

        const isFacilityInsp = (selectedInspectionType === 'Facility Inspection');

        const payload = {
            inspection_id:    selectedInspectionId,
            finding_text:     text,
            is_conformity:    isConform ? 1 : 0,
            assigned_to:      assigned || null,
            deadline_date:    document.getElementById('af-deadline').value || null,
        };
        if (!isFacilityInsp) {
            payload.project_id = selectedInspectionProjectId;
        }
        if (isFacilityInsp && currentFacilitySection) {
            payload.facility_section = currentFacilitySection;
        }

        fetch('{{ route("saveQaFinding") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('addFindingModal')).hide();
                currentFacilitySection = null;
                loadFindings(selectedInspectionId);
                // Update findings count badge in row
                const row = document.querySelector(`.insp-row[data-id="${selectedInspectionId}"] td:nth-last-child(2) .badge`);
                if (row) row.textContent = parseInt(row.textContent || 0) + 1;
            } else {
                errDiv.textContent = data.message;
                errDiv.classList.remove('d-none');
            }
        })
        .catch(() => { btn.disabled = false; errDiv.textContent = 'Network error.'; errDiv.classList.remove('d-none'); });
    };

    // ── Resolve Finding Modal ────────────────────────────────
    window.openResolveModal = function(findingId) {
        document.getElementById('resolve-finding-id').value = findingId;
        document.getElementById('resolve-action').value     = '';
        document.getElementById('resolve-mov').value        = '';
        document.getElementById('resolve-by-name').value    = '';
        // Default resolution date = today
        document.getElementById('resolve-date').value = new Date().toISOString().split('T')[0];
        document.getElementById('resolveErr').classList.add('d-none');
        new bootstrap.Modal(document.getElementById('resolveFindingModal')).show();
    };

    window.submitResolve = function() {
        const errDiv      = document.getElementById('resolveErr');
        const findId      = document.getElementById('resolve-finding-id').value;
        const action      = document.getElementById('resolve-action').value.trim();
        const mov         = document.getElementById('resolve-mov').value.trim();
        const resolveDate = document.getElementById('resolve-date').value;
        const resolveBy   = document.getElementById('resolve-by-name').value.trim();

        if (!action) {
            errDiv.textContent = 'Action point is required.';
            errDiv.classList.remove('d-none');
            return;
        }
        if (!resolveDate) {
            errDiv.textContent = 'La date de résolution est requise.';
            errDiv.classList.remove('d-none');
            return;
        }
        errDiv.classList.add('d-none');

        const btn = document.getElementById('resolveSaveBtn');
        btn.disabled = true;

        fetch('{{ route("resolveQaFinding") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ finding_id: findId, action_point: action, means_of_verification: mov, resolved_date: resolveDate, resolved_by_name: resolveBy || null }),
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('resolveFindingModal')).hide();
                loadFindings(selectedInspectionId);
            } else {
                errDiv.textContent = data.message;
                errDiv.classList.remove('d-none');
            }
        })
        .catch(() => { btn.disabled = false; errDiv.textContent = 'Network error.'; errDiv.classList.remove('d-none'); });
    };

    // ── Edit Finding ─────────────────────────────────────
    window.openEditFindingModal = function(findingId) {
        const card = document.getElementById('fc-' + findingId);
        if (!card) return;
        document.getElementById('ef-finding-id').value    = findingId;
        document.getElementById('ef-finding-text').value  = card.dataset.findingText || '';
        document.getElementById('ef-deadline').value      = card.dataset.deadline || '';
        const assignedId = card.dataset.assignedToId;
        if (assignedId) {
            $('#ef-assigned-to').val(assignedId).trigger('change.select2');
        } else {
            $('#ef-assigned-to').val('').trigger('change.select2');
        }
        document.getElementById('editFindingErr').classList.add('d-none');
        new bootstrap.Modal(document.getElementById('editFindingModal')).show();
    };

    window.submitEditFinding = function() {
        const findingId  = document.getElementById('ef-finding-id').value;
        const text       = document.getElementById('ef-finding-text').value.trim();
        const assignedTo = $('#ef-assigned-to').val();
        const deadline   = document.getElementById('ef-deadline').value;
        const errDiv     = document.getElementById('editFindingErr');

        if (!text) { errDiv.textContent = 'Finding text is required.'; errDiv.classList.remove('d-none'); return; }
        errDiv.classList.add('d-none');

        const btn = document.getElementById('efSaveBtn');
        btn.disabled = true;

        fetch('{{ route("updateQaFinding") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ finding_id: findingId, finding_text: text, assigned_to: assignedTo || null, deadline_date: deadline || null }),
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('editFindingModal')).hide();
                loadFindings(selectedInspectionId);
            } else {
                errDiv.textContent = data.message;
                errDiv.classList.remove('d-none');
            }
        })
        .catch(() => { btn.disabled = false; errDiv.textContent = 'Network error.'; errDiv.classList.remove('d-none'); });
    };

    // ── Delete Finding ───────────────────────────────────────
    window.deleteFinding = function(findingId, btn) {
        if (!confirm('Delete this finding?')) return;
        btn.disabled = true;
        fetch('{{ route("deleteQaFinding") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ finding_id: findingId }),
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            if (data.success) {
                document.getElementById('fc-' + findingId)?.remove();
                // Update count
                const row = document.querySelector(`.insp-row[data-id="${selectedInspectionId}"] td:nth-last-child(2) .badge`);
                if (row) row.textContent = Math.max(0, parseInt(row.textContent || 1) - 1);
            } else alert(data.message);
        });
    };

    // ── Mark Inspection Done ─────────────────────────────────
    window.markInspectionDone = function(inspectionId, btn) {
        if (!confirm('Mark this inspection as completed today? This action cannot be undone from this page.')) return;
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

    // ── Delete Inspection ────────────────────────────────────
    window.deleteInspection = function(inspectionId, btn) {
        if (!confirm('Delete this inspection and all its findings?')) return;
        btn.disabled = true;
        fetch('{{ route("deleteQaInspection") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ inspection_id: inspectionId }),
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            if (data.success) {
                document.querySelector(`.insp-row[data-id="${inspectionId}"]`)?.remove();
                if (selectedInspectionId === inspectionId) {
                    selectedInspectionId = null;
                    document.getElementById('findingsPanelTitle').textContent = 'Select an inspection';
                    document.getElementById('findingsPanelActions').classList.add('d-none');
                    document.getElementById('findingsPanelBody').innerHTML = '';
                }
            } else alert(data.message);
        });
    };

    // ── Facility / Process / Checklist Picker Modal ──────────
    const PROCESS_SECTIONS_META = [
        { slug: 'process-a', letter: 'A', title: 'Equipment Reception, Installation and Management', count: 24 },
        { slug: 'process-b', letter: 'B', title: 'Test Item Reception, Storage and Management', count: 20 },
        { slug: 'process-c', letter: 'C', title: 'Test System Request, Production, Supply and Management', count: 31 },
        { slug: 'process-d', letter: 'D', title: 'Computerized system Reception, registration, validation and maintenance', count: 25 },
        { slug: 'process-e', letter: 'E', title: 'Safety Procedures', count: 11 },
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

    window.openDashboardChecklistModal = function(inspectionId, inspectionName, inspectionType, facilityLocation) {
        document.getElementById('dashPickerSubtitle').textContent = inspectionName;

        const grid         = document.getElementById('dashPickerGrid');
        const progressArea = document.getElementById('dashFacilityProgressArea');

        progressArea.style.display = 'block';
        grid.innerHTML = '<div class="col-12 text-center text-muted py-3"><span class="spinner-border spinner-border-sm me-2"></span>Chargement…</div>';

        let facilityMeta = FACILITY_SECTIONS_META[facilityLocation || 'cotonou'] || FACILITY_SECTIONS_META.cotonou;
        if (inspectionType === 'Process Inspection') { facilityMeta = PROCESS_SECTIONS_META; }
        const progressKey = inspectionType === 'Process Inspection' ? 'process_progress' : 'facility_progress';
        fetch(`{{ url('/ajax/get-checklist-statuses') }}?inspection_id=${inspectionId}`)
            .then(r => r.json())
            .then(data => {
                const statuses = data.statuses || {};
                const done     = statuses[progressKey] ?? 0;
                const total    = facilityMeta.length;
                const pct      = total > 0 ? Math.round(done / total * 100) : 0;

                const bar   = document.getElementById('dashFacilityBar');
                const label = document.getElementById('dashFacilityLabel');
                const alert = document.getElementById('dashFacilityAlert');
                bar.style.width = pct + '%';
                bar.className   = 'progress-bar ' + (done >= total ? 'bg-success' : 'bg-warning');
                label.textContent = `${done}/${total} (${pct}%)`;
                if (done >= total) {
                    alert.className = 'alert alert-success d-flex align-items-center gap-2 py-2 px-3 mb-0 small';
                    alert.innerHTML = '<i class="bi bi-check-circle-fill flex-shrink-0"></i><span>Toutes les sections sont complétées.</span>';
                } else {
                    alert.className = 'alert alert-warning d-flex align-items-center gap-2 py-2 px-3 mb-0 small';
                    alert.innerHTML = `<i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i><span>Il reste <strong>${total - done}</strong> section(s) à compléter.</span>`;
                }

                grid.innerHTML = facilityMeta.map(f => {
                    const filled = statuses[f.slug] === true;
                    const badge  = filled
                        ? '<span class="badge rounded-pill bg-success" style="font-size:.7rem;">Rempli</span>'
                        : '<span class="badge rounded-pill bg-warning text-dark" style="font-size:.7rem;">À compléter</span>';
                    const border = filled ? 'border-color:#198754!important;border-width:2px!important;' : '';
                    return `
                    <div class="col-md-6 col-lg-4">
                        <a href="/checklist/${inspectionId}/${f.slug}" class="text-decoration-none">
                            <div style="border-radius:14px;border:1px solid #e0e0e0;background:#fff;padding:.75rem;display:flex;align-items:center;gap:.75rem;${border}transition:box-shadow .2s;">
                                <div style="width:38px;height:38px;background:linear-gradient(135deg,#C10202,#8b0001);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;flex-shrink:0;">${f.letter}</div>
                                <div class="flex-grow-1">
                                    <div style="font-size:.85rem;font-weight:600;color:#1a1a1a;">${escHtml(f.title)}</div>
                                    <div style="font-size:.72rem;color:#999;">${f.count} questions</div>
                                </div>
                                <div class="d-flex flex-column align-items-end gap-1">${badge}<i class="bi bi-chevron-right text-muted" style="font-size:.75rem;"></i></div>
                            </div>
                        </a>
                    </div>`;
                }).join('');
            })
            .catch(() => {
                grid.innerHTML = '<div class="col-12 text-center text-danger py-3">Erreur de chargement.</div>';
            });

        new bootstrap.Modal(document.getElementById('dashChecklistPickerModal')).show();
    };

})();
</script>

{{-- ══════════════════════════════════════════════════════
     CHECKLIST PICKER MODAL (Facility Inspection)
══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="dashChecklistPickerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 overflow-hidden">
            <div class="modal-header py-3 px-4" style="background:linear-gradient(90deg,#C10202,#8b0001);color:#fff;border-bottom:none;">
                <div>
                    <h5 class="modal-title fw-bold mb-1">
                        <i class="bi bi-building-check me-2"></i>Facility Inspection — Sections
                    </h5>
                    <small id="dashPickerSubtitle" class="opacity-75 d-block"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:brightness(0) invert(1);"></button>
            </div>
            <div class="modal-body p-4">
                <div id="dashFacilityProgressArea" style="display:none;" class="mb-4">
                    <div class="d-flex justify-content-between mb-1" style="font-size:.8rem;">
                        <span class="text-muted">Sections complétées</span>
                        <span id="dashFacilityLabel" class="fw-semibold" style="color:#C10202;"></span>
                    </div>
                    <div class="progress mb-3" style="height:10px;border-radius:999px;">
                        <div id="dashFacilityBar" class="progress-bar" role="progressbar" style="width:0%"></div>
                    </div>
                    <div id="dashFacilityAlert" class="alert d-flex align-items-center gap-2 py-2 px-3 mb-0 small"></div>
                </div>
                <div class="row g-3" id="dashPickerGrid"></div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0 d-flex justify-content-between">
                <a href="{{ route('qaDashboard') }}" class="btn btn-outline-secondary btn-sm rounded-3">
                    <i class="bi bi-arrow-left me-1"></i>Retour au Dashboard QA
                </a>
                <button type="button" class="btn btn-outline-dark btn-sm rounded-3" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     EDIT INSPECTION MODAL
══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="editInspectionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(90deg,#fd7e14,#e35d00);color:#fff;">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Modifier l'inspection</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="editInspErr" class="alert alert-danger d-none small py-2 mb-3"></div>
                <input type="hidden" id="ei-id">
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Nom de l'inspection</label>
                    <input type="text" class="form-control form-control-sm" id="ei-name" maxlength="200">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Date planifiée <span class="text-danger">*</span></label>
                    <input type="date" class="form-control form-control-sm" id="ei-date">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Inspecteur <span class="text-danger">*</span></label>
                    <select class="form-select form-select-sm" id="ei-inspector">
                        <option value="">— Sélectionner —</option>
                        @foreach($all_personnels as $p)
                            <option value="{{ $p->id }}">{{ $p->prenom }} {{ $p->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="ei-facility-location-row" class="mb-3" style="display:none;">
                    <label class="form-label fw-semibold small">Site du Facility Inspection</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="ei_facility_location" id="ei-loc-cotonou" value="cotonou" checked>
                            <label class="form-check-label small" for="ei-loc-cotonou">Cotonou (QA-PR-1-001A/06)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="ei_facility_location" id="ei-loc-cove" value="cove">
                            <label class="form-check-label small" for="ei-loc-cove">Covè (QA-PR-1-001B/06)</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-sm fw-semibold" style="background:#fd7e14;color:#fff;"
                        onclick="submitEditInspection()" id="editInspSaveBtn">
                    <i class="bi bi-floppy me-1"></i>Enregistrer
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    window.openEditInspectionModal = function(id, name, date, inspectorId, type, facilityLocation) {
        document.getElementById('editInspErr').classList.add('d-none');
        document.getElementById('ei-id').value        = id;
        document.getElementById('ei-name').value      = name;
        document.getElementById('ei-date').value      = date;
        document.getElementById('ei-inspector').value = inspectorId || '';

        const facRow = document.getElementById('ei-facility-location-row');
        if (type === 'Facility Inspection') {
            facRow.style.display = '';
            const loc = facilityLocation || 'cotonou';
            document.getElementById('ei-loc-cotonou').checked = (loc === 'cotonou');
            document.getElementById('ei-loc-cove').checked    = (loc === 'cove');
        } else {
            facRow.style.display = 'none';
        }

        new bootstrap.Modal(document.getElementById('editInspectionModal')).show();
    };

    window.submitEditInspection = function() {
        const errDiv = document.getElementById('editInspErr');
        const id     = document.getElementById('ei-id').value;
        const date   = document.getElementById('ei-date').value;
        const insp   = document.getElementById('ei-inspector').value;
        const name   = document.getElementById('ei-name').value.trim();

        if (!date || !insp) {
            errDiv.textContent = 'La date et l\'inspecteur sont requis.';
            errDiv.classList.remove('d-none');
            return;
        }
        errDiv.classList.add('d-none');

        const locEl = document.querySelector('input[name="ei_facility_location"]:checked');
        const payload = {
            inspection_id:     id,
            inspection_name:   name || null,
            date_scheduled:    date,
            qa_inspector_id:   insp,
            facility_location: locEl ? locEl.value : null,
        };

        const btn = document.getElementById('editInspSaveBtn');
        btn.disabled = true;

        fetch('{{ route("updateQaInspection") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('editInspectionModal')).hide();
                location.reload();
            } else {
                errDiv.textContent = data.message;
                errDiv.classList.remove('d-none');
            }
        })
        .catch(() => { btn.disabled = false; errDiv.textContent = 'Erreur réseau.'; errDiv.classList.remove('d-none'); });
    };
})();
</script>

{{-- ── FullCalendar ── --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
(function() {
    const TYPE_COLORS = {
        'Facility Inspection':       '#0d6efd',
        'Process Inspection':        '#6f42c1',
        'Study Inspection':          '#198754',
        'Critical Phase Inspection': '#C10202',
    };

    const rawEvents = @json($calendar_inspections);

    const events = rawEvents.map(e => ({
        id:       e.id,
        title:    e.title,
        start:    e.start,
        color:    e.done ? '#adb5bd' : (TYPE_COLORS[e.type] || '#555'),
        textColor: '#fff',
        extendedProps: {
            type:          e.type,
            done:          e.done,
            project_code:  e.project_code,
            inspector_name: e.inspector_name,
        },
    }));

    const tooltip = document.getElementById('cal-tooltip');

    document.addEventListener('DOMContentLoaded', function() {
        const calEl = document.getElementById('qa-calendar');
        if (!calEl) return;

        const cal = new FullCalendar.Calendar(calEl, {
            initialView:  'dayGridMonth',
            locale:       'fr',
            height:       'auto',
            headerToolbar: {
                left:   'prev,next today',
                center: 'title',
                right:  'dayGridMonth,listMonth',
            },
            buttonText: { today: "Aujourd'hui", month: 'Mois', list: 'Liste' },
            events:   events,
            eventDidMount: function(info) {
                info.el.style.cursor = 'pointer';
                info.el.addEventListener('mouseenter', function(e) {
                    const p = info.event.extendedProps;
                    let html = `<strong>${escHtmlCal(info.event.title)}</strong>`;
                    if (p.project_code) html += `<div class="text-muted small">${escHtmlCal(p.project_code)}</div>`;
                    html += `<div class="mt-1"><span style="font-size:.75rem;background:${info.event.backgroundColor};color:#fff;padding:1px 7px;border-radius:10px;">${escHtmlCal(p.type)}</span></div>`;
                    if (p.inspector_name) html += `<div class="small mt-1"><i class="bi bi-person me-1"></i>${escHtmlCal(p.inspector_name)}</div>`;
                    html += `<div class="small mt-1">${p.done ? '<span style="color:#198754;font-weight:600;">✓ Complétée</span>' : '<span style="color:#0d6efd;font-weight:600;">En cours</span>'}</div>`;
                    tooltip.innerHTML = html;
                    tooltip.style.display = 'block';
                    positionTooltip(e);
                });
                info.el.addEventListener('mousemove', positionTooltip);
                info.el.addEventListener('mouseleave', function() {
                    tooltip.style.display = 'none';
                });
            },
            eventClick: function(info) {
                // Scroll to the inspection row in the table
                const row = document.querySelector(`.insp-row[data-id="${info.event.id}"]`);
                if (row) {
                    row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    row.click();
                }
            },
        });

        cal.render();
    });

    function positionTooltip(e) {
        const x = e.clientX + 14;
        const y = e.clientY + 14;
        const w = tooltip.offsetWidth || 260;
        const h = tooltip.offsetHeight || 100;
        tooltip.style.left = (x + w > window.innerWidth  ? x - w - 28 : x) + 'px';
        tooltip.style.top  = (y + h > window.innerHeight ? y - h - 28 : y) + 'px';
    }

    function escHtmlCal(str) {
        if (!str) return '';
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }
})();
</script>

@endsection

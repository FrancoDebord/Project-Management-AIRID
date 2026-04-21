@extends('index-new')

@section('title', 'List of Projects — AIRID')

@section('content')
<style>
    /* ── Page ── */
    .pl-header {
        background: linear-gradient(135deg, #1a3a6b 0%, #c41230 100%);
        color: #fff;
        border-radius: .75rem;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
    }
    /* ── Filter bar ── */
    .pl-filter-bar {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: .6rem;
        padding: .75rem 1rem;
        margin-bottom: 1rem;
        display: flex;
        gap: .75rem;
        flex-wrap: wrap;
        align-items: center;
    }
    .pl-filter-bar .form-control,
    .pl-filter-bar .form-select {
        font-size: .84rem;
        border-radius: .4rem;
    }
    /* ── Table ── */
    .pl-table-wrap {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: .75rem;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0,0,0,.06);
    }
    #pl-table {
        margin-bottom: 0;
        font-size: .82rem;
    }
    #pl-table thead th {
        background: #1a3a6b;
        color: #fff;
        font-weight: 600;
        font-size: .75rem;
        text-transform: uppercase;
        letter-spacing: .05em;
        white-space: nowrap;
        padding: .65rem .75rem;
        border: none;
        vertical-align: middle;
    }
    #pl-table tbody tr:hover { background: #f4f6fb; }
    #pl-table tbody td {
        vertical-align: middle;
        padding: .55rem .75rem;
        border-bottom: 1px solid #f0f0f0;
    }
    #pl-table tbody tr:last-child td { border-bottom: none; }

    /* ── Status badge ── */
    .pl-status {
        display: inline-block;
        padding: .2rem .6rem;
        border-radius: 20px;
        font-size: .72rem;
        font-weight: 600;
        white-space: nowrap;
    }
    .pl-status-not_started { background: #e9ecef; color: #495057; }
    .pl-status-in_progress { background: #d1ecf1; color: #0c5460; }
    .pl-status-suspended   { background: #fff3cd; color: #856404; }
    .pl-status-completed   { background: #d4edda; color: #155724; }
    .pl-status-archived    { background: #cce5ff; color: #004085; }

    /* ── Print ── */
    @media print {
        .pl-no-print { display: none !important; }
        .pl-header   { background: #1a3a6b !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        #pl-table thead th { background: #1a3a6b !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .pl-table-wrap { box-shadow: none; border: 1px solid #ccc; }
        body { font-size: 11px; }
    }
</style>

<div class="container-fluid py-4">

    {{-- ── Header ── --}}
    <div class="pl-header d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h4 class="mb-1 fw-bold"><i class="bi bi-table me-2"></i>List of Projects</h4>
            <div class="opacity-75 small">{{ $projects->count() }} project(s) found</div>
        </div>
        <div class="d-flex gap-2 pl-no-print">
            <button class="btn btn-light fw-semibold" data-bs-toggle="modal" data-bs-target="#previewModal">
                <i class="bi bi-eye me-1"></i>Aperçu
            </button>
            <a id="pdf-download-btn"
               href="{{ route('projects.list.pdf', array_filter(['search' => $search, 'status' => $status])) }}"
               class="btn fw-semibold" style="background:#c41230;color:#fff;">
                <i class="bi bi-file-earmark-pdf me-1"></i>Télécharger PDF
            </a>
        </div>
    </div>

    {{-- ── Filter bar ── --}}
    <form method="GET" action="{{ route('projects.list') }}" class="pl-filter-bar pl-no-print">
        <div class="flex-grow-1" style="min-width:220px;">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Search code, title, sponsor…"
                       value="{{ $search }}">
            </div>
        </div>
        <select name="status" class="form-select form-select-sm" style="max-width:180px;">
            <option value="">All statuses</option>
            @foreach($statuses as $val => $label)
                <option value="{{ $val }}" {{ $status === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-sm btn-primary px-3">
            <i class="bi bi-funnel me-1"></i>Filter
        </button>
        @if($search || $status)
        <a href="{{ route('projects.list') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-x me-1"></i>Clear
        </a>
        @endif
    </form>

    {{-- ── Print title (hidden on screen) ── --}}
    <div class="d-none d-print-block mb-3">
        <h5 class="fw-bold">AIRID — List of Projects</h5>
        <div class="text-muted small">Printed on {{ now()->format('d/m/Y H:i') }}</div>
        @if($search)<div class="small">Search: <strong>{{ $search }}</strong></div>@endif
        @if($status)<div class="small">Status: <strong>{{ $statuses[$status] ?? $status }}</strong></div>@endif
    </div>

    {{-- ── Table ── --}}
    <div class="pl-table-wrap">
        @if($projects->isEmpty())
            <div class="text-center text-muted py-5">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>No projects found.
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover" id="pl-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Protocol Code</th>
                        <th>Project Code</th>
                        <th>Title</th>
                        <th>Sponsor &amp; Email</th>
                        <th>Manufacturer</th>
                        <th>Study Director</th>
                        <th>Date of Appointment</th>
                        <th>SD Replacement</th>
                        <th>Project Manager</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($projects as $i => $p)
                    @php
                        $sda         = $p->studyDirectorAppointmentForm;
                        $sd          = $sda?->studyDirector;
                        $sdName      = $sd ? trim(($sd->titre_personnel ?? '') . ' ' . $sd->prenom . ' ' . $sd->nom) : '—';
                        $apptDate    = $sda?->sd_appointment_date
                                         ? \Carbon\Carbon::parse($sda->sd_appointment_date)->format('d/m/Y')
                                         : '—';

                        $replacement = $p->studyDirectorReplacementHistory;
                        $replSd      = null;
                        if ($replacement) {
                            $replSd = \App\Models\Pro_Personnel::find($replacement->study_director);
                        }
                        $replName    = $replSd
                                         ? trim(($replSd->titre_personnel ?? '') . ' ' . $replSd->prenom . ' ' . $replSd->nom)
                                         : null;
                        $replDate    = $replacement?->replacement_date
                                         ? \Carbon\Carbon::parse($replacement->replacement_date)->format('d/m/Y')
                                         : null;

                        $pm          = $p->projectManager;
                        $pmName      = $pm ? trim(($pm->titre_personnel ?? '') . ' ' . $pm->prenom . ' ' . $pm->nom) : '—';

                        $stageKey    = str_replace(' ', '_', $p->project_stage ?? 'not_started');
                        $stageLabel  = match($p->project_stage) {
                            'not_started' => 'Not Started',
                            'in progress' => 'In Progress',
                            'suspended'   => 'Suspended',
                            'completed'   => 'Completed',
                            'archived'    => 'Archived',
                            default       => ucfirst($p->project_stage ?? '—'),
                        };

                        $manufacturer = $p->manufacturer_name
                            ?: ($p->sponsor_name ? '<span class="text-muted fst-italic small">Same as Sponsor</span>' : '—');
                    @endphp
                    <tr>
                        <td class="text-muted">{{ $i + 1 }}</td>
                        <td>
                            @if($p->protocol_code)
                                <span class="fw-semibold">{{ $p->protocol_code }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('project.create') }}?project_id={{ $p->id }}"
                               class="fw-bold text-decoration-none" style="color:#1a3a6b;">
                                {{ $p->project_code }}
                            </a>
                        </td>
                        <td style="max-width:220px;">
                            <div class="text-truncate" title="{{ $p->project_title }}">{{ $p->project_title }}</div>
                            @if($p->is_legacy)
                                <span class="badge" style="font-size:.65rem;background:#856404;">Legacy</span>
                            @endif
                            @if($p->is_glp)
                                <span class="badge bg-primary" style="font-size:.65rem;">GLP</span>
                            @endif
                        </td>
                        <td>
                            @if($p->sponsor_name)
                                <div class="fw-semibold">{{ $p->sponsor_name }}</div>
                                @if($p->sponsor_email)
                                    <a href="mailto:{{ $p->sponsor_email }}" class="text-muted small text-decoration-none">
                                        <i class="bi bi-envelope me-1"></i>{{ $p->sponsor_email }}
                                    </a>
                                @endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{!! $manufacturer !!}</td>
                        <td>
                            <div>{{ $sdName }}</div>
                        </td>
                        <td class="text-muted small">{{ $apptDate }}</td>
                        <td>
                            @if($replName)
                                <div class="small">
                                    <i class="bi bi-arrow-right-short text-danger"></i>
                                    <strong>{{ $replName }}</strong>
                                </div>
                                @if($replDate)
                                    <div class="text-muted" style="font-size:.72rem;">{{ $replDate }}</div>
                                @endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $pmName }}</td>
                        <td>
                            <span class="pl-status pl-status-{{ $stageKey }}">{{ $stageLabel }}</span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>

{{-- ── Print Preview Modal ── --}}
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background:#1a3a6b;color:#fff;">
                <h5 class="modal-id fw-semibold" id="previewModalLabel">
                    <i class="bi bi-eye me-2"></i>Aperçu avant impression
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                {{-- Mini AIRID header --}}
                <div class="px-4 pt-3 pb-2 border-bottom d-flex align-items-center justify-content-between">
                    <div class="fw-bold" style="color:#1a3a6b;font-size:.95rem;">
                        AIRID — Liste des projets
                        @if($search || $status)
                            <span class="text-muted fw-normal small ms-2">
                                Filtres :
                                @if($search)"{{ $search }}"@endif
                                @if($status)· {{ $statuses[$status] ?? $status }}@endif
                            </span>
                        @endif
                    </div>
                    <span class="text-muted small">{{ $projects->count() }} projet(s) · {{ now()->format('d/m/Y H:i') }}</span>
                </div>

                <div class="table-responsive px-2 pb-3">
                    <table class="table table-sm table-bordered align-middle mt-2" style="font-size:.75rem;min-width:900px;">
                        <thead style="background:#1a3a6b;color:#fff;">
                            <tr>
                                <th class="px-2">#</th>
                                <th>Code protocole</th>
                                <th>Code projet</th>
                                <th style="max-width:180px;">Titre</th>
                                <th>Sponsor</th>
                                <th>Fabricant</th>
                                <th>Study Director</th>
                                <th>Nomination</th>
                                <th>Remplacement SD</th>
                                <th>Project Manager</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($projects as $i => $p)
                            @php
                                $sda         = $p->studyDirectorAppointmentForm;
                                $sd          = $sda?->studyDirector;
                                $sdName      = $sd ? trim(($sd->titre_personnel ?? '') . ' ' . $sd->prenom . ' ' . $sd->nom) : '—';
                                $apptDate    = $sda?->sd_appointment_date
                                                 ? \Carbon\Carbon::parse($sda->sd_appointment_date)->format('d/m/Y')
                                                 : '—';
                                $replacement = $p->studyDirectorReplacementHistory;
                                $replSd      = $replacement ? \App\Models\Pro_Personnel::find($replacement->study_director) : null;
                                $replName    = $replSd
                                                 ? trim(($replSd->titre_personnel ?? '') . ' ' . $replSd->prenom . ' ' . $replSd->nom)
                                                 : null;
                                $replDate    = $replacement?->replacement_date
                                                 ? \Carbon\Carbon::parse($replacement->replacement_date)->format('d/m/Y')
                                                 : null;
                                $pm          = $p->projectManager;
                                $pmName      = $pm ? trim(($pm->titre_personnel ?? '') . ' ' . $pm->prenom . ' ' . $pm->nom) : '—';
                                $stageKey    = str_replace(' ', '_', $p->project_stage ?? 'not_started');
                                $stageLabel  = match($p->project_stage){
                                    'not_started'=>'Not Started','in progress'=>'In Progress',
                                    'suspended'=>'Suspended','completed'=>'Completed',
                                    'archived'=>'Archived',default=>ucfirst($p->project_stage ?? '—')};
                            @endphp
                            <tr>
                                <td class="text-muted px-2">{{ $i+1 }}</td>
                                <td>{{ $p->protocol_code ?: '—' }}</td>
                                <td class="fw-semibold" style="color:#1a3a6b;">
                                    {{ $p->project_code }}
                                    @if($p->is_glp)<span class="badge" style="background:#1a3a6b;font-size:.6rem;">GLP</span>@endif
                                    @if($p->is_legacy)<span class="badge bg-warning text-dark" style="font-size:.6rem;">Legacy</span>@endif
                                </td>
                                <td style="max-width:180px;"><div class="text-truncate" title="{{ $p->project_title }}">{{ $p->project_title }}</div></td>
                                <td>
                                    {{ $p->sponsor_name ?: '—' }}
                                    @if($p->sponsor_email)<div class="text-muted" style="font-size:.65rem;">{{ $p->sponsor_email }}</div>@endif
                                </td>
                                <td>
                                    @if($p->manufacturer_name){{ $p->manufacturer_name }}
                                    @elseif($p->sponsor_name)<span class="text-muted fst-italic">Same as Sponsor</span>
                                    @else —@endif
                                </td>
                                <td>{{ $sdName }}</td>
                                <td class="text-muted" style="white-space:nowrap;">{{ $apptDate }}</td>
                                <td>
                                    @if($replName)
                                        <div>{{ $replName }}</div>
                                        @if($replDate)<div class="text-muted" style="font-size:.65rem;">{{ $replDate }}</div>@endif
                                    @else —@endif
                                </td>
                                <td>{{ $pmName }}</td>
                                <td><span class="pl-status pl-status-{{ $stageKey }}">{{ $stageLabel }}</span></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer gap-2">
                <span class="text-muted small me-auto">Ce tableau est une prévisualisation. Le PDF téléchargeable a une mise en page optimisée.</span>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <a id="modal-pdf-btn"
                   href="{{ route('projects.list.pdf', array_filter(['search' => $search, 'status' => $status])) }}"
                   class="btn fw-semibold" style="background:#c41230;color:#fff;">
                    <i class="bi bi-file-earmark-pdf me-1"></i>Télécharger PDF
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Keep the PDF download link in sync with the current filter form
(function () {
    var baseUrl = '{{ route("projects.list.pdf") }}';
    var form    = document.querySelector('form[action="{{ route("projects.list") }}"]');
    var pdfBtn  = document.getElementById('pdf-download-btn');
    if (!form || !pdfBtn) return;

    function syncPdfLink() {
        var params = new URLSearchParams();
        var search = form.querySelector('[name="search"]')?.value ?? '';
        var status = form.querySelector('[name="status"]')?.value ?? '';
        if (search) params.set('search', search);
        if (status) params.set('status', status);
        var url = baseUrl + (params.toString() ? '?' + params.toString() : '');
        pdfBtn.href = url;
        var modalBtn = document.getElementById('modal-pdf-btn');
        if (modalBtn) modalBtn.href = url;
    }

    form.querySelectorAll('input, select').forEach(function (el) {
        el.addEventListener('input', syncPdfLink);
        el.addEventListener('change', syncPdfLink);
    });
})();
</script>
@endsection

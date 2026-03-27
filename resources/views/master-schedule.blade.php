@extends('index-new')

@section('title', "Master Schedule")

@php
use Carbon\Carbon;

$fmtDate = function($d): ?string {
    if (!$d) return null;
    try { return Carbon::parse($d)->format('m/y'); } catch (\Throwable $e) { return null; }
};

$phases = [
    ['key' => 'study_start',  'label' => 'Study Start Phase',  'color' => '#1a3a6b', 'text' => '#fff'],
    ['key' => 'planning',     'label' => 'Planning Phase',     'color' => '#2e7d32', 'text' => '#fff'],
    ['key' => 'experimental', 'label' => 'Experimental Phase', 'color' => '#e65100', 'text' => '#fff'],
    ['key' => 'report',       'label' => 'Report Phase',       'color' => '#6a1b9a', 'text' => '#fff'],
    ['key' => 'archiving',    'label' => 'Archiving Phase',    'color' => '#37474f', 'text' => '#fff'],
];
@endphp

@section('content')
<section>
<div class="container-fluid">

    <div class="row mb-3 align-items-center">
        <div class="col">
            <h3 class="title-section mb-0">Master Schedule</h3>
        </div>
        <div class="col-auto">
            <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                <i class="mdi mdi-printer"></i> Print / PDF
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-bordered ms-table" id="master_schedule">
                    <thead>
                        {{-- Row 1: fixed headers + phase group headers --}}
                        <tr>
                            <th rowspan="2" class="ms-fixed-col align-middle text-center ms-sortable" data-col="0" style="width:90px;cursor:pointer;" title="Cliquer pour trier">Study Code <span class="ms-sort-icon">↕</span></th>
                            <th rowspan="2" class="ms-fixed-col align-middle text-center ms-sortable" data-col="1" style="width:130px;cursor:pointer;" title="Cliquer pour trier">Test Systems <span class="ms-sort-icon">↕</span></th>
                            <th rowspan="2" class="ms-fixed-col align-middle text-center ms-sortable" data-col="2" style="width:120px;cursor:pointer;" title="Cliquer pour trier">Nature of Study <span class="ms-sort-icon">↕</span></th>
                            <th rowspan="2" class="ms-fixed-col align-middle text-center ms-sortable" data-col="3" style="width:130px;cursor:pointer;" title="Cliquer pour trier">Study Director <span class="ms-sort-icon">↕</span></th>
                            <th rowspan="2" class="ms-fixed-col align-middle text-center" style="width:150px;">Key Personnel</th>
                            <th rowspan="2" class="ms-fixed-col align-middle text-center ms-sortable" data-col="5" style="width:80px;cursor:pointer;" title="Cliquer pour trier">Study Status <span class="ms-sort-icon">↕</span></th>
                            @foreach($phases as $ph)
                                <th colspan="2" class="text-center text-white fw-bold" style="background:{{ $ph['color'] }};">
                                    {{ $ph['label'] }}
                                </th>
                            @endforeach
                        </tr>
                        {{-- Row 2: Start / End sub-headers --}}
                        <tr>
                            @foreach($phases as $ph)
                                <th class="text-center" style="background:{{ $ph['color'] }}; color:{{ $ph['text'] }}; font-size:.75rem; width:60px;">Start</th>
                                <th class="text-center" style="background:{{ $ph['color'] }}; color:{{ $ph['text'] }}; font-size:.75rem; width:60px;">End</th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($scheduleData as $row)
                            @php
                                $project      = $row['project'];
                                $sd           = $project->studyDirectorAppointmentForm?->studyDirector;
                                $sdName       = $sd ? trim(($sd->titre_personnel ?? '') . ' ' . $sd->prenom . ' ' . $sd->nom) : null;
                                $kpNames      = $project->keyPersonnelProject->map(fn($p) =>
                                    trim(($p->titre_personnel ?? '') . ' ' . $p->prenom . ' ' . $p->nom)
                                )->all();
                                $manageBase   = route('project.create', ['project_id' => $project->id]);
                                $projectUrl   = route('projectOverview', ['id' => $project->id]);
                                $statusClass  = match($project->project_status ?? '') {
                                    'GLP'     => 'badge bg-success',
                                    'NON-GLP' => 'badge bg-secondary',
                                    default   => 'badge bg-light text-dark border',
                                };
                            @endphp
                            <tr>
                                {{-- Study Code --}}
                                <td class="fw-bold text-center align-middle">
                                    <a href="{{ $projectUrl }}" class="text-decoration-none">{{ $project->project_code }}</a>
                                </td>

                                {{-- Test Systems --}}
                                <td class="align-middle" style="font-size:.82rem;">{{ $project->test_system ?? '—' }}</td>

                                {{-- Nature of Study --}}
                                <td class="align-middle" style="font-size:.82rem;">{{ $project->project_nature ?? '—' }}</td>

                                {{-- Study Director --}}
                                <td class="align-middle" style="font-size:.82rem;">
                                    @if($sdName)
                                        {{ $sdName }}
                                    @else
                                        <a href="{{ $manageBase }}#step1" class="ms-nr">Non renseigné</a>
                                    @endif
                                </td>

                                {{-- Key Personnel --}}
                                <td class="align-middle" style="font-size:.82rem;">
                                    @if(count($kpNames))
                                        {!! implode('<br>', $kpNames) !!}
                                    @else
                                        <a href="{{ $manageBase }}#step1" class="ms-nr">Non renseigné</a>
                                    @endif
                                </td>

                                {{-- Study Status --}}
                                <td class="text-center align-middle">
                                    <span class="{{ $statusClass }}" style="font-size:.72rem;">
                                        {{ $project->project_status ?? '—' }}
                                    </span>
                                </td>

                                {{-- Phase date cells --}}
                                @foreach($phases as $ph)
                                    @php
                                        $phData = $row[$ph['key']];
                                        $sInfo  = $phData['start'];
                                        $eInfo  = $phData['end'];
                                        $sFmt   = $fmtDate($sInfo['date']);
                                        $eFmt   = $fmtDate($eInfo['date']);
                                    @endphp
                                    <td class="text-center align-middle ms-date-cell" style="border-left:2px solid {{ $ph['color'] }};">
                                        @if($sFmt)
                                            <span class="ms-date"
                                                  data-bs-toggle="tooltip"
                                                  data-bs-placement="top"
                                                  title="{{ $sInfo['tooltip'] }} : {{ \Carbon\Carbon::parse($sInfo['date'])->format('d/m/Y') }}">
                                                {{ $sFmt }}
                                            </span>
                                        @else
                                            <a href="{{ $sInfo['nr_url'] }}" class="ms-nr"
                                               title="Non renseigné — {{ $sInfo['tooltip'] }}">N/R</a>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle ms-date-cell">
                                        @if($eFmt)
                                            <span class="ms-date"
                                                  data-bs-toggle="tooltip"
                                                  data-bs-placement="top"
                                                  title="{{ $eInfo['tooltip'] }} : {{ \Carbon\Carbon::parse($eInfo['date'])->format('d/m/Y') }}">
                                                {{ $eFmt }}
                                            </span>
                                        @else
                                            <a href="{{ $eInfo['nr_url'] }}" class="ms-nr"
                                               title="Non renseigné — {{ $eInfo['tooltip'] }}">N/R</a>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 6 + count($phases) * 2 }}" class="text-center text-muted py-4">
                                    Aucun projet trouvé.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
</section>

<style>
.ms-table {
    font-size: .83rem;
    white-space: nowrap;
    border-collapse: collapse;
}
.ms-table th, .ms-table td {
    vertical-align: middle;
    padding: 5px 7px;
}
.ms-table thead tr:first-child th {
    font-size: .8rem;
}
.ms-date-cell {
    min-width: 52px;
}
.ms-date {
    font-weight: 700;
    font-size: .8rem;
    letter-spacing: .03em;
}
.ms-nr {
    display: inline-block;
    font-size: .68rem;
    color: #b35900;
    background: #fff3e0;
    border: 1px solid #ffcc80;
    border-radius: 3px;
    padding: 1px 4px;
    text-decoration: none;
    font-weight: 700;
}
.ms-nr:hover { background: #ffe0b2; }

/* Tooltip override for small cells */
.ms-date[data-bs-toggle="tooltip"] { cursor: help; }
/* Sort icons */
.ms-sortable:hover { background: rgba(0,0,0,.06); }
.ms-sort-icon { font-size:.7rem; opacity:.5; margin-left:3px; }
.ms-sortable.sort-asc .ms-sort-icon::after  { content:'↑'; opacity:1; }
.ms-sortable.sort-desc .ms-sort-icon::after { content:'↓'; opacity:1; }
.ms-sortable.sort-asc  .ms-sort-icon,
.ms-sortable.sort-desc .ms-sort-icon { opacity:0; }

@media print {
    .screen-only, button { display: none !important; }
    .ms-table { font-size: 7pt; }
    .ms-date-cell { min-width: 40px; }
    body { font-size: 8pt; }
    .container-fluid { padding: 0; }
    .ms-nr { color: #666; background: none; border: 1px solid #ccc; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Init Bootstrap tooltips
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
        new bootstrap.Tooltip(el, { trigger: 'hover' });
    });

    // ── Tri des colonnes ──
    var table     = document.getElementById('master_schedule');
    var tbody     = table.querySelector('tbody');
    var sortState = { col: 0, dir: 'desc' }; // tri initial : Study Code décroissant

    function sortTable(col, dir) {
        var rows = Array.from(tbody.querySelectorAll('tr'));
        rows.sort(function (a, b) {
            var ta = (a.cells[col] ? a.cells[col].textContent.trim() : '');
            var tb = (b.cells[col] ? b.cells[col].textContent.trim() : '');
            return dir === 'asc' ? ta.localeCompare(tb) : tb.localeCompare(ta);
        });
        rows.forEach(function (r) { tbody.appendChild(r); });

        // Mettre à jour les icônes
        table.querySelectorAll('.ms-sortable').forEach(function (th) {
            th.classList.remove('sort-asc', 'sort-desc');
        });
        var activeTh = table.querySelector('.ms-sortable[data-col="' + col + '"]');
        if (activeTh) activeTh.classList.add(dir === 'asc' ? 'sort-asc' : 'sort-desc');
    }

    // Appliquer le tri initial
    sortTable(sortState.col, sortState.dir);

    // Clic sur en-têtes
    table.querySelectorAll('.ms-sortable').forEach(function (th) {
        th.addEventListener('click', function () {
            var col = parseInt(this.dataset.col);
            var dir = (sortState.col === col && sortState.dir === 'asc') ? 'desc' : 'asc';
            sortState = { col: col, dir: dir };
            sortTable(col, dir);
        });
    });
});
</script>
@endsection

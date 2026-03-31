<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }

@page {
    size: A4 landscape;
    margin: 10mm 10mm 10mm 10mm;
}

body {
    font-family: DejaVu Sans, Arial, sans-serif;
    font-size: 7.5pt;
    color: #222;
}

/* ── Header ────────────────────────────────────────────── */
.doc-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
    border-bottom: 1.5px solid #1a3a6b;
    padding-bottom: 5px;
}

.doc-header-center {
    text-align: center;
    flex: 1;
}
.doc-header-center .doc-title { font-size: 12pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-top: 2px; }
.doc-header-center .org-name  { font-size: 8pt; color:#1a3a6b; font-weight:600; }

.doc-ref-box {
    font-size: 7pt;
    text-align: right;
    white-space: nowrap;
    min-width: 130px;
}
.doc-ref-box .ref-num { font-weight: bold; font-size: 8pt; }
.logo-box { min-width: 160px; max-width:160px; }
.logo-box img { max-width:100%; max-height:50px; }

/* ── Table ─────────────────────────────────────────────── */
table.ms {
    width: 100%;
    border-collapse: collapse;
    margin-top: 6px;
}

table.ms th, table.ms td {
    border: 0.7px solid #aaa;
    padding: 3px 4px;
    vertical-align: middle;
    text-align: center;
}

table.ms thead th {
    font-size: 6pt;
    font-weight: bold;
}

table.ms tbody td {
    font-size: 6.2pt;
}

table.ms tbody tr:nth-child(even) td { background: #f7f7f7; }

/* Phase header colors */
.ph-study   { background: #1a3a6b; color: #fff; }
.ph-planning { background: #2e7d32; color: #fff; }
.ph-exp     { background: #e65100; color: #fff; }
.ph-report  { background: #6a1b9a; color: #fff; }
.ph-archive { background: #37474f; color: #fff; }

/* Date cell border colors */
.bd-study   { border-left: 2px solid #1a3a6b !important; }
.bd-planning { border-left: 2px solid #2e7d32 !important; }
.bd-exp     { border-left: 2px solid #e65100 !important; }
.bd-report  { border-left: 2px solid #6a1b9a !important; }
.bd-archive { border-left: 2px solid #37474f !important; }

.nr { color: #b35900; font-size: 6.5pt; }
.badge-glp  { background: #198754; color: #fff; padding: 1px 5px; border-radius: 3px; font-size: 6pt; }
.badge-nglp { background: #6c757d; color: #fff; padding: 1px 5px; border-radius: 3px; font-size: 6pt; }

/* ── Footer ─────────────────────────────────────────────── */
.doc-footer {
    margin-top: 8px;
    border-top: 1px solid #ccc;
    padding-top: 5px;
    font-size: 6.5pt;
    color: #555;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}
.legend-col { flex: 1; }
.legend-col p { margin-bottom: 2px; }
.legend-col strong { color: #222; }
</style>
</head>
<body>

{{-- ── Document Header ───────────────────────────────────── --}}
<div class="doc-header">
    <div class="logo-box">
        <img src="{{ $headerImagePath }}" alt="AIRID">
    </div>
    <div class="doc-header-center">
        <div class="org-name">AIRID — African Institute for Research in Infectious Diseases</div>
        <div class="doc-title">Master Schedule</div>
    </div>
    <div class="doc-ref-box">
        @php
            $docRef        = $globalSettings['doc_ref_master'] ?? 'QC-MS-001';
            $docIssueDate  = $globalSettings['doc_issue_date'] ?? '';
            $docNextReview = $globalSettings['doc_next_review'] ?? '';
        @endphp
        <div class="ref-num">{{ $docRef }}</div>
        @if($docIssueDate)
            <div>Issue date: {{ \Carbon\Carbon::parse($docIssueDate)->format('d/m/Y') }}</div>
        @endif
        @if($docNextReview)
            <div>Next review date: {{ \Carbon\Carbon::parse($docNextReview)->format('d/m/Y') }}</div>
        @endif
        <div style="margin-top:3px;color:#888;">Generated: {{ now()->format('d/m/Y') }}</div>
    </div>
</div>

{{-- ── Table ─────────────────────────────────────────────── --}}
@php
use Carbon\Carbon;
$fmtDate = function($d): string {
    if (!$d) return '';
    try { return Carbon::parse($d)->format('m/y'); } catch (\Throwable) { return ''; }
};
@endphp

<table class="ms">
    <thead>
        <tr>
            <th rowspan="2" style="width:45px;">Study<br>Code</th>
            <th rowspan="2" style="width:65px;">Test System(s)</th>
            <th rowspan="2" style="width:65px;">Nature of Study</th>
            <th rowspan="2" style="width:75px;">Study Director</th>
            <th rowspan="2" style="width:75px;">Key Personnel</th>
            <th rowspan="2" style="width:40px;">Status</th>
            <th colspan="2" class="ph-study">Study Start*</th>
            <th colspan="2" class="ph-planning">Planning**</th>
            <th colspan="2" class="ph-exp">Experimental**</th>
            <th colspan="2" class="ph-report">Report**</th>
            <th colspan="2" class="ph-archive">Archiving**</th>
            <th rowspan="2" style="width:45px;">Remarks</th>
        </tr>
        <tr>
            <th class="ph-study" style="width:26px;">Start</th><th class="ph-study" style="width:26px;">End</th>
            <th class="ph-planning" style="width:26px;">Start</th><th class="ph-planning" style="width:26px;">End</th>
            <th class="ph-exp" style="width:26px;">Start</th><th class="ph-exp" style="width:26px;">End</th>
            <th class="ph-report" style="width:26px;">Start</th><th class="ph-report" style="width:26px;">End</th>
            <th class="ph-archive" style="width:26px;">Start</th><th class="ph-archive" style="width:26px;">End</th>
        </tr>
    </thead>
    <tbody>
        @forelse($scheduleData as $row)
        @php
            $project = $row['project'];
            $sd = $project->studyDirectorAppointmentForm?->studyDirector;
            $sdName = $sd ? trim(($sd->titre_personnel ?? '') . ' ' . $sd->prenom . ' ' . $sd->nom) : '—';
            $kpNames = $project->keyPersonnelProject->map(fn($p) =>
                trim(($p->titre_personnel ?? '') . ' ' . $p->prenom . ' ' . $p->nom)
            )->all();

            $phases = [
                ['key' => 'study_start',  'cls' => 'bd-study'],
                ['key' => 'planning',     'cls' => 'bd-planning'],
                ['key' => 'experimental', 'cls' => 'bd-exp'],
                ['key' => 'report',       'cls' => 'bd-report'],
                ['key' => 'archiving',    'cls' => 'bd-archive'],
            ];
        @endphp
        <tr>
            <td style="font-weight:bold;">{{ $project->project_code }}</td>
            <td style="text-align:left;">{{ $project->test_system ?? '—' }}</td>
            <td style="text-align:left;">{{ $project->project_nature ?? '—' }}</td>
            <td style="text-align:left;">{{ $sdName }}</td>
            <td style="text-align:left;">{{ implode(', ', $kpNames) ?: '—' }}</td>
            <td>
                @if($project->is_glp)
                    <span class="badge-glp">GLP</span>
                @else
                    <span class="badge-nglp">Non-GLP</span>
                @endif
            </td>

            @foreach($phases as $ph)
                @php
                    $phData = $row[$ph['key']];
                    $sDate  = $fmtDate($phData['start']['date']);
                    $eDate  = $fmtDate($phData['end']['date']);
                @endphp
                <td class="{{ $ph['cls'] }}">{{ $sDate ?: '—' }}</td>
                <td>{{ $eDate ?: '—' }}</td>
            @endforeach

            <td>{{ $project->archived_at ? 'Archived' : '' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="17" style="text-align:center;color:#888;padding:20px;">No projects found.</td>
        </tr>
        @endforelse
    </tbody>
</table>

{{-- ── Footer ─────────────────────────────────────────────── --}}
<div class="doc-footer">
    <div class="legend-col">
        <p><strong>* Date at which the Study Protocol is signed by the Study Director</strong></p>
        <p><strong>** Please insert the Master Schedule review date as each phase changes.</strong></p>
    </div>
    <div class="legend-col" style="margin-left:24px;">
        <p><strong>1. Study start phase:</strong></p>
        <p>Start date (Appointment of SD) &nbsp;|&nbsp; End date (Signature of protocol by SD)</p>
        <p><strong>2. Planning phase:</strong></p>
        <p>Start date (Signature of protocol by all parties) &nbsp;|&nbsp; End date (Date of first experiment)</p>
    </div>
    <div class="legend-col" style="margin-left:24px;">
        <p><strong>3. Experimental phase:</strong></p>
        <p>Start date (Date of first experiment) &nbsp;|&nbsp; End date (Date of last experiment)</p>
        <p><strong>4. Report phase:</strong></p>
        <p>Start date (Date of last experiment) &nbsp;|&nbsp; End date (Signature of final report by SD)</p>
    </div>
    <div class="legend-col" style="margin-left:24px;">
        <p><strong>5. Archiving phase:</strong></p>
        <p>Start date (Date of signature of final report by all parties)</p>
        <p>End date (Date study related documents are submitted to archivist)</p>
    </div>
    <div style="text-align:right;white-space:nowrap;min-width:80px;">
        Page 1
    </div>
</div>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>QA Activities Checklist – {{ $project->project_code }}</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }

@page { size: A4 portrait; margin: 14mm 16mm 16mm 16mm; }

body {
    font-family: Arial, sans-serif;
    font-size: 9.5pt;
    color: #111;
}

/* ── Header ── */
.doc-header {
    display: block;
    width: 100%;
    border-bottom: 2px solid #1a3a6b;
    padding-bottom: 0;
    margin-bottom: 0;
}
.doc-header img.entete { width: 100%; height: auto; display: block; }
.doc-header-ref {
    display: flex;
    justify-content: flex-end;
    padding: 3px 0 6px;
    margin-bottom: 8px;
    border-bottom: 1px solid #ccc;
}
.doc-ref { font-size: 7pt; text-align: right; white-space: nowrap; }
.doc-ref strong { font-size: 8pt; }

/* ── Title ── */
.doc-title {
    text-align: center;
    font-size: 12pt;
    font-weight: bold;
    text-decoration: underline;
    text-transform: uppercase;
    margin: 10px 0 12px;
    letter-spacing: .5px;
}

/* ── Project Info Fields ── */
.proj-fields { margin-bottom: 10px; }
.field-line { display: flex; align-items: baseline; margin-bottom: 5px; }
.field-label { font-weight: bold; font-size: 9pt; white-space: nowrap; margin-right: 6px; }
.field-value {
    border-bottom: 1px solid #333;
    flex: 1;
    min-height: 14px;
    font-size: 9pt;
    padding-bottom: 1px;
}
.field-value.long { min-width: 100%; }

/* ── Table ── */
table.checklist {
    width: 100%;
    border-collapse: collapse;
    margin-top: 6px;
    font-size: 9pt;
}
table.checklist th, table.checklist td {
    border: 1px solid #333;
    padding: 5px 6px;
    vertical-align: top;
}
table.checklist thead th {
    background: #fff;
    font-weight: bold;
    text-align: center;
    font-size: 9pt;
}
table.checklist td.num { text-align: center; width: 28px; }
table.checklist td.activity { width: auto; }
table.checklist td.date-col { width: 72px; text-align: center; }
table.checklist td.mov-col  { width: 120px; }
table.checklist td.check-col{ width: 46px; text-align: center; font-size: 11pt; }

table.checklist tbody tr:nth-child(even) { background: #f9f9f9; }

/* ── Footer ── */
.doc-footer {
    margin-top: 12px;
    border-top: 1px solid #aaa;
    padding-top: 5px;
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    font-size: 7pt;
    color: #555;
}

/* ── Print controls ── */
@media screen {
    .print-bar {
        position: fixed; top: 0; left: 0; right: 0;
        background: #1a3a6b; color: #fff;
        padding: 8px 20px; display: flex; gap: 10px; align-items: center;
        z-index: 9999; font-family: Arial, sans-serif; font-size: 12px;
    }
    .print-bar button {
        background: #c41230; color: #fff; border: none;
        padding: 5px 14px; border-radius: 5px; cursor: pointer; font-weight: bold;
    }
    .print-bar button.close-btn {
        background: rgba(255,255,255,.2);
    }
    body { padding-top: 48px; }
}
@media print {
    .print-bar { display: none !important; }
    body { padding-top: 0; }
}
</style>
</head>
<body>

{{-- Print bar (screen only) --}}
<div class="print-bar">
    <span style="font-weight:600;"><i>QA Activities Checklist — {{ $project->project_code }}</i></span>
    <button onclick="window.print()">🖨 Print / Save PDF</button>
    <button class="close-btn" onclick="window.close()">✕ Close</button>
</div>

{{-- ── Document Header ── --}}
<div class="doc-header">
    <img class="entete" src="{{ asset('storage/assets/header/entete_airid.png') }}" alt="AIRID — African Institute for Research in Infectious Diseases">
</div>
<div class="doc-header-ref">
    @php
        $docRef     = $globalSettings['doc_ref_master'] ?? 'QA-PR-1-011/05';
        $issueDate  = $globalSettings['doc_issue_date']  ?? '01/08/2025';
        $nextReview = $globalSettings['doc_next_review'] ?? '31/07/2027';
    @endphp
    <div class="doc-ref">
        <strong>QA-PR-1-011/05</strong><br>
        Issue date: {{ $issueDate }}<br>
        Next review date: {{ $nextReview }}
    </div>
</div>

{{-- ── Title ── --}}
<div class="doc-title">Quality Assurance Unit Checklist for GLP Studies</div>

{{-- ── Project Info ── --}}
<div class="proj-fields">
    <div class="field-line">
        <span class="field-label">Project Code:</span>
        <span class="field-value">{{ $project->project_code }}</span>
        <span style="width:20px;"></span>
        <span class="field-label">Start Date:</span>
        <span class="field-value">{{ $project->date_debut_effective ? \Carbon\Carbon::parse($project->date_debut_effective)->format('d/m/Y') : '' }}</span>
    </div>
    <div class="field-line">
        <span class="field-label">Study Director:</span>
        <span class="field-value">{{ $sdName }}</span>
    </div>
    <div class="field-line" style="margin-top:4px;">
        <span class="field-label" style="white-space:nowrap;">Project Title:</span>
        <span class="field-value">{{ $project->project_title }}</span>
    </div>
</div>

{{-- ── Checklist Table ── --}}
<table class="checklist">
    <thead>
        <tr>
            <th style="width:28px;">N°</th>
            <th>Activity</th>
            <th style="width:72px;">Date<br>Performed</th>
            <th style="width:120px;">Means of Verification</th>
            <th style="width:46px;">Check*</th>
        </tr>
    </thead>
    <tbody>
    @foreach($activities as $num => $label)
    @php
        $row = $saved[$num] ?? null;
        // Merge: saved takes priority over prefill
        $date = $row?->date_performed
                    ? \Carbon\Carbon::parse($row->date_performed)->format('d/m/Y')
                    : (isset($prefill[$num]['date']) ? \Carbon\Carbon::parse($prefill[$num]['date'])->format('d/m/Y') : '');
        $mov  = $row?->means_of_verification
                    ?? ($prefill[$num]['mov'] ?? '');
        $checked = $row?->is_checked ?? false;
    @endphp
    <tr>
        <td class="num">{{ $num }}.</td>
        <td class="activity">{{ $label }}</td>
        <td class="date-col">{{ $date }}</td>
        <td class="mov-col">{{ $mov }}</td>
        <td class="check-col">{{ $checked ? '✓' : '' }}</td>
    </tr>
    @endforeach
    </tbody>
</table>

{{-- ── Footer ── --}}
<div class="doc-footer">
    <div>*Sign after checking means of verification</div>
    <div style="text-align:center;font-weight:bold;">SANAS OECD GLP COMPLIANT FACILITY N° G0028</div>
    <div>Page 1 of 1</div>
</div>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quality Assurance Statement — {{ $project->project_code }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            color: #000;
            background: #fff;
            padding: 20px;
        }

        /* ── Institutional header (image) ── */
        .airid-header-img {
            width: 100%;
            display: block;
            margin-bottom: 10px;
        }
        .airid-header-missing {
            border: 1px dashed #aaa;
            padding: 6px 10px;
            font-size: 8pt;
            color: #888;
            text-align: center;
            margin-bottom: 10px;
            display: none; /* shown by JS if img fails */
        }

        /* ── Document title strip ── */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 2px solid #000;
            border-bottom: 1px solid #aaa;
            padding: 5px 0;
            margin-bottom: 12px;
        }
        .doc-title {
            text-align: center;
            flex: 1;
        }
        .doc-title h1 {
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
        }
        .qr-area {
            font-size: 7pt;
            text-align: right;
            color: #666;
            min-width: 110px;
            white-space: nowrap;
        }

        /* ── Project info ── */
        .project-info {
            margin-bottom: 10px;
        }
        .project-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .project-info td {
            padding: 2px 4px;
            vertical-align: top;
            font-size: 9.5pt;
        }
        .project-info td:first-child {
            font-weight: bold;
            width: 160px;
            white-space: nowrap;
        }

        /* ── Intro paragraph ── */
        .intro-para {
            font-size: 9pt;
            line-height: 1.5;
            margin-bottom: 12px;
            text-align: justify;
            border: 1px solid #ccc;
            padding: 8px;
            background: #fafafa;
        }
        .intro-para.final-mode {
            border: none;
            background: transparent;
            padding: 0;
        }

        /* ── Inspections Table ── */
        .inspections-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 16px;
        }
        .inspections-table th {
            background: #d0d0d0;
            border: 1px solid #888;
            padding: 4px 6px;
            text-align: center;
            font-weight: bold;
        }
        .inspections-table td {
            border: 1px solid #aaa;
            padding: 3px 6px;
            vertical-align: top;
        }
        .inspections-table tr.section-header td {
            background: #e8e8e8;
            font-weight: bold;
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        .inspections-table tr.sub-item td:first-child {
            padding-left: 20px;
        }
        .na-cell {
            text-align: center;
            font-style: italic;
            color: #666;
        }
        .date-cell {
            text-align: center;
            white-space: nowrap;
        }

        /* ── Footer / Signature ── */
        .signature-section {
            margin-top: 16px;
            border-top: 1px solid #aaa;
            padding-top: 10px;
        }
        .signature-section table {
            width: 100%;
            border-collapse: collapse;
        }
        .signature-section td {
            padding: 3px 6px;
            font-size: 9.5pt;
            vertical-align: top;
        }
        .signature-line {
            border-bottom: 1px solid #000;
            min-height: 28px;
            margin-bottom: 4px;
        }
        .sanas-footer {
            text-align: center;
            font-size: 8pt;
            font-weight: bold;
            margin-top: 18px;
            border-top: 2px solid #000;
            padding-top: 6px;
        }
        .page-num {
            text-align: right;
            font-size: 8pt;
            margin-top: 8px;
        }

        /* ── Print / Screen controls ── */
        .screen-only {
            display: block;
        }
        .print-controls {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 9999;
            display: flex;
            gap: 8px;
        }
        .btn-print {
            background: #1a3a6b;
            color: #fff;
            border: none;
            padding: 8px 18px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            font-weight: 600;
        }
        .btn-edit {
            background: #fd7e14;
            color: #fff;
            border: none;
            padding: 8px 18px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            font-weight: 600;
        }

        @media print {
            .screen-only, .print-controls { display: none !important; }
            body { padding: 0; font-size: 9pt; }
            .intro-para { border: none; background: transparent; padding: 0; }
            @page { margin: 15mm 12mm; size: A4; }
        }
    </style>
</head>
<body>

{{-- Print button (screen only) --}}
<div class="print-controls screen-only">
    @if(!$statement || $statement->status === 'draft')
    <button class="btn-edit" onclick="document.getElementById('editPanel').classList.toggle('d-none')">
        ✏️ Modifier
    </button>
    @endif
    <button class="btn-print" onclick="window.print()">🖨️ Imprimer / PDF</button>
    <button class="btn-print" style="background:#6c757d;" onclick="window.close()">✕ Fermer</button>
</div>

{{-- Doc reference vars (always computed, used by both edit panel and print header) --}}
@php
    $effectiveDocRef        = $statement?->doc_ref        ?? '';
    $effectiveDocIssueDate  = $statement?->doc_issue_date ?? $globalSettings['doc_issue_date']  ?? '';
    $effectiveDocNextReview = $statement?->doc_next_review ?? $globalSettings['doc_next_review'] ?? '';
@endphp

{{-- ── Edit panel (draft only, screen) ── --}}
@if(!$statement || $statement->status === 'draft')
<div id="editPanel" class="d-none screen-only" style="background:#f0f4ff;border:1px solid #bbb;border-radius:8px;padding:16px;margin-bottom:16px;font-family:Arial,sans-serif;">
    <h6 style="font-weight:bold;margin-bottom:10px;color:#1a3a6b;">✏️ Édition du QA Statement (brouillon)</h6>
    <div id="qasEditError" style="color:red;margin-bottom:8px;display:none;"></div>
    <input type="hidden" id="qasProjectId" value="{{ $project->id }}">

    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:10px;">
        <div>
            <label style="font-size:11px;font-weight:bold;">Numéro de rapport</label>
            <input type="text" id="qasReportNumber" value="{{ $statement?->report_number ?? $project->project_code }}"
                   style="width:100%;border:1px solid #ccc;border-radius:4px;padding:4px 8px;">
        </div>
        <div>
            <label style="font-size:11px;font-weight:bold;">Date de signature</label>
            <input type="date" id="qasDateSigned" value="{{ $statement?->date_signed ?? '' }}"
                   style="width:100%;border:1px solid #ccc;border-radius:4px;padding:4px 8px;">
        </div>
        <div>
            <label style="font-size:11px;font-weight:bold;">Statut</label>
            <select id="qasStatus" style="width:100%;border:1px solid #ccc;border-radius:4px;padding:4px 8px;">
                <option value="draft"  {{ (!$statement || $statement->status==='draft')  ? 'selected' : '' }}>Brouillon</option>
                <option value="final"  {{ ($statement && $statement->status==='final')  ? 'selected' : '' }}>Final (non modifiable)</option>
            </select>
        </div>
    </div>

    {{-- Doc reference fields (statement value → global setting → blank) --}}
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:10px;">
        <div>
            <label style="font-size:11px;font-weight:bold;">Référence document</label>
            <input type="text" id="qasDocRef" value="{{ $effectiveDocRef }}"
                   style="width:100%;border:1px solid #ccc;border-radius:4px;padding:4px 8px;"
                   placeholder="Ex : QA-PR-L-001/09">
        </div>
        <div>
            <label style="font-size:11px;font-weight:bold;">Issue date</label>
            <input type="date" id="qasDocIssueDate"
                   value="{{ $effectiveDocIssueDate }}"
                   style="width:100%;border:1px solid #ccc;border-radius:4px;padding:4px 8px;">
        </div>
        <div>
            <label style="font-size:11px;font-weight:bold;">Next review date</label>
            <input type="date" id="qasDocNextReview"
                   value="{{ $effectiveDocNextReview }}"
                   style="width:100%;border:1px solid #ccc;border-radius:4px;padding:4px 8px;">
        </div>
    </div>

    <div style="margin-bottom:10px;">
        <label style="font-size:11px;font-weight:bold;">Texte d'introduction (éditable)</label>
        <textarea id="qasIntroText" rows="5"
                  style="width:100%;border:1px solid #ccc;border-radius:4px;padding:6px 8px;font-size:11px;">{{ $statement?->intro_text ?? "Quality Assurance Inspections of the above referenced study were conducted according to the procedures described in the Standard Operating Procedures of the Quality Assurance unit and according to general requirements of the OECD Principles of Good Laboratory Practice on the dates given in the table below. The report has been audited to ensure that it accurately describes the methods used and that the reported results accurately reflect the raw data of the study. Findings from the inspections were reported to the Facility Manager and the Study Director as also given below. In addition, facility audits are conducted twice per year and the date of the last audit is included in the statement below." }}</textarea>
    </div>

    <button onclick="saveQaStatement()" style="background:#1a3a6b;color:#fff;border:none;padding:7px 20px;border-radius:5px;font-weight:bold;cursor:pointer;">
        💾 Enregistrer
    </button>
    @if($statement && $statement->status === 'final')
    <span style="margin-left:12px;color:#dc3545;font-size:11px;">⚠️ Ce document est finalisé et ne peut plus être modifié.</span>
    @endif
</div>
@endif

{{-- ════════════════════════════════════════════════
     QA STATEMENT DOCUMENT
════════════════════════════════════════════════ --}}

{{-- Institutional header image --}}
<img src="{{ asset('storage/assets/header/entete_airid.png') }}"
     alt="AIRID Header" class="airid-header-img">

{{-- Document title strip --}}
<div class="page-header">
    <div class="doc-title">
        <h1>Quality Assurance Statement</h1>
    </div>
    <div class="qr-area">
        {{ $effectiveDocRef ?: 'QA-PR-L-001/09' }}<br>
        Issue date: {{ $effectiveDocIssueDate ? \Carbon\Carbon::parse($effectiveDocIssueDate)->format('d/m/Y') : '___/___/______' }}<br>
        Next review: {{ $effectiveDocNextReview ? \Carbon\Carbon::parse($effectiveDocNextReview)->format('d/m/Y') : '___/___/______' }}
    </div>
</div>

{{-- Project Info --}}
<div class="project-info">
    <table>
        <tr>
            <td>Report Number <em>(if applicable)</em>:</td>
            <td>{{ $statement?->report_number ?? $project->project_code }}</td>
        </tr>
        <tr>
            <td>Study Code:</td>
            <td>{{ $project->project_code }}</td>
        </tr>
        <tr>
            <td>Protocol Number:</td>
            <td>{{ $project->protocol_code ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td>Study Title:</td>
            <td>{{ $project->project_title }}</td>
        </tr>
        <tr>
            <td>Study Director:</td>
            <td>
                @forelse($studyDirectors as $sdRecord)
                    @if($sdRecord->studyDirector)
                        {{ $sdRecord->studyDirector->prenom }} {{ $sdRecord->studyDirector->nom }}
                        @if($sdRecord->sd_appointment_date || $sdRecord->estimated_end_date)
                            : {{ $sdRecord->sd_appointment_date ? \Carbon\Carbon::parse($sdRecord->sd_appointment_date)->format('M Y') : '' }}
                            — {{ $sdRecord->estimated_end_date  ? \Carbon\Carbon::parse($sdRecord->estimated_end_date)->format('M Y')  : 'End of Study' }}
                        @endif
                        @if(!$loop->last)<br>@endif
                    @endif
                @empty
                    N/A
                @endforelse
            </td>
        </tr>
    </table>
</div>

{{-- Intro paragraph --}}
<div class="intro-para {{ $statement?->status === 'final' ? 'final-mode' : '' }}" id="introParagraph">
    {!! nl2br(e($statement?->intro_text ?? "Quality Assurance Inspections of the above referenced study were conducted according to the procedures described in the Standard Operating Procedures of the Quality Assurance unit and according to general requirements of the OECD Principles of Good Laboratory Practice on the dates given in the table below. The report has been audited to ensure that it accurately describes the methods used and that the reported results accurately reflect the raw data of the study. Findings from the inspections were reported to the Facility Manager and the Study Director as also given below. In addition, facility audits are conducted twice per year and the date of the last audit is included in the statement below.")) !!}
</div>

{{-- ── Inspections Table ── --}}
@php
    function fmtDateRange($insp) {
        $start = $insp->date_start ?? $insp->date_scheduled;
        $end   = $insp->date_end;
        if (!$start) return 'N/A';
        $s = \Carbon\Carbon::parse($start)->format('d/m/Y');
        if ($end && $end !== $start) {
            return $s . '<br>' . \Carbon\Carbon::parse($end)->format('d/m/Y');
        }
        return $s;
    }
    function fmtDate($d) {
        return $d ? \Carbon\Carbon::parse($d)->format('d/m/Y') : 'N/A';
    }
@endphp

<table class="inspections-table">
    <thead>
        <tr>
            <th style="width:35%;">Types of Inspections</th>
            <th style="width:18%;">Inspection dates</th>
            <th style="width:18%;">Date of QA Report to Facility Manager</th>
            <th style="width:18%;">Date of QA report to Study Director</th>
        </tr>
    </thead>
    <tbody>

        {{-- ── PROTOCOL (with protocol code as name) ── --}}
        @foreach($groupedInspections['protocol'] as $insp)
        @php
            $protocolLabel = $project->protocol_code
                ? 'Protocol (N° ' . $project->protocol_code . ')'
                : ($insp->inspection_name ?? 'Study Protocol Inspection');
        @endphp
        <tr>
            <td>{{ $protocolLabel }}</td>
            <td class="date-cell">{!! fmtDateRange($insp) !!}</td>
            <td class="date-cell">{{ fmtDate($insp->date_report_fm) }}</td>
            <td class="date-cell">{{ fmtDate($insp->date_report_sd) }}</td>
        </tr>
        @endforeach

        {{-- ── PROTOCOL AMENDMENTS (N/A if none) ── --}}
        @if($groupedInspections['amendments']->isNotEmpty())
            @foreach($groupedInspections['amendments'] as $insp)
            <tr>
                <td>{{ $insp->inspection_name ?? 'Protocol amendment' }}</td>
                <td class="date-cell">{!! fmtDateRange($insp) !!}</td>
                <td class="date-cell">{{ fmtDate($insp->date_report_fm) }}</td>
                <td class="date-cell">{{ fmtDate($insp->date_report_sd) }}</td>
            </tr>
            @endforeach
        @else
            <tr>
                <td>Protocol amendment</td>
                <td class="date-cell na-cell">N/A</td>
                <td class="date-cell na-cell">N/A</td>
                <td class="date-cell na-cell">N/A</td>
            </tr>
        @endif

        {{-- ── PROTOCOL DEVIATIONS (N/A if none) ── --}}
        @if($groupedInspections['deviations']->isNotEmpty())
            @foreach($groupedInspections['deviations'] as $insp)
            <tr>
                <td>{{ $insp->inspection_name ?? 'Protocol deviation' }}</td>
                <td class="date-cell">{!! fmtDateRange($insp) !!}</td>
                <td class="date-cell">{{ fmtDate($insp->date_report_fm) }}</td>
                <td class="date-cell">{{ fmtDate($insp->date_report_sd) }}</td>
            </tr>
            @endforeach
        @else
            <tr>
                <td>Protocol deviation</td>
                <td class="date-cell na-cell">N/A</td>
                <td class="date-cell na-cell">N/A</td>
                <td class="date-cell na-cell">N/A</td>
            </tr>
        @endif

        {{-- ── CRITICAL PHASES ── --}}
        @if($groupedInspections['critical_phases']->isNotEmpty())
        <tr class="section-header">
            <td colspan="4">CRITICAL PHASES</td>
        </tr>
        @foreach($groupedInspections['critical_phases'] as $insp)
        <tr>
            <td class="sub-item">{{ $insp->inspection_name ?? $insp->type_inspection }}</td>
            <td class="date-cell">{!! fmtDateRange($insp) !!}</td>
            <td class="date-cell">{{ fmtDate($insp->date_report_fm) }}</td>
            <td class="date-cell">{{ fmtDate($insp->date_report_sd) }}</td>
        </tr>
        @endforeach
        @endif

        {{-- ── DATA QUALITY ── --}}
        @if($groupedInspections['data_quality']->isNotEmpty())
        @foreach($groupedInspections['data_quality'] as $insp)
        <tr>
            <td>{{ $insp->inspection_name ?? 'Data Quality Inspections' }}</td>
            <td class="date-cell">{!! fmtDateRange($insp) !!}</td>
            <td class="date-cell">{{ fmtDate($insp->date_report_fm) }}</td>
            <td class="date-cell">{{ fmtDate($insp->date_report_sd) }}</td>
        </tr>
        @endforeach
        @endif

        {{-- ── STUDY REPORTS ── --}}
        @if($groupedInspections['report']->isNotEmpty())
        @foreach($groupedInspections['report'] as $insp)
        @php
            $label = match($insp->type_inspection) {
                'Study Report Inspection'           => $insp->inspection_name ?? 'Draft/Final Report',
                'Study Report Amendment Inspection' => $insp->inspection_name ?? 'Report Amendment',
                default                             => $insp->inspection_name ?? $insp->type_inspection,
            };
        @endphp
        <tr>
            <td>{{ $label }}</td>
            <td class="date-cell">{!! fmtDateRange($insp) !!}</td>
            <td class="date-cell">{{ fmtDate($insp->date_report_fm) }}</td>
            <td class="date-cell">{{ fmtDate($insp->date_report_sd) }}</td>
        </tr>
        @endforeach
        @endif

        {{-- ── FACILITY INSPECTIONS (year {{ $statementYear }} only, grouped by site) ── --}}
        <tr class="section-header">
            <td colspan="4">TEST FACILITY INSPECTION</td>
        </tr>
        @php
            $facilityCotonou = $groupedInspections['facility']->where('facility_location', 'cotonou')->values();
            $facilityCove    = $groupedInspections['facility']->where('facility_location', 'cove')->values();
            $facilityOther   = $groupedInspections['facility']->whereNotIn('facility_location', ['cotonou','cove'])->values();
        @endphp
        @foreach($facilityCotonou as $insp)
        <tr>
            <td class="sub-item">{{ $insp->inspection_name ?? 'Main Facility (Cotonou)' }}</td>
            <td class="date-cell">{!! fmtDateRange($insp) !!}</td>
            <td class="date-cell">{{ fmtDate($insp->date_report_fm) }}</td>
            <td class="date-cell">{{ fmtDate($insp->date_report_sd) }}</td>
        </tr>
        @endforeach
        @foreach($facilityCove as $insp)
        <tr>
            <td class="sub-item">{{ $insp->inspection_name ?? 'Field site (Covè)' }}</td>
            <td class="date-cell">{!! fmtDateRange($insp) !!}</td>
            <td class="date-cell">{{ fmtDate($insp->date_report_fm) }}</td>
            <td class="date-cell">{{ fmtDate($insp->date_report_sd) }}</td>
        </tr>
        @endforeach
        @foreach($facilityOther as $insp)
        <tr>
            <td class="sub-item">
                {{ $insp->inspection_name ?? 'Facility Inspection' }}
                @if($insp->facility_location)
                    ({{ ucfirst($insp->facility_location) }})
                @endif
            </td>
            <td class="date-cell">{!! fmtDateRange($insp) !!}</td>
            <td class="date-cell">{{ fmtDate($insp->date_report_fm) }}</td>
            <td class="date-cell">{{ fmtDate($insp->date_report_sd) }}</td>
        </tr>
        @endforeach
        @if($groupedInspections['facility']->isEmpty())
        <tr>
            <td class="sub-item" style="font-style:italic;color:#888;">Aucune inspection de facilité enregistrée pour {{ $statementYear }}</td>
            <td class="date-cell na-cell">N/A</td>
            <td class="date-cell na-cell">N/A</td>
            <td class="date-cell na-cell">N/A</td>
        </tr>
        @endif

    </tbody>
</table>

{{-- ── N/A note ── --}}
<div style="font-size:8pt;margin-bottom:10px;"><em>N/A = not applicable</em></div>

{{-- ── Signature section ── --}}
<div class="signature-section">
    <table>
        <tr>
            <td style="width:50%;">
                <div class="signature-line">
                    {{-- Signature image could go here --}}
                    &nbsp;
                </div>
                <div><strong>Signed:</strong></div>
            </td>
            <td style="width:50%;"></td>
        </tr>
        <tr>
            <td>
                <strong>Name:</strong> {{ $qaManager ? $qaManager->prenom . ' ' . $qaManager->nom : '___________________' }}
            </td>
            <td>
                <strong>Date:</strong> {{ $statement?->date_signed ? \Carbon\Carbon::parse($statement->date_signed)->format('d/m/Y') : '___/___/______' }}
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <strong>Position:</strong> {{ $qaManager?->function ?? 'QA Manager, Quality Assurance Unit' }}
            </td>
        </tr>
    </table>
</div>

{{-- ── SANAS / GLP footer ── --}}
<div class="sanas-footer">
    SANAS OECD GLP COMPLIANT FACILITY N° G0028
</div>
<div class="page-num">Page 1 of 1</div>

{{-- ── Status watermark if draft ── --}}
@if(!$statement || $statement->status === 'draft')
<div style="position:fixed;top:40%;left:20%;font-size:72pt;color:rgba(200,0,0,.08);transform:rotate(-30deg);pointer-events:none;font-weight:900;z-index:0;" class="screen-only">
    BROUILLON
</div>
@endif

<script>
function saveQaStatement() {
    const errEl = document.getElementById('qasEditError');
    errEl.style.display = 'none';

    const fd = new FormData();
    fd.append('_token',          '{{ csrf_token() }}');
    fd.append('project_id',      document.getElementById('qasProjectId').value);
    fd.append('status',          document.getElementById('qasStatus').value);
    fd.append('date_signed',     document.getElementById('qasDateSigned').value);
    fd.append('intro_text',      document.getElementById('qasIntroText').value);
    fd.append('report_number',   document.getElementById('qasReportNumber').value);
    fd.append('doc_ref',         document.getElementById('qasDocRef').value);
    fd.append('doc_issue_date',  document.getElementById('qasDocIssueDate').value);
    fd.append('doc_next_review', document.getElementById('qasDocNextReview').value);

    fetch('{{ route("saveQaStatement") }}', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                // Refresh to reflect saved changes
                location.reload();
            } else {
                errEl.textContent = data.message || 'Erreur lors de l\'enregistrement.';
                errEl.style.display = 'block';
            }
        })
        .catch(() => {
            errEl.textContent = 'Erreur réseau.';
            errEl.style.display = 'block';
        });
}
</script>

</body>
</html>

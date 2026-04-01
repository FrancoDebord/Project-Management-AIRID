<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QA Findings Response (Follow-Up) – {{ $inspection->inspection_name ?? $inspection->type_inspection }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            color: #000;
            background: #fff;
        }

        /* ── Print & screen page layout (landscape A4) ── */
        @media screen {
            body { background: #e0e0e0; padding: 20px; }
            .page {
                background: #fff;
                width: 297mm;
                min-height: 210mm;
                margin: 0 auto 20px;
                padding: 10mm 15mm 15mm 15mm;
                box-shadow: 0 2px 8px rgba(0,0,0,0.3);
                position: relative;
            }
        }
        @media print {
            @page { size: A4 landscape; margin: 0; }
            body { background: #fff; margin: 0; padding: 0; }
            .page {
                width: 297mm;
                min-height: 210mm;
                padding: 10mm 15mm 15mm 15mm;
                page-break-after: always;
                position: relative;
            }
            .page:last-child { page-break-after: avoid; }
            .no-print { display: none !important; }
        }

        /* ── Header ── */
        .report-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1.5px solid #333;
            padding-bottom: 5px;
            margin-bottom: 12px;
        }/* ── Report title ── */
        .report-title {
            text-align: center;
            margin: 10px 0 12px;
        }
        .report-title h1 {
            font-size: 13pt;
            font-weight: bold;
            color: #1a3a6b;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            border-top: 3px solid #c41230;
            border-bottom: 3px solid #c41230;
            display: inline-block;
            padding: 5px 18px;
        }

        /* ── Meta info lines ── */
        .meta-info {
            margin-bottom: 10px;
            font-size: 10pt;
        }
        .meta-info p {
            margin-bottom: 4px;
        }
        .meta-info strong {
            color: #1a3a6b;
        }

        /* ── Follow-up table ── */
        .followup-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 14px;
        }
        .followup-table th {
            background: #1a3a6b;
            color: #fff;
            font-weight: bold;
            padding: 6px 6px;
            border: 1px solid #0f2550;
            text-align: center;
            vertical-align: middle;
            line-height: 1.3;
        }
        .followup-table td {
            padding: 7px 6px;
            border: 1px solid #aab4c8;
            vertical-align: top;
        }
        .followup-table tr:nth-child(even) td { background: #f4f6fb; }
        .followup-table .num-cell {
            text-align: center;
            font-weight: bold;
            width: 3%;
        }
        .followup-table .blank-cell {
            min-height: 50px;
            height: 55px;
            color: #bbb;
            font-style: italic;
            font-size: 7.5pt;
        }
        .followup-table .deadline-cell {
            text-align: center;
            font-weight: bold;
        }
        .no-findings {
            text-align: center;
            font-style: italic;
            color: #888;
            padding: 20px;
        }

        /* ── Section heading ── */
        .section-heading {
            background: #1a3a6b;
            color: #fff;
            font-size: 9.5pt;
            font-weight: bold;
            padding: 4px 10px;
            margin: 12px 0 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-left: 4px solid #c41230;
        }

        /* ── Signature block ── */
        .signature-row {
            display: flex;
            gap: 25px;
            margin-top: 18px;
        }
        .signature-box {
            flex: 1;
            border-top: 2px solid #c41230;
            padding-top: 5px;
        }
        .signature-box .sig-label {
            font-weight: bold;
            font-size: 9pt;
            color: #1a3a6b;
        }
        .signature-box .sig-name {
            font-size: 9pt;
            margin-top: 2px;
            color: #333;
        }
        .signature-box .sig-date {
            font-size: 8.5pt;
            color: #555;
            margin-top: 60px;
        }
        .signature-box .sig-date-line {
            font-size: 8.5pt;
            color: #555;
            margin-top: 14px;
        }

        /* ── Page number ── */
        .page-number {
            position: absolute;
            bottom: 10mm;
            right: 15mm;
            font-size: 8.5pt;
            color: #999;
        }

        /* ── Print button ── */
        .print-btn-area {
            text-align: center;
            margin-bottom: 20px;
        }
        .print-btn-area button {
            background: #1a3a6b;
            color: #fff;
            border: none;
            padding: 10px 30px;
            font-size: 13pt;
            border-radius: 5px;
            cursor: pointer;
        }
        .print-btn-area button:hover { background: #2a5aaa; }
    </style>
</head>
<body>

<div class="no-print print-btn-area">
    <button onclick="window.print()">&#128438; Imprimer / Enregistrer en PDF</button>
    @if($inspection->project_id)
    <a href="{{ '/project/create?project_id=' . $inspection->project_id . '#step6' }}"
       style="margin-left:15px;font-size:11pt;color:#1a3a6b;">← Retour au projet</a>
    @else
    <a href="{{ route('qaDashboard') }}"
       style="margin-left:15px;font-size:11pt;color:#1a3a6b;">← Dashboard QA</a>
    @endif
    <a href="{{ route('checklist.report', $inspection->id) }}"
       style="margin-left:15px;font-size:11pt;color:#555;">← QA Unit Report</a>
</div>

@php
    $project         = $inspection->project;
    $inspector       = $inspection->inspector;
    $studyDirector   = $project?->studyDirector;
    $qaManager       = $keyPersonnels['quality_assurance'] ?? null;
    $facilityManager = $keyPersonnels['facility_manager'] ?? null;
    $inspName        = $inspection->inspection_name ?? $inspection->type_inspection ?? '—';

    $inspDate = $inspection->date_performed
                    ? \Carbon\Carbon::parse($inspection->date_performed)->format('d/m/Y')
                    : ($inspection->date_scheduled
                        ? \Carbon\Carbon::parse($inspection->date_scheduled)->format('d/m/Y')
                        : '—');

    $isFacilityOrProcess = in_array($inspection->type_inspection, ['Facility Inspection', 'Process Inspection']);
    $sectionsMeta = $sectionsMeta ?? [];
    $inspectorIsManager = $inspector && $qaManager && $inspector->id === $qaManager->id;

    // Non-conformity findings only
    $ncFindings = $inspection->findings->filter(fn($f) => !$f->is_conformity)->values();

    // Meeting date from first finding that has one
    $meetingDate = $ncFindings->first(fn($f) => $f->meeting_date)?->meeting_date
                   ?? $inspection->findings->first(fn($f) => $f->meeting_date)?->meeting_date;

    $fullName = function($person) {
        if (!$person) return '—';
        return trim(($person->titre_personnel ? $person->titre_personnel . ' ' : '') . $person->prenom . ' ' . $person->nom);
    };

    $deadlineDisplay = function($finding) {
        if ($finding->deadline_date) {
            return \Carbon\Carbon::parse($finding->deadline_date)->format('d/m/Y');
        }
        return $finding->deadline_text ?? '—';
    };
@endphp

{{-- ══════════════════════════════════════════════════════════════
     PAGE — Follow-Up Table
═══════════════════════════════════════════════════════════════ --}}
<div class="page">

    {{-- Header --}}
    <div class="report-header">
        <img src="{{ asset('storage/assets/header/entete_airid.png') }}" alt="AIRID — African Institute for Research in Infectious Diseases">
    </div>
        

    {{-- Title --}}
    <div class="report-title">
        <h1>Quality Assurance Findings Response (Follow-Up)</h1>
    </div>

    {{-- Meta info --}}
    <div class="meta-info">
        <p><strong>Type of Inspection:</strong> {{ $inspName }}</p>
        <p><strong>Inspection Date:</strong> {{ $inspDate }}</p>
        <p><strong>Meeting Date:</strong>
            @if($meetingDate)
                {{ \Carbon\Carbon::parse($meetingDate)->format('d/m/Y') }}
            @else
                ___________________________
            @endif
        </p>
    </div>

    {{-- Follow-up table --}}
    @if($ncFindings->isEmpty())
        <p class="no-findings" style="padding:20px 0;">
            No non-conformity findings recorded for this inspection.
        </p>
    @elseif($isFacilityOrProcess && !empty($sectionsMeta))
        @php
            $ncBySection = $ncFindings->groupBy('facility_section');
            $fuRowNum = 1;
        @endphp
        @foreach($sectionsMeta as $slug => $sectionTitle)
            @php $secNcFindings = $ncBySection->get($slug, collect()); @endphp
            @if($secNcFindings->isNotEmpty())
            <div class="section-heading" style="margin-top:8px;">{{ $sectionTitle }}</div>
            <table class="followup-table">
                <thead>
                    <tr>
                        <th style="width:3%">#</th>
                        <th style="width:22%">QA Findings</th>
                        <th style="width:18%">Action points</th>
                        <th style="width:9%">Deadlines for<br>corrective actions</th>
                        <th style="width:12%">Signature of<br>Responder(s)</th>
                        <th style="width:14%">Observations after<br>follow-up</th>
                        <th style="width:14%">Means of verification for<br>implementation of<br>corrective actions</th>
                        <th style="width:8%">QA Personnel<br>Signature &amp; Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($secNcFindings as $finding)
                    <tr>
                        <td class="num-cell">{{ $fuRowNum++ }}</td>
                        <td>{{ $finding->finding_text ?? '—' }}</td>
                        <td>{{ $finding->action_point ?? '' }}</td>
                        <td class="deadline-cell">{{ $deadlineDisplay($finding) }}</td>
                        <td class="blank-cell">
                            {{ $finding->resolved_by_name ?? ($finding->assignedTo ? $fullName($finding->assignedTo) : '') }}
                            @if($finding->meeting_date)<br><small style="color:#555;">{{ \Carbon\Carbon::parse($finding->meeting_date)->format('d/m/Y') }}</small>@endif
                        </td>
                        <td class="blank-cell"></td>
                        <td class="blank-cell">{{ $finding->means_of_verification ?? '' }}</td>
                        <td class="blank-cell"></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        @endforeach
        @php $orphanNc = $ncFindings->filter(fn($f) => !$f->facility_section); @endphp
        @if($orphanNc->isNotEmpty())
        <div class="section-heading" style="margin-top:8px;">Autres</div>
        <table class="followup-table">
            <thead>
                <tr>
                    <th style="width:3%">#</th>
                    <th style="width:22%">QA Findings</th>
                    <th style="width:18%">Action points</th>
                    <th style="width:9%">Deadlines for<br>corrective actions</th>
                    <th style="width:12%">Signature of<br>Responder(s)</th>
                    <th style="width:14%">Observations after<br>follow-up</th>
                    <th style="width:14%">Means of verification for<br>implementation of<br>corrective actions</th>
                    <th style="width:8%">QA Personnel<br>Signature &amp; Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orphanNc as $finding)
                <tr>
                    <td class="num-cell">{{ $fuRowNum++ }}</td>
                    <td>{{ $finding->finding_text ?? '—' }}</td>
                    <td>{{ $finding->action_point ?? '' }}</td>
                    <td class="deadline-cell">{{ $deadlineDisplay($finding) }}</td>
                    <td class="blank-cell">{{ $finding->assignedTo ? $fullName($finding->assignedTo) : '' }}</td>
                    <td class="blank-cell"></td>
                    <td class="blank-cell">{{ $finding->means_of_verification ?? '' }}</td>
                    <td class="blank-cell"></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    @else
        <table class="followup-table">
            <thead>
                <tr>
                    <th style="width:3%">#</th>
                    <th style="width:22%">QA Findings</th>
                    <th style="width:18%">Action points</th>
                    <th style="width:9%">Deadlines for<br>corrective actions</th>
                    <th style="width:12%">Signature of<br>Responder(s)</th>
                    <th style="width:14%">Observations after<br>follow-up</th>
                    <th style="width:14%">Means of verification for<br>implementation of<br>corrective actions</th>
                    <th style="width:8%">QA Personnel<br>Signature &amp; Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ncFindings as $i => $finding)
                <tr>
                    <td class="num-cell">{{ $i + 1 }}</td>
                    <td>{{ $finding->finding_text ?? '—' }}</td>
                    <td>{{ $finding->action_point ?? '' }}</td>
                    <td class="deadline-cell">{{ $deadlineDisplay($finding) }}</td>
                    <td class="blank-cell">
                        @if($finding->assignedTo)
                            {{ $fullName($finding->assignedTo) }}
                        @endif
                    </td>
                    <td class="blank-cell"></td>
                    <td class="blank-cell">{{ $finding->means_of_verification ?? '' }}</td>
                    <td class="blank-cell"></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Signatures --}}
    <div class="signature-row">
        @if($studyDirector)
        <div class="signature-box">
            <div class="sig-label">Study Director</div>
            <div class="sig-name">{{ $fullName($studyDirector) }}</div>
            <div class="sig-date">Signature : _________________________</div>
            <div class="sig-date-line">Date : ___________</div>
        </div>
        @endif
        @if(!$inspectorIsManager)
        <div class="signature-box">
            <div class="sig-label">QA Inspector</div>
            <div class="sig-name">{{ $fullName($inspector) }}</div>
            <div class="sig-date">Signature : _________________________</div>
            <div class="sig-date-line">Date : ___________</div>
        </div>
        @endif
        <div class="signature-box">
            <div class="sig-label">QA Manager</div>
            <div class="sig-name">{{ $fullName($qaManager) }}</div>
            <div class="sig-date">Signature : _________________________</div>
            <div class="sig-date-line">Date : ___________</div>
        </div>
        <div class="signature-box">
            <div class="sig-label">Facility Manager</div>
            <div class="sig-name">{{ $fullName($facilityManager) }}</div>
            <div class="sig-date">Signature : _________________________</div>
            <div class="sig-date-line">Date : ___________</div>
        </div>
    </div>

    <div class="page-number doc-page-num"></div>
</div>

<script>
(function () {
    const pages = document.querySelectorAll('.page');
    const total = pages.length;
    pages.forEach(function (page, idx) {
        const el = page.querySelector('.doc-page-num');
        if (el) el.textContent = (idx + 1) + ' / ' + total;
    });
})();
</script>

</body>
</html>

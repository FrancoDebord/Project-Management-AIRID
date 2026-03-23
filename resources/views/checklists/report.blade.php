<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QA Unit Report – {{ $inspection->inspection_name ?? $inspection->type_inspection }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            color: #000;
            background: #fff;
        }

        /* ── Print & screen page layout ── */
        @media screen {
            body { background: #e0e0e0; padding: 20px; }
            .page {
                background: #fff;
                width: 210mm;
                min-height: 297mm;
                margin: 0 auto 20px;
                padding: 10mm 15mm 15mm 15mm;
                box-shadow: 0 2px 8px rgba(0,0,0,0.3);
                position: relative;
            }
        }
        @media print {
            body { background: #fff; margin: 0; padding: 0; }
            .page {
                width: 210mm;
                min-height: 297mm;
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
        }
        .report-header .logo-area img {
            height: 42px;
            object-fit: contain;
        }
        .report-header .org-block {
            flex: 1;
            text-align: center;
            padding: 0 10px;
        }
        .report-header .org-block .org-name {
            font-size: 11pt;
            font-weight: bold;
            color: #000;
            text-transform: uppercase;
            line-height: 1.2;
        }
        .report-header .contact-block {
            text-align: right;
            font-size: 7.5pt;
            color: #333;
            line-height: 1.5;
        }

        /* ── Report title ── */
        .report-title {
            text-align: center;
            margin-bottom: 14px;
        }
        .report-title h1 {
            font-size: 15pt;
            font-weight: bold;
            color: #1a3a6b;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-top: 3px solid #c41230;
            border-bottom: 3px solid #c41230;
            display: inline-block;
            padding: 5px 18px;
        }
        .report-title .subtitle {
            font-size: 10pt;
            color: #555;
            margin-top: 4px;
        }

        /* ── Info table ── */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .info-table td {
            padding: 5px 8px;
            font-size: 10.5pt;
            vertical-align: top;
        }
        .info-table td.label {
            font-weight: bold;
            color: #1a3a6b;
            width: 38%;
            border-bottom: 1px solid #ddd;
            white-space: nowrap;
        }
        .info-table td.value {
            border-bottom: 1px solid #ddd;
        }

        /* ── Section heading ── */
        .section-heading {
            background: #1a3a6b;
            color: #fff;
            font-size: 10.5pt;
            font-weight: bold;
            padding: 4px 10px;
            margin: 14px 0 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-left: 4px solid #c41230;
        }

        /* ── Findings table ── */
        .findings-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 10pt;
        }
        .findings-table th {
            background: #d0d8e8;
            color: #1a3a6b;
            font-weight: bold;
            padding: 6px 8px;
            border: 1px solid #b0b8c8;
            text-align: left;
        }
        .findings-table td {
            padding: 7px 8px;
            border: 1px solid #c0c8d8;
            vertical-align: top;
        }
        .findings-table tr:nth-child(even) td { background: #f4f6fb; }
        .findings-table .no-findings {
            text-align: center;
            font-style: italic;
            color: #888;
            padding: 15px;
        }

        /* ── Signature block ── */
        .signature-section {
            margin-top: 25px;
        }
        .signature-row {
            display: flex;
            gap: 30px;
            margin-top: 20px;
        }
        .signature-box {
            flex: 1;
            border-top: 2px solid #c41230;
            padding-top: 6px;
        }
        .signature-box .sig-label {
            font-weight: bold;
            font-size: 10pt;
            color: #1a3a6b;
        }
        .signature-box .sig-name {
            font-size: 10pt;
            margin-top: 3px;
            color: #333;
        }
        .signature-box .sig-date {
            font-size: 9.5pt;
            color: #555;
            margin-top: 30px;
        }
        .signature-box .sig-date-line {
            font-size: 9.5pt;
            color: #555;
            margin-top: 14px;
        }
        .signature-box .sig-space {
            height: 40px;
        }

        /* ── Minutes table ── */
        .minutes-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 10pt;
        }
        .minutes-table th {
            background: #d0d8e8;
            color: #1a3a6b;
            font-weight: bold;
            padding: 6px 8px;
            border: 1px solid #b0b8c8;
            text-align: left;
        }
        .minutes-table td {
            padding: 8px;
            border: 1px solid #c0c8d8;
            vertical-align: top;
        }
        .minutes-table .empty-row td {
            height: 30px;
        }

        /* ── Distributed to ── */
        .distributed-to {
            margin-top: 8px;
            font-size: 10pt;
        }
        .distributed-to span {
            display: inline-block;
            background: #f0f3fa;
            border: 1px solid #c0c8d8;
            border-radius: 3px;
            padding: 2px 10px;
            margin: 3px 4px 3px 0;
        }

        /* ── Page number ── */
        .page-number {
            position: absolute;
            bottom: 12mm;
            right: 15mm;
            font-size: 9pt;
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

        .text-muted { color: #777; font-style: italic; }
        .badge-status-pending   { background: #fff3cd; color: #856404; padding: 1px 7px; border-radius: 10px; font-size: 9pt; }
        .badge-status-complete  { background: #d1e7dd; color: #0f5132; padding: 1px 7px; border-radius: 10px; font-size: 9pt; }
    </style>
</head>
<body>

<div class="no-print print-btn-area">
    <button onclick="window.print()">&#128438; Imprimer / Enregistrer en PDF</button>
    <a href="{{ route('checklist.followup', $inspection->id) }}"
       style="margin-left:15px;font-size:11pt;color:#1a3a6b;">&#128196; Follow-Up Report</a>
    @if($inspection->project_id)
    <a href="{{ '/project/create?project_id=' . $inspection->project_id . '#step6' }}"
       style="margin-left:15px;font-size:11pt;color:#555;">← Retour au projet</a>
    @else
    <a href="{{ route('qaDashboard') }}"
       style="margin-left:15px;font-size:11pt;color:#555;">← Dashboard QA</a>
    @endif
</div>

@php
    $project         = $inspection->project;
    $inspector       = $inspection->inspector;
    $studyDirector   = $project?->studyDirector;
    $qaManager       = $keyPersonnels['quality_assurance'] ?? null;
    $facilityManager = $keyPersonnels['facility_manager'] ?? null;
    $findings        = $inspection->findings;
    $inspName        = $inspection->inspection_name ?? $inspection->type_inspection ?? '—';
    $inspDate        = $inspection->date_performed
                          ? \Carbon\Carbon::parse($inspection->date_performed)->format('d/m/Y')
                          : ($inspection->date_scheduled
                              ? \Carbon\Carbon::parse($inspection->date_scheduled)->format('d/m/Y')
                              : '—');
    $reportDate      = now()->format('d/m/Y');
    $isFacilityOrProcess = in_array($inspection->type_inspection, ['Facility Inspection', 'Process Inspection']);
    $sectionsMeta = $sectionsMeta ?? [];

    $fullName = function($person) {
        if (!$person) return '—';
        return trim(($person->titre_personnel ? $person->titre_personnel . ' ' : '') . $person->prenom . ' ' . $person->nom);
    };
    $inspectorIsManager = $inspector && $qaManager && $inspector->id === $qaManager->id;
@endphp

{{-- ══════════════════════════════════════════════════════════════
     PAGE 1 — Inspection details & Findings
═══════════════════════════════════════════════════════════════ --}}
<div class="page">

    {{-- Header --}}
    @php $logoPath = public_path('storage/assets/logo/airid.png'); @endphp
    <div class="report-header">
        <div class="logo-area">
            <img src="{{ asset('storage/assets/logo/airid.png') }}" alt="AIRID">
        </div>
        <div class="org-block">
            <div class="org-name">African Institute for Research<br>in Infectious Diseases</div>
        </div>
        <div class="contact-block">
            IFU: 6202213991612<br>
            LOT 5507, Donaten Cotonou, Benin<br>
            Tel: +229 0167128862<br>
            Email: admin@airid-africa.com<br>
            www.airid-africa.com
        </div>
    </div>

    {{-- Title --}}
    <div class="report-title">
        <h1>Quality Assurance Unit Report</h1>
        <div class="subtitle">Inspection Report — {{ $inspName }}</div>
    </div>

    {{-- Study information --}}
    <div class="section-heading">Study Information</div>
    <table class="info-table">
        <tr>
            <td class="label">Type of Inspection</td>
            <td class="value">{{ $inspName }}</td>
        </tr>
        @if($project)
        <tr>
            <td class="label">Study Code</td>
            <td class="value">{{ $project->project_code ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Study Title</td>
            <td class="value">{{ $project->project_name ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Study Director</td>
            <td class="value">{{ $fullName($studyDirector) }}</td>
        </tr>
        @if($studyDirector?->titre_qualitification)
        <tr>
            <td class="label">Study Director Qualification</td>
            <td class="value">{{ $studyDirector->titre_qualitification }}</td>
        </tr>
        @endif
        @if($studyDirector?->email_professionnel || $studyDirector?->email_personnel)
        <tr>
            <td class="label">Study Director Email</td>
            <td class="value">{{ $studyDirector->email_professionnel ?? $studyDirector->email_personnel }}</td>
        </tr>
        @endif
        @endif
        <tr>
            <td class="label">Date of Inspection</td>
            <td class="value">{{ $inspDate }}</td>
        </tr>
        <tr>
            <td class="label">Date of Report</td>
            <td class="value">{{ $reportDate }}</td>
        </tr>
    </table>

    {{-- QA Inspector Details (only shown when inspector ≠ QA Manager) --}}
    @if(!$inspectorIsManager)
    <div class="section-heading">Quality Assurance Inspector Details</div>
    <table class="info-table">
        <tr>
            <td class="label">QA Inspector Name</td>
            <td class="value">{{ $fullName($inspector) }}</td>
        </tr>
        @if($inspector?->email_professionnel || $inspector?->email_personnel)
        <tr>
            <td class="label">Email</td>
            <td class="value">{{ $inspector->email_professionnel ?? $inspector->email_personnel }}</td>
        </tr>
        @endif
        @if($inspector?->titre_qualitification)
        <tr>
            <td class="label">Qualification</td>
            <td class="value">{{ $inspector->titre_qualitification }}</td>
        </tr>
        @endif
    </table>
    @endif

    {{-- QA Findings --}}
    <div class="section-heading">QA Findings</div>

    @php
        $nonConformities = $findings->filter(fn($f) => !$f->is_conformity);
        $allConform      = $findings->isNotEmpty() && $nonConformities->isEmpty();
    @endphp

    @if($findings->isEmpty())
        <p class="text-muted" style="padding:10px 0;">No findings recorded for this inspection.</p>
    @elseif($isFacilityOrProcess && !empty($sectionsMeta))
        @php
            $findingsBySection = $findings->groupBy('facility_section');
            $rowNum = 1;
        @endphp
        @foreach($sectionsMeta as $slug => $sectionTitle)
            @php $sectionFindings = $findingsBySection->get($slug, collect()); @endphp
            @if($sectionFindings->isNotEmpty())
            <div style="font-weight:700; font-size:9.5pt; color:#1a3a6b; margin: 10px 0 4px; border-left:3px solid #c41230; padding-left:6px; text-transform:uppercase;">
                {{ $sectionTitle }}
            </div>
            <table class="findings-table">
                <thead>
                    <tr>
                        <th style="width:5%">#</th>
                        <th style="width:10%">Type</th>
                        <th style="width:55%">Finding</th>
                        <th style="width:18%">Deadline</th>
                        <th style="width:12%">Assigned To</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sectionFindings as $finding)
                    <tr>
                        <td style="text-align:center;font-weight:bold;">{{ $rowNum++ }}</td>
                        <td style="text-align:center;">
                            @if($finding->is_conformity)
                                <span class="badge-status-complete">Conform.</span>
                            @else
                                <span class="badge-status-pending">Non-conf.</span>
                            @endif
                        </td>
                        <td>{{ $finding->finding_text ?? '—' }}</td>
                        <td>
                            @if($finding->deadline_date)
                                {{ \Carbon\Carbon::parse($finding->deadline_date)->format('d/m/Y') }}
                            @elseif($finding->deadline_text)
                                {{ $finding->deadline_text }}
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @if($finding->assignedTo)
                                {{ $finding->assignedTo->prenom }} {{ $finding->assignedTo->nom }}
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        @endforeach
        @php
            // Findings without a section
            $orphanFindings = $findings->filter(fn($f) => !$f->facility_section);
        @endphp
        @if($orphanFindings->isNotEmpty())
        <div style="font-weight:700; font-size:9.5pt; color:#1a3a6b; margin: 10px 0 4px; border-left:3px solid #c41230; padding-left:6px; text-transform:uppercase;">
            Autres / Non classifiés
        </div>
        <table class="findings-table">
            <thead>
                <tr>
                    <th style="width:5%">#</th>
                    <th style="width:10%">Type</th>
                    <th style="width:55%">Finding</th>
                    <th style="width:18%">Deadline</th>
                    <th style="width:12%">Assigned To</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orphanFindings as $finding)
                <tr>
                    <td style="text-align:center;font-weight:bold;">{{ $rowNum++ }}</td>
                    <td style="text-align:center;">
                        @if($finding->is_conformity)
                            <span class="badge-status-complete">Conform.</span>
                        @else
                            <span class="badge-status-pending">Non-conf.</span>
                        @endif
                    </td>
                    <td>{{ $finding->finding_text ?? '—' }}</td>
                    <td>{{ $finding->deadline_date ? \Carbon\Carbon::parse($finding->deadline_date)->format('d/m/Y') : ($finding->deadline_text ?? '—') }}</td>
                    <td>{{ $finding->assignedTo ? $finding->assignedTo->prenom . ' ' . $finding->assignedTo->nom : '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
        @if($allConform)
        <p style="margin-top:10px; font-size:10.5pt; font-style:italic;">
            There were no issues raised with the <strong>{{ $inspName }}</strong>.
        </p>
        @endif
    @else
        <table class="findings-table">
            <thead>
                <tr>
                    <th style="width:5%">#</th>
                    <th style="width:10%">Type</th>
                    <th style="width:55%">Finding</th>
                    <th style="width:18%">Deadline</th>
                    <th style="width:12%">Assigned To</th>
                </tr>
            </thead>
            <tbody>
                @foreach($findings as $i => $finding)
                <tr>
                    <td style="text-align:center;font-weight:bold;">{{ $i + 1 }}</td>
                    <td style="text-align:center;">
                        @if($finding->is_conformity)
                            <span class="badge-status-complete">Conform.</span>
                        @else
                            <span class="badge-status-pending">Non-conf.</span>
                        @endif
                    </td>
                    <td>{{ $finding->finding_text ?? '—' }}</td>
                    <td>
                        @if($finding->deadline_date)
                            {{ \Carbon\Carbon::parse($finding->deadline_date)->format('d/m/Y') }}
                        @elseif($finding->deadline_text)
                            {{ $finding->deadline_text }}
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        @if($finding->assignedTo)
                            {{ $finding->assignedTo->prenom }} {{ $finding->assignedTo->nom }}
                        @else
                            —
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($allConform)
        <p style="margin-top:10px; font-size:10.5pt; font-style:italic;">
            There were no issues raised with the <strong>{{ $inspName }}</strong>.
        </p>
        @endif
    @endif

    {{-- Distributed to --}}
    <div class="section-heading">Distributed To</div>
    <div class="distributed-to">
        <span>Study Director</span>
        <span>QA Manager</span>
        <span>Facility Manager</span>
        <span>Study File</span>
    </div>
    <div style="margin-top:8px;font-size:10pt;">
        <strong>Distribution Date :</strong>&nbsp; {{ $reportDate }}
    </div>

    <div class="page-number">Page 1</div>
</div>


{{-- ══════════════════════════════════════════════════════════════
     PAGE 2 — Minutes of Meeting
═══════════════════════════════════════════════════════════════ --}}
<div class="page">

    <div class="report-header">
        <div class="logo-area">
            <img src="{{ asset('storage/assets/logo/airid.png') }}" alt="AIRID">
        </div>
        <div class="org-block">
            <div class="org-name">African Institute for Research<br>in Infectious Diseases</div>
        </div>
        <div class="contact-block">
            IFU: 6202213991612<br>
            LOT 5507, Donaten Cotonou, Benin<br>
            Tel: +229 0167128862<br>
            Email: admin@airid-africa.com<br>
            www.airid-africa.com
        </div>
    </div>

    <div class="report-title">
        <h1>Quality Assurance Unit Report</h1>
        <div class="subtitle">Minutes of Meeting — {{ $inspName }}</div>
    </div>

    <div class="section-heading">Minutes of Meeting</div>

    <table class="info-table" style="margin-bottom:14px;">
        @if($project)
        <tr>
            <td class="label">Study Code</td>
            <td class="value">{{ $project->project_code ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Study Title</td>
            <td class="value">{{ $project->project_name ?? '—' }}</td>
        </tr>
        @endif
        <tr>
            <td class="label">Date of Meeting</td>
            <td class="value">
                @if($findings->first()?->meeting_date)
                    {{ \Carbon\Carbon::parse($findings->first()->meeting_date)->format('d/m/Y') }}
                @else
                    ___________________________
                @endif
            </td>
        </tr>
    </table>

    <div class="section-heading" style="margin-top:8px;">Participants</div>

    <table class="minutes-table">
        <thead>
            <tr>
                <th style="width:5%">#</th>
                <th style="width:35%">Name</th>
                <th style="width:30%">Role / Title</th>
                <th style="width:30%">Signature</th>
            </tr>
        </thead>
        <tbody>
            @if($studyDirector)
            <tr>
                <td style="text-align:center;">1</td>
                <td>{{ $fullName($studyDirector) }}</td>
                <td>Study Director</td>
                <td>&nbsp;</td>
            </tr>
            @endif
            @php $distRow = $studyDirector ? 2 : 1; @endphp
            {{-- QA Inspector (only when different from QA Manager) --}}
            @if(!$inspectorIsManager)
            <tr>
                <td style="text-align:center;">{{ $distRow++ }}</td>
                <td>{{ $fullName($inspector) }}</td>
                <td>QA Inspector</td>
                <td>&nbsp;</td>
            </tr>
            @endif
            {{-- QA Manager --}}
            <tr>
                <td style="text-align:center;">{{ $distRow++ }}</td>
                <td>{{ $fullName($qaManager) }}</td>
                <td>QA Manager</td>
                <td>&nbsp;</td>
            </tr>
            {{-- Facility Manager --}}
            <tr>
                <td style="text-align:center;">{{ $distRow++ }}</td>
                <td>{{ $fullName($facilityManager) }}</td>
                <td>Facility Manager</td>
                <td>&nbsp;</td>
            </tr>
            @php $extra = $distRow; @endphp
            @for($r = $extra; $r <= $extra + 3; $r++)
            <tr class="empty-row">
                <td style="text-align:center;">{{ $r }}</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            @endfor
        </tbody>
    </table>

    <div class="section-heading" style="margin-top:16px;">Summary of Meeting</div>
    <div style="border:1px solid #c0c8d8;min-height:80px;padding:8px;font-size:10pt;color:#aaa;font-style:italic;">
        [To be completed during the meeting]
    </div>

    <div class="section-heading" style="margin-top:16px;">Action Items Agreed Upon</div>
    <div style="border:1px solid #c0c8d8;min-height:70px;padding:8px;font-size:10pt;color:#aaa;font-style:italic;">
        [To be completed during the meeting]
    </div>

    {{-- Final Signatures --}}
    <div class="signature-section">
        <div class="signature-row">
            @if($studyDirector)
            <div class="signature-box">
                <div class="sig-label">Study Director</div>
                <div class="sig-name">{{ $fullName($studyDirector) }}</div>
                <div class="sig-space"></div>
                <div class="sig-date">Signature : _________________________</div>
                <div class="sig-date-line">Date : ___________</div>
            </div>
            @endif
            @if(!$inspectorIsManager)
            <div class="signature-box">
                <div class="sig-label">QA Inspector</div>
                <div class="sig-name">{{ $fullName($inspector) }}</div>
                <div class="sig-space"></div>
                <div class="sig-date">Signature : _________________________</div>
                <div class="sig-date-line">Date : ___________</div>
            </div>
            @endif
            <div class="signature-box">
                <div class="sig-label">QA Manager</div>
                <div class="sig-name">{{ $fullName($qaManager) }}</div>
                <div class="sig-space"></div>
                <div class="sig-date">Signature : _________________________</div>
                <div class="sig-date-line">Date : ___________</div>
            </div>
            <div class="signature-box">
                <div class="sig-label">Facility Manager</div>
                <div class="sig-name">{{ $fullName($facilityManager) }}</div>
                <div class="sig-space"></div>
                <div class="sig-date">Signature : _________________________</div>
                <div class="sig-date-line">Date : ___________</div>
            </div>
        </div>
    </div>

    <div class="page-number">Page 2</div>
</div>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Critical Phase Impact Assessment — {{ $project->project_code }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #111; background: #fff; }

        /* ── Page layout ── */
        @media print {
            body { font-size: 9.5pt; }
            .no-print { display: none !important; }
            @page { margin: 15mm 12mm; size: A4 portrait; }
        }

        .wrapper { max-width: 800px; margin: 0 auto; padding: 20px; }

        /* ── Header block ── */
        .report-header {
            display: block;
            width: 100%;
            border-bottom: 2px solid #333;
            padding-bottom: 4px;
            margin-bottom: 0;
        }
        .report-header img {
            width: 100%;
            height: auto;
            display: block;
        }
        .doc-ref-strip {
            display: flex;
            flex-wrap: wrap;
            gap: 0;
            border: 1px solid #bbb;
            border-top: none;
            margin-bottom: 10px;
        }
        .ref-item {
            flex: 1;
            padding: 4px 10px;
            border-right: 1px solid #bbb;
            font-size: 8.5pt;
            min-width: 100px;
        }
        .ref-item:last-child { border-right: none; }
        .ref-label { font-weight: bold; color: #555; margin-right: 4px; }
        .ref-val { color: #111; }

        /* ── Project info box ── */
        .project-info {
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 14px;
            background: #f9f9f9;
        }
        .project-info table { width: 100%; border-collapse: collapse; }
        .project-info td { padding: 3px 6px; font-size: 9pt; }
        .project-info td:first-child { font-weight: bold; color: #555; width: 160px; }

        /* ── Section ── */
        .section-block { margin-bottom: 18px; break-inside: avoid; }

        .section-heading {
            background: #c10202;
            color: #fff;
            padding: 6px 12px;
            font-weight: bold;
            font-size: 10pt;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .section-heading .letter {
            background: rgba(255,255,255,.25);
            border-radius: 50%;
            width: 28px; height: 28px;
            display: flex; align-items: center; justify-content: center;
            font-size: 11pt;
            flex-shrink: 0;
        }

        /* ── Items table ── */
        table.items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }
        table.items-table thead th {
            background: #f5f5f5;
            border: 1px solid #bbb;
            padding: 5px 8px;
            font-weight: bold;
            text-align: center;
        }
        table.items-table thead th.th-item { text-align: left; }
        table.items-table tbody td {
            border: 1px solid #ccc;
            padding: 5px 8px;
            vertical-align: middle;
        }
        table.items-table tbody td.td-num {
            text-align: center;
            width: 32px;
            font-weight: bold;
            color: #c10202;
        }
        table.items-table tbody td.td-score {
            text-align: center;
            width: 80px;
        }
        table.items-table tbody td.td-sel {
            text-align: center;
            width: 60px;
        }
        table.items-table tfoot td {
            background: #f5f5f5;
            border: 1px solid #bbb;
            padding: 5px 8px;
            font-weight: bold;
        }

        /* ── Summary table ── */
        table.summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
            font-size: 9pt;
        }
        table.summary-table th {
            background: #c10202;
            color: #fff;
            border: 1px solid #999;
            padding: 5px 10px;
        }
        table.summary-table td {
            border: 1px solid #ccc;
            padding: 5px 10px;
        }
        table.summary-table tr:nth-child(even) td { background: #fafafa; }
        .grand-total-row td { background: #ffe8e8 !important; font-weight: bold; }

        /* ── Signature block ── */
        .sig-block {
            margin-top: 24px;
            border: 1px solid #ccc;
            border-radius: 6px;
            overflow: hidden;
        }
        .sig-block .sig-title {
            background: #f5f5f5;
            border-bottom: 1px solid #ccc;
            padding: 6px 12px;
            font-weight: bold;
            font-size: 9.5pt;
        }
        .sig-grid {
            display: flex;
            border-top: 1px solid #ccc;
        }
        .sig-cell {
            flex: 1;
            padding: 10px 14px;
            border-right: 1px solid #ccc;
        }
        .sig-cell:last-child { border-right: none; }
        .sig-cell .sig-label { font-size: 8pt; color: #666; margin-bottom: 4px; font-weight: bold; }
        .sig-cell .sig-name  { font-size: 9pt; margin-bottom: 6px; }
        .sig-cell .sig-img   { display: block; height: 46px; max-width: 160px; margin-bottom: 4px; }
        .sig-cell .sig-signed-by { font-size: 7.5pt; color: #444; margin-bottom: 2px; }
        .sig-cell .sig-line  { border-top: 1px solid #555; margin-top: 8px; font-size: 7.5pt; color: #888; }
        .sig-cell .sig-blank { height: 38px; }

        /* ── Print button ── */
        .print-btn {
            display: inline-block;
            background: #c10202;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 8px 20px;
            font-size: .9rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="wrapper">

    {{-- Print button --}}
    <div class="no-print mb-3 d-flex gap-2">
        <button class="print-btn" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>Print / Save PDF
        </button>
        <a href="{{ route('cpia.index', $project->id) }}" class="print-btn" style="background:#555;">
            ← Back to Form
        </a>
    </div>

    {{-- AIRID Header --}}
    <div class="report-header">
        <img src="{{ asset('storage/assets/header/entete_airid.png') }}" alt="AIRID — African Institute for Research in Infectious Diseases">
    </div>

    {{-- Doc reference strip --}}
    <div class="doc-ref-strip">
        <div class="ref-item">
            <span class="ref-label">Document:</span>
            <span class="ref-val">Critical Phase Impact Assessment</span>
        </div>
        <div class="ref-item">
            <span class="ref-label">Ref:</span>
            <span class="ref-val">QA-PR-1-015/05</span>
        </div>
        <div class="ref-item">
            <span class="ref-label">Version:</span>
            <span class="ref-val">05</span>
        </div>
        <div class="ref-item">
            <span class="ref-label">Date:</span>
            <span class="ref-val">{{ date('d/m/Y') }}</span>
        </div>
    </div>

    {{-- Project info --}}
    <div class="project-info">
        <table>
            <tr>
                <td>Project Code</td>
                <td>{{ $assessment->project_code }}</td>
                <td>Study Title</td>
                <td>{{ $assessment->study_title }}</td>
            </tr>
            <tr>
                <td>Study Director</td>
                <td colspan="3">{{ $assessment->study_director_name }}</td>
            </tr>
        </table>
    </div>

    {{-- Instructions --}}
    <div style="font-size:8.5pt;color:#555;margin-bottom:12px;line-height:1.5;border-left:3px solid #c10202;padding-left:10px;">
        The QA Manager selects the relevant sections based on the critical phases identified. For each applicable item,
        assign an impact score from <strong>0</strong> (no impact) to <strong>10</strong> (maximum impact), and mark the
        "Selected" column for phases applicable to this study.
    </div>

    {{-- Sections --}}
    @php $grandTotal = 0; $maxGrand = 0; @endphp

    @foreach ($sections as $section)
    @php
        $sectionTotal = 0;
        $sectionMax   = $section->activeItems->count() * 10;
    @endphp
    <div class="section-block">
        <div class="section-heading">
            <div class="letter">{{ $section->letter }}</div>
            <div>{{ $section->title }}</div>
        </div>
        <table class="items-table">
            <thead>
                <tr>
                    <th class="th-item" style="width:32px;">#</th>
                    <th class="th-item">Item</th>
                    <th>Impact Score /10</th>
                    <th>Selected Phase</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($section->activeItems as $item)
                @php
                    $resp = $responses[$item->id] ?? null;
                    $score = $resp ? $resp->impact_score : null;
                    if ($score !== null) $sectionTotal += $score;
                @endphp
                <tr>
                    <td class="td-num">{{ $item->item_number }}</td>
                    <td>{{ $item->text }}</td>
                    <td class="td-score">{{ $score !== null ? $score : '' }}</td>
                    <td class="td-sel">
                        @if ($resp && $resp->is_selected)
                            &#10003;
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" style="text-align:right;">Section Total:</td>
                    <td class="td-score">{{ $sectionTotal }} / {{ $sectionMax }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
    @php
        $grandTotal += $sectionTotal;
        $maxGrand   += $sectionMax;
    @endphp
    @endforeach

    {{-- Summary table --}}
    <div style="margin-top:20px;">
        <table class="summary-table">
            <thead>
                <tr>
                    <th>Section</th>
                    <th>Title</th>
                    <th style="width:100px;text-align:center;">Score</th>
                    <th style="width:100px;text-align:center;">Max</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sections as $section)
                @php
                    $st = 0;
                    foreach ($section->activeItems as $item) {
                        $r = $responses[$item->id] ?? null;
                        if ($r && $r->impact_score !== null) $st += $r->impact_score;
                    }
                @endphp
                <tr>
                    <td style="text-align:center;font-weight:bold;color:#c10202;">{{ $section->letter }}</td>
                    <td>{{ $section->title }}</td>
                    <td style="text-align:center;">{{ $st }}</td>
                    <td style="text-align:center;">{{ $section->activeItems->count() * 10 }}</td>
                </tr>
                @endforeach
                <tr class="grand-total-row">
                    <td colspan="2" style="text-align:right;">Grand Total</td>
                    <td style="text-align:center;">{{ $grandTotal }}</td>
                    <td style="text-align:center;">{{ $maxGrand }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Signatures --}}
    <div class="sig-block">
        <div class="sig-title"><i class="bi bi-pen me-1"></i>Signatures</div>
        <div class="sig-grid">
            <div class="sig-cell">
                <div class="sig-label">QA Manager</div>
                <div class="sig-name">
                    @if(isset($keyPersonnels['quality_assurance']))
                        {{ $keyPersonnels['quality_assurance']->prenom }} {{ $keyPersonnels['quality_assurance']->nom }}
                    @endif
                </div>
                @if(isset($signatures['qa_manager']))
                    <img src="{{ $signatures['qa_manager']->signature_data }}" class="sig-img" alt="signature">
                    <div class="sig-signed-by">{{ $signatures['qa_manager']->signer_name }} — {{ $signatures['qa_manager']->signed_at?->format('d/m/Y') }}</div>
                @else
                    <div class="sig-blank"></div>
                @endif
                <div class="sig-line">Signature / Date</div>
            </div>
            <div class="sig-cell">
                <div class="sig-label">Study Director</div>
                <div class="sig-name">{{ $assessment->study_director_name }}</div>
                @if(isset($signatures['study_director']))
                    <img src="{{ $signatures['study_director']->signature_data }}" class="sig-img" alt="signature">
                    <div class="sig-signed-by">{{ $signatures['study_director']->signer_name }} — {{ $signatures['study_director']->signed_at?->format('d/m/Y') }}</div>
                @else
                    <div class="sig-blank"></div>
                @endif
                <div class="sig-line">Signature / Date</div>
            </div>
            <div class="sig-cell">
                <div class="sig-label">Facility Manager</div>
                <div class="sig-name">
                    @if(isset($keyPersonnels['facility_manager']))
                        {{ $keyPersonnels['facility_manager']->prenom }} {{ $keyPersonnels['facility_manager']->nom }}
                    @endif
                </div>
                @if(isset($signatures['facility_manager']))
                    <img src="{{ $signatures['facility_manager']->signature_data }}" class="sig-img" alt="signature">
                    <div class="sig-signed-by">{{ $signatures['facility_manager']->signer_name }} — {{ $signatures['facility_manager']->signed_at?->format('d/m/Y') }}</div>
                @else
                    <div class="sig-blank"></div>
                @endif
                <div class="sig-line">Signature / Date</div>
            </div>
        </div>
    </div>

</div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</body>
</html>

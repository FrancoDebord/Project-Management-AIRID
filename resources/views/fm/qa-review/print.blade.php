<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>QA Review Checklist — {{ $review->review_date?->format('d/m/Y') ?? $review->scheduled_date?->format('d/m/Y') }}</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
@page { size: A4 portrait; margin: 14mm 16mm 16mm 16mm; }

body { font-family: Arial, sans-serif; font-size: 9.5pt; color: #111; }

/* Header */
.doc-header {
    display: flex; align-items: center; gap: 12px;
    border-bottom: 2px solid #c20102; padding-bottom: 8px; margin-bottom: 10px;
}
.doc-header img { max-width: 70px; max-height: 70px; object-fit: contain; }
.header-right { flex: 1; }
.header-right .org-name { font-size: 9.5pt; font-weight: bold; color: #c20102; }
.header-right .org-info  { font-size: 7.5pt; color: #444; line-height: 1.7; margin-top: 2px; }
.doc-ref { font-size: 7pt; text-align: right; white-space: nowrap; line-height: 1.7; color: #444; }
.doc-ref strong { font-size: 8pt; color: #c20102; }

/* Title */
.doc-title {
    text-align: center; font-size: 12pt; font-weight: bold;
    text-transform: uppercase; text-decoration: underline;
    margin: 10px 0; letter-spacing: .5px;
}

/* Review date */
.review-date-line { font-size: 9.5pt; font-weight: bold; margin-bottom: 8px; }

/* Documents list */
.docs-section { font-size: 8.5pt; margin-bottom: 10px; }
.docs-section strong { font-size: 9pt; }
.docs-section ol { margin-left: 16px; }
.docs-section ul { margin-left: 20px; list-style: disc; }
.docs-section li { margin-bottom: 1px; }
.nb-note { font-weight: bold; font-size: 8.5pt; margin-top: 5px; }

/* Main table */
table.rev-table {
    width: 100%; border-collapse: collapse; font-size: 8.5pt; margin-top: 6px;
}
table.rev-table th, table.rev-table td {
    border: 1px solid #333; padding: 4px 6px; vertical-align: top;
}
table.rev-table thead th {
    background: #fff; font-weight: bold; text-align: center; font-size: 8.5pt;
}
table.rev-table td.areas   { width: 44%; }
table.rev-table td.yn-col  { width: 70px; text-align: center; font-weight: bold; }
table.rev-table td.com-col { width: 28%; }
table.rev-table .section-hdr {
    background: #c20102; color: #fff; font-weight: bold;
    font-size: 9pt; padding: 5px 8px;
}
table.rev-table .subsection-hdr {
    background: #e5eaf5; color: #c20102; font-weight: bold;
    font-size: 8.5pt; padding: 4px 8px; border-left: 3px solid #c20102;
}
.yn-yes { color: #157347; }
.yn-no  { color: #b02a37; }

/* Signature */
.sign-section { margin-top: 14px; font-size: 9pt; }
.sign-line { display: inline-block; border-bottom: 1px solid #333; min-width: 220px; margin-left: 8px; }

/* Meeting minutes */
.meeting-box {
    border: 1px solid #333; margin-top: 20px; page-break-inside: avoid;
}
.meeting-box .meeting-title {
    background: #c20102; color: #fff; font-weight: bold;
    font-size: 9.5pt; padding: 5px 10px;
}
.meeting-box .meeting-body { padding: 10px; min-height: 180px; font-size: 9pt; }
.meeting-box .meeting-sigs {
    border-top: 1px solid #aaa; padding: 10px;
    display: flex; justify-content: space-between; font-size: 8.5pt;
}
.meeting-box .sig-line { border-bottom: 1px solid #333; width: 130px; }

/* Footer */
.doc-footer {
    margin-top: 12px; border-top: 1px solid #aaa; padding-top: 5px;
    display: flex; justify-content: space-between; font-size: 7pt; color: #555;
}

/* Print controls */
@media screen {
    .print-bar {
        position: fixed; top: 0; left: 0; right: 0;
        background: #c20102; color: #fff;
        padding: 8px 20px; display: flex; gap: 10px; align-items: center;
        z-index: 9999; font-family: Arial, sans-serif; font-size: 12px;
    }
    .print-bar button {
        background: #c41230; color: #fff; border: none;
        padding: 5px 14px; border-radius: 5px; cursor: pointer; font-weight: bold;
    }
    .print-bar button.close-btn { background: rgba(255,255,255,.2); }
    body { padding-top: 48px; }
}
@media print {
    .print-bar { display: none !important; }
    body { padding-top: 0; }
}
</style>
</head>
<body>

<div class="print-bar">
    <span style="font-weight:600;">QA Review Checklist — {{ $review->review_date?->format('d/m/Y') ?? '(draft)' }}</span>
    <button onclick="window.print()">🖨 Print / Save PDF</button>
    <button class="close-btn" onclick="window.close()">✕ Close</button>
</div>

{{-- Header --}}
<div class="doc-header">
    <img src="{{ $logoPath }}" alt="AIRID">
    <div class="header-right">
        <div class="org-name">AIRID — African Institute for Research in Infectious Diseases</div>
        <div class="org-info">
            IFU: 6202213991612 &nbsp;|&nbsp; LOT 5507, Donaten Cotonou, Benin<br>
            Tél: +229 0167128862 &nbsp;|&nbsp; Email: admin@airid-africa.com &nbsp;|&nbsp; www.airid-africa.com
        </div>
    </div>
    <div class="doc-ref">
        <strong>QA-PR-1-016/04</strong><br>
        Issue date: 01/08/2025<br>
        Next review date: 31/07/2027
    </div>
</div>

{{-- Title --}}
<div class="doc-title">Quality Assurance Review Checklist</div>

{{-- Review Date --}}
<div class="review-date-line">
    Review Date: <span style="border-bottom:1px solid #333;padding-bottom:1px;display:inline-block;min-width:160px;">
        {{ $review->review_date?->format('d/m/Y') ?? '' }}
    </span>
</div>

{{-- Documents list --}}
<div class="docs-section">
    <strong>List of documents to be verified as part of this inspections:</strong>
    @php $docNums = ['Manuals','SOPs','Inspections reports','Meeting minutes','QA Calendar']; $dn = 0; @endphp
    <ol>
    @foreach(\App\Models\QaReviewInspection::documentsToVerify() as $cat => $docs)
    @php $dn++; @endphp
    <li>{{ $cat }}
        <ul>
            @foreach($docs as $doc)<li>{{ $doc }}</li>@endforeach
        </ul>
    </li>
    @endforeach
    </ol>
    <div class="nb-note">NB: Interview QA Staff as appropriate</div>
</div>

{{-- Main Table --}}
<table class="rev-table">
    <thead>
        <tr>
            <th class="areas">Areas reviewed</th>
            <th style="width:70px;">Yes or No</th>
            <th class="com-col">Comments</th>
            <th>Corrective Actions</th>
        </tr>
    </thead>
    <tbody>

    @php $sectionNum = 0; @endphp
    @foreach($sections as $code => $section)
    @php $sectionNum++; @endphp

    {{-- Section header --}}
    @if(in_array($code, ['I', 'II', 'IV']))
    <tr>
        <td colspan="4" class="section-hdr" style="border:1px solid #c20102;">
            {{ ['I'=>'I','II'=>'II','III_A'=>'III','III_B'=>'III','III_C'=>'III','IV'=>'IV'][$code] ?? $sectionNum }}.
            {{ $section['title'] }}
        </td>
    </tr>
    @elseif($code === 'III_A')
    <tr><td colspan="4" class="section-hdr" style="border:1px solid #c20102;">III. QA Activities</td></tr>
    <tr><td colspan="4" class="subsection-hdr" style="border:1px solid #ccc;">A- Facility &amp; Process Inspections</td></tr>
    @elseif($code === 'III_B')
    <tr><td colspan="4" class="subsection-hdr" style="border:1px solid #ccc;">B- Study-based Inspections</td></tr>
    @elseif($code === 'III_C')
    <tr><td colspan="4" class="subsection-hdr" style="border:1px solid #ccc;">C- QA and Study Reports</td></tr>
    @endif

    @foreach($section['items'] as $num => $question)
    @php
        $key  = $code . '.' . $num;
        $resp = $responses[$key] ?? null;
    @endphp
    <tr>
        <td class="areas">{{ $num }}. {{ $question }}</td>
        <td class="yn-col">
            @if($resp?->yes_no === 'yes')
                <span class="yn-yes">Yes</span>
            @elseif($resp?->yes_no === 'no')
                <span class="yn-no">No</span>
            @endif
        </td>
        <td class="com-col">{{ $resp?->comments }}</td>
        <td>
            {{ $resp?->corrective_actions }}
            @if($resp?->ca_completed)
                <br><em style="font-size:7.5pt;color:#157347;">
                    ✓ Resolved{{ $resp->ca_date ? ' ' . $resp->ca_date->format('d/m/Y') : '' }}
                </em>
            @endif
        </td>
    </tr>
    @endforeach
    @endforeach

    {{-- Others: custom items --}}
    @php $hasCustom = $review->customItems->count() > 0; @endphp
    <tr><td colspan="4" class="section-hdr" style="border:1px solid #c20102;">V. Others</td></tr>
    @if($hasCustom)
        @foreach($review->customItems as $ci)
        <tr>
            <td class="areas">{{ $ci->question }}</td>
            <td class="yn-col">
                @if($ci->yes_no === 'yes') <span class="yn-yes">Yes</span>
                @elseif($ci->yes_no === 'no') <span class="yn-no">No</span>
                @endif
            </td>
            <td class="com-col">{{ $ci->comments }}</td>
            <td>{{ $ci->corrective_actions }}
                @if($ci->ca_completed)<br><em style="font-size:7.5pt;color:#157347;">✓ Resolved</em>@endif
            </td>
        </tr>
        @endforeach
    @else
        <tr><td class="areas">1.</td><td class="yn-col"></td><td class="com-col"></td><td></td></tr>
        <tr><td class="areas">2.</td><td class="yn-col"></td><td class="com-col"></td><td></td></tr>
    @endif

    </tbody>
</table>

{{-- Signature --}}
<div class="sign-section">
    <div><strong>Name of Reviewer:</strong>
        <span class="sign-line">{{ $review->reviewer_name }}</span>
    </div>
    <div style="margin-top:6px;display:flex;gap:40px;">
        <div><strong>Signature:</strong> <span class="sign-line" style="min-width:180px;">&nbsp;</span></div>
        <div><strong>Date:</strong>
            <span class="sign-line" style="min-width:120px;">{{ $review->date_signed?->format('d/m/Y') }}</span>
        </div>
    </div>
    <div style="margin-top:8px;font-size:8pt;font-style:italic;color:#555;">
        NB: Attached to this checklist is the meeting minutes between the Facility Manager &amp; the QA personnel.
    </div>
</div>

{{-- Meeting Minutes --}}
<div class="meeting-box" style="margin-top:24px;">
    <div class="meeting-title">Minutes of meeting with the Facility Manager &amp; Quality Assurance Personnel</div>
    <div class="meeting-body">
        <div style="margin-bottom:8px;">
            <strong>Date of meeting:</strong>
            <span style="border-bottom:1px solid #333;display:inline-block;min-width:150px;margin-left:6px;">
                {{ $review->meeting_date?->format('d/m/Y') ?? '' }}
            </span>
        </div>
        <div>
            <strong>Participants:</strong>
            @if($review->meeting_participants)
                <div style="margin-top:4px;margin-left:16px;">
                    @foreach(array_filter(explode("\n", $review->meeting_participants)) as $p)
                    <div>- {{ trim($p) }}</div>
                    @endforeach
                </div>
            @else
                <div style="margin-top:4px;margin-left:16px;">- &nbsp;</div>
            @endif
        </div>
        @if($review->meeting_notes)
        <div style="margin-top:12px;white-space:pre-line;font-size:8.5pt;">{{ $review->meeting_notes }}</div>
        @endif
    </div>
    <div class="meeting-sigs">
        <div style="text-align:center;">
            <div class="sig-line">&nbsp;</div>
            <div style="margin-top:4px;">QA Manager</div>
        </div>
        <div style="text-align:center;">
            <div class="sig-line">&nbsp;</div>
            <div style="margin-top:4px;">Facility Manager</div>
        </div>
    </div>
</div>

{{-- Footer --}}
<div class="doc-footer">
    <span>* SANAS OECD GLP COMPLIANT FACILITY N° G0028</span>
    <span>Page 1 of 1</span>
</div>

</body>
</html>

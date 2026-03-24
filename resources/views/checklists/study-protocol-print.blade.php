<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Protocol Inspection Checklist — {{ $inspection->inspection_name ?? $inspection->type_inspection }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }

        /* ── Screen controls ── */
        .screen-only {
            padding: 12px 20px;
            background: #f0f0f0;
            border-bottom: 1px solid #ccc;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .screen-only .btn {
            padding: 6px 16px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: .88rem;
            font-weight: 600;
        }
        .btn-print { background: #C10202; color: #fff; }
        .btn-mode  { background: #0d6efd; color: #fff; }
        .btn-back  { background: #6c757d; color: #fff; }

        /* ── Document pages ── */
        .document {
            max-width: 210mm;
            margin: 0 auto;
            padding: 20px;
        }

        /* ── AIRID Header ── */
        .report-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #333;
            padding-bottom: 6px;
            margin-bottom: 0;
        }
        .report-header .logo-area img {
            height: 52px;
            object-fit: contain;
        }
        .report-header .org-block {
            flex: 1;
            text-align: center;
            padding: 0 12px;
        }
        .report-header .org-block .org-name {
            font-size: 13pt;
            font-weight: 900;
            color: #000;
            text-transform: uppercase;
            line-height: 1.2;
            letter-spacing: .03em;
        }
        .report-header .contact-block {
            text-align: right;
            font-size: 7.5pt;
            color: #333;
            line-height: 1.55;
        }

        /* ── Doc reference strip ── */
        .doc-ref-strip {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #bbb;
            border-top: none;
            padding: 4px 10px;
            font-size: 8pt;
            background: #f5f5f5;
            margin-bottom: 12px;
        }
        .doc-ref-strip .ref-item { display: flex; gap: 6px; }
        .doc-ref-strip .ref-label { color: #666; }
        .doc-ref-strip .ref-val   { font-weight: 700; }

        /* ── Info box ── */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #000;
            margin-bottom: 16px;
        }
        .info-table td, .info-table th {
            border: 1px solid #999;
            padding: 5px 8px;
            font-size: 9pt;
        }
        .info-table .label-col {
            background: #e8e8e8;
            font-weight: 700;
            width: 35%;
        }

        /* ── Section header ── */
        .section-header {
            background: #d0d0d0;
            font-weight: 900;
            font-size: 10pt;
            text-transform: uppercase;
            padding: 5px 8px;
            border: 1.5px solid #000;
            border-bottom: none;
            letter-spacing: .03em;
            page-break-before: auto;
        }

        /* ── Questions table ── */
        .questions-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #000;
            border-top: none;
            margin-bottom: 0;
        }
        .questions-table th {
            background: #f0f0f0;
            font-size: 8pt;
            font-weight: 700;
            text-align: center;
            border: 1px solid #888;
            padding: 4px 6px;
            text-transform: uppercase;
        }
        .questions-table th.q-col   { width: 32px; }
        .questions-table th.txt-col { text-align: left; }
        .questions-table th.ans-col { width: 40px; }
        .questions-table th.str-col { text-align: left; }

        .questions-table td {
            border: 1px solid #ccc;
            padding: 4px 6px;
            vertical-align: middle;
            font-size: 9pt;
        }
        .questions-table td.q-num {
            text-align: center;
            font-weight: 700;
            color: #333;
        }
        .questions-table tr:nth-child(even) td { background: #fafafa; }

        .ans-box {
            width: 32px;
            height: 18px;
            border: 1.5px solid #555;
            border-radius: 3px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8pt;
            font-weight: 700;
        }
        .ans-box.checked-yes { background: #198754; color: #fff; border-color: #198754; }
        .ans-box.checked-no  { background: #dc3545; color: #fff; border-color: #dc3545; }
        .ans-box.checked-na  { background: #6c757d; color: #fff; border-color: #6c757d; }

        /* ── Staff table (Section F) ── */
        .staff-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #000;
            border-top: none;
            margin-bottom: 0;
        }
        .staff-table th {
            background: #f0f0f0;
            font-size: 8pt;
            font-weight: 700;
            text-align: center;
            border: 1px solid #888;
            padding: 4px 6px;
            text-transform: uppercase;
        }
        .staff-table td {
            border: 1px solid #ccc;
            padding: 3px 5px;
            font-size: 8.5pt;
            vertical-align: middle;
        }
        .staff-table td.q-num { text-align: center; font-weight: 700; color: #333; width: 28px; }
        .staff-table td.ans-cell { text-align: center; }
        .staff-table tr:nth-child(even) td { background: #fafafa; }

        /* ── Comments ── */
        .comments-row td {
            padding: 6px 8px;
            border: 1.5px solid #000;
            border-top: 1px solid #888;
        }
        .comments-label { font-weight: 700; font-size: 9pt; color: #333; margin-bottom: 4px; }
        .comments-box {
            min-height: 50px;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 6px;
            font-size: 9pt;
            background: #fafafa;
        }
        .comments-box.empty { min-height: 60px; background: #fff; }

        /* ── Section separator ── */
        .section-spacer { height: 16px; }

        /* ── Signatures block ── */
        .signatures-block {
            border: 1.5px solid #000;
            margin-top: 20px;
        }
        .signatures-block .sig-header {
            background: #d0d0d0;
            font-weight: 900;
            font-size: 10pt;
            text-transform: uppercase;
            padding: 5px 8px;
            letter-spacing: .03em;
        }
        .signatures-block table {
            width: 100%;
            border-collapse: collapse;
        }
        .signatures-block table td {
            border: 1px solid #ccc;
            padding: 10px 12px;
            font-size: 9pt;
            width: 50%;
        }
        .sig-label { font-weight: 700; margin-bottom: 4px; font-size: 8pt; }
        .sig-line  {
            margin-top: 30px;
            border-top: 1px solid #555;
            font-size: 7.5pt;
            color: #555;
            padding-top: 2px;
        }

        /* ── Print styles ── */
        @media print {
            .screen-only { display: none !important; }
            .document { padding: 8mm 10mm; max-width: 100%; }
            .section-header { page-break-inside: avoid; }
            .questions-table { page-break-inside: auto; }
            .questions-table tr { page-break-inside: avoid; }
            .staff-table tr { page-break-inside: avoid; }
            body { font-size: 9pt; }
        }
    </style>
</head>
<body>

{{-- ── Screen controls ── --}}
<div class="screen-only">
    <button class="btn btn-back" onclick="history.back()">← Retour</button>
    <button class="btn btn-print" onclick="window.print()">🖨 Imprimer / Enregistrer PDF</button>
    @if($mode === 'empty')
        <a href="?mode=filled" class="btn btn-mode">Voir avec réponses</a>
    @else
        <a href="?mode=empty" class="btn btn-mode">Voir formulaire vierge</a>
    @endif
    <span style="font-size:.82rem; color:#666; margin-left:8px;">
        Mode : <strong>{{ $mode === 'empty' ? 'Formulaire vierge' : 'Formulaire rempli' }}</strong>
    </span>
</div>

<div class="document">

    {{-- ── AIRID Header ── --}}
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

    {{-- ── Doc reference strip ── --}}
    <div class="doc-ref-strip">
        <div class="ref-item">
            <span class="ref-label">Document :</span>
            <span class="ref-val">Study Protocol Inspection Checklist</span>
        </div>
        <div class="ref-item">
            <span class="ref-label">Réf. :</span>
            <span class="ref-val">QA-PR-1-002/06</span>
        </div>
        <div class="ref-item">
            <span class="ref-label">Version :</span>
            <span class="ref-val">06</span>
        </div>
        <div class="ref-item">
            <span class="ref-label">Date :</span>
            <span class="ref-val">{{ $inspection->date_scheduled ? \Carbon\Carbon::parse($inspection->date_scheduled)->format('d/m/Y') : '___/___/______' }}</span>
        </div>
    </div>

    @php
        $qaManager = $keyPersonnels['quality_assurance'] ?? null;
        $inspector = $inspection->inspector ?? null;
        $inspectorIsManager = $inspector && $qaManager && $inspector->id === $qaManager->id;
    @endphp

    {{-- ── Inspection info table ── --}}
    <table class="info-table">
        <tr>
            <td class="label-col">Inspection Name</td>
            <td>{{ $inspection->inspection_name ?? 'Study Protocol Inspection' }}</td>
            <td class="label-col">Date Scheduled</td>
            <td>{{ $inspection->date_scheduled ? \Carbon\Carbon::parse($inspection->date_scheduled)->format('d/m/Y') : '___/___/______' }}</td>
        </tr>
        @if($inspection->project)
        <tr>
            <td class="label-col">Study Code</td>
            <td>{{ $inspection->project->project_code ?? '——' }}</td>
            <td class="label-col">Study Title</td>
            <td>{{ $inspection->project->project_title ?? '——' }}</td>
        </tr>
        @endif
        @if(!$inspectorIsManager)
        <tr>
            <td class="label-col">QA Inspector</td>
            <td>{{ $inspector ? $inspector->prenom . ' ' . $inspector->nom : '——' }}</td>
            <td class="label-col">Date Performed</td>
            <td>{{ $inspection->date_performed ? \Carbon\Carbon::parse($inspection->date_performed)->format('d/m/Y') : '___/___/______' }}</td>
        </tr>
        @else
        <tr>
            <td class="label-col">Date Performed</td>
            <td colspan="3">{{ $inspection->date_performed ? \Carbon\Carbon::parse($inspection->date_performed)->format('d/m/Y') : '___/___/______' }}</td>
        </tr>
        @endif
        @if($qaManager)
        <tr>
            <td class="label-col">QA Manager</td>
            <td colspan="3">{{ $qaManager->prenom }} {{ $qaManager->nom }}</td>
        </tr>
        @endif
    </table>

    {{-- ── Sections ── --}}
    @foreach ($spForms as $slug => $form)
        @php
            $sectionKey  = $form['section'];
            $fp          = $sectionKey . '_';
            $isPersonnel = ($form['type'] ?? '') === 'study_personnel';
            $staffCount  = $form['staff_count'] ?? 15;
        @endphp

        <div class="section-spacer"></div>

        {{-- Section header --}}
        <div class="section-header">
            {{ $form['letter'] }}. {{ $form['title'] }}
        </div>

        {{-- Questions table (standard sections A–E) --}}
        @if(!$isPersonnel)
        <table class="questions-table">
            <thead>
                <tr>
                    <th class="q-col">Q</th>
                    <th class="txt-col">Questions</th>
                    <th class="ans-col">YES</th>
                    <th class="ans-col">NO</th>
                    <th class="ans-col">N/A</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($form['questions'] as $n => $question)
                    @php
                        $val = ($mode !== 'empty' && $record) ? $record->{"${fp}q${n}"} : null;
                    @endphp
                    <tr>
                        <td class="q-num">{{ $n }}</td>
                        <td>{{ $question }}</td>
                        <td style="text-align:center; padding:3px;">
                            <div class="ans-box{{ $val === 'yes' ? ' checked-yes' : '' }}">
                                @if($val === 'yes')✓@endif
                            </div>
                        </td>
                        <td style="text-align:center; padding:3px;">
                            <div class="ans-box{{ $val === 'no' ? ' checked-no' : '' }}">
                                @if($val === 'no')✗@endif
                            </div>
                        </td>
                        <td style="text-align:center; padding:3px;">
                            <div class="ans-box{{ $val === 'na' ? ' checked-na' : '' }}">
                                @if($val === 'na')—@endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="comments-row">
                    <td colspan="5">
                        <div class="comments-label">Comments / Observations</div>
                        @php $commentsField = $fp . 'comments'; @endphp
                        @if($mode !== 'empty' && $record && $record->{$commentsField})
                            <div class="comments-box">{{ $record->{$commentsField} }}</div>
                        @else
                            <div class="comments-box empty"></div>
                        @endif
                    </td>
                </tr>
            </tfoot>
        </table>

        @else
        {{-- Section F: Study Personnel — two-part table --}}
        {{-- F.Q1: Are study personnel appointed sufficient? --}}
        <table class="questions-table">
            <thead>
                <tr>
                    <th class="q-col">Q</th>
                    <th class="txt-col">Questions</th>
                    <th class="ans-col">YES</th>
                    <th class="ans-col">NO</th>
                    <th class="ans-col">N/A</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($form['questions'] as $n => $question)
                    @php $val = ($mode !== 'empty' && $record) ? $record->{"${fp}q${n}"} : null; @endphp
                    <tr>
                        <td class="q-num">{{ $n }}</td>
                        <td>{{ $question }}</td>
                        <td style="text-align:center; padding:3px;">
                            <div class="ans-box{{ $val === 'yes' ? ' checked-yes' : '' }}">@if($val === 'yes')✓@endif</div>
                        </td>
                        <td style="text-align:center; padding:3px;">
                            <div class="ans-box{{ $val === 'no' ? ' checked-no' : '' }}">@if($val === 'no')✗@endif</div>
                        </td>
                        <td style="text-align:center; padding:3px;">
                            <div class="ans-box{{ $val === 'na' ? ' checked-na' : '' }}">@if($val === 'na')—@endif</div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Staff training records --}}
        <table class="staff-table" style="border-top: 1px solid #888; margin-top: 8px;">
            <thead>
                <tr>
                    <th style="width:28px;">#</th>
                    <th style="text-align:left;">Name / Code</th>
                    <th style="width:40px;">YES</th>
                    <th style="width:40px;">NO</th>
                    <th style="width:40px;">N/A</th>
                    <th style="text-align:left;">Level of Training</th>
                    <th style="text-align:left;">Remarks</th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 1; $i <= $staffCount; $i++)
                    @php
                        $staffResult  = ($mode !== 'empty' && $record) ? $record->{"f_staff_{$i}_result"}  : null;
                        $staffLevel   = ($mode !== 'empty' && $record) ? $record->{"f_staff_{$i}_level"}   : null;
                        $staffRemarks = ($mode !== 'empty' && $record) ? $record->{"f_staff_{$i}_remarks"} : null;
                    @endphp
                    <tr>
                        <td class="q-num">{{ $i }}</td>
                        <td></td>
                        <td class="ans-cell">
                            <div class="ans-box{{ $staffResult === 'yes' ? ' checked-yes' : '' }}">
                                @if($staffResult === 'yes')✓@endif
                            </div>
                        </td>
                        <td class="ans-cell">
                            <div class="ans-box{{ $staffResult === 'no' ? ' checked-no' : '' }}">
                                @if($staffResult === 'no')✗@endif
                            </div>
                        </td>
                        <td class="ans-cell">
                            <div class="ans-box{{ $staffResult === 'na' ? ' checked-na' : '' }}">
                                @if($staffResult === 'na')—@endif
                            </div>
                        </td>
                        <td>{{ $staffLevel }}</td>
                        <td>{{ $staffRemarks }}</td>
                    </tr>
                @endfor
            </tbody>
        </table>

        {{-- Comments for Section F --}}
        <table class="questions-table" style="border-top: 1px solid #888;">
            <tfoot>
                <tr class="comments-row">
                    <td colspan="5">
                        <div class="comments-label">Comments / Observations</div>
                        @php $commentsField = $fp . 'comments'; @endphp
                        @if($mode !== 'empty' && $record && $record->{$commentsField})
                            <div class="comments-box">{{ $record->{$commentsField} }}</div>
                        @else
                            <div class="comments-box empty"></div>
                        @endif
                    </td>
                </tr>
            </tfoot>
        </table>
        @endif

    @endforeach

    {{-- ── Signatures block ── --}}
    <div class="signatures-block">
        <div class="sig-header">Signatures</div>
        <table>
            <tr>
                @if(!$inspectorIsManager)
                <td>
                    <div class="sig-label">QA Inspector</div>
                    <div style="font-size:9pt;">{{ $inspector ? $inspector->prenom . ' ' . $inspector->nom : '' }}</div>
                    <div class="sig-line">Signature &amp; date</div>
                </td>
                @endif
                <td>
                    <div class="sig-label">QA Manager</div>
                    <div style="font-size:9pt;">{{ $qaManager ? $qaManager->prenom . ' ' . $qaManager->nom : '' }}</div>
                    <div class="sig-line">Signature &amp; date</div>
                </td>
            </tr>
        </table>
    </div>

</div>

</body>
</html>

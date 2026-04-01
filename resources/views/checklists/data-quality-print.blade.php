<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Quality Inspection Checklist — {{ $inspection->inspection_name ?? $inspection->type_inspection }}</title>
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

        .document {
            max-width: 210mm;
            margin: 0 auto;
            padding: 20px;
        }

        .report-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #333;
            padding-bottom: 6px;
            margin-bottom: 0;
        }.doc-ref-strip {
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

        .info-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #000;
            margin-bottom: 16px;
        }
        .info-table td, .info-table th { border: 1px solid #999; padding: 5px 8px; font-size: 9pt; }
        .info-table .label-col { background: #e8e8e8; font-weight: 700; width: 35%; }

        .section-header {
            background: #d0d0d0;
            font-weight: 900;
            font-size: 10pt;
            text-transform: uppercase;
            padding: 5px 8px;
            border: 1.5px solid #000;
            border-bottom: none;
            letter-spacing: .03em;
        }

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
        .questions-table th.v1-header { background: #e8f5e9; color: #1b5e20; }
        .questions-table th.v2-header { background: #e3f2fd; color: #0d47a1; }

        .questions-table td {
            border: 1px solid #ccc;
            padding: 4px 6px;
            vertical-align: middle;
            font-size: 9pt;
        }
        .questions-table td.q-num { text-align: center; font-weight: 700; color: #333; }
        .questions-table td.sub-item { padding-left: 20px; color: #444; font-style: italic; }
        .questions-table tr:nth-child(even) td { background: #fafafa; }

        .ans-box {
            width: 28px;
            height: 16px;
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

        .comments-row td {
            padding: 6px 8px;
            border: 1.5px solid #000;
            border-top: 1px solid #888;
        }
        .comments-label { font-weight: 700; font-size: 9pt; color: #333; margin-bottom: 4px; }
        .comments-box { min-height: 50px; border: 1px solid #ccc; border-radius: 4px; padding: 6px; font-size: 9pt; background: #fafafa; }
        .comments-box.empty { min-height: 60px; background: #fff; }

        .verification-footer {
            border: 1.5px solid #000;
            border-top: none;
            background: #f8f8f8;
        }
        .verification-footer table { width: 100%; border-collapse: collapse; }
        .verification-footer td { border: 1px solid #ccc; padding: 4px 8px; font-size: 8pt; }
        .verification-footer .vf-label { font-weight: 700; width: 30%; }

        .section-spacer { height: 16px; }

        .signatures-block { border: 1.5px solid #000; margin-top: 20px; }
        .signatures-block .sig-header { background: #d0d0d0; font-weight: 900; font-size: 10pt; text-transform: uppercase; padding: 5px 8px; letter-spacing: .03em; }
        .signatures-block table { width: 100%; border-collapse: collapse; }
        .signatures-block table td { border: 1px solid #ccc; padding: 10px 12px; font-size: 9pt; width: 50%; }
        .sig-label { font-weight: 700; margin-bottom: 4px; font-size: 8pt; }
        .sig-line  { margin-top: 30px; border-top: 1px solid #555; font-size: 7.5pt; color: #555; padding-top: 2px; }

        @media print {
            .screen-only { display: none !important; }
            .document { padding: 8mm 10mm; max-width: 100%; }
            .section-header { page-break-inside: avoid; }
            .questions-table { page-break-inside: auto; }
            .questions-table tr { page-break-inside: avoid; }
            body { font-size: 9pt; }
        }
    </style>
</head>
<body>

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

    <div class="report-header">
        <img src="{{ asset('storage/assets/header/entete_airid.png') }}" alt="AIRID — African Institute for Research in Infectious Diseases">
    </div>
        

    <div class="doc-ref-strip">
        <div class="ref-item">
            <span class="ref-label">Document :</span>
            <span class="ref-val">Data Quality Inspection Checklist</span>
        </div>
        <div class="ref-item">
            <span class="ref-label">Réf. :</span>
            <span class="ref-val">QA-PR-1-018/03</span>
        </div>
        <div class="ref-item">
            <span class="ref-label">Issue date :</span>
            <span class="ref-val">01/08/2025</span>
        </div>
        <div class="ref-item">
            <span class="ref-label">Date :</span>
            <span class="ref-val">{{ $inspection->date_scheduled ? \Carbon\Carbon::parse($inspection->date_scheduled)->format('d/m/Y') : '___/___/______' }}</span>
        </div>
    </div>

    @php
        $qaManager = $keyPersonnels['quality_assurance'] ?? null;
        $inspector = $inspection->inspector ?? null;
    @endphp

    <table class="info-table">
        <tr>
            <td class="label-col">Inspection Name</td>
            <td>{{ $inspection->inspection_name ?? 'Data Quality Inspection' }}</td>
            <td class="label-col">Date Scheduled</td>
            <td>{{ $inspection->date_scheduled ? \Carbon\Carbon::parse($inspection->date_scheduled)->format('d/m/Y') : '___/___/______' }}</td>
        </tr>
        @if($inspection->project)
        <tr>
            <td class="label-col">Study Code</td>
            <td>{{ $record?->study_start_date ? $inspection->project->project_code . ' (Start: ' . $record->study_start_date->format('d/m/Y') . ')' : ($inspection->project->project_code ?? '——') }}</td>
            <td class="label-col">Study Director</td>
            <td>{{ $record?->study_director_name ?? ($inspection->project->project_title ?? '——') }}</td>
        </tr>
        @endif
        <tr>
            <td class="label-col">QA Inspector</td>
            <td>{{ $inspector ? $inspector->prenom . ' ' . $inspector->nom : '——' }}</td>
            <td class="label-col">Date Performed</td>
            <td>{{ $inspection->date_performed ? \Carbon\Carbon::parse($inspection->date_performed)->format('d/m/Y') : '___/___/______' }}</td>
        </tr>
        @if($qaManager)
        <tr>
            <td class="label-col">QA Manager</td>
            <td colspan="3">{{ $qaManager->prenom }} {{ $qaManager->nom }}</td>
        </tr>
        @endif
        @if($mode !== 'empty' && $record && $record->aspects_inspected)
        <tr>
            <td class="label-col">Aspects Inspectés</td>
            <td colspan="3">{{ implode(', ', $record->aspects_inspected) }}</td>
        </tr>
        @endif
    </table>

    {{-- ── Sections A–E ── --}}
    @foreach ($dqForms as $slug => $form)
        @php
            $sectionKey = $form['section'];
            $formType   = $form['form_type'];
        @endphp

        <div class="section-spacer"></div>

        <div class="section-header">
            {{ $form['letter'] }}. {{ $form['title'] }}
        </div>

        @if ($formType === 'dq_standard')
        {{-- Standard table: YES / NO / N/A --}}
        @php
            $answers = ($mode !== 'empty' && $record) ? ((array)($record->{$sectionKey.'_answers'} ?? [])) : [];
        @endphp
        <table class="questions-table">
            <thead>
                <tr>
                    <th class="q-col">Q</th>
                    <th class="txt-col">Questions</th>
                    <th class="ans-col">YES</th>
                    <th class="ans-col">NO</th>
                    <th class="ans-col">N/A</th>
                    <th style="width:90px;">Date</th>
                    <th style="width:90px;">QA Personnel</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($form['questions'] as $n => $question)
                @php $val = $answers[(string)$n] ?? null; @endphp
                <tr>
                    <td class="q-num">{{ $n }}</td>
                    <td>{{ $question }}</td>
                    <td style="text-align:center; padding:3px;"><div class="ans-box{{ $val === 'yes' ? ' checked-yes' : '' }}">@if($val === 'yes')✓@endif</div></td>
                    <td style="text-align:center; padding:3px;"><div class="ans-box{{ $val === 'no' ? ' checked-no' : '' }}">@if($val === 'no')✗@endif</div></td>
                    <td style="text-align:center; padding:3px;"><div class="ans-box{{ $val === 'na' ? ' checked-na' : '' }}">@if($val === 'na')—@endif</div></td>
                    <td style="font-size:7.5pt;"></td>
                    <td style="font-size:7.5pt;"></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="comments-row">
                    <td colspan="7">
                        <div class="comments-label">Comments</div>
                        @php $cmts = ($mode !== 'empty' && $record) ? $record->{$sectionKey.'_comments'} : null; @endphp
                        @if($cmts)
                            <div class="comments-box">{{ $cmts }}</div>
                        @else
                            <div class="comments-box empty"></div>
                        @endif
                    </td>
                </tr>
            </tfoot>
        </table>

        @elseif ($formType === 'dual_verification')
        {{-- Dual Verification: Vérification 1 & 2 --}}
        @php
            $v1 = ($mode !== 'empty' && $record) ? ((array)($record->{$sectionKey.'_v1_answers'} ?? [])) : [];
            $v2 = ($mode !== 'empty' && $record) ? ((array)($record->{$sectionKey.'_v2_answers'} ?? [])) : [];
        @endphp
        <table class="questions-table">
            <thead>
                <tr>
                    <th class="q-col" rowspan="2">Q</th>
                    <th class="txt-col" rowspan="2">Questions</th>
                    <th colspan="4" class="v1-header">Vérification 1</th>
                    <th colspan="4" class="v2-header">Vérification 2</th>
                </tr>
                <tr>
                    <th class="ans-col v1-header">YES</th>
                    <th class="ans-col v1-header">NO</th>
                    <th class="ans-col v1-header">N/A</th>
                    <th style="width:80px;" class="v1-header">Date</th>
                    <th class="ans-col v2-header">YES</th>
                    <th class="ans-col v2-header">NO</th>
                    <th class="ans-col v2-header">N/A</th>
                    <th style="width:80px;" class="v2-header">Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($form['questions'] as $n => $question)
                @php
                    $a1 = $v1[(string)$n] ?? null;
                    $a2 = $v2[(string)$n] ?? null;
                    $isSub = in_array((string)$n, $form['sub_items'] ?? []);
                @endphp
                <tr>
                    <td class="q-num" style="{{ $isSub ? 'font-size:7.5pt;' : '' }}">{{ $n }}</td>
                    <td class="{{ $isSub ? 'sub-item' : '' }}">{{ $question }}</td>
                    <td style="text-align:center; padding:3px;"><div class="ans-box{{ $a1 === 'yes' ? ' checked-yes' : '' }}">@if($a1 === 'yes')✓@endif</div></td>
                    <td style="text-align:center; padding:3px;"><div class="ans-box{{ $a1 === 'no' ? ' checked-no' : '' }}">@if($a1 === 'no')✗@endif</div></td>
                    <td style="text-align:center; padding:3px;"><div class="ans-box{{ $a1 === 'na' ? ' checked-na' : '' }}">@if($a1 === 'na')—@endif</div></td>
                    <td style="font-size:7pt;"></td>
                    <td style="text-align:center; padding:3px;"><div class="ans-box{{ $a2 === 'yes' ? ' checked-yes' : '' }}">@if($a2 === 'yes')✓@endif</div></td>
                    <td style="text-align:center; padding:3px;"><div class="ans-box{{ $a2 === 'no' ? ' checked-no' : '' }}">@if($a2 === 'no')✗@endif</div></td>
                    <td style="text-align:center; padding:3px;"><div class="ans-box{{ $a2 === 'na' ? ' checked-na' : '' }}">@if($a2 === 'na')—@endif</div></td>
                    <td style="font-size:7pt;"></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="comments-row">
                    <td colspan="10">
                        <div class="comments-label">Comments</div>
                        @php $cmts = ($mode !== 'empty' && $record) ? $record->{$sectionKey.'_comments'} : null; @endphp
                        @if($cmts)
                            <div class="comments-box">{{ $cmts }}</div>
                        @else
                            <div class="comments-box empty"></div>
                        @endif
                    </td>
                </tr>
            </tfoot>
        </table>

        @elseif ($formType === 'study_box')
        {{-- Study Box: YES/NO/N/A + Documents signed --}}
        @php
            $answers = ($mode !== 'empty' && $record) ? ((array)($record->{$sectionKey.'_answers'} ?? [])) : [];
        @endphp
        <table class="questions-table">
            <thead>
                <tr>
                    <th class="q-col">Q</th>
                    <th class="txt-col">Questions</th>
                    <th class="ans-col">YES</th>
                    <th class="ans-col">NO</th>
                    <th class="ans-col">N/A</th>
                    <th style="width:80px; font-size:7pt;">Docs signés?</th>
                    <th style="width:80px;">Date</th>
                    <th style="width:80px;">QA Personnel</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($form['questions'] as $n => $question)
                @php
                    $entry  = $answers[(string)$n] ?? [];
                    $resp   = is_array($entry) ? ($entry['response'] ?? null) : null;
                    $signed = is_array($entry) ? ($entry['signed'] ?? null) : null;
                @endphp
                <tr>
                    <td class="q-num">{{ $n }}</td>
                    <td>{{ $question }}</td>
                    <td style="text-align:center; padding:3px;"><div class="ans-box{{ $resp === 'yes' ? ' checked-yes' : '' }}">@if($resp === 'yes')✓@endif</div></td>
                    <td style="text-align:center; padding:3px;"><div class="ans-box{{ $resp === 'no' ? ' checked-no' : '' }}">@if($resp === 'no')✗@endif</div></td>
                    <td style="text-align:center; padding:3px;"><div class="ans-box{{ $resp === 'na' ? ' checked-na' : '' }}">@if($resp === 'na')—@endif</div></td>
                    <td style="text-align:center; padding:3px; font-size:8pt;">
                        @if($signed === 'yes') <strong>Yes</strong>
                        @elseif($signed === 'no') No
                        @else &nbsp;
                        @endif
                    </td>
                    <td style="font-size:7.5pt;"></td>
                    <td style="font-size:7.5pt;"></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="comments-row">
                    <td colspan="8">
                        <div class="comments-label">Comments</div>
                        @php $cmts = ($mode !== 'empty' && $record) ? $record->{$sectionKey.'_comments'} : null; @endphp
                        @if($cmts)
                            <div class="comments-box">{{ $cmts }}</div>
                        @else
                            <div class="comments-box empty"></div>
                        @endif
                    </td>
                </tr>
            </tfoot>
        </table>
        @endif
        @if($mode !== 'empty' && $record)
        @php $confVal = $record->{$sectionKey . '_is_conforming'}; @endphp
        <div style="display:flex;align-items:center;gap:10px;margin:6px 0 4px;padding:5px 12px;border:1.5px solid #ccc;border-radius:3px;font-size:9.5pt;page-break-inside:avoid;">
            <span style="font-weight:bold;color:#333;white-space:nowrap;">Conclusion :</span>
            @if($confVal === true)
                <span style="font-weight:bold;padding:2px 10px;border-radius:20px;background:#d4edda;color:#155724;border:1px solid #c3e6cb;">&#10003; Conforme</span>
            @elseif($confVal === false)
                <span style="font-weight:bold;padding:2px 10px;border-radius:20px;background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;">&#10007; Non conforme</span>
            @else
                <span style="font-weight:bold;padding:2px 10px;border-radius:20px;background:#e9ecef;color:#6c757d;border:1px solid #dee2e6;">&#8212; Non évalué</span>
            @endif
        </div>
        @endif

    @endforeach

    {{-- ── Signatures block ── --}}
    <div class="section-spacer"></div>
    <div class="signatures-block">
        <div class="sig-header">Signatures</div>
        <table>
            <tr>
                <td>
                    <div class="sig-label">QA Inspector</div>
                    <div>{{ $inspector ? $inspector->prenom . ' ' . $inspector->nom : '________________________________' }}</div>
                    <div class="sig-line">Signature / Date</div>
                </td>
                <td>
                    <div class="sig-label">QA Manager</div>
                    <div>{{ $qaManager ? $qaManager->prenom . ' ' . $qaManager->nom : '________________________________' }}</div>
                    <div class="sig-line">Signature / Date</div>
                </td>
            </tr>
        </table>
    </div>

</div>
</body>
</html>

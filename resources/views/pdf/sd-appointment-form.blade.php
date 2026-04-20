<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 9.5pt;
    color: #000;
    background: #fff;
    padding: 10mm 14mm 14mm 14mm;
  }

  /* ── Header ── */
  .page-header { margin-bottom: 1mm; }
  .doc-ref-bar {
    text-align: right;
    font-size: 7.5pt;
    color: #555;
    border-bottom: 1.5pt solid #1a3a6b;
    padding-bottom: 2mm;
    margin-bottom: 3mm;
  }

  /* ── Title ── */
  .form-title {
    text-align: center;
    font-size: 12pt;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: .06em;
    text-decoration: underline;
    color: #1a3a6b;
    margin: 2mm 0 3mm;
  }

  /* ── Section box ── */
  .section {
    border: 1pt solid #555;
    margin-bottom: 3mm;
    page-break-inside: avoid;
  }
  .section-title {
    background: #d6dde8;
    font-weight: bold;
    font-size: 8.5pt;
    padding: 1.5mm 4mm;
    border-bottom: 1pt solid #555;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: #1a3a6b;
    text-align: center;
  }
  .section-body { padding: 2mm 4mm; }

  /* ── Grid rows ── */
  .field-row {
    display: flex;
    align-items: flex-start;
    padding: 1mm 0;
    border-bottom: .4pt solid #e8e8e8;
    font-size: 8.5pt;
    line-height: 1.3;
  }
  .field-row:last-child { border-bottom: none; }
  .field-label {
    font-weight: bold;
    min-width: 36mm;
    color: #333;
    flex-shrink: 0;
  }
  .field-value { flex: 1; }
  .field-value.long { word-break: break-word; }

  /* ── Two-column layout inside a section ── */
  .two-col { display: flex; gap: 4mm; }
  .two-col .col { flex: 1; }

  /* ── Signature area ── */
  .sig-block {
    display: flex;
    gap: 8mm;
    margin-top: 2mm;
  }
  .sig-item { flex: 1; }
  .sig-label { font-weight: bold; font-size: 8pt; margin-bottom: 7mm; }
  .sig-line {
    border-top: 1pt solid #555;
    padding-top: 1.5mm;
    font-size: 8pt;
    color: #333;
  }

  /* ── Signature status badges ── */
  .sig-status-signed {
    display: inline-block;
    background: #198754;
    color: #fff;
    font-size: 7pt;
    font-weight: bold;
    padding: .8mm 2.5mm;
    border-radius: 1.5mm;
  }
  .sig-status-pending {
    display: inline-block;
    background: #ffc107;
    color: #000;
    font-size: 7pt;
    font-weight: bold;
    padding: .8mm 2.5mm;
    border-radius: 1.5mm;
  }

  /* ── Amplification ── */
  .amp-list { padding: 1mm 0 0 3mm; font-size: 8pt; }
  .amp-list li { margin-bottom: .8mm; list-style: none; display: inline-block; margin-right: 6mm; }
  .amp-list li::before { content: "☑  "; }

  /* ── GLP badge ── */
  .glp-badge {
    display: inline-block;
    background: #1a3a6b;
    color: #fff;
    font-size: 7pt;
    font-weight: bold;
    padding: .5mm 2.5mm;
    border-radius: 1.5mm;
    vertical-align: middle;
    margin-left: 2mm;
  }

  /* ── Footer ── */
  .page-footer {
    position: fixed;
    bottom: 6mm;
    left: 14mm;
    right: 14mm;
    font-size: 7pt;
    color: #aaa;
    border-top: .5pt solid #ddd;
    padding-top: 1.5mm;
    text-align: center;
  }

  @page { size: A4 portrait; margin: 0; }
</style>
</head>
<body>

{{-- ── Header image ── --}}
<div class="page-header">
  <img src="{{ public_path('storage/assets/header/entete_airid.png') }}"
       style="width:100%;max-height:24mm;object-fit:contain;" alt="AIRID">
</div>
<div class="doc-ref-bar">
  <strong>{{ $docRef ?? 'SI-PA-4-010/07' }}</strong>&nbsp;·&nbsp;
  Issue date: {{ $issueDate ?? '01/06/2023' }}&nbsp;·&nbsp;
  Next review: {{ $nextReviewDate ?? '01/06/2027' }}
</div>

{{-- ── Form title ── --}}
<div class="form-title">Study Director Appointment Form</div>

{{-- ── Project Details ── --}}
<div class="section">
  <div class="section-title">Project Details</div>
  <div class="section-body">
    <div class="two-col">
      <div class="col">
        <div class="field-row">
          <div class="field-label">Project code :</div>
          <div class="field-value">
            <strong>{{ $project->project_code ?? '—' }}</strong>
            @if($project->is_glp ?? false)<span class="glp-badge">GLP</span>@endif
          </div>
        </div>
        <div class="field-row">
          <div class="field-label">Protocol Code :</div>
          <div class="field-value">{{ $project->protocol_code ?? '—' }}</div>
        </div>
        <div class="field-row" style="border-bottom:none;">
          <div class="field-label">Est. Start :</div>
          <div class="field-value">{{ $form->estimated_start_date ? \Carbon\Carbon::parse($form->estimated_start_date)->format('d F Y') : '—' }}</div>
        </div>
      </div>
      <div class="col">
        <div class="field-row">
          <div class="field-label">Sponsor :</div>
          <div class="field-value">{{ $project->sponsor_name ?? '—' }}</div>
        </div>
        <div class="field-row">
          <div class="field-label">Manufacturer :</div>
          <div class="field-value">{{ $project->manufacturer_name ?? '—' }}</div>
        </div>
        <div class="field-row" style="border-bottom:none;">
          <div class="field-label">Est. End :</div>
          <div class="field-value">{{ $form->estimated_end_date ? \Carbon\Carbon::parse($form->estimated_end_date)->format('d F Y') : '—' }}</div>
        </div>
      </div>
    </div>
    <div class="field-row" style="border-bottom:none;">
      <div class="field-label">Project Title :</div>
      <div class="field-value long">{{ $project->project_title ?? '—' }}</div>
    </div>
  </div>
</div>

{{-- ── Study Director Details ── --}}
@php
  $sdName = $sd ? trim(($sd->titre_personnel ?? '') . ' ' . $sd->prenom . ' ' . $sd->nom) : '—';
@endphp
<div class="section">
  <div class="section-title">Study Director Details</div>
  <div class="section-body">
    <div class="two-col">
      <div class="col">
        <div class="field-row">
          <div class="field-label">Name :</div>
          <div class="field-value"><strong>{{ $sdName }}</strong></div>
        </div>
        <div class="field-row" style="border-bottom:none;">
          <div class="field-label">Appointment date :</div>
          <div class="field-value">{{ $form->sd_appointment_date ? \Carbon\Carbon::parse($form->sd_appointment_date)->format('d F Y') : '—' }}</div>
        </div>
      </div>
      <div class="col">
        <div class="field-row">
          <div class="field-label">Qualification :</div>
          <div class="field-value">{{ $sd->titre_qualitification ?? '—' }}</div>
        </div>
        <div class="field-row" style="border-bottom:none;">
          <div class="field-label">Signature status :</div>
          <div class="field-value">
            @if($form->sd_signed_at)
              <span class="sig-status-signed">✓ Signé le {{ \Carbon\Carbon::parse($form->sd_signed_at)->format('d/m/Y H:i') }}</span>
            @else
              <span class="sig-status-pending">En attente de signature</span>
            @endif
          </div>
        </div>
      </div>
    </div>
    <div class="field-row" style="padding-top:2mm;border-bottom:none;">
      <div class="field-value" style="font-style:italic;font-size:8pt;color:#444;">
        I hereby consent to act as Study Director for the above-mentioned project.
      </div>
    </div>
    <div class="sig-block" style="margin-top:3mm;">
      <div class="sig-item">
        <div class="sig-label">Signature :</div>
        @if($form->sd_signed_at)
          <div class="sig-line" style="font-style:italic;color:#198754;">
            Signé électroniquement — {{ $sdName }}<br>{{ \Carbon\Carbon::parse($form->sd_signed_at)->format('d/m/Y H:i') }}
          </div>
        @elseif($form->sd_appointment_file ?? false)
          <div class="sig-line">[ Document signé joint ]</div>
        @else
          <div class="sig-line">&nbsp;</div>
        @endif
      </div>
      <div class="sig-item">
        <div class="sig-label">Date :</div>
        <div class="sig-line">{{ $form->sd_appointment_date ? \Carbon\Carbon::parse($form->sd_appointment_date)->format('d/m/Y') : '' }}</div>
      </div>
    </div>
  </div>
</div>

{{-- ── Facility Manager ── --}}
@php
  $fm       = \App\Models\Pro_KeyFacilityPersonnel::where('active', 1)->with('personnel')->first();
  $fmPerson = $fm?->personnel;
  $fmName   = $fmPerson ? trim(($fmPerson->titre_personnel ?? '') . ' ' . $fmPerson->prenom . ' ' . $fmPerson->nom) : '—';
@endphp
<div class="section">
  <div class="section-title">Facility Manager</div>
  <div class="section-body">
    <div class="two-col">
      <div class="col">
        <div class="field-row" style="border-bottom:none;">
          <div class="field-label">Name :</div>
          <div class="field-value"><strong>{{ $fmName }}</strong></div>
        </div>
      </div>
      <div class="col">
        <div class="field-row" style="border-bottom:none;">
          <div class="field-label">Signature status :</div>
          <div class="field-value">
            @if($form->fm_signed_at)
              <span class="sig-status-signed">✓ Signé le {{ \Carbon\Carbon::parse($form->fm_signed_at)->format('d/m/Y H:i') }}</span>
            @else
              <span class="sig-status-pending">En attente de signature</span>
            @endif
          </div>
        </div>
      </div>
    </div>
    <div class="sig-block" style="margin-top:3mm;">
      <div class="sig-item">
        <div class="sig-label">Signature :</div>
        @if($form->fm_signed_at)
          <div class="sig-line" style="font-style:italic;color:#198754;">
            Signé électroniquement — {{ $fmName }}<br>{{ \Carbon\Carbon::parse($form->fm_signed_at)->format('d/m/Y H:i') }}
          </div>
        @else
          <div class="sig-line">&nbsp;</div>
        @endif
      </div>
      <div class="sig-item">
        <div class="sig-label">Date :</div>
        <div class="sig-line">&nbsp;</div>
      </div>
    </div>
  </div>
</div>

{{-- ── Amplification + GLP on same row ── --}}
<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-top:1mm;">
  <div>
    <div style="font-size:8.5pt;font-weight:bold;">Amplification :</div>
    <ul class="amp-list">
      <li>Quality Assurance Manager</li>
      <li>Data Manager</li>
      <li>Laboratory Supervisor</li>
      <li>Field Site Supervisor</li>
      <li>Inventory Supervisor</li>
      <li>Finance/Admin</li>
    </ul>
  </div>
  @if($project->is_glp ?? false)
  <div style="font-size:7.5pt;color:#1a3a6b;font-weight:bold;text-align:right;margin-top:1mm;">
    SANAS OECD GLP COMPLIANT FACILITY N° {{ $glpFacilityNumber ?? 'G0028' }}
  </div>
  @endif
</div>

<div class="page-footer">
  AIRID PMS — Généré le {{ now()->format('d/m/Y H:i') }} &nbsp;·&nbsp;
  {{ $project->project_code ?? '' }} — Study Director Appointment Form
</div>

</body>
</html>

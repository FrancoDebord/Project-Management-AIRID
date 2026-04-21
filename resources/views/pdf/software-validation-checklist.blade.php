<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Software and Program Validation Checklist</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  @page { margin: 14mm 22mm 14mm 22mm; size: A4 portrait; }
  body { font-family: Arial, sans-serif; font-size: 8.5pt; color: #111; line-height: 1.3; padding: 0 3mm; }

  .header-img { width: 100%; display: block; margin-bottom: 6px; }

  .doc-title {
    text-align: center;
    border: 2px solid #000;
    padding: 5px 8px;
    margin-bottom: 8px;
    font-size: 11pt;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: .04em;
    background: #f0f0f0;
  }

  .ref-bar {
    display: flex;
    justify-content: space-between;
    font-size: 7pt;
    color: #555;
    margin-bottom: 6px;
    border-bottom: 1px solid #ccc;
    padding-bottom: 3px;
  }

  /* Main checklist table */
  .cl-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 8.5pt;
    margin-bottom: 8px;
  }
  .cl-table th {
    background: #d0d0d0;
    font-weight: bold;
    border: 1px solid #666;
    padding: 3px 5px;
    text-align: left;
    font-size: 8pt;
  }
  .cl-table td {
    border: 1px solid #888;
    padding: 3px 5px;
    vertical-align: top;
  }
  .cl-table .lbl {
    font-weight: bold;
    background: #f5f5f5;
    width: 42%;
    white-space: nowrap;
  }
  .cl-table .val { width: 58%; }

  /* Yes/No row helper */
  .yn-cell { display: flex; gap: 14px; align-items: center; }
  .cb { display: inline-block; width: 10px; height: 10px; border: 1px solid #333; margin-right: 3px; text-align: center; line-height: 9px; font-size: 8pt; font-weight: bold; }
  .cb.checked { background: #333; color: #fff; }

  /* Details section */
  .section-title {
    font-weight: bold;
    font-size: 9pt;
    background: #1a3a6b;
    color: #fff;
    padding: 3px 6px;
    margin: 8px 0 4px 0;
    border-radius: 2px;
  }

  .proc-box {
    border: 1px solid #ccc;
    padding: 6px 8px;
    font-size: 8pt;
    min-height: 40px;
    background: #fafafa;
    white-space: pre-wrap;
    word-break: break-word;
  }

  /* Attachments */
  .attach-list { margin: 3px 0 0 16px; font-size: 8pt; }
  .attach-list li { margin-bottom: 2px; }

  /* Signature block */
  .sig-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 8pt; }
  .sig-table td { width: 50%; padding: 6px 10px; vertical-align: top; }
  .sig-line { border-top: 1px solid #333; margin-top: 20px; padding-top: 3px; font-size: 7.5pt; color: #555; }

  /* Env / kit row */
  .env-row td { font-size: 8pt; }
</style>
</head>
<body>

{{-- AIRID Header --}}
<img src="{{ $headerImagePath }}" class="header-img" alt="AIRID">

<div class="doc-title">Software and Program Validation Checklist</div>

<div class="ref-bar">
  <span>Project: <strong>{{ $project->project_code }}</strong></span>
  <span>Ref: SV-{{ $validation->id }}</span>
  <span>Generated: {{ now()->format('d/m/Y H:i') }}</span>
  <span>Status: <strong>{{ ucfirst($validation->status) }}</strong></span>
</div>

{{-- ── Main Fields ── --}}
<table class="cl-table">
  <tr>
    <td class="lbl">Computer ID:</td>
    <td class="val">{{ $validation->computer_id ?: '—' }}</td>
    <td class="lbl">Validation Date:</td>
    <td class="val">{{ $validation->validation_date?->format('d/m/Y') ?: '—' }}</td>
  </tr>
  <tr>
    <td class="lbl">Software to validate:</td>
    <td class="val" colspan="3"><strong>{{ $validation->software_name }}</strong>
      @if($validation->current_software_version)
        &nbsp;— v.{{ $validation->current_software_version }}
      @endif
    </td>
  </tr>
  <tr>
    <td class="lbl">Validation done by:</td>
    <td class="val">{{ $validation->validation_done_by ?: '—' }}</td>
    <td class="lbl">Reason for validation:</td>
    <td class="val">{{ $validation->reason_for_validation ?: '—' }}</td>
  </tr>
  <tr>
    <td class="lbl">Current Software Version:</td>
    <td class="val">{{ $validation->current_software_version ?: '—' }}</td>
    <td class="lbl">Operating System:</td>
    <td class="val">{{ $validation->operating_system ?: '—' }}</td>
  </tr>
  <tr>
    <td class="lbl">CPU:</td>
    <td class="val">{{ $validation->cpu ?: '—' }}</td>
    <td class="lbl">RAM:</td>
    <td class="val">{{ $validation->ram ?: '—' }}</td>
  </tr>
  <tr>
    <td class="lbl">Record the validation in the computer (SOP):</td>
    <td class="val" colspan="3">
      <div class="yn-cell">
        <span>
          <span class="cb {{ $validation->is_recorded_in_computer ? 'checked' : '' }}">{{ $validation->is_recorded_in_computer ? '✓' : '' }}</span>
          Yes
        </span>
        <span>
          <span class="cb {{ !$validation->is_recorded_in_computer ? 'checked' : '' }}">{{ !$validation->is_recorded_in_computer ? '✓' : '' }}</span>
          Not Recorded
        </span>
      </div>
    </td>
  </tr>
  <tr>
    <td class="lbl">Validation kit for:</td>
    <td class="val" colspan="3">
      <div class="yn-cell">
        <span>
          <span class="cb {{ $validation->validation_kit_status === 'complete' ? 'checked' : '' }}">{{ $validation->validation_kit_status === 'complete' ? '✓' : '' }}</span>
          Complete
        </span>
        <span>
          <span class="cb {{ $validation->validation_kit_status === 'incomplete' ? 'checked' : '' }}">{{ $validation->validation_kit_status === 'incomplete' ? '✓' : '' }}</span>
          Incomplete
        </span>
      </div>
    </td>
  </tr>
  <tr>
    <td class="lbl">Validation folder created and label as indicated in SOP:</td>
    <td class="val" colspan="3">
      <strong>Folder name:</strong> {{ $validation->validation_folder_name ?: '—' }}
    </td>
  </tr>
  <tr>
    <td class="lbl">Loading data set from software validation kit:</td>
    <td class="val" colspan="3">
      <strong>File name:</strong> {{ $validation->validation_file_name ?: '—' }}
    </td>
  </tr>
  <tr>
    <td class="lbl">Specify the SOP (document code) and section of SOP:</td>
    <td class="val" colspan="3">
      Code: {{ $validation->sop_document_code ?: '—' }} &nbsp;/&nbsp;
      Section: {{ $validation->sop_section ?: '—' }}
    </td>
  </tr>
  <tr class="env-row">
    <td class="lbl">Environmental conditions during validation process:</td>
    <td class="val" colspan="3">
      Temperature: <strong>{{ $validation->env_temperature ?: '—' }}</strong>
      &nbsp;·&nbsp; Humidity: <strong>{{ $validation->env_humidity ?: '—' }}</strong>
      &nbsp;·&nbsp; Data Logger: <strong>{{ $validation->data_logger_env ?: '—' }}</strong>
    </td>
  </tr>
  @if($validation->database)
  <tr>
    <td class="lbl">Database:</td>
    <td class="val" colspan="3">{{ $validation->database->name }}</td>
  </tr>
  @endif
</table>

{{-- ── Details of Procedure ── --}}
<div class="section-title">Details of Procedure</div>
<div class="proc-box">{{ $validation->details_of_procedure ?: 'No procedure details recorded.' }}</div>

{{-- ── Attachments ── --}}
@if($validation->files->isNotEmpty())
<div class="section-title">Attachments</div>
<ol class="attach-list">
  @foreach($validation->files as $f)
  <li>{{ $f->original_name }}</li>
  @endforeach
</ol>
@endif

{{-- ── Signature ── --}}
<table class="sig-table">
  <tr>
    <td>
      <strong>Validated by / IT Technician</strong><br>
      <span style="font-size:7.5pt;color:#555;">{{ $validation->validation_done_by ?: '___________________________' }}</span>
      <div class="sig-line">Signature &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Date: ___________</div>
    </td>
    <td>
      <strong>Study Director</strong><br>
      <span style="font-size:7.5pt;color:#555;">
        @php
          $sda = $project->studyDirectorAppointmentForm;
          $sd  = $sda?->studyDirector;
          echo $sd ? trim(($sd->titre_personnel ?? '') . ' ' . $sd->prenom . ' ' . $sd->nom) : '___________________________';
        @endphp
      </span>
      <div class="sig-line">Signature &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Date: ___________</div>
    </td>
  </tr>
</table>

</body>
</html>

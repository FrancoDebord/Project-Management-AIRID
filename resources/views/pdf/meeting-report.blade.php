<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Critical Phase Agreement Meeting Minutes — {{ $project->project_code }}</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }

  @page {
    margin: 14mm 22mm 16mm 22mm;
    size: A4 portrait;
  }

  body {
    font-family: Arial, sans-serif;
    font-size: 9pt;
    color: #111;
    line-height: 1.35;
    padding: 0 4mm;
  }

  /* ── Header ─────────────────────────────────────────────────── */
  .header-img {
    width: 100%;
    display: block;
    margin-bottom: 6px;
  }

  /* ── Document title block ───────────────────────────────────── */
  .doc-title {
    text-align: center;
    margin: 6px 0 8px 0;
    border-top: 2px solid #C10202;
    border-bottom: 2px solid #C10202;
    padding: 5px 0;
  }
  .doc-title h1 {
    font-size: 13pt;
    font-weight: bold;
    text-transform: uppercase;
    color: #C10202;
    letter-spacing: .03em;
  }
  .doc-title .doc-subtitle {
    font-size: 8.5pt;
    color: #444;
    margin-top: 2px;
  }

  /* ── Info table ─────────────────────────────────────────────── */
  .info-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 8px;
    font-size: 8.5pt;
  }
  .info-table td {
    padding: 3px 6px;
    vertical-align: top;
  }
  .info-table .lbl {
    font-weight: bold;
    width: 38%;
    color: #333;
    white-space: nowrap;
  }
  .info-table .val {
    border-bottom: 1px dotted #aaa;
  }
  .info-outer {
    border: 1px solid #ccc;
    border-radius: 3px;
    padding: 4px;
    margin-bottom: 8px;
  }

  /* ── Section heading ────────────────────────────────────────── */
  .section-heading {
    background: #1a3a6b;
    color: #fff;
    font-weight: bold;
    font-size: 9pt;
    padding: 3px 7px;
    margin: 8px 0 4px 0;
    border-radius: 2px;
  }

  /* ── Agenda list ────────────────────────────────────────────── */
  .agenda-list {
    margin: 0 0 0 18px;
    padding: 0;
    font-size: 8.5pt;
  }
  .agenda-list li { margin-bottom: 2px; }

  /* ── Body section ───────────────────────────────────────────── */
  .body-section {
    margin-bottom: 6px;
    font-size: 8.5pt;
  }
  .body-section .sn {
    font-weight: bold;
    color: #C10202;
  }
  .body-section p { margin-top: 3px; }

  /* ── Critical phases table ──────────────────────────────────── */
  .cp-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 8pt;
    margin-top: 4px;
  }
  .cp-table th {
    background: #1a3a6b;
    color: #fff;
    padding: 3px 5px;
    text-align: left;
    font-size: 7.5pt;
  }
  .cp-table td {
    border: 1px solid #ccc;
    padding: 3px 5px;
    vertical-align: top;
  }
  .cp-table tr:nth-child(even) td { background: #f5f8ff; }

  /* ── Participants table ─────────────────────────────────────── */
  .pt-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 8pt;
    margin-top: 3px;
  }
  .pt-table th {
    background: #e8eef7;
    color: #1a3a6b;
    padding: 3px 5px;
    text-align: left;
    font-size: 7.5pt;
    border: 1px solid #ccc;
  }
  .pt-table td {
    border: 1px solid #ccc;
    padding: 3px 5px;
  }

  /* ── Attachments ────────────────────────────────────────────── */
  .attach-list {
    margin: 2px 0 0 18px;
    font-size: 8.5pt;
  }
  .attach-list li { margin-bottom: 2px; }

  /* ── Signature block ────────────────────────────────────────── */
  .sig-block {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    font-size: 8.5pt;
  }
  .sig-block td {
    width: 50%;
    padding: 6px 10px;
    vertical-align: top;
  }
  .sig-line {
    border-top: 1px solid #333;
    margin-top: 22px;
    padding-top: 3px;
    font-size: 7.5pt;
    color: #555;
  }
  .sig-label {
    font-weight: bold;
    font-size: 8.5pt;
    color: #1a3a6b;
  }

  /* ── Ref / footer ───────────────────────────────────────────── */
  .doc-ref {
    font-size: 7pt;
    color: #888;
    text-align: right;
    margin-bottom: 4px;
  }
</style>
</head>
<body>

{{-- ── AIRID Header ── --}}
<img src="{{ $headerImagePath }}" class="header-img" alt="AIRID Header">

{{-- ── Document title ── --}}
<div class="doc-title">
  <h1>Critical Phase Agreement Meeting Minutes</h1>
  <div class="doc-subtitle">Study Initiation Meeting — Planning Phase</div>
</div>

<div class="doc-ref">
  Ref: {{ $project->project_code }} / SIM-{{ $meeting->id }} &nbsp;|&nbsp;
  Generated: {{ now()->format('d/m/Y H:i') }}
</div>

{{-- ── Meeting information ── --}}
<div class="info-outer">
<table class="info-table">
  <tr>
    <td class="lbl">Project Code:</td>
    <td class="val">{{ $project->project_code }}</td>
    <td class="lbl">Protocol Code:</td>
    <td class="val">{{ $project->protocol_code ?: '—' }}</td>
  </tr>
  <tr>
    <td class="lbl">Study Title:</td>
    <td class="val" colspan="3">{{ $project->project_title }}</td>
  </tr>
  <tr>
    <td class="lbl">Study Director:</td>
    <td class="val">{{ $sdName }}</td>
    <td class="lbl">Type of Meeting:</td>
    <td class="val">Study Initiation Meeting</td>
  </tr>
  <tr>
    <td class="lbl">State Department:</td>
    <td class="val">Quality Assurance</td>
    <td class="lbl">QA Supervisor:</td>
    <td class="val">{{ $qaInspectorName }}</td>
  </tr>
  <tr>
    <td class="lbl">Date:</td>
    <td class="val">{{ $meeting->date_performed
        ? \Carbon\Carbon::parse($meeting->date_performed)->format('d/m/Y')
        : ($meeting->date_scheduled
            ? \Carbon\Carbon::parse($meeting->date_scheduled)->format('d/m/Y')
            : '—') }}</td>
    <td class="lbl">Time:</td>
    <td class="val">{{ $meeting->time_scheduled ?: '—' }}</td>
  </tr>
  <tr>
    <td class="lbl">Venue / Link:</td>
    <td class="val" colspan="3">
      @if($meeting->meeting_link)
        {{ $meeting->meeting_link }}
      @else
        AIRID Premises
      @endif
    </td>
  </tr>
  <tr>
    <td class="lbl">GLP Study:</td>
    <td class="val">{{ $project->is_glp ? 'Yes — GLP' : 'No — Non-GLP' }}</td>
    <td class="lbl">Study Nature:</td>
    <td class="val">{{ $project->project_nature ?: '—' }}</td>
  </tr>
</table>
</div>

{{-- ── Attendees ── --}}
<div class="section-heading">Attendees</div>
@if($participants->isNotEmpty())
<table class="pt-table">
  <thead>
    <tr>
      <th>#</th>
      <th>Name</th>
      <th>Title / Function</th>
    </tr>
  </thead>
  <tbody>
    @foreach($participants as $i => $p)
    <tr>
      <td>{{ $i + 1 }}</td>
      <td>{{ trim(($p->titre_personnel ?? '') . ' ' . $p->prenom . ' ' . $p->nom) }}</td>
      <td>{{ $p->role_personnel ?? '—' }}</td>
    </tr>
    @endforeach
  </tbody>
</table>
@else
<p style="font-size:8.5pt;color:#888;margin:2px 0;">No participants recorded.</p>
@endif

{{-- ── Agenda ── --}}
<div class="section-heading">Agenda</div>
<ol class="agenda-list">
  <li>Opening / Introduction</li>
  <li>GLP Regulatory Requirements</li>
  <li>Study Personnel &amp; Responsibilities</li>
  <li>Testing Programme Overview</li>
  <li>Critical Phases Identification &amp; Agreement on Inspection Dates</li>
  <li>Any Other Business</li>
</ol>

{{-- ── Section 1 — Opening ── --}}
<div class="section-heading">1. Opening / Introduction</div>
<div class="body-section">
  <p>
    The meeting was opened by the Study Director,
    <strong>{{ $sdName }}</strong>, who welcomed the attendees and outlined the purpose
    of the Study Initiation Meeting. The agenda was presented and agreed upon by all parties.
  </p>
  <p>The meeting aimed to ensure all study personnel are briefed on their roles, that applicable
    regulatory requirements are understood, and that critical phases requiring QA oversight are
    formally agreed.</p>
</div>

{{-- ── Section 2 — GLP ── --}}
<div class="section-heading">2. GLP Regulatory Requirements</div>
<div class="body-section">
  @if($project->is_glp)
  <p>
    The Study Director reminded all attendees that this study is conducted in compliance with
    <strong>OECD Principles of Good Laboratory Practice (GLP)</strong> and applicable national
    regulations. Key obligations discussed included:
  </p>
  <ul style="margin:4px 0 0 18px;font-size:8.5pt;">
    <li>Adherence to the approved Study Protocol and any amendments.</li>
    <li>Accurate and contemporaneous recording of all data in raw data files.</li>
    <li>Retention of all study-related records and specimens per GLP archiving requirements.</li>
    <li>Reporting of any deviations from the protocol to the Study Director immediately.</li>
    <li>Cooperation with QA inspections throughout the study duration.</li>
  </ul>
  @else
  <p>
    While this study is <strong>non-GLP</strong>, all personnel are expected to follow
    AIRID's standard operating procedures and good scientific practice throughout the study.
    Data integrity, traceability, and proper documentation remain mandatory.
  </p>
  @endif
</div>

{{-- ── Section 3 — Personnel ── --}}
<div class="section-heading">3. Study Personnel &amp; Responsibilities</div>
<div class="body-section">
  <p>The following key study personnel and their responsibilities were confirmed:</p>
  <table class="pt-table" style="margin-top:4px;">
    <thead>
      <tr>
        <th>Role</th>
        <th>Name</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Study Director</td>
        <td>{{ $sdName }}</td>
      </tr>
      @if($pmName)
      <tr>
        <td>Project Manager</td>
        <td>{{ $pmName }}</td>
      </tr>
      @endif
      @if($qaInspectorName && $qaInspectorName !== '—')
      <tr>
        <td>QA Supervisor / Inspector</td>
        <td>{{ $qaInspectorName }}</td>
      </tr>
      @endif
      @if($sponsor)
      <tr>
        <td>Sponsor</td>
        <td>{{ $sponsor }}</td>
      </tr>
      @endif
      @foreach($keyPersonnel as $kp)
      <tr>
        <td>Key Personnel</td>
        <td>{{ trim(($kp->titre_personnel ?? '') . ' ' . $kp->prenom . ' ' . $kp->nom) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  <p style="margin-top:4px;">
    All personnel acknowledged their responsibilities as defined in the study protocol
    and applicable SOPs. Any questions regarding responsibilities were addressed by the Study Director.
  </p>
</div>

{{-- ── Section 4 — Testing Programme ── --}}
<div class="section-heading">4. Testing Programme Overview</div>
<div class="body-section">
  <table class="info-table" style="margin-bottom:0;">
    <tr>
      <td class="lbl">Test System:</td>
      <td class="val">{{ $project->test_system ?: '—' }}</td>
      <td class="lbl">Study Type:</td>
      <td class="val">{{ $project->studyTypesApplied->pluck('study_type_name')->join(', ') ?: '—' }}</td>
    </tr>
    @if($project->date_debut_previsionnelle || $project->date_fin_previsionnelle)
    <tr>
      <td class="lbl">Planned Start:</td>
      <td class="val">{{ $project->date_debut_previsionnelle
          ? \Carbon\Carbon::parse($project->date_debut_previsionnelle)->format('d/m/Y') : '—' }}</td>
      <td class="lbl">Planned End:</td>
      <td class="val">{{ $project->date_fin_previsionnelle
          ? \Carbon\Carbon::parse($project->date_fin_previsionnelle)->format('d/m/Y') : '—' }}</td>
    </tr>
    @endif
  </table>
  <p style="margin-top:5px;">
    The Study Director presented an overview of the testing programme, including the experimental
    design, planned activities, and expected timelines. A total of
    <strong>{{ $allActivitiesCount }}</strong> study {{ Str::plural('activity', $allActivitiesCount) }}
    {{ $allActivitiesCount === 1 ? 'has' : 'have' }} been planned for this study, of which
    <strong>{{ $criticalPhases->count() }}</strong> {{ $criticalPhases->count() === 1 ? 'has' : 'have' }}
    been identified as critical phases requiring QA inspection.
  </p>
</div>

{{-- ── Section 5 — Critical Phases ── --}}
<div class="section-heading">5. Critical Phases — Agreement on Inspection Dates</div>
<div class="body-section">
  <p>
    The following activities were identified as <strong>Critical Phases</strong> of this study.
    Inspection dates were agreed between the Study Director and the QA unit as shown below.
    All parties signed off on these dates during the meeting.
  </p>
  @if($criticalPhases->isEmpty())
    <p style="color:#888;margin-top:4px;"><em>No critical phases have been identified for this study.</em></p>
  @else
  <table class="cp-table" style="margin-top:5px;">
    <thead>
      <tr>
        <th>#</th>
        <th>Critical Phase / Activity</th>
        <th>Planned Start</th>
        <th>Planned End</th>
        <th>Assigned To</th>
        <th>QA Inspection Date (Agreed)</th>
        <th>Inspection Status</th>
      </tr>
    </thead>
    <tbody>
      @foreach($criticalPhases as $i => $cp)
      @php
        $insp = $cpInspections->get($cp->id);
      @endphp
      <tr>
        <td>{{ $i + 1 }}</td>
        <td>{{ $cp->study_activity_name }}</td>
        <td>{{ $cp->estimated_activity_date
            ? \Carbon\Carbon::parse($cp->estimated_activity_date)->format('d/m/Y') : '—' }}</td>
        <td>{{ $cp->estimated_activity_end_date
            ? \Carbon\Carbon::parse($cp->estimated_activity_end_date)->format('d/m/Y') : '—' }}</td>
        <td>{{ $cp->personneResponsable
            ? trim($cp->personneResponsable->prenom . ' ' . $cp->personneResponsable->nom)
            : '—' }}</td>
        <td>
          @if($insp && $insp->date_scheduled)
            {{ \Carbon\Carbon::parse($insp->date_scheduled)->format('d/m/Y') }}
          @elseif($insp)
            TBD
          @else
            Not scheduled
          @endif
        </td>
        <td>
          @if($insp && $insp->date_performed)
            <span style="color:#198754;font-weight:bold;">Completed</span>
          @elseif($insp)
            <span style="color:#856404;">Planned</span>
          @else
            <span style="color:#888;">—</span>
          @endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  <p style="margin-top:5px;font-size:7.5pt;color:#555;">
    <em>All attendees agreed that QA inspections will be conducted at or around the dates indicated above.
    Any changes to inspection dates must be communicated to the QA unit and approved by the Study Director.</em>
  </p>
  @endif

  {{-- CPIA reference --}}
  @if($cpia)
  <p style="margin-top:5px;">
    A <strong>Critical Phase Impact Assessment (CPIA)</strong> was completed for this study
    on <strong>{{ $cpia->completed_at ? $cpia->completed_at->format('d/m/Y') : '—' }}</strong>.
    The assessment findings informed the identification of the critical phases listed above.
  </p>
  @endif
</div>

{{-- ── Section 6 — AOB ── --}}
<div class="section-heading">6. Any Other Business</div>
<div class="body-section">
  @if($meeting->breve_description)
  <p>{{ $meeting->breve_description }}</p>
  @else
  <p>No other business was raised. The meeting was closed by the Study Director, who thanked
    all attendees for their participation.</p>
  @endif
  <p style="margin-top:4px;">
    The next steps are: finalisation of the study protocol (if not yet approved), commencement
    of experimental activities per the agreed schedule, and scheduling of QA critical phase
    inspections as agreed above.
  </p>
</div>

{{-- ── Attachments ── --}}
<div class="section-heading">Attachments</div>
<ol class="attach-list">
  <li>Study Protocol / Protocol Amendment (if applicable)</li>
  <li>List of Study Personnel with signatures</li>
  @if($cpia)
  <li>Critical Phase Impact Assessment Report (CPIA)</li>
  @endif
  <li>QA Inspection Schedule</li>
</ol>

{{-- ── Signature block ── --}}
<table class="sig-block">
  <tr>
    <td>
      <div class="sig-label">Study Director</div>
      <div style="font-size:8pt;color:#555;margin-top:2px;">{{ $sdName }}</div>
      @if($sdSignedAt)
      <div style="margin-top:8px;padding:4px 6px;background:#d4edda;border-radius:3px;font-size:7.5pt;color:#155724;">
        <strong>Signed electronically</strong><br>
        {{ $sdSignedAt->format('d/m/Y H:i') }}
      </div>
      @else
      <div class="sig-line">Signature &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Date: ___________</div>
      @endif
    </td>
    <td>
      <div class="sig-label">QA Manager / Supervisor</div>
      <div style="font-size:8pt;color:#555;margin-top:2px;">{{ $qaInspectorName }}</div>
      <div class="sig-line">Signature &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Date: ___________</div>
    </td>
  </tr>
</table>

</body>
</html>

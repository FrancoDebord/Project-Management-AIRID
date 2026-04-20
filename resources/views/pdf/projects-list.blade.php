<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 8pt;
    color: #111;
    background: #fff;
    padding: 8mm 18mm 14mm 18mm;
  }

  /* ── Header ── */
  .airid-header {
    margin-bottom: 1mm;
  }
  .meta-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 7pt;
    color: #555;
    border-bottom: 2pt solid #1a3a6b;
    padding-bottom: 2mm;
    margin-bottom: 3mm;
  }
  .meta-bar .title {
    font-size: 11pt;
    font-weight: bold;
    color: #1a3a6b;
    text-transform: uppercase;
    letter-spacing: .04em;
  }
  .meta-bar .right { text-align: right; line-height: 1.6; }

  /* ── Filter summary ── */
  .filter-bar {
    font-size: 7.5pt;
    color: #555;
    margin-bottom: 3mm;
    padding: 1.5mm 3mm;
    background: #f4f6fb;
    border-left: 3pt solid #1a3a6b;
    border-radius: 1mm;
  }

  /* ── Table ── */
  table {
    width: 100%;
    border-collapse: collapse;
    font-size: 7.5pt;
  }
  thead th {
    background: #1a3a6b;
    color: #fff;
    font-weight: bold;
    font-size: 7pt;
    text-transform: uppercase;
    letter-spacing: .04em;
    padding: 2.5mm 2mm;
    border: 1pt solid #1a3a6b;
    white-space: nowrap;
    vertical-align: middle;
  }
  tbody tr:nth-child(even) { background: #f7f8fc; }
  tbody tr:nth-child(odd)  { background: #fff; }
  tbody td {
    padding: 2mm 2mm;
    border: .5pt solid #dde0e8;
    vertical-align: top;
    line-height: 1.4;
  }
  .col-num     { width: 5mm;  text-align: center; color: #888; }
  .col-proto   { width: 22mm; }
  .col-code    { width: 22mm; font-weight: bold; color: #1a3a6b; }
  .col-title   { width: 50mm; }
  .col-sponsor { width: 30mm; }
  .col-manuf   { width: 24mm; }
  .col-sd      { width: 30mm; }
  .col-date    { width: 16mm; white-space: nowrap; color: #444; }
  .col-repl    { width: 26mm; }
  .col-pm      { width: 28mm; }
  .col-status  { width: 18mm; text-align: center; }

  /* ── Status badges ── */
  .status-badge {
    display: inline-block;
    padding: .5mm 2.5mm;
    border-radius: 20pt;
    font-size: 6.5pt;
    font-weight: bold;
    white-space: nowrap;
  }
  .s-not_started { background: #e9ecef; color: #495057; }
  .s-in_progress { background: #d1ecf1; color: #0c5460; }
  .s-suspended   { background: #fff3cd; color: #856404; }
  .s-completed   { background: #d4edda; color: #155724; }
  .s-archived    { background: #cce5ff; color: #004085; }

  /* ── Mini badges ── */
  .tag {
    display: inline-block;
    font-size: 6pt;
    font-weight: bold;
    padding: .3mm 1.5mm;
    border-radius: 2mm;
    vertical-align: middle;
  }
  .tag-glp     { background: #1a3a6b; color: #fff; }
  .tag-legacy  { background: #856404; color: #fff; }

  /* ── Footer ── */
  .page-footer {
    position: fixed;
    bottom: 5mm;
    left: 18mm;
    right: 18mm;
    font-size: 6.5pt;
    color: #aaa;
    border-top: .5pt solid #ddd;
    padding-top: 1.5mm;
    display: flex;
    justify-content: space-between;
  }

  @page { size: A4 landscape; margin: 12mm 18mm 14mm 18mm; }
</style>
</head>
<body>

{{-- ── AIRID Header image ── --}}
<div class="airid-header">
  <img src="{{ public_path('storage/assets/header/entete_airid.png') }}"
       style="width:100%;max-height:20mm;object-fit:contain;" alt="AIRID">
</div>

{{-- ── Meta bar ── --}}
<div class="meta-bar">
  <div class="title"><i>List of Projects</i></div>
  <div class="right">
    <strong>{{ $projects->count() }}</strong> project(s) &nbsp;·&nbsp;
    Generated on {{ now()->format('d/m/Y H:i') }}
  </div>
</div>

{{-- ── Active filters ── --}}
@if($search || $status)
<div class="filter-bar">
  <strong>Filters applied:</strong>
  @if($search) &nbsp; Search: <em>"{{ $search }}"</em> @endif
  @if($status) &nbsp; Status: <em>{{ $statuses[$status] ?? $status }}</em> @endif
</div>
@endif

{{-- ── Projects table ── --}}
@if($projects->isEmpty())
  <p style="text-align:center;color:#888;margin-top:10mm;">No projects found.</p>
@else
<table>
  <thead>
    <tr>
      <th class="col-num">#</th>
      <th class="col-proto">Protocol Code</th>
      <th class="col-code">Project Code</th>
      <th class="col-title">Title</th>
      <th class="col-sponsor">Sponsor</th>
      <th class="col-manuf">Manufacturer</th>
      <th class="col-sd">Study Director</th>
      <th class="col-date">Appointment</th>
      <th class="col-repl">SD Replacement</th>
      <th class="col-pm">Project Manager</th>
      <th class="col-status">Status</th>
    </tr>
  </thead>
  <tbody>
  @foreach($projects as $i => $p)
    @php
      $sda      = $p->studyDirectorAppointmentForm;
      $sd       = $sda?->studyDirector;
      $sdName   = $sd ? trim(($sd->titre_personnel ?? '') . ' ' . $sd->prenom . ' ' . $sd->nom) : '—';
      $apptDate = $sda?->sd_appointment_date
                    ? \Carbon\Carbon::parse($sda->sd_appointment_date)->format('d/m/Y')
                    : '—';

      $replacement = $p->studyDirectorReplacementHistory;
      $replSd      = $replacement ? \App\Models\Pro_Personnel::find($replacement->study_director) : null;
      $replName    = $replSd
                       ? trim(($replSd->titre_personnel ?? '') . ' ' . $replSd->prenom . ' ' . $replSd->nom)
                       : null;
      $replDate    = $replacement?->replacement_date
                       ? \Carbon\Carbon::parse($replacement->replacement_date)->format('d/m/Y')
                       : null;

      $pm     = $p->projectManager;
      $pmName = $pm ? trim(($pm->titre_personnel ?? '') . ' ' . $pm->prenom . ' ' . $pm->nom) : '—';

      $stageKey   = str_replace(' ', '_', $p->project_stage ?? 'not_started');
      $stageLabel = match($p->project_stage) {
          'not_started' => 'Not Started',
          'in progress' => 'In Progress',
          'suspended'   => 'Suspended',
          'completed'   => 'Completed',
          'archived'    => 'Archived',
          default       => ucfirst($p->project_stage ?? '—'),
      };
    @endphp
    <tr>
      <td class="col-num">{{ $i + 1 }}</td>
      <td class="col-proto">{{ $p->protocol_code ?: '—' }}</td>
      <td class="col-code">
        {{ $p->project_code }}
        @if($p->is_glp)<span class="tag tag-glp">GLP</span>@endif
        @if($p->is_legacy)<span class="tag tag-legacy">Legacy</span>@endif
      </td>
      <td class="col-title">{{ $p->project_title }}</td>
      <td class="col-sponsor">
        {{ $p->sponsor_name ?: '—' }}
        @if($p->sponsor_email)
          <br><span style="color:#555;font-size:6.5pt;">{{ $p->sponsor_email }}</span>
        @endif
      </td>
      <td class="col-manuf">
        @if($p->manufacturer_name)
          {{ $p->manufacturer_name }}
        @elseif($p->sponsor_name)
          <span style="color:#888;font-style:italic;">Same as Sponsor</span>
        @else
          —
        @endif
      </td>
      <td class="col-sd">{{ $sdName }}</td>
      <td class="col-date">{{ $apptDate }}</td>
      <td class="col-repl">
        @if($replName)
          {{ $replName }}
          @if($replDate)<br><span style="color:#888;font-size:6.5pt;">{{ $replDate }}</span>@endif
        @else
          <span style="color:#bbb;">—</span>
        @endif
      </td>
      <td class="col-pm">{{ $pmName }}</td>
      <td class="col-status">
        <span class="status-badge s-{{ $stageKey }}">{{ $stageLabel }}</span>
      </td>
    </tr>
  @endforeach
  </tbody>
</table>
@endif

<div class="page-footer">
  <span>AIRID Project Management System</span>
  <span>{{ now()->format('d/m/Y H:i') }}</span>
</div>

</body>
</html>

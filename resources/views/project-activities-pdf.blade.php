<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }

@page {
    size: A4 landscape;
    margin: 14mm 16mm 16mm 16mm;
}

body {
    font-family: DejaVu Sans, Arial, sans-serif;
    font-size: 8.5pt;
    color: #222;
}

/* ── Header ── */
.doc-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
    border-bottom: 2px solid #1a3a6b;
    padding-bottom: 8px;
}
.logo-box { min-width:160px; max-width:160px; }
.logo-box img { max-width:100%; max-height:55px; }
.doc-header-center { text-align:center; flex:1; padding: 0 12px; }
.doc-header-center .org-name { font-size:9pt; color:#1a3a6b; font-weight:600; }
.doc-header-center .doc-title { font-size:13pt; font-weight:bold; text-transform:uppercase; letter-spacing:1px; margin-top:3px; }
.doc-ref-box { font-size:7.5pt; text-align:right; white-space:nowrap; min-width:130px; }
.doc-ref-box .ref-num { font-weight:bold; font-size:9pt; }

/* ── Project Info ── */
.proj-info {
    margin-bottom: 10px;
    padding: 6px 10px;
    background: #f0f4ff;
    border-left: 4px solid #1a3a6b;
    font-size: 8pt;
    line-height: 1.6;
}
.proj-info strong { color:#1a3a6b; }

/* ── Table ── */
table.act {
    width: 100%;
    border-collapse: collapse;
    margin-top: 4px;
}
table.act th, table.act td {
    border: 0.7px solid #bbb;
    padding: 5px 7px;
    vertical-align: middle;
}
table.act thead th {
    background: #1a3a6b;
    color: #fff;
    font-size: 8pt;
    font-weight: bold;
    text-align: center;
    padding: 6px 7px;
}
table.act tbody td { font-size: 8pt; line-height: 1.4; }
table.act tbody tr:nth-child(even) td { background: #f4f6fb; }

.badge-critical { background:#C10202; color:#fff; padding:2px 6px; border-radius:3px; font-size:7pt; }
.badge-done     { background:#198754; color:#fff; padding:2px 6px; border-radius:3px; font-size:7pt; }
.badge-inprog   { background:#0d6efd; color:#fff; padding:2px 6px; border-radius:3px; font-size:7pt; }
.badge-pending  { background:#6c757d; color:#fff; padding:2px 6px; border-radius:3px; font-size:7pt; }

/* ── Footer ── */
.doc-footer {
    margin-top: 10px;
    border-top: 1px solid #ccc;
    padding-top: 5px;
    font-size: 7pt;
    color:#555;
    display:flex;
    justify-content:space-between;
}
</style>
</head>
<body>

{{-- Header --}}
<div class="doc-header">
    <div class="logo-box">
        <img src="{{ $headerImagePath }}" alt="AIRID">
    </div>
    <div class="doc-header-center">
        <div class="org-name">AIRID — African Institute for Research in Infectious Diseases</div>
        <div class="doc-title">Study Activities Schedule</div>
    </div>
    <div class="doc-ref-box">
        <div class="ref-num">ACT-{{ $project->project_code }}</div>
        <div>Generated: {{ now()->format('d/m/Y') }}</div>
    </div>
</div>

{{-- Project info --}}
<div class="proj-info">
    <strong>Study:</strong> {{ $project->project_code }} — {{ $project->project_title }}
    &nbsp;&nbsp;
    <strong>Status:</strong> {{ ucfirst($project->project_stage ?? '—') }}
    &nbsp;&nbsp;
    <strong>GLP:</strong> {{ $project->is_glp ? 'Yes' : 'No' }}
    @if($sdName)
        &nbsp;&nbsp;<strong>Study Director:</strong> {{ $sdName }}
    @endif
</div>

{{-- Activities table --}}
<table class="act">
    <thead>
        <tr>
            <th style="width:22px;">#</th>
            <th style="width:70px;">Category</th>
            <th style="width:160px;">Activity</th>
            <th style="width:48px;">Critical</th>
            <th style="width:75px;">Responsible</th>
            <th style="width:55px;">Planned Start</th>
            <th style="width:55px;">Planned End</th>
            <th style="width:55px;">Actual Date</th>
            <th style="width:50px;">Status</th>
            <th>Comments</th>
        </tr>
    </thead>
    <tbody>
        @php $i = 1; @endphp
        @forelse($activitiesByCategory as $catName => $activities)
        {{-- Category sub-header --}}
        <tr>
            <td colspan="10" style="background:#e8f0fe;color:#1a3a6b;font-weight:bold;font-size:8.5pt;padding:5px 8px;">
                {{ $catName }}
            </td>
        </tr>
        @foreach($activities as $act)
        @php
            $statusLabel = match($act->status) {
                'completed'   => 'Done',
                'in_progress' => 'In progress',
                default       => 'Pending',
            };
            $statusCls = match($act->status) {
                'completed'   => 'badge-done',
                'in_progress' => 'badge-inprog',
                default       => 'badge-pending',
            };
            $resp = $act->personneResponsable;
            $respName = $resp ? trim($resp->prenom . ' ' . $resp->nom) : '—';
        @endphp
        <tr>
            <td style="text-align:center;">{{ $i++ }}</td>
            <td>{{ $catName }}</td>
            <td style="text-align:left;">{{ $act->study_activity_name }}</td>
            <td style="text-align:center;">
                @if($act->phase_critique)
                    <span class="badge-critical">Critical</span>
                @else
                    —
                @endif
            </td>
            <td>{{ $respName }}</td>
            <td style="text-align:center;">{{ $act->estimated_activity_date ? \Carbon\Carbon::parse($act->estimated_activity_date)->format('d/m/Y') : '—' }}</td>
            <td style="text-align:center;">{{ ($act->estimated_activity_end_date && $act->estimated_activity_end_date != $act->estimated_activity_date) ? \Carbon\Carbon::parse($act->estimated_activity_end_date)->format('d/m/Y') : '—' }}</td>
            <td style="text-align:center;">{{ $act->actual_activity_date ? \Carbon\Carbon::parse($act->actual_activity_date)->format('d/m/Y') : '—' }}</td>
            <td style="text-align:center;"><span class="{{ $statusCls }}">{{ $statusLabel }}</span></td>
            <td style="text-align:left;">{{ $act->commentaire ?? '' }}</td>
        </tr>
        @endforeach
        @empty
        <tr><td colspan="10" style="text-align:center;padding:15px;color:#888;">No activities found.</td></tr>
        @endforelse
    </tbody>
</table>

{{-- Footer --}}
<div class="doc-footer">
    <div>AIRID — African Institute for Research in Infectious Diseases</div>
    <div>Study: {{ $project->project_code }} | Activities Schedule | Generated {{ now()->format('d/m/Y H:i') }}</div>
</div>

</body>
</html>

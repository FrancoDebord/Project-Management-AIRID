@extends('index-new')
@section('title', 'QA Activities Checklist – ' . $project->project_code)

@section('content')
@php
    $backUrl = route('project.show', $project->id) . '?project_id=' . $project->id . '#step6';
    $printUrl = route('printQaActivitiesChecklist', ['project_id' => $project->id]);
    $docRef    = $globalSettings['doc_ref_master'] ?? 'QA-PR-1-011/05';
    $issueDate = $globalSettings['doc_issue_date']  ?? '01/08/2025';
    $nextReview= $globalSettings['doc_next_review'] ?? '31/07/2027';
    $headerImg = asset('storage/assets/logo/airid.jpg');
@endphp

<style>
:root { --cl-purple:#6f42c1; --cl-purple-dark:#4e2d8e; }

/* ── AIRID document header ── */
.cl-doc-header {
    display: flex;
    align-items: center;
    border-bottom: 2px solid var(--cl-purple-dark);
    padding-bottom: 12px;
    margin-bottom: 18px;
    gap: 16px;
}
.cl-doc-header img { max-height: 80px; max-width: 80px; object-fit: contain; }
.cl-header-right { flex: 1; }
.cl-header-right .org-name { font-size: 1rem; font-weight: 700; color: #1a3a6b; }
.cl-header-right .org-info  { font-size: .78rem; color: #444; line-height: 1.7; margin-top: 2px; }
.cl-doc-ref { font-size: .74rem; text-align: right; white-space: nowrap; color: #444; line-height: 1.7; }
.cl-doc-ref strong { font-size: .82rem; color: #1a3a6b; }

/* ── Title ── */
.cl-title {
    text-align: center;
    font-size: 1.05rem;
    font-weight: 700;
    text-transform: uppercase;
    text-decoration: underline;
    letter-spacing: .04em;
    color: #1a3a6b;
    margin-bottom: 16px;
}

/* ── Project info banner ── */
.cl-proj-banner {
    border-left: 5px solid var(--cl-purple);
    background: #f8f5ff;
    border-radius: 6px;
    padding: 10px 16px;
    margin-bottom: 18px;
    font-size: .88rem;
}
.cl-proj-banner .label { font-weight: 600; color: var(--cl-purple-dark); min-width: 110px; display: inline-block; }

/* ── Action bar ── */
.cl-action-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    background: linear-gradient(90deg, var(--cl-purple), var(--cl-purple-dark));
    border-radius: 10px;
    padding: 10px 18px;
    margin-bottom: 18px;
}
.cl-action-bar .cl-title-bar { color: #fff; font-weight: 600; font-size: .95rem; }
.cl-action-bar .cl-subtitle   { color: rgba(255,255,255,.75); font-size: .78rem; }

/* ── Table ── */
.cl-table { width: 100%; border-collapse: collapse; font-size: .88rem; }
.cl-table thead th {
    background: linear-gradient(90deg, var(--cl-purple), var(--cl-purple-dark));
    color: #fff;
    padding: 8px 10px;
    font-size: .84rem;
    font-weight: 600;
    border: 1px solid #c3a8f0;
}
.cl-table tbody tr:nth-child(even) { background: #faf8ff; }
.cl-table tbody tr:hover { background: #f0eaff; }
.cl-table td {
    border: 1px solid #ddd;
    padding: 7px 10px;
    vertical-align: middle;
}
.cl-num { width: 38px; text-align: center; font-weight: 600; color: #666; font-size: .82rem; }
.cl-activity { }
.cl-date-col { width: 150px; }
.cl-mov-col  { width: 230px; }
.cl-check-col{ width: 70px; text-align: center; }

/* Auto-prefill badge */
.auto-badge {
    display: inline-block;
    background: #e8f3ff;
    color: #1a3a6b;
    font-size: .65rem;
    font-weight: 600;
    padding: 1px 6px;
    border-radius: 20px;
    margin-left: 5px;
    vertical-align: middle;
}

/* ── Footer note ── */
.cl-footer-note {
    margin-top: 12px;
    font-size: .78rem;
    color: #666;
    border-top: 1px solid #e0d9f5;
    padding-top: 8px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;
}
</style>

{{-- ── Back + breadcrumb ── --}}
<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('project.create') }}?project_id={{ $project->id }}#step6"
       class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to QA
    </a>
    <span class="text-muted small">/ {{ $project->project_code }} / QA Activities Checklist</span>
</div>

{{-- ── AIRID document header ── --}}
<div class="cl-doc-header">
    <div>
        <img src="{{ $headerImg }}" alt="AIRID">
    </div>
    <div class="cl-header-right">
        <div class="org-name">AIRID — African Institute for Research in Infectious Diseases</div>
        <div class="org-info">
            IFU: 6202213991612 &nbsp;|&nbsp;
            LOT 5507, Donaten Cotonou, Benin<br>
            Tél: +229 0167128862 &nbsp;|&nbsp;
            Email: admin@airid-africa.com &nbsp;|&nbsp;
            www.airid-africa.com
        </div>
    </div>
    <div class="cl-doc-ref">
        <strong>{{ $docRef }}</strong><br>
        Issue date: {{ $issueDate }}<br>
        Next review: {{ $nextReview }}
    </div>
</div>

{{-- ── Document title ── --}}
<div class="cl-title">Quality Assurance Unit Checklist for GLP Studies</div>

{{-- ── Project info ── --}}
<div class="cl-proj-banner">
    <div class="d-flex flex-wrap gap-3">
        <div>
            <span class="label">Project Code:</span>
            <strong>{{ $project->project_code }}</strong>
        </div>
        <div>
            <span class="label">Start Date:</span>
            {{ $project->date_debut_effective ? \Carbon\Carbon::parse($project->date_debut_effective)->format('d/m/Y') : '—' }}
        </div>
    </div>
    <div class="mt-1">
        <span class="label">Study Director:</span>
        {{ $sdName ?: '—' }}
    </div>
    <div class="mt-1">
        <span class="label">Project Title:</span>
        {{ $project->project_title }}
    </div>
</div>

{{-- ── Action bar ── --}}
<div class="cl-action-bar">
    <div>
        <div class="cl-title-bar"><i class="bi bi-card-checklist me-2"></i>QA Activities Checklist</div>
        <div class="cl-subtitle">Fields marked <span style="background:#e8f3ff;color:#1a3a6b;font-size:.68rem;font-weight:600;padding:1px 6px;border-radius:20px;">auto</span> are pre-filled from existing project data.</div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        @if($isQaMgr)
        <button id="btnSaveChecklist" class="btn btn-sm fw-semibold"
                style="background:#fff;color:var(--cl-purple-dark);border:none;font-size:.82rem;">
            <i class="bi bi-save me-1"></i>Save
        </button>
        @endif
        <a href="{{ $printUrl }}" target="_blank"
           class="btn btn-sm fw-semibold"
           style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.4);font-size:.82rem;">
            <i class="bi bi-printer me-1"></i>Print / PDF
        </a>
    </div>
</div>

@if(!$isQaMgr)
<div class="alert alert-warning d-flex align-items-center gap-2 py-2 mb-3" style="font-size:.84rem;">
    <i class="bi bi-lock flex-shrink-0"></i>
    <span>Read-only — only QA Manager, Facility Manager and Super Admin can edit this checklist.</span>
</div>
@endif

{{-- ── Checklist table ── --}}
<div class="table-responsive">
    <table class="cl-table">
        <thead>
            <tr>
                <th class="cl-num">N°</th>
                <th class="cl-activity">Activity</th>
                <th class="cl-date-col">Date Performed</th>
                <th class="cl-mov-col">Means of Verification</th>
                <th class="cl-check-col">Check*</th>
            </tr>
        </thead>
        <tbody id="clBody">
        @foreach($activities as $num => $label)
        @php
            $row     = $saved[$num] ?? null;
            $dateVal = $row?->date_performed?->format('Y-m-d') ?? '';
            $movVal  = $row?->means_of_verification ?? '';
            $checked = $row?->is_checked ?? false;
            $hasPrefill = !$row && isset($prefill[$num]);
        @endphp
        <tr data-item="{{ $num }}">
            <td class="cl-num">{{ $num }}.</td>
            <td class="cl-activity">{{ $label }}</td>
            <td class="cl-date-col">
                <div class="d-flex align-items-center gap-1">
                    <input type="date"
                           class="form-control form-control-sm cl-date"
                           data-item="{{ $num }}"
                           value="{{ $dateVal }}"
                           {{ !$isQaMgr ? 'disabled' : '' }}>
                    @if($hasPrefill)
                        <span class="auto-badge" title="Pre-filled from project data">auto</span>
                    @endif
                </div>
            </td>
            <td class="cl-mov-col">
                <input type="text"
                       class="form-control form-control-sm cl-mov"
                       data-item="{{ $num }}"
                       value="{{ $movVal }}"
                       placeholder="Means of verification…"
                       {{ !$isQaMgr ? 'disabled' : '' }}>
            </td>
            <td class="cl-check-col">
                <div class="form-check d-flex justify-content-center mb-0">
                    <input class="form-check-input cl-check" type="checkbox"
                           data-item="{{ $num }}"
                           style="width:1.25rem;height:1.25rem;"
                           {{ $checked ? 'checked' : '' }}
                           {{ !$isQaMgr ? 'disabled' : '' }}>
                </div>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>

<div class="cl-footer-note">
    <span>* Sign after checking means of verification</span>
    <strong>SANAS OECD GLP COMPLIANT FACILITY N° G0028</strong>
    <span>{{ $project->project_code }}</span>
</div>

<script>
(function () {
    const CSRF       = document.querySelector('meta[name="csrf-token"]').content;
    const PROJECT_ID = {{ $project->id }};

    // Apply prefill dates to empty date inputs
    const PREFILL = @json(collect($prefill)->map(fn($v) => is_array($v) ? $v['date'] : $v));
    Object.entries(PREFILL).forEach(([num, date]) => {
        const row = document.querySelector(`tr[data-item="${num}"]`);
        if (!row) return;
        const dateInput = row.querySelector('.cl-date');
        if (dateInput && !dateInput.value) {
            dateInput.value = date instanceof Object ? date : String(date).substring(0, 10);
        }
    });

    // Apply prefill MOVs to empty MOV inputs
    const PREFILL_MOV = @json(collect($prefill)->map(fn($v) => is_array($v) ? ($v['mov'] ?? '') : ''));
    Object.entries(PREFILL_MOV).forEach(([num, mov]) => {
        const row = document.querySelector(`tr[data-item="${num}"]`);
        if (!row) return;
        const movInput = row.querySelector('.cl-mov');
        if (movInput && !movInput.value && mov) {
            movInput.value = mov;
        }
    });

    // Save
    document.getElementById('btnSaveChecklist')?.addEventListener('click', function () {
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving…';

        const items = [];
        document.querySelectorAll('#clBody tr[data-item]').forEach(row => {
            const num = parseInt(row.dataset.item);
            items.push({
                item_number:           num,
                date_performed:        row.querySelector('.cl-date')?.value  || null,
                means_of_verification: row.querySelector('.cl-mov')?.value   || null,
                is_checked:            row.querySelector('.cl-check')?.checked ? 1 : 0,
            });
        });

        fetch('/ajax/save-qa-activities-checklist', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ project_id: PROJECT_ID, items }),
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-save me-1"></i>Save';
            if (data.success) {
                // Show toast if the function exists (from parent layout), otherwise alert
                if (typeof showQaToast === 'function') {
                    showQaToast('Checklist saved successfully.', 'success');
                } else {
                    // Fallback: inline success message
                    const msg = document.createElement('div');
                    msg.className = 'alert alert-success alert-dismissible position-fixed top-0 end-0 m-3';
                    msg.style.zIndex = 9999;
                    msg.innerHTML = '<i class="bi bi-check2-circle me-1"></i>Checklist saved successfully.<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                    document.body.appendChild(msg);
                    setTimeout(() => msg.remove(), 3500);
                }
            } else {
                alert(data.message || 'Error saving checklist.');
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-save me-1"></i>Save';
            alert('Network error — please try again.');
        });
    });
})();
</script>
@endsection

@extends('index-new')
@section('title', 'QA Review — Fill In')

@section('content')
@php
    $isCompleted = $review->status === 'completed';
    $logoImg     = asset('storage/assets/logo/airid.jpg');

    // Build tab definitions: code => [label, sections]
    $tabs = [
        'staff'    => ['label' => 'I. QA Staff & Trainings',   'sections' => ['I']],
        'manuals'  => ['label' => 'II. Manuals & SOPs',         'sections' => ['II']],
        'facility' => ['label' => 'III-A. Facility Inspections','sections' => ['III_A']],
        'study'    => ['label' => 'III-B. Study Inspections',   'sections' => ['III_B']],
        'reports'  => ['label' => 'III-C. Study Reports',       'sections' => ['III_C']],
        'progress' => ['label' => 'IV. QA Progress',            'sections' => ['IV']],
        'others'   => ['label' => 'V. Others',                  'sections' => []],
        'meeting'  => ['label' => 'Meeting Minutes',            'sections' => []],
    ];
@endphp

<style>
:root { --fm-blue:#c20102; --fm-blue2:#8b0001; }

.qa-rev-doc-header {
    display:flex; align-items:center; gap:16px;
    border-bottom:2px solid var(--fm-blue); padding-bottom:12px; margin-bottom:18px;
}
.qa-rev-doc-header img { max-height:80px; max-width:80px; object-fit:contain; }
.qa-rev-org-name { font-size:1rem; font-weight:700; color:var(--fm-blue); }
.qa-rev-org-info  { font-size:.78rem; color:#444; line-height:1.7; margin-top:2px; }
.qa-rev-doc-ref   { font-size:.74rem; text-align:right; white-space:nowrap; color:#444; line-height:1.7; }
.qa-rev-doc-ref strong { font-size:.82rem; color:var(--fm-blue); }

.qa-rev-title {
    text-align:center; font-size:1.1rem; font-weight:700;
    text-transform:uppercase; text-decoration:underline;
    letter-spacing:.05em; color:var(--fm-blue); margin:16px 0;
}

/* Tabs */
.rev-tabs { border-bottom: 2px solid var(--fm-blue); margin-bottom:0; flex-wrap:wrap; }
.rev-tabs .nav-link {
    color:var(--fm-blue); font-size:.82rem; font-weight:600;
    padding:.45rem .9rem; border:1px solid transparent; border-bottom:none;
    border-radius:6px 6px 0 0; margin-bottom:-2px;
}
.rev-tabs .nav-link:hover { background:#edf2ff; }
.rev-tabs .nav-link.active {
    background:var(--fm-blue); color:#fff;
    border-color:var(--fm-blue) var(--fm-blue) #fff;
}
.rev-tab-pane { border:1px solid #dee2e6; border-top:none; border-radius:0 0 10px 10px; padding:20px; background:#fff; }

/* Section headers inside tabs */
.sec-header {
    background:linear-gradient(90deg,var(--fm-blue),var(--fm-blue2));
    color:#fff; font-weight:700; font-size:.85rem;
    padding:7px 14px; border-radius:6px; margin-bottom:8px;
}
.subsec-header {
    background:#e8edf5; color:var(--fm-blue); font-weight:600;
    font-size:.82rem; padding:5px 14px; border-left:3px solid var(--fm-blue2);
    border-radius:3px; margin-bottom:8px;
}

/* Table */
.qa-rev-table { width:100%; border-collapse:collapse; font-size:.86rem; }
.qa-rev-table th {
    background:#dce5f5; color:var(--fm-blue); font-weight:700;
    padding:7px 10px; border:1px solid #bdd0ef; font-size:.82rem;
}
.qa-rev-table td { border:1px solid #ddd; padding:8px 10px; vertical-align:top; }
.qa-rev-table tbody tr:nth-child(even) { background:#f7f9ff; }
.qa-rev-table tbody tr:hover { background:#edf1fb; }
.q-text { font-weight:500; }
.yes-no-group { display:flex; gap:8px; }
.yes-no-group label { display:flex; align-items:center; gap:4px; cursor:pointer; font-size:.82rem; font-weight:500; }
.ca-done-row { background:#f0fff4 !important; }
.no-row-bg { background:#fff8f8 !important; }

/* Custom items */
.custom-item-row td { background:#fffbf0 !important; }

/* Save bar */
.tab-save-bar {
    display:flex; align-items:center; justify-content:flex-end; gap:10px;
    padding:10px 14px; background:#f8f9fa; border-top:1px solid #eee;
    border-radius:0 0 10px 10px; margin:-20px -20px -20px;
    margin-top: 16px;
}

/* Status pill */
.status-pill { font-size:.72rem; padding:3px 10px; border-radius:20px; font-weight:600; }

/* Sign line */
.sign-line { border-bottom:1px solid #aaa; min-height:30px; }
</style>

{{-- Back --}}
<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('fm.qa-review.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Reviews
    </a>
    <span class="text-muted small">/ QA Review /
        {{ $review->review_date?->format('d/m/Y') ?? $review->scheduled_date?->format('d/m/Y') ?? '—' }}
    </span>
    <span class="status-pill ms-1" style="background:{{ $review->statusColor() }}22;color:{{ $review->statusColor() }};">
        {{ $review->statusLabel() }}
    </span>
</div>

{{-- AIRID Header --}}
<div class="qa-rev-doc-header">
    <img src="{{ $logoImg }}" alt="AIRID">
    <div style="flex:1;">
        <div class="qa-rev-org-name">AIRID — African Institute for Research in Infectious Diseases</div>
        <div class="qa-rev-org-info">
            IFU: 6202213991612 &nbsp;|&nbsp; LOT 5507, Donaten Cotonou, Benin<br>
            Tél: +229 0167128862 &nbsp;|&nbsp; Email: admin@airid-africa.com &nbsp;|&nbsp; www.airid-africa.com
        </div>
    </div>
    <div class="qa-rev-doc-ref">
        <strong>QA-PR-1-016/04</strong><br>
        Issue date: 01/08/2025<br>
        Next review date: 31/07/2027
    </div>
</div>

<div class="qa-rev-title">Quality Assurance Review Checklist</div>

{{-- Review header card --}}
<div class="card border-0 shadow-sm mb-3" style="border-radius:10px;">
    <div class="card-body py-3 px-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold small">Review Date</label>
                <input type="date" id="review_date" class="form-control form-control-sm"
                       value="{{ $review->review_date?->format('Y-m-d') }}"
                       {{ $isCompleted ? 'disabled' : '' }}>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Name of Reviewer</label>
                <input type="text" id="reviewer_name" class="form-control form-control-sm personnel-ac"
                       value="{{ $review->reviewer_name }}"
                       placeholder="Full name…" autocomplete="off"
                       {{ $isCompleted ? 'disabled' : '' }}>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small">Date Signed</label>
                <input type="date" id="date_signed" class="form-control form-control-sm"
                       value="{{ $review->date_signed?->format('Y-m-d') }}"
                       {{ $isCompleted ? 'disabled' : '' }}>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold small">Status</label>
                <select id="review_status" class="form-select form-select-sm"
                        {{ $isCompleted ? 'disabled' : '' }}>
                    <option value="scheduled"   {{ $review->status === 'scheduled'   ? 'selected' : '' }}>Scheduled</option>
                    <option value="in_progress" {{ $review->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed"   {{ $review->status === 'completed'   ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
        </div>
        @if(!$isCompleted)
        <div class="d-flex gap-2 mt-3">
            <button id="btnSaveHeader" class="btn btn-sm fw-semibold"
                    style="background:var(--fm-blue);color:#fff;border:none;font-size:.8rem;">
                <i class="bi bi-save me-1"></i>Save Header
            </button>
            <button id="btnCompleteReview" class="btn btn-sm btn-success fw-semibold"
                    style="font-size:.8rem;">
                <i class="bi bi-check2-all me-1"></i>Mark Completed
            </button>
            <a href="{{ route('fm.qa-review.print', $review->id) }}" target="_blank"
               class="btn btn-sm btn-outline-secondary fw-semibold" style="font-size:.8rem;">
                <i class="bi bi-printer me-1"></i>Print / PDF
            </a>
        </div>
        @else
        <div class="mt-2">
            <a href="{{ route('fm.qa-review.print', $review->id) }}" target="_blank"
               class="btn btn-sm btn-outline-secondary fw-semibold" style="font-size:.8rem;">
                <i class="bi bi-printer me-1"></i>Print / PDF
            </a>
            <span class="ms-3 text-success small fw-semibold">
                <i class="bi bi-lock me-1"></i>Completed — read-only
            </span>
        </div>
        @endif
    </div>
</div>

{{-- ── Documents to verify ── --}}
<details class="mb-3">
    <summary style="cursor:pointer;font-size:.85rem;font-weight:600;color:var(--fm-blue);padding:8px 0;">
        <i class="bi bi-file-earmark-text me-1"></i>Documents to verify as part of this inspection
    </summary>
    <div style="background:#f0f4ff;border-radius:8px;padding:12px 18px;margin-top:6px;font-size:.84rem;">
        <ol class="mb-0">
            @foreach(\App\Models\QaReviewInspection::documentsToVerify() as $cat => $docs)
            <li><strong>{{ $cat }}</strong>
                <ul style="list-style:disc;margin:2px 0 6px 16px;">
                    @foreach($docs as $doc)<li>{{ $doc }}</li>@endforeach
                </ul>
            </li>
            @endforeach
        </ol>
        <div class="fw-semibold mt-2" style="font-size:.82rem;"><i class="bi bi-info-circle me-1"></i>NB: Interview QA Staff as appropriate</div>
    </div>
</details>

{{-- ══════════════════════════════
     TABS
══════════════════════════════ --}}
<ul class="nav rev-tabs" id="revTabs" role="tablist">
    @foreach($tabs as $tabId => $tabDef)
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                id="tab-{{ $tabId }}-btn"
                data-bs-toggle="tab"
                data-bs-target="#tab-{{ $tabId }}"
                type="button" role="tab">
            {{ $tabDef['label'] }}
        </button>
    </li>
    @endforeach
</ul>

<div class="tab-content" id="revTabContent">

{{-- ── Tabs I → IV: checklist sections ── --}}
@foreach($tabs as $tabId => $tabDef)
@if(!in_array($tabId, ['others','meeting']))
<div class="tab-pane fade rev-tab-pane {{ $loop->first ? 'show active' : '' }}"
     id="tab-{{ $tabId }}" role="tabpanel">

    <div class="table-responsive">
    <table class="qa-rev-table" data-tab="{{ $tabId }}">
        <thead>
            <tr>
                <th style="width:44%;">Areas reviewed</th>
                <th style="width:90px;text-align:center;">Yes or No</th>
                <th style="width:27%;">Comments</th>
                <th>Corrective Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($tabDef['sections'] as $code)
        @php $section = $sections[$code]; @endphp

        {{-- subsection label for III groups --}}
        @if(str_starts_with($code, 'III_'))
        <tr>
            <td colspan="4" class="subsec-header" style="border:none;">
                @if($code === 'III_A') A- Facility &amp; Process Inspections
                @elseif($code === 'III_B') B- Study-based Inspections
                @elseif($code === 'III_C') C- QA and Study Reports
                @endif
            </td>
        </tr>
        @endif

        @foreach($section['items'] as $num => $question)
        @php
            $key  = $code . '.' . $num;
            $resp = $responses[$key] ?? null;
        @endphp
        <tr class="{{ $resp?->ca_completed ? 'ca-done-row' : '' }}"
            data-section="{{ $code }}" data-item="{{ $num }}">
            <td class="q-text">{{ $num }}. {{ $question }}</td>
            <td style="text-align:center;vertical-align:middle;">
                <div class="yes-no-group justify-content-center">
                    <label>
                        <input type="radio" name="yn_{{ $code }}_{{ $num }}"
                               value="yes" class="yn-radio"
                               data-section="{{ $code }}" data-item="{{ $num }}"
                               {{ $resp?->yes_no === 'yes' ? 'checked' : '' }}
                               {{ $isCompleted ? 'disabled' : '' }}>
                        <span class="text-success fw-semibold">Yes</span>
                    </label>
                    <label>
                        <input type="radio" name="yn_{{ $code }}_{{ $num }}"
                               value="no" class="yn-radio"
                               data-section="{{ $code }}" data-item="{{ $num }}"
                               {{ $resp?->yes_no === 'no' ? 'checked' : '' }}
                               {{ $isCompleted ? 'disabled' : '' }}>
                        <span class="text-danger fw-semibold">No</span>
                    </label>
                </div>
            </td>
            <td>
                <textarea class="form-control form-control-sm resp-comments"
                          rows="2" style="font-size:.8rem;resize:vertical;"
                          data-section="{{ $code }}" data-item="{{ $num }}"
                          placeholder="Comments…"
                          {{ $isCompleted ? 'disabled' : '' }}>{{ $resp?->comments }}</textarea>
            </td>
            <td>
                <textarea class="form-control form-control-sm resp-ca"
                          rows="2" style="font-size:.8rem;resize:vertical;"
                          data-section="{{ $code }}" data-item="{{ $num }}"
                          placeholder="Corrective actions…"
                          {{ $isCompleted ? 'disabled' : '' }}>{{ $resp?->corrective_actions }}</textarea>
                @if(!$isCompleted || $resp?->ca_completed)
                <div class="form-check mt-1 d-flex align-items-center gap-2">
                    <input class="form-check-input ca-completed-check" type="checkbox"
                           id="ca_{{ $code }}_{{ $num }}"
                           data-section="{{ $code }}" data-item="{{ $num }}"
                           style="width:1rem;height:1rem;"
                           {{ $resp?->ca_completed ? 'checked' : '' }}
                           {{ $isCompleted ? 'disabled' : '' }}>
                    <label for="ca_{{ $code }}_{{ $num }}"
                           style="font-size:.72rem;color:#198754;font-weight:600;">CA Resolved</label>
                    @if(!$isCompleted)
                    <input type="date" class="form-control form-control-sm ca-date-input ms-auto"
                           data-section="{{ $code }}" data-item="{{ $num }}"
                           style="font-size:.72rem;width:130px;"
                           value="{{ $resp?->ca_date?->format('Y-m-d') }}">
                    @elseif($resp?->ca_date)
                    <span class="ms-auto" style="font-size:.72rem;color:#198754;">{{ $resp->ca_date->format('d/m/Y') }}</span>
                    @endif
                </div>
                @endif
            </td>
        </tr>
        @endforeach
        @endforeach
        </tbody>
    </table>
    </div>

    @if(!$isCompleted)
    <div class="tab-save-bar">
        <span class="text-muted small me-auto" id="saveMsg_{{ $tabId }}"></span>
        <button type="button" class="btn btn-sm fw-semibold btn-save-tab"
                data-tab="{{ $tabId }}"
                style="background:var(--fm-blue);color:#fff;border:none;font-size:.8rem;">
            <i class="bi bi-save me-1"></i>Save this section
        </button>
    </div>
    @endif
</div>
@endif
@endforeach

{{-- ── Tab V: Others (custom questions) ── --}}
<div class="tab-pane fade rev-tab-pane" id="tab-others" role="tabpanel">
    <table class="qa-rev-table" id="customItemsTable">
        <thead>
            <tr>
                <th style="width:44%;">Question</th>
                <th style="width:90px;text-align:center;">Yes or No</th>
                <th style="width:27%;">Comments</th>
                <th>Corrective Actions</th>
            </tr>
        </thead>
        <tbody id="customItemsBody">
        @foreach($review->customItems as $ci)
        <tr class="custom-item-row" data-custom-id="{{ $ci->id }}">
            <td>
                @if(!$isCompleted)
                <textarea class="form-control form-control-sm ci-question" rows="2"
                          style="font-size:.8rem;resize:vertical;">{{ $ci->question }}</textarea>
                @else
                <span style="font-size:.85rem;">{{ $ci->question }}</span>
                @endif
            </td>
            <td style="text-align:center;vertical-align:middle;">
                <div class="yes-no-group justify-content-center">
                    <label><input type="radio" name="ci_yn_{{ $ci->id }}" value="yes" class="ci-yn-radio"
                                  {{ $ci->yes_no === 'yes' ? 'checked' : '' }}
                                  {{ $isCompleted ? 'disabled' : '' }}>
                        <span class="text-success fw-semibold">Yes</span></label>
                    <label><input type="radio" name="ci_yn_{{ $ci->id }}" value="no" class="ci-yn-radio"
                                  {{ $ci->yes_no === 'no' ? 'checked' : '' }}
                                  {{ $isCompleted ? 'disabled' : '' }}>
                        <span class="text-danger fw-semibold">No</span></label>
                </div>
            </td>
            <td>
                <textarea class="form-control form-control-sm ci-comments" rows="2"
                          style="font-size:.8rem;resize:vertical;"
                          {{ $isCompleted ? 'disabled' : '' }}>{{ $ci->comments }}</textarea>
            </td>
            <td>
                <textarea class="form-control form-control-sm ci-ca" rows="2"
                          style="font-size:.8rem;resize:vertical;"
                          {{ $isCompleted ? 'disabled' : '' }}>{{ $ci->corrective_actions }}</textarea>
                @if(!$isCompleted)
                <div class="d-flex align-items-center gap-2 mt-1">
                    <input class="form-check-input ci-ca-completed" type="checkbox"
                           style="width:1rem;height:1rem;"
                           {{ $ci->ca_completed ? 'checked' : '' }}>
                    <label style="font-size:.72rem;color:#198754;font-weight:600;">CA Resolved</label>
                    <button type="button" class="btn btn-sm btn-outline-danger ms-auto remove-ci-btn"
                            style="font-size:.72rem;padding:1px 8px;"><i class="bi bi-trash"></i></button>
                </div>
                @endif
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>

    @if(!$isCompleted)
    <div class="tab-save-bar">
        <button type="button" id="addCustomItemBtn"
                class="btn btn-sm btn-outline-primary me-auto" style="font-size:.8rem;">
            <i class="bi bi-plus-circle me-1"></i>Add Question
        </button>
        <span class="text-muted small" id="saveMsg_others"></span>
        <button type="button" class="btn btn-sm fw-semibold btn-save-tab"
                data-tab="others"
                style="background:var(--fm-blue);color:#fff;border:none;font-size:.8rem;">
            <i class="bi bi-save me-1"></i>Save this section
        </button>
    </div>
    @endif
</div>

{{-- ── Tab Meeting Minutes ── --}}
<div class="tab-pane fade rev-tab-pane" id="tab-meeting" role="tabpanel">

    {{-- Reviewer signature --}}
    <div class="row g-3 mb-4 pb-3 border-bottom">
        <div class="col-md-5">
            <label class="form-label fw-semibold small">Name of Reviewer</label>
            <input type="text" id="sign_reviewer" class="form-control form-control-sm personnel-ac"
                   value="{{ $review->reviewer_name }}"
                   placeholder="Full name of reviewer…" autocomplete="off"
                   {{ $isCompleted ? 'disabled' : '' }}>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold small">Date Signed</label>
            <input type="date" id="sign_date" class="form-control form-control-sm"
                   value="{{ $review->date_signed?->format('Y-m-d') }}"
                   {{ $isCompleted ? 'disabled' : '' }}>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-semibold small">Signature</label>
            <div class="sign-line"></div>
        </div>
        <div class="col-12">
            <p class="text-muted mb-0" style="font-size:.78rem;font-style:italic;">
                NB: Attached to this checklist is the meeting minutes between the Facility Manager &amp; the QA personnel.
            </p>
        </div>
    </div>

    {{-- Meeting minutes --}}
    <div class="fw-bold mb-3" style="color:var(--fm-blue);font-size:.95rem;">
        <i class="bi bi-people me-2"></i>Minutes of Meeting — Facility Manager &amp; QA Personnel
    </div>
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label fw-semibold small">Date of Meeting</label>
            <input type="date" id="meeting_date" class="form-control form-control-sm"
                   value="{{ $review->meeting_date?->format('Y-m-d') }}"
                   {{ $isCompleted ? 'disabled' : '' }}>
        </div>
        <div class="col-md-8">
            <label class="form-label fw-semibold small">Participants</label>
            <input type="text" id="meeting_participants" class="form-control form-control-sm personnel-ac-multi"
                   value="{{ $review->meeting_participants }}"
                   placeholder="e.g. Dr Corine Ngufor, …" autocomplete="off"
                   {{ $isCompleted ? 'disabled' : '' }}>
            <div class="form-text" style="font-size:.72rem;">Separate multiple names with commas. Type to search from personnel list.</div>
        </div>
        <div class="col-12">
            <label class="form-label fw-semibold small">Meeting Notes</label>
            <textarea id="meeting_notes" class="form-control" rows="6"
                      placeholder="Notes from the meeting…"
                      {{ $isCompleted ? 'disabled' : '' }}>{{ $review->meeting_notes }}</textarea>
        </div>
    </div>

    @if(!$isCompleted)
    <div class="mt-3 pt-3 border-top d-flex gap-3" style="font-size:.82rem;color:#666;">
        <div class="text-center">
            <div style="border-bottom:1px solid #aaa;min-width:140px;min-height:28px;"></div>
            <div class="mt-1">QA Manager</div>
        </div>
        <div class="text-center ms-5">
            <div style="border-bottom:1px solid #aaa;min-width:160px;min-height:28px;"></div>
            <div class="mt-1">Facility Manager</div>
        </div>
    </div>
    <div class="tab-save-bar">
        <span class="text-muted small me-auto" id="saveMsg_meeting"></span>
        <button type="button" class="btn btn-sm fw-semibold btn-save-tab"
                data-tab="meeting"
                style="background:var(--fm-blue);color:#fff;border:none;font-size:.8rem;">
            <i class="bi bi-save me-1"></i>Save Meeting Minutes
        </button>
    </div>
    @endif
</div>

</div>{{-- end tab-content --}}

<div class="mb-5"></div>

{{-- Toast --}}
<div id="revToast" class="position-fixed bottom-0 end-0 m-3" style="z-index:9999;min-width:260px;">
    <div class="toast align-items-center border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body fw-semibold" id="revToastMsg"></div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<script>
(function () {
    const CSRF      = document.querySelector('meta[name="csrf-token"]').content;
    const REVIEW_ID = {{ $review->id }};

    // ── Toast ──────────────────────────────────────────────────────
    function showToast(msg, type) {
        const toastEl = document.getElementById('revToast');
        const t = toastEl.querySelector('.toast');
        t.className = 'toast align-items-center border-0 text-white ' +
            (type === 'success' ? 'bg-success' : 'bg-danger');
        document.getElementById('revToastMsg').textContent = msg;
        bootstrap.Toast.getOrCreateInstance(t, { delay: 3000 }).show();
    }

    // ── Collect responses for specific tab sections ────────────────
    function collectTabResponses(tabId) {
        const responses = [];
        const tables = document.querySelectorAll(`.qa-rev-table[data-tab="${tabId}"]`);
        tables.forEach(table => {
            table.querySelectorAll('tbody tr[data-section][data-item]').forEach(row => {
                const sec  = row.dataset.section;
                const item = parseInt(row.dataset.item);
                const yn   = row.querySelector('.yn-radio:checked');
                const com  = row.querySelector('.resp-comments');
                const ca   = row.querySelector('.resp-ca');
                const caDone = row.querySelector('.ca-completed-check');
                const caDate = row.querySelector('.ca-date-input');
                responses.push({
                    section_code:        sec,
                    item_number:         item,
                    yes_no:              yn ? yn.value : null,
                    comments:            com ? com.value : null,
                    corrective_actions:  ca  ? ca.value  : null,
                    ca_completed:        caDone ? (caDone.checked ? 1 : 0) : 0,
                    ca_date:             caDate ? (caDate.value || null) : null,
                });
            });
        });
        return responses;
    }

    // ── Collect custom items (Others tab) ─────────────────────────
    function collectCustomItems() {
        const items = [];
        document.querySelectorAll('#customItemsBody tr.custom-item-row').forEach(row => {
            const q    = row.querySelector('.ci-question');
            const yn   = row.querySelector('.ci-yn-radio:checked');
            const com  = row.querySelector('.ci-comments');
            const ca   = row.querySelector('.ci-ca');
            const done = row.querySelector('.ci-ca-completed');
            items.push({
                id:                  row.dataset.customId ? parseInt(row.dataset.customId) : null,
                question:            q ? q.value : '',
                yes_no:              yn ? yn.value : null,
                comments:            com ? com.value : null,
                corrective_actions:  ca  ? ca.value  : null,
                ca_completed:        done ? (done.checked ? 1 : 0) : 0,
            });
        });
        return items;
    }

    // ── Collect header fields ─────────────────────────────────────
    function collectHeader() {
        const rName = document.getElementById('reviewer_name')?.value
                   || document.getElementById('sign_reviewer')?.value;
        const rDate = document.getElementById('date_signed')?.value
                   || document.getElementById('sign_date')?.value;
        return {
            review_date:   document.getElementById('review_date')?.value || null,
            reviewer_name: rName || null,
            date_signed:   rDate || null,
            status:        document.getElementById('review_status')?.value || null,
        };
    }

    // ── Generic save ──────────────────────────────────────────────
    function doSave(payload, btn, msgEl) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving…';

        fetch(`/fm/qa-review/${REVIEW_ID}/save`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify(payload),
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-save me-1"></i>Save this section';
            if (data.success) {
                showToast('Section saved.', 'success');
                if (msgEl) { msgEl.textContent = 'Saved ✓'; setTimeout(() => msgEl.textContent = '', 3000); }
            } else {
                showToast(data.message || 'Error.', 'error');
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-save me-1"></i>Save this section';
            showToast('Network error.', 'error');
        });
    }

    // ── Per-tab Save buttons ──────────────────────────────────────
    document.querySelectorAll('.btn-save-tab').forEach(btn => {
        btn.addEventListener('click', function () {
            const tabId = this.dataset.tab;
            const msgEl = document.getElementById('saveMsg_' + tabId);
            let payload = { ...collectHeader() };

            if (tabId === 'others') {
                payload.custom_items = collectCustomItems();
            } else if (tabId === 'meeting') {
                payload.meeting_date         = document.getElementById('meeting_date')?.value || null;
                payload.meeting_participants = document.getElementById('meeting_participants')?.value || null;
                payload.meeting_notes        = document.getElementById('meeting_notes')?.value || null;
                // sync sign fields
                payload.reviewer_name = document.getElementById('sign_reviewer')?.value || payload.reviewer_name;
                payload.date_signed   = document.getElementById('sign_date')?.value   || payload.date_signed;
            } else {
                payload.responses = collectTabResponses(tabId);
            }
            doSave(payload, this, msgEl);
        });
    });

    // ── Save Header button ────────────────────────────────────────
    document.getElementById('btnSaveHeader')?.addEventListener('click', function () {
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>';
        fetch(`/fm/qa-review/${REVIEW_ID}/save`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify(collectHeader()),
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-save me-1"></i>Save Header';
            showToast(data.success ? 'Header saved.' : (data.message || 'Error.'), data.success ? 'success' : 'error');
        })
        .catch(() => { btn.disabled = false; btn.innerHTML = '<i class="bi bi-save me-1"></i>Save Header'; showToast('Network error.','error'); });
    });

    // ── Mark Complete ─────────────────────────────────────────────
    document.getElementById('btnCompleteReview')?.addEventListener('click', function () {
        if (!confirm('Mark this review as completed? It will become read-only.')) return;
        const btn = this;
        btn.disabled = true;
        fetch(`/fm/qa-review/${REVIEW_ID}/complete`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        })
        .then(r => r.json())
        .then(() => location.reload());
    });

    // ── No → highlight row in light red ──────────────────────────
    document.querySelectorAll('.yn-radio').forEach(radio => {
        const applyColor = r => {
            const row = r.closest('tr');
            if (!row) return;
            const checked = row.querySelector('.yn-radio:checked');
            if (checked?.value === 'no') row.classList.add('no-row-bg');
            else row.classList.remove('no-row-bg');
        };
        radio.addEventListener('change', () => applyColor(radio));
        if (radio.checked) applyColor(radio);
    });

    // ── CA resolved → green row ───────────────────────────────────
    document.querySelectorAll('.ca-completed-check').forEach(cb => {
        cb.addEventListener('change', function () {
            const row = this.closest('tr');
            this.checked ? row.classList.add('ca-done-row') : row.classList.remove('ca-done-row');
        });
    });

    // ── Custom items: add / remove ────────────────────────────────
    let ciCounter = 0;
    document.getElementById('addCustomItemBtn')?.addEventListener('click', function () {
        ciCounter++;
        const tr = document.createElement('tr');
        tr.className = 'custom-item-row';
        tr.dataset.customId = '';
        tr.innerHTML = `
            <td><textarea class="form-control form-control-sm ci-question" rows="2"
                          style="font-size:.8rem;resize:vertical;" placeholder="Enter your question…"></textarea></td>
            <td style="text-align:center;vertical-align:middle;">
                <div class="yes-no-group justify-content-center">
                    <label><input type="radio" name="ci_yn_new_${ciCounter}" value="yes" class="ci-yn-radio">
                        <span class="text-success fw-semibold">Yes</span></label>
                    <label><input type="radio" name="ci_yn_new_${ciCounter}" value="no" class="ci-yn-radio">
                        <span class="text-danger fw-semibold">No</span></label>
                </div>
            </td>
            <td><textarea class="form-control form-control-sm ci-comments" rows="2"
                          style="font-size:.8rem;resize:vertical;" placeholder="Comments…"></textarea></td>
            <td>
                <textarea class="form-control form-control-sm ci-ca" rows="2"
                          style="font-size:.8rem;resize:vertical;" placeholder="Corrective actions…"></textarea>
                <div class="d-flex align-items-center gap-2 mt-1">
                    <input class="form-check-input ci-ca-completed" type="checkbox" style="width:1rem;height:1rem;">
                    <label style="font-size:.72rem;color:#198754;font-weight:600;">CA Resolved</label>
                    <button type="button" class="btn btn-sm btn-outline-danger ms-auto remove-ci-btn"
                            style="font-size:.72rem;padding:1px 8px;"><i class="bi bi-trash"></i></button>
                </div>
            </td>`;
        document.getElementById('customItemsBody').appendChild(tr);
        tr.querySelector('textarea').focus();
    });

    document.getElementById('customItemsBody')?.addEventListener('click', e => {
        const btn = e.target.closest('.remove-ci-btn');
        if (btn && confirm('Remove this question?')) btn.closest('tr').remove();
    });

    // ── Sync reviewer name + date between header card and meeting tab ──
    const sync = (from, to) => { from?.addEventListener('input', () => { if (to) to.value = from.value; }); };
    sync(document.getElementById('reviewer_name'), document.getElementById('sign_reviewer'));
    sync(document.getElementById('sign_reviewer'), document.getElementById('reviewer_name'));
    sync(document.getElementById('date_signed'),   document.getElementById('sign_date'));
    sync(document.getElementById('sign_date'),     document.getElementById('date_signed'));

    // ── Personnel autocomplete ────────────────────────────────────
    const PERSONNEL_NAMES = @json($personnelNames);

    function buildDropdown(input) {
        const list = document.createElement('ul');
        list.style.cssText = 'position:absolute;z-index:9999;background:#fff;border:1px solid #ccc;border-radius:6px;max-height:200px;overflow-y:auto;list-style:none;margin:0;padding:4px 0;min-width:240px;box-shadow:0 4px 12px rgba(0,0,0,.12);display:none;';
        input.parentNode.style.position = 'relative';
        input.parentNode.appendChild(list);
        return list;
    }

    function renderMatches(list, matches, onSelect) {
        list.innerHTML = '';
        if (!matches.length) { list.style.display = 'none'; return; }
        matches.forEach(name => {
            const li = document.createElement('li');
            li.textContent = name;
            li.style.cssText = 'padding:6px 12px;cursor:pointer;font-size:.83rem;';
            li.addEventListener('mousedown', e => { e.preventDefault(); onSelect(name); list.style.display = 'none'; });
            li.addEventListener('mouseenter', () => li.style.background = '#f0f4ff');
            li.addEventListener('mouseleave', () => li.style.background = '');
            list.appendChild(li);
        });
        list.style.display = 'block';
    }

    document.querySelectorAll('.personnel-ac').forEach(input => {
        if (input.disabled) return;
        const list = buildDropdown(input);
        const getMatches = q => PERSONNEL_NAMES.filter(n => n.toLowerCase().includes(q.toLowerCase())).slice(0, 10);
        input.addEventListener('input', () => renderMatches(list, getMatches(input.value), n => input.value = n));
        input.addEventListener('focus', () => renderMatches(list, getMatches(input.value), n => input.value = n));
        input.addEventListener('blur',  () => setTimeout(() => list.style.display = 'none', 150));
    });

    document.querySelectorAll('.personnel-ac-multi').forEach(input => {
        if (input.disabled) return;
        const list = buildDropdown(input);
        function getCurrentToken() {
            const val = input.value;
            return val.substring(val.lastIndexOf(',') + 1).trim();
        }
        function getMatches() {
            const q = getCurrentToken().toLowerCase();
            if (!q) return [];
            return PERSONNEL_NAMES.filter(n => n.toLowerCase().includes(q)).slice(0, 10);
        }
        function select(name) {
            const val = input.value;
            const lastComma = val.lastIndexOf(',');
            const before = lastComma >= 0 ? val.substring(0, lastComma + 1) + ' ' : '';
            input.value = before + name + ', ';
            input.focus();
        }
        input.addEventListener('input', () => renderMatches(list, getMatches(), select));
        input.addEventListener('focus', () => renderMatches(list, getMatches(), select));
        input.addEventListener('blur',  () => setTimeout(() => list.style.display = 'none', 150));
    });

})();
</script>
@endsection

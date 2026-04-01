<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Critical Phase Impact Assessment — {{ $project->project_code }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --qa-brand: #C10202;
            --qa-brand-dark: #8b0001;
        }
        body { background: #f4f5f7; font-family: 'Segoe UI', sans-serif; }

        .page-header {
            background: linear-gradient(90deg, var(--qa-brand), var(--qa-brand-dark));
            color: #fff;
            padding: 20px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,.15);
        }
        .page-header .btn-back {
            background: rgba(255,255,255,.2);
            color: #fff;
            border: 1px solid rgba(255,255,255,.4);
            border-radius: 8px;
            font-size: .85rem;
            transition: background .2s;
        }
        .page-header .btn-back:hover { background: rgba(255,255,255,.35); color: #fff; }

        .section-card {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 4px 14px rgba(0,0,0,.05);
            overflow: hidden;
            margin-bottom: 1rem;
            transition: box-shadow .2s;
        }
        .section-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,.09); }

        .section-card-header {
            background: #fff7f7;
            border-bottom: 2px solid #f0d0d0;
            padding: 14px 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 14px;
            user-select: none;
        }
        .section-card-header.filled { background: #f0faf4; border-bottom-color: #a8e6c0; }

        .letter-badge {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, var(--qa-brand), var(--qa-brand-dark));
            color: #fff;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; font-weight: 700;
            flex-shrink: 0;
        }
        .letter-badge.filled {
            background: linear-gradient(135deg, #198754, #0d5c38);
        }

        .section-title { font-size: .95rem; font-weight: 600; color: #1a1a1a; }
        .section-subtitle { font-size: .78rem; color: #888; }

        .chevron-icon { margin-left: auto; transition: transform .25s; }
        .chevron-icon.open { transform: rotate(180deg); }

        .section-body { padding: 0 20px 20px; }

        .item-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid #f5f5f5;
        }
        .item-row:last-child { border-bottom: none; }

        .item-num {
            font-size: .78rem;
            font-weight: 700;
            color: var(--qa-brand);
            min-width: 26px;
        }
        .item-text {
            flex: 1;
            font-size: .87rem;
            line-height: 1.45;
        }

        .score-input {
            width: 68px;
            border-radius: 6px;
            border: 1px solid #ddd;
            padding: 4px 8px;
            font-size: .87rem;
            text-align: center;
        }
        .score-input:focus { border-color: var(--qa-brand); outline: none; box-shadow: 0 0 0 3px rgba(193,2,2,.1); }

        .form-check-input:checked { background-color: var(--qa-brand); border-color: var(--qa-brand); }

        .meta-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e0e0e0;
            padding: 18px 22px;
            margin-bottom: 1.2rem;
        }

        .badge-filled  { background: #198754; color: #fff; border-radius: 999px; padding: .2rem .6rem; font-size: .72rem; font-weight: 600; }
        .badge-todo    { background: #fd7e14; color: #fff; border-radius: 999px; padding: .2rem .6rem; font-size: .72rem; font-weight: 600; }
        .badge-score   { background: #0d6efd; color: #fff; border-radius: 999px; padding: .2rem .6rem; font-size: .72rem; font-weight: 600; }

        .total-bar {
            background: linear-gradient(90deg, var(--qa-brand), var(--qa-brand-dark));
            color: #fff;
            border-radius: 12px;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 1rem;
            font-weight: 600;
            margin-top: 1.2rem;
        }

        .btn-save {
            background: linear-gradient(90deg, var(--qa-brand), var(--qa-brand-dark));
            color: #fff; border: none; border-radius: 8px;
            font-weight: 600; padding: .5rem 1.4rem;
            transition: opacity .2s;
        }
        .btn-save:hover { opacity: .88; color: #fff; }

        #toast-container {
            position: fixed; bottom: 24px; right: 24px; z-index: 9999;
        }
        .toast-msg {
            min-width: 260px;
            background: #1f2937; color: #fff;
            border-radius: 10px;
            padding: 12px 18px;
            font-size: .88rem;
            box-shadow: 0 4px 16px rgba(0,0,0,.25);
            animation: fadeIn .3s ease;
        }
        .toast-msg.success { border-left: 4px solid #22c55e; }
        .toast-msg.error   { border-left: 4px solid #ef4444; }
        @keyframes fadeIn { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:none; } }
    </style>
</head>
<body>

{{-- ── Header ── --}}
<div class="page-header">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="bi bi-clipboard2-pulse me-2"></i>Critical Phase Impact Assessment
            </h4>
            <small class="opacity-75">QA-PR-1-015/05 — SANAS OECD GLP COMPLIANT FACILITY N° G0028</small>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('cpia.print', $project->id) }}" class="btn btn-back" target="_blank">
                <i class="bi bi-printer me-1"></i>Print
            </a>
            <a href="/project/create?project_id={{ $project->id }}#step4" class="btn btn-back">
                <i class="bi bi-arrow-left me-1"></i>Back to Project
            </a>
        </div>
    </div>
</div>

<div class="container-fluid py-4 px-4" style="max-width:1100px;">

    {{-- Status banner when completed --}}
    @if ($assessment->isCompleted())
    <div class="alert d-flex align-items-center gap-3 mb-3"
         style="background:#d1fae5;border:1px solid #6ee7b7;border-radius:10px;">
        <i class="bi bi-check-circle-fill fs-4" style="color:#059669;"></i>
        <div>
            <strong style="color:#065f46;">Assessment Completed</strong>
            <div class="text-muted small">
                Marked complete on {{ $assessment->completed_at->format('d/m/Y H:i') }}.
                Signatories have been notified.
            </div>
        </div>
    </div>
    @endif

    {{-- Meta info --}}
    <div class="meta-card">
        <div class="row g-3 align-items-center">
            <div class="col-md-3">
                <div class="text-muted" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">Project</div>
                <div class="fw-semibold">{{ $project->project_code }} — {{ $project->project_title }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">Study Director</div>
                <div class="fw-semibold" id="sd-name-display">{{ $assessment->study_director_name ?: '—' }}</div>
            </div>
            <div class="col-md-2">
                <div class="text-muted" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">Sections filled</div>
                <div class="fw-semibold">
                    <span id="filled-count">{{ count($filledSectionIds) }}</span>
                    / {{ $sections->count() }}
                </div>
            </div>
            <div class="col-md-2">
                <div class="text-muted" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">Status</div>
                <div>
                    @if ($assessment->isCompleted())
                        <span class="badge" style="background:#198754;font-size:.8rem;">
                            <i class="bi bi-check2 me-1"></i>Completed
                        </span>
                    @else
                        <span class="badge" style="background:#fd7e14;font-size:.8rem;">
                            <i class="bi bi-pencil me-1"></i>Draft
                        </span>
                    @endif
                </div>
            </div>
            <div class="col-md-2 text-end">
                @if (!$assessment->isCompleted())
                <button class="btn btn-sm fw-semibold"
                        style="background:#198754;color:#fff;border:none;border-radius:8px;"
                        onclick="completeAssessment()"
                        id="btn-complete">
                    <i class="bi bi-check-circle me-1"></i>Mark as Completed
                </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Section cards --}}
    @foreach ($sections as $section)
    @php
        $isFilled = in_array($section->id, $filledSectionIds);
    @endphp
    <div class="section-card" id="sc-{{ $section->id }}">
        <div class="section-card-header {{ $isFilled ? 'filled' : '' }}"
             onclick="toggleSection({{ $section->id }})">
            <div class="letter-badge {{ $isFilled ? 'filled' : '' }}">{{ $section->letter }}</div>
            <div>
                <div class="section-title">{{ $section->title }}</div>
                <div class="section-subtitle">{{ $section->items->count() }} items</div>
            </div>
            <div class="ms-auto d-flex align-items-center gap-2">
                @if ($isFilled)
                    <span class="badge-filled"><i class="bi bi-check2 me-1"></i>Filled</span>
                @else
                    <span class="badge-todo">To fill</span>
                @endif
                <i class="bi bi-chevron-down chevron-icon" id="chevron-{{ $section->id }}"></i>
            </div>
        </div>

        <div class="section-body" id="body-{{ $section->id }}" style="display:none;">
            {{-- Column headers --}}
            <div class="d-flex align-items-center gap-2 py-2 mb-1"
                 style="border-bottom:2px solid #f0d0d0;font-size:.75rem;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:.04em;">
                <div style="min-width:26px;">#</div>
                <div style="flex:1;">Item</div>
                <div style="width:80px;text-align:center;">Score /10</div>
                <div style="width:80px;text-align:center;">Selected</div>
            </div>

            @foreach ($section->activeItems as $item)
            @php
                $resp = $responses[$item->id] ?? null;
            @endphp
            <div class="item-row">
                <div class="item-num">{{ $item->item_number }}.</div>
                <div class="item-text">{{ $item->text }}</div>
                <div style="width:80px;text-align:center;">
                    <input type="number"
                           class="score-input item-score"
                           data-item-id="{{ $item->id }}"
                           data-section-id="{{ $section->id }}"
                           min="0" max="10" step="1"
                           value="{{ $resp && $resp->impact_score !== null ? $resp->impact_score : '' }}"
                           placeholder="—"
                           onchange="markDirty({{ $section->id }})">
                </div>
                <div style="width:80px;text-align:center;">
                    <input type="checkbox"
                           class="form-check-input item-selected"
                           data-item-id="{{ $item->id }}"
                           data-section-id="{{ $section->id }}"
                           {{ $resp && $resp->is_selected ? 'checked' : '' }}
                           onchange="markDirty({{ $section->id }})">
                </div>
            </div>
            @endforeach

            <div class="d-flex justify-content-between align-items-center mt-3 pt-2"
                 style="border-top:1px solid #eee;">
                <div style="font-size:.82rem;color:#888;">
                    Section total: <strong id="sec-total-{{ $section->id }}">{{ $responses->filter(fn($r) => $r->section_id === $section->id && $r->impact_score !== null)->sum('impact_score') }}</strong> / {{ $section->activeItems->count() * 10 }}
                </div>
                <button class="btn btn-save btn-sm" onclick="saveSection({{ $section->id }})">
                    <i class="bi bi-floppy me-1"></i>Save section
                </button>
            </div>
        </div>
    </div>
    @endforeach

    {{-- Grand total (denominator = filled sections only) --}}
    @php
        $filledSectionMaxTotal = $sections
            ->filter(fn($s) => in_array($s->id, $filledSectionIds))
            ->sum(fn($s) => $s->activeItems->count() * 10);
    @endphp
    <div class="total-bar">
        <div>
            <div><i class="bi bi-calculator me-2"></i>Grand Total Impact Score</div>
            <div style="font-size:.75rem;opacity:.75;margin-top:2px;">Based on {{ count($filledSectionIds) }} filled section(s)</div>
        </div>
        <div id="grand-total"
             data-filled-max="{{ $filledSectionMaxTotal }}"
             style="font-size:1.3rem;">
            {{ $responses->whereNotNull('impact_score')->sum('impact_score') }}
            / {{ $filledSectionMaxTotal }}
        </div>
    </div>

</div>

<div id="toast-container"></div>

<script>
// ── Toggle sections ────────────────────────────────────────────
function toggleSection(secId) {
    const body    = document.getElementById('body-' + secId);
    const chevron = document.getElementById('chevron-' + secId);
    const isOpen  = body.style.display !== 'none';
    body.style.display = isOpen ? 'none' : 'block';
    chevron.classList.toggle('open', !isOpen);
}

// ── Dirty flag per section ─────────────────────────────────────
const dirty = {};
function markDirty(secId) {
    dirty[secId] = true;
    recalcSectionTotal(secId);
    recalcGrandTotal();
}

function recalcSectionTotal(secId) {
    let sum = 0;
    document.querySelectorAll(`.item-score[data-section-id="${secId}"]`).forEach(inp => {
        const v = parseInt(inp.value, 10);
        if (!isNaN(v)) sum += v;
    });
    const el = document.getElementById('sec-total-' + secId);
    if (el) el.textContent = sum;
}

// Section item counts for denominator recalculation
const sectionItemCounts = {
@foreach ($sections as $s)
    {{ $s->id }}: {{ $s->activeItems->count() }},
@endforeach
};

function recalcGrandTotal() {
    let grand = 0;
    let maxTotal = 0;
    const filledSectionIds = new Set();

    document.querySelectorAll('.item-score').forEach(inp => {
        const v = parseInt(inp.value, 10);
        if (!isNaN(v)) {
            grand += v;
            filledSectionIds.add(parseInt(inp.dataset.sectionId, 10));
        }
    });

    filledSectionIds.forEach(secId => {
        maxTotal += (sectionItemCounts[secId] || 0) * 10;
    });

    const gtEl = document.getElementById('grand-total');
    gtEl.textContent = grand + ' / ' + maxTotal;
    // Update subtitle
    const subtitle = gtEl.closest('.total-bar')?.querySelector('[style*="opacity"]');
    if (subtitle) subtitle.textContent = 'Based on ' + filledSectionIds.size + ' filled section(s)';
}

// ── Save section ───────────────────────────────────────────────
function saveSection(secId) {
    const responses = [];
    document.querySelectorAll(`.item-score[data-section-id="${secId}"]`).forEach(inp => {
        const itemId = inp.dataset.itemId;
        const scoreVal = inp.value !== '' ? parseInt(inp.value, 10) : null;
        const selEl  = document.querySelector(`.item-selected[data-item-id="${itemId}"]`);
        responses.push({
            item_id:      parseInt(itemId, 10),
            impact_score: scoreVal,
            is_selected:  selEl ? selEl.checked : false,
        });
    });

    fetch('/project/{{ $project->id }}/cpia/save', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ responses }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            dirty[secId] = false;
            toast('Section saved.', 'success');
            updateSectionBadge(secId, responses);
            updateFilledCount();
            recalcGrandTotal();
        } else {
            toast(data.message || 'Error saving.', 'error');
        }
    })
    .catch(() => toast('Network error.', 'error'));
}

function updateSectionBadge(secId, responses) {
    const hasFilled = responses.some(r => r.impact_score !== null && r.impact_score !== '');
    const header  = document.querySelector(`#sc-${secId} .section-card-header`);
    const badge   = document.querySelector(`#sc-${secId} .section-card-header .badge-filled, #sc-${secId} .section-card-header .badge-todo`);
    const letterBadge = document.querySelector(`#sc-${secId} .letter-badge`);

    if (hasFilled) {
        header.classList.add('filled');
        letterBadge.classList.add('filled');
        if (badge) { badge.className = 'badge-filled'; badge.innerHTML = '<i class="bi bi-check2 me-1"></i>Filled'; }
    } else {
        header.classList.remove('filled');
        letterBadge.classList.remove('filled');
        if (badge) { badge.className = 'badge-todo'; badge.textContent = 'To fill'; }
    }
}

function updateFilledCount() {
    const count = document.querySelectorAll('.section-card-header.filled').length;
    document.getElementById('filled-count').textContent = count;
}

// ── Toast notifications ────────────────────────────────────────
function toast(msg, type = 'success') {
    const container = document.getElementById('toast-container');
    const el = document.createElement('div');
    el.className = 'toast-msg ' + type;
    el.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>${msg}`;
    container.appendChild(el);
    setTimeout(() => el.remove(), 3500);
}

// ── Complete Assessment ────────────────────────────────────────
function completeAssessment() {
    const filledCount = parseInt(document.getElementById('filled-count').textContent, 10);
    if (filledCount === 0) {
        toast('Please fill at least one section first.', 'error');
        return;
    }
    if (!confirm('Mark this Critical Phase Impact Assessment as completed?\n\nSignatories (QA Manager, Study Director, Facility Manager) will be notified to sign.')) {
        return;
    }
    const btn = document.getElementById('btn-complete');
    if (btn) btn.disabled = true;

    fetch('/project/{{ $project->id }}/cpia/complete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({}),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            toast(data.message, 'success');
            setTimeout(() => location.reload(), 1800);
        } else {
            toast(data.message || 'Error.', 'error');
            if (btn) btn.disabled = false;
        }
    })
    .catch(() => {
        toast('Network error.', 'error');
        if (btn) btn.disabled = false;
    });
}

// Auto-open first unfilled section
document.addEventListener('DOMContentLoaded', function () {
    const todoHeader = document.querySelector('.section-card-header:not(.filled)');
    if (todoHeader) {
        const secId = todoHeader.closest('.section-card').id.replace('sc-', '');
        toggleSection(secId);
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

@extends('index-new')
@section('title', 'QA Review Inspections — Facility Manager')

@section('content')
<style>
.fm-header { background: linear-gradient(135deg, #c20102, #8b0001); border-radius: 1rem; padding: 1.4rem 2rem; color: #fff; }
.review-card { border-radius: 12px; border: 1px solid #dee2e6; transition: box-shadow .2s; }
.review-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,.1); }
.status-badge { font-size: .72rem; padding: 3px 10px; border-radius: 20px; font-weight: 600; }
</style>

{{-- Header --}}
<div class="fm-header d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <i class="bi bi-clipboard2-check fs-4"></i>
            <h4 class="mb-0 fw-bold">QA Review Inspections</h4>
        </div>
        <div style="font-size:.85rem;opacity:.8;">
            QA-PR-1-016/04 &mdash; Biennial review conducted by the Facility Manager
        </div>
    </div>
    <button class="btn fw-semibold" style="background:#fff;color:#c20102;border:none;"
            data-bs-toggle="modal" data-bs-target="#modalSchedule">
        <i class="bi bi-plus-circle me-1"></i>Schedule New Review
    </button>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible py-2 mb-3">
    <i class="bi bi-check2-circle me-1"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Reviews list --}}
@if($reviews->isEmpty())
<div class="text-center py-5 text-muted">
    <i class="bi bi-clipboard2-x d-block fs-1 mb-2 opacity-25"></i>
    No QA Reviews scheduled yet.
    <br><small>Use the "Schedule New Review" button to plan the first one.</small>
</div>
@else
<div class="row g-3">
@foreach($reviews as $review)
@php
    $color = $review->statusColor();
@endphp
<div class="col-12">
    <div class="review-card p-3 bg-white">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <div style="width:48px;height:48px;border-radius:10px;background:{{ $color }}22;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-clipboard2-check" style="font-size:1.3rem;color:{{ $color }};"></i>
                </div>
                <div>
                    <div class="fw-semibold" style="font-size:.95rem;">
                        QA Review —
                        @if($review->review_date)
                            {{ $review->review_date->format('d/m/Y') }}
                        @elseif($review->scheduled_date)
                            Planned {{ $review->scheduled_date->format('d/m/Y') }}
                        @else
                            —
                        @endif
                        <span class="status-badge ms-2" style="background:{{ $color }}22;color:{{ $color }};">
                            {{ $review->statusLabel() }}
                        </span>
                    </div>
                    <div class="text-muted" style="font-size:.8rem;">
                        @if($review->reviewer_name) Reviewer: {{ $review->reviewer_name }} &nbsp;|&nbsp; @endif
                        Scheduled: {{ $review->scheduled_date?->format('d/m/Y') ?? '—' }}
                        &nbsp;|&nbsp; Created by {{ $review->createdBy?->name ?? '—' }}
                        on {{ $review->created_at->format('d/m/Y') }}
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('fm.qa-review.show', $review->id) }}"
                   class="btn btn-sm fw-semibold"
                   style="background:#c20102;color:#fff;border:none;font-size:.8rem;">
                    <i class="bi bi-pencil me-1"></i>
                    {{ $review->status === 'completed' ? 'View' : 'Fill In' }}
                </a>
                <a href="{{ route('fm.qa-review.print', $review->id) }}"
                   target="_blank"
                   class="btn btn-sm btn-outline-secondary fw-semibold"
                   style="font-size:.8rem;">
                    <i class="bi bi-printer me-1"></i>Print
                </a>
                @if($review->status === 'scheduled')
                <button class="btn btn-sm btn-outline-danger fw-semibold"
                        style="font-size:.8rem;"
                        onclick="deleteReview({{ $review->id }})">
                    <i class="bi bi-trash me-1"></i>Delete
                </button>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach
</div>
@endif

{{-- ── Schedule Modal ── --}}
<div class="modal fade" id="modalSchedule" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius:14px;overflow:hidden;">
            <div class="modal-header border-0 py-3"
                 style="background:linear-gradient(90deg,#c20102,#8b0001);color:#fff;">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-calendar-plus me-2"></i>Schedule QA Review
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('fm.qa-review.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Planned Review Date <span class="text-danger">*</span></label>
                        <input type="date" name="scheduled_date" class="form-control" required>
                        <div class="form-text">QA Reviews are typically conducted every 2 years.</div>
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-semibold">Reviewer Name (optional)</label>
                        <input type="text" name="reviewer_name" id="modalReviewerName"
                               class="form-control personnel-ac"
                               placeholder="Name of the reviewer…"
                               value="{{ Auth::user()->name }}"
                               autocomplete="off">
                        <div class="form-text">Can be changed later when filling in the form.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn fw-semibold" style="background:#c20102;color:#fff;border:none;">
                        <i class="bi bi-calendar-check me-1"></i>Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// ── Personnel autocomplete ─────────────────────────────────────────────
const PERSONNEL_NAMES = @json($personnelNames);

function initPersonnelAC(input) {
    if (!input || input._acInit) return;
    input._acInit = true;

    const list = document.createElement('ul');
    list.style.cssText = 'position:absolute;z-index:9999;background:#fff;border:1px solid #ccc;border-radius:6px;max-height:200px;overflow-y:auto;list-style:none;margin:0;padding:4px 0;min-width:220px;box-shadow:0 4px 12px rgba(0,0,0,.12);display:none;';
    input.parentNode.style.position = 'relative';
    input.parentNode.appendChild(list);

    function render(query) {
        const q = query.toLowerCase();
        const matches = PERSONNEL_NAMES.filter(n => n.toLowerCase().includes(q)).slice(0, 10);
        list.innerHTML = '';
        if (!matches.length || !q) { list.style.display = 'none'; return; }
        matches.forEach(name => {
            const li = document.createElement('li');
            li.textContent = name;
            li.style.cssText = 'padding:6px 12px;cursor:pointer;font-size:.85rem;';
            li.addEventListener('mousedown', e => {
                e.preventDefault();
                input.value = name;
                list.style.display = 'none';
            });
            li.addEventListener('mouseenter', () => li.style.background = '#f0f4ff');
            li.addEventListener('mouseleave', () => li.style.background = '');
            list.appendChild(li);
        });
        list.style.display = 'block';
    }

    input.addEventListener('input', () => render(input.value));
    input.addEventListener('focus', () => render(input.value));
    input.addEventListener('blur', () => setTimeout(() => list.style.display = 'none', 150));
}

document.querySelectorAll('.personnel-ac').forEach(initPersonnelAC);

function deleteReview(id) {
    if (!confirm('Delete this scheduled review? This action cannot be undone.')) return;
    fetch(`/fm/qa-review/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) location.reload();
        else alert(data.message || 'Error deleting review.');
    });
}
</script>
@endsection

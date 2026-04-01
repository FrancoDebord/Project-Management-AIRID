@extends('index-new')
@section('title', 'CPIA Sections — Admin')

@section('content')
<style>
.cl-header { background: linear-gradient(135deg, #c20102, #8b0001); border-radius: 1rem; padding: 1.4rem 2rem; color: #fff; }
.section-card { border-radius: 10px; border: 1px solid #dee2e6; transition: box-shadow .2s; }
.section-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.1); }
</style>

<div class="cl-header d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <i class="bi bi-clipboard2-pulse fs-4"></i>
            <h4 class="mb-0 fw-bold">Critical Phase Impact Assessment — Sections</h4>
        </div>
        <div style="font-size:.85rem;opacity:.8;">
            {{ $sections->count() }} sections &mdash;
            {{ $sections->sum('items_count') }} items total
        </div>
    </div>
    <a href="{{ route('indexPage') }}" class="btn fw-semibold" style="background:#fff;color:#c20102;border:none;">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible py-2 mb-3">
    <i class="bi bi-check2-circle me-1"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-3">
@foreach($sections as $section)
<div class="col-12 col-md-6 col-xl-4">
    <div class="section-card bg-white p-3">
        <div class="d-flex align-items-start justify-content-between gap-2">
            <div style="flex:1;min-width:0;">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="fw-bold" style="font-size:1.15rem;color:#c20102;">{{ $section->letter }}.</span>
                    <span class="fw-semibold" style="font-size:.92rem;">{{ $section->title }}</span>
                </div>
                <div class="text-muted" style="font-size:.78rem;">
                    {{ $section->items_count }} item(s)
                    @if(!$section->is_active)
                        <span class="badge bg-secondary ms-1" style="font-size:.65rem;">Inactive</span>
                    @endif
                </div>
            </div>
            <a href="{{ route('admin.cpia.show', $section->id) }}"
               class="btn btn-sm fw-semibold flex-shrink-0"
               style="background:#c20102;color:#fff;border:none;font-size:.78rem;">
                <i class="bi bi-pencil me-1"></i>Manage
            </a>
        </div>
    </div>
</div>
@endforeach
</div>

@endsection

@extends('index-new')
@section('title', 'Checklist Questions — Admin')

@section('content')
<style>
.cl-header { background: linear-gradient(135deg, #c20102, #8b0001); border-radius: 1rem; padding: 1.4rem 2rem; color: #fff; }
.cat-badge { font-size: .72rem; padding: 3px 10px; border-radius: 20px; font-weight: 600; }
.tpl-card  { border-radius: 10px; border: 1px solid #dee2e6; transition: box-shadow .2s; }
.tpl-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.1); }
.cat-section-title { font-size: .78rem; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: #6c757d; margin: 1.5rem 0 .6rem; }
</style>

{{-- Header --}}
<div class="cl-header d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <i class="bi bi-list-check fs-4"></i>
            <h4 class="mb-0 fw-bold">Gestion des Questions de Checklists</h4>
        </div>
        <div style="font-size:.85rem;opacity:.8;">
            {{ $templates->flatten()->count() }} templates &mdash;
            {{ $templates->flatten()->sum(fn($t) => $t->total_questions) }} questions au total
        </div>
    </div>
    <a href="{{ route('indexPage') }}" class="btn fw-semibold" style="background:#fff;color:#c20102;border:none;">
        <i class="bi bi-arrow-left me-1"></i>Retour
    </a>
</div>

@php
$categoryLabels = [
    'qa'             => ['label' => 'QA / Facility Manager', 'color' => '#c20102', 'icon' => 'bi-clipboard2-check'],
    'facility'       => ['label' => 'Facility / Process Inspection', 'color' => '#0d6efd', 'icon' => 'bi-building'],
    'protocol'       => ['label' => 'Study-based Inspection', 'color' => '#198754', 'icon' => 'bi-file-earmark-text'],
    'critical_phase' => ['label' => 'Critical Phase Inspection', 'color' => '#6f42c1', 'icon' => 'bi-exclamation-triangle'],
];
@endphp

@foreach($categoryLabels as $catKey => $catMeta)
@if(isset($templates[$catKey]) && $templates[$catKey]->count())
<div class="cat-section-title">
    <i class="bi {{ $catMeta['icon'] }} me-1"></i>{{ $catMeta['label'] }}
</div>

<div class="row g-3 mb-2">
@foreach($templates[$catKey] as $tpl)
<div class="col-12 col-md-6 col-xl-4">
    <div class="tpl-card bg-white p-3">
        <div class="d-flex align-items-start justify-content-between gap-2">
            <div style="flex:1;min-width:0;">
                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                    <span class="fw-semibold" style="font-size:.92rem;">{{ $tpl->name }}</span>
                    @if($tpl->reference_code)
                    <span class="badge rounded-pill" style="background:{{ $catMeta['color'] }}22;color:{{ $catMeta['color'] }};font-size:.68rem;">{{ $tpl->reference_code }}</span>
                    @endif
                </div>
                <div class="text-muted" style="font-size:.78rem;">
                    {{ $tpl->sections->count() }} section(s) &nbsp;·&nbsp;
                    {{ $tpl->total_questions }} question(s)
                    @if(!$tpl->is_active)
                        <span class="badge bg-secondary ms-1" style="font-size:.65rem;">Inactif</span>
                    @endif
                </div>
            </div>
            <a href="{{ route('admin.checklists.show', $tpl->id) }}"
               class="btn btn-sm fw-semibold flex-shrink-0"
               style="background:{{ $catMeta['color'] }};color:#fff;border:none;font-size:.78rem;">
                <i class="bi bi-pencil me-1"></i>Gérer
            </a>
        </div>
    </div>
</div>
@endforeach
</div>
@endif
@endforeach

@endsection

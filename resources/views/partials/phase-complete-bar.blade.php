@php
    $phaseDone   = in_array($phase, $project->phases_completed ?? []);
    $ps          = $phaseStatuses[$phase] ?? ['can_complete' => true, 'items' => [], 'next' => ''];
    $canComplete = $ps['can_complete'];
    $statusItems = $ps['items'] ?? [];
    $nextStep    = $ps['next'] ?? '';
@endphp

<hr class="mt-4 mb-3">

{{-- Status checklist + next step --}}
@if($project->id && (count($statusItems) || $nextStep))
<div class="mb-3 px-1">
    {{-- Criteria checklist --}}
    @if(count($statusItems))
    <div class="d-flex flex-wrap gap-2 mb-2">
        @foreach($statusItems as $item)
        <span class="badge rounded-pill d-flex align-items-center gap-1 py-1 px-2"
              style="font-size:.78rem;font-weight:500;background:{{ $item['done'] ? '#d1f5de' : '#fde8e8' }};color:{{ $item['done'] ? '#166534' : '#991b1b' }};">
            <i class="bi {{ $item['done'] ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
            {{ $item['label'] }}
        </span>
        @endforeach
    </div>
    @endif

    {{-- Next step hint --}}
    @if($nextStep && !$phaseDone)
    <p class="text-muted small mb-0">
        <i class="bi bi-arrow-right-circle me-1 text-primary"></i>
        <strong>Next:</strong> {{ $nextStep }}
    </p>
    @elseif($phaseDone)
    <p class="text-success small mb-0">
        <i class="bi bi-check-circle-fill me-1"></i>
        <strong>Phase marked as completed.</strong>
    </p>
    @endif
</div>
@endif

{{-- Mark as completed / Unmark button --}}
@if($project->id)
<div class="d-flex align-items-center justify-content-end gap-2 phase-complete-bar">
    @if($phaseDone)
        <button class="btn btn-sm btn-outline-secondary phase-toggle-btn"
                data-phase="{{ $phase }}"
                data-project="{{ $project->id }}"
                onclick="togglePhaseComplete('{{ $phase }}', {{ $project->id }}, this)">
            <i class="bi bi-arrow-counterclockwise me-1"></i>Unmark as Completed
        </button>
    @elseif($canComplete)
        <button class="btn btn-sm btn-success phase-toggle-btn"
                data-phase="{{ $phase }}"
                data-project="{{ $project->id }}"
                onclick="togglePhaseComplete('{{ $phase }}', {{ $project->id }}, this)">
            <i class="bi bi-check-lg me-1"></i>Mark as Completed
        </button>
    @else
        <button class="btn btn-sm btn-secondary phase-toggle-btn" disabled
                title="Complete the required criteria above before marking this phase as done.">
            <i class="bi bi-lock me-1"></i>Mark as Completed
        </button>
    @endif
</div>
@endif

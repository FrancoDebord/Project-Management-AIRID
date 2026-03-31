@extends('index-new')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0" style="color:#1a3a6b;">
            <i class="bi bi-bell-fill me-2"></i>Notifications
        </h4>
        <p class="text-muted small mb-0">All your platform notifications.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-sm btn-outline-secondary" id="markAllReadBtn">
            <i class="bi bi-check2-all me-1"></i>Mark all as read
        </button>
        <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        @forelse($notifications as $n)
        <div class="d-flex align-items-start gap-3 px-4 py-3 border-bottom notif-row {{ $n->isRead() ? '' : 'bg-light' }}"
             data-id="{{ $n->id }}">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-1"
                 style="width:38px;height:38px;background:{{ $n->isRead() ? '#e9ecef' : '#1a3a6b' }};">
                <i class="bi {{ $n->icon ?? 'bi-bell' }} {{ $n->isRead() ? 'text-muted' : 'text-white' }}" style="font-size:.95rem;"></i>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start">
                    <strong class="small {{ $n->isRead() ? 'text-muted' : '' }}">{{ $n->title }}</strong>
                    <span class="text-muted" style="font-size:.72rem;white-space:nowrap;margin-left:12px;">{{ $n->created_at->diffForHumans() }}</span>
                </div>
                @if($n->body)
                    <p class="mb-1 small text-muted">{{ $n->body }}</p>
                @endif
                @if($n->url)
                    <a href="{{ $n->url }}" class="small fw-semibold" style="color:#1a3a6b;">
                        <i class="bi bi-arrow-right me-1"></i>View
                    </a>
                @endif
            </div>
            @if(!$n->isRead())
                <button class="btn btn-sm btn-link p-0 text-muted mark-read-btn flex-shrink-0"
                        data-id="{{ $n->id }}" title="Mark as read" style="font-size:.7rem;">
                    <i class="bi bi-check2"></i>
                </button>
            @endif
        </div>
        @empty
        <div class="text-center text-muted py-5">
            <i class="bi bi-bell-slash fs-1 d-block mb-2 opacity-25"></i>
            No notifications yet.
        </div>
        @endforelse
    </div>
</div>

@if($notifications->hasPages())
    <div class="mt-3">{{ $notifications->links() }}</div>
@endif

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

document.querySelectorAll('.mark-read-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        fetch(`/notifications/${id}/read`, { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF } })
            .then(() => {
                const row = btn.closest('.notif-row');
                row.classList.remove('bg-light');
                btn.remove();
                const icon = row.querySelector('.rounded-circle');
                icon.style.background = '#e9ecef';
                icon.querySelector('i').className = (icon.querySelector('i').className || '').replace('text-white', 'text-muted');
            });
    });
});

document.getElementById('markAllReadBtn').addEventListener('click', () => {
    fetch('/notifications/read-all', { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF } })
        .then(() => location.reload());
});
</script>
@endsection

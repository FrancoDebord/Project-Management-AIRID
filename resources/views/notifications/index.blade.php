@extends('index-new')

@section('content')

{{-- ── Page header ────────────────────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0" style="color:#1a3a6b;">
            <i class="bi bi-bell-fill me-2"></i>Notifications
        </h4>
        <p class="text-muted small mb-0">All your platform notifications.</p>
    </div>
    <div class="d-flex gap-2">
        @if($unread->total() > 0)
        <button class="btn btn-sm fw-semibold text-white" id="markAllReadBtn"
                style="background:linear-gradient(90deg,#1a3a6b,#c41230);border:none;">
            <i class="bi bi-check2-all me-1"></i>Mark all as read
        </button>
        @endif
        <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════
     SECTION 1 — À TRAITER (ouvert par défaut)
══════════════════════════════════════════════════════════════════════ --}}
<div class="accordion mb-2" id="notifAccordion">

    {{-- Panel 1 : À traiter --}}
    <div class="accordion-item border-0 mb-3"
         style="border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(196,18,48,.10);">

        <h2 class="accordion-header" id="headingUnread">
            <button class="accordion-button fw-bold py-3 px-4"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapseUnread"
                    aria-expanded="true"
                    aria-controls="collapseUnread"
                    style="background:linear-gradient(90deg,#fff3f3,#fff);color:#c41230;border-left:4px solid #c41230;border-radius:12px 12px 0 0;">
                <i class="bi bi-bell-fill me-2" style="color:#c41230;"></i>
                À traiter
                <span class="badge rounded-pill ms-2 fw-semibold" style="background:#c41230;font-size:.72rem;">
                    {{ $unread->total() }}
                </span>
            </button>
        </h2>

        <div id="collapseUnread" class="accordion-collapse collapse show"
             aria-labelledby="headingUnread">
            <div class="accordion-body p-0" style="background:#fff;">
                @forelse($unread as $n)
                <div class="d-flex align-items-start gap-3 px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}"
                     data-id="{{ $n->id }}"
                     style="border-color:#f5c6cb;position:relative;background:#fff;">
                    <div style="position:absolute;left:0;top:0;bottom:0;width:4px;background:#c41230;"></div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-1"
                         style="width:40px;height:40px;background:linear-gradient(135deg,#1a3a6b,#c41230);box-shadow:0 2px 6px rgba(196,18,48,.2);">
                        <i class="bi {{ $n->icon ?? 'bi-bell-fill' }} text-white" style="font-size:1rem;"></i>
                    </div>
                    <div class="flex-grow-1 py-1">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <strong class="small" style="color:#1a1a1a;">{{ $n->title }}</strong>
                            <span style="font-size:.7rem;white-space:nowrap;color:#999;">
                                <i class="bi bi-clock me-1"></i>{{ $n->created_at->diffForHumans() }}
                            </span>
                        </div>
                        @if($n->body)
                            <p class="mb-2 mt-1 small" style="color:#444;line-height:1.4;">{{ $n->body }}</p>
                        @endif
                        @if($n->url)
                            <a href="{{ $n->url }}" class="btn btn-sm fw-semibold"
                               style="background:#1a3a6b;color:#fff;font-size:.75rem;padding:3px 12px;border-radius:20px;border:none;">
                                <i class="bi bi-arrow-right me-1"></i>Accéder
                            </a>
                        @endif
                    </div>
                    <button class="btn btn-sm mark-read-btn flex-shrink-0 mt-1 fw-semibold"
                            data-id="{{ $n->id }}" title="Marquer comme géré"
                            style="font-size:.72rem;border:1.5px solid #c41230;color:#c41230;border-radius:20px;padding:2px 10px;background:transparent;white-space:nowrap;">
                        <i class="bi bi-check2 me-1"></i>Gérer
                    </button>
                </div>
                @empty
                <div class="text-center py-5" style="color:#aaa;">
                    <i class="bi bi-check2-circle" style="font-size:2.5rem;color:#a5d6a7;display:block;margin-bottom:8px;"></i>
                    <span class="small fw-semibold">Aucune notification à traiter.</span>
                </div>
                @endforelse
                @if($unread->hasPages())
                    <div class="px-4 py-3">{{ $unread->links() }}</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Panel 2 : Déjà gérées (fermé par défaut) --}}
    <div class="accordion-item border-0"
         style="border-radius:12px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.07);">

        <h2 class="accordion-header" id="headingRead">
            <button class="accordion-button collapsed fw-semibold py-3 px-4"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapseRead"
                    aria-expanded="false"
                    aria-controls="collapseRead"
                    style="background:#f8f9fa;color:#6c757d;border-left:4px solid #ced4da;border-radius:12px 12px 0 0;">
                <i class="bi bi-check2-all me-2" style="color:#6c757d;"></i>
                Déjà gérées
                <span class="badge rounded-pill ms-2 bg-secondary fw-normal" style="font-size:.72rem;">
                    {{ $read->total() }}
                </span>
            </button>
        </h2>

        <div id="collapseRead" class="accordion-collapse collapse"
             aria-labelledby="headingRead">
            <div class="accordion-body p-0" style="background:#f8f9fa;">
                @forelse($read as $n)
                <div class="d-flex align-items-start gap-3 px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}"
                     style="border-color:#dee2e6;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-1"
                         style="width:36px;height:36px;background:#e9ecef;">
                        <i class="bi {{ $n->icon ?? 'bi-bell' }} text-muted" style="font-size:.85rem;"></i>
                    </div>
                    <div class="flex-grow-1 py-1">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <span class="small fw-semibold text-muted">{{ $n->title }}</span>
                            <span style="font-size:.7rem;white-space:nowrap;color:#ccc;">
                                {{ $n->created_at->diffForHumans() }}
                            </span>
                        </div>
                        @if($n->body)
                            <p class="mb-1 mt-1 small text-muted" style="font-size:.8rem;">{{ $n->body }}</p>
                        @endif
                        @if($n->url)
                            <a href="{{ $n->url }}" class="small text-muted">
                                <i class="bi bi-arrow-right me-1"></i>Voir
                            </a>
                        @endif
                    </div>
                    <span class="flex-shrink-0 mt-1 small" style="color:#aaa;white-space:nowrap;">
                        <i class="bi bi-check2-all me-1" style="color:#81c784;"></i>Géré
                    </span>
                </div>
                @empty
                <div class="text-center py-4" style="color:#bbb;">
                    <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:6px;"></i>
                    <span class="small">Aucune notification gérée pour le moment.</span>
                </div>
                @endforelse
                @if($read->hasPages())
                    <div class="px-4 py-3">{{ $read->links() }}</div>
                @endif
            </div>
        </div>
    </div>

</div>{{-- /#notifAccordion --}}

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

document.querySelectorAll('.mark-read-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        fetch(`/notifications/${id}/read`, { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF } })
            .then(() => location.reload());
    });
});

const markAllBtn = document.getElementById('markAllReadBtn');
if (markAllBtn) {
    markAllBtn.addEventListener('click', () => {
        fetch('/notifications/read-all', { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF } })
            .then(() => location.reload());
    });
}
</script>
@endsection

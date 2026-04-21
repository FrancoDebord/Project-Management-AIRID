@extends('index-new')
@section('title', 'Mes paramètres — AIRID')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0" style="color:#1a3a6b;">
            <i class="bi bi-person-gear me-2"></i>Mes paramètres
        </h4>
        <p class="text-muted small mb-0">Préférences personnelles de votre compte.</p>
    </div>
    <a href="{{ route('indexPage') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Retour
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 py-2 px-3">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-4" style="max-width:680px;">

    {{-- ── Push Notifications ── --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header border-0 fw-semibold" style="background:#e8f4fd;color:#1565c0;">
                <i class="bi bi-bell-fill me-2"></i>Notifications Push (navigateur)
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">
                    Les notifications push vous alertent en temps réel même lorsque vous n'êtes pas
                    sur la page — activités en retard, signatures requises, etc.
                </p>

                <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:#f8f9fa;border:1px solid #dee2e6;">
                    <div id="push-icon" class="fs-3"></div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold small" id="push-status-label">Vérification…</div>
                        <div class="text-muted" style="font-size:.78rem;" id="push-status-desc"></div>
                    </div>
                    <button id="push-request-btn" class="btn btn-sm btn-primary d-none">
                        <i class="bi bi-bell me-1"></i>Activer
                    </button>
                    <button id="push-revoke-info-btn" class="btn btn-sm btn-outline-secondary d-none">
                        <i class="bi bi-info-circle me-1"></i>Comment désactiver
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Email notifications ── --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header border-0 fw-semibold" style="background:#e8f4fd;color:#1565c0;">
                <i class="bi bi-envelope-fill me-2"></i>Notifications par email
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">
                    Recevoir une copie email de chaque notification in-app (activités en retard,
                    contrats expirés, signatures requises…).
                </p>
                <form method="POST" action="{{ route('user.settings.update') }}">
                    @csrf
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch"
                               id="emailNotif" name="email_notifications" value="1"
                               {{ (auth()->user()->email_notifications ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold small" for="emailNotif">
                            Recevoir les notifications par email
                        </label>
                    </div>
                    <button type="submit" class="btn btn-sm px-4 fw-semibold"
                            style="background:#1a3a6b;color:#fff;border:none;">
                        <i class="bi bi-save me-1"></i>Enregistrer
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Link to general settings (FM/admin only) ── --}}
    @if(auth()->user()->hasRole(['super_admin','facility_manager']))
    <div class="col-12">
        <a href="{{ route('admin.settings') }}" class="btn btn-outline-secondary w-100">
            <i class="bi bi-gear me-1"></i>Accéder aux Paramètres Généraux de l'application
        </a>
    </div>
    @endif

</div>

<script>
(function () {
    var icon   = document.getElementById('push-icon');
    var label  = document.getElementById('push-status-label');
    var desc   = document.getElementById('push-status-desc');
    var reqBtn = document.getElementById('push-request-btn');
    var revBtn = document.getElementById('push-revoke-info-btn');

    function render(permission) {
        if (permission === 'granted') {
            icon.innerHTML  = '<i class="bi bi-bell-fill text-success"></i>';
            label.textContent = 'Notifications push activées';
            desc.textContent  = 'Vous recevrez des alertes en temps réel dans votre navigateur.';
            revBtn.classList.remove('d-none');
        } else if (permission === 'denied') {
            icon.innerHTML  = '<i class="bi bi-bell-slash-fill text-danger"></i>';
            label.textContent = 'Notifications bloquées';
            desc.textContent  = 'Votre navigateur a bloqué les notifications. Modifiez les permissions dans les paramètres du site.';
        } else {
            icon.innerHTML  = '<i class="bi bi-bell text-warning"></i>';
            label.textContent = 'Notifications non activées';
            desc.textContent  = 'Cliquez sur "Activer" pour recevoir des alertes en temps réel.';
            reqBtn.classList.remove('d-none');
        }
    }

    if (!('Notification' in window)) {
        icon.innerHTML = '<i class="bi bi-exclamation-circle text-muted"></i>';
        label.textContent = 'Non supporté';
        desc.textContent  = 'Votre navigateur ne supporte pas les notifications push.';
        return;
    }

    render(Notification.permission);

    reqBtn.addEventListener('click', function () {
        Notification.requestPermission().then(function (perm) {
            reqBtn.classList.add('d-none');
            render(perm);
        });
    });

    revBtn.addEventListener('click', function () {
        alert('Pour désactiver les notifications :\n\n1. Cliquez sur le cadenas 🔒 dans la barre d\'adresse.\n2. Cherchez "Notifications" et sélectionnez "Bloquer".\n3. Rechargez la page.');
    });
})();
</script>
@endsection

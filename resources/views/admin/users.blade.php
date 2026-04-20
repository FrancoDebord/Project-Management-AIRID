@extends('index-new')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0" style="color:#c20102;">
            <i class="bi bi-people-fill me-2"></i>Gestion des utilisateurs
        </h4>
        <p class="text-muted small mb-0">Attribuez les rôles et les niveaux d'accès à chaque utilisateur.</p>
    </div>
    <a href="{{ route('indexPage') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Retour
    </a>
</div>

{{-- Role legend --}}
<div class="row g-2 mb-4">
    @php
        $roleColors = [
            'super_admin'      => ['bg' => '#c20102', 'icon' => 'bi-shield-fill-check'],
            'facility_manager' => ['bg' => '#0d6efd',  'icon' => 'bi-building'],
            'qa_manager'       => ['bg' => '#198754',  'icon' => 'bi-clipboard2-check-fill'],
            'study_director'   => ['bg' => '#6f42c1',  'icon' => 'bi-person-badge-fill'],
            'project_manager'  => ['bg' => '#0dcaf0',  'icon' => 'bi-kanban-fill'],
            'archivist'        => ['bg' => '#fd7e14',  'icon' => 'bi-archive-fill'],
            'read_only'        => ['bg' => '#6c757d',  'icon' => 'bi-eye-fill'],
        ];
    @endphp
    @foreach($roles as $key => $label)
    <div class="col-6 col-md-3 col-lg-auto">
        <span class="badge d-flex align-items-center gap-1 px-3 py-2 rounded-pill"
              style="background:{{ $roleColors[$key]['bg'] }};font-size:.75rem;">
            <i class="bi {{ $roleColors[$key]['icon'] }}"></i>
            {{ $label }}
        </span>
    </div>
    @endforeach
    <div class="col-6 col-md-3 col-lg-auto">
        <span class="badge d-flex align-items-center gap-1 px-3 py-2 rounded-pill"
              style="background:#7c3aed;font-size:.75rem;">
            <i class="bi bi-person-badge-fill"></i>
            Study Director (désignation scientifique)
        </span>
    </div>
</div>

@php
    $today = \Carbon\Carbon::today();
    // Pre-compute contract state for each user (keyed by user id)
    $contractStates = [];
    foreach($users as $u) {
        $p = $u->personnel;
        if (!$p) {
            $contractStates[$u->id] = 'none';
        } elseif (!$p->sous_contrat) {
            $contractStates[$u->id] = 'inactive';
        } elseif ($p->date_fin_contrat && \Carbon\Carbon::parse($p->date_fin_contrat)->lt($today)) {
            $contractStates[$u->id] = 'expired';
        } else {
            $contractStates[$u->id] = 'active';
        }
    }
    $countExpired  = collect($contractStates)->filter(fn($s) => $s === 'expired')->count();
    $countInactive = collect($contractStates)->filter(fn($s) => $s === 'inactive')->count();
@endphp

<div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
    <div class="input-group" style="max-width:320px;">
        <span class="input-group-text bg-white border-end-0">
            <i class="bi bi-search text-muted"></i>
        </span>
        <input type="text" id="userFilter" class="form-control border-start-0 ps-0"
               placeholder="Filtrer par nom, email, rôle…"
               style="font-size:.88rem;">
    </div>

    {{-- Contract filter buttons --}}
    <div class="btn-group btn-group-sm" id="contractFilterGroup" role="group">
        <button type="button" class="btn btn-outline-secondary active contract-filter-btn" data-filter="all">
            Tous <span class="badge bg-secondary ms-1">{{ count($users) }}</span>
        </button>
        <button type="button" class="btn btn-outline-danger contract-filter-btn" data-filter="expired"
                title="Sous contrat mais date d'expiration dépassée">
            <i class="bi bi-calendar-x me-1"></i>Contrat expiré
            @if($countExpired > 0)
                <span class="badge bg-danger ms-1">{{ $countExpired }}</span>
            @endif
        </button>
        <button type="button" class="btn btn-outline-warning contract-filter-btn" data-filter="inactive"
                title="Marqué comme n'étant plus sous contrat">
            <i class="bi bi-person-slash me-1"></i>Sans contrat
            @if($countInactive > 0)
                <span class="badge bg-warning text-dark ms-1">{{ $countInactive }}</span>
            @endif
        </button>
        <button type="button" class="btn btn-outline-dark contract-filter-btn" data-filter="problem"
                title="Contrat expiré ou inactif">
            <i class="bi bi-exclamation-triangle me-1"></i>Problème contrat
            @if(($countExpired + $countInactive) > 0)
                <span class="badge bg-dark ms-1">{{ $countExpired + $countInactive }}</span>
            @endif
        </button>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead style="background:#f8f9fa;font-size:.82rem;color:#6c757d;text-transform:uppercase;">
                <tr>
                    <th class="px-4 py-3">Utilisateur</th>
                    <th>Email</th>
                    <th>Personnel lié</th>
                    <th>Contrat</th>
                    <th>Rôle actuel</th>
                    <th class="text-center" title="Désignation scientifique Study Director (indépendante du rôle système)">
                        <i class="bi bi-person-badge-fill me-1" style="color:#7c3aed;"></i>Study Director
                    </th>
                    <th class="text-end px-4">Changer le rôle</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                @php
                    $personnelId    = $user->personnel?->id;
                    $isSd           = $personnelId && isset($activeSDPersonnelIds[$personnelId]);
                    $contractState  = $contractStates[$user->id];
                @endphp
                <tr id="user-row-{{ $user->id }}" data-contract="{{ $contractState }}">
                    <td class="px-4">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                 style="width:36px;height:36px;background:{{ $roleColors[$user->role]['bg'] ?? '#6c757d' }};font-size:.9rem;flex-shrink:0;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <span class="fw-semibold">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="text-muted small">{{ $user->email }}</td>
                    <td>
                        @if($user->personnel)
                            <span class="small">{{ $user->personnel->prenom ?? '' }} {{ $user->personnel->nom ?? '' }}</span>
                        @else
                            <span class="text-muted small fst-italic">Non lié</span>
                        @endif
                    </td>

                    {{-- Contract status --}}
                    <td>
                        @if($contractState === 'active')
                            <span class="badge bg-success-subtle text-success border border-success-subtle"
                                  style="font-size:.72rem;">
                                <i class="bi bi-check-circle me-1"></i>Actif
                                @if($user->personnel?->date_fin_contrat)
                                    <span class="fw-normal ms-1">
                                        jusqu'au {{ \Carbon\Carbon::parse($user->personnel->date_fin_contrat)->format('d/m/Y') }}
                                    </span>
                                @endif
                            </span>
                        @elseif($contractState === 'expired')
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle"
                                  style="font-size:.72rem;">
                                <i class="bi bi-calendar-x me-1"></i>Expiré le
                                {{ \Carbon\Carbon::parse($user->personnel->date_fin_contrat)->format('d/m/Y') }}
                            </span>
                        @elseif($contractState === 'inactive')
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle"
                                  style="font-size:.72rem;">
                                <i class="bi bi-person-slash me-1"></i>Inactif
                            </span>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>

                    <td>
                        <span class="badge rounded-pill px-3 py-2 role-badge-{{ $user->id }}"
                              style="background:{{ $roleColors[$user->role]['bg'] ?? '#6c757d' }};font-size:.78rem;">
                            <i class="bi {{ $roleColors[$user->role]['icon'] ?? 'bi-person' }} me-1"></i>
                            {{ $roles[$user->role] ?? $user->role }}
                        </span>
                    </td>

                    {{-- Study Director designation column --}}
                    <td class="text-center" id="sd-cell-{{ $user->id }}">
                        @if(!$personnelId)
                            <span class="text-muted small">—</span>
                        @else
                            @if($isSd)
                                <span class="badge rounded-pill px-2 py-1 sd-badge-{{ $user->id }}"
                                      style="background:#7c3aed;font-size:.73rem;">
                                    <i class="bi bi-person-badge-fill me-1"></i>Study Director
                                </span>
                                <button class="btn btn-sm btn-outline-danger ms-1 sd-toggle-btn py-0 px-2"
                                        data-personnel-id="{{ $personnelId }}"
                                        data-action="demote"
                                        data-user-id="{{ $user->id }}"
                                        title="Révoquer la désignation Study Director"
                                        style="font-size:.72rem;">
                                    <i class="bi bi-x-circle me-1"></i>Révoquer
                                </button>
                            @else
                                <span class="text-muted small sd-badge-{{ $user->id }}">—</span>
                                <button class="btn btn-sm btn-outline-secondary ms-1 sd-toggle-btn py-0 px-2"
                                        data-personnel-id="{{ $personnelId }}"
                                        data-action="promote"
                                        data-user-id="{{ $user->id }}"
                                        title="Désigner comme Study Director"
                                        style="font-size:.72rem;">
                                    <i class="bi bi-person-badge-fill me-1"></i>Désigner SD
                                </button>
                            @endif
                        @endif
                    </td>

                    <td class="text-end px-4">
                        <div class="d-flex align-items-center justify-content-end gap-2">
                            <select class="form-select form-select-sm role-select"
                                    data-user-id="{{ $user->id }}"
                                    style="max-width:180px;font-size:.82rem;">
                                @foreach($roles as $key => $label)
                                    <option value="{{ $key }}" {{ $user->role === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <button class="btn btn-sm save-role-btn"
                                    style="background:#c20102;color:#fff;border:none;"
                                    data-user-id="{{ $user->id }}">
                                <i class="bi bi-check2 me-1"></i>Appliquer
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">Aucun utilisateur trouvé.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ── Study Director designation panel (personnel not linked to a user) ── --}}
<div class="card shadow-sm border-0 mt-4">
    <div class="card-header fw-semibold" style="background:#f3f0ff;color:#7c3aed;font-size:.88rem;">
        <i class="bi bi-person-badge-fill me-2"></i>
        Désignation Study Director — tous les personnels
        <span class="text-muted fw-normal ms-2" style="font-size:.78rem;">
            (y compris les personnels non liés à un compte utilisateur)
        </span>
    </div>
    <div class="card-body p-3">
        <p class="text-muted small mb-3">
            La désignation SD est <strong>indépendante du rôle système</strong>.
            Elle détermine qui peut être sélectionné comme Study Director dans les formulaires de nomination.
        </p>
        <div class="row g-2">
            @foreach($allPersonnel as $p)
            @php $pIsSd = isset($activeSDPersonnelIds[$p->id]); @endphp
            <div class="col-12 col-md-6 col-lg-4">
                <div class="d-flex align-items-center justify-content-between p-2 rounded border
                            {{ $pIsSd ? 'border-purple' : '' }}"
                     style="{{ $pIsSd ? 'background:#f3f0ff;border-color:#7c3aed !important;' : 'background:#fafafa;' }}">
                    <div class="small">
                        <span class="fw-semibold">{{ $p->prenom }} {{ $p->nom }}</span>
                        @if($p->titre_personnel)
                            <span class="text-muted"> — {{ $p->titre_personnel }}</span>
                        @endif
                        @if($pIsSd)
                            <span class="badge ms-1" style="background:#7c3aed;font-size:.65rem;">SD</span>
                        @endif
                    </div>
                    <button class="btn btn-sm py-0 px-2 sd-toggle-panel-btn"
                            data-personnel-id="{{ $p->id }}"
                            data-action="{{ $pIsSd ? 'demote' : 'promote' }}"
                            id="sd-panel-btn-{{ $p->id }}"
                            style="font-size:.72rem;{{ $pIsSd ? 'color:#7c3aed;border-color:#7c3aed;' : '' }}">
                        @if($pIsSd)
                            <i class="bi bi-x-circle me-1"></i>Révoquer
                        @else
                            <i class="bi bi-plus-circle me-1"></i>Désigner
                        @endif
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<script>
const CSRF       = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
const roleColors = @json($roleColors);
const roleLabels = @json($roles);

// ── Live filter (text + contract) ───────────────────────────────
let activeContractFilter = 'all';

function applyFilters() {
    const q = document.getElementById('userFilter').value.toLowerCase().trim();
    document.querySelectorAll('tbody tr[id^="user-row-"]').forEach(row => {
        const contractState = row.dataset.contract ?? 'none';
        const matchContract = activeContractFilter === 'all'
            || activeContractFilter === contractState
            || (activeContractFilter === 'problem' && (contractState === 'expired' || contractState === 'inactive'));
        const matchText = q === '' || row.textContent.toLowerCase().includes(q);
        row.style.display = (matchContract && matchText) ? '' : 'none';
    });
}

document.getElementById('userFilter').addEventListener('input', applyFilters);

document.querySelectorAll('.contract-filter-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.contract-filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        activeContractFilter = this.dataset.filter;
        applyFilters();
    });
});

// ── Role change ─────────────────────────────────────────────────
document.querySelectorAll('.save-role-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const userId = btn.dataset.userId;
        const select = document.querySelector(`.role-select[data-user-id="${userId}"]`);
        const newRole = select.value;

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        fetch(`/admin/users/${userId}/role`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ role: newRole }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const badge  = document.querySelector(`.role-badge-${userId}`);
                const color  = roleColors[newRole]?.bg ?? '#6c757d';
                const icon   = roleColors[newRole]?.icon ?? 'bi-person';
                badge.style.background = color;
                badge.innerHTML = `<i class="bi ${icon} me-1"></i>${roleLabels[newRole]}`;
                const avatar = document.querySelector(`#user-row-${userId} .rounded-circle`);
                if (avatar) avatar.style.background = color;
                alertify.success('Rôle mis à jour avec succès.');
            } else {
                alertify.error('Erreur lors de la mise à jour.');
            }
        })
        .catch(() => alertify.error('Erreur réseau.'))
        .finally(() => { btn.disabled = false; btn.innerHTML = '<i class="bi bi-check2 me-1"></i>Appliquer'; });
    });
});

// ── Study Director toggle (table column buttons) ─────────────────
function sdToggle(personnelId, action, onSuccess) {
    const url = action === 'promote' ? '{{ route("admin.sd.promote") }}' : '{{ route("admin.sd.demote") }}';
    return fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ personnel_id: personnelId }),
    }).then(r => r.json()).then(data => {
        if (data.success) { alertify.success(data.message); onSuccess && onSuccess(); }
        else              { alertify.error(data.message || 'Erreur.'); }
    }).catch(() => alertify.error('Erreur réseau.'));
}

document.querySelectorAll('.sd-toggle-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const pid    = btn.dataset.personnelId;
        const action = btn.dataset.action;
        const userId = btn.dataset.userId;
        btn.disabled = true;
        sdToggle(pid, action, () => location.reload()).finally?.(() => btn.disabled = false);
    });
});

document.querySelectorAll('.sd-toggle-panel-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const pid    = btn.dataset.personnelId;
        const action = btn.dataset.action;
        btn.disabled = true;
        sdToggle(pid, action, () => location.reload()).finally?.(() => btn.disabled = false);
    });
});
</script>
@endsection

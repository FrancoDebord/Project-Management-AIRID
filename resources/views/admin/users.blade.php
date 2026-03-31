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
</div>

<div class="mb-3">
    <div class="input-group" style="max-width:360px;">
        <span class="input-group-text bg-white border-end-0">
            <i class="bi bi-search text-muted"></i>
        </span>
        <input type="text" id="userFilter" class="form-control border-start-0 ps-0"
               placeholder="Filtrer par nom, email, personnel ou rôle…"
               style="font-size:.88rem;">
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
                    <th>Rôle actuel</th>
                    <th class="text-end px-4">Changer le rôle</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr id="user-row-{{ $user->id }}">
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
                    <td>
                        <span class="badge rounded-pill px-3 py-2 role-badge-{{ $user->id }}"
                              style="background:{{ $roleColors[$user->role]['bg'] ?? '#6c757d' }};font-size:.78rem;">
                            <i class="bi {{ $roleColors[$user->role]['icon'] ?? 'bi-person' }} me-1"></i>
                            {{ $roles[$user->role] ?? $user->role }}
                        </span>
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
                                    data-user-id="{{ $user->id }}"
                                    style="white-space:nowrap;">
                                <i class="bi bi-check2 me-1"></i>Appliquer
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-5">Aucun utilisateur trouvé.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
const CSRF   = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
const roleColors = @json($roleColors);
const roleLabels = @json($roles);

// ── Live filter ──────────────────────────────────────────────────
document.getElementById('userFilter').addEventListener('input', function () {
    const q = this.value.toLowerCase().trim();
    document.querySelectorAll('tbody tr[id^="user-row-"]').forEach(row => {
        row.style.display = q === '' || row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});

document.querySelectorAll('.save-role-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const userId = btn.dataset.userId;
        const select = document.querySelector(`.role-select[data-user-id="${userId}"]`);
        const newRole = select.value;

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        fetch(`/admin/users/${userId}/role`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ role: newRole }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const badge = document.querySelector(`.role-badge-${userId}`);
                const color = roleColors[newRole]?.bg ?? '#6c757d';
                const icon  = roleColors[newRole]?.icon ?? 'bi-person';
                badge.style.background = color;
                badge.innerHTML = `<i class="bi ${icon} me-1"></i>${roleLabels[newRole]}`;

                // Update avatar color
                const avatar = document.querySelector(`#user-row-${userId} .rounded-circle`);
                if (avatar) avatar.style.background = color;

                alertify.success('Rôle mis à jour avec succès.');
            } else {
                alertify.error('Erreur lors de la mise à jour.');
            }
        })
        .catch(() => alertify.error('Erreur réseau.'))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check2 me-1"></i>Appliquer';
        });
    });
});
</script>
@endsection

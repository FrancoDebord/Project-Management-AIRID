@php
    $kpProjectId  = request()->get('project_id');
    $kpProject    = $kpProjectId ? \App\Models\Pro_Project::find($kpProjectId) : null;
    $kpCurrent    = $kpProject
        ? $kpProject->keyPersonnelProject->pluck('id')->toArray()
        : [];
    $kpAllPersonnels = \App\Models\Pro_Personnel::where('sous_contrat', 1)->orderBy('nom')->get();
@endphp

<div class="modal fade" id="keyPersonnelModal" data-bs-backdrop="static" data-bs-keyboard="false"
     tabindex="-1" aria-labelledby="keyPersonnelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header" style="background:#5c6bc0;color:#fff;">
                <h5 class="modal-title" id="keyPersonnelModalLabel">
                    <i class="bi bi-people-fill me-2"></i>Key Personnel du projet
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-4 py-3">
                <div id="kp-alert" class="mb-3"></div>

                <p class="text-muted small mb-3">
                    Sélectionnez les membres de l'équipe qui participent à ce projet.
                    Maintenez <kbd>Ctrl</kbd> (ou <kbd>Cmd</kbd> sur Mac) pour sélectionner plusieurs personnes.
                </p>

                {{-- Filtre de recherche --}}
                <div class="mb-2">
                    <input type="text" id="kp-search" class="form-control form-control-sm"
                           placeholder="Filtrer par nom…" autocomplete="off">
                </div>

                <div class="border rounded" style="max-height:360px;overflow-y:auto;">
                    <table class="table table-sm table-hover mb-0" id="kp-table">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th style="width:40px;">
                                    <input type="checkbox" id="kp-check-all" title="Tout sélectionner / désélectionner">
                                </th>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Titre</th>
                                <th>Rôle</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kpAllPersonnels as $p)
                                <tr class="kp-row">
                                    <td>
                                        <input type="checkbox" class="kp-checkbox" name="staff_ids[]"
                                               value="{{ $p->id }}"
                                               {{ in_array($p->id, $kpCurrent) ? 'checked' : '' }}>
                                    </td>
                                    <td class="kp-nom">{{ $p->nom }}</td>
                                    <td>{{ $p->prenom }}</td>
                                    <td class="text-muted small">{{ $p->titre_personnel ?? '—' }}</td>
                                    <td class="text-muted small">{{ $p->role ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-2 text-muted small">
                    <span id="kp-count">{{ count($kpCurrent) }}</span> personne(s) sélectionnée(s)
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn fw-semibold px-4" style="background:#5c6bc0;color:#fff;"
                        id="kp-save-btn">
                    <i class="bi bi-save me-1"></i>Enregistrer
                </button>
            </div>

        </div>
    </div>
</div>

<script>
(function () {
    // Filtre de recherche
    document.getElementById('kp-search').addEventListener('input', function () {
        var q = this.value.toLowerCase();
        document.querySelectorAll('#kp-table tbody .kp-row').forEach(function (row) {
            var nom = row.querySelector('.kp-nom').textContent.toLowerCase();
            row.style.display = nom.includes(q) ? '' : 'none';
        });
    });

    // Tout sélectionner / désélectionner
    document.getElementById('kp-check-all').addEventListener('change', function () {
        var checked = this.checked;
        document.querySelectorAll('.kp-checkbox').forEach(function (cb) {
            if (cb.closest('tr').style.display !== 'none') cb.checked = checked;
        });
        updateCount();
    });

    // Compteur
    function updateCount() {
        var n = document.querySelectorAll('.kp-checkbox:checked').length;
        document.getElementById('kp-count').textContent = n;
    }
    document.querySelectorAll('.kp-checkbox').forEach(function (cb) {
        cb.addEventListener('change', updateCount);
    });

    // Sauvegarde AJAX
    document.getElementById('kp-save-btn').addEventListener('click', function () {
        var btn = this;
        btn.disabled = true;

        var projectId = document.getElementById('project_id_basic_information')?.value
                     || new URLSearchParams(window.location.search).get('project_id');

        var staffIds = Array.from(document.querySelectorAll('.kp-checkbox:checked'))
                           .map(function (cb) { return cb.value; });

        var formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        formData.append('project_id', projectId);
        staffIds.forEach(function (id) { formData.append('staff_ids[]', id); });

        fetch('{{ route("saveKeyPersonnel") }}', { method: 'POST', body: formData })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                var alertEl = document.getElementById('kp-alert');
                if (data.code_erreur === 0) {
                    alertEl.innerHTML = '<div class="alert alert-success py-2">Personnel enregistré avec succès.</div>';
                    setTimeout(function () {
                        bootstrap.Modal.getInstance(document.getElementById('keyPersonnelModal')).hide();
                        window.location.reload();
                    }, 1200);
                } else {
                    alertEl.innerHTML = '<div class="alert alert-danger py-2">' + (data.message || 'Erreur.') + '</div>';
                }
            })
            .catch(function () {
                document.getElementById('kp-alert').innerHTML = '<div class="alert alert-danger py-2">Erreur réseau.</div>';
            })
            .finally(function () { btn.disabled = false; });
    });

    // Réinitialiser l'alerte à l'ouverture
    document.getElementById('keyPersonnelModal').addEventListener('show.bs.modal', function () {
        document.getElementById('kp-alert').innerHTML = '';
        document.getElementById('kp-search').value = '';
        document.querySelectorAll('#kp-table tbody .kp-row').forEach(function (r) { r.style.display = ''; });
    });
})();
</script>

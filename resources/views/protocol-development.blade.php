@php
    $protocol_dev_activities_project = $project ? $project->protocolDeveloppementActivitiesProject : collect();
    $totalPd    = $protocol_dev_activities_project->where('applicable', true)->count();
    $completePd = $protocol_dev_activities_project->where('applicable', true)->where('complete', true)->count();
@endphp

<style>
    .pd-card-header {
        background: linear-gradient(135deg, #1a3a6b 0%, #2a5aaa 100%);
        color: #fff;
        border-radius: .75rem .75rem 0 0;
        padding: .65rem 1rem;
        font-weight: 600;
        font-size: .9rem;
    }
    .pd-row-done   { background: #f0fff4; }
    .pd-row-active { background: #fffbf0; }
    .pd-row-locked { background: #f8f9fa; opacity: .75; }
    .pd-progress-wrap { height: 6px; border-radius: 3px; background: #e9ecef; overflow: hidden; }
    .pd-progress-bar  { height: 100%; border-radius: 3px; background: #1a3a6b; transition: width .4s; }
</style>

<div class="row mt-2">
    @if(!$project)
        <div class="col-12">
            <p class="alert alert-info text-center">Veuillez d'abord sélectionner un projet.</p>
        </div>
    @else

    <div class="col-12 mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h6 class="fw-bold mb-0" style="color:#1a3a6b;">
                <i class="bi bi-file-earmark-code me-2"></i>Protocol Development — Documents
            </h6>
            <small class="text-muted">
                Soumission séquentielle des documents de développement du protocole
                <strong>{{ $project->project_code ?? '' }}</strong>
            </small>
        </div>
        <div class="d-flex align-items-center gap-3">
            {{-- Progress summary --}}
            @if($totalPd > 0)
            <div style="min-width:140px;">
                <div class="d-flex justify-content-between mb-1" style="font-size:.75rem;">
                    <span class="text-muted">Progression</span>
                    <span class="fw-bold {{ $completePd === $totalPd ? 'text-success' : 'text-primary' }}">
                        {{ $completePd }}/{{ $totalPd }}
                    </span>
                </div>
                <div class="pd-progress-wrap">
                    <div class="pd-progress-bar" style="width:{{ $totalPd > 0 ? round($completePd/$totalPd*100) : 0 }}%;"></div>
                </div>
            </div>
            @endif
            {{-- Generate button --}}
            <button class="btn btn-sm fw-semibold"
                    style="background:#1a3a6b;color:#fff;"
                    id="generate-protocol-dev-activities"
                    data-project-id="{{ $project->id }}">
                <i class="bi bi-arrow-repeat me-1"></i>
                {{ $totalPd > 0 ? 'Régénérer les activités' : 'Générer les activités' }}
            </button>
        </div>
    </div>

    @if($totalPd === 0)
        <div class="col-12">
            <div class="alert alert-info d-flex align-items-center gap-2">
                <i class="bi bi-info-circle-fill fs-5"></i>
                <span>Aucune activité Protocol Dev générée pour ce projet. Cliquez sur <strong>Générer les activités</strong> pour commencer.</span>
            </div>
        </div>
    @else
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="pd-card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-collection me-2"></i>Activités Protocol Dev</span>
                <span class="badge bg-white text-primary ms-2">{{ $totalPd }} activité{{ $totalPd > 1 ? 's' : '' }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:4%">#</th>
                                <th>Activité</th>
                                <th style="width:14%">Assigné à</th>
                                <th style="width:12%">Réalisé par</th>
                                <th style="width:10%">Date réalisée</th>
                                <th style="width:10%">Échéance</th>
                                <th style="width:10%" class="text-center">Document</th>
                                <th style="width:12%" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($protocol_dev_activities_project as $index => $activity)
                                @php
                                    $assignedTo     = $activity->assignedTo;
                                    $staffPerformed = $activity->staffPerformed;
                                    $level          = $activity->level_activite;
                                    $isDone         = (bool) $activity->complete;

                                    // Can this activity be acted on?
                                    $prevDone = $level <= 1 || \App\Models\Pro_ProtocolDevActivityProject
                                        ::where('project_id', $project_id)
                                        ->where('level_activite', $level - 1)
                                        ->where('complete', true)->exists();

                                    $prevDate = \App\Models\Pro_ProtocolDevActivityProject
                                        ::where('project_id', $project_id)
                                        ->where('level_activite', $level - 1)
                                        ->where('complete', true)
                                        ->value('date_performed');

                                    $nextDate = \App\Models\Pro_ProtocolDevActivityProject
                                        ::where('project_id', $project_id)
                                        ->where('level_activite', $level + 1)
                                        ->where('complete', true)
                                        ->value('date_performed');

                                    $rowClass = $isDone ? 'pd-row-done' : ($prevDone ? 'pd-row-active' : 'pd-row-locked');
                                @endphp
                                <tr class="{{ $rowClass }}" id="pd-row-{{ $activity->id }}">
                                    <td class="text-muted small ps-3">
                                        @if($isDone)
                                            <i class="bi bi-check-circle-fill text-success"></i>
                                        @elseif($prevDone)
                                            <i class="bi bi-circle text-warning"></i>
                                        @else
                                            <i class="bi bi-lock text-secondary"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold small">{{ $activity->protocolDevActivity->nom_activite }}</div>
                                        @if(!$activity->applicable)
                                            <span class="badge bg-secondary" style="font-size:.65rem;">Non applicable</span>
                                        @endif
                                    </td>
                                    <td class="small text-muted">
                                        {{ $assignedTo ? $assignedTo->prenom . ' ' . $assignedTo->nom : '—' }}
                                    </td>
                                    <td class="small text-muted">
                                        {{ $staffPerformed ? $staffPerformed->prenom . ' ' . $staffPerformed->nom : '—' }}
                                    </td>
                                    <td class="small text-muted">
                                        {{ $activity->date_performed ? \Carbon\Carbon::parse($activity->date_performed)->format('d/m/Y') : '—' }}
                                    </td>
                                    <td class="small text-muted">
                                        {{ $activity->due_date_performed ? \Carbon\Carbon::parse($activity->due_date_performed)->format('d/m/Y') : '—' }}
                                    </td>
                                    <td class="text-center">
                                        @if($isDone && $activity->document_file_path)
                                            <a href="{{ asset('storage/' . $activity->document_file_path) }}"
                                               target="_blank"
                                               class="btn btn-sm btn-outline-success py-0 px-2"
                                               title="Voir le document">
                                                <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                                            </a>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($isDone)
                                            {{-- Update --}}
                                            <button class="btn btn-sm btn-outline-primary py-0 px-2 me-1 pd-upload-btn"
                                                    title="Mettre à jour le document"
                                                    data-record-id="{{ $activity->id }}"
                                                    data-activity-name="{{ $activity->protocolDevActivity->nom_activite }}"
                                                    data-date-performed="{{ $activity->date_performed ?? '' }}"
                                                    data-prev-date="{{ $prevDate ?? '' }}"
                                                    data-next-date="{{ $nextDate ?? '' }}"
                                                    data-file-path="{{ $activity->document_file_path ? asset('storage/'.$activity->document_file_path) : '' }}"
                                                    data-is-update="1">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            {{-- Delete --}}
                                            <button class="btn btn-sm btn-outline-danger py-0 px-2 pd-delete-btn"
                                                    title="Supprimer le document"
                                                    data-record-id="{{ $activity->id }}"
                                                    data-activity-name="{{ $activity->protocolDevActivity->nom_activite }}">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        @elseif($prevDone)
                                            {{-- First upload --}}
                                            <button class="btn btn-sm btn-outline-warning py-0 px-2 pd-upload-btn"
                                                    title="Soumettre le document"
                                                    data-record-id="{{ $activity->id }}"
                                                    data-activity-name="{{ $activity->protocolDevActivity->nom_activite }}"
                                                    data-date-performed=""
                                                    data-prev-date="{{ $prevDate ?? '' }}"
                                                    data-next-date="{{ $nextDate ?? '' }}"
                                                    data-file-path=""
                                                    data-is-update="0">
                                                <i class="bi bi-upload me-1"></i>Soumettre
                                            </button>
                                        @else
                                            <span class="text-muted small"><i class="bi bi-lock me-1"></i>Verrouillé</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">Aucune activité trouvée.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    @endif {{-- end @if($project) --}}
</div>

@include('partials.fill-details-protocol-dev-activity')

<script>
(function () {
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    // ── Generate activities ───────────────────────────────────────
    const genBtn = document.getElementById('generate-protocol-dev-activities');
    if (genBtn) {
        genBtn.addEventListener('click', () => {
            const projectId = genBtn.dataset.projectId;
            if (!confirm('Générer / régénérer les activités Protocol Dev pour ce projet ?')) return;
            genBtn.disabled = true;
            genBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Génération…';

            fetch('{{ route("generateProtocolDevActivitiesForProject") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ project_id: projectId })
            }).then(r => r.json()).then(data => {
                if (data.code_erreur === 0) {
                    location.reload();
                } else {
                    alert('Erreur : ' + data.message);
                    genBtn.disabled = false;
                    genBtn.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i>Générer les activités';
                }
            }).catch(() => { alert('Erreur réseau.'); genBtn.disabled = false; });
        });
    }

    // ── Open modal for upload / update ───────────────────────────
    document.querySelectorAll('.pd-upload-btn').forEach(btn => {
        btn.addEventListener('click', () => openPdModal(btn));
    });

    function openPdModal(btn) {
        const isUpdate    = btn.dataset.isUpdate === '1';
        const recordId    = btn.dataset.recordId;
        const actName     = btn.dataset.activityName;
        const datePerf    = btn.dataset.datePerformed;
        const prevDate    = btn.dataset.prevDate;
        const nextDate    = btn.dataset.nextDate;
        const filePath    = btn.dataset.filePath;

        document.getElementById('pdRecordId').value      = recordId;
        document.getElementById('pdActivityName').value  = actName;
        document.getElementById('pdDatePerformed').value = datePerf || '';
        document.getElementById('pdDocumentFile').value  = '';
        document.getElementById('pdModalError').classList.add('d-none');

        // Title
        document.getElementById('pdModalTitleText').textContent = isUpdate
            ? 'Mettre à jour le document'
            : 'Soumettre le document';

        // File required indicator
        document.getElementById('pdFileRequired').textContent = isUpdate ? '' : '*';

        // Date constraints
        let hint = '';
        if (prevDate) hint += `Après le ${prevDate}`;
        if (nextDate) hint += (hint ? ' — ' : '') + `Avant le ${nextDate}`;
        document.getElementById('pdDateHint').textContent = hint;

        // Current doc banner
        const banner = document.getElementById('pdCurrentDocBanner');
        if (isUpdate && filePath) {
            document.getElementById('pdCurrentDocLink').href = filePath;
            banner.classList.remove('d-none');
        } else {
            banner.classList.add('d-none');
        }

        new bootstrap.Modal(document.getElementById('detailsModal')).show();
    }

    // ── Submit modal ──────────────────────────────────────────────
    window.submitProtocolDevDoc = function () {
        const errDiv     = document.getElementById('pdModalError');
        const recordId   = document.getElementById('pdRecordId').value;
        const datePerf   = document.getElementById('pdDatePerformed').value;
        const fileInput  = document.getElementById('pdDocumentFile');
        const hasExisting = !document.getElementById('pdCurrentDocBanner').classList.contains('d-none');

        errDiv.classList.add('d-none');

        if (!datePerf) {
            errDiv.textContent = 'La date de réalisation est obligatoire.';
            errDiv.classList.remove('d-none');
            return;
        }
        if (!hasExisting && fileInput.files.length === 0) {
            errDiv.textContent = 'Un fichier PDF est obligatoire pour la première soumission.';
            errDiv.classList.remove('d-none');
            return;
        }

        const btn = document.getElementById('pdSaveBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Enregistrement…';

        const fd = new FormData();
        fd.append('_token', CSRF);
        fd.append('protocol_dev_activity_project_id', recordId);
        fd.append('date_performed', datePerf);
        if (fileInput.files.length > 0) fd.append('document_file', fileInput.files[0]);

        fetch('{{ route("saveProtocolDevelopmentActivityCompleted") }}', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-save me-1"></i>Enregistrer';
                if (data.code_erreur === 0) {
                    bootstrap.Modal.getInstance(document.getElementById('detailsModal')).hide();
                    location.reload();
                } else {
                    errDiv.textContent = data.message || 'Une erreur est survenue.';
                    errDiv.classList.remove('d-none');
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-save me-1"></i>Enregistrer';
                errDiv.textContent = 'Erreur réseau.';
                errDiv.classList.remove('d-none');
            });
    };

    // ── Delete document ───────────────────────────────────────────
    document.querySelectorAll('.pd-delete-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const name = btn.dataset.activityName;
            if (!confirm(`Supprimer le document soumis pour « ${name} » ?\n\nL'activité sera marquée comme non complétée.`)) return;
            btn.disabled = true;

            const fd = new FormData();
            fd.append('_token', CSRF);
            fd.append('record_id', btn.dataset.recordId);

            fetch('{{ route("deleteProtocolDevDocument") }}', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Erreur : ' + data.message);
                        btn.disabled = false;
                    }
                })
                .catch(() => { alert('Erreur réseau.'); btn.disabled = false; });
        });
    });
})();
</script>

@php
    $protocol_dev_activities_project = $project ? $project->protocolDeveloppementActivitiesProject()->with(['protocolDevActivity','protocolDevDocuments.qaInspection','protocolDevDocuments.staffPerformed','assignedTo'])->get() : collect();
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
    .pd-row-activity { background: #f0f4ff; }
    .pd-row-activity.pd-done   { background: #eafaf1; }
    .pd-row-activity.pd-active { background: #fffbf0; }
    .pd-row-activity.pd-locked { background: #f8f9fa; opacity: .8; }
    .pd-row-doc  { background: #fff; }
    .pd-row-doc td:first-child { border-left: 3px solid #2a5aaa; }
    .pd-progress-wrap { height: 6px; border-radius: 3px; background: #e9ecef; overflow: hidden; }
    .pd-progress-bar  { height: 100%; border-radius: 3px; background: #1a3a6b; transition: width .4s; }
    .pd-insp-badge { font-size: .68rem; padding: .15rem .4rem; white-space: nowrap; }
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
                <span>Aucune activité Protocol Dev générée. Cliquez sur <strong>Générer les activités</strong> pour commencer.</span>
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
                    <table class="table table-hover align-middle mb-0" style="font-size:.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th style="width:3%"></th>
                                <th>Activité / Document</th>
                                <th style="width:12%">Assigné à</th>
                                <th style="width:11%">Réalisé par</th>
                                <th style="width:9%">Date réalisée</th>
                                <th style="width:9%">Date upload</th>
                                <th style="width:10%" class="text-center">Document</th>
                                <th style="width:11%" class="text-center">Inspection QA</th>
                                <th style="width:9%" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse ($protocol_dev_activities_project as $index => $activity)
                            @php
                                $actDef      = $activity->protocolDevActivity;
                                $level       = $activity->level_activite;
                                $isDone      = (bool) $activity->complete;
                                $multiplicite = $actDef?->multipicite ?? 'une_fois';
                                $docs        = $activity->protocolDevDocuments;

                                // Locking logic (level 5 always unlocked)
                                $prevDone = $level === 5 || $level <= 1 || \App\Models\Pro_ProtocolDevActivityProject
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

                                $rowStateClass = $isDone ? 'pd-done' : ($prevDone ? 'pd-active' : 'pd-locked');

                                // une_fois: can add a new doc if no docs yet; plusieurs_fois: always can add if unlocked
                                $canAddDoc = $prevDone && ($multiplicite === 'plusieurs_fois' || $docs->isEmpty());
                            @endphp

                            {{-- ── Activity header row ── --}}
                            <tr class="pd-row-activity {{ $rowStateClass }}" id="pd-row-{{ $activity->id }}">
                                <td class="ps-3">
                                    @if($isDone)
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                    @elseif($prevDone)
                                        <i class="bi bi-circle text-warning"></i>
                                    @else
                                        <i class="bi bi-lock text-secondary"></i>
                                    @endif
                                </td>
                                <td colspan="7">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="fw-semibold">{{ $actDef?->nom_activite ?? '—' }}</span>
                                        @if(!$activity->applicable)
                                            <span class="badge bg-secondary" style="font-size:.65rem;">Non applicable</span>
                                        @endif
                                        @if($multiplicite === 'plusieurs_fois')
                                            <span class="badge bg-info text-dark" style="font-size:.65rem;">
                                                <i class="bi bi-layers me-1"></i>Multiple soumissions
                                            </span>
                                        @endif
                                        <span class="text-muted small">
                                            — {{ $docs->count() }} document{{ $docs->count() > 1 ? 's' : '' }} soumis
                                        </span>
                                        @if($activity->assignedTo)
                                            <span class="text-muted small ms-auto">
                                                <i class="bi bi-person me-1"></i>{{ $activity->assignedTo->prenom }} {{ $activity->assignedTo->nom }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($canAddDoc)
                                        <button class="btn btn-sm btn-outline-warning py-0 px-2 pd-upload-btn"
                                                title="Soumettre un document"
                                                data-record-id="{{ $activity->id }}"
                                                data-activity-name="{{ $actDef?->nom_activite }}"
                                                data-multiplicite="{{ $multiplicite }}"
                                                data-prev-date="{{ $prevDate ?? '' }}"
                                                data-next-date="{{ $nextDate ?? '' }}">
                                            <i class="bi bi-upload me-1"></i>Soumettre
                                        </button>
                                    @elseif(!$prevDone)
                                        <span class="text-muted small"><i class="bi bi-lock me-1"></i>Verrouillé</span>
                                    @endif
                                </td>
                            </tr>

                            {{-- ── Document sub-rows ── --}}
                            @foreach($docs as $docIndex => $doc)
                                @php
                                    $insp      = $doc->qaInspection;
                                    $performer = $doc->staffPerformed;
                                @endphp
                                <tr class="pd-row-doc" id="pd-doc-row-{{ $doc->id }}">
                                    <td></td>
                                    <td class="ps-4 text-muted small">
                                        <i class="bi bi-file-earmark-text me-1 text-primary"></i>
                                        Document #{{ $docIndex + 1 }}
                                        @if($multiplicite === 'une_fois')
                                            <span class="badge bg-light text-secondary border ms-1" style="font-size:.62rem;">unique</span>
                                        @endif
                                    </td>
                                    <td class="small text-muted">—</td>
                                    <td class="small text-muted">
                                        {{ $performer ? $performer->prenom . ' ' . $performer->nom : '—' }}
                                    </td>
                                    <td class="small text-muted">
                                        {{ $doc->date_performed ? \Carbon\Carbon::parse($doc->date_performed)->format('d/m/Y') : '—' }}
                                    </td>
                                    <td class="small text-muted">
                                        {{ $doc->date_upload ? \Carbon\Carbon::parse($doc->date_upload)->format('d/m/Y') : '—' }}
                                    </td>
                                    <td class="text-center">
                                        @if($doc->document_file_path)
                                            <a href="{{ asset('storage/' . $doc->document_file_path) }}"
                                               target="_blank"
                                               class="btn btn-sm btn-outline-success py-0 px-2"
                                               title="Voir le document">
                                                <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                                            </a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($insp)
                                            <span class="badge bg-primary pd-insp-badge" title="{{ $insp->inspection_name }}">
                                                <i class="bi bi-clipboard2-check me-1"></i>
                                                {{ $insp->date_scheduled ? \Carbon\Carbon::parse($insp->date_scheduled)->format('d/m/Y') : 'Planifiée' }}
                                            </span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(!$isDone)
                                        {{-- Edit button for ALL multiplicite types --}}
                                        <button class="btn btn-sm btn-outline-primary py-0 px-2 me-1 pd-update-btn"
                                                title="Mettre à jour"
                                                data-record-id="{{ $activity->id }}"
                                                data-doc-id="{{ $doc->id }}"
                                                data-activity-name="{{ $actDef?->nom_activite }}"
                                                data-multiplicite="{{ $multiplicite }}"
                                                data-date-performed="{{ $doc->date_performed ?? '' }}"
                                                data-prev-date="{{ $prevDate ?? '' }}"
                                                data-next-date="{{ $nextDate ?? '' }}"
                                                data-file-path="{{ $doc->document_file_path ? asset('storage/'.$doc->document_file_path) : '' }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger py-0 px-2 pd-delete-doc-btn"
                                                title="Supprimer ce document"
                                                data-doc-id="{{ $doc->id }}"
                                                data-activity-name="{{ $actDef?->nom_activite }}">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">Aucune activité trouvée.</td>
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

    // ── Open modal for first submission ──────────────────────────
    document.querySelectorAll('.pd-upload-btn').forEach(btn => {
        btn.addEventListener('click', () => openPdModal(btn, false));
    });

    // ── Open modal for update (all multiplicite types) ───────────
    document.querySelectorAll('.pd-update-btn').forEach(btn => {
        btn.addEventListener('click', () => openPdModal(btn, true));
    });

    function openPdModal(btn, isUpdate) {
        const recordId   = btn.dataset.recordId;
        const docId      = btn.dataset.docId ?? '';
        const actName    = btn.dataset.activityName;
        const prevDate   = btn.dataset.prevDate;
        const nextDate   = btn.dataset.nextDate;
        const datePerf   = btn.dataset.datePerformed ?? '';
        const filePath   = btn.dataset.filePath ?? '';

        document.getElementById('pdRecordId').value      = recordId;
        document.getElementById('pdDocId').value         = docId;
        document.getElementById('pdActivityName').value  = actName;
        document.getElementById('pdDatePerformed').value = datePerf;
        document.getElementById('pdDocumentFile').value  = '';
        document.getElementById('pdModalError').classList.add('d-none');

        document.getElementById('pdModalTitleText').textContent = isUpdate
            ? 'Mettre à jour le document'
            : 'Soumettre un document';

        document.getElementById('pdFileRequired').textContent = isUpdate ? '' : '*';

        let hint = '';
        if (prevDate) hint += `Après le ${prevDate}`;
        if (nextDate) hint += (hint ? ' — ' : '') + `Avant le ${nextDate}`;
        document.getElementById('pdDateHint').textContent = hint;

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
        const errDiv    = document.getElementById('pdModalError');
        const recordId  = document.getElementById('pdRecordId').value;
        const datePerf  = document.getElementById('pdDatePerformed').value;
        const fileInput = document.getElementById('pdDocumentFile');
        const hasExisting = !document.getElementById('pdCurrentDocBanner').classList.contains('d-none');

        errDiv.classList.add('d-none');

        if (!datePerf) {
            errDiv.textContent = 'La date de réalisation est obligatoire.';
            errDiv.classList.remove('d-none');
            return;
        }
        if (!hasExisting && fileInput.files.length === 0) {
            errDiv.textContent = 'Un fichier PDF est obligatoire pour la soumission.';
            errDiv.classList.remove('d-none');
            return;
        }

        const btn = document.getElementById('pdSaveBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Enregistrement…';

        const docId = document.getElementById('pdDocId').value;

        const fd = new FormData();
        fd.append('_token', CSRF);
        fd.append('protocol_dev_activity_project_id', recordId);
        fd.append('date_performed', datePerf);
        if (docId) fd.append('doc_id', docId);
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

    // ── Delete a single document entry ────────────────────────────
    document.querySelectorAll('.pd-delete-doc-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const name  = btn.dataset.activityName;
            const docId = btn.dataset.docId;
            if (!confirm(`Supprimer ce document soumis pour « ${name} » ?`)) return;
            btn.disabled = true;

            const fd = new FormData();
            fd.append('_token', CSRF);
            fd.append('doc_id', docId);

            fetch('{{ route("deleteProtocolDevDocumentEntry") }}', { method: 'POST', body: fd })
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

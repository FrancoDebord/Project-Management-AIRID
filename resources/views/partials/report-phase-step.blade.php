@php
    $project_id  = request('project_id');
    $project     = App\Models\Pro_Project::find($project_id);
    $rp_documents = $project
        ? App\Models\Pro_ReportPhaseDocument::where('project_id', $project_id)
            ->with('qaInspection')
            ->orderByRaw('COALESCE(submission_date, signature_date, created_at) ASC')->get()
        : collect();

    $typeLabels = [
        'final_report'       => ['label' => 'Rapport Final',          'icon' => 'bi-file-earmark-text',  'color' => '#1a3a6b'],
        'report_amendment'   => ['label' => 'Amendement de rapport',  'icon' => 'bi-file-earmark-diff',  'color' => '#c41230'],
        'scientific_article' => ['label' => 'Article Scientifique',   'icon' => 'bi-journal-bookmark',   'color' => '#198754'],
        'publication_link'   => ['label' => 'Lien de Publication',    'icon' => 'bi-link-45deg',         'color' => '#0d6efd'],
        'shared_data'        => ['label' => 'Données Partagées',      'icon' => 'bi-database-fill-share','color' => '#6f42c1'],
        'other'              => ['label' => 'Autre Document',         'icon' => 'bi-paperclip',          'color' => '#6c757d'],
    ];

    $statusLabels = [
        'draft'     => ['label' => 'Brouillon',  'class' => 'bg-secondary'],
        'submitted' => ['label' => 'Soumis',     'class' => 'bg-warning text-dark'],
        'published' => ['label' => 'Publié',     'class' => 'bg-success'],
    ];
@endphp

<style>
    .airid-report {
        --rp-brand: #1a3a6b;
        --rp-accent: #c41230;
    }
    .airid-report .rp-card-header {
        background: linear-gradient(135deg, var(--rp-brand) 0%, #2a5aaa 100%);
        color: #fff;
        border-radius: .75rem .75rem 0 0;
        padding: .65rem 1rem;
        font-weight: 600;
        font-size: .9rem;
    }
    .airid-report .btn-rp-primary {
        background: var(--rp-brand);
        color: #fff;
        border: none;
    }
    .airid-report .btn-rp-primary:hover { background: #2a5aaa; color: #fff; }
    .airid-report .doc-type-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: .78rem;
        padding: 3px 10px;
        border-radius: 20px;
        font-weight: 600;
        color: #fff;
    }
    .airid-report .doc-row:hover { background: #f4f6fb; }
    .airid-report .summary-card {
        border-left: 4px solid var(--rp-brand);
        border-radius: .5rem;
        padding: .6rem 1rem;
        background: #f8f9ff;
        font-size: .85rem;
    }
    .airid-report .summary-card .summary-count {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--rp-brand);
    }
</style>

<div class="row airid-report g-3 mt-1">

    {{-- ── HEADER ── --}}
    <div class="col-12 mb-2 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h5 class="mb-0 fw-bold" style="color:var(--rp-brand);">
                <i class="bi bi-file-earmark-richtext me-2"></i>Report Phase — Documents de fin d'étude
            </h5>
            <small class="text-muted">
                Rapport, articles, publications, données partagées —
                <strong>{{ $project?->project_code ?? '' }}</strong>
            </small>
        </div>
        <button class="btn btn-rp-primary px-4 py-2" onclick="openRpModal()">
            <i class="bi bi-plus-circle me-1"></i> Ajouter un document
        </button>
    </div>

    {{-- ── SUMMARY CARDS ── --}}
    <div class="col-12">
        <div class="row g-2 mb-2">
            @foreach($typeLabels as $typeKey => $typeInfo)
            @php $count = $rp_documents->where('document_type', $typeKey)->count(); @endphp
            <div class="col-6 col-md-4 col-lg-2">
                <div class="summary-card d-flex align-items-center gap-3" style="border-left-color:{{ $typeInfo['color'] }};">
                    <i class="bi {{ $typeInfo['icon'] }} fs-4" style="color:{{ $typeInfo['color'] }};"></i>
                    <div>
                        <div class="summary-count" style="color:{{ $typeInfo['color'] }};">{{ $count }}</div>
                        <div class="text-muted" style="font-size:.72rem; line-height:1.2;">{{ $typeInfo['label'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── TABLE ── --}}
    <div class="col-12">
        <div class="card rounded-4 border-0 shadow-sm">
            <div class="rp-card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-collection me-2"></i>Documents soumis
                    <span class="badge bg-white text-primary ms-2">{{ $rp_documents->count() }}</span>
                </span>
            </div>
            <div class="card-body p-0">
                @if($rp_documents->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-folder2-open fs-2 d-block mb-2"></i>
                        Aucun document soumis pour ce projet.<br>
                        <small>Utilisez le bouton "Ajouter un document" pour commencer.</small>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="rpDocTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:4%">#</th>
                                    <th style="width:14%">Type</th>
                                    <th>Titre</th>
                                    <th style="width:10%">Statut</th>
                                    <th style="width:11%">Date signature</th>
                                    <th style="width:5%" class="text-center">QA</th>
                                    <th style="width:14%">Lien / Fichier</th>
                                    <th style="width:8%" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rp_documents as $i => $doc)
                                <tr class="doc-row" id="rp-doc-{{ $doc->id }}">
                                    <td class="text-muted small">{{ $i + 1 }}</td>
                                    <td>
                                        @php $ti = $typeLabels[$doc->document_type] ?? $typeLabels['other']; @endphp
                                        <span class="doc-type-badge" style="background:{{ $ti['color'] }};">
                                            <i class="bi {{ $ti['icon'] }}"></i>{{ $ti['label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $doc->title }}</div>
                                        @if($doc->description)
                                            <div class="text-muted small">{{ Str::limit($doc->description, 80) }}</div>
                                        @endif
                                        @if($doc->doi)
                                            <div class="small text-muted">DOI : {{ $doc->doi }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @php $si = $statusLabels[$doc->status] ?? ['label' => $doc->status, 'class' => 'bg-secondary']; @endphp
                                        <span class="badge {{ $si['class'] }}">{{ $si['label'] }}</span>
                                    </td>
                                    <td class="small text-muted">
                                        {{ $doc->signature_date ? \Carbon\Carbon::parse($doc->signature_date)->format('d/m/Y') : '—' }}
                                    </td>
                                    <td class="text-center">
                                        @if($doc->qaInspection)
                                            @php $insp = $doc->qaInspection; @endphp
                                            <span class="badge bg-primary"
                                                  style="font-size:.68rem;cursor:default;white-space:normal;max-width:130px;line-height:1.3;"
                                                  title="{{ $insp->inspection_name ?? $insp->type_inspection }}">
                                                <i class="bi bi-clipboard2-check me-1"></i>
                                                {{ $insp->date_scheduled ? \Carbon\Carbon::parse($insp->date_scheduled)->format('d/m/Y') : 'Planifiée' }}
                                            </span>
                                            <div class="text-muted" style="font-size:.65rem;line-height:1.2;margin-top:2px;">
                                                {{ \Str::limit($insp->type_inspection, 30) }}
                                            </div>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($doc->url)
                                            <a href="{{ $doc->url }}" target="_blank" class="btn btn-outline-primary btn-sm py-0 px-2">
                                                <i class="bi bi-box-arrow-up-right me-1"></i>Ouvrir
                                            </a>
                                        @endif
                                        @if($doc->file_path)
                                            <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank"
                                               class="btn btn-outline-secondary btn-sm py-0 px-2">
                                                <i class="bi bi-download me-1"></i>Fichier
                                            </a>
                                        @endif
                                        @if(!$doc->url && !$doc->file_path)
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-outline-primary btn-sm py-0 px-2 me-1"
                                                onclick="editRpDoc({id:{{ $doc->id }},document_type:'{{ $doc->document_type }}',title:{{ json_encode($doc->title) }},description:{{ json_encode($doc->description ?? '') }},url:{{ json_encode($doc->url ?? '') }},doi:{{ json_encode($doc->doi ?? '') }},submission_date:'{{ $doc->submission_date ?? '' }}',signature_date:'{{ $doc->signature_date ?? '' }}',status:'{{ $doc->status }}'})"
                                                title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm py-0 px-2"
                                                onclick="deleteRpDoc({{ $doc->id }})"
                                                title="Supprimer">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════
     MODAL — Ajouter un document
═══════════════════════════════════════ --}}
<div class="modal fade" id="rpDocModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 overflow-hidden">
            <div class="modal-header" style="background:linear-gradient(135deg,#1a3a6b,#2a5aaa);color:#fff;">
                <h5 class="modal-title fw-bold" id="rpDocModalTitle">
                    <i class="bi bi-plus-circle me-2"></i>Ajouter un document — Report Phase
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="rpDocError" class="alert alert-danger d-none mb-3"></div>
                <input type="hidden" id="rpDocId" value="">

                <div class="row g-3">
                    {{-- Type --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Type de document <span class="text-danger">*</span></label>
                        <select class="form-select" id="rpDocType" onchange="rpToggleFields()">
                            <option value="">— Sélectionner —</option>
                            <option value="final_report">📄 Rapport Final</option>
                            <option value="report_amendment">📝 Amendement de rapport</option>
                            <option value="scientific_article">📖 Article Scientifique</option>
                            <option value="publication_link">🔗 Lien de Publication</option>
                            <option value="shared_data">🗄️ Données Partagées</option>
                            <option value="other">📎 Autre Document</option>
                        </select>
                    </div>

                    {{-- Statut --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Statut <span class="text-danger">*</span></label>
                        <select class="form-select" id="rpDocStatus">
                            <option value="draft">Brouillon</option>
                            <option value="submitted">Soumis</option>
                            <option value="published">Publié</option>
                        </select>
                    </div>

                    {{-- Titre --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">Titre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="rpDocTitle"
                               placeholder="Ex: Final Report – Cone Bioassay Study 2025">
                    </div>

                    {{-- Description --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea class="form-control" id="rpDocDescription" rows="2"
                                  placeholder="Résumé ou notes sur ce document…"></textarea>
                    </div>

                    {{-- Fichier (masqué pour publication_link) --}}
                    <div class="col-md-6" id="rpFileWrapper">
                        <label class="form-label fw-semibold">Fichier</label>
                        <input type="file" class="form-control" id="rpDocFile"
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png">
                        <div class="form-text">PDF, Word, Excel, PPT, Image — max 20 Mo</div>
                    </div>

                    {{-- URL --}}
                    <div class="col-md-6" id="rpUrlWrapper">
                        <label class="form-label fw-semibold">URL / Lien</label>
                        <input type="url" class="form-control" id="rpDocUrl"
                               placeholder="https://…">
                    </div>

                    {{-- DOI (visible pour article scientifique) --}}
                    <div class="col-md-6 d-none" id="rpDoiWrapper">
                        <label class="form-label fw-semibold">DOI</label>
                        <input type="text" class="form-control" id="rpDocDoi"
                               placeholder="10.xxxx/xxxxxxx">
                    </div>

                    {{-- Date soumission : auto-renseignée à la date du jour, non visible --}}

                    {{-- Date signature (visible pour final_report et report_amendment) --}}
                    <div class="col-md-6 d-none" id="rpSignatureDateWrapper">
                        <label class="form-label fw-semibold" id="rpSignatureDateLabel">Date de signature</label>
                        <input type="date" class="form-control" id="rpDocSignatureDate">
                        <div class="form-text" id="rpSignatureDateHelp"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-rp-primary px-4" id="btnSaveRpDoc" onclick="saveRpDoc()">
                    <i class="bi bi-save me-1"></i>Enregistrer
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openRpModal() {
    document.getElementById('rpDocId').value              = '';
    document.getElementById('rpDocType').value            = '';
    document.getElementById('rpDocStatus').value          = 'draft';
    document.getElementById('rpDocTitle').value           = '';
    document.getElementById('rpDocDescription').value     = '';
    document.getElementById('rpDocFile').value            = '';
    document.getElementById('rpDocUrl').value             = '';
    document.getElementById('rpDocDoi').value             = '';
    document.getElementById('rpDocSignatureDate').value   = '';
    document.getElementById('rpDocError').classList.add('d-none');
    document.getElementById('rpDocModalTitle').innerHTML = '<i class="bi bi-plus-circle me-2"></i>Ajouter un document — Report Phase';
    document.getElementById('btnSaveRpDoc').innerHTML = '<i class="bi bi-save me-1"></i>Enregistrer';
    rpToggleFields();
    new bootstrap.Modal(document.getElementById('rpDocModal')).show();
}

function editRpDoc(doc) {
    document.getElementById('rpDocId').value              = doc.id;
    document.getElementById('rpDocType').value            = doc.document_type;
    document.getElementById('rpDocStatus').value          = doc.status;
    document.getElementById('rpDocTitle').value           = doc.title;
    document.getElementById('rpDocDescription').value     = doc.description;
    document.getElementById('rpDocFile').value            = '';
    document.getElementById('rpDocUrl').value             = doc.url;
    document.getElementById('rpDocDoi').value             = doc.doi;
    document.getElementById('rpDocSignatureDate').value   = doc.signature_date || '';
    document.getElementById('rpDocError').classList.add('d-none');
    document.getElementById('rpDocModalTitle').innerHTML = '<i class="bi bi-pencil me-2"></i>Modifier le document — Report Phase';
    document.getElementById('btnSaveRpDoc').innerHTML = '<i class="bi bi-save me-1"></i>Mettre à jour';
    rpToggleFields();
    new bootstrap.Modal(document.getElementById('rpDocModal')).show();
}

function rpToggleFields() {
    const type       = document.getElementById('rpDocType').value;
    const isLink     = type === 'publication_link';
    const isArticle  = type === 'scientific_article';
    const needsSig   = type === 'final_report' || type === 'report_amendment';

    // Fichier : masqué si c'est uniquement un lien
    document.getElementById('rpFileWrapper').classList.toggle('d-none', isLink);
    // DOI : visible pour articles
    document.getElementById('rpDoiWrapper').classList.toggle('d-none', !isArticle);
    // Signature date : visible pour final report et amendments
    document.getElementById('rpSignatureDateWrapper').classList.toggle('d-none', !needsSig);
    if (needsSig) {
        if (type === 'final_report') {
            const status = document.getElementById('rpDocStatus').value;
            document.getElementById('rpSignatureDateLabel').textContent =
                status === 'draft' ? 'Date de signature du Study Director' : 'Date de signature (toutes parties)';
            document.getElementById('rpSignatureDateHelp').textContent =
                status === 'draft' ? 'Date à laquelle le SD a signé le brouillon' : 'Date à laquelle toutes les parties ont signé le rapport final';
        } else {
            document.getElementById('rpSignatureDateLabel').textContent = 'Date de signature de l\'amendement';
            document.getElementById('rpSignatureDateHelp').textContent  = '';
        }
    }
}

// Update signature label when status changes
document.getElementById('rpDocStatus').addEventListener('change', rpToggleFields);

function saveRpDoc() {
    const docId       = document.getElementById('rpDocId').value;
    const isEdit      = docId !== '';
    const type          = document.getElementById('rpDocType').value;
    const title         = document.getElementById('rpDocTitle').value.trim();
    const status        = document.getElementById('rpDocStatus').value;
    const description   = document.getElementById('rpDocDescription').value.trim();
    const url           = document.getElementById('rpDocUrl').value.trim();
    const doi           = document.getElementById('rpDocDoi').value.trim();
    const signatureDate = document.getElementById('rpDocSignatureDate').value;
    const fileInput     = document.getElementById('rpDocFile');
    const errDiv      = document.getElementById('rpDocError');
    const btn         = document.getElementById('btnSaveRpDoc');
    const project_id  = "{{ request('project_id') }}";

    errDiv.classList.add('d-none');

    if (!type || !title) {
        errDiv.textContent = 'Le type et le titre sont obligatoires.';
        errDiv.classList.remove('d-none');
        return;
    }

    const fd = new FormData();
    fd.append('project_id',    project_id);
    fd.append('document_type', type);
    fd.append('title',         title);
    fd.append('status',        status);
    fd.append('description',   description);
    fd.append('url',           url);
    fd.append('doi',           doi);
    fd.append('signature_date', signatureDate);
    fd.append('_token',        document.querySelector('meta[name="csrf-token"]').content);
    if (fileInput.files.length > 0) {
        fd.append('file', fileInput.files[0]);
    }
    if (isEdit) {
        fd.append('document_id', docId);
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Enregistrement…';

    const endpoint = isEdit ? '/ajax/update-report-document' : '/ajax/save-report-document';

    fetch(endpoint, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = isEdit
                ? '<i class="bi bi-save me-1"></i>Mettre à jour'
                : '<i class="bi bi-save me-1"></i>Enregistrer';
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('rpDocModal')).hide();
                if (isEdit) {
                    location.reload();
                } else {
                    rpAddDocRow(data.document);
                }
            } else {
                errDiv.textContent = data.message || 'Une erreur est survenue.';
                errDiv.classList.remove('d-none');
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = isEdit
                ? '<i class="bi bi-save me-1"></i>Mettre à jour'
                : '<i class="bi bi-save me-1"></i>Enregistrer';
            errDiv.textContent = 'Erreur réseau.';
            errDiv.classList.remove('d-none');
        });
}

const rpTypeLabels = {
    final_report:       { label: 'Rapport Final',         icon: 'bi-file-earmark-text',   color: '#1a3a6b' },
    report_amendment:   { label: 'Amendement de rapport', icon: 'bi-file-earmark-diff',   color: '#c41230' },
    scientific_article: { label: 'Article Scientifique',  icon: 'bi-journal-bookmark',    color: '#198754' },
    publication_link:   { label: 'Lien de Publication',   icon: 'bi-link-45deg',          color: '#0d6efd' },
    shared_data:        { label: 'Données Partagées',     icon: 'bi-database-fill-share', color: '#6f42c1' },
    other:              { label: 'Autre Document',        icon: 'bi-paperclip',           color: '#6c757d' },
};
const rpStatusLabels = {
    draft:     { label: 'Brouillon', cls: 'bg-secondary' },
    submitted: { label: 'Soumis',    cls: 'bg-warning text-dark' },
    published: { label: 'Publié',    cls: 'bg-success' },
};

function rpAddDocRow(doc) {
    const tbody = document.querySelector('#rpDocTable tbody');
    // Retirer le message "aucun document" si présent
    const emptyDiv = document.querySelector('.airid-report .text-center.py-5');
    if (emptyDiv) location.reload(); // simple reload si c'était vide

    const ti = rpTypeLabels[doc.document_type] || rpTypeLabels['other'];
    const si = rpStatusLabels[doc.status] || { label: doc.status, cls: 'bg-secondary' };
    const rowCount = tbody ? tbody.querySelectorAll('tr').length + 1 : 1;

    let linkCell = '—';
    if (doc.url)       linkCell = `<a href="${doc.url}" target="_blank" class="btn btn-outline-primary btn-sm py-0 px-2"><i class="bi bi-box-arrow-up-right me-1"></i>Ouvrir</a>`;
    if (doc.file_path) linkCell = (linkCell === '—' ? '' : linkCell + ' ') + `<a href="${doc.file_path}" target="_blank" class="btn btn-outline-secondary btn-sm py-0 px-2"><i class="bi bi-download me-1"></i>Fichier</a>`;

    const tr = document.createElement('tr');
    tr.className = 'doc-row';
    tr.id = 'rp-doc-' + doc.id;
    tr.innerHTML = `
        <td class="text-muted small">${rowCount}</td>
        <td><span class="doc-type-badge" style="background:${ti.color};"><i class="bi ${ti.icon}"></i>${ti.label}</span></td>
        <td>
            <div class="fw-semibold">${doc.title}</div>
            ${doc.description ? `<div class="text-muted small">${doc.description.substring(0,80)}</div>` : ''}
            ${doc.doi ? `<div class="small text-muted">DOI : ${doc.doi}</div>` : ''}
        </td>
        <td><span class="badge ${si.cls}">${si.label}</span></td>
        <td class="small text-muted">${doc.submission_date ?? '—'}</td>
        <td>${linkCell}</td>
        <td class="text-center">
            <button class="btn btn-outline-danger btn-sm py-0 px-2" onclick="deleteRpDoc(${doc.id})" title="Supprimer">
                <i class="bi bi-trash3"></i>
            </button>
        </td>`;
    if (tbody) tbody.prepend(tr);
}

function deleteRpDoc(docId) {
    if (!confirm('Supprimer ce document ?')) return;
    const fd = new FormData();
    fd.append('document_id', docId);
    fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);

    fetch('/ajax/delete-report-document', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const row = document.getElementById('rp-doc-' + docId);
                if (row) row.remove();
            } else {
                alert(data.message || 'Erreur lors de la suppression.');
            }
        })
        .catch(() => alert('Erreur réseau.'));
}
</script>

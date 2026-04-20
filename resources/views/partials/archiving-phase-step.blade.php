@php
    use App\Models\Pro_Project;
    use App\Models\Pro_StudyActivities;
    use App\Models\Pro_QaInspection;
    use App\Models\Pro_QaInspectionFinding;
    use App\Models\Pro_ReportPhaseDocument;
    use App\Models\Pro_ArchivingDocument;

    $project_id = request('project_id');
    $project    = Pro_Project::find($project_id);

    $arch_docs  = $project ? Pro_ArchivingDocument::where('project_id', $project_id)->orderBy('created_at','desc')->get() : collect();

    // ── Auto-checklist items ──────────────────────────────────
    $allActivities       = $project ? Pro_StudyActivities::where('project_id', $project_id)->get() : collect();
    $criticalActivities  = $allActivities->where('phase_critique', 1);
    $allActDone          = $allActivities->count() > 0 && $allActivities->every(fn($a) => $a->status === 'completed');
    $allCritActDone      = $criticalActivities->count() > 0 && $criticalActivities->every(fn($a) => $a->status === 'completed');

    $inspectedActivityIds = Pro_QaInspection::where('project_id', $project_id)->whereNotNull('activity_id')->pluck('activity_id')->toArray();
    $uninspectedCritical  = $criticalActivities->filter(fn($a) => !in_array($a->id, $inspectedActivityIds));
    $allCritInspected     = $criticalActivities->count() > 0 && $uninspectedCritical->count() === 0;

    $pendingFindings = $project ? Pro_QaInspectionFinding::where('project_id', $project_id)->where('status','pending')->where('is_conformity', 0)->count() : 0;
    $allFindingsResolved = $pendingFindings === 0;

    $hasReport = $project ? Pro_ReportPhaseDocument::where('project_id', $project_id)->where('document_type','final_report')->exists() : false;

    // Manual checklist keys & labels
    $manualItems = [
        'physical_docs_archived' => 'Physical documents have been archived in the archive room',
        'raw_data_secured'       => 'Raw data have been secured and stored',
        'electronic_backup'      => 'Electronic backup has been completed',
        'samples_stored'         => 'Samples/specimens have been properly stored or disposed',
        'personnel_notified'     => 'All involved personnel have been notified of project closure',
    ];

    $savedChecklist = $project && $project->archive_checklist ? $project->archive_checklist : [];
    $isArchived     = $project && $project->archived_at;
    $archivedByUser = $isArchived && $project->archived_by ? \App\Models\User::find($project->archived_by) : null;
    // Legacy projects always keep forms editable even when archived
    $editLocked     = $isArchived && !($project && $project->is_legacy);
@endphp

<style>
    .airid-archiving {
        --arch-brand: #1a3a6b;
        --arch-accent: #c41230;
    }
    .airid-archiving .arch-card-header {
        background: linear-gradient(135deg, var(--arch-brand) 0%, #2a5aaa 100%);
        color: #fff;
        border-radius: .75rem .75rem 0 0;
        padding: .65rem 1rem;
        font-weight: 600;
        font-size: .9rem;
    }
    .airid-archiving .btn-arch-primary {
        background: var(--arch-brand);
        color: #fff;
        border: none;
    }
    .airid-archiving .btn-arch-primary:hover { background: #2a5aaa; color: #fff; }
    .airid-archiving .checklist-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: .45rem .75rem;
        border-radius: .4rem;
        background: #f8f9ff;
        border-left: 4px solid #dee2e6;
        font-size: .88rem;
    }
    .airid-archiving .checklist-item.ok   { border-left-color: #198754; background: #f0fff4; }
    .airid-archiving .checklist-item.nok  { border-left-color: #dc3545; background: #fff5f5; }
    .airid-archiving .checklist-item.warn { border-left-color: #f0ad4e; background: #fffbf0; }
    .airid-archiving .arch-locked-banner {
        background: linear-gradient(135deg, #1a3a6b 0%, #c41230 100%);
        color: #fff;
        border-radius: .75rem;
        padding: 1rem 1.5rem;
    }
    .airid-archiving .arch-doc-row:hover { background: #f4f6fb; }
</style>

<div class="airid-archiving p-2">

    @if(!$project)
        <div class="alert alert-info mt-3">Please select a project.</div>
    @else

    {{-- ── ARCHIVED BANNER ───────────────────────────────────── --}}
    @if($isArchived)
    <div class="arch-locked-banner d-flex align-items-center justify-content-between mb-4">
        <div>
            <i class="bi bi-lock-fill fs-4 me-2"></i>
            <strong>This project is ARCHIVED</strong>
            <div class="small mt-1 opacity-75">
                Archived on {{ \Carbon\Carbon::parse($project->archived_at)->format('d/m/Y H:i') }}
                @if($archivedByUser) by {{ $archivedByUser->name }} @endif
            </div>
        </div>
        <button class="btn btn-sm btn-light fw-semibold" onclick="unarchiveProject()">
            <i class="bi bi-unlock-fill me-1"></i>Unarchive
        </button>
    </div>
    @endif

    {{-- ── AUTO CHECKLIST ────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="arch-card-header">
            <i class="bi bi-clipboard2-check me-2"></i>Pre-Archiving Checklist
        </div>
        <div class="card-body">
            <p class="text-muted small mb-3">The following items are computed automatically. Complete any pending items before archiving.</p>
            <div class="d-flex flex-column gap-2 mb-4">
                {{-- All activities completed --}}
                <div class="checklist-item {{ $allActDone ? 'ok' : 'nok' }}">
                    <i class="bi {{ $allActDone ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' }} fs-5 flex-shrink-0"></i>
                    <div>
                        <strong>All study activities completed</strong>
                        <div class="text-muted small">
                            @if($allActivities->count() === 0) No activities recorded.
                            @elseif($allActDone) All {{ $allActivities->count() }} activities are completed.
                            @else {{ $allActivities->where('status','completed')->count() }} / {{ $allActivities->count() }} completed.
                            @endif
                        </div>
                    </div>
                </div>
                {{-- All critical activities inspected --}}
                <div class="checklist-item {{ $allCritInspected ? 'ok' : ($criticalActivities->count() === 0 ? 'warn' : 'nok') }}">
                    <i class="bi {{ $allCritInspected ? 'bi-check-circle-fill text-success' : ($criticalActivities->count() === 0 ? 'bi-dash-circle-fill text-warning' : 'bi-x-circle-fill text-danger') }} fs-5 flex-shrink-0"></i>
                    <div>
                        <strong>All critical activities have a QA inspection</strong>
                        <div class="text-muted small">
                            @if($criticalActivities->count() === 0) No critical activities recorded.
                            @elseif($allCritInspected) All {{ $criticalActivities->count() }} critical activities have been inspected.
                            @else {{ $uninspectedCritical->count() }} critical {{ Str::plural('activity', $uninspectedCritical->count()) }} without inspection.
                            @endif
                        </div>
                    </div>
                </div>
                {{-- All findings resolved --}}
                <div class="checklist-item {{ $allFindingsResolved ? 'ok' : 'nok' }}">
                    <i class="bi {{ $allFindingsResolved ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' }} fs-5 flex-shrink-0"></i>
                    <div>
                        <strong>All QA findings resolved</strong>
                        <div class="text-muted small">
                            @if($allFindingsResolved) No pending findings.
                            @else {{ $pendingFindings }} unresolved {{ Str::plural('finding', $pendingFindings) }} remaining.
                            @endif
                        </div>
                    </div>
                </div>
                {{-- Final report available --}}
                <div class="checklist-item {{ $hasReport ? 'ok' : 'warn' }}">
                    <i class="bi {{ $hasReport ? 'bi-check-circle-fill text-success' : 'bi-exclamation-circle-fill text-warning' }} fs-5 flex-shrink-0"></i>
                    <div>
                        <strong>Final report available (Report Phase)</strong>
                        <div class="text-muted small">
                            @if($hasReport) A final report document has been uploaded.
                            @else No final report uploaded yet.
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Manual checklist --}}
            <hr>
            <p class="fw-semibold small mb-2">Manual confirmations</p>
            <div class="d-flex flex-column gap-2 mb-3" id="manualChecklistContainer">
                @foreach($manualItems as $key => $label)
                @php $checked = in_array($key, (array)$savedChecklist); @endphp
                <label class="checklist-item {{ $checked ? 'ok' : '' }} user-select-none" style="cursor:pointer;">
                    <input type="checkbox"
                           class="manual-check form-check-input flex-shrink-0"
                           data-key="{{ $key }}"
                           {{ $editLocked ? 'disabled' : '' }}
                           {{ $checked ? 'checked' : '' }}
                           style="width:1.1rem;height:1.1rem;">
                    <span>{{ $label }}</span>
                </label>
                @endforeach
            </div>

            {{-- Archive submission date + deposition form --}}
            <hr>
            <p class="fw-semibold small mb-2">Archive Deposition</p>
            <div class="row g-3 mb-3" id="archiveDepositionSection">
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Date — all study documents submitted to archivist</label>
                    <input type="date" class="form-control form-control-sm" id="archSubmissionDate"
                           value="{{ $project->archive_submission_date ?? '' }}"
                           {{ $editLocked ? 'disabled' : '' }}>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Archive Deposition Form &amp; Study Checklist</label>
                    @if($project->archive_deposition_form_path)
                        <div class="mb-1">
                            <a href="{{ asset('storage/' . $project->archive_deposition_form_path) }}" target="_blank"
                               class="btn btn-outline-secondary btn-sm py-0 px-2">
                                <i class="bi bi-download me-1"></i>Download current file
                            </a>
                        </div>
                    @endif
                    @if(!$editLocked)
                        <input type="file" class="form-control form-control-sm" id="archDepositionFile"
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip">
                        <div class="form-text">PDF, Word, Excel, Image, ZIP — max 30 MB</div>
                    @endif
                </div>
                @if(!$editLocked)
                <div class="col-12">
                    <button class="btn btn-sm btn-outline-primary fw-semibold" onclick="saveArchiveDeposition()" id="archDepositionSaveBtn">
                        <i class="bi bi-save me-1"></i>Save Deposition Info
                    </button>
                    <span id="archDepositionMsg" class="ms-2 small"></span>
                </div>
                @endif
            </div>

            {{-- Archive / Unarchive button --}}
            @if(!$isArchived)
            <div class="d-flex align-items-center gap-3 mt-3">
                <button class="btn btn-arch-primary fw-semibold px-4" onclick="archiveProject()">
                    <i class="bi bi-archive-fill me-2"></i>Archive this Project
                </button>
                <span class="text-muted small">Archiving will lock all activities and inspections.</span>
            </div>
            @endif
        </div>
    </div>

    {{-- ── ARCHIVING DOCUMENTS ───────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="arch-card-header d-flex align-items-center justify-content-between">
            <span><i class="bi bi-folder2-open me-2"></i>Archiving Documents</span>
            @if(!$editLocked)
            <button class="btn btn-sm btn-light fw-semibold" onclick="openArchDocModal()">
                <i class="bi bi-plus-circle me-1"></i>Add Document
            </button>
            @endif
        </div>
        <div class="card-body p-0">
            @if($arch_docs->isEmpty())
                <p class="text-muted small text-center py-4 mb-0">No archiving documents uploaded yet.</p>
            @else
            <table class="table table-sm table-hover mb-0" id="archDocTable">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Physical Location</th>
                        <th>Archive Date</th>
                        <th>File</th>
                        @if(!$editLocked)<th></th>@endif
                    </tr>
                </thead>
                <tbody id="archDocTbody">
                    @foreach($arch_docs as $doc)
                    <tr class="arch-doc-row" data-id="{{ $doc->id }}">
                        <td class="align-middle">{{ $doc->title }}</td>
                        <td class="align-middle text-muted small">{{ $doc->document_type ?? '—' }}</td>
                        <td class="align-middle text-muted small">{{ $doc->physical_location ?? '—' }}</td>
                        <td class="align-middle text-muted small">{{ $doc->archive_date ? \Carbon\Carbon::parse($doc->archive_date)->format('d/m/Y') : '—' }}</td>
                        <td class="align-middle">
                            @if($doc->file_path)
                                <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="btn btn-xs btn-outline-secondary btn-sm py-0 px-2">
                                    <i class="bi bi-download"></i>
                                </a>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        @if(!$editLocked)
                        <td class="align-middle">
                            <button class="btn btn-xs btn-outline-danger btn-sm py-0 px-2" onclick="deleteArchDoc({{ $doc->id }}, this)">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>

    @endif {{-- end @if($project) --}}
</div>

{{-- ── ADD ARCHIVING DOCUMENT MODAL ──────────────────────────── --}}
<div class="modal fade" id="archDocModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Archiving Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="archDocErr" class="alert alert-danger d-none small py-2 mb-3"></div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="archDocTitle" maxlength="255">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Document Type</label>
                        <input type="text" class="form-control form-control-sm" id="archDocType" placeholder="e.g. Study Raw Data, Final Protocol…" maxlength="100">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-semibold small">Description</label>
                        <textarea class="form-control form-control-sm" id="archDocDesc" rows="2" maxlength="2000"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Physical Location</label>
                        <input type="text" class="form-control form-control-sm" id="archDocLocation" placeholder="e.g. Archive Room, Shelf B3" maxlength="255">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Archive Date</label>
                        <input type="date" class="form-control form-control-sm" id="archDocDate">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-semibold small">File (optional)</label>
                        <input type="file" class="form-control form-control-sm" id="archDocFile" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip">
                        <div class="form-text">PDF, Word, Excel, Image, or ZIP — max 30 MB</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-arch-primary btn-sm fw-semibold" onclick="saveArchDoc()" id="archDocSaveBtn">
                    <i class="bi bi-save me-1"></i>Save Document
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function(){
    const PROJECT_ID = {{ $project ? $project->id : 'null' }};
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    // ── Manual checklist ──────────────────────────────────────
    document.querySelectorAll('.manual-check').forEach(cb => {
        cb.addEventListener('change', () => saveChecklist());
    });

    function getCheckedKeys() {
        return [...document.querySelectorAll('.manual-check:checked')].map(c => c.dataset.key);
    }

    function saveChecklist() {
        fetch('{{ route('saveArchiveChecklist') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ project_id: PROJECT_ID, checklist: getCheckedKeys() })
        }).then(r => r.json()).then(data => {
            if (!data.success) console.warn('Checklist save failed:', data.message);
            // Update visual state of labels
            document.querySelectorAll('.manual-check').forEach(cb => {
                cb.closest('label').classList.toggle('ok', cb.checked);
            });
        });
    }

    // ── Archive project ───────────────────────────────────────
    window.archiveProject = function() {
        if (!confirm('Archive this project?\n\nThis will lock all activities and inspections. You can unarchive later if needed.')) return;
        fetch('{{ route('archiveProject') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ project_id: PROJECT_ID })
        }).then(r => r.json()).then(data => {
            if (data.success) location.reload();
            else alert('Error: ' + data.message);
        });
    };

    // ── Unarchive project ─────────────────────────────────────
    window.unarchiveProject = function() {
        if (!confirm('Unarchive this project?\n\nThis will allow modifications again.')) return;
        fetch('{{ route('unarchiveProject') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ project_id: PROJECT_ID })
        }).then(r => r.json()).then(data => {
            if (data.success) location.reload();
            else alert('Error: ' + data.message);
        });
    };

    // ── Archive deposition (date + form upload) ───────────────
    window.saveArchiveDeposition = function() {
        const btn    = document.getElementById('archDepositionSaveBtn');
        const msgEl  = document.getElementById('archDepositionMsg');
        const date   = document.getElementById('archSubmissionDate')?.value ?? '';
        const file   = document.getElementById('archDepositionFile')?.files[0];

        btn.disabled = true;
        msgEl.textContent = '';

        const fd = new FormData();
        fd.append('project_id',              PROJECT_ID);
        fd.append('archive_submission_date', date);
        if (file) fd.append('file', file);

        fetch('{{ route('saveArchiveSubmission') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: fd
        }).then(r => r.json()).then(data => {
            btn.disabled = false;
            if (data.success) {
                msgEl.className = 'ms-2 small text-success';
                msgEl.textContent = '✔ Saved.';
                if (data.archive_deposition_form_path) {
                    // Refresh the download link area
                    const fileInput = document.getElementById('archDepositionFile');
                    if (fileInput) fileInput.value = '';
                }
            } else {
                msgEl.className = 'ms-2 small text-danger';
                msgEl.textContent = data.message || 'Error saving.';
            }
        }).catch(() => {
            btn.disabled = false;
            msgEl.className = 'ms-2 small text-danger';
            msgEl.textContent = 'Network error.';
        });
    };

    // ── Archiving documents modal ─────────────────────────────
    window.openArchDocModal = function() {
        document.getElementById('archDocTitle').value    = '';
        document.getElementById('archDocType').value     = '';
        document.getElementById('archDocDesc').value     = '';
        document.getElementById('archDocLocation').value = '';
        document.getElementById('archDocDate').value     = '';
        document.getElementById('archDocFile').value     = '';
        document.getElementById('archDocErr').classList.add('d-none');
        new bootstrap.Modal(document.getElementById('archDocModal')).show();
    };

    window.saveArchDoc = function() {
        const errDiv = document.getElementById('archDocErr');
        const title  = document.getElementById('archDocTitle').value.trim();
        if (!title) {
            errDiv.textContent = 'Title is required.';
            errDiv.classList.remove('d-none');
            return;
        }
        errDiv.classList.add('d-none');
        const btn = document.getElementById('archDocSaveBtn');
        btn.disabled = true;

        const fd = new FormData();
        fd.append('project_id',        PROJECT_ID);
        fd.append('title',             title);
        fd.append('description',       document.getElementById('archDocDesc').value.trim());
        fd.append('document_type',     document.getElementById('archDocType').value.trim());
        fd.append('physical_location', document.getElementById('archDocLocation').value.trim());
        fd.append('archive_date',      document.getElementById('archDocDate').value);
        const fileInput = document.getElementById('archDocFile');
        if (fileInput.files[0]) fd.append('file', fileInput.files[0]);

        fetch('{{ route('saveArchivingDocument') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: fd
        }).then(r => r.json()).then(data => {
            btn.disabled = false;
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('archDocModal')).hide();
                archDocAddRow(data.document);
            } else {
                errDiv.textContent = data.message;
                errDiv.classList.remove('d-none');
            }
        }).catch(() => { btn.disabled = false; errDiv.textContent = 'Network error.'; errDiv.classList.remove('d-none'); });
    };

    function archDocAddRow(doc) {
        const tbody = document.getElementById('archDocTbody');
        if (!tbody) { location.reload(); return; }

        // Remove "no documents" message if present
        const emptyMsg = document.querySelector('.airid-archiving .text-center.py-4');
        if (emptyMsg) emptyMsg.closest('p')?.remove();

        // If table doesn't exist yet, reload to get it properly rendered
        const table = document.getElementById('archDocTable');
        if (!table) { location.reload(); return; }

        const fileCell = doc.file_path
            ? `<a href="${doc.file_path}" target="_blank" class="btn btn-xs btn-outline-secondary btn-sm py-0 px-2"><i class="bi bi-download"></i></a>`
            : '<span class="text-muted small">—</span>';

        const tr = document.createElement('tr');
        tr.className = 'arch-doc-row';
        tr.dataset.id = doc.id;
        tr.innerHTML = `
            <td class="align-middle">${doc.title}</td>
            <td class="align-middle text-muted small">${doc.document_type || '—'}</td>
            <td class="align-middle text-muted small">${doc.physical_location || '—'}</td>
            <td class="align-middle text-muted small">${doc.archive_date || '—'}</td>
            <td class="align-middle">${fileCell}</td>
            <td class="align-middle">
                <button class="btn btn-xs btn-outline-danger btn-sm py-0 px-2" onclick="deleteArchDoc(${doc.id}, this)">
                    <i class="bi bi-trash"></i>
                </button>
            </td>`;
        tbody.prepend(tr);
    }

    window.deleteArchDoc = function(docId, btn) {
        if (!confirm('Delete this archiving document?')) return;
        btn.disabled = true;
        fetch('{{ route('deleteArchivingDocument') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ document_id: docId })
        }).then(r => r.json()).then(data => {
            btn.disabled = false;
            if (data.success) {
                btn.closest('tr').remove();
            } else {
                alert('Error: ' + data.message);
            }
        });
    };
})();
</script>

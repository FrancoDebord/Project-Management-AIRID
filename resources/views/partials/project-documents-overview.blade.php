{{--
    Project Documents Overview
    Variables required:
      $project               — Pro_Project (with relations loaded)
      $qaStatement           — Pro_QaStatement|null
      $canDownloadAll        — bool (super_admin, facility_manager)
      $canDownloadQA         — bool (super_admin, facility_manager, qa_manager)
      $canDownloadProject    — bool (super_admin, facility_manager, study_director of this project)
--}}

@php
/* ──────────────────────────────────────────────────────────────
   Helper: build a normalised document list per category.
   Each document entry: ['title', 'url', 'date', 'type', 'canDownload']
────────────────────────────────────────────────────────────── */
$assetUrl = fn($path) => $path ? asset('storage/' . ltrim($path, '/')) : null;

$categories = [];

// ── 1. Study Creation ────────────────────────────────────────
$studyCreationDocs = [];

$sdForm = $project->studyDirectorAppointmentForm;
if ($sdForm && $sdForm->sd_appointment_file) {
    $studyCreationDocs[] = [
        'title'       => 'SD Appointment Letter',
        'url'         => $assetUrl($sdForm->sd_appointment_file),
        'date'        => $sdForm->sd_appointment_date,
        'type'        => 'appointment',
        'canDownload' => $canDownloadProject,
    ];
}
foreach ($project->otherBasicDocuments as $doc) {
    if ($doc->document_file_path) {
        $studyCreationDocs[] = [
            'title'       => $doc->titre_document,
            'url'         => $assetUrl($doc->document_file_path),
            'date'        => $doc->upload_date,
            'type'        => $doc->document_type ?? 'document',
            'canDownload' => $canDownloadProject,
        ];
    }
}
if ($studyCreationDocs) {
    $categories[] = [
        'label'    => 'Study Creation',
        'icon'     => 'bi-file-earmark-person',
        'color'    => '#1a3a6b',
        'bg'       => '#e8f0fe',
        'docs'     => $studyCreationDocs,
    ];
}

// ── 2. Protocol Development ──────────────────────────────────
$protoDocs = [];
foreach ($project->protocolDeveloppementActivitiesProject as $actProj) {
    foreach ($actProj->protocolDevDocuments as $doc) {
        if ($doc->document_file_path) {
            $label = $actProj->protocolDevActivity->activity_name ?? ('Protocol Dev #' . $actProj->level_activite);
            $protoDocs[] = [
                'title'       => $label,
                'url'         => $assetUrl($doc->document_file_path),
                'date'        => $doc->date_upload ?? $doc->date_performed,
                'type'        => 'protocol',
                'canDownload' => $canDownloadProject,
            ];
        }
    }
}
if ($protoDocs) {
    $categories[] = [
        'label'    => 'Protocol Development',
        'icon'     => 'bi-file-earmark-code',
        'color'    => '#2a5aaa',
        'bg'       => '#eef3fc',
        'docs'     => $protoDocs,
    ];
}

// ── 3. Quality Assurance ─────────────────────────────────────
if ($project->is_glp) {
    $qaDocs = [];
    if ($qaStatement) {
        // QA Statement is printable via route, no stored file
        $qaDocs[] = [
            'title'       => 'QA Statement — ' . $project->project_code,
            'url'         => route('printQaStatement', ['project_id' => $project->id]),
            'date'        => $qaStatement->date_signed,
            'type'        => 'qa_statement',
            'canDownload' => $canDownloadQA,
            'badge'       => $qaStatement->status === 'final' ? ['label' => 'Final', 'bg' => '#198754'] : ['label' => 'Draft', 'bg' => '#fd7e14'],
        ];
    }
    if ($qaDocs) {
        $categories[] = [
            'label'    => 'Quality Assurance',
            'icon'     => 'bi-shield-check',
            'color'    => '#6f42c1',
            'bg'       => '#f3eeff',
            'docs'     => $qaDocs,
        ];
    }
}

// ── 4. Report Phase ──────────────────────────────────────────
$reportDocs = [];
foreach ($project->reportPhaseDocuments as $doc) {
    if ($doc->file_path) {
        $reportDocs[] = [
            'title'       => $doc->title ?? $doc->document_type,
            'url'         => $assetUrl($doc->file_path),
            'date'        => $doc->submission_date,
            'type'        => $doc->document_type ?? 'report',
            'canDownload' => $canDownloadProject,
            'badge'       => $doc->status ? ['label' => ucfirst($doc->status), 'bg' => $doc->status === 'submitted' ? '#198754' : '#ffc107'] : null,
        ];
    }
}
if ($reportDocs) {
    $categories[] = [
        'label'    => 'Report Phase',
        'icon'     => 'bi-file-earmark-text',
        'color'    => '#0d6efd',
        'bg'       => '#e7f1ff',
        'docs'     => $reportDocs,
    ];
}

// ── 5. Archiving ─────────────────────────────────────────────
$archiveDocs = [];
foreach ($project->archivingDocuments as $doc) {
    if ($doc->file_path) {
        $archiveDocs[] = [
            'title'       => $doc->title ?? $doc->document_type,
            'url'         => $assetUrl($doc->file_path),
            'date'        => $doc->archive_date,
            'type'        => $doc->document_type ?? 'archive',
            'canDownload' => $canDownloadProject,
        ];
    }
}
if ($archiveDocs) {
    $categories[] = [
        'label'    => 'Archiving',
        'icon'     => 'bi-archive',
        'color'    => '#6c757d',
        'bg'       => '#f0f0f0',
        'docs'     => $archiveDocs,
    ];
}

$totalDocs = collect($categories)->sum(fn($c) => count($c['docs']));
@endphp

{{-- ── Document viewer modal (shared) ──────────────────────── --}}
<div class="modal fade" id="docViewerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:12px;overflow:hidden;">
            <div class="modal-header py-2 px-3 border-0" style="background:#1a3a6b;">
                <h6 class="modal-title text-white fw-semibold mb-0" id="docViewerTitle">
                    <i class="bi bi-file-earmark me-2"></i><span id="docViewerTitleText"></span>
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" style="height:80vh;">
                <div id="docViewerContent" style="width:100%;height:100%;"></div>
            </div>
            <div class="modal-footer border-0 py-2 px-3" style="background:#f8f9fa;">
                <a id="docViewerDownload" href="#" download
                   class="btn btn-sm fw-semibold d-none"
                   style="background:#1a3a6b;color:#fff;border-radius:6px;">
                    <i class="bi bi-download me-1"></i>Télécharger
                </a>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

{{-- ── Accordion section ────────────────────────────────────── --}}
<div class="accordion-item border-0 shadow-sm mb-3 rounded-3 overflow-hidden">
    <h2 class="accordion-header">
        <button class="accordion-button collapsed fw-semibold" type="button"
                data-bs-toggle="collapse" data-bs-target="#colDocs">
            <i class="bi bi-folder2-open me-2" style="color:#fd7e14;"></i>
            Project Documents
            @if($totalDocs > 0)
                <span class="ms-2 badge" style="background:#fd7e14;">{{ $totalDocs }}</span>
            @else
                <span class="ms-2 badge bg-secondary">No documents</span>
            @endif
            <span class="ms-2 small text-white-50" style="font-size:.72rem;">
                @if(!$canDownloadProject && !$canDownloadQA)
                    <i class="bi bi-eye ms-1"></i> Read only
                @endif
            </span>
        </button>
    </h2>
    <div id="colDocs" class="accordion-collapse collapse" data-bs-parent="#ovAccordion">
        <div class="accordion-body p-0">

            @if($totalDocs === 0)
                <p class="text-muted text-center py-4">No documents have been uploaded yet.</p>
            @else
            @foreach($categories as $cat)
            {{-- Category header --}}
            <div class="px-3 pt-3 pb-1">
                <div class="d-flex align-items-center gap-2 mb-2 pb-1"
                     style="border-bottom:2px solid {{ $cat['color'] }};">
                    <span style="background:{{ $cat['bg'] }};color:{{ $cat['color'] }};border-radius:6px;padding:4px 8px;font-size:.8rem;font-weight:600;">
                        <i class="bi {{ $cat['icon'] }} me-1"></i>{{ $cat['label'] }}
                    </span>
                    <span class="badge rounded-pill" style="background:{{ $cat['color'] }};font-size:.68rem;">
                        {{ count($cat['docs']) }}
                    </span>
                </div>

                @foreach($cat['docs'] as $doc)
                @php
                    $ext = strtolower(pathinfo(parse_url($doc['url'], PHP_URL_PATH), PATHINFO_EXTENSION));
                    $isViewable = in_array($ext, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']);
                    $viewIcon = in_array($ext, ['jpg','jpeg','png','gif','bmp','webp']) ? 'bi-image' : 'bi-file-earmark-pdf';
                @endphp
                <div class="d-flex align-items-center gap-2 py-2 px-1 mb-1 rounded-2"
                     style="background:#fafafa;border:1px solid #eee;">
                    {{-- Icon --}}
                    <div class="flex-shrink-0 text-center" style="width:32px;">
                        <i class="bi {{ $viewIcon }}" style="font-size:1.25rem;color:{{ $cat['color'] }};"></i>
                    </div>

                    {{-- Info --}}
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="fw-semibold text-truncate" style="font-size:.85rem;">{{ $doc['title'] }}</div>
                        <div class="d-flex flex-wrap gap-2 mt-1" style="font-size:.72rem;color:#6c757d;">
                            @if(!empty($doc['type']))
                                <span class="badge" style="background:{{ $cat['bg'] }};color:{{ $cat['color'] }};font-size:.68rem;">
                                    {{ $doc['type'] }}
                                </span>
                            @endif
                            @if(!empty($doc['badge']))
                                <span class="badge" style="background:{{ $doc['badge']['bg'] }};color:#fff;font-size:.68rem;">
                                    {{ $doc['badge']['label'] }}
                                </span>
                            @endif
                            @if(!empty($doc['date']))
                                <span><i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($doc['date'])->format('d/m/Y') }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex-shrink-0 d-flex gap-1">
                        @if($isViewable || str_ends_with(strtolower($doc['url']), '.pdf'))
                        <button type="button"
                                class="btn btn-sm doc-view-btn"
                                style="background:#e8f0fe;color:#1a3a6b;border:1px solid #c5d8f8;font-size:.75rem;padding:3px 9px;"
                                data-url="{{ $doc['url'] }}"
                                data-title="{{ $doc['title'] }}"
                                data-can-download="{{ $doc['canDownload'] ? '1' : '0' }}"
                                title="Visualiser">
                            <i class="bi bi-eye me-1"></i>View
                        </button>
                        @else
                        <a href="{{ $doc['url'] }}" target="_blank"
                           class="btn btn-sm"
                           style="background:#e8f0fe;color:#1a3a6b;border:1px solid #c5d8f8;font-size:.75rem;padding:3px 9px;"
                           title="Ouvrir">
                            <i class="bi bi-box-arrow-up-right me-1"></i>Open
                        </a>
                        @endif

                        @if($doc['canDownload'])
                        <a href="{{ $doc['url'] }}" download
                           class="btn btn-sm"
                           style="background:#1a3a6b;color:#fff;border:none;font-size:.75rem;padding:3px 9px;"
                           title="Télécharger">
                            <i class="bi bi-download me-1"></i>DL
                        </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endforeach
            @endif

            {{-- Permission note --}}
            <div class="px-3 py-2 mt-1 border-top d-flex align-items-center gap-2"
                 style="background:#f8f9fa;font-size:.72rem;color:#6c757d;">
                <i class="bi bi-info-circle"></i>
                @if($canDownloadAll)
                    <span>You can download <strong>all documents</strong>.</span>
                @elseif($canDownloadQA && $canDownloadProject)
                    <span>You can download <strong>all documents</strong>.</span>
                @elseif($canDownloadQA)
                    <span>You can download <strong>QA documents only</strong>. Project documents are read-only for your role.</span>
                @elseif($canDownloadProject)
                    <span>You can download <strong>project documents</strong>. QA documents are read-only for your role.</span>
                @else
                    <span>Your role has <strong>read-only</strong> access to all documents. Contact the Facility Manager to request a file.</span>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    // Open document viewer modal
    document.querySelectorAll('.doc-view-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const url        = this.dataset.url;
            const title      = this.dataset.title;
            const canDl      = this.dataset.canDownload === '1';
            const ext        = url.split('.').pop().toLowerCase().split('?')[0];
            const isImg      = ['jpg','jpeg','png','gif','bmp','webp'].includes(ext);

            document.getElementById('docViewerTitleText').textContent = title;

            const content = document.getElementById('docViewerContent');
            if (isImg) {
                content.innerHTML = `<img src="${url}" style="max-width:100%;max-height:100%;display:block;margin:auto;object-fit:contain;padding:12px;" alt="${title}">`;
            } else {
                content.innerHTML = `<iframe src="${url}" style="width:100%;height:100%;border:none;" title="${title}"></iframe>`;
            }

            const dlBtn = document.getElementById('docViewerDownload');
            if (canDl) {
                dlBtn.href = url;
                dlBtn.download = title;
                dlBtn.classList.remove('d-none');
            } else {
                dlBtn.classList.add('d-none');
            }

            new bootstrap.Modal(document.getElementById('docViewerModal')).show();
        });
    });

    // Clean up iframe on modal close to stop any PDF loading
    document.getElementById('docViewerModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('docViewerContent').innerHTML = '';
    });
})();
</script>

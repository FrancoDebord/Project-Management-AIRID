<style>
/* ── Study Creation – local styles ─────────────────────────────── */
.sc-action-bar {
    display: flex;
    flex-wrap: wrap;
    gap: .5rem;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: .75rem 1rem;
    box-shadow: 0 2px 8px rgba(0,0,0,.05);
}
.sc-action-btn {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    padding: .45rem .9rem;
    border-radius: 8px;
    font-size: .82rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: opacity .18s, transform .18s;
    white-space: nowrap;
}
.sc-action-btn:hover { opacity: .88; transform: translateY(-1px); }
.sc-action-btn.navy   { background: #1a3a6b; color: #fff; }
.sc-action-btn.indigo { background: #4e2d8e; color: #fff; }
.sc-action-btn.teal   { background: #0d7377; color: #fff; }
.sc-action-btn.slate  { background: #475569; color: #fff; }
.sc-action-btn.violet { background: #5c6bc0; color: #fff; }
.sc-action-btn.amber  { background: #d97706; color: #fff; }
.sc-action-btn.amber:disabled { opacity: .45; cursor: not-allowed; transform: none; }

/* Info cards */
.sc-card { border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.07); overflow: hidden; height: 100%; }
.sc-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: .7rem 1rem;
    font-size: .85rem;
    font-weight: 700;
    letter-spacing: .02em;
    color: #fff;
}
.sc-card-header.navy   { background: linear-gradient(90deg,#1a3a6b,#22549a); }
.sc-card-header.indigo { background: linear-gradient(90deg,#4e2d8e,#6b4ab5); }
.sc-card-header .edit-btn {
    padding: .25rem .55rem;
    border-radius: 6px;
    font-size: .75rem;
    font-weight: 600;
    border: 1.5px solid rgba(255,255,255,.5);
    color: #fff;
    background: transparent;
    cursor: pointer;
    transition: background .15s;
}
.sc-card-header .edit-btn:hover { background: rgba(255,255,255,.2); }

/* Field rows */
.sc-field { display: flex; align-items: baseline; gap: .5rem; padding: .55rem .1rem; border-bottom: 1px solid #f3f4f6; font-size: .83rem; }
.sc-field:last-child { border-bottom: none; }
.sc-field-icon { color: #94a3b8; flex-shrink: 0; font-size: .9rem; width: 1.1rem; text-align: center; }
.sc-field-label { color: #6b7280; min-width: 155px; flex-shrink: 0; font-size: .78rem; }
.sc-field-value { color: #1e293b; font-weight: 500; word-break: break-word; }
.sc-field-value a { color: #1a3a6b; text-decoration: none; font-weight: 600; font-size: .78rem; }
.sc-field-value a:hover { text-decoration: underline; }
.sc-empty { color: #9ca3af; font-size: .83rem; text-align: center; padding: 1.5rem 0; }

/* Progression */
.sc-progress-card { border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.07); }

/* Section tables */
.sc-section-card { border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.07); overflow: hidden; }
.sc-section-card .sc-section-header {
    padding: .6rem 1rem;
    background: #f8fafc;
    border-bottom: 2px solid #e5e7eb;
    font-size: .83rem;
    font-weight: 700;
    color: #374151;
    display: flex;
    align-items: center;
    gap: .5rem;
}
.sc-table { font-size: .82rem; margin-bottom: 0; }
.sc-table thead th { background: #f1f5f9; font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: #64748b; border-bottom: 1px solid #e2e8f0; padding: .55rem .75rem; }
.sc-table tbody td { padding: .55rem .75rem; vertical-align: middle; border-color: #f1f5f9; color: #374151; }
.sc-table tbody tr:hover td { background: #f8fafc; }

/* Badge GLP */
.badge-glp-yes { background: #dcfce7; color: #15803d; font-size: .72rem; font-weight: 700; padding: .2rem .5rem; border-radius: 20px; }
.badge-glp-no  { background: #fef9c3; color: #854d0e; font-size: .72rem; font-weight: 700; padding: .2rem .5rem; border-radius: 20px; }
</style>

<div class="row g-3">

    {{-- ── Page header ───────────────────────────────────────────── --}}
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1">
            <div>
                <h5 class="fw-bold mb-0" style="color:#1a3a6b;">
                    <i class="bi bi-folder2-open me-2"></i>Study Creation
                </h5>
                <p class="text-muted small mb-0 mt-1">
                    Provide basic study information and upload founding documents before moving to the next phase.
                </p>
            </div>
        </div>
    </div>

    {{-- ── Action toolbar ─────────────────────────────────────────── --}}
    @if(auth()->user()->canManageProtocol())
    <div class="col-12">
        <div class="sc-action-bar">
            <button class="sc-action-btn navy"
                    data-bs-toggle="modal" data-bs-target="#detailedInformationProjectModal">
                <i class="bi bi-pencil-square"></i>Edit Project Info
            </button>
            <button class="sc-action-btn indigo"
                    data-bs-toggle="modal" data-bs-target="#customModal">
                <i class="bi bi-person-badge"></i>Study Director Form
            </button>
            <button class="sc-action-btn teal"
                    data-bs-toggle="modal" data-bs-target="#replacementModal">
                <i class="bi bi-arrow-repeat"></i>Director Replacement
            </button>
            <button class="sc-action-btn slate"
                    data-bs-toggle="modal" data-bs-target="#otherBasicDocumentsModal">
                <i class="bi bi-paperclip"></i>Other Documents
            </button>
            <button class="sc-action-btn violet"
                    data-bs-toggle="modal" data-bs-target="#keyPersonnelModal">
                <i class="bi bi-people-fill"></i>Manage Key Personnel
            </button>
            @if($project->is_legacy)
            <button class="sc-action-btn amber"
                    data-bs-toggle="modal" data-bs-target="#legacyDatesModal"
                    data-no-lock="1"
                    title="Set key dates for the Master Schedule">
                <i class="bi bi-calendar3-range"></i>Legacy Key Dates
            </button>
            @else
            <button class="sc-action-btn amber" disabled
                    data-no-lock="1"
                    title="Only available for legacy (already-completed) projects">
                <i class="bi bi-calendar3-range"></i>Legacy Key Dates
            </button>
            @endif
        </div>
    </div>
    @endif

    {{-- ── Project Basic Info  +  Study Director Form ──────────── --}}
    @php
        $study_director_appointment = $project->studyDirectorAppointmentForm ?? null;
        $studyDirector   = $study_director_appointment->studyDirector  ?? null;
        $projectManager  = $study_director_appointment->projectManager ?? null;
        $isGlp = $project->is_glp ?? null;
        $isGlpLabel = is_bool($isGlp) ? ($isGlp ? 'Yes' : 'No') : ($isGlp ?? 'N/A');

        // Signature state
        $currentUserPersonnelId = auth()->user()->personnel?->id;
        $sdSigned  = $study_director_appointment?->sd_signed_at;
        $fmSigned  = $study_director_appointment?->fm_signed_at;
        $fmRecord  = \App\Models\Pro_KeyFacilityPersonnel::where('active', 1)->with('personnel')->first();
        $fmPerson  = $fmRecord?->personnel;

        // Can the current user sign as SD?
        $canSignAsSd = $study_director_appointment
            && !$sdSigned
            && $currentUserPersonnelId
            && (int)$study_director_appointment->study_director === (int)$currentUserPersonnelId;

        // Can the current user sign as FM?
        $canSignAsFm = $study_director_appointment
            && !$fmSigned
            && $currentUserPersonnelId
            && $fmRecord
            && (int)$fmRecord->personnel_id === (int)$currentUserPersonnelId;
    @endphp

    {{-- Project Basic Information --}}
    <div class="col-lg-6">
        <div class="sc-card">
            <div class="sc-card-header navy">
                <span><i class="bi bi-info-circle me-2"></i>Project Basic Information</span>
                @if(auth()->user()->canManageProtocol())
                <button class="edit-btn" data-bs-toggle="modal" data-bs-target="#detailedInformationProjectModal">
                    <i class="bi bi-pencil me-1"></i>Edit
                </button>
                @endif
            </div>
            <div class="card-body px-3 py-2">
                @if(isset($project))
                    <div class="sc-field">
                        <i class="bi bi-hash sc-field-icon"></i>
                        <span class="sc-field-label">Project Code</span>
                        <span class="sc-field-value">{{ $project->project_code ?? '—' }}</span>
                    </div>
                    <div class="sc-field">
                        <i class="bi bi-journal-text sc-field-icon"></i>
                        <span class="sc-field-label">Project Title</span>
                        <span class="sc-field-value">{{ $project->project_title ?? '—' }}</span>
                    </div>
                    <div class="sc-field">
                        <i class="bi bi-patch-check sc-field-icon"></i>
                        <span class="sc-field-label">GLP Study</span>
                        <span class="sc-field-value">
                            @if($isGlpLabel === 'Yes')
                                <span class="badge-glp-yes"><i class="bi bi-check2 me-1"></i>Yes — GLP</span>
                            @elseif($isGlpLabel === 'No')
                                <span class="badge-glp-no"><i class="bi bi-x me-1"></i>No — Non-GLP</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </span>
                    </div>
                    <div class="sc-field">
                        <i class="bi bi-tag sc-field-icon"></i>
                        <span class="sc-field-label">Project Nature</span>
                        <span class="sc-field-value">{{ $project->project_nature ?? '—' }}</span>
                    </div>
                    <div class="sc-field">
                        <i class="bi bi-upc-scan sc-field-icon"></i>
                        <span class="sc-field-label">Protocol Code</span>
                        <span class="sc-field-value">{{ $project->protocol_code ?? '—' }}</span>
                    </div>
                    <div class="sc-field">
                        <i class="bi bi-virus sc-field-icon"></i>
                        <span class="sc-field-label">Test System</span>
                        <span class="sc-field-value">{{ $project->test_system ?? '—' }}</span>
                    </div>
                    <div class="sc-field">
                        <i class="bi bi-layers sc-field-icon"></i>
                        <span class="sc-field-label">Project Phase</span>
                        <span class="sc-field-value">{{ $project->project_stage ?? '—' }}</span>
                    </div>
                @else
                    <p class="sc-empty">No project information available.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Study Director Appointment Form --}}
    <div class="col-lg-6">
        <div class="sc-card">
            <div class="sc-card-header indigo">
                <span><i class="bi bi-person-badge me-2"></i>Study Director Appointment</span>
                @if(auth()->user()->canManageProtocol())
                <button class="edit-btn" data-bs-toggle="modal" data-bs-target="#customModal">
                    <i class="bi bi-pencil me-1"></i>Edit
                </button>
                @endif
            </div>
            <div class="card-body px-3 py-2">
                @if(isset($study_director_appointment))
                    <div class="sc-field">
                        <i class="bi bi-person-circle sc-field-icon"></i>
                        <span class="sc-field-label">Study Director</span>
                        <span class="sc-field-value">
                            {{ $studyDirector
                                ? trim(($studyDirector->titre ?? '') . ' ' . $studyDirector->prenom . ' ' . $studyDirector->nom)
                                : '—' }}
                        </span>
                    </div>
                    <div class="sc-field">
                        <i class="bi bi-award sc-field-icon"></i>
                        <span class="sc-field-label">Director Qualification</span>
                        <span class="sc-field-value">{{ $studyDirector->titre_qualitification ?? '—' }}</span>
                    </div>
                    <div class="sc-field">
                        <i class="bi bi-calendar-check sc-field-icon"></i>
                        <span class="sc-field-label">Date of Appointment</span>
                        <span class="sc-field-value">{{ $study_director_appointment->sd_appointment_date ?? '—' }}</span>
                    </div>
                    <div class="sc-field">
                        <i class="bi bi-paperclip sc-field-icon"></i>
                        <span class="sc-field-label">Appointment File</span>
                        <span class="sc-field-value">
                            @if($study_director_appointment->sd_appointment_file)
                                <a href="{{ asset('storage/' . $study_director_appointment->sd_appointment_file) }}" target="_blank">
                                    <i class="bi bi-file-earmark-arrow-down me-1"></i>View file
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </span>
                    </div>
                    <div class="sc-field">
                        <i class="bi bi-calendar2-week sc-field-icon"></i>
                        <span class="sc-field-label">Est. Start Date</span>
                        <span class="sc-field-value">{{ $study_director_appointment->estimated_start_date ?? '—' }}</span>
                    </div>
                    <div class="sc-field">
                        <i class="bi bi-calendar2-x sc-field-icon"></i>
                        <span class="sc-field-label">Est. End Date</span>
                        <span class="sc-field-value">{{ $study_director_appointment->estimated_end_date ?? '—' }}</span>
                    </div>
                    <div class="sc-field">
                        <i class="bi bi-person-workspace sc-field-icon"></i>
                        <span class="sc-field-label">Project Manager</span>
                        <span class="sc-field-value">
                            {{ $projectManager
                                ? trim(($projectManager->titre ?? '') . ' ' . $projectManager->prenom . ' ' . $projectManager->nom)
                                : '—' }}
                        </span>
                    </div>
                    <div class="sc-field">
                        <i class="bi bi-award sc-field-icon"></i>
                        <span class="sc-field-label">Manager Qualification</span>
                        <span class="sc-field-value">{{ $projectManager->titre_qualitification ?? '—' }}</span>
                    </div>
                    {{-- ── Signature status ─────────────────────────── --}}
                    <div class="mt-2 pt-2 border-top">
                        <div class="small fw-semibold text-muted mb-1">État des signatures</div>

                        {{-- Study Director signature --}}
                        <div class="d-flex align-items-center justify-content-between mb-1 gap-2">
                            <div class="d-flex align-items-center gap-2 small">
                                @if($sdSigned)
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Signé</span>
                                    <span class="text-muted">Study Director — {{ \Carbon\Carbon::parse($sdSigned)->format('d/m/Y H:i') }}</span>
                                @else
                                    <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>En attente</span>
                                    <span class="text-muted">Study Director —
                                        {{ $studyDirector ? trim(($studyDirector->titre_personnel ?? '') . ' ' . $studyDirector->prenom . ' ' . $studyDirector->nom) : '—' }}
                                    </span>
                                @endif
                            </div>
                            @if($canSignAsSd)
                                <button class="btn btn-sm btn-success py-0 px-2 sign-btn"
                                        data-project="{{ $project->id }}" data-role="sd">
                                    <i class="bi bi-pen me-1"></i>Signer
                                </button>
                            @endif
                        </div>

                        {{-- Facility Manager signature --}}
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <div class="d-flex align-items-center gap-2 small">
                                @if($fmSigned)
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Signé</span>
                                    <span class="text-muted">Facility Manager — {{ \Carbon\Carbon::parse($fmSigned)->format('d/m/Y H:i') }}</span>
                                @else
                                    <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>En attente</span>
                                    <span class="text-muted">Facility Manager —
                                        {{ $fmPerson ? trim(($fmPerson->titre_personnel ?? '') . ' ' . $fmPerson->prenom . ' ' . $fmPerson->nom) : '—' }}
                                    </span>
                                @endif
                            </div>
                            @if($canSignAsFm)
                                <button class="btn btn-sm btn-success py-0 px-2 sign-btn"
                                        data-project="{{ $project->id }}" data-role="fm">
                                    <i class="bi bi-pen me-1"></i>Signer
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="mt-2 pt-2 border-top text-end">
                        <a href="{{ route('pdf.sd-appointment-form', ['project_id' => $project->id]) }}"
                           target="_blank"
                           class="btn btn-sm btn-outline-danger fw-semibold">
                            <i class="bi bi-file-earmark-pdf me-1"></i>Télécharger le formulaire
                        </a>
                    </div>
                @else
                    <p class="sc-empty">No appointment form submitted yet.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Progression ─────────────────────────────────────────────── --}}
    <div class="col-12">
        <div class="sc-progress-card card">
            <div class="card-body py-3 px-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="fw-bold small" style="color:#374151;">
                        <i class="bi bi-bar-chart-line me-1" style="color:#1a3a6b;"></i>Overall Progression
                    </span>
                    <span class="fw-bold small {{ $execution_rate >= 100 ? 'text-success' : ($execution_rate >= 60 ? 'text-primary' : 'text-danger') }}">
                        {{ $execution_rate }}%
                    </span>
                </div>
                @php
                    $progressColor = match(true) {
                        $execution_rate <= 20 => 'bg-danger',
                        $execution_rate <= 40 => 'bg-warning',
                        $execution_rate <= 60 => 'bg-info',
                        $execution_rate <= 80 => 'bg-primary',
                        default               => 'bg-success',
                    };
                @endphp
                <div class="progress mb-3" style="height:12px;border-radius:8px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated {{ $progressColor }}"
                         role="progressbar" style="width:{{ $execution_rate }}%;"
                         aria-valuenow="{{ $execution_rate }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                @if(!empty($phase_metrics))
                @php
                    $metricRows = [
                        ['key' => 'activities',   'label' => 'Study Activities',          'icon' => 'bi-list-check'],
                        ['key' => 'protocol_dev', 'label' => 'Protocol Dev Documents',    'icon' => 'bi-file-earmark-code'],
                        ['key' => 'inspections',  'label' => 'QA Inspections',            'icon' => 'bi-shield-check'],
                        ['key' => 'nc_findings',  'label' => 'NC Findings resolved',      'icon' => 'bi-exclamation-triangle'],
                        ['key' => 'report_docs',  'label' => 'Report submitted',          'icon' => 'bi-file-earmark-text',  'type' => 'milestone'],
                        ['key' => 'archiving',    'label' => 'Archiving',                 'icon' => 'bi-archive',            'type' => 'milestone'],
                    ];
                @endphp
                <div class="row row-cols-2 row-cols-sm-3 row-cols-md-6 g-2">
                    @foreach($metricRows as $row)
                    @php
                        $m = $phase_metrics[$row['key']] ?? ['total' => 0, 'done' => 0];
                        $isMilestone = ($row['type'] ?? '') === 'milestone';
                        if ($isMilestone) {
                            $isDone = (bool) $m['done'];
                            $label  = $isDone ? 'Done' : 'Pending';
                            $isNA   = false;
                        } else {
                            $isNA   = $m['total'] === 0;
                            $isDone = !$isNA && $m['done'] >= $m['total'];
                            $label  = $isNA ? 'N/A' : $m['done'] . '/' . $m['total'];
                        }
                        $chipBg  = $isNA ? '#f1f5f9' : ($isDone ? '#dcfce7' : '#fee2e2');
                        $chipClr = $isNA ? '#64748b' : ($isDone ? '#166534' : '#991b1b');
                        $iconClr = $isNA ? 'text-secondary' : ($isDone ? 'text-success' : 'text-danger');
                    @endphp
                    <div class="col">
                        <div class="d-flex flex-column align-items-center text-center p-2"
                             style="border-radius:10px;background:{{ $chipBg }};border:1px solid {{ $isDone && !$isNA ? '#bbf7d0' : ($isNA ? '#e2e8f0' : '#fecaca') }};">
                            <i class="bi {{ $row['icon'] }} {{ $iconClr }} mb-1" style="font-size:1.1rem;"></i>
                            <span style="font-size:.68rem;color:#374151;line-height:1.2;margin-bottom:.2rem;">{{ $row['label'] }}</span>
                            <span style="font-size:.75rem;font-weight:700;color:{{ $chipClr }};">{{ $label }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Study Director Replacement History ─────────────────────── --}}
    @php $replacementHistory = $project->studyDirectorReplacementHistory()->get(); @endphp
    <div class="col-12">
        <div class="sc-section-card card">
            <div class="sc-section-header">
                <i class="bi bi-arrow-repeat" style="color:#0d7377;"></i>
                Study Director Replacement History
                @if(auth()->user()->canManageProtocol())
                <button class="ms-auto sc-action-btn teal" style="font-size:.76rem;padding:.3rem .7rem;"
                        data-bs-toggle="modal" data-bs-target="#replacementModal">
                    <i class="bi bi-plus-lg"></i>Add
                </button>
                @endif
            </div>
            <div class="table-responsive">
                <table class="table sc-table mb-0">
                    <thead>
                        <tr>
                            <th>Replaced Study Director</th>
                            <th>Appointment Date</th>
                            <th>Replacement Date</th>
                            <th>Comments</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($replacementHistory as $replacement)
                        @php $sd = $replacement->studyDirector ?? null; @endphp
                        <tr>
                            <td class="fw-semibold">
                                {{ $sd ? trim(($sd->titre ?? '') . ' ' . $sd->prenom . ' ' . $sd->nom) : '—' }}
                            </td>
                            <td>{{ $replacement->sd_appointment_date ?? '—' }}</td>
                            <td>{{ $replacement->replacement_date ?? '—' }}</td>
                            <td class="text-muted">{{ $replacement->replacement_reason ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3" style="font-size:.82rem;">
                                <i class="bi bi-dash-circle me-1"></i>No replacement history.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── Other Basic Documents ───────────────────────────────────── --}}
    @php $otherBasicDocuments = $project->otherBasicDocuments()->get(); @endphp
    <div class="col-12">
        <div class="sc-section-card card">
            <div class="sc-section-header">
                <i class="bi bi-paperclip" style="color:#475569;"></i>
                Other Basic Documents
                @if(auth()->user()->canManageProtocol())
                <button class="ms-auto sc-action-btn slate" style="font-size:.76rem;padding:.3rem .7rem;"
                        data-bs-toggle="modal" data-bs-target="#otherBasicDocumentsModal">
                    <i class="bi bi-upload"></i>Upload
                </button>
                @endif
            </div>
            <div class="table-responsive">
                <table class="table sc-table mb-0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Uploaded By</th>
                            <th>Upload Date</th>
                            <th>Document</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($otherBasicDocuments as $document)
                        @php $uploadedBy = \App\Models\User::find($document->uploaded_by); @endphp
                        <tr>
                            <td class="fw-semibold">{{ $document->titre_document ?? '—' }}</td>
                            <td class="text-muted">{{ $document->description_document ?? '—' }}</td>
                            <td>{{ $uploadedBy ? $uploadedBy->name : '—' }}</td>
                            <td>{{ $document->upload_date ?? '—' }}</td>
                            <td>
                                @if($document->document_file_path)
                                    <a href="{{ asset('storage/' . $document->document_file_path) }}" target="_blank"
                                       class="btn btn-outline-secondary btn-sm py-0 px-2" style="font-size:.76rem;">
                                        <i class="bi bi-download me-1"></i>View
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3" style="font-size:.82rem;">
                                <i class="bi bi-dash-circle me-1"></i>No documents uploaded yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── Key Personnel ───────────────────────────────────────────── --}}
    @php
        $keyPersonnel    = $project ? $project->keyPersonnelProject()->orderBy('nom')->get() : collect();
        $kpCurrentIds    = $keyPersonnel->pluck('id')->toArray();
        $kpAllPersonnels = \App\Models\Pro_Personnel::orderBy('nom')->get();
        $kpProjectId     = $project?->id;
    @endphp
    <div class="col-12">
        <div class="sc-section-card card">
            <div class="sc-section-header">
                <i class="bi bi-people-fill" style="color:#5c6bc0;"></i>
                Key Personnel
            </div>
            <div class="card-body pt-2 pb-3">

                {{-- Add person row --}}
                @if(auth()->user()->canManageProtocol())
                <div class="d-flex gap-2 mb-3" id="kp-add-row">
                    <select id="kp-add-select" class="form-select form-select-sm" style="max-width:360px;">
                        <option value="">— Select person to add —</option>
                        @foreach($kpAllPersonnels as $p)
                            @if(!in_array($p->id, $kpCurrentIds))
                            <option value="{{ $p->id }}">
                                {{ trim(($p->titre_personnel ?? '') . ' ' . $p->prenom . ' ' . $p->nom) }}
                                @if($p->role) ({{ $p->role }}) @endif
                            </option>
                            @endif
                        @endforeach
                    </select>
                    <button class="sc-action-btn violet" onclick="kpAddPerson()">
                        <i class="bi bi-plus-circle"></i>Add
                    </button>
                    <span id="kp-add-msg" class="align-self-center small"></span>
                </div>
                @endif

                <div class="table-responsive">
                    <table class="table sc-table table-hover align-middle mb-0" id="kp-inline-table">
                        <thead>
                            <tr>
                                <th style="width:40px;">#</th>
                                <th>Name</th>
                                <th>Title</th>
                                <th>Role</th>
                                @if(auth()->user()->canManageProtocol())
                                <th class="text-center" style="width:60px;">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody id="kp-inline-tbody">
                            @forelse($keyPersonnel as $i => $p)
                            <tr id="kp-row-{{ $p->id }}">
                                <td class="text-muted small">{{ $i + 1 }}</td>
                                <td class="fw-semibold">{{ trim(($p->titre_personnel ?? '') . ' ' . $p->prenom . ' ' . $p->nom) }}</td>
                                <td class="text-muted small">{{ $p->titre_personnel ?? '—' }}</td>
                                <td class="text-muted small">{{ $p->role ?? '—' }}</td>
                                @if(auth()->user()->canManageProtocol())
                                <td class="text-center">
                                    <button class="btn btn-outline-danger btn-sm py-0 px-2"
                                            onclick="kpRemovePerson({{ $p->id }}, this)" title="Remove">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr id="kp-empty-row">
                                <td colspan="{{ auth()->user()->canManageProtocol() ? 5 : 4 }}" class="text-muted text-center small py-3">
                                    <i class="bi bi-dash-circle me-1"></i>No key personnel assigned yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

</div>{{-- /row --}}

<script>
(function () {
    const PROJECT_ID = {{ $kpProjectId ?? 'null' }};
    const CSRF       = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    let   rowCount   = {{ $keyPersonnel->count() }};
    const CAN_EDIT   = {{ auth()->user()->canManageProtocol() ? 'true' : 'false' }};

    var kpTomAdd = null;
    document.addEventListener('DOMContentLoaded', function () {
        var el = document.getElementById('kp-add-select');
        if (el && window.TomSelect) {
            kpTomAdd = new TomSelect(el, { allowEmptyOption: true, placeholder: '— Select person to add —' });
        }
    });

    window.kpAddPerson = function () {
        var selectEl = document.getElementById('kp-add-select');
        var staffId  = kpTomAdd ? kpTomAdd.getValue() : selectEl?.value;
        var msgEl    = document.getElementById('kp-add-msg');
        msgEl.textContent = '';

        if (!staffId) {
            msgEl.className = 'align-self-center small text-warning';
            msgEl.textContent = 'Please select a person.';
            return;
        }

        const fd = new FormData();
        fd.append('_token',     CSRF);
        fd.append('project_id', PROJECT_ID);
        fd.append('staff_id',   staffId);

        fetch('{{ route('addKeyPersonnelMember') }}', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                if (!data.success) {
                    msgEl.className = 'align-self-center small text-danger';
                    msgEl.textContent = data.message || 'Error.';
                    return;
                }
                var emptyRow = document.getElementById('kp-empty-row');
                if (emptyRow) emptyRow.remove();

                rowCount++;
                const p  = data.person;
                const tr = document.createElement('tr');
                tr.id = 'kp-row-' + p.id;
                tr.innerHTML = `
                    <td class="text-muted small">${rowCount}</td>
                    <td class="fw-semibold">${p.full_name}</td>
                    <td class="text-muted small">${p.titre_personnel || '—'}</td>
                    <td class="text-muted small">${p.role || '—'}</td>
                    ${CAN_EDIT ? `<td class="text-center">
                        <button class="btn btn-outline-danger btn-sm py-0 px-2"
                                onclick="kpRemovePerson(${p.id}, this)" title="Remove">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </td>` : ''}`;
                document.getElementById('kp-inline-tbody').appendChild(tr);

                if (kpTomAdd) { kpTomAdd.removeOption(String(p.id)); kpTomAdd.setValue(''); }
                else { selectEl.querySelector(`option[value="${p.id}"]`)?.remove(); selectEl.value = ''; }

                msgEl.className = 'align-self-center small text-success';
                msgEl.textContent = '✔ Added.';
                setTimeout(() => { msgEl.textContent = ''; }, 2000);
            })
            .catch(() => {
                msgEl.className = 'align-self-center small text-danger';
                msgEl.textContent = 'Network error.';
            });
    };

    window.kpRemovePerson = function (staffId, btn) {
        if (!confirm('Remove this person from the project team?')) return;
        btn.disabled = true;

        const fd = new FormData();
        fd.append('_token',     CSRF);
        fd.append('project_id', PROJECT_ID);
        fd.append('staff_id',   staffId);

        fetch('{{ route('removeKeyPersonnelMember') }}', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                if (!data.success) { btn.disabled = false; alert(data.message || 'Error.'); return; }
                const row = document.getElementById('kp-row-' + staffId);
                if (row) row.remove();
                rowCount--;

                document.querySelectorAll('#kp-inline-tbody tr').forEach(function (tr, idx) {
                    var first = tr.querySelector('td:first-child');
                    if (first && first.textContent.trim().match(/^\d+$/)) first.textContent = idx + 1;
                });

                if (rowCount <= 0) {
                    rowCount = 0;
                    var emptyTr = document.createElement('tr');
                    emptyTr.id = 'kp-empty-row';
                    emptyTr.innerHTML = `<td colspan="${CAN_EDIT ? 5 : 4}" class="text-muted text-center small py-3"><i class="bi bi-dash-circle me-1"></i>No key personnel assigned yet.</td>`;
                    document.getElementById('kp-inline-tbody').appendChild(emptyTr);
                }
            })
            .catch(() => { btn.disabled = false; alert('Network error.'); });
    };
})();
</script>

@include('partials.dialog_detailed_information_project')
@include('partials.study_director_appointment_form')
@include('partials.study_director_replacement_form')
@include('partials.other_documents_project')

{{-- ── Legacy Key Dates Modal ─────────────────────────────────────── --}}
@if($project->is_legacy)
<div class="modal fade" id="legacyDatesModal" tabindex="-1" aria-labelledby="legacyDatesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header border-0 py-3 px-4"
                 style="background:linear-gradient(135deg,#92400e 0%,#d97706 100%);">
                <div>
                    <h5 class="modal-title text-white fw-bold mb-0" id="legacyDatesModalLabel">
                        <i class="bi bi-calendar3-range me-2"></i>Legacy Key Dates
                    </h5>
                    <p class="text-white-50 small mb-0">These dates generate the Master Schedule automatically.</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <div id="legacy-dates-msg"></div>
                <div class="p-3 rounded-3" style="background:#fffbeb;border:1px solid #fde68a;">

                    {{-- Phase 1: Study Start --}}
                    <div class="mb-3">
                        <div class="fw-semibold" style="font-size:.78rem;color:#1a3a6b;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.5rem;">
                            <i class="bi bi-1-circle me-1"></i>Study Start Phase
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small mb-1">Start — Appointment of SD</label>
                                <input type="date" id="ld_sd_appointment_date" class="form-control form-control-sm"
                                       value="{{ $project->legacy_sd_appointment_date?->format('Y-m-d') ?? '' }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label small mb-1">End — Protocol signed by SD</label>
                                <input type="date" id="ld_protocol_signed_sd_date" class="form-control form-control-sm"
                                       value="{{ $project->legacy_protocol_signed_sd_date?->format('Y-m-d') ?? '' }}">
                            </div>
                        </div>
                    </div>

                    {{-- Phase 2: Planning --}}
                    <div class="mb-3">
                        <div class="fw-semibold" style="font-size:.78rem;color:#1a3a6b;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.5rem;">
                            <i class="bi bi-2-circle me-1"></i>Planning Phase
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small mb-1">Start — Protocol signed by all parties</label>
                                <input type="date" id="ld_protocol_signed_all_date" class="form-control form-control-sm"
                                       value="{{ $project->legacy_protocol_signed_all_date?->format('Y-m-d') ?? '' }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label small mb-1">End — Date of first experiment</label>
                                <input type="date" id="ld_first_experiment_date" class="form-control form-control-sm"
                                       value="{{ $project->legacy_first_experiment_date?->format('Y-m-d') ?? '' }}"
                                       oninput="document.getElementById('ld_first_experiment_date_exp').value=this.value">
                            </div>
                        </div>
                    </div>

                    {{-- Phase 3: Experimental --}}
                    <div class="mb-3">
                        <div class="fw-semibold" style="font-size:.78rem;color:#1a3a6b;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.5rem;">
                            <i class="bi bi-3-circle me-1"></i>Experimental Phase
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small mb-1">Start — Date of first experiment</label>
                                <input type="date" id="ld_first_experiment_date_exp" class="form-control form-control-sm"
                                       value="{{ $project->legacy_first_experiment_date?->format('Y-m-d') ?? '' }}"
                                       readonly style="background:#f3f4f6;">
                            </div>
                            <div class="col-6">
                                <label class="form-label small mb-1">End — Date of last experiment</label>
                                <input type="date" id="ld_last_experiment_date" class="form-control form-control-sm"
                                       value="{{ $project->legacy_last_experiment_date?->format('Y-m-d') ?? '' }}"
                                       oninput="document.getElementById('ld_last_experiment_date_report').value=this.value">
                            </div>
                        </div>
                    </div>

                    {{-- Phase 4: Report --}}
                    <div class="mb-3">
                        <div class="fw-semibold" style="font-size:.78rem;color:#1a3a6b;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.5rem;">
                            <i class="bi bi-4-circle me-1"></i>Report Phase
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small mb-1">Start — Date of last experiment</label>
                                <input type="date" id="ld_last_experiment_date_report" class="form-control form-control-sm"
                                       value="{{ $project->legacy_last_experiment_date?->format('Y-m-d') ?? '' }}"
                                       readonly style="background:#f3f4f6;">
                            </div>
                            <div class="col-6">
                                <label class="form-label small mb-1">End — Final report signed by SD</label>
                                <input type="date" id="ld_final_report_signed_sd_date" class="form-control form-control-sm"
                                       value="{{ $project->legacy_final_report_signed_sd_date?->format('Y-m-d') ?? '' }}">
                            </div>
                        </div>
                    </div>

                    {{-- Phase 5: Archiving --}}
                    <div class="mb-1">
                        <div class="fw-semibold" style="font-size:.78rem;color:#1a3a6b;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.5rem;">
                            <i class="bi bi-5-circle me-1"></i>Archiving Phase
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small mb-1">Start — Final report signed by all parties</label>
                                <input type="date" id="ld_final_report_signed_all_date" class="form-control form-control-sm"
                                       value="{{ $project->legacy_final_report_signed_all_date?->format('Y-m-d') ?? '' }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label small mb-1">End — Documents submitted to archivist</label>
                                <input type="date" id="ld_archive_submission_date" class="form-control form-control-sm"
                                       value="{{ $project->legacy_archive_submission_date?->format('Y-m-d') ?? '' }}">
                            </div>
                        </div>
                    </div>

                </div>{{-- /amber panel --}}
            </div>
            <div class="modal-footer border-0 pt-0 px-4 pb-3" style="background:#f8f9fa;">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm fw-semibold text-white"
                        style="background:#d97706;border:none;border-radius:8px;"
                        onclick="saveLegacyDates()">
                    <i class="bi bi-check2-circle me-1"></i>Save Dates
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const PROJECT_ID = {{ $project->id }};
    const SAVE_URL   = '{{ route('project.saveLegacyDates', $project->id) }}';
    const CSRF       = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    window.saveLegacyDates = function () {
        const msgEl = document.getElementById('legacy-dates-msg');
        msgEl.innerHTML = '';

        const body = new URLSearchParams({
            _token:                              CSRF,
            legacy_sd_appointment_date:          document.getElementById('ld_sd_appointment_date').value,
            legacy_protocol_signed_sd_date:      document.getElementById('ld_protocol_signed_sd_date').value,
            legacy_protocol_signed_all_date:     document.getElementById('ld_protocol_signed_all_date').value,
            legacy_first_experiment_date:        document.getElementById('ld_first_experiment_date').value,
            legacy_last_experiment_date:         document.getElementById('ld_last_experiment_date').value,
            legacy_final_report_signed_sd_date:  document.getElementById('ld_final_report_signed_sd_date').value,
            legacy_final_report_signed_all_date: document.getElementById('ld_final_report_signed_all_date').value,
            legacy_archive_submission_date:      document.getElementById('ld_archive_submission_date').value,
        });

        fetch(SAVE_URL, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    msgEl.innerHTML = '<div class="alert alert-success py-2 px-3 mb-3" style="font-size:.82rem;"><i class="bi bi-check-circle me-1"></i>' + data.message + '</div>';
                    setTimeout(() => {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('legacyDatesModal'));
                        if (modal) modal.hide();
                    }, 1500);
                } else {
                    msgEl.innerHTML = '<div class="alert alert-danger py-2 px-3 mb-3" style="font-size:.82rem;"><i class="bi bi-exclamation-triangle me-1"></i>' + (data.message ?? 'An error occurred.') + '</div>';
                }
            })
            .catch(() => {
                msgEl.innerHTML = '<div class="alert alert-danger py-2 px-3 mb-3" style="font-size:.82rem;"><i class="bi bi-wifi-off me-1"></i>Network error. Please try again.</div>';
            });
    };
})();
</script>
@endif

{{-- ── Appointment Form — Electronic Signature ─────────────────────── --}}
@if($study_director_appointment ?? false)
<script>
(function () {
    document.querySelectorAll('.sign-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var projectId = btn.dataset.project;
            var role      = btn.dataset.role;
            var label     = role === 'sd' ? 'Study Director' : 'Facility Manager';

            if (!confirm('Confirmer votre signature électronique en tant que ' + label + ' ?')) return;

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            var fd = new FormData();
            fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            fd.append('project_id', projectId);
            fd.append('role', role);

            fetch('{{ route("signAppointmentForm") }}', { method: 'POST', body: fd })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.code_erreur === 0) {
                        window.location.reload();
                    } else {
                        alert(data.message || 'Erreur.');
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bi bi-pen me-1"></i>Signer';
                    }
                })
                .catch(function () {
                    alert('Erreur réseau.');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-pen me-1"></i>Signer';
                });
        });
    });
})();
</script>
@endif

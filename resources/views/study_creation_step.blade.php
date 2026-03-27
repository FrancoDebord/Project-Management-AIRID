<style>
        .btn-custom {
        font-weight: 600;
        padding: 12px 22px;
        border-radius: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
    }

    /* Bouton principal */
    .btn-primary-custom {
        background-color: #c20102;
        color: #fff;
        border: none;
    }

    .btn-primary-custom:hover {
        background-color: #a10001;
        transform: translateY(-2px);
    }

    /* Variante plus claire */
    .btn-secondary-custom {
        background-color: #e45c5d;
        color: #fff;
        border: none;
    }

    .btn-secondary-custom:hover {
        background-color: #c94a4b;
        transform: translateY(-2px);
    }

    /* Variante encore plus claire */
    .btn-tertiary-custom {
        background-color: #f28b8c;
        color: #fff;
        border: none;
    }

    .btn-tertiary-custom:hover {
        background-color: #d67374;
        transform: translateY(-2px);
    }

    /* Variante très claire */
    .btn-light-custom {
        background-color: #f5b5b5;
        color: #333;
        border: none;
    }

    .btn-light-custom:hover {
        background-color: #e49c9c;
        color: #fff;
        transform: translateY(-2px);
    }
</style>

<div class="row">
    <div class="col-md-12">
        <h4>Study Creation</h4>
        <p>In this section, you are asked to provide basic information about the study along with basic documents such
            as the Study Director Appointment Form..</p>
    </div>

    <div class="col-12 col-sm-7 ">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-column gap-3 w-50 mx-auto">
                    <button class="btn btn-custom btn-primary-custom" id="project_basic_information" data-bs-toggle="modal" data-bs-target="#detailedInformationProjectModal">Update Project Basic
                        Information</button>

                    <button class="btn btn-custom btn-secondary-custom" data-bs-toggle="modal"
                        data-bs-target="#customModal"> Study Director Appointment
                        Form</button>

                    <button class="btn btn-custom btn-tertiary-custom" data-bs-toggle="modal"
                        data-bs-target="#replacementModal">Study Director Replacement Form</button>

                    <button class="btn btn-custom btn-light-custom" data-bs-toggle="modal"
                        data-bs-target="#otherBasicDocumentsModal">Upload other basic documents</button>

                    <button class="btn btn-custom" style="background:#5c6bc0;color:#fff;" data-bs-toggle="modal"
                        data-bs-target="#keyPersonnelModal">
                        <i class="bi bi-people-fill me-1"></i> Manage Key Personnel
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-5">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Progression</h5>

                @php
                    $progressColor = '';
                    if ($execution_rate <= 20) {
                        $progressColor = 'bg-danger';
                    } elseif ($execution_rate <= 40) {
                        $progressColor = 'bg-warning';
                    } elseif ($execution_rate <= 60) {
                        $progressColor = 'bg-info';
                    } elseif ($execution_rate <= 80) {
                        $progressColor = 'bg-primary';
                    } else {
                        $progressColor = 'bg-success';
                    }
                @endphp
                <div class="progress" style="height: 25px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated {{ $progressColor }}"
                        role="progressbar" style="width: {{ $execution_rate }}%;" aria-valuenow="{{ $execution_rate }}"
                        aria-valuemin="0" aria-valuemax="100">{{ $execution_rate }}%</div>
                </div>

                <p class="mt-3 mb-0 small text-muted">Overall execution rate: <strong class="{{ $execution_rate >= 100 ? 'text-success' : ($execution_rate >= 60 ? 'text-primary' : 'text-danger') }}">{{ $execution_rate }}%</strong></p>
                @if(!empty($phase_metrics))
                @php
                    $metricRows = [
                        ['key' => 'activities',   'label' => 'Activités de l\'étude',     'icon' => 'bi-list-check',          'type' => 'count'],
                        ['key' => 'protocol_dev', 'label' => 'Documents Protocol Dev',    'icon' => 'bi-file-earmark-code',   'type' => 'count'],
                        ['key' => 'inspections',  'label' => 'Inspections QA',             'icon' => 'bi-shield-check',        'type' => 'count'],
                        ['key' => 'nc_findings',  'label' => 'NC findings résolus',        'icon' => 'bi-exclamation-triangle','type' => 'count'],
                        ['key' => 'report_docs',  'label' => 'Rapport (document soumis)',  'icon' => 'bi-file-earmark-text',   'type' => 'milestone'],
                        ['key' => 'archiving',    'label' => 'Archivage',                  'icon' => 'bi-archive',             'type' => 'milestone'],
                    ];
                @endphp
                <ul class="list-unstyled mt-2 mb-0">
                    @foreach($metricRows as $row)
                        @php
                            $m = $phase_metrics[$row['key']] ?? ['total' => 0, 'done' => 0];
                            if ($row['type'] === 'milestone') {
                                $isDone  = (bool) $m['done'];
                                $label   = $isDone ? 'Fait' : 'En attente';
                                $isNA    = false;
                            } else {
                                $isNA    = $m['total'] === 0;
                                $isDone  = !$isNA && $m['done'] >= $m['total'];
                                $label   = $isNA ? 'N/A' : $m['done'] . '/' . $m['total'];
                            }
                        @endphp
                        <li class="d-flex align-items-center gap-2 py-1 border-bottom" style="font-size:.8rem;">
                            @if($isNA)
                                <i class="bi bi-dash-circle text-secondary flex-shrink-0" style="font-size:.9rem;"></i>
                                <span class="text-muted flex-grow-1">{{ $row['label'] }}</span>
                                <span class="badge bg-light text-secondary border" style="font-size:.68rem;">N/A</span>
                            @elseif($isDone)
                                <i class="bi bi-check-circle-fill text-success flex-shrink-0" style="font-size:.9rem;"></i>
                                <span class="text-muted flex-grow-1">{{ $row['label'] }}</span>
                                <span class="badge bg-success" style="font-size:.68rem;">{{ $label }}</span>
                            @else
                                <i class="bi bi-x-circle-fill text-danger flex-shrink-0" style="font-size:.9rem;"></i>
                                <span class="fw-semibold flex-grow-1" style="color:#dc3545;">{{ $row['label'] }}</span>
                                <span class="badge bg-danger" style="font-size:.68rem;">{{ $label }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
                @endif

            </div>
        </div>
    </div>
    <div class="row mt-2">

        <h5 class="card-title h5 mb-3 mt-3">Summary of Entered Data </h5>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Project Basic Information
                </div>
                <div class="card-body">
                    @if (isset($project))
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>Project Code</th>
                                    <td>{{ $project->project_code ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Project Title</th>
                                    <td>{{ $project->project_title ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Is GLP ? </th>
                                    @php
                                        $isGlp = $project->is_glp ?? 'N/A';
                                        if (is_bool($isGlp)) {
                                            $isGlp = $isGlp ? 'Yes' : 'No';
                                        }
                                    @endphp
                                    <td>{{ $isGlp }}</td>
                                </tr>
                                <tr>
                                    <th>Project Nature</th>
                                    <td>{{ $project->project_nature ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Protocol Code</th>
                                    <td>{{ $project->protocol_code ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Test System</th>
                                    <td>{{ $project->test_system ?? 'N/A' }}</td>
                                </tr>
                                {{-- <tr>
                                    <th>Study Director</th>
                                    @php
                                        $studyDirector = $project->studyDirector ?? 'N/A';
                                    @endphp
                                    <td>{{ $studyDirector && $studyDirector != 'N/A' ? $studyDirector->titre . ' ' . $studyDirector->prenom . ' ' . $studyDirector->nom : 'N/A' }}
                                    </td>
                                </tr>

                                <tr>
                                    <th>Project Manager</th>
                                    @php
                                        $projectManager = $project->projectManager ?? 'N/A';
                                    @endphp
                                    <td>{{ $projectManager && $projectManager != 'N/A' ? $projectManager->titre . ' ' . $projectManager->prenom . ' ' . $projectManager->nom : 'N/A' }}
                                    </td>
                                </tr> --}}


                                <tr>
                                    <th>Project Phase</th>
                                    <td>{{ $project->project_stage ?? 'N/A' }}</td>
                                </tr>

                            </tbody>
                        </table>
                        <a href="#" class="btn btn-primary btn-sm"
                           data-bs-toggle="modal" data-bs-target="#detailedInformationProjectModal">
                            <i class="bi bi-pencil-square me-1"></i>Edit
                        </a>
                    @else
                        <p>No Project Basic Information available.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Study Director Appointment Form
                </div>
                <div class="card-body">

                    @php
                        $study_director_appointment = $project->studyDirectorAppointmentForm ?? null;

                        $studyDirector = $study_director_appointment->studyDirector ?? null;
                        $projectManager = $study_director_appointment->projectManager ?? null;
                    @endphp
                    @if (isset($study_director_appointment))
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>Study Director Name</th>
                                    <td>{{ $studyDirector ? $studyDirector->titre . ' ' . $studyDirector->prenom . ' ' . $studyDirector->nom : 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Study Director Title</th>
                                    <td>{{ $studyDirector->titre_qualitification ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Date of Appointment</th>
                                    <td>{{ $study_director_appointment->sd_appointment_date ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Study Director Appointment File</th>
                                    <td>
                                        @if (isset($study_director_appointment) && $study_director_appointment->sd_appointment_file)
                                            <a href="{{ asset('storage/' . $study_director_appointment->sd_appointment_file) }}"
                                                target="_blank" class="mt-2 d-block">View Current Study Director
                                                Appointment Form</a>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Estimated Start Date</th>
                                    <td>{{ $study_director_appointment->estimated_start_date ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Estimated End Date</th>
                                    <td>{{ $study_director_appointment->estimated_end_date ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Project Manager Name</th>
                                    <td>{{ $projectManager ? $projectManager->titre . ' ' . $projectManager->prenom . ' ' . $projectManager->nom : 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Project Manager Title</th>
                                    <td>{{ $projectManager->titre_qualitification ?? 'N/A' }}</td>
                                </tr>
                                <!-- Display other fields as needed -->
                            </tbody>
                        </table>
                        <a href="#" class="btn btn-primary btn-sm"
                           data-bs-toggle="modal" data-bs-target="#customModal">
                            <i class="bi bi-pencil-square me-1"></i>Edit
                        </a>
                    @else
                        <p>No Study Director Appointment Form available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <h5 class="mt-2 mb-3 ">Study Director Replacement History</h5>
        <table class="table">
            <thead>
                <tr>
                    <th>Replaced Study Director </th>
                    <th>Appointment Date</th>
                    <th>Replacement Date</th>
                    <th>Comments</th>
                </tr>
            </thead>

            @php
                $replacementHistory = $project->studyDirectorReplacementHistory()->get(); ;

            @endphp
            <tbody>
                @forelse ($replacementHistory ?? [] as $replacement)

                    <tr>
                        @php
                             $studyDirector = $replacement->studyDirector ?? null;
                        @endphp
                        <td>{{ $studyDirector ? $studyDirector->titre . ' ' . $studyDirector->prenom . ' ' . $studyDirector->nom : 'N/A' }}</td>
                        <td>{{ $replacement->sd_appointment_date }}</td>
                        <td>{{ $replacement->replacement_date }}</td>
                        <td>{{ $replacement->replacement_reason }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">No replacements found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="row mt-2">
        <h5 class="mt-2 mb-3 ">Other Basic Documents Submitted</h5>
        <table class="table">
            <thead>
                <tr>
                    <th>Title of Document</th>
                    <th>Description</th>
                    <th>Uploaded By</th>
                    <th>Upload Date</th>
                    <th>Document</th>
                </tr>
            </thead>

            @php
                $otherBasicDocuments = $project->otherBasicDocuments()->get(); ;

            @endphp
            <tbody>
                @forelse ($otherBasicDocuments ?? [] as $document)

                    <tr>
                        <td>{{ $document->titre_document }}</td>
                        <td>{{ $document->description_document }}</td>
                        @php
                            $uploadedBy = \App\Models\User::find($document->uploaded_by);
                        @endphp
                        <td>{{ $uploadedBy ? $uploadedBy->prenom . ' ' . $uploadedBy->nom : 'N/A' }}</td>
                        <td>{{ $document->upload_date }}</td>
                        <td>
                            @if ($document->document_file_path)
                                <a href="{{ asset('storage/' . $document->document_file_path) }}" target="_blank"
                                    class="mt-2 d-block">View Document</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">No other basic documents found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── KEY PERSONNEL ── --}}
    @php
        $keyPersonnel    = $project ? $project->keyPersonnelProject()->orderBy('nom')->get() : collect();
        $kpCurrentIds    = $keyPersonnel->pluck('id')->toArray();
        $kpAllPersonnels = \App\Models\Pro_Personnel::orderBy('nom')->get();
        $kpProjectId     = $project?->id;
    @endphp
    <div class="row mt-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h5 class="mb-0"><i class="bi bi-people-fill me-2" style="color:#5c6bc0;"></i>Key Personnel</h5>
        </div>

        {{-- Add person inline --}}
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
            <button class="btn btn-sm fw-semibold" style="background:#5c6bc0;color:#fff;" onclick="kpAddPerson()">
                <i class="bi bi-plus-circle me-1"></i>Add
            </button>
            <span id="kp-add-msg" class="align-self-center small"></span>
        </div>

        <table class="table table-sm table-hover align-middle mb-0" id="kp-inline-table">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Title</th>
                    <th>Role</th>
                    <th class="text-center" style="width:60px;">Action</th>
                </tr>
            </thead>
            <tbody id="kp-inline-tbody">
                @forelse($keyPersonnel as $i => $p)
                <tr id="kp-row-{{ $p->id }}">
                    <td class="text-muted small">{{ $i + 1 }}</td>
                    <td class="fw-semibold">{{ trim(($p->titre_personnel ?? '') . ' ' . $p->prenom . ' ' . $p->nom) }}</td>
                    <td class="text-muted small">{{ $p->titre_personnel ?? '—' }}</td>
                    <td class="text-muted small">{{ $p->role ?? '—' }}</td>
                    <td class="text-center">
                        <button class="btn btn-outline-danger btn-sm py-0 px-2"
                                onclick="kpRemovePerson({{ $p->id }}, this)" title="Remove">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr id="kp-empty-row">
                    <td colspan="5" class="text-muted text-center small py-3">No key personnel assigned yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

<script>
(function () {
    const PROJECT_ID = {{ $kpProjectId ?? 'null' }};
    const CSRF       = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    let   rowCount   = {{ $keyPersonnel->count() }};

    // Initialise Tom Select on the add-select
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
                // Remove empty row if present
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
                    <td class="text-center">
                        <button class="btn btn-outline-danger btn-sm py-0 px-2"
                                onclick="kpRemovePerson(${p.id}, this)" title="Remove">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </td>`;
                document.getElementById('kp-inline-tbody').appendChild(tr);

                // Remove from Tom Select options
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

                // Re-number rows
                document.querySelectorAll('#kp-inline-tbody tr').forEach(function (tr, idx) {
                    var first = tr.querySelector('td:first-child');
                    if (first) first.textContent = idx + 1;
                });

                // Show empty row if no one left
                if (rowCount <= 0) {
                    rowCount = 0;
                    var emptyTr = document.createElement('tr');
                    emptyTr.id = 'kp-empty-row';
                    emptyTr.innerHTML = '<td colspan="5" class="text-muted text-center small py-3">No key personnel assigned yet.</td>';
                    document.getElementById('kp-inline-tbody').appendChild(emptyTr);
                }

                // Add person back to Tom Select options
                // We need to fetch the person name — it's in the row cells we just removed
                // Use the removed row's text content
                // (already removed, so we skip re-adding to select — page refresh will restore it)
            })
            .catch(() => { btn.disabled = false; alert('Network error.'); });
    };
})();
</script>

</div>


@include('partials.dialog_detailed_information_project')
@include('partials.study_director_appointment_form')
@include('partials.study_director_replacement_form')
@include('partials.other_documents_project')

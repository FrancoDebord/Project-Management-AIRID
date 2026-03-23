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
                        data-bs-target="#otherBasicDocumentsModal">Upload other basic documents </button>
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

                <p class="mt-3 mb-0">Overall project execution rate: <strong>{{ $execution_rate }}%</strong></p>
                @if(!empty($phase_metrics))
                <ul class="list-unstyled small text-muted mt-2 mb-0" style="font-size:.82rem;">
                    <li><i class="bi bi-check2-circle me-1"></i>Activities: <strong>{{ $phase_metrics['activities']['done'] }}/{{ $phase_metrics['activities']['total'] }}</strong></li>
                    <li><i class="bi bi-shield-check me-1"></i>QA Inspections: <strong>{{ $phase_metrics['inspections']['done'] }}/{{ $phase_metrics['inspections']['total'] }}</strong></li>
                    <li><i class="bi bi-exclamation-triangle me-1"></i>NC Findings resolved: <strong>{{ $phase_metrics['nc_findings']['done'] }}/{{ $phase_metrics['nc_findings']['total'] }}</strong></li>
                    <li><i class="bi bi-file-earmark-text me-1"></i>Report documents: <strong>{{ $phase_metrics['report_docs']['done'] }}/{{ $phase_metrics['report_docs']['total'] }}</strong></li>
                    <li><i class="bi bi-archive me-1"></i>Archiving: <strong>{{ $phase_metrics['archiving']['done'] ? 'Done' : 'Pending' }}</strong></li>
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


</div>


@include('partials.dialog_detailed_information_project')
@include('partials.study_director_appointment_form')
@include('partials.study_director_replacement_form')
@include('partials.other_documents_project')

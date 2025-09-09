<div class="row mt-2">

    @if ($project)

        @php
            $protocol_dev_activities_project = $project->protocolDeveloppementActivitiesProject;

        @endphp


        <!-- Bouton principal -->
        <div class="mb-4 text-center">
            <a href="{{ route('generateProtocolDevActivitiesForProject') }}" class="btn btn-inverse-danger btn-lg shadow"
                id="generate-protocol-dev-activities" data-project-id='{{ $project->id }}'>
                Generate Protocol Dev activities for Project
            </a>
        </div>


        <!-- Tableau des activités -->
        <div class="container">
            <div class="card shadow">
                <div class="card-body p-0">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Activity</th>
                                <th scope="col">Assigned To</th>
                                <th scope="col">Performed By</th>
                                <th scope="col">Deadline</th>
                                <th scope="col" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($protocol_dev_activities_project ??[] as $index => $activity)
                                <tr>
                                    @php
                                        $assignedTo = $activity->assignedTo;
                                        $staffPerformed = $activity->staffPerformed;

                                        $level_activity = $activity->level_activite;

                                        $check_current_step_completed = \App\Models\Pro_ProtocolDevActivityProject::where(
                                            'project_id',
                                            $project_id,
                                        )
                                            ->where('level_activite', $level_activity)
                                            ->where('complete', true)
                                            ->first();

                                    @endphp
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $activity->protocolDevActivity->nom_activite }}</td>
                                    <td>{{ $assignedTo ? $assignedTo->prenom . ' ' . $assignedTo->nom : 'Not yet assigned' }}
                                    <td>{{ $staffPerformed ? $staffPerformed->prenom . ' ' . $staffPerformed->nom : 'Not yet performed' }}
                                    </td>
                                    <td>{{ $activity->due_date_performed }}</td>
                                    <td class="text-center">

                                        @if ($check_current_step_completed)
                                            <i class="fa fa-check-circle text-success"></i> <br>
                                            <a href="{{ asset("storage/".$check_current_step_completed->document_file_path) }}" target="_blank" class="btn btn-outline-success mt-2">
                                                <i class="fa fa-eye">&nbsp;</i> See Document
                                            </a>
                                        @else
                                            @php

                                                $check_step_before_completed = \App\Models\Pro_ProtocolDevActivityProject::where(
                                                    'project_id',
                                                    $project_id,
                                                )
                                                    ->where('level_activite', $level_activity - 1)
                                                    ->where('complete', true)
                                                    ->first();
                                            @endphp

                                            @if ($check_step_before_completed)
                                                <button class="btn btn-outline-warning  btn-upload-doc-protocol-dev" data-bs-toggle="modal"
                                                    data-bs-target="#detailsModal"
                                                    data-activity="{{ $activity->protocolDevActivity->nom_activite }}"
                                                    data-activity-project-id="{{ $activity->id }}">
                                                    <i class="fa fa-upload">&nbsp;</i>
                                                    Upload Document

                                                </button>
                                            @else
                                                @if ($level_activity == 1)
                                                    <button class="btn btn-outline-warning btn-upload-doc-protocol-dev" data-bs-toggle="modal"
                                                        data-bs-target="#detailsModal"
                                                        data-activity="{{ $activity->protocolDevActivity->nom_activite }}"
                                                        data-activity-project-id="{{ $activity->id }}">
                                                        <i class="fa fa-upload">&nbsp;</i>
                                                        Upload Document
                                                    </button>
                                                @endif
                                            @endif
                                        @endif



                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Aucune activité trouvée</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="col-12">
            <p class="alert alert-info text-center">
                Veuillez d'abord sélectionner un projet
            </p>
        </div>
    @endif

</div>

@include('partials.fill-details-protocol-dev-activity')

<script>
    // const detailsModal = document.getElementById('detailsModal');
    // detailsModal.addEventListener('show.bs.modal', event => {
    //     const button = event.relatedTarget;
    //     const activityName = button.getAttribute('data-activity');
    //     const activityInput = detailsModal.querySelector('#activityName');
    //     activityInput.value = activityName;
    // });
</script>

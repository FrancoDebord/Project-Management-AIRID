<div class="container">

    @php
        $project_id = request('project_id');

        $project = App\Models\Pro_Project::find($project_id);

        $study_initiation_meeting = App\Models\Pro_StudyQualityAssuranceMeeting::where('project_id', $project_id)
            ->where('meeting_type', 'study_initiation_meeting')
            ->first();

        $all_phases_critiques = $project->allPhasesCritiques;
    @endphp

    @if (!$study_initiation_meeting)
        <h4>Planning Phase</h4>
        <!-- Bouton principal -->
        <button type="button" class="btn btn-primary mb-4 study_qa_initiation" data-project-id="{{ $project_id }}"
            data-qa-meeting-id="" data-ajaxroute="">
            Schedule Study Initiation Meeting
        </button>
    @else
        @if (!$all_phases_critiques)
            <h4>Planning Phase</h4>
            <!-- Bouton principal -->
            <button type="button" class="btn btn-info mb-4 study_qa_initiation" data-project-id="{{ $project_id }}"
                data-qa-meeting-id="{{ $study_initiation_meeting->id }}"
                data-ajaxroute="{{ route('getMeetingInfoById') }}">
                Modify the Meeting's Info
            </button>
        @endif
    @endif


    @include('partials.modal-schedule-meeting')


    <!-- Tableau des réunions -->
    <h4 class="mb-3">Study Initition Meeting Details</h4>
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>Date scheduled</th>
                <th>Time scheduled</th>
                <th>Participants</th>
                <th>Link</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr>

                @if ($study_initiation_meeting)
                    <td>{{ $study_initiation_meeting->date_scheduled }}</td>
                    <td>{{ $study_initiation_meeting->time_scheduled }}</td>
                    @php
                        $participants = $study_initiation_meeting->participants;

                        $all_participants = [];

                        foreach ($participants ?? [] as $key => $participant) {
                            # code...

                            $all_participants[] = $participant->prenom . ' ' . $participant->nom;
                        }

                    @endphp
                    <td>{{ implode(', ', $all_participants) }}</td>
                    <td>
                        @if ($study_initiation_meeting->meeting_link)
                            <a href="{{ $study_initiation_meeting->meeting_link }}">Meeting Link</a>
                        @else
                            No link Provided
                        @endif
                    </td>
                    <td>

                        @if (!$all_phases_critiques)
                            <button class="btn btn-sm btn-warning study_qa_initiation"
                                data-project-id="{{ $project_id }}"
                                data-qa-meeting-id="{{ $study_initiation_meeting->id }}"
                                data-ajaxroute="{{ route('getMeetingInfoById') }}">Edit</button>
                            <button class="btn btn-sm btn-danger supprimer-meeting"
                                data-ajaxroute="{{ route('deleteQAMeeting') }}"
                                data-qa-meeting-id="{{ $study_initiation_meeting->id }}">Delete</button>
                        @endif

                        <button class="btn btn-sm btn-info" data-bs-toggle="modal"
                            data-bs-target="#participantsModal">
                            <i class="fa fa-users">&nbsp;</i> View
                        </button>
                    </td>
                @else
                    <td colspan="5">The meeting is not scheduled yet</td>
                @endif

            </tr>
        </tbody>
    </table>


    <!-- Modal Participants -->
    <div class="modal fade" id="participantsModal" tabindex="-1" aria-labelledby="participantsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="participantsModalLabel">Liste des Participants</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul>

                        @forelse ($all_participants ?? [] as $participant_name)
                            <li>{{ $participant_name }}</li>
                        @empty
                            <li>No Participants invited</li>
                        @endforelse
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Tableau des activités -->
    <h4 class="mt-5 mb-3">Activities</h4>
    <table class="table table-bordered align-middle table-hover table-striped">
        <thead class="table-secondary">
            <tr>
                <th>Activity</th>
                <th>Date Start</th>
                <th>Date End </th>
                <th>Parent Activity</th>
                <th>Assigned to</th>
                <th>Mark</th>
            </tr>
        </thead>
        <tbody>

            @if ($study_initiation_meeting)
                @php
                    $all_activities = $project->allActivitiesProject;
                @endphp

                @forelse ($all_activities??[] as $activite)
                    <tr>
                        <td>{{ $activite->study_activity_name }}</td>
                        <td>{{ $activite->estimated_activity_date }}</td>
                        <td>{{ $activite->estimated_activity_end_date }}</td>
                        <td>{{ $activite->ParentActivity ? $activite->ParentActivity->study_activity_name : 'N/A' }}
                        </td>
                        <td>{{ $activite->personneResponsable ? $activite->personneResponsable->prenom . ' ' . $activite->personneResponsable->nom : 'N/A' }}
                        </td>
                        <td>
                            @if ($activite->phase_critique == false)
                                <button class="btn btn-outline-danger btn-sm marquer-critique" data-bs-toggle="modal"
                                    data-bs-target="#criticalModal" data-project-id="{{ $project_id }}"
                                    data-qa-meeting-id="{{ $study_initiation_meeting->id }}"
                                    data-activity-id="{{ $activite->id }}"
                                    data-ajaxroute="{{ route('marquerActivitePhaseCritique') }}">Mark as
                                    critique</button>
                            @else
                                <button class="btn btn-outline-info btn-sm marquer-non-critique" data-bs-toggle="modal"
                                    data-bs-target="#criticalModal" data-project-id="{{ $project_id }}"
                                    data-qa-meeting-id="{{ $study_initiation_meeting->id }}"
                                    data-activity-id="{{ $activite->id }}"
                                    data-ajaxroute="{{ route('marquerActiviteNonPhaseCritique') }}">Unmark</button>
                            @endif

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">No activity scheduled yet</td>
                    </tr>
                @endforelse
            @else
                <tr>
                    <td colspan="5">Veuillez d'abord programmer la réunion avant de sélectionner les phases critiques
                    </td>
                </tr>
            @endif



        </tbody>
    </table>

    {{-- <h2 class="mb-4 text-center">📅 Mon Agenda avec FullCalendar</h2> --}}

    <div id="calendar" class="shadow rounded p-3 bg-white"></div>


</div>

@php

    $events = [];

    foreach ($all_phases_critiques ?? [] as $key => $phase_critique) {
        # code...

        $events[] = [
            'title' => $phase_critique->study_activity_name,
            'start' => $phase_critique->estimated_activity_date,
            'end' => $phase_critique->estimated_activity_end_date,
        ];
    }

@endphp


<script>
    const events = @json($events);

    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'en', // calendrier en français
            themeSystem: 'bootstrap5',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            // buttonText: {
            //     today: 'Aujourd\'hui',
            //     month: 'Mois',
            //     week: 'Semaine',
            //     day: 'Jour',
            //     list: 'Liste'
            // },
            events: events,
            eventClick: function(info) {
                alert("📌 " + info.event.title + "\n📅 " + info.event.start.toLocaleDateString());
            }
        });
        calendar.render();
    });
</script>

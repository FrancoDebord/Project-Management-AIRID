<div class="row">
    <div class="col-12 mt-2 mb-3">
        <div class="card  rounded-4">
            <div class="card-header bg-light text-center fw-bold">
                List of the activities related to this project
            </div>

            @php

                $project_id = request('project_id');

                $project = App\Models\Pro_Project::find($project_id);

                $study_initiation_meeting = App\Models\Pro_StudyQualityAssuranceMeeting::where(
                    'project_id',
                    $project_id,
                )
                    ->where('meeting_type', 'study_initiation_meeting')
                    ->first();

                $all_phases_critiques = $project->allPhasesCritiques;
                $all_activities = $project->allActivitiesProject;

                $events_phases_critiques = [];

                foreach ($all_phases_critiques ?? [] as $key => $phase_critique) {
                    # code...

                    $events_phases_critiques[] = [
                        'title' => $phase_critique->study_activity_name,
                        'start' => $phase_critique->estimated_activity_date,
                        'end' => $phase_critique->estimated_activity_end_date,
                    ];
                }


                 $events_all_activities = [];

                foreach ($all_activities ?? [] as $key => $phase_critique) {
                    # code...

                    $events_all_activities[] = [
                        'title' => $phase_critique->study_activity_name,
                        'start' => $phase_critique->estimated_activity_date,
                        'end' => $phase_critique->estimated_activity_end_date,
                    ];
                }


            @endphp
            <div class="card-body">
                <table class="table table-hover table-striped align-middle text-left">
                    <thead class="table-dark">

                        <tr>
                            <th class="col">Activity</th>
                            <th class="col">Date Start</th>
                            <th class="col">Date End </th>
                            <th class="col">Parent Activity</th>
                            <th class="col">Assigned to</th>
                            <th class="col">Mark</th>
                        </tr>

                    </thead>
                    <tbody>

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
                                    <button class="btn btn-success btn-sm">
                                        <i class="bi bi-play-circle"></i> Exécuter l'activité
                                    </button>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">No activity scheduled yet</td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12 mt-3">
        {{-- <h2 class="mb-4 text-center">📅 Mon Agenda avec FullCalendar</h2> --}}

        <div id="calendar-experimental-phase" class=" rounded p-3 bg-white"></div>
    </div>
</div>


<script>

    const events_phases_critiques = @json($events_phases_critiques);

    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar-experimental-phase');

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            selectable: true,
            editable: true,
            dayMaxEvents: true,
            navLinks: true,
            height: 'auto',
            events: events_phases_critiques,
            select: function(info) {
                // When selecting a date range
                openCreateModal(info.start);
                calendar.unselect();
            },
            dateClick: function(info) {
                // Single click on a date -> create event on that date
                openCreateModal(info.date);
            },
            eventClick: function(info) {
                // Open edit modal
                openEditModal(info.event);
            },
            eventDrop: function(info) {
                // update storage after drag/drop
                updateEventFromInstance(info.event);
            },
            eventResize: function(info) {
                updateEventFromInstance(info.event);
            },
            eventDidMount: function(arg) {
                // ensure contrast: if dark bg make text white
                // FullCalendar already uses textColor if provided
            },
            // show tooltip (title) on hover (simple)
            eventMouseEnter: function(info) {
                // optional: custom behavior
            }
        });

        calendar.render();


    });
</script>

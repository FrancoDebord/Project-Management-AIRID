<div class="container">

    @php
        $project_id = request('project_id');

        $project = App\Models\Pro_Project::find($project_id);

        $study_initiation_meeting = App\Models\Pro_StudyQualityAssuranceMeeting::where('project_id', $project_id)
            ->where('meeting_type', 'study_initiation_meeting')
            ->first();

        $all_phases_critiques = $project->allPhasesCritiques;
    @endphp

    @if($project && $project->is_legacy)
    <div class="alert d-flex align-items-center gap-3 py-3 px-4 mb-4"
         style="background:#fffbeb;border:1px solid #fde68a;border-radius:12px;">
        <i class="bi bi-archive-fill fs-4 flex-shrink-0" style="color:#92400e;"></i>
        <div>
            <div class="fw-semibold" style="color:#78350f;">Legacy project — Planning Phase pré-validée</div>
            <div class="small text-muted mt-1">
                Cette étude étant un ancien projet déjà terminé, la réunion d'initiation, l'identification des phases critiques et la CPIA ne sont pas requises.<br>
                @if($project->legacy_protocol_signed_all_date)
                    <strong>Dates enregistrées :</strong>
                    Planning du <strong>{{ $project->legacy_protocol_signed_all_date->format('d/m/Y') }}</strong>
                    au <strong>{{ $project->legacy_first_experiment_date?->format('d/m/Y') ?? '—' }}</strong>
                @endif
            </div>
        </div>
    </div>
    @elseif (!$study_initiation_meeting)
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


    {{-- ── Study Initiation Meeting Report ── --}}
    @if($study_initiation_meeting)
    <div class="mt-4 p-3 rounded-3 border" id="meeting-report-section" style="background:#f8f9ff;">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
            <div>
                <strong style="color:#1a3a6b;"><i class="bi bi-file-earmark-text me-1"></i>Study Initiation Meeting Report</strong>
                <div class="text-muted small">Upload a PDF or draft a text report for this meeting.</div>
            </div>
            <button class="btn btn-sm fw-semibold"
                    style="background:#1a3a6b;color:#fff;"
                    data-bs-toggle="modal"
                    data-bs-target="#meetingReportModal">
                <i class="bi bi-pencil me-1"></i>
                {{ ($study_initiation_meeting->report_file_path || $study_initiation_meeting->report_content) ? 'Edit Report' : 'Add Report' }}
            </button>
        </div>

        @if($study_initiation_meeting->report_file_path || $study_initiation_meeting->report_content)
        <div class="mt-2">
            @if($study_initiation_meeting->report_date)
                <div class="text-muted small mb-1">
                    <i class="bi bi-calendar3 me-1"></i>
                    Report date: <strong>{{ \Carbon\Carbon::parse($study_initiation_meeting->report_date)->format('d/m/Y') }}</strong>
                </div>
            @endif
            @if($study_initiation_meeting->report_file_path)
                <a href="{{ asset('storage/' . $study_initiation_meeting->report_file_path) }}"
                   target="_blank"
                   class="btn btn-sm btn-outline-primary me-2">
                    <i class="bi bi-file-earmark-pdf me-1"></i>View PDF Report
                </a>
            @endif
            @if($study_initiation_meeting->report_content)
                <div class="mt-2 p-2 rounded border bg-white small"
                     style="max-height:120px;overflow-y:auto;white-space:pre-wrap;">{{ $study_initiation_meeting->report_content }}</div>
            @endif
        </div>
        @else
            <div class="text-muted small mt-1"><i class="bi bi-info-circle me-1"></i>No report yet.</div>
        @endif
    </div>

    {{-- Meeting Report Modal --}}
    <div class="modal fade" id="meetingReportModal" tabindex="-1" aria-labelledby="meetingReportModalLabel" aria-hidden="true"
         data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background:#1a3a6b;color:#fff;">
                    <h5 class="modal-title" id="meetingReportModalLabel">
                        <i class="bi bi-file-earmark-text me-2"></i>Study Initiation Meeting Report
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <div id="meeting-report-messages" class="mb-3"></div>
                    <form id="form_meeting_report" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="meeting_id" value="{{ $study_initiation_meeting->id }}">

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Report Date</label>
                            <input type="date" name="report_date" class="form-control"
                                   value="{{ $study_initiation_meeting->report_date ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">
                                Report PDF (upload)
                                @if($study_initiation_meeting->report_file_path)
                                    <a href="{{ asset('storage/' . $study_initiation_meeting->report_file_path) }}"
                                       target="_blank" class="ms-2 small text-success">
                                        <i class="bi bi-file-earmark-pdf me-1"></i>Current file
                                    </a>
                                @endif
                            </label>
                            <input type="file" name="report_file" class="form-control" accept="application/pdf">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Drafted Text Report</label>
                            <textarea name="report_content" class="form-control" rows="8"
                                      placeholder="Write the meeting report here…">{{ $study_initiation_meeting->report_content ?? '' }}</textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2 border-top pt-3 mt-1">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary fw-semibold px-4">
                                <i class="bi bi-save me-1"></i>Save Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    (function () {
        const reportForm = document.getElementById('form_meeting_report');
        if (!reportForm) return;
        reportForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const msgBox = document.getElementById('meeting-report-messages');
            msgBox.innerHTML = '';
            const fd = new FormData(this);
            fetch('{{ route("saveMeetingReport") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: fd,
            })
            .then(r => r.json())
            .then(data => {
                if (data.code_erreur === 0) {
                    bootstrap.Modal.getInstance(document.getElementById('meetingReportModal')).hide();
                    location.reload();
                } else {
                    msgBox.innerHTML = '<div class="alert alert-danger py-2">' + (data.message || 'Error saving report') + '</div>';
                }
            })
            .catch(() => {
                msgBox.innerHTML = '<div class="alert alert-danger py-2">Network error.</div>';
            });
        });
    })();
    </script>
    @endif

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
                @if($project && $project->is_glp)
                <th>Mark as Critical</th>
                @endif
            </tr>
        </thead>
        <tbody>

            @if ($study_initiation_meeting)
                @php
                    $all_activities = $project->allActivitiesProject;
                    // Activity IDs whose Critical Phase Inspection has already been performed
                    $performedInspectionActivityIds = App\Models\Pro_QaInspection::where('project_id', $project_id)
                        ->whereNotNull('activity_id')
                        ->whereNotNull('date_performed')
                        ->pluck('activity_id')
                        ->flip();
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
                        @if($project && $project->is_glp)
                        <td>
                            @if ($activite->phase_critique == false)
                                <button class="btn btn-outline-danger btn-sm marquer-critique" data-bs-toggle="modal"
                                    data-bs-target="#criticalModal" data-project-id="{{ $project_id }}"
                                    data-qa-meeting-id="{{ $study_initiation_meeting->id }}"
                                    data-activity-id="{{ $activite->id }}"
                                    data-ajaxroute="{{ route('marquerActivitePhaseCritique') }}">Mark as
                                    critique</button>
                            @else
                                @if(isset($performedInspectionActivityIds[$activite->id]))
                                    <button class="btn btn-outline-secondary btn-sm" disabled
                                            title="The Critical Phase Inspection has already been performed — cannot unmark.">
                                        <i class="bi bi-lock-fill me-1"></i>Unmark
                                    </button>
                                @else
                                    <button class="btn btn-outline-info btn-sm marquer-non-critique" data-bs-toggle="modal"
                                        data-bs-target="#criticalModal" data-project-id="{{ $project_id }}"
                                        data-qa-meeting-id="{{ $study_initiation_meeting->id }}"
                                        data-activity-id="{{ $activite->id }}"
                                        data-ajaxroute="{{ route('marquerActiviteNonPhaseCritique') }}">Unmark</button>
                                @endif
                            @endif
                        </td>
                        @endif
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

    {{-- ── Critical Phase Impact Assessment (GLP only) ── --}}
    @if($project && $project->is_glp)
    @php
        $cpiaEnabled    = $study_initiation_meeting && $all_phases_critiques && $all_phases_critiques->where('phase_critique', true)->count() > 0;
        $existingCpia   = \App\Models\CpiaAssessment::where('project_id', $project_id)->first();
    @endphp
    <div class="mt-4 p-3 rounded-3 border" style="background:#fff7f7;">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <strong style="color:#C10202;"><i class="bi bi-clipboard2-pulse me-1"></i>Critical Phase Impact Assessment</strong>
                <div class="text-muted small">
                    @if(!$study_initiation_meeting)
                        Schedule the Study Initiation Meeting first.
                    @elseif(!$cpiaEnabled)
                        Identify at least one critical phase first.
                    @else
                        @if($existingCpia && $existingCpia->isCompleted())
                            <span style="color:#198754;font-weight:600;"><i class="bi bi-check-circle-fill me-1"></i>Completed on {{ $existingCpia->completed_at->format('d/m/Y') }}</span>
                        @elseif($existingCpia)
                            Assessment in progress — {{ \App\Models\CpiaResponse::where('assessment_id', $existingCpia->id)->whereNotNull('impact_score')->count() }} items scored.
                        @else
                            Ready to fill — meeting scheduled and critical phases identified.
                        @endif
                    @endif
                </div>
            </div>
            <div class="d-flex gap-2" style="pointer-events:auto !important;">
                {{-- Download — always clickable if assessment exists --}}
                @if($cpiaEnabled && isset($existingCpia) && $existingCpia)
                <a href="{{ route('cpia.print', $project_id) }}"
                   target="_blank"
                   data-no-lock="1"
                   class="btn btn-sm fw-semibold"
                   style="background:rgba(193,2,2,.12);color:#C10202;border:1px solid #C10202;">
                    <i class="bi bi-download me-1"></i>Download
                </a>
                @endif
                <a href="{{ $cpiaEnabled ? route('cpia.index', $project_id) : '#' }}"
                   target="{{ $cpiaEnabled ? '_blank' : '' }}"
                   data-no-lock="1"
                   class="btn btn-sm fw-semibold {{ $cpiaEnabled ? '' : 'disabled' }}"
                   style="background:#C10202;color:#fff;opacity:{{ $cpiaEnabled ? '1' : '.45' }};">
                    <i class="bi bi-clipboard2-pulse me-1"></i>
                    Open Impact Assessment
                </a>
            </div>
        </div>
    </div>
    @endif {{-- /is_glp CPIA --}}

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

    let planningCalendar = null;

    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');

        planningCalendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'en',
            themeSystem: 'bootstrap5',
            height: 'auto',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            events: events,
            eventClick: function(info) {
                alert("📌 " + info.event.title + "\n📅 " + info.event.start.toLocaleDateString());
            }
        });

        planningCalendar.render();

        // Re-calculate dimensions each time the planning tab becomes visible
        const step4Tab = document.getElementById('step4-tab');
        if (step4Tab) {
            step4Tab.addEventListener('shown.bs.tab', function () {
                planningCalendar.updateSize();
            });
        }
    });
</script>

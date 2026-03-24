<style>
    :root {
        --brand: #C10202;
        /* Rouge AIRID */
        --brand-dark: #8b0001;
        /* Rouge foncé pour hover/gradients */
        --text-main: #010101;
        /* Noir institutionnel */
        --text-secondary: #706D6B;
        /* Gris foncé institutionnel */
        --bg-main: #FFFFFF;
        /* Fond principal */
        --border-muted: #E5E5E5;
    }

    .airid-exper .card {
        border-radius: 16px;
        border: 1px solid var(--border-muted);
        box-shadow: 0 8px 20px rgba(0, 0, 0, .04);
        background-color: var(--bg-main);
    }

    .airid-exper .card-header {
        background: linear-gradient(90deg, var(--brand), var(--brand-dark));
        color: #fff;
        font-weight: 600;
        letter-spacing: .02em;
        border-bottom: none;
    }

    .airid-exper .table thead th {
        background: linear-gradient(90deg, var(--brand), var(--brand-dark));
        color: #fff;
        border: none;
        font-weight: 600;
    }

    .airid-exper .table tbody tr {
        vertical-align: middle;
    }

    .airid-exper .badge.bg-success {
        background-color: #198754;
        border-radius: 999px;
        padding: .25rem .6rem;
        font-weight: 600;
    }

    .airid-exper .btn-success {
        background-color: var(--brand);
        border-color: var(--brand);
        font-weight: 600;
    }

    .airid-exper .btn-success:hover {
        background-color: var(--brand-dark);
        border-color: var(--brand-dark);
    }

    .airid-exper .btn-outline-secondary {
        border-radius: 999px;
        color: var(--text-secondary);
        border-color: var(--border-muted);
    }

    .airid-exper #calendar-experimental-phase {
        border-radius: 14px;
        border: 1px solid var(--border-muted);
        background-color: var(--bg-main);
        box-shadow: 0 6px 18px rgba(0, 0, 0, .04);
    }

    .airid-exper .modal-header.bg-success {
        background: linear-gradient(90deg, var(--brand), var(--brand-dark)) !important;
    }

    .airid-exper .modal-footer .btn-success {
        min-width: 200px;
    }
</style>

<div class="row airid-exper">
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

                // IDs of critical-phase activities that already have an inspection performed
                $inspectedCriticalIds = \DB::table('pro_qa_inspections')
                    ->whereNotNull('activity_id')
                    ->whereNotNull('date_performed')
                    ->pluck('activity_id')
                    ->toArray();

                // Whether the experimental phase has been manually marked as completed
                $experimentalPhaseDone = in_array('experimental', $project->phases_completed ?? []);

            @endphp
            <div class="card-body">
                <table class="table table-hover table-striped align-middle text-left">
                    <thead class="table-dark">

                        <tr>
                            <th class="col">Activity</th>
                            <th class="col">Date Start</th>
                            <th class="col">Date End </th>
                            <th class="col">Actual Date</th>
                            <th class="col">Status</th>
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
                                <td>
                                    @if ($activite->actual_activity_date)
                                        <strong class="text-success">{{ $activite->actual_activity_date }}</strong>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($activite->status === 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @elseif ($activite->status === 'in_progress')
                                        <span class="badge bg-warning text-dark">In Progress</span>
                                    @elseif ($activite->status === 'delayed')
                                        <span class="badge bg-danger">Delayed</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($activite->status) }}</span>
                                    @endif
                                </td>
                                <td>{{ $activite->ParentActivity ? $activite->ParentActivity->study_activity_name : 'N/A' }}
                                </td>
                                <td>{{ $activite->personneResponsable ? $activite->personneResponsable->prenom . ' ' . $activite->personneResponsable->nom : 'N/A' }}
                                </td>
                                <td>
                                    @php
                                        $canReset = $activite->status === 'completed'
                                            && !$experimentalPhaseDone
                                            && !($activite->phase_critique && in_array($activite->id, $inspectedCriticalIds));
                                    @endphp
                                    @if ($activite->status !== 'completed')
                                        <button class="btn btn-success btn-sm" onclick="openExecuteActivityModal({{ $activite->id }}, '{{ addslashes($activite->study_activity_name) }}')">
                                            <i class="bi bi-play-circle"></i> Exécuter
                                        </button>
                                    @else
                                        <div class="d-flex flex-column gap-1">
                                            <span class="badge bg-success">✓ Done</span>
                                            @if($canReset)
                                                <button class="btn btn-outline-secondary btn-sm" onclick="resetActivityStatus({{ $activite->id }})">
                                                    <i class="bi bi-arrow-counterclockwise"></i> Revenir en pending
                                                </button>
                                            @elseif($experimentalPhaseDone)
                                                <span class="text-muted" style="font-size:.72rem;">
                                                    <i class="bi bi-lock me-1"></i>Phase complétée
                                                </span>
                                            @else
                                                <span class="text-muted" style="font-size:.72rem;">
                                                    <i class="bi bi-shield-check me-1"></i>Inspection réalisée
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">No activity scheduled yet</td>
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

    {{-- Historique des exécutions --}}
    @include('partials.activity-execution-history')
</div>

<!-- Zone des toasts Bootstrap -->
<div aria-live="polite" aria-atomic="true" class="position-relative">
    <div class="toast-container position-fixed top-0 end-0 p-3" id="toastContainer">
        <!-- Les toasts seront injectés ici dynamiquement -->
    </div>
</div>

<!-- Modal pour exécuter une activité -->
<div class="modal fade" id="executeActivityModal" tabindex="-1" aria-labelledby="executeActivityLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="executeActivityLabel">
                    <i class="bi bi-play-circle"></i> Enregistrer l'exécution de l'activité
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Activité</label>
                    <p class="form-control-plaintext" id="activityNameDisplay"></p>
                    <input type="hidden" id="activityIdHidden">
                </div>

                <div class="mb-3">
                    <label for="actualActivityDate" class="form-label fw-bold">Date d'exécution réelle <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="actualActivityDate" required>
                </div>

                <div class="mb-3">
                    <label for="performedBySelect" class="form-label fw-bold">Exécuté par <span class="text-danger">*</span></label>
                    <select class="form-select" id="performedBySelect" required>
                        <option value="">-- Sélectionner une personne --</option>
                        @foreach ($all_personnels ?? [] as $personnel)
                            <option value="{{ $personnel->id }}">{{ $personnel->prenom }} {{ $personnel->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="activityComments" class="form-label">Commentaires</label>
                    <textarea class="form-control" id="activityComments" rows="3" placeholder="Ajoutez des commentaires si nécessaire..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-success" onclick="saveActivityExecution()">
                    <i class="bi bi-check-circle"></i> Enregistrer l'exécution
                </button>
            </div>
        </div>
    </div>
</div>


<script>

    const events_all_activities = @json($events_all_activities);

    let experimentalCalendar = null;

    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar-experimental-phase');
        const dateInput = document.getElementById('actualActivityDate');
        if (dateInput) {
            const today = new Date().toISOString().split('T')[0];
            dateInput.setAttribute('max', today);
        }

        experimentalCalendar = new FullCalendar.Calendar(calendarEl, {
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
            events: events_all_activities,
        });

        experimentalCalendar.render();

        // Re-calculate dimensions each time the experimental tab becomes visible
        const step5Tab = document.getElementById('step5-tab');
        if (step5Tab) {
            step5Tab.addEventListener('shown.bs.tab', function () {
                experimentalCalendar.updateSize();
            });
        }
    });

    function showToast(message, type = 'success') {
        const toastContainer = document.getElementById('toastContainer');
        if (!toastContainer) return;

        const bgClass = type === 'success' ? 'bg-success' : (type === 'error' ? 'bg-danger' : 'bg-info');

        const toastEl = document.createElement('div');
        toastEl.className = `toast align-items-center text-white ${bgClass} border-0`;
        toastEl.setAttribute('role', 'alert');
        toastEl.setAttribute('aria-live', 'assertive');
        toastEl.setAttribute('aria-atomic', 'true');

        toastEl.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;

        toastContainer.appendChild(toastEl);

        const bsToast = new bootstrap.Toast(toastEl, { delay: 4000 });
        bsToast.show();

        toastEl.addEventListener('hidden.bs.toast', () => {
            toastEl.remove();
        });
    }

    function openExecuteActivityModal(activityId, activityName) {
        document.getElementById('activityIdHidden').value = activityId;
        document.getElementById('activityNameDisplay').textContent = activityName;
        document.getElementById('actualActivityDate').value = '';
        document.getElementById('performedBySelect').value = '';
        document.getElementById('activityComments').value = '';

        const el = document.getElementById('executeActivityModal');
        const existing = bootstrap.Modal.getInstance(el);
        if (existing) existing.dispose();
        new bootstrap.Modal(el, {}).show();
    }

    function saveActivityExecution() {
        const activityId = document.getElementById('activityIdHidden').value;
        const project_id = "{{ request('project_id') }}";
        const actualDate = document.getElementById('actualActivityDate').value;
        const performedBy = document.getElementById('performedBySelect').value;
        const comments = document.getElementById('activityComments').value;

        if (!actualDate || !performedBy) {
            showToast('Veuillez remplir tous les champs obligatoires', 'error');
            return;
        }

        // Vérifier que la date n\'est pas ultérieure à aujourd\'hui
        const today = new Date().toISOString().split('T')[0];
        if (actualDate > today) {
            showToast('La date réelle d\'exécution ne peut pas être ultérieure à aujourd\'hui', 'error');
            return;
        }

        const formData = new FormData();
        formData.append('activity_id', activityId);
        formData.append('project_id', project_id);
        formData.append('actual_activity_date', actualDate);
        formData.append('performed_by', performedBy);
        formData.append('comments', comments);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        fetch('/ajax/execute-activity', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Activité enregistrée avec succès!', 'success');
                bootstrap.Modal.getInstance(document.getElementById('executeActivityModal')).hide();
                setTimeout(() => {
                    location.reload();
                }, 800);
            } else {
                showToast('Erreur: ' + (data.message || 'Une erreur est survenue'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Erreur lors de la sauvegarde', 'error');
        });
    }

    function resetActivityStatus(activityId) {
        if (!confirm('Voulez-vous vraiment remettre cette activité en mode pending ?')) {
            return;
        }

        const formData = new FormData();
        formData.append('activity_id', activityId);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        fetch('/ajax/reset-activity', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Activité remise en pending avec succès', 'success');
                setTimeout(() => {
                    location.reload();
                }, 800);
            } else {
                showToast('Erreur: ' + (data.message || 'Une erreur est survenue'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Erreur lors de la remise en pending', 'error');
        });
    }
</script>

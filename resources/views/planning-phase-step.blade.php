<div class="container">

    @php
        $project_id = request('project_id');

        
        $project = App\Models\Pro_Project::find($project_id);

        $study_initiation_meeting = App\Models\Pro_StudyQualityAssuranceMeeting::where("project_id",$project_id)
        ->where("meeting_type","study_initiation_meeting")
        ->first();
    @endphp

    @if (!$study_initiation_meeting)
        <!-- Bouton principal -->
        <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#meetingModal">
            Schedule Study Initiation Meeting
        </button>

        @else
         <!-- Bouton principal -->
        <button type="button" class="btn btn-info mb-4" data-bs-toggle="modal" data-bs-target="#meetingModal">
            Modify the Meeting's Info
        </button>
    @endif


    @include('partials.modal-schedule-meeting')


    <!-- Tableau des réunions -->
    <h4 class="mb-3">Meeting Details</h4>
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>Date</th>
                <th>Heure</th>
                <th>Participants</th>
                <th>Lien</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>2025-09-15</td>
                <td>14:00</td>
                <td>John, Alice, Marc</td>
                <td><a href="#">Lien Zoom</a></td>
                <td>
                    <button class="btn btn-sm btn-warning">Edit</button>
                    <button class="btn btn-sm btn-danger">Delete</button>
                    <button class="btn btn-sm btn-info" data-bs-toggle="modal"
                        data-bs-target="#participantsModal">Voir</button>
                </td>
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
                        <li>John</li>
                        <li>Alice</li>
                        <li>Marc</li>
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
    <table class="table table-bordered align-middle">
        <thead class="table-secondary">
            <tr>
                <th>Activité</th>
                <th>Status</th>
                <th>Critique</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Préparation des documents</td>
                <td>En cours</td>
                <td>
                    <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                        data-bs-target="#criticalModal">Marquer Critique</button>
                </td>
            </tr>
            <tr>
                <td>Vérification des données</td>
                <td>Terminé</td>
                <td>
                    <button class="btn btn-outline-success btn-sm">Non Critique</button>
                </td>
            </tr>
        </tbody>
    </table>


    <!-- Modal Critique -->
    <div class="modal fade" id="criticalModal" tabindex="-1" aria-labelledby="criticalModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="criticalModalLabel">Définir une Inspection</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="criticalForm">
                        <div class="mb-3">
                            <label for="inspectionDate" class="form-label">Date d'inspection</label>
                            <input type="date" class="form-control" id="inspectionDate" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" form="criticalForm" class="btn btn-danger">Valider</button>
                </div>
            </div>
        </div>
    </div>


</div>

<script>
    // Gestion des réunions
    const meetingForm = document.getElementById('meetingForm');
    const meetingsTableBody = document.querySelector('#meetingsTable tbody');
    const participantsList = document.getElementById('participantsList');

    meetingForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const date = document.getElementById('meetingDate').value;
        const time = document.getElementById('meetingTime').value;
        const participants = document.getElementById('participants').value;
        const link = document.getElementById('meetingLink').value;


        const row = document.createElement('tr');
        row.innerHTML = `
<td>${date}</td>
<td>${time}</td>
<td>${participants}</td>
<td>${link ? `<a href="${link}" target="_blank">Lien</a>` : ''}</td>
<td>
<button class="btn btn-sm btn-warning edit-btn">Edit</button>
<button class="btn btn-sm btn-danger delete-btn">Delete</button>
<button class="btn btn-sm btn-info view-btn" data-participants="${participants}">Voir</button>
</td>
`;
        meetingsTableBody.appendChild(row);
        meetingForm.reset();
        bootstrap.Modal.getInstance(document.getElementById('meetingModal')).hide();
    });


    // Gestion actions tableau réunions
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-btn')) {
            e.target.closest('tr').remove();
        }
        if (e.target.classList.contains('view-btn')) {
            const participants = e.target.getAttribute('data-participants').split(',');
            participantsList.innerHTML = '<ul>' + participants.map(p => `<li>${p.trim()}</li>`).join('') +
                '</ul>';
            new bootstrap.Modal(document.getElementById('participantsModal')).show();
        }
    });


    // Gestion des activités critiques
    const criticalForm = document.getElementById('criticalForm');
    const criticalActivityInput = document.getElementById('criticalActivity');


    // Ouvrir modal critique
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('toggle-critical')) {
            const activity = e.target.dataset.activity;
            if (e.target.textContent.includes('Marquer')) {
                criticalActivityInput.value = activity;
                new bootstrap.Modal(document.getElementById('criticalModal')).show();
            } else {
                e.target.textContent = 'Marquer Critique';
                e.target.classList.remove('btn-outline-success');
                e.target.classList.add('btn-outline-danger');
            }
        }
    });


    // Validation critique
    criticalForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const inspectionDate = document.getElementById('inspectionDate').value;
        const activity = criticalActivityInput.value;
        if (inspectionDate) {
            // Trouver bouton correspondant
            document.querySelectorAll('.toggle-critical').forEach(btn => {
                if (btn.dataset.activity === activity) {
                    btn.textContent = `Critique (Inspection: ${inspectionDate})`;
                    btn.classList.remove('btn-outline-danger');
                    btn.classList.add('btn-outline-success');
                }
            });
            bootstrap.Modal.getInstance(document.getElementById('criticalModal')).hide();
            criticalForm.reset();
        }
    });
</script>

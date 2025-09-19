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
                            <input type="text" class="form-control" id="inspectionDate" required>
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

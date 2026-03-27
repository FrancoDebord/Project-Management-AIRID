<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 overflow-hidden">
            <div class="modal-header" style="background:linear-gradient(135deg,#1a3a6b,#2a5aaa);color:#fff;">
                <h5 class="modal-title fw-bold" id="detailsModalLabel">
                    <i class="bi bi-file-earmark-arrow-up me-2"></i>
                    <span id="pdModalTitleText">Soumettre un document</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <div id="pdModalError" class="alert alert-danger d-none small py-2 mb-3"></div>

                {{-- Current document banner (shown when updating) --}}
                <div id="pdCurrentDocBanner" class="alert alert-success d-none small py-2 mb-3">
                    <i class="bi bi-file-earmark-check-fill me-2"></i>
                    Document actuel :
                    <a id="pdCurrentDocLink" href="#" target="_blank" class="fw-semibold">Voir le fichier actuel</a>
                    — le nouveau fichier le remplacera (optionnel).
                </div>

                <input type="hidden" id="pdRecordId" value="">
                <input type="hidden" id="pdDocId" value="">

                <div class="row g-3">
                    {{-- Activity name --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Activité</label>
                        <input type="text" class="form-control bg-light" id="pdActivityName" readonly>
                    </div>

                    {{-- Date performed --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">
                            Date de réalisation <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control" id="pdDatePerformed">
                        <div id="pdDateHint" class="form-text text-muted" style="font-size:.75rem;"></div>
                    </div>

                    {{-- File upload --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">
                            Fichier (PDF) <span id="pdFileRequired" class="text-danger">*</span>
                        </label>
                        <input type="file" class="form-control" id="pdDocumentFile" accept="application/pdf">
                        <div class="form-text" style="font-size:.73rem;">Format PDF uniquement — max 10 Mo</div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn fw-semibold px-4" id="pdSaveBtn"
                        style="background:#1a3a6b;color:#fff;"
                        onclick="submitProtocolDevDoc()">
                    <i class="bi bi-save me-1"></i>Enregistrer
                </button>
            </div>
        </div>
    </div>
</div>

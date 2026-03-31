{{--
    Reusable Electronic Signature Modal
    Usage: @include('partials.signature-modal')
    Trigger JS: openSignatureModal(documentType, documentId, roleInDocument, label)
--}}

<div class="modal fade" id="signatureModal" tabindex="-1" aria-labelledby="signatureModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:520px;">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <div class="modal-header border-0 pb-0" style="background:linear-gradient(90deg,#1a3a6b,#c41230);border-radius:1rem 1rem 0 0;">
                <div>
                    <h5 class="modal-title text-white fw-bold mb-0" id="signatureModalLabel">
                        <i class="bi bi-pen-fill me-2"></i>Electronic Signature
                    </h5>
                    <p class="text-white-50 small mb-0" id="sigModalSubtitle"></p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body px-4 py-3">
                <p class="text-muted small mb-2">Draw your signature in the box below:</p>

                <div style="border:2px solid #dee2e6;border-radius:8px;background:#fafafa;position:relative;">
                    <canvas id="signatureCanvas" width="460" height="160"
                            style="width:100%;height:160px;cursor:crosshair;border-radius:6px;display:block;"></canvas>
                    <button type="button" id="clearSigBtn"
                            style="position:absolute;top:6px;right:8px;background:none;border:none;color:#adb5bd;font-size:.75rem;cursor:pointer;">
                        <i class="bi bi-trash"></i> Clear
                    </button>
                </div>

                <div class="mt-3">
                    <label class="form-label small fw-semibold">Signer name <span class="text-danger">*</span></label>
                    <input type="text" id="signerName" class="form-control form-control-sm"
                           value="{{ auth()->user()?->personnel ? trim(auth()->user()->personnel->prenom . ' ' . auth()->user()->personnel->nom) : auth()->user()?->name }}"
                           placeholder="Full name">
                </div>

                <div id="sigError" class="alert alert-danger py-2 px-3 mt-2 small d-none"></div>
            </div>

            <div class="modal-footer border-0 pt-0 px-4 pb-4 gap-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm fw-semibold text-white" id="submitSignatureBtn"
                        style="background:linear-gradient(90deg,#1a3a6b,#c41230);border:none;min-width:130px;">
                    <i class="bi bi-pen-fill me-1"></i>Sign Document
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const canvas  = document.getElementById('signatureCanvas');
    const ctx     = canvas.getContext('2d');
    let drawing   = false;
    let lastX, lastY;

    // Hi-DPI canvas
    const dpr = window.devicePixelRatio || 1;
    canvas.width  = 460 * dpr;
    canvas.height = 160 * dpr;
    ctx.scale(dpr, dpr);
    ctx.strokeStyle = '#1a3a6b';
    ctx.lineWidth   = 2.5;
    ctx.lineCap     = 'round';
    ctx.lineJoin    = 'round';

    function getPos(e) {
        const r = canvas.getBoundingClientRect();
        const src = e.touches ? e.touches[0] : e;
        return [(src.clientX - r.left) * (460 / r.width), (src.clientY - r.top) * (160 / r.height)];
    }

    canvas.addEventListener('mousedown',  e => { drawing = true; [lastX, lastY] = getPos(e); });
    canvas.addEventListener('mousemove',  e => {
        if (!drawing) return;
        const [x, y] = getPos(e);
        ctx.beginPath(); ctx.moveTo(lastX, lastY); ctx.lineTo(x, y); ctx.stroke();
        [lastX, lastY] = [x, y];
    });
    canvas.addEventListener('mouseup',   () => drawing = false);
    canvas.addEventListener('mouseleave',() => drawing = false);
    canvas.addEventListener('touchstart', e => { e.preventDefault(); drawing = true; [lastX, lastY] = getPos(e); }, { passive: false });
    canvas.addEventListener('touchmove',  e => {
        e.preventDefault();
        if (!drawing) return;
        const [x, y] = getPos(e);
        ctx.beginPath(); ctx.moveTo(lastX, lastY); ctx.lineTo(x, y); ctx.stroke();
        [lastX, lastY] = [x, y];
    }, { passive: false });
    canvas.addEventListener('touchend', () => drawing = false);

    document.getElementById('clearSigBtn').addEventListener('click', () => {
        ctx.clearRect(0, 0, 460, 160);
    });

    // State
    let _docType, _docId, _role;

    window.openSignatureModal = function(documentType, documentId, roleInDocument, label) {
        _docType = documentType;
        _docId   = documentId;
        _role    = roleInDocument;
        document.getElementById('sigModalSubtitle').textContent = label || '';
        document.getElementById('sigError').classList.add('d-none');
        ctx.clearRect(0, 0, 460, 160);
        new bootstrap.Modal(document.getElementById('signatureModal')).show();
    };

    document.getElementById('submitSignatureBtn').addEventListener('click', () => {
        const signerName = document.getElementById('signerName').value.trim();
        const errEl      = document.getElementById('sigError');

        // Check canvas not empty
        const blank = document.createElement('canvas');
        blank.width = canvas.width; blank.height = canvas.height;
        if (canvas.toDataURL() === blank.toDataURL()) {
            errEl.textContent = 'Please draw your signature before submitting.';
            errEl.classList.remove('d-none');
            return;
        }
        if (!signerName) {
            errEl.textContent = 'Please enter your name.';
            errEl.classList.remove('d-none');
            return;
        }
        errEl.classList.add('d-none');

        const btn = document.getElementById('submitSignatureBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Signing…';

        const CSRF = document.querySelector('meta[name="csrf-token"]')?.content;

        fetch('/ajax/save-signature', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({
                document_type:    _docType,
                document_id:      _docId,
                role_in_document: _role,
                signature_data:   canvas.toDataURL('image/png'),
                signer_name:      signerName,
            }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('signatureModal')).hide();
                alertify.success('Document signed successfully.');
                // Trigger custom event so pages can refresh signature blocks
                document.dispatchEvent(new CustomEvent('signatureAdded', {
                    detail: { documentType: _docType, documentId: _docId, role: _role, signerName: data.signer_name, signedAt: data.signed_at }
                }));
            } else {
                errEl.textContent = data.message || 'Error saving signature.';
                errEl.classList.remove('d-none');
            }
        })
        .catch(() => {
            errEl.textContent = 'Network error. Please try again.';
            errEl.classList.remove('d-none');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-pen-fill me-1"></i>Sign Document';
        });
    });
})();
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign Document — {{ $docInfo['title'] }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; font-family: Arial, sans-serif; min-height: 100vh; }

        .sign-card {
            max-width: 720px;
            margin: 40px auto 60px;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 6px 32px rgba(0,0,0,.12);
            overflow: hidden;
        }

        .sign-header {
            background: linear-gradient(90deg, #1a3a6b, #c41230);
            padding: 22px 28px;
        }

        .sig-box {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 14px 16px;
            margin-bottom: 10px;
        }
        .sig-box.signed  { background: #f0fff4; border-color: #198754; }
        .sig-box.pending { background: #fffdf0; border-color: #ffc107; }

        .sig-image {
            max-height: 56px;
            max-width: 200px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            background: #fff;
            display: block;
        }

        /* Tab switcher */
        .sig-mode-tabs { display: flex; gap: 0; border: 1px solid #dee2e6; border-radius: 8px; overflow: hidden; margin-bottom: 14px; }
        .sig-mode-tab {
            flex: 1; padding: 8px 12px; text-align: center; cursor: pointer;
            font-size: .85rem; font-weight: 600; color: #6c757d;
            background: #f8f9fa; border: none; transition: all .2s;
        }
        .sig-mode-tab.active { background: #1a3a6b; color: #fff; }

        /* Canvas pad */
        .canvas-wrap {
            border: 2px solid #c8d0e0;
            border-radius: 8px;
            background: #fafbff;
            position: relative;
            overflow: hidden;
        }
        #signatureCanvas {
            width: 100%;
            height: 160px;
            cursor: crosshair;
            display: block;
            touch-action: none;
        }
        #clearBtn {
            position: absolute; top: 6px; right: 8px;
            background: none; border: none; color: #adb5bd;
            font-size: .75rem; cursor: pointer;
        }
        #clearBtn:hover { color: #dc3545; }

        /* Upload pad */
        .upload-drop {
            border: 2px dashed #c8d0e0;
            border-radius: 8px;
            background: #fafbff;
            min-height: 140px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: border-color .2s, background .2s;
            position: relative;
        }
        .upload-drop:hover, .upload-drop.drag-over { border-color: #1a3a6b; background: #f0f4ff; }
        #uploadPreview { max-height: 120px; max-width: 100%; display: block; border-radius: 4px; }
        #fileInput { display: none; }

        .submit-btn {
            background: linear-gradient(90deg, #1a3a6b, #c41230);
            border: none; min-width: 160px;
        }

        .role-label {
            font-size: .7rem; font-weight: 600; text-transform: uppercase;
            letter-spacing: .05em; color: #6c757d; background: #f0f2f5;
            border-radius: 20px; padding: 2px 10px;
        }
        .role-label.signed  { background: #d1f7e0; color: #198754; }
        .role-label.pending { background: #fff3cd; color: #856404; }

        .date-chip {
            background: #f0f4ff; border: 1px solid #c8d0e0; border-radius: 6px;
            padding: 6px 12px; font-size: .85rem; color: #1a3a6b;
        }
    </style>
</head>
<body>

<div class="sign-card">

    {{-- Header --}}
    <div class="sign-header">
        <div class="d-flex align-items-center gap-3">
            <img src="{{ asset('storage/assets/logo/airid.jpg') }}"
                 alt="AIRID"
                 style="height:52px;border-radius:6px;background:#fff;padding:2px;">
            <div>
                <h5 class="text-white fw-bold mb-0">{{ $docInfo['title'] }}</h5>
                <p class="mb-0 small" style="color:rgba(255,255,255,.75);">{{ $docInfo['subtitle'] }}</p>
                @if($docInfo['date'])
                    <p class="mb-0 small" style="color:rgba(255,255,255,.6);">
                        <i class="bi bi-calendar3 me-1"></i>{{ $docInfo['date'] }}
                    </p>
                @endif
            </div>
        </div>
    </div>

    <div class="p-4">

        {{-- View document link --}}
        <div class="d-flex justify-content-between align-items-center mb-4 p-3 rounded"
             style="background:#f8f9fa;border:1px solid #e9ecef;">
            <span class="text-muted small">
                <i class="bi bi-info-circle me-1"></i>
                Review the document before signing.
            </span>
            <a href="{{ $docInfo['view_url'] }}" target="_blank"
               class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-eye me-1"></i>View Document
            </a>
        </div>

        {{-- Signatories status --}}
        <h6 class="fw-bold mb-3">
            <i class="bi bi-people me-1"></i>Signatories
        </h6>

        @foreach($requiredRoles as $role)
            @php $sig = $signatures->get($role); @endphp
            <div class="sig-box {{ $sig ? 'signed' : 'pending' }}">
                <div class="d-flex justify-content-between align-items-center gap-2">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="fw-semibold">
                                {{ \Illuminate\Support\Str::title(str_replace('_', ' ', $role)) }}
                            </span>
                            <span class="role-label {{ $sig ? 'signed' : 'pending' }}">
                                {{ $sig ? 'Signed' : 'Pending' }}
                            </span>
                        </div>
                        @if($sig)
                            <div class="text-muted small">
                                <i class="bi bi-person me-1"></i>{{ $sig->signer_name }}
                                &nbsp;·&nbsp;
                                <i class="bi bi-calendar3 me-1"></i>{{ $sig->signed_at?->format('d/m/Y') }}
                                &nbsp;·&nbsp;
                                <i class="bi bi-clock me-1"></i>{{ $sig->signed_at?->format('H:i') }}
                            </div>
                        @else
                            <div class="text-muted small">Awaiting signature</div>
                        @endif
                    </div>
                    @if($sig)
                        <div class="text-end">
                            <img src="{{ $sig->signature_data }}" alt="signature" class="sig-image">
                        </div>
                    @else
                        <i class="bi bi-hourglass-split text-warning fs-5"></i>
                    @endif
                </div>
            </div>
        @endforeach

        {{-- Current user signing area --}}
        @if($userRole || $canChooseRole)
            @if($alreadySigned)
                <div class="alert alert-success mt-4 mb-0">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    You have already signed this document as
                    <strong>{{ \Illuminate\Support\Str::title(str_replace('_', ' ', $userRole)) }}</strong>.
                </div>
            @elseif($canChooseRole && count($availableRoles) === 0)
                <div class="alert alert-info mt-4 mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    All required roles have already been signed.
                </div>
            @else
                <div class="mt-4 p-4 rounded-3 border" style="background:#f8f9ff;border-color:#c8d0e0 !important;">
                    <h6 class="fw-bold mb-1">
                        <i class="bi bi-pen-fill me-1"></i>Your Signature
                    </h6>

                    {{-- Role selector for super_admin --}}
                    @if($canChooseRole)
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">
                            Signing as <span class="text-danger">*</span>
                        </label>
                        <select id="roleSelector" class="form-select form-select-sm">
                            <option value="">— Select your role for this document —</option>
                            @foreach($availableRoles as $r)
                                <option value="{{ $r }}">{{ \Illuminate\Support\Str::title(str_replace('_', ' ', $r)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    @else
                    <p class="text-muted small mb-3">
                        Signing as: <strong>{{ \Illuminate\Support\Str::title(str_replace('_', ' ', $userRole)) }}</strong>
                        &nbsp;·&nbsp;
                        <span class="date-chip">
                            <i class="bi bi-calendar-check me-1"></i>
                            Signing date: <strong>{{ now()->format('d/m/Y') }}</strong>
                        </span>
                    </p>
                    @endif

                    {{-- Mode switcher: Draw / Upload --}}
                    <div class="sig-mode-tabs mb-3">
                        <button type="button" class="sig-mode-tab active" id="tab-draw" onclick="switchMode('draw')">
                            <i class="bi bi-pen me-1"></i>Draw signature
                        </button>
                        <button type="button" class="sig-mode-tab" id="tab-upload" onclick="switchMode('upload')">
                            <i class="bi bi-upload me-1"></i>Upload image
                        </button>
                    </div>

                    {{-- Draw panel --}}
                    <div id="panel-draw">
                        <p class="text-muted small mb-1">Draw your signature in the box below:</p>
                        <div class="canvas-wrap mb-2">
                            <canvas id="signatureCanvas" width="640" height="160"></canvas>
                            <button type="button" id="clearBtn" title="Clear">
                                <i class="bi bi-trash"></i> Clear
                            </button>
                        </div>
                    </div>

                    {{-- Upload panel --}}
                    <div id="panel-upload" style="display:none;">
                        <p class="text-muted small mb-1">Upload an image of your signature (PNG, JPG, GIF):</p>
                        <div class="upload-drop" id="uploadDrop" onclick="document.getElementById('fileInput').click()">
                            <div id="uploadPlaceholder" class="text-center text-muted">
                                <i class="bi bi-cloud-upload fs-2 d-block mb-2"></i>
                                <span class="small">Click or drag & drop a signature image</span>
                            </div>
                            <img id="uploadPreview" src="" alt="" style="display:none;">
                        </div>
                        <input type="file" id="fileInput" accept="image/png,image/jpeg,image/gif">
                    </div>

                    <div class="mb-3 mt-3">
                        <label class="form-label small fw-semibold">
                            Full name <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="signerName" class="form-control form-control-sm"
                               value="{{ auth()->user()?->personnel
                                    ? trim(auth()->user()->personnel->prenom . ' ' . auth()->user()->personnel->nom)
                                    : auth()->user()?->name }}"
                               placeholder="Full name">
                    </div>

                    <div id="sigError" class="alert alert-danger py-2 px-3 small d-none"></div>

                    <div class="d-flex align-items-center gap-3">
                        <button type="button" id="submitSignBtn"
                                class="btn btn-sm fw-semibold text-white submit-btn">
                            <i class="bi bi-pen-fill me-1"></i>Sign Document
                        </button>
                        <span class="text-muted small">
                            <i class="bi bi-shield-lock me-1"></i>
                            Recorded with your name, {{ now()->format('d/m/Y') }}, and IP address.
                        </span>
                    </div>
                </div>
            @endif
        @else
            <div class="alert alert-warning mt-4 mb-0">
                <i class="bi bi-person-x me-2"></i>
                Your account (role: <strong>{{ auth()->user()?->role }}</strong>) does not have a signing role for this document type.
                If you should be able to sign, please ask your administrator to assign the correct role to your account.
            </div>
        @endif

    </div>
</div><!-- /.sign-card -->

@if(($userRole || $canChooseRole) && !$alreadySigned && !($canChooseRole && count($availableRoles) === 0))
<script>
(function () {
    // ── Canvas drawing ──────────────────────────────────────────
    const canvas = document.getElementById('signatureCanvas');
    const ctx    = canvas.getContext('2d');
    let drawing  = false, lastX = 0, lastY = 0;

    const dpr     = window.devicePixelRatio || 1;
    canvas.width  = 640 * dpr;
    canvas.height = 160 * dpr;
    ctx.scale(dpr, dpr);
    ctx.strokeStyle = '#1a3a6b';
    ctx.lineWidth   = 2.5;
    ctx.lineCap     = 'round';
    ctx.lineJoin    = 'round';

    function getPos(e) {
        const r   = canvas.getBoundingClientRect();
        const src = e.touches ? e.touches[0] : e;
        return [
            (src.clientX - r.left) * (640 / r.width),
            (src.clientY - r.top)  * (160 / r.height),
        ];
    }

    canvas.addEventListener('mousedown',  e => { drawing = true; [lastX, lastY] = getPos(e); });
    canvas.addEventListener('mousemove',  e => {
        if (!drawing) return;
        const [x, y] = getPos(e);
        ctx.beginPath(); ctx.moveTo(lastX, lastY); ctx.lineTo(x, y); ctx.stroke();
        [lastX, lastY] = [x, y];
    });
    canvas.addEventListener('mouseup',    () => drawing = false);
    canvas.addEventListener('mouseleave', () => drawing = false);
    canvas.addEventListener('touchstart', e => {
        e.preventDefault(); drawing = true; [lastX, lastY] = getPos(e);
    }, { passive: false });
    canvas.addEventListener('touchmove', e => {
        e.preventDefault();
        if (!drawing) return;
        const [x, y] = getPos(e);
        ctx.beginPath(); ctx.moveTo(lastX, lastY); ctx.lineTo(x, y); ctx.stroke();
        [lastX, lastY] = [x, y];
    }, { passive: false });
    canvas.addEventListener('touchend', () => drawing = false);

    document.getElementById('clearBtn').addEventListener('click', () => {
        ctx.clearRect(0, 0, 640, 160);
    });

    // ── Image upload ────────────────────────────────────────────
    let uploadedDataUrl = null;

    const fileInput   = document.getElementById('fileInput');
    const uploadDrop  = document.getElementById('uploadDrop');
    const uploadPreview = document.getElementById('uploadPreview');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');

    function loadImageFile(file) {
        if (!file || !file.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = e => {
            uploadedDataUrl = e.target.result;
            uploadPreview.src = uploadedDataUrl;
            uploadPreview.style.display = 'block';
            uploadPlaceholder.style.display = 'none';
        };
        reader.readAsDataURL(file);
    }

    fileInput.addEventListener('change', () => loadImageFile(fileInput.files[0]));

    uploadDrop.addEventListener('dragover', e => { e.preventDefault(); uploadDrop.classList.add('drag-over'); });
    uploadDrop.addEventListener('dragleave', () => uploadDrop.classList.remove('drag-over'));
    uploadDrop.addEventListener('drop', e => {
        e.preventDefault();
        uploadDrop.classList.remove('drag-over');
        loadImageFile(e.dataTransfer.files[0]);
    });

    // ── Mode switcher ───────────────────────────────────────────
    let currentMode = 'draw';

    window.switchMode = function(mode) {
        currentMode = mode;
        document.getElementById('panel-draw').style.display   = mode === 'draw'   ? '' : 'none';
        document.getElementById('panel-upload').style.display = mode === 'upload' ? '' : 'none';
        document.getElementById('tab-draw').classList.toggle('active', mode === 'draw');
        document.getElementById('tab-upload').classList.toggle('active', mode === 'upload');
    };

    // ── Submit ──────────────────────────────────────────────────
    document.getElementById('submitSignBtn').addEventListener('click', () => {
        const signerName = document.getElementById('signerName').value.trim();
        const errEl      = document.getElementById('sigError');
        errEl.classList.add('d-none');

        // Determine the role: fixed or chosen by super_admin
        const canChoose  = {{ $canChooseRole ? 'true' : 'false' }};
        const fixedRole  = '{{ $userRole }}';
        let   chosenRole = fixedRole;
        if (canChoose) {
            const roleEl = document.getElementById('roleSelector');
            chosenRole   = roleEl ? roleEl.value : '';
            if (!chosenRole) {
                errEl.textContent = 'Please select the role you are signing as.';
                errEl.classList.remove('d-none');
                return;
            }
        }

        let signatureData = null;

        if (currentMode === 'draw') {
            const blank   = document.createElement('canvas');
            blank.width   = canvas.width;
            blank.height  = canvas.height;
            if (canvas.toDataURL() === blank.toDataURL()) {
                errEl.textContent = 'Please draw your signature before submitting.';
                errEl.classList.remove('d-none');
                return;
            }
            signatureData = canvas.toDataURL('image/png');
        } else {
            if (!uploadedDataUrl) {
                errEl.textContent = 'Please upload an image of your signature.';
                errEl.classList.remove('d-none');
                return;
            }
            signatureData = uploadedDataUrl;
        }

        if (!signerName) {
            errEl.textContent = 'Please enter your full name.';
            errEl.classList.remove('d-none');
            return;
        }

        const btn = document.getElementById('submitSignBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Signing…';

        fetch('/ajax/save-signature', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                document_type:    '{{ $documentType }}',
                document_id:      {{ $documentId }},
                role_in_document: chosenRole,
                signature_data:   signatureData,
                signer_name:      signerName,
            }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                errEl.textContent = data.message || 'Error saving signature.';
                errEl.classList.remove('d-none');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-pen-fill me-1"></i>Sign Document';
            }
        })
        .catch(() => {
            errEl.textContent = 'Network error. Please try again.';
            errEl.classList.remove('d-none');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-pen-fill me-1"></i>Sign Document';
        });
    });
})();
</script>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

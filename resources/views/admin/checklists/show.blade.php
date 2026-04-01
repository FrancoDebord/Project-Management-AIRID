@extends('index-new')
@section('title', $template->name . ' — Questions')

@section('content')
<style>
.cl-header { background: linear-gradient(135deg, #c20102, #8b0001); border-radius: 1rem; padding: 1.2rem 1.8rem; color: #fff; }
.section-card { border-radius: 10px; border: 1px solid #dee2e6; margin-bottom: 1.2rem; }
.section-head  { background: #f8f9fa; border-radius: 10px 10px 0 0; padding: .7rem 1rem; border-bottom: 1px solid #dee2e6; }
.q-row { border-bottom: 1px solid #f0f0f0; padding: .45rem .8rem; display: flex; align-items: flex-start; gap: .6rem; }
.q-row:last-child { border-bottom: none; }
.q-num  { font-size:.75rem;font-weight:700;color:#888;min-width:32px;padding-top:2px;flex-shrink:0; }
.q-text { flex:1;font-size:.85rem;line-height:1.45; }
.q-badges { display:flex;gap:4px;flex-wrap:wrap;flex-shrink:0; }
.q-actions { display:flex;gap:4px;flex-shrink:0; }
.inactive-row { opacity:.45; }
.badge-type { font-size:.63rem; padding: 2px 7px; border-radius:10px; background:#e9ecef; color:#555; }
.badge-used { font-size:.63rem; padding: 2px 7px; border-radius:10px; background:#d1ecf1; color:#0c5460; }
.badge-copy { font-size:.63rem; padding: 2px 7px; border-radius:10px; background:#fff3cd; color:#856404; }
</style>

{{-- Header --}}
<div class="cl-header d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <a href="{{ route('admin.checklists.index') }}" class="text-white opacity-75" style="text-decoration:none;">
                <i class="bi bi-arrow-left me-1"></i>
            </a>
            <h4 class="mb-0 fw-bold">{{ $template->name }}</h4>
            @if($template->reference_code)
            <span class="badge rounded-pill" style="background:rgba(255,255,255,.25);font-size:.72rem;">{{ $template->reference_code }}</span>
            @endif
        </div>
        <div style="font-size:.82rem;opacity:.8;">
            {{ $template->sections->count() }} section(s) &mdash;
            {{ $template->sections->sum(fn($s) => $s->questions->count()) }} questions
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible py-2 mb-3">
    <i class="bi bi-check2-circle me-1"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Sections --}}
@foreach($template->sections as $section)
<div class="section-card">
    <div class="section-head d-flex align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-2">
            @if($section->letter)
            <span class="fw-bold" style="color:#c20102;font-size:.9rem;">{{ $section->letter }}.</span>
            @endif
            <span class="fw-semibold" style="font-size:.9rem;">{{ $section->title }}</span>
            @if($section->subtitle)
            <span class="text-muted" style="font-size:.78rem;">— {{ $section->subtitle }}</span>
            @endif
            <span class="badge-type">{{ $section->form_type ?? 'yes_no_na' }}</span>
            <span class="badge" style="background:#c2010222;color:#c20102;font-size:.65rem;">
                {{ $section->questions->count() }} question(s)
            </span>
        </div>
        <button class="btn btn-sm fw-semibold"
                style="background:#c20102;color:#fff;border:none;font-size:.75rem;"
                onclick="openAddModal({{ $section->id }}, '{{ addslashes($section->title) }}')">
            <i class="bi bi-plus me-1"></i>Ajouter
        </button>
    </div>

    <div class="px-0">
        @forelse($section->questions as $q)
        <div class="q-row {{ !$q->is_active ? 'inactive-row' : '' }}" id="q-row-{{ $q->id }}">
            <div class="q-num">{{ $q->item_number }}.</div>
            <div class="q-text">
                <span class="q-text-val">{{ $q->text }}</span>
                @if($q->notes)
                <div class="text-muted" style="font-size:.72rem;margin-top:2px;">{{ $q->notes }}</div>
                @endif
            </div>
            <div class="q-badges">
                <span class="badge-type">{{ $q->response_type }}</span>
                @if($q->usage_count > 0)
                <span class="badge-used">Utilisé {{ $q->usage_count }}×</span>
                @endif
                @if($q->copied_from_id)
                <span class="badge-copy">Copie</span>
                @endif
                @if(!$q->is_active)
                <span class="badge bg-secondary" style="font-size:.63rem;">Inactif</span>
                @endif
            </div>
            <div class="q-actions">
                <button class="btn btn-sm btn-outline-secondary" style="font-size:.72rem;padding:2px 7px;"
                        onclick="openEditModal({{ $q->id }}, '{{ addslashes($q->item_number) }}', '{{ addslashes(str_replace("'", "\\'", $q->text)) }}', '{{ $q->response_type }}', '{{ addslashes($q->notes ?? '') }}')">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-outline-primary" style="font-size:.72rem;padding:2px 7px;"
                        title="Dupliquer"
                        onclick="duplicateQuestion({{ $q->id }}, this)">
                    <i class="bi bi-copy"></i>
                </button>
                <button class="btn btn-sm {{ $q->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                        style="font-size:.72rem;padding:2px 7px;"
                        title="{{ $q->is_active ? 'Désactiver' : 'Activer' }}"
                        onclick="toggleQuestion({{ $q->id }}, this)">
                    <i class="bi {{ $q->is_active ? 'bi-eye-slash' : 'bi-eye' }}"></i>
                </button>
                @if($q->isDeletable())
                <button class="btn btn-sm btn-outline-danger" style="font-size:.72rem;padding:2px 7px;"
                        title="Supprimer"
                        onclick="deleteQuestion({{ $q->id }}, this)">
                    <i class="bi bi-trash"></i>
                </button>
                @endif
            </div>
        </div>
        @empty
        <div class="text-muted text-center py-3" style="font-size:.83rem;">Aucune question dans cette section.</div>
        @endforelse
    </div>
</div>
@endforeach

{{-- ── Add Question Modal ── --}}
<div class="modal fade" id="modalAdd" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius:14px;overflow:hidden;">
            <div class="modal-header border-0 py-3" style="background:linear-gradient(90deg,#c20102,#8b0001);color:#fff;">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>Nouvelle question — <span id="addSectionName"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="addSectionId">
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Numéro d'item <span class="text-danger">*</span></label>
                    <input type="text" id="addItemNumber" class="form-control form-control-sm" placeholder="ex: 21, 2a, f_staff…">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Texte de la question <span class="text-danger">*</span></label>
                    <textarea id="addText" class="form-control form-control-sm" rows="3" placeholder="Libellé complet de la question…"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Type de réponse</label>
                    <select id="addResponseType" class="form-select form-select-sm">
                        <option value="yes_no_na">Yes / No / NA</option>
                        <option value="yes_no">Yes / No</option>
                        <option value="checkbox_date_text">Checkbox + Date + Text</option>
                        <option value="text_only">Text only</option>
                        <option value="staff_training">Staff Training (spécial)</option>
                        <option value="study_box_item">Study Box (response + signed)</option>
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label fw-semibold small">Note interne (optionnel)</label>
                    <input type="text" id="addNotes" class="form-control form-control-sm" placeholder="Note admin visible uniquement ici…">
                </div>
                <div id="addError" class="text-danger small mt-2" style="display:none;"></div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-sm fw-semibold" style="background:#c20102;color:#fff;border:none;"
                        onclick="submitAdd()">
                    <i class="bi bi-plus-circle me-1"></i>Ajouter
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── Edit Question Modal ── --}}
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius:14px;overflow:hidden;">
            <div class="modal-header border-0 py-3" style="background:linear-gradient(90deg,#c20102,#8b0001);color:#fff;">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil me-2"></i>Modifier la question</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="editQuestionId">
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Numéro d'item</label>
                    <input type="text" id="editItemNumber" class="form-control form-control-sm" disabled>
                    <div class="form-text">Le numéro d'item ne peut pas être modifié.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Texte de la question <span class="text-danger">*</span></label>
                    <textarea id="editText" class="form-control form-control-sm" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Type de réponse</label>
                    <select id="editResponseType" class="form-select form-select-sm">
                        <option value="yes_no_na">Yes / No / NA</option>
                        <option value="yes_no">Yes / No</option>
                        <option value="checkbox_date_text">Checkbox + Date + Text</option>
                        <option value="text_only">Text only</option>
                        <option value="staff_training">Staff Training (spécial)</option>
                        <option value="study_box_item">Study Box (response + signed)</option>
                    </select>
                    <div class="form-text" id="editResponseTypeNote" style="display:none;color:#dc3545;">
                        Cette question a été utilisée dans des inspections. Le type de réponse ne peut pas être modifié.
                    </div>
                </div>
                <div class="mb-1">
                    <label class="form-label fw-semibold small">Note interne</label>
                    <input type="text" id="editNotes" class="form-control form-control-sm">
                </div>
                <div id="editError" class="text-danger small mt-2" style="display:none;"></div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-sm fw-semibold" style="background:#c20102;color:#fff;border:none;"
                        onclick="submitEdit()">
                    <i class="bi bi-check2 me-1"></i>Enregistrer
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// ── Add Modal ─────────────────────────────────────────────────────────────────
function openAddModal(sectionId, sectionName) {
    document.getElementById('addSectionId').value   = sectionId;
    document.getElementById('addSectionName').textContent = sectionName;
    document.getElementById('addItemNumber').value  = '';
    document.getElementById('addText').value        = '';
    document.getElementById('addNotes').value       = '';
    document.getElementById('addError').style.display = 'none';
    document.getElementById('addResponseType').value = 'yes_no_na';
    new bootstrap.Modal(document.getElementById('modalAdd')).show();
}

function submitAdd() {
    const sectionId = document.getElementById('addSectionId').value;
    const body = {
        item_number:   document.getElementById('addItemNumber').value.trim(),
        text:          document.getElementById('addText').value.trim(),
        response_type: document.getElementById('addResponseType').value,
        notes:         document.getElementById('addNotes').value.trim() || null,
    };

    if (!body.item_number || !body.text) {
        showErr('addError', 'Le numéro et le texte sont obligatoires.');
        return;
    }

    fetch(`/admin/checklists/sections/${sectionId}/questions`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify(body),
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) { showErr('addError', data.error); return; }
        bootstrap.Modal.getInstance(document.getElementById('modalAdd')).hide();
        location.reload();
    });
}

// ── Edit Modal ────────────────────────────────────────────────────────────────
function openEditModal(id, itemNumber, text, responseType, notes) {
    document.getElementById('editQuestionId').value     = id;
    document.getElementById('editItemNumber').value     = itemNumber;
    document.getElementById('editText').value           = text;
    document.getElementById('editNotes').value          = notes;
    document.getElementById('editResponseType').value   = responseType;
    document.getElementById('editError').style.display  = 'none';
    document.getElementById('editResponseTypeNote').style.display = 'none';
    new bootstrap.Modal(document.getElementById('modalEdit')).show();
}

function submitEdit() {
    const id   = document.getElementById('editQuestionId').value;
    const body = {
        text:          document.getElementById('editText').value.trim(),
        response_type: document.getElementById('editResponseType').value,
        notes:         document.getElementById('editNotes').value.trim() || null,
    };

    if (!body.text) { showErr('editError', 'Le texte est obligatoire.'); return; }

    fetch(`/admin/checklists/questions/${id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify(body),
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) {
            showErr('editError', data.error);
            if (data.error.includes('response type')) {
                document.getElementById('editResponseTypeNote').style.display = 'block';
            }
            return;
        }
        bootstrap.Modal.getInstance(document.getElementById('modalEdit')).hide();
        // Update text in DOM without reload
        const row = document.getElementById('q-row-' + id);
        if (row) row.querySelector('.q-text-val').textContent = data.question.text;
    });
}

// ── Duplicate ─────────────────────────────────────────────────────────────────
function duplicateQuestion(id, btn) {
    if (!confirm('Dupliquer cette question ? Une copie sera ajoutée à la fin de la section.')) return;
    btn.disabled = true;
    fetch(`/admin/checklists/questions/${id}/duplicate`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) { alert(data.error); btn.disabled = false; return; }
        location.reload();
    });
}

// ── Toggle active ──────────────────────────────────────────────────────────────
function toggleQuestion(id, btn) {
    fetch(`/admin/checklists/questions/${id}/toggle`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    })
    .then(r => r.json())
    .then(data => {
        const row = document.getElementById('q-row-' + id);
        if (data.is_active) {
            row.classList.remove('inactive-row');
            btn.classList.replace('btn-outline-success', 'btn-outline-warning');
            btn.querySelector('i').className = 'bi bi-eye-slash';
            btn.title = 'Désactiver';
        } else {
            row.classList.add('inactive-row');
            btn.classList.replace('btn-outline-warning', 'btn-outline-success');
            btn.querySelector('i').className = 'bi bi-eye';
            btn.title = 'Activer';
        }
    });
}

// ── Delete ─────────────────────────────────────────────────────────────────────
function deleteQuestion(id, btn) {
    if (!confirm('Supprimer définitivement cette question ?')) return;
    btn.disabled = true;
    fetch(`/admin/checklists/questions/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) { alert(data.error); btn.disabled = false; return; }
        document.getElementById('q-row-' + id)?.remove();
    });
}

function showErr(elId, msg) {
    const el = document.getElementById(elId);
    el.textContent = msg;
    el.style.display = 'block';
}
</script>
@endsection

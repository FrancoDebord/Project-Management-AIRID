@extends('index-new')
@section('title', $section->letter . '. ' . $section->title . ' — CPIA Admin')

@section('content')
<style>
.cl-header   { background: linear-gradient(135deg, #c20102, #8b0001); border-radius: 1rem; padding: 1.2rem 1.8rem; color: #fff; }
.item-card   { border-radius: 10px; border: 1px solid #dee2e6; margin-bottom: .6rem; }
.item-head   { background: #f8f9fa; border-radius: 10px 10px 0 0; padding: .6rem 1rem; border-bottom: 1px solid #dee2e6; }
.i-row       { border-bottom: 1px solid #f0f0f0; padding: .45rem .9rem; display: flex; align-items: flex-start; gap: .6rem; }
.i-row:last-child { border-bottom: none; }
.i-num       { font-size:.75rem;font-weight:700;color:#c20102;min-width:30px;padding-top:2px;flex-shrink:0; }
.i-text      { flex:1;font-size:.85rem;line-height:1.45; }
.i-badges    { display:flex;gap:4px;flex-wrap:wrap;flex-shrink:0; }
.i-actions   { display:flex;gap:4px;flex-shrink:0; }
.inactive-row{ opacity:.45; }
.badge-used  { font-size:.63rem; padding: 2px 7px; border-radius:10px; background:#d1ecf1; color:#0c5460; }
.badge-copy  { font-size:.63rem; padding: 2px 7px; border-radius:10px; background:#fff3cd; color:#856404; }
</style>

<div class="cl-header d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <a href="{{ route('admin.cpia.index') }}" class="text-white opacity-75" style="text-decoration:none;">
                <i class="bi bi-arrow-left me-1"></i>
            </a>
            <h4 class="mb-0 fw-bold">{{ $section->letter }}. {{ $section->title }}</h4>
        </div>
        <div style="font-size:.82rem;opacity:.8;">
            {{ $section->items->count() }} item(s) in this section
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible py-2 mb-3">
    <i class="bi bi-check2-circle me-1"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="section-card bg-white" style="border-radius:10px;border:1px solid #dee2e6;">
    <div class="item-head d-flex align-items-center justify-content-between gap-2">
        <span class="fw-semibold" style="font-size:.9rem;">Items</span>
        <button class="btn btn-sm fw-semibold"
                style="background:#c20102;color:#fff;border:none;font-size:.75rem;"
                onclick="openAddModal()">
            <i class="bi bi-plus me-1"></i>Add item
        </button>
    </div>

    <div class="px-0">
        @forelse($section->items as $item)
        <div class="i-row {{ !$item->is_active ? 'inactive-row' : '' }}" id="i-row-{{ $item->id }}">
            <div class="i-num">{{ $item->item_number }}.</div>
            <div class="i-text">
                <span class="i-text-val">{{ $item->text }}</span>
            </div>
            <div class="i-badges">
                @if($item->usage_count > 0)
                <span class="badge-used">Used {{ $item->usage_count }}×</span>
                @endif
                @if($item->copied_from_id)
                <span class="badge-copy">Copy</span>
                @endif
                @if(!$item->is_active)
                <span class="badge bg-secondary" style="font-size:.63rem;">Inactive</span>
                @endif
            </div>
            <div class="i-actions">
                <button class="btn btn-sm btn-outline-secondary" style="font-size:.72rem;padding:2px 7px;"
                        onclick="openEditModal({{ $item->id }}, '{{ addslashes(str_replace("'", "\\'", $item->text)) }}')">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-outline-primary" style="font-size:.72rem;padding:2px 7px;" title="Duplicate"
                        onclick="duplicateItem({{ $item->id }}, this)">
                    <i class="bi bi-copy"></i>
                </button>
                <button class="btn btn-sm {{ $item->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                        style="font-size:.72rem;padding:2px 7px;" title="{{ $item->is_active ? 'Deactivate' : 'Activate' }}"
                        onclick="toggleItem({{ $item->id }}, this)">
                    <i class="bi bi-{{ $item->is_active ? 'eye-slash' : 'eye' }}"></i>
                </button>
                @if($item->isDeletable())
                <button class="btn btn-sm btn-outline-danger" style="font-size:.72rem;padding:2px 7px;" title="Delete"
                        onclick="deleteItem({{ $item->id }}, this)">
                    <i class="bi bi-trash"></i>
                </button>
                @endif
            </div>
        </div>
        @empty
        <div class="p-3 text-muted" style="font-size:.85rem;">No items yet. Click "Add item" to create the first one.</div>
        @endforelse
    </div>
</div>

{{-- ── Add modal ── --}}
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:#c20102;color:#fff;">
                <h5 class="modal-title mb-0 fw-bold" style="font-size:.95rem;">Add Item</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label fw-semibold" style="font-size:.85rem;">Item text</label>
                <textarea class="form-control" id="add-text" rows="3" maxlength="500" placeholder="Describe the checklist item…"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm fw-semibold" style="background:#c20102;color:#fff;border:none;"
                        onclick="submitAdd()">Add</button>
            </div>
        </div>
    </div>
</div>

{{-- ── Edit modal ── --}}
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:#c20102;color:#fff;">
                <h5 class="modal-title mb-0 fw-bold" style="font-size:.95rem;">Edit Item</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit-id">
                <label class="form-label fw-semibold" style="font-size:.85rem;">Item text</label>
                <textarea class="form-control" id="edit-text" rows="3" maxlength="500"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm fw-semibold" style="background:#c20102;color:#fff;border:none;"
                        onclick="submitEdit()">Save</button>
            </div>
        </div>
    </div>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content
           || '{{ csrf_token() }}';
const SECTION_ID = {{ $section->id }};

// ── Modals ────────────────────────────────────────────────────
function openAddModal() {
    document.getElementById('add-text').value = '';
    new bootstrap.Modal(document.getElementById('addModal')).show();
}
function openEditModal(id, text) {
    document.getElementById('edit-id').value   = id;
    document.getElementById('edit-text').value = text;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

// ── Add ───────────────────────────────────────────────────────
function submitAdd() {
    const text = document.getElementById('add-text').value.trim();
    if (!text) return alert('Please enter item text.');
    apiFetch(`/admin/cpia/sections/${SECTION_ID}/items`, 'POST', { text })
        .then(d => {
            if (d.success) { location.reload(); }
            else alert(d.message || 'Error.');
        });
}

// ── Edit ──────────────────────────────────────────────────────
function submitEdit() {
    const id   = document.getElementById('edit-id').value;
    const text = document.getElementById('edit-text').value.trim();
    if (!text) return alert('Please enter item text.');
    apiFetch(`/admin/cpia/items/${id}`, 'PUT', { text })
        .then(d => {
            if (d.success) {
                bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
                document.querySelector(`#i-row-${id} .i-text-val`).textContent = text;
                toast('Item updated.');
            } else alert(d.message || 'Error.');
        });
}

// ── Duplicate ─────────────────────────────────────────────────
function duplicateItem(id, btn) {
    btn.disabled = true;
    apiFetch(`/admin/cpia/items/${id}/duplicate`, 'POST')
        .then(d => {
            btn.disabled = false;
            if (d.success) { location.reload(); }
            else alert(d.message || 'Error.');
        });
}

// ── Toggle ────────────────────────────────────────────────────
function toggleItem(id, btn) {
    apiFetch(`/admin/cpia/items/${id}/toggle`, 'POST')
        .then(d => {
            if (d.success) {
                const row = document.getElementById(`i-row-${id}`);
                if (d.is_active) {
                    row.classList.remove('inactive-row');
                    btn.className = 'btn btn-sm btn-outline-warning';
                    btn.title = 'Deactivate';
                    btn.innerHTML = '<i class="bi bi-eye-slash"></i>';
                } else {
                    row.classList.add('inactive-row');
                    btn.className = 'btn btn-sm btn-outline-success';
                    btn.title = 'Activate';
                    btn.innerHTML = '<i class="bi bi-eye"></i>';
                }
                btn.style.fontSize = '.72rem';
                btn.style.padding  = '2px 7px';
            }
        });
}

// ── Delete ────────────────────────────────────────────────────
function deleteItem(id, btn) {
    if (!confirm('Delete this item permanently?')) return;
    btn.disabled = true;
    apiFetch(`/admin/cpia/items/${id}`, 'DELETE')
        .then(d => {
            if (d.success) {
                document.getElementById(`i-row-${id}`).remove();
                toast('Item deleted.');
            } else {
                btn.disabled = false;
                alert(d.message || 'Cannot delete.');
            }
        });
}

// ── Helper ────────────────────────────────────────────────────
function apiFetch(url, method, body) {
    return fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: body ? JSON.stringify(body) : undefined,
    }).then(r => r.json());
}

function toast(msg) {
    const el = document.createElement('div');
    el.style.cssText = 'position:fixed;bottom:20px;right:20px;background:#1f2937;color:#fff;border-radius:8px;padding:10px 16px;font-size:.85rem;z-index:9999;border-left:4px solid #22c55e;';
    el.textContent = msg;
    document.body.appendChild(el);
    setTimeout(() => el.remove(), 3000);
}
</script>

@endsection

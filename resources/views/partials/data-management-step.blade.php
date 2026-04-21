@php
    $project_id = request('project_id');
    $project    = App\Models\Pro_Project::with([
        'dmDatabases.labTest',
        'dmPcAssignments',
        'dmSoftwareValidations.database',
        'dmSoftwareValidations.files',
        'dmDataloggerValidations.files',
        'dmDoubleEntries.database',
        'labTestsConcerned',
    ])->find($project_id);

    $labTests        = $project?->labTestsConcerned ?? collect();
    $dmDatabases     = $project?->dmDatabases ?? collect();
    $pcAssignments   = $project?->dmPcAssignments ?? collect();
    $softValidations = $project?->dmSoftwareValidations ?? collect();
    $dlValidations   = $project?->dmDataloggerValidations ?? collect();
    $doubleEntries   = $project?->dmDoubleEntries ?? collect();
    $isGlp           = $project?->is_glp ?? false;

    $dbTypeLabels = [
        'lab_test'     => 'Lab Test DB',
        'field'        => 'Base de terrain / cases',
        'experimental' => 'Données expérimentales',
        'case_data'    => 'Données de cas',
        'other'        => 'Autre',
    ];
@endphp

<style>
.dm-section { background:#fff; border:1px solid #e5e7eb; border-radius:.75rem; padding:1.2rem 1.4rem; margin-bottom:1.2rem; }
.dm-section-title { font-weight:700; font-size:.9rem; color:#1a3a6b; margin-bottom:.75rem; display:flex; align-items:center; gap:.4rem; }
.dm-badge-ok   { background:#d4edda;color:#155724;border-radius:20px;padding:.1rem .55rem;font-size:.72rem;font-weight:600; }
.dm-badge-warn { background:#fff3cd;color:#856404;border-radius:20px;padding:.1rem .55rem;font-size:.72rem;font-weight:600; }
.dm-badge-draft{ background:#e2e3e5;color:#383d41;border-radius:20px;padding:.1rem .55rem;font-size:.72rem;font-weight:600; }
.dm-table { width:100%;border-collapse:collapse;font-size:.8rem; }
.dm-table th { background:#1a3a6b;color:#fff;padding:5px 8px;font-size:.74rem;text-align:left; }
.dm-table td { border-bottom:1px solid #f0f0f0;padding:5px 8px;vertical-align:middle; }
.dm-table tr:last-child td { border-bottom:none; }
.dm-table tr:hover td { background:#f8f9ff; }
.dm-add-btn { font-size:.78rem;padding:.25rem .7rem; }
.dm-hero { background:linear-gradient(135deg,#1a3a6b 0%,#0d6efd 100%);border-radius:.75rem;padding:1rem 1.4rem;margin-bottom:1.2rem;color:#fff; }
</style>

{{-- ── Hero ── --}}
<div class="dm-hero d-flex align-items-center gap-3">
    <i class="bi bi-database-fill-gear fs-3 opacity-75"></i>
    <div>
        <h5 class="fw-bold mb-0">Data Management</h5>
        <div class="small opacity-75">Bases de données · Postes de saisie · Validations · Double saisie</div>
    </div>
    @if($isGlp)
    <span class="ms-auto badge rounded-pill px-3 py-2" style="background:rgba(255,255,255,.2);font-size:.78rem;">GLP</span>
    @endif
</div>

{{-- ════════════════════════════════════════════════════════════════════════
     SECTION 1 — BASES DE DONNÉES
══════════════════════════════════════════════════════════════════════ --}}
<div class="dm-section">
    <div class="dm-section-title">
        <i class="bi bi-database"></i> Bases de données du projet
        <button class="btn btn-primary dm-add-btn ms-auto" onclick="openDbModal()">
            <i class="bi bi-plus-circle me-1"></i>Ajouter une base
        </button>
    </div>
    <div class="small text-muted mb-3">
        Recensement de toutes les bases de données utilisées (lab tests, terrain, cas expérimentaux…).
    </div>
    @if($dmDatabases->isEmpty())
    <div class="text-center py-3 text-muted small"><i class="bi bi-inbox me-1"></i>Aucune base de données enregistrée.</div>
    @else
    <table class="dm-table">
        <thead><tr><th>#</th><th>Nom</th><th>Type</th><th>Lab Test lié</th><th>Description</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
        @foreach($dmDatabases as $i => $db)
        <tr>
            <td>{{ $i+1 }}</td>
            <td class="fw-semibold">{{ $db->name }}</td>
            <td>{{ $dbTypeLabels[$db->type] ?? $db->type }}</td>
            <td>{{ $db->labTest?->lab_test_name ?? '—' }}</td>
            <td class="text-muted" style="max-width:200px;">{{ Str::limit($db->description ?? '', 60) }}</td>
            <td class="text-end" style="white-space:nowrap;">
                <button class="btn btn-sm btn-outline-primary py-0 px-2" onclick="openDbModal({{ $db->id }}, '{{ addslashes($db->name) }}', '{{ $db->type }}', {{ $db->lab_test_id ?? 'null' }}, '{{ addslashes($db->description ?? '') }}')">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger py-0 px-2" onclick="deleteDb({{ $db->id }})">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @endif
</div>

{{-- ════════════════════════════════════════════════════════════════════════
     SECTION 2 — POSTES DE SAISIE (PC)
══════════════════════════════════════════════════════════════════════ --}}
<div class="dm-section">
    <div class="dm-section-title">
        <i class="bi bi-pc-display"></i> Postes de saisie (PC attribués)
        <button class="btn btn-primary dm-add-btn ms-auto" onclick="openPcModal()">
            <i class="bi bi-plus-circle me-1"></i>Attribuer un PC
        </button>
    </div>
    <div class="small text-muted mb-3">Historique de tous les PC attribués à ce projet avec leurs périodes d'activité.</div>
    @if($pcAssignments->isEmpty())
    <div class="text-center py-3 text-muted small"><i class="bi bi-inbox me-1"></i>Aucun PC attribué.</div>
    @else
    <table class="dm-table">
        <thead><tr><th>PC</th><th>N° Série</th><th>GLP</th><th>Attribué le</th><th>Retourné le</th><th>Raison retour</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
        @foreach($pcAssignments as $pc)
        <tr>
            <td class="fw-semibold">{{ $pc->pc_name }}</td>
            <td>{{ $pc->pc_serial ?? '—' }}</td>
            <td>
                @if($pc->is_glp) <span class="dm-badge-ok">GLP</span>
                @else <span class="dm-badge-warn">Non-GLP</span> @endif
            </td>
            <td>{{ $pc->assigned_at?->format('d/m/Y') }}</td>
            <td>
                @if($pc->returned_at) {{ $pc->returned_at->format('d/m/Y') }}
                @else <span class="dm-badge-ok">Actif</span> @endif
            </td>
            <td class="text-muted">{{ $pc->reason_for_return ?? '—' }}</td>
            <td class="text-end" style="white-space:nowrap;">
                @if(!$pc->returned_at)
                <button class="btn btn-sm btn-outline-warning py-0 px-2" onclick="openReturnPcModal({{ $pc->id }}, '{{ addslashes($pc->pc_name) }}')">
                    <i class="bi bi-arrow-return-left"></i>
                </button>
                @endif
                <button class="btn btn-sm btn-outline-primary py-0 px-2" onclick="openPcModal({{ $pc->id }}, '{{ addslashes($pc->pc_name) }}', '{{ $pc->pc_serial ?? '' }}', {{ $pc->is_glp ? 1 : 0 }}, '{{ $pc->assigned_at?->format('Y-m-d') }}', '{{ addslashes($pc->notes ?? '') }}')">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger py-0 px-2" onclick="deletePc({{ $pc->id }})">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @endif
</div>

{{-- ════════════════════════════════════════════════════════════════════════
     SECTION 3 — COMPUTERIZED SYSTEM VALIDATION (GLP ONLY)
══════════════════════════════════════════════════════════════════════ --}}
@if($isGlp)
<div class="dm-section">
    <div class="dm-section-title">
        <i class="bi bi-patch-check"></i> Validation des logiciels / bases de données (GLP)
        <button class="btn btn-primary dm-add-btn ms-auto" onclick="openSoftvalModal()">
            <i class="bi bi-plus-circle me-1"></i>Ajouter une validation
        </button>
    </div>
    <div class="small text-muted mb-3">Validation de chaque logiciel / base de données utilisé dans le projet selon les exigences GLP.</div>
    @if($softValidations->isEmpty())
    <div class="text-center py-3 text-muted small"><i class="bi bi-inbox me-1"></i>Aucune validation logicielle enregistrée.</div>
    @else
    <table class="dm-table">
        <thead><tr><th>Logiciel</th><th>Base de données</th><th>PC</th><th>Date validation</th><th>Validé par</th><th>Statut</th><th>Fichiers</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
        @foreach($softValidations as $sv)
        <tr>
            <td class="fw-semibold">{{ $sv->software_name }}<br><span class="text-muted" style="font-size:.7rem;">{{ $sv->current_software_version }}</span></td>
            <td>{{ $sv->database?->name ?? '—' }}</td>
            <td>{{ $sv->computer_id ?? '—' }}</td>
            <td>{{ $sv->validation_date?->format('d/m/Y') ?? '—' }}</td>
            <td>{{ $sv->validation_done_by ?? '—' }}</td>
            <td>
                @if($sv->status === 'validated') <span class="dm-badge-ok"><i class="bi bi-check-circle me-1"></i>Validé</span>
                @else <span class="dm-badge-draft">Brouillon</span> @endif
            </td>
            <td>
                @foreach($sv->files as $f)
                <div style="white-space:nowrap;">
                    <a href="{{ asset('storage/'.$f->file_path) }}" target="_blank" class="small text-primary" style="font-size:.7rem;">
                        <i class="bi bi-paperclip me-1"></i>{{ Str::limit($f->original_name, 22) }}
                    </a>
                    <button class="btn btn-link p-0 text-danger ms-1" style="font-size:.65rem;" onclick="deleteSoftvalFile({{ $f->id }}, event)"><i class="bi bi-x-circle"></i></button>
                </div>
                @endforeach
                <button class="btn btn-sm btn-outline-secondary py-0 px-1 mt-1" style="font-size:.68rem;" onclick="openSoftvalFileUpload({{ $sv->id }})">
                    <i class="bi bi-upload me-1"></i>Fichier
                </button>
            </td>
            <td class="text-end" style="white-space:nowrap;">
                <a href="{{ route('pdf.dm.software-validation', $sv->id) }}" target="_blank" class="btn btn-sm btn-outline-danger py-0 px-2" title="PDF"><i class="bi bi-file-earmark-pdf"></i></a>
                <button class="btn btn-sm btn-outline-primary py-0 px-2" onclick="openSoftvalModal({{ $sv->id }})"><i class="bi bi-pencil"></i></button>
                <button class="btn btn-sm btn-outline-danger py-0 px-2" onclick="deleteSoftval({{ $sv->id }})"><i class="bi bi-trash"></i></button>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @endif
</div>

<div class="dm-section">
    <div class="dm-section-title">
        <i class="bi bi-thermometer-half"></i> Validation des Data Loggers (GLP)
        <button class="btn btn-primary dm-add-btn ms-auto" onclick="openDlModal()">
            <i class="bi bi-plus-circle me-1"></i>Ajouter un Data Logger
        </button>
    </div>
    <div class="small text-muted mb-3">Validation de chaque data logger impliqué dans le projet.</div>
    @if($dlValidations->isEmpty())
    <div class="text-center py-3 text-muted small"><i class="bi bi-inbox me-1"></i>Aucun data logger enregistré.</div>
    @else
    <table class="dm-table">
        <thead><tr><th>Nom / Modèle</th><th>N° Série</th><th>Emplacement</th><th>Date validation</th><th>Validé par</th><th>Statut</th><th>Fichiers</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
        @foreach($dlValidations as $dl)
        <tr>
            <td class="fw-semibold">{{ $dl->name }}</td>
            <td>{{ $dl->serial_number ?? '—' }}</td>
            <td>{{ $dl->location ?? '—' }}</td>
            <td>{{ $dl->validation_date?->format('d/m/Y') ?? '—' }}</td>
            <td>{{ $dl->validated_by ?? '—' }}</td>
            <td>
                @if($dl->status === 'validated') <span class="dm-badge-ok"><i class="bi bi-check-circle me-1"></i>Validé</span>
                @else <span class="dm-badge-draft">Brouillon</span> @endif
            </td>
            <td>
                @foreach($dl->files as $f)
                <div style="white-space:nowrap;">
                    <a href="{{ asset('storage/'.$f->file_path) }}" target="_blank" class="small text-primary" style="font-size:.7rem;">
                        <i class="bi bi-paperclip me-1"></i>{{ Str::limit($f->original_name, 22) }}
                    </a>
                    <button class="btn btn-link p-0 text-danger ms-1" style="font-size:.65rem;" onclick="deleteDlFile({{ $f->id }}, event)"><i class="bi bi-x-circle"></i></button>
                </div>
                @endforeach
                <button class="btn btn-sm btn-outline-secondary py-0 px-1 mt-1" style="font-size:.68rem;" onclick="openDlFileUpload({{ $dl->id }})">
                    <i class="bi bi-upload me-1"></i>Fichier
                </button>
            </td>
            <td class="text-end" style="white-space:nowrap;">
                <button class="btn btn-sm btn-outline-primary py-0 px-2" onclick="openDlModal({{ $dl->id }})"><i class="bi bi-pencil"></i></button>
                <button class="btn btn-sm btn-outline-danger py-0 px-2" onclick="deleteDl({{ $dl->id }})"><i class="bi bi-trash"></i></button>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @endif
</div>
@endif {{-- /is_glp --}}

{{-- ════════════════════════════════════════════════════════════════════════
     SECTION 4 — DOUBLE SAISIES
══════════════════════════════════════════════════════════════════════ --}}
<div class="dm-section">
    <div class="dm-section-title">
        <i class="bi bi-input-cursor-text"></i> Sessions de double saisie
        <button class="btn btn-primary dm-add-btn ms-auto" onclick="openDeModal()">
            <i class="bi bi-plus-circle me-1"></i>Ajouter une session
        </button>
    </div>
    <div class="small text-muted mb-3">Enregistrement des double saisies, comparaisons et conformité des données.</div>
    @if($doubleEntries->isEmpty())
    <div class="text-center py-3 text-muted small"><i class="bi bi-inbox me-1"></i>Aucune session de double saisie enregistrée.</div>
    @else
    <table class="dm-table">
        <thead>
            <tr>
                <th>Base</th>
                <th>1ère saisie</th>
                <th>Effectuée par</th>
                <th>2ème saisie</th>
                <th>Effectuée par</th>
                <th>Conformité</th>
                <th>Rapport</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($doubleEntries as $de)
        <tr>
            <td>{{ $de->database?->name ?? '—' }}</td>
            <td>{{ $de->first_entry_date?->format('d/m/Y') }}</td>
            <td class="text-muted" style="max-width:130px;">{{ Str::limit($de->first_entry_by, 35) }}</td>
            <td>{{ $de->second_entry_date?->format('d/m/Y') }}</td>
            <td class="text-muted" style="max-width:130px;">{{ Str::limit($de->second_entry_by, 35) }}</td>
            <td>
                @if(is_null($de->is_compliant))
                    <span class="dm-badge-warn">Non évalué</span>
                @elseif($de->is_compliant)
                    <span class="dm-badge-ok"><i class="bi bi-check-circle me-1"></i>Conforme</span>
                @else
                    <span class="badge" style="background:#fde8e8;color:#dc3545;border-radius:20px;padding:.1rem .55rem;font-size:.72rem;font-weight:600;"><i class="bi bi-x-circle me-1"></i>Non conforme</span>
                    @if($de->comments)<div class="small text-muted" style="font-size:.68rem;">{{ Str::limit($de->comments,50) }}</div>@endif
                @endif
            </td>
            <td>
                @if($de->comparison_file_path)
                <a href="{{ asset('storage/'.$de->comparison_file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size:.7rem;">
                    <i class="bi bi-file-earmark-pdf me-1"></i>Voir
                </a>
                @else <span class="text-muted small">—</span> @endif
            </td>
            <td class="text-end" style="white-space:nowrap;">
                <button class="btn btn-sm btn-outline-primary py-0 px-2" onclick="openDeModal({{ $de->id }})"><i class="bi bi-pencil"></i></button>
                <button class="btn btn-sm btn-outline-danger py-0 px-2" onclick="deleteDe({{ $de->id }})"><i class="bi bi-trash"></i></button>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @endif
</div>

{{-- ════════════════════════════════════════════════════════════════════════
     MODALS
══════════════════════════════════════════════════════════════════════ --}}

{{-- Modal: Database --}}
<div class="modal fade" id="dmDbModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header" style="background:#1a3a6b;color:#fff;">
        <h5 class="modal-title"><i class="bi bi-database me-2"></i>Base de données</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body px-4 py-3">
        <div id="dmDbMessages"></div>
        <form id="formDmDb">
          @csrf
          <input type="hidden" name="id" id="dmDbId">
          <input type="hidden" name="project_id" value="{{ $project_id }}">
          <div class="mb-3">
            <label class="form-label fw-semibold small">Nom de la base *</label>
            <input type="text" name="name" id="dmDbName" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold small">Type *</label>
            <select name="type" id="dmDbType" class="form-select" required>
              @foreach($dbTypeLabels as $val => $lbl)
              <option value="{{ $val }}">{{ $lbl }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold small">Lab Test lié (optionnel)</label>
            <select name="lab_test_id" id="dmDbLabTest" class="form-select">
              <option value="">— Aucun —</option>
              @foreach($labTests as $lt)
              <option value="{{ $lt->id }}">{{ $lt->lab_test_name }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold small">Description</label>
            <textarea name="description" id="dmDbDesc" class="form-control" rows="2"></textarea>
          </div>
          <div class="d-flex justify-content-end gap-2 border-top pt-3">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary px-4">Enregistrer</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- Modal: PC Assignment --}}
<div class="modal fade" id="dmPcModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header" style="background:#1a3a6b;color:#fff;">
        <h5 class="modal-title"><i class="bi bi-pc-display me-2"></i>Attribution PC</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body px-4 py-3">
        <div id="dmPcMessages"></div>
        <form id="formDmPc">
          @csrf
          <input type="hidden" name="id" id="dmPcId">
          <input type="hidden" name="project_id" value="{{ $project_id }}">
          <div class="row g-2 mb-3">
            <div class="col-8">
              <label class="form-label fw-semibold small">Identifiant / Nom du PC *</label>
              <input type="text" name="pc_name" id="dmPcName" class="form-control" required placeholder="ex: PC-LAB-001">
            </div>
            <div class="col-4">
              <label class="form-label fw-semibold small">N° Série</label>
              <input type="text" name="pc_serial" id="dmPcSerial" class="form-control">
            </div>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label fw-semibold small">Date d'attribution *</label>
              <input type="date" name="assigned_at" id="dmPcAssignedAt" class="form-control" required>
            </div>
            <div class="col-6">
              <label class="form-label fw-semibold small">GLP</label>
              <select name="is_glp" id="dmPcIsGlp" class="form-select">
                <option value="0">Non-GLP</option>
                <option value="1" {{ $isGlp ? 'selected' : '' }}>GLP</option>
              </select>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold small">Notes</label>
            <textarea name="notes" id="dmPcNotes" class="form-control" rows="2"></textarea>
          </div>
          <div class="d-flex justify-content-end gap-2 border-top pt-3">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary px-4">Enregistrer</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- Modal: Return PC --}}
<div class="modal fade" id="dmReturnPcModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header" style="background:#856404;color:#fff;">
        <h5 class="modal-title"><i class="bi bi-arrow-return-left me-2"></i>Retour du PC : <span id="dmReturnPcName"></span></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body px-4 py-3">
        <div id="dmReturnPcMessages"></div>
        <form id="formDmReturnPc">
          @csrf
          <input type="hidden" name="id" id="dmReturnPcId">
          <div class="mb-3">
            <label class="form-label fw-semibold small">Date de retour *</label>
            <input type="date" name="returned_at" class="form-control" required value="{{ date('Y-m-d') }}">
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold small">Raison du retour</label>
            <input type="text" name="reason_for_return" class="form-control" placeholder="Panne, fin d'utilisation…">
          </div>
          <div class="d-flex justify-content-end gap-2 border-top pt-3">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-warning px-4">Enregistrer le retour</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- Modal: Software Validation --}}
<div class="modal fade" id="dmSoftvalModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header" style="background:#1a3a6b;color:#fff;">
        <h5 class="modal-title"><i class="bi bi-patch-check me-2"></i>Fiche de Validation Logicielle / Base de Données</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body px-4 py-3">
        <div id="dmSoftvalMessages"></div>
        <form id="formDmSoftval">
          @csrf
          <input type="hidden" name="id" id="dmSvId">
          <input type="hidden" name="project_id" value="{{ $project_id }}">
          <div class="row g-3">
            <div class="col-md-5">
              <label class="form-label fw-semibold small">Logiciel à valider *</label>
              <input type="text" name="software_name" id="dmSvSoftware" class="form-control" required placeholder="ex: Minitab 18, MS Excel…">
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold small">Version actuelle</label>
              <input type="text" name="current_software_version" id="dmSvVersion" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold small">Date de validation</label>
              <input type="date" name="validation_date" id="dmSvDate" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold small">Computer ID</label>
              <input type="text" name="computer_id" id="dmSvComputerId" class="form-control" placeholder="ex: PC-23-001-22-28">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold small">Validé par</label>
              <input type="text" name="validation_done_by" id="dmSvDoneBy" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold small">Raison de la validation</label>
              <input type="text" name="reason_for_validation" id="dmSvReason" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold small">Base de données liée</label>
              <select name="database_id" id="dmSvDatabase" class="form-select">
                <option value="">— Aucune —</option>
                @foreach($dmDatabases as $db)
                <option value="{{ $db->id }}">{{ $db->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold small">Système d'exploitation</label>
              <input type="text" name="operating_system" id="dmSvOs" class="form-control" placeholder="Windows 11 Pro…">
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold small">CPU</label>
              <input type="text" name="cpu" id="dmSvCpu" class="form-control" placeholder="Intel i7…">
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold small">RAM</label>
              <input type="text" name="ram" id="dmSvRam" class="form-control" placeholder="16 GB">
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold small">Enregistré dans ordinateur ?</label>
              <select name="is_recorded_in_computer" id="dmSvRecorded" class="form-select">
                <option value="0">Non</option>
                <option value="1">Oui</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold small">Kit de validation</label>
              <select name="validation_kit_status" id="dmSvKit" class="form-select">
                <option value="">—</option>
                <option value="complete">Complet</option>
                <option value="incomplete">Incomplet</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold small">Dossier de validation (chemin/étiquette)</label>
              <input type="text" name="validation_folder_name" id="dmSvFolder" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold small">Nom du fichier jeu de données</label>
              <input type="text" name="validation_file_name" id="dmSvFile" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold small">Code document SOP</label>
              <input type="text" name="sop_document_code" id="dmSvSopCode" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold small">Section SOP</label>
              <input type="text" name="sop_section" id="dmSvSopSection" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold small">Data logger (env.)</label>
              <input type="text" name="data_logger_env" id="dmSvDataLogger" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold small">Température ambiante</label>
              <input type="text" name="env_temperature" id="dmSvTemp" class="form-control" placeholder="ex: 27,4 °C">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold small">Humidité ambiante</label>
              <input type="text" name="env_humidity" id="dmSvHumidity" class="form-control" placeholder="ex: 84,6 %">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold small">Détails de la procédure (numérotés)</label>
              <textarea name="details_of_procedure" id="dmSvDetails" class="form-control" rows="5"
                placeholder="1) Calcul effectué sur MS Excel (sauvegarde et impression écran/image)&#10;2) Calcul effectué dans Minitab 18 (sauvegarde et impression)&#10;3) Ouverture fichier Comparaison Validation.txt&#10;4) Impressions fichiers Comparaison Validation.html…"></textarea>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold small">Statut</label>
              <select name="status" id="dmSvStatus" class="form-select">
                <option value="draft">Brouillon</option>
                <option value="validated">Validé</option>
              </select>
            </div>
          </div>
          <div class="d-flex justify-content-end gap-2 border-top pt-3 mt-3">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary px-4">Enregistrer</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- Modal: Software Validation File Upload --}}
<div class="modal fade" id="dmSoftvalFileModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header" style="background:#0d6efd;color:#fff;">
        <h5 class="modal-title"><i class="bi bi-upload me-2"></i>Téléverser un fichier</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body px-4 py-3">
        <div id="dmSoftvalFileMessages"></div>
        <form id="formDmSoftvalFile" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="validation_id" id="dmSvfValidationId">
          <div class="mb-3">
            <label class="form-label fw-semibold small">Fichier (PDF, images, docs…)</label>
            <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg">
          </div>
          <div class="d-flex justify-content-end gap-2 border-top pt-3">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary px-4">Téléverser</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- Modal: Data Logger --}}
<div class="modal fade" id="dmDlModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header" style="background:#1a3a6b;color:#fff;">
        <h5 class="modal-title"><i class="bi bi-thermometer-half me-2"></i>Data Logger</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body px-4 py-3">
        <div id="dmDlMessages"></div>
        <form id="formDmDl">
          @csrf
          <input type="hidden" name="id" id="dmDlId">
          <input type="hidden" name="project_id" value="{{ $project_id }}">
          <div class="row g-2 mb-3">
            <div class="col-8">
              <label class="form-label fw-semibold small">Nom / Modèle *</label>
              <input type="text" name="name" id="dmDlName" class="form-control" required>
            </div>
            <div class="col-4">
              <label class="form-label fw-semibold small">N° Série</label>
              <input type="text" name="serial_number" id="dmDlSerial" class="form-control">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold small">Emplacement / Déploiement</label>
            <input type="text" name="location" id="dmDlLocation" class="form-control" placeholder="Insectarium, chambre froide…">
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label fw-semibold small">Date de validation</label>
              <input type="date" name="validation_date" id="dmDlDate" class="form-control">
            </div>
            <div class="col-6">
              <label class="form-label fw-semibold small">Validé par</label>
              <input type="text" name="validated_by" id="dmDlBy" class="form-control">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold small">Notes</label>
            <textarea name="notes" id="dmDlNotes" class="form-control" rows="2"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold small">Statut</label>
            <select name="status" id="dmDlStatus" class="form-select">
              <option value="draft">Brouillon</option>
              <option value="validated">Validé</option>
            </select>
          </div>
          <div class="d-flex justify-content-end gap-2 border-top pt-3">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary px-4">Enregistrer</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- Modal: Data Logger File Upload --}}
<div class="modal fade" id="dmDlFileModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header" style="background:#0d6efd;color:#fff;">
        <h5 class="modal-title"><i class="bi bi-upload me-2"></i>Téléverser — Data Logger</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body px-4 py-3">
        <div id="dmDlFileMessages"></div>
        <form id="formDmDlFile" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="datalogger_validation_id" id="dmDlfDlId">
          <div class="mb-3">
            <label class="form-label fw-semibold small">Fichier</label>
            <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg">
          </div>
          <div class="d-flex justify-content-end gap-2 border-top pt-3">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary px-4">Téléverser</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- Modal: Double Entry --}}
<div class="modal fade" id="dmDeModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background:#1a3a6b;color:#fff;">
        <h5 class="modal-title"><i class="bi bi-input-cursor-text me-2"></i>Session de double saisie</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body px-4 py-3">
        <div id="dmDeMessages"></div>
        <form id="formDmDe" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="id" id="dmDeId">
          <input type="hidden" name="project_id" value="{{ $project_id }}">
          <div class="mb-3">
            <label class="form-label fw-semibold small">Base de données concernée</label>
            <select name="database_id" id="dmDeDatabaseId" class="form-select">
              <option value="">— Sélectionner —</option>
              @foreach($dmDatabases as $db)
              <option value="{{ $db->id }}">{{ $db->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold small">Date de la 1ère saisie *</label>
              <input type="date" name="first_entry_date" id="dmDeDate1" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold small">Effectuée par *</label>
              <input type="text" name="first_entry_by" id="dmDeBy1" class="form-control" required placeholder="Noms des opérateurs…">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold small">Date de la 2ème saisie *</label>
              <input type="date" name="second_entry_date" id="dmDeDate2" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold small">Effectuée par *</label>
              <input type="text" name="second_entry_by" id="dmDeBy2" class="form-control" required placeholder="Noms des opérateurs…">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold small">Rapport de comparaison (PDF / document)</label>
            <input type="file" name="comparison_file" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx">
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold small">Conformité</label>
            <select name="is_compliant" id="dmDeCompliant" class="form-select">
              <option value="">— Non évalué —</option>
              <option value="1">Conforme</option>
              <option value="0">Non conforme</option>
            </select>
          </div>
          <div class="mb-3" id="dmDeCommentsBlock" style="display:none;">
            <label class="form-label fw-semibold small">Commentaires (erreurs constatées)</label>
            <textarea name="comments" id="dmDeComments" class="form-control" rows="3" placeholder="Décrire les écarts/erreurs constatés…"></textarea>
          </div>
          <div class="d-flex justify-content-end gap-2 border-top pt-3">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary px-4">Enregistrer</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- ════════════════════════════════════════════════════════════════════════
     JAVASCRIPT
══════════════════════════════════════════════════════════════════════ --}}
<script>
(function () {
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content;

function dmPost(url, formData, msgBoxId, onSuccess) {
    const msgBox = document.getElementById(msgBoxId);
    if (msgBox) msgBox.innerHTML = '';
    fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF }, body: formData })
        .then(r => r.json())
        .then(d => {
            if (d.code_erreur === 0) { onSuccess(d); }
            else if (msgBox) { msgBox.innerHTML = '<div class="alert alert-danger py-2">' + (d.message || 'Erreur') + '</div>'; }
        })
        .catch(() => { if (msgBox) msgBox.innerHTML = '<div class="alert alert-danger py-2">Erreur réseau.</div>'; });
}

function confirmDelete(msg, url, data) {
    if (!confirm(msg)) return;
    const fd = new FormData();
    fd.append('_token', CSRF);
    Object.entries(data).forEach(([k,v]) => fd.append(k, v));
    fetch(url, { method:'POST', headers:{'X-CSRF-TOKEN':CSRF}, body:fd })
        .then(r=>r.json())
        .then(d=>{ if(d.code_erreur===0) location.reload(); else alert(d.message||'Erreur'); })
        .catch(()=>alert('Erreur réseau'));
}

// ── Database ───────────────────────────────────────────────────────────────
window.openDbModal = function(id, name, type, labTestId, desc) {
    document.getElementById('dmDbId').value = id || '';
    document.getElementById('dmDbName').value = name || '';
    document.getElementById('dmDbDesc').value = desc || '';
    document.getElementById('dmDbType').value = type || 'other';
    document.getElementById('dmDbLabTest').value = labTestId || '';
    document.getElementById('dmDbMessages').innerHTML = '';
    new bootstrap.Modal(document.getElementById('dmDbModal')).show();
};
document.getElementById('formDmDb').addEventListener('submit', function(e) {
    e.preventDefault();
    dmPost('{{ route("dm.database.save") }}', new FormData(this), 'dmDbMessages', () => location.reload());
});
window.deleteDb = function(id) { confirmDelete('Supprimer cette base de données ?', '{{ route("dm.database.delete") }}', {id}); };

// ── PC ─────────────────────────────────────────────────────────────────────
window.openPcModal = function(id, name, serial, isGlp, assignedAt, notes) {
    document.getElementById('dmPcId').value = id || '';
    document.getElementById('dmPcName').value = name || '';
    document.getElementById('dmPcSerial').value = serial || '';
    document.getElementById('dmPcIsGlp').value = (isGlp !== undefined && isGlp !== null) ? isGlp : {{ $isGlp ? 1 : 0 }};
    document.getElementById('dmPcAssignedAt').value = assignedAt || '';
    document.getElementById('dmPcNotes').value = notes || '';
    document.getElementById('dmPcMessages').innerHTML = '';
    new bootstrap.Modal(document.getElementById('dmPcModal')).show();
};
document.getElementById('formDmPc').addEventListener('submit', function(e) {
    e.preventDefault();
    dmPost('{{ route("dm.pc.save") }}', new FormData(this), 'dmPcMessages', () => location.reload());
});
window.deletePc = function(id) { confirmDelete('Supprimer cette attribution PC ?', '{{ route("dm.pc.delete") }}', {id}); };

window.openReturnPcModal = function(id, name) {
    document.getElementById('dmReturnPcId').value = id;
    document.getElementById('dmReturnPcName').textContent = name;
    document.getElementById('dmReturnPcMessages').innerHTML = '';
    new bootstrap.Modal(document.getElementById('dmReturnPcModal')).show();
};
document.getElementById('formDmReturnPc').addEventListener('submit', function(e) {
    e.preventDefault();
    dmPost('{{ route("dm.pc.return") }}', new FormData(this), 'dmReturnPcMessages', () => location.reload());
});

// ── Software Validation ────────────────────────────────────────────────────
const _svData = @json($softValidations->keyBy('id'));

window.openSoftvalModal = function(id) {
    const el = (fId) => document.getElementById(fId);
    ['dmSvId','dmSvSoftware','dmSvVersion','dmSvDate','dmSvComputerId','dmSvDoneBy','dmSvReason',
     'dmSvDatabase','dmSvOs','dmSvCpu','dmSvRam','dmSvFolder','dmSvFile',
     'dmSvSopCode','dmSvSopSection','dmSvDataLogger','dmSvTemp','dmSvHumidity','dmSvDetails'].forEach(f => { if(el(f)) el(f).value=''; });
    if(el('dmSvRecorded')) el('dmSvRecorded').value='0';
    if(el('dmSvKit')) el('dmSvKit').value='';
    if(el('dmSvStatus')) el('dmSvStatus').value='draft';
    if (id && _svData[id]) {
        const d = _svData[id];
        el('dmSvId').value = id;
        if(el('dmSvSoftware')) el('dmSvSoftware').value = d.software_name||'';
        if(el('dmSvVersion')) el('dmSvVersion').value = d.current_software_version||'';
        if(el('dmSvDate')) el('dmSvDate').value = d.validation_date||'';
        if(el('dmSvComputerId')) el('dmSvComputerId').value = d.computer_id||'';
        if(el('dmSvDoneBy')) el('dmSvDoneBy').value = d.validation_done_by||'';
        if(el('dmSvReason')) el('dmSvReason').value = d.reason_for_validation||'';
        if(el('dmSvDatabase')) el('dmSvDatabase').value = d.database_id||'';
        if(el('dmSvOs')) el('dmSvOs').value = d.operating_system||'';
        if(el('dmSvCpu')) el('dmSvCpu').value = d.cpu||'';
        if(el('dmSvRam')) el('dmSvRam').value = d.ram||'';
        if(el('dmSvRecorded')) el('dmSvRecorded').value = d.is_recorded_in_computer ? '1':'0';
        if(el('dmSvKit')) el('dmSvKit').value = d.validation_kit_status||'';
        if(el('dmSvFolder')) el('dmSvFolder').value = d.validation_folder_name||'';
        if(el('dmSvFile')) el('dmSvFile').value = d.validation_file_name||'';
        if(el('dmSvSopCode')) el('dmSvSopCode').value = d.sop_document_code||'';
        if(el('dmSvSopSection')) el('dmSvSopSection').value = d.sop_section||'';
        if(el('dmSvDataLogger')) el('dmSvDataLogger').value = d.data_logger_env||'';
        if(el('dmSvTemp')) el('dmSvTemp').value = d.env_temperature||'';
        if(el('dmSvHumidity')) el('dmSvHumidity').value = d.env_humidity||'';
        if(el('dmSvDetails')) el('dmSvDetails').value = d.details_of_procedure||'';
        if(el('dmSvStatus')) el('dmSvStatus').value = d.status||'draft';
    }
    document.getElementById('dmSoftvalMessages').innerHTML = '';
    new bootstrap.Modal(document.getElementById('dmSoftvalModal')).show();
};
document.getElementById('formDmSoftval').addEventListener('submit', function(e) {
    e.preventDefault();
    dmPost('{{ route("dm.softval.save") }}', new FormData(this), 'dmSoftvalMessages', () => location.reload());
});
window.deleteSoftval = function(id) { confirmDelete('Supprimer cette validation logicielle ?', '{{ route("dm.softval.delete") }}', {id}); };

window.openSoftvalFileUpload = function(validationId) {
    document.getElementById('dmSvfValidationId').value = validationId;
    document.getElementById('dmSoftvalFileMessages').innerHTML = '';
    document.getElementById('formDmSoftvalFile').reset();
    document.getElementById('dmSvfValidationId').value = validationId;
    new bootstrap.Modal(document.getElementById('dmSoftvalFileModal')).show();
};
document.getElementById('formDmSoftvalFile').addEventListener('submit', function(e) {
    e.preventDefault();
    dmPost('{{ route("dm.softval.uploadFile") }}', new FormData(this), 'dmSoftvalFileMessages', () => location.reload());
});
window.deleteSoftvalFile = function(id, event) { event.preventDefault(); confirmDelete('Supprimer ce fichier ?', '{{ route("dm.softval.deleteFile") }}', {id}); };

// ── Data Logger ────────────────────────────────────────────────────────────
const _dlData = @json($dlValidations->keyBy('id'));

window.openDlModal = function(id) {
    const el = (fId) => document.getElementById(fId);
    ['dmDlId','dmDlName','dmDlSerial','dmDlLocation','dmDlDate','dmDlBy','dmDlNotes'].forEach(f => { if(el(f)) el(f).value=''; });
    if(el('dmDlStatus')) el('dmDlStatus').value='draft';
    if (id && _dlData[id]) {
        const d = _dlData[id];
        el('dmDlId').value = id;
        if(el('dmDlName')) el('dmDlName').value = d.name||'';
        if(el('dmDlSerial')) el('dmDlSerial').value = d.serial_number||'';
        if(el('dmDlLocation')) el('dmDlLocation').value = d.location||'';
        if(el('dmDlDate')) el('dmDlDate').value = d.validation_date||'';
        if(el('dmDlBy')) el('dmDlBy').value = d.validated_by||'';
        if(el('dmDlNotes')) el('dmDlNotes').value = d.notes||'';
        if(el('dmDlStatus')) el('dmDlStatus').value = d.status||'draft';
    }
    document.getElementById('dmDlMessages').innerHTML = '';
    new bootstrap.Modal(document.getElementById('dmDlModal')).show();
};
document.getElementById('formDmDl').addEventListener('submit', function(e) {
    e.preventDefault();
    dmPost('{{ route("dm.datalogger.save") }}', new FormData(this), 'dmDlMessages', () => location.reload());
});
window.deleteDl = function(id) { confirmDelete('Supprimer ce data logger ?', '{{ route("dm.datalogger.delete") }}', {id}); };

window.openDlFileUpload = function(dlId) {
    document.getElementById('dmDlfDlId').value = dlId;
    document.getElementById('dmDlFileMessages').innerHTML = '';
    document.getElementById('formDmDlFile').reset();
    document.getElementById('dmDlfDlId').value = dlId;
    new bootstrap.Modal(document.getElementById('dmDlFileModal')).show();
};
document.getElementById('formDmDlFile').addEventListener('submit', function(e) {
    e.preventDefault();
    dmPost('{{ route("dm.datalogger.uploadFile") }}', new FormData(this), 'dmDlFileMessages', () => location.reload());
});
window.deleteDlFile = function(id, event) { event.preventDefault(); confirmDelete('Supprimer ce fichier ?', '{{ route("dm.datalogger.deleteFile") }}', {id}); };

// ── Double Entry ───────────────────────────────────────────────────────────
const _deData = @json($doubleEntries->keyBy('id'));

document.getElementById('dmDeCompliant').addEventListener('change', function() {
    document.getElementById('dmDeCommentsBlock').style.display = (this.value === '0') ? '' : 'none';
});

window.openDeModal = function(id) {
    const el = (fId) => document.getElementById(fId);
    ['dmDeId','dmDeDatabaseId','dmDeDate1','dmDeBy1','dmDeDate2','dmDeBy2','dmDeComments'].forEach(f => { if(el(f)) el(f).value=''; });
    if(el('dmDeCompliant')) el('dmDeCompliant').value='';
    if(el('dmDeCommentsBlock')) el('dmDeCommentsBlock').style.display='none';
    if (id && _deData[id]) {
        const d = _deData[id];
        el('dmDeId').value = id;
        if(el('dmDeDatabaseId')) el('dmDeDatabaseId').value = d.database_id||'';
        if(el('dmDeDate1')) el('dmDeDate1').value = d.first_entry_date||'';
        if(el('dmDeBy1')) el('dmDeBy1').value = d.first_entry_by||'';
        if(el('dmDeDate2')) el('dmDeDate2').value = d.second_entry_date||'';
        if(el('dmDeBy2')) el('dmDeBy2').value = d.second_entry_by||'';
        if(el('dmDeCompliant')) el('dmDeCompliant').value = d.is_compliant===null?'':(d.is_compliant?'1':'0');
        if(el('dmDeComments')) el('dmDeComments').value = d.comments||'';
        if(d.is_compliant===false && el('dmDeCommentsBlock')) el('dmDeCommentsBlock').style.display='';
    }
    document.getElementById('dmDeMessages').innerHTML = '';
    new bootstrap.Modal(document.getElementById('dmDeModal')).show();
};
document.getElementById('formDmDe').addEventListener('submit', function(e) {
    e.preventDefault();
    dmPost('{{ route("dm.doubleEntry.save") }}', new FormData(this), 'dmDeMessages', () => location.reload());
});
window.deleteDe = function(id) { confirmDelete('Supprimer cette session de double saisie ?', '{{ route("dm.doubleEntry.delete") }}', {id}); };

})();
</script>

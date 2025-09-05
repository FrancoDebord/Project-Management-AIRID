<style>
    /* :root {
        --brand: #c20102;
        --brand-dark: #8b0001;
        --brand-soft: #f5b5b5;
    } */

   :root {
    --brand: #7c3aed;       /* Violet vibrant */
    --brand-dark: #5b21b6;  /* Violet foncé */
    --brand-soft: #e9d5ff;  /* Violet pastel */
}

    /* Accordion look */
    .accordion-item {
        border: none;
        border-radius: 14px !important;
        overflow: hidden;
        box-shadow: 0 8px 22px rgba(0, 0, 0, .08);
        margin-bottom: 18px;
    }

    .accordion-button {
        background: linear-gradient(90deg, var(--brand), var(--brand-dark));
        color: #fff;
        font-weight: 600;
        padding: 16px 22px;
    }

    .accordion-button:not(.collapsed) {
        color: #fff;
        box-shadow: inset 0 -1px 0 rgba(255, 255, 255, .15);
    }

    .accordion-button:focus {
        box-shadow: 0 0 0 .2rem rgba(194, 1, 2, .25);
    }

    /* Chip buttons (fils) */
    .btn-chip {
        border-radius: 999px;
        border: 1px solid #e6a3a3;
        background: #fff;
        color: #7a3a3a;
        font-weight: 600;
        padding: 10px 16px;
        transition: .25s ease;
    }

    .btn-chip:hover {
        background: #ffe4e4;
        color: #6b0506;
        transform: translateY(-1px);
    }

    .btn-chip.active {
        background: linear-gradient(90deg, var(--brand), var(--brand-dark));
        color: #fff;
        border-color: transparent;
        box-shadow: 0 6px 14px rgba(194, 1, 2, .25);
    }

    /* Table styling */
    .table-wrap {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 6px 18px rgba(0, 0, 0, .06);
    }

    .table thead th {
        background: linear-gradient(90deg, var(--brand), var(--brand-dark));
        color: #fff;
        border: none;
        font-weight: 600;
    }

    .table tbody tr {
        vertical-align: middle;
    }

    .status-badge {
        border-radius: 999px;
        font-weight: 600;
        padding: .35rem .65rem;
    }

    .status-open {
        background: #ffe8e8;
        color: #a10001;
    }

    .status-inprogress {
        background: #fff4cf;
        color: #7a5a00;
    }

    .status-done {
        background: #e7f7ec;
        color: #207a3a;
    }

    .filter-info {
        color: #6c757d;
        font-size: .95rem;
    }

    .clear-filter {
        color: var(--brand);
        text-decoration: none;
        font-weight: 600;
    }

    .clear-filter:hover {
        color: var(--brand-dark);
    }
</style>


<div class="row">
    <div class="accordion" id="zonesAccordion">

        @php
            $all_study_types = App\Models\Pro_StudyType::orderBy('level_type', 'asc')->get();
        @endphp

        @forelse ($all_study_types as $study_type)

        @php
            $compteur=0;
        @endphp
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingLab{{ $study_type->id }}">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                        data-bs-target="#zoneLab{{ $study_type->id }}" aria-expanded="{{ $compteur == 0?"true":"false" }}"
                        aria-controls="zoneLab{{ $study_type->id }}">
                        <i class="bi bi-flask me-2"></i> {{ $study_type->study_type_name }}
                    </button>

                </h2>
                <div id="zoneLab{{ $study_type->id }}" class="accordion-collapse collapse {{ $compteur++ == 0?"show":"" }} "
                    aria-labelledby="headingLab{{ $study_type->id }}" data-bs-parent="#zonesAccordion">
                    <div class="accordion-body zone-block" data-zone="lab">

                        <div class="row mt-2 mb-3">
                            <div class="col-12">
                                <button type="button" class="btn btn-outline-danger add-activity" data-project-id="{{ $project->id }}" data-ajax-route="{{ route('getStudyTypeById', ['id' => $study_type->id]) }}" data-study-type-id="{{ $study_type->id }}">Add Activity</button>

                            </div>
                        </div>
                        <!-- Fils -->
                        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">

                            @php
                                $all_subcategories = $study_type->allSubCategories;
                            @endphp
                            @forelse ($all_subcategories as $sub_category)
                                <button type="button" class="btn btn-chip"
                                    data-child="{{ $sub_category->study_sub_category_name }}">{{ $sub_category->study_sub_category_name }}</button>
                            @empty
                                <span>Aucun sous-catégorie disponible</span>
                            @endforelse

                            <div class="ms-auto filter-info">
                                Filtre : <strong class="current-filter">Aucun</strong>
                                <a href="#" class="ms-2 clear-filter d-none">Effacer</a>
                            </div>
                        </div>

                        <!-- Tableau -->
                        <div class="table-wrap">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width:20%">Activity</th>
                                            <th style="width:15%">SubCategory</th>
                                            <th style="width:18%">Parent Activity</th>
                                            <th style="width:14%">Responsible</th>
                                            <th style="width:14%">Due Date</th>
                                            <th style="width:10%">Status</th>
                                            <th style="width:9%">Action</th>
                                        </tr>
                                    </thead>

                                    @php
                                        $all_activities_project = $project->allActivitiesProject($study_type->id)->get();
                                    @endphp
                                    <tbody>

                                        @forelse ($all_activities_project as $study_activity)
                                            @php
                                                $categorie = $study_activity->category;
                                                $personneResponsable = $study_activity->personneResponsable;
                                                $status = $study_activity->status;

                                                $status_progress = 'status-inprogress';

                                                if ($status == 'pending') {
                                                    $status_progress = '';
                                                } elseif ($status == 'in_progress') {
                                                    $status_progress = 'status-inprogress';
                                                } elseif ($status == 'completed') {
                                                    # code...
                                                    $status_progress = 'status-done';
                                                }

                                            @endphp
                                            <tr data-child="{{ $categorie->study_sub_category_name }}">
                                                <td>{{ $study_activity->study_activity_name }}</td>
                                                <td>
                                                    <span
                                                        class="badge bg-light text-dark">{{ $categorie->study_sub_category_name }}</span>
                                                </td>

                                                @php
                                                    $parent_activity = $study_activity->ParentActivity;
                                                @endphp
                                                <td>
                                                    <span
                                                        class="badge bg-light text-dark">{{ $parent_activity? $parent_activity->study_activity_name:"N/A" }}</span>
                                                </td>
                                                <td>{{ $personneResponsable ? $personneResponsable->titre . ' ' . $personneResponsable->prenom . ' ' . $personneResponsable->nom : 'Not yet assigned' }}
                                                </td>
                                                <td>{{ $study_activity->estimated_activity_date }}</td>
                                                <td><span
                                                        class="status-badge {{ $status_progress }}">{{ $study_activity->status }}</span>
                                                </td>
                                                <td>
                                                    <a href="#" class="btn btn-outline-warning">
                                                        <i class="fa fa-edit">&nbsp;</i>
                                                    </a>
                                                    <a href="#" class="btn btn-outline-danger">
                                                        <i class="fa fa-trash-alt">&nbsp;</i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">Aucune activité trouvée</td>
                                            </tr>
                                        @endforelse


                                    </tbody>
                                </table>
                            </div>
                        </div> <!-- /table-wrap -->
                    </div>
                </div>
            </div>
        @empty

        @endforelse
        <!-- ========== 1) Lab Study ========== -->
        {{-- <div class="accordion-item">
            <h2 class="accordion-header" id="headingLab">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#zoneLab"
                    aria-expanded="true" aria-controls="zoneLab">
                    <i class="bi bi-flask me-2"></i> Lab Study
                </button>
            </h2>
            <div id="zoneLab" class="accordion-collapse collapse show" aria-labelledby="headingLab"
                data-bs-parent="#zonesAccordion">
                <div class="accordion-body zone-block" data-zone="lab">
                    <!-- Fils -->
                    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                        <button type="button" class="btn btn-chip" data-child="ITN Evaluation">ITN Evaluation</button>
                        <button type="button" class="btn btn-chip" data-child="IRS Evaluation">IRS Evaluation</button>
                        <button type="button" class="btn btn-chip" data-child="Resistance Testing">Resistance
                            Testing</button>

                        <div class="ms-auto filter-info">
                            Filtre : <strong class="current-filter">Aucun</strong>
                            <a href="#" class="ms-2 clear-filter d-none">Effacer</a>
                        </div>
                    </div>

                    <!-- Tableau -->
                    <div class="table-wrap">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th style="width:38%">Tâche</th>
                                        <th style="width:18%">Fils</th>
                                        <th style="width:18%">Responsable</th>
                                        <th style="width:14%">Échéance</th>
                                        <th style="width:12%">Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr data-child="ITN Evaluation">
                                        <td>Préparer les échantillons ITN</td>
                                        <td><span class="badge bg-light text-dark">ITN Evaluation</span></td>
                                        <td>Amira</td>
                                        <td>2025-09-20</td>
                                        <td><span class="status-badge status-inprogress">En cours</span></td>
                                    </tr>
                                    <tr data-child="IRS Evaluation">
                                        <td>Calibrer les doses IRS</td>
                                        <td><span class="badge bg-light text-dark">IRS Evaluation</span></td>
                                        <td>Jonas</td>
                                        <td>2025-09-18</td>
                                        <td><span class="status-badge status-open">Ouverte</span></td>
                                    </tr>
                                    <tr data-child="Resistance Testing">
                                        <td>Tester la résistance (lot B)</td>
                                        <td><span class="badge bg-light text-dark">Resistance Testing</span></td>
                                        <td>Salma</td>
                                        <td>2025-09-25</td>
                                        <td><span class="status-badge status-done">Fait</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- /table-wrap -->
                </div>
            </div>
        </div> --}}

        <!-- ========== 2) Hut Trial ========== -->
        {{-- <div class="accordion-item">
            <h2 class="accordion-header" id="headingHut">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#zoneHut" aria-expanded="false" aria-controls="zoneHut">
                    <i class="bi bi-house-door me-2"></i> Hut Trial
                </button>
            </h2>
            <div id="zoneHut" class="accordion-collapse collapse" aria-labelledby="headingHut"
                data-bs-parent="#zonesAccordion">
                <div class="accordion-body zone-block" data-zone="hut">
                    <!-- Fils -->
                    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                        <button type="button" class="btn btn-chip" data-child="ITN Evaluation">ITN Evaluation</button>
                        <button type="button" class="btn btn-chip" data-child="IRS Evaluation">IRS Evaluation</button>
                        <button type="button" class="btn btn-chip" data-child="Resistance Testing">Resistance
                            Testing</button>
                        <button type="button" class="btn btn-chip" data-child="Spatial Repellents">Spatial
                            Repellents</button>
                        <button type="button" class="btn btn-chip" data-child="Other Products">Other Products</button>

                        <div class="ms-auto filter-info">
                            Filtre : <strong class="current-filter">Aucun</strong>
                            <a href="#" class="ms-2 clear-filter d-none">Effacer</a>
                        </div>
                    </div>

                    <!-- Tableau -->
                    <div class="table-wrap">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th style="width:38%">Tâche</th>
                                        <th style="width:18%">Fils</th>
                                        <th style="width:18%">Responsable</th>
                                        <th style="width:14%">Échéance</th>
                                        <th style="width:12%">Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr data-child="ITN Evaluation">
                                        <td>Installer moustiquaires de test</td>
                                        <td><span class="badge bg-light text-dark">ITN Evaluation</span></td>
                                        <td>Felix</td>
                                        <td>2025-09-19</td>
                                        <td><span class="status-badge status-inprogress">En cours</span></td>
                                    </tr>
                                    <tr data-child="Spatial Repellents">
                                        <td>Valider protocole répulsifs spatiaux</td>
                                        <td><span class="badge bg-light text-dark">Spatial Repellents</span></td>
                                        <td>Nadia</td>
                                        <td>2025-09-30</td>
                                        <td><span class="status-badge status-open">Ouverte</span></td>
                                    </tr>
                                    <tr data-child="Other Products">
                                        <td>Évaluer produit X (prototype)</td>
                                        <td><span class="badge bg-light text-dark">Other Products</span></td>
                                        <td>Louis</td>
                                        <td>2025-10-05</td>
                                        <td><span class="status-badge status-open">Ouverte</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- /table-wrap -->
                </div>
            </div>
        </div> --}}

        <!-- ========== 3) Community Trial ========== -->
        {{-- <div class="accordion-item">
            <h2 class="accordion-header" id="headingComm">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#zoneComm" aria-expanded="false" aria-controls="zoneComm">
                    <i class="bi bi-people me-2"></i> Community Trial
                </button>
            </h2>
            <div id="zoneComm" class="accordion-collapse collapse" aria-labelledby="headingComm"
                data-bs-parent="#zonesAccordion">
                <div class="accordion-body zone-block" data-zone="community">
                    <!-- Fils -->
                    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                        <button type="button" class="btn btn-chip" data-child="ITN Phase 3">ITN Phase 3</button>
                        <button type="button" class="btn btn-chip" data-child="IRS Phase 3">IRS Phase 3</button>
                        <button type="button" class="btn btn-chip" data-child="RCT">RCT</button>
                        <button type="button" class="btn btn-chip" data-child="Other Products">Other
                            Products</button>

                        <div class="ms-auto filter-info">
                            Filtre : <strong class="current-filter">Aucun</strong>
                            <a href="#" class="ms-2 clear-filter d-none">Effacer</a>
                        </div>
                    </div>

                    <!-- Tableau -->
                    <div class="table-wrap">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th style="width:38%">Tâche</th>
                                        <th style="width:18%">Fils</th>
                                        <th style="width:18%">Responsable</th>
                                        <th style="width:14%">Échéance</th>
                                        <th style="width:12%">Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr data-child="ITN Phase 3">
                                        <td>Pré-enrôlement des ménages</td>
                                        <td><span class="badge bg-light text-dark">ITN Phase 3</span></td>
                                        <td>Brice</td>
                                        <td>2025-09-22</td>
                                        <td><span class="status-badge status-inprogress">En cours</span></td>
                                    </tr>
                                    <tr data-child="IRS Phase 3">
                                        <td>Plan logistique IRS P3</td>
                                        <td><span class="badge bg-light text-dark">IRS Phase 3</span></td>
                                        <td>Rania</td>
                                        <td>2025-10-01</td>
                                        <td><span class="status-badge status-open">Ouverte</span></td>
                                    </tr>
                                    <tr data-child="RCT">
                                        <td>Randomisation des clusters</td>
                                        <td><span class="badge bg-light text-dark">RCT</span></td>
                                        <td>David</td>
                                        <td>2025-10-10</td>
                                        <td><span class="status-badge status-open">Ouverte</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- /table-wrap -->
                </div>
            </div>
        </div> --}}

    </div> <!-- /accordion -->
</div>

@include('partials.add-activity-project')

<script>
    // Gestion locale des filtres par zone
    document.querySelectorAll('.zone-block').forEach(zone => {
        const chips = zone.querySelectorAll('.btn-chip');
        const rows = zone.querySelectorAll('tbody tr');
        const label = zone.querySelector('.current-filter');
        const clear = zone.querySelector('.clear-filter');

        function applyFilter(value) {
            if (!value) {
                rows.forEach(r => r.classList.remove('d-none'));
                label.textContent = 'Aucun';
                clear.classList.add('d-none');
                chips.forEach(c => c.classList.remove('active'));
                return;
            }
            rows.forEach(r => {
                r.classList.toggle('d-none', r.dataset.child !== value);
            });
            label.textContent = value;
            clear.classList.remove('d-none');
        }

        chips.forEach(btn => {
            btn.addEventListener('click', () => {
                const isActive = btn.classList.contains('active');
                chips.forEach(c => c.classList.remove('active'));
                if (isActive) {
                    applyFilter(null);
                } else {
                    btn.classList.add('active');
                    applyFilter(btn.dataset.child);
                }
            });
        });

        clear?.addEventListener('click', (e) => {
            e.preventDefault();
            applyFilter(null);
        });
    });
</script>

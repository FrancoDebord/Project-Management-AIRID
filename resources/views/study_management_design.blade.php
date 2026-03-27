    @extends('index-new')
    @section('title', 'Gestion Projet')



    <style>
        .wizard {
            display: flex;
            margin: 30px auto;
            padding: 0;
            list-style: none;
        }

        .wizard li {
            position: relative;
            flex: 1;
        }

        .wizard li a {
            display: block;
            padding: 12px 18px;
            background: #f5b5b5;
            /* couleur inactive */
            color: #333;
            font-weight: 600;
            font-size: 14px;
            text-align: center;
            text-decoration: none;
            position: relative;
            transition: background 0.3s ease, color 0.3s ease;
            border-radius: 0;
        }

        /* Pointe à droite */
        /* .wizard li a::after {
            content: "";
            position: absolute;
            top: 0;
            right: -20px;
            width: 0;
            height: 0;
            border-top: 30px solid transparent;
            border-bottom: 30px solid transparent;
            border-left: 20px solid #f5b5b5;
            z-index: 1;
            transition: border-left-color 0.3s ease;
        } */

        /* Pointe à gauche (fente arrière) */
        /* .wizard li:not(:first-child) a::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 0;
            border-top: 30px solid transparent;
            border-bottom: 30px solid transparent;
            border-left: 20px solid #fff;
            z-index: 2;
        } */

        /* Actif */
        .wizard li a.active {
            background: #c20102;
            color: #fff;
        }

        .wizard li a.active::after {
            border-left-color: #c20102;
        }

        /* Dernier élément sans pointe */
        .wizard li:last-child a::after {
            display: none;
        }

        /* Contenu */
        .tab-content {
            margin-top: 25px;
            padding: 25px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
        }

        .tab-pane h4 {
            color: #c20102;
            font-weight: bold;
        }
    </style>

    <style>
        .alert-success-custom {
            background: linear-gradient(135deg, #9CDAAAFF, #9FDBACFF);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 18px 22px;
            font-size: 1rem;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success-custom .icon {
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .alert-success-custom .btn-close {
            filter: brightness(0) invert(1);
            margin-left: auto;
        }
    </style>

     <style>
    .action-card {
      display: flex;
      align-items: center;
      gap: 15px;
      padding: 15px;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    }

    .btn-custom {
      font-weight: 600;
      padding: 12px 18px;
      border-radius: 8px;
      transition: all 0.3s ease;
      box-shadow: 0 3px 8px rgba(0,0,0,0.1);
      white-space: nowrap;
    }

    .btn-primary-custom {
      background-color: #c20102;
      color: #fff;
      border: none;
    }
    .btn-primary-custom:hover {
      background-color: #a10001;
      transform: translateY(-2px);
    }

    .btn-secondary-custom {
      background-color: #e45c5d;
      color: #fff;
      border: none;
    }
    .btn-secondary-custom:hover {
      background-color: #c94a4b;
      transform: translateY(-2px);
    }

    .btn-tertiary-custom {
      background-color: #f28b8c;
      color: #fff;
      border: none;
    }
    .btn-tertiary-custom:hover {
      background-color: #d67374;
      transform: translateY(-2px);
    }

    .btn-light-custom {
      background-color: #f5b5b5;
      color: #333;
      border: none;
    }
    .btn-light-custom:hover {
      background-color: #e49c9c;
      color: #fff;
      transform: translateY(-2px);
    }

    .progress {
      flex: 1;
      height: 12px;
      border-radius: 6px;
      overflow: hidden;
      background: #eee;
    }

    .progress-bar {
      background-color: #c20102;
    }

    .check-icon {
      font-size: 1.5rem;
      color: #28a745;
      display: none; /* caché tant que non complété */
    }

    .completed .check-icon {
      display: inline-block;
    }
  </style>


@section('css_vendor')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
@endsection

@section('js_vendor')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
@endsection
    @section('content')
        <div class="row justify-content-center">
            <div class="col-lg-11 col-xl-11">

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h2 class="h5">Liste des projets</h2>

                        <form action="">
                            <div class="input-group mb-3">

                                @php
                                    $project_id = request()->get('project_id');
                                @endphp
                                <select name="project_id" id="project_id"
                                    class="form-select form-control selectpicker show-tick" data-live-search="true">
                                    <option value="">Sélectionner un projet</option>
                                    @foreach ($all_projects as $proj)
                                        <option value="{{ $proj->id }}"
                                            {{ $project_id == $proj->id ? 'selected' : '' }}>{{ $proj->project_code }}
                                        </option>
                                    @endforeach
                                </select>

                                <button class="btn btn-outline-secondary" type="submit" id="button-search">Charger ce
                                    projet </button>

                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 border-top"></div>
                                    <span class="mx-2">ou</span>
                                    <div class="flex-grow-1 border-top"></div>
                                </div>

                                <a href="#" id="creer-nouveau-projet" class="btn btn-danger ms-3">Créer un nouveau
                                    projet</a>
                            </div>

                        </form>

                    </div>
                </div>


                @if ($project->project_code)
                    <div class="card">
                    <div class="card-body">
                        <h2 class="h5" style="color :  #c20102">Project Management Design for :
                            <span class=" ">
                                {{ $project ? $project->project_code . '(' . $project->project_title . ' )' : 'No project selected' }}
                            </span>
                        </h2>
                        <p>Use the tabs below to navigate through the different sections of the study management.</p>
                    </div>
                </div>

                  @if (session('success'))
                    <div class="alert alert-success-custom alert-dismissible fade show mt-3" role="alert">
                        <i class="bi bi-check-circle-fill icon"></i>
                        <div>
                            <strong>Success!</strong>  {{ session('success') }}
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($project && $project->archived_at)
                <div class="alert mb-3 mt-2 d-flex align-items-center gap-3 py-2 px-3" style="background:linear-gradient(90deg,#1a3a6b 0%,#c41230 100%);color:#fff;border-radius:.6rem;border:none;">
                    <i class="bi bi-lock-fill fs-5 flex-shrink-0"></i>
                    <div class="small">
                        <strong>Archived project — read-only.</strong>
                        All modifications are disabled. Go to <strong>Step 8 – Archiving Phase</strong> to unarchive.
                    </div>
                </div>
                @endif

                {{-- ── Phase Progress Banner ── --}}
                @if(isset($project_phase) && $project->project_code)
                @php
                    $phaseBanners = [
                        'study_creation' => [
                            'bg'    => 'linear-gradient(90deg,#6c757d,#495057)',
                            'icon'  => 'bi-hourglass',
                            'phase' => 'Step 1 — Study Creation',
                            'tab'   => '#step1',
                        ],
                        'protocol_details' => [
                            'bg'    => 'linear-gradient(90deg,#495057,#343a40)',
                            'icon'  => 'bi-file-earmark-ruled',
                            'phase' => 'Step 2 — Protocol Details',
                            'tab'   => '#step2',
                        ],
                        'protocol_development' => [
                            'bg'    => 'linear-gradient(90deg,#1a3a6b,#2a5aaa)',
                            'icon'  => 'bi-file-earmark-code',
                            'phase' => 'Step 3 — Protocol Development',
                            'tab'   => '#step3',
                        ],
                        'planning' => [
                            'bg'    => 'linear-gradient(90deg,#0d6efd,#0950c5)',
                            'icon'  => 'bi-calendar2-week',
                            'phase' => 'Step 4 — Planning Phase',
                            'tab'   => '#step4',
                        ],
                        'experimental' => [
                            'bg'    => 'linear-gradient(90deg,#fd7e14,#c96d0f)',
                            'icon'  => 'bi-activity',
                            'phase' => 'Step 5 — Experimental Phase',
                            'tab'   => '#step5',
                        ],
                        'quality_assurance' => [
                            'bg'    => 'linear-gradient(90deg,#6f42c1,#4e2d8e)',
                            'icon'  => 'bi-shield-check',
                            'phase' => 'Step 6 — Quality Assurance',
                            'tab'   => '#step6',
                        ],
                        'reporting' => [
                            'bg'    => 'linear-gradient(90deg,#20c997,#0f9d6b)',
                            'icon'  => 'bi-file-earmark-text',
                            'phase' => 'Step 7 — Report Phase',
                            'tab'   => '#step7',
                        ],
                        'archiving' => [
                            'bg'    => 'linear-gradient(90deg,#198754,#0d5c38)',
                            'icon'  => 'bi-archive',
                            'phase' => 'Step 8 — Archiving Phase',
                            'tab'   => '#step8',
                        ],
                        'all_done' => [
                            'bg'    => 'linear-gradient(90deg,#1a3a6b,#c41230)',
                            'icon'  => 'bi-patch-check-fill',
                            'phase' => 'Project Completed & Archived',
                            'tab'   => '#step8',
                        ],
                    ];

                    $phaseOrderBlade = [
                        'study_creation', 'protocol_details', 'protocol_development',
                        'planning', 'experimental', 'quality_assurance', 'reporting', 'archiving',
                    ];

                    $banner       = $phaseBanners[$project_phase] ?? $phaseBanners['study_creation'];
                    $dynamicNext  = $phaseStatuses[$project_phase]['next'] ?? null;

                    // Next phase label
                    $curIdx        = array_search($project_phase, $phaseOrderBlade);
                    $nextPhaseKey  = ($curIdx !== false && $curIdx < count($phaseOrderBlade) - 1)
                                        ? $phaseOrderBlade[$curIdx + 1] : null;
                    $nextPhaseLabel = $nextPhaseKey ? ($phaseBanners[$nextPhaseKey]['phase'] ?? null) : null;
                    $canCompleteNow = $phaseStatuses[$project_phase]['can_complete'] ?? false;
                @endphp
                <div class="d-flex align-items-center gap-3 mb-3 px-3 py-2 rounded-3"
                     style="background:{{ $banner['bg'] }};color:#fff;border:none;">
                    <i class="bi {{ $banner['icon'] }} fs-4 flex-shrink-0"></i>
                    <div class="flex-grow-1 small lh-sm">
                        <div class="fw-bold" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.07em;opacity:.75;">
                            Current Phase
                        </div>
                        <div class="fw-bold" style="font-size:.95rem;">{{ $banner['phase'] }}</div>
                        @if($dynamicNext)
                        <div style="opacity:.9;font-size:.82rem;">
                            <i class="bi bi-{{ $canCompleteNow ? 'check-circle' : 'arrow-right-circle' }} me-1"></i>{{ $dynamicNext }}
                        </div>
                        @endif
                        @if($nextPhaseLabel && $project_phase !== 'all_done')
                        <div style="opacity:.7;font-size:.75rem;margin-top:.15rem;">
                            <i class="bi bi-chevron-double-right me-1"></i>Next: <strong>{{ $nextPhaseLabel }}</strong>
                        </div>
                        @endif
                    </div>
                    <div class="flex-shrink-0 text-end">
                        <div style="font-size:1.5rem;font-weight:700;line-height:1;">{{ $execution_rate }}%</div>
                        <div style="font-size:.72rem;opacity:.8;">overall progress</div>
                        @php $bannerTab = $banner['tab']; @endphp
                        <button class="btn btn-sm mt-1 py-0 px-2"
                                style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.4);font-size:.75rem;"
                                onclick="(function(h){ var t=document.querySelector('[data-bs-toggle=tab][href=&quot;'+h+'&quot;]'); if(t) new bootstrap.Tab(t).show(); })('{{ $bannerTab }}')">
                            Go to step →
                        </button>
                    </div>
                </div>
                @endif

                @php $phasesCompleted = $project->phases_completed ?? []; @endphp
                @php $isGlp = $project && $project->is_glp; @endphp
                <ul class="wizard" id="myTab" role="tablist">
                    <li><a class="active" id="step1-tab" data-bs-toggle="tab" href="#step1" role="tab">1. Study Creation @if(in_array('study_creation',$phasesCompleted))<i class="bi bi-check-circle-fill text-success ms-1" style="font-size:.8rem;"></i>@endif</a></li>
                    <li><a id="step2-tab" data-bs-toggle="tab" href="#step2" role="tab">2. Protocol Details @if(in_array('protocol_details',$phasesCompleted))<i class="bi bi-check-circle-fill text-success ms-1" style="font-size:.8rem;"></i>@endif</a></li>
                    <li><a id="step3-tab" data-bs-toggle="tab" href="#step3" role="tab">3. Protocol Dev. @if(in_array('protocol_development',$phasesCompleted))<i class="bi bi-check-circle-fill text-success ms-1" style="font-size:.8rem;"></i>@endif</a></li>
                    @if($isGlp)
                    <li><a id="step4-tab" data-bs-toggle="tab" href="#step4" role="tab">4. Planning Phase @if(in_array('planning',$phasesCompleted))<i class="bi bi-check-circle-fill text-success ms-1" style="font-size:.8rem;"></i>@endif</a></li>
                    @endif
                    <li><a id="step5-tab" data-bs-toggle="tab" href="#step5" role="tab">{{ $isGlp ? '5.' : '4.' }} Exper. Phase @if(in_array('experimental',$phasesCompleted))<i class="bi bi-check-circle-fill text-success ms-1" style="font-size:.8rem;"></i>@endif</a></li>
                    @if($isGlp)
                    <li><a id="step6-tab" data-bs-toggle="tab" href="#step6" role="tab">6. Qual. Assurance @if(in_array('quality_assurance',$phasesCompleted))<i class="bi bi-check-circle-fill text-success ms-1" style="font-size:.8rem;"></i>@endif</a></li>
                    @endif
                    <li><a id="step7-tab" data-bs-toggle="tab" href="#step7" role="tab">{{ $isGlp ? '7.' : '5.' }} Report Phase @if(in_array('reporting',$phasesCompleted))<i class="bi bi-check-circle-fill text-success ms-1" style="font-size:.8rem;"></i>@endif</a></li>
                    <li><a id="step8-tab" data-bs-toggle="tab" href="#step8" role="tab">{{ $isGlp ? '8.' : '6.' }} Archiving @if(in_array('archiving',$phasesCompleted))<i class="bi bi-check-circle-fill text-success ms-1" style="font-size:.8rem;"></i>@endif</a></li>
                </ul>

                   <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="step1" role="tabpanel">
                        @include('study_creation_step')
                        @include('partials.phase-complete-bar', ['phase' => 'study_creation', 'phaseStatuses' => $phaseStatuses])
                    </div>
                    <div class="tab-pane fade" id="step2" role="tabpanel">
                        @include('protocol-details-step')
                        @include('partials.phase-complete-bar', ['phase' => 'protocol_details', 'phaseStatuses' => $phaseStatuses])
                    </div>
                    <div class="tab-pane fade" id="step3" role="tabpanel">
                        <h4>Protocol Development ({{ $project ? $project->project_code:"No project selected"}})</h4>
                        @include('protocol-development')
                        @include('partials.phase-complete-bar', ['phase' => 'protocol_development', 'phaseStatuses' => $phaseStatuses])
                    </div>
                    <div class="tab-pane fade" id="step4" role="tabpanel">
                        @include('planning-phase-step')
                        @include('partials.phase-complete-bar', ['phase' => 'planning', 'phaseStatuses' => $phaseStatuses])
                    </div>
                    <div class="tab-pane fade" id="step5" role="tabpanel">
                        <h4>Experimental Phase</h4>
                        @include('experimental-phase-step')
                        @include('partials.phase-complete-bar', ['phase' => 'experimental', 'phaseStatuses' => $phaseStatuses])
                    </div>
                    <div class="tab-pane fade" id="step6" role="tabpanel">
                        <h4>Quality Assurance</h4>
                        @include('partials.qa-assurance-step')
                        @if($project && $project->is_glp)
                            @php
                                $qaInspCount      = \App\Models\Pro_QaInspection::where('project_id', $project->id)->count();
                                $qaInspDoneCount  = \App\Models\Pro_QaInspection::where('project_id', $project->id)->whereNotNull('date_performed')->count();
                                $qaStatement      = \App\Models\Pro_QaStatement::where('project_id', $project->id)->first();
                                $allInspDone      = $qaInspCount > 0 && $qaInspCount === $qaInspDoneCount;
                            @endphp
                            <div class="mt-3 p-3 rounded-3 border" style="background:#f8f9ff;">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <div>
                                        <strong style="color:#6f42c1;"><i class="bi bi-file-earmark-check me-1"></i>Quality Assurance Statement</strong>
                                        <div class="text-muted small">
                                            @if($qaStatement)
                                                Statut :
                                                <span class="badge {{ $qaStatement->status === 'final' ? 'bg-success' : 'bg-warning text-dark' }}">
                                                    {{ $qaStatement->status === 'final' ? 'Finalisé' : 'Brouillon' }}
                                                </span>
                                                — {{ $qaInspDoneCount }}/{{ $qaInspCount }} inspections terminées
                                            @elseif($allInspDone)
                                                Toutes les inspections sont terminées — vous pouvez générer le QA Statement.
                                            @else
                                                {{ $qaInspDoneCount }}/{{ $qaInspCount }} inspections terminées. Complétez toutes les inspections avant de générer le QA Statement.
                                            @endif
                                        </div>
                                    </div>
                                    <a href="{{ route('printQaStatement', ['project_id' => $project->id]) }}"
                                       target="_blank"
                                       class="btn btn-sm fw-semibold {{ ($allInspDone || $qaStatement) ? '' : 'disabled' }}"
                                       style="background:#6f42c1;color:#fff;">
                                        <i class="bi bi-file-earmark-pdf me-1"></i>
                                        {{ $qaStatement ? 'Voir / Imprimer le QA Statement' : 'Générer le QA Statement' }}
                                    </a>
                                </div>
                            </div>
                        @endif
                        @include('partials.phase-complete-bar', ['phase' => 'quality_assurance', 'phaseStatuses' => $phaseStatuses])
                    </div>
                    <div class="tab-pane fade" id="step7" role="tabpanel">
                        @include('partials.report-phase-step')
                        @include('partials.phase-complete-bar', ['phase' => 'reporting', 'phaseStatuses' => $phaseStatuses])
                    </div>
                    <div class="tab-pane fade" id="step8" role="tabpanel">
                        @include('partials.archiving-phase-step')
                        @include('partials.phase-complete-bar', ['phase' => 'archiving', 'phaseStatuses' => $phaseStatuses])
                    </div>
                </div>

                @if($project && $project->archived_at)
                <script>
                (function () {
                    const LOCKED_STEPS = ['step1','step2','step3','step4','step5','step6','step7'];

                    function lockSteps() {
                        LOCKED_STEPS.forEach(function(stepId) {
                            const pane = document.getElementById(stepId);
                            if (!pane) return;

                            // Disable all form controls
                            pane.querySelectorAll('input, select, textarea').forEach(function(el) {
                                el.disabled = true;
                            });

                            // Disable all buttons
                            pane.querySelectorAll('button').forEach(function(el) {
                                el.disabled = true;
                                el.style.opacity = '0.45';
                                el.style.cursor  = 'not-allowed';
                            });

                            // Disable all anchor-buttons (Bootstrap btn links)
                            pane.querySelectorAll('a.btn').forEach(function(el) {
                                el.classList.add('disabled');
                                el.setAttribute('tabindex', '-1');
                                el.setAttribute('aria-disabled', 'true');
                                el.style.pointerEvents = 'none';
                                el.style.opacity = '0.45';
                            });

                            // Add a subtle overlay tint on the pane
                            pane.style.position = 'relative';
                        });
                    }

                    // Run immediately and also after each Bootstrap tab shown (lazy-loaded content)
                    document.addEventListener('DOMContentLoaded', lockSteps);
                    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(function(tab) {
                        tab.addEventListener('shown.bs.tab', lockSteps);
                    });
                    // Run now in case DOM is already ready
                    if (document.readyState !== 'loading') lockSteps();
                })();
                </script>
                @endif

                @else
                <div class="row">
                    <div class="col-12">
                        <p class="alert alert-info text-center mt-2">
                            <strong>Veuillez sélectionner un projet pour voir ses détails.</strong>
                        </p>
                    </div>
                </div>
                @endif

                

              


             

            </div>

            @include('partials.dialog-create-project')
            @include('partials.modal-key-personnel')
        @endsection

        @section('js')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Activer l'onglet correspondant au hash de l'URL
                const hash = window.location.hash;
                if (hash) {
                    const tabLink = document.querySelector('a[href="' + hash + '"]');
                    if (tabLink) {
                        new bootstrap.Tab(tabLink).show();
                    }
                }

                // Mettre à jour le hash à chaque changement d'onglet
                document.querySelectorAll('[data-bs-toggle="tab"]').forEach(function (tabEl) {
                    tabEl.addEventListener('shown.bs.tab', function (e) {
                        history.replaceState(null, null, e.target.getAttribute('href'));
                    });
                });
            });

            function togglePhaseComplete(phase, projectId, btn) {
                btn.disabled = true;
                fetch('{{ route("togglePhaseCompleted") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ project_id: projectId, phase: phase }),
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success) {
                        location.reload();
                    } else {
                        alertify.error(data.message || 'An error occurred.');
                        btn.disabled = false;
                    }
                })
                .catch(function() {
                    alertify.error('Network error. Please try again.');
                    btn.disabled = false;
                });
            }
        </script>
        @endsection

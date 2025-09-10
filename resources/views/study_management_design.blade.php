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

                
                <ul class="wizard" id="myTab" role="tablist">
                    <li><a class="active" id="step1-tab" data-bs-toggle="tab" href="#step1" role="tab">1. Study
                            Creation</a></li>
                    <li><a id="step2-tab" data-bs-toggle="tab" href="#step2" role="tab">2. Protocol Details</a></li>
                    <li><a id="step3-tab" data-bs-toggle="tab" href="#step3" role="tab">3. Protocol Dev.</a>
                    {{-- <li><a id="step3-tab" data-bs-toggle="tab" href="#step3" role="tab">3. Protocol Development</a> --}}
                    </li>
                    <li><a id="step4-tab" data-bs-toggle="tab" href="#step4" role="tab">4. Planning Phase</a></li>
                    <li><a id="step5-tab" data-bs-toggle="tab" href="#step5" role="tab">5. Exper. Phase</a></li>
                    {{-- <li><a id="step5-tab" data-bs-toggle="tab" href="#step5" role="tab">5. Experiment. Phase</a></li> --}}
                    <li><a id="step6-tab" data-bs-toggle="tab" href="#step6" role="tab">6. Qual. Assurance</a></li>
                    <li><a id="step7-tab" data-bs-toggle="tab" href="#step7" role="tab">7. Report Phase</a></li>
                    <li><a id="step8-tab" data-bs-toggle="tab" href="#step8" role="tab">8. Archiving Phase</a></li>
                </ul>

                   <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="step1" role="tabpanel">
                        @include('study_creation_step')
                    </div>
                    <div class="tab-pane fade" id="step2" role="tabpanel">
                        {{-- <h4>Protocol Details</h4>
                        <p>Contenu de la deuxième étape.</p> --}}

                        @include('protocol-details-step')
                    </div>
                    <div class="tab-pane fade" id="step3" role="tabpanel">
                        <h4>Protocol Development ({{ $project ? $project->project_code:"No project selected"}})</h4>
                        {{-- <p>Contenu de la troisième étape.</p> --}}
                        @include('protocol-development')
                    </div>
                    <div class="tab-pane fade" id="step4" role="tabpanel">
                        <h4>Planning Phase</h4>
                        <p>Contenu de la quatrième étape.</p>
                    </div>
                    <div class="tab-pane fade" id="step5" role="tabpanel">
                        <h4>Experimental Phase</h4>
                        <p>Contenu de la cinquième étape.</p>
                    </div>
                    <div class="tab-pane fade" id="step6" role="tabpanel">
                        <h4>Data Analysis</h4>
                        <p>Contenu de la sixième étape.</p>
                    </div>
                    <div class="tab-pane fade" id="step7" role="tabpanel">
                        <h4>Report Phase</h4>
                        <p>Contenu de la septième étape.</p>
                    </div>
                    <div class="tab-pane fade" id="step8" role="tabpanel">
                        <h4>Archiving Phase</h4>
                        <p>Contenu de la huitième étape.</p>
                    </div>
                </div>

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
        @endsection

        @section('js')
         
        @endsection

@extends('index-new')

@section('breadcrumb')
    {{-- <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Sample Page</h5>
                        <p class="m-b-0">Lorem Ipsum is simply dummy text of the printing</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb-title">
                        <li class="breadcrumb-item">
                            <a href="index-2.html"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item"><a href="#!">Pages</a>
                        </li>
                        <li class="breadcrumb-item"><a href="#!">Sample Page</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div> --}}
@endsection


@section('content')
    <h1 class="h3 fw-semibold mb-4">Tableau de bord</h1>

    {{-- KPI --}}
    <div class="row g-4 mb-5">
        {{-- KPI 1 --}}
        <div class="col-md-6 col-xl-3">
            <div class="card kpi-card bg-white p-3">
                <div class="d-flex align-items-center">
                    <div class="kpi-icon bg-primary text-white me-3"><i class="bi bi-graph-up"></i></div>
                    <div>
                        <h6 class="text-muted mb-1">Projets soumis</h6>
                        <h3 class="fw-semibold mb-0">{{ $projectsCount }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI 2 --}}
        <div class="col-md-6 col-xl-3">
            <div class="card kpi-card bg-white p-3">
                <div class="d-flex align-items-center">
                    <div class="kpi-icon bg-success text-white me-3"><i class="bi bi-people"></i></div>
                    <div>
                        <h6 class="text-muted mb-1">Utilisateurs actifs</h6>
                        <h3 class="fw-semibold mb-0">{{ $activeUsers }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI 3 --}}
        <div class="col-md-6 col-xl-3">
            <div class="card kpi-card bg-white p-3">
                <div class="d-flex align-items-center">
                    <div class="kpi-icon bg-warning text-white me-3"><i class="bi bi-cash-stack"></i></div>
                    <div>
                        <h6 class="text-muted mb-1">Budget total (€)</h6>
                        <h3 class="fw-semibold mb-0">{{ number_format($totalBudget, 2, ',', ' ') }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI 4 --}}
        <div class="col-md-6 col-xl-3">
            <div class="card kpi-card bg-white p-3">
                <div class="d-flex align-items-center">
                    <div class="kpi-icon bg-danger text-white me-3"><i class="bi bi-list-task"></i></div>
                    <div>
                        <h6 class="text-muted mb-1">Tâches en cours</h6>
                        <h3 class="fw-semibold mb-0">{{ $tasksInProgress }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>



    {{-- Graphique budgets --}}
    {{-- <div class="card shadow-sm border-0 rounded-4 p-4">
        <h5 class="mb-3">Évolution du budget par mois</h5>
        <canvas id="budgetChart" height="100"></canvas>
    </div> --}}


    <div class="row mt-3" style="background-color: #fff; padding: 10px; border-radius: 10px;">
        <div class="col-12 mt-2">
            <h1 class="mt-3">Overview of all projects</h1>
        </div>

        <div class="col-12 mt-3 text-center">
            <div class="btn-group" role="group" aria-label="Basic mixed styles example">
                <button type="button" class="btn btn-outline-danger all_projects">All Projects</button>
                <button type="button" class="btn btn-outline-warning not_started_projects">Not started
                    Projects</button>
                <button type="button" class="btn btn-outline-success in_progress_project">In progress</button>
                <button type="button" class="btn btn-outline-primary suspended_projects">Suspended</button>
                <button type="button" class="btn btn-outline-secondary completed_projects">Completed</button>
                <button type="button" class="btn btn-outline-dark archived_projects">Archived</button>
            </div>
        </div>

        <div class="col-12 ">
            <div class="row">
                @forelse ($all_projects as $project)
                    @php
                        $phase = $project->project_stage;

                        $class_phase = 'all projet ';
                        $progress_class = '';

                        if ($phase == 'in progress') {
                            $class_phase .= '  in_progress';
                            $progress_class = 'bg-success';
                        } elseif ($phase == 'not_started') {
                            $class_phase .= '  not_started';
                            $progress_class = 'bg-danger';
                        } elseif ($phase == 'suspended') {
                            $class_phase .= '  suspended';
                            $progress_class = 'bg-primary';
                        } elseif ($phase == 'completed') {
                            $class_phase .= '  completed';
                            $progress_class = 'bg-secondary';
                        } elseif ($phase == 'archived') {
                            $class_phase .= '  archived';
                            $progress_class = 'bg-dark';
                        }

                    @endphp
                    <div class="col-12 col-sm-6 col-md-4 mt-3 {{ $class_phase }}">
                        <div class="row div-project">
                            <div class="col-12">
                                <h6 class="project-title">Project : {{ $project->project_code }}</h6>
                            </div>
                            <div class="col-12">
                                <p class="project-title">Date Start :
                                    {{ $project->date_debut_effective ? date('d/m/Y', strtotime($project->date_debut_effective)) : 'Unknown' }}
                                </p>
                            </div>
                            <div class="col-12">
                                <p class="project-title">Date End :
                                    {{ $project->date_fin_effective ? date('d/m/Y', strtotime($project->date_fin_effective)) : 'Unknown' }}
                                </p>
                            </div>
                            <div class="col-12">
                                <p class="project-title">Stage : {{ $project->project_stage }}</p>
                            </div>

                            <div class="col-12 mt-2">

                                <div class="progress ">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated {{ $progress_class }}"
                                        role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"
                                        style="width: 75%"></div>
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <a href="#" class="btn btn-outline-danger">More Details</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <p class="alert alert-danger text-center mt-2">
                            <i class="fa fa-exclamation-circle">&nbsp;</i> No projects registered yet.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

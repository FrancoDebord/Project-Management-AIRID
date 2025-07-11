@extends('index')

@section('content')
    <section class="">
        <div class="container bg-white p-3">

            <div class="row">
                <div class="col-12">
                    <h3 class="title-section">Overview of all projects</h3>
                </div>

                <div class="col-12 mt-3 text-center">
                    <div class="btn-group" role="group" aria-label="Basic mixed styles example">
                        <button type="button" class="btn btn-outline-danger all_projects">All Projects</button>
                        <button type="button" class="btn btn-outline-warning not_started_projects">Not started
                            Projects</button>
                        <button type="button" class="btn btn-outline-success in_progress_project">In progress</button>
                        <button type="button" class="btn btn-outline-primary suspended_projects">Suspended</button>
                        <button type="button" class="btn btn-outline-secondary completed_projects">Completed</button>
                    </div>
                </div>

                <div class="col-12">
                    <div class="row">
                        @forelse ($all_projects as $project)
                            @php
                                $phase = $project->project_stage;

                                $class_phase = 'all projet ';
                                $progress_class = "";

                                if ($phase == 'in progress') {
                                    $class_phase .= '  in_progress';
                                    $progress_class = "bg-success";
                                } elseif ($phase == 'not_started') {
                                    $class_phase .= '  not_started';
                                    $progress_class = "bg-danger";
                                } elseif ($phase == 'suspended') {
                                    $class_phase .= '  suspended';
                                    $progress_class = "bg-primary";
                                } elseif ($phase == 'completed') {
                                    $class_phase .= '  completed';
                                    $progress_class = "bg-secondary";
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
        </div>
    </section>
@endsection

@extends('index')

@section('title', 'Liste des projets')

@section('content')
    <section>
        <div class="container">
            <div class="row">

                <div class="col-12 mt-2 mb-2">
                    <a href="{{ route("project.create") }}" class="btn btn-outline-warning btn-lg pull-right" style="float: right; font-size : 1.1em">Create a New Project</a>
                </div>
                <div class="col-12">
                    <h4 class="title-section">List of all projects</h4>
                </div>

                <div class="col-12 table-responsive">

                    <table class="table table-striped table-bordered table-condensed" id="table-projects">
                        <thead>
                            <tr>
                                <th>Project Code</th>
                                <th>Project Title</th>
                                <th>Protocol Code</th>
                                <th>Date Start </th>
                                <th>Date End </th>
                                <th>Percentage </th>
                                <th>Stage </th>
                                <th>Actions </th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($all_projects as $project)
                                @php
                                    $phase = $project->project_stage;
                                    $progress_class = '';

                                    if ($phase == 'in progress') {
                                        $progress_class = 'bg-success';
                                    } elseif ($phase == 'not_started') {
                                        $progress_class = 'bg-danger';
                                    } elseif ($phase == 'suspended') {
                                        $progress_class = 'bg-primary';
                                    } elseif ($phase == 'completed') {
                                        $progress_class = 'bg-secondary';
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $project->project_code }}</td>
                                    <td>{{ $project->project_title }}</td>
                                    <td>{{ $project->protocol_code }}</td>
                                    <td>{{ $project->date_debut_effective ? date('d/m/Y', strtotime($project->date_debut_effective)) : 'Unknown' }}
                                    </td>
                                    <td>{{ $project->date_fin_effective ? date('d/m/Y', strtotime($project->date_fin_effective)) : 'Unknown' }}
                                    </td>

                                    <td>
                                        <div class="progress ">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated {{ $progress_class }}"
                                                role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"
                                                style="width: 75%"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge p-2 {{ $progress_class }}">{{ $project->project_stage }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route("project.edit",$project->id) }}" class="btn btn-outline-primary">Modif</a>
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

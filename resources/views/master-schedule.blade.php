@extends('index-3')

@section('title', "Projects' Activities schedules")


@section('content')
    <section>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 mt-2">
                    <h3 class="title-section">Master Schedule</h3>
                </div>
            </div>


            <div class="row">

                <div class="col-12 table-responsive">

                    <table class="table table-striped table-bordered table-condensed" id="master_schedule">
                        <thead>
                            <tr>
                                <th>Study Code</th>
                                <th>Test System</th>
                                <th>Nature of Project</th>
                                <th>Study Director </th>
                                <th>Key Personnel </th>
                                <th>Project Status </th>
                                @forelse ($all_phases as $study_phase)
                                    <th colspan="2" class="{{ $study_phase->class_couleur }}">
                                        {{ $study_phase->phase_title }}</th>
                                @empty
                                @endforelse
                                {{-- <th>Remarks</th> --}}
                                <th></th>
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
                                    } elseif ($phase == 'archived') {
                                        $progress_class = 'bg-info';
                                    }

                                    $study_director = $project->studyDirector;
                                    $key_personnel = $project->keyPersonnelProject;
                                    $project_teams = [];

                                    foreach ($key_personnel as $key => $staff) {
                                        # code...

                                        $project_teams[] =
                                            $staff->titre_personnel . ' ' . $staff->prenom . ' ' . $staff->nom;
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $project->project_code }}</td>
                                    <td>{{ $project->test_system }}</td>
                                    <td>{{ $project->project_nature }}</td>
                                    <td>{{ $study_director ? $study_director->titre_personnel . ' ' . $study_director->prenom . ' ' . $study_director->nom : 'Not set' }}
                                    </td>
                                    <td>{!! implode('/ <br/>', $project_teams) !!}</td>
                                    <td>{{ $project->project_status }}</td>

                                    @forelse ($all_phases as $study_phase)
                                        <th class="{{ $study_phase->class_couleur }}">01/01</th>
                                        <th class="{{ $study_phase->class_couleur }}">01/02</th>
                                    @empty
                                    @endforelse
                                    <td></td>

                                    {{-- <td>
                                        <span class="badge p-2 {{ $progress_class }}">{{ $project->project_stage }}</span>
                                    </td> --}}

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

@extends('index-3')

@section('title', 'Create a new Project')

@section('content')
    <section>
        <div class="container">

            <div class="row">
                <div class="col-12">
                    <h4 class="title-section">Create a new Project</h4>
                </div>



                @if (session('error'))
                    <div class="col-12 mt-3">
                        <p class="alert alert-danger text-center mt-2">
                            {{ session()->get('error') }}
                        </p>
                    </div>
                @endif

                {{-- @if (session('message')) --}}
                <div class="col-12 mt-3">
                    <p class="alert alert-success text-center mt-2">
                        {{ session()->get('message') }}
                    </p>
                </div>
                {{-- @endif --}}


                <div class="col-12 mt-2">

                    <form action="{{ $project->id ? route('project.update', $project->id) : route('project.store') }}"
                        method="POST" class="">

                        @csrf
                        <input type="hidden" name="_method" value="{{ $project->id ? 'PUT' : 'POST' }}">

                        <div class="row">

                            @php
                                $project_code = old('project_code') ?? ($project->project_code ?? '');
                            @endphp
                            {{-- CODE DU PROJET --}}
                            <div class="col-12 col-sm-6 form-group-sm mt-2">
                                <label for="project_code">Code of Project</label>
                                <input type="text" name="project_code" id=""
                                    class="form-control @error('project_code') is-invalid @enderror"
                                    value="{{ $project_code }}" />

                                @error('project_code')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Titre du projet --}}
                            <div class="col-12 col-sm-6 form-group-sm mt-2">

                                @php
                                    $project_title = old('project_title') ?? ($project->project_title ?? '');
                                @endphp

                                <label for="project_title">Title of Project</label>
                                <input type="text" name="project_title" id=""
                                    class="form-control @error('project_title') is-invalid @enderror"
                                    value="{{ $project_title }}" />

                                @error('project_title')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>


                            {{-- Code du protocole du projet --}}
                            <div class="col-12 col-sm-6 form-group-sm mt-2">

                                @php
                                    $protocol_code = old('protocol_code') ?? ($project->protocol_code ?? '');
                                @endphp
                                <label for="protocol_code">Code of the Protocol</label>
                                <input type="text" name="protocol_code" id=""
                                    class="form-control @error('protocol_code') is-invalid @enderror"
                                    value="{{ $protocol_code }}" />

                                @error('protocol_code')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>


                            {{-- Study Director --}}
                            <div class="col-12 col-sm-6 form-group-sm mt-2">
                                <label for="study_director">Study Director</label>

                                @php
                                    $study_director = old('study_director') ?? ($project->study_director ?? '');

                                @endphp
                                <select name="study_director" id="study_director"
                                    class="form-control form-select selectpicker show-tick @error('study_director') is-invalid @enderror"
                                    data-live-search="true">
                                    <option value=""></option>

                                    @forelse ($all_personnels as $personnel)
                                        <option value="{{ $personnel->id }}"
                                            {{ $study_director == $personnel->id ? 'selected' : '' }}>
                                            {{ $personnel->titre_personnel . ' ' . $personnel->prenom . ' ' . $personnel->nom }}
                                        </option>
                                    @empty
                                    @endforelse
                                </select>

                                @error('study_director')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Project Manager --}}
                            <div class="col-12 col-sm-6 form-group-sm mt-2">
                                <label for="project_manager">Project Manager</label>

                                @php
                                    $project_manager = old('project_manager') ?? ($project->project_manager ?? '');

                                @endphp
                                <select name="project_manager" id="project_manager"
                                    class="form-control form-select selectpicker show-tick @error('project_manager') is-invalid @enderror"
                                    data-live-search="true">
                                    <option value=""></option>

                                    @forelse ($all_personnels as $personnel)
                                        <option value="{{ $personnel->id }}"
                                            {{ $project_manager == $personnel->id ? 'selected' : '' }}>
                                            {{ $personnel->titre_personnel . ' ' . $personnel->prenom . ' ' . $personnel->nom }}
                                        </option>
                                    @empty
                                    @endforelse
                                </select>

                                @error('project_manager')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>


                            {{-- Date prévisionnelle de démarrage --}}
                            <div class="col-12 col-sm-6 form-group-sm mt-2">

                                @php
                                    $date_debut_previsionnelle =
                                        old('date_debut_previsionnelle') ?? ($project->date_debut_previsionnelle ?? '');

                                @endphp
                                <label for="date_debut_previsionnelle">Expected Date of Start</label>
                                <input type="date" name="date_debut_previsionnelle" id=""
                                    class="form-control @error('date_debut_previsionnelle') is-invalid @enderror"
                                    value="{{ $date_debut_previsionnelle }}" />

                                @error('date_debut_previsionnelle')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Date effective de démarrage --}}
                            <div class="col-12 col-sm-6 form-group-sm mt-2">

                                @php
                                    $date_debut_effective =
                                        old('date_debut_effective') ?? ($project->date_debut_effective ?? '');

                                @endphp

                                <label for="date_debut_effective">Actual Date of Start</label>
                                <input type="date" name="date_debut_effective" id=""
                                    class="form-control @error('date_debut_effective') is-invalid @enderror"
                                    value="{{ $date_debut_effective }}" />

                                @error('date_debut_effective')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Date prévisionnelle de fin --}}
                            <div class="col-12 col-sm-6 form-group-sm mt-2">

                                @php
                                    $date_fin_previsionnelle =
                                        old('date_fin_previsionnelle') ?? ($project->date_fin_previsionnelle ?? '');

                                @endphp

                                <label for="date_fin_previsionnelle">Expected Date of End</label>
                                <input type="date" name="date_fin_previsionnelle" id=""
                                    class="form-control @error('date_fin_previsionnelle') is-invalid @enderror"
                                    value="{{ $date_fin_previsionnelle }}" />

                                @error('date_fin_previsionnelle')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>


                            {{-- Date effective de fin --}}
                            <div class="col-12 col-sm-6 form-group-sm mt-2">

                                @php
                                    $date_fin_effective =
                                        old('date_fin_effective') ?? ($project->date_fin_effective ?? '');

                                @endphp

                                <label for="date_fin_effective">Actual Date of End</label>
                                <input type="date" name="date_fin_effective" id=""
                                    class="form-control @error('date_fin_effective') is-invalid @enderror"
                                    value="{{ $date_fin_effective }}" />

                                @error('date_fin_effective')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Project Stage --}}
                            <div class="col-12 col-sm-6 form-group-sm mt-2">
                                <label for="project_stage">Project Stage</label>

                                @php
                                    $project_stage = old('project_stage') ?? ($project->project_stage ?? '');

                                @endphp
                                <select name="project_stage" id="project_stage"
                                    class="form-control form-select selectpicker show-tick @error('project_stage') is-invalid @enderror"
                                    data-live-search="true">
                                    <option value="not_started" {{ $project_stage == 'not_started' ? 'selected' : '' }}>Not
                                        started</option>
                                    <option value="in progress" {{ $project_stage == 'in progress' ? 'selected' : '' }}>In
                                        Progress</option>
                                    <option value="suspended" {{ $project_stage == 'suspended' ? 'selected' : '' }}>
                                        Suspended
                                    </option>
                                    <option value="completed" {{ $project_stage == 'completed' ? 'selected' : '' }}>
                                        Completed
                                    </option>

                                </select>

                                @error('project_stage')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-12 form-group mt-3">
                                <button type="submit" class="btn btn-inverse-primary">Create Project</button>
                                <a href="{{ route("project.create") }}" class="btn btn-inverse-success">Clear all fields</a>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

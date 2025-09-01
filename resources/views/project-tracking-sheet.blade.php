@extends('index-3')

@section('title', "Projects' Activities schedules")


@section('content')
    <section>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 mt-2">
                    <h3 class="title-section">Project Tracking Sheet</h3>
                </div>


                <div class="col-12 mt-2">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif


                    @if (session('status'))
                        @if (session('status') == 'error')
                            <p class="alert alert-danger text-center mt-2">
                                {{ session('message') }}
                            </p>
                        @else
                            <p class="alert alert-success text-center mt-2">
                                {{ session('message') }}
                            </p>
                        @endif
                    @endif

                </div>
            </div>


            <div class="row">
                <form action="{{ route('saveProjectTrackingSheet') }}" method="POST" enctype="multipart/form-data"
                    class="col-12">
                    @csrf

                    <div class="row m-3 p-2" style="border: 1px dashed #c20102">
                        <div class="col-12 col-sm-6  form-group mt-2">
                            <label for="project_id">
                                <strong>Select a Project / Study</strong>
                            </label>

                            @php
                                $project_id = old('project_id') ?? '';
                            @endphp
                            <select name="project_id" id="project_id"
                                class="form-control selectpicker show-tick @error('project_id') is-invalid @enderror"
                                data-live-search="true">
                                <option value="">Select</option>

                                @forelse ($all_projects as $project)
                                    <option value="{{ $project->id }}" {{ $project_id == $project->id ? 'selected' : '' }}>
                                        {{ $project->project_code }}</option>
                                @empty
                                @endforelse
                            </select>

                            @error('project_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror


                        </div>
                        <div class="col-12 col-sm-6  form-group mt-2">
                            <label for="study_phase_id">
                                <strong>Select a study Phase</strong>
                            </label>

                            @php
                                $study_phase_id = old('study_phase_id') ?? '';
                            @endphp
                            <select name="study_phase_id" id="study_phase_id"
                                class="form-control selectpicker show-tick @error('study_phase_id') is-invalid @enderror"
                                data-live-search="true">
                                <option value="">Select</option>

                                @forelse ($all_phases as $study_phase)
                                    <option value="{{ $study_phase->id }}"
                                        {{ $study_phase_id == $study_phase->id ? 'selected' : '' }}>
                                        {{ $study_phase->phase_title }}</option>
                                @empty
                                @endforelse
                            </select>

                            @error('study_phase_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                        </div>

                        <div class="col-12 col-sm-6  form-group mt-2">
                            <label for="date_start">
                                <strong>Start Date</strong> <br>

                                <span id="start_date_evidence" class="text-danger"></span>
                            </label>

                            @php
                                $date_start = old('date_start') ?? '';
                                $date_start = $date_start ? date('Y-m-d', strtotime($date_start)) : '';
                                // If you want to use a datepicker, you can format it accordingly
                            @endphp
                            <input type="text" name="date_start" id="date_start"
                                class="form-control datepicker mt-1 @error('date_start') is-invalid @enderror"
                                value="{{ $date_start }}" />

                            @error('date_start')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                        </div>

                        <div class="col-12 col-sm-6  form-group mt-2">
                            <label for="date_end">
                                <strong>End Date</strong> <br>

                                <span id="end_date_evidence" class="text-danger"></span>
                            </label>

                            @php
                                $date_end = old('date_end') ?? '';
                                $date_end = $date_end ? date('Y-m-d', strtotime($date_end)) : '';
                                // If you want to use a datepicker, you can format it accordingly
                            @endphp
                            <input type="text" name="date_end" id="date_end"
                                class="form-control mt-1 datepicker @error('date_end') is-invalid @enderror"
                                value="{{ $date_end }}" />

                            @error('date_end')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="col-12 col-sm-6  form-group mt-2">
                            <label for="file_evidence1">
                                <strong>Evidence 1 (Upload file)</strong> <br>

                            </label>

                            @php
                                $file_evidence1 = old('file_evidence1') ?? '';
                                // If you want to use a datepicker, you can format it accordingly
                            @endphp
                            <input type="file" name="file_evidence1" id="file_evidence1"
                                class="form-control datepicker fileclass @error('file_evidence1') is-invalid @enderror"
                                accept=".pdf" />

                            @error('file_evidence1')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                            <br>
                            <span class="page-block text-info text-center mt-2"
                                id="span_evidence1_file">{{ $file_evidence1 }}</span>
                        </div>

                        <div class="col-12 col-sm-6  form-group mt-2">
                            <label for="file_evidence2">
                                <strong>Evidence 2 (Upload file)</strong> <br>

                            </label>

                            @php
                                $file_evidence2 = old('file_evidence2') ?? '';
                                // If you want to use a datepicker, you can format it accordingly
                            @endphp
                            <input type="file" name="file_evidence2" id="file_evidence2"
                                class="form-control datepicker fileclass @error('file_evidence2') is-invalid @enderror"
                                accept=".pdf" />

                            @error('file_evidence2')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                            <br>
                            <span class="page-block text-info text-center"
                                id="span_evidence2_file">{{ $file_evidence2 }}</span>
                        </div>


                        <div class="col-12  " style="margin-top: 35px;">
                            <button type="submit" class="btn  btn-outline-danger">
                                <i class="fa fa-check-circle">&nbsp;</i>
                                Submit
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </section>
@endsection

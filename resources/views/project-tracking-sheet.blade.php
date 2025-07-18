@extends('index-3')

@section('title', "Projects' Activities schedules")


@section('content')
    <section>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 mt-2">
                    <h3 class="title-section">Project Tracking Sheet</h3>
                </div>
            </div>


            <div class="row">
                <form action="#" class="col-12">
                    @csrf

                    <div class="row m-3 p-2" style="border: 1px dashed #c20102">
                        <div class="col-12 col-sm-6  form-group mt-2">
                            <label for="project_id">
                                <strong>Select a Project / Study</strong>
                            </label>

                            <select name="project_id" id="project_id"
                                class="form-control selectpicker show-tick @error('project_id') is-invalid @enderror"
                                data-live-search="true">
                                <option value="">Select</option>

                                @forelse ($all_projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->project_code }}</option>
                                @empty
                                @endforelse
                            </select>


                        </div>
                        <div class="col-12 col-sm-6  form-group mt-2">
                            <label for="study_phase_id">
                                <strong>Select a study Phase</strong>
                            </label>

                            <select name="study_phase_id" id="study_phase_id"
                                class="form-control selectpicker show-tick @error('study_phase_id') is-invalid @enderror"
                                data-live-search="true">
                                <option value="">Select</option>

                                @forelse ($all_phases as $study_phase)
                                    <option value="{{ $study_phase->id }}">{{ $study_phase->phase_title }}</option>
                                @empty
                                @endforelse
                            </select>

                        </div>

                        <div class="col-12 col-sm-6  form-group mt-2">
                            <label for="start_date">
                                <strong>Start Date</strong> <br>

                                <span id="start_date_evidence"></span>
                            </label>

                            <input type="date" name="start_date" id="start_date"
                                class="form-control datepicker @error('start_date') is-invalid @enderror" />

                        </div>

                        <div class="col-12 col-sm-6  form-group mt-2">
                            <label for="end_date">
                                <strong>End Date</strong> <br>

                                <span id="end_date_evidence"></span>
                            </label>

                            <input type="date" name="end_date" id="end_date"
                                class="form-control datepicker @error('end_date') is-invalid @enderror" />

                        </div>

                        <div class="col-12 col-sm-6  form-group mt-2">
                            <label for="file_evidence1">
                                <strong>Evidence 1 (Upload file)</strong> <br>

                            </label>

                            <input type="file" name="file_evidence1" id="file_evidence1"
                                class="form-control datepicker fileclass @error('file_evidence1') is-invalid @enderror" />

                        </div>

                        <div class="col-12 col-sm-6  form-group mt-2">
                            <label for="file_evidence2">
                                <strong>Evidence 2 (Upload file)</strong> <br>

                            </label>

                            <input type="file" name="file_evidence2" id="file_evidence2"
                                class="form-control datepicker fileclass @error('file_evidence2') is-invalid @enderror" />

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

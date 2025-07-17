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
                        <div class="col-12 col-sm-8 form-group mt-2">
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
                        <div class="col-12 col-sm-4 " style="margin-top: 35px;">
                            <button type="submit" class="btn  btn-outline-danger">
                                <i class="fa fa-check-circle">&nbsp;</i>
                                Submit
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="container-fluid mt-2">
            <div class="row">
                <form action="" class="col-12 p-2">
                    @csrf

                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table class=" " style="border: 1px solid  #000; " id="table-project-tracking-sheet">
                                <thead>
                                    <tr>
                                        <th>Phase</th>
                                        <th>Description</th>
                                        <th>Periods</th>
                                        <th>Dates</th>
                                        <th>Evidence</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($all_phases as $study_phase)
                                        <tr>
                                            <td rowspan="2">{{ $study_phase->phase_title }}</td>
                                            <td rowspan="2">{{ $study_phase->description }}</td>

                                            <td>Start Date</td>
                                            <td>
                                                <input type="date" name="date_start[]" id="date_start"
                                                class="form-control datepicker" />
                                            </td>
                                            <td>{{ $study_phase->evidence1 }}</td>
                                            
                                            <td>End Date</td>
                                            <td>
                                                <input type="date" name="date_end[]" id="date_end"
                                                    class="form-control datepicker" />
                                            </td>

                                            <td>{{ $study_phase->evidence2 }}</td>
                                        </tr>

                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

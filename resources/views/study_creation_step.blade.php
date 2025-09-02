<style>
    <style>.btn-custom {
        font-weight: 600;
        padding: 12px 22px;
        border-radius: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
    }

    /* Bouton principal */
    .btn-primary-custom {
        background-color: #c20102;
        color: #fff;
        border: none;
    }

    .btn-primary-custom:hover {
        background-color: #a10001;
        transform: translateY(-2px);
    }

    /* Variante plus claire */
    .btn-secondary-custom {
        background-color: #e45c5d;
        color: #fff;
        border: none;
    }

    .btn-secondary-custom:hover {
        background-color: #c94a4b;
        transform: translateY(-2px);
    }

    /* Variante encore plus claire */
    .btn-tertiary-custom {
        background-color: #f28b8c;
        color: #fff;
        border: none;
    }

    .btn-tertiary-custom:hover {
        background-color: #d67374;
        transform: translateY(-2px);
    }

    /* Variante très claire */
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
</style>
</style>

<div class="row">
    <div class="col-md-12">
        <h4>Study Creation</h4>
        <p>In this section, you are asked to provide basic information about the study along with basic documents such
            as the Study Director Appointment Form..</p>
    </div>

    <div class="col-12 col-sm-7 ">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-column gap-3 w-50 mx-auto">
                    <button class="btn btn-custom btn-primary-custom" id="project_basic_information">Update Project Basic
                        Information</button>

                    <button class="btn btn-custom btn-secondary-custom" data-bs-toggle="modal" data-bs-target="#customModal"> Study Director Appointment
                        Form</button>

                    <button class="btn btn-custom btn-tertiary-custom">Study Director Replacement Form</button>

                    <button class="btn btn-custom btn-light-custom">Upload other basic documents </button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-5">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Progression</h5>

                @php
                $totalSteps = 2;

                $completedSteps = 0;
                if ($total_filled_percentage_projects == 100) {
                    $completedSteps = 1;

                }
                if ($total_filled_percentage_study_director_appointment == 100) {
                    $completedSteps = 2;
                }

                $pourcentage = round(($total_filled_percentage_projects + $total_filled_percentage_study_director_appointment) / $totalSteps, 2);

              $nextStep = "";
                if ($completedSteps == 0) {
                    $nextStep = "Update Project Basic Information";
                } elseif ($completedSteps == 1) {
                    $nextStep = "Study Director Appointment Form";
                } else {
                    $nextStep = "All steps completed";
                }

                $progressColor = "";
                if ($pourcentage <= 20) {
                    $progressColor = 'bg-danger';
                } elseif ($pourcentage <= 40) {
                    $progressColor = 'bg-warning';
                } elseif ($pourcentage <= 60) {
                    $progressColor = 'bg-info';
                } elseif ($pourcentage <= 80) {
                    $progressColor = 'bg-primary';
                } else {
                    $progressColor = 'bg-success';
                }

                @endphp
                <div class="progress" style="height: 25px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated {{ $progressColor }}" role="progressbar"
                        style="width: {{ $pourcentage }}%;" aria-valuenow="{{ $pourcentage }}" aria-valuemin="0" aria-valuemax="100">{{ $pourcentage }}%</div>
                </div>

                <p class="mt-3 mb-0">You have completed {{ $completedSteps }} out of {{ $totalSteps }} steps.</p>
                <p class="mb-0">Next step: <strong class="text-primary">{{ $nextStep??"" }}</strong></p>
                <p class="mb-0">Please ensure that all information provided is accurate and up-to-date. </p>
                     {{-- The Study Director Appointment Form is a mandatory document that must be submitted before proceeding to the next steps of the study creation process.</p> --}}
                <p class="mb-0">Please contact the support team at <a href="mailto:support@example.com">support@example.com</a>.</p>    

            </div>
        </div>
    </div>


</div>


@include('partials.dialog_detailed_information_project')
@include('partials.study_director_appointment_form')

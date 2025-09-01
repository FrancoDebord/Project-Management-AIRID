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

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-column gap-3 w-50 mx-auto">
                    <button class="btn btn-custom btn-primary-custom" id="project_basic_information">Update Project Basic Information</button>
                    <button class="btn btn-custom btn-secondary-custom">Complete the Study Director Appointment
                        Form</button>
                    <button class="btn btn-custom btn-tertiary-custom">Fill A Study Director Replacement Form</button>
                    <button class="btn btn-custom btn-light-custom">Upload other basic documents on the project</button>
                </div>
            </div>
        </div>
    </div>
</div>


@include('partials.dialog_detailed_information_project')

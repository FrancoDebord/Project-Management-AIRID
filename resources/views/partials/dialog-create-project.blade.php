<div class="modal fade" id="ModalformCreateNewProject" tabindex="-1" aria-labelledby="ModalformCreateNewProjectLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-3 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="ModalformCreateNewProjectLabel">Create a New Project</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Fermer"></button>
            </div>
            <div class="modal-body">

                <div id="error-messages"></div>

                <form method="POST" action="{{ route('storeProject') }}" id="formCreateNewProject">
                    {{-- CSRF Token --}}
                    @csrf

                    <div class="row">
                        {{-- Code projet --}}
                        <div class="mb-3 col">
                            <label for="code" class="form-label">Project Code <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="code" name="code"
                                class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}">
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Project Title --}}
                        <div class="mb-3 col">
                            <label for="title" class="form-label">Project Title <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="title" name="title"
                                class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    @php
                        $all_study_types = App\Models\Pro_StudyType::orderBy('level_type')->get();
                        $all_product_types = App\Models\Pro_ProductType::orderBy('level_product')->get();
                        $all_lab_tests = App\Models\Pro_LabTest::orderBy('level_test')->get();
                    @endphp


                    <div class="row">
                        <div class="col mb-3">
                            <label for="study_type_id" class="form-label">Select all appliable Study Types <span
                                    class="text-danger">*</span> </label>

                            <select name="study_type_id[]" id="study_type_id_form_create_project" class=" form-control selectpicker "
                                multiple data-live-search="true">

                                @forelse ($all_study_types??[] as $study_type)
                                    <option value="{{ $study_type->id }}">{{ $study_type->study_type_name }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>

                        <div class="col mb-3">
                            <label for="product_type_id" class="form-label">Specify Evaluation Product <span
                                    class="text-danger">*</span> </label>

                            <select name="product_type_id[]" id="product_type_id" class=" form-control selectpicker "
                                multiple data-live-search="true">

                                @forelse ($all_product_types??[] as $product_type)
                                    <option value="{{ $product_type->id }}">{{ $product_type->product_type_name }}
                                    </option>
                                @empty
                                @endforelse
                            </select>
                        </div>


                    </div>

                    <div class="row">

                        <div class="col mb-3">
                            <label for="lab_test_id" class="form-label">All Lab Test appliable <span
                                    class="text-danger">*</span> </label>

                            <select name="lab_test_id[]" id="lab_test_id" class=" form-control selectpicker " multiple
                                data-live-search="true">

                                @forelse ($all_lab_tests??[] as $lab_test)
                                    <option value="{{ $lab_test->id }}">{{ $lab_test->lab_test_name }}
                                    </option>
                                @empty
                                @endforelse
                            </select>
                        </div>

                        {{-- GLP ? (Select au lieu de checkbox) --}}
                        <div class="mb-3 col">
                            <label for="is_glp" class="form-label">Is it a GLP Project ?</label>
                            <select id="is_glp" name="is_glp"
                                class="form-control selectpicker show-tick @error('is_glp') is-invalid @enderror">
                                <option value="0" {{ old('is_glp') == '0' ? 'selected' : '' }}>No</option>
                                <option value="1" {{ old('is_glp') == '1' ? 'selected' : '' }}>Yes</option>
                            </select>
                            @error('is_glp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>




                    {{-- Buttons --}}
                    <div class="d-grid">
                        <button type="submit" class="btn btn-danger">Save the project</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

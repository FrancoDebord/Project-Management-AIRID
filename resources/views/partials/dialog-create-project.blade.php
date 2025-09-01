<div class="modal fade" id="ModalformCreateNewProject" tabindex="-1" aria-labelledby="ModalformCreateNewProjectLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
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

                    {{-- Code projet --}}
                    <div class="mb-3">
                        <label for="code" class="form-label">Project Code</label>
                        <input type="text" id="code" name="code"
                            class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}"
                            >
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Project Title --}}
                    <div class="mb-3">
                        <label for="title" class="form-label">Project Title</label>
                        <input type="text" id="title" name="title"
                            class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}"
                            >
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- GLP ? (Select au lieu de checkbox) --}}
                    <div class="mb-3">
                        <label for="is_glp" class="form-label">Is it a GLP Project ?</label>
                        <select id="is_glp" name="is_glp" class="form-select @error('is_glp') is-invalid @enderror"
                            >
                            <option value="0" {{ old('is_glp') == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('is_glp') == '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                        @error('is_glp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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

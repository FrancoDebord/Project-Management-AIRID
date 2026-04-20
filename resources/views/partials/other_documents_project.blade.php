@php
    $project_id = request()->get('project_id');
    $project    = $project_id ? \App\Models\Pro_Project::find($project_id) : null;
@endphp

<div class="modal fade" id="otherBasicDocumentsModal" tabindex="-1" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 overflow-hidden">

            <div class="modal-header" style="background:linear-gradient(135deg,#1565c0,#1976d2);color:#fff;">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-file-earmark-arrow-up me-2"></i>Upload Basic Document
                    @if($project) — <span class="fw-normal opacity-75">{{ $project->project_code }}</span>@endif
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body px-4 py-4">
                <div id="error-messages-other-basic-documents" class="mb-3"></div>

                <form action="{{ route('saveOtherBasicDocuments') }}" method="POST"
                      enctype="multipart/form-data" id="form_other_basic_documents">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project_id }}">

                    <div class="row g-3">

                        {{-- Title --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Document Title <span class="text-danger">*</span></label>
                            <input type="text" id="titre_document" name="titre_document"
                                   class="form-control" placeholder="Enter document title…" maxlength="255">
                        </div>

                        {{-- Description --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Description</label>
                            <textarea id="description_document" name="description_document"
                                      class="form-control" rows="3"
                                      placeholder="Optional description or notes…"></textarea>
                        </div>

                        {{-- File --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold small">File <span class="text-danger">*</span></label>
                            <input type="file" id="document_file" name="document_file"
                                   class="form-control"
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.zip">
                            <div class="form-text">PDF, Word, Excel, PPT, Image, ZIP — max 20 MB</div>
                        </div>

                        <div class="col-12 text-end mt-2">
                            <button type="submit" class="btn fw-semibold px-4"
                                    style="background:linear-gradient(135deg,#1565c0,#1976d2);color:#fff;">
                                <i class="bi bi-cloud-upload me-1"></i>Upload Document
                            </button>
                        </div>

                    </div>
                </form>
            </div>

            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

{{-- Submit handled by javascript_ajax.js (#form_other_basic_documents jQuery handler) --}}

 <div class="modal fade" id="otherBasicDocumentsModal" tabindex="-1" aria-labelledby="otherBasicDocumentsModalLabel"
     aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
     <div class="modal-dialog modal-dialog-centered modal-lg">
         <div class="modal-content">

             @php
                 $project_id = request()->get('project_id');
                 $project = null;
                 if ($project_id) {
                     $project = \App\Models\Pro_Project::find($project_id);
                 }

             @endphp
             <!-- Header -->
             <div class="modal-header" style="background-color: #c20102; color: white;">
                 <h5 class="modal-title" id="otherBasicDocumentsModalLabel">
                     <i class="bi bi-info-circle-fill"></i> Submit Other Basic Documents for :
                     {{ $project ? $project->project_code : '' }}
                 </h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
             </div>

             <!-- Body -->
             <div class="modal-body">

                 {{-- Formulaire --}}

                 <div id="error-messages-other-basic-documents" class="mt-2"></div>

                 <form action="{{ route('saveOtherBasicDocuments') }}" method="POST" enctype="multipart/form-data"
                     id="form_other_basic_documents">
                     {{-- CSRF Token --}}
                     {{ csrf_field() }}

                     <input type="hidden" name="project_id" id="project_id" value="{{ $project_id }}">
                     <div class="row">


                         <div class="col-12 form-group-sm mt-2">
                             <label for="titre_document" class="form-label">Title of Document</label>
                             <input type="text" id="titre_document" name="titre_document" class="form-control  "
                                 value="">
                         </div>
                         <div class="col-12 form-group-sm mt-2">
                             <label for="description_document" class="form-label">Description of Document</label>
                             <textarea id="description_document" name="description_document" class="form-control" rows="3"></textarea>
                         </div>


                         <div class="col-12  form-group-sm mt-2">
                             <label for="document_file" class="form-label">Upload Document (PDF only)</label>
                             <input type="file" id="document_file" name="document_file" class="form-control fileclass"
                                 accept="application/pdf">

                         </div>




                         <div class="col-12 form-group-sm mt-4 text-end">
                             <button type="submit" class="btn btn-outline-danger "
                                 style="float: right;">Submit</button>
                         </div>

                     </div>
                 </form>
             </div>

             <!-- Footer -->
             <div class="modal-footer">
                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>

             </div>

         </div>
     </div>
 </div>

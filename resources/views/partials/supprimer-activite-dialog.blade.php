 <!-- Modal -->
 <div class="modal fade" id="modalDeleteActivity" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered modal-lg">
         <div class="modal-content">

             @php
                 $project_id = request()->get('project_id');
                 $project = \App\Models\Pro_Project::find($project_id);
                 $all_study_types = \App\Models\Pro_StudyType::all();
             @endphp
             <!-- En-tête -->
             <div class="modal-header">
                 <h5 class="modal-title">Delete an activity on a project</h5>
                 <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                     aria-label="Close"></button>
             </div>

             <!-- Corps -->
             <div class="modal-body">

                 <div class="row mt-2 mb-3">
                     <div class="col-12" id="div-messages-error"></div>

                 </div>

                 <form id="supprimerActivite" action="{{ route('supprimerActivite') }}" method="POST">
                     @csrf

                     <input type="hidden" name="activity_id" id="activity_id_delete" value="">

                     <div class="row">

                         <div class="mb-3 ">
                             <label for="delete_cascade" class="form-label">Delete also all linked activities ?</label>
                             <select name="delete_cascade" class="form-control" id="delete_cascade">
                                 <option value="2">No</option>
                                 <option value="1">Yes</option>
                             </select>
                         </div>

                         <div class="col-12 text-center">

                             <div class="mb-3  ">
                                 <h5 class="mt-2">List of linked activities</h5>

                                 <p class="alert alert-info" id="list_all_children_activity"></p>
                             </div>
                         </div>

                     </div>

                     <div class="row mt-2">
                         <div class="col text-end">
                             <button type="submit" class="btn btn-main btn-danger">
                                 <i class="fa fa-trash-alt">&nbsp;</i>
                                 Proceed with Delete
                             </button>
                         </div>
                     </div>

                 </form>
             </div>

             <!-- Pied -->
             <div class="modal-footer">
                 <button type="button" class="btn btn-outline-main" data-bs-dismiss="modal">Cancel</button>
             </div>
         </div>
     </div>
 </div>

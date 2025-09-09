 <!-- Modal -->
 <div class="modal fade" id="ModalAddActivity" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered modal-lg">
         <div class="modal-content">

             @php
                 $project_id = request()->get('project_id');
                 $project = \App\Models\Pro_Project::find($project_id);
                 $all_study_types = \App\Models\Pro_StudyType::all();
             @endphp
             <!-- En-tête -->
             <div class="modal-header">
                 <h5 class="modal-title">Add Activity on Project</h5>
                 <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                     aria-label="Close"></button>
             </div>

             <!-- Corps -->
             <div class="modal-body">

                 <div class="row mt-2 mb-3">
                     <div class="col-12" id="div-messages-error"></div>

                </div>

                 <form id="formAddActivity" action="{{ route("saveActivityProject") }}" method="POST">
                     @csrf

                     <input type="hidden" name="project_id" id="project_id" value="{{ $project_id }}">
                     <input type="hidden" name="study_type_id" id="study_type_id" value="">
                     <input type="hidden" name="id" id="activity_id" value="">

                     <div class="row">
                         <div class="mb-3 col">
                             <label for="study_sub_category_id" class="form-label">Study Type</label>
                             <select name="study_sub_category_id" id="study_sub_category_id" class="form-select selectpicker   "
                                 required>
                                 <option value="">Select a Study Type...</option>
                             </select>
                         </div>


                         <div class="mb-3 col">
                             <label for="parent_activity_id" class="form-label">Parent Activity</label>
                             <select name="parent_activity_id" id="parent_activity_id" class="form-select">
                                 <option value="">Select a Parent Activity...</option>
                             </select>
                         </div>

                     </div>
                     <div class="mb-3 ">
                         <label for="study_activity_name" class="form-label">Activity Name</label>
                         <input type="text" class="form-control" id="study_activity_name" name="study_activity_name"
                             required>
                     </div>

                     <div class="mb-3">
                         <label for="activity_description" class="form-label">Description</label>
                         <textarea class="form-control" id="activity_description" name="activity_description" rows="3" ></textarea>
                     </div>

                     @php
                         $all_personnels = \App\Models\Pro_Personnel::orderBy('prenom', 'asc')->get();
                     @endphp

                     <div class="row">
                         <div class="col">
                             <label for="estimated_activity_date" class="form-label">Activity Due Date</label>
                             <input type="text" class="form-control" id="estimated_activity_date"
                                 name="estimated_activity_date" required>
                         </div>
                         <div class="col">
                             <label for="should_be_performed_by" class="form-label">Responsible Person for this activity
                             </label>
                             <select name="should_be_performed_by" id="should_be_performed_by"
                                 class="form-select selectpicker   " required>
                                 <option value="">Select a Responsible Person...</option>
                                 @foreach ($all_personnels as $personnel)
                                     <option value="{{ $personnel->id }}">{{ $personnel->prenom }} {{ $personnel->nom }}
                                     </option>
                                 @endforeach
                             </select>
                         </div>
                     </div>

                     <div class="row mt-2">
                            <div class="col text-end">
                                <button type="submit" class="btn btn-main btn-outline-danger">Save Activity</button>
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

 <div class="modal fade" id="replacementModal" tabindex="-1" aria-labelledby="replacementModalLabel" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">
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
                 <h5 class="modal-title" id="replacementModalLabel">
                     <i class="bi bi-info-circle-fill"></i> Study Director Replacement Form :
                     {{ $project ? $project->project_code : '' }}
                 </h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
             </div>

             <!-- Body -->
             <div class="modal-body">

                 {{-- Formulaire --}}

                 <div id="error-messages-study-director-replacement" class="mt-2"></div>

                 <form action="{{ route('saveStudyDirectorReplacementForm') }}" method="POST"
                     enctype="multipart/form-data" id="form_study_director_replacement">
                     {{-- CSRF Token --}}
                     {{ csrf_field() }}

                     <input type="hidden" name="project_id" id="project_id" value="{{ $project_id }}">
                     <div class="row">
                         <div class="col-12 col-sm-6 form-group-sm mt-2">
                             <label for="study_director" class="form-label">New Study Director Appointed</label>
                             <select id="study_director" name="study_director"
                                 class=" form-control selectpicker show-tick" data-allow-live-search="true">

                                 <option value="">Select Study Director</option>
                                 @foreach ($all_personnels as $personnel)
                                     <option value="{{ $personnel->id }}">
                                         {{ $personnel->titre }} {{ $personnel->prenom }} {{ $personnel->nom }}
                                     </option>
                                 @endforeach
                             </select>
                         </div>

                         <div class="col-12 col-sm-6 form-group-sm mt-2">
                             <label for="project_manager" class="form-label">Project Manager</label>
                             <select id="project_manager" name="project_manager"
                                 class=" form-control selectpicker show-tick" data-allow-live-search="true">

                                 <option value="">Select Project Manager</option>
                                 @foreach ($all_personnels as $personnel)
                                     <option value="{{ $personnel->id }}"
                                         {{ isset($project) && $project->project_manager == $personnel->id ? 'selected' : '' }}>
                                         {{ $personnel->titre }} {{ $personnel->prenom }} {{ $personnel->nom }}
                                     </option>
                                 @endforeach
                             </select>
                         </div>

                         <div class="col-12 col-sm-6 form-group-sm mt-2">
                             <label for="replacement_date" class="form-label">Date of Replacement</label>
                             <input type="text" id="replacement_date" name="replacement_date"
                                 class="form-control date " value="">
                         </div>


                         <div class="col-12 col-sm-6 form-group-sm mt-2">
                             <label for="sd_appointment_file" class="form-label">Upload the signed Study Director
                                 Replacement Form (PDF only)</label>
                             <input type="file" id="sd_appointment_file" name="sd_appointment_file"
                                 class="form-control fileclass" accept="application/pdf">

                         </div>


                         <div class="col-12 form-group-sm mt-2">
                             <label for="replacement_reason" class="form-label">Replacement Reason (if any)</label>
                             <textarea id="replacement_reason" name="replacement_reason" class="form-control" rows="3"></textarea>
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

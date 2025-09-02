 <div class="modal fade" id="customModal" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">
     <div class="modal-dialog modal-dialog-centered modal-lg">
         <div class="modal-content">

             @php
                 $project_id = request()->get('project_id');
                 $project = null;
                 $study_director_appointment = null;
                 if ($project_id) {
                     $project = \App\Models\Pro_Project::find($project_id);

                     if($project) {
                         $study_director_appointment = $project->studyDirectorAppointmentForm;
                     }
                 }

             @endphp
             <!-- Header -->
             <div class="modal-header" style="background-color: #c20102; color: white;">
                 <h5 class="modal-title" id="customModalLabel">
                     <i class="bi bi-info-circle-fill"></i> Study Director Appointment Form : {{ $project ? $project->project_code : '' }} 
                 </h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
             </div>

             <!-- Body -->
             <div class="modal-body">

                 {{-- Formulaire --}}

                    <div id="error-messages-study-director-appointment" class="mt-2"></div>

                 <form action="{{ route("saveStudyDirectorAppointmentForm") }}" method="POST" enctype="multipart/form-data" id="form_study_director_appointment">
                     {{-- CSRF Token --}}
                     {{ csrf_field() }}

                     <input type="hidden" name="project_id" id="project_id" value="{{ $project_id }}">
                     <div class="row">
                         <div class="col-12 col-sm-6 form-group-sm mt-2">
                             <label for="study_director" class="form-label">Study Director</label>
                             <select id="study_director" name="study_director"
                                 class="form-select form-control selectpicker show-tick" data-live-search="true">

                                 @foreach ($all_personnels as $personnel)
                                     <option value="{{ $personnel->id }}"
                                         {{ isset($project) && $project->study_director == $personnel->id ? 'selected' : '' }}>
                                         {{ $personnel->titre }} {{ $personnel->prenom }} {{ $personnel->nom }}
                                     </option>
                                 @endforeach
                             </select>
                         </div>

                         <div class="col-12 col-sm-6 form-group-sm mt-2">
                             <label for="project_manager" class="form-label">Project Manager</label>
                             <select id="project_manager" name="project_manager"
                                 class="form-select form-control selectpicker show-tick" data-live-search="true">

                                 @foreach ($all_personnels as $personnel)
                                     <option value="{{ $personnel->id }}"
                                         {{ isset($project) && $project->project_manager == $personnel->id ? 'selected' : '' }}>
                                         {{ $personnel->titre }} {{ $personnel->prenom }} {{ $personnel->nom }}
                                     </option>
                                 @endforeach
                             </select>
                         </div>

                         <div class="col-12 col-sm-4 form-group-sm mt-2">
                             <label for="sd_appointment_date" class="form-label">Date of Appointment</label>
                             <input type="text" id="sd_appointment_date" name="sd_appointment_date" class="form-control date "
                                 value="{{ isset($study_director_appointment) ? $study_director_appointment->sd_appointment_date : '' }}">
                         </div>

                         <div class="col-12 col-sm-4 form-group-sm mt-2">
                             <label for="estimated_start_date" class="form-label">Estimated start Date</label>
                             <input type="text" id="estimated_start_date" name="estimated_start_date"
                                 class="form-control date "
                                 value="{{ isset($study_director_appointment) ? $study_director_appointment->estimated_start_date : '' }}">
                         </div>

                         <div class="col-12 col-sm-4 form-group-sm mt-2">
                             <label for="estimated_end_date" class="form-label">Estimated end Date</label>
                             <input type="text" id="estimated_end_date" name="estimated_end_date"
                                 class="form-control date"
                                 value="{{ isset($study_director_appointment) ? $study_director_appointment->estimated_end_date : '' }}">
                         </div>

                         <div class="col-12 form-group-sm mt-2">
                             <label for="sd_appointment_file" class="form-label">Upload the signed Study Director
                                 Appointment Form (PDF only)</label>
                             <input type="file" id="sd_appointment_file" name="sd_appointment_file"
                                 class="form-control fileclass" accept="application/pdf">
                             @if (isset($study_director_appointment) && $study_director_appointment->sd_appointment_file)
                                 <a href="{{ asset('storage/' . $study_director_appointment->sd_appointment_file) }}" target="_blank"
                                     class="mt-2 d-block">View Current Study Director Appointment Form</a>
                             @endif
                         </div>


                         <div class="col-12 form-group-sm mt-2">
                                <label for="comments" class="form-label">Comments (if any)</label>
                                <textarea id="comments" name="comments" class="form-control" rows="3">{{ isset($study_director_appointment) ? $study_director_appointment->comments : '' }}</textarea>
                         </div>

                         <div class="col-12 form-group-sm mt-4 text-end">
                            <button type="submit" class="btn btn-outline-danger " style="float: right;">Submit</button>
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

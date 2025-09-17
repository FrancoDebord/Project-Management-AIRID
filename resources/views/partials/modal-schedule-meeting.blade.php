 <!-- Modal pour créer un meeting -->
 <div class="modal fade" id="meetingModal" tabindex="-1" aria-labelledby="meetingModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-lg">
         <div class="modal-content ">
             <div class="modal-header bg-primary text-white">
                 <h5 class="modal-title" id="meetingModalLabel">Schedule Meeting</h5>
                 <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                     aria-label="Close"></button>
             </div>

             @php
                 $project_id = request('project_id');

                 $project = App\Models\Pro_Project::find($project_id);
             @endphp
             <div class="modal-body">

                 <div id="div-messages-error7"></div>

                 <form id="meetingForm" action="{{ route('scheduleStudyInitiationMeeting') }}">

                     @csrf
                     <input type="hidden" name="project_id" id="project_id_qa_meeting" value="{{ $project_id }}">
                     <input type="hidden" name="meeting_id" id="meeting_id" value="">
                     <input type="hidden" name="meeting_type" value="study_initiation_meeting">
                     <div class="row">
                         <div class="mb-3 col">
                             <label for="meeting_date" class="form-label">Date</label>
                             <input type="text" class="form-control" id="meeting_date" name="meeting_date" required>
                         </div>
                         <div class="mb-3 col">
                             <label for="meeting_time" class="form-label">Heure</label>
                             <input type="time" class="form-control" name="meeting_time" id="meeting_time" required>
                         </div>
                         <div class="mb-3 col">
                             <label for="participants" class="form-label">Participants</label>
                             <select id="participants" name="participants[]"
                                 class=" form-control selectpicker show-tick" data-live-search="true" multiple>

                                 @foreach ($all_personnels as $personnel)
                                     <option value="{{ $personnel->id }}">
                                         {{ $personnel->titre }} {{ $personnel->prenom }} {{ $personnel->nom }}
                                     </option>
                                 @endforeach
                             </select>
                         </div>
                     </div>

                     <div class="row">
                         <div class="mb-3">
                             <label for="meeting_link" class="form-label">Lien de réunion (optionnel)</label>
                             <input type="url" class="form-control" name="meeting_link" id="meeting_link"
                                 placeholder="https://...">
                         </div>
                         <div class="mb-3">
                             <label for="breve_description" class="form-label">Brief description</label>
                             <textarea name="breve_description" class="form-control" id="breve_description" cols="30" rows="4"></textarea>
                         </div>
                         <div class="mb-3">
                             <label for="meeting_file" class="form-label">Agenda of the meeting (Optional)</label>
                             <input type="file" class="form-control fileClass" name="meeting_file" id="meeting_file">
                         </div>
                     </div>



                     <div class="modal-footer">
                         <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                         <button type="submit" form="meetingForm" class="btn btn-primary">Enregistrer</button>
                     </div>
                 </form>
             </div>

         </div>
     </div>
 </div>

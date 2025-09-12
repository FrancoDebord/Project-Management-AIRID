 <!-- Modal pour créer un meeting -->
 <div class="modal fade" id="meetingModal" tabindex="-1" aria-labelledby="meetingModalLabel" aria-hidden="true">
     <div class="modal-dialog">
         <div class="modal-content">
             <div class="modal-header bg-primary text-white">
                 <h5 class="modal-title" id="meetingModalLabel">Schedule Meeting</h5>
                 <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                     aria-label="Close"></button>
             </div>
             <div class="modal-body">
                 <form id="meetingForm" action="">

                    @csrf
                    <input type="hidden" name="meeting_type" value="study_initiation_meeting">
                     <div class="mb-3">
                         <label for="meetingDate" class="form-label">Date</label>
                         <input type="date" class="form-control" id="meetingDate" name="meeting_date" required>
                     </div>
                     <div class="mb-3">
                         <label for="meetingTime" class="form-label">Heure</label>
                         <input type="time" class="form-control" id="meetingTime" required>
                     </div>
                     <div class="mb-3">
                         <label for="participants" class="form-label">Participants</label>
                         <select id="participants" name="participants" class=" form-control selectpicker show-tick"
                             data-live-search="true" multiple>

                             @foreach ($all_personnels as $personnel)
                                 <option value="{{ $personnel->id }}">
                                     {{ $personnel->titre }} {{ $personnel->prenom }} {{ $personnel->nom }}
                                 </option>
                             @endforeach
                         </select>
                     </div>
                     <div class="mb-3">
                         <label for="meetingLink" class="form-label">Lien de réunion (optionnel)</label>
                         <input type="url" class="form-control" id="meetingLink" placeholder="https://...">
                     </div>
                 </form>
             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                 <button type="submit" form="meetingForm" class="btn btn-primary">Enregistrer</button>
             </div>
         </div>
     </div>
 </div>

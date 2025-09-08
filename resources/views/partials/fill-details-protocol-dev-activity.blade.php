  <!-- Modal -->
  <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="detailsModalLabel">Fill Activity Details</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form action="#" method="POST">
                  @csrf
                  <div class="modal-body">
                    
                      <div class="row mt-2 mb-3">
                          <div class="col-12" id="div-messages-error"></div>

                      </div>

                      <div class="row">
                          <div class="mb-3 col">
                              <input type="hidden" name="protocol_dev_activity_project_id"
                                  id="protocol_dev_activity_project_id" value="">
                              <label for="activityName" class="form-label">Activity</label>
                              <input type="text" class="form-control" id="activityName" name="activityName" readonly>
                          </div>
                          <div class="col mb-3">
                              <label for="date_performed" class="form-label">Date Performed</label>
                              <input type="text" class="form-control" id="date_performed_protocol_dev"
                                  name="date_performed" readonly>
                          </div>
                      </div>

                  </div>
                  <div class="row">
                      <div class="col  ">
                          <div class="m-3">
                              <label for="document_file" class="form-label">Upload Document</label>
                              <input type="file" class="form-control fileClass" id="document_file"
                                  name="document_file" accept="application/pdf" />
                          </div>

                      </div>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-primary">Save changes</button>
                  </div>
              </form>
          </div>
      </div>
  </div>

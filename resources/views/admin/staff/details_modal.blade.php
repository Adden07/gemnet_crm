  <!-- Modal -->
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">({{ $details->username }}) Details</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="col-md-12">
              <div class="row">
                <div class="col-md-3">
                  <label>Name</label>
                  <br>
                  {{ @$details->name }}
                </div>
                <div class="col-md-3">
                  <label>Username</label>
                  <br>
                  {{ @$details->username }}
                </div>
                <div class="col-md-3">
                  <label>Email</label>
                  <br>
                  {{ @$details->email }}
                </div>
                <div class="col-md-3">
                  <label>NIC</label>
                  <br>
                  {{ @$details->nic }}
                </div>
              </div>
            </div>
    
            <div class="col-md-12 mt-3">
              <div class="row">
                <div class="col-md-3">
                  <label>Mobile No</label>
                  <br>
                  {{ @$details->mobile }}
                </div>
                <div class="col-md-3">
                  <label>Address</label>
                  <br>
                  {{ @$details->address }}
                </div>
                <div class="col-md-3">
                  <label>Created on</label>
                  <br>
                  {{ @date('Y-m-d',strtotime($details->created_at)) }}
                </div>
                <div class="col-md-3">
                  <label>Status</label>
                  <br>
                  @if(@$details->is_active == 'active')
                    <span class="badge badge-success">Active</span>
                  @endif
                </div>
              </div>
            </div>
    
            <div class="col-md-12 mt-3">
              <div class="row">
                <div class="col-md-3">
                  <label>Admin Photo</label> <br>
                  <img src="@if(@file_exists($details->image)) {{ asset($details->image) }} @else {{ asset('admin_uploads/no_image.jpg') }} @endif" alt="NIC Back" class="w-100" height="150px">
                </div>
  
    
                <div class="col-md-3">
                  <label>NIC Front</label> <br>
                  <img src="@if(@file_exists($details->nic_front)) {{ asset($details->nic_front) }} @else {{ asset('admin_uploads/no_image.jpg') }} @endif" alt="NIC Back" class="w-100" height="150px">
                </div>
    
                <div class="col-md-3">
                  <label>NIC Back</label> <br>
                  <img src="@if(@file_exists($details->nic_back)) {{ asset($details->nic_back) }} @else {{ asset('admin_uploads/no_image.jpg') }} @endif" alt="NIC Back" class="w-100" height="150px">
                </div>
    
              </div>
            </div>
        </div>
      </div>
    </div>

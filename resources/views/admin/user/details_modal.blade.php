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
              <label>
                Name
              </label>
              <br>
              {{ @$details->name }}
            </div>
            <div class="col-md-3">
              <label>
                Username
              </label>
              <br>
              {{ @$details->username }}
            </div>
            <div class="col-md-3">
              <label>
                Password
              </label>
              <br>
              <div class="input-group mpass">
                <input type="password" name="password" value="{{ @$details->password }}" value="" class="form-control border-0" id="password" disabled>
                <span class="input-group-text pass-show">
                  <a href="javascript:void(0)" class="pas"><i class="fa fa-eye"></i></a>
                </span>
                <span class="input-group-text pass-hide d-none">
                  <a href="javascript:void(0)"><i class="fa fa-eye-slash"></i></a>
                </span>
              </div>
            </div>

            <div class="col-md-3">
              <label>
                NIC
              </label>
              <br>
              {{ @$details->nic }}
            </div>
          </div>
        </div>

        <div class="col-md-12 mt-3">
          <div class="row">
            <div class="col-md-3">
              <label>Mobile No</label> <br>
              {{ @$details->mobile }}
            </div>

            <div class="col-md-3">
              <label>Sales Person</label> <br>
              {{ @$details->admin->name }}
            </div>

            <div class="col-md-3">
              <label>Address</label> <br>
              {{ @$details->address }}
            </div>

            <div class="col-md-3">
              <label>City</label> <br>
              {{ @$details->city->city_name }}
            </div>

          </div>
        </div>


        <div class="col-md-12 mt-3">
          <div class="row">
            <div class="col-md-3">
              <label>Area</label> <br>
              {{ @$details->area->area_name }}
            </div>

            <div class="col-md-3">
              <label>Subarea</label> <br>
              {{ @$details->subarea->area_name }}
            </div>

            <div class="col-md-3">
              <label>Status</label> <br>
              @if($details->status == 'registered')
              <span class="badge badge-info">Registered</span>
              @endif
            </div>

            <div class="col-md-3">
              <label>Created On</label> <br>
              {{ @date('Y-m-d',strtotime($details->created_at)) }}
            </div>

          </div>
        </div>


        <div class="col-md-12 mt-3">
          <div class="row">
            <div class="col-md-3">
              <label>NIC Front</label> <br>
              <img src="@if(@file_exists($details->nic_front)) {{ asset($details->nic_front) }} @else {{ asset('admin_uploads/no_image.jpg') }} @endif" alt="NIC Back" class="w-100" height="150px">
            </div>

            <div class="col-md-3">
              <label>NIC Back</label> <br>
              <img src="@if(@file_exists($details->nic_back)) {{ asset($details->nic_back) }} @else {{ asset('admin_uploads/no_image.jpg') }} @endif" alt="NIC Back" class="w-100" height="150px">
            </div>

            <div class="col-md-3">
              <label>User Form Front</label> <br>
              <img src="@if(@file_exists($details->user_form_front)) {{ asset($details->user_form_front) }} @else {{ asset('admin_uploads/no_image.jpg') }} @endif" alt="NIC Back" class="w-100" height="150px">
            </div>

            <div class="col-md-3">
              <label>User Form Back</label> <br>
              <img src="@if(@file_exists($details->user_form_back)) {{ asset($details->user_form_back) }} @else {{ asset('admin_uploads/no_image.jpg') }} @endif" alt="NIC Back" class="w-100" height="150px">
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>

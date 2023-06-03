@extends('layouts.admin')
@section('content')
<style>
    .select2-container{width: 100% !important}
    .switch {
      position: relative;
      display: inline-block;
      width: 35px;
      height: 22px;
    }
    
    .switch input { 
      opacity: 0;
      width: 0;
      height: 0;
    }
    
    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #ccc;
      -webkit-transition: .4s;
      transition: .4s;
    }
    
    .slider:before {
      position: absolute;
      content: "";
      height: 15px;
      width: 15px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      -webkit-transition: .4s;
      transition: .4s;
    }
    
    input:checked + .slider {
      background-color: #1bb99a;
    }
    
    input:focus + .slider {
      box-shadow: 0 0 1px #1bb99a;
    }
    
    input:checked + .slider:before {
      -webkit-transform: translateX(13px);
      -ms-transform: translateX(13px);
      transform: translateX(13px);
    }
    
    /* Rounded sliders */
    .slider.round {
      border-radius: 34px;
    }
    
    .slider.round:before {
      border-radius: 50%;
    }

    label.error {display:none;}
</style>
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">User</a></li>
                    <li class="breadcrumb-item active">Profile </li>
                </ol>
            </div>
            <h4 class="page-title">Profile-{{ $user_details->username }} -- 
                Balance 
                @if($user_details->user_current_balance < 0)
                    <span class="text-danger">{{ number_format($user_details->user_current_balance, 2) }}</span>
                @elseif($user_details->user_current_balance == 0)
                    {{ number_format($user_details->user_current_balance, 2) }}
                @else
                    <span class="text-success">{{ number_format($user_details->user_current_balance, 2) }}</span>
                @endif
                @if($user_details->credit_limit != 0)
                -- Credit Limit {{ number_format($user_details->credit_limit) }}
                @endif
            </h4>
        </div>
    </div>
</div>

<ul class="nav nav-tabs" id="myTab" role="tablist">
    @if($user_details->status != 'registered')
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="service_details_tab" data-toggle="tab" href="#service_details" role="tab" aria-controls="personal_info" aria-selected="true" >Service Details</a>
        </li>
    @endif
    <li class="nav-item" role="presentation">
        <a class="nav-link active" id="personal_info_tab" data-toggle="tab" href="#personal_info" role="tab" aria-controls="personal_info" aria-selected="true" >Personal Info</a>
      </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="change_pass_tab" data-toggle="tab" href="#change_pass" role="tab" aria-controls="change_pass" aria-selected="true" >Change Password</a>
    </li>

    <li class="nav-item" role="presentation">
        <a class="nav-link" id="doc_tab" data-toggle="tab" href="#document_tab" role="tab" aria-selected="true" >Documents</a>
    </li>

    <li class="nav-item" role="presentation">
        <a class="nav-link" id="record_tab" data-toggle="tab" href="#record" role="tab" aria-selected="true" >Packages History</a>
    </li>

    <li class="nav-item" role="presentation">
        <a class="nav-link" id="invoice_tab" data-toggle="tab" href="#invoices" role="tab" aria-selected="true" >Invoices/Payments</a>
    </li>
    @if(auth()->user()->user_type == 'admin' && $user_details->status != 'registered')
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="edit_expiration_tab" data-toggle="tab" href="#edit_expiration" role="tab" aria-selected="true" >Edit Expiration</a>
        </li>
    @endif
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="remarks_tab" data-toggle="tab" href="#remarks" role="tab" aria-selected="true" >Remarks</a>
    </li>
    @can('user-credit-limit')
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="credit_limit_tab" data-toggle="tab" href="#credit_limit" role="tab" aria-selected="true" >Credit Limit</a>
        </li>
    @endcan
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="queue_tab" data-toggle="tab" href="#queue" role="tab" aria-selected="true" >Queue</a>
    </li>
</ul>

<div class="tab-content pt-0" id="myTabContent">
    <div class="tab-pane fade show active" id="personal_info" role="tabpanel">
        <div class="row">
            <div class="col-lg-12">
                <div class="card-box">
                    @can('edit-user')
                        <a href="{{ route('admin.users.edit',['id'=>$user_details->hashid]) }}" class="btn btn-primary float-right mb-3 ml-2" id="edit_personal_info">Edit User</a>
                        <a href="javascript:void(0)" onClick="window.location.reload()"  class="btn btn-primary float-right mb-3 mr-2 d-none" id="reset_btn">Cancel</a>

                    @endcan
                    @if($user_details->status == 'registered' && auth()->user()->id == $user_details->admin_id)
                    {{-- @can('active-user') --}}
                        <a href="{{ route('admin.packages.add_user_package',['id'=>$user_details->hashid]) }}" class="btn btn-primary float-right add_package" title="Activate Package" data-status="{{ $user_details->status }}">
                            Activate Package
                        </a>
                    {{-- @endcan --}}
                    {{-- @can('renew-user')     --}}

                    {{-- @endcan --}}
                @endif
                    <form action="{{ route('admin.users.update_info') }}" method="POST" class="ajaxForm" id="personal_info_form">
                        @csrf
                        <table class="table">
                            <tr>
                                <th>Name</th>
                                <td style="vertical-align: middle;padding-top: 0;padding-bottom: 0;"><input type="text" class="form-control border-0" disabled name="name" id="name" data-ov="{{ $user_details->name }}" value="{{ $user_details->name }}"></td>
                            </tr>
                            <tr>
                                <th>NIC</th>
                                <td style="vertical-align: middle;padding-top: 0;padding-bottom: 0;"><input type="text" class="form-control border-0" disabled name="nic" id="nic" data-ov="{{ $user_details->nic }}" value="{{ $user_details->nic }}"></td>
                            </tr>
                            <tr>
                                <th>Mobile</th>
                                <td style="vertical-align: middle;padding-top: 0;padding-bottom: 0;">
                                    {{-- <div class="d-none"id="show_mobile_code">

                                    </div> --}}
                                    <div id="mobile_code">
                                        <input type="text" class="form-control border-0" disabled name="mobile" id="mobile" data-ov="{{ substr($user_details->mobile,2) }}"  value="{{ substr($user_details->mobile,2) }}">
                                        <div class="mobile_err w-100"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Connection Address</th>
                                <td style="vertical-align: middle;padding-top: 0;padding-bottom: 0;"><input type="text" class="form-control border-0" disabled name="address" id="address" data-ov="{{ $user_details->address }}" value="{{ $user_details->address }}"></td>
                            </tr>
                            <tr>
                                <th>Area</th>
                                <td style="vertical-align: middle;padding-top: 0;padding-bottom: 0;">
                                    <select class="form-control border-0"name="area_id" id="area_id" data-ov="{{ hashids_encode($user_details->area_id) }}" disabled>
                                        <option value="">Select Area</option>
                                        @foreach($areas AS $area)
                                            <option value="{{ $area->hashid }}" @if(@$user_details->area_id == $area->id) selected @endif>{{ $area->area_name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th>Subarea</th>
                                <td style="vertical-align: middle;padding-top: 0;padding-bottom: 0;">
                                    <select class="form-control border-0" name="subarea_id" id="subarea_id" disabled data-ov="{{ hashids_encode($user_details->subarea_id) }}">
                                        <option value="">Select Subarea</option>
                                            {{-- @foreach($subareas AS $subarea)
                                                <option value="{{ $subarea->hashid }}" @if($user_details->subarea_id == $subarea->id) selected @endif>{{ $subarea->area_name }}</option>
                                            @endforeach --}}
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th>City</th>
                                <td style="vertical-align: middle;padding-top: 0;padding-bottom: 0;">
                                    <select class="form-control border-0" name="city_id" id="city_id" @if(auth()->user()->user_type != 'admin' && auth()->user()->user_type != 'superadmin') @endif disabled data-ov="{{ hashids_encode($user_details->city_id) }}">
                                        <option value="">Select City</option>
                                        @foreach($cities AS $city)
                                            <option value="{{ $city->hashid }}" @if(@$user_details->city_id == $city->id || auth()->user()->city_id == $city->id) selected @endif>{{ $city->city_name }}</option>
                                        @endforeach
                                    </select>
                                <td>
                            </tr>
                        </table>
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" name="user_id" value="{{ $user_details->hashid }}">
                                <input type="submit" class="btn btn-primary float-right d-none" value="update" id="personal_info_form_submit">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @if($user_details->status != 'registered')
        {{-- <div class="row">
            <div class="col-md-6">
                
            </div>
        </div> --}}
        <div class="tab-pane fade show" id="service_details" role="tabpanel">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        <div class="row">
                            <div class="col-lg-12">
                                {{-- @if(auth()->user()->id == $user_details->admin_id || auth()->user()->user_type == 'admin') --}}
                                    @if(@$user_details->user_status == 1)
                                        <a href="#" class="btn btn-danger btn-sm float-right ml-2" data-btn-text="" data-msg="" onclick="ajaxRequest(this)" id="disable_user" data-url="{{ route('admin.users.change_status',['id'=>$user_details->hashid,'status'=>0]) }}">Disable</a>
                                        <a href="#" class="btn btn-info btn-sm float-right ml-2" id="user_password" data-pass="{{ $user_details->password }}">User Password</a>
                                        @can('change-user-package')
                                            <button class="btn btn-primary float-right mb-1 btn-sm" id="edit_package">Change Package</button>
                                        @endcan
                                        <!---dispaly upgrade package button when user is active-->
                                        @can('upgrade-user-package')
                                            @if($user_details->status == 'active')
                                                {{-- @if(@$user_records->sortByDesc('id')->first()->status != 'upgrade') --}}
                                                    <button class="btn btn-info float-right mb-1 mr-2 btn-sm" id="upgrade_package">Upgrade Package</button>
                                                {{-- @endif --}}
                                            @endif
                                        @endcan
                                        
                                        <button class="btn btn-danger float-right mb-1 btn-sm d-none" id="cancel_button">Cancel</button>
                                        <button class="btn btn-success float-right mb-1 mr-2 btn-sm d-none" id="update_package">Update Package</button>
                                        <!--inly dispaly kick button when user is online-->
                                        @if($user_details->last_logout_time == null)
                                            <a href="#" class="btn btn-danger btn-sm float-right mr-2" id="kick_user" onclick="ajaxRequest(this)" data-url="{{ route('admin.users.kick',['id'=>$user_details->hashid]) }}" title="kick user">
                                                Kick User
                                            </a>
                                        @endif
                                        <!--only display reset qouta button when qt_used not equal to zero and user is admin-->
                                        @if(auth()->user()->user_type == 'admin' && $user_details->qt_used != 0)
                                            <a href="#" class="btn btn-warning btn-sm float-right mr-2" id="reset_qouta" title="Reset Qouta" onclick="ajaxRequest(this)" data-url="{{ route('admin.users.reset_qouta',['id'=>$user_details->hashid]) }}">Reset Qouta</a>
                                        @endif
                                        <!--only display remove mac button when mac 0 and macaddress is null-->
                                        @if((auth()->user()->user_type == 'admin') || ($user_details->macs > 0 && $user_details->macaddress != null))
                                            <a href="#" class="btn btn-secondary btn-sm float-right mr-2" id="remove_mac" onclick="ajaxRequest(this)" data-url="{{ route('admin.users.remove_mac',['id'=>$user_details->hashid]) }}">Remove Mac</a>
                                        @endif
                                        @can('renew-user')
                                            {{-- @if(auth()->user()->id == $user_details->admin_id) --}}
                                                <a href="{{ route('admin.packages.add_user_package',['id'=>$user_details->hashid]) }}" class="btn btn-primary float-right add_package btn-sm mr-2" title="Renew Package" data-status="{{ $user_details->status }}">
                                                    Renew Package
                                                </a>
                                            {{-- @endif --}}
                                        @endcan
                                    {{-- @endif --}}
                                    <!--if user_status is when then display disable button otherwise enable-->
                                    {{-- @if($user_details->user_status == 1)
                                        <a href="#" class="btn btn-danger btn-sm float-right mr-2" data-btn-text="" data-msg="" onclick="ajaxRequest(this)" id="disable_user" data-url="{{ route('admin.users.change_status',['id'=>$user_details->hashid,'status'=>0]) }}">Disable</a> --}}
                                    <!--when user is disabled by admin then only admin can enable the user-->
                                    @elseif((($user_details->user_status == 2 || $user_details->user_status == 0) && auth()->user()->user_type == 'admin') || $user_details->user_status == 0 )
                                        <a href="#" class="btn btn-success btn-sm float-right mr-2" onclick="ajaxRequest(this)" id="enable_user" data-url="{{ route('admin.users.change_status',['id'=>$user_details->hashid,'status'=>1]) }}">Enable</a>
                                    @endif
                                {{-- @endif --}}
                                {{-- <a href="{{ route('admin.users.edit',['id'=>$user_details->hashid]) }}" class="btn btn-primary float-right mb-3">Edit User</a> --}}
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="text-center bg-primary">
                                    <h3 class="m-0 p-2 text-white">Package Details</h3>
                                </div>
                                <table class="table  table-hover">
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            @if($user_details->user_status == 1)
                                                @if($user_details->status == 'registered')
                                                    <span class="badge badge-info">Registered</span>
                                                @elseif($user_details->status == 'active' && $user_details->last_logout_time != null && date('Y',strtotime($user_details->last_logout_time)) == 1970)
                                                    <span class="badge badge-success">Active</span>-<span class="badge badge-warning">Never Online</span>  
                                                @elseif($user_details->status == 'expired' && $user_details->last_logout_time != null && date('Y',strtotime($user_details->last_logut_time)) == 1970)
                                                    <span class="badge badge-danger">Expired</span>-<span class="badge badge-warning">Never Online</span>
                                                @elseif($user_details->status == 'active' && $user_details->last_logout_time == null)
                                                    <span class="badge badge-success">Active</span>-<span class="badge badge-success">Online</span>
                                                @elseif($user_details->status == 'active' && $user_details->last_logout_time != null)
                                                    <span class="badge badge-success">Active</span>-<span class="badge badge-danger">Offline</span>
                                                @elseif($user_details->status == 'expired' && $user_details->last_logout_time == null)
                                                    <span class="badge badge-danger">Expired</span>-<span class="badge badge-success">Online</span>
                                                @elseif($user_details->status == 'expired' && $user_details->last_logout_time != null)
                                                    <span class="badge badge-danger">Expired</span>-<span class="badge badge-danger">Offline</span>
                                                @else
                                                    <span class="badge badge-danger">Expired</span>
                                                @endif
                                            @elseif($user_details->user_status == 2)
                                                <span class="badge badge-danger">Disabled By Admin</span>
                                            @else
                                                <span class="badge badge-danger">Disabled</span>
                                            @endif    

                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Total Volume</th>
                                        <td>{{ number_format($user_details->qt_total/pow(1024,3),2) }} GB</td>
                                    </tr>
                                    <tr>
                                        <th>Used Volume</th>
                                        <td>{{ number_format($user_details->qt_used/pow(1024,3),2) }} GB</td>
                                    </tr>
                                    <tr>
                                        <th>Remaining Volume</th>
                                        <td>
                                            @if($user_details->status != 'registered')
                                                @php 
                                                    @$remaining_volume = $user_details->qt_total - $user_details->qt_used;
                                                    @$remaining_percent = ($remaining_volume * 100)/$user_details->qt_total;
                                                @endphp
                                                @if($remaining_percent >= 60)
                                                    <span class="badge badge-info">{{ number_format($remaining_volume/pow(1024,3)) }} GB</span>
                                                @elseif(@$remaining_percent >= 26 )
                                                    <span class="badge badge-success">{{ number_format($remaining_volume/pow(1024,3)) }} GB</span>
                                                @else
                                                    <span class="badge badge-danger">{{ number_format($remaining_volume/pow(1024,3)) }} GB</span>
                                                @endif
                                            @else   
                                                <span class="badge badge-danger">0 GB</span>
                                            @endif     
                                    </tr>
                                    <tr>
                                        <th>Primary Package</th>
                                        <td id="primary_package">{{ @$user_details->primary_package->name }}</td>
                                        <td>
                                            <select class="form-control d-none" name="primary_package_ddl" id="primary_package_ddl">
                                                <option value="">Select Package</option>
                                                @foreach($packages AS $package)
                                                    <option value="{{ $package->hashid }}" @if($user_details->package == $package->id) selected @endif>{{ $package->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                    @if(!empty($user_details->last_package) && !empty($user_details->last_expiration_date))
                                        <tr>
                                            <th>Last Packge</th>
                                            <td>{{ @$user_details->lastPackage->name }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th>Current Package</th>
                                        <td id="current_package">{{ @$user_details->current_package->name }}</td>
                                        <td>
                                            <select class="form-control d-none" name="current_package_ddl" id="current_package_ddl">
                                                {{-- @foreach($packages AS $package)
                                                    <option value="{{ $package->hashid }}" @if($user_details->c_package == $package->id) selected @endif>{{ $package->name }}</option>
                                                @endforeach --}}
                                                @if(isset($user_packages))<!--when user is subdealer-->
                                                    @foreach($user_packages AS $package)
                                                        @if(!in_array($package->package_id,$ids))
                                                            {{-- <option value="{{ $package->hashid }}" @if($user_details->c_package == $package->id) selected @endif>{{ $package->package->name }}</option> --}}
                                                            {{-- <option value="{{ hashids_encode($package->package->id) }}" @if(@$user_package_id->package_id == $package->id) selected @endif>{{ $package->package->name }}</option> --}}
                                                            
                                                            <option value="{{ hashids_encode($package->package->id) }}" @if($user_details->c_package == $package->package->id) selected @endif>{{ $package->package->name }}</option>
                                                        @endif    
                                                    @endforeach
                                                    @else<!--when user is not subdealer-->
                                                        @foreach($packages AS $package)
                                                            <option value="{{ $package->hashid }}" @if($user_details->c_package == $package->id) selected @endif>{{ $package->name }}</option>
                                                        @endforeach
                                                    @endif
                                            </select>
                                        </td>
                                    </tr>
                                    {{-- <tr>
                                        <th>Sales Person</th>
                                        <td>{{ $user_details->admin->username }}</td>
                                    </tr> --}}
                                    @if(date('Y',strtotime($user_details->last_expiration_date)) != 1970)
                                    <tr>
                                        <th>Last Expiration</th>
                                        <td>
                                            <span class="badge badge-primary">{{ date('d-M-Y H:i:s',strtotime($user_details->last_expiration_date)) }}</span>
                                        </td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <th>Current Expiration</th>
                                        <td>
                                            @if($user_details->status == 'active')
                                                <span class="badge badge-success">{{ date('d-M-Y H:i:s',strtotime($user_details->current_expiration_date)) }}</span>
                                            @elseif($user_details->status == 'expired')
                                                <span class="badge badge-danger">{{ date('d-M-Y H:i:s',strtotime($user_details->current_expiration_date)) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <!--display macaddress when not null-->
                                    {{-- @if($user_details->macaddress != null)
                                        <tr>
                                            <th>Mac Address</th>
                                            <td>{{ $user_details->macaddress }}</td>
                                        </tr>
                                    @endif --}}
                                    {{-- <tr>
                                        <th>Mac Count</th>
                                        <td>{{ $user_details->macs }}</td>
                                    </tr> --}}
                                    {{-- <tr>
                                        <th>Mac Lock</th>
                                        <td>
                                            @if($user_details->maclock == 1)
                                                <span class="badge badge-success">Enabled</span>
                                            @else 
                                                <span class="badge badge-danger">Disabled</span>
                                            @endif
                                        </td>
                                    </tr> --}}
                                    <!--disaply mac vendor when not null-->
                                    {{-- @if($user_details->macvendor)
                                        <tr>
                                            <th>Mac Vendor</th>
                                            <td>{{ $user_details->macvendor }}</td>
                                        </tr>
                                    @endif
                                    @if(!is_null($user_details->last_login_time) && date('Y',strtotime($user_details->last_login_time)) != 1970)
                                    <tr>
                                        <th>Last Login Time</th>
                                        <td>{{ date('d-M-Y H:i:s',strtotime($user_details->last_login_time)) }}</td>
                                    </tr>
                                    @endif
                                    @if(!is_null($user_details->last_logout_time) && date('Y',strtotime($user_details->last_logout_time)) != 1970)
                                    <tr>
                                        <th>Last Logout Time</th>
                                        <td>{{ date('d-M-Y H:i:s',strtotime($user_details->last_logout_time)) }}</td>
                                    </tr>
                                    @endif --}}
                                </table>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center bg-primary">
                                    <h3 class="m-0 p-2 text-white">Router Details</h3>
                                </div>
                                <table class="table  table-hover">
                                    @if($user_details->macvendor != NULL)
                                    <tr>
                                        <th>Current Router</th>
                                        <td>{{ $user_details->macvendor }}</td>
                                    </tr>
                                    @endif
                                    @if($user_details->last_macvendor != NULL)
                                        <tr>
                                            <th>Last Router</th>
                                            <td>{{ $user_details->last_macvendor }}</td>
                                        </tr>
                                    @endif
                                    @if($user_details->macaddress != null)
                                        <tr>
                                            <th>Current Mac Address</th>
                                            <td>{{ $user_details->macaddress }}</td>
                                        </tr>
                                    @endif
                                    @if($user_details->last_macaddress != null)
                                        <tr>
                                            <th>Last Mac Address</th>
                                            <td>{{ $user_details->last_macaddress }}</td>
                                        </tr>
                                    @endif
                                    @if($user_details->macs != 0 && $user_details->maclock == 1)
                                        <tr>
                                            <th>Mac Count</th>
                                            <td>{{ $user_details->macs }}</td>
                                        </tr>
                                    @endif
                                    @if($user_details->maclock == 1)
                                        <tr>
                                            <th>Mac Lock</th>
                                            <td>
                                                @if($user_details->maclock == 1)
                                                    <span class="badge badge-success">Enabled</span>
                                                @else 
                                                    <span class="badge badge-danger">Disabled</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                    @if(!is_null($user_details->last_login_time) && date('Y',strtotime($user_details->last_login_time)) != 1970)
                                    <tr>
                                        <th>Last Login Time</th>
                                        <td>
                                        {{-- <td>{{ date('d-M-Y H:i:s',strtotime($user_details->last_login_time)) }}</td> --}}
                                            @if(date('d',strtotime($user_details->last_login_time)) == date('d'))
                                                <span class="badge badge-success">{{ $user_details->last_login_time }}</span>
                                            @elseif(date('d',strtotime($user_details->last_login_time)) == date('d',strtotime('-1 day')))
                                                <span class="badge badge-info">{{ $user_details->last_login_time }}</span>
                                            @else
                                                <span class="badge" style='background-color:orangered'>{{ $user_details->last_login_time }}</span> 
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                    @if(!is_null($user_details->last_logout_time) && date('Y',strtotime($user_details->last_logout_time)) != 1970)
                                    <tr>
                                        <th>Last Logout Time</th>
                                        {{-- <td>{{ date('d-M-Y H:i:s',strtotime($user_details->last_logout_time)) }}</td> --}}
                                        <td>
                                            @if(date('d',strtotime($user_details->last_logout_time)) == date('d'))
                                                <span class="badge badge-success">{{ $user_details->last_logout_time }}</span>
                                            @elseif(date('d',strtotime($user_details->last_logout_time)) == date('d',strtotime('-1 day')))
                                                <span class="badge badge-warning">{{ $user_details->last_logout_time }}</span>
                                            @else
                                                <span class="badge" style='background-color:orangered'>{{ $user_details->last_logout_time }}</span> 
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center bg-primary">
                                    <h3 class="m-0 p-2 text-white">Sales Details</h3>
                                </div>
                                <table class="table  table-hover">
                                    <tr>
                                        <th>Activation By</th>
                                        <td>{{ $user_details->activation->username }}</td>
                                    </tr>
                                    <tr>
                                        <th>Activation Date</th>
                                        <td>{{ date('d-M-Y H:i:s',strtotime($user_details->activation_date)) }}</td>
                                    </tr>
                                    @if($user_details->renew_by != null)
                                        <tr>
                                            <th>Last Renew By</th>
                                            <td>{{ @$user_details->renew->username }}</td>
                                        </tr>
                                        <tr>
                                            <th>Last Renew Date</th>
                                            <td>{{ date('d-M-Y H:i:s',strtotime($user_details->renew_date)) }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th>Sales Person</th>
                                        <td>{{ @$user_details->salePerson->username }}</td>
                                    </tr>
                                    {{-- {{ dd($user_details) }} --}}
                                    <tr>
                                        <th>Field Enginner</th>
                                        <td>{{ @$user_details->fieldEngineer->username }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    @endif

    <div class="tab-pane fade" id="change_pass" role="tabpanel">
        <div class="row">
            <div class="col-lg-12">
                <div class="card-box">
                    <form action="{{ route('admin.users.update_password') }}" class="ajaxForm" method="post" enctype="multipart/form-data" novalidate id="change_pass_form">
                        @csrf
                        <div class="row">
                            <div class="form-group  col-md-6">
                                <label for="password">Connection Password<span class="text-danger">*</span></label>
                                <div class="input-group mpass">
                                    <input type="password" name="password" placeholder="Enter password" minlength="6" maxlength="12"  value="" class="form-control" id="password" ><span class="input-group-text pass-show"><a href="javascript:void(0)"><i class="fa fa-eye"></i></a></span><span class="input-group-text pass-hide d-none"><a href="javascript:void(0)"><i class="fa fa-eye-slash"></i></a></span>
                                </div>
                                
                            </div>
        
                            <div class="form-group col-md-6">
                                <label for="password_confirmation ">Confirm Password<span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" placeholder="Confirm password" minlength="6" maxlength="12" value="" class="form-control" id="password_confirmation" >
                            </div>
                        </div>  
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" name="user_id" value="{{ $user_details->hashid }}">
                                <input type="submit" class="btn btn-primary float-right"value="Update">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="document_tab" role="tabpanel">
        <div class="row">
            <div class="col-lg-12">
                <div class="card-box">
                    <form action="{{ route('admin.users.update_document') }}" method="POST" enctype="multipart/form-data" class="ajaxForm" id="document_form">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="logo">NIC Front</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="nic_front"  id="nic_front" onchange="showPreview('preview_nic_front')">
                                        <label class="custom-file-label profile_img_label" for="logo">Choose NIC front</label>
                                    </div>
                                    <div class="nic_front_err w-100"></div>
                                    <div class="position-relative mt-3">
                                        <img id="preview_nic_front" src="@if(@file_exists($user_details->nic_front)) {{ asset($user_details->nic_front) }} @else {{ asset('admin_uploads/no_image.jpg') }}  @endif"  class="@if(!isset($is_update)) @endif" width="100px" height="100px"/>
                                        @if(@file_exists($user_details->nic_front))
                                            <a   href="javascript:void(0)" class="btn btn-danger btn-sm rounded position-absolute" style="top: 0;right:0" data-url="{{ route('admin.users.remove_attachment',['id'=>$user_details->hashid,'type'=>'nic_front','path'=>$user_details->nic_front]) }}" onclick="ajaxRequest(this)" id="remove_nic_front">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
        
                            <div class="form-group col-md-6">
                                <label for="logo">NIC Back</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="nic_back"  id="nic_back" onchange="showPreview('preview_nic_back')">
                                        <label class="custom-file-label profile_img_label" for="logo">Choose NIC back</label>
                                    </div>
                                    <div class="nic_back_err w-100"></div>
        
                                    <div class="position-relative mt-3">
                                        <img id="preview_nic_back" src="@if(@file_exists($user_details->nic_back)) {{ asset($user_details->nic_back) }} @else {{ asset('admin_uploads/no_image.jpg') }} @endif"  class="@if(!isset($is_update))  @endif" width="100px" height="100px"/>
                                        @if(@file_exists($user_details->nic_back))
                                            <a   href="javascript:void(0)" class="btn btn-danger btn-sm rounded position-absolute" style="top: 0;right:0" data-url="{{ route('admin.users.remove_attachment',['id'=>$user_details->hashid,'type'=>'nic_back','path'=>$user_details->nic_back]) }}" onclick="ajaxRequest(this)" id="remove_nic_back">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        @endif
                                    </div>
        
                                </div>
                            </div>
                        </div>
        
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="logo">User Form Front</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="user_form_front"  id="user_form_front" onchange="showPreview('preview_user_form_front')">
                                        <label class="custom-file-label profile_img_label" for="logo">Choose NIC front</label>
                                    </div>
                                    <div class="user_form_front_err w-100"></div>
                                    <div class="position-relative mt-3">
                                        <img id="preview_user_form_front" src="@if(@file_exists($user_details->user_form_front)) {{ asset($user_details->user_form_front) }} @else {{ asset('admin_uploads/no_image.jpg') }}  @endif"  class="@if(!isset($is_update))  @endif" width="100px" height="100px"/>
                                        @if(@file_exists($user_details->user_form_front))
                                            <a   href="javascript:void(0)" class="btn btn-danger btn-sm rounded position-absolute" style="top: 0;right:0" data-url="{{ route('admin.users.remove_attachment',['id'=>$user_details->hashid,'type'=>'user_form_front','path'=>$user_details->user_form_front]) }}" onclick="ajaxRequest(this)" id="remove_user_form_front">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
        
                            <div class="form-group col-md-6">
                                <label for="logo">User Form Back</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input"  name="user_form_back"  id="user_form_back" onchange="showPreview('preview_user_form_back')">
                                        <label class="custom-file-label profile_img_label" for="logo">Choose NIC back</label>
                                    </div>
                                    <div class="user_form_back_err w-100"></div>
        
                                    <div class="position-relative mt-3">
                                        <img id="preview_user_form_back" src="@if(@file_exists($user_details->user_form_back)) {{ asset($user_details->user_form_back) }} @else {{ asset('admin_uploads/no_image.jpg') }} @endif"  class="@if(!isset($is_update))  @endif" width="100px" height="100px"/>
                                        @if(@file_exists($user_details->user_form_back))
                                            <a   href="javascript:void(0)" class="btn btn-danger btn-sm rounded position-absolute" style="top: 0;right:0" data-url="{{ route('admin.users.remove_attachment',['id'=>$user_details->hashid,'type'=>'nic_back','path'=>$user_details->user_form_back]) }}" onclick="ajaxRequest(this)" id="remove_user_form_back">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        @endif
                                    </div>
        
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" name="old_nic_front" value="{{ @$user_details->nic_front }}">
                                <input type="hidden" name="old_nic_back" value="{{ @$user_details->nic_back }}">
                                <input type="hidden" name="old-user_form_front" value="{{ @$user_details->user_form_front }}">
                                <input type="hidden" name="old_user_form_back" value="{{ @$user_details->user_form_back }}">
                                <input type="hidden" name="user_id" value="{{ $user_details->hashid }}">
                                <input type="submit" class="btn btn-primary float-right mt-2" value="Update"> 
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="record" role="tabpanel">
        <div class="row">
            <div class="col-lg-12">
                <div class="card-box">
                    {{-- <h4 class="header-title m-t-0">Conntaction Pass</h4>
                    <p class="text-muted font-14 m-b-20">
                        Here you can change Settings.
                    </p> --}}
                    <table class="table">
                        <thead>
                            <th>No</th>
                            <th>Package Date</th>
                            <th>Last Package</th>
                            <th>New Package</th>
                            <th>Status</th>
                            {{-- <th>Package Date</th> --}}
                            <th>Last Expiration</th>
                            <th>New Expiration</th>
                            {{-- <th>Package Status</th> --}}
                        </thead>
                        <tbody>
                            @foreach($user_records AS $record)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ date('d-M-y H:i:s',strtotime($record->created_at)) }}</td>
                                    <td>{{ @$record->last_package->name }}</td>
                                    <td>{{ $record->package->name }}</td>
                                    
                                    {{-- <td>{{ date('d-M-y H:i:s',strtotime($record->expiration)) }}</td> --}}
                                    <td>
                                        @if($record->status == 'activate')
                                            <span class="badge badge-success">Activate</span>
                                        @elseif($record->status == 'renew') 
                                            <span class="badge badge-primary">Renew</span>
                                        @elseif($record->status == 'upgrade')
                                            <span class="badge badge-info">Upgrade</span>    
                                        @elseif($record->status == 'change')
                                            <span class="badge badge-warning">change</span>
                                        @endif
                                    </td>
                                    {{-- <td>
                                        @if($record->package_status == 'active')
                                            <span class="badge badge-success">Active</span>
                                        @elseif($record->package_status == 'expired')
                                            <span class="badge badge-danger">Expired</span>
                                        @endif
                                    </td> --}}
                                    <td>
                                        @if($record->last_expiration != null)
                                            {{ date('d-M-y H:i:s',strtotime($record->last_expiration)) }}
                                        @endif
                                    </td>
                                    <td>{{ date('d-M-y H:i:s',strtotime($record->expiration)) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="invoices" role="tabpanel">
        <div class="row">
            <div class="col-lg-12">
                <div class="card-box">
                    <table class="table">
                        <thead>
                            <th>S.No</th>
                            <th>Invoice ID</th>
                            <th>Datetime</th>
                            <th>Package</th>
                            <th>Current Exp</th>
                            <th>New Exp</th>
                            <th>Amount</th>

                        </thead>
                        <tbody>
                            @foreach($user_invoices As $invoice)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $invoice->invoice_id }}</td>
                                    <td>{{ date('d-M-Y H:i:s',strtotime($invoice->created_at)) }}</td>
                                    <td>{{  $invoice->package->name}}</td>
                                    <td>
                                        @if(date('Y',strtotime($invoice->current_exp_date)) != 1970)
                                            {{ date('d-M-Y H:i:s',strtotime($invoice->current_exp_date)) }}
                                        @endif
                                    </td>
                                    <td>{{ (!is_null($invoice->new_exp_date))? date('d-M-Y H:i:s',strtotime($invoice->new_exp_date)) : '' }}</td>
                                    <td>{{ round($invoice->total) }}</td>
                                    {{-- <td>
                                        @if($invoice->paid == 0)
                                            <label class="switch mb-0">
                                                <input type="checkbox" class="nopopup" onchange="ajaxRequest(this)" data-url="{{ route('admin.accounts.invoices.pay_invoice',['id'=>$invoice->hashid]) }}">
                                                <span class="slider round"></span>
                                            </label>
                                        @elseif($invoice->paid == 1)
                                            <span class="badge badge-success">paid</span>
                                        @endif
                                    </td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card-box">
                    <table class="table">
                        <thead>
                            <th>S.No</th>
                            <th>DateTime</th>

                            <th>Amount</th>
                            <th>New Balance</th>
                            <th>Old Balance</th>
                            <th>Type</th>
                            <th>Online Date</th>
                        </thead>
                        <tbody>
                            @foreach($user_details->payments As $payment)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ date('d-M-Y H:i:s', strtotime($payment->created_at)) }}</td>
                                    <td>{{ number_format($payment->amount) }}</td>
                                    <td>{{ number_format($payment->old_balance) }}</td>
                                    <td>{{ number_format($payment->new_balance) }}</td>
                                    <td>{{ $payment->type }}</td>
                                    <td>{{ (!is_null($payment->online_date)) ? date('d-M-Y', strtotime($payment->online_date)) : '' }}</td>

                                </tr>
                                {{-- <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $invoice->invoice_id }}</td>
                                    <td>{{ date('d-M-Y H:i:s',strtotime($invoice->created_at)) }}</td>
                                    <td>{{  $invoice->package->name}}</td>
                                    <td>
                                        @if(date('Y',strtotime($invoice->current_exp_date)) != 1970)
                                            {{ date('d-M-Y H:i:s',strtotime($invoice->current_exp_date)) }}
                                        @endif
                                    </td>
                                    <td>{{ (!is_null($invoice->new_exp_date))? date('d-M-Y H:i:s',strtotime($invoice->new_exp_date)) : '' }}</td>
                                    <td>{{ round($invoice->total) }}</td>
                                </tr> --}}
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="remarks" role="tabpanel">
        <div class="row">
            <div class="col-lg-12">
                <div class="card-box">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="{{ route('admin.users.remarks') }}"  method="GET" class="ajaxForm" id="remarks_form">
                                @csrf
                                <div class="form-group">
                                    <label for="">Remark Type</label>
                                    <select class="form-control" name="remark_type" id="">
                                        <option value="">Select remark type</option>
                                        @foreach($remarks AS $remark)
                                            <option value="{{ $remark->remark_type }}" @if(@$edit_remark->remark_type == $remark->remark_type) selected @endif>{{ $remark->remark_type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="exampleFormControlTextarea1">Remarks</label>
                                    <textarea name="remark" class="form-control" id="exampleFormControlTextarea1" rows="3">{{ @$edit_remark->text }}</textarea>
                                  </div>
                                  <input type="hidden" name="user_id" value="{{ $user_details->hashid }}">
                                  {{-- @if(auth()->user()->user_type == 'admin' || $user_details->admin_id == auth()->user()->id) --}}
                                  <input type="hidden" name="remark_id" id="remark_id" value="{{@$edit_remark->hashid }}">
                                    <input type="submit" class="btn btn-primary float-right" value="update">
                                  {{-- @endif --}}
                            </form>
                        </div>
                        <div class="col-md-12 mt-2">
                            <table class="table">
                                <thead>
                                    <th>#</th>
                                    <th>DateTime</th>
                                    <th>By</th>
                                    <th>Remark Type</th>
                                    <th>Text</th>
                                    <th>Action</th>
                                </thead>
                                <tbody>
                                    @foreach($user_details->remark->sortByDesc('id') AS $remark)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ date('d-M-Y H:i:s', strtotime($remark->created_at)) }}</td>
                                            <td>{{ $remark->admin->name }} ({{ $remark->admin->user_type }})</td>
                                            <td>{{ $remark->remark_type }}</td>
                                            <td>{{ $remark->text }}</td>
                                            <td>
                                                @if(auth()->user()->user_type != 'admin' && date('Y-m-d',strtotime($remark->created_at)) == date('Y-m-d'))
                                                    <a href="{{ route('admin.users.profile',['id'=>$user_details->hashid, 'remark_id'=>$remark->hashid]) }}" class="btn btn-warning btn-xs waves-effect waves-light">
                                                        <span class="btn-label"><i class="icon-pencil"></i></span>Edit
                                                    </a>
                                                @elseif(auth()->user()->user_type == 'admin')
                                                    <a href="{{ route('admin.users.profile',['id'=>$user_details->hashid, 'remark_id'=>$remark->hashid]) }}" class="btn btn-warning btn-xs waves-effect waves-light">
                                                        <span class="btn-label"><i class="icon-pencil"></i></span>Edit
                                                    </a>
                                                @endif
                                                @if(auth()->user()->user_type == 'admin')
                                                    <button type="button" onclick="ajaxRequest(this)" data-url="{{ route('admin.users.delete_remark', ['id'=>$remark->hashid]) }}" class="btn btn-danger btn-xs waves-effect waves-light">
                                                        <span class="btn-label"><i class="icon-trash"></i></span>Delete
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="credit_limit" role="tabpanel">
        <div class="row">
            <div class="col-lg-12">
                <div class="card-box">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="{{ route('admin.users.update_credit_limit') }}"  method="GET" class="ajaxForm" id="">
                                @csrf
                                <div class="form-group">
                                    <label for="exampleFormControlTextarea1">Credit Limit</label>
                                    <input type="number" class="form-control" name="credit_limit" value="{{ $user_details->credit_limit }}">
                                  </div>
                                  <input type="hidden" name="user_id" value="{{ $user_details->hashid }}" id="user_id_input">
                                  <input type="submit" class="btn btn-primary float-right" value="update">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="queue" role="tabpanel">
        <div class="row">
            <div class="col-lg-12">
                <div class="card-box">
                    <table class="table">
                        <thead>
                            <th>No</th>
                            <th>Package</th>
                            <th>Applied On</th>
                            <td>Date</td>
                        </thead>
                        <tbody>
                            @foreach($user_details->queue AS $q)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ @$q->package->name }}</td>
                                    <td>{{ (!is_null($q->applied_on)) ? date('d-M-Y', strtotime($q->applied_on)) : '' }}</td>
                                    <td>{{ date('d-M-Y', strtotime($q->created_at)) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="edit_expiration" role="tabpanel">
        <div class="row">
            <div class="col-lg-12">
                <div class="card-box">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="{{ route('admin.users.update_expiration', ['user_id'=>$user_details->hashid]) }}"  method="GET" class="ajaxForm" id="remarks_form">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-md-10">
                                        <label for="">Expiration</label>
                                        @if($user_details->status != 'registered' && $user_details->status != 'expired')
                                            <input type="date" class="form-control" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d', strtotime($user_details->current_expiration_date)) }}" name="expiration_date" required>
                                        @else
                                            <input type="date" class="form-control" min="{{ date('Y-m-d') }}" name="expiration_date" required>
                                        @endif
                                    </div>
                                    <div class="col-md-2">
                                        <input type="submit" class="btn btn-primary mt-3" value="update">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<form action="{{ route('admin.packages.change_user_package') }}" class="ajaxForm" id="change_package_form">
    <input type="hidden" name="user_id" value="{{ $user_details->hashid }}">
    <input type="hidden" name="package_type" id="package_type">
    <input type="hidden" name="package_id" id="package_id">
</form>
<!-- Package upgrade Modal -->
<div class="modal fade" id="package" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="package_modal_title"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        <form action="{{ route('admin.packages.upgrade_user_package') }}" class="ajaxForm" id="add_package_form" method="GET">
            @csrf
            <div class="modal-body">
   
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <input type="submit" class="btn btn-primary" value="Update" id="">
            </div>
        </form>
        </div>
    </div>
</div>

<!-- Package Activation And Renew Modal -->
<div class="modal fade" id="user_package" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="user_package_modal_title"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        <form action="{{ route('admin.packages.update_user_package') }}" class="ajaxForm" id="add_user_package_form" method="GET">
            @csrf
            <div class="user-package modal-body">
   
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <input type="submit" class="btn btn-primary" value="Update" id="user_package_modal_submit">
            </div>
        </form>
        </div>
    </div>
</div>
<!-- USER PASSWORD MODAL -->
<div class="modal fade" id="user_password_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">User Password</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="password_modal_body modal-body">
          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

@endsection
@section('page-scripts')
<script>
    //which tab to open when page get reload
    var tabs_local_storage = localStorage.getItem('tab');

    if(tabs_local_storage == null){
        tabs_local_storage = localStorage.setItem('tab','service_details');
    }
    $('#personal_info_tab').click(function(){
        tabs_local_storage = localStorage.setItem('tab','personal_info_tab');
    });
    $('#change_pass_tab').click(function(){
        tabs_local_storage = localStorage.setItem('tab','change_pass_tab');
    });
    $('#doc_tab').click(function(){
        tabs_local_storage = localStorage.setItem('tab','doc_tab');
    });
    $('#record_tab').click(function(){
        tabs_local_storage = localStorage.setItem('tab','record');
    });
    $('#service_details_tab').click(function(){
        tabs_local_storage = localStorage.setItem('tab','service_details');
    });
    $('#invoice_tab').click(function(){
        tabs_local_storage = localStorage.setItem('tab','invoices');
    });
    $('#remarks_tab').click(function(){
        tabs_local_storage = localStorage.setItem('tab','remarks');
    });
    $('#credit_limit_tab').click(function(){
        tabs_local_storage = localStorage.setItem('tab','credit_limit');
    });
    $('#edit_expiration_tab').click(function(){
        tabs_local_storage = localStorage.setItem('tab','edit_expiration');
    });
    $('#queue_tab').click(function(){
        tabs_local_storage = localStorage.setItem('tab','queue');
    });

    if(tabs_local_storage == 'personal_info_tab'){
        $('#personal_info_tab').click();
    }else if(tabs_local_storage == 'change_pass_tab'){
        $('#change_pass_tab').click();
    }else if(tabs_local_storage == 'doc_tab'){
        $('#doc_tab').click();
    }else if(tabs_local_storage == 'record'){
        $('#record_tab').click();
    }else if(tabs_local_storage == 'service_details'){
        $('#service_details_tab').click();
    }else if(tabs_local_storage == 'invoices'){
        $('#invoice_tab').click();
    }else if(tabs_local_storage == 'remarks'){
        $('#remarks_tab').click();
    }else if(tabs_local_storage == 'edit_expiration'){
        $('#edit_expiration_tab').click();
    }else if(tabs_local_storage == 'credit_limit'){
        $('#credit_limit_tab').click();
    }else if(tabs_local_storage == 'queue'){
        $('#queue_tab').click();
    }


    //display password
    $('.pass-show').click(function(e){
        e.preventDefault();
        $('#password').attr('type','text');
        $('#password_confirmation').attr('type','text');
        $(this).addClass('d-none');
        $('.pass-hide').removeClass('d-none');
    });
    //hide pass
    $('.pass-hide').click(function(e){
        e.preventDefault();
        $('#password').attr('type','password');
        $('#password_confirmation').attr('type','password')
        $(this).addClass('d-none');
        $('.pass-show').removeClass('d-none');
    });


    //change password form validation
    $('#change_pass_form').validate({
        rules:{
            password:{
                required:true,
                minlength:6,
                maxlength:12
            },
            password_confirmation:{
                required:true,
                minlength:6,
                maxlength:12,
                equalTo:'#password',
            }
        },
        highlight:function(element){
            $(element).addClass('is-invalid');        
        },
        unhighlight:function(element){
            $(element).removeClass('is-invalid');
            $(element).addClass('is-valid');
        },
    });

    $('#change_pass_form input[type="password"]').blur(function(){
        var id = $(this).attr('id');
        var validator = $('#change_pass_form').validate();
        validator.element('#'+id);
    });

    //document form validaton
    $('#document_form').validate({
        rules:{
            nic_front:{
                accept: "jpg,jpeg,png",
                maxsize: 2000000
            },
            nic_back:{
                accept: "jpg,jpeg,png",
                maxsize: 2000000
            },
            user_form_front:{
                accept: "jpg,jpeg,png",
                maxsize: 2000000
            },
            user_form_back:{
                accept: "jpg,jpeg,png",
                maxsize: 2000000
            },
        },
        highlight:function(element){
            $(element).addClass('is-invalid');        
        },
        unhighlight:function(element){
            $(element).removeClass('is-invalid');
            $(element).addClass('is-valid');
        },
        messages:{
            nic_front:{
                accept:"invalid file format allowed type ( jpg,jpeg,png )",
                maxsize:'Max size is 2MB'
            },
            nic_back:{
                accept:"invalid file format allowed type ( jpg,jpeg,png )",
                maxsize:'Max size is 2MB'
            },
            user_form_front:{
                accept:"invalid file format allowed type ( jpg,jpeg,png )",
                maxsize:'Max size is 2MB'
            },
            user_form_back:{
                accept:"invalid file format allowed type ( jpg,jpeg,png )",
                maxsize:'Max size is 2MB'
            },
        },
        errorPlacement:function(error,element){
            if(element.attr('name') == 'nic_front'){
                error.appendTo('.nic_front_err');
            }else if(element.attr('name') == 'nic_back'){
                error.appendTo('.nic_back_err');
            }else if(element.attr('name') == 'user_form_front'){
                error.appendTo('.user_form_front_err');
            }else if(element.attr('name') == 'user_form_back'){
                error.appendTo('.user_form_back_err');
            }else{
                element.after( error );
            }
        }
    });
    

    $('#document_form input[type="file"]').change(function(){
        var id = $(this).attr('id');
        var validator = $('#document_form').validate();
        validator.element('#'+id);
    });
    
    /*
        edit user personal information
    */
    
    //function to detect changes in input and select
    function detectChangesInPersonaInForm(){

        var break_out = true;

        $('#personal_info_form input').each(function(index,value){
            
            var id      = $(this).attr('id');
            var new_val = $(this).val();
            var old_val = $(this).data('ov');   

            if(id == 'name' || id == 'nic' || id == 'address'){
                if(new_val != old_val){
                    break_out = false;
                }
            }
        });

        //for mobile because mobile input is dyamic 
        if(break_out != false){
            if($('#mobile').val() != $('#mobile').data('ov')){
                break_out = false;
            }
        }

        //for city_id,area_id,subarea_id
        if(break_out != false){
            if($('#city_id').val().length > 0 && $('#city_id').val() != $('#city_id').data('ov')){
                break_out = false;
            }else if($('#area_id').val().length > 0 && $('#area_id').val() != $('#area_id').data('ov')){
                break_out = false;
            }
            //for subarea
            if($('#subarea_id').val() == null || $('#subarea_id').val() == 'undefined'){
                // console.log('null');
                break_out = false;
            }else if($('#subarea_id').val() != $('#subarea_id').data('ov')){
                // console.log('not');
                break_out = false;
            }
        }
        return break_out;
    }

    //display in put fields and fomr update options
    $('#edit_personal_info').click(function(e){
        e.preventDefault();
        page_loader('show');
        
        $('#personal_info_form input,select').removeClass('border-0');//remove border
        $('#personal_info_form input').removeAttr('disabled');//remove disabled attribute
        
        //if user is admin or superadmin dont remove disabeld attribute from city_id
        @if(auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'superadmin')
            $('#personal_info_form select').removeAttr('disabled');
        @else
            $('#personal_info_form select').not('#city_id').removeAttr('disabled');
        @endif
        //mobile html
        var mobile_html = '<div class="input-group ">'+
                            '<div class="input-group-prepend">'+
                                '<span class="input-group-text" id="basic-addon1">92</span>'+
                            '</div>'+
                            '<input type="text" name="mobile"  placeholder="Enter mobile no" value="{{ @substr($user_details->mobile,2) }}"'+ 'class="form-control" id="mobile" data-ov="{{ @substr($user_details->mobile,2) }}">'+
                            '<div class="mobile_err w-100"></div>'+
                        '</div>';
        $('#mobile_code').html(mobile_html);

        //set masking here because it will not work outside because the filed is dynamic
        $('#personal_info_form #mobile').mask('3000000000');

        $('#reset_btn').removeClass('d-none');                       
        page_loader('hide');
    });

    //area list
    $('#city_id').change(function(){
        var city_id = $(this).val();
        if(city_id.length != 0){
            var route   = "";
            route       = route.replace(':city_id',city_id);
            getAjaxRequests(route,'','GET',function(resp){
                $('#subarea_id').empty();
                $('#area_id').html('<option value="">Select area</option>'+resp.html);
            });
        }
    });

    //subareas
    $('#area_id').change(function(){
        var area_id = $(this).val();
        var route = "{{ route('admin.users.subareas',':id') }}";
        route = route.replace(':id',area_id);
        
        if(area_id.length != 0){
            getAjaxRequests(route, '', 'GET', function(resp){
                $('#subarea_id').html(resp.html);
            });
        }
    });
        

        //masking
        $('#nic').mask('00000-0000000-0');
        $('#personal_info_form #mobile').mask('3000000000');
        
        var admin_id = "{{ @$user_details->hashid }}";

        // form validation
        $('#personal_info_form').validate({
        rules:{
            name:{
                required:true,
                maxlength:50,                    
            },
            nic:{
                required:true,
                minlength:15,
                maxlength:15,
                remote:{
                    url  : "{{ route('admin.users.check_unique') }}",
                    type : "GET",
                    data : { 
                        column:'nic',
                        value:function(){
                            return $('#nic').val();
                        },
                        id    : admin_id,
                        },
                }
            },
            mobile:{
                required:true,
                minlength:10,
                maxlength:10,
                remote:{
                    url : "{{ route('admin.users.check_unique') }}",
                    type: "GET",
                    data :{
                        column : 'mobile',
                        value:function(){
                            return '92'+$('#mobile').val();
                        },
                        id : admin_id,    
                    },
                }
            },
            city_id:{
                required:true
            },
            address:{
                required:true
            },
            area_id:{
                required:false
            },
            subarea_id:{
                required:false
            }

        },
        highlight:function(element){
            $(element).addClass('is-invalid');
            
        },
        unhighlight:function(element){
            $(element).removeClass('is-invalid');
            $(element).addClass('is-valid');
        },
        messages:{
            nic:{
                remote:'NIC already in use',
            },
            mobile:{
                remote:'Number already in use',
            }
        },
        errorPlacement:function(error,element){
            if(element.attr('name') == 'mobile'){
                error.appendTo('.mobile_err');
            }else if(element.attr('name') == 'area_id'){
                error.appendTo('.area_err');
            }else if(element.attr('name') == 'subarea_id'){
                error.appendTo('.subarea_err');
            }else{
                element.after( error );
            }
        }
    });
    //validate each input field type of text,password,email
    $('input[type="text"],select').blur(function(){
        var id = $(this).attr('id');
        // alert(id);
        var validator = $('#personal_info_form').validate();
        validator.element('#'+id);
    });


    /*
        make the personal info form smart
    */
    $('#personal_info_form input').keyup(function(){
       var result =  detectChangesInPersonaInForm();
       if(result == false){
            $('#personal_info_form_submit').removeClass('d-none');
        }else{
            $('#personal_info_form_submit').addClass('d-none');
        }
    });
    //for mobile input because its dynamic 
    $(document).on('keyup','#mobile',function(){
        var result =  detectChangesInPersonaInForm();
        if(result == false){
            $('#personal_info_form_submit').removeClass('d-none');
        }else{
            $('#personal_info_form_submit').addClass('d-none');
        }
    });
    
    $('#personal_info_form select').change(function(){
        if($(this).val().length > 0){
             // execute after some time because when there is change in area it insert the options in subarea with out this subarea will be null
            setTimeout(function(){
                var result =  detectChangesInPersonaInForm();
                if(result == false){
                    $('#personal_info_form_submit').removeClass('d-none');
                }else{
                    $('#personal_info_form_submit').addClass('d-none');
                }
            }, 500)

        }
    });
    
    //remove city_id disable attribute when form get submit and all inputs filled
    $('#personal_info_form').bind('submit',function(){
        if($("#personal_info_form").valid()){
                $(this).find('#city_id').removeAttr('disabled');
            }          
    });

    //edit package
    $('#edit_package').click(function(){
        if("{{ auth()->user()->user_type == 'admin' }}"){
            $('#primary_package').addClass('d-none');
            $('#primary_package_ddl').removeClass('d-none');
        }else{
            $('#current_package').addClass('d-none');
            $('#current_package_ddl').removeClass('d-none');
        }
        $('#update_package').removeClass('d-none');//display update button
        $('#cancel_button').removeClass('d-none');//disaply cancel button
        $('#edit_package').addClass('d-none');//hiden edit button
        $('#disable_user').addClass('d-none');//hide disable button
        $('#enable_user').addClass('d-none');//hide enable button
        $('#kick_user').addClass('d-none');//hide kccik user button
        $('#remove_mac').addClass('d-none');//hide remove mac button
        $('#upgrade_package').addClass('d-none');//hide upgrade package button
        $('#reset_qouta').addClass('d-none');//hide rest qouta button
        $('.add_package').addClass('d-none');//hiden renew package button
    });

    //when click on update button submit the hidden form
    // $('#update_package').click(function(){
    //     $('#change_package_form').submit();
    // });

    //change in priamry package
    $('#primary_package_ddl').change(function(){
        $('#package_type').val('primary');
        $('#package_id').val($(this).val());
    });

    //change in priamry package
    $('#current_package_ddl').change(function(){
        $('#package_type').val('current');
        $('#package_id').val($(this).val());
    });
    //reload page when click on cancel button
    $('#cancel_button').click(function(){
        location.reload();
    }); 
    //change action when update package  
    $('#update_package').click(function(){
        // alert('done');
        var current_package = "{{ @$user_details->current_package->name }}";
        // var new_package     = $('#current_package_ddl').find(':selected').text();
        var new_package     = $('#primary_package_ddl').find(':selected').text();
        var nopopup         = false;
        var btn_txt         = 'yes, confirm it!';
        var data_msg        = '';

        if (!nopopup) {
            Swal.fire({
                title: "Want to change package?",
                text: (data_msg && data_msg != '') ? data_msg : "Your current package is "+current_package+"\n and yor new package is "+new_package,
                type: "warning",
                showCancelButton: !0,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: (btn_txt && btn_txt != '') ? btn_txt : "Yes, confirm it!"
            }).then(function (t) {
                if (t.value){
                    $('#change_package_form').submit();
                }
            });
        }
    });

    //package upgrade modal 
    $('#upgrade_package').click(function(e){
        e.preventDefault();
        // var route = $(this).attr('href');
        // var title = $(this).attr('title');
        // var status = $(this).data('status');
        // //set package modal submit value
        // if(status == 'registered'){
        //     status = 'Activate';
        // }else{
        //     status = 'Renew'
        // }
        var route = "{{ route('admin.packages.upgrade_user_package_modal',['id'=>$user_details->hashid]) }}";
        // alert(route);
        getAjaxRequests(route, '', 'GET', function(resp){
            $('.modal-body').html(resp.html);
            $('#package').modal('show');
            $('#package_modal_title').html('Upgrade User Package');
            $('#package_modal_submit').val(status);//change modal submit value
        });
        // $('#package').modal('show');
    });

        //package activation modal 
        $('.add_package').click(function(e){
        e.preventDefault();
        var route = $(this).attr('href');
        var title = $(this).attr('title');
        var status = $(this).data('status');
        //set package modal submit value
        if(status == 'registered'){
            status = 'Activate';
        }else{
            status = 'Renew'
        }
        getAjaxRequests(route, '', 'GET', function(resp){
            $('.user-package').html(resp.html);
            $('#user_package').modal('show');
            $('#user_package_modal_title').html(title);
            $('#user_package_modal_submit').val(status);//change modal submit value
        });
    });

    //validation on package activation modal
    $('#add_user_package_form').validate({
    rules : {
        package_id:{
            required:true,
            maxlength:191,
        }
    },
    highlight:function(element){
        if($(element).attr('name') == 'package_id'){
            $(element).addClass('is-invalid');
        }
    },
    unhighlight:function(element){
        if($(element).attr('name') == 'package_id'){
            $(element).removeClass('is-invalid');
            $(element).addClass('is-valid');
        }
    }
    });

    //remoev disabled attribute when form submit add_package_form
    $('#add_user_package_form').bind('submit',function(){
        $('#package_id').removeAttr('disabled');
        $('#userstatus').removeAttr('disabled');
        $('#username').removeAttr('disabled');
    });

    //when there in modal month_type
    $(document).on('change','#month_type',function(){
        var min_date = '';
        var max_date = '';
        var date = new Date();//js date
        // var current_date = "{{ date('Y-m-d') }}";//php date
        if($(this).val() == 'monthly'){
            min_date = '';
            max_date = '';
        }else if($(this).val() == 'half_month'){
            if(date.getDate() > 15){
                min_date = "{{ date('Y-m-01',strtotime('+1 month')) }}";
                max_date = "{{ date('Y-m-15',strtotime('+1 month')) }}";
            }else{
                min_date = "{{ date('Y-m-d',strtotime('+1 day')) }}";
                max_date = "{{ date('Y-m-15') }}";
            }

        }else if($(this).val() == 'full_month'){
            if(date.getDate() > 29){
                min_date = "{{ date('Y-m-1',strtotime('+1 month')) }}";
                max_date = "{{ date('Y-m-t',strtotime('+1 month')) }}";
            }else{
                min_date = "{{ date('Y-m-d',strtotime('+1 day')) }}";
                max_date = "{{ date('Y-m-t') }}";
            }
        }

        $('#calendar').attr('min',min_date);
        $('#calendar').attr('max',max_date);
        //if billing cycle is monthy then hide the calendar otherwise show the calendar
        if($(this).val() == 'monthly'){
            $('#calendar_form').addClass('d-none');
        }else{
            $('#calendar_form').removeClass('d-none');
        }

        $('#new_expiration').html($('#hidden_new_expiry_date').val());

    });
    //when there is change in calendar date
    $(document).on('change','#calendar',function(){
        //make date using js
        var expiration_date = new Date($('#calendar').val());
        var date            = expiration_date.getDate();
        var month           = expiration_date.toLocaleString('en-us', { month: 'short' });
        var year            = expiration_date.getFullYear();
        
        var new_expiration_date = date+'-'+month+'-'+year+' 12:00';//concate date

        $('#new_expiration').html(new_expiration_date);//update the new_expiration date
        
    });

    //user passowr modal
    $('#user_password').click(function(){
        var pass = $(this).data('pass');
    
        $('.password_modal_body').html(pass);
        $('#user_password_modal').modal('show');
    });

    //remarks form validation
    $('#remarks_form').validate({
        rules:{
            remark:{
                required:true,
                maxlength:250,
            }
        }
    });

    $(document).on('change', '#package_id', function(){
        var package_id = $(this).val();
        var user_id = $('#user_id_input').val();
        var route    = "{{ route('admin.packages.get_upgrade_package_price') }}";
        getAjaxRequests(route, {user_id:user_id,package_id:package_id}, 'GET', function(resp){
            $('#upgrade_package_price_tab').removeClass('d-none');
            $('#upgrade_package_price').html(resp.upgrade_package_price);
        });
    });
    
</script>
@endsection
@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Settings</li>
                    {{-- <li class="breadcrumb-item active">Settings</li> --}}
                </ol>
            </div>
            <h4 class="page-title">{{ isset($edit_setting) ? 'Edit' : 'Add'}} Settings</h4>
        </div>
    </div>
</div>



<ul class="nav nav-tabs" id="myTab" role="tablist">
    @can('view-general-settings')
    <li class="nav-item" role="presentation">
      <a class="nav-link active" id="general_settings_tab" data-toggle="tab" href="#general_settings" role="tab" aria-controls="general_settings" aria-selected="true" >General Settings</a>
    </li>
    @endcan
    @can('enabled-settings-locations')
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="locations_settings_tab" data-toggle="tab" href="#locations" role="tab" aria-controls="locations_settings" aria-selected="true" >Locations</a>
        </li>
    @endcan
    @if(auth()->user()->user_type == 'superadmin')
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="maintenance_tab" data-toggle="tab" href="#maintenance" role="tab" aria-controls="maintenance" aria-selected="true" >Maintenance</a>
    </li>
    @endif 
    @if(auth()->user()->user_type == 'superadmin' || auth()->user()->user_type == 'admin')
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="acl_tab" data-toggle="tab" href="#acl" role="tab" aria-controls="maintenance" aria-selected="true" >Admin ACL</a>
    </li>
    @endif  
    {{-- <li class="nav-item" role="presentation">
        <a class="nav-link" id="city_tab" data-toggle="tab" href="#city" role="tab" aria-controls="city" aria-selected="false">City</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="location_tab" data-toggle="tab" href="#location" role="tab" aria-controls="location" aria-selected="false">Areas</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="subareas_tab" data-toggle="tab" href="#subarea" role="tab" aria-controls="location" aria-selected="false">Subareas</a>
    </li> --}}
  </ul>
  
  <div class="tab-content pt-0" id="myTabContent">
    @can('view-general-settings')
    <div class="tab-pane fade show active" id="general_settings" role="tabpanel" aria-labelledby="general_settings-tab">
        <div class="row">
            <div class="col-lg-12">
                <div class="card-box">
                    <h4 class="header-title m-t-0">{{ isset($edit_setting) ? 'Edit' : 'Add'}} Settings</h4>
                    <p class="text-muted font-14 m-b-20">
                        Here you can {{ isset($edit_setting) ? 'Edit' : 'Add'}} Settings.
                    </p>
        
                    <form action="{{ route('admin.settings.store') }}" class="ajaxForm" method="post" enctype="multipart/form-data" novalidate id="form">
                        @csrf
        
                        <div class="row">
                            <div class="form-group mb-3 col-md-6">
                                <label for="company_name">Company Name<span class="text-danger">*</span></label>
                                <input type="text" name="company_name"  placeholder="Enter company name" value="{{ @$edit_setting->company_name }}" class="form-control" id="company_name" @if(auth()->user()->user_type != 'superadmin') disabled @endif>
                            </div>
        
                            <div class="form-group mb-3 col-md-6">
                                <label for="slogan">Slogan<span class="text-danger">*</span></label>
                                <input type="text" name="slogan"  placeholder="Enter slogan" value="{{ @$edit_setting->slogan }}" class="form-control" id="username" @if(auth()->user()->user_type != 'superadmin') disabled @endif>
                            </div>
                        </div>    
        
                        <div class="row">
                            <div class="form-group mb-3 col-md-6">
                                <label for="mobile">Mobile<span class="text-danger">*</span></label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                      <span class="input-group-text" id="basic-addon1">92</span>
                                    </div>
                                    <input type="text" name="mobile"  placeholder="Enter mobile no" value="{{ @$edit_setting->mobile }}" class="form-control" id="mobile" @if(auth()->user()->user_type != 'superadmin') disabled @endif>
                                    <div class="mobile_err w-100"></div>
                                </div>
                                
                            </div>
        
                            <div class="form-group mb-3 col-md-6">
                                <label for="email">Email<span class="text-danger">*</span></label>
                                <input type="email" name="email"  placeholder="Enter email" value="{{ @$edit_setting->email }}" class="form-control" id="email" @if(auth()->user()->user_type != 'superadmin') disabled @endif>
                            </div>
                        </div>    
        
                        <div class="row">
                            <div class="form-group mb-3 col-md-6">
                                <label for="address ">Address<span class="text-danger">*</span></label>
                                <input type="text" name="address"  placeholder="Enter Address" value="{{ @$edit_setting->address }}" class="form-control" id="address" @if(auth()->user()->user_type != 'superadmin') disabled @endif>
                            </div>
        
                            <div class="form-group mb-3 col-md-6">
                                <label for="email">Country<span class="text-danger">*</span></label>
                                <select class="form-control"  name="country" id="country" @if(auth()->user()->user_type != 'superadmin') disabled @endif>
                                    <option value="">Select Country</option>
                                    <option value="pakistan" @if(@$edit_setting->country == 'pakistan') selected @endif>Pakistan</option>
                                </select>
                            </div>
                        </div>
        
                        
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="zipcode ">Zipcode<span class="text-danger">*</span></label>
                                <input type="text" class="form-control"  placeholder="Enter zipcode" value="{{ @$edit_setting->zipcode }}" name="zipcode" id="zipcode" @if(auth()->user()->user_type != 'superadmin') disabled @endif>
                            </div>
        
                            <div class="form-group col-md-6">
                                <label for="copyright">Copyright</label>
                                <input type="text" class="form-control"  placeholder="Enter copyright" value="{{ @$edit_setting->copyright }}" name="copyright" id="copyright" @if(auth()->user()->user_type != 'superadmin') disabled @endif>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="logo">Logo</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="logo" id="logo" accept=".jpg, .png, .jpeg" onchange="showPreview('preview_logo')" @if(auth()->user()->user_type != 'superadmin') disabled @endif>
                                        <label class="custom-file-label profile_img_label" for="logo">Choose Logo file</label>
                                    </div>
                                    <div class="logo_erro w-100"></div>
                                    <img id="preview_logo" src="@if(@file_exists($edit_setting->logo)) {{ asset($edit_setting->logo) }} @endif"  class="@if(empty(@$edit_setting)) d-none  @endif" width="100px" height="100px"/>
                                </div>
                            </div>
        
                            <div class="form-group col-md-6">
                                <label for="favicon">Favicon</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input"  name="favicon" id="favicon" accept=".jpg, .png, .jpeg" onchange="showPreview('preview_favicon')" @if(auth()->user()->user_type != 'superadmin') disabled @endif>
                                        <label class="custom-file-label profile_img_label" for="favicon">Choose Logo file</label>
                                    </div>
                                    <div class="favicon_err w-100"></div>
                                    <img id="preview_favicon" src="@if(@file_exists($edit_setting->favicon)) {{ asset($edit_setting->favicon) }} @endif"  class="@if(empty($edit_setting)) d-none  @endif" width="100px" height="100px"/>
                                </div>
                            </div>
                        </div>
        
                        
                        @if(auth()->user()->user_type == 'superadmin')
                            <div class="form-group mb-3 text-right">
                                <input type="hidden" value="{{ @$edit_setting->hashid }}" name="setting_id">
                                <button class="btn btn-primary waves-effect waves-light" type="submit">
                                    {{ (isset($edit_setting)) ? 'Update' : 'Add' }}
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endcan
    @can('enabled-settings-locations')
        <div class="tab-pane fade" id="locations" role="tabpanel" aria-labelledby="locations-tab">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">

                        <ul class="nav nav-tabs" id="mmyTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="city_tab" data-toggle="tab" href="#city" role="tab" aria-controls="city" aria-selected="true">City</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="location_tab" data-toggle="tab" href="#location" role="tab" aria-controls="location" aria-selected="false">Areas</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="subareas_tab" data-toggle="tab" href="#subarea" role="tab" aria-controls="location" aria-selected="false">Subareas</a>
                            </li>
                        </ul>


                        <div class="tab-pane fade show active d-none" id="city" role="tabpanel" aria-labelledby="location-tab">
                            <div class="row">
                                @can('add-settings-locations')
                                    <div class="col-lg-12">
                                        <div class="card-box">
                                            <h4 class="header-title m-t-0">{{ isset($is_update) ? 'Edit' : 'Add'}} City</h4>
                                            <p class="text-muted font-14 m-b-20">
                                                Here you can {{ isset($is_update) ? 'edit' : 'add'}} City.
                                            </p>
                                
                                            <form action="{{ route('admin.cities.store') }}" class="ajaxForm" method="post" novalidate id="city_form">
                                                @csrf
                                
                                                <div class="row">
                                
                                                    <div class="form-group col-md-11">
                                                        <label for="city_name">City Name</label>
                                                        <input type="text" class="form-control"  placeholder="Enter Area Name" value="{{ @$edit_city->city_name }}" name="city_name" id="city_name">
                                                    </div>
                        
                                                    <div class="col-md-1 mt-3 form-group text-right">
                                                        <input type="hidden" value="{{ @$edit_city->hashid }}" name="city_id">
                                                        <button class="btn btn-primary area_submit waves-effect waves-light" type="submit">
                                                            {{ (isset($is_update_city)) ? 'Update' : 'Add' }}
                                                        </button>
                                                    </div>
                                                </div>    
                                            </form>
                                        </div>
                                    </div>
                                @endcan
                                {{-- @can('all-settings-locations') --}}
                                <div class="col-lg-12">
                                    <h4 class="page-title"> Cities List </h4>
                            
                                    <div class="card-box">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h4 class="header-title">Cities</h4>
                                            {{-- <a href="{{ route('admin.staffs.add') }}" class="btn btn-sm btn-primary">Add Staff</a> --}}
                                        </div>
                                        <p class="sub-header">Following is the list of all the cities.</p>
                                        <table class="table dt_table table-bordered w-100 nowrap" id="laravel_datatable">
                                            <thead>
                                                <tr>
                                                    <th width="20">S.No</th>
                                                    <th>City</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($cities AS $city)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $city->city_name }}</td>
                                                        <td>
                                                            @can('edit-settings-locations')
                                                                <a href="{{ route('admin.cities.edit',['id'=>$city->hashid]) }}" class="btn btn-warning btn-xs waves-effect waves-light">
                                                                    <span class="btn-label"><i class="fa fa-edit"></i></span>Edit
                                                                </a>
                                                            @endcan
                                                            @can('delete-settings-locations')
                                                                <button type="button" onclick="ajaxRequest(this)" data-url="{{ route('admin.cities.delete',['id'=>$city->hashid]) }}" class="btn btn-danger btn-xs waves-effect waves-light">
                                                                    <span class="btn-label"><i class="icon-trash"></i></span>Delete
                                                                </button>
                                                            @endcan
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        {{-- <span class="float-right">{{ $areas->links()  }} --}}
                                        </span>
                                    </div>
                                </div>
                                {{-- @endcan --}}
                            </div>
                        </div>

                        <div class="tab-pane fade d-none" id="location" role="tabpanel" aria-labelledby="location-tab">
                            <div class="row">
                                @can('add-settings-locations')
                                    <div class="col-lg-12">
                                        <div class="card-box">
                                            <h4 class="header-title m-t-0">{{ isset($is_update) ? 'Edit' : 'Add'}} Area</h4>
                                            <p class="text-muted font-14 m-b-20">
                                                Here you can {{ isset($is_update) ? 'edit' : 'add'}} Area.
                                            </p>
                                
                                            <form action="{{ route('admin.areas.store') }}" class="ajaxForm" method="post" novalidate id="area_forma">
                                                @csrf
                                                <div class="row">
                                                    <div class="form-group mb-4 col-md-6">
                                                        <label for="city_id">City<span class="text-danger">*</span></label>
                                                        <select class="form-control" name="city_id" id="city_id">
                                                            <option value="">Select City</option>
                                                            @foreach($cities AS $city)
                                                                <option value="{{ $city->hashid }}" @if(@$edit_area->city_id == $city->id) selected @endif>{{ $city->city_name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="city_err w-100"></div>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="area_name">Area Name</label>
                                                        <input type="text" class="form-control"  placeholder="Enter Area Name" value="{{ @$edit_area->area_name }}" name="area_name" id="area_name">
                                                    </div>
                        
                                                    <div class="col-12">
                                                        <div class="form-group text-right float-right">
                                                            <input type="hidden" value="{{ @$edit_area->hashid }}" name="area_edit_id">
                                                            <button class="btn btn-primary area_submit waves-effect waves-light" type="submit">
                                                                {{ (isset($is_update_area)) ? 'Update' : 'Add' }}
                                                            </button>
                                                            @if(isset($is_update_area))
                                                                <button class="btn btn-danger" id="cancel">
                                                                    Cancel
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                        
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endcan
                                {{-- @can('all-settings-locations') --}}
                                    <div class="col-lg-12">
                                        <h4 class="page-title"> Area List </h4>
                                
                                        <div class="card-box">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <h4 class="header-title">Areas</h4>
                                                {{-- <a href="{{ route('admin.staffs.add') }}" class="btn btn-sm btn-primary">Add Staff</a> --}}
                                            </div>
                                            <p class="sub-header">Following is the list of all the areas.</p>
                                            <table class="table dt_table table-bordered w-100 nowrap" id="laravel_datatable">
                                                <thead>
                                                    <tr>
                                                        <th width="20">S.No</th>
                                                        <th>City</th>
                                                        <th>Area</th>
                                                        <th>Type</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($areas AS $area)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ @$area->city->city_name}}</td>
                                                            <td>{{ $area->area_name }}</td>
                                                            <td><span class="badge badge-success">Main Area</span></td>
                                                            <td>
                                                                @can('edit-settings-locations')
                                                                    <a href="{{ route('admin.areas.edit',['id'=>$area->hashid]) }}" class="btn btn-warning btn-xs waves-effect waves-light">
                                                                        <span class="btn-label"><i class="fa fa-edit"></i></span>Edit
                                                                    </a>
                                                                @endcan
                                                                @can('delete-settings-locations')
                                                                    <button type="button" onclick="ajaxRequest(this)" data-url="{{ route('admin.areas.delete',['id'=>$area->hashid,'type'=>$area->type]) }}" class="btn btn-danger btn-xs waves-effect waves-light">
                                                                        <span class="btn-label"><i class="icon-trash"></i></span>Delete
                                                                    </button>
                                                                @endcan
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            {{-- <span class="float-right">{{ $areas->links()  }} --}}
                                            </span>
                                        </div>
                                    </div>
                                {{-- @endcan --}}
                            </div>
                        </div>

                        <div class="tab-pane fade d-none" id="subarea" role="tabpanel" aria-labelledby="location-tab">
                            <div class="row">
                                @can('add-settings-locations')
                                <div class="col-lg-12">
                                    <div class="card-box">
                                        <h4 class="header-title m-t-0">{{ isset($is_update) ? 'Edit' : 'Add'}} Subarea</h4>
                                        <p class="text-muted font-14 m-b-20">
                                            Here you can {{ isset($is_update) ? 'edit' : 'add'}} Subarea.
                                        </p>
                            
                                        <form action="{{ route('admin.areas.store_subarea') }}" class="ajaxForm" method="post" novalidate id="subarea_form">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group mb-4 col-md-4">
                                                    <label for="city_id">City<span class="text-danger">*</span></label>
                                                    <select class="form-control" name="city_id" id="city_id">
                                                        <option value="">Select City</option>
                                                        @foreach($cities AS $city)
                                                            <option value="{{ $city->hashid }}" @if(@$edit_subarea->city_id == $city->id) selected @endif>{{ $city->city_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="city_err w-100"></div>
                                                </div>
                    
                                                <div class="form-group mb-4 col-md-4">
                                                    <label for="area_id">Area<span class="text-danger">*</span></label>
                                                    <select class="form-control" name="area_id" id="area_id">
                                                        <option value="">Select Area</option>
                                                        @if(isset($is_update_subarea))
                                                            @foreach($areas_list AS $area)
                                                                <option value="{{ $area->hashid }}" @if(@$edit_subarea->area_id == $area->id) selected @endif>{{ $area->area_name }}</option>
                                                            @endforeach
                                                        @endif
                    
                                                    </select>
                                                    <div class="city_err w-100"></div>
                                                </div>
                    
                                                <div class="form-group col-md-4">
                                                    <label for="area_name">Subarea Name</label>
                                                    <input type="text" class="form-control"  placeholder="Enter Subarea Name" value="{{ @$edit_subarea->area_name }}" name="subarea_name" id="subarea_name">
                                                </div>
                    
                                                <div class="col-12">
                                                    <div class="form-group text-right float-right">
                                                        <input type="hidden" value="{{ @$edit_subarea->hashid }}" name="edit_subarea_id">
                                                        <button class="btn btn-primary area_submit waves-effect waves-light" type="submit">
                                                            {{ (isset($is_update_subarea)) ? 'Update' : 'Add' }}
                                                        </button>
                                                        @if(isset($is_update_subarea))
                                                            <button class="btn btn-danger" id="subarea_cancel">
                                                                Cancel
                                                            </button>
                                                        @endif
                                                    </div>
                    
                                                </div>
                    
                                            </div>    
                                            
                                        </form>
                                    </div>
                                </div>
                                @endcan

                                {{-- @can('all-settings-locations') --}}
                                    <div class="col-lg-12">
                                        <h4 class="page-title"> Subareas List </h4>
                                
                                        <div class="card-box">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <h4 class="header-title">Subareas</h4>
                                                {{-- <a href="{{ route('admin.staffs.add') }}" class="btn btn-sm btn-primary">Add Staff</a> --}}
                                            </div>
                                            <p class="sub-header">Following is the list of all the Subareas.</p>
                                            <table class="table dt_table table-bordered w-100 nowrap" id="laravel_datatable">
                                                <thead>
                                                    <tr>
                                                        <th width="20">S.No</th>
                                                        <th>City</th>
                                                        <th>Main Area</th>
                                                        <th>Subarea</th>
                                                        <th>Type</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($subareas AS $subarea)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ @$subarea->city->city_name}}</td>
                                                            <td>{{ @$subarea->area->area_name }}</td>
                                                            <td>{{ $subarea->area_name }}</td>
                                                            <td><span class="badge badge-success">Subarea</span></td>
                                                            <td>
                                                                @can('edit-settings-locations')
                                                                    <a href="{{ route('admin.areas.edit_subarea',['id'=>$subarea->hashid]) }}" class="btn btn-warning btn-xs waves-effect waves-light">
                                                                        <span class="btn-label"><i class="fa fa-edit"></i></span>Edit
                                                                    </a>
                                                                @endcan 
                                                                @can('delete-settings-locations')
                                                                    <button type="button" onclick="ajaxRequest(this)" data-url="{{ route('admin.areas.delete_subarea',['id'=>$subarea->hashid,'type'=>$subarea->type]) }}" class="btn btn-danger btn-xs waves-effect waves-light">
                                                                        <span class="btn-label"><i class="icon-trash"></i></span>Delete
                                                                    </button>
                                                                @endcan
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            </span>
                                        </div>
                                    </div>
                                {{-- @endcan --}}
                            </div>
                        </div>





                    </div>
                </div>
            </div>
        </div>
    @endcan
    @if(auth()->user()->user_type == 'superadmin')
        <div class="tab-pane fade show active" id="maintenance" role="tabpanel" aria-labelledby="maintenance">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        <form action="{{ route('admin.settings.mode') }}" method="POST" class="ajaxForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="">Fully Maintenance Mode</label>
                                    <select class="form-control" name="full_maintenance" id="full_maintenance">
                                        <option value="on" @if($edit_setting->full_maintenance == 'off') selected @endif>ON</option>
                                        <option value="off" @if($edit_setting->full_maintenance == 'off') selected @endif>OFF</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="submit" class="btn btn-primary mt-2 float-right" value="Update">
                                    <input type="hidden" name="setting_id" value="{{ $edit_setting->hashid }}">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>            
        </div>
    @endif

    <div class="tab-pane fade show active" id="acl" role="tabpanel" aria-labelledby="general_settings-tab">
        <div class="row">
            <div class="col-lg-12">
                <div class="card-box">
                    <form action="{{ route('admin.acls.store') }}" method="POST" class="ajaxForm">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-5">
                                <label for="">Users</label>
                                <select name="admin_id" id="admin_id" class="form-control">
                                    <option value="">Select Admin</option>
                                    @foreach($admins AS $admin)
                                        <option value="{{ $admin->hashid }}">{{ $admin->name }} ({{ $admin->username }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-5">
                                <label for="">IP</label>
                                {{-- <input type="text" class="form-control" minlength="7" maxlength="15" size="15" pattern="^((\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.){3}(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$"> --}}
                                <input type="text" class="form-control" name="ip" id="ip">

                            </div>
                            <div class="col-md-1">
                                <input type="submit" class="form-control btn-primary mt-3" value="Add">
                            </div>
                        </div>

                    </form>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card-box">
                    <table class="table table bordered table-bordered" id="acl_table">
                        <thead>
                            <th>S.No</th>
                            <th>Name</th>
                            <th>IP</th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

  </div>


  </div>



@endsection
@section('page-scripts')
@include('admin.partials.datatable', ['load_swtichery' => true])
<script>
   
   //general settings JS code

    //which tab to open when page get reload
    var tabs_local_storage = localStorage.getItem('tab');

    if(tabs_local_storage == null){
        tabs_local_storage = localStorage.setItem('tab','general_settings');
    }
    $('#general_settings_tab').click(function(){
        tabs_local_storage = localStorage.setItem('tab','general_settings');
    });
    $('#location_tab').click(function(){//location tab means area tab
        tabs_local_storage = localStorage.setItem('tab','location');
        $('#location').removeClass('d-none');
        $('#city').addClass('d-none');
        $('#subarea').addClass('d-none');
    });
    $('#city_tab').click(function(){
        tabs_local_storage = localStorage.setItem('tab','city');
        $('#city').removeClass('d-none');
        $('#location').addClass('d-none');
        $('#subarea').addClass('d-none');
    });
    $('#subareas_tab').click(function(){
        tabs_local_storage = localStorage.setItem('tab','subareas');
        $('#subarea').removeClass('d-none');
        $('#city').addClass('d-none');
        $('#location').addClass('d-none');
    });
    $('#locations_settings_tab').click(function(){
        tabs_local_storage = localStorage.setItem('tab','city');
        $('#city_tab').click();
    });

    $('#maintenance_tab').click(function(){
        tabs_local_storage = localStorage.setItem('tab','maintenance');
    });
    $('#acl_tab').click(function(){
        tabs_local_storage = localStorage.setItem('tab','acl');
    });


        if(tabs_local_storage == 'location'){
            $('#locations_settings_tab').click();
            $('#location_tab').click();
        }else if(tabs_local_storage == 'city'){
            $('#locations_settings_tab').click();
            $('#city_tab').click();
        }else if(tabs_local_storage == 'subareas'){
            $('#locations_settings_tab').click();
            $('#subareas_tab').click();
        }else if(tabs_local_storage == 'maintenance'){
            $('#maintenance_tab').click();
        }else if(tabs_local_storage == 'acl'){
            $('#acl_tab').click();
        }

    //mobile masking
    $('#mobile').mask('3000000000');

    //cancel button
    $('#cancel').click(function(){
        $('form').trigger('reset');
        window.location.href="{{ route('admin.settings.index') }}"
    });


    var setting_id = "{{ @$edit_setting->hashid }}";
    
    //live validation
    $('#form').validate({
        rules:{
            company_name:{
                required:true,
                maxlength:190
            },
            slogan:{
                required:true,
                maxlength:190
            },
            mobile:{
                required:true,
                minlength:10,
                maxlength:10
            },
            email:{
                required:true,
                maxlength:190
            },
            address:{
                required:true
            },
            country:{
                required:true
            },
            zipcode:{
                required:true,
                digits:true
            },
            copyright:{
                required:true
            },
            logo:{
                required:function(){
                        return setting_id.length == 0;
                    },
                accept: "jpg,jpeg,png",
                maxsize: 1000000
            },
            favicon:{
                required:function(){
                        return setting_id.length == 0;
                    },
                accept: "jpg,jpeg,png",
                maxsize: 1000000
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
            logo:{
                accept:"invalid file format allowed type ( jpg,jpeg,png )",
                maxsize:'Max size is 1MB'
            },
            favicon:{
                accept:"invalid file format allowed type ( jpg,jpeg,png )",
                maxsize:'Max size is 1MB'
            }
        },
        errorPlacement:function(error,element){
            if(element.attr('id') == 'logo'){
                error.appendTo('.logo_erro');
            }else if(element.attr('id') == 'favicon'){
                error.appendTo('.favicon_err');
            }else if(element.attr('id') == 'mobile'){
                error.appendTo('.mobile_err');
            }else{
                element.after(error);
            }
        }
    });
    //validarte input type text,email,select
    $('#form input[type="text"],input[type="email"],select').blur(function(){
        var id = $(this).attr('id');
        //do not validate inputs or dropdowns of this ids because it was conflicting with the #form
        if(id != 'area_id' && id != 'city_id' && id != 'area_name'){
            var validator = $('#form').validate();
            validator.element('#'+id);
        }
    });

    $('#form input[type="file"]').change(function(){
        var id = $(this).attr('id');
        var validator = $('#form').validate();
        validator.element('#'+id);
    });

    //are js code

    //display area list when city id get selected
    // $('#city_id').change(function(){
    //     var city_id = $(this).val();
    //     var route = "{{ route('admin.areas.area_list',':city_id') }}";
    //     route = route.replace(':city_id',city_id);
        
    //     if(city_id.length != 0){
    //         getAjaxRequests(route,'','GET',function(resp){
    //         $('#area_id').html(resp.html);
    //         $('#area_id').addClass('is-valid');
    //         });
    //     }else{
    //         $('#area_id').removeClass('is-valid');
    //         $('#area_id').addClass('is-invalid');
    //         $('#area_id').html('<option value="">Select Area</option>');
    //     }

    // });

    //area validation
    $('#area_forma').validate({
        rules:{
            city_id:{
                required:true
            },
            area_name:{
                required:true,
                remote:{
                    url : "{{ route('admin.areas.check_unique_area_name') }}",
                    type:"GET",
                    data:{
                        city_id:function(){
                            return $('#city_id option:selected').val()
                        },
                        edit_area_id:function(){
                            return  "{{ @$edit_area->hashid }}";
                        }
                    }
                }
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
            area_name:{
                remote:'Area name already exists',
            }
        }
    });
    //need to validate in this way because it was conflicting with the #form validation rules
    $('#city_id').blur(function(){
        var validator = $('#area_forma').validate();
        validator.element('#city_id');
    });
    $('#area_forma #area_id').blur(function(){
        var validator = $('#area_forma').validate();
        validator.element('#area_id');
    });
    $('#area_name').blur(function(){
        var validator = $('#area_forma').validate();
        validator.element('#area_name');
    });
    //when city name change then again validate the area name
    $('#city_id').change(function(){
        var validator = $('#area_forma').validate();
        validator.element('#area_name');
    });



   //city validation
   $('#city_form').validate({
       rules:{
           city_name :{
               required:true,
               minlength:2,
               maxlength:190,
               remote:{
                   url : "{{ route('admin.cities.check_unique_name') }}",
                   type: "GET",
                   data:{
                       id:"{{ @$edit_city->hashid }}"
                   }

               }
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
           city_name:{
               remote:"City name alreay exists",
           }
       }
   });

   $('#city_name').blur(function(){
        var validator = $('#city_form').validate();
        validator.element('#city_name');
    });


    //subareas 

    //display area list when city id get selected
    $('#subarea_form #city_id').change(function(){
        var city_id = $(this).val();
        var route = "{{ route('admin.areas.area_list',':city_id') }}";
        route = route.replace(':city_id',city_id);
        
        if(city_id.length != 0){
            getAjaxRequests(route,'','GET',function(resp){
            $('#subarea_form #area_id').html(resp.html);
            $('#subarea_form #area_id').addClass('is-valid');
            });
        }else{
            $('#subarea_form #area_id').removeClass('is-valid');
            $('#subarea_form #area_id').addClass('is-invalid');
            $('#subarea_form #area_id').html('<option value="">Select Area</option>');
        }

    });

        //subarea validation
        $('#subarea_form').validate({
        rules:{
            city_id:{
                required:true
            },
            area_id:{
                required:true
            },
            // subarea_name:{
            //     required:true
            // },
            subarea_name:{
                required:true,
                remote:{
                    url : "{{ route('admin.areas.check_unique_subarea') }}",
                    type:"GET",
                    data:{
                        city_id:function(){
                            return $('#subarea_form #city_id option:selected').val()
                        },
                        area_id:function(){
                            return $('#subarea_form #area_id option:selected').val()
                        },
                        edit_subarea_id:"{{ @$edit_subarea->hashid }}"
                    }
                }
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
            subarea_name:{
                remote:'Area name already exists',
            }
        }
    });
    //validate form
    $('#subarea_form input,select').blur(function(){
        var id = $(this).attr('id');
        var validator = $('#subarea_form').validate();
        validator.element('#'+id);
    });



    //cancel button
    $('#subarea_cancel').click(function(){
        $('form').trigger('reset');
        window.location.href="{{ route('admin.settings.index') }}"
    });

    //datatable on admin acls
    $(document).ready(function(){
        var table = $('#acl_table').DataTable({
                    processing: true, 
                    serverSide: true,
                    "order": [[ 0, "desc" ]],
                    "pageLength": 300,
                    "lengthMenu": [300,500,1000,1500],
                    "dom": '<"top"ifl<"clear">>rt<"bottom"ip<"clear">>',

                    ajax:{
                        url : "{{ route('admin.acls.index') }}",
                        data:function(d){
                                    d.username        = $('#username').val(),
                                    d.added_by        = $('#added_by').val(),
                                    d.from_date       = $('#from_date').val(),
                                    d.to_date         = $('#to_date').val(),
                                    d.search          = $('input[type="search"]').val()
                        },
                    },
                    
                    columns : [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable:true,searchable:false},
                        {data:'name', name:'admin.name', orderable:false, searchable:true},  
                        {data:'ip', name:'admin_acls.ip', orderable:false, searchable:true},  
                    ],
                });
        });
        
        //add select2 on admin acl dropdowe
        $('#admin_id').select2({
            placeholder : 'Select areas',
        });
                

</script>
@endsection
@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.staffs') }}">Staffs</a></li>
                    <li class="breadcrumb-item active">{{ isset($staff) ? 'Edit' : 'Add'}} Staff</li>
                </ol>
            </div>
            <h4 class="page-title">{{ isset($staff) ? 'Edit' : 'Add'}} Staff</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            <h4 class="header-title m-t-0">{{ isset($staff) ? 'Edit' : 'Add'}} Staff</h4>
            <p class="text-muted font-14 m-b-20">
                Here you can {{ isset($staff) ? 'edit' : 'add'}} staff users.
            </p>

            <form action="{{ route('admin.staffs.save') }}" class="ajaxForm" method="post" enctype="multipart/form-data" novalidate>
                @csrf
                @if(isset($staff) && $staff->image)
                    <div class="form-group my-3">
                        <img src="{{check_file($staff->image, 'user')}}" alt="{{ $staff->full_name ?? 'No Image' }}" class="img-fluid fit-image avatar-xl rounded-circle">
                    </div>
                @endif
                <div class="form-group mb-3">
                    <label>Profile Image</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="profile_img" id="profile_img" accept=".gif, .jpg, .png, .jpeg">
                            <label class="custom-file-label profile_img_label" for="profile_img">Choose file</label>
                        </div>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label for="firstname">First Name<span class="text-danger">*</span></label>
                    <input type="text" name="firstname" parsley-trigger="change" data-parsley-required placeholder="Enter first name" class="form-control" id="firstname" value="{{ $staff->firstname ?? '' }}">
                </div>
                <div class="form-group mb-3">
                    <label for="lastname">Last Name<span class="text-danger">*</span></label>
                    <input type="text" name="lastname" parsley-trigger="change" data-parsley-required placeholder="Enter last name" class="form-control" id="lastname" value="{{ $staff->lastname ?? '' }}">
                </div>

                <div class="form-group mb-3">
                    <label for="name">Display Name</label>
                    <input type="text" name="name" placeholder="Enter display name" class="form-control" id="name" value="{{ $staff->name ?? '' }}">
                </div>

                <div class="form-group mb-3">
                    <label for="email">Email<span class="text-danger">*</span></label>
                    <input type="text" @if (!isset($staff)) name="email" parsley-trigger="change" data-parsley-required @else disabled @endif placeholder="Enter email" class="form-control" id="email" value="{{ $staff->email ?? '' }}">
                </div>

                @if(!isset($staff))
                <div class="form-group mb-3">
                    <label for="password">Password<span class="text-danger">*</span></label>
                    <input type="password" name="password" parsley-trigger="change" data-parsley-required placeholder="Enter password atleast 8 charactes long" class="form-control" id="password" value="{{ $staff->last_name ?? '' }}">
                </div>
                @else
                <input type="hidden" value="{{ $staff->hashid }}" name="staff_id" />
                @endif

                <div class="form-group mb-3">
                    <label for="user_type">User Type<span class="text-danger">*</span></label>
                    <select class="form-control" onchange="permission_types(this.value)" data-parsley-required name="user_type" id="user_type">
                        <option value="">Select User Type</option>
                        <option {{isset($staff) && $staff->user_type == 'admin' ? 'selected' : ''}} value="admin">Admin</option>
                        <option {{isset($staff) && $staff->user_type == 'author' ? 'selected' : ''}} value="author">Author</option>
                        <option {{isset($staff) && $staff->user_type == 'normal' ? 'selected' : ''}} value="normal">Normal</option>
                    </select>
                </div>

                <div class="form-group mb-3 staff_permissions" style="display: none">
                    <label for="user_type">Permissions<span class="text-danger">*</span></label>
                    <table class="table table-sm table-bordered">
                        <tr>
                            <th width="50%">Section Name</th>
                            <th class="text-center">Read</th>
                            <th class="text-center">Write / Update</th>
                            <th class="text-center">Delete</th>
                        </tr>
                        @php
                            $permissions = [];
                            if(isset($staff) && !empty($staff->user_permissions)){
                                $permissions = (array) $staff->user_permissions;
                            }
                        @endphp
                        @foreach (\App\Models\PermissionType::all() as $type)
                        @php
                            $is_read_allowed = $is_write_allowed = $is_delete_allowed = false;
                            if(isset($staff)){
                                $is_read_allowed = isset($permissions[$type->slug.'-read']);
                                $is_write_allowed = isset($permissions[$type->slug.'-write']);
                                $is_delete_allowed = isset($permissions[$type->slug.'-delete']);
                            }
                        @endphp
                            <tr class="{{$type->slug.'-permissions'}}">
                                <td>{{$type->name}}</td>
                                <td class="text-center">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="permissions[{{$type->slug}}-read]" id="custom_switch_{{$type->id}}_read" {{$is_read_allowed ? 'checked' : ''}} value="true">
                                        <label class="custom-control-label" for="custom_switch_{{$type->id}}_read"></label>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="permissions[{{$type->slug}}-write]" id="custom_switch_{{$type->id}}_write" {{$is_write_allowed ? 'checked' : ''}} value="true">
                                        <label class="custom-control-label" for="custom_switch_{{$type->id}}_write"></label>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="permissions[{{$type->slug}}-delete]" id="custom_switch_{{$type->id}}_delete" {{$is_delete_allowed ? 'checked' : ''}} value="true">
                                        <label class="custom-control-label" for="custom_switch_{{$type->id}}_delete"></label>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>

                <div class="form-group mb-3 text-right">
                    <button class="btn btn-primary waves-effect waves-light" type="submit">
                        Submit
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@if(isset($staff))
<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            <h4 class="header-title m-t-0 mb-3">Update Password For Staff</h4>

            <form action="{{ route('admin.staffs.update_password') }}" class="ajaxForm" method="post">
                @csrf
                <div class="form-group mb-3">
                    <label for="new_password">Password<span class="text-danger">*</span></label>
                    <input type="password" name="password" parsley-trigger="change" data-parsley-minlength="8" data-parsley-required placeholder="Enter password atleast 8 charactes long" class="form-control" id="new_password">
                </div>
                <div class="form-group mb-3">
                    <label for="password_confirmation">Confirm Password<span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" data-parsley-minlength="8" parsley-trigger="change" data-parsley-equalto="#new_password" data-parsley-required placeholder="Enter confirm password atleast 8 charactes long" class="form-control" id="password_confirmation">
                </div>
                <div class="form-group mb-3 text-right">
                    <input type="hidden" value="{{ $staff->hashid }}" name="staff_id" />
                    <button class="btn btn-primary waves-effect waves-light" type="submit">
                        Submit
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endif
@endsection

@section('page-scripts')
<script>
    @if(isset($staff))
        permission_types('{{$staff->user_type}}');
    @endif
    $('#profile_img').change(function() {
        var filename = $('#profile_img').val();
        if (filename.substring(3,11) == 'fakepath') {
            filename = filename.substring(12);
        }
        if(filename && filename != ''){
            $('.profile_img_label').html(filename);
        }else{
            $('.profile_img_label').html('Choose file');
        }
   });

   function permission_types(type){
       if(type != 'admin'){
            $('.staff_permissions').show();
       }else{
            $('.staff_permissions').hide();
       }

       if(type == 'author'){
        $('.blog-permissions input').prop('checked', true).prop('disabled', true);
       }else{
        $('.blog-permissions input').prop('checked', false).prop('disabled', false);
       }
   }
</script>
@endsection
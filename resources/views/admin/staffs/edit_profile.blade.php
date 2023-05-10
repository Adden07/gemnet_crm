@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Update Profile</li>
                </ol>
            </div>
            <h4 class="page-title">Update Profile</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            <h4 class="header-title m-t-0">Update Profile</h4>
            <p class="text-muted font-14 m-b-20">
                Update your profile
            </p>

            <form action="{{ route('admin.save_profile') }}" class="ajaxForm" method="post" enctype="multipart/form-data" novalidate>
                @csrf
                @if($staff->image)
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
                    <input type="text" name="name" placeholder="Enter Display Name" class="form-control" id="name" value="{{ $staff->name ?? '' }}">
                </div>
                <div class="form-group mb-3">
                    <label for="email">Email<span class="text-danger">*</span></label>
                    <input type="text" disabled placeholder="Enter email" class="form-control" id="email" value="{{ $staff->email ?? '' }}">
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
<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            <h4 class="header-title m-t-0 mb-3">Change Your Password</h4>

            <form action="{{ route('admin.change_password') }}" class="ajaxForm" method="post">
                @csrf
                <div class="form-group mb-3">
                    <label for="old_password">Password<span class="text-danger">*</span></label>
                    <input type="password" name="old_password" parsley-trigger="change" data-parsley-required placeholder="Enter your current password" class="form-control" id="old_password">
                </div>
                <div class="form-group mb-3">
                    <label for="new_password">New Password<span class="text-danger">*</span></label>
                    <input type="password" name="password" parsley-trigger="change" data-parsley-minlength="8" data-parsley-required placeholder="Enter password atleast 8 charactes long" class="form-control" id="new_password">
                </div>
                <div class="form-group mb-3">
                    <label for="password_confirmation">Confirm New Password<span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" data-parsley-minlength="8" parsley-trigger="change" data-parsley-equalto="#new_password" data-parsley-required placeholder="Enter confirm password atleast 8 charactes long" class="form-control" id="password_confirmation">
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
@endsection

@section('page-scripts')
<script>
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
</script>

@endsection

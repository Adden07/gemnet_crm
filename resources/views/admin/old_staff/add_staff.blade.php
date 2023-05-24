@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="">Admin</a></li>
                    <li class="breadcrumb-item active">{{ isset($staff) ? 'Edit' : 'Add'}} Admin</li>
                </ol>
            </div>
            <h4 class="page-title">{{ isset($staff) ? 'Edit' : 'Add'}} Admin</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            <h4 class="header-title m-t-0">{{ isset($staff) ? 'Edit' : 'Add'}} Admin</h4>
            <p class="text-muted font-14 m-b-20">
                Here you can {{ isset($staff) ? 'Edit' : 'Add'}} Admin.
            </p>

            <form action="{{ route('admin.staffs.store') }}" class="ajaxForm" method="post" enctype="multipart/form-data" novalidate>
                @csrf

                <div class="row">
                    <div class="form-group mb-3 col-md-6">
                        <label for="role">Select Role<span class="text-danger">*</span></label>
                        <select class="form-control" parsley-trigger="change" data-parsley-required name="role" id="role" required>
                            <option value="">Select Role</option>
                            <option value="supervisor">Supervisor</option>
                            <option value="sales_person">Sales Person</option>
                            <option value="accounts">Accounts</option>
                            <option value="support">Support</option>
                            <option value="support">Recovery</option>
                        </select>
                    </div>

                    <div class="form-group mb-3 col-md-6">
                        <label for="city_id">Select city<span class="text-danger">*</span></label>
                        <select class="form-control" parsley-trigger="change" data-parsley-required name="city_id" id="city_id" required>
                            <option value="">Select City</option>
                            @foreach($cities AS $city)
                                <option value="{{ $city->hashid }}" @if(@$edit_isp->city_id == $city->id) selected @endif>{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group mb-3 col-md-6">
                        <label for="name">Name<span class="text-danger">*</span></label>
                        <input type="text" name="name" parsley-trigger="change" data-parsley-required placeholder="Enter name" value="{{ @$edit_isp->company_name }}" class="form-control" id="name" required>
                    </div>

                    <div class="form-group mb-3 col-md-6">
                        <label for="username">Username<span class="text-danger">*</span></label>
                        <input type="text" name="username" parsley-trigger="change" data-parsley-required placeholder="Enter username" value="{{ @$edit_isp->username }}" class="form-control" id="username">
                    </div>
                </div>    

                <div class="row">
                    <div class="form-group mb-3 col-md-6">
                        <label for="password">Password<span class="text-danger">*</span></label>
                        <input type="password" name="password" parsley-trigger="change" data-parsley-required placeholder="Enter password" value="{{ @$edit_isp->password }}" class="form-control" id="password">
                    </div>

                    <div class="form-group mb-3 col-md-6">
                        <label for="password_confirmation ">Confirm Password<span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" parsley-trigger="change" data-parsley-required placeholder="Confirm password" value="{{ @$edit_isp->username }}" class="form-control" id="password_confirmation">
                    </div>
                </div>    

                <div class="row">
                    <div class="form-group mb-3 col-md-6">
                        <label for="email">Email<span class="text-danger">*</span></label>
                        <input type="email" name="email" parsley-trigger="change" data-parsley-required placeholder="Enter email" value="{{ @$edit_isp->email }}" class="form-control" id="email">
                    </div>

                    <div class="form-group mb-3 col-md-6">
                        <label for="nic ">NIC<span class="text-danger">*</span></label>
                        <input type="text" name="nic" parsley-trigger="change" data-parsley-required placeholder="Enter NIC" value="{{ @$edit_isp->nic }}" class="form-control" id="nic">
                    </div>
                </div>    

                <div class="row">
                    <div class="form-group mb-3 col-md-12">
                        <label for="address ">Address<span class="text-danger">*</span></label>
                        <input type="text" name="address" parsley-trigger="change" data-parsley-required placeholder="Enter Address" value="{{ @$edit_isp->address }}" class="form-control" id="address">
                    </div>
                </div>



                <div class="form-group mb-3 text-right">
                    {{-- <input type="hidden" value="{{ @$edit_isp->hashid }}" name="isp_id"> --}}
                    <button class="btn btn-primary waves-effect waves-light" type="submit">
                        {{ (isset($is_update)) ? 'Update' : 'Add' }}
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

@endsection
@section('page-scripts')
<script>
    $('#nic').mask('00000-0000000-0');
</script>
@endsection
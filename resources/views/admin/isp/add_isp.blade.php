@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="">ISP</a></li>
                    <li class="breadcrumb-item active">{{ isset($staff) ? 'Edit' : 'Add'}} ISP</li>
                </ol>
            </div>
            <h4 class="page-title">{{ isset($staff) ? 'Edit' : 'Add'}} ISP</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            <h4 class="header-title m-t-0">{{ isset($staff) ? 'Edit' : 'Add'}} ISP</h4>
            <p class="text-muted font-14 m-b-20">
                Here you can {{ isset($staff) ? 'edit' : 'add'}} ISP.
            </p>

            <form action="{{ route('admin.isp.store') }}" class="ajaxForm" method="post" enctype="multipart/form-data" novalidate>
                @csrf
                <div class="row">
                    <div class="form-group mb-3 col-md-6">
                        <label for="firstname">Select City<span class="text-danger">*</span></label>
                        <select class="form-control" parsley-trigger="change" data-parsley-required name="city_id" id="city_id" required>
                            <option value="">Select City</option>
                            @foreach($cities AS $city)
                                <option value="{{ $city->hashid }}" @if(@$edit_isp->city_id == $city->id) selected @endif>{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3 col-md-6">
                        <label for="company_name">Company Name<span class="text-danger">*</span></label>
                        <input type="text" name="company_name" parsley-trigger="change" data-parsley-required placeholder="Enter company name name" value="{{ @$edit_isp->company_name }}" class="form-control" id="company_name" required>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group mb-3 col-md-6">
                        <label for="poc_name">POC Name<span class="text-danger">*</span></label>
                        <input type="text" name="poc_name" parsley-trigger="change" data-parsley-required placeholder="Enter POC name" value="{{ @$edit_isp->poc_name }}" class="form-control" id="poc_name" required>
                    </div>
                    <div class="form-group mb-3 col-md-6">
                        <label for="mobile">Mobile<span class="text-danger">*</span></label>
                        <input type="text" name="mobile" parsley-trigger="change" data-parsley-required placeholder="Enter POC name" value="{{ @$edit_isp->mobile }}" class="form-control" id="mobile" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="form-group mb-3 col-md-12">
                        <label for="address">Address<span class="text-danger">*</span></label>
                        <input type="text" name="address" parsley-trigger="change" data-parsley-required placeholder="Enter  address" value="{{ @$edit_isp->address }}" class="form-control" id="address" required>
                    </div>
                </div>


                <div class="form-group mb-3 text-right">
                    <input type="hidden" value="{{ @$edit_isp->hashid }}" name="isp_id">
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
 $('#mobile').mask('+920000000000');
</script>
@endsection
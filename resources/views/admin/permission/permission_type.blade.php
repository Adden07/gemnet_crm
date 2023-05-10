@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="">Admin</a></li>
                    <li class="breadcrumb-item active">{{ isset($is_update) ? 'Edit' : 'Add'}} Permission Type</li>
                </ol>
            </div>
            <h4 class="page-title">{{ isset($is_update) ? 'Edit' : 'Add'}} Permission Type</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            <h4 class="header-title m-t-0">{{ isset($is_update) ? 'Edit' : 'Add'}} Permission Type</h4>
            <p class="text-muted font-14 m-b-20">
                Here you can {{ isset($is_update) ? 'edit' : 'add'}} Permission Type.
            </p>

            <form action="{{ route('admin.permissions.store_type') }}" class="ajaxForm" method="post" enctype="multipart/form-data" novalidate>
                @csrf

                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="area_name">Permission  Type</label>
                        <input type="text" class="form-control" parsley-trigger="change" data-parsley-required placeholder="Enter Permission Type" value="" name="permission_type" id="permission_type">
                    </div>
                </div>    

                

                <div class="form-group mb-3 text-right">
                    <input type="hidden" value="{{ @$edit_area->hashid }}" name="area_edit_id">
                    <button class="btn btn-primary waves-effect waves-light" type="submit">
                        {{ (isset($is_update)) ? 'Update' : 'Add' }}
                    </button>
                </div>

            </form>
        </div>
    </div>
    
    <div class="col-lg-12">
        <h4 class="page-title"> Permission Type List </h4>

        <div class="card-box">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="header-title">Permission Types</h4>
                {{-- <a href="{{ route('admin.staffs.add') }}" class="btn btn-sm btn-primary">Add Staff</a> --}}
            </div>
            <p class="sub-header">Following is the list of all the Permission Types.</p>
            <table class="table dt_table table-bordered w-100 nowrap" id="laravel_datatable">
                <thead>
                    <tr>
                        <th width="20">S.No</th>
                        <th>Slug</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($permission_types AS $type)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $type->slug }}</td>
                        </tr> 
                    @endforeach
                </tbody>
            </table>
            {{-- <span class="float-right">{{ $areas->links()  }} --}}
            </span>
        </div>
    </div>
</div>

@endsection
@section('page-scripts')
@endsection
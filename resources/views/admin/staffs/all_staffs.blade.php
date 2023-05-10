@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Staffs</li>
                </ol>
            </div>
            <h4 class="page-title">All Staffs</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="header-title">Staffs</h4>
                <a href="{{ route('admin.staffs.add') }}" class="btn btn-sm btn-primary">Add Staff</a>
            </div>
            <p class="sub-header">Following is the list of all the staffs.</p>
            <table class="table dt_table table-bordered w-100 nowrap" id="laravel_datatable">
                <thead>
                    <tr>
                        <th width="20">S.No</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Type</th>
                        <th>Added On</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($staffs as $k => $staff)
                    <tr>
                        <td>
                            <p class="m-0 text-center">{{ $k + 1 }}</p>
                        </td>
                        <td>{{ $staff->full_name }}</td>
                        <td><small>{{ $staff->email }}</small></td>
                        <td><small class="badge badge-{{$staff->user_type == 'admin' ? 'danger' : 'info'}}">{{ ucwords($staff->user_type) }}</small></td>
                        <td>
                            <p class="m-0"><small>{{ get_date($staff->created_at) }}</small></p>
                        </td>
                        
                        <td class="text-center">
                            <p class="mb-1">
                                <span class="badge badge-{{$staff->is_active ? 'success' : 'danger'}}">{{$staff->is_active ? 'Active' : 'Disabled'}}</span>
                            </p>
                            <p class="m-0 text-center">
                                <input type="checkbox" class="nopopup" onchange="ajaxRequest(this)" data-url="{{ route('admin.staffs.update_status', $staff->hashid) }}" {{ $staff->is_active ? 'checked' : ''}} data-toggle="switchery" data-size="small" data-color="#1bb99a" />
                            </p>
                        </td>
                        <td>
                            <a href="{{route('admin.staffs.edit', $staff->hashid)}}" class="btn btn-warning btn-xs waves-effect waves-light">
                                <span class="btn-label"><i class="fa fa-edit"></i></span>Edit
                            </a>
                            <button type="button" onclick="ajaxRequest(this)" data-url="{{ route('admin.staffs.delete', $staff->hashid) }}"  class="btn btn-danger btn-xs waves-effect waves-light">
                                <span class="btn-label"><i class="fa fa-trash"></i></span>Delete
                            </button>
                        </td>
                    </tr>
                
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('page-scripts')
@include('admin.partials.datatable', ['load_swtichery' => true])
@endsection
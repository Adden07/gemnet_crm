@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('admin.admins.index') }}"></a>Staff</li>
                    <li class="breadcrumb-item active">All </li>

                </ol>
            </div>
            <h4 class="page-title">All Staff</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            @can('add-admin')
                <a href="{{ route('admin.admins.add') }}" class="btn btn-primary float-right">Add Staff</a>
            @endcan
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="header-title">All Staff List</h4>
                {{-- <a href="{{ route('admin.staffs.add') }}" class="btn btn-sm btn-primary">Add Staff</a> --}}
            </div>
            <p class="sub-header">Following is the list of all the Staff.</p>
            <table class="table dt_table table-bordered w-100 nowrap" id="laravel_datatable">
                <thead>
                    <tr>
                        <th width="20">S.No</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>NIC</th>
                        <th>User Type</th>
                        <th>Status</th>
                        @can('view-admin')
                            <th>View</th>
                        @endcan
                        @can('edit-admin')
                            <th>Action</th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @foreach($admins AS $admin)
                        <tr>
                            <td>{{  $loop->iteration }}</td>
                            <td>{{ $admin->name }}</td>
                            <td>{{ $admin->username }}</td>
                            <td>{{ $admin->email }}</td>
                            <td>{{ $admin->nic }}</td>
                            <td>{{ $admin->user_type}}</td>
                            <td>
                                @if($admin->is_active == 'active')
                                <span class="badge badge-success">Active</span>
                                @else
                                <span class="badge badge-danger">Deactive</span>
                                @endif
                            </td>
                            @can('view-admin')
                                <td>
                                    <a href="{{ route('admin.admins.detail',['id'=>$admin->hashid]) }}" class="text-primary details"><i class="icon-eye"></i></a>
                                </td>
                            @endcan
                            @can('edit-admin')
                            <td>
                                <a href="{{ route('admin.admins.edit',['id'=>$admin->hashid]) }}" class="btn btn-warning btn-xs waves-effect waves-light">
                                    <span class="btn-label"><i class="icon-pencil"></i></span>Edit
                                </a>
                                <button type="button" onclick="ajaxRequest(this)" data-url="{{ route('admin.admins.delete', ['id'=>$admin->hashid]) }}" class="btn btn-danger btn-xs waves-effect waves-light">
                                    <span class="btn-label"><i class="icon-trash"></i></span>Delete
                                </button>
                            </td>
                            @endcan
                        </tr>
                    @endforeach
                </tbody>
            </table>
           <span class="float-right">{{ $admins->links() }}</span>
        </div>
    </div>
</div>
<div class="modal fade bd-example-modal-lg" id="details_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
</div>
@endsection

@section('page-scripts')
@include('admin.partials.datatable', ['load_swtichery' => true])
<script>
    // $('.details').click(function(e){
    //     e.preventDefault();
    //     var route = $(this).attr('href');
        
    //     getAjaxRequests(route,'','GET',function(resp){
    //         $('#details_modal').html(resp.html);
    //         $('#details_modal').modal('show');
    //     })
    // });
</script>
@endsection
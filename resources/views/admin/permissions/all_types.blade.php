@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Admin Permission Types</li>
                </ol>
            </div>
            <h4 class="page-title">All Admin Permission Types</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="header-title">All Admin Permission Types</h4>
                <button class="btn btn-primary" onclick="add_permission_type(this)">Add New Permission Type</button>
            </div>
            <p class="sub-header">Following is the list of all the permissions types for the backend user.</p>
            <table class="table dt_table table-bordered w-100 nowrap">
                <thead>
                    <tr>
                        <th width="30">S.No</th>
                        <th>Name</th>
                        <th>Permissions</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($permissions as $k => $type)
                    <tr>
                        <td>{{ $k + 1 }}</td>
                        <td>{{$type->name}}</td>
                        <td>
                            <p class="mb-0"><small>{{ $type->name }} Read</small></p>
                            <p class="mb-0"><small>{{ $type->name }} Write</small></p>
                            <p class="mb-0"><small>{{ $type->name }} Delete</small></p>
                        </td>
                        <td>
                            <a href="javascript:void(0)" onclick="add_permission_type(this, true)" data-id="{{$type->hashid}}" data-name="{{$type->name}}" class="btn btn-info btn-xs waves-effect waves-light">
                                <span class="btn-label"><i class="icon-eye"></i></span>Edit
                            </a>
                            <button type="button" onclick="ajaxRequest(this)" data-url="{{ route('admin.permission_types.delete', $type->hashid) }}" class="btn btn-danger btn-xs waves-effect waves-light">
                                <span class="btn-label"><i class="icon-trash"></i></span>Delete
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="permission_type_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="permission_type_modalLabel"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <form class="ajaxForm" method="post" action="{{route('admin.permission_types.save')}}">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="name">Permission Type Name</label>
                        <input type="text" id="permission_type_name" name="name" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <input type="hidden" name="type_id" class="form-control" id="permission_type_id">
                        <button class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection

@section('page-scripts')
@include('admin.partials.datatable')
<script type="text/javascript">
    function add_permission_type(ele, is_edit = false){
        var name = null,
            id = null; 
        if(is_edit){
            name = $(ele).data('name');
            id = $(ele).data('id');
            $("#permission_type_modalLabel").html('Update Permission Type: '+name);
        }else{
            $("#permission_type_modalLabel").html('Add New Permission Type');
        }
        $("#permission_type_name").val(name);
        $("#permission_type_id").val(id);
        $("#permission_type_modal").modal('show');
    }
</script>
@endsection

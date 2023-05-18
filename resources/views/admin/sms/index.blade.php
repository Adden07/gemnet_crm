@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('admin.admins.index') }}"></a>SMS</li>
                    <li class="breadcrumb-item active"> All Sms</li>

                </ol>
            </div>
            <h4 class="page-title">Admins</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            <form action="{{ route('admin.sms.store') }}" method="POST" class="ajaxForm">
                @csrf
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="">SMS Type</label>
                        <select class="form-control" name="type" id="type">
                            <option value="">Select sms type</option>
                            <option value="alert" @if(@$edit_sms->type == 'alert') selected @endif>Alert</option>
                            <option value="user_created" @if(@$edit_sms->type == 'user_createds') selected @endif>User Created</option>
                            <option value="user_registered" @if(@$edit_sms->type == 'user_registered') selected @endif>User Renew</option>
                            <option value="user_registered" @if(@$edit_sms->type == 'user_registered') selected @endif>User Registered</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="">SMS</label>
                        <input type="text" class="form-control" placeholder="Enter sms type" value="{{ @$edit_sms->message }}" name="message" id="message">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="">Stauts</label>
                        <select class="form-control"name="status" id="">
                            <option value="1" @if(@$edit_sms->status) selected @endif>Active</option>
                            <option value="0" @if(@!$edit_sms->status) selected @endif>Deactive</option>
                        </select>
                    </div>
                </div>
                <input type="hidden" name="sms_id" value="{{ @$edit_sms->hashid }}">
                <input type="submit" class="btn btn-primary" value="{{ (isset($is_update) ? 'Update' : 'Add') }}" style="float:right">
            </form>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="header-title">All SMS List</h4>
                {{-- <a href="{{ route('admin.staffs.add') }}" class="btn btn-sm btn-primary">Add Staff</a> --}}
            </div>
            <p class="sub-header">Following is the list of all the SMS.</p>
            <table class="table dt_table table-bordered w-100 nowrap" id="laravel_datatable">
                <thead>
                    <tr>
                        <th width="20">S.No</th>
                        <th>Type</th>
                        <th>Message</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($messages AS $sms)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $sms->type }}</td>
                            <td>{{ $sms->message }}</td>
                            <td>
                                <a href="{{ route('admin.sms.edit', ['id'=>$sms->hashid]) }}" class="btn btn-warning btn-xs waves-effect waves-light">
                                    <span class="btn-label"><i class="icon-pencil"></i></span>Edit
                                </a>
                                <button type="button" onclick="ajaxRequest(this)" data-url="{{ route('admin.sms.delete', ['id'=>$sms->hashid]) }}" class="btn btn-danger btn-xs waves-effect waves-light">
                                    <span class="btn-label"><i class="icon-trash"></i></span>Delete
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    {{-- @foreach($admins AS $admin)
                        <tr>
                            <td>{{  $loop->iteration }}</td>
                            <td>{{ $admin->name }}</td>
                            <td>{{ $admin->username }}</td>
                            <td>{{ $admin->email }}</td>
                            <td>{{ $admin->nic }}</td>
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
                                <button type="button" onclick="ajaxRequest(this)" data-url="" class="btn btn-danger btn-xs waves-effect waves-light">
                                    <span class="btn-label"><i class="icon-trash"></i></span>Delete
                                </button>
                            </td>
                            @endcan
                        </tr>
                    @endforeach --}}
                </tbody>
            </table>
           {{-- <span class="float-right">{{ $admins->links() }}</span> --}}
        </div>
    </div>
</div>
<div class="modal fade bd-example-modal-lg" id="details_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
</div>
@endsection

@section('page-scripts')
@include('admin.partials.datatable', ['load_swtichery' => true])
<script>
</script>
@endsection
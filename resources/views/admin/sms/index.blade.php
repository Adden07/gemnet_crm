@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('admin.admins.index') }}"></a>SMS</li>
                    <li class="breadcrumb-item active"> SMS Types</li>

                </ol>
            </div>
            <h4 class="page-title">SMS Types</h4>
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
                            <option value="user_created" @if(@$edit_sms->type == 'user_registrtaion') selected @endif>User Registration</option>
                            <option value="expiry_alert" @if(@$edit_sms->type == 'expiry_alert') selected @endif>Expiry Alert</option>
                            <option value="low_qouta" @if(@$edit_sms->type == 'low_qouta') selected @endif>low qouta</option>
                            <option value="user_balance" @if(@$edit_sms->type == 'user_balance') selected @endif>User Balance</option>
                            <option value="admin_registration" @if(@$edit_sms->type == 'admin_registration') selected @endif>Admin Registration</option>
                            <option value="user_add_payment" @if(@$edit_sms->type == 'user_add_payment') selected @endif>User Add Payment</option>
                            <option value="user_renew" @if(@$edit_sms->type == 'user_renew') selected @endif>User Renew</option>
                            <option value="user_activation" @if(@$edit_sms->type == 'user_activation') selected @endif>User Activation</option>
                            <option value="user_near_expiry" @if(@$edit_sms->type == 'user_near_expiry') selected @endif>User Near Expiry</option>
                            <option value="user_expired" @if(@$edit_sms->type == 'user_expired') selected @endif>User Expired</option>

                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="">SMS</label>
                        <input type="text" class="form-control" placeholder="Enter sms type" value="{{ @$edit_sms->message }}" name="message" id="message">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="">Status</label>
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
                <h4 class="header-title">SMS Type List</h4>
                {{-- <a href="{{ route('admin.staffs.add') }}" class="btn btn-sm btn-primary">Add Staff</a> --}}
            </div>
            <p class="sub-header">Following is the list of all the SMS types.</p>
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
                </tbody>
            </table>
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
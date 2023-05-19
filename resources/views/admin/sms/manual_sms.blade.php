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
            <form action="{{ route('admin.sms.send_manual_sms') }}" method="POST" class="ajaxForm">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <label for="">Mobile No</label>
                        <input type="text" class="form-control" placeholder="920000000000" oninput="mobileNoMasking(this)" maxlength="12" id="mobile_no" name="mobile_no" required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="">SMS</label>
                        <textarea class="form-control" name="message" id="message" cols="30" rows="10"></textarea>
                    </div>
                </div>
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
                        <th>Mobile No</th>
                        <th>Message</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($messages AS $sms)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $sms->mobile_no }}</td>
                            <td>{{ $sms->sms }}</td>
                            <td>
                                @if($sms->status == 1)
                                    <span class='badge badge-success'>success</span>
                                @else 
                                    <span class='badge badge-danger'>failed</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    {{-- @foreach($messages AS $sms)
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
                    @endforeach --}}
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

@endsection
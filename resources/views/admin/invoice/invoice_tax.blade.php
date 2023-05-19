@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('admin.admins.index') }}"></a>Finance</li>
                    <li class="breadcrumb-item active"> Invoice Tax</li>

                </ol>
            </div>
            <h4 class="page-title">Taxed Invoice Export</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            <form action="{{ route('admin.accounts.invoices.export_invoice_tax') }}" method="POST" class="">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <label for="">Month</label>
                        <select class="form-control" name="month" id="month" required>
                            <option value="">Select month</option>
                            @foreach($months AS $month)
                                <option value="{{ date('m', strtotime($month->created_at)) }}">{{ date('M', strtotime($month->created_at)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mt-3">
                        <input type="submit" class="btn btn-primary" value="Export">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="header-title">All SMS log</h4>
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
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- @foreach($messages AS $sms)
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
                            <td>{{ date('d-M-Y H:i:s', strtotime($sms->created_at)) }}</td>
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
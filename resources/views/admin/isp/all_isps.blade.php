@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">All ISPs</li>
                </ol>
            </div>
            <h4 class="page-title">All ISPs</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="header-title">ISPs</h4>
                {{-- <a href="{{ route('admin.staffs.add') }}" class="btn btn-sm btn-primary">Add Staff</a> --}}
            </div>
            <p class="sub-header">Following is the list of all the ISPs.</p>
            <table class="table dt_table table-bordered w-100 nowrap" id="laravel_datatable">
                <thead>
                    <tr>
                        <th width="20">S.No</th>
                        <th>City</th>
                        <th>Company Name</th>
                        <th>POC Name</th>
                        <th>Mobile No</th>
                        <th>Address</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($isps AS $isp)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $isp->cities->name }}</td>
                            <td>{{ $isp->company_name }}</td>
                            <td>{{ $isp->poc_name }}</td>
                            <td>{{ $isp->mobile }}</td>
                            <td>{{ $isp->address }}</td>
                            <td>
                                <a href="{{ route('admin.isp.edit',['id'=>$isp->hashid]) }}" class="btn btn-warning btn-xs waves-effect waves-light">
                                    <span class="btn-label"><i class="fa fa-edit"></i></span>Edit
                                </a>
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
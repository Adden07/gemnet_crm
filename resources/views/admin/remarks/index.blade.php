@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Activity Logs</li>
                </ol>
            </div>
            <h4 class="page-title"> Remark Types</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            <div class=" align-items-center justify-content-between">
                <form action="{{ route('admin.remarks.store') }}" method="post" class="ajaxForm" >
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-10">
                            <label for="from_date">Remark Type</label>
                            <input type="text" class="form-control" value="{{ @$edit_remark->remark_type }}" placeholder="Enter remarks type" parsley-trigger="change" data-parsley-required  name="remark_type" id="remark_type">
                        </div>
                        <div class="col-md-2">
                            <input type="hidden" value="{{ @$edit_remark->hashid }}" name="remark_id">
                            <input type="submit" class="form-control btn btn-primary mt-3" value="{{ (!isset($is_update)) ? 'Add' : 'Update' }}">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="card-box">
            <div class="d-flex align-items-center justify-content-between">
                {{-- <h4 class="header-title">Activity Logs</h4> --}}
            </div>
            {{-- <p class="sub-header">Following is the list of all the activities.</p> --}}
            <table class="table  table-bordered w-100 nowrap" id="log_table">
                <thead>
                    <tr>
                        <th width="20">S.No</th>
                        <th>Added By</th>
                        <th>Type</th>
                        <th>Aciton</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($remarks as $remark)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $remark->admin->username }}</td>
                            <td>{{ $remark->remark_type }}</td>
                            <td>
                                <a href="{{ route('admin.remarks.edit', ['id'=>$remark->hashid]) }}" class='btn btn-warning btn-xs waves-effect waves-light' title='Edit'>
                                    <i class='icon-pencil'></i>
                                </a>
                                <button type='button' onclick='ajaxRequest(this)' data-url="{{ route('admin.remarks.delete', ['id'=>$remark->hashid]) }}" class='btn btn-danger btn-xs waves-effect waves-light'>
                                    <span class='btn-label'><i class='icon-trash'></i>
                                    </span>Delete
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
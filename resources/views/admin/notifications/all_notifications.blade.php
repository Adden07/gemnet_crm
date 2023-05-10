@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item active">All Notifications</li>
                </ol>
            </div>
            <h4 class="page-title">All Notifications</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="header-title">All Notifications</h4>
                <button class="btn btn-success" onclick="ajaxRequest(this)" data-msg="Are you sure you want to mark all notifications as read?" data-url="{{ route('admin.notifications.clear_all') }}" data-btn-text="Yes! Mark all as read"><span class="btn-label"><i class="icon-check"></i></span> Mark all notifications as read</button>
            </div>
            <p class="sub-header">Following is the list of all the notifications admin have recieved.</p>
            <form action="{{ route('admin.notifications') }}" method="get" class="form-inline my-3 border-top border-bottom py-3 align-items-center justify-content-center">
                <div class="form-group">
                    <label for="from_date" class="sr-onlys mr-1">From:</label>
                    <input type="text" class="form-control human_datepicker" id="from_date" value="{{ (request()->from) ? request()->from : date('Y-m-1') }}" placeholder="From" name="from">
                </div>
                <div class="form-group ml-2">
                    <label for="to_date" class="sr-onlys mr-1">To:</label>
                    <input type="text" class="form-control human_datepicker" id="to_date" value="{{ (request()->to) ? request()->to : date('Y-m-d') }}" placeholder="To" name="to">
                </div>
                <button type="submit" class="btn btn-primary waves-effect waves-light ml-2">Submit</button>
            </form>
            <table class="table dt_table table-bordered w-100 nowrap">
                <thead>
                    <tr>
                        <th width="30">S.No</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Notification</th>
                        <th>Recieved On</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($notifications as $k => $notify)
                    @php
                    $is_read = $notify->read_at !== null;
                    $type = str_replace('_', ' ', $notify->data['notify_type']);
                    $route = \Route::has($notify->data['route']) ? route($notify->data['route'], $notify->data['id'] ?? '') : route('admin.notifications');
                    @endphp
                    <tr class="{{ $is_read ? '' : 'table-active' }}">
                        <td>{{ $k + 1 }}</td>
                        <td><a href="{{$route}}" target="_blank">{{ $notify->data['notify_title'] }}</a></td>
                        <td><span class="badge badge-primary">{{ ucwords($type) }}</span></td>
                        <td>{{ $notify->data['msg'] }}</td>
                        <td>{{ $notify->created_at->diffForHumans() }}</td>
                        <td>
                            @if($is_read)
                            <span class="badge bg-soft-success text-success">Read</span>
                            @else
                            <span class="badge bg-soft-danger text-danger">Unread</span>
                            @endif
                        </td>
                        <td>
                            @if(!$is_read)
                            <button type="button" onclick="ajaxRequest(this)" data-url="{{ route('admin.notifications.clear', $notify->id) }}" class="btn btn-success nopopup btn-xs waves-effect waves-light">
                                <span class="btn-label"><i class="icon-check"></i></span> Mark as read
                            </button>
                            @endif
                            <button type="button" onclick="ajaxRequest(this)" data-url="{{ route('admin.notifications.delete', $notify->id) }}" class="btn btn-danger btn-xs waves-effect waves-light">
                                <i class="icon-trash"></i>
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
    @include('admin.partials.datatable')
@endsection
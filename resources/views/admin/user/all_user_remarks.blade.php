@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Remarks</li>
                </ol>
            </div>
            <h4 class="page-title"> Remarks</h4>
        </div>
    </div>
</div>

<div class="row">
    {{-- <div class="col-lg-12">
        <div class="card-box">
            <div class=" align-items-center justify-content-between">
                <form action="{{ route('admin.activity_logs.index') }}" method="GET" class="" novalidate>
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-5">
                            <label for="from_date">From Date</label>
                            <input type="date" class="form-control" value="{{ $from_date ?? date('Y-m-d') }}" parsley-trigger="change" data-parsley-required  name="from_date" id="from_date">
                        </div>
                        <div class="form-group col-md-5">
                            <label for="">To Date</label>
                            <input type="date" class="form-control" value="{{ $to_date ?? date('Y-m-d') }}" parsely-trigger="change" data-parsley-required name="to_date" id="to_date">
                        </div>
                        <div class="col-md-2">
                            <input type="submit" class="form-control btn btn-primary mt-3" value="search">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}
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
                        <th>Admin</th>
                        <th>User</th>
                        <th>Remark Type</th>
                        <th>Remark</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- @foreach($activity_logs AS $key=>$log)
                        <tr>
                            <td>{{ $activity_logs->firstItem() + $loop->index }}</td>
                            <td>{{ $log->user->username }}</td>
                            <td>{{ $log->user->user_type }}</td>
                            <td>{{ $log->user_ip }}</td>
                            <td>{{ $log->activity }}</td>
                            <td>{{ date('Y-m-d H:i:s',strtotime($log->created_at)) }}</td>
                        </tr>
                    @endforeach --}}
                </tbody>
            </table>
            {{-- <span class="float-right">{{ $activity_logs->links() }}</span> --}}
        </div>
    </div>
</div>
@endsection

@section('page-scripts')
@include('admin.partials.datatable', ['load_swtichery' => true])
<script>
    $(document).ready(function(){
        var table = $('#log_table').DataTable({
            processing : true,
            serverSide : true,
            "order": [[ 0, "desc" ]],
            "pageLength" : 300,
            "lengthMenu" : [300, 500, 1500, 1500],
            "dom": '<"top"ifl<"clear">>rt<"bottom"ip<"clear">>',
            ajax:{
                url : "{{ route('admin.users.all_user_remarks') }}",
                data:function(d){
                            d.from_date = $('#from_date').val(),
                            d.to_date = $('#to_date').val(),
                            d.search = $('input[type="search"]').val()
                        },
            },
            columns : [
                { data : 'DT_RowIndex', name : 'DT_RowIndex', searchable:false },
                { data : 'admin', name: 'admin' },
                { data : 'user', name : 'user' },
                { data : 'remark_type', name : 'remark_type' },
                { data : 'remark', name : 'remark' },
                { data : 'date', name : 'date' }
            ],   
        });

        $('#to_date, #from_date').change(function(){
            table.draw();
       });
    })
</script>
@endsection
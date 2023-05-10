@extends('layouts.admin')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Users</li>
                    <li class="breadcrumb-item active">Task History</li>

                </ol>
            </div>
            <h4 class="page-title">Update users expiration task history (Task ID : {{ $task->task_id }})</h4>
            <h4 class="page-title">DateTime : {{ date('d-M-Y H:i:s', strtotime($task->task_datetime)) }} -- Admin {{ $task->admin->username }}</h4>
        </div>
    </div>
</div>


<div class="tab-content pt-0" id="myTabContent">
    <div class="tab-pane fade show active" id="personal_info" role="tabpanel">
        <div class="row">
            <div class="col-lg-12">
                <div class="card-box">
                    <table class="table table-bordered" id="history">
                        <thead>
                            <tr>
                                <th width="20">#</th>
                                <th>Username</th>
                                <th>Current Expiration</th>
                                <th>New Expiration</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


</div>

@endsection

@section('page-scripts')
@include('admin.partials.datatable', ['load_swtichery' => true])
<script>
    $(document).ready(function(){
        var history =      $('#history').DataTable({
                            processing:true,
                            serverSide:true,
                            "order": [[ 0, "desc" ]],
                            "pageLength":300,
                            "lengthMenu":[300,500,1000,1500],
                            "dom": '<"top"ifl<"clear">>rt<"bottom"ip<"clear">>',

                            ajax:{
                                url : "{{ route('admin.users.update_users_expiration_task_history',['task_id'=>$task->task_id]) }}",
                                data:function(d){
                                    d.search = $('#history_filter input[type="search"]').val()
                                    d.history_admin_id = $('#history_admin_id').val();
                                },
                            },
                            columns : [
                                { data:'DT_RowIndex', name:'DT_RowIndex', orderable:true, searchable:false },
                                { data:'username', name:'username', searchable:true },
                                { data:'expiration', name:'expiration', searchable:false },
                                { data:'new_expiration', name:'new_expiration', searchable:false },
                            ]
                        });  

        // $('#history_admin_id').change(function(){
        //     history.draw();
        // });

    });
</script>
@endsection
@extends('layouts.admin')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Users</li>
                    <li class="breadcrumb-item active">Update users expiration </li>

                </ol>
            </div>
            <h4 class="page-title">Update users expiration </h4>
            </h4>
        </div>
    </div>
</div>

<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link active" id="personal_info_tab" data-toggle="tab" href="#personal_info" role="tab" aria-controls="personal_info" aria-selected="true" >Update users expiration</a>
    </li>

    <li class="nav-item" role="presentation">
        <a class="nav-link" id="packages_tab" data-toggle="tab" href="#packages" role="tab" aria-controls="personal_info" aria-selected="true" >History</a>
    </li>
</ul>

<div class="tab-content pt-0" id="myTabContent">
    <div class="tab-pane fade show active" id="personal_info" role="tabpanel">
        <div class="row">
            <div class="col-lg-12">
                <div class="card-box">
                    <form action="{{ route('admin.users.import_update_user_expiration_excel') }}" class="ajaxForm" method="post" id="excel_form" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            {{-- <div class="form-group col-md-5">
                                <label for="logo">Users Excel File</label>
                                <div class="input-group">
                                    <div class="form-group w-100">
                                        <input class="form-control" type="file" id="excel_file" name="excel_file">
                                    </div>
                                </div>
                            </div> --}}
                            <div class="form-group col-md-5">
                                <label for="">Select Admin </label>
                                {{-- <select class="form-control" name="city_id" id="city_id">
                                    <option value="">Select City</option>
                                    @foreach($cities AS $city)
                                        <option value="{{ $city->hashid }}">{{ $city->city_name }}</option>
                                    @endforeach
                                </select> --}}
                                <select class="form-control" name="admin_id" id="admin_id" required>
                                    <option value="">Select Admin</option>
                                    @forelse($admins As $admin)
                                        <option value="{{ $admin->hashid }}">{{ $admin->name }} ({{ $admin->username }}) ({{ $admin->users_count }})</option>
                                    @empty
                                        <option value="">no admin found</option>
                                    @endforelse
                                </select>
                            </div>
                            <div class="form-group col-md-5">
                                <label for="">User Status</label>
                                <select class="form-control" name="user_status" id="user_status" required>
                                    <option value="">Select user status</option>
                                    <option value="all">All</option>
                                    <option value="active">Active</option>
                                    <option value="expired">Expired</option>
                                </select>
                            </div>
                            <div class="col-md-2 mt-3">
                                <input type="submit" class="form-control btn-primary" value="List Users">
                            </div>
                        </div>
                    </form>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="">Select Task ID</label>
                                <select class="form-control" name="task_id" id="task_id" required>
                                    <option value="">Select Task ID </option>
                                    @foreach($task_ids AS $task_id)
                                        <option value="{{  $task_id->task_id }}">{{ $task_id->task_id }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md">
                            {{-- <input type="submit" class="btn btn-primary mt-3 d-none" name="submit" value="Validate Users" id="validate_btn"> --}}
                            <input type="submit" class="btn btn-primary mt-3 d-none" name="submit" value="Update Now"  id="import_btn">
                            <input type="submit" class="btn btn-danger mt-3" name="" value="Delete" id="delete_btn">
                            {{-- <button class="btn btn-info mt-3 d-none" id="export_btn">Export</button> --}}
                            <button class="btn btn-warning mt-3 d-none" id="delete_users">Delete Users</button>

                        </div>
                    </div>
                    <form action="" method="POST" id="general_form" class="ajaxForm">
                        @csrf
                        <input type="hidden" name="task_id" id="task_id">
                        <input type="hidden" name="admin_id" id="admin_id">
                        <input type="hidden" name="user_ids" id="user_ids">
                        <input type="hidden" name="type" id="type" value="delete">
                    </form>
                    <form action="" method="POST" id="export_form" class="">
                        @csrf
                        <input type="hidden" name="task_id" id="task_id">
                    </form>
                </div>
    
                <div class="card-box">
                    <table class="table table-bordered" id="online_users">
                        <thead>
                            <tr>
                                <th width="20">#</th>
                                <th>Check</th>
                                <th>Task ID</th>
                                <th>DateTime</th>
                                <th>Admin</th>
                                {{-- <th>City</th>
                                <th>Name</th> --}}
                                <th>Username</th>
                                {{-- <th>Pass</th>
                                <th>NIC</th>
                                <th>Mobile</th>
                                <th>Address</th>
                                <th>Pkg</th>
                                <th>Exp</th>
                                <th>Status</th> --}}
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


    <div class="tab-pane fade show" id="packages" role="tabpanel">
        <div class="row">
            <div class="col-lg-12">
                <div class="card-box">
                    <form action="#" class="ajaxForm" method="post" id="" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-5">
                                <label for="">Select Admin </label>
                                <select class="form-control" name="history_admin_id" id="history_admin_id" required>
                                    <option value="">Select Admin</option>
                                    @forelse($admins As $admin)
                                        <option value="{{ $admin->hashid }}">{{ $admin->name }} ({{ $admin->username }}) ({{ $admin->users_count }})</option>
                                    @empty
                                        <option value="">no admin found</option>
                                    @endforelse
                                </select>
                            </div>
                            
                            {{-- <div class="form-group col-md-5">
                                <label for="">User Status</label>
                                <select class="form-control" name="user_status" id="user_status" required>
                                    <option value="">Select user status</option>
                                    <option value="all">All</option>
                                    <option value="active">Active</option>
                                    <option value="expired">Expired</option>
                                </select>
                            </div> --}}
                            {{-- <div class="col-md-2 mt-3">
                                <input type="submit" class="form-control btn-primary" value="Upload">
                            </div> --}}
                        </div>
                    </form>

                </div>


                <div class="card-box">
                    <div class="col-12">
                        <table class="table table-bordered" style="width: 100% !important" id="history">
                            <thead>
                                <tr>
                                    <th width="20">#</th>
                                    {{-- <th>Check</th> --}}
                                    <th>Task ID</th>
                                    <th>DateTime</th>
                                    {{-- <th>Admin</th> --}}
                                    {{-- <th>Username</th>
                                    <th>Current Expiration</th>
                                    <th>New Expiration</th> --}}
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

</div>
<!-- admin modal -->
<div class="modal fade" id="admin_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Import Users (<span id="total_users_count">0</span>)</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form action="">
                <div class="form-group">
                    <label for="">Select Admin</label>
                    <select class="form-control" name="admin_id" id="admin_id" required>
                        @forelse($admins As $admin)
                            <option value="{{ $admin->hashid }}">{{ $admin->name }} ({{ $admin->username }})</option>
                        @empty
                            <option value="">no admin found</option>
                        @endforelse
                    </select>
                </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="modal_submit">Import now</button>
        </div>
      </div>
    </div>
</div>
<!-- Validation checkbox modal -->
{{-- <div class="modal fade" id="validation_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Fields validation checks</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{ route('admin.users.validate') }}" method="POST" class="ajaxForm" id="validation_form">
            <div class="modal-body">
                @csrf
                <div class="row ml-4">
                    <div class="form-check col-md-3">
                        <input class="form-check-input" type="checkbox" value="1" id="username" name="username_check" checked >
                        <label class="form-check-label" for="flexCheckChecked">
                        Username
                        </label>
                    </div>
                    <div class="form-check col-md-3">
                        <input class="form-check-input" type="checkbox" value="1" id="nic" name="nic_check" checked >
                        <label class="form-check-label" for="flexCheckChecked">
                        NIC
                        </label>
                    </div>
                    <div class="form-check col-md-3">
                        <input class="form-check-input" type="checkbox" value="1" id="password" name="password_check" checked>
                        <label class="form-check-label" for="flexCheckChecked">
                        Password
                        </label>
                    </div>
                    <div class="form-check col-md-3">
                        <input class="form-check-input" type="checkbox" value="1" id="mobile" name="mobile_check" checked>
                        <label class="form-check-label" for="flexCheckChecked">
                        Mobile
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            <input type="hidden" name="type" id="type" value="valdiate">
            <input type="hidden" name="task_id" id="task_id" value="">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary" id="">Validate</button>
            </div>
        </form>
      </div>
    </div>
</div> --}}
{{-- <form action="{{ route('admin.users.update_users_expiration_validate') }}" method="POST" class="ajaxForm" id="validation_form">
    <input type="hidden" name="type" id="type" value="valdiate">
    <input type="hidden" name="task_id" id="task_id" value="">
</form> --}}

<div class="modal fade" id="users_modal" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="{{ route('admin.users.update_user_expiration') }}" method="POST" class="ajaxForm" id="users_modal_form">

        </form>
    </div>
</div>  
@endsection

@section('page-scripts')
@include('admin.partials.datatable', ['load_swtichery' => true])
<script>
    $(document).ready(function(){

        var table =      $('#online_users').DataTable({
                            processing:true,
                            serverSide:true,
                            "order": [[ 0, "desc" ]],
                            "pageLength":300,
                            "lengthMenu":[300,500,1000,1500],
                            "dom": '<"top"ifl<"clear">>rt<"bottom"ip<"clear">>',

                            ajax:{
                                url : "{{ route('admin.users.update_users_expiration') }}",
                                data:function(d){
                                    // d.status = $('#user_status').val(),
                                    d.search = $('input[type="search"]').val()
                                },
                            },
                            columns : [
                                { data:'DT_RowIndex', name:'DT_RowIndex', orderable:true, searchable:false },
                                {data:'check_box', name:'check_box'},
                                { data:'task_id', name:'task_id', searchable:true },
                                { data:'task_datetime', name:'task_datetime' },
                                {data:'admin', name:'admin'},
                                { data:'username', name:'username', searchable:true },
                                { data:'expiration', name:'expiration', searchable:true },
                                { data:'new_expiration', name:'new_expiration', searchable:true },


                            ]
                        }); 
        var history =      $('#history').DataTable({
                            processing:true,
                            serverSide:true,
                            "order": [[ 0, "desc" ]],
                            "pageLength":300,
                            "lengthMenu":[300,500,1000,1500],
                            "dom": '<"top"ifl<"clear">>rt<"bottom"ip<"clear">>',

                            ajax:{
                                url : "{{ route('admin.users.update_users_expiration_history') }}",
                                data:function(d){
                                    d.search = $('#history_filter input[type="search"]').val()
                                    d.history_admin_id = $('#history_admin_id').val();
                                },
                            },
                            columns : [
                                { data:'DT_RowIndex', name:'DT_RowIndex', orderable:true, searchable:false },
                                { data:'task_id', name:'task_id', searchable:true },
                                { data:'task_datetime', name:'task_datetime' },
                                // {data:'admin', name:'admin', searchable:true},
                                // { data:'username', name:'username', searchable:true },
                                // { data:'expiration', name:'expiration', searchable:false },
                                // { data:'new_expiration', name:'new_expiration', searchable:false },


                            ]
                        });  

        // $('#user_status').change(function(){
        //     table.draw();
        // });
        $('#history_admin_id').change(function(){
            history.draw();
        });

        $(document).on('click', '.users_form', function(){//show errors
            var route = $(this).attr('route');
            
            getAjaxRequests(route, '', 'get', function(resp){
                $('#users_modal_form').html(resp.html);
            })
            // var route = 
            // getAjaxRequests()
            $('#users_modal').modal('show');
        });
        var task_id = null //declare task id globally
        var total_users = 0;
        //when there is change in task id
        $('#task_id').change(function(){

            task_id = $(this).val();
            var route = "{{ route('admin.users.update_user_expiration_task_status', ':task_id') }}";
            route = route.replace(':task_id', task_id);
            
            getAjaxRequests(route, '', 'GET', function(resp){
                // if(resp.status == 'validate'){
                //     $('#validate_btn').removeClass('d-none');
                //     $('#import_btn').addClass('d-none');
                // }else if(resp.status == 'import'){
                //     $('#validate_btn').addClass('d-none');
                //     $('#import_btn').removeClass('d-none');
                //     total_users = resp.total_users
                // }
                // $('#validate_btn').addClass('d-none');
                $('#import_btn').removeClass('d-none');
                total_users = resp.total_users
            });
            //show export btn
            $('#export_btn').removeClass('d-none');
        });

        //when click on import btn show admin ddl modal
        $("#import_btn").click(function(e){
            // $('#total_users_count').html(total_users);
            // $('#admin_modal').modal('show');
            $('#general_form').attr('action', "{{ route('admin.users.migrate_update_user_expiration') }}");
            //set inputs in general form
            $('#general_form #task_id').val(task_id);
            // $('#general_form #admin_id').val(admin_id);
            $('#general_form #type').val('import');
            $('#general_form').submit();
        });

        //whens click on modal submit button then submit the form for importing users from users_tmp to users table
        $('#modal_submit').click(function(e){
            e.preventDefault();
            var admin_id = $('#admin_modal #admin_id option:selected').val();
            //set action
            $('#general_form').attr('action', "{{ route('admin.users.migrate') }}");
            //set inputs in general form
            $('#general_form #task_id').val(task_id);
            $('#general_form #admin_id').val(admin_id);
            $('#general_form #type').val('import');
            $('#general_form').submit();
        });

        // $('#validate_btn').click(function(e){
        //      //set action
        //     // $('#validation_form #task_id').val(task_id);
        //     // $('#validation_modal').modal('show');
        //     // $('#validation_form').submit();
        //     // e.preventDefault();
        //     // var admin_id = $('#admin_modal #admin_id option:selected').val();
        //     //set action
        //     // $('#general_form').attr('action', "{{ route('admin.users.validate_update_users_expiration') }}");
        //     //set inputs in general form
        //     $('#general_form #task_id').val(task_id);
        //     // $('#general_form #admin_id').val(admin_id);
        //     $('#general_form #type').val('validate');
        //     // alert('dones');
        //     $('#general_form').submit();
        // });

        //delete btn 
        $('#delete_btn').click(function(){
            $('#general_form').attr('action', "{{ route('admin.users.delete_update_user_expiration') }}");
            $('#general_form #task_id').val(task_id);
            $('#general_form').submit();
        });
        //export btn 
        $('#export_btn').click(function(){
            $('#export_form').attr('action', "{{ route('admin.users.export_update_user_expiration') }}");
            $('#export_form #task_id').val(task_id);
            $('#export_form').submit();
        });
        $(document).on('click', '.delete_check_box', function(){
            var ids = [];
            var count = 0;
            $(".delete_check_box:checked").each(function(index) {
                ++count;
                ids.push($(this).val());
            });
            
            if(count  > 0){
                $('#delete_users').removeClass('d-none');
                $('#general_form #user_ids').val(ids);
            }else{
                $('#delete_users').addClass('d-none');
            }           
        });

        $('#delete_users').click(function(){
            $('#general_form').attr('action', "{{ route('admin.users.delete_multiple_update_user_expiration') }}");
            $('#general_form').submit();
        });
    });
</script>
@endsection
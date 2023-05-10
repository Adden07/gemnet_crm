@extends('layouts.admin')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Users</li>
                    <li class="breadcrumb-item active">Import Users </li>

                </ol>
            </div>
            <h4 class="page-title">Import Users </h4>
            </h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card-box" style="padding: 10px 24px">
            <form action="{{ route('admin.users.import_update_user_excel') }}" class="ajaxForm" method="post" id="excel_form" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="form-group col-md-5">
                        <label for="logo">Users Excel File</label>
                        <div class="input-group">
                            <div class="form-group w-100">
                                <input class="form-control" type="file" id="excel_file" name="excel_file">
                              </div>
                        </div>
                    </div>
                    <div class="form-group col-md-5">
                        <label for="">Select Admin</label>
                        <select class="form-control" name="admin_id" id="admin_id">
                            <option value="">Select Admin</option>
                            @foreach($admins AS $admin)
                                <option value="{{ $admin->hashid }}">{{ $admin->name }}- ({{ $admin->username }}) - ({{ $admin->users_count }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mt-3">
                        <input type="submit" class="form-control btn-primary" value="Upload">
                    </div>
                </div>
            </form>
            <div class="row">
                <div class="col-md-10">
                    <div class="form-group">
                        <label for="">Select Admin</label>
                        <select class="form-control" name="export_admin_id" id="export_admin_id" required>
                            <option value="">Select Admin</option>
                            @foreach($admins AS $admin)
                                <option value="{{ $admin->hashid }}">{{ $admin->name }}- ({{ $admin->username }}) - ({{ $admin->users_count }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md">
                    <input type="submit" class="btn btn-primary mt-3 d-none" name="submit" value="Validate Users" id="validate_btn">
                    <input type="submit" class="btn btn-primary mt-3 d-none" name="submit" value="Import Users"  id="import_btn">
                    <input type="submit" class="btn btn-danger mt-3 d-none" name="" value="Delete" id="delete_btn">
                    <button class="btn btn-info mt-3" id="export_btn">Export</button>
                </div>
            </div>
            <form action="" method="POST" id="general_form" class="ajaxForm">
                @csrf
                <input type="hidden" name="task_id" id="task_id">
                <input type="hidden" name="admin_id" id="admin_id">
                <input type="hidden" name="type" id="type" value="delete">
            </form>
            <form action="" method="POST" id="export_form" class="">
                @csrf
                <input type="hidden" name="admin_id" id="admin_id">
            </form>
        </div>
    </div>
    
    <div class="col-lg-12">
        <div class="card-box">
            <table class="table table-bordered w-100 nowrapp" id="online_users">
                <thead>
                    <tr>
                        <th width="20">#</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Nic</th>
                        <th>Mobile</th>
                        <th>Address</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
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
<div class="modal fade" id="validation_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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
</div>

  <div class="modal fade" id="users_modal" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="{{ route('admin.users.update_import') }}" method="POST" class="ajaxForm" id="users_modal_form">

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
                                url : "{{ route('admin.users.update_users') }}",
                                data:function(d){
                                    // d.status = $('#user_status').val(),
                                    d.search = $('input[type="search"]').val()
                                },
                            },
                            columns : [
                                { data:'DT_RowIndex', name:'DT_RowIndex', orderable:true, searchable:false },
                                // { data:'task_id', name:'task_id', searchable:true },
                                // { data:'task_datetime', name:'task_datetime' },
                                // { data:'city', name:'city' },
                                { data:'name', name:'name', searchable:true },
                                { data:'username', name:'username', searchable:true },
                                // { data:'password', name:'password' },
                                { data:'nic', name:'nic', searchable:true },
                                { data:'mobile', name:'mobile', searchable:true },
                                { data:'address', name:'address', searchable:true },
                                // { data:'package', name:'packages.name', searchable:true },
                                // { data:'expiration', name:'expiration', searchable:true },
                                // { data:'status', name:'status' },


                            ]
                        });  

        $('#user_status').change(function(){
            table.draw();
        });


        $(document).on('click', '.users_form', function(){//show errors
            var route = $(this).attr('route');
            
            getAjaxRequests(route, '', 'get', function(resp){
                $('#users_modal_form').html(resp.html);
            })
            $('#users_modal').modal('show');
        });
        var task_id = null //declare task id globally
        var total_users = 0;
        //when there is change in task id
        $('#task_id').change(function(){

            task_id = parseInt($(this).val());
            var route = "{{ route('admin.users.task_status', ':task_id') }}";
            route = route.replace(':task_id', task_id);
            
            getAjaxRequests(route, '', 'GET', function(resp){
                if(resp.status == 'validate'){
                    $('#validate_btn').removeClass('d-none');
                    $('#import_btn').addClass('d-none');
                }else if(resp.status == 'import'){
                    $('#validate_btn').addClass('d-none');
                    $('#import_btn').removeClass('d-none');
                    total_users = resp.total_users
                }
            });
            //show export btn
            $('#export_btn').removeClass('d-none');
        });

        //when click on import btn show admin ddl modal
        $("#import_btn").click(function(e){
            $('#total_users_count').html(total_users);
            $('#admin_modal').modal('show');
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

        $('#validate_btn').click(function(e){
             //set action
            $('#validation_form #task_id').val(task_id);
            $('#validation_modal').modal('show');
        });

        //delete btn 
        $('#delete_btn').click(function(){
            $('#general_form').attr('action', "{{ route('admin.users.delete_import') }}");
            $('#general_form #task_id').val(task_id);
            $('#general_form').submit();
        });
        //export btn 
        $('#export_btn').click(function(){
            var export_admin_id = $('#export_admin_id').val();
            if(export_admin_id ==  ''){
                Swal.fire({
                    type: 'error',
                    title: 'Error',
                    text: 'Please select the admin id',
                });
                return false;
            }
            $('#export_form').attr('action', "{{ route('admin.users.export_update_users') }}");
            $('#export_form #admin_id').val(export_admin_id);
            $('#export_form').submit();
        });
    });

</script>
@endsection
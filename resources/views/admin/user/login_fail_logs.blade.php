@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Users</li>
                    <li class="breadcrumb-item active">Login Fail Logs </li>

                </ol>
            </div>
            <h4 class="page-title">All Login Fail Logs ({{ $login_fail_logs->total() }})</h4>
        </div>
    </div>
</div>

<div class="row">
    {{-- <div class="col-lg-12">
        <div class="card-box"></div>
    </div> --}}
    <div class="col-lg-12">
        <div class="card-box">
            <div class="col-md-12">
                <div class="form-gorup col-md-6">
                    <label for="">Reasons</label>
                    <select name="reasons" id="reasons" class="form-control">
                        <option value="">Select Reason</option>
                        @foreach($reasons AS $reason)
                            <option value="{{ $reason->reply }}">{{ $reason->reply }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-between">
                {{-- <h4 class="header-title">All Login Fail Logs</h4> --}}
                {{-- <a href="{{ route('admin.staffs.add') }}" class="btn btn-sm btn-primary">Add Staff</a> --}}
            </div>
            {{-- <p class="sub-header">Following is the list of all the login fail logs.</p> --}}
            <table class="table  table-bordered w-100 nowrapp" id="login_fail_table">
                <thead>
                    <tr>
                        <th width="20">S.No</th>
                        <th>Datetime</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Reject Reason</th>
                        <th>Mac</th>
                        {{-- <th>Port</th> --}}
                        @if(auth()->user()->user_type == 'admin')
                            <th>NAS</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    {{-- @php $counter = 300 * ($login_fail_logs->currentPage() - 1)  @endphp
                    @foreach($login_fail_logs AS $log)
                        <tr>
                            <td>{{ ++$counter }}</td>
                            <td>{{ date('d-M-Y H:i:s',strtotime($log->authdate)) }}</td>
                            <td>{{ $log->username }}</td>
                            <td>{{ $log->pass }}</td>
                            <td>
                                @if($log->reply == 'Access-Reject - ')
                                    <span class="badge badge-dark">{{ $log->reply }}</span>
                                @elseif($log->reply == 'Access-Reject - Wrong-Pass')
                                    <span class="badge badge-danger">{{ $log->reply }}</span>
                                @elseif($log->reply == 'Access-Reject - Wrong Mac Address')
                                    <span class="badge badge-primary">{{ $log->reply }}</span>
                                @elseif($log->reply == 'Access-Reject - User is Disabled')
                                    <span class="badge badge-success">{{ $log->reply }}</span>
                                @elseif($log->reply == 'Access-Reject - User Already Online')    
                                    <span class="badge badge-secondary">{{ $log->reply }}</span>    
                                @else
                                    <span class="badge badge-warning">{{ $log->reply }}</span>    
                                @endif
                            </td>
                            <td>{{ $log->mac }}</td>
                            @if(auth()->user()->user_type == 'admin')
                                <td>{{ $log->nasipaddress }}</td>
                            @endif
                        </tr>
                    @endforeach --}}
                </tbody>
            </table>
           {{-- <span class="float-right">{{ $login_fail_logs->links() }}</span> --}}
        </div>
    </div>
</div>
<div class="modal fade bd-example-modal-lg" id="details_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
</div>
<!-- Package Activation And Renew Modal -->
<div class="modal fade" id="package" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="package_modal_title"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        <form action="{{ route('admin.packages.update_user_package') }}" class="ajaxForm" id="add_package_form" method="GET">
            @csrf
            <div class="modal-body">
   
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <input type="submit" class="btn btn-primary" value="Update" id="package_modal_submit">
            </div>
        </form>
        </div>
    </div>
</div>
@endsection

@section('page-scripts')
@include('admin.partials.datatable', ['load_swtichery' => true])
<script>
        $(document).ready(function(){
        var table = $('#login_fail_table').DataTable({
                    processing: true, 
                    serverSide: true,
                    "pageLength": 300,
                    "lengthMenu": [300,500,1000,1500],
                    "dom": '<"top"ifl<"clear">>rt<"bottom"ip<"clear">>',

                    ajax:{
                        url : "{{ route('admin.users.login_fail_log') }}",
                        data:function(d){
                                    d.reasons = $('#reasons').val(),
                                    d.search = $('input[type="search"]').val()
                        },
                    },
                    
                    columns : [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable:false,searchable:false},
                        {data:'dateTime', name:'authdate'},
                        {data:'username', name:'username'},
                        {data:'pass', name:'pass'},
                        {data:'reason', name:'reason'},
                        {data:'mac',name:'mac'},
                        @if(auth()->user()->user_type == 'admin')
                            {data:'nas', name:'nas'},
                        @endif
                    ],
            });

        $('input[type="search"]').keyup(function(){
            table.draw();
       });
       $('#reasons').change(function(){
            table.draw();
       });
    });
    //display password in details modal
    $(document).on('click','.pas',function(){
        $(this).children('i').toggleClass('fa-eye');
        $(this).children('i').toggleClass('fa-eye-slash');
        var attr_type = $(this).parent().parent().find('input').attr('type');
        if(attr_type == 'password'){
            $(this).parent().parent().find('input').attr('type','text');
        }else{
            $(this).parent().parent().find('input').attr('type','password');
        }
    });

    //package activation modal 
    $('.add_package').click(function(e){
        e.preventDefault();
        var route = $(this).attr('href');
        var title = $(this).attr('title');
        var status = $(this).data('status');
        //set package modal submit value
        if(status == 'registered'){
            status = 'Activate';
        }else{
            status = 'Renew'
        }
        getAjaxRequests(route, '', 'GET', function(resp){
            $('.modal-body').html(resp.html);
            $('#package').modal('show');
            $('#package_modal_title').html(title);
            $('#package_modal_submit').val(status);//change modal submit value
        });
    });

    //validation on package activation modal
    $('#add_package_form').validate({
    rules : {
        package_id:{
            required:true,
            maxlength:191,
        }
    },
    highlight:function(element){
        if($(element).attr('name') == 'package_id'){
            $(element).addClass('is-invalid');
        }
    },
    unhighlight:function(element){
        if($(element).attr('name') == 'package_id'){
            $(element).removeClass('is-invalid');
            $(element).addClass('is-valid');
        }
    }
    });

    //remoev disabled attribute when form submit add_package_form
    $('#add_package_form').bind('submit',function(){
        $('#package_id').removeAttr('disabled');
        $('#userstatus').removeAttr('disabled');
        $('#username').removeAttr('disabled');
    })

</script>
@endsection
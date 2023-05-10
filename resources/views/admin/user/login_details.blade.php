@extends('layouts.admin')
@section('content')
<style>
    .select2-container{width: 100% !important}
    .switch {
      position: relative;
      display: inline-block;
      width: 35px;
      height: 22px;
    }
    
    .switch input { 
      opacity: 0;
      width: 0;
      height: 0;
    }
</style>
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Users</li>
                    <li class="breadcrumb-item active">Login Details </li>

                </ol>
            </div>
            <h4 class="page-title">Login Details </h4>
            </h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card-box" style="padding: 10px 24px">
            <form action="" method="GET" id="login_details_form">
                @csrf
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="">From Date</label>
                        <input type="date" class="form-control" name="from_date" id="from_date" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="">To Date</label>
                        <input type="date" class="form-control" name="to_date" id="to_date">
                    </div>
                    @if(auth()->user()->user_type == 'admin')
                        <div class="form-group col-md-3">
                            <label for="">Search Type</label>
                            <select class="form-control" name="type" id="type">
                                <option value="">Select Type</option>
                                <option value="username">Username</option>
                                <option value="ip">IP</option>
                                <option value="mac_address">Mac Address</option>
                            </select>
                        </div>
                    @endif
                    <div class="form-group col-md-3 @if(auth()->user()->user_type == 'admin') d-none @endif" id="username_div">
                        <label for="">Usernames</label>
                        <select class="form-control" name="username" id="username">
                            <option value="">Select User</option>
                            @foreach($users AS $user)
                                <option value="{{ $user->username }}">{{ $user->username }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3 d-none" id="ip_div">
                        <label for="">IP</label>
                        <input type="text" class="form-control" placeholder="192.168.108.105" name="ip" id="ip">
                    </div>
                    <div class="form-group col-md-3 d-none" id="macaddress_div">
                        <label for="">Mac Address</label>
                        <input type="text" class="form-control" placeholder="Macaddress" name="macaddress" id="macaddress">
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="card-box">
            <table class="table table-bordered w-100 nowrapp" id="online_users">
                <thead>
                    <tr>
                        <th width="20">#</th>
                        <th>Username</th>
                        <th>DC Reason</th>
                        <th>Login</th>
                        <th>Logoff</th>
                        <th>Uptime</th>
                        <th>Mac Address</th>
                        <th>IP Address</th>
                        <th>Up</th>
                        <th>Down</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
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
                            // "order": [[ 0, "desc" ]],
                            "pageLength":300,
                            "lengthMenu":[300,500,1000,1500],
                            "dom": '<"top"ifl<"clear">>rt<"bottom"ip<"clear">>',

                            ajax:{
                                url : "{{ route('admin.users.login_detail') }}",
                                data:function(d){
                                    d.from_date     = $('#from_date').val(),
                                    d.to_date       = $('#to_date').val(),
                                    d.username      = $('#username').val();
                                    d.ip            = $('#ip').val();
                                    d.macaddress    = $('#macaddress').val();
                                    d.type          = $('#type').val();
                                    d.search        = $('input[type="search"]').val()
                                },
                            },
                            columns : [
                                { data:'DT_RowIndex', name:'DT_RowIndex', orderable:false, searchable:false },
                                { data:'username', name:'username', orderable:true, searchable:true },
                                { data:'dc_reason', name:'acctterminatecause', orderable:true, searchable:true },
                                { data:'login', name:'acctstarttime', orderable:true, searchable:true },
                                { data:'logoff', name:'acctstoptime', orderable:true, searchable:true },
                                { data:'uptime', name:'uptime' },
                                { data:'macaddress', name:'nasipaddress', orderable:true, searchable:true },
                                { data:'ip', name:'framedipaddress', orderable:true, searchable:true },
                                { data:'upload', name:'upload' },
                                { data:'download', name:'download' },
                                { data:'total', name:'total' },


                            ]
                        });
                        
            $('#from_date, #to_date, #username, #macaddress, #ip').change(function(){
                table.draw();
            });
            $('#macaddress, #ip').keyup(function(){
                table.draw();
            });
        })


    $(document).ready(function(){
        $('#username').select2({
            placeholder : 'Select areas',
        });
    });

    //when there is change in type then dispaly the input fields according to type
    $('#type').change(function(){
        var type = $(this).val();
        
        if(type == 'username'){
            $('#username_div').removeClass('d-none');
            $('#ip_div').addClass('d-none');
            $('macaddress_div').addClass('d-none');
            //set value to null
            $('#ip').val(null);
            $('#macaddress').val(null);
        }else if(type == 'ip'){
            $('#ip_div').removeClass('d-none');
            $('#username_div').addClass('d-none');
            $('macaddress_div').addClass('d-none');
            //set values to null
            $('#username').val(null);
            $('#macaddress').val(null);
        }else if(type == 'mac_address'){
            $('#macaddress_div').removeClass('d-none');
            $('#ip_div').addClass('d-none');
            $('#username_div').addClass('d-none');
             //set values to null
            $('#username').val(null);
            $('#ip').val(null);

        }
    });
</script>
@endsection
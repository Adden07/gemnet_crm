@extends('layouts.admin')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Users</li>
                    <li class="breadcrumb-item active">Online Users </li>

                </ol>
            </div>
            <h4 class="page-title">Online Users </h4>
                {{-- @if(request()->has('status'))
                    @if(request()->get('status') == 'active')
                        All Active
                    @elseif(request()->get('status') == 'expired')
                        All Expired
                    @elseif(request()->get('status') == 'all')
                        All Online
                    @endif
                @else
                    All Online
                @endif 
                 Users --}}
                 {{-- All Online Users | Active Users {{ $radaccts->where('framedipaddress','like','172%')->count('framedipaddress') }} --}}
            </h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card-box" style="padding: 10px 24px">
            <form action="{{ route('admin.users.online_user') }}" method="GET">
                <div class="row">
                    <div class="form-group mb-0 col-md-5">
                        <label for="">Time Interval</label>
                        <select class="form-control" name="time_interval" id="time_interval">
                            <option value="">Select Interval</option>
                            <option value="15000" @if(request()->get('time_interval') == 15000) selected @endif>15 Secs</option>
                            <option value="30000" @if(request()->get('time_interval') == 30000) selected @endif>30 Secs</option>
                            <option value="45000" @if(request()->get('time_interval') == 45000) selected @endif>45 Secs</option>
                            <option value="60000" @if(request()->get('time_interval') == 60000) selected @endif>60 Secs</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="w-100">&nbsp;	</label>
                        <a href="" class="btn btn-primary btn-sm" id="refresh"><i class="icon-refresh"></i></a>
                    </div>
                    <div class="col-md-5">
                        <label for="">Select Status</label>
                        <select class="form-control" name="status" id="user_status">
                            <option value="all">All</option>
                            <option value="active">Active</option>
                            <option value="expired">Expired</option>
                        </select>
                    </div>
                    {{-- <div class="col-md-1">
                        <label class="w-100">&nbsp;	</label>
               
                        <input type="submit" class="btn btn-primary" value="Search">
                    </div> --}}
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
                        <th>Name</th>
                        <th>Username</th>
                        <th>Login</th>
                        <th>Uptime</th>
                        <th>Mac Address</th>
                        <th>IP Address</th>
                        <th>Up</th>
                        <th>Down</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- <tr>
                        <td></td>
                    </tr> --}}
                    {{-- @php $counter = 300 * ($radaccts->currentPage() - 1)  @endphp
                    @foreach($radaccts AS $radacct)
                        <tr>
                            <td>{{ ++$counter }}</td>
                            <td>{{ $radacct->user->name }}</a></td>
                            <td><a href="{{ route('admin.users.profile',['id'=>hashids_encode($radacct->user->id)]) }}" target="_blank">{{ $radacct->user->username }}</a></td>
                            <td>{{ date('d-M-Y H:i:s',strtotime($radacct->acctstarttime)) }}</td>
                            <td>
                                @php
                                    $date1 = date_create($radacct->acctstarttime);
                                    $date2 = date_create(date('Y-m-d H:i:s'));

                                    $dateDifference = date_diff($date1, $date2)->format('%ad %Hh %im %ss');

                                    echo $dateDifference;

                                @endphp
                            </td>
                            <td>{{ $radacct->callingstationid }}</td>
                            <td>{{ $radacct->framedipaddress }}</td>
                            <td><span class="badge badge-primary">{{ number_format($radacct->acctinputoctets/pow(1024,3),2) }}GB</span></td>
                            <td><span class="badge badge-primary">{{ number_format($radacct->acctoutputoctets/pow(1024,3),2) }}GB</span></td>
                            <td>
                                <a href="#" class="btn btn-danger btn-sm" onclick="ajaxRequest(this)" data-url="{{ route('admin.users.kick',['id'=>hashids_encode($radacct->user->id)]) }}" title="kick user">
                                    kick
                                </a>
                            </td>
                        </tr>
                    @endforeach --}}
                </tbody>
            </table>
            {{-- <span class="float-right">{{ $radaccts->links() }}</span> --}}
           {{-- <span class="float-right">{{ $users->links() }}</span> --}}
        </div>
    </div>
</div>

@endsection

@section('page-scripts')
@include('admin.partials.datatable', ['load_swtichery' => true])
<script>
    //if time is set then refresh the page
    $(document).ready(function(){

        var table =      $('#online_users').DataTable({
                            processing:true,
                            serverSide:true,
                            "order": [[ 0, "desc" ]],
                            "pageLength":300,
                            "lengthMenu":[300,500,1000,1500],
                            "dom": '<"top"ifl<"clear">>rt<"bottom"ip<"clear">>',

                            ajax:{
                                url : "{{ route('admin.users.online_user') }}",
                                data:function(d){
                                    d.status = $('#user_status').val(),
                                    d.search = $('input[type="search"]').val()
                                },
                            },
                            columns : [
                                { data:'DT_RowIndex', name:'DT_RowIndex', orderable:true, searchable:false },
                                { data:'name', name:'users.name' },
                                { data:'username', name:'radacct.username', orderable:true, searchable:true},
                                { data:'login', name:'radacct.acctstarttime' },
                                { data:'uptime', name:'uptime',searcchable:false },
                                { data:'mac_address', name:'radacct.callingstationid' },
                                { data:'ip_address', name:'radacct.framedipaddress' },
                                { data:'up',name:'up', searchable:false },
                                { data:'down', name:'down', searchable:false },
                                { data:'kick', name:'kick', searchable:false, orderable:false }

                            ]
                        });  

        $('#user_status').change(function(){
            table.draw();
        });


        if("{{ request()->has('time_interval') }}"){
            var time = "{{ request()->get('time_interval') }}";
            if(time != ''){
                setTimeout(function(){
                    location.reload();
                },time);
            }
        }
        
    });
    //set the page refresh time
    $('#refresh').click(function(e){
        e.preventDefault();
        var time = $("#time_interval").val();
        
        if(time != ''){
            if("{{ request()->has('status') }}"){
                var route = "{{ route('admin.users.online_user') }}"+'?time_interval='+time+'&status='+"{{ request()->get('status') }}";
                window.location.href=route;
            }else{
                var route = "{{ route('admin.users.online_user') }}"+'?time_interval='+time;
                window.location.href=route;
            }
        }else{
            window.location.href="{{ route('admin.users.online_user') }}";
        }
    });
</script>
@endsection
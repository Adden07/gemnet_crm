@extends('layouts.admin')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Users</li>
                    <li class="breadcrumb-item active">Offline Users </li>

                </ol>
            </div>
            <h4 class="page-title">{{ request()->get('status') ?? 'Offline' }} Users </h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card-box" style="padding: 10px 24px">
            <div class="row">
                <div class="col-lg-12">
                    <form action="" method="GET">
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
                            <div class="form-group mb-0 col-md-5">
                                <label for="">Last Logout Filter</label>
                                <select class="form-control" name="last_logout_time" id="last_logout_time">
                                    <option value="">Select Last Logout</option>
                                    <option value="{{ date('Y-m-d H:i:s',strtotime('-24 hours')) }}">24 Hours</option>
                                    <option value="{{ date('Y-m-d H:i:s',strtotime('-48 hours')) }}">48 Hours</option>
                                    <option value="{{ date('Y-m-d H:i:s',strtotime('-7 days')) }}">7 Days</option>
                                </select>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="col-lg-6">
                    {{-- <form action="{{ route('admin.users.ofline_user') }}" method="GET">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-10">
                                <label for="">Days</label>
                                <select class="form-control" name="time" id="time">
                                    <option value="24">24 Hours</option>
                                    <option value="48">Last 48 Hours</option>
                                    <option value="7">Over 7 Days</option>
                
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="submit" class="btn btn-primary mt-3" value="Search">
                            </div>
                        </div>
                    </form> --}}
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-12">
        <div class="card-box">
            <table class="table  table-bordered w-100 nowrapp" id="ofline_table">
                <thead>
                    <tr>
                        <th width="20">S.No</th>
                        <th>Username</th>
                        <th>Name</th>
                        @if(auth()->user()->user_type != 'admin')
                            <th>Remarks</th>
                        @endif
                        {{-- <th>Remarks</th> --}}
                        <th>Address</th>
                        <th>Expiration</th>
                        <th>Last Login</th>
                        <th>Last Logout</th>
                        {{-- <th>Login</th>
                        <th>Mac Address</th>
                        <th>IP Address</th>
                        <th>Status</th>
                        <th>Upload</th>
                        <th>Download</th> --}}
                    </tr>
                </thead>
                <tbody>
                    {{-- @php  $counter = 300 * ($users->currentPage() - 1) @endphp
                    @foreach($users AS $user)
                        <tr>
                            <td>
                                {{ ++$counter }}
                            </td>
                            <td><a href="{{ route('admin.users.profile',['id'=>hashids_encode($user->id)]) }}" target="_blank">{{ $user->username }}</a></td>
                            <td>{{ $user->name }}</td>
                            @if(auth()->user()->user_type != 'admin')
                            <td></td>
                            @endif
                            <td>{{ $user->address }}</td>
                            <td>{{ date('d-M-Y H:i:s',strtotime($user->current_expiration_date)) }}</td>
                            <td>
                                @if(date('d',strtotime($user->last_login_time)) == date('d'))
                                    <span class="badge badge-success">{{ $user->last_login_time }}</span>
                                @elseif(date('d',strtotime($user->last_login_time)) == date('d',strtotime('-1 day')))
                                    <span class="badge badge-warning">{{ $user->last_login_time }}</span>
                                @else
                                    <span class="badge" style='background-color:orangered'>{{ $user->last_login_time }}</span> 
                                @endif
                            </td>
                            <td>
                                @if(date('d',strtotime($user->last_logout_time)) == date('d'))
                                    <span class="badge badge-success">{{ $user->last_logout_time }}</span>
                                @elseif(date('d',strtotime($user->last_logout_time)) == date('d',strtotime('-1 day')))
                                    <span class="badge badge-warning">{{ $user->last_logout_time }}</span>
                                @else
                                    <span class="badge" style='background-color:orangered'>{{ $user->last_logout_time }}</span> 
                                @endif
                            </td>
                        </tr>
                    @endforeach --}}
                </tbody>
            </table>
           {{-- <span class="float-right">{{ $users->links() }}</span> --}}
        </div>
    </div>
</div>

@endsection

@section('page-scripts')
@include('admin.partials.datatable', ['load_swtichery' => true])
<script>
    $(document).ready(function(){
        var table = $('#ofline_table').DataTable({
                        processing: true, 
                        serverSide: true,
                        "order": [[ 0, "desc" ]],
                        "pageLength": 300,
                        "lengthMenu": [300,500,1000,1500],
                        "dom": '<"top"ifl<"clear">>rt<"bottom"ip<"clear">>',

                        ajax:{
                            url : "{{ route('admin.users.ofline_user') }}",
                            data:function(d){
                                    d.search = $('input[type="search"]').val(),
                                    d.last_logout_time = $('#last_logout_time').val()
                            },
                        },
                        
                        columns : [
                            {data : 'DT_RowIndex', name : 'DT_RowIndex', orderable:true, searchable:false},
                            { data: 'username', name : 'users.username' },
                            { data : 'name', name : 'users.name' },
                            @if(auth()->user()->user_type != 'admin')
                                { data: 'remarks', name : 'remarks' },
                            @endif
                            { data : 'address', name : 'address' },
                            { data: 'expiration', name : 'current_expiration_date' },
                            { data : 'last_login', name : 'last_login_time' },
                            { data : 'last_logout', name : 'last_logout_time' },
                        ],
                });
        $('input[type="search"]').keyup(function(){
            table.draw();
       });
       $('#last_logout_time').change(function(){
            table.draw();
       });
    });

    //if time is set then refresh the page
    $(document).ready(function(){
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
                var route = "{{ route('admin.users.ofline_user') }}"+'?time_interval='+time+'&status='+"{{ request()->get('status') }}";
                window.location.href=route;
            }else{
                var route = "{{ route('admin.users.ofline_user') }}"+'?time_interval='+time;
                window.location.href=route;
            }
        }else{
            window.location.href="{{ route('admin.users.ofline_user') }}";
        }
    });
</script>
@endsection
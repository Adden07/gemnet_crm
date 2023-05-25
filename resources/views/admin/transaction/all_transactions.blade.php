@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Finance</li>
                    <li class="breadcrumb-item active">Transactions</li>
                </ol>
            </div>
            <h4 class="page-title">Transactions</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="header-title">Filters</h4>
            </div>
            <form action="{{ route('admin.accounts.payments.index') }}" method="GET">
                @csrf
                <div class="row">
                    {{-- <div class="form-group col-md-3">
                        <label for="">Receiver Username</label>
                        <select class="form-control" name="username" id="username">
                            <option value="">Select Username</option>
                            @foreach($admins as $admin)
                                <option value="{{ $admin->hashid }}" @if(request()->has('username') && request()->get('username') == $admin->hashid) selected @endif>{{ $admin->username }}</option>
                            @endforeach
                        </select>
                    </div> --}}
                    {{-- <div class="form-group col-md-3">
                        <label for="">Added By</label>
                        <select class="form-control" name="added_by" id="added_by">
                            <option value="">Select Added By</option>
                            <option value="person" @if(request()->has('added_by') && request()->get('added_by') == 'person') selected @endif>Person</option>
                            <option value="system" @if(request()->has('added_by') && request()->get('added_by') == 'system') selected @endif>System</option>
                        </select>
                    </div> --}}
                    <div class="form-group col-md-3">
                        <label for="">From Date</label>
                        <input type="date" class="form-control" value="{{ (request()->has('from_date')) ? date('Y-m-d',strtotime(request()->get('from_date'))) : date('Y-m-d') }}" name="from_date" id="from_date">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="">To Date</label>
                        <input type="date" class="form-control" value="{{ (request()->has('to_date')) ? date('Y-m-d',strtotime(request()->get('to_date'))) : date('Y-m-d') }}" name="to_date" id="to_date">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="">Type</label>
                        <select name="type" id="type" class="form-control">
                            <option value="all">All</option>
                            <option value="0">Invoice</option>
                            <option value="1">Payment</option>

                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="">Users</label>
                        <select class="form-control select2" name="user_id" id="user_id">
                            <option value="">Select user</option>
                            @foreach($users AS $user)
                                <option value="{{ $user->hashid }}">{{ $user->name }}-({{ $user->username }})</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- @if(auth()->user()->user_type == 'admin')
                        <div class="form-group col-md-3">
                            <label for="">Franchises</label>
                            <select class="form-control" name="franchise_id" id="franchise_id">
                                <option value="">Select Franchise</option>
                                @foreach($franchises AS $franchise)
                                    <option value="{{ $franchise->hashid }}">{{  $franchise->name }} ({{ $franchise->username }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="">Dealers</label>
                            <select class="form-control" name="dealer_id" id="dealer_id">
                                <option value="">Select Dealer</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="">Sub Dealers</label>
                            <select class="form-control" name="subdealer_id" id="subdealer_id">
                                <option value="">Select Sub Dealer</option>
                            </select>
                        </div>
                    @endif --}}
                </div>
            </form>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <div class="table-responsive">
                <table class="table table-bordered w-100 nowrap" id="payment_table">
                    <thead>
                        <tr>
                            <th width="10">#</th>
                            <th>Date</th>
                            <th>Transaction ID</th>
                            @if(auth()->user()->user_type == 'admin')
                                <th>Admin</th>
                            @endif
                            <th>User</th>
                            <th>Amount</th>
                            <th>Old Bal</th>
                            <th>New Bal</th>
                            <th>Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-scripts')
@include('admin.partials.datatable', ['load_swtichery' => true])
<script>
    $(document).ready(function(){
        var table = $('#payment_table').DataTable({
                    processing: true, 
                    serverSide: true,
                    "order": [[ 0, "desc" ]],
                    "pageLength": 300,
                    "lengthMenu": [300,500,1000,1500],
                    "dom": '<"top"ifl<"clear">>rt<"bottom"ip<"clear">>',

                    ajax:{
                        url : "{{ route('admin.accounts.transactions.index') }}",
                        data:function(d){
                                    // d.username        = $('#username').val(),
                                    // d.added_by        = $('#added_by').val(),
                                    d.from_date       = $('#from_date').val(),
                                    d.to_date         = $('#to_date').val(),
                                    d.search          = $('input[type="search"]').val()
                                    // d.franchise_id    = $('#franchise_id').val();
                                    // d.dealer_id       = $('#dealer_id').val();
                                    // d.subdealer_id    = $('#subdealer_id').val();
                                    d.user_id         = $('#user_id').val();
                                    d.type    = $('#type').val();
                        },
                    },
                    
                    columns : [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable:true,searchable:false},
                        {data:'date', name:'created_at', orderable:true, searchable:true},  
                        {data:'transaction_id', name:'transaction_id', orderable:false, searchable:false},
                        @if(auth()->user()->user_type == 'admin')  
                            {data:'added_by', name:'admin.name',orderable:false,searchable:true},
                        @endif
                        {data:'user', name:'user.name',orderable:false,searchable:true},
                        {data:'amount', name:'amount',orderable:true,searchable:true},
                        {data:'old_balance', name:'old_balance',orderable:true,searchable:true},
                        {data:'new_balance', name:'new_balance',orderable:true,searchable:true},
                        {data:'type', name:'payments.type',orderable:true,searchable:true}
                    ],
                });


       $('#username,#from_date,#to_date,#added_by,#type,#user_id').change(function(){
            table.draw();
       });
    });
    //get dealers of selected franchise
    $('#franchise_id').change(function(){
        var id = $(this).val();
        var route = ""
        route = route.replace(':id',id);
        //send ajax request when value is set
        if(id.length != 0){
            getAjaxRequests(route,'','GET',function(resp){
                $('#dealer_id').html("<option value='' selected>Select Dealer</option>"+resp.html);
            });
        }
    });

    //get subdealers of selected dealer
    $('#dealer_id').change(function(){
        var id   = $(this).val();
        var route = "{{ route('admin.accounts.invoices.get_subdealers',':id') }}";
        var route = route.replace(':id',id);
        //send ajax request when value is set
        if(id.length != 0){
            getAjaxRequests(route, '', 'GET', function(resp){
                $('#subdealer_id').html("<option value=''>Select Sub Dealer</option>"+resp.html);
            });
        }
    });

    //select 2
    $('#username').select2({
        placeholder: 'Select Receiver'
    });
</script>
@endsection

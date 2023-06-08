@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Finance</li>
                    <li class="breadcrumb-item active">Payments</li>
                </ol>
            </div>
            <h4 class="page-title">Payments</h4>
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
                    <div class="form-group col-md-3">
                        <label for="">Username</label>
                        <select class="form-control select2" name="username" id="receiver_id">
                            <option value="all">All</option>
                            @foreach($receivers as $user)
                                <option value="{{ $user->hashid }}" @if(request()->has('username') && request()->get('username') == $user->hashid) selected @endif>{{ $user->username }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="">Added By</label>
                        <select class="form-control select2" name="added_by" id="admin_id">
                            <option value="">Select Added By</option>
                            <option value="all">All</option>
                            @foreach($admins as $user)
                                <option value="{{ $user->hashid }}" @if(request()->has('username') && request()->get('username') == $user->hashid) selected @endif>{{ $user->username }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="">Mode</label>
                        <select class="form-control" name="type" id="type">
                            <option value="all" @if(request()->has('type') && request()->get('type') == 'all') selected @endif>All</option>
                            <option value="cash" @if(request()->has('type') && request()->get('type') == 'cash') selected @endif>Cash</option>
                            <option value="online" @if(request()->has('type') && request()->get('type') == 'online') selected @endif>Online</option>
                            <option value="cheque" @if(request()->has('type') && request()->get('type') == 'cheque') selected @endif>Cheque</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="">From Date</label>
                        <input type="date" class="form-control" value="{{ (request()->has('from_date')) ? date('Y-m-d',strtotime(request()->get('from_date'))) : date('Y-m-d') }}" name="from_date" id="from_date">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="">To Date</label>
                        <input type="date" class="form-control" value="{{ (request()->has('to_date')) ? date('Y-m-d',strtotime(request()->get('to_date'))) : date('Y-m-d') }}" name="to_date" id="to_date">
                    </div>
                    {{-- <div class="col-md-12">
                        <input type="submit" class="btn btn-primary float-right" value="Search">
                    </div> --}}
                </div>
            </form>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            <div class="col-md-12 mb-4">
                @can('add-payments')
                    <a href="{{ route('admin.accounts.payments.add') }}" class="btn btn-primary float-right">Add Payment</a>
                 @endcan
            </div>

            {{-- <div class="d-flex align-items-center justify-content-between">
                <h4 class="header-title">All Payments List</h4>
            </div> --}}
            {{-- <p class="sub-header">Following is the list of all the Payments.</p> --}}
            <p class="font-weight-bold text-center" style="font-size:17px">Total : <span id="total"></span></p>
            <table class="table table-bordered w-100 nowrap" id="payment_table">
                <thead>
                    <tr>
                        <th width="20">S.No</th>
                        <th>Date</th>
                        <th>Username</th>
                        <th>Added By</th>
                        <th>Mode</th>
                        <th>Amount</th>
                        <th>Old Balance</th>
                        <th>New Balance</th>
                        @if(auth()->user()->can('delete-payments'))
                            <th>Action</th>
                        @elseif(auth()->user()->can('print-payments'))
                            <th>Action</th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tbody>
                </tbody>
            </table>

            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="float-right"> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-scripts')
@include('admin.partials.datatable', ['load_swtichery' => true])
<script src="https://cdn.datatables.net/plug-ins/1.10.19/api/sum().js"></script>

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
                        url : "{{ route('admin.accounts.payments.index') }}",
                        data:function(d){
                                    d.receiver_id        = $('#receiver_id').val(),
                                    d.admin_id        = $('#admin_id').val(),
                                    d.added_by        = $('#added_by').val(),
                                    d.from_date       = $('#from_date').val(),
                                    d.to_date         = $('#to_date').val(),
                                    d.search          = $('input[type="search"]').val(),
                                    d.type            = $('#type').val()
                        },
                    },
                    drawCallback: function () {
                        var sum = $('#payment_table').DataTable().column(5).data().sum();
                        
                        internationalNumberFormat = new Intl.NumberFormat('en-US')
                        $('#total').html(internationalNumberFormat.format(sum));
                    },	
                    
                    columns : [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable:true,searchable:false},
                        {data:'date', name:'payments.created_at', orderable:true, searchable:true},  
                        {data:'reciever_name', name:'receiver.name',orderable:false,searchable:true},
                        {data:'added_by', name:'admin.name',orderable:false,searchable:true},
                        {data:'type', name:'payments.type',orderable:true,searchable:true},
                        {data:'amount', name:'payments.amount',orderable:true,searchable:true},
                        {data:'old_balance', name:'payments.old_balance',orderable:true,searchable:true},
                        {data:'new_balance', name:'payments.new_balance',orderable:true,searchable:true},
                        @if(auth()->user()->can('delete-payments'))
                            {data:'action', name:'payments.action',orderable:false,searchable:false},
                        @elseif(auth()->user()->can('print-payments'))
                            {data:'action', name:'payments.action',orderable:false,searchable:false},
                        @endcan

                    ],
                });


       $('#receiver_id,#admin_id,#from_date,#to_date,#added_by,#type').change(function(){
            table.draw();
       });
    });

    //select 2
    $('#username').select2({
        placeholder: 'Select Receiver'
    });
</script>
@endsection

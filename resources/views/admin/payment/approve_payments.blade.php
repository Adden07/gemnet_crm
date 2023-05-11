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
    {{-- <div class="col-lg-12">
        <div class="card-box">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="header-title">Filters</h4>
            </div>
            <form action="{{ route('admin.accounts.payments.index') }}" method="GET">
                @csrf
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="">Receiver Username</label>
                        <select class="form-control" name="username" id="username">
                            <option value="">Select Username</option>
                            @foreach($admins as $admin)
                                <option value="{{ $admin->hashid }}" @if(request()->has('username') && request()->get('username') == $admin->hashid) selected @endif>{{ $admin->username }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="">Added By</label>
                        <select class="form-control" name="added_by" id="added_by">
                            <option value="">Select Added By</option>
                            <option value="person" @if(request()->has('added_by') && request()->get('added_by') == 'person') selected @endif>Person</option>
                            <option value="system" @if(request()->has('added_by') && request()->get('added_by') == 'system') selected @endif>System</option>
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
                </div>
            </form>
        </div>
    </div> --}}
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            <table class="table table-bordered w-100 nowrap" id="payment_table">
                <thead>
                    <tr>
                        <th width="20">S.No</th>
                        <th>Date</th>
                        <th>Approved <br />Date</th>
                        <th>Receiver Name</th>
                        <th>Added By</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Old Balance</th>
                        <th>New Balance</th>
                        <th>Status</th>
                        <th>Approve</th>
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
                        {{-- {{ $transactions->links() }} --}}
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
                        url : "{{ route('admin.accounts.payments.approve_payments') }}",
                        data:function(d){
                                    d.username        = $('#username').val(),
                                    d.added_by        = $('#added_by').val(),
                                    d.from_date       = $('#from_date').val(),
                                    d.to_date         = $('#to_date').val(),
                                    d.search          = $('input[type="search"]').val()
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
                        {data:'approved_date', name:'payments.approved_date', orderable:true, searchable:true},  
                        {data:'reciever_name', name:'receiver.name',orderable:false,searchable:true},
                        {data:'added_by', name:'admin.name',orderable:false,searchable:true},
                        {data:'type', name:'payments.type',orderable:true,searchable:true},
                        {data:'amount', name:'payments.amount',orderable:true,searchable:true},
                        {data:'old_balance', name:'payments.old_balance',orderable:true,searchable:true},
                        {data:'new_balance', name:'payments.new_balance',orderable:true,searchable:true},
                        {data:'status', name:'payments.status',orderable:false,searchable:false},
                        {data:'action', name:'payments.action',orderable:false,searchable:false},
                        

                    ],
                });


       $('#username,#from_date,#to_date,#added_by').change(function(){
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

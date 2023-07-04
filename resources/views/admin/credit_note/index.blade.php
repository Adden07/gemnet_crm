@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Finance</li>
                    <li class="breadcrumb-item active">Credit Note</li>
                </ol>
            </div>
            <h4 class="page-title">Credit Note</h4>
        </div>
    </div>
</div>
@can('add-credit-note')
<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="header-title">{{ (isset($_update)) ? 'Update' : 'Add' }} Credit Note</h4>
            </div>
            <form action="{{ route('admin.accounts.credit_notes.store') }}" class="ajaxForm" method="POST" id="form">
                @csrf
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="">Users</label>
                        <select class="form-control select2" name="user_id" id="user_id">
                            <option value="">Select User</option>
                            @foreach($users AS $user)
                                <option value="{{ $user->hashid }}" @if(@$edit_credit_note->user_id == $user->id) selected @endif>{{ $user->name}}--({{ $user->username }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="">User Invoices</label>
                        <select class="form-control" name="invoice_id" id="invoice_id" required>
                            <option value="">Select User</option>
                            @isset($is_update)
                                @foreach($invoices AS $invoice)
                                    <option value="{{ $invoice->hashid }}" @if($invoice->id == $edit_credit_note->invoice_id) selected @endif>{{ $invoice->invoice_id }}</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="">Amount</label>
                        <input type="number" class="form-control" min="1" name="amount" id="amount" value="{{ @$edit_credit_note->amount }}" required>
                    </div>
                    <div class="col-md-12">
                        <input type="hidden" name="credit_note_id" value="{{ @$edit_credit_note->hashid }}">
                        <input type="submit" class="btn btn-primary float-right" value="{{ (isset($is_update)) ? 'Update' : 'Add' }}">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
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
                        <select class="form-control select2" name="username" id="user_id_filter">
                            <option value="all">All</option>
                            @foreach($users as $user)
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
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            {{-- <div class="d-flex align-items-center justify-content-between">
                <h4 class="header-title">All Payments List</h4>
            </div> --}}
            {{-- <p class="sub-header">Following is the list of all the Payments.</p> --}}
            {{-- <p class="font-weight-bold text-center" style="font-size:17px">Total From Current Displayed Entries : <span id="total"></span></p> --}}
            {{-- <p class="font-weight-bold text-center" style="font-size:17px">Total : <span>{{ number_format($total_payments) }}</span></p> --}}

            <table class="table table-bordered w-100 nowrap" id="payment_table">
                <thead>
                    <tr>
                        <th width="20">S.No</th>
                        <th>Date</th>
                        <th>Username</th>
                        <th>Credit Note ID</th>
                        <th>Invoice ID</th>
                        <th>Added By</th>
                        {{-- <th>Mode</th> --}}
                        <th>Amount</th>
                        <th>Old Balance</th>
                        <th>New Balance</th>
                        @if(auth()->user()->can('edit-credit-note') || auth()->user()->can('delete-credit-note'))
                            <th>Action</th>
                        @endif
                        {{-- @if(auth()->user()->can('delete-payments'))
                            <th>Action</th>
                        @elseif(auth()->user()->can('print-payments'))
                            <th>Action</th>
                        @endcan --}}
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
    $('#user_id').change(function(){
        let route = "{{ route('admin.accounts.credit_notes.get_user_invoices', ':id') }}";
        route     = route.replace(':id', $(this).val());
        
        if($(this).val() != ''){
            getAjaxRequests(route, '', 'GET',function(resp){
                $('#invoice_id').html(resp.html);
            });
        }
    });
    $('#form').validate();
    $(document).ready(function(){
        var table = $('#payment_table').DataTable({
                    processing: true, 
                    serverSide: true,
                    "order": [[ 0, "desc" ]],
                    "pageLength": 300,
                    "lengthMenu": [300,500,1000,1500,2500,5000],
                    "dom": '<"top"ifl<"clear">>rt<"bottom"ip<"clear">>',

                    ajax:{
                        url : "{{ route('admin.accounts.credit_notes.index') }}",
                        data:function(d){
                            d.user_id    = $('#user_id_filter').val(),
                            d.admin_id   = $('#admin_id').val(),
                            d.from_date  = $('#from_date').val(),
                            d.to_date    = $('#to_date').val(),
                            d.search     = $('input[type="search"]').val()
                        },
                    },
                    
                    columns : [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable:true,searchable:false},
                        {data:'date', name:'payments.created_at', orderable:true, searchable:true},  
                        {data:'reciever_name', name:'receiver.name',orderable:true,searchable:true},
                        {data:'credit_note_id', name:'credit_note_id',orderable:true,searchable:true},
                        {data:'invoice', name:'invoice.invoice_id',orderable:true,searchable:false},
                        {data:'added_by', name:'admin.name',orderable:true,searchable:true},
                        // // {data:'type', name:'payments.type',orderable:true,searchable:true},
                        {data:'amount', name:'payments.amount',orderable:true,searchable:true},
                        {data:'old_balance', name:'payments.old_balance',orderable:true,searchable:true},
                        {data:'new_balance', name:'payments.new_balance',orderable:true,searchable:true},
                        @if(auth()->user()->can('edit-credit-note') || auth()->user()->can('delete-credit-note'))
                            {data:'action', name:'action',orderable:true,searchable:true},
                        @endif

                    ],
                });


       $('#user_id_filter,#admin_id,#from_date,#to_date').change(function(){
            table.draw();
       });
    });

    //select 2
    $('#username').select2({
        placeholder: 'Select Receiver'
    });
</script>
@endsection

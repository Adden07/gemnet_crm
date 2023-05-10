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
                        <th>Receiver Name</th>
                        <th>Added By</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Old Balance</th>
                        <th>New Balance</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- @php $total = 0; @endphp
                    @foreach($transactions AS $transaction)
                        <tr>
                            <td>{{ $transactions->firstItem() + $loop->index }}</td>
                            <td>
                                @if(date('l',strtotime($transaction->created_at)) == 'Saturday')
                                    <span class="badge" style="background-color: #0071bd
                                    ">{{ date('d-M-Y H:i A',strtotime($transaction->created_at)) }}
                                @elseif(date('l',strtotime($transaction->created_at)) == 'Sunday')
                                    <span class="badge" style="background-color: #f3872f">{{ date('d-M-Y H:i A',strtotime($transaction->created_at)) }}
                                @elseif(date('l',strtotime($transaction->created_at)) == 'Monday') 
                                    <span class="badge" style="background-color: #236e96">{{ date('d-M-Y H:i A',strtotime($transaction->created_at)) }}
                                @elseif(date('l',strtotime($transaction->created_at)) == 'Tuesday')
                                    <span class="badge" style="background-color: #ef5a54">{{ date('d-M-Y H:i A',strtotime($transaction->created_at)) }}
                                @elseif(date('l',strtotime($transaction->created_at)) == 'Wednesday')
                                    <span class="badge" style="background-color: #8b4f85" style="background-color: #000">{{ date('d-M-Y H:i A',strtotime($transaction->created_at)) }}
                                @elseif(date('l',strtotime($transaction->created_at)) == 'Thursday')
                                    <span class="badge" style="background-color: #ca4236
                                    ">{{ date('d-M-Y H:i A',strtotime($transaction->created_at)) }}
                                @elseif(date('l',strtotime($transaction->created_at)) == 'Friday')
                                    <span class="badge" style="background-color: #6867ab">{{ date('d-M-Y H:i A',strtotime($transaction->created_at)) }}
                                @endif
                            </span></td>
                            <td>
                                @if($transaction->receiver->user_type == 'franchise')
                                    <span class="badge" style="background-color:#2875F3">
                                        {{ $transaction->receiver->name }} ({{ $transaction->receiver->username }})
                                    </span>
                                @elseif($transaction->receiver->user_type == 'dealer')
                                    <span class="badge" style="background-color:#3ABC01">
                                        {{ $transaction->receiver->name }} ({{ $transaction->receiver->username }})
                                    </span>
                                @elseif($transaction->receiver->user_type == 'sub_dealer')
                                    <span class="badge" style="background-color:#F19806">
                                        {{ $transaction->receiver->name }} ({{ $transaction->receiver->username }})
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if(@$transaction->admin->id == 10)
                                    <span class="badge badge-danger">{{ $transaction->admin->name }}</span>
                                @else 
                                    {{ @$transaction->admin->name }} (<strong>{{ @$transaction->admin->username }}</strong>)
                                @endif
                            </td>
                            <td>
                                @if($transaction->type == 0)
                                    <span class="badge badge-danger">System</span>
                                @else   
                                    <span class="badge badge-success">Person</span>
                                @endif
                            </td>
                            <td>{{ number_format($transaction->amount) }}</td>
                            <td>{{ number_format($transaction->old_balance) }}</td>
                            <td>{{ number_format($transaction->new_balance) }}</td>
                            @php  $total += $transaction->amount @endphp
                        </tr>
                    @endforeach --}}
                    {{-- <tr>
                        <td colspan="3" class="border-right-0">Total</td>
                        <td colspan="3" class="text-right pr-4">{{ number_format($total,2) }}</td>
                    </tr> --}}
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
                        url : "{{ route('admin.accounts.payments.index') }}",
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
                        {data:'reciever_name', name:'receiver.name',orderable:false,searchable:true},
                        {data:'added_by', name:'admin.name',orderable:false,searchable:true},
                        {data:'type', name:'payments.type',orderable:true,searchable:true},
                        {data:'amount', name:'payments.amount',orderable:true,searchable:true},
                        {data:'old_balance', name:'payments.old_balance',orderable:true,searchable:true},
                        {data:'new_balance', name:'payments.new_balance',orderable:true,searchable:true},
                    ],
                });


       $('#username,#from_date,#to_date,#added_by').change(function(){
            table.draw();
       });
    });
    //get dealers of selected franchise
    $('#franchise_id').change(function(){
        var id = $(this).val();
        var route = "{{ route('admin.sub_dealers.get_dealer',':id') }}"
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

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
    
    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #ccc;
      -webkit-transition: .4s;
      transition: .4s;
    }
    
    .slider:before {
      position: absolute;
      content: "";
      height: 15px;
      width: 15px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      -webkit-transition: .4s;
      transition: .4s;
    }
    
    input:checked + .slider {
      background-color: #1bb99a;
    }
    
    input:focus + .slider {
      box-shadow: 0 0 1px #1bb99a;
    }
    
    input:checked + .slider:before {
      -webkit-transform: translateX(13px);
      -ms-transform: translateX(13px);
      transform: translateX(13px);
    }
    
    /* Rounded sliders */
    .slider.round {
      border-radius: 34px;
    }
    
    .slider.round:before {
      border-radius: 50%;
    }

    label.error {display:none;}
    
</style>


<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Finance</li>
                    <li class="breadcrumb-item active">Unpaid Invoices</li>
                </ol>
            </div>
            <h4 class="page-title">Unpaid Invoices</h4>
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
                    </div>
                    <div class="form-group col-md-3">
                        <label for="">Added By</label>
                        <select class="form-control" name="added_by" id="added_by">
                            <option value="">Select Added By</option>
                            <option value="person" @if(request()->has('added_by') && request()->get('added_by') == 'person') selected @endif>Person</option>
                            <option value="system" @if(request()->has('added_by') && request()->get('added_by') == 'system') selected @endif>System</option>
                        </select>
                    </div> --}}
                    <div class="form-group col-md-6">
                        <label for="">From Date</label>
                        <input type="date" class="form-control" value="{{ (request()->has('from_date')) ? date('Y-m-d',strtotime(request()->get('from_date'))) : date('Y-m-d') }}" name="from_date" id="from_date">
                    </div>
                    <div class="form-group col-md-6">
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
            <table class="table table-bordered w-100 nowrap" id="payment_table">
                <thead>
                    <tr>
                        <th width="20">S.No</th>
                        <th>Date</th>
                        <th>Username</th>
                        <th>Address</th>
                        <th>Paid</th>
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
        var table = $('#payment_table').DataTable({
                    processing: true, 
                    serverSide: true,
                    "order": [[ 0, "desc" ]],
                    "pageLength": 300,
                    "lengthMenu": [300,500,1000,1500],
                    "dom": '<"top"ifl<"clear">>rt<"bottom"ip<"clear">>',

                    ajax:{
                        url : "{{ route('admin.accounts.invoices.unpaid_invoice') }}",
                        data:function(d){
                                    // d.username        = $('#username').val(),
                                    // d.added_by        = $('#added_by').val(),
                                    d.from_date       = $('#from_date').val(),
                                    d.to_date         = $('#to_date').val(),
                                    d.search          = $('input[type="search"]').val()
                        },
                    },
                    
                    columns : [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable:true,searchable:false},
                        {data:'date', name:'created_at', orderable:true, searchable:true},  
                        {data:'username', name:'user.name',orderable:false,searchable:true},
                        {data:'address', name:'user.address',orderable:false,searchable:true},
                        {data:'paid', name:'paid',orderable:false,searchable:false},
                    ],
                    // "initComplete": function(settings, json) {
                    //     alert( 'DataTables has finished its initialisation.' );
                    // }
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

@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Sms</li>
                    <li class="breadcrumb-item active">SMS Logs</li>
                </ol>
            </div>
            <h4 class="page-title">SMS Logs</h4>
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
                        <select class="form-control select2" name="user_id" id="user_id">
                            <option value="all">All</option>

                            @foreach($users as $user)
                                <option value="{{ $user->hashid }}">{{ $user->username }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="">SMS Type</label>
                        <select class="form-control select2" name="sms_type" id="sms_type">
                            <option value="all">All</option>
                            @foreach($sms_types AS $type)
                                <option value="{{ $type }}">{{ $type }}</option>
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
            <table class="table table-bordered w-100 nowrap" id="payment_table">
                <thead>
                    <tr>
                        <th width="20">S.No</th>
                        <th>DateTime</th>
                        <th>Username</th>
                        <th>SMS Type</th>
                        <th>Mobile No</th>
                        <th>SMS</th>
                        <th>Manual Status</th>
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
                        url : "{{ route('admin.sms.log_page') }}",
                        data:function(d){
                                    d.user_id         = $('#user_id').val(),
                                    d.sms_type        = $('#sms_type').val(),
                                    d.from_date       = $('#from_date').val(),
                                    d.to_date         = $('#to_date').val(),
                                    d.search          = $('input[type="search"]').val()
                        },
                    },                    
                    columns : [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable:true,searchable:false},
                        {data:'date', name:'payments.created_at', orderable:true, searchable:true},  
                        {data:'username', name:'username', orderable:true, searchable:true},  
                        {data:'sms_type', name:'sms_type', orderable:true, searchable:false},  
                        {data:'mobile_no', name:'mobile_no', orderable:true, searchable:false},  
                        {data:'sms', name:'sms', orderable:true, searchable:false},
                        {data:'is_manual', name:'is_manual', orderable:true, searchable:false},  
  
                    ],
                });


            $('#user_id,#sms_type,#from_date,#to_date').change(function(){
                    table.draw();
            });
    });

    //select 2
    $('#username').select2({
        placeholder: 'Select Receiver'
    });
</script>
@endsection

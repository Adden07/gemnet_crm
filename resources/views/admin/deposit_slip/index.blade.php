@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Finance</li>
                    <li class="breadcrumb-item active">Deposit Slip</li>
                </ol>
            </div>
            <h4 class="page-title">Deposit Slip</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="header-title">Filters</h4>
            </div>
                <form action="{{ route('admin.accounts.deposit_slips.store') }}" method="POST" class="ajaxForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <label for="">Deposit Date</label>
                            <select class="form-control" name="deposit_date" id="deposit_date">
                                <option value="">Select date</option>
                                @foreach($payment_dates AS $date)
                                    <option value="{{ now()->parse($date->created_at)->format('Y-m-d') }}">{{ now()->parse($date->created_at)->format('d-M-Y') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="">Amount</label>
                            <input type="number" class="form-control" placeholder="0" readonly name="amount" id="amount" required>
                        </div>
                        <div class="form-group col-md-4 image" id="transaction_image_col">
                            <label for="logo">Slip Photo</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="image"  id="image" onchange="showPreview('preview_nic_front')">
                                    <label class="custom-file-label profile_img_label" for="logo">Choose Slip Photo</label>
                                </div>
                                <div class="nic_front_err w-100"></div>
                                <div class="position-relative mt-3">
                                    <img id="preview_nic_front" src="@if(@file_exists($edit_user->nic_front)) {{ asset($edit_user->nic_front) }} @else {{ asset('admin_uploads/no_image.jpg') }}  @endif"  class="@if(!isset($is_update)) d-none  @endif" width="100px" height="100px"/>
                                    @if(@file_exists($edit_user->nic_front))
                                        <a   href="javascript:void(0)" class="btn btn-danger btn-sm rounded position-absolute nopopup" style="top: 0;right:0" data-url="{{ route('admin.users.remove_attachment',['id'=>$edit_user->hashid,'type'=>'nic_front','path'=>$edit_user->nic_front]) }}" onclick="ajaxRequest(this)" id="remove_nic_front">
                                            <i class="fa fa-times"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <input type="submit" class="btn btn-primary float-right">
                        </div>
                    </div>
                </form>
        </div>
    </div>
</div>
<div class="row">
    
    <div class="col-lg-12">
        <div class="card-box">
            <form action="{{ route('admin.accounts.payments.approve_payment') }}" class="ajaxForm" method="POST">
                @csrf
                <input type="hidden" name="ids[]" id="ids">
                <input type="submit" class="btn btn-primary d-none" id="checkbox_submit" name="submit" value="Approve">
            </form>
            <table class="table table-bordered w-100 nowrap dt_table" id="payment_table">
                <thead>
                    <tr>
                        <th width="20">S.No</th>
                        <th>Deposit Date</th>
                        <td>Amount</td>
                        <td>Image</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach($deposit_slips AS $slip)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ now()->parse($slip->deposit_date)->format('d-M-Y') }}</td>
                            <td>{{ number_format($slip->amount) }}</td>
                            <td>
                                <a href="{{ asset($slip->image) }}" target="_blank">
                                    <img src="{{ asset($slip->image) }}" alt="" width="70px" hieght="100px">
                                </a>
                            </td>
                        </tr>
                    @endforeach
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
    // $(document).ready(function(){
    //     var table = $('#payment_table').DataTable({
    //                 processing: true, 
    //                 serverSide: true,
    //                 "order": [[ 0, "desc" ]],
    //                 "pageLength": 300,
    //                 "lengthMenu": [300,500,1000,1500],
    //                 "dom": '<"top"ifl<"clear">>rt<"bottom"ip<"clear">>',

    //                 ajax:{
    //                     url : "{{ route('admin.accounts.payments.approve_payments') }}",
    //                     data:function(d){
    //                         d.status    = $('#payement_status').val(),
    //                         d.from_date = $('#from_date').val(),
    //                         d.to_date   = $('#to_date').val()
    //                     },
    //                 },
    //                 drawCallback: function () {
    //                     var sum = $('#payment_table').DataTable().column(5).data().sum();
                        
    //                     internationalNumberFormat = new Intl.NumberFormat('en-US')
    //                     $('#total').html(internationalNumberFormat.format(sum));
    //                 },	
                    
    //                 columns : [
    //                     {data: 'checkbox', name: 'checkbox',orderable:false,searchable:false},
    //                     {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable:true,searchable:false},
    //                     {data:'date', name:'payments.created_at', orderable:true, searchable:true},  
    //                     {data:'online_date', name:'payments.online_date', orderable:true, searchable:true},  
    //                     {data:'approved_date', name:'payments.approved_date', orderable:true, searchable:true},  
    //                     {data:'reciever_name', name:'receiver.name',orderable:false,searchable:true},
    //                     {data:'added_by', name:'admin.name',orderable:false,searchable:true},
    //                     // {data:'type', name:'payments.type',orderable:true,searchable:true},
    //                     {data:'amount', name:'payments.amount',orderable:true,searchable:true},
    //                     {data:'old_balance', name:'payments.old_balance',orderable:true,searchable:true},
    //                     {data:'new_balance', name:'payments.new_balance',orderable:true,searchable:true},
    //                     {data:'image', name:'image',orderable:false,searchable:false},
    //                     {data:'status', name:'payments.status',orderable:false,searchable:false},
    //                     @can('approve-payments')
    //                         {data:'action', name:'payments.action',orderable:false,searchable:false},
    //                     @endcan

    //                 ],
    //             });


    //    $('#payement_status, #from_date, #to_date').change(function(){
    //         table.draw();
    //    });
    // });
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

    function getCheckbox(){
          // Array to store the values of selected checkboxes
        var selectedCheckboxes = [];
        // Iterate over each checked checkbox
        $('input[name="checkbox[]"]:checked').each(function() {
            // Get the value of the checkbox (e.g., data ID)
            var value = $(this).val();-
            // Push the value to the selectedCheckboxes array
            selectedCheckboxes.push(value);
        });
        
        // Display the selected checkboxes (for demonstration purposes)
        if(selectedCheckboxes.length > 0){
            $('input[name="submit"]').removeClass('d-none');
        }else{
            $('#checkbox_submit').addClass('d-none');
        }  
        $('#ids').val(selectedCheckboxes);
    }
    $('#check_all').click(function(){
       if($(this).prop('checked')){
            $('input[name="checkbox[]"]').prop('checked', true)
       }else{
            $('input[name="checkbox[]"]').prop('checked', false)
       }
       getCheckbox();

    });

    $('#deposit_date').change(function(){
        var route = "{{ route('admin.accounts.deposit_slips.get_payment_date_amount', ':date') }}";
        route     = route.replace(':date', $(this).val());
        
        getAjaxRequests(route, {}, 'GET', function(resp){
            $('#amount').val(resp.amount);
        });
    });
</script>
@endsection

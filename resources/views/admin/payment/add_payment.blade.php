@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Accounts</li>
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
            <form action="{{ route('admin.accounts.payments.store') }}" method="POST" id="form" class="ajaxForm">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group @if(auth()->user()->user_type == 'dealer') d-none @endif">
                            <label for="">Type</label>
                            <select class="form-control" name="type" id="type">
                                <option value="">Select Type</option>
                                @if(auth()->user()->user_type == 'admin')
                                    <option value="franchise">Franchise</option>
                                @endif
                                @if(auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'franchise')
                                    <option value="dealer">Dealer</option>
                                @endif
                        
                                <option value="subdealer" @if(auth()->user()->user_type == 'dealer') selected @endif>Sub Dealer</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 d-none" id="franchise_col">
                        <div class="form-group">
                            <label for="">Franchises</label>
                            <select class="form-control" name="franchise_id" id="franchise_id">
                                <option value="">Select Franchise</option>
                                @if($user_type == 'admin')
                                    @foreach($franchises AS $franchise)
                                        <option value="{{ $franchise->hashid }}" @if(@$edit_transaction->receiver_id == $franchise->id) selected @endif>{{ $franchise->name }} ({{ $franchise->username }})</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 d-none" id="dealer_col">
                        <div class="form-group">
                            <label for="">Dealers</label>
                            <select class="form-control" name="dealer_id" id="dealer_id">
                                <option value="">Select Dealer</option>
                                @if($user_type == 'franchise')
                                    @foreach($dealers AS $dealer)
                                        <option value="{{ $dealer->hashid }}">{{ $dealer->username }} ({{ $dealer->username }})</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6" {{ (auth()->user()->user_type != 'dealer') ? 'd-none' : '' }} id="subdealer_col">
                        <div class="form-group">
                            <label for="">Select Subdealer</label>
                            <select class="form-control" name="subdealer_id" id="subdealer_id">
                                <option value="">Select Subdealer</option>
                                @if($user_type == 'dealer')
                                    @foreach($subdealers AS $sub)
                                        <option value="{{ $sub->hashid }}">{{ $sub->username }} ({{ $sub->username }})</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6" {{ (auth()->user()->user_type != 'dealer') ? 'd-none' : '' }} id="payment_col">
                        <div class="form-group">
                            <label for="">Payment Amount</label>
                            <input type="number" class="form-control" placeholder="Payment Amount" value="{{ @$edit_transaction->amount }}" name="amount" id="amount">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="">Available Balance</label>
                            <input type="number" class="form-control" placeholder="0" value="" name="amount" id="available_balance" disabled>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <input type="hidden" value="{{ $user_type }}" name="user_type" id="user_type">
                        <input type="hidden" value="{{ @$edit_transaction->hashid }}" name="transaction_id" id="transaction_id">
                        @if(auth()->user()->user_type == 'franchise')
                            <input type="hidden" name="franchise_id" value="{{ hashids_encode(auth()->user()->id) }}">
                        @endif
                        <input type="submit" class="btn btn-primary float-right" id="submit" value="{{ (isset($is_update)) ? 'Update' : 'Add' }}" required @if(auth()->user()->user_type != 'dealer') disabled @endif>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade bd-example-modal-lg" id="details_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
</div>
@endsection

@section('page-scripts')
@include('admin.partials.datatable', ['load_swtichery' => true])
<script>
    // $(document).ready(function(){
    //     var user_type = "{{ auth()->user()->user_type }}";
    //     var min_amount = 1;
    //     if(user_type == 'admin'){
    //         min_amount = 10000;
    //     }
    // });
    var reciever_name = '';
    //get dealers of selected franchise
    $('#franchise_id').change(function(){
        reciever_name = $(this).find(':selected').text();
        var id = $(this).val();
        var route = "{{ route('admin.sub_dealers.get_dealer',':id') }}"
        route = route.replace(':id',id);
        //send ajax request when value is set
        if(id.length != 0){
            getAjaxRequests(route,'','GET',function(resp){
                $('#dealer_id').html("<option value='' selected>Select Dealer</option>"+resp.html);
                // $('#dealer_id').html(resp.html);
                // $('#franchise_city_id').val(resp.city_id);                    
            });
        }
    });

    //get subdealers of selected dealer
    $('#dealer_id').change(function(){
        reciever_name = $(this).find(':selected').text();
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

    //when there is change in subdealer
    $('#subdealer_id').change(function(){
        reciever_name = $(this).find(':selected').text();
    });

    //disaply fields when there is type change display fields according to types
    $('#type').change(function(){
        var type = $(this).val();
        var user_type = $('#user_type').val();
        // alert(user_type);
        if(type.length != 0){
            /*
                when user is admin then display all fields 
                if user is franchise dispaly dealer and subadealer 
                and if user is dealer then display only subdealer 
            */
            if(type == 'franchise'){
                
                toggleCol('franchise_col','show');
                toggleCol('dealer_col','remove');
                toggleCol('subdealer_col','remove');
            
            }else if(type == 'dealer'){
                
                if(user_type == 'admin'){
                    toggleCol('franchise_col','show');

                }
                
                toggleCol('dealer_col','show');
                toggleCol('subdealer_col','remove');
            
            }else if(type == 'subdealer'){
                
                if(user_type == 'admin'){
                    toggleCol('franchise_col','show');
                }
                if(user_type == 'admin' || user_type == 'franchise'){
                    toggleCol('dealer_col','show');

                }
                toggleCol('subdealer_col','show');
            }

            $('#payment_col').removeClass('d-none');
            $('#submit').removeAttr('disabled');
            // $('#form').valid();
        }else{
            toggleCol('franchise_col','remove');
            toggleCol('dealer_col','remove');
            toggleCol('subdealer_col','remove');
            $('#payment_col').addClass('d-none');
            $('#submit').attr('disabled',true);
        }
    });

    //add and  remove d-none class
    function toggleCol(id,status){
        if(status == 'show'){
            $('#'+id).removeClass('d-none');
        }else{
            $('#'+id).addClass('d-none');
        }
    }
    
    //form validation
    $('#form').validate({
        rules:{
            type:{
                required:true
            },
            franchise_id:{
                required:true
            },
            dealer_id:{
                required:true
            },
            subdealer_id:{
                required:true
            },
            amount:{
                required:true,
                digits:true, 
                // minlength:6,
                minlength:function(element){//if user is admin then minimum required amount is 10000
                    var user_type = "{{ auth()->user()->user_type }}";
                    var min_amount = 1;

                    if(user_type == 'admin'){
                        min_amount = 5;
                    }
                    return parseInt(min_amount);
                },
                maxlength:7 
            },
            payment_method:{
                required:true
            }
        },
        messages:{
            amount:{
                minlength:"Minimum required amount is 10,000"
            }            
        }
    });

    //get user available balance
    $('#franchise_id, #dealer_id, #subdealer_id').change(function(){
        var id = $(this).val();
        var route = "{{ route('admin.accounts.payments.balance',':id') }}";
        var route = route.replace(':id',id);
        
        getAjaxRequests(route, '', 'GET', function(resp){
                $('#available_balance').val(resp.balance);
            });
    });

    //change action when update package  
    $('#submit').click(function(e){
        e.preventDefault();
        $('#form').valid(); //validate form

        if($('#form').valid()){ //check if form is valid
            
            number_format = new Intl.NumberFormat('en-US')

            var current_package = "{{ @$user_details->current_package->name }}";
            var new_package     = $('#current_package_ddl').find(':selected').text();
            var nopopup         = false;
            var btn_txt         = 'yes, confirm it!';
            var data_msg        = '';
            var amount          = $('#amount').val();
            var available_bal   = $('#available_balance').val();
            var total           = parseInt(amount) + parseInt(available_bal);
            var msg             = "Available balance is "+number_format.format(available_bal)+
                                "\n Adding amount is "+number_format.format(amount)+
                                "\n New balance amount is "+number_format.format(total)+
                                "\n Are you sure you want to add "+amount+" to "+reciever_name;

            if (!nopopup) {
                Swal.fire({
                    title: "Want to add payment?",
                    text:  msg,
                    type: "warning",
                    showCancelButton: !0,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: (btn_txt && btn_txt != '') ? btn_txt : "Yes, confirm it!"
                }).then(function (t) {
                    if (t.value){
                        $('#form').submit();
                    }
                });
            }
        }
    });
</script>
@endsection

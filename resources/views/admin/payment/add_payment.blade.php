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
                            <label for="">Payment Type</label>
                            <select class="form-control" name="type" id="type">
                                <option value="">Select Type</option>
                                <option value="cash">Cash</option>
                                <option value="online">Online</option>
                                <option value="cheque">Cheque</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6" id="payment_col">
                        <div class="form-group">
                            <label for="">Payment Amount</label>
                            <input type="number" class="form-control" placeholder="Payment Amount" value="{{ @$edit_transaction->amount }}" name="amount" id="amount">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="">Users</label>
                        <select class="form-control select2" name="receiver_id" id="receiver_id">
                            @if(request()->has('user_id'))
                                @php $user = $users->where('id', hashids_decode(request()->get('user_id')))->first() @endphp
                                <option value="{{ $user->hashid }}" selected readonly>{{ $user->name }}</option>
                            @else 
                                <option></option>
                                @foreach($users AS $user)
                                    <option value="{{ $user->hashid }}">{{ $user->name }}--( {{ $user->username }} )</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-6 d-none" id="franchise_col">
                        <div class="form-group">
                            <label for="">Franchises</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="">Available Balance</label>
                            <input type="number" class="form-control" placeholder="0" value="" name="amount" id="available_balance" disabled style="background-color: #EBF4E6">
                        </div>
                    </div>
                    {{-- <div class="col-md-6 d-none online" id="transaction_id_col">
                        <div class="form-group">
                            <label for="">Transaction ID</label>
                            <input type="number" class="form-control" placeholder="0" value="" name="transaction_id" id="transaction_id">
                        </div>
                    </div> --}}
                    <div class="col-md-6 d-none online" id="online_transaction_col">
                        <div class="form-group">
                            <label for="">Online Transaciton ID</label>
                            <input type="number" class="form-control" placeholder="0" value="" name="online_transaction" id="online_transaction">
                        </div>
                    </div>
                    <div class="col-md-6 d-none online" id="online_transaction_col">
                        <div class="form-group">
                            <label for="">Online Date</label>
                            <input type="date" class="form-control" placeholder="0" value="" name="online_date" id="online_date">
                        </div>
                    </div>
                    <div class="col-md-6 d-none cheque" id="">
                        <div class="form-group">
                            <label for="">Cheque No</label>
                            <input type="number" class="form-control" placeholder="0" value="" name="cheque_no" id="cheque_no">
                        </div>
                    </div>
                    <div class="col-md-6 d-none cheque" id="">
                        <div class="form-group">
                            <label for="">Cheque Date</label>
                            <input type="date" class="form-control" placeholder="0" value="" name="cheque_date" id="cheque_date">
                        </div>
                    </div>
                    <div class="form-group col-md-6 image d-none" id="transaction_image_col">
                        <label for="logo">Transaction/Recipt photo</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="transaction_image"  id="transaction_image" onchange="showPreview('preview_nic_front')">
                                <label class="custom-file-label profile_img_label" for="logo">Choose Transaction/Receipt photo</label>
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
                    <div class="col-md-6" id="auto_renew_col">
                        <label for="">Auto Renew If Expired</label>
                        <select class="form-control" name="auto_renew" id="auto_renew">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        @if(request()->has('user_id'))
                            <input type="hidden" name="redirect" value="{{ route('admin.users.profile',['id'=>request()->get('user_id')]) }}">
                        @endif
                        <input type="hidden" value="{{ $user_type }}" name="user_type" id="user_type">
                        {{-- <input type="hidden" value="{{ @$edit_transaction->hashid }}" name="transaction_id" id="transaction_id"> --}}
                        <input type="submit" class="btn btn-primary float-right" id="submit" value="{{ (isset($is_update)) ? 'Update' : 'Add' }}" required>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="col-lg-12" id="user_profile_col"></div>
</div>
<div class="modal fade bd-example-modal-lg" id="details_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
</div>
@endsection

@section('page-scripts')
@include('admin.partials.datatable', ['load_swtichery' => true])
<script>

    var reciever_name = '';
    //get dealers of selected franchise

    //disaply fields when there is type change display fields according to types
    $('#type').change(function(){
        var type = $(this).val();
        var user_type = $('#user_type').val();
        if(type.length != 0){
            if(type == 'online'){
                $('.online').removeClass('d-none');
                $('.image').removeClass('d-none');
                $('.cheque').addClass('d-none');
            }else if(type == 'cash'){
                $('.online').addClass('d-none');
                $('.cheque').addClass('d-none');
                $('.image').addClass('d-none');
            }else if(type == 'cheque'){
                $('.image').removeClass('d-none');
                $('.online').addClass('d-none');
                $('.cheque').removeClass('d-none');

            }
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
            amount:{
                required:true,
                digits:true, 
                minlength:1,
                maxlength:7 
            },
            payment_method:{
                required:true
            },
            transaction_image:{
                accept: "jpg,jpeg,png",
                maxsize: 2000000
            }
        },
        messages:{
            amount:{
                minlength:"Minimum required amount is 10,000"
            }            
        }
    });

    //change action when update package  
    $('#submit').click(function(e){
        e.preventDefault();
        $('#form').valid(); //validate form

        if($('#form').valid()){ //check if form is valid
            
            number_format = new Intl.NumberFormat('en-US')

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
    $('#receiver_id').change(function(){//when there is change is user id get the user current balance
        getUserProfile();
        getUserPackageAndBalanceDetails();
    });

    $('#amount').on('keyup', function() {
        setTimeout(() => {
            getUserPackageAndBalanceDetails();
        }, 1000);
    });

    function getUserPackageAndBalanceDetails(){

        var user_id = $('#receiver_id').find(':selected').val();
        var route   = "{{ route('admin.users.get_user_current_balance', ':id') }}";
        var amount  = ($('#amount').val() != '') ? $('#amount').val() : 0;

        route       = route.replace(':id', user_id);
        
        if(user_id != ''){
            getAjaxRequests(route, {amount:amount}, 'GET', function(resp){//run ajax 
            if(resp.status == 'expired' && resp.renew_status == 1){
                $('#auto_renew_col').removeClass('d-none');
                $('#auto_renew').removeClass('is-invalid');
                $('#auto_renew').addClass('is-valid');
            }else if(resp.status == 'active'){
                $('#auto_renew_col').removeClass('d-none');
                $('#auto_renew').removeClass('is-valid');
                $('#auto_renew').removeClass('is-invalid');
            }else if(resp.status == 'registered'){
                $('#auto_renew_col').addClass('d-none');
            }else{
                $('#auto_renew_col').removeClass('d-none');
                $('#auto_renew').removeClass('is-valid');
                $('#auto_renew').addClass('is-invalid');
            }
            $('#available_balance').val(resp.user);//put the value in input
            
            });
        }
    }

    function getUserProfile(){
        var user_id = $('#receiver_id').find(':selected').val();
        var route   = "{{ route('admin.users.get_user_profile', ':id') }}";
        route       = route.replace(':id', user_id);

        getAjaxRequests(route,'', 'GET', function(resp){
            $('#user_profile_col').html(resp.html);
        });
    }

    $(document).ready(function(){
        @if(request()->has('user_id'))
            getUserProfile();
            getUserPackageAndBalanceDetails();
        @endif
    });

    $('.select2').select2({
        placeholder: "Select user",
    });
</script>
@endsection

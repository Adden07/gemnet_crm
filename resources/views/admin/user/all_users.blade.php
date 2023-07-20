@extends('layouts.admin')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Users</li>
                    <li class="breadcrumb-item active">All </li>

                </ol>
            </div>
            <h4 class="page-title">All Users | Active Users ({{ $users_count->where('status','active')->count() }}) | Expired Users ({{ $users_count->where('status','expired')->count() }}) | Terminated ({{ $users_count->where('status','terminated')->count() }}) | Total Users ({{ $users_count->count() }})</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            <div class="row">
                <div class="form-group col-md-3">
                    <label for="">Status</label>
                    <select class="form-control" name="user_status" id="user_status">
                        <option value="">All</option>
                        <optgroup label="Active">
                            <option value="active">Active</option>
                            <option value="active_never_online">Active Never Online</option>
                            <option value="active_online">Active Online</option>
                            <option value="active_offline">Active Offline</option>
                        </optgroup>
                        <optgroup label="Expired">
                            <option value="expired">Expired</option>
                            <option value="expired_never_online">Expired Never Online</option>
                            <option value="expired_online">Expired Online</option>
                            <option value="expired_offline">Expired Offline</option>
                        </optgroup>
                        {{-- <optgroup label="Registered">
                            <option value="registered">Registered</option>
                        </optgroup>
                        <optgroup label="Disabled">
                            <option value="disabled">Disabled</option>
                        </optgroup> --}}
                        <optgroup label="others">
                            <option value="registered">Registered</option>
                            <option value="disabled">Disabled</option>
                            <option value="terminated">Terminated</option>
                        </optgroup>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="">From Date</label>
                    <input type="date" class="form-control" name="from_date" id="from_date">
                </div>
                <div class="form-group col-md-3">
                    <label for="">To Date</label>
                    <input type="date" class="form-control" name="to_date" id="to_date">
                </div>
                <div class="form-group col-md-3">
                    <label for="">Packages</label>
                    <select class="form-control" name="package_id" id="package_id">
                        <option value="">Select Package</option>
                        @foreach($packages AS $package)
                            @if($user_type != 'admin')
                                <option value="{{ $package->hashid }}">{{ $package->name }}</option>
                            @elseif($user_type == 'admin' && $package->users_count > 0)
                                <option value="{{ $package->hashid }}">{{ $package->name }} ({{ $package->users_count }})</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-3">
                    <label for="">Expiration</label>
                    <select class="form-control" name="expiration_date" id="expiration_date">
                        <option value="all">All</option>
                        <option value="{{ date('Y-m-d') }}">Today</option>
                        <option value="{{ date('Y-m-d',strtotime('+1 days')) }}">Tomorrow</option>
                        <option value="{{ date('Y-m-d',strtotime('+3 days')) }}">Next 3 Days</option>
                        <option value="{{ date('Y-m-d',strtotime('+1 week')) }}">Next 7 Days</option>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="">Paid/Unpaid</label>
                    <select class="form-control" name="paid" id="paid">
                        <option value="all">All</option>
                        <option value="1">Paid</option>
                        <option value="0">Unpaid</option>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="">Balance</label>
                    <select class="form-control" name="balance" id="balance">
                        <option value="all">All</option>
                        <option value="1">Advance</option>
                        <option value="0">Balance</option>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="">User Type</label>
                    <select class="form-control" name="user_type" id="user_type">
                        <option value="all">All</option>
                        <option value="individual">Individual</option>
                        <option value="company">Company</option>
                    </select>
                </div>
            </div>  
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            <div class="d-flex align-items-center justify-content-between">
                {{-- <h4 class="header-title">All Users List</h4> --}}
                <div class="col-12 mb-4">
                    @can('add-user')
                        <a href="{{ route('admin.users.add') }}" class="btn btn-primary float-right">Add User</a>
                    @endcan
                </div>
            </div>
            {{-- <p class="sub-header">Following is the list of all the Users.</p> --}}
            <table class="table table-bordered " id="users_table">
                <thead>
                    <tr>
                        <th width="20">S.No</th>
                        <th>Customer ID</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Mobile</th>
                        {{-- <th>Sales Person</th> --}}
                        <th>Package</th>
                        <th>Status</th>
                        <th>Expiration</th>
                        <th>Balance</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade bd-example-modal-lg" id="details_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
</div>
<!-- Package Activation And Renew Modal -->
<div class="modal fade" id="package" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="package_modal_title"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        <form action="{{ route('admin.packages.update_user_package') }}" class="ajaxForm" id="add_package_form" method="GET">
            @csrf
            <div class="modal-body">
   
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <input type="submit" class="btn btn-primary" value="Update" id="package_modal_submit">
            </div>
        </form>
        </div>
    </div>
</div>
@endsection

@section('page-scripts')
@include('admin.partials.datatable', ['load_swtichery' => true])
<script>
    $(document).ready(function(){
        var table = $('#users_table').DataTable({
                    processing: true, 
                    serverSide: true,
                    "order": [[ 0, "desc" ]],
                    "pageLength": 300,
                    "lengthMenu": [300,500,1000,1500],
                    "dom": '<"top"ifl<"clear">>rt<"bottom"ip<"clear">>',

                    ajax:{
                        url : "{{ route('admin.users.index') }}",
                        data:function(d){
                                    d.status        = $('#user_status').val(),
                                    d.from_date     = $('#from_date').val(),
                                    d.to_date       = $('#to_date').val(),
                                    d.package_id    = $('#package_id').val();
                                    d.expiration_date = $('#expiration_date').val(),
                                    d.search        = $('input[type="search"]').val(),
                                    d.paid          = $('#paid').val(),
                                    d.balance       = $('#balance').val(),
                                    d.user_type     = $('#user_type').val()
                        },
                    },
                    
                    columns : [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable:true,searchable:false},
                        {data:'customer_id', name:'customer_id', orderable:true, searchable:false},  
                        {data:'name', name:'users.name', orderable:true},  
                        {data:'username', name:'users.username'},
                        {data:'mobile', name:'users.mobile'},
                        // {data:'sales_person', name:'admin.username', orderable:false},
                        {data:'package', name:'primary_package.name'},
                        {data:'status',name:'users.status'},
                        {data:'expiration', name:'users.current_expiration_date'},
                        {data:'user_current_balance', name:'users.user_current_balance'},
                        {data:'action',name:'action',orderable:false,searchable:false}
                        
                    ],

                       // Other DataTables options...
                    "createdRow": function(row, data, dataIndex) {
                        // Apply row color based on condition
                        if (data.user_type === 'company'){
                            $(row).css({
                                'background-color': '#DAE1D7',
                            });
                        }
                    }
                });


       $('#user_status,#from_date,#to_date,#package_id,#franchise_id,#dealer_id,#subdealer_id,#expiration_date,#paid,#balance,#user_type').change(function(){
            if($(this).attr('id') == 'expiration_date'){
                $('#from_date').val('');
                $('#to_date').val('');
            }
            table.draw();
            // getPackageCount();
       });
    });
    //get package count
    function getPackageCount(){
        var arr = {
            status          : $('#user_status').val(),
            from_date       : $('#from_date').val(),
            to_date         : $('#to_date').val(),
            package_id      : $('#package_id').val(),
            expiration_date : $('#expiration_date').val()
        }
        var url = "{{ route('admin.users.get_pacakge_count') }}";
        getAjaxRequests(url, arr, 'GET', function(resp){
            $('#package_id').html(resp.packages);
        });
    }
    //display password in details modal
    $(document).on('click','.pas',function(){
        $(this).children('i').toggleClass('fa-eye');
        $(this).children('i').toggleClass('fa-eye-slash');
        var attr_type = $(this).parent().parent().find('input').attr('type');
        if(attr_type == 'password'){
            $(this).parent().parent().find('input').attr('type','text');
        }else{
            $(this).parent().parent().find('input').attr('type','password');
        }
    });

    function addPackage(user_id,status,e){  
        //alert(user_id);
        e.preventDefault();
        // return false;
        // var route = $(this).attr('href');
        // var title = $(this).attr('title');
        // var status = $(this).data('status');
        //set package modal submit value
        var route = "{{ route('admin.packages.add_user_package',['id'=>':user_id']) }}";
        var route = route.replace(':user_id',user_id);

        if(status == 'registered'){
            status = 'Activate';
        }else{
            status = 'Renew'
        }
        getAjaxRequests(route, '', 'GET', function(resp){
            $('.modal-body').html(resp.html);
            // var parsedHtml  = $(resp);
            // var idValue = parsedHtml.find('#renew_button_status').val();
            // console.log($(resp.html).find('#renew_button_status').attr('id'));
            // console.log($(resp.html).find('#renew_button_status').attr('id'));

            $('#package').modal('show');
            $('#package_modal_title').html(status);
            $('#package_modal_submit').val(status);//change modal submit value
        });
    }

    //validation on package activation modal
    $('#add_package_form').validate({
    rules : {
        package_id:{
            required:true,
            maxlength:191,
        }
    },
    highlight:function(element){
        if($(element).attr('name') == 'package_id'){
            $(element).addClass('is-invalid');
        }
    },
    unhighlight:function(element){
        if($(element).attr('name') == 'package_id'){
            $(element).removeClass('is-invalid');
            $(element).addClass('is-valid');
        }
    }
    });

    //remoev disabled attribute when form submit add_package_form
    $('#add_package_form').bind('submit',function(){
        $('#package_id').removeAttr('disabled');
        $('#userstatus').removeAttr('disabled');
        $('#username').removeAttr('disabled');
    });

    //when there in modal month_type
    // $(document).on('change','#month_type',function(){
    //     var min_date = '';
    //     var max_date = '';
    //     var date = new Date();//js date
    //     // var current_date = "{{ date('Y-m-d') }}";//php date
    //     if($(this).val() == 'monthly'){
    //         min_date = '';
    //         max_date = '';
    //     }else if($(this).val() == 'half_month'){
    //         if(date.getDate() > 15){
    //             min_date = "{{ date('Y-m-01',strtotime('+1 month')) }}";
    //             max_date = "{{ date('Y-m-15',strtotime('+1 month')) }}";
    //         }else{
    //             min_date = "{{ date('Y-m-d',strtotime('+1 day')) }}";
    //             max_date = "{{ date('Y-m-15') }}";
    //         }

    //     }else if($(this).val() == 'full_month'){
    //         if(date.getDate() > 29){
    //             min_date = "{{ date('Y-m-1',strtotime('+1 month')) }}";
    //             max_date = "{{ date('Y-m-t',strtotime('+1 month')) }}";
    //         }else{
    //             min_date = "{{ date('Y-m-d',strtotime('+1 day')) }}";
    //             max_date = "{{ date('Y-m-t') }}";
    //         }
    //     }

    //     $('#calendar').attr('min',min_date);
    //     $('#calendar').attr('max',max_date);
    //     //if billing cycle is monthy then hide the calendar otherwise show the calendar
    //     if($(this).val() == 'monthly'){
    //         $('#calendar_form').addClass('d-none');
    //     }else{
    //         $('#calendar_form').removeClass('d-none');
    //     }

    //     $('#new_expiration').html($('#hidden_new_expiry_date').val());

    // });
    //when there is change in calendar date
    // $(document).on('change','#calendar',function(){
    //     //make date using js
    //     var expiration_date = new Date($('#calendar').val());
    //     var date            = expiration_date.getDate();
    //     var month           = expiration_date.toLocaleString('en-us', { month: 'short' });
    //     var year            = expiration_date.getFullYear();
        
    //     var new_expiration_date = date+'-'+month+'-'+year+' 12:00';//concate date

    //     $('#new_expiration').html(new_expiration_date);//update the new_expiration date
        
    // });

    $(document).on('change', '#month_type', function(){//get the packages according to the user type
        var type    = $(this).val();
        var route   = "{{ route('admin.packages.get_packages', ':user_type') }}";
        var user_id = $('#user_id').val();
        
        route = route.replace(':user_type', type);
  
        getAjaxRequests(route, '', 'GET', function(resp){
            $('.package_id').html(resp.html);
        });
    });

    $(document).on('change', '.package_id', function(){//get the new expiration date

        var package_id  = $(this).val();
        var user_id     = $('#user_id').val();
        var route       = "{{ route('admin.packages.create_expiration_date', [':package_id', ':user_id']) }}";
        route           = route.replace(':package_id', package_id);
        route           = route.replace(':user_id', user_id);
        var total_amount=0;
        var month_type  = $('#month_type').find(':selected').val();
        
        getAjaxRequests(route, '', 'GET', function(resp){
            $('#new_expiration').html(resp.new_expiration_date);
            $('#package_price').html(resp.package_price.toLocaleString('en-US'));
            $('#renew_package_name').html(resp.renew_package_name);
            
            total_amount += parseInt(resp.package_price);

            if(resp.user_status == 'registered' || month_type == 'full_year'){
                $('#package_price_tab').removeClass('d-none');
                
                $('#otc_price').html(resp.otc.toLocaleString('en-US'));
                $('#otc_tab').removeClass('d-none');
                
                if(resp.otc != false){
                    total_amount += parseInt(resp.otc);
                }
            }else{
                $('#otc_tab').addClass('d-none');

            }
            $('#total_amount').html(total_amount.toLocaleString('en-US'));
            $('#total_amount_tab').removeClass('d-none');
        });
    });

    $(document).on('change', '#renew_type', function(){
        if($(this).val() == 'queue'){
            $('#queue_package').removeClass('d-none');
        }else{
            $('#queue_package').addClass('d-none')
        }
        
    });

    $(document).on('change', '#otc', function(){
        var otc           = parseInt($('#otc_price').text().replace(',', ''));
        var package_price = parseInt($('#package_price').text().replace(',', ''));
        
        if(typeof otc != 'number' && typeof package_price != 'number'){
            otc = 0;
            package_price=0;
        }
        if($(this).val() == 1){
           $('#otc_tab').removeClass('d-none'); 
           $('#total_amount').html((otc+package_price).toLocaleString('en-US'));
        }else{
            $('#total_amount').html((package_price).toLocaleString('en-US'));
            $('#otc_tab').addClass('d-none');
        }
    });

    $(document).on('change', '#month_type', function(){
        let user_status = $('#hidden_user_status').val();
        let month_type  = $(this).val();
        
        if(user_status != 'registered' && month_type == 'full_year'){
            $('#otc_div').removeClass('d-none');
        }else if(user_status != 'registered'){
            $('#otc_div').addClass('d-none');
        }
    });
</script>
@endsection
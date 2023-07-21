@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">User</a></li>
                    <li class="breadcrumb-item active">{{ isset($is_update) ? 'Edit' : 'Add'}} </li>
                </ol>
            </div>
            <h4 class="page-title">{{ isset($is_update) ? 'Edit' : 'Add'}} User</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            <h4 class="header-title m-t-0">{{ isset($is_update) ? 'Edit' : 'Add'}} User</h4>
            <p class="text-muted font-14 m-b-20">
                Here you can {{ isset($is_update) ? 'Edit' : 'Add'}} User.
            </p>

            <form action="{{ route('admin.users.store') }}" class="ajaxForm" method="post" enctype="multipart/form-data" novalidate id="form">
                @csrf
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="name">User type<span class="text-danger">*</span></label>
                        <select class="form-control" name="user_type" id="user_type">
                            <option value="individual" @if(@$edit_user->user_type == 'individual') selected @endif>Individual</option>
                            <option value="company" @if(@$edit_user->user_type == 'company') selected @endif>Company</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="name">Email</label>
                        <input type="text" name="email"   placeholder="Enter email" value="{{ @$edit_user->email }}" class="form-control" id="name">
                    </div>
                    <div class="form-group col-md-6 compy @if(@$edit_user->user_type != 'company') d-none @endif">
                        <label for="name">Business Name<span class="text-danger">*</span></label>
                        <input type="text" name="business_name"   placeholder="Enter business name" value="{{ @$edit_user->business_name }}" class="form-control" id="name">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="name">NTN<span class="text-danger">*</span></label>
                        <input type="string" name="ntn"   placeholder="Enter NTN" value="{{ @$edit_user->ntn }}" class="form-control" id="ntn">
                    </div>
                    <div class="form-group col-md-6 compy @if(@$edit_user->user_type != 'company') d-none @endif">
                        <label for="name">POC Name<span class="text-danger">*</span></label>
                        <input type="text" name="comp_name"   placeholder="Enter name" value="{{ @$edit_user->name }}" class="form-control" id="name">
                    </div>
                    <div class="form-group  col-md-6 compy @if(@$edit_user->user_type != 'company') d-none @endif">
                        <label for="city_id">POC Mobile No<span class="text-danger">*</span></label>
                        <div class="input-group ">
                            <div class="input-group-prepend">
                              <span class="input-group-text" id="basic-addon1">92</span>
                            </div>
                            <input type="text" name="comp_mobile"  placeholder="Enter POC mobile no" value="{{ @substr($edit_user->mobile,2) }}" class="form-control" id="poc_mobile">
                            <div class="mobile_err w-100"></div>
                        </div>
                    </div>
                    <div class="form-group col-md-6 compy @if(@$edit_user->user_type != 'company') d-none @endif">
                        <label for="name">Landline No<span class="text-danger">*</span></label>
                        <input type="text" name="landline_no"   placeholder="Enter landline no" value="{{ @$edit_user->landline_no }}" class="form-control" id="landline_no">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6  ind @if(@$edit_user->user_type == 'company') d-none @endif">
                        <label for="name">Name<span class="text-danger">*</span></label>
                        <input type="text" name="name"   placeholder="Enter name" value="{{ @$edit_user->name }}" class="form-control" id="name">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="username">Username<span class="text-danger">*</span></label>
                        {{-- @if(auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'superadmin')
                            <input type="text" name="username"  minlength="1" maxlength="15"placeholder="Enter username" value="{{ @$edit_user->username }}" class="form-control" id="username" @if(isset($is_update)) readonly style="background-color:#e9ecef" @endif>
                            <div class="username_err w-100"></div>
                        @else 
                            <div class="input-group">
                                <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">{{ auth()->user()->username }} -</span>
                                </div>
                                <input type="text" name="username"  minlength="1" maxlength="{{ (15 - (strlen(auth()->user()->username)+1)) }}"placeholder="Enter username" value="{{ @substr($edit_user->username,strlen(auth()->user()->username)+1) }}" class="form-control" id="username" @if(isset($is_update)) readonly style="background-color:#e9ecef" @endif>
                                <div class="username_err w-100"></div>
                            </div>
                        @endif --}}
                        <input type="text" name="username"  minlength="1" maxlength="15"placeholder="Enter username" value="{{ @$edit_user->username }}" class="form-control" id="username" @if(isset($is_update)) readonly style="background-color:#e9ecef" @endif>
                        <div class="username_err w-100"></div>
                    </div>
                </div>    
                @if(!isset($is_update))
                    <div class="row">
                        <div class="form-group  col-md-6">
                            <label for="password">Connection Password<span class="text-danger">*</span></label>
                            <div class="input-group mpass">
                                <input type="password" name="password" placeholder="Enter password" minlength="6" maxlength="12"  value="" class="form-control" id="password" ><span class="input-group-text pass-show"><a href="javascript:void(0)"><i class="fa fa-eye"></i></a></span><span class="input-group-text pass-hide d-none"><a href="javascript:void(0)"><i class="fa fa-eye-slash"></i></a></span>
                            </div>
                            
                        </div>

                        <div class="form-group col-md-6">
                            <label for="password_confirmation ">Confirm Password<span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" placeholder="Confirm password" minlength="6" maxlength="12" value="" class="form-control" id="password_confirmation" >
                        </div>
                    </div>    
                @endif
                <div class="row">
                    <div class="form-group  col-md-6 ind @if(@$edit_user->user_type == 'company') d-none @endif">
                        <label for="nic ">NIC<span class="text-danger">*</span></label>
                        <input type="text" name="nic"   placeholder="Enter NIC" value="{{ @$edit_user->nic }}" class="form-control" id="nic">
                    </div>

                    <div class="form-group  col-md-6 ind @if(@$edit_user->user_type == 'company') d-none @endif">
                        <label for="city_id">Mobile No<span class="text-danger">*</span></label>
                        <div class="input-group ">
                            <div class="input-group-prepend">
                              <span class="input-group-text" id="basic-addon1">92</span>
                            </div>
                            <input type="text" name="mobile"  placeholder="Enter mobile no" value="{{ @substr($edit_user->mobile,2) }}" class="form-control" id="mobile">
                            <div class="mobile_err w-100"></div>
                        </div>
                    </div>
                </div>    

                <div class="row">
                    <div class="form-group  col-md-6">
                        <label for="city_id">City</label>
                        <select class="form-control" name="city_id" id="city_id" @if(auth()->user()->user_type != 'admin' && auth()->user()->user_type != 'superadmin') disabled @endif>
                            <option value="">Select City</option>
                            @foreach($cities AS $city)
                                <option value="{{ $city->hashid }}" @if(@$edit_user->city_id == $city->id || auth()->user()->city_id == $city->id) selected @endif>{{ $city->city_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="area_id">Area</label>
                        <select class="form-control"name="area_id" id="area_id">
                            <option value="">Select Area</option>
                            {{-- @foreach($areas AS $area)
                                <option value="{{ $area->hashid }}" @if(@$edit_user->area_id == $area->id) selected @endif>{{ $area->area_name }}</option>
                            @endforeach --}}
                        </select>
                        <div class="area_err"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="subarea_id">Subarea</label>
                        <select class="form-control" name="subarea_id" id="subarea_id">
                            <option value="">Select Subarea</option>
                            @if(isset($is_update))
                                @foreach($subareas AS $subarea)
                                    <option value="{{ $subarea->hashid }}" @if($edit_user->subarea_id == $subarea->id) selected @endif>{{ $subarea->area_name }}</option>
                                @endforeach
                            @endif
                        </select>
                        <div class="subarea_err"></div>
                    </div>
                    <div class="form-group  col-md-6">
                        <label for="address ">Connection Address<span class="text-danger">*</span></label>
                        <input type="text" name="address"   placeholder="Enter Address" value="{{ @$edit_user->address }}" class="form-control" id="address">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="logo">NIC Front</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="nic_front"  id="nic_front" onchange="showPreview('preview_nic_front')">
                                <label class="custom-file-label profile_img_label" for="logo">Choose NIC front</label>
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

                    <div class="form-group col-md-6">
                        <label for="logo">NIC Back</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="nic_back"  id="nic_back" onchange="showPreview('preview_nic_back')">
                                <label class="custom-file-label profile_img_label" for="logo">Choose NIC back</label>
                            </div>
                            <div class="nic_back_err w-100"></div>

                            <div class="position-relative mt-3">
                                <img id="preview_nic_back" src="@if(@file_exists($edit_user->nic_back)) {{ asset($edit_user->nic_back) }} @else {{ asset('admin_uploads/no_image.jpg') }} @endif"  class="@if(!isset($is_update)) d-none  @endif" width="100px" height="100px"/>
                                @if(@file_exists($edit_user->nic_back))
                                    <a   href="javascript:void(0)" class="btn btn-danger btn-sm rounded position-absolute nopopup" style="top: 0;right:0" data-url="{{ route('admin.users.remove_attachment',['id'=>$edit_user->hashid,'type'=>'nic_back','path'=>$edit_user->nic_back]) }}" onclick="ajaxRequest(this)" id="remove_nic_back">
                                        <i class="fa fa-times"></i>
                                    </a>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="logo">User Form Front</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="user_form_front"  id="user_form_front" onchange="showPreview('preview_user_form_front')">
                                <label class="custom-file-label profile_img_label" for="logo">Choose NIC front</label>
                            </div>
                            <div class="user_form_front_err w-100"></div>
                            {{-- <i class="fa fa-trash"></i> --}}
                            <div class="position-relative mt-3">
                                <img id="preview_user_form_front" src="@if(@file_exists($edit_user->user_form_front)) {{ asset($edit_user->user_form_front) }} @else {{ asset('admin_uploads/no_image.jpg') }}  @endif"  class="@if(!isset($is_update)) d-none  @endif" width="100px" height="100px"/>
                                @if(@file_exists($edit_user->user_form_front))
                                    <a   href="javascript:void(0)" class="btn btn-danger btn-sm rounded position-absolute nopopup" style="top: 0;right:0" data-url="{{ route('admin.users.remove_attachment',['id'=>$edit_user->hashid,'type'=>'user_form_front','path'=>$edit_user->user_form_front]) }}" onclick="ajaxRequest(this)" id="remove_user_form_front">
                                        <i class="fa fa-times"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="logo">User Form Back</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="user_form_back"  id="user_form_back" onchange="showPreview('preview_user_form_back')">
                                <label class="custom-file-label profile_img_label" for="logo">Choose NIC back</label>
                            </div>
                            <div class="user_form_back_err w-100"></div>

                            <div class="position-relative mt-3">
                                <img id="preview_user_form_back" src="@if(@file_exists($edit_user->user_form_back)) {{ asset($edit_user->user_form_back) }} @else {{ asset('admin_uploads/no_image.jpg') }} @endif"  class="@if(!isset($is_update)) d-none  @endif" width="100px" height="100px"/>
                                @if(@file_exists($edit_user->user_form_back))
                                    <a   href="javascript:void(0)" class="btn btn-danger btn-sm rounded position-absolute nopopup" style="top: 0;right:0" data-url="{{ route('admin.users.remove_attachment',['id'=>$edit_user->hashid,'type'=>'nic_back','path'=>$edit_user->user_form_back]) }}" onclick="ajaxRequest(this)" id="remove_user_form_back">
                                        <i class="fa fa-times"></i>
                                    </a>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>

                <div class="row">
                    @if(auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'supervisor')
                        <div class="form-group col-md-6">
                            <label for="subarea_id">Paid</label>
                            <select class="form-control" name="paid" id="paid">
                                <option value="1">Paid</option>
                                <option value="0">Unpaid</option>
                            </select>
                        </div>
                    @endif
                    {{-- @if(auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'supervisor')
                        <div class="form-group col-md-6">
                            <label for="subarea_id">Tax</label>
                            <select class="form-control" name="is_tax" id="is_tax">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                            <div class="subarea_err"></div>
                        </div>
                    @endif --}}
                    <div class="form-group col-md-6">
                        <label for="subarea_id">Sales Person</label>
                        <select class="form-control" name="sales_id" id="sales_id">
                            <option value="">Select sales person</option>
                            @foreach($user_types->where('user_type', 'sales_person') AS $sales)
                                <option value="{{ $sales->hashid }}" @if(@$edit_user->sales_id == $sales->id) selected @endif>{{ $sales->name }}</option>
                            @endforeach
                        </select>
                        <div class="subarea_err"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="subarea_id">Field Engineer</label>
                        <select class="form-control" name="fe_id" id="fe_id">
                            <option value="">Select field engineer</option>
                            @foreach($user_types->where('user_type', 'field_engineer') AS $engineer)
                                <option value="{{ $engineer->hashid }}" @if(@$edit_user->fe_id == $engineer->id) selected @endif> {{ $engineer->name }}</option>
                            @endforeach
                        </select>
                        <div class="subarea_err"></div>
                    </div>
                </div>


                <div class="form-group mb-3 text-right">
                    <input type="hidden" value="{{ @$edit_user->hashid }}" name="user_id">
                    <button class="btn btn-primary waves-effect waves-light" type="submit" id="submit">
                        {{ (isset($is_update)) ? 'Update' : 'Add' }}
                    </button>
                    <button type="reset" class="btn btn-warning" id="reset">Reset</button>
                    @if(isset($is_update))
                        <a href="{{ route('admin.users.index') }}" class="btn btn-danger">Cancel</a>
                    @endif
                </div>

            </form>
        </div>
    </div>
</div>

@endsection
@section('page-scripts')
<script>
    $(document).ready(function(){
        //masking
        $('#nic').mask('00000-0000000-0');
        $('#mobile').mask('3000000000')
        $('#poc_mobile').mask('3000000000')
        $('#ntn').mask('0000000-0');
        $('#landline_no').mask('000-0000000');

        //display password
        $('.pass-show').click(function(e){
            e.preventDefault();
            $('#password').attr('type','text');
            $('#password_confirmation').attr('type','text');
            $(this).addClass('d-none');
            $('.pass-hide').removeClass('d-none');
        });
        //hide pass
        $('.pass-hide').click(function(e){
            e.preventDefault();
            $('#password').attr('type','password');
            $('#password_confirmation').attr('type','password')
            $(this).addClass('d-none');
            $('.pass-show').removeClass('d-none');
        });
        //id
        var admin_id = "{{ @$edit_user->hashid }}";

        //select2 for area
        $('#area_id').select2({
            placeholder : "Select area"
        });
        //select2 for subarea
        $('#subarea_id').select2({
            placeholder : 'Select Subarea'
        });

        //remove city_id disable attribute when form get submit and all inputs filled
        $('#form').bind('submit',function(){
            if($("#form").valid()){
                $(this).find('#city_id').removeAttr('disabled');
            }
        });

        //area list
        $('#city_id').change(function(){
            var city_id = $(this).val();
            if(city_id.length != 0){
                var route   = "{{ route('admin.areas.area_list',':city_id') }}";
                route       = route.replace(':city_id',city_id);
                getAjaxRequests(route,'','GET',function(resp){
                    $('#subarea_id').empty();
                    $('#area_id').html('<option value="">Select area</option>'+resp.html);
                });
            }
        });

        //reset button
        $('#reset').on('click',function(e){
            e.preventDefault();
            $("#reset").unbind('click').click();
            $('#form').valid();
        });

        // form validation
        $('#form').validate({
            rules:{
                name:{
                    required:true,
                    maxlength:50,
                    lettersonly:true,                 
                },
                username:{
                    required:true,
                    minlength:3,
                    maxlength:function(){
                        return "{{ (!isset($is_update)) ? 15 : 30 }}"
                    },
                    // maxlength:15,
                    nowhitespace:true,
                    remote:{
                        url  : "{{ route('admin.users.check_unique') }}",
                        type : "GET",
                        data : { 
                            column:'username',
                            value:function(){
                                return $('#username').val();
                            },
                            id    : admin_id,
                         },
                    },
                    lettersnumbersonly : true,
                    noCaps : true,
                },
                password:{
                    required:function(element){
                        return admin_id.length == 0;
                    },
                    minlength:6,
                    maxlength:12,
                },
                password_confirmation:{
                    required:function(element){
                        return admin_id.length == 0;
                    },
                    minlength:6,
                    maxlength:12,
                    equalTo:'#password',
                },
                nic:{
                    required:true,
                    minlength:15,
                    maxlength:15,
                    remote:{
                        url  : "{{ route('admin.users.check_unique') }}",
                        type : "GET",
                        data : { 
                            column:'nic',
                            value:function(){
                                return $('#nic').val();
                            },
                            id    : admin_id,
                         },
                    }
                },
                mobile:{
                    required:true,
                    minlength:10,
                    maxlength:10,
                    remote:{
                        url : "{{ route('admin.users.check_unique') }}",
                        type: "GET",
                        data :{
                            column : 'mobile',
                            value:function(){
                                return '92'+$('#mobile').val();
                            },
                            id : admin_id,    
                        },
                    }
                },
                city_id:{
                    required:true
                },
                address:{
                    required:true
                },
                nic_front:{
                    accept: "jpg,jpeg,png",
                    maxsize: 2000000
                },
                nic_back:{
                    accept: "jpg,jpeg,png",
                    maxsize: 2000000
                },
                user_form_front:{
                    accept: "jpg,jpeg,png",
                    maxsize: 2000000
                },
                user_form_back:{
                    accept: "jpg,jpeg,png",
                    maxsize: 2000000
                },
                area_id:{
                    required:false
                },
                subarea_id:{
                    required:false
                }

            },
            highlight:function(element){
                $(element).addClass('is-invalid');
                
            },
            unhighlight:function(element){
                $(element).removeClass('is-invalid');
                $(element).addClass('is-valid');
            },
            messages:{
                username:{
                    remote:'Username already in use'
                },
                email:{
                    remote:'Email already in use'
                },
                nic:{
                    remote:'NIC already in use',
                },
                nic_front:{
                    accept:"invalid file format allowed type ( jpg,jpeg,png )",
                    maxsize:'Max size is 2MB'
                },
                nic_back:{
                    accept:"invalid file format allowed type ( jpg,jpeg,png )",
                    maxsize:'Max size is 2MB'
                },
                mobile:{
                    remote:'Number already in use',
                }
            },
            errorPlacement:function(error,element){
                if(element.attr('name') == 'nic_front'){
                    error.appendTo('.nic_front_err');
                }else if(element.attr('name') == 'nic_back'){
                    error.appendTo('.nic_back_err');
                }else if(element.attr('name') == 'mobile'){
                    error.appendTo('.mobile_err');
                }else if(element.attr('name') == 'username'){
                    error.appendTo('.username_err');
                }else if(element.attr('name') == 'user_form_front'){
                    error.appendTo('.user_form_front_err');
                }else if(element.attr('name') == 'user_form_back'){
                    error.appendTo('.user_form_back_err');
                }else if(element.attr('name') == 'area_id'){
                    error.appendTo('.area_err');
                }else if(element.attr('name') == 'subarea_id'){
                    error.appendTo('.subarea_err');
                }else{
                    element.after( error );
                }
            }
        });
        //validate each input field type of text,password,email
        $('input[type="text"],input[type="email"],input[type="password"],select').blur(function(){
            var id = $(this).attr('id');
            var validator = $('#form').validate();
            validator.element('#'+id);
        });
        //validate input type file
        $('input[type="file"]').change(function(){
            var id = $(this).attr('id');
            var validator = $('#form').validate();
            validator.element('#'+id);
        });

        //subareas
        $('#area_id').change(function(){
            var area_id = $(this).val();
            var route = "{{ route('admin.areas.sub_area_list',':id') }}";
            route = route.replace(':id',area_id);
            
            if(area_id.length != 0){
                getAjaxRequests(route, '', 'GET', function(resp){
                    $('#subarea_id').html(resp.html);
                });
            }
        });

    });
    
    //when there is change is user type
    $('#user_type').change(function(){
        if($(this).val() == 'company'){
            hideAndShow('hide', 'ind');
            hideAndShow('show', 'compy');
        }else{
            hideAndShow('show', 'ind');
            hideAndShow('hide', 'compy');
        }
    });

    function hideAndShow(type, class_name){
        if(type == 'hide'){
            $('.'+class_name).addClass('d-none');
        }else{
            $('.'+class_name).removeClass('d-none');
        }
    }
</script>
@endsection
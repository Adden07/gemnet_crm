@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.admins.index') }}">Admin</a></li>
                    <li class="breadcrumb-item active">{{ isset($is_update) ? 'Edit' : 'Add'}} </li>
                </ol>
            </div>
            <h4 class="page-title">{{ isset($is_update) ? 'Edit' : 'Add'}} Admin</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            <h4 class="header-title m-t-0">{{ isset($is_update) ? 'Edit' : 'Add'}} Admin</h4>
            <p class="text-muted font-14 m-b-20">
                Here you can {{ isset($is_update) ? 'Edit' : 'Add'}} Admin.
            </p>

            <form action="{{ route('admin.admins.store') }}" class="ajaxForm" method="post" enctype="multipart/form-data" novalidate id="form">
                @csrf

                <div class="row">
                    <div class="form-group mb-3 col-md-6">
                        <label for="name">Name<span class="text-danger">*</span></label>
                        <input type="text" name="name"   placeholder="Enter name" value="{{ @$edit_admin->name }}" class="form-control" id="name">
                    </div>

                    <div class="form-group mb-3 col-md-6">
                        <label for="username">Username<span class="text-danger">*</span></label>
                        <input type="text" name="username"  minlength="3" maxlength="10"placeholder="Enter username" value="{{ @$edit_admin->username }}" class="form-control" id="username" @if(isset($is_update)) readonly style="background-color:#e9ecef" @endif>
                    </div>
                </div>    
                @if(!isset($is_update))
                    <div class="row">
                        <div class="form-group  mb-3 col-md-6">
                            <label for="password">Password<span class="text-danger">*</span></label>
                            <div class="input-group mpass">
                                <input type="password" name="password" placeholder="Enter password" value="" class="form-control" id="password" ><span class="input-group-text pass-show"><a href="javascript:void(0)"><i class="fa fa-eye"></i></a></span><span class="input-group-text pass-hide d-none"><a href="javascript:void(0)"><i class="fa fa-eye-slash"></i></a></span>
                            </div>
                            
                        </div>

                        <div class="form-group mb-3 col-md-6">
                            <label for="password_confirmation ">Confirm Password<span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" placeholder="Confirm password" value="" class="form-control" id="password_confirmation" >
                        </div>
                    </div>    
                @endif
                <div class="row">
                    <div class="form-group mb-3 col-md-6">
                        <label for="email">Email<span class="text-danger">*</span></label>
                        <input type="email" name="email"   placeholder="Enter email" value="{{ @$edit_admin->email }}" class="form-control" id="email">
                    </div>

                    <div class="form-group mb-3 col-md-6">
                        <label for="nic ">NIC<span class="text-danger">*</span></label>
                        <input type="text" name="nic"   placeholder="Enter NIC" value="{{ @$edit_admin->nic }}" class="form-control" id="nic">
                    </div>
                </div>    

                <div class="row">
                    <div class="form-group mb-3 col-md-6">
                        <label for="city_id">Mobile No<span class="text-danger">*</span></label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                              <span class="input-group-text" id="basic-addon1">92</span>
                            </div>
                            <input type="text" name="mobile"  placeholder="Enter mobile no" value="{{ @substr($edit_admin->mobile,2) }}" class="form-control" id="mobile">
                            <div class="mobile_err w-100"></div>
                        </div>
                    </div>

                    <div class="form-group mb-3 col-md-6">
                        <label for="address ">Address<span class="text-danger">*</span></label>
                        <input type="text" name="address"   placeholder="Enter Address" value="{{ @$edit_admin->address }}" class="form-control" id="address">
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
                            {{-- <img id="preview_nic_front" src="@if(@file_exists($edit_admin->nic_front)) {{ asset($edit_admin->nic_front) }} @else {{ asset('admin_uploads/no_image.jpg') }}  @endif"  class="@if(!isset($is_update)) d-none  @endif" width="100px" height="100px"/>
                            
                            @if(@file_exists($edit_admin->nic_front))
                                <a   href="javascript:void(0)" class="btn btn-danger btn-sm rounded position-absolute nopopup" style="top: 0;right:0" data-url="{{ route('admin.users.remove_attachment',['id'=>$edit_admin->hashid,'type'=>'nic_front','path'=>$edit_admin->nic_front]) }}" onclick="ajaxRequest(this)" id="remove_nic_front">
                                    <i class="fa fa-times"></i>
                                </a>
                            @endif --}}
                            <div class="position-relative mt-3">
                                <img id="preview_nic_front" src="@if(@file_exists($edit_admin->nic_front)) {{ asset($edit_admin->nic_front) }} @else {{ asset('admin_uploads/no_image.jpg') }}  @endif"  class="@if(!isset($is_update)) d-none  @endif" width="100px" height="100px"/>
                                @if(@file_exists($edit_admin->nic_front))
                                    <a   href="javascript:void(0)" class="btn btn-danger btn-sm rounded position-absolute nopopup" style="top: 0;right:0" data-url="{{ route('admin.admins.remove_attachment',['id'=>$edit_admin->hashid,'type'=>'nic_front','path'=>$edit_admin->nic_front]) }}" onclick="ajaxRequest(this)" id="remove_nic_front">
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
                            {{-- <img id="preview_nic_back" src="@if(@file_exists($edit_admin->nic_back)) {{ asset($edit_admin->nic_back) }} @else {{ asset('admin_uploads/no_image.jpg') }} @endif"  class="@if(!isset($is_update)) d-none  @endif" width="100px" height="100px"/> --}}
                            <div class="position-relative mt-3">
                                <img id="preview_nic_front" src="@if(@file_exists($edit_admin->nic_back)) {{ asset($edit_admin->nic_back) }} @else {{ asset('admin_uploads/no_image.jpg') }}  @endif"  class="@if(!isset($is_update)) d-none  @endif" width="100px" height="100px"/>
                                @if(@file_exists($edit_admin->nic_back))
                                    <a   href="javascript:void(0)" class="btn btn-danger btn-sm rounded position-absolute nopopup" style="top: 0;right:0" data-url="{{ route('admin.admins.remove_attachment',['id'=>$edit_admin->hashid,'type'=>'nic_back','path'=>$edit_admin->nic_back]) }}" onclick="ajaxRequest(this)" id="remove_nic_back">
                                        <i class="fa fa-times"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="logo">Admin Photo</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="image" id="logo" onchange="showPreview('preview_image')">
                                <label class="custom-file-label profile_img_label" for="logo">Choose admin photo</label>
                            </div>
                            <div class="image_err w-100"></div>
                            {{-- <img id="preview_image" src="@if(@file_exists($edit_admin->image)) {{ asset($edit_admin->image) }} @else {{ asset('admin_uploads/no_image.jpg') }} @endif"  class="@if(!isset($is_update)) d-none  @endif" width="100px" height="100px"/> --}}
                            <div class="position-relative mt-3">
                                <img id="preview_nic_front" src="@if(@file_exists($edit_admin->image)) {{ asset($edit_admin->image) }} @else {{ asset('admin_uploads/no_image.jpg') }}  @endif"  class="@if(!isset($is_update)) d-none  @endif" width="100px" height="100px"/>
                                @if(@file_exists($edit_admin->image))
                                    <a   href="javascript:void(0)" class="btn btn-danger btn-sm rounded position-absolute nopopup" style="top: 0;right:0" data-url="{{ route('admin.admins.remove_attachment',['id'=>$edit_admin->hashid,'type'=>'image','path'=>$edit_admin->image]) }}" onclick="ajaxRequest(this)" id="remove_image">
                                        <i class="fa fa-times"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>



                <div class="form-group mb-3 text-right">
                    <input type="hidden" value="{{ @$edit_admin->hashid }}" name="admin_id">
                    <button class="btn btn-primary waves-effect waves-light" type="submit" id="submit">
                        {{ (isset($is_update)) ? 'Update' : 'Add' }}
                    </button>
                    <button type="reset" class="btn btn-warning" id="reset">Reset</button>
                    @if(isset($is_update))
                        <a href="{{ route('admin.admins.index') }}" class="btn btn-danger">Cancel</a>
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
        var is_update = "{{ @$is_update }}";
        
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
        var admin_id = "{{ @$edit_admin->hashid }}";

        //reset button
        $('#reset').on('click',function(e){
            // alert('done');
            e.preventDefault();
            // $('#form').trigger("reset");
            // document.getElementById("form").reset();
            $("#reset").unbind('click').click();
            // $('#reset').removeAttr('disabled');
            // e.preventDefault = true;
            $('#form').valid();
            // $('#form').submit();
            
        });

        // form validation
        $('#form').validate({
            rules:{
                name:{
                    // required:true,
                    // maxlength:50,
                    // alphanumeric:true, 
                    // nowhitespace:true,
                    // lettersonly:true,
                    required:true,
                    maxlength:50,
                    lettersonly:true,   
                    
                },
                username:{
                    required:true,
                    minlength:function(element){
                        if(!is_update){
                            return 3;
                        }
                    },
                    maxlength:10,
                    nowhitespace:true,
                    remote:{
                        url  : "{{ route('admin.admins.check_unique') }}",
                        type : "GET",
                        data : { 
                            column:'username',
                            value:function(){
                                return $('#username').val();
                            },
                            id    : admin_id,
                         },
                    },
                    alphanumeric:true,
                },
                password:{
                    required:function(element){
                        return admin_id.length == 0;
                    },
                    minlength:6,
                },
                password_confirmation:{
                    required:function(element){
                        return admin_id.length == 0;
                    },
                    minlength:6,
                    equalTo:'#password',
                },
                email:{
                    required:true,
                    nowhitespace:true,
                    email:true,
                    maxlength:191,
                    remote:{
                        url  : "{{ route('admin.admins.check_unique') }}",
                        type : "GET",
                        data : { 
                            column:'email',
                            value:function(){
                                return $('#email').val();
                            },
                            id    : admin_id,
                        },
                    }
                },
                nic:{
                    required:true,
                    minlength:15,
                    maxlength:15,
                    // remote:{
                    //     url  : "{{ route('admin.admins.check_unique') }}",
                    //     type : "GET",
                    //     data : { 
                    //         column:'nic',
                    //         value:function(){
                    //             return $('#nic').val();
                    //         },
                    //         id    : admin_id,
                    //      },
                    // }
                },
                mobile:{
                    required:true,
                    minlength:10,
                    maxlength:10
                },
                address:{
                    required:true
                },
                nic_front:{
                    // required:function(){
                    //     return admin_id.length == 0;
                    // },
                    accept: "jpg,jpeg,png",
                    maxsize: 2000000
                },
                nic_back:{
                    // required:function(){
                    //     return admin_id.length == 0;
                    // },
                    accept: "jpg,jpeg,png",
                    maxsize: 2000000
                },
                image:{
                    // required:function(){
                    //     return admin_id.length == 0;
                    // },
                    accept: "jpg,jpeg,png",
                    maxsize: 2000000
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
                image:{
                    accept:"invalid file format allowed type ( jpg,jpeg,png )",
                    maxsize:'Max size is 2MB'
                },
                
            },
            errorPlacement:function(error,element){
                if(element.attr('name') == 'nic_front'){
                    error.appendTo('.nic_front_err');
                }else if(element.attr('name') == 'nic_back'){
                    error.appendTo('.nic_back_err');
                }else if(element.attr('name') == 'image'){
                    error.appendTo('.image_err');
                }else if(element.attr('name') == 'mobile'){
                    error.appendTo('.mobile_err');
                }else{
                    element.after( error );
                }
            }
        });
        //validate each input field type of text,password,email
        $('input[type="text"],input[type="email"],input[type="password"]').blur(function(){
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


    });
    
</script>
@endsection
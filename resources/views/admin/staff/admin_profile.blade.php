@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.admins.index') }}">Admin</a></li>
                    <li class="breadcrumb-item active">Profile </li>
                </ol>
            </div>
            <h4 class="page-title">Profile-{{ $admin_details->username }} </h4>
        </div>
    </div>
</div>

<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link active" id="personal_info_tab" data-toggle="tab" href="#personal_info" role="tab" aria-controls="personal_info" aria-selected="true" >Personal Info</a>
      </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="change_pass_tab" data-toggle="tab" href="#change_pass" role="tab" aria-controls="change_pass" aria-selected="true" >Change Password</a>
    </li>

    <li class="nav-item" role="presentation">
        <a class="nav-link" id="doc_tab" data-toggle="tab" href="#document_tab" role="tab" aria-selected="true" >Documents</a>
    </li>

    <li class="nav-item" role="presentation">
        <a class="nav-link" id="record_tab" data-toggle="tab" href="#record" role="tab" aria-selected="true" >Records-activity</a>
    </li>
</ul>

<div class="tab-content pt-0" id="myTabContent">
    <div class="tab-pane fade show active" id="personal_info" role="tabpanel">
        <div class="row">
            <div class="col-lg-12">
                <div class="card-box">
                    @can('edit-staff')
                        <a href="{{ route('admin.users.edit',['id'=>$admin_details->hashid]) }}" class="btn btn-primary float-right mb-3" id="edit_personal_info">Edit User</a>
                        <a href="javascript:void(0)" onClick="window.location.reload()"  class="btn btn-primary float-right mb-3 mr-2 d-none" id="reset_btn">Cancel</a>
                    @endcan
                    
                    @if($admin_details->is_active == 'active')
                        <a href="javascript:void(0)" class="btn btn-danger float-right mb-3 mr-2 nopopup" id="edit_personal_info" data-url="{{ route('admin.profiles.disable_user',['id'=>$admin_details->hashid]) }}" onclick="ajaxRequest(this)">Disable</a>

                    @else
                        <a href="javascript:void(0)" class="btn btn-info float-right mb-3 mr-2 nopopup" id="edit_personal_info" data-url="{{ route('admin.profiles.enable_user',['id'=>$admin_details->hashid]) }}" onclick="ajaxRequest(this)">Enable</a>
                    @endif

                    <form action="{{ route('admin.admins.update_info') }}" method="POST" class="ajaxForm" id="personal_info_form">
                        @csrf
                        <table class="table">
                            <tr>
                                <th>Name</th>
                                <td style="vertical-align: middle;padding-top: 0;padding-bottom: 0;"><input type="text" class="form-control border-0" disabled name="name" id="name" data-ov="{{ $admin_details->name }}" value="{{ $admin_details->name }}"></td>
                            </tr>
                            <tr>
                                <th>NIC</th>
                                <td style="vertical-align: middle;padding-top: 0;padding-bottom: 0;"><input type="text" class="form-control border-0" disabled name="nic" id="nic" data-ov="{{ $admin_details->nic }}" value="{{ $admin_details->nic }}"></td>
                            </tr>
                            <tr>
                                <th>Mobile</th>
                                <td style="vertical-align: middle;padding-top: 0;padding-bottom: 0;">
                                    {{-- <div class="d-none"id="show_mobile_code">

                                    </div> --}}
                                    <div id="mobile_code">
                                        <input type="text" class="form-control border-0" disabled name="mobile" id="mobile" data-ov="{{ substr($admin_details->mobile,2) }}"  value="{{ substr($admin_details->mobile,2) }}">
                                        <div class="mobile_err w-100"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <td style="vertical-align: middle;padding-top: 0;padding-bottom: 0;"><input type="text" class="form-control border-0" disabled name="address" id="address" data-ov="{{ $admin_details->address }}" value="{{ $admin_details->address }}"></td>
                            </tr>
                        </table>
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" name="admin_id" value="{{ $admin_details->hashid }}">
                                @can('edit-staff')
                                    <input type="submit" class="btn btn-primary float-right d-none" value="update" id="personal_info_form_submit">
                                @endcan
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="change_pass" role="tabpanel">
        <div class="row">
            <div class="col-lg-12">
                <div class="card-box">
                    <form action="{{ route('admin.admins.update_password') }}" class="ajaxForm" method="post" enctype="multipart/form-data" novalidate id="change_pass_form">
                        @csrf
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
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" name="admin_id" value="{{ $admin_details->hashid }}">
                                <input type="submit" class="btn btn-primary float-right"value="Update">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="document_tab" role="tabpanel">
        <div class="row">
            <div class="col-lg-12">
                <div class="card-box">
                    <form action="{{ route('admin.admins.update_document') }}" method="POST" enctype="multipart/form-data" class="ajaxForm" id="document_form">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="logo">NIC Front</label>
                                <div class="input-group">
                                    @can('update-admin-documents')
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="nic_front"  id="nic_front" onchange="showPreview('preview_nic_front')">
                                            <label class="custom-file-label profile_img_label" for="logo">Choose NIC front</label>
                                        </div>
                                    @endcan
                                    <div class="nic_front_err w-100"></div>
                                    <div class="position-relative mt-3">
                                        <img id="preview_nic_front" src="@if(@file_exists($admin_details->nic_front)) {{ asset($admin_details->nic_front) }} @else {{ asset('admin_uploads/no_image.jpg') }}  @endif"  class="@if(!isset($is_update)) @endif" width="100px" height="100px"/>
                                        @can('update-admin-documents')
                                            @if(@file_exists($admin_details->nic_front))
                                                <a   href="javascript:void(0)" class="btn btn-danger btn-sm rounded position-absolute" style="top: 0;right:0" data-url="{{ route('admin.admins.remove_attachment',['id'=>$admin_details->hashid,'type'=>'nic_front','path'=>$admin_details->nic_front]) }}" onclick="ajaxRequest(this)" id="remove_nic_front">
                                                    <i class="fa fa-times"></i>
                                                </a>
                                            @endif
                                        @endcan
                                    </div>
                                </div>
                            </div>
        
                            <div class="form-group col-md-6">
                                <label for="logo">NIC Back</label>
                                <div class="input-group">
                                    @can('update-admin-documents')
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="nic_back"  id="nic_back" onchange="showPreview('preview_nic_back')">
                                            <label class="custom-file-label profile_img_label" for="logo">Choose NIC back</label>
                                        </div>
                                    @endcan
                                    <div class="nic_back_err w-100"></div>
        
                                    <div class="position-relative mt-3">
                                        <img id="preview_nic_back" src="@if(@file_exists($admin_details->nic_back)) {{ asset($admin_details->nic_back) }} @else {{ asset('admin_uploads/no_image.jpg') }} @endif"  class="@if(!isset($is_update))  @endif" width="100px" height="100px"/>
                                        @can('update-admin-documents')
                                            @if(@file_exists($admin_details->nic_back))
                                                <a   href="javascript:void(0)" class="btn btn-danger btn-sm rounded position-absolute" style="top: 0;right:0" data-url="{{ route('admin.admins.remove_attachment',['id'=>$admin_details->hashid,'type'=>'nic_back','path'=>$admin_details->nic_back]) }}" onclick="ajaxRequest(this)" id="remove_nic_back">
                                                    <i class="fa fa-times"></i>
                                                </a>
                                            @endif
                                        @endcan
                                    </div>
        
                                </div>
                            </div>
                        </div>
        
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="logo">Admin Profile</label>
                                <div class="input-group">
                                    @can('update-admin-documents')
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input"  name="image"  id="image" onchange="showPreview('preview_user_form_back')">
                                            <label class="custom-file-label profile_img_label" for="logo">Choose NIC back</label>
                                        </div>
                                    @endcan
                                    <div class="user_form_back_err w-100"></div>
        
                                    <div class="position-relative mt-3">
                                        <img id="preview_user_form_back" src="@if(@file_exists($admin_details->image)) {{ asset($admin_details->image) }} @else {{ asset('admin_uploads/no_image.jpg') }} @endif"  class="@if(!isset($is_update))  @endif" width="100px" height="100px"/>
                                        @can('update-admin-documents')
                                            @if(@file_exists($admin_details->image))
                                                <a   href="javascript:void(0)" class="btn btn-danger btn-sm rounded position-absolute" style="top: 0;right:0" data-url="{{ route('admin.admins.remove_attachment',['id'=>$admin_details->hashid,'type'=>'image','path'=>$admin_details->image]) }}" onclick="ajaxRequest(this)" id="remove_user_form_back">
                                                    <i class="fa fa-times"></i>
                                                </a>
                                            @endif
                                        @endcan
                                    </div>
        
                                </div>
                            </div>
                        </div>
                        @can('update-admin-documents')
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" name="old_nic_front" value="{{ @$admin_details->nic_front }}">
                                <input type="hidden" name="old_nic_back" value="{{ @$admin_details->nic_back }}">
                                <input type="hidden" name="old_image" value="{{ @$admin_details->image }}">
                                <input type="hidden" name="user_id" value="{{ $admin_details->hashid }}">
                                <input type="submit" class="btn btn-primary float-right mt-2" value="Update"> 
                            </div>
                        </div>
                        @endcan
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="record" role="tabpanel">
        <div class="row">
            <div class="col-lg-12">
                <div class="card-box">
                    <table class="table">
                        <thead>
                            <th>No</th>
                            {{-- <th>Username</th> --}}
                            {{-- <th>Type</th> --}}
                            <th>IP</th>
                            <th>Activity</th>
                            <th>Date</th>
                        </thead>
                        <tbody>
                            @foreach($activity_logs AS $log)
                                <tr>
                                    <td>{{ $activity_logs->firstItem() +$loop->index }}</td>
                                    <td>{{ $log->user_ip }}</td>
                                    <td>{{ $log->activity }}</td>
                                    <td>{{ date('d-M-y H:i:s',strtotime($log->created_at)) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex">
                        <div class="ml-auto">
                            {{ $activity_logs->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
@section('page-scripts')
<script>
    //which tab to open when page get reload
    var tabs_local_storage = localStorage.getItem('tab');

    if(tabs_local_storage == null){
        tabs_local_storage = localStorage.setItem('tab','service_details');
    }
    $('#personal_info_tab').click(function(){
        tabs_local_storage = localStorage.setItem('tab','personal_info_tab');
    });
    $('#change_pass_tab').click(function(){
        tabs_local_storage = localStorage.setItem('tab','change_pass_tab');
    });
    $('#doc_tab').click(function(){
        tabs_local_storage = localStorage.setItem('tab','doc_tab');
    });
    $('#record_tab').click(function(){
        tabs_local_storage = localStorage.setItem('tab','record');
    });
    $('#service_details_tab').click(function(){
        tabs_local_storage = localStorage.setItem('tab','service_details');
    });

    if(tabs_local_storage == 'personal_info_tab'){
        $('#personal_info_tab').click();
    }else if(tabs_local_storage == 'change_pass_tab'){
        $('#change_pass_tab').click();
    }else if(tabs_local_storage == 'doc_tab'){
        $('#doc_tab').click();
    }else if(tabs_local_storage == 'record'){
        $('#record_tab').click();
    }else if(tabs_local_storage == 'service_details'){
        $('#service_details_tab').click();
    }


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


    //change password form validation
    $('#change_pass_form').validate({
        rules:{
            password:{
                required:true,
                minlength:6,
                maxlength:12
            },
            password_confirmation:{
                required:true,
                minlength:6,
                maxlength:12,
                equalTo:'#password',
            }
        },
        highlight:function(element){
            $(element).addClass('is-invalid');        
        },
        unhighlight:function(element){
            $(element).removeClass('is-invalid');
            $(element).addClass('is-valid');
        },
    });

    $('#change_pass_form input[type="password"]').blur(function(){
        var id = $(this).attr('id');
        var validator = $('#change_pass_form').validate();
        validator.element('#'+id);
    });

    //document form validaton
    $('#document_form').validate({
        rules:{
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
        },
        highlight:function(element){
            $(element).addClass('is-invalid');        
        },
        unhighlight:function(element){
            $(element).removeClass('is-invalid');
            $(element).addClass('is-valid');
        },
        messages:{
            nic_front:{
                accept:"invalid file format allowed type ( jpg,jpeg,png )",
                maxsize:'Max size is 2MB'
            },
            nic_back:{
                accept:"invalid file format allowed type ( jpg,jpeg,png )",
                maxsize:'Max size is 2MB'
            },
            user_form_front:{
                accept:"invalid file format allowed type ( jpg,jpeg,png )",
                maxsize:'Max size is 2MB'
            },
            user_form_back:{
                accept:"invalid file format allowed type ( jpg,jpeg,png )",
                maxsize:'Max size is 2MB'
            },
        },
        errorPlacement:function(error,element){
            if(element.attr('name') == 'nic_front'){
                error.appendTo('.nic_front_err');
            }else if(element.attr('name') == 'nic_back'){
                error.appendTo('.nic_back_err');
            }else if(element.attr('name') == 'user_form_front'){
                error.appendTo('.user_form_front_err');
            }else if(element.attr('name') == 'user_form_back'){
                error.appendTo('.user_form_back_err');
            }else{
                element.after( error );
            }
        }
    });

    $('#document_form input[type="file"]').change(function(){
        var id = $(this).attr('id');
        var validator = $('#document_form').validate();
        validator.element('#'+id);
    });
    
    /*
        edit user personal information
    */
    
    //function to detect changes in input and select
    function detectChangesInPersonaInForm(){

        var break_out = true;

        $('#personal_info_form input').each(function(index,value){
            
            var id      = $(this).attr('id');
            var new_val = $(this).val();
            var old_val = $(this).data('ov');   

            if(id == 'name' || id == 'nic' || id == 'address'){
                if(new_val != old_val){
                    break_out = false;
                }
            }
        });

        //for mobile because mobile input is dyamic 
        if(break_out != false){
            if($('#mobile').val() != $('#mobile').data('ov')){
                break_out = false;
            }
        }

        //for city_id,area_id,subarea_id
        // if(break_out != false){
        //     if($('#city_id').val().length > 0 && $('#city_id').val() != $('#city_id').data('ov')){
        //         break_out = false;
        //     }else if($('#area_id').val().length > 0 && $('#area_id').val() != $('#area_id').data('ov')){
        //         break_out = false;
        //     }
        //     //for subarea
        //     if($('#subarea_id').val() == null || $('#subarea_id').val() == 'undefined'){
        //         // console.log('null');
        //         break_out = false;
        //     }else if($('#subarea_id').val() != $('#subarea_id').data('ov')){
        //         // console.log('not');
        //         break_out = false;
        //     }
        // }
        return break_out;
    }

    //display in put fields and fomr update options
    $('#edit_personal_info').click(function(e){
        e.preventDefault();
        page_loader('show');
        
        $('#personal_info_form input').removeClass('border-0');//remove border
        $('#personal_info_form input').removeAttr('disabled');//remove disabled attribute
        
        //if user is admin or superadmin dont remove disabeld attribute from city_id
        @if(auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'superadmin')
            $('#personal_info_form select').removeAttr('disabled');
        @else
            $('#personal_info_form select').not('#city_id').removeAttr('disabled');
        @endif
        //mobile html
        var mobile_html = '<div class="input-group ">'+
                            '<div class="input-group-prepend">'+
                                '<span class="input-group-text" id="basic-addon1">92</span>'+
                            '</div>'+
                            '<input type="text" name="mobile"  placeholder="Enter mobile no" value="{{ @substr($admin_details->mobile,2) }}"'+ 'class="form-control" id="mobile" data-ov="{{ @substr($admin_details->mobile,2) }}">'+
                            '<div class="mobile_err w-100"></div>'+
                        '</div>';
        $('#mobile_code').html(mobile_html);

        //set masking here because it will not work outside because the filed is dynamic
        $('#personal_info_form #mobile').mask('3000000000');

        $('#reset_btn').removeClass('d-none');
        page_loader('hide');
    });

    //area list
    $('#city_id').change(function(){
        var city_id = $(this).val();
        if(city_id.length != 0){
            var route   = "";
            route       = route.replace(':city_id',city_id);
            getAjaxRequests(route,'','GET',function(resp){
                $('#subarea_id').empty();
                $('#area_id').html('<option value="">Select area</option>'+resp.html);
            });
        }
    });

    //subareas
    $('#area_id').change(function(){
        var area_id = $(this).val();
        var route = "{{ route('admin.users.subareas',':id') }}";
        route = route.replace(':id',area_id);
        
        if(area_id.length != 0){
            getAjaxRequests(route, '', 'GET', function(resp){
                $('#subarea_id').html(resp.html);
            });
        }
    });
        

        //masking
        $('#nic').mask('00000-0000000-0');
        $('#personal_info_form #mobile').mask('3000000000');
        
        var admin_id = "{{ @$user_details->hashid }}";

        // form validation
        $('#personal_info_form').validate({
        rules:{
            name:{
                required:true,
                maxlength:50,                    
            },
            nic:{
                required:true,
                minlength:15,
                maxlength:15,
                // remote:{
                //     url  : "{{ route('admin.users.check_unique') }}",
                //     type : "GET",
                //     data : { 
                //         column:'nic',
                //         value:function(){
                //             return $('#nic').val();
                //         },
                //         id    : admin_id,
                //         },
                // }
            },
            mobile:{
                required:true,
                minlength:10,
                maxlength:10,
                // remote:{
                //     url : "{{ route('admin.users.check_unique') }}",
                //     type: "GET",
                //     data :{
                //         column : 'mobile',
                //         value:function(){
                //             return '92'+$('#mobile').val();
                //         },
                //         id : admin_id,    
                //     },
                // }
            },
            // city_id:{
            //     required:true
            // },
            // address:{
            //     required:true
            // },
            // area_id:{
            //     required:true
            // },
            // subarea_id:{
            //     required:true
            // }

        },
        highlight:function(element){
            $(element).addClass('is-invalid');
            
        },
        unhighlight:function(element){
            $(element).removeClass('is-invalid');
            $(element).addClass('is-valid');
        },
        messages:{
            nic:{
                remote:'NIC already in use',
            },
            mobile:{
                remote:'Number already in use',
            }
        },
        errorPlacement:function(error,element){
            if(element.attr('name') == 'mobile'){
                error.appendTo('.mobile_err');
            }else{
                element.after( error );
            }
        }
    });
    //validate each input field type of text,password,email
    $('input[type="text"],select').blur(function(){
        var id = $(this).attr('id');
        // alert(id);
        var validator = $('#personal_info_form').validate();
        validator.element('#'+id);
    });


    /*
        make the personal info form smart
    */
    $('#personal_info_form input').keyup(function(){
       var result =  detectChangesInPersonaInForm();
       if(result == false){
            $('#personal_info_form_submit').removeClass('d-none');
        }else{
            $('#personal_info_form_submit').addClass('d-none');
        }
    });
    //for mobile input because its dynamic 
    $(document).on('keyup','#mobile',function(){
        var result =  detectChangesInPersonaInForm();
        if(result == false){
            $('#personal_info_form_submit').removeClass('d-none');
        }else{
            $('#personal_info_form_submit').addClass('d-none');
        }
    });
    
    // $('#personal_info_form select').change(function(){
    //     if($(this).val().length > 0){
    //          // execute after some time because when there is change in area it insert the options in subarea with out this subarea will be null
    //         setTimeout(function(){
    //             var result =  detectChangesInPersonaInForm();
    //             if(result == false){
    //                 $('#personal_info_form_submit').removeClass('d-none');
    //             }else{
    //                 $('#personal_info_form_submit').addClass('d-none');
    //             }
    //         }, 500)

    //     }
    // });
</script>
@endsection
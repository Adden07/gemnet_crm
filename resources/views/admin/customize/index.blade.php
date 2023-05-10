@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="">Admin</a></li>
                    <li class="breadcrumb-item active"> Customize</li>
                </ol>
            </div>
            <h4 class="page-title"> customization</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            <h4 class="header-title m-t-0">{{ isset($staff) ? 'Edit' : 'Add'}} customization</h4>
            <p class="text-muted font-14 m-b-20">
                Here you can Customize layotus.
            </p>

            <form action="{{ route('admin.customizes.store') }}" class="ajaxForm" method="post" enctype="multipart/form-data" novalidate>
                @csrf   

                    {{-- <input type="hidden" name="admin_id" value="<?= $this->session->userdata('user_id') ?>"> --}}
                    <div class="form-group">
                        <label for="">Top Bar</label>
                        <input type="text" class="jscolor form-control" value="{{ @$data['top_bar'] ?? '2c94f2' }}" style="" name="top_bar">
                    </div>
                    <div class="form-group">
                        <label for="">Side Bar</label>
                        <input type="text" class="jscolor form-control" value="{{ @$data['side_bar'] ?? '38414a' }}" name="side_bar">
                    </div>
                    <div class="form-group">
                        <label for="">Footer</label>
                        <input type="text" class="jscolor form-control" value="{{ @$data['footer'] ?? '2c94f2' }}" name="footer">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Theme Primary</label>
                                <input type="text" class="jscolor form-control" value="{{ @$data['primary'] ?? '2c94f2' }}" name="primary">
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary" type="button">Sample Button</button>
                                <label for="" class="label label-primary">Sample Label</label>
                                <label for="" class="badge badge-primary">Sample Badge</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                    <label for="">Primary Hover</label>
                                    <input type="text" class="jscolor form-control" value="{{ @$data['primary_hover'] ?? '1479B8' }}" name="primary_hover">
                                </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Theme Success</label>
                                <input type="text" class="jscolor form-control" value="{{ @$data['success'] ?? '1ABB9C' }}" name="success">
                            </div>
                            <div class="form-group">
                                <button class="btn btn-success" type="button">Sample Button</button>
                                <label for="" class="label label-success">Sample Label</label>
                                <label for="" class="badge badge-success">Sample Badge</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                    <label for="">Success Hover</label>
                                    <input type="text" class="jscolor form-control" value="{{ @$data['success_hover'] ?? '19A98B' }}" name="success_hover">
                                </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Theme Info</label>
                                <input type="text" class="jscolor form-control" value="{{ @$data['info'] ?? '31b0d5' }}" name="info">
                            </div>
                            <div class="form-group">
                                <button class="btn btn-info" type="button">Sample Button</button>
                                <label for="" class="label label-info">Sample Label</label>
                                <label for="" class="badge badge-info">Sample Badge</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                    <label for="">Info Hover</label>
                                    <input type="text" class="jscolor form-control" value="{{ @$data['info_hover'] ?? '4FB5D3' }}" name="info_hover">
                                </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Theme Warning</label>
                                <input type="text" class="jscolor form-control" value="{{ @$data['warning'] ?? 'ec971f' }}" name="warning">
                            </div>
                            <div class="form-group">
                                <button class="btn btn-warning" type="button">Sample Button</button>
                                <label for="" class="label label-warning">Sample Label</label>
                                <label for="" class="badge badge-warning">Sample Badge</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                    <label for="">Warning Hover</label>
                                    <input type="text" class="jscolor form-control" value="{{ @$data['warning_hover'] ?? 'd58512' }}" name="warning_hover">
                                </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Theme Danger</label>
                                <input type="text" class="jscolor form-control" value="{{ @$data['danger'] ?? 'd43f3a' }}" name="danger">
                            </div>
                            <div class="form-group">
                                <button class="btn btn-danger" type="button">Sample Button</button>
                                <label for="" class="label label-danger">Sample Label</label>
                                <label for="" class="badge badge-danger">Sample Badge</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                    <label for="">Danger Hover</label>
                                    <input type="text" class="jscolor form-control" value="{{ @$data['danger_hover'] ?? 'c9302c' }}" name="danger_hover">
                                </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="">Fonts</label>
                        <input type="text" class="jscolor form-control" value="{{ @$data['fonts'] ?? '73879C' }}" name="fonts">
                        <p >Sample Text</p>
                    </div>
                    <div class="row">
                        <div class="col-md-6">

                            <div class="form-group">
                                <label for="">Links</label>
                                <input type="text" class="jscolor form-control" value="{{ @$data['links'] ?? '5A738E' }}" name="links">
                                <a href="#">Sample Link</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                        <div class="form-group">
                                <label for="">Links Hover</label>
                                <input type="text" class="jscolor form-control" value="{{ @$data['links_hover'] ?? '5A738E' }}" name="links_hover">
                            </div>
                        </div>
                    </div>
                    {{--<div class="form-group " style="margin-top:10px;">
                        <input type="submit" class="btn btn-primary" value="Update">
                         if(!empty($color->id)): 
                            <a href=" base_url('customize/remove/').$color->id ?>" class="btn btn-info">Reset</a>
                         endif; 
                    </div> --}}

                <div class="form-group mb-3 text-right">
                    <input type="hidden" value="{{ @$edit->hashid }}" name="customize_id">
                    <button class="btn btn-primary waves-effect waves-light" type="submit">
                        Update
                    </button>
                    <a href="javascript:void(0)" class="btn btn-warning nopopup" onclick="ajaxRequest(this)" data-url="{{ route('admin.customizes.reset',['id'=>@$edit->hashid]) }}">Reset</a>
                </div>

            </form>
        </div>
    </div>
</div>

@endsection
@section('page-scripts')
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jscolor/2.4.8/jscolor.min.js"></script> --}}
{{-- <script src="{{admin_assets()}}/js/jscolor.js"></script> --}}
<script src="{{ get_asset('admin_assets') }}/js/jscolor.js"></script>
<script>
    $(function() {
        $('.jscolor').on('change' , function () {
           var target = $(this).attr('name');
           var color = "#"+$(this).val();
           switch (target) {
               case 'top_bar':
                   $(document).find('.navbar-custom').css({"background-color":`${color}`});
                break;
                
                case 'primary':
                $('.btn-primary ,  .label-primary , .badge-primary , .alert-primary ,  .primary').css({
                    "background-color":`${color}` , "border-color":`${color}`
                })  
                $('.text-primary').css({
                    "color":`${color}`
                })
                $('.bg-primary , .list-group-item-primary , .panel-primary').css({
                    "background-color":`${color}`
                })    
                break;

               
                case 'success':
                $('.btn-success ,  .label-success , .badge-success , .alert-success ,  .success').css({
                    "background-color":`${color}` , "border-color":`${color}`
                })  
                $('.text-success').css({
                    "color":`${color}`
                })
                $('.bg-success , .list-group-item-success , .panel-success').css({
                    "background-color":`${color}`
                })    
                break;

               
                case 'info':
                $('.btn-info ,  .label-info , .badge-info , .alert-info ,  .info').css({
                    "background-color":`${color}` , "border-color":`${color}`
                })  
                $('.text-info').css({
                    "color":`${color}`
                })
                $('.bg-info , .list-group-item-info , .panel-info').css({
                    "background-color":`${color}`
                })    
                break;

                case 'warning':
                $('.btn-warning ,  .label-warning , .badge-warning , .alert-warning ,  .warning').css({
                    "background-color":`${color}` , "border-color":`${color}`
                })  
                $('.text-warning').css({
                    "color":`${color}`
                })
                $('.bg-warning , .list-group-item-warning , .panel-warning').css({
                    "background-color":`${color}`
                })    
                break;

                case 'danger':
                $('.btn-danger ,  .label-danger , .badge-danger , .alert-danger ,  .danger').css({
                    "background-color":`${color}` , "border-color":`${color}`
                })  
                $('.text-danger').css({
                    "color":`${color}`
                })
                $('.bg-danger , .list-group-item-danger , .panel-danger').css({
                    "background-color":`${color}`
                })    
                break;

                case 'fonts':
                    $('*').css({ "color":`${color}`}) 
                break;
                case 'links':

                    $('a').css({ "color":`${color}`}) 
                break;
                case 'side_bar':                    
                    $('.left-side-menu').css({
                    "background-color":`${color}`
                })    
                break;
           
               default:
                   break;
           }
        })
    })
</script>
@endsection
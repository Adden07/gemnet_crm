@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="">Areas</a></li>
                    {{-- <li class="breadcrumb-item active">{{ isset($is_update) ? 'Edit' : 'Add'}} Area</li> --}}
                </ol>
            </div>
            <h4 class="page-title">{{ isset($is_update) ? 'Edit' : 'Add'}} Area</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            <h4 class="header-title m-t-0">{{ isset($is_update) ? 'Edit' : 'Add'}} Area</h4>
            <p class="text-muted font-14 m-b-20">
                Here you can {{ isset($is_update) ? 'edit' : 'add'}} Area.
            </p>

            <form action="{{ route('admin.areas.store') }}" class="ajaxForm" method="post" enctype="multipart/form-data" novalidate>
                @csrf

                <div class="row">
                    <div class="form-group mb-3 col-md-4">
                        <label for="city_id">City<span class="text-danger">*</span></label>
                        <select class="form-control" parsley-trigger="change" data-parsley-required name="city_id" id="city_id">
                            <option value="">Select City</option>
                            @foreach($cities AS $city)
                                <option value="{{ $city->hashid }}" @if(@$edit_area->city_id == $city->id) selected @endif>{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3 col-md-4">
                        <label for="city_id">Area<span class="text-danger">*</span></label>
                        <select class="form-control" parsley-trigger="change" data-parsley-required name="area_id" id="area_id">

                            @if(isset($is_update))
                                @if(@$edit_area->area_id == 0)
                                    <option value="{{ hashids_encode(0) }}">Main Area</option>
                                @else
                                    @foreach($edit_areas AS $area)
                                        <option value="{{ $area->hashid }}">{{ $area->area_name }}</option>
                                    @endforeach
                                @endif
                            @else
                                <option value="">Select Area</option>
                            @endif
                        </select>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="area_name">Area Name</label>
                        <input type="text" class="form-control" parsley-trigger="change" data-parsley-required placeholder="Enter Area Name" value="{{ @$edit_area->area_name }}" name="area_name" id="area_name">
                    </div>
                </div>    

                

                <div class="form-group mb-3 text-right">
                    <input type="hidden" value="{{ @$edit_area->hashid }}" name="area_edit_id">
                    <button class="btn btn-primary waves-effect waves-light" type="submit">
                        {{ (isset($is_update)) ? 'Update' : 'Add' }}
                    </button>
                </div>

            </form>
        </div>
    </div>
    
    <div class="col-lg-12">
        <h4 class="page-title"> Area List </h4>

        <div class="card-box">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="header-title">Areas</h4>
                {{-- <a href="{{ route('admin.staffs.add') }}" class="btn btn-sm btn-primary">Add Staff</a> --}}
            </div>
            <p class="sub-header">Following is the list of all the areas.</p>
            <table class="table dt_table table-bordered w-100 nowrap" id="laravel_datatable">
                <thead>
                    <tr>
                        <th width="20">S.No</th>
                        <th>City</th>
                        <th>Area</th>c
                        <th>Subarea</th>
                        <th>Type</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($areas AS $area)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $area->city->name}}</td>
                            <td>{{ ($area->area_id == 0) ? $area->area_name : $area->sub_area->area_name  }}</td>
                            <td>{{ ($area->area_id != 0) ? $area->area_name  : '' }}</td>
                            <td>
                                @if($area->area_id == 0)
                                    <span class="badge badge-success">Main Area</span>
                                @else 
                                    <span class="badge badge-info">Sub Area</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.areas.edit',['id'=>$area->hashid]) }}" class="btn btn-warning btn-xs waves-effect waves-light">
                                    <span class="btn-label"><i class="fa fa-edit"></i></span>Edit
                                </a>
                                <button type="button" onclick="ajaxRequest(this)" data-url="" class="btn btn-danger btn-xs waves-effect waves-light">
                                    <span class="btn-label"><i class="icon-trash"></i></span>Delete
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    {{-- @php @$counter =  0 @endphp
                    @foreach($areas AS $area)
                        <tr>
                            <td>{{ ++$counter }}</td>
                            <td>{{ $area->city->name}}</td>
                            <td>{{ $area->area_name }}</td>
                            <td></td>
                            <td><span class="badge badge-success">Main Area</span></td>
                            <td>
                                <a href="{{ route('admin.areas.edit',['id'=>$area->hashid]) }}" class="btn btn-warning btn-xs waves-effect waves-light">
                                    <span class="btn-label"><i class="fa fa-edit"></i></span>Edit
                                </a>
                            </td>
                        </tr>
                        @foreach($area->sub_areas AS $sub_area)
                            <tr>
                                <td>{{ ++$counter }}</td>
                                <td>{{ $area->city->name }}</td>
                                <td>{{ $area->area_name }}</td>
                                <td>{{ @$sub_area->area_name }}</td>
                                <td><span class="badge badge-info">Sub Area</span></td>
                                <td>
                                    <a href="{{ route('admin.areas.edit',['id'=>$sub_area->hashid]) }}" class="btn btn-warning btn-xs waves-effect waves-light">
                                        <span class="btn-label"><i class="fa fa-edit"></i></span>Edit
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach --}}
                </tbody>
            </table>
            {{-- <span class="float-right">{{ $areas->links()  }} --}}
            </span>
        </div>
    </div>
</div>

@endsection
@section('page-scripts')
{{-- @include('admin.partials.datatable', ['load_swtichery' => true]) --}}

<script>
    //get area list of specified city
    $('#city_id').change(function(){
        var city_id = $(this).val();
        var route = "{{ route('admin.areas.area_list',':city_id') }}";
        route = route.replace(':city_id',city_id);
        
        getAjaxRequests(route,'','GET',function(resp){
            $('#area_id').html(resp.html);
        });
    });
</script>
@endsection
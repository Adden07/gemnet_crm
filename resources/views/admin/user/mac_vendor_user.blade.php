@extends('layouts.admin')

@section('content')
<style>
    #route {
  width: 350px;
}

.select2.select2-container{width: 100% !important}

</style>
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Users</li>
                    <li class="breadcrumb-item active">Mac Vendor User </li>
                </ol>
            </div>
            {{-- <h4 class="page-title">All Mac Vendor Users ({{ $users->total() }})</h4> --}}
        </div>
    </div>
</div>


<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link active" id="personal_info_tab" data-toggle="tab" href="#personal_info" role="tab" aria-controls="personal_info" aria-selected="true" >Make Vendor</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="packages_tab" data-toggle="tab" href="#packages" role="tab" aria-controls="personal_info" aria-selected="true" >Make Vendor Users</a>
    </li>
</ul>


<div class="tab-content pt-0" id="myTabContent">
    <div class="tab-pane fade show active" id="personal_info" role="tabpanel">
        <div class="row">
            <div class="col-lg-12">
                <div class="card-box">
                    <table class="table dt_table table-bordered w-100 nowrapp" id="laravel_datatable">
                        <thead>
                            <tr>
                                <th width="20">S.No</th>
                                <th>vendor</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($macvendors AS $vendor)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $vendor->macvendor }}</td>
                                    <td>{{ $vendor->total }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade show" id="packages" role="tabpanel">
        <div class="row">
            <div class="col-lg-12">
                <div class="card-box">

                    <form action="#" class="mb-3">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="">Vendor</label>
                                <select class="form-control" name="vendor" id="vendor">
                                    <option></option>
                                    @foreach($macvendors AS $vendor)
                                        <option value="{{ $vendor->macvendor }}">{{ $vendor->macvendor }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="">Status</label>
                                <select class="form-control" name="user_status" id="user_status">
                                    <option value="all">All</option>
                                    <option value="active">Active</option>
                                    <option value="expired">Expired</option>
                                </select>
                            </div>
                        </div>
                    </form>

                    <table class="table table-bordered w-100" id="users_table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Saleperson</th>
                                <th>MAC</th>
                                <th>Mac Vendor</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


</div>


@endsection

@section('page-scripts')
@include('admin.partials.datatable', ['load_swtichery' => true])
<script>

    $('#vendor').select2({
        placeholder:'Filter By Vendor',
    });

    $(document).ready(function(){
        var table = $('#users_table').DataTable({
                    processing: true, 
                    serverSide: true,
                    "order": [[ 0, "desc" ]],
                    "pageLength": 300,
                    "lengthMenu": [300,500,1000,1500],
                    "dom": '<"top"ifl<"clear">>rt<"bottom"ip<"clear">>',

                    ajax:{
                        url : "{{ route('admin.users.mac_vendor_user') }}",
                        data:function(d){
                                d.search = $('#users_table_search').val(),
                                d.vendor = $('#vendor').val(),
                                d.status = $('#user_status').val()
                        },
                    },
                    // columnDefs: [
                    //     { "width": "10px", "targets": 0 },
                    //     { "width": "40px", "targets": 1 },
                    //     { "width": "100px", "targets": 2 },
                    //     { "width": "70px", "targets": 3 },
                    //     { "width": "70px", "targets": 4 },
                    //     { "width": "70px", "targets": 5 },
                    //     { "width": "70px", "targets": 6 }
                    //     ],
                    columns : [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable:true,searchable:false},
                        {data:'name', name:'name', searchable:true, orderable:true},  
                        {data:'username', name:'username', searchable:true, orderable:true},
                        {data:'sales_person', name:'admin.username', searchable:true, orderable:true},
                        {data:'macaddress',name:'macaddress',searchable:true, orderable:false},
                        {data:'mac_vendor', name:'macvendor',searchable:true, orderable:false},
                        {data:'status', name:'users.status',orderable:true,searchable:false}
                    ],
                    // columnDefs: [
                    //     { width: 200, targets: 0 }
                    // ],

                    // initComplete : function() {
                    //     $("#users_table input").prop( 'id', 'search_box' );
                    //     // $("#example_filter").detach().appendTo('#new-search-area');
                    // },
                });

       $('#vendor, #user_status').change(function(){
            table.draw();
       });
       //set id on yajra datatable (because we are using two tables on a same page its cauing an issue on search)
       $('#users_table_filter label input[type="search"]').attr('id','users_table_search');
    });
</script>
@endsection
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
            {{-- <h4 class="page-title">All Users | Active Users ({{ $users_count->where('status','active')->count() }}) | Expired Users ({{ $users_count->where('status','expired')->count() }}) | Total Users ({{ $users_count->count() }})</h4> --}}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card-box">

            <table class="table table-bordered " id="users_table">
                <thead>
                    <tr>
                        <th width="20">S.No</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Mobile</th>
                        <th>NIC</th>
                        <th>Address</th>
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
                        url : "{{ route('admin.users.search') }}",
                        data:function(d){
                                    d.search = $('input[type="search"]').val()
                        },
                    },
                    
                    columns : [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable:true,searchable:false},
                        {data:'name', name:'name', orderable:true},  
                        {data:'username', name:'username', searchable:true},
                        {data:'mobile', name:'mobile', searchable:true},
                        {data:'nic', name:'nic', searchable:true},
                        {data:'address', name:'address', searchable:true}

                    ],
                });


    //    $('#user_status,#from_date,#to_date,#package_id,#franchise_id,#dealer_id,#subdealer_id,#expiration_date').change(function(){
    //         if($(this).attr('id') == 'expiration_date'){
    //             $('#from_date').val('');
    //             $('#to_date').val('');
    //         }
    //         table.draw();
    //         getPackageCount();
    //    });
     });


</script>
@endsection
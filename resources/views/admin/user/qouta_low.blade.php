@extends('layouts.admin')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Users</li>
                    <li class="breadcrumb-item active">Qouta Low </li>

                </ol>
            </div>
            <h4 class="page-title">Qouta Low</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card-box" style="padding: 10px 24px">
            <form action="" method="GET" id="login_details_form">
                @csrf
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="">Users</label>
                        <select class="form-control select2"name="user_id" id="user_id">
                            <option value="">Select User</option>
                            @foreach($users As $user)
                                <option value="{{ $user->hashid }}">{{ $user->name }}--({{ $user->username }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="">From Date</label>
                        <input type="date" class="form-control" name="from_date" id="from_date" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="">To Date</label>
                        <input type="date" class="form-control" name="to_date" id="to_date">
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="card-box">
            {{-- <p class="sub-header">Following is the list of all the Users.</p> --}}
            <table class="table table-bordered " id="users_table">
                <thead>
                    <tr>
                        <th width="20">S.No</th>
                        {{-- <th>DateTime</th> --}}
                        <th>User</th>
                        <th>Package</th>
                        <th>Total Qouta</th>
                        <th>Qouta Used</th>
                        <th>Expiraiton</th>

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
        var table =  $('#users_table').DataTable({
            processing:true,
            serverSide:true,
            "order":[[0,"desc"]],
            "pageLength": 300,
            "lengthMenu": [300,500,1000,1500],
            "dom": '<"top"ifl<"clear">>rt<"bottom"ip<"clear">>',

            ajax:{
                url : "{{ route('admin.users.qouta_low') }}",
                data:function(d){
                    d.user_id    = $('#user_id').val(),
                    d.from_date  = $('#from_date').val(),
                    d.to_date    = $('#to_date').val()
                }
            },
            columns : [
                {data: 'DT_RowIndex', name:'DT_RowIndex'},
                // {data:'date', name:'date'},
                {data:'name', name:'name'},
                {data:'package', name:'package'},
                {data:'qt_total', name:'qt_total'},
                {data:'qt_used', name:'qt_used'},
                {data:'expiration', name:'expiration'},

            ]
        });
        $('#user_id, #from_date, #to_date').change(function(){
            table.draw();
        });
    });

</script>
@endsection
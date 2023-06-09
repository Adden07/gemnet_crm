@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">finance</li>
                    <li class="breadcrumb-item active">Invoices</li>
                </ol>
            </div>
            <h4 class="page-title">Invoices</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            <h3 class="header-title text-center">Summary</h3>
            <table class="table table-bordered w-100 nowrap">
                <thead>
                    <th>Type</th>
                    <th>Total</th>
                    <th>Total Amount</th>
                </thead>
                <tbody>
                    <tr>
                        <td>Activations</td>
                        <td>{{ $invoices_total->where('type',0)->count() }}</td>
                        <td>Rs.{{ round($invoices_total->where('type',0)->sum('total')) }}</td>
                    </tr>
                    <tr>
                        <td>Renews</td>
                        <td>{{ $invoices_total->where('type',1)->count() }}</td>
                        <td>Rs.{{ round($invoices_total->where('type',1)->sum('total')) }}</td>
                    </tr>
                    <tr>
                        <td>Upgrades</td>
                        <td>{{ $invoices_total->where('type',2)->count() }}</td>
                        <td>Rs.{{ round($invoices_total->where('type',2)->sum('total')) }}</td>
                    </tr>

                    <tr>
                        <td>Total</td>
                        <td>{{ $invoices_total->count() }}</td>
                        <td>Rs.{{ round($invoices_total->sum('total_cost')) }}</td>
                    </tr>
                </tbody>
            </table>   
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            <h3 class="header-title text-center">Packages Summary</h3>
            <table class="table table-bordered w-100 nowrap">
                <thead>
                    <th>#</th>
                    <th>Package Name</th>
                    <th>Activation</th>
                    <th>Renew</th>
                    <th>Upgrade</th>
                    <th>Total Amount</th>
                </thead>
                <tbody>
                    @foreach($invoices_total->groupBy('pkg_id') AS $data)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ @$data[0]->package->name }}</td>
                            <td>{{ $data->where('type', 0)->count() }}</td>
                            <td>{{ $data->where('type', 1)->count() }}</td>
                            <td>{{ $data->where('type', 3)->count() }}</td>
                            <td>{{ number_format($data->sum('total')) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td>Total</td>
                        <td></td>
                        <td>{{ $invoices_total->where('type', 0)->count() }}</td>
                        <td>{{ $invoices_total->where('type', 1)->count() }}</td>
                        <td>{{ $invoices_total->where('type', 3)->count() }}</td>
                        <td>{{ number_format($invoices_total->sum('total')) }}</td>
                    </tr>
                </tfoot>
            </table>   
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            <h4 class="header-title">Search By Filters</h4>
            <form action="{{ route('admin.accounts.invoices.index') }}" method="GET" novalidate>
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="">Packages</label>
                        <select class="form-control" name="package_id" id="package_id">
                            <option value="">Select Package</option>
                            @foreach($packages AS $package)
                                <option value="{{ $package->hashid }}" {{ (request()->has('package_id') && request()->get('package_id') == $package->hashid) ? 'selected' : ''  }}>{{ $package->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="">From Date</label>
                        <input type="date" class="form-control" value="{{ request()->has('from_date') ? date('Y-m-d',strtotime(request()->get('from_date'))) : date('Y-m-d') }}" name="from_date" id="from_date">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="">To Date</label>
                        <input type="date" class="form-control" value="{{ (request()->has('to_date')) ? date('Y-m-d',strtotime(request()->get('to_date'))) : date('Y-m-d') }}" name="to_date" id="to_date">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="">Status</label>
                        <select class="form-control" name="type" id="type">
                            <option value="all">All</option>
                            <option value="0" {{ (request()->has('type') && request()->get('type') == 0) ? 'selected' : '' }}>Activation</option>
                            <option value="1" {{ (request()->has('type') && request()->get('type') == 1) ? 'selected' : '' }}>Renew</option>
                            <option value="2" {{ (request()->has('type') && request()->get('type') == 2) ? 'selected' : '' }}>OTC</option>
                            <option value="3" {{ (request()->has('type') && request()->get('type') == 3) ? 'selected' : '' }}>pgrade</option>


                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <input type="submit" class="btn btn-primary float-right" value="search">

                    </div>
                </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="header-title">All Invoices List</h4>
            </div>
            <p class="sub-header">Following is the list of all the Invoices.</p>
            <table class="table dt_table table-bordered w-100 nowrap border-0" id="laravel_datatable">
                <thead>
                    <tr>
                        <th width="20">S.No</th>
                        <th>Invoice No</th>
                        <th>Datetime</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Package Name</th>
                        <th>Current Exp </th>
                        <th>New Exp</th>
                        <th>Package Price</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $admin_ids = array(); 
                        $increment = 0;
                        $page_counter = 1000 * ($invoices->currentPage() - 1)
                    @endphp
                    @foreach($invoices AS $invoice)
                        
                        @if(!in_array(hashids_encode($invoice->admin_id), $admin_ids))<!--if admin id does not exists then print the name on top-->
                            @php  
                                $admin_ids[] = hashids_encode($invoice->admin_id); $increment = 0; 
                                $last_id     = $invoices->where('admin_id',$invoice->admin_id)->last(); //get the last id so we can sum total cost
                                $last_id     = hashids_encode($last_id->id);
                            @endphp                        
                            <tr>
                                <td colspan="8" class="text-center"><h4 style="color:rgb(15, 8, 8)">{{ @$invoice->admin->username }}</h4></td>
                            </tr>
                        @endif
                        
                        <tr @if($invoice->type == 0) style="background-color:#DFDDD9" @elseif($invoice->type == 2) style="background-color:#82F1DB" @endif>
                            <td>{{ ++$page_counter }}</td>
                            <td><a href="{{ route('admin.accounts.invoices.get_invoice', ['id'=>$invoice->hashid]) }}" target="_blank">{{ $invoice->invoice_id }}</a></td>
                            <td>{{ date('d-M-y H:i:s',strtotime($invoice->created_at)) }}</td>
                            <td>{{ @$invoice->user->name }}</td>
                            <td><a href="{{ route('admin.users.profile',['id'=>hashids_encode($invoice->user_id)]) }}" target="_blank">{{ @$invoice->user->username }}</a></td>
                            <td>{{ @$invoice->package->name }}</td>
                            <td>{{ ($invoice->current_exp_date != NULL) ? date('d-M-Y H:i:s',strtotime($invoice->current_exp_date)) : '' }}</td>
                            <td>{{ (!is_null(@$invoice->new_exp_date)) ? date('d-M-y H:i:s',strtotime($invoice->new_exp_date)) : '' }}</td>
                            <td>Rs.{{ round($invoice->total) }}</td>
                        </tr>

                        @if($invoice->id == hashids_decode($last_id))
                            <tr>    
                                <td colspan="6"></td>
                                <td><b>Total</b></td>
                                <td><b>Rs.{{ $invoices->where('admin_id',$invoice->admin_id)->sum('total') }}</b></td>
                            </tr>
                        @endif
                    
                    @endforeach
                </tbody>
            </table>
            <div class="row col-md-12">
                <div class="float-right">
                    {{ $invoices->links() }}
                </div>
            </div>

           {{-- <span class="float-right">{{ $dealers->links() }}</span> --}}
        </div>
    </div>
</div>
<div class="modal fade bd-example-modal-lg" id="details_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
</div>
@endsection

@section('page-scripts')
@include('admin.partials.datatable', ['load_swtichery' => true])
<script>
    //get dealers of selected franchise
    $('#franchise_id').change(function(){
        var id = $(this).val();
        var route = ""
        route = route.replace(':id',id);
        //send ajax request when value is set
        if(id.length != 0){
            getAjaxRequests(route,'','GET',function(resp){
                $('#dealer_id').html("<option value='' selected>Select Dealer</option>"+resp.html);
            });
        }
    });

    //get subdealers of selected dealer
    $('#dealer_id').change(function(){
        var id   = $(this).val();
        var route = "";
        var route = route.replace(':id',id);
        //send ajax request when value is set
        if(id.length != 0){
            getAjaxRequests(route, '', 'GET', function(resp){
                $('#subdealer_id').html("<option value=''>Select Sub Dealer</option>"+resp.html);
            });
        }
    });
</script>
@endsection

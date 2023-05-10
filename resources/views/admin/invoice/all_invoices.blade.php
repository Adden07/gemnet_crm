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
                    {{-- <th>S.No</th> --}}
                    <th>Type</th>
                    <th>Total</th>
                    <th>Total Amount</th>
                </thead>
                <tbody>
                    {{-- @foreach($invoices->groupBy('type') AS $key=>$invoice_type)
                        <tr>
                            <td>
                                @if($key == 0)
                                    Activations
                                @elseif($key == 1)
                                    Renews
                                @elseif($key == 2)
                                    Upgrades
                                @endif
                            </td>
                            <td>{{ $invoice_type->count() }}</td>
                            <td>Rs.{{ round($invoice_type->sum('total_cost')) }}</td>
                        </tr>
                    @endforeach --}}
                    <tr>
                        <td>Activations</td>
                        <td>{{ $invoices_total->where('type',0)->count() }}</td>
                        <td>Rs.{{ round($invoices_total->where('type',0)->sum('total_cost')) }}</td>
                    </tr>
                    <tr>
                        <td>Renews</td>
                        <td>{{ $invoices_total->where('type',1)->count() }}</td>
                        <td>Rs.{{ round($invoices_total->where('type',1)->sum('total_cost')) }}</td>
                    </tr>
                    <tr>
                        <td>Upgrades</td>
                        <td>{{ $invoices_total->where('type',2)->count() }}</td>
                        <td>Rs.{{ round($invoices_total->where('type',2)->sum('total_cost')) }}</td>
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
                    @if($user_type == 'admin')
                        <div class="form-group col-md-3">
                            <label for="">Franchises</label>
                            <select class="form-control" name="franchise_id" id="franchise_id">
                                <option value="">Select Franchise</option>
                                @foreach($franchises AS $franchise)
                                    <option value="{{ $franchise->hashid }}">{{  $franchise->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
                <div class="row">
                    @if($user_type == 'admin' || $user_type == 'franchise')
                        <div class="form-group col-md-3">
                            <label for="">Dealers</label>
                            <select class="form-control" name="dealer_id" id="dealer_id">
                                <option value="">Select Dealer</option>
                                @if(isset($childs))
                                    @foreach($childs AS $dealer)
                                        <option value="{{ $dealer->hashid }}">{{ $dealer->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    @endif
                    @if($user_type != 'sub_dealer')
                        <div class="form-group col-md-3">
                            <label for="">Sub Dealers</label>
                            <select class="form-control" name="subdealer_id" id="subdealer_id">
                                <option value="">Select Sub Dealer</option>
                                @if($user_type == 'dealer' && isset($childs))
                                    @foreach($childs AS $dealer)
                                        <option value="{{ $dealer->hashid }}">{{ $dealer->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    @endif
                    <div class="form-group col-md-3">
                        <label for="">Status</label>
                        <select class="form-control" name="type" id="type">
                            <option value="">Select Status</option>
                            <option value="renew" {{ (request()->has('type') && request()->get('type') == 'renew') ? 'selected' : '' }}>Renew</option>
                            <option value="new" {{ (request()->has('type') && request()->get('type') == 'new') ? 'selected' : '' }}>New</option>
                        </select>
                    </div>
                    <div class="col-md-3 mt-4">
                        <input type="submit" class="btn btn-primary float-right" value="search">

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
                        <th>Datetime</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Package Name</th>
                        <th>Current Exp </th>
                        <th>New Exp</th>
                        <th>Amount</th>
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
                            <td>{{ date('d-M-y H:i:s',strtotime($invoice->created_at)) }}</td>
                            <td>{{ @$invoice->user->name }}</td>
                            <td><a href="{{ route('admin.users.profile',['id'=>hashids_encode($invoice->user_id)]) }}" target="_blank">{{ @$invoice->user->username }}</a></td>
                            <td>{{ @$invoice->package->name }}</td>
                            <td>{{ ($invoice->current_exp_date != NULL) ? date('d-M-Y H:i:s',strtotime($invoice->current_exp_date)) : '' }}</td>
                            <td>{{ date('d-M-y H:i:s',strtotime($invoice->new_exp_date)) }}</td>
                            <td>Rs.{{ round($invoice->total_cost) }}</td>
                        </tr>

                        @if($invoice->id == hashids_decode($last_id))
                            <tr>    
                                <td colspan="6"></td>
                                <td><b>Total</b></td>
                                <td><b>Rs.{{ $invoices->where('admin_id',$invoice->admin_id)->sum('total_cost') }}</b></td>
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
        var route = "{{ route('admin.sub_dealers.get_dealer',':id') }}"
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
        var route = "{{ route('admin.accounts.invoices.get_subdealers',':id') }}";
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

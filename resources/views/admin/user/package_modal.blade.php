<div class="form-group mb-0">
    <label for="name" class="col-form-label">Name:</label>
    <span>{{ $user->name }}</span>
</div>
<div class="form-group mb-0">
    <label for="username" class="col-form-label">Username:</label>
    <span>{{ $user->username }}</span>
</div>

<div class="">
    <label class="col-form-label">Status:</label>
    <span class="badge @if($user->status == 'active') badge-success @elseif($user->status == 'registered') badge-info @else badge-danger @endif">
        {{ $user->status }}
    </span>
</div>

@if(!empty($user->current_expiration_date))
    <div class="">
        <label class="col-form-label">Apply As</label>
        <select class="form-control" name="renew_type" id="renew_type">
            <option value="immediate">Immediate</option>
        <option value="queue">Queue</option>
        </select>
    </div>
@endif

@if(!empty($user->current_expiration_date))
    <div id="queue_package" class="d-none">
        <div class="form-group mt-2">
            <label for="">Package type</label>
            <select class="form-control" name="month_type" id="month_type">
                <option value="monthly">Monthly</option>
                <option value="half_year">Half year</option>
                <option value="full_year">Full Year</option>
                <option value="promo">Promo</option>
            </select>
        </div>
        <label for="package">Packages:</label>
        <select class="form-control package_id @if(!empty($user_package_id)) disabled @endif" name="package_id" id="package_id">
            <option value="">Select Package</option>
            @foreach($packages AS $package)
                <option value="{{ $package->hashid }}" @if(@$user_package_id->package_id == $package->id) selected @endif>{{ $package->name }}</option>
            @endforeach
        </select>
    </div>
@endif

@if($user->status != 'registered')
    <div class="">
        <label for="package" class="mt-2">Current Package:</label>
        <span class="badge badge-info">
            {{ @$user->packages->name }}
        </span>
    </div>
@endif
{{-- @php
$current_date = strtotime(date('Y-m-d'));
$current_expiration = strtotime(date('Y-m-d',strtotime($user->current_expiration_date)));
@endphp --}}
{{-- @php
    $expiration_date = strtotime(date('Y-m-d H:i:s',strtotime($user->current_expiration_date)));
    $current_date    = strtotime(date('Y-m-d H:i:s'));
    $exp             = strtotime(date('Y-m-d H:i:s',strtotime('-1 day',strtotime($user->current_expiration_date))));
    
    if($user->status == 'active'){
        if(date('m',$current_date) == date('m',$expiration_date)){
            (date('d',$expiration_date) < 15) ? $half_month = true : $half_month = false;
            (date('d',$expiration_date) < date('t')) ? $full_month = true : $full_month = false;
        }else{
            $half_month = false; 
            $full_month = false;
        }
    }elseif($user->status == 'expired' || $user_status == 'registered'){
        $half_month = true;
        $full_month = true;
    }
@endphp --}}
@if(empty($user->current_expiration_date))
    <div class="form-group mt-2">
        <label for="">Package type</label>
        <select class="form-control" name="month_type" id="month_type">
            <option value="monthly">Monthly</option>
            <option value="half_year">Half year</option>
            <option value="full_year">Full Year</option>
            <option value="promo">Promo</option>

            {{-- @can('monthly')
                <option value="monthly">Monthly</option>
            @endcan --}}
            {{-- @if($half_month == true) --}}
            {{-- @can('half-month')
                <option value="half_month">Half Month</option>
            @endcan     --}}
            {{-- @endif
            @if($full_month == true) --}}
            {{-- @can('full-month')
                <option value="full_month">Full Month</option>
            @endcan     --}}
            {{-- @endif --}}
        </select>
    </div>
@endif
<div class="form-group d-none" id="calendar_form">
    <label for="">Calendar</label>
    <input type="date" class="form-control" name="calendar" id="calendar" min="">
</div>
<div class="form-group">
    @php 
        $expiration_date = strtotime(date('Y-m-d H:i:s',strtotime($user->current_expiration_date)));
        $current_date    = strtotime(date('Y-m-d H:i:s'));
        $exp             = strtotime(date('Y-m-d H:i:s',strtotime('-1 day',strtotime($user->current_expiration_date))));
        
        if(!empty($user->current_expiration_date)){
            $mrc_sales_tax   = ($edit_setting->mrc_sales_tax   != 0)   ? ($user->packages->price * $edit_setting->mrc_sales_tax)/100: 0;
            $mrc_adv_inc_tax = ($edit_setting->mrc_adv_inc_tax != 0) ? (($user->packages->price+$mrc_sales_tax) * $edit_setting->mrc_adv_inc_tax)/100: 0;
            $package_price   = (int) round($user->packages->price+$mrc_sales_tax+$mrc_adv_inc_tax);
        }
    @endphp

    @if($user->status == 'expired' || $user->status == 'registered' || $current_date > $exp) 
        <label for="package">Packages:</label>
        <select class="form-control package_id @if(!empty($user_package_id)) disabled @endif" name="package_id" id="package_id">
            <option value="">Select Package</option>
            @if(isset($user_packages))<!--when user is subdealer-->
                @foreach($user_packages AS $s_package)
                    @if(!in_array($s_package->package_id,$ids))
                        <option value="{{ hashids_encode($s_package->package->id) }}" @if(@$user_package_id->package_id == $s_package->id) selected @endif>{{ $s_package->package->name }}</option>
                    @endif    
                @endforeach
            @else<!--when user is not subdealer-->
                @foreach($packages AS $package)
                    <option value="{{ $package->hashid }}" @if(@$user_package_id->package_id == $package->id) selected @endif>{{ $package->name }}</option>
                @endforeach
            @endif
        </select>
    @else
        <label for="package">Renew Package:</label>
        <span class="badge badge-info ml-3" id="renew_package_name">
            {{ @$user->packages->name }}
        </span>
        {{-- <input type="hidden" name="package_id" value="{{ hashids_encode($user->package) }}"> --}}
    @endif
</div>

@if(empty($user->current_expiration_date))
    <div class="form-group">
        <label for="">One Time Charges</label>
        <select class="form-control" name="otc" id="otc">
            <option value="1">Yes</option>
            <option value="0">No</option>
        </select>
    </div>
@endif

@if(!empty($user->current_expiration_date))
    <div class="">
        <label>Current Expiration:</label>
        <span class="badge  ml-1 @if($user->status == 'active') badge-success @elseif($user->status == 'expired') badge-danger  @endif">
            {{ @Carbon\Carbon::parse(@$user->current_expiration_date)->format('d-M-Y 12:00') }}
        </span>
    </div>
    <div class="">
        <label>New Expiration:</label>
        <span class="badge badge-info" style="margin-left:27px" id="new_expiration">
            @if($user->status == 'expired')
                {{ @Carbon\Carbon::parse(date('Y-m-d 12:00'))->addMonth()->format('d-M-Y 12:00') }}
            @elseif($user->status == 'active')
                {{ @Carbon\Carbon::parse(@$user->current_expiration_date)->addMonth()->format('d-M-Y 12:00') }}
            @endif
        </span>
    </div>
    <input type="hidden" name="expiration_date"  value="{{ @$user->current_expiration_date }}">
    @else
    <div class="">
        <label>Expiration Date:</label>
        <span class="badge badge-info ml-1" id="new_expiration">
            {{ Carbon\Carbon::now()->addMonth()->format('d-M-y 12.00') }}
        </span>
    </div>
@endif

{{-- @if(isset($package_price)) --}}
    <div class="@unless(@$package_price) d-none @endunless" id="package_price_tab">
        <label>package Price</label>
        <span class="badge badge-success ml-1" id="package_price">
            {{ @number_format($package_price, 2) }}
        </span>
    </div>
    <div class="@unless(@$otc) d-none @endunless" id="otc_tab">
        <label>OTC Price</label>
        <span class="badge badge-success ml-1" id="otc_price">
        </span>
    </div>
    <div class="d-none" id="total_amount_tab">
        <label>Total Amount</label>
        <span class="badge badge-success ml-1" id="total_amount">
        </span>
    </div>
{{-- @endif --}}

<div class="">
    <label>Current Balance</label>
    <span class="badge  ml-1 @if(isset($package_price) && $package_price > $user->user_current_balance)badge-danger @else badge-success @endif" id="new_expiration">
        {{ number_format($user->user_current_balance, 2) }}
    </span>
</div>

<input type="hidden" class="form-control " id="" name="status" value="{{ $user->status }}">
<input type="hidden" class="form-control" id="username" name="username"  value="{{ $user->username }}">
<input type="hidden" name="user_id" id="user_id" value="{{ $user->hashid }}">


@if($user->status == 'expired')
    <input type="hidden" name="hidden_new_expiry_date" id="hidden_new_expiry_date" value="{{ @Carbon\Carbon::parse(date('Y-m-d 12:00'))->addMonth()->format('d-M-Y 12:00') }}">
@elseif($user->status == 'active')
    <input type="hidden" name="hidden_new_expiry_date" id="hidden_new_expiry_date" value="{{ @Carbon\Carbon::parse(@$user->current_expiration_date)->addMonth()->format('d-M-Y 12:00') }}">
@endif
@if(isset($package_price) && $user->user_current_balance)
    <input type="hidden" name="renew_button_status" value="1" id="renew_button_status">
@endif
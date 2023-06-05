@extends('layouts.admin')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Finance</li>
                    <li class="breadcrumb-item active">Ledger </li>

                </ol>
            </div>
            <h4 class="page-title">User Ledger</h4>
                {{-- @if(request()->has('status'))
                    @if(request()->get('status') == 'active')
                        All Active
                    @elseif(request()->get('status') == 'expired')
                        All Expired
                    @elseif(request()->get('status') == 'all')
                        All Online
                    @endif
                @else
                    All Online
                @endif 
                 Users --}}
                 {{-- All Online Users | Active Users {{ $radaccts->where('framedipaddress','like','172%')->count('framedipaddress') }} --}}
            </h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card-box" style="padding: 10px 24px">
            <form action="{{ route('admin.accounts.ledgers.user_ledgers') }}" method="GET">
                @csrf
                <div class="row">
                    <div class="col-md-5">
                        <label for="">Users</label>
                        <select class="form-control select2" name="user_id" id="user_id">
                            <option value="">Select User</option>
                            @foreach($users AS $user)
                                <option value="{{ $user->hashid }}" @if(request()->get('user_id') == $user->hashid) selected @endif>{{ $user->name }} ({{$user->username }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <input type="submit" class="btn btn-primary mt-3">
                    </div>
                    
                </div>
                
            </form>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="card-box">
            <table class="table table-bordered w-100 nowrapp" id="online_users">
                <thead>
                    <tr>
                        <th width="20">#</th>
                        <th>DateTime</th>
                        <th>Payment</th>
                        <th>Invoice</th>
                        <th>Total</th>
                </thead>
                <tbody>
                    @php
                        $invoice = 0;
                        $payment = 0;
                        $total   = 0;
                    @endphp
                    @isset($is_ledger)
                        @foreach($payments->concat($invoices)->sortBy('created_at') AS $key=>$data)
                            <tr>
                                <td>
                                    {{ $loop->iteration }}
                                </td>
                                <td>{{ date('d-M-Y H:i:s', strtotime($data->created_at)) }}</td>
                                <td>{{ @$data->amount }}</td>
                                <td>{{ @$data->total }}</td>

                                {{-- @if($key == 0 && isset($data->total))
                                    @php $invoice = -$data->total; $total =  @endphp
                                @elseif($key == 0 && isset($data->payment))
                                    @php $payment = $data->amount @endphp
                                @elseif($key != 0 && isset($data->total))
                                    @php $invoice @endphp 
                                @endif
                                  --}}
                            </tr>
                        @endforeach
                        {{-- <tr>
                            <td colspan=4>Total</td>
                            <td>100000</td>
                        </tr> --}}
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('page-scripts')
@include('admin.partials.datatable', ['load_swtichery' => true])
<script>
    //if time is set then refresh the page
    $(document).ready(function(){
        
    });

</script>
@endsection
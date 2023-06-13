@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('admin.admins.index') }}"></a>Finance</li>
                    <li class="breadcrumb-item active">Taxes Summary</li>

                </ol>
            </div>
            <h4 class="page-title">Taxes Summary</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            <form action="{{ route('admin.accounts.invoices.invoice_taxes') }}" method="GET" class="">
                @csrf
                <div class="row">

                    <div class="col-md-6">
                        <label for="">Month</label>
                        <select class="form-control" name="date" id="date" required>
                            <option value="">Select month</option>
                            @foreach($months->sortDesc() AS $month)
                                <option value="{{ $month->created_at }}">{{ date('F-y', strtotime($month->created_at)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mt-3">
                        <input type="submit" class="btn btn-primary" value="search">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@isset($invoices)
<div class="row mt-3">
    <div class="col-md-6">
        <div class="text-center bg-primary">
            <h3 class="m-0 p-2 text-white">SRB Sales Tax Summary</h3>
        </div>
        <table class="table table-bordered">
            <tr>
                <th>Total Invoices</th>
                <td>{{ number_format($invoices->where('sales_tax', '>', 0)->count()) }}</td>
            </tr>
            <tr>
                <th>Total Value Before Taxes</th>
                <td>{{ number_format($invoices->where('sales_tax', '>', 0)->sum('pkg_price')) }}</td>
            </tr>
            <tr>
                <th>Total  Sales Tax (SRB)</th>
                <td>{{ number_format($invoices->where('sales_tax', '>', 0)->sum('sales_tax')) }}</td>
            </tr>
            <tr>
                <th>Total Value After Taxes</th>
                <td>{{ number_format($invoices->where('sales_tax', '>', 0)->sum('sales_tax')+$invoices->where('sales_tax', '>', 0)->sum('pkg_price')) }}</td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <div class="text-center bg-primary">
            <h3 class="m-0 p-2 text-white">FBR Adv: Income Tax Summary</h3>
        </div>
        <table class="table table-bordered">
            <tr>
                <th>Total Invoices</th>
                <td>{{ number_format($invoices->count()) }}</td>
            </tr>
            <tr>
                <th>Total Value Before Taxes</th>
                <td>{{ number_format($invoices->sum('pkg_price')+$invoices->sum('sales_tax')) }}</td>
            </tr>
            <tr>
                <th>Total Adv: Income Tax (FBR)</th>
                <td>{{ number_format($invoices->sum('adv_inc_tax')) }}</td>
            </tr>
            <tr>
                <th>Total Value After Taxe</th>
                <td>{{ number_format($invoices->sum('pkg_price')+$invoices->sum('sales_tax')+$invoices->sum('adv_inc_tax')) }}</td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <div class="text-center bg-primary">
            <h3 class="m-0 p-2 text-white">Overall Summary</h3>
        </div>
        <table class="table table-bordered">
            <tr>
                <th>Total Invoices</th>
                <td>{{ number_format($invoices->count()) }}</td>
            </tr>
            <tr>
                <th>Total Revenue (Without Tax)</th>
                <td>{{ number_format($invoices->sum('pkg_price')) }}</td>
            </tr>
            <tr>
                <th>Total Taxes</th>
                <td>{{ number_format($invoices->sum('sales_tax')+$invoices->sum('adv_inc_tax')) }}</td>
            </tr>
            <tr>
                <th>Total Revenue (With Taxes)</th>
                <td>{{ number_format($invoices->sum('pkg_price')+$invoices->sum('sales_tax')+$invoices->sum('adv_inc_tax')) }}</td>
            </tr>
        </table>
    </div>
</div>
@endisset
@endsection

@section('page-scripts')
@include('admin.partials.datatable', ['load_swtichery' => true])

@endsection
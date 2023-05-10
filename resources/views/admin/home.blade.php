@extends('layouts.admin')

@section('content')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right d-none">
                <form class="form-inline">
                    <div class="form-group">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control border-white" id="dash-daterange">
                            <div class="input-group-append">
                                <span class="input-group-text bg-blue border-blue text-white">
                                    <i class="mdi mdi-calendar-range font-13"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <a href="javascript: void(0);" class="btn btn-blue btn-sm ml-2">
                        <i class="mdi mdi-autorenew"></i>
                    </a>
                    <a href="javascript: void(0);" class="btn btn-blue btn-sm ml-1">
                        <i class="mdi mdi-filter-variant"></i>
                    </a>
                </form>
            </div>
            <h4 class="page-title">Dashboard</h4>
        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    @can('users-read')
        <div class="col-md-6 col-xl-3">
            <div class="widget-rounded-circle card-box">
                <div class="row">
                    <div class="col-6">
                        <div class="avatar-lg rounded-circle bg-soft-primary border-primary border">
                            <i class="fe-users font-22 avatar-title text-primary"></i>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-right">
                            <h3 class="text-dark mt-1"><span data-plugin="counterup">{{number_format($total_shippers ?? 0)}}</span></h3>
                            <p class="text-muted mb-0">Total<br/>Shippers</p>
                        </div>
                    </div>
                </div> <!-- end row-->
            </div> <!-- end widget-rounded-circle-->
        </div> <!-- end col-->

        <div class="col-md-6 col-xl-3">
            <div class="widget-rounded-circle card-box">
                <div class="row">
                    <div class="col-6">
                        <div class="avatar-lg rounded-circle bg-soft-primary border-primary border">
                            <i class="fe-users font-22 avatar-title text-primary"></i>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-right">
                            <h3 class="text-dark mt-1"><span data-plugin="counterup">{{number_format($total_providers ?? 0)}}</span></h3>
                            <p class="text-muted mb-0">Total<br/>Providers</p>
                        </div>
                    </div>
                </div> <!-- end row-->
            </div> <!-- end widget-rounded-circle-->
        </div> <!-- end col-->

        <div class="col-md-6 col-xl-3">
            <div class="widget-rounded-circle card-box">
                <div class="row">
                    <div class="col-6">
                        <div class="avatar-lg rounded-circle bg-soft-primary border-primary border">
                            <i class="fe-users font-22 avatar-title text-primary"></i>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-right">
                            <h3 class="text-dark mt-1"><span data-plugin="counterup">{{number_format($unapproved_providers ?? 0)}}</span></h3>
                            <p class="text-muted mb-0">Unapproved Providers</p>
                        </div>
                    </div>
                </div> <!-- end row-->
            </div> <!-- end widget-rounded-circle-->
        </div> <!-- end col-->

        <div class="col-md-6 col-xl-3">
            <div class="widget-rounded-circle card-box">
                <div class="row">
                    <div class="col-6">
                        <div class="avatar-lg rounded-circle bg-soft-primary border-primary border">
                            <i class="fe-users font-22 avatar-title text-primary"></i>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-right">
                            <h3 class="text-dark mt-1"><span data-plugin="counterup">{{number_format($ltl_carriers ?? 0)}}</span></h3>
                            <p class="text-muted mb-0">LTL<br/>Carriers</p>
                        </div>
                    </div>
                </div> <!-- end row-->
            </div> <!-- end widget-rounded-circle-->
        </div> <!-- end col-->
    @endcan

    @can('shipment-read')
        <div class="col-md-6 col-xl-3">
            <div class="widget-rounded-circle card-box">
                <div class="row">
                    <div class="col-6">
                        <div class="avatar-lg rounded-circle bg-soft-danger border-danger border">
                            <i class="fe-truck font-22 avatar-title text-danger"></i>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-right">
                            <h3 class="text-dark mt-1"><span data-plugin="counterup">{{number_format($booked_shipments ?? 0)}}</span></h3>
                            <p class="text-muted mb-0">Booked Shipments</p>
                        </div>
                    </div>
                </div> <!-- end row-->
            </div> <!-- end widget-rounded-circle-->
        </div> <!-- end col-->

        <div class="col-md-6 col-xl-3">
            <div class="widget-rounded-circle card-box">
                <div class="row">
                    <div class="col-6">
                        <div class="avatar-lg rounded-circle bg-soft-danger border-danger border">
                            <i class="fe-truck font-22 avatar-title text-danger"></i>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-right">
                            <h3 class="text-dark mt-1"><span data-plugin="counterup">{{number_format($delivered_shipments ?? 0)}}</span></h3>
                            <p class="text-muted mb-0">Delivered Shipments</p>
                        </div>
                    </div>
                </div> <!-- end row-->
            </div> <!-- end widget-rounded-circle-->
        </div> <!-- end col-->

        <div class="col-md-6 col-xl-3">
            <div class="widget-rounded-circle card-box">
                <div class="row">
                    <div class="col-6">
                        <div class="avatar-lg rounded-circle bg-soft-danger border-danger border">
                            <i class="fe-truck font-22 avatar-title text-danger"></i>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-right">
                            <h3 class="text-dark mt-1"><span data-plugin="counterup">{{number_format($cancelled_shipments ?? 0)}}</span></h3>
                            <p class="text-muted mb-0">Cancelled Shipments</p>
                        </div>
                    </div>
                </div> <!-- end row-->
            </div> <!-- end widget-rounded-circle-->
        </div> <!-- end col-->

        <div class="col-md-6 col-xl-3">
            <div class="widget-rounded-circle card-box">
                <div class="row">
                    <div class="col-6">
                        <div class="avatar-lg rounded-circle bg-soft-danger border-danger border">
                            <i class="fe-truck font-22 avatar-title text-danger"></i>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-right">
                            <h3 class="text-dark mt-1"><span data-plugin="counterup">{{number_format($total_shipments ?? 0)}}</span></h3>
                            <p class="text-muted mb-0">Total Shipments</p>
                        </div>
                    </div>
                </div> <!-- end row-->
            </div> <!-- end widget-rounded-circle-->
        </div> <!-- end col-->
    @endcan

    @can('payments-read')
        <div class="col-md-6 col-xl-3">
            <div class="widget-rounded-circle card-box">
                <div class="row">
                    <div class="col-5">
                        <div class="avatar-lg rounded-circle bg-soft-success border-success border">
                            <i class="fe-dollar-sign font-22 avatar-title text-success"></i>
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="text-right">
                            <h4 class="text-dark mt-1">$<span data-plugin="counterup">{{number_format($total_payments ?? 0, 2)}}</span></h4>
                            <p class="text-muted mb-0">Total<br/>Payments</p>
                        </div>
                    </div>
                </div> <!-- end row-->
            </div> <!-- end widget-rounded-circle-->
        </div> <!-- end col-->

        <div class="col-md-6 col-xl-3">
            <div class="widget-rounded-circle card-box">
                <div class="row">
                    <div class="col-5">
                        <div class="avatar-lg rounded-circle bg-soft-success border-success border">
                            <i class="fe-dollar-sign font-22 avatar-title text-success"></i>
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="text-right">
                            <h4 class="text-dark mt-1">$<span data-plugin="counterup">{{number_format($booking_fee ?? 0, 2)}}</span></h4>
                            <p class="text-muted mb-0">Total Booking Fees</p>
                        </div>
                    </div>
                </div> <!-- end row-->
            </div> <!-- end widget-rounded-circle-->
        </div> <!-- end col-->

        <div class="col-md-6 col-xl-3">
            <div class="widget-rounded-circle card-box">
                <div class="row">
                    <div class="col-5">
                        <div class="avatar-lg rounded-circle bg-soft-success border-success border">
                            <i class="fe-dollar-sign font-22 avatar-title text-success"></i>
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="text-right">
                            <h4 class="text-dark mt-1">$<span data-plugin="counterup">{{number_format($freight_charges ?? 0, 2)}}</span></h4>
                            <p class="text-muted mb-0">Total Frieght Charges</p>
                        </div>
                    </div>
                </div> <!-- end row-->
            </div> <!-- end widget-rounded-circle-->
        </div> <!-- end col-->

        <div class="col-md-6 col-xl-3">
            <div class="widget-rounded-circle card-box">
                <div class="row">
                    <div class="col-5">
                        <div class="avatar-lg rounded-circle bg-soft-success border-success border">
                            <i class="fe-dollar-sign font-22 avatar-title text-success"></i>
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="text-right">
                            <h4 class="text-dark mt-1">$<span data-plugin="counterup">{{number_format($insurance_charges ?? 0, 2)}}</span></h4>
                            <p class="text-muted mb-0">Total Insurance Charges</p>
                        </div>
                    </div>
                </div> <!-- end row-->
            </div> <!-- end widget-rounded-circle-->
        </div> <!-- end col-->

        <div class="col-md-6 col-xl-3">
            <div class="widget-rounded-circle card-box">
                <div class="row">
                    <div class="col-5">
                        <div class="avatar-lg rounded-circle bg-soft-success border-success border">
                            <i class="fe-dollar-sign font-22 avatar-title text-success"></i>
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="text-right">
                            <h4 class="text-dark mt-1">$<span data-plugin="counterup">{{number_format($premium_services ?? 0, 2)}}</span></h4>
                            <p class="text-muted mb-0">Total Premium Charges</p>
                        </div>
                    </div>
                </div> <!-- end row-->
            </div> <!-- end widget-rounded-circle-->
        </div> <!-- end col-->
    @endcan

    @can('payouts-read')
        <div class="col-md-6 col-xl-3">
            <div class="widget-rounded-circle card-box">
                <div class="row">
                    <div class="col-5">
                        <div class="avatar-lg rounded-circle bg-soft-success border-success border">
                            <i class="fe-dollar-sign font-22 avatar-title text-success"></i>
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="text-right">
                            <h4 class="text-dark mt-1">$<span data-plugin="counterup">{{number_format($total_payouts ?? 0, 2)}}</span></h4>
                            <p class="text-muted mb-0">Total Payouts to Providers</p>
                        </div>
                    </div>
                </div> <!-- end row-->
            </div> <!-- end widget-rounded-circle-->
        </div> <!-- end col-->
    @endcan
</div>

@endsection
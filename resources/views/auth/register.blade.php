@extends('layouts.frontend')
@section('title', "Join the Shipit4us. As you want to join the account type to register like freight forwarder.")
@section('meta_keywords', "freight forwarder")
@section('meta_description', "We are offering the unique role to join Shipit4us. We have different account types like a shipper, carrier, independent driver, broker, freight forwarder and you have to accept all the terms and conditions.")

@section('content')
<section class="pt-3 mt-3 pb-5">
    <div class="container">
        <div class="row">
            <div class="col-md-10 offset-md-1 col-12">
                <div class="registration register_formmain">
                    <div class="box-header text-left mb-2 pb-2">
                        <h2 class="margin-bottom-0 text-center"><strong>JOIN SHIPIT4US</strong></h2>
                        <h4><strong>Join the community as:</strong></h4>
                    </div>

                    <div>
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="nav-item">
                                <a class="nav-link active" href="#shipper" aria-controls="shipper" role="tab" data-toggle="tab">
                                    <span>Shipper</span>
                                    <img src="{{ get_asset('frontend_assets') }}/images/signup_icons/man-user.png" class="img-resposnive">
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" href="#carrier" aria-controls="carrier" role="tab" data-toggle="tab">
                                    <span>Carrier</span>
                                    <img src="{{ get_asset('frontend_assets') }}/images/signup_icons/logistics-delivery-truck-in-movement.png" class="img-resposnive">
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" href="#independent_drive" aria-controls="independent_drive" role="tab" data-toggle="tab">
                                    <span style="top: 0;">Independent Driver</span>
                                    <img src="{{ get_asset('frontend_assets') }}/images/signup_icons/logistics-delivery-truck-in-movement.png" class="img-resposnive">
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" href="#broker" aria-controls="broker" role="tab" data-toggle="tab">
                                    <span>Broker</span>
                                    <img src="{{ get_asset('frontend_assets') }}/images/signup_icons/bill.png" class="img-resposnive">
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" href="#freight" aria-controls="freight" role="tab" data-toggle="tab">
                                    <span style="top: 0;">Freight Forwarder</span>
                                    <img src="{{ get_asset('frontend_assets') }}/images/signup_icons/bill.png" class="img-resposnive">
                                </a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                        </div>
                    </div>
                    <div class="text-center">
                        <iframe width="100%" height="400" src="https://www.youtube-nocookie.com/embed/kxWazvstFiM" title="YouTube video player" frameborder="0" autoplay allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('page-scripts')
<link rel="stylesheet" href="{{ get_asset('frontend_assets') }}/intTel/intlTelInput.css" />
<script src="{{ get_asset('frontend_assets') }}/intTel/intlTelInput.min.js"></script>
<script>
    function hide_state(contry_field) {
        var state_field = contry_field + "_state_field";
        var state_dropdown = contry_field + "_state_dropdown";
        if ($(contry_field).val() != "United States of America") {
            // $(state_field).show().attr('required', 'required');
            $(state_dropdown).hide().removeAttr('required');
        } else {
            // $(state_field).hide().removeAttr('required');
            $(state_dropdown).show().attr('required', 'required');
        }
    }

    var intl_opts = {
        initialCountry: "{{ config('app.default_country') }}",
        placeholderNumberType: 'MOBILE',
        utilsScript: "{{ get_asset('frontend_assets') }}/intTel/utils.js"
    };

    var shipper_individual_phone = document.querySelector("#shipper_individual_phone"),
        shipper_company_phone = document.querySelector("#shipper_company_phone"),
        carrier_phone = document.querySelector("#carrier_phone"),
        independent_phone = document.querySelector("#independent_phone"),
        broker_phone = document.querySelector("#broker_phone"),
        frieght_phone = document.querySelector("#frieght_phone");


    window.shipper_individual_phone_intl = window.intlTelInput(shipper_individual_phone, intl_opts);
    window.shipper_company_phone_intl = window.intlTelInput(shipper_company_phone, intl_opts);
    window.carrier_phone_intl = window.intlTelInput(carrier_phone, intl_opts);
    window.independent_phone_intl = window.intlTelInput(independent_phone, intl_opts);
    window.broker_phone_intl = window.intlTelInput(broker_phone, intl_opts);
    window.frieght_phone_intl = window.intlTelInput(frieght_phone, intl_opts);

    $(function(){
        var url = document.location.toString();
        if (url.match('#')) {
            $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
        }
    })

</script>
@endsection
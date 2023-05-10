<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @hasSection('title')
        <meta name="title" content="@yield('title') | Shipit4us" />
        <title> @yield('title') | Shipit4us</title>
    @else
        <meta name="title" content="{{ $title ?? 'The Transport Marketplace | Get Delivery Quotes | LTL/FTL & Freight Instant Rate | Shipit4us'}}" />
        <title> {{ $title ?? 'The Transport Marketplace | Get Delivery Quotes | LTL/FTL & Freight Instant Rate'}}  | Shipit4us</title>
    @endif

    @hasSection('meta_description')
        <meta name="description" content="@yield('meta_description')" />
    @else
        <meta name="description" content="{{ $meta_description ?? 'Shipit4us where the money comes into the marketplace LTL/ FTL  custom shipping solution. Join the marketplace, list your shipment, and choose one of our rated service providers. Hassle-free transport marketplace courier services with shipit4us. Save time and money when customer-rated courier companies compete for your work.' }}" />
    @endif

    @hasSection('meta_keywords')
        <meta name="keywords" content="@yield('meta_keywords')" />
    @else
        <meta name="keywords" content="{{ $meta_keywords ?? 'Transport Marketplace, furniture shipping quote, shipping furniture quotes, Freight shipping quotes, freight rates, freight quotes, Freight Service quotes, Heavy Equipment service, Furniture shipping service, Furniture shipping quote, antiques shipping quote, antiques shipping service, pianos shipping quote,car shipping, furniture shipping, furniture shipping, motorcycle shipping, car shipping quotes, rv transport, pallet shipping, auto shipping quotes, car transport, motorcycle transport, shipping furniture companies, furniture shipping services, furniture shipping company, rv transport services near me, car transport quote, vehicle shipping quotes, vehicle shipping quote, car shipping companies, rv transport service, auto transport quotes, rv transport companies, shipping furniture, motorcycle shipping cost, boat shipping, pet transportation services cost, cheap freight shipping, delivery quote, furniture shipping costs, pianos shipping service, fitness equipment shipping, furniture shipping near me, house moving quotes, freight quote' }}" />
    @endif


	<meta name="copyright" content="Copyright by Shipit4us. All Rights Reserved." />
	<link rel="canonical" href="{{url()->current()}}" />

    <meta name="csrf-token" content="{{ csrf_token() }}" />


    <link rel="shortcut icon" href="{{ get_asset('admin_assets') }}/images/favicon.png">

    <!-- =-=-=-=-=-=-= Google Fonts =-=-=-=-=-=-= -->
    <link href="//fonts.googleapis.com/css?family=Source+Sans+Pro:400,400italic,600,600italic,700,700italic,900italic,900,300,300italic" rel="stylesheet" type="text/css">

    <link href="{{ get_asset('frontend_assets') }}/css/bundled.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ get_asset("frontend_assets/cookie-consent/cookie-consent.css") }}">
    <link rel="stylesheet" type="text/css" href="{{ get_asset('frontend_assets') }}/css/dianujCss.css">
    <link rel="stylesheet" type="text/css" href="{{ get_asset('frontend_assets') }}/css/mystyle.css">

    <script src="{{ get_asset('frontend_assets') }}/js/modernizr.js"></script>
    <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "Organization",
          "name": "Shipit4us",
          "alternateName": "Transport Marketplace",
          "url": "https://www.shipit4us.com",
          "logo": "https://cdn.shipit4us.com/frontend_assets/images/web_logo.png",
          "sameAs": [
            "https://www.facebook.com/shipit4us/",
            "https://twitter.com/Shipit4usllc1",
            "https://www.instagram.com/shipit4us_/",
            "https://www.linkedin.com/company/gexton/",
            "https://www.youtube.com/channel/UCJqwaaQCw1gtfipZMJsRjJg",
            "https://www.shipit4us.com"
          ]
        }
    </script>
    
    @if (config('app.enable_analytics_chatting') == true)
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-BPL5NVZRHG"></script>
        <script>
            //google tag
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'G-BPL5NVZRHG');
            gtag('config', 'AW-792089427');

            //gtag_report_conversion function for tracking anchor tags
            function gtag_report_conversion(url) { var callback = function () { if (typeof(url) != 'undefined') { window.location = url; } };
            gtag('event', 'conversion', { 'send_to': 'AW-792089427/DJj0CLmH8JsDENOm2fkC', 'event_callback': callback }); return false; }

            //taboola tag
            window._tfa = window._tfa || [];
            window._tfa.push({notify: 'event', name: 'page_view', id: 1437665});
            !function (t, f, a, x) {
                    if (!document.getElementById(x)) {
                        t.async = 1;t.src = a;t.id=x;f.parentNode.insertBefore(t, f);
                    }
            }(document.createElement('script'),
            document.getElementsByTagName('script')[0], '//cdn.taboola.com/libtrc/unip/1437665/tfa.js', 'tb_tfa_script');
        </script>
    @else
        <script>
            function gtag(){}
            function gtag_report_conversion(){}
        </script>
    @endif
</head>

<body>
    <!-- =-=-=-=-=-=-= PRELOADER =-=-=-=-=-=-= -->
    <div class="preloader">
        <!-- <span class="preloader-gif"></span> -->
        <div class="position-fixed spinner text-center">
            <i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>
            <span class="sr-only">Loading...</span>
        </div>
    </div>

    @if(auth('web')->check() && !auth('web')->user()->is_approved() && auth('web')->user()->hasVerifiedEmail())
    <div class="alert-danger py-2 alert alert-dismissible fade show m-0 rounded-0" role="alert">
        <div class="container position-relative">
            <div class="row">
                <div class="col-12">
                    <p class="m-0">
                        Trust and Safety Team haven't approved your account. Pending review.
                    </p>
                </div>
            </div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="line-height:0 !important">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
    @endif


    @if(auth()->check() && !auth()->user()->hasVerifiedEmail())
    <div class="alert-danger py-2 alert alert-dismissible fade show m-0 rounded-0" role="alert">
        <div class="container position-relative">
            <div class="row">
                <div class="col-12">
                    <p class="m-0">
                        Your email is not verified yet please check your inbox for email or get a fresh one by going to this <a href="{{ url('email/verify') }}" class="alert-link">link</a>.
                    </p>
                </div>
            </div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="line-height:0 !important">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
    @endif

    <!-- =-=-=-=-=-=-= HEADER =-=-=-=-=-=-= -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top py-0">
        {{-- <a class="navbar-brand py-2" href="{{ route('front.home') }}"> --}}
            <img alt="Logo" src="{{ get_asset('frontend_assets') }}/images/web_logo.png" class="img-responsive">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse main_menu" id="navbarText">
            {{-- @include('frontend.partials.main_nav') --}}
        </div>
    </nav>

    @yield('content')

    <div class="modal fade theme-modal" id="payment_discount_popup" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header d-none">
                    <h4 class="modal-title">WELCOME TO SHIPIT4US</h4>
                </div>
                <div class="modal-body p-0">
                    <button type="button" class="close modal_close_btn" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <!-- <a href="javascript:void(0)" target="_blank" class="d-block"> -->
                        <img alt="Promotion" src="{{get_asset('frontend_assets/images/payment_popup.jpg')}}" />
                    <!-- </a> -->
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade theme-modal" id="login_for_get_quote" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header d-none">
                    <h4 class="modal-title">WELCOME TO SHIPIT4US</h4>
                </div>
                <div class="modal-body p-0">
                    <button type="button" class="close modal_close_btn" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <a href="{{route('login')}}" target="_blank" class="d-block">
                        <img alt="Login First" src="{{get_asset('frontend_assets/images/category_popup.jpg')}}" />
                    </a>
                    {{-- <h4 class="text-justify">We would like to remind you that instant quotes are only available after you <a href="{{ route('register') }}" target="_blank">sign up</a>. To avoid unnecessary typing and prolonged skipping through various steps we figured it is only best to let you know upfront. If this hasn't scared you off yet please follow a few simple steps and <a href="{{ route('register') }}" target="_blank">become a member</a>. It's free!</h4> --}}
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade theme-modal" id="under_construction_modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Under Development</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <img src="{{ get_asset('frontend_assets') }}/images/under_maintainance.png" height="75" alt="Under Development" />
                        <h3 class="text-primary mb-2 pb-2 border-bottom">This category will be available soon</h3>
                    </div>
                    <h4 class="text-justify">We are working very hard to give you the best experience with this feature and will launch soon!</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="mpreloader-bg" style="position: fixed;height: 100vh;background: rgba(255,255,255, 0.7);z-index: 999999999;width: 100%;text-align: center; top: 0;display: none;">
        <div class="mpreloader-container" style="display: inline-block;position: absolute;top: 50%;-webkit-transform: translate(-50%,-50%);left: 50%;color:#ff511d">
            <i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>
            <h3 style="position: relative;margin-top: 30px;" id="load_text">Please wait...</h3>
        </div>
    </div>

    <!-- =-=-=-=-=-=-= FOOTER =-=-=-=-=-=-= -->
    <footer class="footer-area border-top">
        <!--Footer Upper-->
        <div class="footer-content pt-2 pb-0 ">
            <div class="row border-bottom align-content-center">
                <div class="col-sm-8 pt-1 px-3 pb-0">
                    <div class="footer-widget">
                        <!--Footer Column-->
                        <div class="footer-widget links-widget">
                            <ul class="list-inline">
                                {{-- <li class="list-inline-item pr-1"><a href="{{route('front.company')}}">Company</a></li>
                                <li class="list-inline-item pr-1"><a href="{{route('front.blog')}}">Blog</a></li>
                                <li class="list-inline-item pr-1"><a href="{{route('front.pages.categories')}}">Categories</a></li> --}}
                                <!-- <li class="list-inline-item"><a href="https://help.shipit4us.com/hc/en-us" target="_blank">Help</a></li> -->
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 px-3">
                    <div class="footer-widget">
                        <!--Footer Column-->
                        <div class="footer-widget links-widget text-right">
                            <div class="social-links-two clearfix">
                                <a href="https://www.facebook.com/Shipit4us-106697844082427" target="_blank" class="facebook img-circle"><span class="fa fa-facebook-f"></span></a>
                                <a href="https://twitter.com/Shipit4usllc1" target="_blank" class="twitter img-circle"><span class="fa fa-twitter"></span></a>
                                <a href="https://www.instagram.com/shipit4us_/" target="_blank" class="instagram img-circle"><span class="fa fa-instagram"></span></a>
                                <a href="https://www.youtube.com/channel/UCJqwaaQCw1gtfipZMJsRjJg" target="_blank" class="linkedin img-circle"><span class="fa fa-youtube"></span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row align-items-center pb-2">
                <div class="col-sm-6 px-3">
                    <div class="footer-widget">
                        <!--Footer Column-->
                        <div class="footer-widget links-widget margin-top-20">
                            <p>Copyright &copy; <?= date('Y') ?>.</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 px-3">
                    <div class="footer-widget">
                        <!--Footer Column-->
                        <div class="footer-widget links-widget margin-top-20">
                            <ul class="list-inline text-right">
                                {{-- <li class="list-inline-item pr-1"><a href="{{route('front.pages.user_agreement')}}">User Agreement</a></li>
                                <li class="list-inline-item pr-1"><a href="{{route('front.pages.privacy_policy')}}">Privacy Policy</a></li>
                                <li class="list-inline-item"><a href="{{route('front.pages.cookie_policy')}}">Cookie Policy</a></li> --}}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </footer>
    <script src="{{ get_asset('frontend_assets') }}/js/bundled.js"></script>
    <script>
        $(document).ready(function() {
            function toggleDropdown(e) {
                const _d = $(e.target).closest('.dropdown'),
                    _m = $('.dropdown-menu', _d);
                setTimeout(function() {
                    const shouldOpen = e.type !== 'click' && _d.is(':hover');
                    _m.toggleClass('show', shouldOpen);
                    _d.toggleClass('show', shouldOpen);
                    $('[data-toggle="dropdown"]', _d).attr('aria-expanded', shouldOpen);
                }, e.type == 'mouseleave' ? 300 : 0);
            }

            $('body')
                .on('mouseenter mouseleave', '.dropdown', toggleDropdown)
                .on('click', '.dropdown-menu a', toggleDropdown);
            $('.counter').counterUp({
                delay: 10,
                time: 2000
            });
            // $(".custom_form").parsley();
        });
    </script>
    <script src="{{ get_asset('frontend_assets') }}/js/custom.js"></script>
    @if (config('app.enable_analytics_chatting') == true)
        <script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key=3d153f71-25e6-4a53-9f60-78400504eb37"> </script>
    @endif
    @yield('page-scripts')
</body>

</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>{{ $title ?? 'Dashboard' }} - Gemnet</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ get_asset('admin_assets') }}/images/favicon.png">

    <!-- Plugins css -->
    <link href="{{ get_asset('admin_assets') }}/css/bundled.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ get_asset('admin_assets') }}/css/dianujAdminCss.css" rel="stylesheet" type="text/css" />
    <link href="{{ get_asset('admin_assets') }}/css/aksFileUpload.min.css" rel="stylesheet" type="text/css" />
    @include('admin.customize.custom_style')
    <style>
        #sidebar-menu>ul>li>a{
            font-size: .9rem;
        }
        label.error{
            color:#f1556c;
            font-weight:400;
            position: relative;
            padding-left: 20px;
            padding-top: 5px; 
        }
        label.error:before{
            content: "\F159";
            font-family: "Material Design Icons";
            position: absolute;
            left: 2px;
            top: 5px;
        }

        .mpass label#password-error {
            order: 3;
            width: 100%;
        }
        .select2-container .select2-selection--multiple .select2-selection__choice{color: #000}
        .disabled{background-color: #e9ecef !important;opacity: 1 !important;}

        table{font-size: 13px; color: #000}
        table tr td,
        table tr th{color: #000;}
        .badge{
            font-size:11px !important;
        } 
        /* table tr td{font-weight: 500}
        table tr th{font-weight: 600}*/
</style>
</head>

<body class="left-side-menu-dark">

    <div id="preloader" class="preloader">
        <div id="status">
            <div class="spinner">Loading...</div>
        </div>
    </div>

    <!-- Begin page -->
    <div id="wrapper">

        <!-- Topbar Start -->
        <div class="navbar-custom">
            <ul class="list-unstyled topnav-menu float-right mb-0">
                <li class="dropdown notification-list">
                    <a class="nav-link dropdown-toggle  waves-effect waves-light" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="fe-bell noti-icon"></i>
                        <span class="badge badge-danger rounded-circle noti-icon-badge">{{ $_all_unread_notification_count ?? 0 }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-lg">

                        <div class="dropdown-item noti-title">
                            <h5 class="m-0">
                                <span class="float-right">
                                    <a href="javascript:void(0)" onclick="ajaxRequest(this)" data-msg="Are you sure you want to mark all notifications as read?" data-url="{{ route('admin.notifications.clear_all') }}" data-btn-text="Yes! Mark all as read" class="text-dark">
                                        <small>Clear All</small>
                                    </a>
                                </span>Notification
                            </h5>
                        </div>
                        @if(isset($_all_unread_notification_count) && $_all_unread_notification_count > 0)
                        <div class="slimscroll noti-scroll">
                            @foreach($_all_unread_notification as $notify)
                            @php
                            $notify_type = @notification_colors($notify->data['notify_type']);
                            $route = \Route::has($notify->data['route']) ? route($notify->data['route'], $notify->data['id'] ?? '') : route('admin.notifications');
                            @endphp
                            <a href="{{ $route }}" class="dropdown-item notify-item">
                                <div class="notify-icon {{ @$notify_type['color'] }}">
                                    <i class="{{ @$notify_type['icon'] }}"></i>
                                </div>
                                <p class="notify-details">{{ $notify->data['notify_title'] }}</p>
                                <p class="text-muted mb-0 user-msg">
                                    <small>{{ $notify->data['msg'] }}</small>
                                </p>
                                <p class="text-muted mb-0 user-msg">
                                    <small>{{ $notify->created_at->diffForHumans() }}</small>
                                </p>
                            </a>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-2 border-top border-bottom">
                            <h4 class="m-0">
                                <i class="fas fa-check fa-1x text-success d-block mb-2"></i>
                                <span class="text-dark font-weight-light">No new notification</span>
                            </h4>
                        </div>
                        @endif

                        <a href="{{ route('admin.notifications') }}" class="dropdown-item text-center text-primary notify-item notify-all">
                            View all
                            <i class="fi-arrow-right"></i>
                        </a>

                    </div>
                </li>

                <li class="dropdown notification-list">
                    <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect waves-light" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        {{-- @if(check_file(auth('admin')->user()->image, 'user')) --}}
                            {{-- <img src="{{ check_file(auth('admin')->user()->image, 'user') }}" alt="user-image" class="rounded-circle"> --}}
                        {{-- @endif --}}
                        {{-- @if(public_path(auth('admin')->user()->image))
                            {{ public_path(auth('admin')->user()->image) }}
                        @endif --}}
                        @if(file_exists(auth()->user()->image))
                            <img src="{{ get_asset(auth()->user()->image)  }}" alt="user-image" class="rounded-circle">
                        @else
                            <img src="{{ get_asset('admin_assets/dummy/dummy_profile_image.png')  }}" alt="user-dummy-image" class="rounded-circle">
                        @endif
                        <span class="pro-user-name ml-1">
                            {{ auth('admin')->user()->full_name }} <i class="mdi mdi-chevron-down"></i>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                        <!-- item-->
                        <div class="dropdown-header noti-title">
                            <h6 class="text-overflow m-0">Welcome {{ auth('admin')->user()->full_name }}!</h6>
                        </div>

                        <!-- item-->
                        <a href="{{route('admin.profiles.index')}}" class="dropdown-item notify-item">
                            <i class="fe-user"></i>
                            <span>My Profile</span>
                        </a>


                        <div class="dropdown-divider"></div>

                        <!-- item-->
                        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                        <a href="{{ route('logout') }}" onclick="logout(event)" class="dropdown-item notify-item">
                            <i class="fe-log-out"></i>
                            <span>Logout</span>
                        </a>

                    </div>
                </li>
            </ul>

            <!-- LOGO -->
            <div class="logo-box">
                <a href="{{ route('admin.home') }}" class="logo text-center">
                    <span class="logo-lg">
                        <img src="{{ get_asset('admin_assets') }}/images/web_logo_light.png" alt="" height="40">
                    </span>
                    <span class="logo-sm">
                        <img src="{{ get_asset('admin_assets') }}/images/web_logo_light_sm.png" alt="" height="40">
                    </span>
                </a>
            </div>

            <ul class="list-unstyled topnav-menu topnav-menu-left m-0">
                <li>
                    <button class="button-menu-mobile waves-effect waves-light">
                        <i class="fe-menu"></i>
                    </button>
                </li>
                
                {{-- @if(auth()->user()->user_type != 'admin' && auth()->user()->user_type != 'superadmin')
                    @if(auth()->user()->credit_limit != 0)
                        <li class="mr-3">
                            <h4 class="text-white mt-3">Credit Limit: {{ number_format(auth()->user()->credit_limit,2) }}</h4>
                        </li>
                    @endif
                    <li class="mr-3">
                        <h4 class="text-white mt-3">Balance: {{ number_format(auth()->user()->balance,2) }}</h4>
                    </li>
                @endif --}}
                
                <li>
                    <h4 class="text-white mt-3">{{ ucwords(auth()->user()->user_type) }}</h4>
                </li>
            </ul>
        </div>
        <!-- end Topbar -->

        <!-- ======= Left Sidebar Start ======= -->
        <div class="left-side-menu">

            <div class="slimscroll-menu">

                <!--- Sidemenu -->
                <div id="sidebar-menu">

                    <ul class="metismenu" id="side-menu">

                        <li class="menu-title">Navigation</li>

                        <li>
                            <a href="{{ route('admin.home') }}">
                                <i class="fe-airplay"></i>
                                <span> Dashboards </span>
                            </a>
                        </li>
                        @can('view-activity-log')
                        <li>
                            <a href="{{ route('admin.activity_logs.index') }}">
                                <i class="fe-airplay"></i>
                                <span> Activity Log </span>
                            </a>
                        </li>
                        @endcan


                        @can('enabled-admin')
                            <li>
                                <a href="{{ route('admin.admins.index') }}">
                                    <i class="fe-airplay"></i>
                                    <span> Admin </span>
                                </a>
                            </li>
                        @endcan

                        
                        @can('enabled-staff')
                            <li>
                                <a href="{{ route('admin.staffs.index') }}">
                                    <i class="fe-airplay"></i>
                                    <span> Staff </span>
                                </a>
                            </li>
                        @endcan

                    @can('enabled-user')
                        <li>
                            <a href="javascript: void(0);">
                                <i class="fe-pocket"></i>
                                <span> Users </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul class="nav-second-level" aria-expanded="false">
                                @can('all-user')
                                    <li>
                                        <a href="{{ route('admin.users.index') }}">All Users</a>
                                    </li>
                                @endcan
                                @can('online-users')
                                    <li>
                                        <a href="{{ route('admin.users.online_user') }}">Online Users</a>
                                    </li>
                                @endcan
                                @can('offline-users')
                                    <li>
                                        <a href="{{ route('admin.users.ofline_user') }}">Offline Users</a>
                                    </li>
                                @endcan
                                @can('login-fail-users')
                                    <li>
                                        <a href="{{ route('admin.users.login_fail_log') }}">Login Fail Logs</a>
                                    </li>
                                @endcan
                                @can('user-login-detail')
                                    <li>
                                        <a href="{{ route('admin.users.login_detail') }}">Login Details</a>
                                    </li>
                                @endcan
                                @can('mac-vendor-users')
                                    <li>
                                        <a href="{{ route('admin.users.mac_vendor_user') }}">Mac Vendor User</a>
                                    </li>
                                @endcan
                                @can('search-user')
                                    <li>
                                        <a href="{{ route('admin.users.search') }}">Search User</a>
                                    </li>
                                @endcan
                                <li>
                                    <a href="{{ route('admin.users.all_user_remarks') }}">Remarks</a>
                                </li>
                                @can('queue-user')
                                    <li>
                                        <a href="{{ route('admin.users.queue_user') }}">Queue User</a>
                                    </li>
                                @endcan
                                @can('quota-user')
                                    <li>
                                        <a href="{{ route('admin.users.qouta_over') }}">Qouta Over</a>
                                    </li>
                                @endcan
                                @can('quota-low')
                                    <li>
                                        <a href="{{ route('admin.users.qouta_low') }}">Qouta Low</a>
                                    </li>
                                @endcan
                                @can('qouta-reset')
                                <li>
                                    <a href="{{ route('admin.users.qouta_reset') }}">Qouta Reset</a>
                                </li>
                                @endcan
                            </ul>
                        </li>
                    @endcan

                    @if(auth()->user()->user_type == 'superadmin')    
                        <li>
                            <a href="{{ route('admin.role_permissions.index') }}">
                                <i class="fe-airplay"></i>
                                <span> User Role Permissions </span>
                            </a>
                        </li>
                    @endif
                    @can('enabled-finance')
                        <li>
                            <a href="javascript: void(0);">
                                <i class="fe-pocket"></i>
                                <span> Finance </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul class="nav-second-level" aria-expanded="false">
                                @can('view-invoice')
                                <li>
                                    <a href="{{ route('admin.accounts.invoices.index') }}">Invoices</a>
                                </li>
                                @endcan
                                @can('view-payments')
                                <li>
                                    <a href="{{ route('admin.accounts.payments.index') }}">Payments</a>
                                </li>
                                @endcan
                                @can('view-approve-payments')
                                    <li>
                                        <a href="{{ route('admin.accounts.payments.approve_payments') }}">Approve Payments</a>
                                    </li>
                                @endcan
                                @can('transaction')
                                    <li>
                                        <a href="{{ route('admin.accounts.transactions.index') }}">Transactions</a>
                                    </li>
                                @endcan
                                @can('taxation')
                                    <li>
                                        <a href="{{ route('admin.accounts.invoices.invoice_tax') }}">Taxation</a>
                                    </li>
                                @endcan
                                @can('ledger')
                                    <li>
                                        <a href="{{ route('admin.accounts.ledgers.index') }}">Ledger</a>
                                    </li>
                                @endcan
                                @can('taxes-summary')
                                    <li>
                                        <a href="{{ route('admin.accounts.invoices.invoice_taxes') }}">Taxes Summary</a>
                                    </li>
                                @endcan
                                @can('view-credit-note')
                                    <li>
                                        <a href="{{ route('admin.accounts.credit_notes.index') }}">Credit Note</a>
                                    </li>
                                @endcan
                                @can('enabled-deposit-slip')
                                    <li>
                                        <a href="{{ route('admin.accounts.deposit_slips.index') }}">Deposit Slip</a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                        @endcan
                        @can('enabled-sms')
                            <li>
                                <a href="javascript: void(0);">
                                    <i class="fe-pocket"></i>
                                    <span> SMS </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul class="nav-second-level" aria-expanded="false">
                                    @can('all-sms')
                                    <li>
                                        <a href="{{ route('admin.sms.index') }}">SMS Types</a>
                                    </li>
                                    @endcan
                                    @can('manual-sms')
                                    <li>
                                        <a href="{{ route('admin.sms.manual_sms') }}">Manul SMS</a>
                                    </li>
                                    @endcan
                                    @can('sms-by-user')
                                    <li>
                                        <a href="{{ route('admin.sms.sms_by_user') }}">SMS By User</a>
                                    </li>
                                    @endcan
                                    @can('sms-logs')
                                    <li>
                                        <a href="{{ route('admin.sms.log_page') }}">SMS Logs</a>
                                    </li>
                                    @endcan
                                </ul>
                            </li>
                        @endcan
                        {{-- <li>
                            <a href="javascript: void(0);">
                                <i class="fe-pocket"></i>
                                <span> Users Qouta </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul class="nav-second-level" aria-expanded="false">
                                <li>
                                    <a href="{{ route('admin.users.qouta_over') }}">Qouta Over</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.users.qouta_low') }}">Qouta Low</a>
                                </li>
                            </ul>
                        </li> --}}
                        {{-- <li>
                            <a href="{{ route('admin.customizes.index') }}">
                                <i class="fe-airplay"></i>
                                <span> Customize </span>
                            </a>
                        </li> --}}

                        {{-- <li>
                            <a href="{{ route('admin.areas.index') }}">
                                <i class="fe-airplay"></i>
                                <span> Areas </span>
                            </a>
                        </li> --}}
                        @can('enabled-settings')
                            <li>
                                <a href="{{ route('admin.settings.index') }}">
                                    <i class="fe-airplay"></i>
                                    <span> Settings </span>
                                </a>
                            </li>
                        @endcan
                        <li>
                            <a href="{{ route('admin.remarks.index') }}">
                                <i class="fe-airplay"></i>
                                <span> Remarks </span>
                            </a>
                        </li>
                        {{-- @can('enabled-settings')
                        <li>
                            <a href="{{ route('admin.settings.index') }}">
                                <i class="fe-airplay"></i>
                                <span> Settings </span>
                            </a>
                        </li>
                        @endcan --}}

                    </ul>

                </div>
                <!-- End Sidebar -->

                <div class="clearfix"></div>

            </div>
            <!-- Sidebar -left -->

        </div>
        <!-- Left Sidebar End -->

        <!-- ========================================== -->
        <!-- Start Page Content here -->
        <!-- ========================================== -->
        <div class="content-page">
            <div class="content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
            <!-- Footer Start -->
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            {{ date('Y') }} &copy; All rights reserved by {{config('app.name')}}.
                        </div>
                    </div>
                </div>
            </footer>
            <!-- end Footer -->
        </div>

        <!-- ========================================== -->
        <!-- End Page content -->
        <!-- ========================================== -->


    </div>
    <!-- END wrapper -->

    <script src="{{ get_asset('admin_assets') }}/js/bundled.min.js"></script>
    <script src="{{ get_asset('admin_assets') }}/js/jquery_mask.min.js"></script>
    <script src="{{ get_asset('admin_assets') }}/js/aksFileUpload.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/additional-methods.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8-beta.17/jquery.inputmask.min.js"></script>
    @yield('page-scripts')

    <script src="{{ get_asset('admin_assets') }}/js/app.min.js"></script>
    <script src="{{ get_asset('admin_assets') }}/js/custom.js"></script>

    <script>
    function showPreview(preivew_id) {
        var img_src = URL.createObjectURL(event.target.files[0]);
        $('#'+preivew_id).attr('src',img_src).removeClass('d-none');;
    }
    //custom methods for jquery validator
    jQuery.validator.addMethod("lettersonly", function(value, element) {
        return this.optional(element) || /^[a-z\s]+$/i.test(value);
    }, "Only alphabetical characters");

    jQuery.validator.addMethod("lettersnumbersonly", function(value, element) {
        return this.optional(element) || /^[a-z-0-9]+$/i.test(value);
    }, "Only alphabetical characters and numbers only");

    jQuery.validator.addMethod("noCaps", function(value, element) {
        return this.optional(element) || !/[A-Z]/.test(value); 
    }, "Only small characters are allowed");

    $('.select2').select2();

    </script>

</body>

</html>

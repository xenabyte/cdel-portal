
<!doctype html>
<?php 
    $student = Auth::guard('student')->user();
    $notifications = $student->notifications()->orderBy('created_at', 'desc')->get();
    $name = $student->applicant->lastname. ' ' . $student->applicant->othernames;
    $mode = $student->dashboard_mode;
?>
<html lang="en" data-layout="vertical" data-topbar="light" data-layout-mode="{{ $mode }}" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

<head>

    <meta charset="utf-8" />
    <title> {{ env('APP_NAME') }} || Student Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="{{ env('APP_NAME') }} Dashboard" name="description" />
    <meta content="Olanrewaju kolawole" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{asset('favicon.png')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <!--datatable responsive css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />

    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">

    <!-- Layout config Js -->
    <script src="{{asset('assets/js/layout.js')}}"></script>
    <!-- Bootstrap Css -->
    <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{asset('assets/css/app.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="{{asset('assets/css/custom.min.css')}}" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tiny.cloud/1/b9d45cy4rlld8ypwkzb6yfzdza63fznxtcoc3iyit61r4rv9/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
          selector: 'textarea',
          plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak',
          toolbar_mode: 'floating',
        });
    </script>

    <script>
        window.Laravel = {!! json_encode(['csrfToken' => csrf_token()]) !!};
    </script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
</head>

<body>
    @include('sweetalert::alert')
    <!-- Begin page -->
    <div id="layout-wrapper">

        <header id="page-topbar">
            <div class="layout-width">
                <div class="navbar-header">
                    <div class="d-flex">
                        <!-- LOGO -->
                        <div class="navbar-brand-box horizontal-logo">
                            <a href="{{ url('/student/home') }}" class="logo logo-dark">
                                <span class="logo-sm">
                                    <img src="{{ !empty($pageGlobalData->setting) ? asset($pageGlobalData->setting->logo) : null }}" alt="" width="50">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{ !empty($pageGlobalData->setting) ? asset($pageGlobalData->setting->logo) : null }}" alt="" width="200">
                                </span>
                            </a>

                            <a href="{{ url('/student/home') }}" class="logo logo-light">
                                <span class="logo-sm">
                                    <img src="{{ !empty($pageGlobalData->setting) ? asset($pageGlobalData->setting->logo) : null }}" alt="" width="50">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{ !empty($pageGlobalData->setting) ? asset($pageGlobalData->setting->logo) : null }}" alt="" width="200">
                                </span>
                            </a>
                        </div>

                        <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger shadow-none" id="topnav-hamburger-icon">
                            <span class="hamburger-icon">
                                <span></span>
                                <span></span>
                                <span></span>
                            </span>
                        </button>

                    </div>

                    <div class="d-flex align-items-center">

                        <div class="ms-1 header-item d-none d-sm-flex">
                            <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle shadow-none" data-toggle="fullscreen">
                                <i class='bx bx-fullscreen fs-22'></i>
                            </button>
                        </div>

                        <div class="ms-1 header-item d-none d-sm-flex">
                            <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle light-dark-mode shadow-none">
                                <i class='bx bx-moon fs-22'></i>
                            </button>
                        </div>

                        <div class="dropdown topbar-head-dropdown ms-1 header-item" id="notificationDropdown">
                            <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle shadow-none" id="page-header-notifications-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                                <i class='bx bx-bell fs-22'></i>
                                <span class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger">{{ $notifications->where('status', 0)->count() }}<span class="visually-hidden">unread messages</span></span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-notifications-dropdown">

                                <div class="dropdown-head bg-primary bg-pattern rounded-top">
                                    <div class="p-3">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <h6 class="m-0 fs-16 fw-semibold text-white"> Notifications </h6>
                                            </div>
                                            <div class="col-auto dropdown-tabs">
                                                <span class="badge badge-soft-light fs-13"> {{ $notifications->where('status', 0)->count() }} New</span>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                  <div class="tab-pane fade show active py-2 ps-2" id="all-noti-tab" role="tabpanel">
                                        <div data-simplebar style="max-height: 300px;" class="pe-2">

                                            @foreach ($notifications as $notification)
                                            <a href="#" data-notification-id="{{ $notification->id }}" onclick="updateNotificationStatus(event)">
                                                <div class="text-reset notification-item d-block dropdown-item position-relative {{ $loop->iteration != 1 ? 'border-top border-top-dashed':null }} {{ $notification->status == 1 ? 'notification-read' : 'notification-unread' }}">
                                                    <div class="d-flex">
                                                        <div class="flex-1">
                                                            <div class="fs-13 text-muted">
                                                                <p class="mb-1">{!! $notification->description !!}</p>
                                                                @if(!empty($notification->attachment))<a class="badge text-bg-danger" href="{{ asset($notification->attachment) }}">View Attachment</a>@endif
                                                            </div>
                                                            @php
                                                                $createdAt = \Carbon\Carbon::parse($notification->created_at);
                                                                $diff = $createdAt->diffForHumans();
                                                            @endphp
                                                            <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                                <span><i class="mdi mdi-clock-outline"></i> {{ $diff }}</span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                            @endforeach
                                        </div>

                                    </div>
                            </div>
                        </div>

                        <div class="dropdown ms-sm-3 header-item topbar-user">
                            <button type="button" class="btn shadow-none" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="d-flex align-items-center">
                                    <img class="rounded-circle header-profile-user" src="{{empty($student->image)?asset('assets/images/users/user-dummy-img.jpg'):asset($student->image)}}" alt="Header Avatar">
                                    <span class="text-start ms-xl-2">
                                        <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{ $student->applicant->lastname.' '.$student->applicant->othernames }}</span>
                                        <span class="d-none d-xl-block ms-1 fs-12 text-muted user-name-sub-text"><strong>Matric Number:</strong> {{ $student->matric_number }}</span>
                                    </span>
                                </span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <h6 class="dropdown-header">Welcome {{ $name }}!</h6>
                                <div class="dropdown-divider"></div>
                                <h6 class="dropdown-header">Support Code: <span class="text-danger">ST{{ sprintf("%06d", $student->id) }}</span></h6>
                                @if(env('WALLET_STATUS'))<a class="dropdown-item" href=#"><i class="mdi mdi-wallet text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Balance : <b>₦{{ number_format($student->amount_balance/100, 2) }}</b></span></a>@endif
                                <a class="dropdown-item" href="{{ url('/student/profile') }}"><i class="mdi mdi-cog-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Settings</span></a>
                                <a class="dropdown-item" href="{{ url('/student/logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span class="align-middle" data-key="t-logout">Logout</span></a>
                                <form id="logout-form" action="{{ url('/student/logout') }}" method="POST" style="display: none;">@csrf</form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>


        <!-- ========== App Menu ========== -->
        <div class="app-menu navbar-menu">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <!-- Dark Logo-->
                <a href="{{url('student/home')}}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ !empty($pageGlobalData->setting) ? asset($pageGlobalData->setting->logo) : null }}" alt="" width="50">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ !empty($pageGlobalData->setting) ? asset($pageGlobalData->setting->logo) : null }}" alt="" width="200">
                    </span>
                </a>
                <!-- Light Logo-->
                <a href="{{ url('/student/home') }}" class="logo logo-light">
                   <span class="logo-sm">
                        <img src="{{ !empty($pageGlobalData->setting) ? asset($pageGlobalData->setting->logo) : null }}" alt="" width="50">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ !empty($pageGlobalData->setting) ? asset($pageGlobalData->setting->logo) : null }}" alt="" width="200">
                    </span>
                </a>
                <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
                    <i class="ri-record-circle-line"></i>
                </button>
            </div>

            <div id="scrollbar">
                <div class="container-fluid">

                    <div id="two-column-menu">
                    </div>
                    <ul class="navbar-nav" id="navbar-nav">
                        @if(env('WALLET_STATUS'))
                        <hr class="text-light">
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#"><i class="mdi mdi-wallet fs-16 align-middle me-1"></i> <span class="align-middle">Balance: <b>₦{{ number_format($student->amount_balance/100, 2) }}</b></span></a>
                        </li>
                        <hr class="text-light">
                        @endif
                        <li class="menu-title"><span data-key="t-menu">Menu</span></li>

                        <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ url('/student/home') }}">
                                <i class="mdi mdi-view-dashboard"></i> <span>Dashboard</span>
                            </a>
                        </li>
                        
                        @if(!empty($student->image) && !empty($student->linkedIn) && !empty($student->bandwidth_username))
                            @if($passTuition)
                            <li class="nav-item">
                                <a class="nav-link menu-link" href="{{ url('student/mentor') }}">
                                    <i class="mdi mdi-account-child-circle"></i> <span data-key="t-transaction">Mentor</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link menu-link" href="#bandwidth" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="transaction">
                                    <i class="mdi mdi-bank-transfer"></i> <span data-key="t-transaction">Bandwidth</span>
                                </a>
                                <div class="collapse menu-dropdown" id="bandwidth">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="{{ url('/student/checkBalance') }}" class="nav-link">Bandwidth Balance</a>
                                        </li>
                                        @if(env('WALLET_STATUS'))
                                        <li class="nav-item">
                                            <a href="{{ url('/student/purchaseBandwidth') }}" class="nav-link">Purchase Bandwidth</a>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link menu-link" href="#transaction" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="transaction">
                                    <i class="mdi mdi-bank-transfer"></i> <span data-key="t-transaction">Transactions</span>
                                </a>
                                <div class="collapse menu-dropdown" id="transaction">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="{{ url('/student/transactions') }}" class="nav-link">School Transactions</a>
                                        </li>
                                        @if(env('WALLET_STATUS'))
                                        <li class="nav-item">
                                            <a href="{{ url('/student/walletTransactions') }}" class="nav-link">Wallet Transactions</a>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                            </li> 

                            <li class="nav-item">
                                <a class="nav-link menu-link" href="#courseManagement" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="courseManagement">
                                    <i class="mdi mdi-bookshelf"></i> <span data-key="t-courseManagement">Course Management</span>
                                </a>
                                <div class="collapse menu-dropdown" id="courseManagement">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="{{ url('/student/courseRegistration') }}" class="nav-link">Course Registration</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ url('/student/allCourseRegs') }}" class="nav-link">All Course Registrations</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ url('/student/editCourseReg') }}" class="nav-link">Add/Remove Course(s)</a>
                                        </li>
                                    </ul>
                                </div>
                            </li> 

                            <li class="nav-item">
                                <a class="nav-link menu-link" href="#examManagement" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="examManagement">
                                    <i class="mdi mdi-facebook-gaming"></i> <span data-key="t-examManagement">Exam Management</span>
                                </a>
                                <div class="collapse menu-dropdown" id="examManagement">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="{{ url('/student/examDocket') }}" class="nav-link">Exam Docket</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ url('/student/allExamDockets') }}" class="nav-link">All Exam Docket</a>
                                        </li>
                                    </ul>
                                </div>
                            </li> 

                            <li class="nav-item">
                                <a class="nav-link menu-link" href="#resultManagement" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="resultManagement">
                                    <i class="mdi mdi-cards"></i> <span data-key="t-resultManagement">Result Management</span>
                                </a>
                                <div class="collapse menu-dropdown" id="resultManagement">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="{{ url('/student/examResult') }}" class="nav-link">Exam Result</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ url('/student/transcript') }}" class="nav-link">Transcript</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link menu-link" href="{{ url('student/exits') }}">
                                    <i class="mdi mdi-arrow-top-right-bold-box"></i> <span data-key="t-transaction">Exit(s)</span>
                                </a>
                            </li>
                            @endif
                        @endif
                       
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ url('user/logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="mdi mdi-power"></i> <span data-key="t-logout">Logout</span>
                            </a>
                        </li> <!-- end Logout Menu -->
                    </ul>
                </div>
                <!-- Sidebar -->
            </div>

            <div class="sidebar-background"></div>
        </div>
        <!-- Left Sidebar End -->
        <!-- Vertical Overlay-->
        <div class="vertical-overlay"></div>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">

                   @yield('content')

                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <script>document.write(new Date().getFullYear())</script> © {{ env('APP_NAME') }}.
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end d-none d-sm-block">
                                Design & Develop by TAU ICT
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

<!-- removeNotificationModal -->
<div id="removeNotificationModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="NotificationModalbtn-close"></button>
            </div>
            <div class="modal-body">
                <div class="mt-2 text-center">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                    <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                        <h4>Are you sure ?</h4>
                        <p class="text-muted mx-4 mb-0">Are you sure you want to remove this Notification ?</p>
                    </div>
                </div>
                <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                    <button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn w-sm btn-danger" id="delete-notification">Yes, Delete It!</button>
                </div>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



    <!--start back-to-top-->
    <button onclick="topFunction()" class="btn btn-danger btn-icon" id="back-to-top">
        <i class="ri-arrow-up-line"></i>
    </button>
    <!--end back-to-top-->

    <!--preloader-->
    <div id="preloader">
        <div id="status">
            <div class="spinner-border text-primary avatar-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <script src="{{asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('assets/libs/simplebar/simplebar.min.js')}}"></script>
    <script src="{{asset('assets/libs/node-waves/waves.min.js')}}"></script>
    <script src="{{asset('assets/libs/feather-icons/feather.min.js')}}"></script>
    <script src="{{asset('assets/js/pages/plugins/lord-icon-2.1.0.js')}}"></script>
    {{-- <script src="{{asset('assets/js/plugins.js')}}"></script> --}}

    <script src="{{ asset('assets/js/lga.min.js') }}"></script>
    <!-- form wizard init -->
    <script src="{{ asset('assets/js/pages/form-wizard.init.js') }}"></script>
    <!-- App js -->
    <script src="{{asset('assets/js/app.js')}}"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <!--datatable js-->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

    <script src="{{asset('assets/js/pages/datatables.init.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>


    <script>
        function handlePaymentMethodChange(event) {
            const selectedPaymentMethod = event.target.value;
            console.log(selectedPaymentMethod);
            const submitButton = document.getElementById('submit-button');
            if(selectedPaymentMethod != ''){
                if(selectedPaymentMethod == 'Remita' || selectedPaymentMethod == 'Zenith') {
                    submitButton.disabled = true;
                }else{
                    submitButton.disabled = false;
                }

                if(selectedPaymentMethod == 'BankTransfer'){
                    document.getElementById('transferInfo').style.display = 'block';
                    document.getElementById('submit-button').style.display = 'none';
                }else{
                    document.getElementById('transferInfo').style.display = 'none';
                    document.getElementById('submit-button').style.display = 'block';
                }
               
            }else{
                submitButton.disabled = true;
            }
        }

        function handlePaymentMainMethodChange(event) {
            const selectedPaymentMethod = event.target.value;
            console.log(selectedPaymentMethod);
            const submitButton = document.getElementById('submit-button-main');
            if(selectedPaymentMethod != ''){
                if(selectedPaymentMethod == 'Remita' || selectedPaymentMethod == 'Zenith') {
                    submitButton.disabled = true;
                }else{
                    submitButton.disabled = false;
                }

                if(selectedPaymentMethod == 'BankTransfer'){
                    document.getElementById('transferInfoMain').style.display = 'block';
                    document.getElementById('submit-button-main').style.display = 'none';
                }else{
                    document.getElementById('transferInfoMain').style.display = 'none';
                    document.getElementById('submit-button-main').style.display = 'block';
                }
               
            }else{
                submitButton.disabled = true;
            }
        }
    </script>
    <script>
        // Get the current time
        var currentTime = new Date();
        var currentHour = currentTime.getHours();

        // Define the greeting messages
        var morningGreeting = "Good morning";
        var afternoonGreeting = "Good afternoon";
        var eveningGreeting = "Good evening";

        // Get the DOM element to display the greeting
        var greetingElement = document.getElementById("greeting");

        // Determine the appropriate greeting based on the time of day
        var greeting;
        if (currentHour >= 0 && currentHour < 12) {
            greeting = morningGreeting;
        } else if (currentHour >= 12 && currentHour < 18) {
            greeting = afternoonGreeting;
        } else {
            greeting = eveningGreeting;
        }

        // Display the greeting
        greetingElement.innerHTML = greeting;
    </script>
    <script>
        function updateNotificationStatus(event) {
            event.preventDefault();
            
            const notificationId = event.currentTarget.getAttribute('data-notification-id');
            
            axios.post("{{ url('/updateNotificationStatus') }}", {
                notificationId: notificationId
            })
            .then(response => {
                event.currentTarget.classList.add('notification-read');
            })
            .catch(error => {
                console.error(error);
            });
        }
    </script>
    <script>
        $(document).ready(function() {
          $("#submit-button-main").click(function() {
            // Disable the button
            $(this.form).submit();
    
            $(this).prop("disabled", true);
        
            // Remove the text
            $(this).text("");
        
            // Replace the text with a spinner
            $(this).html("<i class='fa fa-spinner fa-spin'></i>");
          });
        });
    </script>

    <script>
        $(document).ready(function() {
        $("#submit-button").click(function() {
            // Disable the button
            $(this.form).submit();

            $(this).prop("disabled", true);
        
            // Remove the text
            $(this).text("");
        
            // Replace the text with a spinner
            $(this).html("<i class='fa fa-spinner fa-spin'></i>");
        });
        });
    </script>
    <script>
        $(document).ready(function () {
            // Hide initially
            $('#exit_date, #return_date').closest('.col-lg-6').hide();
    
            // On change of the 'type' select
            $('#type').on('change', function () {
                var selectedType = $(this).val();
    
                // Show or hide the fields based on the selected type
                if (selectedType === 'Holiday') {
                    $('#exit_date, #return_date').closest('.col-lg-6').hide();
                } else {
                    $('#exit_date, #return_date').closest('.col-lg-6').show();
                }
            });
        });
    </script>
    
</body>

</html>

<!doctype html>
<html lang="en" data-layout="twocolumn" data-layout-style="default" data-layout-position="fixed" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-layout-width="fluid" data-preloader="disable">
<?php 
    $admin = Auth::guard('admin')->user();
?>

<head>

    <meta charset="utf-8" />
    <title>Admin Dashboard | {{ env('APP_NAME') }} </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="{{ env('APP_NAME') }} Dashboard" name="description" />
    <meta content="Oladipo Damilare(KoderiaNG)" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{asset('assets/images/favicon.ico')}}">

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
                            <a href="{{ env('WEBSITE_URL') }}" class="logo logo-dark">
                                <span class="logo-sm">
                                    <img src="{{asset('assets/images/logo-sm.png')}}" alt="" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{asset('assets/images/logo-dark.png')}}" alt="" height="17">
                                </span>
                            </a>

                            <a href="{{ env('WEBSITE_URL') }}" class="logo logo-light">
                                <span class="logo-sm">
                                    <img src="{{asset('assets/images/logo-sm.png')}}" alt="" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{asset('assets/images/logo-light.png')}}" alt="" height="17">
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
                        <div class="dropdown topbar-head-dropdown ms-1 header-item">
                            <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle shadow-none" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class='bx bx-category-alt fs-22'></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-lg p-0 dropdown-menu-end">
                                <div class="p-3 border-top-0 border-start-0 border-end-0 border-dashed border">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h6 class="m-0 fw-semibold fs-15"> Web Apps </h6>
                                        </div>
                                        <div class="col-auto">
                                            <a href="#!" class="btn btn-sm btn-soft-info shadow-none"> View All Apps
                                                <i class="ri-arrow-right-s-line align-middle"></i></a>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-2">
                                    <div class="row g-0">
                                        <div class="col">
                                            <a class="dropdown-icon-item" href="#!">
                                                <img src="{{asset('assets/images/brands/github.png')}}" alt="Github">
                                                <span>GitHub</span>
                                            </a>
                                        </div>
                                        <div class="col">
                                            <a class="dropdown-icon-item" href="#!">
                                                <img src="{{asset('assets/images/brands/bitbucket.png')}}" alt="bitbucket">
                                                <span>Bitbucket</span>
                                            </a>
                                        </div>
                                        <div class="col">
                                            <a class="dropdown-icon-item" href="#!">
                                                <img src="{{asset('assets/images/brands/dribbble.png')}}" alt="dribbble">
                                                <span>Dribbble</span>
                                            </a>
                                        </div>
                                    </div>

                                    <div class="row g-0">
                                        <div class="col">
                                            <a class="dropdown-icon-item" href="#!">
                                                <img src="{{asset('assets/images/brands/dropbox.png')}}" alt="dropbox">
                                                <span>Dropbox</span>
                                            </a>
                                        </div>
                                        <div class="col">
                                            <a class="dropdown-icon-item" href="#!">
                                                <img src="{{asset('assets/images/brands/mail_chimp.png')}}" alt="mail_chimp">
                                                <span>Mail Chimp</span>
                                            </a>
                                        </div>
                                        <div class="col">
                                            <a class="dropdown-icon-item" href="#!">
                                                <img src="{{asset('assets/images/brands/slack.png')}}" alt="slack">
                                                <span>Slack</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


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
                                <span class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger">3<span class="visually-hidden">unread messages</span></span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-notifications-dropdown">

                                <div class="dropdown-head bg-primary bg-pattern rounded-top">
                                    <div class="p-3">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <h6 class="m-0 fs-16 fw-semibold text-white"> Notifications </h6>
                                            </div>
                                            <div class="col-auto dropdown-tabs">
                                                <span class="badge badge-soft-light fs-13"> 4 New</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="px-2 pt-2">
                                        <ul class="nav nav-tabs dropdown-tabs nav-tabs-custom" data-dropdown-tabs="true" id="notificationItemsTab" role="tablist">
                                            <li class="nav-item waves-effect waves-light">
                                                <a class="nav-link" data-bs-toggle="tab" href="#messages-tab" role="tab" aria-selected="false">
                                                    Messages
                                                </a>
                                            </li>
                                        </ul>
                                    </div>

                                </div>

                                <div class="tab-content position-relative" id="notificationItemsTabContent">
                                    <div class="tab-pane fade show active py-2 ps-2" id="all-noti-tab" role="tabpanel">
                                        <div data-simplebar style="max-height: 300px;" class="pe-2">

                                            <a href="#"> 
                                                <div class="text-reset notification-item d-block dropdown-item position-relative">
                                                    <div class="d-flex">
                                                        <img src="{{asset('assets/images/users/avatar-2.jpg')}}" class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                                        <div class="flex-1">
                                                            <a href="#!" class="stretched-link">
                                                                <h6 class="mt-0 mb-1 fs-13 fw-semibold">Angela Bernier</h6>
                                                            </a>
                                                            <div class="fs-13 text-muted">
                                                                <p class="mb-1">Answered to your comment on the cash flow forecast's
                                                                    graph 🔔.</p>
                                                            </div>
                                                            <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                                <span><i class="mdi mdi-clock-outline"></i> 48 min ago</span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>

                                            <div class="my-3 text-center view-all">
                                                <button type="button" class="btn btn-soft-success waves-effect waves-light">View
                                                    All Notifications <i class="ri-arrow-right-line align-middle"></i></button>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="dropdown ms-sm-3 header-item topbar-user">
                            <button type="button" class="btn shadow-none" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="d-flex align-items-center">
                                    <img class="rounded-circle header-profile-user" src="{{asset('assets/images/users/user-dummy-img.jpg')}}" alt="Header Avatar">
                                    <span class="text-start ms-xl-2">
                                        <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{ $admin->name }}</span>
                                        <span class="d-none d-xl-block ms-1 fs-12 text-muted user-name-sub-text">{{ $admin->role }}</span>
                                    </span>
                                </span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <h6 class="dropdown-header">Welcome {{ $admin->name }}!</h6>
                                <a class="dropdown-item" href="pages-profile.html"><i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Profile</span></a>
                                <a class="dropdown-item" href="apps-chat.html"><i class="mdi mdi-message-text-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Messages</span></a>
                                <a class="dropdown-item" href="apps-tasks-kanban.html"><i class="mdi mdi-calendar-check-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Taskboard</span></a>
                                <a class="dropdown-item" href="pages-faqs.html"><i class="mdi mdi-lifebuoy text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Help</span></a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="pages-profile-settings.html"><span class="badge bg-soft-success text-success mt-1 float-end">New</span><i class="mdi mdi-cog-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Settings</span></a>
                                <a class="dropdown-item" href="{{ url('/admin/logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span class="align-middle" data-key="t-logout">Logout</span></a>
                                <form id="logout-form" action="{{ url('/admin/logout') }}" method="POST" style="display: none;">@csrf</form>
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
                <a href="{{url('admin/home')}}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{asset('assets/images/logo-sm.png')}}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{asset('assets/images/logo-dark.png')}}" alt="" height="17">
                    </span>
                </a>
                <!-- Light Logo-->
                <a href="{{ env('WEBSITE_URL') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{asset('assets/images/logo-sm.png')}}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{asset('assets/images/logo-light.png')}}" alt="" height="17">
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
                        <li class="menu-title"><span data-key="t-menu">Menu</span></li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ url('/admin/home') }}">
                                <i class="mdi mdi-view-dashboard"></i> <span>Dashboard</span>
                            </a>
                        </li>
                       
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#appSetting" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarApps">
                                <i class="mdi mdi-application-settings"></i> <span data-key="t-apps">General App Setting</span>
                            </a>
                            <div class="collapse menu-dropdown" id="appSetting">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{url('/admin/setting')}}" class="nav-link" data-key="t-calendar">App Settings </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{url('/admin/sessionSetup')}}" class="nav-link" data-key="t-calendar">Academic Session Setup </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ url('/admin/courseRegMgt') }}" class="nav-link">Course Reg. Mgt</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ url('/admin/examDocketMgt') }}" class="nav-link">Exam Docket Mgt</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{url('/admin/programmeCategory')}}" class="nav-link" data-key="t-chat">Programme Category </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{url('/admin/academicLevel')}}" class="nav-link" data-key="t-chat">Academic Levels </a>
                                    </li>
                                </ul>
                            </div>
                        </li>


                        <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-bursary">Bursary</span></li>

                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#bursary" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="bursary">
                                <i class="mdi mdi-bank-transfer"></i> <span data-key="t-bursary">Bursary</span>
                            </a>
                            <div class="collapse menu-dropdown" id="bursary">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ url('/admin/payments') }}" class="nav-link"> Payments </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{('/admin/transactions')}}" class="nav-link"> Transactions </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{('/admin/chargeStudent')}}" class="nav-link">Charge 'a' Student </a>
                                    </li>
                                </ul>
                            </div>
                        </li> <!-- end Bursary Menu -->


                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#admission" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="admission">
                                <i class="mdi mdi-account-box-multiple"></i> <span data-key="t-admission">Admission</span>
                            </a>
                            <div class="collapse menu-dropdown" id="admission">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ url('/admin/applicants') }}" class="nav-link">Applicants</a>
                                    </li>
            
                                    <li class="nav-item">
                                        <a href="{{ url('/admin/students') }}" class="nav-link">Students</a>
                                    </li>
                                </ul>
                            </div>
                        </li> <!-- end Bursary Menu -->

                        <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-pages">Academics</span></li>

                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#academicSettings" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="academicSettings">
                                <i class="mdi mdi-view-carousel-outline"></i> <span class="badge badge-pill bg-danger" data-key="t-hot">Academics</span>
                            </a>
                            <div class="collapse menu-dropdown" id="academicSettings">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ url('/admin/faculties') }}" class="nav-link">Faculties</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ url('/admin/populateFaculty') }}" class="nav-link">Populate Faculty</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ url('/admin/populateCourse') }}" class="nav-link">Populate Course</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ url('/admin/populateStaff') }}" class="nav-link">Populate Staff</a>
                                    </li>
                                </ul>
                            </div>
                        </li> <!-- end Dashboard Menu -->

                        <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-pages">Student Management</span></li>

                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#studentMgt" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="studentMgt">
                                <i class="mdi mdi-account-tie"></i> <span class="badge badge-pill bg-danger" data-key="t-hot">Students</span>
                            </a>
                            <div class="collapse menu-dropdown" id="studentMgt">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ url('/admin/campusCapacity') }}" class="nav-link">Campus Capacity</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ url('/admin/allStudents') }}" class="nav-link">All Student</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ url('/admin/massPromotion') }}" class="nav-link">Promote Student</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ url('/admin/demoteStudent') }}" class="nav-link">Demote Student</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ url('/admin/demoteStudentRecord') }}" class="nav-link">Demoted Student Record</a>
                                    </li>
                                </ul>
                            </div>
                        </li> <!-- end Dashboard Menu -->

                        <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-staff">Staff Management</span></li>

                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#staffMgt" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="staffMgt">
                                <i class="mdi mdi-account-supervisor-circle-outline"></i> <span data-key="t-staff">Staff Management</span>
                            </a>
                            <div class="collapse menu-dropdown" id="staffMgt">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="#staffApplicants" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarSignIn" data-key="t-signin"> Staff Applicants
                                        </a>
                                        <div class="collapse menu-dropdown" id="staffApplicants">
                                            <ul class="nav nav-sm flex-column">
                                                <li class="nav-item">
                                                    <a href="auth-signin-basic.html" class="nav-link" data-key="t-basic"> Staff Applications </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="auth-signin-cover.html" class="nav-link" data-key="t-cover"> Role Applications </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#staffs" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="staffs" data-key="t-staffs"> Staff
                                        </a>
                                        <div class="collapse menu-dropdown" id="staffs">
                                            <ul class="nav nav-sm flex-column">
                                                <li class="nav-item">
                                                    <a href="{{ url('/admin/staff') }}" class="nav-link" data-key="t-basic"> All Staff </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="{{('/admin/staffRoles')}}" class="nav-link" data-key="t-cover"> Staff Roles </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="{{url('/admin/approvalLevel')}}" class="nav-link" data-key="t-chat">Approval Levels </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </li>


                        <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-pages"></span></li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ url('admin/logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
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
    <script src="{{asset('assets/js/plugins.js')}}"></script>
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

<script>
    function handleFacultyChange(event) {
        const selectedFaculty = event.target.value;
        if(selectedFaculty != ''){
            axios.get("{{ url('/applicant/facultyById')  }}/"+selectedFaculty)
            .then(response => {
                const data = response.data;
                const totalAmount = getTotalAmountForApplicationFee(data);
                
                // Set the total amount in the paragraph element
                const amountParagraph = document.getElementById('amount');
                amountParagraph.textContent = `Application Fee(Non Refundable): ₦${totalAmount.toFixed(2)}`;
                document.getElementById('paymentInfo').style.display = 'block';
            })
            .catch(error => {
                console.error(error);
            });
        }else{
            document.getElementById('paymentInfo').style.display = 'none';
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
</body>

</html>
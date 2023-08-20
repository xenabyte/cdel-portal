
<!doctype html>
<html lang="en" data-layout="vertical" data-layout-style="default" data-layout-position="fixed" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-layout-width="fluid" data-preloader="disable">
@php
    $staff = Auth::guard('staff')->user();
    $staffDeanRole = false;
    $staffSubDeanRole = false;
    $staffHODRole = false;
    $staffVCRole = false;
    $staffRegistrarRole = false;
    $staffHRRole = false;
    $staffLevelAdviserRole = false;
    $staffExamOfficerRole = false;
    $staffPublicRelationRole = false;
    $staffStudentCareRole = false;
    $staffBursaryRole = false;

    $notifications = $staff->notifications()->orderBy('created_at', 'desc')->get();
    
    
    foreach ($staff->staffRoles as $staffRole) {
        if (strtolower($staffRole->role->role) == 'dean') {
            $staffDeanRole = true;
        }
        if (strtolower($staffRole->role->role) == 'sub-dean') {
            $staffSubDeanRole = true;
        }
        if (strtolower($staffRole->role->role) == 'hod') {
            $staffHODRole = true;
        }
        if (strtolower($staffRole->role->role) == 'vice chancellor') {
            $staffVCRole = true;
        }
        if (strtolower($staffRole->role->role) == 'registrar') {
            $staffRegistrarRole = true;
        }
        if (strtolower($staffRole->role->role) == 'human resource') {
            $staffHRRole = true;
        }
        if (strtolower($staffRole->role->role) == 'level adviser') {
            $staffLevelAdviserRole = true;
        }
        if (strtolower($staffRole->role->role) == 'exam officer') {
            $staffExamOfficerRole = true;
        }
        if (strtolower($staffRole->role->role) == 'public relation') {
            $staffPublicRelationRole = true;
        }
        if (strtolower($staffRole->role->role) == 'student care') {
            $staffStudentCareRole = true;
        }
        if(strtolower($staffRole->role->role) == 'staffBursaryRole'){
            $staffBursaryRole = true;
        }
        
    }
@endphp

<head>

    <meta charset="utf-8" />
    <title>Staff Dashboard | {{ env('APP_NAME') }} </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="{{ env('APP_NAME') }} Dashboard" name="description" />
    <meta content="Aremu Adeola Abidemi(Codex)" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{asset('favicon.png')}}">

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
                            <a href="{{url('staff/home')}}" class="logo logo-dark">
                                <span class="logo-sm">
                                    <img src="{{asset('assets/images/logo-sm.png')}}" alt="" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{asset('assets/images/logo-dark.png')}}" alt="" height="17">
                                </span>
                            </a>

                            <a href="{{url('staff/home')}}" class="logo logo-light">
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
                                                                <p class="mb-1">{{ $notification->description }}</p>
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
                                    <img class="rounded-circle header-profile-user" src="{{!empty($staff->image) ? $staff->image : asset('assets/images/users/user-dummy-img.jpg')}}" alt="Header Avatar">
                                    <span class="text-start ms-xl-2">
                                        <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{ $staff->title.' '. $staff->lastname .' '.$staff->othernames }}</span>
                                        <span class="d-none d-xl-block ms-1 fs-12 text-muted user-name-sub-text">Staff ID: {{ $staff->staffId}}</span>
                                    </span>
                                </span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <h6 class="dropdown-header">Welcome {{ $staff->title.' '. $staff->lastname .' '.$staff->othernames }}!</h6>
                                <span class="dropdown-item"><i class="mdi mdi-account-child-circle text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Referral Code: {{ $staff->referral_code }}</span></span>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ url('/staff/profile') }}"><i class="mdi mdi-cog-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Settings</span></a>
                                <a class="dropdown-item" href="{{ url('/staff/logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span class="align-middle" data-key="t-logout">Logout</span></a>
                                <form id="logout-form" action="{{ url('/staff/logout') }}" method="POST" style="display: none;">@csrf</form>
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
                <a href="{{url('staff/home')}}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{asset('assets/images/logo-sm.png')}}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{asset('assets/images/logo-dark.png')}}" alt="" height="17">
                    </span>
                </a>
                <!-- Light Logo-->
                <a href="{{url('staff/home')}}" class="logo logo-light">
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
                            <a class="nav-link menu-link" href="{{ url('/staff/home') }}">
                                <i class="mdi mdi-view-dashboard"></i> <span>Dashboard</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ url('staff/mentee') }}">
                                <i class="mdi mdi-account-child-circle"></i> <span data-key="t-transaction">Mentee(s)</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ url('staff/reffs') }}">
                                <i class="mdi mdi-account-network-outline"></i> <span data-key="t-transaction">Referred Student(s)</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ url('staff/courses') }}">
                                <i class="mdi mdi-book-education-outline"></i> <span data-key="t-transaction">Course(s)</span>
                            </a>
                        </li>

                        @if($staffDeanRole || $staffSubDeanRole || $staffHODRole || $staffRegistrarRole ||  $staffHRRole || $staffVCRole)
                        <li class="nav-item">
                            <a class="na<li class="nav-item">
                                <a class="nav-link menu-link" href="#staffManagement" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="staffManagement">
                                    <i class="mdi mdi-account-supervisor"></i> <span data-key="t-staffManagement">Staff Management</span>
                                </a>
                                <div class="collapse menu-dropdown" id="staffManagement">
                                    <ul class="nav nav-sm flex-column">
                                        @if($staffHODRole)
                                        <li class="nav-item">
                                            <a href="{{ url('/staff/courseAllocation') }}" class="nav-link">Course-to-Staff Allocation</a>
                                        </li>
                                        @endif
                                        @if($staffDeanRole || $staffSubDeanRole || $staffHODRole || $staffRegistrarRole || $staffHRRole || $staffVCRole)
                                        <li class="nav-item">
                                            <a href="{{ url('/staff/staff') }}" class="nav-link" data-key="t-basic"> All Staff </a>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                            </li> <!-- end Bursary Menu -->
                        </li>
                        @endif

                        @if($staffDeanRole || $staffSubDeanRole)
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#facultySettings" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="facultySettings">
                                <i class="mdi mdi-view-carousel-outline"></i> <span  data-key="t-hot">Faculty Management</span>
                            </a>
                            <div class="collapse menu-dropdown" id="facultySettings">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ url('/staff/faculties') }}" class="nav-link">Faculty</a>
                                    </li>
                                </ul>
                            </div>
                        </li> <!-- end Dashboard Menu -->
                        @endif


                        @if($staffHODRole)
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#deptSettings" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="deptSettings">
                                <i class="mdi mdi-cards-variant"></i> <span  data-key="t-hot">Dept. Management</span>
                            </a>
                            <div class="collapse menu-dropdown" id="deptSettings">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ url('/staff/departments') }}" class="nav-link">Department</a>
                                    </li>
                                </ul>
                            </div>
                        </li> <!-- end Dashboard Menu -->
                        @endif

                        @if($staffLevelAdviserRole || $staffHODRole || $staffVCRole || $staffDeanRole || $staffSubDeanRole)
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#courseSettings" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="courseSettings">
                                <i class="mdi mdi-card"></i> <span  data-key="t-hot">Prog. Management</span>
                            </a>
                            <div class="collapse menu-dropdown" id="courseSettings">
                                <ul class="nav nav-sm flex-column">
                                    @if($staffLevelAdviserRole)
                                    <li class="nav-item">
                                        <a href="{{ url('/staff/adviserProgrammes') }}" class="nav-link">Programmes</a>
                                    </li>
                                    @endif

                                    <li class="nav-item">
                                        <a href="{{ url('/staff/studentCourses') }}" class="nav-link">Student Courses</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        @endif


                        @if($staffExamOfficerRole || $staffHODRole)
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#resultMgt" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="resultMgt">
                                <i class="mdi mdi-credit-card-search-outline"></i> <span data-key="t-result">Result Management</span>
                            </a>
                            <div class="collapse menu-dropdown" id="resultMgt">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ url('/staff/getStudentResults') }}" class="nav-link">Students Results</a>
                                    </li>
                                </ul>
                            </div>
                        </li> <!-- end Dashboard Menu -->
                        @endif

                        @if($staffBursaryRole || $staffVCRole)
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#bursary" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="bursary">
                                <i class="mdi mdi-bank-transfer"></i> <span data-key="t-bursary">Bursary</span>
                            </a>
                            <div class="collapse menu-dropdown" id="bursary">
                                <ul class="nav nav-sm flex-column">
                                    @if($staffBursaryRole)
                                    <li class="nav-item">
                                        <a href="{{ url('/staff/payments') }}" class="nav-link"> Payments </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{('/staff/transactions')}}" class="nav-link"> Transactions </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{('/staff/chargeStudent')}}" class="nav-link">Charge 'a' Student </a>
                                    </li>
                                    @endif
                                    <li class="nav-item">
                                        <a href="{{('/staff/transactionReport')}}" class="nav-link"> Transaction Report </a>
                                    </li>
                                </ul>
                            </div>
                        </li> <!-- end Bursary Menu -->
                        @endif

                        <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ url('staff/profile') }}">
                                <i class="mdi mdi-account-child-circle"></i> <span data-key="t-profile">Profile</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ url('staff/logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
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
    <script src="{{ asset('assets/js/plugins.js') }}"></script>
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
        function handleFacultyChange(event) {
            const selectedFaculty = event.target.value;
            const departmentSelect = $('#department');

            if(selectedFaculty != ''){
                axios.get("{{ url('/staff/getDepartments') }}/"+selectedFaculty)
                .then(function (response) {
                    departmentSelect.empty().append($('<option>', {
                        value: '',
                        text: '--Select--'
                    }));

                    var staffRoleVCRole = "<?php echo $staffVCRole ?>";
                    var staffDepartmentId =  "<?php echo $staff->department_id ?>";
                    
                    $.each(response.data, function (index, department) {
                        if (!staffRoleVCRole && staffDepartmentId == department.id) {
                            departmentSelect.append($('<option>', {
                                value: department.id,
                                text: department.name
                            }));
                        }
                    });

                    if (staffRoleVCRole) {
                        $.each(response.data, function (index, department) {
                            departmentSelect.append($('<option>', {
                                value: department.id,
                                text: department.name
                            }));
                        });
                    }

                    
                })
                .catch(function (error) {
                    console.error("Error fetching departments:", error);
                });
            }else{
                
            }
        }

        function handleDepartmentChange(event) {
            const selectedDepartment = event.target.value;
            const programmeSelect = $('#programme');

            if(selectedDepartment != ''){
                axios.get("{{ url('/admin/getProgrammes') }}/"+selectedDepartment)
                .then(function (response) {

                    programmeSelect.empty().append($('<option>', {
                        value: '',
                        text: '--Select--'
                    }));
                    $.each(response.data, function (index, programme) {
                        programmeSelect.append($('<option>', {
                            value: programme.id,
                            text: programme.name
                        }));
                    });
                })
                .catch(function (error) {
                    console.error("Error fetching departments:", error);
                });
            }else{
                
            }
        }
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

</body>

</html>
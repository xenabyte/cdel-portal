<!doctype html>
<html lang="en" data-layout="vertical" data-layout-style="default" data-layout-position="fixed" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-layout-width="fluid" data-preloader="disable">
@php
    use App\Models\Unit;
    use App\Models\Faculty;
    $programmeCategory = new \App\Models\ProgrammeCategory;


    $staff = Auth::guard('staff')->user();
    $staffId = $staff->id;

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
    $staffAdmissionOfficerRole = false;
    $staffAcademicPlannerRole = false;

    $committeeIds = $staff->committeeMembers->pluck('committee_id');

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
        if(strtolower($staffRole->role->role) == 'bursary'){
            $staffBursaryRole = true;
        }
        if(strtolower($staffRole->role->role) == 'admission'){
            $staffAdmissionOfficerRole = true;
        }
        if(strtolower($staffRole->role->role) == 'academic planning'){
            $staffAcademicPlannerRole = true;
        }   
    }


    $unitNames = ['UNIT_REGISTRY', 'UNIT_BURSARY', 'UNIT_STUDENT_CARE', 'UNIT_LIBRARY', 'UNIT_WORK_STUDY'];

    $units = [];
    foreach ($unitNames as $unitName) {
        $units[] = constant("App\Models\Unit::$unitName");
    }

    $isUnitHead = Unit::whereIn('name', $units)
                    ->where('unit_head_id', $staff->id)
                    ->exists();

    // Get the Work Study unit constant
    $workStudyUnit = constant("App\Models\Unit::UNIT_WORK_STUDY");

    $isWorkStudyUnitHeadOrMember = Unit::where('name', $workStudyUnit)
    ->where(function ($query) use ($staff, $workStudyUnit) {
        $query->where('unit_head_id', $staff->id) 
            ->orWhere(function ($query) use ($staff, $workStudyUnit) {
                $query->where('id', $staff->unit_id);
            });
    })
    ->exists();

    $pendingStudentClearanceCount = \App\Models\FinalClearance::where('status', null)->count();

    $pendingStudentProgrammeChangeCount = \App\Models\ProgrammeChangeRequest::where('status', 'pending')
    ->where(function ($query) use ($staffId) {
        $query->where('old_programme_hod_id', $staffId)
              ->orWhere('old_programme_dean_id', $staffId)
              ->orWhere('new_programme_hod_id', $staffId)
              ->orWhere('new_programme_dean_id', $staffId)
              ->orWhere('dap_id', $staffId)
              ->orWhere('registrar_id', $staffId);
    })
    ->count();

    $isFacultyOfficer = Faculty::where('faculty_officer_id', $staff->id)->exists();

@endphp

<head>

    <meta charset="utf-8" />
    <title>{{ env('APP_NAME') }} || Staff Dashboard || {{ $isWorkStudyUnitHeadOrMember }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="{{ env('APP_NAME') }} Dashboard" name="description" />
    <meta content="Aremu Adeola Abidemi(Codex)" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{asset('favicon.png')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <!--datatable responsive css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />

    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">

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
    
    <script>
        window.Laravel = {!! json_encode(['csrfToken' => csrf_token()]) !!};
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <script type="text/javascript">
        var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
        (function(){
        var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
        s1.async=true;
        s1.src='https://embed.tawk.to/6672b07219f6c616eadbe18d/1i0o02fu8';
        s1.charset='UTF-8';
        s1.setAttribute('crossorigin','*');
        s0.parentNode.insertBefore(s1,s0);
        })();
    </script>

    <script src="{{ env('CKEDITOR_CDN') }}"></script>
    <script>
        // Select all textarea elements and initialize CKEditor on each
        document.querySelectorAll('ckeditor').forEach((textarea) => {
            CKEDITOR.replace(textarea);
        });
    </script>    
    <style>
        .cke_notifications_area{
            display: none;
        }
    </style>
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
                                    <img src="{{ !empty($pageGlobalData->setting) ? asset($pageGlobalData->setting->logo) : null }}" alt="" width="50">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{ !empty($pageGlobalData->setting) ? asset($pageGlobalData->setting->logo) : null }}" alt="" width="200">
                                </span>
                            </a>

                            <a href="{{url('staff/home')}}" class="logo logo-light">
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
                        <img src="{{ !empty($pageGlobalData->setting) ? asset($pageGlobalData->setting->logo) : null }}" alt="" width="50">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ !empty($pageGlobalData->setting) ? asset($pageGlobalData->setting->logo) : null }}" alt="" width="200">
                    </span>
                </a>
                <!-- Light Logo-->
                <a href="{{url('staff/home')}}" class="logo logo-light">
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
                        <li class="menu-title"><span data-key="t-menu">Menu</span></li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ url('/staff/home') }}">
                                <i class="mdi mdi-view-dashboard"></i> <span>Dashboard</span>
                            </a>
                        </li>

                        @if(!empty($staff->change_password))
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

                            {{-- @if($staff->staffRoles->count() > 0) --}}
                            <li class="nav-item">
                                <a class="nav-link menu-link" href="#student" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarForms">
                                    <i class="mdi mdi-account-group"></i> <span data-key="t-studentMGT">Student MGT</span> 
                                    @if($staffDeanRole || $staffHODRole || $staffRegistrarRole || $isUnitHead)
                                    <span class="badge badge-pill bg-danger" data-key="t-hot">({{ $pendingStudentClearanceCount }}) ({{ $pendingStudentProgrammeChangeCount }})</span>
                                    @endif
                                </a>
                                <div class="collapse menu-dropdown" id="student">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="#allStudent" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="allStudent" data-key="t-allStudent">All Student</a>
                                            <div class="collapse menu-dropdown" id="allStudent">
                                                <ul class="nav nav-sm flex-column">
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/allStudents/'.$programmeCategory::UNDERGRADUATE) }}" class="nav-link" data-key="t-basic"> {{ $programmeCategory::UNDERGRADUATE }} </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/allStudents/'.$programmeCategory::TOPUP) }}" class="nav-link" data-key="t-basic"> {{ $programmeCategory::TOPUP }} </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/allStudents/'.$programmeCategory::PGD) }}" class="nav-link" data-key="t-basic"> {{ $programmeCategory::PGD }} </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/allStudents/'.$programmeCategory::MASTER) }}" class="nav-link" data-key="t-basic"> {{ $programmeCategory::MASTER }} </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/allStudents/'.$programmeCategory::DOCTORATE) }}" class="nav-link" data-key="t-basic"> {{ $programmeCategory::DOCTORATE }} </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>

                                        <li class="nav-item">
                                            <a href="#graduatingStudents" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="graduatingStudents" data-key="t-graduatingStudents"> Graduating Students</a>
                                            <div class="collapse menu-dropdown" id="graduatingStudents">
                                                <ul class="nav nav-sm flex-column">
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/graduatingStudents/'.$programmeCategory::UNDERGRADUATE) }}" class="nav-link" data-key="t-basic"> {{ $programmeCategory::UNDERGRADUATE }} </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/graduatingStudents/'.$programmeCategory::TOPUP) }}" class="nav-link" data-key="t-basic"> {{ $programmeCategory::TOPUP }} </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/graduatingStudents/'.$programmeCategory::PGD) }}" class="nav-link" data-key="t-basic"> {{ $programmeCategory::PGD }} </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/graduatingStudents/'.$programmeCategory::MASTER) }}" class="nav-link" data-key="t-basic"> {{ $programmeCategory::MASTER }} </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/graduatingStudents/'.$programmeCategory::DOCTORATE) }}" class="nav-link" data-key="t-basic"> {{ $programmeCategory::DOCTORATE }} </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>

                                        <li class="nav-item">
                                            <a class="nav-link menu-link" href="{{ url('staff/alumni') }}" data-key="t-profile">Alumni (Graduated Students)</a>
                                        </li>

                                        <li class="nav-item">
                                            <a class="nav-link menu-link" href="{{ url('staff/programmeChangeRequests') }}" data-key="t-profile">Intra Transfer Applications <span class="badge badge-pill bg-danger" data-key="t-hot">{{  $pendingStudentProgrammeChangeCount }} </span></a>
                                        </li>

                                        @if($staffDeanRole || $staffHODRole || $staffRegistrarRole || $isUnitHead)
                                        <li class="nav-item">
                                            <a class="nav-link menu-link" href="{{ url('staff/studentFinalClearance') }}" data-key="t-profile">Final Year Student Clearance <span class="badge badge-pill bg-danger" data-key="t-hot">{{  $pendingStudentClearanceCount }} </span></a>
                                        </li>
                                        @endif                                      
                                    </ul>
                                </div>
                            </li>
                            {{-- @endif --}}

                            @if($staffRegistrarRole || $staffVCRole || $staffBursaryRole)
                            <li class="nav-item">
                                <a class="nav-link menu-link" href="#studentMgtSDC" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="studentMgtSDC">
                                    <i class="mdi mdi-account-lock"></i> <span data-key="t-student">Students Disciplinary</span>
                                </a>
                                <div class="collapse menu-dropdown" id="studentMgtSDC">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="{{ url('/staff/expelledStudents') }}" class="nav-link">Expelled Student Record</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ url('/staff/suspendedStudents') }}" class="nav-link">Suspended Student Record</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ url('/staff/deletedStudents') }}" class="nav-link">Deleted Student Record</a>
                                        </li>
                                    </ul>
                                </div>
                            </li> 
                            @endif

                            <li class="nav-item">
                                <a class="nav-link menu-link" href="#studentMGT" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarForms">
                                    <i class="mdi mdi-wrench-clock"></i> <span data-key="t-studentMGT">Leave MGT</span>
                                </a>
                                <div class="collapse menu-dropdown" id="studentMGT">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link menu-link" href="{{ url('staff/leaveApplication') }}" data-key="t-profile">Leave Application</a>
                                        </li>

                                        <li class="nav-item">
                                            <a class="nav-link menu-link" href="{{ url('staff/leaves') }}" data-key="t-profile">My Leaves</a>
                                        </li>

                                        <li class="nav-item">
                                            <a class="nav-link menu-link" href="{{ url('staff/manageLeaves') }}" data-key="t-profile">Manage Leaves</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link menu-link" href="#committeeMgt" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="committeeMgt">
                                    <i class="mdi mdi-account-supervisor-circle-outline"></i> <span data-key="t-committee">Committee MGT</span><span class="badge badge-pill bg-danger" data-key="t-hot">New</span>
                                </a>
                                <div class="collapse menu-dropdown" id="committeeMgt">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                               <a href="{{ url('/staff/committees') }}" class="nav-link"> Committees </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            @if($staffStudentCareRole || $staffBursaryRole || $staffRegistrarRole || $staffVCRole)
                            <li class="nav-item">
                                <a class="nav-link menu-link" href="#communications" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarForms">
                                    <i class="mdi mdi-broadcast"></i> <span data-key="t-communications">Communications</span>
                                </a>
                                <div class="collapse menu-dropdown" id="communications">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link menu-link" href="{{ url('staff/messageStudent') }}" data-key="t-profile">Message Student/Parent</a>
                                        </li>
                                        
                                        <li class="nav-item">
                                            <a class="nav-link menu-link" href="{{ url('staff/messageAllStudent') }}" data-key="t-profile">Message All Student</a>
                                        </li> 

                                        <li class="nav-item">
                                            <a class="nav-link menu-link" href="{{ url('staff/messageAllParent') }}" data-key="t-profile">Message All Parent</a>
                                        </li> 
                                    </ul>
            
                                </div>
                            </li>
                            @endif


                            @if($staffAdmissionOfficerRole || $staffPublicRelationRole || $staffRegistrarRole || $staffVCRole || $staffBursaryRole)
                            <li class="nav-item">
                                <a class="nav-link menu-link" href="#admission" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="admission">
                                    <i class="mdi mdi-account"></i> <span data-key="t-admission">Admissions</span>
                                </a>
                                <div class="collapse menu-dropdown" id="admission">
                                    <ul class="nav nav-sm flex-column">
                                        
                                        <li class="nav-item">
                                            <a href="#undergraduateAdmission" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="undergraduateAdmission" data-key="t-undergraduateAdmission"> {{ $programmeCategory::UNDERGRADUATE }}
                                            </a>
                                            <div class="collapse menu-dropdown" id="undergraduateAdmission">
                                                <ul class="nav nav-sm flex-column">
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/applicants/'.$programmeCategory::UNDERGRADUATE) }}" class="nav-link">Applicants</a>
                                                    </li>
                            
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/students/'.$programmeCategory::UNDERGRADUATE) }}" class="nav-link">Students</a>
                                                    </li>
                
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/matriculants/'.$programmeCategory::UNDERGRADUATE) }}" class="nav-link">Matriculating List</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>

                                        <li class="nav-item">
                                            <a href="#topupAdmission" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="topupAdmission" data-key="t-topupAdmission"> {{ $programmeCategory::TOPUP }}
                                            </a>
                                            <div class="collapse menu-dropdown" id="topupAdmission">
                                                <ul class="nav nav-sm flex-column">
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/applicants/'.$programmeCategory::TOPUP) }}" class="nav-link">Applicants</a>
                                                    </li>
                            
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/students/'.$programmeCategory::TOPUP) }}" class="nav-link">Students</a>
                                                    </li>
                
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/matriculants/'.$programmeCategory::TOPUP) }}" class="nav-link">Matriculating List</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>

                                        <li class="nav-item">
                                            <a href="#pgdAdmission" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="pgdAdmission" data-key="t-pgdAdmission"> {{ $programmeCategory::PGD }}
                                            </a>
                                            <div class="collapse menu-dropdown" id="pgdAdmission">
                                                <ul class="nav nav-sm flex-column">
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/applicants/'.$programmeCategory::PGD) }}" class="nav-link">Applicants</a>
                                                    </li>
                            
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/students/'.$programmeCategory::PGD) }}" class="nav-link">Students</a>
                                                    </li>
                
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/matriculants/'.$programmeCategory::PGD) }}" class="nav-link">Matriculating List</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
    
                                        <li class="nav-item">
                                            <a href="#mastersAdmission" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="mastersAdmission" data-key="t-mastersAdmission"> {{ $programmeCategory::MASTER }}
                                            </a>
                                            <div class="collapse menu-dropdown" id="mastersAdmission">
                                                <ul class="nav nav-sm flex-column">
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/applicants/'.$programmeCategory::MASTER) }}" class="nav-link">Applicants</a>
                                                    </li>
                            
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/students/'.$programmeCategory::MASTER) }}" class="nav-link">Students</a>
                                                    </li>
                
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/matriculants/'.$programmeCategory::MASTER) }}" class="nav-link">Matriculating List</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
    
                                        <li class="nav-item">
                                            <a href="#phdAdmission" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="phdAdmission" data-key="t-phdAdmission"> {{ $programmeCategory::DOCTORATE }}
                                            </a>
                                            <div class="collapse menu-dropdown" id="phdAdmission">
                                                <ul class="nav nav-sm flex-column">
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/applicants/'.$programmeCategory::DOCTORATE) }}" class="nav-link">Applicants</a>
                                                    </li>
                            
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/students/'.$programmeCategory::DOCTORATE) }}" class="nav-link">Students</a>
                                                    </li>
                
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/matriculants/'.$programmeCategory::DOCTORATE) }}" class="nav-link">Matriculating List</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </li> <!-- end Dashboard Menu -->
                            @endif

                            @if($staffDeanRole || $staffSubDeanRole || $staffHODRole || $staffRegistrarRole ||  $staffHRRole || $staffVCRole || $isWorkStudyUnitHeadOrMember)
                            <li class="nav-item">
                                <a class="na<li class="nav-item">
                                    <a class="nav-link menu-link" href="#staffManagement" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="staffManagement">
                                        <i class="mdi mdi-account-supervisor"></i> <span data-key="t-staffManagement">Staff Management</span>
                                    </a>
                                    <div class="collapse menu-dropdown" id="staffManagement">
                                        <ul class="nav nav-sm flex-column">
                                            @if($staffDeanRole || $staffSubDeanRole || $staffHODRole || $staffRegistrarRole || $staffHRRole || $staffVCRole)
                                            <li class="nav-item">
                                                <a href="{{ url('/staff/staff') }}" class="nav-link" data-key="t-basic"> All Staff </a>
                                            </li>
                                            @endif
                                            @if($staffRegistrarRole || $staffHRRole || $staffVCRole)
                                            <li class="nav-item">
                                                <a href="{{('/staff/attendance')}}" class="nav-link" data-key="t-cover"> Staff Attendance </a>
                                            </li>
                                            @endif

                                            @if($staffRegistrarRole || $staffHRRole || $staffVCRole || $isWorkStudyUnitHeadOrMember)
                                            <li class="nav-item">
                                                <a href="#prospectiveStaff" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="prospectiveStaff" data-key="t-prospectiveStaff"> Staff
                                                </a>
                                                <div class="collapse menu-dropdown" id="prospectiveStaff">
                                                    <ul class="nav nav-sm flex-column">
                                                        <li class="nav-item">
                                                            <a href="{{ url('/staff/jobVacancy') }}" class="nav-link" data-key="t-basic"> Job Vacancies </a>
                                                        </li>
                                                        @if($staffRegistrarRole || $staffHRRole || $staffVCRole)
                                                        <li class="nav-item">
                                                            <a href="{{('/staff/prospectiveStaff')}}" class="nav-link" data-key="t-cover">Job Applicants </a>
                                                        </li>
                                                        @endif
                                                        @if($isWorkStudyUnitHeadOrMember)
                                                        <li class="nav-item">
                                                            <a href="{{('/staff/workStudyApplicants')}}" class="nav-link" data-key="t-cover">Work Study Applicants </a>
                                                        </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                </li>
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
                            </li> 
                            @endif


                            @if($staffHODRole || $staffAcademicPlannerRole)
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
                            </li> 
                            @endif

                            @if($staffLevelAdviserRole || $staffHODRole || $staffDeanRole || $staffSubDeanRole || $staffAcademicPlannerRole || $isFacultyOfficer)
                            <li class="nav-item">
                                <a class="nav-link menu-link" href="#courseSettings" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="courseSettings">
                                    <i class="mdi mdi-card"></i> <span  data-key="t-hot">Prog. Management </span><span class="badge badge-pill bg-danger" data-key="t-hot">{{ $staffAcademicPlannerRole ? $pageGlobalData->adviserProgrammesCount : $pageGlobalData->totalPendingRegistrations }} </span>
                                </a>
                                <div class="collapse menu-dropdown" id="courseSettings">
                                    <ul class="nav nav-sm flex-column">
                                        @if($staffLevelAdviserRole || $staffHODRole)

                                        <li class="nav-item">
                                            <a href="#adviserProgrammes" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="adviserProgrammes" data-key="t-adviserProgrammes"> Course Reg MGT <span class="badge badge-pill bg-danger" data-key="t-hot">{{  $staffAcademicPlannerRole ? $pageGlobalData->adviserProgrammesCount : $pageGlobalData->totalPendingRegistrations }} </span>
                                            </a>
                                            <div class="collapse menu-dropdown" id="adviserProgrammes">
                                                <ul class="nav nav-sm flex-column">
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/adviserProgrammes/'.$programmeCategory::UNDERGRADUATE) }}" class="nav-link" data-key="t-basic"> {{ $programmeCategory::UNDERGRADUATE }} </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/adviserProgrammes/'.$programmeCategory::TOPUP) }}" class="nav-link" data-key="t-basic"> {{ $programmeCategory::TOPUP }} </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/adviserProgrammes/'.$programmeCategory::PGD) }}" class="nav-link" data-key="t-basic"> {{ $programmeCategory::PGD }} </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/adviserProgrammes/'.$programmeCategory::MASTER) }}" class="nav-link" data-key="t-basic"> {{ $programmeCategory::MASTER }} </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/adviserProgrammes/'.$programmeCategory::DOCTORATE) }}" class="nav-link" data-key="t-basic"> {{ $programmeCategory::DOCTORATE }} </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>

                                        <li class="nav-item">
                                            <a href="{{ url('/staff/departmentForCourses') }}" class="nav-link">All Courses</a>
                                        </li>
                                        @endif

                                        @if($isFacultyOfficer)
                                        <li class="nav-item">
                                            <a href="{{ url('/staff/studentCourseReg') }}" class="nav-link">Student Course Reg</a>
                                        </li>
                                        @endif

                                        <li class="nav-item">
                                            <a href="#studentCourses" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="studentCourses" data-key="t-adviserProgrammes"> Student Courses 
                                            </a>
                                            <div class="collapse menu-dropdown" id="studentCourses">
                                                <ul class="nav nav-sm flex-column">
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/studentCourses/'.$programmeCategory::UNDERGRADUATE) }}" class="nav-link" data-key="t-basic">{{ $programmeCategory::UNDERGRADUATE }}  </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/studentCourses/'.$programmeCategory::TOPUP) }}" class="nav-link" data-key="t-basic"> {{ $programmeCategory::TOPUP }} </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/studentCourses/'.$programmeCategory::PGD) }}" class="nav-link" data-key="t-basic"> {{ $programmeCategory::PGD }} </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/studentCourses/'.$programmeCategory::MASTER) }}" class="nav-link" data-key="t-basic"> {{ $programmeCategory::MASTER }} </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/studentCourses/'.$programmeCategory::DOCTORATE) }}" class="nav-link" data-key="t-basic"> {{ $programmeCategory::DOCTORATE }} </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>

                                        @if($staffAcademicPlannerRole)
                                        <li class="nav-item">
                                            <a href="#adviserProgrammes" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="adviserProgrammes" data-key="t-adviserProgrammes"> Level Advisers <span class="badge badge-pill bg-danger" data-key="t-hot">{{ $pageGlobalData->adviserProgrammesCount }} </span>
                                            </a>
                                            <div class="collapse menu-dropdown" id="adviserProgrammes">
                                                <ul class="nav nav-sm flex-column">
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/adviserProgrammes/'.$programmeCategory::UNDERGRADUATE) }}" class="nav-link" data-key="t-basic"> {{ $programmeCategory::UNDERGRADUATE }} </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/adviserProgrammes/'.$programmeCategory::TOPUP) }}" class="nav-link" data-key="t-basic"> {{ $programmeCategory::TOPUP }} </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/adviserProgrammes/'.$programmeCategory::PGD) }}" class="nav-link" data-key="t-basic"> {{ $programmeCategory::PGD }} </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/adviserProgrammes/'.$programmeCategory::MASTER) }}" class="nav-link" data-key="t-basic"> {{ $programmeCategory::MASTER }} </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/adviserProgrammes/'.$programmeCategory::DOCTORATE) }}" class="nav-link" data-key="t-basic"> {{ $programmeCategory::DOCTORATE }} </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>

                                        @endif
                                    </ul>
                                </div>
                            </li>
                            @endif


                            @if($staffVCRole || $staffRegistrarRole || $staffAcademicPlannerRole || $staffExamOfficerRole || $staffHODRole || $staffStudentCareRole || $staffDeanRole || $staffSubDeanRole)
                            <li class="nav-item">
                                <a class="nav-link menu-link" href="#resultMgt" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="resultMgt">
                                    <i class="mdi mdi-credit-card-search-outline"></i> <span data-key="t-result">Result Management</span>
                                </a>
                                <div class="collapse menu-dropdown" id="resultMgt">
                                    <ul class="nav nav-sm flex-column">

                                        <li class="nav-item">
                                            <a href="{{ url('/staff/getStudentResults') }}" class="nav-link">Students Results</a>
                                        </li>

                                        <li class="nav-item">
                                            <a href="{{ url('/staff/getStudentResultSummary') }}" class="nav-link">Students Results Summary</a>
                                        </li>
                                    </ul>
                                </div>
                            </li> <!-- end Dashboard Menu -->
                            @endif


                            @if($staffStudentCareRole)
                            <li class="nav-item">
                                <a class="nav-link menu-link" href="#studentCare" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="studentCare">
                                    <i class="mdi mdi-account-heart"></i> <span data-key="t-student">Student Care</span> <span class="badge badge-pill bg-danger" data-key="t-hot">{{ !empty($pageGlobalData->exitApplicationCount) ? $pageGlobalData->exitApplicationCount : 0 }}</span>
                                </a>
                                <div class="collapse menu-dropdown" id="studentCare">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="{{ url('/staff/studentExits') }}" class="nav-link">Student Exit <span class="badge badge-pill bg-danger" data-key="t-hot">{{ !empty($pageGlobalData->exitApplicationCount) ? $pageGlobalData->exitApplicationCount : 0 }}</span></a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ url('/staff/verifyStudentExits') }}" class="nav-link">Verify Student Exit</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#studentHostelMgt" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="studentHostelMgt" data-key="t-studentHostelMgt"> Student Hostel Mgt
                                            </a>
                                            <div class="collapse menu-dropdown" id="studentHostelMgt">
                                                <ul class="nav nav-sm flex-column">
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/hostel') }}" class="nav-link" data-key="t-basic"> Hostel </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/hostelType') }}" class="nav-link" data-key="t-basic"> Room Type </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/allocations') }}" class="nav-link" data-key="t-basic"> Allocations </a>
                                                    </li>
                                                </ul>
                                            </div>
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
                                            <a href="#bills" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="bills" data-key="t-bills"> Bills</a>
                                            </a>
                                            <div class="collapse menu-dropdown" id="bills">
                                                <ul class="nav nav-sm flex-column">
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/payments/'.$programmeCategory::UNDERGRADUATE) }}" class="nav-link"> {{ $programmeCategory::UNDERGRADUATE }} </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/payments/'.$programmeCategory::TOPUP) }}" class="nav-link"> {{ $programmeCategory::TOPUP }} </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/payments/'.$programmeCategory::PGD) }}" class="nav-link"> {{ $programmeCategory::PGD }} </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/payments/'.$programmeCategory::MASTER) }}" class="nav-link"> {{ $programmeCategory::MASTER }} </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="{{ url('/staff/payments/'.$programmeCategory::DOCTORATE) }}" class="nav-link"> {{ $programmeCategory::DOCTORATE }} </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{('/staff/chargeStudent')}}" class="nav-link">Payment/Charge </a>
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
                        @endif
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


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

                    var staffVCRole = "<?php echo $staffVCRole ?>";
                    var staffBursaryRole = "<?php echo $staffBursaryRole ?>";
                    var staffDepartmentId =  "<?php echo $staff->department_id ?>";
                    var staffStudentCareRole = "<?php echo $staffStudentCareRole ?>";
                    var staffDeanRole = "<?php echo $staffDeanRole ?>";
                    var staffSubDeanRole = "<?php echo $staffSubDeanRole ?>";
                    var staffRegistrarRole = "<?php echo $staffRegistrarRole ?>";
                    var staffAcademicPlannerRole = "<?php echo $staffAcademicPlannerRole ?>";
                    
                    $.each(response.data, function (index, department) {
                        if (!staffVCRole && 
                        !staffBursaryRole && 
                        !staffStudentCareRole && 
                        !staffDeanRole && 
                        !staffSubDeanRole &&
                        !staffRegistrarRole && 
                        !staffAcademicPlannerRole &&
                        staffDepartmentId == department.id) {
                            departmentSelect.append($('<option>', {
                                value: department.id,
                                text: department.name
                            }));
                        }
                    });

                    if (staffVCRole || staffBursaryRole || staffStudentCareRole || staffDeanRole || staffSubDeanRole || staffRegistrarRole || staffAcademicPlannerRole) {
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
            const selectedProgrammeCategory = $('#programme_category').val();

            if (selectedDepartment !== '') {
                axios.get("{{ url('/staff/getProgrammes') }}/" + selectedDepartment, {
                    params: {
                        programme_category_id: selectedProgrammeCategory
                    }
                })
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
                    console.error("Error fetching programmes:", error);
                });
            }
        }
    
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
        document.getElementById('copyButton').addEventListener('click', function() {
            // Select the link text
            var link = document.getElementById('myLink');
            var range = document.createRange();
            range.selectNode(link);
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);
    
            // Copy the selected text to the clipboard
            try {
                document.execCommand('copy');
                alert('Link copied to clipboard');
            } catch (err) {
                console.error('Unable to copy link: ', err);
            }
    
            // Deselect the text
            window.getSelection().removeAllRanges();
        });
    </script> 
    <script>
        $(document).ready(function() {
            $('.selectWithSearch').select2();
        });

        $(document).ready(function() {
            $('.selectRoom').select2();
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#buttons-datatables1').DataTable({
                dom: 'Bfrtip',
                pageLength: 20,
                lengthMenu: [ [10, 20, 50, -1], [10, 20, 50, "All"] ],  
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
    
            $('#buttons-datatables2').DataTable({
                dom: 'Bfrtip',
                pageLength: 20,
                lengthMenu: [ [10, 20, 50, -1], [10, 20, 50, "All"] ],  
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
    
            $('#buttons-datatables3').DataTable({
                dom: 'Bfrtip',
                pageLength: 20,  
                lengthMenu: [ [10, 20, 50, -1], [10, 20, 50, "All"] ],
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
    
            $('#buttons-datatables4').DataTable({
                dom: 'Bfrtip',
                pageLength: 20, 
                lengthMenu: [ [10, 20, 50, -1], [10, 20, 50, "All"] ], 
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });

            $('#buttons-datatables5').DataTable({
                dom: 'Bfrtip',
                pageLength: 20,
                lengthMenu: [ [10, 20, 50, -1], [10, 20, 50, "All"] ],  
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });

            $('#buttons-datatables6').DataTable({
                dom: 'Bfrtip',
                pageLength: 20,
                lengthMenu: [ [10, 20, 50, -1], [10, 20, 50, "All"] ],  
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });

            $('#buttons-datatables7').DataTable({
                dom: 'Bfrtip',
                pageLength: 20, 
                lengthMenu: [ [10, 20, 50, -1], [10, 20, 50, "All"] ], 
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });

            $('#buttons-result').DataTable({
                dom: 'Bfrtip',
                pageLength: 20, 
                lengthMenu: [ [10, 20, 50, -1], [10, 20, 50, "All"] ], 
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
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
        document.addEventListener('DOMContentLoaded', function () {
            const validateButton = document.querySelector('.validate-button');
            const hiddenFields = document.querySelector('.hidden-fields');
            const studentIdInput = document.querySelector('#student_id');
            const StudentGender = document.querySelector('#studentGender')
            const matricNumberInput = document.querySelector('#matricNumber');

            hiddenFields.style.display = 'none';

            validateButton.addEventListener('click', function () {
                const matricNumber = document.querySelector('#matricNumber').value;

                if(matricNumber === '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Matric Number is required',
                        text: 'Fill in your matric number',
                    });

                    return false;
                }

                // Send a POST request to the Laravel route
                axios.post('/student/getStudent', { matric_number: matricNumber })
                    .then(function (response) {
                        if (response.data.status === 'record_not_found') {
                            // Show a SweetAlert for record not found
                            Swal.fire({
                                icon: 'error',
                                title: 'Record Not Found',
                                text: 'The student record was not found.',
                            });
                        } else {
                            // Set the student ID and show the hidden fields
                            studentIdInput.value = response.data.student.id;
                            StudentGender.value = response.data.student.applicant.gender;
                            hiddenFields.style.display = 'flex';
                            validateButton.style.display = 'none';
                            matricNumberInput.setAttribute('disabled', 'disabled');
                        }
                    })
                    .catch(function (error) {
                        console.error(error);
                    });
            });
        });
    </script>
    <script>
        function handleCampusChange(event) {
            const selectedCampus = event.target.value;
            const gender = $('.gender').val();;
            const hostelSelect = $('#hostel');

            if (selectedCampus !== '') {
                axios.post("{{ url('/student/getHostels') }}", {
                    campus: selectedCampus, 
                    gender: gender
                })
                .then(function (response) {
                    hostelSelect.empty().append($('<option>', {
                        value: '',
                        text: '--Select--'
                    }));

                    $.each(response.data, function (index, hostel) {
                        hostelSelect.append($('<option>', {
                            value: hostel.id,
                            text: hostel.name
                        }));
                    });
                })
                .catch(function (error) {
                    console.error("Error fetching hostels:", error);
                });
            } else {
                hostelSelect.empty().append($('<option>', {
                    value: '',
                    text: '--Select--'
                }));
            }
        }

        function handleHostelChange(event) {
            const selectedCampus = $('#campus').val(); 
            const gender = $('.gender').val();
            const hostel = $('#hostel').val(); 
            const roomTypeSelect = $('#roomType');

            if (selectedCampus !== '') {
                axios.post("{{ url('/student/getRoomTypes') }}", {
                    campus: selectedCampus, 
                    gender: gender,
                    hostelId: hostel
                })
                .then(function (response) {
                    roomTypeSelect.empty().append($('<option>', {
                        value: '',
                        text: '--Select--'
                    }));

                    $.each(response.data, function (index, roomType) {
                        const formattedAmount = (roomType.amount / 100).toLocaleString('en-NG', { style: 'currency', currency: 'NGN' });

                        roomTypeSelect.append($('<option>', {
                            value: roomType.id,  
                            text: `${roomType.name} (${roomType.capacity} Bed Spaces) (${formattedAmount})`
                        }));
                    });
                })
                .catch(function (error) {
                    console.error("Error fetching room types:", error);
                });
            } else {
                roomTypeSelect.empty().append($('<option>', {
                    value: '',
                    text: '--Select--'
                }));
            }
        }

        function handleRoomTypeChange(event) {
            const hostel = $('#hostel').val(); 
            const typeId = event.target.value;
            const roomSelect = $('#room');

            if (typeId !== '') {
                axios.post("{{ url('/student/getRooms') }}", {
                    typeId: typeId, 
                    hostelId: hostel
                })
                .then(function (response) {
                    roomSelect.empty().append($('<option>', {
                        value: '',
                        text: '--Select--'
                    }));

                    if (response.data.length === 0) {
                        // Trigger SweetAlert when no rooms are found
                        Swal.fire({
                            icon: 'warning',
                            title: 'No Rooms Available',
                            text: 'No rooms available for the selected room type.',
                        });
                    } else {
                        $.each(response.data, function (index, room) {
                            roomSelect.append($('<option>', {
                                value: room.id,  
                                text: room.number
                            }));
                        });
                    }
                })
                .catch(function (error) {
                    console.error("Error fetching rooms:", error);
                });
            } else {
                roomSelect.empty().append($('<option>', {
                    value: '',
                    text: '--Select--'
                }));
            }
        }

    </script>
</body>

</html>
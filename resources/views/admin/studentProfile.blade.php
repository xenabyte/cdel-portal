@extends('admin.layout.dashboard')
@php

$qrcode = 'https://quickchart.io/chart?chs=300x300&cht=qr&chl='.env('APP_URL').'/studentDetails/'.$student->slug;
$name = $student->applicant->lastname.' '.$student->applicant->othernames;
$transactions = $student->transactions()->orderBy('created_at', 'desc')->get();
$studentRegistrations = $student->courseRegistrationDocument()->orderBy('created_at', 'desc')->take(10)->get();
$failedCourses = $student->registeredCourses()->where('grade', 'F')->where('re_reg', null)->get();
$studentAdvisoryData = (object) $student->getAcademicAdvisory();

@endphp
@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Student</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Student</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-lg-12">
        <div class="card mt-n4 mx-n4">
            <div class="{{$student->isExpelled() ?"bg-soft-danger": "bg-soft-primary"}}">
                <div class="card-body pb-0 px-4">
                    <div class="row mb-3">
                        <div class="col-md">
                            <div class="row align-items-center g-3">
                                <div class="col-md-auto">
                                    <div class="avatar-md">
                                        <img src="{{ !empty($student->image) ? asset($student->image) : asset('assets/images/users/user-dummy-img.jpg') }}" alt="" class="img-thumbnail rounded-circle avatar-md">
                                    </div>
                                </div>
                                <div class="col-md">
                                    <div>
                                        <h4 class="fw-bold">{{$name}} <span class="badge 
                                            @if($student->academic_status == 'Good Standing') bg-success
                                            @elseif($student->academic_status == 'Suspended' || $student->academic_status == 'Probation') bg-warning
                                            @elseif($student->academic_status == 'Expelled') bg-danger
                                            @else bg-secondary
                                            @endif">
                                            {{ $student->academic_status }}
                                        </span></h4>
                                        <div class="hstack gap-3 flex-wrap">
                                            <div><i class="ri-building-line align-bottom me-1"></i> {{ $student->programme->name }}</div>
                                            <div class="vr"></div>
                                            <div>CGPA: <span class="fw-medium">{{ $student->cgpa }}</span></div>
                                            <div class="vr"></div>
                                            <div>Level: <span class="fw-medium">{{ $student->academicLevel->level }} Level</span></div>
                                            <div class="vr"></div>
                                            <strong>Support Code:</strong> <span class="text-danger">{{ $student->applicant->id }}-ST{{ sprintf("%03d", $student->id) }}</span> 
                                        </div>
                                        @if($student->studyCenter)
                                        <div class="hstack gap-3 flex-wrap">
                                            <div><i class="ri-building-line align-bottom me-1"></i> {{ $student->studyCenter ? $student->studyCentre->center_name : null }}</div>
                                            <div class="vr"></div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-auto">
                            <div class="hstack gap-1 flex-wrap">
                                <form action="{{ url('admin/resendGuardianOnboarding') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                                    <button type="submit" class="btn btn-success">Send Guardian Onboarding Mail</button>
                                </form>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#assignStudyCenter">Assign Study Center</button>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#expelStudent">Expel Student</button>
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#suspendStudent">Suspend Student</button>

                                @if(empty($student->deleted_at))
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteStudent">Delete Student</button>
                                @else             
                                   <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#enableStudent">Enable Student</button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <ul class="nav nav-tabs-custom border-bottom-0" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active fw-semibold" data-bs-toggle="tab" href="#project-overview" role="tab">
                                Overview
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#suspension" role="tab">
                                Student Suspension
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#expulsion" role="tab">
                                Student Expulsion
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#settings" role="tab">
                                Settings
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#partners" role="tab">
                                Referred Students
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- end card body -->
            </div>
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end row -->
<div class="row">
    <div class="col-lg-12">
        <div class="tab-content text-muted">
            <div class="tab-pane fade show active" id="project-overview" role="tabpanel">
                <div class="row">
                    <div class="col-lg-8">
                        
                        <div class="card">
                            <div class="card-header border-0 align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Documents</h4>
                            </div><!-- end card header -->
                
                            <div class="card-body border-top border-top-dashed">
                                <div class="vstack gap-2">
                                    <div class="border rounded border-dashed p-2">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar-sm">
                                                    <div class="avatar-title bg-light text-secondary rounded fs-24 shadow">
                                                        <i class="ri-file-pdf-fill"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 overflow-hidden">
                                                <h5 class="fs-13 mb-1"><a href="{{ asset($student->admission_letter) }}" class="text-body text-truncate d-block">Admission Letter</a></h5>
                                            </div>
                                            <div class="flex-shrink-0 ms-2">
                                                <div class="d-flex gap-1">
                                                    @if(!empty($student->admission_letter))
                                                    <a href="{{ asset($student->admission_letter) }}"  class="btn btn-icon text-muted btn-sm fs-18 shadow-none"><i class="ri-download-2-line"></i></a>
                                                    @else
                                                    <form action="{{ url('admin/generateAdmissionLetter') }}" method="POST">
                                                        @csrf
                                                        <input name="applicant_id" type="hidden" value="{{ $student->user_id }}">
                                                        <button type="submit" id="submit-button" class="btn btn-icon text-muted btn-sm fs-18 shadow-none" style="background-color: transparent; border: none;">
                                                            <i class="ri-download-2-line"></i>
                                                        </button>
                                                    </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                
                                    @if(strtolower($student->applicant->application_type) == 'utme')
                                    <div class="border rounded border-dashed p-2">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar-sm">
                                                    <div class="avatar-title bg-light text-secondary rounded fs-24 shadow">
                                                        <i class="ri-image-fill"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 overflow-hidden">
                                                <h5 class="fs-13 mb-1"><a href="{{ asset($student->applicant->utme) }}" class="text-body text-truncate d-block">Jamb Result</a></h5>
                                            </div>
                                            <div class="flex-shrink-0 ms-2">
                                                <div class="d-flex gap-1">
                                                    <a href="{{ asset($student->applicant->utme) }}"  class="btn btn-icon text-muted btn-sm fs-18 shadow-none"><i class="ri-download-2-line"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @else
                                    <div class="border rounded border-dashed p-2">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar-sm">
                                                    <div class="avatar-title bg-light text-secondary rounded fs-24 shadow">
                                                        <i class="ri-image-fill"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 overflow-hidden">
                                                <h5 class="fs-13 mb-1"><a href="{{ asset($student->applicant->de_result) }}" class="text-body text-truncate d-block">Direct Entry/Prev Institution Result</a></h5>
                                            </div>
                                            <div class="flex-shrink-0 ms-2">
                                                <div class="d-flex gap-1">
                                                    <a href="{{ asset($student->applicant->de_result) }}"  class="btn btn-icon text-muted btn-sm fs-18 shadow-none"><i class="ri-download-2-line"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                
                                    <div class="border rounded border-dashed p-2">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar-sm">
                                                    <div class="avatar-title bg-light text-secondary rounded fs-24 shadow">
                                                        <i class="ri-image-fill"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 overflow-hidden">
                                                <h5 class="fs-13 mb-1"><a href="{{ asset($student->applicant->olevel_1) }}" class="text-body text-truncate d-block">Olevel Result</a></h5>
                                            </div>
                                            <div class="flex-shrink-0 ms-2">
                                                <div class="d-flex gap-1">
                                                    <a href="{{ asset($student->applicant->olevel_1) }}"  class="btn btn-icon text-muted btn-sm fs-18 shadow-none"><i class="ri-download-2-line"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                
                                    @if(!empty($student->applicant->olevel_2))
                                    <div class="border rounded border-dashed p-2">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar-sm">
                                                    <div class="avatar-title bg-light text-secondary rounded fs-24 shadow">
                                                        <i class="ri-image-fill"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 overflow-hidden">
                                                <h5 class="fs-13 mb-1"><a href="{{ asset($student->applicant->olevel_2) }}" class="text-body text-truncate d-block">Olevel Result(Second Sitting)</a></h5>
                                            </div>
                                            <div class="flex-shrink-0 ms-2">
                                                <div class="d-flex gap-1">
                                                    <a href="{{ asset($student->applicant->olevel_2) }}"  class="btn btn-icon text-muted btn-sm fs-18 shadow-none"><i class="ri-download-2-line"></i></a> 
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>


                        <div class="card">
                            <div class="card-body">
                                <div class="text-muted">
                                    <h6 class="mb-3 fw-semibold text-uppercase">Transactions</h6>
                                    <div class="border-top border-top-dashed pt-3">
                                        <div class="table-responsive">
                                            <!-- Bordered Tables -->
                                            <table id="buttons-datatables" class="display table table-stripped" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Id</th>
                                                        <th scope="col">Reference</th>
                                                        <th scope="col">Amount(₦)</th>
                                                        <th scope="col">Payment For</th>
                                                        <th scope="col">Session</th>
                                                        <th scope="col">Payment Gateway</th>
                                                        <th scope="col">Status</th>
                                                        <th scope="col">Payment Date</th>
                                                        <th scope="col"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($transactions as $transaction)
                                                    <tr>
                                                        <th scope="row">{{ $loop->iteration }}</th>
                                                        <td>{{ $transaction->reference }}</td>
                                                        <td>₦{{ number_format($transaction->amount_payed/100, 2) }} </td>
                                                        <td>{{ !empty($transaction->paymentType) ? ($transaction->paymentType->type == 'General Fee' ? $transaction->paymentType->title : $transaction->paymentType->type) : 'Wallet Deposit' }} </td>
                                                        <td>{{ $transaction->session }}</td>
                                                        <td>{{ $transaction->payment_method }}</td>
                                                        <td><span class="badge badge-soft-{{ $transaction->status == 1 ? 'success' : 'warning' }}">{{ $transaction->status == 1 ? 'Paid' : 'Pending' }}</span></td>
                                                        <td>{{ $transaction->status == 1 ? $transaction->updated_at : null }} </td>
                                                        <td>
                                                            @if($transaction->status == 1)
                                                            <form action="{{ url('/admin/generateInvoice') }}" method="post" enctype="multipart/form-data">
                                                                @csrf
                                                                <input name="payment_id" type="hidden" value="{{!empty($transaction->paymentType)?$transaction->paymentType->id:0}}">
                                                                <input name="student_id" type="hidden" value="{{$transaction->student_id}}">
                                                                <input name="session" type="hidden" value="{{ $transaction->session }}">
                                                                <button type="submit" id="submit-button" class="btn btn-primary"><i class="mdi mdi-printer"></i></button>
                                                            </form>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->

                        <div class="card">
                            <div class="card-body">
                                <div class="text-center">
                                    <div class="row justify-content-center">
                                        <div class="col-lg-9">
                                            <h4 class="mt-4 fw-semibold">Generate Examination result</h4>
                                            <p class="text-muted mt-3"></p>
                                    
                                            <div class="mt-4">
                                                <form action="{{ url('/admin/generateResult') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="examSetting_id" value="{{ !empty($pageGlobalData->examSetting)?$pageGlobalData->examSetting->id:null }}">
                                                    <input type="hidden" name="academic_session" value="{{ $pageGlobalData->sessionSetting->academic_session }}">
                                                    <input type="hidden" name="student_id" value="{{ $student->id }}">

                                                    <div class="row g-3">
                                                        
                                                        <div class="col-lg-12">
                                                            <div class="form-floating">
                                                                <select class="form-select" id="level" name="level_id" aria-label="level">
                                                                    <option value="" selected>--Select--</option>
                                                                    @foreach($academicLevels as $academicLevel)
                                                                        @if($academicLevel->id <= $student->level_id)
                                                                            <option value="{{ $academicLevel->id }}">{{ $academicLevel->level }} Level</option>
                                                                        @endif
                                                                    @endforeach
                                                                </select>
                                                                <label for="level">Academic Level</label>
                                                            </div>
                                                        </div>
                        
                                                        <div class="col-lg-12">
                                                            <div class="form-floating">
                                                                <select class="form-select" id="semester" name="semester" aria-label="semester">
                                                                    <option value="" selected>--Select--</option>
                                                                    <option value="1">Harmattan Semester</option>
                                                                    <option value="2">Rain Semester</option>
                                                                </select>
                                                                <label for="semester">Semester</label>
                                                            </div>
                                                        </div>
                        
                        
                                                        <div class="col-lg-12">
                                                            <div class="form-floating">
                                                                <select class="form-select" id="session" name="session" aria-label="Academic Session">
                                                                    <option value="" selected>--Select--</option>
                                                                    @foreach($sessions as $session)<option value="{{ $session->year }}">{{ $session->year }}</option>@endforeach
                                                                </select>
                                                                <label for="session">Academic Session</label>
                                                            </div>
                                                        </div>
                
                                                        <button type="submit" id="submit-button" class="btn btn-fill btn-primary btn-lg btn-block mb-5">Generate Result</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ene col -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body p-4">
                                <div>
                                    <div class="flex-shrink-0 avatar-md mx-auto">
                                        <div class="avatar-title bg-light rounded">
                                            <img src="{{empty($student->image)?asset('assets/images/users/user-dummy-img.jpg'):asset($student->image)}}" alt="" height="50" />
                                        </div>
                                    </div>
                                    <div class="mt-4 text-center">
                                        <h5 class="mb-1">{{$name}}</h5>
                                        <p class="text-muted">{{ $student->programme->name }} <br>
                                            <strong>Matric Number:</strong> {{ $student->matric_number }}<br>
                                            <strong>Wifi Username:</strong> {{ $student->bandwidth_username }}<br>
                                            <strong>Email:</strong> {{ $student->email }}<br>
                                            <strong>Phone Number:</strong> {{ $student->applicant->phone_number }}<br>
                                            <strong>Address:</strong> {{ $student->applicant->address }}<br>
                                            @if(env('WALLET_STATUS'))<a class="dropdown-item" href="#"><i class="mdi mdi-wallet text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Balance : <b>₦{{ number_format($student->amount_balance/100, 2) }}</b></span></a>@endif
                                        </p>
                                        <p class="text-muted border-top border-top-dashed pt-2">
                                            <strong>Programme Category:</strong> {{ $student->programmeCategory->category }} Programme<br>
                                            <strong>Department:</strong> {{ $student->department->name }}<br>
                                            <strong>Faculty:</strong> {{ $student->faculty->name }}<br>
                                            <strong>Jamb Reg. Number:</strong> {{ $student->applicant->jamb_reg_no }} <br>
                                            <strong>Academic Level:</strong> <span class="text-primary">{{ $student->level_id * 100 }} Level</span><br>
                                            <strong>Academic session:</strong> {{ $student->academic_session }}</span>
                                            <br>
                                            @if($student->level_id >= $student->programme->duration && !$student->is_passed_out)
                                            <span class="text-warning"><strong>Graduating Set</strong></span> <br>
                                            @endif
                                            <strong>Support Code:</strong> <span class="text-danger">{{ $student->applicant->id }}-ST{{ sprintf("%03d", $student->id) }}</span> 
                                        </p>
                                        <p class="text-muted border-top border-top-dashed pt-2">
                                            <strong>CGPA:</strong> {{ $student->cgpa }} <br>
                                            <strong>Class:</strong> {{ $student->degree_class }}<br>
                                        </p>
                                        <p class="text-muted border-top border-top-dashed pt-2 text-start">
                                            @if($failedCourses->count() > 0)<strong class="text-danger">Failed Courses:</strong> <span class="text-danger">@foreach($failedCourses as $failedCourse) {{ $failedCourse->course_code.',' }} @endforeach</span> @endif <br>
                                            <strong>Promotion Eligibility:</strong> {{ $studentAdvisoryData->promotion_eligible?'You are eligible to promote':'You are not eligible to promote' }}<br>
                                            <strong>Promotion Message:</strong> {{ $studentAdvisoryData->promotion_message }}<br>
                                            <strong>GPA Trend:</strong> {{ $studentAdvisoryData->trajectory_analysis['cgpa_trend'] }}<br>
                                            <strong>CGPA Trajectory Analysis:</strong> {{ $studentAdvisoryData->trajectory_analysis['academic_risk'] }}<br>
                                            <strong>Course Strength:</strong> @foreach($studentAdvisoryData->trajectory_analysis['strengths'] as $strength) {{ $strength.', ' }} @endforeach<br>
                                            <strong>Course Weakness:</strong> @foreach($studentAdvisoryData->trajectory_analysis['weaknesses'] as $weakness) {{ $weakness.', ' }} @endforeach<br>
                                            <strong>Tips:</strong> @foreach($studentAdvisoryData->trajectory_analysis['tips'] as $tips) {{ $tips }} @endforeach<br>
                                        </p>
                                    </div>
                                    <div class="table-responsive border-top border-top-dashed">
                                        <table class="table mb-0 table-borderless">
                                            <tbody>
                                                <tr>
                                                    <th><span class="fw-medium">Department:</span></th>
                                                    <td>{{ $student->department->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Faculty:</span></th>
                                                    <td>{{ $student->faculty->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Email:</span></th>
                                                    <td>{{ $student->email }}</td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Contact No.:</span></th>
                                                    <td>{{ $student->applicant->phone_number }}</td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Address:</span></th>
                                                    <td>{!! $student->applicant->address !!}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!--end card-body-->
                            <div class="card-body p-4 border-top border-top-dashed">
                                <div class="avatar-title bg-light rounded">
                                    <img src="{{ $qrcode }}" style="border: 1px solid black;">
                                </div>
                            </div>
                
                            @if(!empty($student->applicant->guardian))
                            <div class="card-body border-top border-top-dashed p-4">
                                <div>
                                    <h6 class="text-muted text-uppercase fw-semibold mb-4">Guardian Info</h6>
                                    <div class="table-responsive">
                                        <table class="table mb-0 table-borderless">
                                            <tbody>
                                                <tr>
                                                    <th><span class="fw-medium">SN</span></th>
                                                    <td class="text-danger">#{{ $student->applicant->guardian->id }}</td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Name</span></th>
                                                    <td>{{ $student->applicant->guardian->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Email</span></th>
                                                    <td>{{ $student->applicant->guardian->email }}</td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Contact No.</span></th>
                                                    <td>{{ $student->applicant->guardian->phone_number }}</td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Address</span></th>
                                                    <td>{!! $student->applicant->guardian->address !!}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>

            <div class="tab-pane fade" id="suspension" role="tabpanel">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <div class="text-muted">
                                    <h4 class="card-title mb-0 flex-grow-1">Student Suspension Records</h4>
                                    <div class="border-top border-top-dashed pt-3">
                                        <div class="table-responsive">
                                            <!-- Bordered Tables -->
                                            @if($student->suspensions->isNotEmpty())
                                                <h4 class="mt-4">Suspension Record</h4>
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">Reason</th>
                                                            <th scope="col">Start Date</th>
                                                            <th scope="col">End Date</th>
                                                            <th scope="col">File</th>
                                                            <th scope="col">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($student->suspensions as $suspension)
                                                        <tr>
                                                            <td>{!! $suspension->reason !!}</td>
                                                            <td>{{ $suspension->start_date }}</td>
                                                            <td>
                                                                @if($suspension->end_date)
                                                                    {{ $suspension->end_date }}
                                                                @else
                                                                    <span class="badge bg-warning">Ongoing</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($suspension->file)
                                                                    <a href="{{ asset($suspension->file) }}" class="btn btn-sm btn-info" target="_blank">
                                                                        <i class="ri-file-download-line"></i> View File
                                                                    </a>
                                                                @else
                                                                    <span class="text-muted">No file</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <a href="{{ url('admin/viewSuspension/'.$suspension->slug) }}" class="btn btn-sm btn-info">
                                                                    <i class="ri-eye-line"></i> View Details
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <p class="text-muted">No suspension records found.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- ene col -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body p-4">
                                <div>
                                    <div class="flex-shrink-0 avatar-md mx-auto">
                                        <div class="avatar-title bg-light rounded">
                                            <img src="{{empty($student->image)?asset('assets/images/users/user-dummy-img.jpg'):asset($student->image)}}" alt="" height="50" />
                                        </div>
                                    </div>
                                    <div class="mt-4 text-center">
                                        <h5 class="mb-1">{{$name}}</h5>
                                        <p class="text-muted">{{ $student->programme->name }} <br>
                                            <strong>Matric Number:</strong> {{ $student->matric_number }}<br>
                                            <strong>Wifi Username:</strong> {{ $student->bandwidth_username }}<br>
                                            <strong>Email:</strong> {{ $student->email }}<br>
                                            <strong>Phone Number:</strong> {{ $student->applicant->phone_number }}<br>
                                            <strong>Address:</strong> {{ $student->applicant->address }}<br>
                                        </p>
                                        <p class="text-muted border-top border-top-dashed pt-2">
                                            <strong>Programme Category:</strong> {{ $student->programmeCategory->category }} Programme<br>
                                            <strong>Department:</strong> {{ $student->department->name }}<br>
                                            <strong>Faculty:</strong> {{ $student->faculty->name }}<br>
                                            <strong>Jamb Reg. Number:</strong> {{ $student->applicant->jamb_reg_no }} <br>
                                            <strong>Academic Level:</strong> <span class="text-primary">{{ $student->level_id * 100 }} Level</span><br>
                                            <strong>Academic session:</strong> {{ $student->academic_session }}</span>
                                            <br>
                                            @if($student->level_id >= $student->programme->duration && !$student->is_passed_out)
                                            <span class="text-warning"><strong>Graduating Set</strong></span> <br>
                                            @endif
                                            <strong>Support Code:</strong> <span class="text-danger">{{ $student->applicant->id }}-ST{{ sprintf("%03d", $student->id) }}</span> 
                                        </p>
                                        <p class="text-muted border-top border-top-dashed pt-2">
                                            <strong>CGPA:</strong> {{ $student->cgpa }} <br>
                                            <strong>Class:</strong> {{ $student->degree_class }}<br>
                                        </p>
                                        <p class="text-muted border-top border-top-dashed pt-2 text-start">
                                            @if($failedCourses->count() > 0)<strong class="text-danger">Failed Courses:</strong> <span class="text-danger">@foreach($failedCourses as $failedCourse) {{ $failedCourse->course_code.',' }} @endforeach</span> @endif <br>
                                            <strong>Promotion Eligibility:</strong> {{ $studentAdvisoryData->promotion_eligible?'You are eligible to promote':'You are not eligible to promote' }}<br>
                                            <strong>Promotion Message:</strong> {{ $studentAdvisoryData->promotion_message }}<br>
                                            <strong>GPA Trend:</strong> {{ $studentAdvisoryData->trajectory_analysis['cgpa_trend'] }}<br>
                                            <strong>CGPA Trajectory Analysis:</strong> {{ $studentAdvisoryData->trajectory_analysis['academic_risk'] }}<br>
                                            <strong>Course Strength:</strong> @foreach($studentAdvisoryData->trajectory_analysis['strengths'] as $strength) {{ $strength.', ' }} @endforeach<br>
                                            <strong>Course Weakness:</strong> @foreach($studentAdvisoryData->trajectory_analysis['weaknesses'] as $weakness) {{ $weakness.', ' }} @endforeach<br>
                                            <strong>Tips:</strong> @foreach($studentAdvisoryData->trajectory_analysis['tips'] as $tips) {{ $tips }} @endforeach<br>
                                        </p>
                                    </div>
                                    <div class="table-responsive border-top border-top-dashed">
                                        <table class="table mb-0 table-borderless">
                                            <tbody>
                                                <tr>
                                                    <th><span class="fw-medium">Department:</span></th>
                                                    <td>{{ $student->department->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Faculty:</span></th>
                                                    <td>{{ $student->faculty->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Email:</span></th>
                                                    <td>{{ $student->email }}</td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Contact No.:</span></th>
                                                    <td>{{ $student->applicant->phone_number }}</td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Address:</span></th>
                                                    <td>{!! $student->applicant->address !!}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!--end card-body-->
                            <div class="card-body p-4 border-top border-top-dashed">
                                <div class="avatar-title bg-light rounded">
                                    <img src="{{ $qrcode }}" style="border: 1px solid black;">
                                </div>
                            </div>
                
                            @if(!empty($student->applicant->guardian))
                            <div class="card-body border-top border-top-dashed p-4">
                                <div>
                                    <h6 class="text-muted text-uppercase fw-semibold mb-4">Guardian Info</h6>
                                    <div class="table-responsive">
                                        <table class="table mb-0 table-borderless">
                                            <tbody>
                                                <tr>
                                                    <th><span class="fw-medium">SN</span></th>
                                                    <td class="text-danger">#{{ $student->applicant->guardian->id }}</td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Name</span></th>
                                                    <td>{{ $student->applicant->guardian->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Email</span></th>
                                                    <td>{{ $student->applicant->guardian->email }}</td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Contact No.</span></th>
                                                    <td>{{ $student->applicant->guardian->phone_number }}</td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Address</span></th>
                                                    <td>{!! $student->applicant->guardian->address !!}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>

            <div class="tab-pane fade" id="expulsion" role="tabpanel">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <div class="text-muted">
                                    <h4 class="card-title mb-0 flex-grow-1">Student Expulsion Records</h4>
                                    <div class="border-top border-top-dashed pt-3">
                                        <div class="table-responsive">
                                            <!-- Bordered Tables -->
                                            @if($student->expulsions->isNotEmpty())
                                                <h4 class="mt-4">Expulsion Record</h4>
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">Reason</th>
                                                            <th scope="col">Start Date</th>
                                                            <th scope="col">File</th>
                                                            <th scope="col">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($student->expulsions as $expulsion)
                                                        <tr>
                                                            <td>{!! $expulsion->reason !!}</td>
                                                            <td>{{ $expulsion->start_date }}</td>
                                                            <td>
                                                                @if($expulsion->file)
                                                                    <a href="{{ asset($expulsion->file) }}" class="btn btn-sm btn-info" target="_blank">
                                                                        <i class="ri-file-download-line"></i> View File
                                                                    </a>
                                                                @else
                                                                    <span class="text-muted">No file</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <form action="{{ url('admin/recall') }}" method="POST" style="display:inline;">
                                                                    @csrf
                                                                    <input type="hidden" name="expulsion_id" value="{{ $expulsion->id }}">
                                                                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                                        <i class="ri-delete-bin-6-line"></i> Lift Expulsion
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <p class="text-muted">No suspension records found.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- ene col -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body p-4">
                                <div>
                                    <div class="flex-shrink-0 avatar-md mx-auto">
                                        <div class="avatar-title bg-light rounded">
                                            <img src="{{empty($student->image)?asset('assets/images/users/user-dummy-img.jpg'):asset($student->image)}}" alt="" height="50" />
                                        </div>
                                    </div>
                                    <div class="mt-4 text-center">
                                        <h5 class="mb-1">{{$name}}</h5>
                                        <p class="text-muted">{{ $student->programme->name }} <br>
                                            <strong>Matric Number:</strong> {{ $student->matric_number }}<br>
                                            <strong>Wifi Username:</strong> {{ $student->bandwidth_username }}<br>
                                            <strong>Email:</strong> {{ $student->email }}<br>
                                            <strong>Phone Number:</strong> {{ $student->applicant->phone_number }}<br>
                                            <strong>Address:</strong> {{ $student->applicant->address }}<br>
                                            @if(env('WALLET_STATUS'))<a class="dropdown-item" href="#"><i class="mdi mdi-wallet text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Balance : <b>₦{{ number_format($student->amount_balance/100, 2) }}</b></span></a>@endif
                                        </p>
                                        <p class="text-muted border-top border-top-dashed pt-2">
                                            <strong>Programme Category:</strong> {{ $student->programmeCategory->category }} Programme<br>
                                            <strong>Department:</strong> {{ $student->department->name }}<br>
                                            <strong>Faculty:</strong> {{ $student->faculty->name }}<br>
                                            <strong>Jamb Reg. Number:</strong> {{ $student->applicant->jamb_reg_no }} <br>
                                            <strong>Academic Level:</strong> <span class="text-primary">{{ $student->level_id * 100 }} Level</span><br>
                                            <strong>Academic session:</strong> {{ $student->academic_session }}</span>
                                            <br>
                                            @if($student->level_id >= $student->programme->duration && !$student->is_passed_out)
                                            <span class="text-warning"><strong>Graduating Set</strong></span> <br>
                                            @endif
                                            <strong>Support Code:</strong> <span class="text-danger">{{ $student->applicant->id }}-ST{{ sprintf("%03d", $student->id) }}</span> 
                                        </p>
                                        <p class="text-muted border-top border-top-dashed pt-2">
                                            <strong>CGPA:</strong> {{ $student->cgpa }} <br>
                                            <strong>Class:</strong> {{ $student->degree_class }}<br>
                                        </p>
                                        <p class="text-muted border-top border-top-dashed pt-2 text-start">
                                            @if($failedCourses->count() > 0)<strong class="text-danger">Failed Courses:</strong> <span class="text-danger">@foreach($failedCourses as $failedCourse) {{ $failedCourse->course_code.',' }} @endforeach</span> @endif <br>
                                            <strong>Promotion Eligibility:</strong> {{ $studentAdvisoryData->promotion_eligible?'You are eligible to promote':'You are not eligible to promote' }}<br>
                                            <strong>Promotion Message:</strong> {{ $studentAdvisoryData->promotion_message }}<br>
                                            <strong>GPA Trend:</strong> {{ $studentAdvisoryData->trajectory_analysis['cgpa_trend'] }}<br>
                                            <strong>CGPA Trajectory Analysis:</strong> {{ $studentAdvisoryData->trajectory_analysis['academic_risk'] }}<br>
                                            <strong>Course Strength:</strong> @foreach($studentAdvisoryData->trajectory_analysis['strengths'] as $strength) {{ $strength.', ' }} @endforeach<br>
                                            <strong>Course Weakness:</strong> @foreach($studentAdvisoryData->trajectory_analysis['weaknesses'] as $weakness) {{ $weakness.', ' }} @endforeach<br>
                                            <strong>Tips:</strong> @foreach($studentAdvisoryData->trajectory_analysis['tips'] as $tips) {{ $tips }} @endforeach<br>
                                        </p>
                                    </div>
                                    <div class="table-responsive border-top border-top-dashed">
                                        <table class="table mb-0 table-borderless">
                                            <tbody>
                                                <tr>
                                                    <th><span class="fw-medium">Department:</span></th>
                                                    <td>{{ $student->department->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Faculty:</span></th>
                                                    <td>{{ $student->faculty->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Email:</span></th>
                                                    <td>{{ $student->email }}</td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Contact No.:</span></th>
                                                    <td>{{ $student->applicant->phone_number }}</td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Address:</span></th>
                                                    <td>{!! $student->applicant->address !!}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!--end card-body-->
                            <div class="card-body p-4 border-top border-top-dashed">
                                <div class="avatar-title bg-light rounded">
                                    <img src="{{ $qrcode }}" style="border: 1px solid black;">
                                </div>
                            </div>
                
                            @if(!empty($student->applicant->guardian))
                            <div class="card-body border-top border-top-dashed p-4">
                                <div>
                                    <h6 class="text-muted text-uppercase fw-semibold mb-4">Guardian Info</h6>
                                    <div class="table-responsive">
                                        <table class="table mb-0 table-borderless">
                                            <tbody>
                                                <tr>
                                                    <th><span class="fw-medium">SN</span></th>
                                                    <td class="text-danger">#{{ $student->applicant->guardian->id }}</td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Name</span></th>
                                                    <td>{{ $student->applicant->guardian->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Email</span></th>
                                                    <td>{{ $student->applicant->guardian->email }}</td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Contact No.</span></th>
                                                    <td>{{ $student->applicant->guardian->phone_number }}</td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Address</span></th>
                                                    <td>{!! $student->applicant->guardian->address !!}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>

            <div class="tab-pane fade" id="settings" role="tabpanel">
               <div class="row">
                    <div class="col-lg-8">
                        <!-- Accordions with Icons -->
                        <div class="accordion custom-accordionwithicon" id="accordionWithicon">
                            <div class="accordion-item shadow">
                                <h2 class="accordion-header" id="accordionwithiconExample1">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#accor_iconExamplecollapse1" aria-expanded="true" aria-controls="accor_iconExamplecollapse1">
                                        Upload Student Image
                                    </button>
                                </h2>
                                <div id="accor_iconExamplecollapse1" class="accordion-collapse collapse show" aria-labelledby="accordionwithiconExample1" data-bs-parent="#accordionWithicon">
                                    <div class="accordion-body">
                                        <div class="mt-4">
                                            <form action="{{ url('/admin/uploadStudentImage') }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="student_id" value="{{ $student->id }}">

                                                <div class="row g-3">

                                                    <div class="row">
                                                        <div class="col-lg-12 text-center">
                                                            <div class="profile-user position-relative d-inline-block mx-auto mb-2">
                                                                <img src="{{empty($student->image)?asset('assets/images/users/user-dummy-img.jpg'):asset($student->image)}}" class="rounded-circle avatar-lg img-thumbnail user-profile-image" alt="user-profile-image">
                                                                <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                                                                    <input id="profile-img-file-input" type="file" class="profile-img-file-input" accept="image/png, image/jpeg" name="image" required>
                                                                    <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                                                                        <span class="avatar-title rounded-circle bg-light text-body">
                                                                            <i class="ri-camera-fill"></i>
                                                                        </span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <h5 class="fs-14">Add Passport Photograph</h5>
                                                        </div>
                                                        <hr>
                                                    </div>
            
                                                    <button type="submit" id="submit-button" class="btn btn-fill btn-primary btn-lg mb-5">Upload Image</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item shadow">
                                <h2 class="accordion-header" id="accordionwithiconExample2">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accor_iconExamplecollapse2" aria-expanded="false" aria-controls="accor_iconExamplecollapse2">
                                        Update Student Name
                                    </button>
                                </h2>
                                <div id="accor_iconExamplecollapse2" class="accordion-collapse collapse" data-bs-parent="#accordionWithicon">
                                    <div class="accordion-body">
                                        <form action="{{ url('admin/changeStudentName') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="student_id" value="{{ $student->id }}">

                                            <div class="row g-2">
                                                <div class="row mt-3 g-3">
                                                    <div class="col-lg-6">
                                                        <div class="form-floating">
                                                            <input type="text" class="form-control" id="lastname" name="lastname" value="{{ $student->applicant->lastname }}">
                                                            <label for="lastname">Lastname(Surname)</label>
                                                        </div>
                                                    </div>
                            
                                                    <div class="col-lg-6">
                                                        <div class="form-floating">
                                                            <input type="text" class="form-control" id="othernames" name="othernames" value="{{ $student->applicant->othernames }}">
                                                            <label for="othernames">Othernames</label>
                                                        </div>
                                                    </div>
                            
                                                    <div class="col-lg-12">
                                                        <div class="form-floating">
                                                            <input type="email" class="form-control" id="email" name="email" value="{{ $student->email }}">
                                                            <label for="email">TAU Student Email</label>
                                                        </div>
                                                    </div>   
                                                    
                                                    <div class="col-lg-12">
                                                        <div class="form-floating">
                                                            <select class="form-control" name="gender" id="gender" required>
                                                                <option @if($student->applicant->gender == '') selected  @endif value="" selected>Select Gender</option>
                                                                <option @if($student->applicant->gender == 'Male') selected  @endif value="Male">Male</option>
                                                                <option @if($student->applicant->gender == 'Female') selected  @endif value="Female">Female</option>
                                                            </select>
                                                            <label for="gender" class="form-label">Gender</label>
                                                        </div>
                                                    </div>
                                                </div>   
                                                  
                
                                                <!--end col-->
                                                <div class="col-lg-12">
                                                    <div class="text-end">
                                                        <br>
                                                        <button type="submit" id="submit-button" class="btn btn-primary btn-lg">Save Changes</button>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                            </div>
                                            <!--end row-->
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item shadow">
                                <h2 class="accordion-header" id="accordionwithiconExample3">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accor_iconExamplecollapse3" aria-expanded="false" aria-controls="accor_iconExamplecollapse3">
                                        Update Student Level
                                    </button>
                                </h2>
                                <div id="accor_iconExamplecollapse3" class="accordion-collapse collapse" data-bs-parent="#accordionWithicon">
                                    <div class="accordion-body">
                                        <form action="{{ url('admin/changeStudentLevel') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="student_id" value="{{ $student->id }}">

                                            <div class="row g-2">
                                                <div class="mb-3">
                                                    <label for="level" class="form-label">Level</label>
                                                    <select class="form-select" name="level_id" id="level" data-choices data-choices-search-false required>
                                                        <option value="" selected>Choose...</option>
                                                        @foreach($academicLevels as $academicLevel)<option value="{{ $academicLevel->id }}">{{ $academicLevel->level }}</option>@endforeach
                                                    </select>
                                                </div>
                
                                                <!--end col-->
                                                <div class="col-lg-12">
                                                    <div class="text-end">
                                                        <br>
                                                        <button type="submit" id="submit-button" class="btn btn-primary btn-lg">Save Changes</button>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                            </div>
                                            <!--end row-->
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item shadow">
                                <h2 class="accordion-header" id="accordionwithiconExample4">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accor_iconExamplecollapse4" aria-expanded="false" aria-controls="accor_iconExamplecollapse4">
                                    Update Student Password
                                    </button>
                                </h2>
                                <div id="accor_iconExamplecollapse4" class="accordion-collapse collapse" aria-labelledby="accordionwithiconExample4" data-bs-parent="#accordionWithicon">
                                    <div class="accordion-body">
                                        <form action="{{ url('admin/changeStudentPassword') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="student_id" value="{{ $student->id }}">

                                            <div class="row g-2">
                                                <div class="col-lg-4">
                                                    <div>
                                                        <label for="newpasswordInput" class="form-label">New Password<span class="text-danger">*</span></label>
                                                        <input type="password"  name="password"  class="form-control" id="newpasswordInput" placeholder="Enter new password">
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-lg-4">
                                                    <div>
                                                        <label for="confirmpasswordInput" class="form-label">Confirm Password<span class="text-danger">*</span></label>
                                                        <input type="password"  name="confirm_password" class="form-control" id="confirmpasswordInput" placeholder="Confirm password">
                                                    </div>
                                                </div>
                
                                                <!--end col-->
                                                <div class="col-lg-4">
                                                    <div class="text-end">
                                                        <br>
                                                        <button type="submit" id="submit-button" class="btn btn-primary btn-lg">Change Password</button>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                            </div>
                                            <!--end row-->
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item shadow">
                                <h2 class="accordion-header" id="accordionwithiconExample5">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accor_iconExamplecollapse5" aria-expanded="false" aria-controls="accor_iconExamplecollapse45">
                                        Update Student Credit Load
                                    </button>
                                </h2>
                                <div id="accor_iconExamplecollapse5" class="accordion-collapse collapse" aria-labelledby="accordionwithiconExample5" data-bs-parent="#accordionWithicon">
                                    <div class="accordion-body">
                                        <form action="{{ url('admin/changeStudentCreditLoad') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="student_id" value="{{ $student->id }}">

                                            <div class="row g-2">
                                                <div class="col-lg-12">
                                                    <div>
                                                        <label for="creditLoad" class="form-label">Credit Load<span class="text-danger">*</span></label>
                                                        <input type="number"  name="credit_load"  class="form-control" id="credit_load" value="{{ $student->credit_load }}">
                                                    </div>
                                                </div>
                
                                                <!--end col-->
                                                <div class="col-lg-12">
                                                    <div class="text-end">
                                                        <br>
                                                        <button type="submit" id="submit-button" class="btn btn-primary btn-lg">Save Changes</button>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                            </div>
                                            <!--end row-->
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item shadow">
                                <h2 class="accordion-header" id="accordionwithiconExample6">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accor_iconExamplecollapse6" aria-expanded="false" aria-controls="accor_iconExamplecollapse6">
                                        Update Student Batch (Batch {{ $student->batch }})
                                    </button>
                                </h2>
                                <div id="accor_iconExamplecollapse6" class="accordion-collapse collapse" aria-labelledby="accordionwithiconExample6" data-bs-parent="#accordionWithicon">
                                    <div class="accordion-body">
                                        <form action="{{ url('admin/changeStudentBatch') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="student_id" value="{{ $student->id }}">

                                            <div class="row g-2">
                                                <div class="mb-3">
                                                    <label for="choices-batch-input" class="form-label">Student Batch (Batch {{ $student->batch }})</label>
                                                    <select class="form-select" name="batch" id="choices-batch-input" data-choices data-choices-search-false required>
                                                        <option value="" selected>Choose...</option>
                                                        <option value="A">Batch A</option>
                                                        <option value="B">Batch B</option>
                                                        <option value="C">Batch C</option>
                                                    </select>
                                                </div>
                
                                                <!--end col-->
                                                <div class="col-lg-12">
                                                    <div class="text-end">
                                                        <br>
                                                        <button type="submit" id="submit-button" class="btn btn-primary btn-lg">Save Changes</button>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                            </div>
                                            <!--end row-->
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body p-4">
                                <div>
                                    <div class="flex-shrink-0 avatar-md mx-auto">
                                        <div class="avatar-title bg-light rounded">
                                            <img src="{{empty($student->image)?asset('assets/images/users/user-dummy-img.jpg'):asset($student->image)}}" alt="" height="50" />
                                        </div>
                                    </div>
                                    <div class="mt-4 text-center">
                                        <h5 class="mb-1">{{$name}}</h5>
                                        <p class="text-muted">{{ $student->programme->name }} <br>
                                            <strong>Matric Number:</strong> {{ $student->matric_number }}<br>
                                            <strong>Wifi Username:</strong> {{ $student->bandwidth_username }}<br>
                                            <strong>Email:</strong> {{ $student->email }}<br>
                                            <strong>Phone Number:</strong> {{ $student->applicant->phone_number }}<br>
                                            <strong>Address:</strong> {{ $student->applicant->address }}<br>
                                        </p>
                                        <p class="text-muted border-top border-top-dashed pt-2">
                                            <strong>Programme Category:</strong> {{ $student->programmeCategory->category }} Programme<br>
                                            <strong>Department:</strong> {{ $student->department->name }}<br>
                                            <strong>Faculty:</strong> {{ $student->faculty->name }}<br>
                                            <strong>Jamb Reg. Number:</strong> {{ $student->applicant->jamb_reg_no }} <br>
                                            <strong>Academic Level:</strong> <span class="text-primary">{{ $student->level_id * 100 }} Level</span><br>
                                            <strong>Academic session:</strong> {{ $student->academic_session }}</span>
                                            <br>
                                            @if($student->level_id >= $student->programme->duration && !$student->is_passed_out)
                                            <span class="text-warning"><strong>Graduating Set</strong></span> <br>
                                            @endif
                                            <strong>Support Code:</strong> <span class="text-danger">{{ $student->applicant->id }}-ST{{ sprintf("%03d", $student->id) }}</span> 
                                        </p>
                                        <p class="text-muted border-top border-top-dashed pt-2">
                                            <strong>CGPA:</strong> {{ $student->cgpa }} <br>
                                            <strong>Class:</strong> {{ $student->degree_class }}<br>
                                        </p>
                                        <p class="text-muted border-top border-top-dashed pt-2 text-start">
                                            @if($failedCourses->count() > 0)<strong class="text-danger">Failed Courses:</strong> <span class="text-danger">@foreach($failedCourses as $failedCourse) {{ $failedCourse->course_code.',' }} @endforeach</span> @endif <br>
                                            <strong>Promotion Eligibility:</strong> {{ $studentAdvisoryData->promotion_eligible?'You are eligible to promote':'You are not eligible to promote' }}<br>
                                            <strong>Promotion Message:</strong> {{ $studentAdvisoryData->promotion_message }}<br>
                                            <strong>GPA Trend:</strong> {{ $studentAdvisoryData->trajectory_analysis['cgpa_trend'] }}<br>
                                            <strong>CGPA Trajectory Analysis:</strong> {{ $studentAdvisoryData->trajectory_analysis['academic_risk'] }}<br>
                                            <strong>Course Strength:</strong> @foreach($studentAdvisoryData->trajectory_analysis['strengths'] as $strength) {{ $strength.', ' }} @endforeach<br>
                                            <strong>Course Weakness:</strong> @foreach($studentAdvisoryData->trajectory_analysis['weaknesses'] as $weakness) {{ $weakness.', ' }} @endforeach<br>
                                            <strong>Tips:</strong> @foreach($studentAdvisoryData->trajectory_analysis['tips'] as $tips) {{ $tips }} @endforeach<br>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <!--end card-body-->
                            <div class="card-body p-4 border-top border-top-dashed">
                                <div class="avatar-title bg-light rounded">
                                    <img src="{{ $qrcode }}" style="border: 1px solid black;">
                                </div>
                            </div>
                
                            @if(!empty($student->applicant->guardian))
                            <div class="card-body border-top border-top-dashed p-4">
                                <div>
                                    <h6 class="text-muted text-uppercase fw-semibold mb-4">Guardian Info</h6>
                                    <div class="table-responsive">
                                        <table class="table mb-0 table-borderless">
                                            <tbody>
                                                <tr>
                                                    <th><span class="fw-medium">SN</span></th>
                                                    <td class="text-danger">#{{ $student->applicant->guardian->id }}</td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Name</span></th>
                                                    <td>{{ $student->applicant->guardian->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Email</span></th>
                                                    <td>{{ $student->applicant->guardian->email }}</td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Contact No.</span></th>
                                                    <td>{{ $student->applicant->guardian->phone_number }}</td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Address</span></th>
                                                    <td>{!! $student->applicant->guardian->address !!}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col -->
               </div>
            </div>

            <div class="tab-pane fade" id="partners" role="tabpanel">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <div class="text-muted">
                                    <h4 class="card-title mb-0 flex-grow-1">Referred Student(s) for {{ $pageGlobalData->sessionSetting->application_session }} application session </h4>
                                    <div class="border-top border-top-dashed pt-3">
                                        <div class="table-responsive">
                                            <!-- Bordered Tables -->
                                            <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Id</th>
                                                        <th scope="col">Name</th>
                                                        <th scope="col">Programme</th>
                                                        <th scope="col">Email</th>
                                                        <th scope="col">Phone Number</th>
                                                        <th scope="col">Academic Session</th>
                                                        <th scope="col">Application Status</th>
                                                        <th scope="col">Applied Date</th>
                                                        <th scope="col"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($applicants as $applicant)
                                                    <tr>
                                                        <th scope="row">{{ $loop->iteration }}</th>
                                                        <td>{{ $applicant->lastname .' '. $applicant->othernames }}</td>
                                                        <td>{{ $applicant->programme->name }}</td>
                                                        <td>{{ $applicant->email }} </td>
                                                        <td>{{ $applicant->phone_number }} </td>
                                                        <td>{{ $applicant->academic_session }} </td>
                                                        <td>{{ ucwords($applicant->status) }} </td>
                                                        <td>{{ $applicant->created_at }} </td>
                                                        <td>
                                                            <a href="{{ !empty($applicant->student)? url('admin/student/'.$applicant->student->slug) : url('admin/applicant/'.$applicant->slug) }}" class="btn btn-primary m-1"><i class= "ri-user-6-fill"></i> View Applicant/Student</a>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- ene col -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body p-4">
                                <div>
                                    <div class="flex-shrink-0 avatar-md mx-auto">
                                        <div class="avatar-title bg-light rounded">
                                            <img src="{{empty($student->image)?asset('assets/images/users/user-dummy-img.jpg'):asset($student->image)}}" alt="" height="50" />
                                        </div>
                                    </div>
                                    <div class="mt-4 text-center">
                                        <h5 class="mb-1">{{$name}}</h5>
                                        <p class="text-muted">{{ $student->programme->name }} <br>
                                            <strong>Matric Number:</strong> {{ $student->matric_number }}<br>
                                            <strong>Wifi Username:</strong> {{ $student->bandwidth_username }}<br>
                                            <strong>Email:</strong> {{ $student->email }}<br>
                                            <strong>Phone Number:</strong> {{ $student->applicant->phone_number }}<br>
                                            <strong>Address:</strong> {{ $student->applicant->address }}<br>
                                            @if(env('WALLET_STATUS'))<a class="dropdown-item" href="#"><i class="mdi mdi-wallet text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Balance : <b>₦{{ number_format($student->amount_balance/100, 2) }}</b></span></a>@endif
                                        </p>
                                        <p class="text-muted border-top border-top-dashed pt-2">
                                            <strong>Programme Category:</strong> {{ $student->programmeCategory->category }} Programme<br>
                                            <strong>Department:</strong> {{ $student->department->name }}<br>
                                            <strong>Faculty:</strong> {{ $student->faculty->name }}<br>
                                            <strong>Jamb Reg. Number:</strong> {{ $student->applicant->jamb_reg_no }} <br>
                                            <strong>Academic Level:</strong> <span class="text-primary">{{ $student->level_id * 100 }} Level</span><br>
                                            <strong>Academic session:</strong> {{ $student->academic_session }}</span>
                                            <br>
                                            @if($student->level_id >= $student->programme->duration && !$student->is_passed_out)
                                            <span class="text-warning"><strong>Graduating Set</strong></span> <br>
                                            @endif
                                            <strong>Support Code:</strong> <span class="text-danger">{{ $student->applicant->id }}-ST{{ sprintf("%03d", $student->id) }}</span> 
                                        </p>
                                        <p class="text-muted border-top border-top-dashed pt-2">
                                            <strong>CGPA:</strong> {{ $student->cgpa }} <br>
                                            <strong>Class:</strong> {{ $student->degree_class }}<br>
                                        </p>
                                        <p class="text-muted border-top border-top-dashed pt-2 text-start">
                                            @if($failedCourses->count() > 0)<strong class="text-danger">Failed Courses:</strong> <span class="text-danger">@foreach($failedCourses as $failedCourse) {{ $failedCourse->course_code.',' }} @endforeach</span> @endif <br>
                                            <strong>Promotion Eligibility:</strong> {{ $studentAdvisoryData->promotion_eligible?'You are eligible to promote':'You are not eligible to promote' }}<br>
                                            <strong>Promotion Message:</strong> {{ $studentAdvisoryData->promotion_message }}<br>
                                            <strong>GPA Trend:</strong> {{ $studentAdvisoryData->trajectory_analysis['cgpa_trend'] }}<br>
                                            <strong>CGPA Trajectory Analysis:</strong> {{ $studentAdvisoryData->trajectory_analysis['academic_risk'] }}<br>
                                            <strong>Course Strength:</strong> @foreach($studentAdvisoryData->trajectory_analysis['strengths'] as $strength) {{ $strength.', ' }} @endforeach<br>
                                            <strong>Course Weakness:</strong> @foreach($studentAdvisoryData->trajectory_analysis['weaknesses'] as $weakness) {{ $weakness.', ' }} @endforeach<br>
                                            <strong>Tips:</strong> @foreach($studentAdvisoryData->trajectory_analysis['tips'] as $tips) {{ $tips }} @endforeach<br>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <!--end card-body-->
                            <div class="card-body p-4 border-top border-top-dashed">
                                <div class="avatar-title bg-light rounded">
                                    <img src="{{ $qrcode }}" style="border: 1px solid black;">
                                </div>
                            </div>
                
                            @if(!empty($student->applicant->guardian))
                            <div class="card-body border-top border-top-dashed p-4">
                                <div>
                                    <h6 class="text-muted text-uppercase fw-semibold mb-4">Guardian Info</h6>
                                    <div class="table-responsive">
                                        <table class="table mb-0 table-borderless">
                                            <tbody>
                                                <tr>
                                                    <th><span class="fw-medium">SN</span></th>
                                                    <td class="text-danger">#{{ $student->applicant->guardian->id }}</td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Name</span></th>
                                                    <td>{{ $student->applicant->guardian->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Email</span></th>
                                                    <td>{{ $student->applicant->guardian->email }}</td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Contact No.</span></th>
                                                    <td>{{ $student->applicant->guardian->phone_number }}</td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Address</span></th>
                                                    <td>{!! $student->applicant->guardian->address !!}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>
        </div>
    </div>
    <!-- end col -->
</div>
<!-- end row -->

<!-- Expel Student Modal -->
<div id="expelStudent" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="text-end">
                    <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="mt-2">
                    <h4 class="mb-3 mt-4 text-danger">
                        Are you sure you want to expel <br/> {{ $student->applicant->lastname.' '.$student->applicant->othernames }}?
                    </h4>
                    <form action="{{ url('/admin/expel') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input name="student_id" type="hidden" value="{{ $student->id }}">

                        <!-- Reason for Expulsion -->
                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason for Expulsion <span class="text-danger">*</span></label>
                            <textarea name="reason" id="reason" class="form-control ckeditor" rows="3"></textarea>
                        </div>

                        <!-- Upload Expulsion File -->
                        <div class="mb-3">
                            <label for="file" class="form-label">Upload Document <span class="text-danger">*</span></label>
                            <input type="file" name="file" id="file" class="form-control" accept=".pdf,.doc,.docx,.jpg,.png" required>
                            <small class="text-muted">Accepted formats: PDF, DOC, DOCX, JPG, PNG</small>
                        </div>

                        <hr>
                        <button type="submit" class="btn btn-danger w-100">Yes, Expel Student</button>
                    </form>
                </div>
            </div>
            <div class="modal-footer bg-light p-3 justify-content-center">
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="assignStudyCenter" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="text-end">
                    <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="mt-2">
                    <form action="{{ url('/admin/assignStudyCenter') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input name="student_id" type="hidden" value="{{ $student->id }}">

                        <div class="mb-3">
                            <label for="centerId" class="form-label">Select Study Center</label>
                            <select class="form-select" aria-label="centerId" name="center_id" required>
                                @foreach($studyCenters as $studyCenter)<option @if($student->center_id == $studyCenter->id) selected @endif value="{{ $studyCenter->id }}">{{ $studyCenter->name }}</option>@endforeach
                            </select>
                        </div>

                        <hr>
                        <button type="submit" class="btn btn-danger w-100">Yes, proceed</button>
                    </form>
                </div>
            </div>
            <div class="modal-footer bg-light p-3 justify-content-center">
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Expel Student Modal -->
<div id="suspendStudent" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="text-end">
                    <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="mt-2">
                    <h4 class="mb-3 mt-4 text-warning">
                        Are you sure you want to expend <br/> {{ $student->applicant->lastname.' '.$student->applicant->othernames }}?
                    </h4>
                    <form action="{{ url('/admin/suspend') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input name="student_id" type="hidden" value="{{ $student->id }}">

                        <!-- Reason for Suspension -->
                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason for Suspension <span class="text-danger">*</span></label>
                            <textarea name="reason" id="reason" class="form-control ckeditor" rows="3" required></textarea>
                        </div>

                        <!-- Upload Suspension File -->
                        <div class="mb-3">
                            <label for="file" class="form-label">Upload Document <span class="text-danger">*</span></label>
                            <input type="file" name="file" id="file" class="form-control" accept=".pdf,.doc,.docx,.jpg,.png">
                            <small class="text-muted">Accepted formats: PDF, DOC, DOCX, JPG, PNG</small>
                        </div>

                        <hr>
                        <button type="submit" class="btn btn-danger w-100">Yes, Suspension Student</button>
                    </form>
                </div>
            </div>
            <div class="modal-footer bg-light p-3 justify-content-center">
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



<div id="deleteStudent" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="text-end">
                    <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="mt-2">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="hover" style="width:150px;height:150px">
                    </lord-icon>
                    <h4 class="mb-3 mt-4">Are you sure you want to disable <br/> {{ $student->applicant->lastname.' '.$student->applicant->othernames }}?</h4>
                    <form action="{{ url('/admin/expelStudent') }}" method="POST">
                        @csrf
                        <input name="student_id" type="hidden" value="{{$student->id}}">
                        <hr>
                        <button type="submit" id="submit-button" class="btn btn-danger w-100">Yes, Disable</button>
                    </form>
                </div>
            </div>
            <div class="modal-footer bg-light p-3 justify-content-center">

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="enableStudent" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="text-end">
                    <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="mt-2">
                    <lord-icon src="https://cdn.lordicon.com/tqywkdcz.json" trigger="hover" style="width:150px;height:150px">
                    </lord-icon>
                    <h4 class="mb-3 mt-4">Are you sure you want to enable <br/> {{ $student->applicant->lastname.' '.$student->applicant->othernames }}?</h4>
                    <form action="{{ url('/admin/enableStudent') }}" method="POST">
                        @csrf
                        <input name="student_id" type="hidden" value="{{$student->id}}">
                        <hr>
                        <button type="submit" id="submit-button" class="btn btn-success w-100">Yes, Enable</button>
                    </form>
                </div>
            </div>
            <div class="modal-footer bg-light p-3 justify-content-center">

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@endsection
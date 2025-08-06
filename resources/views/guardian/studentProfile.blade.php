@extends('guardian.layout.dashboard')
@php
use \App\Models\ResultApprovalStatus;

$qrcode = 'https://quickchart.io/chart?chs=300x300&cht=qr&chl='.env('APP_URL').'/studentDetails/'.$student->slug;
$name = $student->applicant->lastname.' '.$student->applicant->othernames;
$transactions = $student->transactions()->orderBy('created_at', 'desc')->get();
$studentRegistrations = $student->courseRegistrationDocument()->orderBy('created_at', 'desc')->take(10)->get();
$currentHostelAllocation = $student->currentHostelAllocation;
$failedCourses = $student->registeredCourses()->where('grade', 'F')->where('re_reg', null)->where('result_approval_id', ResultApprovalStatus::getApprovalStatusId(ResultApprovalStatus::SENATE_APPROVED))->get();
$studentAdvisoryData = (object) $student->getAcademicAdvisory();

$applicationSession = $student->programmeCategory->academicSessionSetting->application_session;
$admissionSession = $student->programmeCategory->academicSessionSetting->admission_session;
$academicSession = $student->programmeCategory->academicSessionSetting->academic_session;
$accomondationBookingStatus = $student->programmeCategory->academicSessionSetting->accomondation_booking_status;

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
<style>
    /* Adjust the width of the ID column */
    .table th:nth-child(1),
    .table td:nth-child(1) {
        width: 10px; /* Adjust the width as needed */
    }
    .semester-heading {
        font-weight: bold;
        font-size: 1.2em;
        padding: 10px 0;
    }
</style>

<div class="row">
    <div class="col-lg-12">
        <div class="card mt-n4 mx-n4">
            <div class="bg-soft-primary">
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
                                        <h4 class="fw-bold">{{$name}}</h4>
                                        <div class="hstack gap-3 flex-wrap">
                                            <div><i class="ri-building-line align-bottom me-1"></i> {{ $student->programme->name }}</div>
                                            <div class="vr"></div>
                                            <div>CGPA: <span class="fw-medium">{{ $student->cgpa }}</span></div>
                                            <div class="vr"></div>
                                            <div>Level: <span class="fw-medium">{{ $student->academicLevel->level }} Level</span></div>
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
                                <div class="flex-shrink-0">
                                    @if(empty($currentHostelAllocation) && strtolower($accomondationBookingStatus) == 'start')
                                    <button type="button" class="btn btn-warning mb-2" data-bs-toggle="modal" data-bs-target="#payAccomondation">Pay Accomondation Fee</button>
                                    @endif
                                    <br>
                                    @if(!$student->schoolFeeDetails->fullTuitionPayment)
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTransaction">Pay Tuition Fee</button>
                                    @endif
                                    @if(env('WALLET_STATUS'))
                                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#topUpWallet">Topup Student Wallet</button>
                                    @endif
                                </div>
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
                            <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#courses" role="tab">
                                Registered Course(s)
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
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Payments </h4>
                            </div><!-- end card header -->
                            <div class="card-body">
                                <div class="text-muted">
                                    <div class="pt-3">
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
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($transactions as $transaction)
                                                        @if($transaction->amount_payed > 0)
                                                            <tr>
                                                                <th scope="row">{{ $loop->iteration }}</th>
                                                                <td>{{ $transaction->reference }}</td>
                                                               <td>₦{{ number_format($transaction->amount_payed/100, 2) }} </td>
                                                                <td>
                                                                    {{ !empty($transaction->paymentType) 
                                                                        ? (in_array($transaction->paymentType->type, ['General Fee', 'Other Fee']) 
                                                                            ? $transaction->paymentType->title 
                                                                            : $transaction->paymentType->type) 
                                                                        : 'Wallet Deposit' 
                                                                    }}
                                                                </td>
                                                                <td>{{ $transaction->session }}</td>
                                                                <td>{{ $transaction->schoolPayment_method }}</td>
                                                                <td><span class="badge badge-soft-{{ $transaction->status == 1 ? 'success' : 'warning' }}">{{ $transaction->status == 1 ? 'Paid' : 'Pending' }}</span></td>
                                                                <td>{{ $transaction->status == 1 ? $transaction->updated_at : null }} </td>
                                                                <td>
                                                                    @if($transaction->status == 1)
                                                                        <form action="{{ url('/guardian/generateInvoice') }}" method="post" enctype="multipart/form-data">
                                                                            @csrf
                                                                            <input name="payment_id" type="hidden" value="{{!empty($transaction->paymentType)?$transaction->paymentType->id:0}}">
                                                                            <input name="student_id" type="hidden" value="{{$transaction->student_id}}">
                                                                            <input name="session" type="hidden" value="{{ $transaction->session }}">
                                                                            <button type="submit" id="submit-button" class="btn btn-primary"><i class="mdi mdi-printer"></i></button>
                                                                        </form>
                                                                    @endif
                                                                    @if($transaction->status == 0)
                                                                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#payNow{{$transaction->id}}" style="margin: 5px" class="btn btn-warning">Pay Now</a>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                    
                                                            <div id="payNow{{$transaction->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                                                    <div class="modal-content border-0 overflow-hidden">
                                                                        <div class="modal-header p-3">
                                                                            <h4 class="card-title mb-0">Pay Now</h4>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                        </div>
                                    
                                                                        <div class="modal-body border-top border-top-dashed">
                                                                            <div class="mt-2 text-center">
                                                                            <lord-icon src="https://cdn.lordicon.com/ggihhudh.json" trigger="hover" style="width:150px;height:150px">
                                                                            </lord-icon>
                                                                            </div>
                                                                            <form action="{{ url('/guardian/makePayment') }}" method="post" enctype="multipart/form-data">
                                                                                @csrf
                                                                                <input type="hidden" name='programme_id' value="{{ $student->programme->id }}">
                                                                                <input type="hidden" name="student_id" value="{{ $student->id }}">
                                                                                <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">
                                                                                <input type="hidden" name="payment_id" value="{{ !empty($transaction->paymentType)? $transaction->paymentType->id : 0 }}">
                                                                                <input type="hidden" name="reference" value="{{ $transaction->reference }}">
                                                                                <input type="hidden" name="amount" value="{{ $transaction->amount_payed/100 }}">
                                                                                
                                                                                <div class="mb-3">
                                                                                    <label for="paymentGateway" class="form-label">Select Payment Gateway<span class="text-danger">*</span></label>
                                                                                    <select class="form-select" aria-label="paymentGateway" name="paymentGateway" required onchange="handlePaymentMethodChange(event)">
                                                                                        <option value= "" selected>Select Payment Gateway</option>
                                                                                        @if(env('UPPERLINK_STATUS'))<option value="Upperlink">Upperlink</option>@endif
                                                                                        @if(env('FLUTTERWAVE_STATUS'))<option value="Rave">Flutterwave</option>@endif
                                                                                        @if(env('MONNIFY_STATUS'))<option value="Monnify">Monnify</option>@endif
                                                                                        @if(env('PAYSTACK_STATUS'))<option value="Paystack">Paystack</option>@endif
                                                                                        {{-- @if(env('BANK_TRANSFER_STATUS'))<option value="BankTransfer">Transfer</option>@endif --}}
                                                                                    </select>
                                                                                </div>
                                    
                                                                                <hr>
                                                                                <!-- Primary Alert -->
                                                                                <div class="alert alert-primary alert-dismissible alert-additional fade show" role="alert" style="display: none" id="transferInfo">
                                                                                    <div class="alert-body">
                                                                                        <div class="d-flex">
                                                                                            <div class="flex-shrink-0 me-3">
                                                                                                <i class="fs-16 align-middle"></i>
                                                                                            </div>
                                                                                            <div class="flex-grow-1">
                                                                                                <h5 class="alert-heading">Well done !</h5>
                                                                                                <p class="mb-0">Kindly make transfer to the below transaction </p>
                                                                                                <br>
                                                                                                <ul class="list-group">
                                                                                                    <li class="list-group-item"><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i><strong>Bank Name:</strong> {{env('BANK_NAME')}}</li>
                                                                                                    <li class="list-group-item"><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i><strong>Bank Account Number:</strong> {{env('BANK_ACCOUNT_NUMBER')}}</li>
                                                                                                    <li class="list-group-item"><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i><strong>Bank Account Name:</strong> {{env('BANK_ACCOUNT_NAME')}}</li>
                                                                                                </ul>
                                                                                                <br>
                                                                                                <p>Please send proof of payment as an attachment to {{ env('ACCOUNT_EMAIL') }}, including your name, registration number, and purpose of payment. For any inquiries, you can also call {{ env('ACCOUNT_PHONE') }}.</p>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="alert-content">
                                                                                        <p class="mb-0">NOTE: PLEASE ENSURE TO VERIFY THE TRANSACTION DETAILS PROPERLY. TRANSFER ONLY TO THE ACCOUNT ABOVE. STUDENTS TAKE RESPONSIBILITY FOR ANY MISPLACEMENT OF FUNDS.</p>
                                                                                    </div>
                                                                                </div>
                                                                                <div>
                                                                                    <button type="submit" id="submit-button" class="btn btn-primary">Make payment</button>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div><!-- /.modal-content -->
                                                                </div><!-- /.modal-dialog -->
                                                            </div><!-- /.modal -->
                                                        @endif
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
                                                Student must have paid 100% of academic session school fee
                                            </div>
                                            <div class="mt-4">
                                                <form action="{{ url('/guardian/generateResult') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="examSetting_id" value="{{ !empty($pageGlobalData->examSetting)?$pageGlobalData->examSetting->id:null }}">
                                                    <input type="hidden" name="academic_session" value="{{ $academicSession }}">
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
                                            <strong>Address:</strong> {!! preg_replace('/<\/?p[^>]*>/', '', $student->applicant->address) !!}<br>
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
                                            <strong>Promotion Eligibility:</strong> {{ is_null($student->cgpa) || $student->cgpa == 0 ? 'You are a fresh student; promotion eligibility will be determined after your first semester.' : ($studentAdvisoryData->promotion_eligible ? 'You are eligible to promote.' : 'You are not eligible to promote.') }} <br>
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
            <!-- end tab pane -->

            <div class="tab-pane fade" id="courses" role="tabpanel">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <div class="text-muted">
                                    <h4 class="card-title mb-0 flex-grow-1">Registered Courses(s) for {{ $academicSession }} academic session</h4>
                                    <div class="border-top border-top-dashed pt-3">
                                        <div class="table-responsive">
                                            <!-- Bordered Tables -->
                                            @php
                                            $courseRegs = $student->registeredCourses->where('academic_session', $academicSession);
                                            @endphp
                                            <div class="row">   
                                                <div class="col-lg-12">
                                                    <div class="card">
                                                        <div class="card-header align-items-center">
                                                            <br/>
                                                            <p class=""><strong>Programme:</strong> {{ $student->programme->name }}
                                                            <br/><strong>Academic Session:</strong> {{ $student->academic_session }}
                                                            <br/><strong>Level:</strong> {{ $student->academicLevel->level }} Level</p>
                                            
                                                        </div><!-- end card header -->
                                            
                                                        <div class="card-body table-responsive">
                                                            <table class="table table-borderless table-nowrap">
                                                                
                                                                <tbody class="first-semester">
                                                                    <tr>
                                                                        <td colspan="6" class="semester-heading">
                                                                            
                                                                            <div class="card-header align-items-center">
                                                                                <h4 class="card-title mb-0 flex-grow-1">Harmattan Semester Courses</h4>
                                                                            </div><!-- end card header -->
                                            
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="col">ID</th>
                                                                        <th scope="col">Attendance Percentage</th>
                                                                        <th scope="col">Course Code</th>
                                                                        <th scope="col">Course Title</th>
                                                                        <th scope="col">Course Unit</th>
                                                                    </tr>
                                                                    @php
                                                                        $firstSemester = 1;
                                                                        $secondSemester = 1;
                                                                        $firstCreditUnits = $courseRegs->where('semester', 1)->sum('course_credit_unit');
                                                                        $secondCreditUnits = $courseRegs->where('semester', 2)->sum('course_credit_unit');
                                                                    @endphp
                                                                    @foreach($courseRegs->where('semester', 1) as $course11)
                                                                        <tr>
                                                                            <td>{{ $firstSemester++ }}</td>
                                                                            <td>{{ $course11->attendancePercentage() }}% </td>
                                                                            <td>{{ $course11->course->code }}</td>
                                                                            <td>{{ ucwords(strtolower($course11->course->name)) }}</td>
                                                                            <td>{{ $course11->course_credit_unit }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                                <tbody>
                                                                    <tr class="first-semester-total">
                                                                        <td>Total Harmattan Semester Credit Unit</td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td>{{ $firstCreditUnits }}</td>
                                                                    </tr>
                                                                </tbody>
                                                                
                                                                <tbody class="second-semester">
                                                                    <tr>
                                                                        <td colspan="6" class="semester-heading">
                                                                            
                                                                            <div class="card-header align-items-center">
                                                                                <h4 class="card-title mb-0 flex-grow-1">Rain Semester Courses</h4>
                                                                            </div><!-- end card header -->
                                            
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="col">ID</th>
                                                                        <th scope="col">Attendance Percentage</th>
                                                                        <th scope="col">Course Code</th>
                                                                        <th scope="col">Course Title</th>
                                                                        <th scope="col">Course Unit</th>
                                                                    </tr>
                                                                    @foreach($courseRegs->where('semester', 2) as $course12)
                                                                        <tr>
                                                                            <td>{{ $secondSemester++ }}</td>
                                                                            <td>{{ $course12->attendancePercentage() }}% </td>
                                                                            <td>{{ $course12->course->code }}</td>
                                                                            <td>{{ ucwords(strtolower($course12->course->name)) }}</td>
                                                                            <td>{{ $course12->course_credit_unit }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                                <tbody>
                                                                    <tr class="second-semester-total">
                                                                        <td>Total Rain Semester Credit Unit</td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td>{{ $secondCreditUnits }}</td>
                                                                    
                                                                    </tr>
                                                                </tbody>
                                                            </table>               
                                                        </div>
                                                    </div><!-- end card -->
                                                </div>
                                                <!-- end col -->
                                            </div>
                                           
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
                                            <strong>Address:</strong> {!! preg_replace('/<\/?p[^>]*>/', '', $student->applicant->address) !!}<br>
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
                                            <strong>Promotion Eligibility:</strong> {{ is_null($student->cgpa) || $student->cgpa == 0 ? 'You are a fresh student; promotion eligibility will be determined after your first semester.' : ($studentAdvisoryData->promotion_eligible ? 'You are eligible to promote.' : 'You are not eligible to promote.') }} <br>
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


<div id="addTransaction" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Make 'a' Payment</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body border-top border-top-dashed">
                <form action="{{ url('/guardian/makePayment') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name='student_id' value="{{ $student->id }}">
                    <input type="hidden" name='programme_id' value="{{ $student->programme->id }}">
                    <input type="hidden" name="payment_id" value="{{ $student->schoolFeeDetails->schoolPayment->id }}">

                    <div class="mb-3">
                        <label for="amount" class="form-label">Payment Amount<span class="text-danger">*</span></label>
                        <select class="form-select" aria-label="amount" name="amount" required>
                            <option value= "" selected>Select Amount</option>
                            @if(!$student->schoolFeeDetails->passTuitionPayment)
                            <option value="{{ $student->schoolFeeDetails->schoolPayment->structures->sum('amount') - $student->schoolFeeDetails->schoolPaymentTransaction->sum('amount_payed') }}">₦{{ number_format(($student->schoolFeeDetails->schoolPayment->structures->sum('amount') - $student->schoolFeeDetails->schoolPaymentTransaction->sum('amount_payed'))/100, 2) }} - Balance</option>
                            <option value="{{ $student->schoolFeeDetails->schoolPayment->structures->sum('amount') }}">₦{{ number_format($student->schoolFeeDetails->schoolPayment->structures->sum('amount')/100, 2) }} - 100%</option>
                            <option value="{{ $student->schoolFeeDetails->schoolPayment->structures->sum('amount')*0.9 }}">₦{{ number_format($student->schoolFeeDetails->schoolPayment->structures->sum('amount')*0.9/100, 2) }} - 90%</option>
                            <option value="{{ $student->schoolFeeDetails->schoolPayment->structures->sum('amount')*0.8 }}">₦{{ number_format($student->schoolFeeDetails->schoolPayment->structures->sum('amount')*0.8/100, 2) }} - 80%</option>
                            <option value="{{ $student->schoolFeeDetails->schoolPayment->structures->sum('amount')*0.7 }}">₦{{ number_format($student->schoolFeeDetails->schoolPayment->structures->sum('amount')*0.7/100, 2) }} - 70%</option>
                            <option value="{{ $student->schoolFeeDetails->schoolPayment->structures->sum('amount')*0.6 }}">₦{{ number_format($student->schoolFeeDetails->schoolPayment->structures->sum('amount')*0.6/100, 2) }} - 60%</option>
                            <option value="{{ $student->schoolFeeDetails->schoolPayment->structures->sum('amount')*0.5 }}">₦{{ number_format($student->schoolFeeDetails->schoolPayment->structures->sum('amount')*0.5/100, 2) }} - 50%</option>
                            <option value="{{ $student->schoolFeeDetails->schoolPayment->structures->sum('amount')*0.4 }}">₦{{ number_format($student->schoolFeeDetails->schoolPayment->structures->sum('amount')*0.4/100, 2) }} - 40%</option>
                            @endif
                            @if($student->schoolFeeDetails->passTuitionPayment && !$student->schoolFeeDetails->fullTuitionPayment && !$student->schoolFeeDetails->passEightyTuition)
                            <option value="{{ $student->schoolFeeDetails->schoolPayment->structures->sum('amount') - $student->schoolFeeDetails->schoolPaymentTransaction->sum('amount_payed') }}">₦{{ number_format(($student->schoolFeeDetails->schoolPayment->structures->sum('amount') - $student->schoolFeeDetails->schoolPaymentTransaction->sum('amount_payed'))/100, 2) }} - Balance</option>
                            <option value="{{ $student->schoolFeeDetails->schoolPayment->structures->sum('amount')*0.6 }}">₦{{ number_format($student->schoolFeeDetails->schoolPayment->structures->sum('amount')*0.6/100, 2) }} - 60%</option>
                            <option value="{{ $student->schoolFeeDetails->schoolPayment->structures->sum('amount')*0.5 }}">₦{{ number_format($student->schoolFeeDetails->schoolPayment->structures->sum('amount')*0.5/100, 2) }} - 50%</option>
                            <option value="{{ $student->schoolFeeDetails->schoolPayment->structures->sum('amount')*0.4 }}">₦{{ number_format($student->schoolFeeDetails->schoolPayment->structures->sum('amount')*0.4/100, 2) }} - 40%</option>
                            <option value="{{ $student->schoolFeeDetails->schoolPayment->structures->sum('amount')*0.3 }}">₦{{ number_format($student->schoolFeeDetails->schoolPayment->structures->sum('amount')*0.3/100, 2) }} - 30%</option>
                            <option value="{{ $student->schoolFeeDetails->schoolPayment->structures->sum('amount')*0.2 }}">₦{{ number_format($student->schoolFeeDetails->schoolPayment->structures->sum('amount')*0.2/100, 2) }} - 20%</option>
                            <option value="{{ $student->schoolFeeDetails->schoolPayment->structures->sum('amount')*0.1 }}">₦{{ number_format($student->schoolFeeDetails->schoolPayment->structures->sum('amount')*0.1/100, 2) }} - 10%</option>
                            @endif
                            @if($student->schoolFeeDetails->passTuitionPayment && !$student->schoolFeeDetails->fullTuitionPayment && $student->schoolFeeDetails->passEightyTuition)
                            <option value="{{ $student->schoolFeeDetails->schoolPayment->structures->sum('amount') - $student->schoolFeeDetails->schoolPaymentTransaction->sum('amount_payed') }}">₦{{ number_format(($student->schoolFeeDetails->schoolPayment->structures->sum('amount') - $student->schoolFeeDetails->schoolPaymentTransaction->sum('amount_payed'))/100, 2) }} - Balance</option>
                            <option value="{{ $student->schoolFeeDetails->schoolPayment->structures->sum('amount')*0.6 }}">₦{{ number_format($student->schoolFeeDetails->schoolPayment->structures->sum('amount')*0.6/100, 2) }} - 60%</option>
                            <option value="{{ $student->schoolFeeDetails->schoolPayment->structures->sum('amount')*0.5 }}">₦{{ number_format($student->schoolFeeDetails->schoolPayment->structures->sum('amount')*0.5/100, 2) }} - 50%</option>
                            <option value="{{ $student->schoolFeeDetails->schoolPayment->structures->sum('amount')*0.4 }}">₦{{ number_format($student->schoolFeeDetails->schoolPayment->structures->sum('amount')*0.4/100, 2) }} - 40%</option>
                            <option value="{{ $student->schoolFeeDetails->schoolPayment->structures->sum('amount')*0.3 }}">₦{{ number_format($student->schoolFeeDetails->schoolPayment->structures->sum('amount')*0.3/100, 2) }} - 30%</option>
                            <option value="{{ $student->schoolFeeDetails->schoolPayment->structures->sum('amount')*0.2 }}">₦{{ number_format($student->schoolFeeDetails->schoolPayment->structures->sum('amount')*0.2/100, 2) }} - 20%</option>
                            <option value="{{ $student->schoolFeeDetails->schoolPayment->structures->sum('amount')*0.1 }}">₦{{ number_format($student->schoolFeeDetails->schoolPayment->structures->sum('amount')*0.1/100, 2) }} - 10%</option>
                            @endif
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="paymentGateway" class="form-label">Select Payment Gateway<span class="text-danger">*</span></label>
                        <select class="form-select" aria-label="paymentGateway" name="paymentGateway" required onchange="handlePaymentMethodChange(event)">
                            <option value= "" selected>Select Payment Gateway</option>
                            @if(env('UPPERLINK_STATUS'))<option value="Upperlink">Upperlink</option>@endif
                            @if(env('FLUTTERWAVE_STATUS'))<option value="Rave">Flutterwave</option>@endif
                            @if(env('MONNIFY_STATUS'))<option value="Monnify">Monnify</option>@endif
                            @if(env('PAYSTACK_STATUS'))<option value="Paystack">Paystack</option>@endif
                            {{-- @if(env('BANK_TRANSFER_STATUS'))<option value="BankTransfer">Transfer</option>@endif --}}
                        </select>
                    </div>

                    <!-- Primary Alert -->
                    <div class="alert alert-primary alert-dismissible alert-additional fade show" role="alert" style="display: none" id="transferInfo">
                        <div class="alert-body">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <i class="mdi mdi-check-bold fs-16 align-middle"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="alert-heading">Well done !</h5>
                                    <p class="mb-0">Kindly make transfer to the below transaction </p>
                                    <br>
                                    <ul class="list-group">
                                        <li class="list-group-item"><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i><strong>Bank Name:</strong> {{env('BANK_NAME')}}</li>
                                        <li class="list-group-item"><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i><strong>Bank Account Number:</strong> {{env('BANK_ACCOUNT_NUMBER')}}</li>
                                        <li class="list-group-item"><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i><strong>Bank Account Name:</strong> {{env('BANK_ACCOUNT_NAME')}}</li>
                                    </ul>
                                    <br>
                                    <p>Please send proof of payment as an attachment to {{ env('ACCOUNT_EMAIL') }}, including your name, registration number, and purpose of payment. For any inquiries, you can also call {{ env('ACCOUNT_PHONE') }}.</p>
                                </div>
                            </div>
                        </div>
                        <div class="alert-content">
                            <p class="mb-0">NOTE: PLEASE ENSURE TO VERIFY THE TRANSACTION DETAILS PROPERLY. TRANSFER ONLY TO THE ACCOUNT ABOVE. STUDENTS TAKE RESPONSIBILITY FOR ANY MISPLACEMENT OF FUNDS.</p>
                        </div>
                    </div>

                    <div>
                        <button type="submit" id="submit-button" class="btn btn-primary">Make payment</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="topUpWallet" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Deposit into Wallet</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body border-top border-top-dashed">
                <form action="{{ url('/guardian/makePayment') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name='student_id' value="{{ $student->id }}">
                    <input type="hidden" name='programme_id' value="{{ $student->programme->id }}">
                    <input type="hidden" name="payment_id" value="0">

                    <div class="mb-3">
                        <label for="amount" class="form-label">Payment Amount<span class="text-danger">*</span></label>
                        <input type='number' name='amount' class="form-control" required placeholder="Enter Deposit Amount">
                    </div>

                    <div class="mb-3">
                        <label for="paymentGateway" class="form-label">Select Payment Gateway<span class="text-danger">*</span></label>
                        <select class="form-select" aria-label="paymentGateway" name="paymentGateway" required onchange="handlePaymentMainMethodChange(event)">
                            <option value= "" selected>Select Payment Gateway</option>
                            @if(env('UPPERLINK_STATUS'))<option value="Upperlink">Upperlink</option>@endif
                            @if(env('FLUTTERWAVE_STATUS'))<option value="Rave">Flutterwave</option>@endif
                            @if(env('MONNIFY_STATUS'))<option value="Monnify">Monnify</option>@endif
                            @if(env('PAYSTACK_STATUS'))<option value="Paystack">Paystack</option>@endif
                            {{-- @if(env('BANK_TRANSFER_STATUS'))<option value="BankTransfer">Transfer</option>@endif --}}
                        </select>
                    </div>

                    <!-- Primary Alert -->
                    <div class="alert alert-primary alert-dismissible alert-additional fade show" role="alert" style="display: none" id="transferInfoMain">
                        <div class="alert-body">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <i class="mdi mdi-check-bold fs-16 align-middle"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="alert-heading">Well done !</h5>
                                    <p class="mb-0">Kindly make transfer to the below transaction </p>
                                    <br>
                                    <ul class="list-group">
                                        <li class="list-group-item"><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i><strong>Bank Name:</strong> {{env('BANK_NAME')}}</li>
                                        <li class="list-group-item"><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i><strong>Bank Account Number:</strong> {{env('BANK_ACCOUNT_NUMBER')}}</li>
                                        <li class="list-group-item"><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i><strong>Bank Account Name:</strong> {{env('BANK_ACCOUNT_NAME')}}</li>
                                    </ul>
                                    <br>
                                    <p>Please send proof of payment as an attachment to {{ env('ACCOUNT_EMAIL') }}, including your name, registration number, and purpose of payment. For any inquiries, you can also call {{ env('ACCOUNT_PHONE') }}.</p>
                                </div>
                            </div>
                        </div>
                        <div class="alert-content">
                            <p class="mb-0">NOTE: PLEASE ENSURE TO VERIFY THE TRANSACTION DETAILS PROPERLY. TRANSFER ONLY TO THE ACCOUNT ABOVE. STUDENTS TAKE RESPONSIBILITY FOR ANY MISPLACEMENT OF FUNDS.</p>
                        </div>
                    </div>

                    <div>
                        <button type="submit" id="submit-button-main" class="btn btn-primary">Make payment</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="payAccomondation" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Pay Accomondation</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body border-top border-top-dashed">
                @if(!empty($student->accomondationDetails->ACCOMMODATIONPaymentTransactions) && $student->accomondationDetails->ACCOMMODATIONPaymentTransactions->count() > 0)
                <div class="mt-2">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="hover" style="width:150px;height:150px">
                    </lord-icon>
                    <p class="mb-3 mt-4">You have successfully made payment for accomondation, kindly inform your ward to login he/her portal and select a room of her choice. Thank you.</p>
                </div>

                @elseif(empty($student->applicant->gender))
                <div class="mt-2">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="hover" style="width:150px;height:150px">
                    </lord-icon>
                    <p class="mb-3 mt-4">Kindly inform you ward to update gender on he/her portal. Thank you.</p>
                </div>
                @else
                <form action="{{ url('/guardian/makePayment') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name='student_id' value="{{ $student->id }}">
                    <input type="hidden" name="payment_id" value="{{ $student->accomondationDetails->ACCOMMODATIONPayment->id }}">
                    <input type="hidden" id="studentGender" class="gender" name="gender" value="{{ $student->applicant->gender }}">

                    <div class="col-lg-12 mb-3">
                        <div class="form-floating">
                            <select class="form-select" id="campus" aria-label="role" name="campus" onchange="handleCampusChange(event)" required>
                                <option selected value="">Select Option </option>
                                <option value="West">West Campus</option>
                                <option value="East">East Campus</option>
                            </select>
                            <label for="campus" class="form-label">Select Campus</label>
                        </div>
                    </div>
                    

                    <div class="col-lg-12 mb-3">
                        <div class="form-floating">
                            <select class="form-select" id="roomType" name="type_id" aria-label="roomType" required>
                                <option value="" selected>--Select--</option>
                            </select>
                            <label for="roomType">Room Type</label>
                        </div>
                    </div>


                    
                    <div class="col-lg-12 mb-3">
                        <div class="form-floating">
                            <select class="form-select" aria-label="paymentGateway" name="paymentGateway" required onchange="handlePaymentMethodChange(event)">
                                <option value= "" selected>Select Payment Gateway</option>
                                @if(env('UPPERLINK_STATUS'))<option value="Upperlink">Upperlink</option>@endif
                                @if(env('FLUTTERWAVE_STATUS'))<option value="Rave">Flutterwave</option>@endif
                                @if(env('MONNIFY_STATUS'))<option value="Monnify">Monnify</option>@endif
                                @if(env('PAYSTACK_STATUS'))<option value="Paystack">Paystack</option>@endif
                                {{-- @if(env('BANK_TRANSFER_STATUS'))<option value="BankTransfer">Transfer</option>@endif --}}
                            </select>
                            <label for="paymentGateway" class="form-label">Select Payment Gateway<span class="text-danger">*</span></label>
                        </div>
                    </div>

                    <!-- Primary Alert -->
                    <div class="alert alert-primary alert-dismissible alert-additional fade show" role="alert" style="display: none" id="transferInfo">
                        <div class="alert-body">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <i class="mdi mdi-check-bold fs-16 align-middle"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="alert-heading">Well done !</h5>
                                    <p class="mb-0">Kindly make transfer to the below transaction </p>
                                    <br>
                                    <ul class="list-group">
                                        <li class="list-group-item"><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i><strong>Bank Name:</strong> {{env('BANK_NAME')}}</li>
                                        <li class="list-group-item"><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i><strong>Bank Account Number:</strong> {{env('BANK_ACCOUNT_NUMBER')}}</li>
                                        <li class="list-group-item"><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i><strong>Bank Account Name:</strong> {{env('BANK_ACCOUNT_NAME')}}</li>
                                    </ul>
                                    <br>
                                    <p>Please send proof of payment as an attachment to {{ env('ACCOUNT_EMAIL') }}, including your name, registration number, and purpose of payment. For any inquiries, you can also call {{ env('ACCOUNT_PHONE') }}.</p>
                                </div>
                            </div>
                        </div>
                        <div class="alert-content">
                            <p class="mb-0">NOTE: PLEASE ENSURE TO VERIFY THE TRANSACTION DETAILS PROPERLY. TRANSFER ONLY TO THE ACCOUNT ABOVE. STUDENTS TAKE RESPONSIBILITY FOR ANY MISPLACEMENT OF FUNDS.</p>
                        </div>
                    </div>

                    <div>
                        <button type="submit" id="submit-button" class="btn btn-primary">Make payment</button>
                    </div>
                </form>
                @endif
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@endsection
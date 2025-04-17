@extends('student.layout.dashboard')
@php
use \App\Models\ResultApprovalStatus;

$student = Auth::guard('student')->user();
$qrcode = 'https://quickchart.io/chart?chs=300x300&cht=qr&chl='.env('APP_URL').'/studentDetails/'.$student->slug;
$name = $student->applicant->lastname.' '.$student->applicant->othernames;
$studentAdvisoryData = (object) $student->getAcademicAdvisory();
$failedCourses = $student->registeredCourses()->where('grade', 'F')->where('re_reg', null)->where('result_approval_id', ResultApprovalStatus::getApprovalStatusId(ResultApprovalStatus::SENATE_APPROVED))->get();

$stage = 0;
@endphp
@section('content')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Suspension Details</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Suspension Details</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-lg-12">
        <div class="card mt-n4 mx-n4">
            <div class="bg-soft-info">
                <div class="card-body pb-0 px-4">
                    <div class="row mb-3">
                        <div class="col-md">
                            <div class="row align-items-center g-3">
                                <div class="col-md-auto">
                                    <div class="avatar-md">
                                        <div class="avatar-title bg-white rounded-circle">
                                            <img src="{{ !empty($student->image) ? $student->image : asset('assets/images/users/user-dummy-img.jpg') }}" alt="" class="img-thumbnail rounded-circle avatar-md">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md">
                                    <div>
                                        <h4 class="fw-bold">{{ $student->applicant->lastname .' '. $student->applicant->othernames }}</h4>
                                        <div class="hstack gap-3 flex-wrap">
                                            <div><i class="ri-building-line align-bottom me-1"></i> {{  $student->academic_status }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end card body -->
            </div>
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->
</div>

<div class="row">
    <div class="col-xl-4">
        <div class="card card-height-100">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-xl-12">
                        <h6 class="fs-14 mb-2">Suspension Details</h6>
                        <hr>
                        <p class="text-muted"><strong>Academic Session:</strong> {{ $suspension->academic_session }} Academic Session</p>
                        <p class="text-muted"><strong>Reason:</strong> {!! $suspension->reason !!}</p>
                        <p class="text-muted"><strong>Suspension Letter:</strong> {!! $suspension->file !!}</p>
                        <p class="text-muted"><strong>Start Date:</strong> {{ \Carbon\Carbon::parse($suspension->start_date)->format('jS \o\f F, Y') }}</p>
                        <p class="text-muted"><strong>End Date:</strong> {{ $suspension->end_date? \Carbon\Carbon::parse($suspension->end_date)->format('jS \o\f F, Y') : '' }}</p>
                    </div>
                    <!-- end col -->
                </div>    
                <hr>
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
                            <strong>Promotion Eligibility:</strong> {{ is_null($student->cgpa) || $student->cgpa == 0 ? 'You are a fresh student; promotion eligibility will be determined after your first semester.' : ($studentAdvisoryData->promotion_eligible ? 'You are eligible to promote.' : 'You are not eligible to promote.') }}
                            <strong>Promotion Message:</strong> {{ $studentAdvisoryData->promotion_message }}<br>
                            <strong>GPA Trend:</strong> {{ $studentAdvisoryData->trajectory_analysis['cgpa_trend'] }}<br>
                            <strong>CGPA Trajectory Analysis:</strong> {{ $studentAdvisoryData->trajectory_analysis['academic_risk'] }}<br>
                            <strong>Course Strength:</strong> @foreach($studentAdvisoryData->trajectory_analysis['strengths'] as $strength) {{ $strength.', ' }} @endforeach<br>
                            <strong>Course Weakness:</strong> @foreach($studentAdvisoryData->trajectory_analysis['weaknesses'] as $weakness) {{ $weakness.', ' }} @endforeach<br>
                            <strong>Tips:</strong> @foreach($studentAdvisoryData->trajectory_analysis['tips'] as $tips) {{ $tips }} @endforeach<br>
                        </p>
                    </div>

                    <div class="border-top border-top-dashed mb-3">
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
            </div>
        </div>

        
    </div>
    <!--end col-->
    <div class="col-xl-8">

        <div class="card card-height-100">
            <div class="card-header border-bottom-dashed align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Suspension Approval Activities</h4>
            </div><!-- end cardheader -->
            <div class="card-body p-0">
                <div data-simplebar="init" style="max-height: ;" class="p-3 simplebar-scrollable-y"><div class="simplebar-wrapper" style="margin: -16px;"><div class="simplebar-height-auto-observer-wrapper"><div class="simplebar-height-auto-observer"></div></div><div class="simplebar-mask"><div class="simplebar-offset" style="right: 0px; bottom: 0px;"><div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content" style="height: auto; overflow: hidden scroll;"><div class="simplebar-content" style="padding: 16px;">
                    
                    <div class="acitivity-timeline acitivity-main">
                        @php $stage = 0; @endphp
                        
                        @if(empty($suspension->transaction_id))
                            <div class="acitivity-item d-flex mb-3">
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">Payment is pending, kindly proceed to pay for re-admission</h6>
                                    <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#paymentModal">Pay Now</button>
                                </div>
                            </div>
                        @else
                            <div class="acitivity-item d-flex mb-3">
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-2 ms-2">- Payment received</h6>
                                </div>
                            </div>
                            @php $stage = 1; @endphp
                        @endif
                    
                        @if($stage >= 1)
                            <div class="acitivity-item d-flex mb-3">
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-2 ms-2">
                                        - {{ empty($suspension->court_affidavit) ? 'Court affidavit upload is pending' : 'Court affidavit uploaded' }}
                            
                                        @if(!empty($suspension->court_affidavit))
                                            <br>
                                            <img class="img-thumbnail mt-3" width="50%" src="{{ url('uploads/'.$suspension->court_affidavit) }}">
                                            <hr style="width:30%">
                                            <a href="{{ url('uploads/'.$suspension->court_affidavit) }}" target="_blank" class="btn btn-sm btn-secondary">View</a>
                                        @endif
                                        <hr width="30%">
                                        <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#courtaffidavitModal">Upload Court affidavit</button>
                                    </h6>
                                </div>
                            </div>
                            @php $stage = !empty($suspension->court_affidavit) ? 2 : $stage; @endphp
                        @endif
                    
                        @if($stage >= 2)
                            <div class="acitivity-item d-flex mb-3">
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-2 ms-2">
                                        - {{ empty($suspension->undertaking_letter) ? 'Guardian letter of undertaking upload is pending' : 'Guardian letter uploaded' }}
                                        
                                        @if(!empty($suspension->undertaking_letter))
                                            <br>
                                            <img class="img-thumbnail mt-3" width="50%" src="{{ url('uploads/'.$suspension->undertaking_letter) }}">
                                            <hr style="width:30%">
                                            <a href="{{ url('uploads/'.$suspension->undertaking_letter) }}" target="_blank" class="btn btn-sm btn-secondary">View</a>
                                        @endif
                                        <hr width="30%">
                                        <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#undertakingletterModal">Upload guardian letter of undertaking</button>
                                    </h6>
                                </div>
                            </div>
                            @php $stage = !empty($suspension->undertaking_letter) ? 3 : $stage; @endphp
                        @endif
                    
                        @if($stage >= 3)
                            <div class="acitivity-item d-flex mb-3">
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-2 ms-2">
                                        - {{ empty($suspension->traditional_ruler_reference) ? 'Traditional ruler reference upload is pending' : 'Traditional ruler reference uploaded' }}
                                        
                                        @if(!empty($suspension->traditional_ruler_reference))
                                            <br>
                                            <img class="img-thumbnail mt-3" width="50%" src="{{ url('uploads/'.$suspension->traditional_ruler_reference) }}">
                                            <hr style="width:30%">
                                            <a href="{{ url('uploads/'.$suspension->traditional_ruler_reference) }}" target="_blank" class="btn btn-sm btn-secondary">View</a>
                                        @endif
                                        <hr width="30%">
                                        <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#traditionalrulerreferenceModal">Upload traditional ruler reference</button>
                                    </h6>
                                </div>
                            </div>
                            @php $stage = !empty($suspension->traditional_ruler_reference) ? 4 : $stage; @endphp
                        @endif
                    
                        @if($stage >= 4)
                            <div class="acitivity-item d-flex mb-3">
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-2 ms-2">
                                        - {{ empty($suspension->ps_reference) ? 'Public servant reference upload is pending' : 'Public servant reference uploaded' }}
                                        
                                        @if(!empty($suspension->ps_reference))
                                            <br>
                                            <img class="img-thumbnail mt-3" width="50%" src="{{ url('uploads/'.$suspension->ps_reference) }}">
                                            <hr style="width:30%">
                                            <a href="{{ url('uploads/'.$suspension->ps_reference) }}" target="_blank" class="btn btn-sm btn-secondary">View</a>
                                        @endif
                                        <hr width="30%">
                                        <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#psreferenceModal">Upload public servant reference</button>
                                    </h6>
                                </div>
                            </div>
                            @php $stage = !empty($suspension->ps_reference) ? 5 : $stage; @endphp
                        @endif
                    
                        @if($stage >= 5)
                            <div class="acitivity-item d-flex mb-3">
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-2 ms-2">- {{ empty($suspension->admin_comment) ? 'Admin review is pending' : 'Admin has reviewed your documents' }}</h6>
                                    {!! $suspension->admin_comment !!}
                                </div>
                            </div>
                            @php $stage = !empty($suspension->admin_comment) ? 6 : $stage; @endphp
                        @endif
                    
                        @if($stage == 6)
                            <div class="acitivity-item d-flex mb-3">
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-2 ms-2">-Your re-admission process has been completed successfully.</h6>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                </div></div></div></div><div class="simplebar-placeholder" style="width: 342px; height: 895px;"></div></div><div class="simplebar-track simplebar-horizontal" style="visibility: hidden;"><div class="simplebar-scrollbar" style="width: 0px; display: none;"></div></div><div class="simplebar-track simplebar-vertical" style="visibility: visible;"><div class="simplebar-scrollbar" style="height: 148px; transform: translate3d(0px, 0px, 0px); display: block;"></div></div></div>
            </div><!-- end card body -->
        </div>
    </div>
</div>
<!--end row-->


<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header p-3">
                <h5 class="modal-title text-uppercase fw-bold" id="paymentModalLabel">Re-admission Fee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body border-top border-top-dashed">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="pills-bill-info" role="tabpanel" aria-labelledby="pills-bill-info-tab">
                        
                        <form action="{{ url('/student/makePayment') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="payment_id" value="{{ $suspensionPayment->id }}">
                            <input type="hidden" name="student_id" value="{{ $student->id }}">
                            <input type="hidden" name="amount" value="{{ $suspensionPayment->structures->sum('amount') }}">
                            <input type="hidden" name="suspension_id" value="{{ $suspension->id }}">

                            <div class="alert alert-warning text-center">
                                <strong>Notice:</strong> You are about to make a payment of <strong>₦{{ number_format($suspensionPayment->structures->sum('amount')/100, 2) }}</strong>.  
                                This payment is <strong>non-refundable</strong>. Please ensure that all details are correct before proceeding.
                            </div>
        
                            <div class="mb-3">
                                <label for="paymentGateway" class="form-label">Select Payment Gateway<span class="text-danger">*</span></label>
                                <select class="form-select" aria-label="paymentGateway" name="paymentGateway" required onchange="handlePaymentMethodChange(event)">
                                    <option value= "" selected>Select Payment Gateway</option>
                                    @if(env('UPPERLINK_STATUS'))<option value="Upperlink">Upperlink</option>@endif
                                    @if(env('FLUTTERWAVE_STATUS'))<option value="Rave">Flutterwave</option>@endif
                                    @if(env('MONNIFY_STATUS'))<option value="Monnify">Monnify</option>@endif
                                    @if(env('PAYSTACK_STATUS'))<option value="Paystack">Paystack</option>@endif
                                    @if(env('BANK_TRANSFER_STATUS'))<option value="BankTransfer">Transfer</option>@endif
                                    @if(env('WALLET_STATUS'))<option value="Wallet">Wallet</option>@endif
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
        
                            <div class="border-top border-top-dashed ">
                                <button type="submit" id="submit-button" id='submit-button' class="btn btn-primary mt-1">Make payment</button>
                            </div>
                        </form>
                    </div>
                    <!-- end tab pane -->
                </div>
                <!-- end tab content -->
            </div>
        </div>
    </div>
</div>
<!--end modal-->

<!-- Upload Modals -->
@foreach(['court_affidavit', 'undertaking_letter', 'traditional_ruler_reference', 'ps_reference'] as $doc)
<div id="{{ str_replace('_', '', $doc) }}Modal" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload {{ ucfirst(str_replace('_', ' ', $doc)) }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ url('student/manageSuspension') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="suspension_id" value="{{ $suspension->id }}">
                    <input type="file" name="{{ $doc }}" class="form-control" required>
                    <button type="submit" class="btn btn-success mt-3">Upload</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection

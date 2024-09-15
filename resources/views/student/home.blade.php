@extends('student.layout.dashboard')
@php

$student = Auth::guard('student')->user();
$qrcode = 'https://quickchart.io/chart?chs=300x300&cht=qr&chl='.env('APP_URL').'/studentDetails/'.$student->slug;
$name = $student->applicant->lastname.' '.$student->applicant->othernames;
$transactions = $student->transactions()->orderBy('created_at', 'desc')->take(10)->get();
$studentRegistrations = $student->courseRegistrationDocument()->orderBy('created_at', 'desc')->take(10)->get();

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
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row mb-5">
    <div class="col-12">
        <div class="d-flex align-items-lg-center flex-lg-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-16 mb-1"><span id="greeting">Hello</span>, {{ $name }}!</h4>
                <p class="text-muted mb-0">Here's what's happening with your dashboard today.</p>
            </div>
            <div class="mt-3 mt-lg-0">
                <form action="javascript:void(0);">
                    <div class="row g-3 mb-0 align-items-center">
                        
                        
                        <!--end col-->
                        <div class="col-auto">
                            <button type="button" class="btn btn-soft-info btn-icon waves-effect waves-light layout-rightside-btn shadow-none"><i class="mdi mdi-account"></i></button>
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                </form>
            </div>
        </div><!-- end card header -->
    </div>
</div>
@if(empty($student->linkedIn))
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="mb-sm-0">Update LinkedIn Url</h4>
                <div class="flex-shrink-0">
                </div>
            </div><!-- end card header -->

            <div class="card-body">
                <div class="row">
                    <div class="col-md-8 offset-md-2 ">
                        <div class="bg-soft-info p-2">
                            <p>As part of our ongoing efforts to enhance your academic and professional development, we strongly encourage you to create a LinkedIn profile. LinkedIn is a valuable platform for networking, learning, and showcasing your skills and accomplishments to potential employers and collaborators. click <a href="javascript:void(0)" class="text-danger" data-bs-toggle="modal" data-bs-target="#read">here</a>
                                to read more</p>
                        </div>
                        
                        <form action="{{ url('student/setMode') }}" method="post" enctype="multipart/form-data">
                            @csrf

                            <div class="row mt-3 g-3">        
                                <div class="col-lg-12">
                                    <div class="form-floating">
                                        <input type="url" class="form-control" id="linkedIn" name="linkedIn" placeholder="Enter LinkedIn Profile Link" required>
                                        <label for="linkedIn">Student Linkedin Profile Link</label>
                                    </div>
                                </div>

                                <input type="hidden" name="student_id" value="{{ $student->id }}">

                                <div class="mb-3">
                                    <label for="choices-publish-status-input" class="form-label">Set Dashboard Theme</label>
                                    <select class="form-select" name="dashboard_mode" id="choices-publish-status-input" data-choices data-choices-search-false required>
                                        <option value="" selected>Choose...</option>
                                        <option value="dark">Dark</option>
                                        <option value="light">Light</option>
                                    </select>
                                </div>
        
                
                                <!--end col-->
                                <div class="col-lg-12 border-top border-top-dashed">
                                    <div class="d-flex align-items-start gap-3 mt-3">
                                        <button type="submit" id="submit-button" class="btn btn-primary btn-label right ms-auto nexttab" data-nexttab="pills-bill-address-tab"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i> Submit</button>
                                    </div>
                                </div>
                                <!--end col-->
                            </div>    
                        </form>
                    </div>
                </div>

            </div>
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end row -->
@else
<div class="row">
    <div class="col-xxl-4">
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
                            <strong>Jamb Reg. Number:</strong> {{ $student->applicant->jamb_reg_no }} <br>
                            <strong>Academic Level:</strong> <span class="text-primary">{{ $student->level_id * 100 }} Level</span><br>
                            <strong>Academic session:</strong> {{ $student->academic_session }}</span>
                            <br><br>
                            @if($student->level_id >= $student->programme->duration && !$student->is_passed_out)
                            <span class="text-warning"><strong>Graduating Set</strong></span> <br><br>
                            @endif
                            <strong>Support Code:</strong> <span class="text-danger">{{ $student->applicant->id }}-ST{{ sprintf("%03d", $student->id) }}</span> 
                        </p>
                        <p class="text-muted border-top border-top-dashed pt-2"><strong>CGPA:</strong> {{ $student->cgpa }} <br>
                            <strong>Class:</strong> {{ $student->degree_class }}<br>
                            <strong>Standing:</strong> {{ $student->standing }}<br>
                        </p>

                        @if($student->level_id >= $student->programme->duration && $student->is_passed_out)
                            <div class="alert alert-success alert-dismissible alert-additional shadow fade show mb-0" role="alert">
                                <div class="alert-body">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <i class="ri-alert-line fs-16 align-middle"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="alert-heading">Congratulations!!!</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="alert-content">
                                    <p class="mb-0">You are have come to the end of your programme and been prepared for graduation</p>
                                </div>
                            </div>

                            <hr>
                            @if(empty($student->finalClearance))
                            <button type="button" class="btn btn-primary btn-label right ms-auto nexttab" data-nexttab="pills-bill-address-tab" data-bs-toggle="modal" data-bs-target="#startClearance"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i> Start Final Clearance Process</button>
                            @else
                            <button type="button" class="btn btn-info btn-label right ms-auto nexttab" data-nexttab="pills-bill-address-tab" data-bs-toggle="modal" data-bs-target="#clearanceStatus"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Check Clearance Status</button>
                            @endif

                        @endif
                    </div>



                    <div class="table-responsive border-top border-top-dashed mt-4">
                        <table class="table mb-0 table-borderless">
                            <tbody>
                                <tr>
                                    <th><span class="fw-medium">Link:</span></th>
                                    <td><a href="{{env('ADMISSION_URL').'?ref='.$student->referral_code}}" target="_blank" id="myLink">{{env('ADMISSION_URL').'?ref='.$student->referral_code}}</a>  <button class="btn btn-sm btn-info" id="copyButton"><i class="ri-file-copy-fill"></i></button></td>
                                </tr>
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
        <!--end card-->
    </div>
    <!--end col-->

    <div class="col-xxl-8">
        @if(env('WALLET_STATUS'))
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-light text-primary rounded-circle shadow fs-3">
                                    <i class="ri-money-dollar-circle-fill align-middle"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">
                                    Total Deposit</p>
                                <h4 class=" mb-0">₦<span class="counter-value" data-target="{{ $totalDeposit/100 }}">0</span></h4>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end col -->
            <div class="col-lg-4 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-light text-primary rounded-circle shadow fs-3">
                                    <i class="ri-money-dollar-circle-fill align-middle"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">
                                    Total Expenditure</p>
                                <h4 class=" mb-0">₦<span class="counter-value" data-target="{{$totalExpenditure/100}}">0</span></h4>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end col -->
            <div class="col-lg-4 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-light text-primary rounded-circle shadow fs-3">
                                    <i class="ri-money-dollar-circle-fill align-middle"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">Wallet Balance</p>
                                <h4 class=" mb-0">₦<span class="counter-value" data-target="{{$student->amount_balance/100 }}">0</span></h4>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end col -->
        </div><!-- end row -->
        @endif

        <div class="card">
            <div class="card-header border-0 align-items-center d-flex bg-info">
                <h4 class="card-title mb-0 flex-grow-1 text-white">University Information Board</h4>
            </div><!-- end card header -->

           <div class="card-body border-top border-top-dashed">
                <div class="vstack gap-2">
                    <marquee behavior="alternate">{!! strip_tags($pageGlobalData->sessionSetting->campus_wide_message) !!}</marquee>
                </div>
            </div>
        </div>


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
                                    <a href="{{ asset($student->admission_letter) }}"  class="btn btn-icon text-muted btn-sm fs-18 shadow-none"><i class="ri-download-2-line"></i></a>
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
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Course Registration </h4>
            </div><!-- end card header -->

            <div class="card-body border-top border-top-dashed table-responsive">
                <!-- Bordered Tables -->
                <table class="display table table-stripped" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Academic Session</th>
                            <th scope="col">Level Adviser Status</th>
                            <th scope="col">HOD Status</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($studentRegistrations as $studentRegistration)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $studentRegistration->academic_session }}</td>
                            <td><span class="badge badge-soft-{{ $studentRegistration->level_adviser_status == 1 ? 'success' : 'warning' }}">{{ $studentRegistration->level_adviser_status == 1 ? 'Approved' : 'Pending' }}</span></td>
                            <td><span class="badge badge-soft-{{ $studentRegistration->hod_status == 1 ? 'success' : 'warning' }}">{{ $studentRegistration->hod_status == 1 ? 'Approved' : 'Pending' }}</span></td>
                            <td>
                                <a href="{{ asset($studentRegistration->file) }}" target="_blank" style="margin: 5px" class="btn btn-warning">Download Form</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div><!-- end card -->

        <div class="card">
            <div class="card-header border-0 align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Transactions</h4>
            </div><!-- end card header -->

            <div class="card-body pb-2 border-top border-top-dashed">
                <div class="table-responsive">
                    <table id="buttons-datatables" class="display table table-stripped" style="width:100%">
                        <thead>
                            <tr>
                                <th scope="col">Id</th>bhm
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
                                            <form action="{{ url('/student/makePayment') }}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name='programme_id' value="{{ $student->programme->id }}">
                                                <input type="hidden" name="student_id" value="{{ $student->id }}">
                                                <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">
                                                <input type="hidden" name="payment_id" value="{{ !empty($transaction->paymentType)? $transaction->paymentType->id : 0 }}">
                                                <input type="hidden" name="reference" value="{{ $transaction->reference }}">
                                                <input type="hidden" name="amount" value="{{ $transaction->amount_payed }}">
                                                
                                                <div class="mb-3">
                                                    <label for="paymentGateway" class="form-label">Select Payment Gateway<span class="text-danger">*</span></label>
                                                    <select class="form-select" aria-label="paymentGateway" name="paymentGateway" required onchange="handlePaymentMethodChange(event)">
                                                        <option value= "" selected>Select Payment Gateway</option>
                                                        @if(env('FLUTTERWAVE_STATUS'))<option value="Rave">Flutterwave</option>@endif
                                                        @if(env('PAYSTACK_STATUS'))<option value="Paystack">Paystack</option>@endif
                                                        @if(env('BANK_TRANSFER_STATUS'))<option value="BankTransfer">Transfer</option>@endif
                                                        @if(env('WALLET_STATUS'))<option value="Wallet">Wallet</option>@endif
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
                                                    <button type="submit" id="submit-button" id='submit-button' class="btn btn-primary">Make payment</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div><!-- /.modal-content -->
                                </div><!-- /.modal-dialog -->
                            </div><!-- /.modal -->
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div><!-- end card body -->
        </div><!-- end card -->

    </div>
    <!--end col-->
</div>
<!--end row-->
@endif



<div id="startClearance" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Start Your Clearance Process</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body border-top border-top-dashed">
                <form action="{{ url('/student/startClearance') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="experience" class="form-label">Share Your TAU Experience</label>
                        <textarea class="form-control ckeditor" name="experience" id="experience" ></textarea>
                    </div>

                    <div>
                        <button type="submit" id='submit-button-main' class="btn btn-primary">Proceed</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@if($student->finalClearance)
<div id="clearanceStatus" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Clearance Status</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body border-top border-top-dashed">
                <div class="row">
                    <div class="col-md-6 border-end">
                        <div class="card-body p-0" style="height: 400px">
                            <div class="simplebar-mask">
                                <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                                    <div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content">
                                        <div class="simplebar-content" style="padding: 16px;">
                                            <div class="acitivity-timeline acitivity-main">
                                                <!-- HOD Activity -->
                                                @if(!empty($student->finalClearance->hod_id) && $student->finalClearance->hod)
                                                <div class="acitivity-item d-flex mb-3">
                                                    <div class="flex-shrink-0">
                                                        <img src="{{ $student->finalClearance->hod->image }}" alt="" class="avatar-xs rounded-circle acitivity-avatar shadow">
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h6 class="mb-1">{{ $student->finalClearance->hod->title.' '.$student->finalClearance->hod->lastname.' '.$student->finalClearance->hod->othernames }}</h6>
                                                        <p class="text-muted mb-2 fst-italic">{{ ucwords($student->finalClearance->hod_status) }}</p>
                                                        <small class="mb-0 text-muted">Comment: {!! strip_tags($student->finalClearance->hod_comment) !!}</small>
                                                    </div>
                                                </div>
                                                @endif
                
                                                <!-- Dean Activity -->
                                                @if(!empty($student->finalClearance->dean_id) && $student->finalClearance->dean)
                                                <div class="acitivity-item d-flex mb-3">
                                                    <div class="flex-shrink-0">
                                                        <img src="{{ $student->finalClearance->dean->image }}" alt="" class="avatar-xs rounded-circle acitivity-avatar shadow">
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h6 class="mb-1">{{ $student->finalClearance->dean->title.' '.$student->finalClearance->dean->lastname.' '.$student->finalClearance->dean->othernames }}</h6>
                                                        <p class="text-muted mb-2 fst-italic">{{ ucwords($student->finalClearance->dean_status) }}</p>
                                                        <small class="mb-0 text-muted">Comment: {!! strip_tags($student->finalClearance->dean_comment) !!}</small>
                                                    </div>
                                                </div>
                                                @endif
                
                                                <!-- Registrar Activity -->
                                                @if(!empty($student->finalClearance->registrar_id) && $student->finalClearance->registrar)
                                                <div class="acitivity-item d-flex mb-3">
                                                    <div class="flex-shrink-0">
                                                        <img src="{{ $student->finalClearance->registrar->image }}" alt="" class="avatar-xs rounded-circle acitivity-avatar shadow">
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h6 class="mb-1">{{ $student->finalClearance->registrar->title.' '.$student->finalClearance->registrar->lastname.' '.$student->finalClearance->registrar->othernames }}</h6>
                                                        <p class="text-muted mb-2 fst-italic">{{ ucwords($student->finalClearance->registrar_status) }}</p>
                                                        <small class="mb-0 text-muted">Comment: {!! strip_tags($student->finalClearance->registrar_comment) !!}</small>
                                                    </div>
                                                </div>
                                                @endif
                
                                                <!-- Bursary Activity -->
                                                @if(!empty($student->finalClearance->bursary_id) && $student->finalClearance->bursary)
                                                <div class="acitivity-item d-flex mb-3">
                                                    <div class="flex-shrink-0">
                                                        <img src="{{ $student->finalClearance->bursary->image }}" alt="" class="avatar-xs rounded-circle acitivity-avatar shadow">
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h6 class="mb-1">{{ $student->finalClearance->bursary->title.' '.$student->finalClearance->bursary->lastname.' '.$student->finalClearance->bursary->othernames }}</h6>
                                                        <p class="text-muted mb-2 fst-italic">{{ ucwords($student->finalClearance->bursary_status) }}</p>
                                                        <small class="mb-0 text-muted">Comment: {!! strip_tags($student->finalClearance->bursary_comment) !!}</small>
                                                    </div>
                                                </div>
                                                @endif
                
                                                <!-- Library Activity -->
                                                @if(!empty($student->finalClearance->library_id) && $student->finalClearance->librarian)
                                                <div class="acitivity-item d-flex mb-3">
                                                    <div class="flex-shrink-0">
                                                        <img src="{{ $student->finalClearance->librarian->image }}" alt="" class="avatar-xs rounded-circle acitivity-avatar shadow">
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h6 class="mb-1">{{ $student->finalClearance->librarian->title.' '.$student->finalClearance->librarian->lastname.' '.$student->finalClearance->librarian->othernames }}</h6>
                                                        <p class="text-muted mb-2 fst-italic">{{ ucwords($student->finalClearance->library_status) }}</p>
                                                        <small class="mb-0 text-muted">Comment: {!! strip_tags($student->finalClearance->library_comment) !!}</small>
                                                    </div>
                                                </div>
                                                @endif
                
                                                <!-- Student Care Dean Activity -->
                                                @if(!empty($student->finalClearance->student_care_dean_id) && $student->finalClearance->student_care_dean)
                                                <div class="acitivity-item d-flex mb-3">
                                                    <div class="flex-shrink-0">
                                                        <img src="{{ $student->finalClearance->student_care_dean->image }}" alt="" class="avatar-xs rounded-circle acitivity-avatar shadow">
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h6 class="mb-1">{{ $student->finalClearance->student_care_dean->title.' '.$student->finalClearance->student_care_dean->lastname.' '.$student->finalClearance->student_care_dean->othernames }}</h6>
                                                        <p class="text-muted mb-2 fst-italic">{{ ucwords($student->finalClearance->student_care_dean_status) }}</p>
                                                        <small class="mb-0 text-muted">Comment: {!! strip_tags($student->finalClearance->student_care_dean_comment) !!}</small>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        {!! $student->finalClearance->experience !!}
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                    @if(!empty($student->finalClearance) && $student->finalClearance->status == 'approved')
                    <form action="{{ url('/student/downloadClearance') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div>
                            <button type="submit" id='submit-button-main' class="btn btn-primary">Download Clearance</button>
                        </div>
                    </form>
                    @endif
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endif
@endsection
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
                            <strong>Jamb Reg. Number:</strong> {{ $student->applicant->jamb_reg_no }}<br><br>
                            <strong>Support Code:</strong> <span class="text-danger">ST{{ sprintf("%06d", $student->id) }}</span> 
                        </p>
                        <p class="text-muted border-top border-top-dashed"><strong>CGPA:</strong> {{ $student->cgpa }} <br>
                            <strong>Class:</strong> {{ $student->degree_class }}<br>
                            <strong>Standing:</strong> {{ $student->standing }}<br>
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

<div class="modal fade" id="read" tabindex="-1" role="dialog" aria-labelledby="read" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="read">More about LinkedIn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <h6 class="fs-15">Here are a few benefits of having a LinkedIn profile:</h6>
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="text-muted mb-0"><strong>Professional Networking:</strong> Connect with alumni, professors, and professionals in your field of interest.</p>
                    </div>
                </div>
                <div class="d-flex mt-2">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2 ">
                        <p class="text-muted mb-0"><strong>Career Opportunities:</strong> Discover job and internship opportunities tailored to your skills and interests.</p>
                    </div>
                </div>
                <div class="d-flex mt-2">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2 ">
                        <p class="text-muted mb-0"><strong>Skill Development:</strong> Join groups and follow influencers to stay updated on industry trends and enhance your knowledge.</p>
                    </div>
                </div>
                <div class="d-flex mt-2">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2 ">
                        <p class="text-muted mb-0"><strong>Showcase Your Achievements:</strong> Highlight your academic achievements, projects, and extracurricular activities to potential employers.</p>
                    </div>
                </div>
                <div class="d-flex mt-2">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2 ">
                        <p class="text-muted mb-0"><strong>Recommendations:</strong> Request recommendations from teachers, mentors, or colleagues to strengthen your profile.</p>
                    </div>
                </div>
                <h6 class="fs-16 my-3">Action Required:</h6>
                <div class="d-flex mt-2">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2 ">
                        <p class="text-muted mb-0"><strong>Create Your LinkedIn Profile:</strong> If you don't already have one, visit <a href="https://www.linkedin.com">LinkedIn</a> and create your profile. Make sure to add a professional profile picture and complete all relevant sections, including your education, skills, and experiences.</p>
                    </div>
                </div>
                <div class="d-flex mt-2">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2 ">
                        <p class="text-muted mb-0"><strong>Add Your LinkedIn Profile Link:</strong> Once your LinkedIn profile is complete, copy the URL of your profile page.</p>
                    </div>
                </div>
                <div class="d-flex mt-2">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2 ">
                        <p class="text-muted mb-0"><strong>Paste the Link in the URL Below:</strong> Click on the link provided below to access your student profile on our university website. There, you'll find a field to add your LinkedIn profile URL. Paste the link and save your changes.</p>
                    </div>
                </div>
                <div class="d-flex mt-5">
                    <div class="flex-grow-1 ms-2 ">
                        <p class="text-muted mb-0">Having an active LinkedIn profile will not only expand your professional network but also open doors to various opportunities.</p>
                        <p class="text-muted mb-0">Thank you for your attention and proactive participation in building your professional identity. We look forward to seeing your LinkedIn profiles grow and flourish.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


@endsection

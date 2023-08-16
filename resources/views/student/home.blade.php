@extends('student.layout.dashboard')
@php

$student = Auth::guard('student')->user();
$qrcode = 'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl='.env('APP_URL').'/studentDetails/'.$student->slug;
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
                            <strong>Jamb Reg. Number:</strong> {{ $student->applicant->jamb_reg_no }}
                        </p>
                        <p class="text-muted">CGPA: {{ $student->cgpa }}</p>
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
        </div>
        <!--end card-->
    </div>
    <!--end col-->

    <div class="col-xxl-8">
        <div class="card">
            <div class="card-header border-0 align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Transactions</h4>
            </div><!-- end card header -->

            <div class="card-body pb-2 border-top border-top-dashed">
                <div class="table-responsive">
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
                                <td>{{ $transaction->paymentType->type }} </td>
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
                                                <input type="hidden" name="payment_id" value="{{ $transaction->paymentType->id }}">
                                                <input type="hidden" name="reference" value="{{ $transaction->reference }}">
                                                <input type="hidden" name="amount" value="{{ $transaction->amount_payed }}">
                                                
                                                <div class="mb-3">
                                                    <label for="paymentGateway" class="form-label">Select Payment Gateway<span class="text-danger">*</span></label>
                                                    <select class="form-select" aria-label="paymentGateway" name="paymentGateway" required onchange="handlePaymentMethodChange(event)">
                                                        <option value= "" selected>Select Payment Gateway</option>
                                                        <option value="Paystack">Paystack</option>
                                                        <option value="Remita">Remita</option>
                                                        <option value="Zenith">Zenith Pay</option>
                                                        <option value="BankTransfer">Transfer</option>
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
                                                    <button type="submit" id='submit-button' class="btn btn-primary">Make payment</button>
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
                                <h5 class="fs-13 mb-1"><a href="#" class="text-body text-truncate d-block">Admission Letter</a></h5>
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
                                <h5 class="fs-13 mb-1"><a href="#" class="text-body text-truncate d-block">Jamb Result</a></h5>
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
                                <h5 class="fs-13 mb-1"><a href="#" class="text-body text-truncate d-block">Direct Entry Result</a></h5>
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
                                <h5 class="fs-13 mb-1"><a href="#" class="text-body text-truncate d-block">Olevel Result</a></h5>
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
                                <h5 class="fs-13 mb-1"><a href="#" class="text-body text-truncate d-block">Olevel Result(Second Sitting)</a></h5>
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

    </div>
    <!--end col-->
</div>
<!--end row-->

@endsection

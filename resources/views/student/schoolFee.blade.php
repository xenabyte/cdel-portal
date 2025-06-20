@extends('student.layout.dashboard')
<?php 
    $student = Auth::guard('student')->user();
    $academicSession = $student->programmeCategory->academicSessionSetting->academic_session;
    $admissionSession = $student->programmeCategory->academicSessionSetting->admission_session;
?>
@section('content')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Student Payment</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Student Payment</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

@if($studentPendingTransactions->count() > 0)
@php
$studentPendingTransaction = $studentPendingTransactions->first();
@endphp
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <div class="row justify-content-center">
                        <div class="col-lg-9">
                            <h4 class="mt-4 fw-semibold">School Fee</h4>
                            <p class="text-muted mt-3">Please note: Access to the dashboard requires prior payment of school fees balance for the {{ $studentPendingTransaction->session  }} session.</p>
                            <div class="mt-4">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                    Click here to pay
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-center mt-5 mb-2">
                        <div class="col-sm-7 col-8">
                            <img src="{{asset('assets/images/cc.png')}}" alt="" class="img-fluid" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header p-3">
                <h5 class="modal-title text-uppercase fw-bold" id="exampleModalLabel">Pending Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <div class="modal-body border-top border-top-dashed">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="pills-bill-info" role="tabpanel" aria-labelledby="pills-bill-info-tab">
                        
                        <form action="{{ url('/student/makePayment') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="payment_id" value="{{ $studentPendingTransaction->payment_id }}">
        
                            <div class="mb-3">
                                <label for="amount" class="form-label">Payment Amount<span class="text-danger">*</span></label>
                                <select class="form-select" aria-label="amount" name="amount" required>
                                    <option value= "" selected>Select Amount</option>
                                    <option value="{{$studentPendingTransaction->amount_payed}}">₦{{number_format($studentPendingTransaction->amount_payed/100, 2)}}</option>
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
        
                            <div>
                                <button type="submit" id="submit-button" id='submit-button' class="btn btn-primary">Make payment</button>
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

@else
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <div class="row justify-content-center">
                        <div class="col-lg-9">
                            <h4 class="mt-4 fw-semibold">School Fee</h4>
                            @if($academicSession != $admissionSession)
                            <p class="text-muted mt-3">Please note: Payment not available yet, stay tuned.</p>
                            @else
                            <p class="text-muted mt-3">Please note: Access to the dashboard requires prior payment of school fees for the {{ $academicSession  }} session.</p>
                            <div class="mt-4">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                    Click here to pay
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="row justify-content-center mt-5 mb-2">
                        <div class="col-sm-7 col-8">
                            <img src="{{asset('assets/images/cc.png')}}" alt="" class="img-fluid" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header p-3">
                <h5 class="modal-title text-uppercase fw-bold" id="exampleModalLabel">School Fee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <div class="modal-body border-top border-top-dashed">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="pills-bill-info" role="tabpanel" aria-labelledby="pills-bill-info-tab">
                        
                        <form action="{{ url('/student/makePayment') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="payment_id" value="{{ $payment->id }}">
        
                            <div class="mb-3">
                                <label for="amount" class="form-label">Payment Amount<span class="text-danger">*</span></label>
                                <select class="form-select" aria-label="amount" name="amount" required>
                                    <option value= "" selected>Select Amount</option>
                                    <option value="{{ $payment->structures->sum('amount') }}">₦{{ number_format($payment->structures->sum('amount')/100, 2) }} - 100%</option>
                                    <option value="{{ $payment->structures->sum('amount')*0.9 }}">₦{{ number_format($payment->structures->sum('amount')*0.9/100, 2) }} - 90%</option>
                                    <option value="{{ $payment->structures->sum('amount')*0.8 }}">₦{{ number_format($payment->structures->sum('amount')*0.8/100, 2) }} - 80%</option>
                                    <option value="{{ $payment->structures->sum('amount')*0.7 }}">₦{{ number_format($payment->structures->sum('amount')*0.7/100, 2) }} - 70%</option>
                                    <option value="{{ $payment->structures->sum('amount')*0.6 }}">₦{{ number_format($payment->structures->sum('amount')*0.6/100, 2) }} - 60%</option>
                                    <option value="{{ $payment->structures->sum('amount')*0.5 }}">₦{{ number_format($payment->structures->sum('amount')*0.5/100, 2) }} - 50%</option>
                                    <option value="{{ $payment->structures->sum('amount')*0.4 }}">₦{{ number_format($payment->structures->sum('amount')*0.4/100, 2) }} - 40%</option>
                                    @if($passTuition && !$fullTuitionPayment && !$passEightyTuititon)
                                    <option value="{{ $payment->structures->sum('amount')*0.4 }}">₦{{ number_format($payment->structures->sum('amount')*0.4/100, 2) }} - 40%</option>
                                    <option value="{{ $payment->structures->sum('amount')*0.3 }}">₦{{ number_format($payment->structures->sum('amount')*0.3/100, 2) }} - 30%</option>
                                    <option value="{{ $payment->structures->sum('amount')*0.2 }}">₦{{ number_format($payment->structures->sum('amount')*0.2/100, 2) }} - 20%</option>
                                    <option value="{{ $payment->structures->sum('amount')*0.1 }}">₦{{ number_format($payment->structures->sum('amount')*0.1/100, 2) }} - 10%</option>
                                    @endif
                                    @if($passTuition && !$fullTuitionPayment && $passEightyTuititon)
                                    <option value="{{ $payment->structures->sum('amount')*0.2 }}">₦{{ number_format($payment->structures->sum('amount')*0.2/100, 2) }} - 20%</option>
                                    <option value="{{ $payment->structures->sum('amount')*0.1 }}">₦{{ number_format($payment->structures->sum('amount')*0.1/100, 2) }} - 10%</option>
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
        
                            <div>
                                <button type="submit" id="submit-button" id='submit-button' class="btn btn-primary">Make payment</button>
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
@endif

@endsection

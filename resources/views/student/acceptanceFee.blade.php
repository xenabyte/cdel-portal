@extends('student.layout.dashboard')
<?php 
    $student = Auth::guard('student')->user();
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

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <div class="row justify-content-center">
                        <div class="col-lg-9">
                            <h4 class="mt-4 fw-semibold">Acceptance Fee</h4>
                            <p class="text-muted mt-3">Welcome! To secure your spot, kindly pay a non-refundable acceptance fee of ₦{{ number_format($payment->structures->sum('amount')/100, 2) }}. We're excited to have you join us!</p>
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
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header p-3">
                <h5 class="modal-title text-uppercase fw-bold" id="exampleModalLabel">Acceptance Fee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <div class="modal-body">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="pills-bill-info" role="tabpanel" aria-labelledby="pills-bill-info-tab">
                        <form class="needs-validation" method="POST" novalidate action="{{ url('student/makePayment') }}">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $student->id }}">
                            <input type="hidden" name="payment_id" value="{{ $payment->id }}">
                            <input type="hidden" name="amount" value="{{ $payment->structures->sum('amount') }}">

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

                            <div class="mt-4">
                                <button class="btn btn-success w-100" id='submit-button' disabled type="submit">Make Payment</button>
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

@endsection

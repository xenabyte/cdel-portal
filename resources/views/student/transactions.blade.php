@extends('student.layout.dashboard')
@php
    $student = Auth::guard('student')->user();
@endphp
@section('content')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">School Transactions</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">School Transactions</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">School Transactions </h4>
                <div class="flex-shrink-0">
                    @if(!$fullTuitionPayment)
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTransaction">Pay Tuition Fee</button>
                    @endif
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#payOthers">Pay Other Fees</button>
                </div>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
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
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>

<div id="addTransaction" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Make 'a' Payment</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body border-top border-top-dashed">
                <form action="{{ url('/student/makePayment') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name='programme_id' value="{{ $student->programme->id }}">
                    <input type="hidden" name="payment_id" value="{{ $payment->id }}">

                    <div class="mb-3">
                        <label for="amount" class="form-label">Payment Amount<span class="text-danger">*</span></label>
                        <select class="form-select" aria-label="amount" name="amount" required>
                            <option value= "" selected>Select Amount</option>
                            @if(!$passTuition)
                            <option value= "" selected>Select Amount</option>
                            <option value="{{ $payment->structures->sum('amount') }}">₦{{ number_format($payment->structures->sum('amount')/100, 2) }} - 100%</option>
                            <option value="{{ $payment->structures->sum('amount')*0.9 }}">₦{{ number_format($payment->structures->sum('amount')*0.9/100, 2) }} - 90%</option>
                            <option value="{{ $payment->structures->sum('amount')*0.8 }}">₦{{ number_format($payment->structures->sum('amount')*0.8/100, 2) }} - 80%</option>
                            <option value="{{ $payment->structures->sum('amount')*0.7 }}">₦{{ number_format($payment->structures->sum('amount')*0.7/100, 2) }} - 70%</option>
                            <option value="{{ $payment->structures->sum('amount')*0.6 }}">₦{{ number_format($payment->structures->sum('amount')*0.6/100, 2) }} - 60%</option>
                            <option value="{{ $payment->structures->sum('amount')*0.5 }}">₦{{ number_format($payment->structures->sum('amount')*0.5/100, 2) }} - 50%</option>
                            @endif
                            @if($passTuition && !$fullTuitionPayment && !$passEightyTuition)
                            <option value="{{ $payment->structures->sum('amount')*0.5 }}">₦{{ number_format($payment->structures->sum('amount')*0.5/100, 2) }} - 50%</option>
                            <option value="{{ $payment->structures->sum('amount')*0.4 }}">₦{{ number_format($payment->structures->sum('amount')*0.4/100, 2) }} - 40%</option>
                            <option value="{{ $payment->structures->sum('amount')*0.3 }}">₦{{ number_format($payment->structures->sum('amount')*0.3/100, 2) }} - 30%</option>
                            <option value="{{ $payment->structures->sum('amount')*0.2 }}">₦{{ number_format($payment->structures->sum('amount')*0.2/100, 2) }} - 20%</option>
                            <option value="{{ $payment->structures->sum('amount')*0.1 }}">₦{{ number_format($payment->structures->sum('amount')*0.1/100, 2) }} - 10%</option>
                            @endif
                            @if($passTuition && !$fullTuitionPayment && $passEightyTuition)
                            <option value="{{ $payment->structures->sum('amount')*0.2 }}">₦{{ number_format($payment->structures->sum('amount')*0.2/100, 2) }} - 20%</option>
                            <option value="{{ $payment->structures->sum('amount')*0.1 }}">₦{{ number_format($payment->structures->sum('amount')*0.1/100, 2) }} - 10%</option>
                            @endif
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="paymentGateway" class="form-label">Select Payment Gateway<span class="text-danger">*</span></label>
                        <select class="form-select" aria-label="paymentGateway" name="paymentGateway" required onchange="handlePaymentMainMethodChange(event)">
                            <option value= "" selected>Select Payment Gateway</option>
                            @if(env('FLUTTERWAVE_STATUS'))<option value="Rave">Flutterwave</option>@endif
                            @if(env('PAYSTACK_STATUS'))<option value="Paystack">Paystack</option>@endif
                            @if(env('BANK_TRANSFER_STATUS'))<option value="BankTransfer">Transfer</option>@endif
                            @if(env('WALLET_STATUS'))<option value="Wallet">Wallet</option>@endif
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
                        <button type="submit" id='submit-button-main' class="btn btn-primary">Make payment</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

{{-- <div id="payOthers" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Create Payment</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body border-top border-top-dashed">
                <form action="{{ url('/student/makePayment') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="studentId" name="student_id" value="{{ $student->id }}">
                    <input type="hidden" id="programmeId" name="programme_id" value="{{ $student->programme_id }}">
                    <input type="hidden" name="paymentGateway" value="Manual/BankTransfer">
                    <input type="hidden" id="userType" name="userType" value="student">


                    <div class="mb-3">
                        <label for="academic_session" class="form-label">Select Academic Session</label>
                        <select class="form-select" id="academicSession" aria-label="academic_session" name="academic_session" required>
                            <option selected value= "">Select Select Academic Session </option>
                            @foreach($sessions as $session)
                            <option value="{{ $session->year }}">{{ $session->year }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="level" class="form-label">Select Level </label>
                        <select class="form-select" id="level" aria-label="level" name="level">
                            <option selected value= "">Select Level </option>
                            @foreach($levels as $level)
                                @if($level->id <= $student->level_id)
                                <option value="{{ $level->id }}">{{ $level->level }} Level</option>
                                @endif
                            @endforeach
                        </select>
                    </div>


                    <div class="mb-3">
                        <label for="type" class="form-label">Select Payment Type </label>
                        <select class="form-select" aria-label="type" name="type" required onchange="handlePaymentTypeChange(event)">
                            <option selected value= "">Select Type </option>
                            <option value="General Application Fee">General Application Fee</option>
                            <option value="Inter Transfer Application Fee">Inter Transfer Application Fee</option>
                            <option value="Acceptance Fee">Acceptance Fee</option>
                            <option value="School Fee">School Fee</option>
                            <option value="DE School Fee">Direct Entry School Fee</option>
                            <option value="General Fee">General Fee</option>
                        </select>
                    </div>

                    <input type="hidden" id="paymentId" name="payment_id">

                    <div class="mb-3" id='payment-for' style="display: none">
                        <label for="payment_for" class="form-label">Payment For<span class="text-danger">*</span></label>
                        <select class="form-select" aria-label="payment_for" name="payment_id">
                            <option value= "" selected>Select Payment</option>
                        </select>
                    </div>


                    <div class="mb-3" id='payment-options-acceptance' style="display: none">
                        <label for="amount" class="form-label">Payment Amount<span class="text-danger">*</span></label>
                        <select class="form-select" aria-label="amount" name="amountAcceptance">
                            <option value= "" selected>Select Amount</option>
                        </select>
                    </div>

                    @if(env('PAYMENT_TYPE') == 'Percentage')
                    <div class="mb-3" id='payment-options-tuition' style="display: none">
                        <label for="amount" class="form-label">Payment Amount<span class="text-danger">*</span></label>
                        <select class="form-select" aria-label="amount" name="amountTuition">
                            <option value= "" selected>Select Amount</option>
                        </select>
                    </div>
                    @else
                    <div class="mb-3" id='payment-options-tuition' style="display: none">
                        <label for="amount" class="form-label">Payment Amount<span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="amountTuition">
                    </div>
                    @endif

                    <div class="mb-3" id='payment-options-general' style="display: none">
                        <label for="amount" class="form-label">Payment Amount<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="amountGeneral" id="amountGeneral">
                    </div>

                    <div class="mb-3">
                        <label for="paymentStatus" class="form-label">Select Payment Status<span class="text-danger">*</span></label>
                        <select class="form-select" aria-label="paymentStatus" name="paymentStatus" required>
                            <option value= "" selected>Select Payment Status</option>
                            <option value="1">Paid</option>
                            <option value="0">Not Paid</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="narration" class="form-label">Payment Narration</label>
                        <input type="text" class="form-control" name="narration" maxlength="200" id="narration">
                    </div>

                    <div class="text-end border-top border-top-dashed p-3">
                        <br>
                        <button type="submit" id="submit-button" class="btn btn-primary">Pay/Charge</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal --> --}}

@endsection
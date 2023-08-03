@extends('student.layout.dashboard')
<?php 
    $student = Auth::guard('student')->user();
?>
@section('content')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">{{ Auth::guard('student')->user()->applicant->othernames }}'s Dashboard</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Payments</li>
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
                <h4 class="card-title mb-0 flex-grow-1">Payments </h4>
                <div class="flex-shrink-0">
                    @if(!$fullTuitionPayment)
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTransaction">Pay Tuition Fee</button>
                    @endif
                    {{-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#payOthers">Pay Other Fees</button> --}}
                </div>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
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

                                    <div class="modal-body">
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

            <div class="modal-body">
                <form action="{{ url('/student/makePayment') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name='programme_id' value="{{ $student->programme->id }}">
                    <input type="hidden" name="payment_id" value="{{ $payment->id }}">

                    <div class="mb-3">
                        <label for="amount" class="form-label">Payment Amount<span class="text-danger">*</span></label>
                        <select class="form-select" aria-label="amount" name="amount" required>
                            <option value= "" selected>Select Amount</option>
                            @if(!$passTuition)
                            <option value="{{ $payment->structures->sum('amount') }}">₦{{ number_format($payment->structures->sum('amount')/100, 2) }} - 100%</option>
                            <option value="{{ $payment->structures->sum('amount')*0.5 }}">₦{{ number_format($payment->structures->sum('amount')*0.5/100, 2) }} - 50%</option>
                            @endif
                            @if($passTuition && !$fullTuitionPayment && !$passEightyTuititon)
                            <option value="{{ $payment->structures->sum('amount')*0.5 }}">₦{{ number_format($payment->structures->sum('amount')*0.5/100, 2) }} - 50%</option>
                            <option value="{{ $payment->structures->sum('amount')*0.3 }}">₦{{ number_format($payment->structures->sum('amount')*0.3/100, 2) }} - 30%</option>
                            @endif
                            @if($passTuition && !$fullTuitionPayment && $passEightyTuititon)
                            <option value="{{ $payment->structures->sum('amount')*0.2 }}">₦{{ number_format($payment->structures->sum('amount')*0.2/100, 2) }} - 20%</option>
                            @endif
                        </select>
                    </div>

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

@endsection
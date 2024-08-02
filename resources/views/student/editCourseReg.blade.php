@extends('student.layout.dashboard')
<?php 
    $student = Auth::guard('student')->user();
?>
@section('content')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Add or Remove Course Payment</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Add or Remove Course Payment</li>
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
                <h4 class="card-title mb-0 flex-grow-1">Records </h4>
                <div class="flex-shrink-0">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTransaction">Apply to add/remove courses</button>
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
                            <th scope="col">Is Used</th>
                            <th scope="col">Payment Date</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($addOrRemoveTxs as $addOrRemoveTx)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $addOrRemoveTx->reference }}</td>
                            <td>₦{{ number_format($addOrRemoveTx->amount_payed/100, 2) }} </td>
                            <td>{{ $addOrRemoveTx->paymentType->type }} </td>
                            <td>{{ $addOrRemoveTx->session }}</td>
                            <td>{{ $addOrRemoveTx->payment_method }}</td>
                            <td><span class="badge badge-soft-{{ $addOrRemoveTx->is_used == 1 ? 'success' : 'warning' }}">{{ $addOrRemoveTx->is_used == 1 ? 'Used' : 'Not Used' }}</span></td>
                            <td><span class="badge badge-soft-{{ $addOrRemoveTx->status == 1 ? 'success' : 'warning' }}">{{ $addOrRemoveTx->status == 1 ? 'Paid' : 'Pending' }}</span></td>
                            <td>{{ $addOrRemoveTx->status == 1 ? $addOrRemoveTx->updated_at : null }} </td>
                            <td>
                                @if($addOrRemoveTx->status == 0)
                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#payNow{{$addOrRemoveTx->id}}" style="margin: 5px" class="btn btn-warning">Pay Now</a>
                                @endif
                            </td>
                        </tr>

                        <div id="payNow{{$addOrRemoveTx->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
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
                                            <input type="hidden" name="transaction_id" value="{{ $addOrRemoveTx->id }}">
                                            <input type="hidden" name="payment_id" value="{{ $addOrRemoveTx->paymentType->id }}">
                                            <input type="hidden" name="reference" value="{{ $addOrRemoveTx->reference }}">
                                            <input type="hidden" name="amount" value="{{ $addOrRemoveTx->amount_payed }}">
                                            
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

            <div class="modal-body">
                <form action="{{ url('/student/makePayment') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="payment_id" value="{{ $payment->id }}">
                    <input type="hidden" name="amount" value="{{ $payment->structures->sum('amount') }}">

                    <div class="alert alert-info">
                        <p class="mb-0">Please note that there is a fee for applying to add or remove a course. Kindly pay the amount below </p>
                        <hr>
                        <h5 class="alert-heading text-center">₦{{ number_format($payment->structures->sum('amount')/100, 2) }}</h5>
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
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@endsection
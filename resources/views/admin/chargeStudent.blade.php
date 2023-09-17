@extends('admin.layout.dashboard')

@section('content')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Charge 'a' student</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Charge 'a' student</li>
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
                <h4 class="card-title mb-0 flex-grow-1">Get Student Information</h4>
                <div class="flex-shrink-0">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#getStudent">Get Student</button>
                </div>
            </div><!-- end card header -->
        </div><!-- end card -->
    </div>
</div>
<!-- end row -->
@if(!empty($student))
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
                        <h5 class="mb-1">{{$student->applicant->lastname.' '.$student->applicant->othernames}}</h5>
                        <p class="text-muted">{{ $student->programme->name }} <br>
                            <strong>Matric Number:</strong> {{ $student->matric_number }}<br>
                            <strong>Jamb Reg. Number:</strong> {{ $student->applicant->jamb_reg_no }}
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
                <div class="text-end mb-5">
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addTransaction" class="btn btn-primary">Charge Student</a>
                </div>
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
                                                    <select class="form-select" aria-label="paymentGateway" name="paymentGateway" required onchange="handlePaymentTypeChange(event)">
                                                        <option value= "" selected>Select Payment Gateway</option>
                                                        <option value="Rave">Fluterwave</option>
                                                        {{-- <option value="Paystack">Paystack</option>
                                                        <option value="Remita">Remita</option>
                                                        <option value="Zenith">Zenith Pay</option>
                                                        <option value="BankTransfer">Transfer</option> --}}
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

    </div>
    <!--end col-->
</div>
<!--end row-->

<div id="addTransaction" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Create Payment</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body border-top border-top-dashed">
                <form action="{{ url('/admin/chargeStudent') }}" method="post" enctype="multipart/form-data">
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
                            <option value="Acceptance Fee">Acceptance Fee</option>
                            <option value="School Fee">School Fee</option>
                            <option value="General Fee">General Fee</option>
                        </select>
                    </div>


                    <div class="mb-3" id='payment-options-acceptance' style="display: none">
                        <label for="amount" class="form-label">Payment Amount<span class="text-danger">*</span></label>
                        <select class="form-select" aria-label="amount" name="amountAcceptance">
                            <option value= "" selected>Select Amount</option>
                        </select>
                    </div>


                   <div class="mb-3" id='payment-options-tuition' style="display: none">
                        <label for="amount" class="form-label">Payment Amount<span class="text-danger">*</span></label>
                        <select class="form-select" aria-label="amount" name="amountTuition">
                            <option value= "" selected>Select Amount</option>
                        </select>
                    </div>

                    <input type="hidden" id="paymentId" name="payment_id">

                    <div class="mb-3" id='payment-for' style="display: none">
                        <label for="amount" class="form-label">Payment For<span class="text-danger">*</span></label>
                        <select class="form-select" aria-label="amount" name="payment_id">
                            <option value= "" selected>Select Payment</option>
                        </select>
                    </div>

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


                    <div class="text-end border-top border-top-dashed p-3">
                        <br>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endif

@if(!empty($applicant))
<div class="row">
    <div class="col-xxl-4">
        <div class="card">
            <div class="card-body p-4">
                <div>
                    <div class="flex-shrink-0 avatar-md mx-auto">
                        <div class="avatar-title bg-light rounded">
                            <img src="{{empty($applicant->image)?asset('assets/images/users/user-dummy-img.jpg'):asset($applicant->image)}}" alt="" height="50" />
                        </div>
                    </div>
                    <div class="mt-4 text-center">
                        <h5 class="mb-1">{{$applicant->lastname.' '.$applicant->othernames}}</h5>
                        <p class="text-muted">{{ $applicant->programme->name }} <br>
                            <strong>Application Number:</strong> {{ $applicant->application_number }}<br>
                            <strong>Jamb Reg. Number:</strong> {{ $applicant->jamb_reg_no }}
                        </p>
                    </div>
                    <div class="table-responsive border-top border-top-dashed">
                        <table class="table mb-0 table-borderless">
                            <tbody>
                                <tr>
                                    <th><span class="fw-medium">Email:</span></th>
                                    <td>{{ $applicant->email }}</td>
                                </tr>
                                <tr>
                                    <th><span class="fw-medium">Contact No.:</span></th>
                                    <td>{{ $applicant->phone_number }}</td>
                                </tr>
                                <tr>
                                    <th><span class="fw-medium">Address:</span></th>
                                    <td>{!! $applicant->address !!}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
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
                                    <td>{{ $applicant->guardian->name }}</td>
                                </tr>
                                <tr>
                                    <th><span class="fw-medium">Email</span></th>
                                    <td>{{ $applicant->guardian->email }}</td>
                                </tr>
                                <tr>
                                    <th><span class="fw-medium">Contact No.</span></th>
                                    <td>{{ $applicant->guardian->phone_number }}</td>
                                </tr>
                                <tr>
                                    <th><span class="fw-medium">Address</span></th>
                                    <td>{!! $applicant->guardian->address !!}</td>
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
                <div class="text-end mb-5">
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addTransaction" class="btn btn-primary">Charge Student</a>
                </div>
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

<div id="addTransaction" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Create Payment</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body border-top border-top-dashed">
                <form action="{{ url('/admin/chargeStudent') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="studentId" name="user_id" value="{{ $applicant->id }}">
                    <input type="hidden" id="programmeId" name="programme_id" value="{{ $applicant->programme_id }}">
                    <input type="hidden" id="academicSession" name="academic_session" value="{{ $applicant->academic_session }}">
                    <input type="hidden" id="level" name="level" value="0">
                    <input type="hidden" name="paymentGateway" value="Manual/BankTransfer">
                    <input type="hidden" id="userType" name="userType" value="applicant">

                    <div class="mb-3">
                        <label for="type" class="form-label">Select Payment Type </label>
                        <select class="form-select" aria-label="type" name="type" required onchange="handlePaymentTypeChange(event)">
                            <option selected value= "">Select Type </option>
                            <option value="General Application Fee">General Application Fee</option>
                            <option value="Inter Transfer Application Fee">Inter Transfer Application Fee</option>
                            <option value="Acceptance Fee">Acceptance Fee</option>
                            <option value="School Fee">School Fee</option>
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
                        <input type="text" class="form-control" name="amountTuition">
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


                    <div class="text-end border-top border-top-dashed p-3">
                        <br>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endif


<div id="getStudent" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Get Student</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body border-top border-top-dashed">
                <form action="{{ url('/admin/getStudent') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="reg" class="form-label">Application/Matric Number</label>
                        <input type="text" class="form-control" name="reg_number" id="reg">
                    </div>
                    <div class="mb-3">
                        <label for="type" class="form-label">Select  Type</label>
                        <select class="form-select" aria-label="type" name="type" required>
                            <option selected value= "">Select type </option>
                            <option value="Student">Student</option>
                            <option value="Applicant">Applicant</option>
                        </select>
                    </div>
                    <div class="text-end border-top border-top-dashed p-3">
                        <br>
                        <button type="submit" class="btn btn-primary">Get student</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    function handlePaymentTypeChange(event) {
        const selectedPaymentType = event.target.value;
        const academicSession = document.getElementById('academicSession').value;
        const programmeId = document.getElementById('programmeId').value;
        const paymentId = document.getElementById('paymentId');
        const studentId = document.getElementById('studentId').value;
        const level = document.getElementById('level').value;
        const userType = document.getElementById('userType').value;

        if(selectedPaymentType != ''){
            axios.post("{{ url('/admin/getPayment') }}", {
                type: selectedPaymentType,
                academic_session: academicSession,
                programme_id: programmeId,
                student_id: studentId,
                level: level,
                userType: userType
            })
            .then(response => {
                const data = response.data;

                if (selectedPaymentType === 'School Fee') {
                    const amount = data.structures.reduce((total, structure) => total + parseInt(structure.amount), 0); 
                    paymentId.value = data.id;
                    console.log(paymentId.value);

                    document.getElementById('payment-options-acceptance').style.display = 'none';
                    document.getElementById('payment-options-tuition').style.display = 'block';
                    document.getElementById('payment-options-general').style.display = 'none';
                    document.getElementById('payment-for').style.display = 'none';


                    const selectElement = document.querySelector('[name="amountTuition"]');
                    if (!data.passTuition) {
                        selectElement.innerHTML += `<option value="${amount}">₦${(amount / 100).toFixed(2)} - 100%</option>`;
                        selectElement.innerHTML += `<option value="${amount * 0.9}">₦${((amount * 0.9) / 100).toFixed(2)} - 90%</option>`;
                        selectElement.innerHTML += `<option value="${amount * 0.8}">₦${((amount * 0.8) / 100).toFixed(2)} - 80%</option>`;
                        selectElement.innerHTML += `<option value="${amount * 0.7}">₦${((amount * 0.7) / 100).toFixed(2)} - 70%</option>`;
                        selectElement.innerHTML += `<option value="${amount * 0.6}">₦${((amount * 0.6) / 100).toFixed(2)} - 60%</option>`;
                        selectElement.innerHTML += `<option value="${amount * 0.5}">₦${((amount * 0.5) / 100).toFixed(2)} - 50%</option>`;
                    }
                    if (data.passTuition && !data.fullTuitionPayment && !data.passEightyTuition) {
                        selectElement.innerHTML += `<option value="${amount * 0.5}">₦${((amount * 0.5) / 100).toFixed(2)} - 50%</option>`;
                        selectElement.innerHTML += `<option value="${amount * 0.4}">₦${((amount * 0.4) / 100).toFixed(2)} - 40%</option>`;
                        selectElement.innerHTML += `<option value="${amount * 0.3}">₦${((amount * 0.3) / 100).toFixed(2)} - 30%</option>`;
                        selectElement.innerHTML += `<option value="${amount * 0.2}">₦${((amount * 0.2) / 100).toFixed(2)} - 20%</option>`;
                        selectElement.innerHTML += `<option value="${amount * 0.1}">₦${((amount * 0.1) / 100).toFixed(2)} - 10%</option>`;
                    }
                    if (data.passTuition && !data.fullTuitionPayment && data.passEightyTuition) {
                        selectElement.innerHTML += `<option value="${amount * 0.2}">₦${((amount * 0.2) / 100).toFixed(2)} - 20%</option>`;
                        selectElement.innerHTML += `<option value="${amount * 0.1}">₦${((amount * 0.1) / 100).toFixed(2)} - 10%</option>`;
                    }

                }else if (selectedPaymentType === 'General Fee') {
                    document.getElementById('payment-options-tuition').style.display = 'none';
                    document.getElementById('payment-options-acceptance').style.display = 'none';
                    document.getElementById('payment-options-general').style.display = 'block';
                    document.getElementById('payment-for').style.display = 'block';

                    const selectElement = document.querySelector('select[name="payment_id"]');

                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id; 
                        option.textContent = item.title;
                        selectElement.appendChild(option);
                    });

                } else {
                    paymentId.value = data.id;
                    console.log(paymentId.value);

                    document.getElementById('payment-options-tuition').style.display = 'none';
                    document.getElementById('payment-options-acceptance').style.display = 'block';
                    document.getElementById('payment-options-general').style.display = 'none';
                    document.getElementById('payment-for').style.display = 'none';

                    
                    const selectElement = document.querySelector('[name="amountAcceptance"]');
                    selectElement.innerHTML = '<option value="">Select Amount</option>';

                    const option = document.createElement('option');
                    option.value = data.structures.reduce((total, structure) => total + structure.amount, 0);
                    option.text = '₦' + (option.value / 100).toFixed(2) + ' - ' + data.title;
                    selectElement.appendChild(option);
                }
            })
            
        }

    }
</script>
@endsection
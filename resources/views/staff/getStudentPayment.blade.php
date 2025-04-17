@extends('staff.layout.dashboard')

@section('content')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Student Transactions</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Student Transactions</li>
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
@php
$payment = new \App\Models\Payment;
$paymentAmount =  $student->amountBilled; //$payment->getTotalStructureAmount($student->paymentId);
$totalPaid = $transactions->where('status', 1)->sum('amount_payed');
$balance = $paymentAmount>0? $paymentAmount - $totalPaid : 0;
@endphp

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
                        @php
                            $studentAdvisoryData = (object) $student->getAcademicAdvisory();
                            $failedCourses = $student->registeredCourses()->where('grade', 'F')->where('re_reg', null)->get();
                        @endphp
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
                            <strong>Promotion Eligibility:</strong> {{ is_null($student->cgpa) || $student->cgpa == 0 ? 'You are a fresh student; promotion eligibility will be determined after your first semester.' : ($studentAdvisoryData->promotion_eligible ? 'You are eligible to promote.' : 'You are not eligible to promote.') }} <br>
                            <strong>Promotion Message:</strong> {{ $studentAdvisoryData->promotion_message }}<br>
                            <strong>GPA Trend:</strong> {{ $studentAdvisoryData->trajectory_analysis['cgpa_trend'] }}<br>
                            <strong>CGPA Trajectory Analysis:</strong> {{ $studentAdvisoryData->trajectory_analysis['academic_risk'] }}<br>
                            <strong>Course Strength:</strong> @foreach($studentAdvisoryData->trajectory_analysis['strengths'] as $strength) {{ $strength.', ' }} @endforeach<br>
                            <strong>Course Weakness:</strong> @foreach($studentAdvisoryData->trajectory_analysis['weaknesses'] as $weakness) {{ $weakness.', ' }} @endforeach<br>
                            <strong>Tips:</strong> @foreach($studentAdvisoryData->trajectory_analysis['tips'] as $tips) {{ $tips }} @endforeach<br>
                        </p>
                    </div>
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
                                    Amount Billed</p>
                                <h4 class=" mb-0">₦<span class="counter-value" data-target="{{ $paymentAmount/100 }}">0</span></h4>
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
                                    Total Paid</p>
                                <h4 class=" mb-0">₦<span class="counter-value" data-target="{{$totalPaid/100}}">0</span></h4>
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
                                <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">Balance</p>
                                <h4 class=" mb-0">₦<span class="counter-value" data-target="{{$balance/100 }}">0</span></h4>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end col -->
        </div><!-- end row -->

        <div class="card">
            <div class="card-header border-0 align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">{{ $student->paymentType}} Transactions for {{ $student->session }} Academic Session </h4>
                <div class="text-end mb-5">
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addTransaction" class="btn btn-primary">Create Payment</a>
                </div>
            </div><!-- end card header -->

            <div class="card-body pb-2 border-top border-top-dashed">
                <div class="table-responsive">
                    <table id="buttons-datatables" class="display table table-stripped" style="width:100%">
                        <thead>
                            <tr>
                                <th scope="col">Id</th>
                                <th scope="col">Payment For</th>
                                <th scope="col">Reference</th>
                                <th scope="col">Amount(₦)</th>
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
                                <td>{{ !empty($transaction->paymentType)? $transaction->paymentType->title : 'Wallet Deposit' }} </td>
                                <td>{{ $transaction->reference }}</td>
                                <td>₦{{ number_format($transaction->amount_payed/100, 2) }} </td>
                                <td>{{ $transaction->payment_method }}</td>
                                <td><span class="badge badge-soft-{{ $transaction->status == 1 ? 'success' : 'warning' }}">{{ $transaction->status == 1 ? 'Paid' : 'Pending' }}</span></td>
                                <td>{{ $transaction->status == 1 ? $transaction->updated_at : null }} </td>
                                <td>
                                    @if($transaction->status == 1)
                                    <form action="{{ url('/staff/generateInvoice') }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <input name="payment_id" type="hidden" value="{{!empty($transaction->paymentType)?$transaction->paymentType->id:0}}">
                                        <input name="student_id" type="hidden" value="{{$transaction->student_id}}">
                                        <input name="session" type="hidden" value="{{ $transaction->session }}">
                                        <input name="type" type="hidden" value="single">
                                        <button type="submit" id="submit-button" class="btn btn-primary"><i class="mdi mdi-printer"></i></button>
                                    </form>
                                    @endif
                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#editTransaction{{ $transaction->id }}" class="btn btn-info my-1"><i class="mdi mdi-application-edit"></i></a>
                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#deleteTransaction{{ $transaction->id }}" class="btn btn-danger my-1"><i class="mdi mdi-trash-can"></i></a>
                                </td>

                                <div id="deleteTransaction{{$transaction->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-body text-center p-5">
                                                <div class="text-end">
                                                    <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="mt-2">
                                                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="hover" style="width:150px;height:150px">
                                                    </lord-icon>
                                                    <h4 class="mb-3 mt-4">Are you sure you want to delete transaction of <br/> ₦{{ number_format($transaction->amount_payed/100, 2) }}?</h4>
                                                    <form action="{{ url('/staff/deleteTransaction') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" value="{{$transaction->id}}" name="transaction_id">
                                                        <input type="hidden" value="{{$transaction->student_id}}" name="student_id">
                                                        <hr>
                                                        <button type="submit" id="submit-button" class="btn btn-danger w-100">Yes, Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light p-3 justify-content-center">

                                            </div>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->

                                <div id="editTransaction{{ $transaction->id }}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content border-0 overflow-hidden">
                                            <div class="modal-header p-3">
                                                <h4 class="card-title mb-0">Edit Transaction</h4>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                
                                            <div class="modal-body border-top border-top-dashed">
                                                <form action="{{ url('/staff/editTransaction') }}" method="post" enctype="multipart/form-data">
                                                    @csrf
                                                    <input type="hidden" value="{{$transaction->id}}" name="transaction_id">
                                                    <input type="hidden" value="{{$transaction->student_id}}" name="student_id">
                                                    
                                
                                                    <div class="mb-3">
                                                        <label for="academic_session" class="form-label">Select Academic Session</label>
                                                        <select class="form-select" id="academicSession" aria-label="academic_session" name="academic_session" required>
                                                            @foreach($sessions as $session)
                                                            <option @if($session->year == $student->session)selected @endif value="{{ $session->year }}">{{ $session->year }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                
                                                    <div class="mb-3">
                                                        <label for="level" class="form-label">Select Level </label>
                                                        <select class="form-select" id="level" aria-label="level" name="level">
                                                            @foreach($levels as $level)
                                                                @if($level->id <= $student->level_id)
                                                                <option @if($level->id == $transaction->level_id)selected @endif value="{{ $level->id }}">{{ $level->level }} Level</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                
                                                    <div class="mb-3">
                                                        <label for="amount" class="form-label">Payment Amount<span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="amount" id="amount" value="{{ $transaction->amount_payed/100 }}">
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
                                                        <button type="submit" id="submit-button" class="btn btn-primary">Update</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->
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
                <form action="{{ url('/staff/chargeStudent') }}" method="post" enctype="multipart/form-data">
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
                            @foreach($paymentTypes as $paymentType)
                                <option value="{{ $paymentType->type }}">{{ $paymentType->type }}</option>
                            @endforeach
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
                <form action="{{ url('/staff/getStudent') }}" method="post" enctype="multipart/form-data">
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
                        <button type="submit" id="submit-button" class="btn btn-primary">Get student</button>
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

        const paymentSelect = document.querySelector('select[name="payment_id"]');
        const paymentHidden = document.querySelector('input[name="payment_id"]');


        if(selectedPaymentType != ''){
            axios.post("{{ url('/staff/getPayment') }}", {
                type: selectedPaymentType,
                academic_session: academicSession,
                programme_id: programmeId,
                student_id: studentId,
                level: level,
                userType: userType
            })
            .then(response => {
                const data = response.data.data;
                if (response.data.status != 'success') {
                    // Show a SweetAlert for record not found
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops!!',
                        text: response.data.status,
                    });
                }else{
                    if (selectedPaymentType === 'School Fee' || selectedPaymentType === 'DE School Fee') {
                        const amount = data.structures.reduce((total, structure) => total + parseInt(structure.amount), 0); 
                        paymentId.value = data.id;

                        document.getElementById('payment-options-acceptance').style.display = 'none';
                        document.getElementById('payment-options-tuition').style.display = 'block';
                        document.getElementById('payment-options-general').style.display = 'none';
                        document.getElementById('payment-for').style.display = 'none';
                        paymentSelect.disabled = true;
                        paymentHidden.disabled = false;


                        const selectElement = document.querySelector('[name="amountTuition"]');
                        if (!data.passTuition) {
                            selectElement.innerHTML += `<option value="${amount}">₦${(amount / 100).toFixed(2)} - 100%</option>`;
                            selectElement.innerHTML += `<option value="${amount * 0.9}">₦${((amount * 0.9) / 100).toFixed(2)} - 90%</option>`;
                            selectElement.innerHTML += `<option value="${amount * 0.8}">₦${((amount * 0.8) / 100).toFixed(2)} - 80%</option>`;
                            selectElement.innerHTML += `<option value="${amount * 0.7}">₦${((amount * 0.7) / 100).toFixed(2)} - 70%</option>`;
                            selectElement.innerHTML += `<option value="${amount * 0.6}">₦${((amount * 0.6) / 100).toFixed(2)} - 60%</option>`;
                            selectElement.innerHTML += `<option value="${amount * 0.5}">₦${((amount * 0.5) / 100).toFixed(2)} - 50%</option>`;
                            selectElement.innerHTML += `<option value="${amount * 0.4}">₦${((amount * 0.4) / 100).toFixed(2)} - 40%</option>`;
                        }
                        if (data.passTuition && !data.fullTuitionPayment && !data.passEightyTuition) {
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

                        paymentSelect.disabled = false;
                        paymentHidden.disabled = true; 

                        const selectElement = document.querySelector('select[name="payment_id"]');

                        data.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.id; 
                            option.textContent = item.title;
                            selectElement.appendChild(option);
                        });

                    } else {
                        paymentId.value = data.id;

                        document.getElementById('payment-options-tuition').style.display = 'none';
                        document.getElementById('payment-options-acceptance').style.display = 'block';
                        document.getElementById('payment-options-general').style.display = 'none';
                        document.getElementById('payment-for').style.display = 'none';

                        paymentSelect.disabled = true;
                        paymentHidden.disabled = false;

                        
                        const selectElement = document.querySelector('[name="amountAcceptance"]');
                        selectElement.innerHTML = '<option value="">Select Amount</option>';

                        const option = document.createElement('option');
                        option.value = data.structures.reduce((total, structure) => total + structure.amount, 0);
                        option.text = '₦' + (option.value / 100).toFixed(2) + ' - ' + data.title;
                        selectElement.appendChild(option);
                    }
                }
            })
            
        }
    }
</script>
@endsection
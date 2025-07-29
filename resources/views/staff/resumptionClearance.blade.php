@extends('staff.layout.dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Resumption Clearance</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Resumption Clearance</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->
@if(empty($student))
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col-lg-9">
                        <h4 class="mt-4 fw-semibold">Generate Resumption Clearance</h4>
                        <p class="text-muted mt-3"></p>
                        <div class="mt-4">
                            <form action="{{ url('/staff/getStudentResumptionClearance') }}" method="POST">
                                @csrf
                                <div class="row g-3">

                                     <div class="form-floating mb-3">
                                        <select class="form-select select2 selectWithSearch" id="selectWithSearch" name="student_id" aria-label="username">
                                            <option value="" selected>-- Student Name --</option>
                                            @foreach($students as $student)<option value="{{$student->id}}">{{$student->applicant->lastname.' '.$student->applicant->othernames}}</option>@endforeach
                                        </select>
                                        {{-- <label for="username">Bandwidth Username <span class="text-danger">*</span></label> --}}
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-floating">
                                            <select class="form-select" id="semester" name="semester" aria-label="semester">
                                                <option value="" selected>--Select--</option>
                                                <option value="1">Harmattan Semester Resumption</option>
                                                 <option value="3">Mid Academic(New Year) Resumption</option>
                                                <option value="2">Rain Semester Resumption</option>
                                            </select>
                                            <label for="semester">Semester</label>
                                        </div>
                                    </div>
    
                                    <button type="submit" id="submit-button" class="btn btn-fill btn-primary btn-lg btn-block mb-5">Generate Clearance</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>

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
                            <strong>Address:</strong> {!! preg_replace('/<\/?p[^>]*>/', '', $student->applicant->address) !!}<br>
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
                    </div>
                </div>
            </div>

            @if($allocatedRoom)
                <div class="card-body border-top border-top-dashed p-4">
                    <div>
                        <h6 class="text-muted text-uppercase fw-semibold mb-4">Hostel Allocation</h6>
                        <div class="table-responsive">
                            <table class="table mb-0 table-borderless">
                                <tbody>
                                    <tr>
                                        <th><span class="fw-medium">Campus</span></th>
                                        <td>{{ $allocatedRoom->room->type->campus }} Campus</td>
                                    </tr>
                                    <tr>
                                        <th><span class="fw-medium">Hostel</span></th>
                                        <td>{{ $allocatedRoom->room->hostel->name }}</td>
                                    </tr>
                                    <tr>
                                        <th><span class="fw-medium">Room Number</span></th>
                                        <td>{{ $allocatedRoom->room->number }}</td>
                                    </tr>
                                    <tr>
                                        <th><span class="fw-medium">Room Type</span></th>
                                        <td>{{ $allocatedRoom->room->type->name }} - {{ $allocatedRoom->room->type->capacity }} Bed Space(s)</td>
                                    </tr>
                                    <tr>
                                        <th><span class="fw-medium">Bed Space</span></th>
                                        <td>{{ $allocatedRoom->bedSpace->space }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="card-body border-top border-top-dashed p-4">
                    <div>
                        <h6 class="text-muted text-uppercase fw-semibold mb-4">Hostel Allocation</h6>
                        <p class="text-muted mb-0">
                            You have not been allocated a room yet. Please wait for further updates.
                        </p>
                    </div>
                </div>
            @endif

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
        <div class="card">
            <div class="card-header border-0 align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Tuition Fee Payments</h4>
                <div class="text-end mb-5">
                    <div class="text-end mb-5">
                        <form action="{{ url('/staff/generateResumptionClearance') }}" method="POST" target="_blank">
                            @csrf
                            <input type="hidden" name="student_id" value="{{ $student->id }}">
                            <input type="hidden" name="semester" value="{{ $semester }}">
                            <button type="submit" class="btn btn-primary">
                                Generate Payment PDF
                            </button>
                        </form>
                    </div>
                </div>
            </div><!-- end card header -->

            <div class="card-body pb-2 border-top border-top-dashed">
                @php
                    $amountBilled = $student->paymentCheck->schoolPayment->structures->sum('amount') ?? 0;
                    $amountPaid = $student->paymentCheck->schoolPaymentTransaction->sum('amount_payed') ?? 0;
                    $balance = $amountBilled - $amountPaid;
                    $percentagePaid = $amountBilled > 0 ? round(($amountPaid / $amountBilled) * 100, 2) : 0;

                    // Determine required percentage based on semester
                    $requiredPercentage = match((int) $semester) {
                        1 => 40,
                        2 => 100,
                        3 => 80,
                        default => 0
                    };

                    $isCleared = $percentagePaid >= $requiredPercentage;
                @endphp

                <div class="row">
                    {{-- Amount Billed --}}
                    <div class="col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-light text-primary rounded-circle shadow fs-3">
                                            <i class="ri-money-dollar-circle-fill align-middle"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">Amount Billed</p>
                                        <h4 class="mb-0">₦<span class="counter-value" data-target="{{ $amountBilled / 100 }}">0</span></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Total Paid --}}
                    <div class="col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-light text-success rounded-circle shadow fs-3">
                                            <i class="ri-wallet-3-fill align-middle"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">Total Paid</p>
                                        <h4 class="mb-0">₦<span class="counter-value" data-target="{{ $amountPaid / 100 }}">0</span></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Balance --}}
                    <div class="col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-light text-warning rounded-circle shadow fs-3">
                                            <i class="ri-exchange-dollar-fill align-middle"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">Balance</p>
                                        <h4 class="mb-0">₦<span class="counter-value" data-target="{{ $balance / 100 }}">0</span></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Percentage Paid --}}
                    <div class="col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-light text-info rounded-circle shadow fs-3">
                                            <i class="ri-percent-fill align-middle"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">Percentage Paid</p>
                                        <h4 class="mb-0"><span class="counter-value" data-target="{{ $percentagePaid }}">0</span>%</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Clearance Status --}}
                    <div class="col-lg-12 mt-3">
                        <div class="card border-{{ $isCleared ? 'success' : 'danger' }}">
                            <div class="card-body text-center">
                                @if ($isCleared)
                                    <i class="mdi mdi-check-decagram text-success fs-1"></i>
                                    <h5 class="mt-2 text-success">Cleared for Entry</h5>
                                    <p class="text-muted">{{ $percentagePaid }}% paid (required: {{ $requiredPercentage }}%)</p>
                                @else
                                    <i class="mdi mdi-alert-circle text-danger fs-1"></i>
                                    <h5 class="mt-2 text-danger">Not Cleared for Entry</h5>
                                    <p class="text-muted">
                                        {{ $percentagePaid }}% paid (required: {{ $requiredPercentage }}%)<br>
                                        Outstanding balance: ₦{{ number_format($balance / 100, 2) }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                     <div class="col-lg-12 mt-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="buttons-datatables" class="display table table-stripped" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th scope="col">Id</th>
                                                <th scope="col">Payment Method</th>
                                                <th class="bg bg-success text-light" scope="col">Amount Paid(₦)</th>
                                                <th scope="col">Payment Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $id = 1;
                                            @endphp
                                            @foreach($student->paymentCheck->schoolPaymentTransaction as $transaction)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $transaction->payment_method }}</td>
                                                    <td class="bg bg-soft-success">₦{{ number_format($transaction->amount_payed/100, 2) }}</td>
                                                    <td>{{date('l, jS F, Y', strtotime($transaction->updated_at))}}</td>
                                                </tr>
                                                
                                            @endforeach
                                        </tbody> 
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

        
            </div><!-- end card body -->
        </div><!-- end card -->

    </div>
    <!--end col-->
</div>
<!--end row-


@endif

@endSection
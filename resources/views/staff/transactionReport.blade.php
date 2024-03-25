@extends('staff.layout.dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Transactions</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Transactions</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->
@if(empty($students))
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <div class="row justify-content-center">
                        <div class="col-lg-9">
                            <h4 class="mt-4 fw-semibold">Fetch Transaction Report(s)</h4>
                            <p class="text-muted mt-3"></p>
                            <div class="mt-4">
                                <form action="{{ url('/staff/generateReport') }}" method="POST">
                                    @csrf
                                    <div class="row g-3">

                                        <div class="col-lg-12">
                                            <div class="form-floating">
                                                <select class="form-select" id="faculty" name="faculty_id" aria-label="faculty" onchange="handleFacultyChange(event)">
                                                    <option value="" selected>--Select--</option>
                                                    @foreach($faculties as $faculty)
                                                        <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                                                    @endforeach
                                                </select>
                                                <label for="faculty">Faculty</label>
                                            </div>
                                        </div>

                                        <div class="col-lg-12">
                                            <div class="form-floating">
                                                <select class="form-select" id="department" name="department_id" aria-label="department" onchange="handleDepartmentChange(event)">
                                                    <option value="" selected>--Select--</option>
                                                </select>
                                                <label for="department">Department</label>
                                            </div>
                                        </div>

                                        <div class="col-lg-12">
                                            <div class="form-floating">
                                                <select class="form-select" id="programme" name="programme_id" aria-label="programme">
                                                    <option value="" selected>--Select--</option>
                                                </select>
                                                <label for="department">Programme</label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-lg-12">
                                            <div class="form-floating">
                                                <select class="form-select" id="level" name="level_id" aria-label="level">
                                                    <option value="" selected>--Select--</option>
                                                    @foreach($academicLevels as $academicLevel)
                                                        <option value="{{ $academicLevel->id }}">{{ $academicLevel->level }} Level</option>
                                                    @endforeach
                                                </select>
                                                <label for="level">Academic Level</label>
                                            </div>
                                        </div>
        
                                        <div class="col-lg-12">
                                            <div class="form-floating">
                                                <select class="form-select" id="session" name="session" aria-label="Academic Session">
                                                    <option value="" selected>--Select--</option>
                                                    @foreach($academicSessions as $session)<option value="{{ $session->year }}">{{ $session->year }}</option>@endforeach
                                                </select>
                                                <label for="session">Academic Session</label>
                                            </div>
                                        </div>

                                        <button type="submit" id="submit-button" class="btn btn-fill btn-primary btn-lg btn-block mb-5">Get Report</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>
@endif

@if(!empty($students))
@php
 $totalSchoolFeeExpected = 0;
 $totalSchoolFeePaid = 0;

 foreach ($students as $student) {
    if(!empty($students)) {
        $schoolFeeDetails = $student->schoolFeeDetails;

        if (isset($schoolFeeDetails->schoolPayment) && isset($schoolFeeDetails->schoolPaymentTransaction)) {
            $totalAmountExpected = $schoolFeeDetails->schoolPayment->structures->sum('amount');
            $totalAmountPaid = $schoolFeeDetails->schoolPaymentTransaction->sum('amount_payed');

            $totalSchoolFeePaid += $totalAmountPaid;
            $totalSchoolFeeExpected += $totalAmountExpected;
        }
    }
}    
@endphp
<div class="row">
    <div class="col-md-4">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="fw-medium text-muted mb-0 text-primary">Total Expected Funds</p>
                        <h2 class="mt-4 ff-secondary fw-semibold"><span class="text-primary">₦{{ number_format($totalSchoolFeeExpected/100, 2) }}</span></h2>
                    </div>
                    <div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-soft-primary rounded-circle fs-2 shadow">
                                <i data-feather="dollar-sign" class="text-primary"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div><!-- end card body -->
        </div> <!-- end card-->
    </div> <!-- end col-->

    <div class="col-md-4">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="fw-medium text-muted mb-0">Total Paid Funds</p>
                        <h2 class="mt-4 ff-secondary fw-semibold"><span class="text-success">₦{{ number_format($totalSchoolFeePaid/100, 2) }}</span></h2>
                    </div>
                    <div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-soft-success rounded-circle fs-2 shadow">
                                <i data-feather="dollar-sign" class="text-success"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div><!-- end card body -->
        </div> <!-- end card-->
    </div> <!-- end col-->

    <div class="col-md-4">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="fw-medium text-muted mb-0 text-danger">Total Funds To Be Paid</p>
                        <h2 class="mt-4 ff-secondary fw-semibold"><span class="text-danger">₦{{ number_format(($totalSchoolFeeExpected - $totalSchoolFeePaid)/100, 2) }}</span></h2>
                    </div>
                    <div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-soft-danger rounded-circle fs-2 shadow">
                                <i data-feather="dollar-sign" class="text-danger"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div><!-- end card body -->
        </div> <!-- end card-->
    </div> <!-- end col-->

</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Report(s) for {{ $academicSession }} Academic Session | <span class="text-danger">{{ $academicLevel->level.' Level |' }}</span> <span class="text-primary"> {{ !empty($faculty) ? 'Faculty of '.$faculty->name.' |' : null }} </span>  <span class="text-info">{{ !empty($department) ? 'Department of '.$department->name.' |' : null }} </span> <span class="text-warning"> {{ !empty($programme) ? $programme->name.' ' : null }}</span></h4>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->

                <table id="buttons-datatables" class="display table table-bordered table-striped p-3" style="width:100%">
                    <thead>
                        <tr>
                            <th>SN</th>
                            <th>Student Name</th>
                            <th>Matric Number</th>
                            <th>Level</th>
                            <th>Amount to pay</th>
                            <th>Amount paid</th>
                            <th>Amount left</th>
                        </tr>
                    </thead>
                    <tbody>                     
                        @foreach($students as $student)
                            @if(!empty($student))
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $student->applicant->lastname.' '.$student->applicant->othernames }}</td>
                                    <td>{{ $student->matric_number }}</td>
                                    <td>{{ $student->academicLevel->level}}</td>
                                    <td class="text-primary">₦{{ number_format(isset($student->schoolFeeDetails->schoolPayment) && isset($student->schoolFeeDetails->schoolPaymentTransaction) ? $student->schoolFeeDetails->schoolPayment->structures->sum('amount')/100 : 0, 2)}}</td>
                                    <td class="text-success">₦{{ number_format(isset($student->schoolFeeDetails->schoolPayment) && isset($student->schoolFeeDetails->schoolPaymentTransaction) ? $student->schoolFeeDetails->schoolPaymentTransaction->sum('amount_payed')/100 : 0, 2)}}</td>
                                    <td class="text-danger">₦{{ number_format(isset($student->schoolFeeDetails->schoolPayment) && isset($student->schoolFeeDetails->schoolPaymentTransaction) ? ($student->schoolFeeDetails->schoolPayment->structures->sum('amount') - $student->schoolFeeDetails->schoolPaymentTransaction->sum('amount_payed'))/100 : 0, 2)}}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
                
                
            </div>
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end row -->
@endif
@endsection

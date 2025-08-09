@extends('student.layout.dashboard')
<?php 
    $student = Auth::guard('student')->user();
    $programme = $student->programme;
    $maxUnit = !empty($student->credit_load)?$student->credit_load:24;
    $levelAdviser = $programme->academicAdvisers->where('level_id', $student->level_id)->where('academic_session', $student->academic_session)->first();
    
    $academicSession = $student->academic_session;

    $allowedMatrics = [];
?>
@section('content')
<style>
    /* Adjust the width of the ID column */
    .table th:nth-child(1),
    .table td:nth-child(1) {
        width: 10px; /* Adjust the width as needed */
    }
    .semester-heading {
        font-weight: bold;
        font-size: 1.2em;
        padding: 10px 0;
    }
</style>
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Course Registration for {{ $academicSession }} academic session</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Course Registration</li>
                </ol>
            </div>

        </div>
    </div>
</div>
@if($existingRegistrations->count() > 0 && !$addOrRemoveTxs->count() > 0)
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <div class="row justify-content-center">
                        <div class="col-lg-9">
                            <h4 class="mt-4 fw-semibold">Course Registration for {{ $academicSession }} academic session </h4>
                            <p class="text-muted mt-3"></p>
                            <div class="mt-4">
                              Your <strong>Course Registration</strong> is complete! Click the button below to print your course review.
                            </div>
                            <div class="mt-4">
                                <form action="{{ url('/student/printCourseReg') }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <button type="submit" id="submit-button" class="btn btn-info">
                                        Click here to download
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-center mt-5 mb-2">
                        <div class="col-sm-7 col-8">
                            <img src="{{asset('assets/images/done_creg.png')}}" alt="" class="img-fluid" />
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
    @if($checkLateReg->isLate && !$checkNewStudentStatus && empty($lateRegTx) && !$addOrRemoveTxs->count() > 0)
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center">
                            <div class="row justify-content-center mt-5 mb-2">
                                <div class="col-sm-7 col-8">
                                    <img src="{{asset('assets/images/course_reg.png')}}" alt="" class="img-fluid" />
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-lg-9">
                                    <h4 class="mt-4 fw-semibold">Late Course Registration</h4>
                                    <p class="text-muted mt-3"></p>
                                    <div class="mt-4">
                                        You have incurred <span class="text-danger"><strong>₦{{ number_format(($checkLateReg->daysPast * $lateRegTxPay->structures->sum('amount'))/100, 2) }} </strong></span> as late registration fee, kindly proced to make payment below.
                                        <form action="{{ url('/student/makePayment') }}" method="POST">
                                            @csrf
                                            <div class="row g-3 mt-4">
                            
                                                <input type="hidden" name="payment_id" value="{{ $lateRegTxPay->id }}">
                                                <input type="hidden" name="amount" value="{{ ($checkLateReg->daysPast * $lateRegTxPay->structures->sum('amount')) }}">
                                                
                                               
                                                <div class="col-lg-12">
                                                    <div class="form-floating">
                                                        <select class="form-select" aria-label="paymentGateway" name="paymentGateway" required>
                                                            <option value= "" selected>Select Payment Gateway</option>
                                                            @if(env('UPPERLINK_STATUS'))<option value="Upperlink">Upperlink</option>@endif
                                                            @if(env('FLUTTERWAVE_STATUS'))<option value="Rave">Flutterwave</option>@endif
                                                            @if(env('MONNIFY_STATUS'))<option value="Monnify">Monnify</option>@endif
                                                            @if(env('PAYSTACK_STATUS'))<option value="Paystack">Paystack</option>@endif
                                                            @if(env('WALLET_STATUS'))<option value="Wallet">Wallet</option>@endif
                                                            {{-- @if(env('BANK_TRANSFER_STATUS'))<option value="BankTransfer">Transfer</option>@endif --}}
                                                        </select>
                                                        <label for="paymentGateway" class="form-label">Select Payment Gateway</label>
                                                    </div>
                                                </div>
                                               
                                                <button type="submit" id="submit-button" class="btn btn-fill btn-primary btn-lg btn-block mb-5">Proceed to make payment</button>
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
    @else
        @if(empty($levelAdviser))
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center">
                            <div class="row justify-content-center">
                                <div class="col-lg-9">
                                    <h4 class="mt-4 fw-semibold">Course Registration</h4>
                                    <p class="text-muted mt-3"></p>
                                    <div class="mt-4">
                                        Kindly be informed that course registration has not yet commenced. Please wait for further instructions from your level adviser, and we will notify you as soon as the registration period opens.
                                    </div>
                                </div>
                            </div>

                            <div class="row justify-content-center mt-5 mb-2">
                                <div class="col-sm-7 col-8">
                                    <img src="{{asset('assets/images/course_reg.png')}}" alt="" class="img-fluid" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end card-->
            </div>
            <!--end col-->
        </div>
        @elseif($levelAdviser && ($levelAdviser->course_registration == 'stop'))
            {{-- && !in_array($student->matric_number, $allowedMatrics) --}}
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-center">
                                <div class="row justify-content-center">
                                    <div class="col-lg-9">
                                        <h4 class="mt-4 fw-semibold">Course Registration</h4>
                                        <p class="text-muted mt-3"></p>
                                        <div class="mt-4">
                                            Please be advised that course registration has not yet begun. We will notify you as soon as the registration period becomes available.
                                        </div>
                                    </div>
                                </div>

                                <div class="row justify-content-center mt-5 mb-2">
                                    <div class="col-sm-7 col-8">
                                        <img src="{{asset('assets/images/course_reg.png')}}" alt="" class="img-fluid" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end card-->
                </div>
                <!--end col-->
            </div>
        @elseif($levelAdviser && ($levelAdviser->course_registration == 'start') && !$addOrRemoveTxs->count() > 0)
            <div class="row">   
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header align-items-center">
                            <h4 class="card-title mb-0 flex-grow-1">Course Registration {{ $academicSession }} academic session</h4>
                            <br/>
                            <p class=""><strong>Programme:</strong> {{ $student->programme->name }}
                            <br/><strong>Academic Session:</strong> {{ $student->academic_session }}
                            <br/><strong>Level:</strong> {{ $student->academicLevel->level }} Level</p>

                        </div><!-- end card header -->

                        <div class="card-body table-responsive">
                            <!-- Bordered Tables -->
                            <form method="post" action="{{ url('/student/registerCourses') }}">
                                <input type="hidden" value="{{ $addOrRemoveTxs->count() > 0?$addOrRemoveTxs->first()->id:null }}" name="tx_id">
                                @csrf
                                <table class="table table-borderless table-nowrap">
                                
                                    <tbody class="first-semester">
                                        <tr>
                                            <td colspan="6" class="semester-heading">
                                                
                                                <div class="card-header align-items-center">
                                                    <h4 class="card-title mb-0 flex-grow-1">Harmattan Semester Courses</h4>
                                                </div><!-- end card header -->

                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="col">ID</th>
                                            <th scope="col">Course Code</th>
                                            <th scope="col">Course Title</th>
                                            <th scope="col">Course Unit</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Select</th>
                                        </tr>
                                        @php
                                            $firstSemester = 1;
                                            $secondSemester = 1;
                                        @endphp
                                        @foreach($carryOverCourses->where('semester', 1) as $failedCourse)
                                            <tr>
                                                <td>{{ $firstSemester++ }}</td>
                                                <td>{{ $failedCourse->course->code }}</td>
                                                <td>{{ ucwords(strtolower($failedCourse->course->name)) }}</td>
                                                <td>{{ $failedCourse->credit_unit }}</td>
                                                <td>{{ $failedCourse->status }}</td>
                                                <td>
                                                    <input type="checkbox" name="selected_courses[]" value="{{ $failedCourse->id }}"
                                                        checked disabled>
                                                    <input type="hidden" name="failed_selected_courses[]" value="{{ $failedCourse->id }}">
                                                </td>
                                            </tr>
                                        @endforeach
                                        @foreach($unregisteredRequiredCourses->where('semester', 1) as $unregisteredCourse)
                                            <tr>
                                                <td>{{ $firstSemester++ }}</td>
                                                <td>{{ $unregisteredCourse->course->code }}</td>
                                                <td>{{ ucwords(strtolower($unregisteredCourse->course->name)) }}</td>
                                                <td>{{ $unregisteredCourse->credit_unit }}</td>
                                                <td>{{ $unregisteredCourse->status }}</td>
                                                <td>
                                                    <input type="checkbox" name="selected_courses[]" value="{{ $unregisteredCourse->id }}">
                                                </td>
                                            </tr>
                                        @endforeach
                                        @foreach($courses->where('semester', 1) as $course11)
                                            <tr>
                                                <td>{{ $firstSemester++ }}</td>
                                                <td>{{ $course11->course->code }}</td>
                                                <td>{{ ucwords(strtolower($course11->course->name)) }}</td>
                                                <td>{{ $course11->credit_unit }}</td>
                                                <td>{{ $course11->status }}</td>
                                                <td>
                                                    <input type="checkbox" name="selected_courses[]" value="{{ $course11->id }}">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tbody>
                                        <tr class="first-semester-total">
                                            <td>Total Harmattan Semester Credit Unit</td>
                                            <td></td>
                                            <td></td>
                                            <td>0</td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                    
                                    <tbody class="second-semester">
                                        <tr>
                                            <td colspan="6" class="semester-heading">
                                                
                                                <div class="card-header align-items-center">
                                                    <h4 class="card-title mb-0 flex-grow-1">Rain Semester Courses</h4>
                                                </div><!-- end card header -->

                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="col">ID</th>
                                            <th scope="col">Course Code</th>
                                            <th scope="col">Course Title</th>
                                            <th scope="col">Course Unit</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Select</th>
                                        </tr>
                                        @foreach($carryOverCourses->where('semester', 2) as $failedCourse2)
                                            <tr>
                                                <td>{{ $secondSemester++ }}</td>
                                                <td>{{ $failedCourse2->course->code }}</td>
                                                <td>{{ ucwords(strtolower($failedCourse2->course->name)) }}</td>
                                                <td>{{ $failedCourse2->credit_unit }}</td>
                                                <td>{{ $failedCourse2->status }}</td>
                                                <td>
                                                    <input type="checkbox" name="selected_courses[]" value="{{ $failedCourse2->id }}"
                                                        checked disabled>
                                                    <input type="hidden" name="failed_selected_courses[]" value="{{ $failedCourse2->id }}">
                                                </td>
                                            </tr>
                                        @endforeach
                                        @foreach($unregisteredRequiredCourses->where('semester', 2) as $unregisteredCourse2)
                                            <tr>
                                                <td>{{ $secondSemester++ }}</td>
                                                <td>{{ $unregisteredCourse2->course->code }}</td>
                                                <td>{{ ucwords(strtolower($unregisteredCourse2->course->name)) }}</td>
                                                <td>{{ $unregisteredCourse2->credit_unit }}</td>
                                                <td>{{ $unregisteredCourse2->status }}</td>
                                                <td>
                                                    <input type="checkbox" name="selected_courses[]" value="{{ $unregisteredCourse2->id }}">
                                                </td>
                                            </tr>
                                        @endforeach
                                        @foreach($courses->where('semester', 2) as $course12)
                                            <tr>
                                                <td>{{ $secondSemester++ }}</td>
                                                <td>{{ $course12->course->code }}</td>
                                                <td>{{ ucwords(strtolower($course12->course->name)) }}</td>
                                                <td>{{ $course12->credit_unit }}</td>
                                                <td>{{ $course12->status }}</td>
                                                <td>
                                                    <input type="checkbox" name="selected_courses[]" value="{{ $course12->id }}">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tbody>
                                        <tr class="second-semester-total">
                                            <td>Total Rain Semester Credit Unit</td>
                                            <td></td>
                                            <td></td>
                                            <td>0</td>
                                            <td></td>
                                        
                                        </tr>
                                    </tbody>
                                </table>
                                <hr>
                                <button type="submit" id="submit-button" class="btn btn-primary">Register Selected Courses</button>
                            </form>                
                        </div>
                    </div><!-- end card -->
                </div>
                <!-- end col -->
            </div>
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
                $(document).ready(function() {

                    const maxUnit = <?php echo $maxUnit; ?>; 

                    $("input[name='selected_courses[]']").change(function() {
                        calculateTotals();
                    });

                    function calculateTotals() {
                        let firstSemesterTotal = 0;
                        let secondSemesterTotal = 0;

                        $("input[name='selected_courses[]']:checked").each(function() {
                            let creditUnit = parseFloat($(this).closest("tr").find("td:eq(3)").text());
                            if ($(this).closest("tbody").hasClass("first-semester")) {
                                firstSemesterTotal += creditUnit;
                            } else if ($(this).closest("tbody").hasClass("second-semester")) {
                                secondSemesterTotal += creditUnit;
                            }
                        });

                        // Update the total credit units for each semester
                        $(".first-semester-total td:eq(3)").text(firstSemesterTotal);
                        $(".second-semester-total td:eq(3)").text(secondSemesterTotal);

                        // Enable/Disable rows based on selected credit units
                        $("input[name='selected_courses[]']").each(function() {
                        let creditUnit = parseFloat($(this).closest("tr").find("td:eq(3)").text());
                            if (!$(this).prop("checked")) {
                                if ($(this).closest("tbody").hasClass("first-semester") && firstSemesterTotal + creditUnit > maxUnit) {
                                    $(this).prop("disabled", true);
                                } else if ($(this).closest("tbody").hasClass("second-semester") && secondSemesterTotal + creditUnit > maxUnit) {
                                    $(this).prop("disabled", true);
                                } else {
                                    $(this).prop("disabled", false);
                                }
                            }
                        });
                    }
                });
            </script>
        @else
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header align-items-center">
                            <h4 class="card-title mb-0 flex-grow-1">Add or Removal of Courses, {{ $academicSession }} academic session</h4>
                            <br/>
                            <p class=""><strong>Programme:</strong> {{ $student->programme->name }}
                            <br/><strong>Academic Session:</strong> {{ $student->academic_session }}
                            <br/><strong>Level:</strong> {{ $student->academicLevel->level }} Level</p>
                            <br/>
                            <h3 class="text-danger">Ensure that you make use of a PC to make the changes.</h3>
                        </div><!-- end card header -->
                    </div>
                </div>
            </div>

            <form method="post" action="{{ url('/student/updateCourses') }}" class="mb-3">
                @csrf
                <input type="hidden" value="{{ $addOrRemoveTxs->count() > 0 ? $addOrRemoveTxs->first()->id : null }}" name="tx_id">
                
                <!-- Harmattan Semester Section -->
                <div class="row d-flex align-items-stretch">
                    <!-- Registered Courses (Harmattan) -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h4 class="card-title">Registered Courses (Harmattan)</h4>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-borderless table-nowrap registered-courses-table" data-semester="1">
                                    <thead>
                                        <tr>
                                            <th>Course Code</th>
                                            <th>Course Title</th>
                                            <th>Unit</th>
                                            <th>Status</th>
                                            <th>Remove</th>
                                        </tr>
                                    </thead>
                                    <tbody class="registered-first-semester">
                                        @foreach($registeredHarmattanCourses as $registeredHarmattanCourse)
                                            <tr>
                                                <td>{{ $registeredHarmattanCourse->course->code }}</td>
                                                <td>{{ ucwords(strtolower($registeredHarmattanCourse->course->name)) }}</td>
                                                <td>{{ $registeredHarmattanCourse->course_credit_unit }}</td>
                                                <td>{{ $registeredHarmattanCourse->course_status }}</td>
                                                <td>
                                                    <input type="checkbox" name="removed_courses[]" value="{{ $registeredHarmattanCourse->id }}">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="first-semester-total">
                                            <td colspan="2">**Current Unit Total:**</td>
                                            <td class="total-units">0</td>
                                            <td colspan="2"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Available Courses to Register (Harmattan) -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h4 class="card-title">Courses to Register (Harmattan)</h4>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-borderless table-nowrap available-courses-table" data-semester="1">
                                    <thead>
                                        <tr>
                                            <th>Course Code</th>
                                            <th>Course Title</th>
                                            <th>Unit</th>
                                            <th>Status</th>
                                            <th>Select</th>
                                        </tr>
                                    </thead>
                                    <tbody class="available-first-semester">
                                        @foreach($unregisteredRequiredCourses->where('semester', 1) as $unregisteredCourse)
                                            <tr>
                                                <td>{{ $unregisteredCourse->course->code }}</td>
                                                <td>{{ ucwords(strtolower($unregisteredCourse->course->name)) }}</td>
                                                <td>{{ $unregisteredCourse->credit_unit }}</td>
                                                <td>{{ $unregisteredCourse->status }}</td>
                                                <td>
                                                    <input type="checkbox" name="selected_courses[]" value="{{ $unregisteredCourse->id }}">
                                                </td>
                                            </tr>
                                        @endforeach
                                        @foreach($availableHarmattanCourses as $availableHarmattanCourse)
                                            <tr>
                                                <td>{{ $availableHarmattanCourse->course->code }}</td>
                                                <td>{{ ucwords(strtolower($availableHarmattanCourse->course->name)) }}</td>
                                                <td>{{ $availableHarmattanCourse->credit_unit }}</td>
                                                <td>{{ $availableHarmattanCourse->status }}</td>
                                                <td>
                                                    <input type="checkbox" name="selected_courses[]" value="{{ $availableHarmattanCourse->id }}">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                
                <!-- Rain Semester Section -->
                <div class="row mt-4 d-flex align-items-stretch">
                    <!-- Registered Courses (Rain) -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h4 class="card-title">Registered Courses (Rain)</h4>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-borderless table-nowrap registered-courses-table" data-semester="2">
                                    <thead>
                                        <tr>
                                            <th>Course Code</th>
                                            <th>Course Title</th>
                                            <th>Unit</th>
                                            <th>Status</th>
                                            <th>Remove</th>
                                        </tr>
                                    </thead>
                                    <tbody class="registered-second-semester">
                                        @foreach($registeredRainCourses as $registeredRainCourse)
                                            <tr>
                                                <td>{{ $registeredRainCourse->course->code }}</td>
                                                <td>{{ ucwords(strtolower($registeredRainCourse->course->name)) }}</td>
                                                <td>{{ $registeredRainCourse->course_credit_unit }}</td>
                                                <td>{{ $registeredRainCourse->course_status }}</td>
                                                <td>
                                                    <input type="checkbox" name="removed_courses[]" value="{{ $registeredRainCourse->id }}">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="second-semester-total">
                                            <td colspan="2">**Current Unit Total:**</td>
                                            <td class="total-units">0</td>
                                            <td colspan="2"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Available Courses to Register (Rain) -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h4 class="card-title">Courses to Register (Rain)</h4>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-borderless table-nowrap available-courses-table" data-semester="2">
                                    <thead>
                                        <tr>
                                            <th>Course Code</th>
                                            <th>Course Title</th>
                                            <th>Unit</th>
                                            <th>Status</th>
                                            <th>Select</th>
                                        </tr>
                                    </thead>
                                    <tbody class="available-second-semester">
                                        @foreach($unregisteredRequiredCourses->where('semester', 2) as $unregisteredCourse2)
                                            <tr>
                                                <td>{{ $unregisteredCourse2->course->code }}</td>
                                                <td>{{ ucwords(strtolower($unregisteredCourse2->course->name)) }}</td>
                                                <td>{{ $unregisteredCourse2->credit_unit }}</td>
                                                <td>{{ $unregisteredCourse2->status }}</td>
                                                <td>
                                                    <input type="checkbox" name="selected_courses[]" value="{{ $unregisteredCourse2->id }}">
                                                </td>
                                            </tr>
                                        @endforeach
                                        @foreach($availableRainCourses as $availableRainCourse)
                                            <tr>
                                                <td>{{ $availableRainCourse->course->code }}</td>
                                                <td>{{ ucwords(strtolower($availableRainCourse->course->name)) }}</td>
                                                <td>{{ $availableRainCourse->credit_unit }}</td>
                                                <td>{{ $availableRainCourse->status }}</td>
                                                <td>
                                                    <input type="checkbox" name="selected_courses[]" value="{{ $availableRainCourse->id }}">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary mt-3">Update All Courses</button>
                </div>
            </form>

            <script>
                $(document).ready(function() {
                    const maxUnit = <?php echo $maxUnit; ?>;

                    calculateTotals();

                    $("input[name='selected_courses[]'], input[name='removed_courses[]']").change(function() {
                        calculateTotals();
                    });

                    function calculateTotals() {
                        let firstSemesterTotal = 0;
                        let secondSemesterTotal = 0;

                        // 1. Calculate the initial total from all currently registered courses
                        $(".registered-first-semester tr").each(function() {
                            const creditUnit = parseFloat($(this).find("td:eq(2)").text());
                            firstSemesterTotal += creditUnit;
                        });

                        $(".registered-second-semester tr").each(function() {
                            const creditUnit = parseFloat($(this).find("td:eq(2)").text());
                            secondSemesterTotal += creditUnit;
                        });

                        // 2. Adjust totals based on courses marked for removal
                        $("input[name='removed_courses[]']:checked").each(function() {
                            const creditUnit = parseFloat($(this).closest("tr").find("td:eq(2)").text());
                            const semesterTable = $(this).closest("table");
                            if (semesterTable.data("semester") === 1) {
                                firstSemesterTotal -= creditUnit;
                            } else if (semesterTable.data("semester") === 2) {
                                secondSemesterTotal -= creditUnit;
                            }
                        });

                        // 3. Adjust totals based on courses marked for addition
                        $("input[name='selected_courses[]']:checked").each(function() {
                            const creditUnit = parseFloat($(this).closest("tr").find("td:eq(2)").text());
                            const semesterTable = $(this).closest("table");
                            if (semesterTable.data("semester") === 1) {
                                firstSemesterTotal += creditUnit;
                            } else if (semesterTable.data("semester") === 2) {
                                secondSemesterTotal += creditUnit;
                            }
                        });

                        $(".first-semester-total .total-units").text(firstSemesterTotal);
                        $(".second-semester-total .total-units").text(secondSemesterTotal);

                        $("input[name='selected_courses[]']").each(function() {
                            const creditUnit = parseFloat($(this).closest("tr").find("td:eq(2)").text());
                            const semesterTable = $(this).closest("table");
                            const semester = semesterTable.data("semester");
                            const currentTotal = semester === 1 ? firstSemesterTotal : secondSemesterTotal;

                            if (!$(this).prop("checked")) {
                                if (currentTotal + creditUnit > maxUnit) {
                                    $(this).prop("disabled", true);
                                } else {
                                    $(this).prop("disabled", false);
                                }
                            } else {
                                $(this).prop("disabled", false);
                            }
                        });
                    }
                });
            </script>
        @endif
    @endif
@endif
@endsection
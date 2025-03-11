@extends('student.layout.dashboard')
<?php 
    $student = Auth::guard('student')->user();
    $programme = $student->programme;
    $maxUnit = !empty($student->credit_load)?$student->credit_load:24;
    $levelAdviser = $programme->academicAdvisers->where('level_id', $student->level_id)->where('academic_session', $student->academic_session)->first();

    $allowedMatrics = [
        '22/05PTP026', 
        '24/05PTP159', 
        '24/05PTP157', 
        '22/05BLL022', 
        '20/05NSS010', 
        '20/05NSS006', 
        '20/05NSS003', 
        '23/05NSS246', 
        '22/05PTP038', 
        '23/05NSS246',
        "24/04NSS446",
        "24/04NSS447",
        "24/04NSS450",
        "24/04NSS455",
        "24/04NSS458",
        "24/04NSS459",
        "24/04NSS474",
        "24/04NSS490",
        "24/04NSS571",
        "24/04NSS575",
        "24/04NSS576",
        "24/20LAL156",
        "24/04NSS389",
        "24/04NSS398",
        "24/15ECC034",
        "24/04NSS423",
        "24/20LAL112",
        "24/04NSS549",
        "24/04NSS566",
        "24/20LAL142",
        "24/15MAA082",
        "24/04NSS551",
        "24/05PHP056",
        "24/05PTP150",
        "24/05PHP062",
        "24/05PHP040",
        "24/15SCS108",
        "24/10MSS082",
        "24/05BLL133",
        "24/10MSC065",
        "24/06ELE031",
        "24/05PHP041",
        "24/10MSC071",
        "24/10MSC064",
        "24/05BLL153",
        "24/10MSC072",
        "24/05PTP140",
        "24/05BLL140",
        "24/10MSS080",
        "24/05PTP152",
        "24/10MSC073",
        "24/05PHP060",
        "24/05PHP059",
        "24/15MAA140",
        "24/10MSS084",
        "24/05PHP063",
        "24/15MAA139",
        "24/15BAA062",
        "24/15SCS119",
        "24/10MSC077",
        "24/05ANA049",
        "24/15SCS120",
        "24/30COEC034",
        "24/15MAA141",
        "23/04NSS354",
        "24/30MEEM027",
        "24/10MSS073",
        "24/10MSC078",
        "24/05PTP158",
    ]; 
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
            <h4 class="mb-sm-0">Course Registration for {{ $pageGlobalData->sessionSetting->academic_session }} academic session</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Course Registration</li>
                </ol>
            </div>

        </div>
    </div>
</div>
@if($existingRegistration->count() > 0 && !$addOrRemoveTxs->count() > 0)
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <div class="row justify-content-center">
                        <div class="col-lg-9">
                            <h4 class="mt-4 fw-semibold">Course Registration for {{ $pageGlobalData->sessionSetting->academic_session }} academic session </h4>
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
    @if($checkLateReg->isLate && !$checkNewStudentStatus && empty($lateRegTx))
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
                                                            @if(env('BANK_TRANSFER_STATUS'))<option value="BankTransfer">Transfer</option>@endif
                                                            @if(env('WALLET_STATUS'))<option value="Wallet">Wallet</option>@endif
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
        @endif
        @if($levelAdviser && ($levelAdviser->course_registration == 'stop' && !in_array($student->matric_number, $allowedMatrics)))
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
        @else
            <div class="row">   
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header align-items-center">
                            <h4 class="card-title mb-0 flex-grow-1">Course Registration {{ $pageGlobalData->sessionSetting->academic_session }} academic session</h4>
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
        @endif
    @endif
@endif
@endsection
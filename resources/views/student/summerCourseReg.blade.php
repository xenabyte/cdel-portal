@extends('student.layout.dashboard')
@php
    $student = Auth::guard('student')->user();
    $maxUnit = !empty($student->credit_load)?$student->credit_load:15;

    $academicSession = $student->academic_session;
@endphp

@section('content')

@if(!env('SUMMER_COURSE_REGISTRATION'))
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <div class="row justify-content-center">
                        <div class="col-lg-9">
                            <h4 class="mt-4 fw-semibold">Course Registration for {{ $academicSession }} Academic Session - Summer Semester</h4>
                            <p class="text-muted mt-3"></p>
                            <div class="mt-4">
                                <strong>Summer Course Registration has not started yet.</strong> Please check back later for updates.
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
    @if($existingSummerRegistration->count() > 0)
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        <div class="row justify-content-center">
                            <div class="col-lg-9">
                                <div class="mt-4">
                                    <h4 class="mt-4 fw-semibold">Course Registration for {{ $academicSession }} Academic Session - Summer Semester</h4>
                                </div>
                                <div class="mt-4">
                                    <form action="{{ url('/student/printSummerCourseReg') }}" method="post" enctype="multipart/form-data">
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
    <div class="row">   
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center">
                    <h4 class="card-title mb-0 flex-grow-1">Summer Course Registration for {{ $academicSession }} academic session</h4>
                    <br/>
                    <p class=""><strong>Programme:</strong> {{ $student->programme->name }}
                    <br/><strong>Academic Session:</strong> {{ $student->academic_session }}
                    <br/><strong>Level:</strong> {{ $student->academicLevel->level }} Level</p>

                </div><!-- end card header -->

                <div class="card-body table-responsive">
                    <!-- Bordered Tables -->
                    <form method="post" action="{{ url('/student/makePayment') }}">
                        @csrf

                        <input type="hidden" name="payment_id" value="{{ $summerCourseRegPayment->id }}">

                        <input type="hidden" name="amount" id="totalAmountToPay">
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
                                @foreach($failedCourseRegs->where('semester', 1) as $failedCourse)
                                    <tr>
                                        <td>{{ $firstSemester++ }}</td>
                                        <td>{{ $failedCourse->course->code }}</td>
                                        <td>{{ ucwords(strtolower($failedCourse->course->name)) }}</td>
                                        <td>{{ $failedCourse->course_credit_unit }}</td>
                                        <td>{{ $failedCourse->status }}</td>
                                        <td>
                                            <input type="checkbox" name="failed_selected_courses[]" value="{{ $failedCourse->id }}">
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
                                @foreach($failedCourseRegs->where('semester', 2) as $failedCourse2)
                                    <tr>
                                        <td>{{ $secondSemester++ }}</td>
                                        <td>{{ $failedCourse2->course->code }}</td>
                                        <td>{{ ucwords(strtolower($failedCourse2->course->name)) }}</td>
                                        <td>{{ $failedCourse2->course_credit_unit }}</td>
                                        <td>{{ $failedCourse2->status }}</td>
                                        <td>
                                            <input type="checkbox" name="failed_selected_courses[]" value="{{ $failedCourse2->id }}">
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
                        <div class="col-lg-12 mt-4">
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

                        <div class="alert alert-success alert-dismissible alert-additional fade show mt-5" id="courseFeeAlert" role="alert" style="display: none;">
                            <div class="alert-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <h5 class="alert-heading">Total Fee Calculation</h5>
                                        <hr>
                                        <p class="mb-0">
                                            You have selected <strong><span id="selectedCoursesCount">0</span></strong> course(s).
                                            Total amount to pay (excluding gateway charges): <strong>₦<span id="calculatedAmount">0.00</span></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" id="submit-button" class="btn btn-primary mt-5">Make payment & Register Selected Courses</button>
                    </form>                
                </div>
            </div><!-- end card -->
        </div>
        <!-- end col -->
    </div>
    @endif
@endif
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        const maxUnit = <?php echo $maxUnit; ?>; 

        $("input[name='failed_selected_courses[]']").change(function() {
            calculateTotals();
        });

        function calculateTotals() {
            let firstSemesterTotal = 0;
            let secondSemesterTotal = 0;

            $("input[name='failed_selected_courses[]']:checked").each(function() {
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
            $("input[name='failed_selected_courses[]']").each(function() {
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

    document.addEventListener('DOMContentLoaded', function () {
        const checkboxes = document.querySelectorAll('input[name="failed_selected_courses[]"]');
        const paymentSelect = document.querySelector('select[name="paymentGateway"]');
        const alertBox = document.getElementById('courseFeeAlert');
        const selectedCountSpan = document.getElementById('selectedCoursesCount');
        const calculatedAmountSpan = document.getElementById('calculatedAmount');
        const totalAmountInput = document.getElementById('totalAmountToPay');
        const submitButton = document.getElementById('submit-button');

        const feePerCourse = {{ $summerCourseRegPayment->structures->sum('amount') }};

        function calculateTotal() {
            let selectedCount = 0;
            let totalCreditUnits = 0;

            checkboxes.forEach(cb => {
                if (cb.checked) {
                    selectedCount++;
                    totalCreditUnits += parseInt(cb.dataset.credit || 0);
                }
            });

            const paymentGatewaySelected = paymentSelect.value !== '';

            if (selectedCount > 0 && paymentGatewaySelected) {
                const total = (selectedCount * feePerCourse) / 100;
                
                selectedCountSpan.textContent = selectedCount;
                calculatedAmountSpan.textContent = total.toLocaleString();
                totalAmountInput.value = total;

                alertBox.style.display = 'block';
                submitButton.disabled = false;
            } else {
                alertBox.style.display = 'none';
                totalAmountInput.value = '';
                submitButton.disabled = true;
            }

            // Optional: Update per-semester credit unit totals
            document.querySelector('.first-semester-total td:nth-child(4)').textContent = calculateSemesterCredit(1);
            document.querySelector('.second-semester-total td:nth-child(4)').textContent = calculateSemesterCredit(2);
        }

        function calculateSemesterCredit(semester) {
            let sum = 0;
            checkboxes.forEach(cb => {
                const row = cb.closest('tr');
                if (cb.checked && row.closest(`.${semester === 1 ? 'first' : 'second'}-semester`)) {
                    sum += parseInt(cb.dataset.credit || 0);
                }
            });
            return sum;
        }

        // Attach listeners
        checkboxes.forEach(cb => cb.addEventListener('change', calculateTotal));
        paymentSelect.addEventListener('change', calculateTotal);

        // Disable button on load
        submitButton.disabled = true;
    });
</script>
@endsection
@extends('student.layout.dashboard')
<?php 
    $student = Auth::guard('student')->user();
    $programme = $student->programme;
    $maxUnit = !empty($student->credit_load)?$student->credit_load:24;
    $levelAdviser = $programme->academicAdvisers->where('level_id', $student->level_id)->where('academic_session', $student->academic_session)->first();
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
            <h4 class="mb-sm-0">Registered Courses for {{ $pageGlobalData->sessionSetting->academic_session }} academic session</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Registered Courses</li>
                </ol>
            </div>

        </div>
    </div>
</div>
@if($courseRegs->count() < 1)
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <div class="row justify-content-center">
                        <div class="col-lg-9">
                            <h4 class="mt-4 fw-semibold">Registered Courses for {{ $pageGlobalData->sessionSetting->academic_session }} academic session </h4>
                            <p class="text-muted mt-3"></p>
                            <div class="mt-4">
                              Your <strong>Registered Courses</strong> is not complete! Click the button below to proceed to your Registered Courses.
                            </div>
                            <div class="mt-4">
                                <a href="{{ url('/student/courseRegistration') }}" class="btn btn-secondary m-1">Proceed to Registration</a>
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
                <h4 class="card-title mb-0 flex-grow-1">Registered Courses {{ $pageGlobalData->sessionSetting->academic_session }} academic session</h4>
                <br/>
                <p class=""><strong>Programme:</strong> {{ $student->programme->name }}
                <br/><strong>Academic Session:</strong> {{ $student->academic_session }}
                <br/><strong>Level:</strong> {{ $student->academicLevel->level }} Level</p>

            </div><!-- end card header -->

            <div class="card-body table-responsive">
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
                            <th scope="col">Attendance Percentage</th>
                            <th scope="col">Course Code</th>
                            <th scope="col">Course Title</th>
                            <th scope="col">Course Unit</th>
                        </tr>
                        @php
                            $firstSemester = 1;
                            $secondSemester = 1;
                            $firstCreditUnits = $courseRegs->where('semester', 1)->sum('course_credit_unit');
                            $secondCreditUnits = $courseRegs->where('semester', 2)->sum('course_credit_unit');
                        @endphp
                        @foreach($courseRegs->where('semester', 1) as $course11)
                            <tr>
                                <td>{{ $firstSemester++ }}</td>
                                <td>{{ $course11->attendancePercentage() }}% </td>
                                <td>{{ $course11->course->code }}</td>
                                <td>{{ ucwords(strtolower($course11->course->name)) }}</td>
                                <td>{{ $course11->course_credit_unit }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tbody>
                        <tr class="first-semester-total">
                            <td>Total Harmattan Semester Credit Unit</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{ $firstCreditUnits }}</td>
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
                            <th scope="col">Attendance Percentage</th>
                            <th scope="col">Course Code</th>
                            <th scope="col">Course Title</th>
                            <th scope="col">Course Unit</th>
                        </tr>
                        @foreach($courseRegs->where('semester', 2) as $course12)
                            <tr>
                                <td>{{ $secondSemester++ }}</td>
                                <td>{{ $course12->attendancePercentage() }}% </td>
                                <td>{{ $course12->course->code }}</td>
                                <td>{{ ucwords(strtolower($course12->course->name)) }}</td>
                                <td>{{ $course12->course_credit_unit }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tbody>
                        <tr class="second-semester-total">
                            <td>Total Rain Semester Credit Unit</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{ $secondCreditUnits }}</td>
                        
                        </tr>
                    </tbody>
                </table>               
            </div>
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>

@endif
@endsection
@extends('staff.layout.dashboard')
@php
    $totalStudent = $registrations->count();
    $registrationDetails = $registrations->first();
    $passedPercent = 0;
    $failedPercent = 0;
    if($totalStudent){
        $totalStudentPassed = $registrations->where('grade', '!=', 'F')->count();
        $totalStudentFailed = $registrations->where('grade', 'F')->count();


        $passedPercent = number_format(($totalStudentPassed / $totalStudent) * 100, 2);
        $failedPercent = number_format(($totalStudentFailed / $totalStudent) * 100, 2);
    }

    
    $staff = !empty($lecturerDetails) ? $lecturerDetails->staff : null;
    $staffName = !empty($staff) ? $staff->title.' '.$staff->lastname.' '.$staff->othernames :null;
    $staffId = !empty($staff) ? $staff->id : null;
@endphp
@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Course Details</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Course Details</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h5 class="fs-15 fw-semibold">Course code: {{ $course->code  }}</h5>
                <p class="text-muted">{{ ucwords(strtolower($course->name))  }}</p>
                <p class="text-muted">Credit Unit: {{ !empty($registrationDetails) ? $registrationDetails->course_credit_unit : null }}</p>
                <p class="text-muted">Lecturer: {{ $staffName }}</p>
                <hr>
                <div class="d-flex flex-wrap justify-content-evenly">
                    <p class="text-muted mb-0"><i class="mdi mdi-account-circle text-success fs-18 align-middle me-2 rounded-circle shadow"></i>{{ $totalStudent }} Student(s)</p>
                    <p class="text-muted mb-0"><i class="mdi mdi-book-clock text-info fs-18 align-middle me-2 rounded-circle shadow"></i>{{ !empty($registrationDetails) ? ($registrationDetails->semester == 1 ? 'First' : 'Second') : null }} Semester</p>
                    <p class="text-muted mb-0"><i class="mdi mdi-clipboard-clock text-primary fs-18 align-middle me-2 rounded-circle shadow"></i>{{ $academicSession }} Academic Session</p>
                </div>
            </div>
            <div class="progress animated-progress bg-soft-primary rounded-bottom rounded-0" style="height: 6px;">
                <div class="progress-bar bg-success rounded-0" role="progressbar" style="width: 30%" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                <div class="progress-bar bg-info rounded-0" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                <div class="progress-bar rounded-0" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
    </div><!-- end col -->


    <div class="col-xl-6">
        <!-- card -->
        <div class="card card-height-100">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Result Summary </h4>
            </div><!-- end card header -->

            <!-- card body -->
            <div class="card-body">

                <div class="px-2 py-2 mt-1">
                    <p class="mb-1">Passed <span class="float-end">{{$passedPercent}}%</span></p>
                    <div class="progress mt-2 bg-soft-success" style="height: 6px;">
                        <div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: {{$passedPercent}}%" aria-valuenow="{{$passedPercent}}" aria-valuemin="0" aria-valuemax="{{$passedPercent}}">
                        </div>
                    </div>

                    <p class="mt-3 mb-1">Failed <span class="float-end">{{$failedPercent}}%</span></p>
                    <div class="progress mt-2 bg-soft-danger" style="height: 6px;">
                        <div class="progress-bar progress-bar-striped bg-danger" role="progressbar" style="width: {{$failedPercent}}%" aria-valuenow="{{$failedPercent}}" aria-valuemin="0" aria-valuemax="{{$failedPercent}}">
                        </div>
                    </div>
                </div>
            </div>
            <!-- end card body -->
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->

<div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Lectures </h4>
                    <div class="flex-shrink-0">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createLecture">Create Lecture</button>
                    </div>
                </div><!-- end card header -->
    
                <div class="table-responsive mt-5">
                    <table id="buttons-datatables2" class="display table table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th scope="col">Id</th>
                                <th scope="col">Course Code</th>
                                <th scope="col">Topic</th>
                                <th scope="col">Date</th>
                                <th scope="col">Duration</th>
                                <th scope="col">Student Attendance(s)</th>
                                <th scope="col"></th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courseLectures as $courseLecture)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $course->code }}</td>
                                    <td>{{ $courseLecture->topic }}</td>
                                    <td>{{ $courseLecture->date }}</td>
                                    <td>{{ $courseLecture->duration }}</td>
                                    <td>
                                        {{ $courseLecture->lectureAttendance->count() }}  student(s) 
                                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#viewLectureAttendance{{$courseLecture->id}}" class="btn btn-secondary m-1"><i class= "ri-eye-fill"></i> View</a>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#uploadAttendance{{$courseLecture->id}}">Bulk upload attendance</button>
                                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#updateLecture{{$courseLecture->id}}" class="btn btn-secondary m-1"><i class="ri-edit-circle-fill"></i> Edit Lecture</a>
                                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#deleteLecture{{$courseLecture->id}}" class="btn btn-danger m-1"><i class="ri-delete-bin-5-line"></i> Delete Lecture</a>
                                    </td>
                                    <td>
                                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#markLectureAttendance{{$courseLecture->id}}" class="btn btn-success m-1"><i class="ri-user-follow-fill"></i> Mark Attendance</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div><!-- end col -->

    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Registered Student(s)</h4>
                    <div class="flex-shrink-0">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#messageStudents">Message All Students</button>
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#uploadResult">Bulk upload result</button>
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#updateStudentResult">Update Student Result</button>
                    </div>
                </div><!-- end card header -->
    
                <div class="table-responsive mt-5">
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <strong>Attention Lecturers!</strong> Download the CSV file of students enrolled for this course by clicking <strong>CSV Button</strong>  below, update the scores, and upload the file to update student results.
                    </div>
                    <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th scope="col">Id</th>
                                <th scope="col">Course Code</th>
                                <th scope="col">Attendance Percentage</th>
                                <th scope="col">Name</th>
                                <th scope="col">Matric Number</th>
                                <th scope="col">Programme</th>
                                <th scope="col">Email</th>
                                <th scope="col">Phone No</th>
                                <th scope="col">Test Score</th>
                                <th scope="col">Exam Score</th>
                                <th scope="col">Total Score</th>
                                <th scope="col">Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($registrations as $registration)
                                @if($registration->student)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $course->code }}</td>
                                    <td>{{ round($registration->attendancePercentage()) }}% </td>
                                    <td>{{ $registration->student->applicant->lastname .' '. $registration->student->applicant->othernames }}</td>
                                    <td>{{ $registration->student->matric_number }}</td>
                                    <td>{{ $registration->student->programme->name }}</td>
                                    <td>{{ $registration->student->email }} </td>
                                    <td>{{ $registration->student->applicant->phone_number }} </td>
                                    <td>{{ $registration->ca_score }} </td>
                                    <td>{{ $registration->exam_score }} </td>
                                    <td>{{ $registration->total }}</td>
                                    <td>{{ $registration->grade }} </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div><!-- end col -->
</div> <!-- end row-->

<div id="messageStudents" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Send Message to Registered Students</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <div class="modal-body">
                <form action="{{ url('/staff/sendMessage') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $course->id }}">
                    <input type="hidden" name="programme_category_id" value="{{ $programmeCategory->id }}" />

                    <div class="form-floating">
                        <textarea class="form-control ckeditor" name="message"></textarea>
                        <label for="semester">Message</label>
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Send Message</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="updateStudentResult" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Update Result</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <div class="modal-body">
                <form action="{{ url('/staff/updateStudentResult') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $course->id }}">
                    <input type="hidden" name="programme_category_id" value="{{ $programmeCategory->id }}" />


                    <div class="form-floating mb-3">
                        <input type="text" name="matric_number" id="matric_number" class="form-control" required>
                        <label for="matric_number">Matric Number</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="number" name="test" id="test" step="0.01" class="form-control">
                        <label for="test">Test Score</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="number" name="exam" id="exam" step="0.01" class="form-control">
                        <label for="exam">Exam Score</label>
                    </div>

                    <div class="col-lg-12">
                        <div class="form-floating">
                            <select class="form-select" id="type" name="type" aria-label="type" required>
                                <option value="" selected>--Select--</option>
                                <option value="test">Test</option>
                                <option value="exam">Exam</option>
                            </select>
                            <label for="type">Result Type</label>
                        </div>
                    </div>

                    @if(!empty($registrations->grade))
                    <div class="form-floating mb-3 mt-3">
                        <input type="password" name="passcode" id="passcode" class="form-control" required>
                        <label for="passcode">Password <code>Get Code from your HOD</code></label>
                    </div>
                    @endif

                    <hr>
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div id="uploadResult" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Upload Result</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <div class="modal-body">
                <form action="{{ url('/staff/staffUploadResult') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $course->id }}">
                    <input type="hidden" name="programme_category_id" value="{{ $programmeCategory->id }}" />

                    <div class="row">
                        <div class="col-lg-12">
                            <div>
                                <label for="formSizeLarge" class="form-label">Result (CSV)</label>
                                <input name="result"  class="form-control form-control-lg" id="formSizeLarge" type="file" required>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="form-floating">
                            <select class="form-select" id="type" name="type" aria-label="type" required>
                                <option value="" selected>--Select--</option>
                                <option value="test">Test</option>
                                <option value="exam">Exam</option>
                            </select>
                            <label for="type">Result Type</label>
                        </div>
                    </div>

                    @if(!empty($registrations->grade))
                    <div class="form-floating mb-3 mt-3">
                        <input type="password" name="passcode" id="passcode" class="form-control" required>
                        <label for="passcode">Password <code>Get Code from your HOD</code></label>
                    </div>
                    @endif
                    <hr>
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Upload Result</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="createLecture" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Create Lecture</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <div class="modal-body">
                <form action="{{ url('/staff/createLecture') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $course->id }}">
                    <input type="hidden" name="programme_category_id" value="{{ $programmeCategory->id }}" />


                    <div class="form-floating mb-3">
                        <input type="text" name="topic" id="topic" class="form-control" required>
                        <label for="topic">Lecture Topic</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="text" name="duration" id="duration" class="form-control">
                        <label for="test">Duration</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="date" name="date" id="date" class="form-control">
                        <label for="date">Lecture Date</label>
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Create Lecture</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@foreach($courseLectures as $courseLecture)
<div id="viewLectureAttendance{{ $courseLecture->id }}" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
    <!-- Fullscreen Modals -->
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Student(s) in Attendance for {{ $courseLecture->topic }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <h4>Students</h4>
                    </div>
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="buttons-datatables3" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">Id</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Matric No</th>
                                        <th scope="col">Programme</th>
                                        <th scope="col">Email</th>
                                        <th scope="col"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($courseLecture->lectureAttendance as $attendance)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td>{{ $attendance->student->applicant->lastname .' '. $attendance->student->applicant->othernames }}</td>
                                        <td>{{ $attendance->student->matric_number }}</td>
                                        <td>{{ $attendance->student->programme->name }}</td>
                                        <td>{{ $attendance->student->email }} </td>
                                        <td>
                                            <form action="{{ url('/staff/deleteStudentAttendance') }}" method="POST">
                                                @csrf
                                                <input name="attendance_id" type="hidden" value="{{$attendance->id}}">
                                                <button type="submit" id="submit-button" class="btn btn-danger w-100"><i class="ri-delete-bin-5-line"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="markLectureAttendance{{ $courseLecture->id }}" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
    <!-- Fullscreen Modals -->
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Mark Student(s) in Attendance for {{ $courseLecture->topic }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form action="{{ url('/staff/markStudentAttendance') }}" method="post" enctype="multipart/form-data">
                            @csrf
                        
                            <input name="lecture_id" type="hidden" value="{{$courseLecture->id}}">
                            <input type="hidden" name="programme_category_id" value="{{ $programmeCategory->id }}" />

                            <div class="mb-3">
                                <label>Select Students Present</label>
                                <select class="form-select select2 selectWithSearch" name="student_id[]" multiple aria-label="cstatus">
                                    @foreach($registrations as $studentReg)
                                        @if($studentReg->student)
                                            <option value="{{$studentReg->student->id}}">{{$studentReg->student->matric_number}} - {{ $studentReg->student->applicant->lastname .' '. $studentReg->student->applicant->othernames }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <hr>
                            <div>
                                <button type="submit" id="submit-button" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="uploadAttendance{{ $courseLecture->id }}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Upload Attendance for {{ $courseLecture->topic }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <div class="modal-body">
                <form action="{{ url('/staff/staffUploadAttendance') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $course->id }}">
                    <input type="hidden" name="staff_id" value="{{ $staffId }}">
                    <input type="hidden" name="lecture_id" value="{{ $courseLecture->id }}">
                    <input type="hidden" name="programme_category_id" value="{{ $programmeCategory->id }}" />


                    <div class="row">
                        <div class="col-lg-12">
                            <div>
                                <label for="formSizeLarge" class="form-label">Attendance (CSV)</label>
                                <input name="attendance"  class="form-control form-control-lg" id="formSizeLarge" type="file" required>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Upload Attendance</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="updateLecture{{ $courseLecture->id }}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Update Lecture</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <div class="modal-body">
                <form action="{{ url('/staff/updateLecture') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $course->id }}">
                    <input type="hidden" name="lecture_id" value="{{ $courseLecture->id }}">
                    <input type="hidden" name="programme_category_id" value="{{ $programmeCategory->id }}" />



                    <div class="form-floating mb-3">
                        <input type="text" name="topic" id="topic" class="form-control" value="{{ $courseLecture->topic }}" required>
                        <label for="topic">Lecture Topic</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="text" name="duration" id="duration" value="{{ $courseLecture->duration }}" class="form-control">
                        <label for="test">Duration</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="date" name="date" id="date" value="{{ $courseLecture->date }}" class="form-control">
                        <label for="date">Lecture Date</label>
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="deleteLecture{{ $courseLecture->id }}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="text-end">
                    <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="mt-2">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="hover" style="width:150px;height:150px">
                    </lord-icon>
                    <h4 class="mb-3 mt-4">Are you sure you want to delete <br/> {{ $courseLecture->topic }}?</h4>
                    <form action="{{ url('/staff/deleteLecture') }}" method="POST">
                        @csrf
                        <input type="hidden" name="lecture_id" value="{{ $courseLecture->id }}">
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
@endforeach

@endsection
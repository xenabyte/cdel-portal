@extends('staff.layout.dashboard')
@php
    $staff = Auth::guard('staff')->user();

    $totalStudent = $course->registeredStudents->count();

    $passedPercent = 0;
    $failedPercent = 0;
    if($totalStudent){
        $totalStudentPassed = $course->registeredStudents->where('grade', '!=', 'F')->count();
        $totalStudentFailed = $course->registeredStudents->where('grade', 'F')->count();

        $passedPercent = number_format(($totalStudentPassed / $totalStudent) * 100, 2);
        $failedPercent = number_format(($totalStudentFailed / $totalStudent) * 100, 2);
    }
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
                <p class="text-muted">{{ $course->name  }}</p>
                <p class="text-muted">Credit Unit: {{ $course->credit_unit  }}</p>
                <hr>
                <div class="d-flex flex-wrap justify-content-evenly">
                    <p class="text-muted mb-0"><i class="mdi mdi-account-circle text-success fs-18 align-middle me-2 rounded-circle shadow"></i>{{ $course->registeredStudents->count() }} Student(s)</p>
                    <p class="text-muted mb-0"><i class="mdi mdi-book-clock text-info fs-18 align-middle me-2 rounded-circle shadow"></i>{{ $course->semester == 1 ? 'First' : 'Second' }} Semester</p>
                    <p class="text-muted mb-0"><i class="mdi mdi-clipboard-clock text-primary fs-18 align-middle me-2 rounded-circle shadow"></i>{{ $pageGlobalData->sessionSetting->academic_session }} Academic Session</p>
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
                    <h4 class="card-title mb-0 flex-grow-1">Registered Student(s) </h4>
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
                                <th scope="col">Name</th>
                                <th scope="col">Matric No</th>
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
                            @foreach($course->registrations as $registration)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
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
                    <div class="form-floating">
                        <textarea class="form-control" name="message"></textarea>
                        <label for="semester">Message</label>
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Send Message</button>
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
                    <div class="form-floating mb-3">
                        <input type="text" name="matric_number" id="matric_number" class="form-control" required>
                        <label for="matric_number">Matric Number</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="number" name="test" id="test" class="form-control" required>
                        <label for="test">Test Score</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="number" name="exam" id="exam" class="form-control" required>
                        <label for="exam">Exam Score</label>
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
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
                    <div class="row">
                        <div class="col-lg-12">
                            <div>
                                <label for="formSizeLarge" class="form-label">Result (CSV)</label>
                                <input name="result"  class="form-control form-control-lg" id="formSizeLarge" type="file" required>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Upload Result</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@endsection
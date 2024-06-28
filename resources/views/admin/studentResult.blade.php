@extends('admin.layout.dashboard')

@section('content')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Student Result</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Student Result</li>
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
    <div class="col-xxl-3">
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
                            <strong>Jamb Reg. Number:</strong> {{ $student->applicant->jamb_reg_no }}<br>
                            <strong>Level:</strong> {{ $student->level_id *100 }} Level <br>
                            <strong>Support Code:</strong> <span class="text-danger">ST{{ sprintf("%06d", $student->id) }}</span> <br>
                            <strong>CGPA:</strong> <span class="text-primary">{{ $student->cgpa }}</span> 
                            <hr>
                            @if(env('WALLET_STATUS'))<a class="dropdown-item" href=#"><i class="mdi mdi-wallet text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Balance : <b>₦{{ number_format($student->amount_balance/100, 2) }}</b></span></a>@endif
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

            @if(!empty($student->applicant->guardian))
            <div class="card-body border-top border-top-dashed p-4">
                <div>
                    <h6 class="text-muted text-uppercase fw-semibold mb-4">Guardian Info</h6>
                    <div class="table-responsive">
                        <table class="table mb-0 table-borderless">
                            <tbody>
                                <tr>
                                    <th><span class="fw-medium">SN</span></th>
                                    <td class="text-danger">#{{ $student->applicant->guardian->id }}</td>
                                </tr>
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

    <div class="col-xxl-9">
        {{-- Student Message --}}
        @if(empty($registeredCourses))
        <div class="card">
            <div class="card-header border-0 align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Get Student Result</h4>
                <div class="text-end mb-5">
                    
                </div>
            </div><!-- end card header -->

            <div class="card-body pb-2 border-top border-top-dashed">
                <form action="{{ url('/admin/result/getStudentResult') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="url" value="admin.studentResult">
                    <input type="hidden" name="reg_number" value="{{ $student->matric_number }}">
                    
                    <div class="mb-3">
                        <label for="type" class="form-label">Select Level</label>
                        <select class="form-select" aria-label="type" name="level_id" required>
                            <option selected value="">Select Level</option>
                            @foreach($levels as $level) @if($student->level_id >= $level->id)<option value="{{ $level->id }}">{{ $level->id * 100 }} Level</option> @endif @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Select Session</label>
                        <select class="form-select" aria-label="type" name="session" required>
                            <option selected value="">Select Session</option>
                            @foreach($sessions as $session)<option value="{{ $session->year }}">{{ $session->year }}</option>@endforeach
                        </select>
                    </div>

                    <div class="text-end border-top border-top-dashed p-3">
                        <br>
                        <button type="submit" id="submit-button" class="btn btn-primary">Get Student Result</button>
                    </div>
                </form>
            </div><!-- end card body -->
        </div><!-- end card -->
        @else
        <div class="card">
            <div class="card-header border-0 align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Student Result for {{ $studentLevelId * 100 }} Level for {{ $studentSession }} Academic Session</h4>
                <div class="text-end mb-5">
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#add" class="btn btn-primary">
                        Add Missing Course</a>
                </div>
            </div><!-- end card header -->

            <div class="card-body table-responsive pb-2 border-top border-top-dashed">
                <table id="buttons-datatables" class="display table table-bordered" style="width:100%">            
                    <tbody>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Semester</th>
                            <th scope="col">Course Code</th>
                            <th scope="col">Course Title</th>
                            <th scope="col">Course Unit</th>
                            <th scope="col">Test Score</th>
                            <th scope="col">Exam Score</th>
                            <th scope="col">Total</th>
                            <th scope="col">Grade</th>
                            <th scope="col">action</th>
                        </tr>
                        @foreach($registeredCourses as $registeredCourse)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $registeredCourse->semester }}</td>
                                <td>{{ $registeredCourse->course->code }}</td>
                                <td>{{ $registeredCourse->course->name }}</td>
                                <td>{{ $registeredCourse->course_credit_unit }}</td>
                                <td>{{ $registeredCourse->ca_score }}</td>
                                <td>{{ $registeredCourse->exam_score  }}</td>
                                <td>{{ $registeredCourse->total  }}</td>
                                <td>{{ $registeredCourse->grade  }}</td>
                                <td>
                                    <div class="flex-shrink-0">
                                        <div class="flex-shrink-0">
                                            <form method="post" action="{{ url('/admin/result/deleteStudentResult') }}">
                                                @csrf
                                                <input type="hidden" name="url" value="admin.studentResult">
                                                <input type="hidden" name='course_reg_id' value="{{ $registeredCourse->id }}">
                                                <input type="hidden" name='student_id' value="{{ $student->id }}">
                                                <input type="hidden" name='session' value="{{ $studentSession }}">
                                                <input type="hidden" name='level_id' value="{{  $studentLevelId }}">

                                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit{{$registeredCourse->id}}" class="btn btn-info m-2"><i class= "mdi mdi-application-edit"></i></a>
                                                
                                                <button type="submit" class="btn btn-danger waves-effect waves-light m-2">
                                                  <i class="mdi mdi-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <div id="edit{{$registeredCourse->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 overflow-hidden">
                                                <div class="modal-header p-3">
                                                    <h4 class="card-title mb-0">Update Result</h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
            
                                                <div class="modal-body">
                                                    <h4 class="card-title mb-0">{{ $registeredCourse->course->code }} || {{ $registeredCourse->course->name }} || {{ $registeredCourse->course_credit_unit }} Unit</h4>
                                                    <hr>
                                                    <form action="{{ url('/admin/result/updateStudentResult') }}" method="post" enctype="multipart/form-data">
                                                        @csrf
                                                        <input type="hidden" name="url" value="admin.studentResult">
                                                        <input type="hidden" name='course_reg_id' value="{{ $registeredCourse->id }}">
                                                        <input type="hidden" name='student_id' value="{{ $student->id }}">
                                                        <input type="hidden" name='session' value="{{ $studentSession }}">
                                                        <input type="hidden" name='level_id' value="{{  $studentLevelId }}">


                                                        <div class="mb-3">
                                                            <label for="ca_score" class="form-label">CA Score</label>
                                                            <input type="number" class="form-control" name="ca_score" id="ca_score" value="{{ $registeredCourse->ca_score }}">
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="exam_score" class="form-label">Exam Score</label>
                                                            <input type="number" class="form-control" name="exam_score" id="exam_score" value="{{ $registeredCourse->exam_score }}">
                                                        </div>

                                                        {{-- <div class="mb-3">
                                                            <label for="total" class="form-label">Total Score</label>
                                                            <input type="number" class="form-control" name="total" id="total" value="{{ $registeredCourse->total }}">
                                                        </div> --}}
            
                                                        <hr>
                                                        <div class="text-end">
                                                            <button type="submit" id="submit-button" class="btn btn-primary">Save Changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div><!-- /.modal -->

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div><!-- end card body -->
        </div><!-- end card -->
        @endif
    </div>
    <!--end col-->
    
</div>
<!--end row-->

<div id="add" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Add Missing Course</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="{{ url('/admin/result/addStudentCourse') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="url" value="admin.studentResult">
                    <input type="hidden" name='student_id' value="{{ $student->id }}">
                    <input type="hidden" name='session' value="{{ $studentSession }}">
                    <input type="hidden" name='level_id' value="{{  $studentLevelId }}">

                    <div class="mb-3">
                        <label for="course_code" class="form-label">Course Code</label>
                        <input type="text" class="form-control" name="course_code" id="course_code" placeholder="Enter Course Code">
                    </div>

                    <div class="mb-3">
                        <label for="semester">Semester</label>
                        <select class="form-select" id="semester" name="semester" aria-label="semester">
                            <option value="" selected>--Select--</option>
                            <option value="1">First Semester</option>
                            <option value="2">Second Semester</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="ca_score" class="form-label">CA Score</label>
                        <input type="number" class="form-control" name="ca_score" id="ca_score" >
                    </div>

                    <div class="mb-3">
                        <label for="exam_score" class="form-label">Exam Score</label>
                        <input type="number" class="form-control" name="exam_score" id="exam_score">
                    </div>

                    {{-- <div class="mb-3">
                        <label for="total" class="form-label">Total Score</label>
                        <input type="number" class="form-control" name="total" id="total">
                    </div> --}}
                    <hr>
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Add Course </button>
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
                <form action="{{ url('/admin/result/getStudent') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="url" value="admin.studentResult">
                    <input type="hidden" name="type" value="Student">
                    <div class="mb-3">
                        <label for="reg" class="form-label">Application/Matric Number</label>
                        <input type="text" class="form-control" name="reg_number" id="reg">
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
@endsection
@extends('admin.layout.dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Student Results</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Student Results</li>
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
                            <h4 class="mt-4 fw-semibold">Fetch Examination result</h4>
                            <p class="text-muted mt-3"></p>
                            <div class="mt-4">
                                <form action="{{ url('/admin/generateStudentResults') }}" method="POST">
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
                                                <select class="form-select" id="semester" name="semester" aria-label="semester">
                                                    <option value="" selected>--Select--</option>
                                                    <option value="1">First Semester</option>
                                                    <option value="2">Second Semester</option>
                                                </select>
                                                <label for="semester">Semester</label>
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

                                        <button type="submit" id="submit-button" class="btn btn-fill btn-primary btn-lg btn-block mb-5">Get Results</button>
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
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Result(s) for {{ $academiclevel->level }} Level,  {{ !empty($programme)?$programme->name:null }} for {{ $academicSession }} Academic Session</h4>
                <div class="flex-shrink-0">
                    @if(!empty($programme))
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#approveResult">Approve Result(s)</button>
                    @endif
                </div>
            </div><!-- end card header -->

            <div id="approveResult" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body text-center p-5">
                            <div class="text-end">
                                <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="mt-2">
                                <lord-icon src="https://cdn.lordicon.com/xxdqfhbi.json" trigger="hover" style="width:150px;height:150px">
                                </lord-icon>
                                <h4 class="mb-3 mt-4">Are you sure you want to approve result for <br>{{ $academiclevel->level }} level {{ !empty($programme)?$programme->name:null }}?</h4>
                                <form action="{{ url('/admin/approveResult') }}" method="POST">
                                    @csrf
                                    @foreach ($students as $studentforIds)
                                    <input type="hidden" name="student_ids[]" value="{{ $studentforIds->id }}">
                                    @endforeach
                                    @if(!empty($programme))
                                    <input type="hidden" name="level_id" value="{{ $academiclevel->id }}">
                                    <input type="hidden" name="programme_id" value="{{ $programme->id }}">
                                    <input type="hidden" name="department_id" value="{{ $department_id }}">
                                    <input type="hidden" name="faculty_id" value="{{ $faculty_id }}">
                                    <input type="hidden" name="session" value="{{ $academicSession }}">
                                    <input type="hidden" name="semester" value="{{ $semester }}">
                                    @endif
                                    <hr>
                                    <button type="submit" id="submit-button" class="btn btn-success w-100">Yes, Approve</button>
                                </form>
                            </div>
                        </div>
                        <div class="modal-footer bg-light p-3 justify-content-center">

                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->

                <table id="buttons-datatables" class="display table table-bordered table-striped p-3" style="width:100%">
                    <thead>
                        <tr>
                            <th>SN</th>
                            <th>Student Name</th>
                            <th>Matric Number</th>
                            <th>Degree Class</th>
                            <th>Standing</th>
                            <th>No of failed course</th>
                            <th>Total failed unit</th>
                            <th>Failed courses</th>
                            <th>Previous Total Credit Units</th>
                            <th>Previous Total Credit Points</th>
                            <th>Previous CGPA</th>
                            <th class="bg bg-primary text-light">Current Total Credit Units</th>
                            <th class="bg bg-primary text-light">Current Total Credit Points</th>
                            <th class="bg bg-primary text-light">Current GPA</th>
                            <th>Cumulative Total Credit Units</th>
                            <th>Cumulative Total Credit Points</th>
                            <th>Cumulative CGPA</th>
                            <th style="">{{ $semester == 1 ? 'First' : 'Second' }} Semester Courses</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                            @if(!empty($students))
                                @php
                                    $degreeClass = new \App\Models\DegreeClass;

                                    $semesterRegisteredCourses = $student->registeredCourses->where('semester', $semester)->where('level_id', $academiclevel->id)->where('academic_session', $academicSession)->where('grade', '!=', null);
                                    $currentRegisteredCreditUnits =  $semesterRegisteredCourses->sum('course_credit_unit');
                                    $currentRegisteredGradePoints = $semesterRegisteredCourses->sum('points');
                                    $currentGPA = $currentRegisteredGradePoints > 0 ? number_format($currentRegisteredGradePoints / $currentRegisteredCreditUnits, 2) : 0;
                                    $failedSemesterCourses = $semesterRegisteredCourses->where('grade', 'F');

                                    $allRegisteredCourses = $student->registeredCourses->where('grade', '!=', null);
                                    $allRegisteredCreditUnits =  $allRegisteredCourses->sum('course_credit_unit');
                                    $allRegisteredGradePoints = $allRegisteredCourses->sum('points');
                                    $CGPA = $allRegisteredGradePoints > 0 ? number_format($allRegisteredGradePoints / $allRegisteredCreditUnits, 2) : 0;

                                    $prevRegisteredCourses = $student->registeredCourses->where('semester', '!=', $semester)->where('level_id', '!=', $academiclevel->id);
                                    $prevRegisteredCreditUnits =  $prevRegisteredCourses->sum('course_credit_unit');
                                    $prevRegisteredGradePoints = $prevRegisteredCourses->sum('points');
                                    if ($prevRegisteredCreditUnits != 0) {
                                        $prevCGPA = number_format($prevRegisteredGradePoints / $prevRegisteredCreditUnits, 2);
                                    } else {
                                        $prevCGPA = 0.00; // Set a default value or handle the situation accordingly
                                    }

                                    $classGrade = $degreeClass->computeClass($CGPA);
                                    $class = $classGrade->degree_class;
                                    $standing = $classGrade->id > 3? 'NGS' : 'GS'; 
                                    
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ strtoupper($student->applicant->lastname).', '.$student->applicant->othernames }}</td>
                                    <td>{{ $student->matric_number }}</td>
                                    <td>{{$class}}</td>
                                    <td>{{ $standing }}</td>
                                    <td class="text-danger">{{ $failedSemesterCourses->count() }}</td>
                                    <td class="text-danger">{{ $failedSemesterCourses->sum('course_credit_unit') }}</td>
                                    <td>
                                        @if($failedSemesterCourses->count() > 0)
                                            <span class="text-danger">
                                                @foreach($failedSemesterCourses as $failedSemesterCourse)
                                                    {{ $failedSemesterCourse->course_code }}
                                                @endforeach
                                            </span>
                                        @endif    
                                    </td>
                                    <td>{{ $prevRegisteredCreditUnits }}</td>
                                    <td>{{ $prevRegisteredGradePoints }}</td>
                                    <td>{{ $prevCGPA }}</td>
                                    <td class="bg bg-soft-primary">{{ $currentRegisteredCreditUnits }}</td>
                                    <td class="bg bg-soft-primary">{{ $currentRegisteredGradePoints }}</td>
                                    <td class="bg bg-soft-primary">{{ $currentGPA }}</td>
                                    <td>{{ $allRegisteredCreditUnits }}</td>
                                    <td>{{ $allRegisteredGradePoints }}</td>
                                    <td>{{ $CGPA }}</td>
                                    <td width="200px">
                                        <div class="accordion" id="default-accordion-example">
                                            <div class="accordion-item shadow">
                                                <h2 class="accordion-header" id="headingTwo">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#studentCourses{{ $student->id  }}" aria-expanded="false" aria-controls="studentCourses">
                                                        View Courses
                                                    </button>
                                                </h2>
                                                <div id="studentCourses{{ $student->id  }}" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#default-accordion-example">
                                                    <div class="accordion-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered table-striped">
                                                                <thead>
                                                                <tr>
                                                                    <th>SN</th>
                                                                    <th>Code</th>
                                                                    <th>Course Title</th>
                                                                    <th>Unit</th>
                                                                    <th>Total Score</th>
                                                                    <th>Grade</th>
                                                                    <th>Point</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($semesterRegisteredCourses as $registeredCourse)
                                                                        <tr>
                                                                            <td>{{ $loop->iteration }}</td>
                                                                            <td>{{ $registeredCourse->course->code }}</td>
                                                                            <td>{{ $registeredCourse->course->name }}</td>
                                                                            <td>{{ $registeredCourse->course_credit_unit }}</td>
                                                                            <td>{{ $registeredCourse->total }}</td>
                                                                            <td>{{ $registeredCourse->grade }}</td>
                                                                            <td>{{ $registeredCourse->points }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
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

{{-- <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
    <thead>
        <tr>
            <th>S/N</th>
            <th>Student Name</th>
            <th>Matric Number</th>
            <th>No of failed course</th>
            <th>Total failed unit</th>
            <th>Failed courses</th>
            <th>Previous CGPA</th>
            <th>Current GPA</th>
            <th>Cumulative CGPA</th>
            <th>Class</th>
            <th>Level</th>
            <th colspan="6">{{ $semester == 1 ? 'First' : 'Second' }} Semester Courses</th>
            
        </tr>
    </thead>
    <tbody>
        @foreach($students as $student)
        @php
            $degreeClass = new \App\Models\DegreeClass;

            $semesterRegisteredCourses = $student->registeredCourses->where('semester', $semester)->where('level_id', $academiclevel->id)->where('academic_session', $academicSession);
            $currentRegisteredCreditUnits =  $semesterRegisteredCourses->sum('course_credit_unit');
            $currentRegisteredGradePoints = $semesterRegisteredCourses->sum('points');
            $currentGPA = number_format($currentRegisteredGradePoints / $currentRegisteredCreditUnits, 2);
            $failedSemesterCourses = $semesterRegisteredCourses->where('grade', 'F');

            $allRegisteredCourses = $student->registeredCourses->where('grade', '!=', null);
            $allRegisteredCreditUnits =  $allRegisteredCourses->sum('course_credit_unit');
            $allRegisteredGradePoints = $allRegisteredCourses->sum('points');
            $CGPA = number_format($allRegisteredGradePoints / $allRegisteredCreditUnits, 2);

            $prevRegisteredCourses = $student->registeredCourses->where('semester', '!=', $semester)->where('level_id', '!=', $academiclevel->id);
            $prevRegisteredCreditUnits =  $prevRegisteredCourses->sum('course_credit_units');
            $prevRegisteredGradePoints = $prevRegisteredCourses->sum('points');
            if ($prevRegisteredCreditUnits != 0) {
                $prevCGPA = number_format($prevRegisteredGradePoints / $prevRegisteredCreditUnits, 2);
            } else {
                $prevCGPA = 0.00; // Set a default value or handle the situation accordingly
            }

            $classGrade = $degreeClass->computeClass($CGPA);
            $class = $classGrade->degree_class;
            $standing = $classGrade->id > 3? 'NGS' : 'GS'; 
            
        @endphp
        <tr style="text-align: center; vertical-align: bottom;">
            <td >{{ $loop->iteration }}</td>
            <td>{{ $student->applicant->lastname.' '.$student->applicant->othernames }}</td>
            <td>{{ $student->matric_number }}</td>
            <tdclass="text-danger">{{ $failedSemesterCourses->count() }}</tdclass=>
            <td class="text-danger">{{ $failedSemesterCourses->sum('course_credit_unit') }}</td>
            <td>
                @if($failedSemesterCourses->count() > 0)
                    <span class="text-danger">
                        @foreach($failedSemesterCourses as $failedSemesterCourse)
                            {{ $failedSemesterCourse->course_code }} 
                        @endforeach
                    </span>
                @endif    
            </td>
            <td>{{ $prevRegisteredCreditUnits }}</td>
            <td>{{ $prevRegisteredGradePoints }}</td>
            <td>{{ $prevRegisteredGradePoints }}</td>
            <td>{{ $currentRegisteredCreditUnits }}</td>
            <td>{{ $currentRegisteredGradePoints }}</td>
            <td>{{ $currentGPA }}</td>
            <td>{{ $allRegisteredCreditUnits }}</td>
            <td>{{ $allRegisteredGradePoints }}</td>
            <td>{{ $CGPA }}</td>
            <td>{{$class}}</td>
            <td>{{ $standing }}</td>
            <td>Course Code</td>
            <td>Credit Unit</td>
            <td>Course Type</td>
            <td>Course Code</td>
            <td>Credit Unit</td>
            <td>Course Type</td>
        </tr>
        <tr>
            <td>Bio 101</td>
            <td>3</td>
            <td>R</td>
            <td>Bio 101</td>
            <td>3</td>
            <td>R</td>
        </tr>
        <tr>
            <td>70</td>
            <td>A</td>
            <td>5</td>
            <td>Bio 101</td>
            <td>3</td>
            <td>R</td>
        </tr>
        @endforeach
    </tbody>
</table> --}}
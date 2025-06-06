@extends('admin.layout.dashboard')

@section('content')
 <!-- start page title -->
 <div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Graduating Students</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Graduating Students</li>
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
                <h4 class="card-title mb-0 flex-grow-1">{{ $programmeCategory->category }} Programme Graduating Students for {{ $programmeCategory->academicSessionSetting->academic_session }} Academic Session</h4>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#confirmModal">Submit Selected Students</button>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <form action="{{ url('admin/graduateStudents') }}" method="POST" id="submitStudentsForm">
                    @csrf
                    <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th scope="col">Select</th>
                                <th scope="col">Id</th>
                                <th scope="col">CGPA</th>
                                <th scope="col">Class</th>
                                <th scope="col">Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">Level</th>
                                <th scope="col">Matric Number</th>
                                <th scope="col">Total Credit Unit</th>
                                <th scope="col">Total Point</th>
                                <th scope="col">Failed Courses</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($classifiedStudents as $faculty => $departments)
                                <tr>
                                    <td colspan="12" class="table-primary"><strong>Faculty: {{ $faculty }}</strong></td>
                                </tr>
                                @foreach($departments as $department => $programs)
                                    <tr>
                                        <td colspan="12" class="table-secondary"><strong>Department: {{ $department }}</strong></td>
                                    </tr>
                                    @foreach($programs as $program => $levels)
                                        <tr>
                                            <td colspan="12" class="table-success"><strong>Program: {{ $program }}</strong></td>
                                        </tr>
                                        @foreach($levels as $level => $students)
                                            <tr>
                                                <td colspan="12" class="table-info"><strong>Level: {{ $level }}</strong></td>
                                            </tr>
                                            @foreach($students as $student)
                                                @php
                                                    $degreeClass = new \App\Models\DegreeClass;
                                                    $overallCreditUnits = 0;
                                                    $overallGradePoints = 0;

                                                    $registeredCourses = $student->registeredCourses;
                                                    $allRegisteredCourses = $student->registeredCourses->where('grade', '!=', null);
                                                    $allRegisteredCreditUnits = $allRegisteredCourses->sum('course_credit_unit');
                                                    $allRegisteredGradePoints = $allRegisteredCourses->sum('points');
                                                    $CGPA = $allRegisteredGradePoints > 0 ? number_format($allRegisteredGradePoints / $allRegisteredCreditUnits, 2) : 0.00;

                                                    $failedCourses = $student->registeredCourses->where('grade', 'F')->where('re_reg', null);

                                                    $classGrade = $degreeClass->computeClass($CGPA);
                                                    $class = $classGrade->degree_class;
                                                    
                                                @endphp
                                                <tr>
                                                    <td><input type="checkbox" name="selected_students[]" value="{{ $student->id }}"></td>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td><span class="text-primary">{{ $student->cgpa }}</span></td>
                                                    <td>{{ $class }}</td>
                                                    <td>{{ $student->applicant->lastname .' '. $student->applicant->othernames }}</td>
                                                    <td>{{ $student->email }} </td>
                                                    <td>{{ $student->academicLevel->level }} </td>
                                                    <td>{{ $student->matric_number }}</td>
                                                    <td>{{ $allRegisteredCreditUnits }}</td>
                                                    <td>{{ $allRegisteredGradePoints }}</td>
                                                    <td>
                                                        @if($failedCourses)
                                                            @foreach($failedCourses as $failedCourse)
                                                                <ul>
                                                                    <li>
                                                                        <span class="text-danger"> ({{ $failedCourse->id }}) - {{ $failedCourse->course_code }} - {{ ucwords(strtolower($failedCourse->course->name)) }} ({{ $failedCourse->course_credit_unit }} unit)</span>
                                                                    </li>
                                                                </ul>
                                                            @endforeach
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ url('admin/studentProfile/'.$student->slug) }}" class="btn btn-primary m-1"><i class="ri-user-6-fill"></i> View Student</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </form>
                      
            </div>
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end row -->

<!-- Confirm Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Confirm Submission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to submit the selected students as graduated successfully?</p>
                <ul id="selectedStudentsList"></ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="submitStudentsForm" class="btn btn-success">Submit</button>
            </div>
        </div>
    </div>
</div>
<!-- End Confirm Modal -->

@endsection

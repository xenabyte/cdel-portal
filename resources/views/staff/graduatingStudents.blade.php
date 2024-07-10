@extends('staff.layout.dashboard')

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
                <h4 class="card-title mb-0 flex-grow-1">Graduating Students </h4>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                    <thead>
                        <tr>
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
                                <td colspan="11" class="table-primary"><strong>Faculty: {{ $faculty }}</strong></td>
                            </tr>
                            @foreach($departments as $department => $programs)
                                <tr>
                                    <td colspan="11" class="table-secondary"><strong>Department: {{ $department }}</strong></td>
                                </tr>
                                @foreach($programs as $program => $levels)
                                    <tr>
                                        <td colspan="11" class="table-success"><strong>Program: {{ $program }}</strong></td>
                                    </tr>
                                    @foreach($levels as $level => $students)
                                        <tr>
                                            <td colspan="11" class="table-info"><strong>Level: {{ $level }}</strong></td>
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
                                                $CGPA = $allRegisteredGradePoints > 0 ? number_format($allRegisteredGradePoints / $allRegisteredCreditUnits, 2) : 0;

                                                $failedCourses = $student->registeredCourses->where('grade', 'F')->where('re_reg', null);

                                                $classGrade = $degreeClass->computeClass($CGPA);
                                                $class = $classGrade->degree_class;
                                                
                                            @endphp
                                            <tr>
                                                <th scope="row">{{ $loop->iteration }}</th>
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
                                                                    <span class="text-danger"> {{ $failedCourse->id }} - {{ $failedCourse->course_code }} - {{ $failedCourse->course->name }} ({{ $failedCourse->course_credit_unit }} unit)</span>
                                                                </li>
                                                            </ul>
                                                        @endforeach
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ url('staff/studentProfile/'.$student->slug) }}" class="btn btn-primary m-1"><i class="ri-user-6-fill"></i> View Student</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                @endforeach
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
                      
            </div>
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end row -
@endsection

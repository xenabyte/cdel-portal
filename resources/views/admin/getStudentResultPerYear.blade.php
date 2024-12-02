@extends('admin.layout.dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Student Results Per Year</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Student Results Per Year</li>
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
                            <h4 class="mt-4 fw-semibold">Fetch Records</h4>
                            <p class="text-muted mt-3"></p>
                            <div class="mt-4">
                                <form action="{{ url('/admin/studentResultPerYear') }}" method="POST">
                                    @csrf
                                    <div class="row g-3">

                                        <div class="col-lg-12">
                                            <div class="form-floating">
                                                <select class="form-select" id="faculty" name="faculty_id" aria-label="faculty" onchange="handleFacultyChange(event)">
                                                    <option value="" selected>--Select--</option>
                                                    <option value="">All</option>
                                                    @foreach($faculties as $faculty)
                                                        <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                                                    @endforeach
                                                </select>
                                                <label for="faculty">Faculty</label>
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
                                                <label for="level">Present Academic Level</label>
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
                <h4 class="card-title mb-0 flex-grow-1">Summary of Result(s) for {{ $academicSession }} Academic Session</h4>
            </div><!-- end card header -->
        </div>

        </div>
    </div>


    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Result(s) for {{ $academicSession }} Academic Session</h4>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table id="buttons-result" class="display table table-bordered table-striped p-3" style="width:100%">
                    <thead>
                        <tr>
                            <th>SN</th>
                            <th>Student Name</th>
                            <th>Student Level</th>
                            <th>Matric Number</th>
                            <th>Degree Class</th>
                            <th>Standing</th>
                            <th>Programme</th>
                            <th class="bg bg-primary text-light">Current Total Credit Units</th>
                            <th class="bg bg-primary text-light">Current Total Credit Points</th>
                            <th class="bg bg-primary text-light">Current GPA</th>
                            <th class="bg bg-dark text-light">Cumulative CGPA</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $degreeClassModel = new \App\Models\DegreeClass;
                        @endphp
                        @foreach($students as $student)
                            @php
                                // Calculate current GPA and other metrics
                                $yearRegisteredCourses = $student->registeredCourses
                                    ->where('academic_session', $academicSession)
                                    ->where('grade', '!=', null);
            
                                $currentRegisteredCreditUnits = $yearRegisteredCourses->sum('course_credit_unit');
                                $currentRegisteredGradePoints = $yearRegisteredCourses->sum('points');
                                $currentGPA = $currentRegisteredCreditUnits > 0
                                    ? floor($currentRegisteredGradePoints / $currentRegisteredCreditUnits * 100) / 100
                                    : 0;
            
                                // Determine degree class and standing
                                $classGrade = $degreeClassModel->computeClass($student->cgpa ?? 0);
                                $class = $classGrade->degree_class ?? 'N/A';
                                $standing = ($classGrade->id ?? 0) > 4 ? 'NGS' : 'GS'; 
                            @endphp
                            @if($currentGPA > 0)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ strtoupper($student->applicant->lastname ?? 'N/A').', '. ucwords(strtolower($student->applicant->othernames ?? 'N/A')) }}</td>
                                <td>{{ $student->academicLevel->level ?? 'N/A' }}</td>
                                <td>{{ $student->matric_number ?? 'N/A' }}</td>
                                <td>{{ $class }}</td>
                                <td>{{ $standing }}</td>
                                <td>{{ $student->programme->name ?? 'N/A' }}</td>
                                <td class="bg bg-soft-primary">{{ $currentRegisteredCreditUnits }}</td>
                                <td class="bg bg-soft-primary">{{ $currentRegisteredGradePoints }}</td>
                                <td class="bg bg-soft-primary">{{ $currentGPA }}</td>
                                <td class="bg bg-soft-dark">{{ $student->cgpa ?? '0.00' }}</td>
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
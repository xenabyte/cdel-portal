@extends('admin.layout.dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Student Result Summary</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Student Result Summary</li>
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
                                <form action="{{ url('/admin/generateStudentResultSummary') }}" method="POST">
                                    @csrf
                                    <div class="row g-3">

                                        <div class="col-lg-12">
                                            <div class="form-floating">
                                                <select class="form-select" id="faculty" name="faculty_id" aria-label="faculty">
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
                                                <select class="form-select" id="semester" name="semester" aria-label="semester">
                                                    <option value="" selected>--Select--</option>
                                                    <option value="1">Harmattan Semester</option>
                                                    <option value="2">Rain Semester</option>
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
                                                <select class="form-select" id="batch" name="batch" aria-label="Batch">
                                                    <option value="" selected>--Select--</option>
                                                    <option value="A">Batch A</option>
                                                    <option value="B">Batch B</option>
                                                    <option value="C">Batch C</option>
                                                </select>
                                                <label for="batch">Batch</label>
                                            </div>
                                        </div>

                                        <div class="col-lg-12">
                                            <div class="form-floating">
                                                <select class="form-select" id="programme_category" name="programme_category_id" aria-label="Programme Category">
                                                    <option value="" selected>--Select--</option>
                                                    @foreach($programmeCategories as $programmeCategory)<option value="{{ $programmeCategory->id }}">{{ $programmeCategory->category }} Programme</option>@endforeach
                                                </select>
                                                <label for="session">Programme Category</label>
                                            </div>
                                        </div>

                                        <button type="submit" id="submit-button" class="btn btn-fill btn-primary btn-lg btn-block mb-5">Get Summary</button>
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
                <h4 class="card-title mb-0 flex-grow-1">{{ $semester==1? 'Harmattan' : 'Rain'}} Semester Result Summary for  {{ !empty($faculty)?$faculty->name:null }} for {{ $academicSession }} Academic Session</h4>
                <div class="flex-shrink-0">
                    @if(!empty($faculty))
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
                                <h4 class="mb-3 mt-4">Are you sure you want to approve result for <br>{{ !empty($faculty)?$faculty->name:null }}?</h4>
                                <form action="{{ url('/admin/approveResult') }}" method="POST">
                                    @csrf
                                    @foreach ($students as $studentforIds)
                                    <input type="hidden" name="student_ids[]" value="{{ $studentforIds->id }}">
                                    @endforeach
                                    @if(!empty($faculty))
                                    <input type="hidden" name="faculty_id" value="{{ $faculty->id }}">
                                    <input type="hidden" name="session" value="{{ $academicSession }}">
                                    <input type="hidden" name="semester" value="{{ $semester }}">
                                    @endif
                                    <input type="hidden" name="url" value="admin.getStudentResultSummary">
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
                @foreach($academicLevels as $academicLevel)
                    @if($academicLevel->id <= count($classifiedStudents))
                    <div class="card-header align-items-center d-flex mb-3">
                        <h4 class="card-title mb-0 flex-grow-1">Summary of Result for {{ $academicLevel->level }} Level</h4>
                    </div><!-- end card header -->
                    <table id="buttons-datatables{{ $loop->iteration }}" class="display table table-bordered table-striped p-3 mt-3" style="width:100%">
                        <thead>
                            <tr>
                                <th>SN</th>
                                <th>Programmes</th>
                                <th>Level</th>
                                <th>Number of Students</th>
                                <th class="bg bg-success text-light">Number of Students in Good Standing</th>
                                <th class="bg bg-warning text-light">Number of Students Not in Good Standing</th>
                                <th class="bg bg-primary text-light">Number of Students with First Class</th>
                                <th class="bg bg-secondary text-light">Number of Students with Second Class Upper</th>
                                <th class="bg bg-dark text-light">Number of Students with Second Class Lower</th>
                                <th class="bg bg-info text-light">Number of Students with Third Class</th>
                                <th class="bg bg-warning text-light">Number of Students with Pass</th>
                                <th class="bg bg-danger text-light">Number of Students with Fail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($classifiedStudents[$academicLevel->level]))
                                @foreach($classifiedStudents[$academicLevel->level] as $programName => $students)
                                    @php
                                        $totalStudents = count($students);
                                        $goodStandingCount = 0;
                                        $notInGoodStandingCount = 0;
                    
                                        $totalStudentsWithNullGrades = 0;
                    
                                        $semesterGoodStandingCount = 0;
                                        $semesterNotInGoodStandingCount =0;
                    
                                        $degreeClassCounts = (object) [
                                            'First Class' => 0,
                                            'Second Class Upper' => 0,
                                            'Second Class Lower' => 0,
                                            'Third Class' => 0,
                                            'Pass' => 0,
                                            'Fail' => 0,
                                        ];
                    
                                        $degreeClass = new \App\Models\DegreeClass;
                    
                                        foreach($students as $student){
                                            $semesterRegisteredCourses = $student->registeredCourses
                                                ->where('semester', $semester)
                                                ->where('level_id', $academicLevel->id)
                                                ->where('academic_session', $academicSession);
                    
                                            $nullGradeCount = $semesterRegisteredCourses->where('grade', null)->count();
                    
                                            $totalCoursesCount = $semesterRegisteredCourses->count();
                                            $eightyPercent = 0.8 * $totalCoursesCount;
                    
                                            if ($nullGradeCount >= $eightyPercent) {
                                                $totalStudentsWithNullGrades++;
                                            }
                    
                                            $semesterRegisteredCourses = $student->registeredCourses->where('semester', $semester)->where('level_id', $academicLevel->id)->where('academic_session', $academicSession)->where('grade', '!=', null);
                                            $currentRegisteredCreditUnits =  $semesterRegisteredCourses->sum('course_credit_unit');
                                            $currentRegisteredGradePoints = $semesterRegisteredCourses->sum('points');
                                            $currentGPA = $currentRegisteredGradePoints > 0 ? number_format($currentRegisteredGradePoints / $currentRegisteredCreditUnits, 2) : 0.00;
                    
                                            $allRegisteredCourses = $student->registeredCourses->where('grade', '!=', null);
                                            $allRegisteredCreditUnits =  $allRegisteredCourses->sum('course_credit_unit');
                                            $allRegisteredGradePoints = $allRegisteredCourses->sum('points');
                                            $CGPA = $allRegisteredGradePoints > 0 ? number_format($allRegisteredGradePoints / $allRegisteredCreditUnits, 2) : 0.00;
                    
                                            $semesterClassGrade = $degreeClass->computeClass($currentGPA);
                                            $semesterClass = $semesterClassGrade->degree_class;
                                            $semesterStanding = $semesterClassGrade->id > 4? 'NGS' : 'GS'; 
                    
                                            if ($semesterStanding === 'GS') {
                                                $semesterGoodStandingCount++;
                                            } else {
                                                $semesterNotInGoodStandingCount++;
                                            }
                    
                                            $classGrade = $degreeClass->computeClass($CGPA);
                                            $class = $classGrade->degree_class;
                                            $standing = $classGrade->id > 4? 'NGS' : 'GS'; 
                    
                                            if ($standing === 'GS') {
                                                $goodStandingCount++;
                                            } else {
                                                $notInGoodStandingCount++;
                                            }
                    
                                            switch ($class) {
                                                case 'First Class':
                                                    $degreeClassCounts->{'First Class'}++;
                                                    break;
                                                case 'Second Class Upper':
                                                    $degreeClassCounts->{'Second Class Upper'}++;
                                                    break;
                                                case 'Second Class Lower':
                                                    $degreeClassCounts->{'Second Class Lower'}++;
                                                    break;
                                                case 'Third Class':
                                                    $degreeClassCounts->{'Third Class'}++;
                                                    break;
                                                case 'Pass':
                                                    $degreeClassCounts->{'Pass'}++;
                                                    break;
                                                default:
                                                    $degreeClassCounts->{'Fail'}++;
                                                    break;
                                            }
                                        }
                    
                                        $goodStandingPercentage = $totalStudents > 0 ? ($goodStandingCount / $totalStudents) * 100 : 0;
                                        $notInGoodStandingPercentage = $totalStudents > 0 ? ($notInGoodStandingCount / $totalStudents) * 100 : 0;
                    
                                        $semesterGoodStandingPercentage = $totalStudents > 0 ? ($semesterGoodStandingCount / $totalStudents) * 100 : 0;
                                        $semesterNotInGoodStandingPercentage = $totalStudents > 0 ? ($semesterNotInGoodStandingCount / $totalStudents) * 100 : 0;
                                        
                                    @endphp
                                    
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $programName }}</td>
                                        <td>{{ $academicLevel->level }}</td>
                                        <td>{{ $totalStudents }}</td>
                                        <td class="bg bg-soft-success">{{ $semesterGoodStandingCount }}</td>
                                        <td class="bg bg-soft-warning">{{ $semesterNotInGoodStandingCount }}</td>
                                        <td class="bg bg-soft-primary"> @foreach($degreeClassCounts as $degreeClass => $count) @if($degreeClass == 'First Class') {{ $count }}  @endif @endforeach</td>
                                        <td class="bg bg-soft-secondary"> @foreach($degreeClassCounts as $degreeClass => $count) @if($degreeClass == 'Second Class Upper') {{ $count }}  @endif @endforeach</td>
                                        <td class="bg bg-soft-dark"> @foreach($degreeClassCounts as $degreeClass => $count) @if($degreeClass == 'Second Class Lower') {{ $count }}  @endif @endforeach</td>
                                        <td class="bg bg-soft-info"> @foreach($degreeClassCounts as $degreeClass => $count) @if($degreeClass == 'Third Class') {{ $count }}  @endif @endforeach</td>
                                        <td class="bg bg-soft-warning"> @foreach($degreeClassCounts as $degreeClass => $count) @if($degreeClass == 'Pass') {{ $count }}  @endif @endforeach</td>
                                        <td class="bg bg-soft-danger"> @foreach($degreeClassCounts as $degreeClass => $count) @if($degreeClass == 'Fail') {{ $count }}  @endif @endforeach</td>

                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                    @endif
                    <br>
                @endforeach
            </div>
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end row -->
@endif
@endsection
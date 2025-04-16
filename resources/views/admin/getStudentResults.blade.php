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
                                                <select class="form-select" id="programme_category" name="programme_category_id" aria-label="Programme Category">
                                                    <option value="" selected>--Select--</option>
                                                    @foreach($programmeCategories as $programmeCategory)<option value="{{ $programmeCategory->id }}">{{ $programmeCategory->category }} Programme</option>@endforeach
                                                </select>
                                                <label for="session">Programme Category</label>
                                            </div>
                                        </div>

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
                                                <select class="form-select" id="batch" name="batch" aria-label="Batch">
                                                    <option value="" selected>--Select--</option>
                                                    <option value="A">Batch A</option>
                                                    <option value="B">Batch B</option>
                                                    <option value="C">Batch C</option>
                                                </select>
                                                <label for="batch">Batch</label>
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
            ->where('level_id', $academiclevel->id)
            ->where('academic_session', $academicSession);

        $nullGradeCount = $semesterRegisteredCourses->where('grade', null)->count();

        $totalCoursesCount = $semesterRegisteredCourses->count();
        $eightyPercent = 0.8 * $totalCoursesCount;

        if ($nullGradeCount >= $eightyPercent) {
            $totalStudentsWithNullGrades++;
        }

        $semesterRegisteredCourses = $student->registeredCourses->where('semester', $semester)->where('level_id', $academiclevel->id)->where('academic_session', $academicSession)->where('grade', '!=', null);
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
<div class="row">

    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Summary of Result(s) for {{ $academiclevel->level }} Level,  {{ !empty($programme)?$programme->name:null }} for {{ $academicSession }} Academic Session</h4>
            </div><!-- end card header -->
        </div>

        <div class="row">
            <div class="col-xl-3">
                <div class="card card-height-100">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Summary by Standing - (This Semester)</h4>
                    </div><!-- end card header -->
                    <div class="card-body">

                        <div class="table-responsive mt-3">
                            <table class="table table-borderless table-sm table-centered align-middle table-nowrap mb-0">
                                <tbody class="border-0">
                                    <tr>
                                        <td>
                                            <h4 class="text-truncate fs-14 fs-medium mb-0"><i class="ri-stop-fill align-middle fs-18 text-primary me-2"></i>Total Students</h4>
                                        </td>
                                        <td>
                                            <p class="text-muted mb-0"><i data-feather="users" class="me-2 icon-sm"></i>{{ $totalStudents }}</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <h4 class="text-truncate fs-14 fs-medium mb-0"><i class="ri-stop-fill align-middle fs-18 text-warning me-2"></i>Total Students with Batch B/C</h4>
                                        </td>
                                        <td>
                                            <p class="text-muted mb-0"><i data-feather="users" class="me-2 icon-sm"></i>{{ $totalStudentsWithNullGrades }}</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <h4 class="text-truncate fs-14 fs-medium mb-0"><i class="ri-stop-fill align-middle fs-18 text-success me-2"></i>Good Standing (GS)</h4>
                                        </td>
                                        <td>
                                            <p class="text-muted mb-0"><i data-feather="users" class="me-2 icon-sm"></i>{{ $semesterGoodStandingCount }}</p>
                                        </td>
                                        <td class="text-end">
                                            <p class="text-success fw-medium fs-12 mb-0"><i class="ri-arrow-up-s-fill fs-5 align-middle"></i>{{ number_format($semesterGoodStandingPercentage, 2) }}%</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <h4 class="text-truncate fs-14 fs-medium mb-0"><i class="ri-stop-fill align-middle fs-18 text-danger me-2"></i>Not in Good Standing (NGS)</h4>
                                        </td>
                                        <td>
                                            <p class="text-muted mb-0"><i data-feather="users" class="me-2 icon-sm"></i>{{ $semesterNotInGoodStandingCount }}</p>
                                        </td>
                                        <td class="text-end">
                                            <p class="text-danger fw-medium fs-12 mb-0"><i class="ri-arrow-down-s-fill fs-5 align-middle"></i>{{ number_format($semesterNotInGoodStandingPercentage, 2) }}%</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end col -->

            <div class="col-xl-4">
                <div class="card card-height-100">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Summary by Standing - (Overall)</h4>
                    </div><!-- end card header -->
                    <div class="card-body">

                        <div class="table-responsive mt-3">
                            <table class="table table-borderless table-sm table-centered align-middle table-nowrap mb-0">
                                <tbody class="border-0">
                                    <tr>
                                        <td>
                                            <h4 class="text-truncate fs-14 fs-medium mb-0"><i class="ri-stop-fill align-middle fs-18 text-primary me-2"></i>Total Students</h4>
                                        </td>
                                        <td>
                                            <p class="text-muted mb-0"><i data-feather="users" class="me-2 icon-sm"></i>{{ $totalStudents }}</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <h4 class="text-truncate fs-14 fs-medium mb-0"><i class="ri-stop-fill align-middle fs-18 text-success me-2"></i>Good Standing (GS)</h4>
                                        </td>
                                        <td>
                                            <p class="text-muted mb-0"><i data-feather="users" class="me-2 icon-sm"></i>{{ $goodStandingCount }}</p>
                                        </td>
                                        <td class="text-end">
                                            <p class="text-success fw-medium fs-12 mb-0"><i class="ri-arrow-up-s-fill fs-5 align-middle"></i>{{ number_format($goodStandingPercentage, 2) }}%</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <h4 class="text-truncate fs-14 fs-medium mb-0"><i class="ri-stop-fill align-middle fs-18 text-danger me-2"></i>Not in Good Standing (NGS)</h4>
                                        </td>
                                        <td>
                                            <p class="text-muted mb-0"><i data-feather="users" class="me-2 icon-sm"></i>{{ $notInGoodStandingCount }}</p>
                                        </td>
                                        <td class="text-end">
                                            <p class="text-danger fw-medium fs-12 mb-0"><i class="ri-arrow-down-s-fill fs-5 align-middle"></i>{{ number_format($notInGoodStandingPercentage, 2) }}%</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end col -->

            <div class="col-xl-5">
                <div class="card card-height-100">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Summary by Class</h4>
                    </div>

                    <div class="card-body">

                        <div class="row align-items-center">
                            <div class="col-6">
                                <h6 class="text-muted text-uppercase fw-semibold text-truncate fs-12 mb-3">
                                    Total Students</h6>
                                <h4 class="fs- mb-0">{{ $totalStudents }}</h4>
                                <p class="mb-0 mt-2 text-muted"><span class="badge bg-success-subtle text-success mb-0"></p>
                            </div><!-- end col -->
                            <div class="col-6">
                                <div class="text-center">
                                    <img src="{{ asset('assets/images/user-illustarator-2.png') }}" class="img-fluid" alt="">
                                </div>
                            </div><!-- end col -->
                        </div><!-- end row -->
                        
                        <div class="mt-3 pt-2">
                            <div class="progress progress-lg rounded-pill">
                                @foreach($degreeClassCounts as $degreeClass => $count)
                                @php
                                    $percentage = $totalStudents > 0 ? number_format(($count / $totalStudents) * 100, 2) : 0;
                                @endphp
                                    <div class="progress-bar @switch($degreeClass)
                                            @case('First Class')
                                                bg-primary
                                                @break
                                            @case('Second Class Upper')
                                                bg-secondary
                                                @break
                                            @case('Second Class Lower')
                                                bg-success
                                                @break
                                            @case('Third Class')
                                                bg-info
                                                @break
                                            @case('Pass')
                                                bg-warning
                                                @break
                                            @default
                                                bg-danger
                                        @endswitch"
                                        role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                @endforeach
                            </div>
                        </div><!-- end -->

                        <div class="mt-3 pt-2">
                            @foreach($degreeClassCounts as $degreeClass => $count)
                                <div class="d-flex mb-2">
                                    <div class="flex-grow-1">
                                        <p class="text-truncate text-muted fs-14 mb-0"><i class="mdi mdi-circle align-middle  @switch($degreeClass)
                                            @case('First Class')
                                                text-primary
                                                @break
                                            @case('Second Class Upper')
                                                text-secondary
                                                @break
                                            @case('Second Class Lower')
                                                text-success
                                                @break
                                            @case('Third Class')
                                                text-info
                                                @break
                                            @case('Pass')
                                                text-warning
                                                @break
                                            @default
                                                text-danger
                                            @endswitch
                                            
                                            me-2"></i>{{ $degreeClass }}: {{ $count }}</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        @php
                                            $percentage = $totalStudents > 0 ? number_format(($count / $totalStudents) * 100, 2):0;
                                        @endphp
                                        <p class="mb-0">{{ number_format($percentage, 2) }}%</p>
                                    </div>
                                </div><!-- end -->
                            @endforeach

                        </div><!-- end -->

                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end col -->

            {{-- <div class="col-xl-3">
                <div class="card card-height-100">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Top Pages</h4>
                        <div class="flex-shrink-0">
                            <div class="dropdown card-header-dropdown">
                                <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="text-muted fs-16"><i class="mdi mdi-dots-vertical align-middle"></i></span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="#">Today</a>
                                    <a class="dropdown-item" href="#">Last Week</a>
                                    <a class="dropdown-item" href="#">Last Month</a>
                                    <a class="dropdown-item" href="#">Current Year</a>
                                </div>
                            </div>
                        </div>
                    </div><!-- end card header -->
                    <div class="card-body">
                        <div class="table-responsive table-card">
                            <table class="table align-middle table-borderless table-centered table-nowrap mb-0">
                                <thead class="text-muted table-light">
                                    <tr>
                                        <th scope="col" style="width: 62;">Active Page</th>
                                        <th scope="col">Active</th>
                                        <th scope="col">Users</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <a href="javascript:void(0);">/themesbrand/skote-25867</a>
                                        </td>
                                        <td>99</td>
                                        <td>25.3%</td>
                                    </tr><!-- end -->
                                    <tr>
                                        <td>
                                            <a href="javascript:void(0);">/dashonic/chat-24518</a>
                                        </td>
                                        <td>86</td>
                                        <td>22.7%</td>
                                    </tr><!-- end -->
                                    <tr>
                                        <td>
                                            <a href="javascript:void(0);">/skote/timeline-27391</a>
                                        </td>
                                        <td>64</td>
                                        <td>18.7%</td>
                                    </tr><!-- end -->
                                    <tr>
                                        <td>
                                            <a href="javascript:void(0);">/themesbrand/minia-26441</a>
                                        </td>
                                        <td>53</td>
                                        <td>14.2%</td>
                                    </tr><!-- end -->
                                    <tr>
                                        <td>
                                            <a href="javascript:void(0);">/dashon/dashboard-29873</a>
                                        </td>
                                        <td>33</td>
                                        <td>12.6%</td>
                                    </tr><!-- end -->
                                    <tr>
                                        <td>
                                            <a href="javascript:void(0);">/doot/chats-29964</a>
                                        </td>
                                        <td>20</td>
                                        <td>10.9%</td>
                                    </tr><!-- end -->
                                    <tr>
                                        <td>
                                            <a href="javascript:void(0);">/minton/pages-29739</a>
                                        </td>
                                        <td>10</td>
                                        <td>07.3%</td>
                                    </tr><!-- end -->
                                </tbody><!-- end tbody -->
                            </table><!-- end table -->
                        </div><!-- end -->
                    </div><!-- end cardbody -->
                </div><!-- end card -->
            </div><!-- end col --> --}}
        </div><!-- end row -->

        </div>
    </div>


    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Result(s) for {{ $academiclevel->level }} Level,  {{ !empty($programme)?$programme->name:null }} for {{ $academicSession }} Academic Session</h4>
                <div class="flex-shrink-0">
                    @if(!empty($programme))
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#approveResult">Approve Result(s)</button>
                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#generateResult">Download Result Broadsheet</button>
                    @endif
                </div>
            </div><!-- end card header -->

            <div id="generateResult" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body text-center p-5">
                            <div class="text-end">
                                <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="mt-2">
                                <lord-icon src="https://cdn.lordicon.com/xxdqfhbi.json" trigger="hover" style="width:150px;height:150px">
                                </lord-icon>
                                <h4 class="mb-3 mt-4">Are you sure you want to generate result broadsheet for <br>{{ $academiclevel->level }} level {{ !empty($programme)?$programme->name:null }}?</h4>
                                <form action="{{ url('/admin/generateResultBroadSheet') }}" method="POST">
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
                                    <input type="hidden" name="url" value="admin.getStudentResults">
                                    <hr>
                                    <button type="submit" id="submit-button" class="btn btn-success w-100">Yes, Proceed</button>
                                </form>
                            </div>
                        </div>
                        <div class="modal-footer bg-light p-3 justify-content-center">

                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->

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
                                    <input type="hidden" name="url" value="admin.getStudentResults">
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
                <table id="buttons-result" class="display table table-bordered table-striped p-3" style="width:100%">
                    <thead>
                        <tr>
                            <th rowspan="2">SN</th>
                            <th class="bg bg-info text-light" rowspan="2">Result Approval Status</th>
                            <th rowspan="2">Student Result</th>
                            <th rowspan="2">Student Name</th>
                            <th rowspan="2">Matric Number</th>
                            <th rowspan="2">Degree Class</th>
                            <th rowspan="2">Standing</th>
                            <th rowspan="2">No of failed course</th>
                            <th rowspan="2">Total failed unit</th>
                            <th rowspan="2">Failed courses</th>
                            <th class="bg bg-info text-light" rowspan="2">Previous Total Credit Units</th>
                            <th class="bg bg-info text-light" rowspan="2">Previous Total Credit Points</th>
                            <th class="bg bg-info text-light" rowspan="2">Previous CGPA</th>
                            <th class="bg bg-primary text-light" rowspan="2">Current Total Credit Units</th>
                            <th class="bg bg-primary text-light" rowspan="2">Current Total Credit Points</th>
                            <th class="bg bg-primary text-light" rowspan="2">Current GPA</th>
                            <th class="bg bg-dark text-light" rowspan="2">Cumulative Total Credit Units</th>
                            <th class="bg bg-dark text-light" rowspan="2">Cumulative Total Credit Points</th>
                            <th class="bg bg-dark text-light" rowspan="2">Cumulative CGPA</th>
                            @foreach($classifiedCourses as $courseName => $allStudents)
                                <th class="bg bg-dark text-light" colspan="6">{{ $courseName }}</th>
                            @endforeach
                        </tr>
                        <tr>
                            <!-- Subtable headers -->
                            @foreach($classifiedCourses as $courseName => $allStudents)
                                <th class="bg bg-soft-dark">Credit Unit</th>
                                <th class="bg bg-soft-dark">Test Score</th>
                                <th class="bg bg-soft-dark">Exam Score</th>
                                <th class="bg bg-soft-dark">Total Score</th>
                                <th class="bg bg-soft-dark">Point</th>
                                <th class="bg bg-soft-dark">Grade</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                            {{-- @if(!empty($student)) --}}
                                @php
                                    $degreeClass = new \App\Models\DegreeClass;
                                    $viewSemesterRegisteredCourses = $student->registeredCourses->where('semester', $semester)->where('level_id', $academiclevel->id)->where('academic_session', $academicSession);
                                    $countRegCourses = count($viewSemesterRegisteredCourses);
                                    $approvedSemesterCourse = $student->registeredCourses->where('semester', $semester)->where('level_id', $academiclevel->id)->where('academic_session', $academicSession)->where('result_approval_id', '!=', null);
                                    $countApprovedResult = count($approvedSemesterCourse);
                                    $approvedStudentStatus = false;

                                    if($countRegCourses == $countApprovedResult){
                                        $approvedStudentStatus = true;
                                    }

                                    $semesterRegisteredCourses = $student->registeredCourses->where('semester', $semester)->where('level_id', $academiclevel->id)->where('academic_session', $academicSession)->where('grade', '!=', null);
                                    $currentRegisteredCreditUnits =  $semesterRegisteredCourses->sum('course_credit_unit');
                                    $currentRegisteredGradePoints = $semesterRegisteredCourses->sum('points');
                                    $currentGPA = $currentRegisteredGradePoints > 0 ? number_format($currentRegisteredGradePoints / $currentRegisteredCreditUnits, 2) : 0;
                                    $failedSemesterCourses = $semesterRegisteredCourses->where('grade', 'F');

                                    $missingSemesterCourses = $semesterRegisteredCourses->where('grade', null);

                                    $allRegisteredCourses = $student->registeredCourses->where('grade', '!=', null);
                                    $allRegisteredCreditUnits =  $allRegisteredCourses->sum('course_credit_unit');
                                    $allRegisteredGradePoints = $allRegisteredCourses->sum('points');
                                    $CGPA = $allRegisteredGradePoints > 0 ? number_format($allRegisteredGradePoints / $allRegisteredCreditUnits, 2) : 0;
                                    $prevRegisteredCourses = $student->registeredCourses->where('semester', '!=', $semester)->where('level_id', '!=', $academiclevel->id);
                                    $prevRegisteredCreditUnits =  $prevRegisteredCourses->sum('course_credit_unit');
                                    $prevRegisteredGradePoints = $prevRegisteredCourses->sum('points');
                                    $prevCGPA = ($prevRegisteredCreditUnits != 0) ? number_format($prevRegisteredGradePoints / $prevRegisteredCreditUnits, 2) : 0.00;
                
                                    $classGrade = $degreeClass->computeClass($CGPA);
                                    $class = $classGrade->degree_class;
                                    $standing = ($classGrade->id > 4) ? 'NGS' : 'GS'; 
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="bg bg-soft-info">{{ $approvedStudentStatus?'Approved':'Not Approved' }}</td>
                                    <td width="200px">
                                        <div class="accordion" id="default-accordion-example">
                                            <div class="accordion-item shadow">
                                                <h2 class="accordion-header" id="headingTwo">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#studentCourses{{ $student->id  }}" aria-expanded="false" aria-controls="studentCourses">
                                                        View Courses - {{ strtoupper($student->applicant->lastname).', '.$student->applicant->othernames }} - {{ $CGPA }} -  @if($missingSemesterCourses->count() > 0)
                                                        <span class="text-danger">
                                                            @foreach($missingSemesterCourses as $missingSemesterCourse)
                                                                {{ $missingSemesterCourse->course_code }}, <br>
                                                            @endforeach
                                                        </span>
                                                    @endif
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
                                                                    @foreach($viewSemesterRegisteredCourses as $registeredCourse)
                                                                        <tr>
                                                                            <td>{{ $loop->iteration }}</td>
                                                                            <td>{{ $registeredCourse->course->code }}</td>
                                                                            <td>{{ ucwords(strtolower($registeredCourse->course->name)) }}</td>
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
                                    <td class="bg bg-soft-info">{{ $prevRegisteredCreditUnits }}</td>
                                    <td class="bg bg-soft-info">{{ $prevRegisteredGradePoints }}</td>
                                    <td class="bg bg-soft-info">{{ $prevCGPA }}</td>
                                    <td class="bg bg-soft-primary">{{ $currentRegisteredCreditUnits }}</td>
                                    <td class="bg bg-soft-primary">{{ $currentRegisteredGradePoints }}</td>
                                    <td class="bg bg-soft-primary">{{ $currentGPA }}</td>
                                    <td class="bg bg-soft-dark">{{ $allRegisteredCreditUnits }}</td>
                                    <td class="bg bg-soft-dark">{{ $allRegisteredGradePoints }}</td>
                                    <td class="bg bg-soft-dark">{{ $CGPA }}</td>
                                    @foreach($classifiedCourses as $courseName => $students)
                                        @php
                                            $courseDetails = $student->registeredCourses->where('course_code', $courseName)->where('semester', $semester)->where('level_id', $academiclevel->id)->where('academic_session', $academicSession)->first();
                                        @endphp
                                        <td>
                                            @if($courseDetails)
                                                {{ $courseDetails->course_credit_unit }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($courseDetails)
                                                {{ $courseDetails->ca_score }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($courseDetails)
                                                {{ $courseDetails->exam_score }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($courseDetails)
                                                {{ $courseDetails->total }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($courseDetails)
                                                {{ $courseDetails->points }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($courseDetails)
                                                {{ $courseDetails->grade }}
                                            @endif
                                        </td>
                                    @endforeach
                                    
                                </tr>
                            {{-- @endif --}}
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
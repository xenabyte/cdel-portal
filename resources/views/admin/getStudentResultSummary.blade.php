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

    {{-- <div class="col-lg-12">
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
                                    $percentage = number_format(($count / $totalStudents) * 100, 2);
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
                                            $percentage = ($count / $totalStudents) * 100;
                                        @endphp
                                        <p class="mb-0">{{ number_format($percentage, 2) }}%</p>
                                    </div>
                                </div><!-- end -->
                            @endforeach

                        </div><!-- end -->

                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end col -->
        </div><!-- end row -->

        </div>
    </div> --}}


    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Result(s) for  {{ !empty($faculty)?$faculty->name:null }} for {{ $academicSession }} Academic Session</h4>
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
                                    @if(!empty($programme))
                                    <input type="hidden" name="faculty_id" value="{{ $faculty->id }}">
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
                @foreach($academicLevels as $academicLevel)
                <table id="buttons-datatables" class="display table table-bordered table-striped p-3" style="width:100%">
                    <thead>
                        <tr>
                            <th>SN</th>
                            <th>Programmes</th>
                            <th>Number of Students</th>
                            <th>Number of Good Standing</th>
                            <th>Number of Not in Good Standing</th>
                            <th>Number of First Class</th>
                            <th>Number of Second Class Upper</th>
                            <th>Number of Second Lower</th>
                            <th>Number of Third Class</th>
                            <th>Number of Fail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($classifiedStudents[$academicLevel->name] as $programName => $students)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $programName }}</td>
                                <td>{{ count($students) }}</td>
                                <td>{{ $students->where('status', 'Good Standing')->count() }}</td>
                                <td>{{ $students->where('status', 'Not in Good Standing')->count() }}</td>
                                <td>{{ $students->where('class', 'First Class')->count() }}</td>
                                <td>{{ $students->where('class', 'Second Class Upper')->count() }}</td>
                                <td>{{ $students->where('class', 'Second Class Lower')->count() }}</td>
                                <td>{{ $students->where('class', 'Third Class')->count() }}</td>
                                <td>{{ $students->where('class', 'Fail')->count() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach
            </div>
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end row -->
@endif
@endsection
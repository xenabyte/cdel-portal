@php
    $staff = Auth::guard('staff')->user();
    $staffAcademicPlannerRole = false;
    $staffLevelAdviserRole = false;
    foreach ($staff->staffRoles as $staffRole) {
        if(strtolower($staffRole->role->role) == 'academic planning'){
            $staffAcademicPlannerRole = true;
        }  
        if (strtolower($staffRole->role->role) == 'level adviser') {
            $staffLevelAdviserRole = true;
        } 
    }

@endphp
@extends('staff.layout.dashboard')

@section('content')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Programme(s)</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Programme(s)</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center">
                <h4 class="card-title mb-0 flex-grow-1">Programmes</h4>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <table class="table table-stripped table-bordered table-nowrap">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            @if($staffAcademicPlannerRole)
                                <th scope="col">Level Adviser</th>
                            @endif
                            <th scope="col">Programme</th>
                            <th scope="col">Level</th>
                            <th scope="col">DAP Comment</th>
                            <th scope="col">DAP Approval Status</th>
                            <th scope="col">Course(s)</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($adviserProgrammes as $adviserProgramme)
                        <tr>
                            <td scope="row"> {{ $loop->iteration }}</td>
                            @if($staffAcademicPlannerRole)
                            <td>{{$adviserProgramme->staff? $adviserProgramme->staff->title.' '.$adviserProgramme->staff->lastname.' '.$adviserProgramme->staff->othernames: null}}</td>
                            @endif
                            <td>{{$adviserProgramme->programme->name}}</td>
                            <td>
                                {{$adviserProgramme->level->level}} Level <span class="badge badge-pill bg-danger" data-key="t-hot">{{ $adviserProgramme->studentRegistrationsCount }} </span>
                            </td>
                            <td>
                                @if(!empty($adviserProgramme->comment))
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#viewComment{{ $adviserProgramme->id }}">View Comment</button>                                   
                                @endif
                            </td>
                            <td>
                                @if(!empty($adviserProgramme->course_approval_status))
                                    @if(strtolower($adviserProgramme->course_approval_status) == 'approved')
                                        <span class="badge bg-success p-2 rounded-pill">Approved</span>
                                    @else
                                        <span class="badge bg-warning p-2 rounded-pill">Pending</span>
                                    @endif
                                @endif
                            </td>
                            <td><a href="#" data-bs-toggle="modal" data-bs-target="#viewCourses{{ $adviserProgramme->id }}" class="btn btn-primary">Courses</a></td>
                            <td>
                                @if(!$staffAcademicPlannerRole)
                                    @if($adviserProgramme->course_approval_status == 'approved')
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#viewCourses{{ $adviserProgramme->id }}" class="btn btn-primary">Courses</a>
                                    <a href="{{ url('/staff/levelCourseReg/'.$adviserProgramme->id) }}" class="btn btn-info">Course Registrations</a>
                                    <a href="{{ url('/staff/levelStudents/'.$adviserProgramme->id) }}" class="btn btn-dark">All Students</a>
                                    @endif
                                    @if($staffLevelAdviserRole)
                                        @if($adviserProgramme->course_approval_status == null)
                                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#submitCourses{{ $adviserProgramme->id }}">Submit For DAP Approval</button>
                                        @else
                                        <span class="p-2 badge btn btn-primary-subtle text-primary badge-border">{{ ucwords($adviserProgramme->course_approval_status) }}</span>
                                        @endif
                                    @endif
                                @else
                                    {{-- @if(strtolower($adviserProgramme->course_approval_status) == 'pending') --}}
                                    {{-- @if(!empty($adviserProgramme->course_approval_status)) --}}
                                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#manage{{ $adviserProgramme->id }}">Manage Level Adviser Request</button>
                                    {{-- @endif --}}
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                      
            </div>
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>

@foreach($adviserProgrammes as $adviserProgramme)
<div id="submitCourses{{ $adviserProgramme->id }}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="text-end">
                    <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="mt-2">
                    <lord-icon src="https://cdn.lordicon.com/tqywkdcz.json" trigger="hover" style="width:150px;height:150px">
                    </lord-icon>
                    <h4 class="mb-3 mt-4">Are you sure you want to submit this courses for <br> {{$adviserProgramme->level->level}} Level {{$adviserProgramme->programme->name}} <br> for approval?</h4>
                    <form action="{{ url('/staff/requestCourseApproval') }}" method="POST">
                        @csrf
                        <input type="hidden" name="level_id" value="{{ $adviserProgramme->level->id }}">
                        <input type="hidden" name="programme_id" value="{{ $adviserProgramme->programme->id }}">
                        <input type="hidden" name="level_adviser_id" value="{{ $adviserProgramme->id }}">
                        <hr>
                        <button type="submit" id="submit-button" class="btn btn-danger w-100">Yes, Proceed</button>
                    </form>
                </div>
            </div>
            <div class="modal-footer bg-light p-3 justify-content-center">

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="manage{{ $adviserProgramme->id }}" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
    <!-- Fullscreen Modals -->
    <div class="modal-dialog modal-md">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Manage Courses</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <div class="modal-body">
                <form action="{{ url('/staff/courseApproval') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="level_id" value="{{ $adviserProgramme->level->id }}">
                    <input type="hidden" name="programme_id" value="{{ $adviserProgramme->programme->id }}">
                    <input type="hidden" name="level_adviser_id" value="{{ $adviserProgramme->id }}">

                    <div class="mb-3">
                        <label for="comment" class="form-label">Comment</label>
                        <textarea class="form-control ckeditor" name="comment" id="comment">{!! $adviserProgramme->comment !!}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Select Option</label>
                        <select class="form-select" aria-label="role" name="status" required>
                            <option selected value= "">Select Option </option>
                            <option value="approved">Confirm</option>
                            <option value="request changes">Request Changes</option>
                        </select>
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="viewComment{{ $adviserProgramme->id }}" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
    <!-- Fullscreen Modals -->
    <div class="modal-dialog modal-md">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">DAP Comment</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <div class="modal-body">
                {!! $adviserProgramme->comment !!}</td>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div id="viewCourses{{ $adviserProgramme->id }}" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
    <!-- Fullscreen Modals -->
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Courses</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <h4>Harmattan Semester Courses</h4>
                    </div>
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>SN</th>
                                    <th>Code</th>
                                    <th>Course Title</th>
                                    <th>Unit</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $firstSemester = 1;
                                        $secondSemester = 1;
                                    @endphp
                                    @foreach($adviserProgramme->coursesForReg as $firstSemsRegisteredCourse)
                                        @if($firstSemsRegisteredCourse->semester == 1)
                                            <tr>
                                                <td>{{ $firstSemester++ }}</td>
                                                <td>{{ $firstSemsRegisteredCourse->course->code }}</td>
                                                <td>{{ $firstSemsRegisteredCourse->course->name }}</td>
                                                <td>{{ $firstSemsRegisteredCourse->credit_unit }}</td>
                                                <td>{{ strtoupper(substr($firstSemsRegisteredCourse->status, 0, 1)) }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <h4>Rain Semester Courses</h4>
                    </div>
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-stripped">
                                <thead>
                                <tr>
                                    <th>SN</th>
                                    <th>Code</th>
                                    <th>Course Title</th>
                                    <th>Unit</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($adviserProgramme->coursesForReg as $secondSemsRegisteredCourse)
                                        @if($secondSemsRegisteredCourse->semester == 2)
                                            <tr>
                                                <td>{{ $secondSemester++ }}</td>
                                                <td>{{ $secondSemsRegisteredCourse->course->code }}</td>
                                                <td>{{ $secondSemsRegisteredCourse->course->name }}</td>
                                                <td>{{ $secondSemsRegisteredCourse->credit_unit }}</td>
                                                <td>{{ strtoupper(substr($secondSemsRegisteredCourse->status, 0, 1)) }}</td>
                                            </tr>
                                        @endif
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
@endforeach
@endsection

@php
    $staff = Auth::guard('staff')->user();
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
                            <th scope="col">Level Adviser</th>
                            <th scope="col">Programme</th>
                            <th scope="col">Level</th>
                           
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($adviserProgrammes as $adviserProgramme)
                        <tr>
                            <td scope="row"> {{ $loop->iteration }}</td>
                            <td>{{$adviserProgramme->staff? $adviserProgramme->staff->title.' '.$adviserProgramme->staff->lastname.' '.$adviserProgramme->staff->othernames: null}}</td>
                            <td>{{$adviserProgramme->programme->name}}</td>
                            <td>
                                {{$adviserProgramme->level->level}} Level <span class="badge badge-pill bg-danger" data-key="t-hot">{{ $adviserProgramme->studentRegistrationsCount }} </span>
                            </td>
                            <td>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#viewCourses{{ $adviserProgramme->id }}" class="btn btn-primary">Courses</a>
                                <a href="{{ url('/staff/levelCourseReg/'.$adviserProgramme->id) }}" class="btn btn-info">Course Registrations</a>
                                <a href="{{ url('/staff/levelStudents/'.$adviserProgramme->id) }}" class="btn btn-dark">All Students</a>
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

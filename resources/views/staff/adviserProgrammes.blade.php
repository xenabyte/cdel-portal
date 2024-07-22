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
                            <th scope="col">Programme</th>
                            <th scope="col">Level</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($adviserProgrammes as $adviserProgramme)
                        <tr>
                            <td scope="row"> {{ $loop->iteration }}</td>
                            <td>{{$adviserProgramme->programme->name}}</td>
                            <td>{{$adviserProgramme->level->level}} Level <span class="badge badge-pill bg-danger" data-key="t-hot">{{ $adviserProgramme->studentRegistrationsCount }} </span></td>
                            <td>
                                <a href="{{ url('/staff/programme/'.$adviserProgramme->programme->slug) }}" class="btn btn-primary">Programme Details</a>
                                <a href="{{ url('/staff/levelCourseReg/'.$adviserProgramme->id) }}" class="btn btn-info">Course Registrations</a>
                                <a href="{{ url('/staff/levelStudents/'.$adviserProgramme->id) }}" class="btn btn-dark">All Students</a>
                                @if($adviserProgramme->course_approval_status == null)
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#submitCourses">Submit For DAP Approval</button>
                                @else
                                <a href="#" class="btn btn-info">{{ ucwords($adviserProgramme->course_approval_status) }}</a>
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
<div id="submitCourses" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
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
@endforeach
@endsection

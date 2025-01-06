@extends('admin.layout.dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Student Course(s)</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Student Course(s)</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->
@if(empty($courseForReg))
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col-lg-9">
                        <h4 class="mt-4 fw-semibold">Fetch Course(s)</h4>
                        <p class="text-muted mt-3"></p>
                        <div class="mt-4">
                            <form action="{{ url('/admin/getCourseResults') }}" method="POST">
                                @csrf
                                <div class="row g-3">

                                    <div class="col-lg-12">
                                        <div class="">
                                            <label>Select Course</label>
                                            <select class="form-select select2 selectWithSearch" id="selectWithSearch" name="course_id" aria-label="cstatus">
                                                 <option value="" selected>--Select--</option>
                                                @foreach($allCourses as $allCourse)<option value="{{$allCourse->id}}">{{$allCourse->code}} - {{ucwords(strtolower($allCourse->name))}}</option>@endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-floating">
                                            <select class="form-select" id="programme" name="programme_id" aria-label="programme">
                                                <option value="" selected>--Select--</option>
                                                @foreach($programmes as $programme)
                                                    <option value="{{ $programme->id }}">{{ $programme->name }}</option>
                                                @endforeach
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
                                            <select class="form-select" id="academic_session" name="academic_session" aria-label="academic_session">
                                                <option value="" selected>--Select--</option>
                                                @foreach($academicSessions as $academicSession)
                                                    <option value="{{ $academicSession->year }}">{{ $academicSession->year }}</option>
                                                @endforeach
                                            </select>
                                            <label for="level">Academic Session</label>
                                        </div>
                                    </div>

                                    <button type="submit" id="submit-button" class="btn btn-fill btn-primary btn-lg btn-block mb-5">Get Course</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>
@else
<div class="row">
    <div class="col-xxl-4">
        <div class="card">
            <div class="card-body p-4">
                <div>
                    <div class="mt-4 text-center">
                        <h5 class="mb-1">{{ ucwords(strtolower($courseForReg->course->name)) }} - {{ $courseForReg->course->code }} - {{ $courseForReg->id }}</h5>
                        <p class="text-muted"><strong>Academic Session:</strong> {{ $courseForReg->academic_session }} <br>
                            <strong>Programme:</strong> {{ $courseForReg->programme->name }}<br>
                            <strong>Credit Unit:</strong> {{ $courseForReg->credit_unit }}<br>
                        </p>
                    </div>
                    {{-- <div class="table-responsive border-top border-top-dashed">
                        <table class="table mb-0 table-borderless">
                            <tbody>
                                <tr>
                                    <th><span class="fw-medium">Department:</span></th>
                                    <td>{{ $student->department->name }}</td>
                                </tr>
                                <tr>
                                    <th><span class="fw-medium">Faculty:</span></th>
                                    <td>{{ $student->faculty->name }}</td>
                                </tr>
                                <tr>
                                    <th><span class="fw-medium">Email:</span></th>
                                    <td>{{ $student->email }}</td>
                                </tr>
                                <tr>
                                    <th><span class="fw-medium">Contact No.:</span></th>
                                    <td>{{ $student->applicant->phone_number }}</td>
                                </tr>
                                <tr>
                                    <th><span class="fw-medium">Address:</span></th>
                                    <td>{!! $student->applicant->address !!}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div> --}}
                </div>
            </div>

        </div>
        <!--end card-->
    </div>
    <!--end col-->

    <div class="col-xxl-8">
        <div class="card">
            <div class="card-header border-0 align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Update Course</h4>
                <div class="text-end mb-5">
                    
                </div>
            </div><!-- end card header -->

            <div class="card-body pb-2 border-top border-top-dashed">
                <form action="{{ url('/admin/updateCourseResult') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="course_per_prog_id" value="{{ $courseForReg->id }}">
                    
                    <div class="mb-3">
                        <label for="type" class="form-label">Select Level</label>
                        <select class="form-select" aria-label="type" name="level_id" required>
                            <option value="">Select Level</option>
                            @foreach($academicLevels as $level) <option @if($courseForReg->level_id == $level->id) @endif value="{{ $level->id }}">{{ $level->id * 100 }} Level</option> @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Select Session</label>
                        <select class="form-select" aria-label="type" name="session" required>
                            <option selected value="">Select Session</option>
                            @foreach($academicSessions as $session)<option value="{{ $session->year }}">{{ $session->year }}</option>@endforeach
                        </select>
                    </div>

                    <div class="col-lg-12 mt-3">
                        <div class="form-floating">
                            <input type="text" class="form-control" max="6" name="credit_unit" id="credit_unit" value="{{ $courseForReg->credit_unit }}">
                            <label for="semester">Credit Unit</label>
                        </div>
                    </div>

                    <div class="mt-3">
                        <select class="form-select" id="semester" name="semester" aria-label="semester">
                            <option value="" selected>Select Semester</option>
                            <option value="1">Harmattan Semester</option>
                            <option value="2">Rain Semester</option>
                        </select>
                    </div>

                    <div class="text-end border-top border-top-dashed p-3">
                        <br>
                        <button type="submit" id="submit-button" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div><!-- end card body -->
        </div><!-- end card -->
       
    </div>
    <!--end col-->
    
</div>
<!--end row-->
@endif
@endsection
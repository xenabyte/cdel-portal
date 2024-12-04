@extends('staff.layout.dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Department</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Department</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row project-wrapper">
    <div class="col-xxl-8 card-height-100">
        <div class="row">
            <div class="col-xl-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-primary rounded-2 fs-2">
                                    <i data-feather="briefcase"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 overflow-hidden ms-3">
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-3">Staffs</p>
                                <div class="d-flex align-items-center mb-3">
                                    <h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value" data-target="{{ $department->staffs->count() }}">0</span></h4>
                                </div>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div>
            </div><!-- end col -->

            <div class="col-xl-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-warning rounded-2 fs-2">
                                    <i data-feather="award"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-uppercase fw-medium text-muted mb-3">Courses</p>
                                <div class="d-flex align-items-center mb-3">
                                    <h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value" data-target="{{ $department->courses->count() }}">0</span></h4>
                                </div>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div>
            </div><!-- end col -->
        </div><!-- end row -->

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header border-0 align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Department Overview - {{ $department->name }}</h4>
                    </div><!-- end card header -->

                    <div class="card-header p-0 border-0 bg-soft-light">
                        <div class="row g-0 text-center">
                            <div class="col-6 col-sm-12">
                                <div class="p-3 border border-dashed border-start-0">
                                    <strong>Dept Code:</strong> {{ $department->code }}
                                </div>
                            </div>
                        </div>
                    </div><!-- end card header -->
                    <div class="card-body">

                        {{-- <div class="align-items-center d-flex mt-3 pt-3">
                            <p class="mb-0 flex-grow-1">Add Course</p>
                            <div class="flex-shrink-0">
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addCourse" class="btn btn-primary">Add Course</a>
                            </div>
                        </div> --}}

                        @if($department->id == env('VOCATION_ID'))
                        <div class="align-items-center d-flex mt-3 pt-3">
                            <p class="mb-0 flex-grow-1">Upload Result</p>
                            <div class="flex-shrink-0">
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#uploadResult" class="btn btn-primary">Upload Result</a>
                            </div>
                        </div>
                        @endif
                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end col -->
        </div><!-- end row -->
    </div><!-- end col -->
    <div class="col-xxl-4">
        <div class="card card-height-100">
            <div class="card-header border-0">
                <h4 class="card-title mb-0">HOD's Profile</h4>
            </div><!-- end cardheader -->
            @if(!empty($department->hod))
            <div class="card-body pt-0">
                <img class="card-img-top img-fluid" src="{{ $department->hod->image }}" alt="Card image cap">
                <div class="card-body">
                    <p class="card-text text-center"><strong>{{ $department->hod->lastname.' '. $department->hod->othernames }}</strong> <br> HOD, {{ $department->name }}</p>
                </div>


                <div class="card-header p-0 border-0 bg-soft-light">
                    <div class="row g-0 text-center">
                        <div class="col-12 col-sm-12">
                            <div class="p-3 border border-dashed border-start-0">

                            </div>
                        </div>
                    </div>
                </div><!-- end card header -->
            </div><!-- end cardbody -->
            @endif
        </div><!-- end card -->
    </div><!-- end col -->
</div><!-- end row -->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Departmental Courses </h4>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Course Name</th>
                            <th scope="col">Course Code</th>
                            <th scope="col">Course Lecturer</th>
                            <th scope="col">Course Password</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($department->courses as $course)
                        <tr>
                            @php
                                $courseManagement =  $course->courseManagement->where('academic_session', $pageGlobalData->sessionSetting->academic_session);
                                $assignedCourse = $courseManagement->where('academic_session', $pageGlobalData->sessionSetting->academic_session)->first();
                                $staff = !empty($assignedCourse) && !empty($assignedCourse->staff) ? $assignedCourse->staff->title.' '.$assignedCourse->staff->lastname.' '.$assignedCourse->staff->othernames :null;
                                $password = !empty($assignedCourse) ? $assignedCourse->passcode :null;
                            @endphp
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $course->name }}</td>
                            <td>{{ $course->code }}</td>
                            <td>{{ $staff }}</td>
                            <td>{{ $password }}</td>
                            <td>
                                <div class="hstack gap-3 fs-15">
                                    <a href="{{ url('/staff/courseDetail/'.$course->id) }}" class="btn btn-lg btn-primary">Course Details</a>
                                    {{-- <a href="avascript:void(0);"  data-bs-toggle="modal" data-bs-target="#edit{{$course->id}}"  class="btn btn-primary m-1"><i class= "mdi mdi-edit"></i> Edit Course</a> --}}
                                    @if(empty($staff))
                                    <a href="avascript:void(0);"  data-bs-toggle="modal" data-bs-target="#assignCourse{{$course->id}}" class="btn btn-info m-1"><i class= "mdi mdi-link"></i> Assign Staff To Course</a>
                                    @endif
                                    @if(!empty($staff))
                                    <a href="avascript:void(0);"  data-bs-toggle="modal" data-bs-target="#unsetStaff{{$course->id}}" class="btn btn-danger m-1"><i class= "mdi mdi-link"></i> Unset Staff From Course</a>
                                    @endif

                                    {{-- <div id="edit{{$course->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 overflow-hidden">
                                                <div class="modal-header p-3">
                                                    <h4 class="card-title mb-0">Edit Course</h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                        
                                                <div class="modal-body border-top border-top-dashed">
                                                    <form action="{{ url('/staff/updateCourse') }}" method="post" enctype="multipart/form-data">
                                                        @csrf

                                                        <input name="course_id" type="hidden" value="{{$course->id}}">
                        
                                                        <div class="mb-3">
                                                            <label for="name" class="form-label">Course Name</label>
                                                            <input type="text" class="form-control" name="name" id="name" value="{{ $course->name }}"  required>
                                                        </div>
                                    
                                                        <div class="mb-3">
                                                            <label for="code" class="form-label">Course Code</label>
                                                            <input type="text" class="form-control" name="code" id="code" value="{{ $course->code }}" required>
                                                        </div>

                                                        <div class="text-end border-top border-top-dashed p-3">
                                                            <button type="submit" id="submit-button" class="btn btn-primary">Save Changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div><!-- /.modal --> --}}

                                    <div id="assignCourse{{$course->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 overflow-hidden">
                                                <div class="modal-header p-3">
                                                    <h4 class="card-title mb-0">Assign Staff To Course</h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                        
                                                <div class="modal-body border-top border-top-dashed">
                                                    <form action="{{ url('/staff/assignCourse') }}" method="post" enctype="multipart/form-data">
                                                        @csrf

                                                        <input name="course_id" type="hidden" value="{{$course->id}}">
                        
                                                        <div class="mb-3">
                                                            <label for="staff_id" class="form-label">Select Staff</label>
                                                            <select class="form-select" aria-label="staff_id" name="staff_id">
                                                                <option selected value= "">Select Staff </option>
                                                                @foreach($department->staffs as $staff)
                                                                <option value="{{ $staff->id }}">{{ $staff->title.' '.$staff->lastname.' '.$staff->othernames }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="text-end border-top border-top-dashed p-3">
                                                            <button type="submit" id="submit-button" class="btn btn-primary">Assign </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div><!-- /.modal -->

                                    <div id="unsetStaff{{$course->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-body text-center p-5">
                                                    <div class="text-end">
                                                        <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="mt-2">
                                                        <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="hover" style="width:150px;height:150px">
                                                        </lord-icon>
                                                        <h4 class="mb-3 mt-4">Are you sure you want to delete <br/> {{ $course->code }}?</h4>
                                                        <form action="{{ url('/staff/unsetStaff') }}" method="POST">
                                                            @csrf
                                                            <input name="course_id" type="hidden" value="{{$course->id}}">
                                                            <hr>
                                                            <button type="submit" id="submit-button" class="btn btn-danger w-100">Yes, Unset Assign</button>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light p-3 justify-content-center">

                                                </div>
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div><!-- /.modal -->
                                </div>
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

{{-- <div id="addCourse" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Add Courses</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body border-top border-top-dashed">

                <form action="{{ url('/staff/addCourse') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="department_id" value="{{ $department->id }}">

                    <div class="mb-3">
                        <label for="name" class="form-label">Course Name</label>
                        <input type="text" class="form-control" name="name" id="name" required>
                    </div>

                    <div class="mb-3">
                        <label for="code" class="form-label">Course Code</label>
                        <input type="text" class="form-control" name="code" id="code" required>
                    </div>

                    <div class="text-end border-top border-top-dashed p-3 p-3">
                        <button type="submit" id="submit-button" class="btn btn-primary">Add Course</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal --> --}}

<div id="uploadResult" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Upload Result</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <div class="modal-body">
                <form action="{{ url('/staff/uploadVocationResult') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-lg-12">
                            <div>
                                <label for="formSizeLarge" class="form-label">Result (CSV)</label>
                                <input name="result"  class="form-control form-control-lg" id="formSizeLarge" type="file" required>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Upload Result</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@endsection
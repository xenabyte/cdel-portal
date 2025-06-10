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
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($department->courses as $course)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ ucwords(strtolower($course->name)) }}</td>
                            <td>{{ $course->code }}</td>
                            <td>
                                @foreach($programmeCategories as $programmeCategory)
                                    @if(!empty($programmeCategory->academicSessionSetting))
                                        @php
                                            $academicSession = $programmeCategory->academicSessionSetting->academic_session;
                                            $courseManagement = $course->courseManagement->where('programme_category_id', $programmeCategory->id);
                                            $assignedCourse = $courseManagement->where('academic_session', $academicSession)->first();
                                            $staff = !empty($assignedCourse) && !empty($assignedCourse->staff)
                                                ? ucwords(strtolower($assignedCourse->staff->title.' '.$assignedCourse->staff->lastname.' '.$assignedCourse->staff->othernames))
                                                : null;
                                            $staffId = !empty($assignedCourse->staff) ? $assignedCourse->staff->id : null;
                                            $password = !empty($assignedCourse) ? $assignedCourse->passcode : null;
                                        @endphp

                                        @if($staff || $password)
                                            <div class="mb-3 p-2 rounded border shadow-sm bg-light">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong class="text-primary">{{ $programmeCategory->category }}</strong><br>
                                                        <span class="text-muted">{{ $staff ?? 'N/A' }}</span> 
                                                        <span class="badge bg-secondary ms-2">{{ $password ?? 'No Passcode' }}</span>
                                                    </div>
                                                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#unsetStaff{{$course->id}}{{$programmeCategory->id}}">
                                                        <i class="mdi mdi-link"></i> Unset Staff
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Modal -->
                                            <div class="modal fade" id="unsetStaff{{$course->id}}{{$programmeCategory->id}}" tabindex="-1" aria-labelledby="modalLabel{{$course->id}}{{$programmeCategory->id}}" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header border-0">
                                                            <h5 class="modal-title" id="modalLabel{{$course->id}}{{$programmeCategory->id}}">Confirm Unset</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body text-center">
                                                            <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" style="width:100px;height:100px"></lord-icon>
                                                            <p class="mt-3">Are you sure you want to unset <br><strong>{{ $staff }}</strong> from <strong>{{ $course->code }}</strong> in <strong>{{ $programmeCategory->category }}</strong>?</p>
                                                            <form action="{{ url('/staff/unsetStaff') }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="course_id" value="{{ $course->id }}">
                                                                <input type="hidden" name="staff_id" value="{{ $staffId }}">
                                                                <input type="hidden" name="programme_category_id" value="{{ $programmeCategory->id }}">
                                                                <button type="submit" class="btn btn-danger w-100 mt-3">Yes, Unset Staff</button>
                                                            </form>
                                                        </div>
                                                        <div class="modal-footer justify-content-center border-0">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                @endforeach
                            </td>
                            <td>
                                <div>
                                    {{-- <a href="{{ url('/staff/courseDetail/'.$course->id) }}" class="btn btn-lg btn-primary">Course Details</a> --}}
                                    <form id="courseDetailForm{{ $loop->iteration }}" action="{{ url('/staff/courseDetail/'.$course->id) }}" method="get">
                                        @csrf
                                        <div class="input-group" style="display: flex; flex-wrap: nowrap;">
                                            <select id="programmeSelect{{ $loop->iteration }}" class="form-select select2 selectWithSearch" required style="flex-grow: 1;">
                                                <option value="" selected>Select Programme Category</option>
                                                @foreach($programmeCategories as $category)
                                                    <option value="{{ $category->category }}">{{ $category->category }} Programme</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="btn btn-outline-secondary btn-sm shadow-none">Fetch Course Details</button>
                                        </div>
                                        <hr>
                                    </form>

                                    <script>
                                        document.getElementById('courseDetailForm{{ $loop->iteration }}').addEventListener('submit', function(event) {
                                            event.preventDefault();

                                            var programmeSelect = document.getElementById('programmeSelect{{ $loop->iteration }}').value;

                                            if (programmeSelect) {
                                                var updatedAction = this.action + '/' + programmeSelect;
                                                window.location.href = updatedAction;
                                            } else {
                                                alert('Please select a programme category');
                                            }
                                        });
                                    </script>
                                    <a href="avascript:void(0);"  data-bs-toggle="modal" data-bs-target="#edit{{$course->id}}"  class="btn btn-sm btn-primary m-1"><i class= "mdi mdi-edit"></i> Edit Course</a>
                                    <a href="avascript:void(0);"  data-bs-toggle="modal" data-bs-target="#assignCourse{{$course->id}}" class="btn btn-sm btn-info m-1"><i class= "mdi mdi-link"></i> Assign Staff To Course</a>

                                    <div id="edit{{$course->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
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
                                    </div><!-- /.modal -->

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
                                                                <option value="{{ $staff->id }}">{{ ucwords(strtolower($staff->title.' '.$staff->lastname.' '.$staff->othernames)) }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="mb-3">
                                                            <select class="form-select" name="programme_category_id" required style="flex-grow: 1;">
                                                                <option value="" selected>Select Programme Category</option>
                                                                @foreach($programmeCategories as $category)
                                                                    <option value="{{ $category->id }}">{{ $category->category }} Programme</option>
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

<div id="addCourse" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
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
</div><!-- /.modal -->


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
                        <div class="col-lg-12 mb-3">
                            <div>
                                <label for="formSizeLarge" class="form-label">Result (CSV)</label>
                                <input name="result"  class="form-control form-control-lg" id="formSizeLarge" type="file" required>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <select class="form-select select2 selectWithSearch" name="programme_category_id" required style="flex-grow: 1;">
                                <option value="" selected>Select Programme Category</option>
                                @foreach($programmeCategories as $category)
                                    <option value="{{ $category->category }}">{{ $category->category }} Programme</option>
                                @endforeach
                            </select>
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
@extends('staff.layout.dashboard')
@php
    $staff = Auth::guard('staff')->user();
    $staffLevelAdviserRole = false;    
    
    foreach ($staff->staffRoles as $staffRole) {
        if (strtolower($staffRole->role->role) == 'level adviser') {
            $staffLevelAdviserRole = true;
        }
    }
@endphp
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">{{ $programmeCategory->category }} Programme Student Course(s) for {{ $pageGlobalData->sessionSetting->academic_session }} academic session</h4>

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
@if(empty($courses))
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <div class="row justify-content-center">
                        <div class="col-lg-9">
                            <h4 class="mt-4 fw-semibold">Fetch {{ $programmeCategory->category }} Programme Student Course(s) for {{ $pageGlobalData->sessionSetting->academic_session }}</h4>
                            <p class="text-muted mt-3"></p>
                            <div class="mt-4">
                                <form action="{{ url('/staff/getStudentCourses') }}" method="POST">
                                    @csrf
                                    <div class="row g-3">
                                        <input type="hidden" name="programme_category_id" value="{{ $programmeCategory->id }}">

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

                                        {{-- <div class="col-lg-12">
                                            <div class="form-floating">
                                                <select class="form-select" id="programme_category" name="programme_category_id" aria-label="Programme Category">
                                                    <option value="" selected>--Select--</option>
                                                    @foreach($programmeCategories as $programmeCategory)<option value="{{ $programmeCategory->id }}">{{ $programmeCategory->category }} Programme</option>@endforeach
                                                </select>
                                                <label for="session">Programme Category</label>
                                            </div>
                                        </div> --}}
        
                                        <div class="col-lg-12">
                                            <div class="form-floating">
                                                <select class="form-select" id="semester" name="semester" aria-label="semester">
                                                    <option value="" selected>--Select--</option>
                                                    <option value="1">Harmattan Semester</option>
                                                    <option value="2">Rain Semester</option>
                                                    <option value="3">Summer Semester</option>
                                                </select>
                                                <label for="semester">Semester</label>
                                            </div>
                                        </div>
        
                                        <button type="submit" id="submit-button" class="btn btn-fill btn-primary btn-lg btn-block mb-5">Get Courses</button>
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
@if(!empty($courses))
@if($staffLevelAdviserRole)
<div class="col-lg-12">
    <div class="card">
        <div class="card-body">
            <div class="accordion" id="default-accordion-example">
                <div class="accordion-item shadow">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed bg-info" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Add Course to be registered by student
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#default-accordion-example">
                        <div class="accordion-body">
                            <form action="{{ url('/staff/addCourseForStudent') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="level_id" value="{{ $academiclevel->id }}">
                                <input type="hidden" name="programme_id" value="{{ $programme->id }}">
                                <input type="hidden" name="semester" value="{{$semester}}">
                                <input type="hidden" name="programme" value="{{$programme}}">
                                <input type="hidden" name="academiclevel" value="{{$academiclevel}}">
                                <input type="hidden" name="courses" value="{{$courses}}">
                                <input type="hidden" name="allCourses" value="{{$allCourses}}">
                                <input type="hidden" name="academic_session" value="{{$academic_session}}">
                                <input type="hidden" name="programme_category_id" value="{{$programme_category_id}}">
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
                                            <select class="form-select" id="cstatus" name="status" aria-label="cstatus">
                                                <option value="" selected>--Select--</option>
                                                <option value="Required">Required</option>
                                                <option value="Core">Core</option>
                                                <option value="Elective">Elective</option>
                                            </select>
                                            <label for="cstatus">Status</label>
                                        </div>
                                    </div>
            
                                    <div class="col-lg-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" max="6" name="credit_unit" id="credit_unit">
                                            <label for="semester">Credit Unit</label>
                                        </div>
                                    </div>
            
                                    <button type="submit" id="submit-button" class="btn btn-primary">Add Course</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">{{ $semester == 1 ? 'Harmattan' : ($semester == 2 ? 'Rain' : 'Summer') }} Semester Course(s) for {{ $academiclevel->level }} Level, {{ $programmeCategory->category}} Programme for {{ $programme->name }} for {{ $academic_session }} Academic Session</h4>
            </div><!-- end card header -->

            {{-- <div id="approveResult" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body text-center p-5">
                            <div class="text-end">
                                <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="mt-2">
                                <lord-icon src="https://cdn.lordicon.com/xxdqfhbi.json" trigger="hover" style="width:150px;height:150px">
                                </lord-icon>
                                <h4 class="mb-3 mt-4">Are you sure you want to approve result for <br>{{ $academiclevel->level }} level {{ $programme->name }}?</h4>
                                <form action="{{ url('/admin/approveResult') }}" method="POST">
                                    @csrf
                                    @foreach ($students as $studentforIds)
                                    <input type="hidden" name="student_ids[]" value="{{ $studentforIds->id }}">
                                    @endforeach
                                    <input type="hidden" name="level_id" value="{{ $academiclevel->id }}">
                                    <input type="hidden" name="programme_id" value="{{ $programme->id }}">
                                    <input type="hidden" name="department_id" value="{{ $department_id }}">
                                    <input type="hidden" name="faculty_id" value="{{ $faculty_id }}">
                                    <input type="hidden" name="session" value="{{ $academicSession }}">
                                    <hr>
                                    <button type="submit" id="submit-button" class="btn btn-success w-100">Yes, Approve</button>
                                </form>
                            </div>
                        </div>
                        <div class="modal-footer bg-light p-3 justify-content-center">

                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal --> --}}

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table class="table table-stripped table-bordered table-nowrap">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Staff Details</th>
                            <th scope="col">Course Code</th>
                            <th scope="col">Course Title</th>
                            <th scope="col">Course Unit</th>
                            <th scope="col">Status</th>
                            <th scope="col">Level</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($courses as $course)
                        @php
                            $courseManagement =  $course->course->courseManagement->where('academic_session', $pageGlobalData->sessionSetting->academic_session);
                            $assignedCourse = $courseManagement->where('academic_session', $pageGlobalData->sessionSetting->academic_session)->first();
                            $staff = !empty($assignedCourse) && !empty($assignedCourse->staff) ? $assignedCourse->staff->title.' '.$assignedCourse->staff->lastname.' '.$assignedCourse->staff->othernames :null;
                        @endphp
                        <tr>
                            <td scope="row"> {{ $loop->iteration }}</td>
                            <td>{{!empty($staff)? $staff : null }}</td>
                            <td>{{$course->course->code}}</td>
                            <td>{{ucwords(strtolower($course->course->name)) }}</td>
                            <td>{{$course->credit_unit}} </td>
                            <td>{{$course->status}}</td>
                            <td>{{$course->level->level}}</td>
                            <td>
                                <a href="{{ url('/staff/courseDetail/'.$course->course->id.'/'.$programmeCategory->category) }}" class="btn btn-lg btn-primary">Course Details</a>
                                <br>
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete{{$course->id}}" class="btn btn-danger"><i class="ri-delete-bin-5-line"></i> Delete</a>
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit{{$course->id}}" class="btn btn-info"><i class="ri-edit-circle-fill"></i> Edit</a>

                                <div id="delete{{$course->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-body text-center p-5">
                                                <div class="text-end">
                                                    <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="mt-2">
                                                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="hover" style="width:150px;height:150px">
                                                    </lord-icon>
                                                    <h4 class="mb-3 mt-4">Are you sure you want to delete <br/> {{ $course->course->code }}?</h4>
                                                    <form action="{{ url('/staff/deleteCourseForStudent') }}" method="POST">
                                                        @csrf
                                                        <input name="student_course_id" type="hidden" value="{{$course->id}}">
                                                        <input type="hidden" name="level_id" value="{{ $academiclevel->id }}">
                                                        <input type="hidden" name="programme_id" value="{{ $programme->id }}">
                                                        <input type="hidden" name="semester" value="{{$semester}}">
                                                        <input type="hidden" name="academic_session" value="{{$academic_session}}">
                                                        <input type="hidden" name="programme_category_id" value="{{$programme_category_id}}">
                                                        <hr>
                                                        <button type="submit" id="submit-button" class="btn btn-danger w-100">Yes, Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light p-3 justify-content-center">

                                            </div>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->

                                <div id="edit{{$course->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 overflow-hidden">
                                            <div class="modal-header p-3">
                                                <h4 class="card-title mb-0">Edit Level</h4>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                    
                                            <div class="modal-body">
                                                <form action="{{ url('/staff/updateCourseForStudent') }}" method="post" enctype="multipart/form-data">
                                                    @csrf
                                                    <input name="student_course_id" type="hidden" value="{{$course->id}}">
                                                    <input type="hidden" name="level_id" value="{{ $academiclevel->id }}">
                                                    <input type="hidden" name="programme_id" value="{{ $programme->id }}">
                                                    <input type="hidden" name="semester" value="{{$semester}}">
                                                    <input type="hidden" name="academic_session" value="{{$academic_session}}">
                                                    <input type="hidden" name="programme_category_id" value="{{$programme_category_id}}">
                                                    
                                                    <div class="col-lg-12">
                                                        <div class="form-floating">
                                                            <select class="form-select" id="cstatus" name="status" aria-label="cstatus">
                                                                <option value="" selected>--Select--</option>
                                                                <option @if($course->status == "Required") selected @endif value="Required">Required</option>
                                                                <option @if($course->status == "Core") selected @endif value="Core">Core</option>
                                                                <option @if($course->status == "Elective") selected @endif value="Elective">Elective</option>
                                                            </select>
                                                            <label for="cstatus">Status</label>
                                                        </div>
                                                    </div>
                            
                                                    <div class="col-lg-12 mt-3">
                                                        <div class="form-floating">
                                                            <input type="text" class="form-control" max="6" name="credit_unit" id="credit_unit" value="{{ $course->credit_unit }}">
                                                            <label for="semester">Credit Unit</label>
                                                        </div>
                                                    </div>
                            
                                                    <hr>
                                                    <div class="text-end">
                                                        <button type="submit" id="submit-button" class="btn btn-primary">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->
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
<!-- end row -->
{{-- <form id="courseDetailForm{{ $loop->iteration }}" action="{{ url('/staff/courseDetail/'.$course->course->id) }}" method="get">
    @csrf
    <div class="input-group" style="display: flex; flex-wrap: nowrap;">
        <select id="programmeSelect{{ $loop->iteration }}" class="form-select select2 selectWithSearch" required style="flex-grow: 1;">
            <option value="" selected>Select Programme Category</option>
            @foreach($programmeCategories as $category)
                <option value="{{ $category->category }}">{{ $category->category }} Programme</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-outline-secondary shadow-none">Fetch Course Details</button>
    </div>
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
</script> --}}

@endif
@endsection
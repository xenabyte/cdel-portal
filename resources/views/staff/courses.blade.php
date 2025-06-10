@extends('staff.layout.dashboard')
@section('content')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Course(s)</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Course(s)</li>
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
                <h4 class="card-title mb-0 flex-grow-1">Course Allocated</h4>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <table id="buttons-datatables" class="table table-stripped table-bordered table-nowrap">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Course Code</th>
                            <th scope="col">Course Title</th>
                            <th scope="col">Acadmic Session</th>
                            <th scope="col">Programme Category</th>
                            <th scope="col">Course Unit</th>
                            <th scope="col">Status</th>
                            <th scope="col">Level</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staffCourses as $staffCourse)
                            @php
                                $course = $staffCourse->course;
                                $courseData = $staffCourse->courseData();
                            @endphp
                            @if($course && $courseData)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $course->code }}</td>
                                <td>{{ ucwords(strtolower($course->name)) }}</td>
                                 <td>{{ $staffCourse->academic_session }}</td>
                                <td>{{ $staffCourse->programmeCategory->category }}</td>
                                <td>{{ $courseData->credit_unit }}</td>
                                <td>{{ $courseData->status }}</td>
                                <td>{{ $courseData->level->level }}</td>
                                <td>
                                    <a href="{{ url('/staff/courseDetail/'.$course->id.'/'.$staffCourse->programmeCategory->category) }}" class="btn btn-primary">Fetch Course Details</a>
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table> 
            </div>
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>

{{-- <form id="courseDetailForm{{ $loop->iteration }}" action="{{ url('/staff/courseDetail/'.$course->id) }}" method="get">
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

@endsection

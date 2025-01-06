@extends('staff.layout.dashboard')
@php
$staff = Auth::guard('staff')->user();
$name = $staff->title.' '.$staff->lastname.' '.$staff->othernames;
$staffCourses = $staff->staffCourses;
@endphp
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
                <h4 class="card-title mb-0 flex-grow-1">Course Allocated  for {{ $pageGlobalData->sessionSetting->academic_session }} academic session</h4>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <table class="table table-stripped table-bordered table-nowrap">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Course Code</th>
                            <th scope="col">Course Title</th>
                            <th scope="col">Course Unit</th>
                            <th scope="col">Status</th>
                            <th scope="col">Enrolled Student Count</th>
                            <th scope="col">Level</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staffCourses->where('academic_session', $pageGlobalData->sessionSetting->academic_session) as $staffCourse)
                        @php
                            $courseData = $staffCourse->course->coursePerProgrammePerAcademicSession->where('academic_session', $pageGlobalData->sessionSetting->academic_session)->first();
                        @endphp
                        @if(!empty($courseData))
                        <tr>
                            <td scope="row"> {{ $loop->iteration }}</td>
                            <td>{{ $staffCourse->course->code}}</td>
                            <td>{{ ucwords(strtolower($staffCourse->course->name)) }}</td>
                            <td>{{ $courseData->credit_unit}}</td>
                            <td>{{ $courseData->status}}</td>
                            <td>{{ $courseData->registrations->where('academic_session', $pageGlobalData->sessionSetting->academic_session)->count()}}</td>
                            <td>{{ $courseData->level->level}}</td>
                            <td></td>
                            <td>
                                {{-- <a href="{{ url('/staff/courseDetail/'.$staffCourse->course->id) }}" class="btn btn-lg btn-primary">Course Details</a> --}}
                                <form id="courseDetailForm{{ $loop->iteration }}" action="{{ url('/staff/courseDetail/'.$staffCourse->course->id) }}" method="get">
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
                                </script>
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

@endsection

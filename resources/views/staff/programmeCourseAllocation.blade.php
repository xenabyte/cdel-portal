@extends('staff.layout.dashboard')
@php
    $staff = Auth::guard('staff')->user();
@endphp

@section('content')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Course Allocation</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Course Allocation</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->


@if(!empty($courses))

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center">
                <h4 class="card-title mb-0 flex-grow-1">Course Allocation {{ $pageGlobalData->sessionSetting->academic_session }} academic session</h4>
                <br/>
                <p class=""><strong>Programme:</strong> {{ $mainProgramme->name }}
                <br/><strong>Academic Session:</strong> {{ $pageGlobalData->sessionSetting->academic_session }}
                <br/><strong>Level:</strong> {{ $mainLevel->level }} Level</p>

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
                            <th scope="col">Lecturer</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($courses as $course)
                        <tr>
                            <td scope="row"> {{ $loop->iteration }}</td>
                            <td>{{$course->code}}</td>
                            <td>{{ucwords(strtolower($course->name)) }}</td>
                            <td>{{$course->credit_unit}} </td>
                            <td>{{$course->status}}</td>
                            <td>{{!empty($course->staff)?$course->staff->title.' '.$course->staff->lastname.' '.$course->staff->othernames : null }}</td>
                            <td>
                                <form action="{{ url('/staff/assignCourse') }}" method="POST">
                                    @csrf
                                    <div class="row g-3">
                                        <input type="hidden" name="course_id" value="{{ $course->id }}">
                                        <input type="hidden" name="level_id" value="{{ $mainLevel->id }}">
                                        <input type="hidden" name="semester" value="{{ $course->semester }}">
                                        <input type="hidden" name="programme_id" value="{{ $mainProgramme->id }}">
                                        <div class="col-lg-8">
                                            <div class="form-floating">
                                                <input type="text" id="staff" class="form-control" placeholder="Staff Id(eg. TAU/SSPF021)" name="staff_id" />
                                                <label for="staff">Staff ID (eg. TAU/SSPF/021)</label>
                                            </div>
                                        </div>
        
                                        <div class="col-lg-4">
                                            <div class="form-floating">
                                                <button type="submit" id="submit-button" class="btn btn-lg btn-primary">Assign Lecturer</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
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
@endif

@endsection

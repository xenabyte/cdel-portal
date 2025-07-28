@extends('staff.layout.dashboard')

@section('content')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Summer Course Registrations</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Summer Course Registrations</li>
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
                <h4 class="card-title mb-0 flex-grow-1">{{ $programmeCategory->category }} Students Summer Course Registrations for {{ $programmeCategory->academicSessionSetting->academic_session  }} Academic Session</h4>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>SN</th>
                            <th>Name</th>
                            <th>Matric Number</th>
                            <th>Level</th>
                            <th>Programme</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Registered Courses</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $student['name'] }}</td>
                                <td>{{ $student['matric_number'] }}</td>
                                <td>{{ $student['level'] }} Level</td>
                                <td>{{ $student['programme'] }}</td>
                                <td>{{ $student['email'] }}</td>
                                <td>{{ $student['phone_number'] }}</td>
                                <td>
                                    @if(count($student['courses']))
                                        <ul class="mb-0">
                                            @foreach($student['courses'] as $course)
                                                <li>{{ $course['course_code'] }} - {{ $course['course_name'] }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-muted">No Courses</span>
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

@endsection

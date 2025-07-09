@extends('staff.layout.dashboard')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Examination Attendance</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                        <li class="breadcrumb-item active">Examination Attendance</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->
    <a class="btn btn-secondary my-2" href="{{route('courseDetail', [$courseId, $programmeCategory])}}"><i class="mdi mdi-keyboard-backspace me-2"></i> Go back</a>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Staff </h4>
                </div><!-- end card header -->

                <div class="card-body table-responsive">
                    <h4 class="col-lg-9">Eligible Students to take {{$course->code}} Exam based on (≥ 75% Attendance + Full Payment)</h4>
                    <div class="col-lg-3">
                        <a href="{{ route('staff.export-authorized-students', ['courseId' => $courseId, 'programmeCategory' => $programmeCategory->category, 'academicSession' => str_replace('/', '-', $academicSession)]) }}"
                            class="btn btn-sm btn-outline-danger" target="_blank">Export PDF</a>
                    </div>
                    <!-- Bordered Tables -->
                    <table class="table table-bordered table-hover mt-2">
                        <thead>
                            <tr>
                                <th>S/No</th>
                                <th>Passport</th>
                                <th>Matric No</th>
                                <th>Full Name (Surname first)</th>
                                <th>Sex</th>
                                <th>Level</th>
                                <th>Faculty</th>
                                <th>Department</th>
                                <th>Programme</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $entry)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><img src="{{!empty($entry['student']->image) ? asset($entry['student']->image) : asset('assets/images/users/user-dummy-img.jpg')}}"
                                            alt="Passport" width="50"></td>
                                    <td>{{ $entry['student']->matric_number ?? 'N/A' }}</td>
                                    <td>{{ $entry['student']->applicant->lastname }}
                                        {{ $entry['student']->applicant->othernames }}</td>
                                    <td>{{ $entry['student']->applicant->gender }}</td>
                                    <td>{{ $entry['student']->academicLevel->level }}</td>
                                    <td>{{ $entry['student']->faculty->name }}</td>
                                    <td>{{ $entry['student']->department->name }}</td>
                                    <td>{{ $entry['student']->programme->award }}</td>

a

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">No authorized students found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div><!-- end card -->
        </div>
        <!-- end col -->
    </div>
    @if(Session::has('pdf_download_path'))
    <script>
        window.open("{{ Session::get('pdf_download_path') }}", "_blank");
    </script>
@endif

@endsection
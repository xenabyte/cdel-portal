@extends('staff.layout.dashboard')

@section('content')
<div class="container mt-4">
        <a class="btn btn-secondary my-2" href="{{route('courseDetail', [$courseId, $programmeCategory])}}"><i class="mdi mdi-keyboard-backspace me-2"></i> Go back</a>
    <div class="card">
        <div class="card-body">
            <div class="row my-4">
                <h4 class="col-lg-9">Eligible Students to take {{$course->code}} Exam based on (≥ 75% Attendance + Full Payment)</h4>

            <div class="col-lg-3">
                <a href="{{ route('staff.export-authorized-students', ['courseId' => $courseId, 'programmeCategory' => $programmeCategory, 'academicSession' => str_replace('/', '-', $academicSession), 'type' => 'csv']) }}" class="btn btn-sm btn-outline-primary">Export CSV</a>
                <a href="{{ route('staff.export-authorized-students', ['courseId' => $courseId, 'programmeCategory' => $programmeCategory, 'academicSession' => str_replace('/', '-', $academicSession), 'type' => 'excel']) }}" class="btn btn-sm btn-outline-success">Export Excel</a>
                <a href="{{ route('staff.export-authorized-students', ['courseId' => $courseId, 'programmeCategory' => $programmeCategory, 'academicSession' => str_replace('/', '-', $academicSession), 'type' => 'pdf']) }}" class="btn btn-sm btn-outline-danger">Export PDF</a>
            </div>
            </div>

            <table class="table table-bordered table-hover">
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
                            <td><img src="{{!empty($entry['student']->image) ? $entry['student']->image : asset('assets/images/users/user-dummy-img.jpg')}}" alt="Passport" width="50"></td>
                            <td>{{ $entry['student']->matric_number ?? 'N/A' }}</td>
                            <td>{{ $entry['student']->applicant->lastname }} {{ $entry['student']->applicant->othernames }}</td>
                            <td>{{ $entry['student']->applicant->gender }}</td>
                            <td>{{ $entry['student']->academicLevel->level }}</td>
                            <td>{{ $entry['student']->faculty->name }}</td>
                            <td>{{ $entry['student']->department->name }}</td>
                            <td>{{ $entry['student']->programme->award }}</td>



                        </tr>
                        @empty
            <tr>
                <td colspan="4">No authorized students found.</td>
            </tr>
        @endforelse
                </tbody>
            </table>

        </div>
    </div>
</div>
@endsection

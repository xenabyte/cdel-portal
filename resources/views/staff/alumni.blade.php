@extends('staff.layout.dashboard')

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Graduated Students (Alumni)</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Graduated Students (Alumni)</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Graduated Students (Alumni)</h4>
                <div class="flex-shrink-0">
                    <form action="{{ url('staff/alumni') }}" method="GET" class="d-flex">
                        <select name="academicSession" class="form-select me-2" onchange="this.form.submit()">
                            <option value="">Select Academic Session</option>
                            @foreach($academicSessions as $session)
                                <option value="{{ $session->academic_session }}" {{ request('academicSession') == $session->academic_session ? 'selected' : '' }}>
                                    {{ $session->academic_session }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </form>
                </div>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Support Code</th>
                            <th scope="col">CGPA</th>
                            <th scope="col">Name</th>
                            <th scope="col">Level</th>
                            <th scope="col">Passcode</th>
                            <th scope="col">Matric Number</th>
                            <th scope="col">Application Number</th>
                            <th scope="col">Programme</th>
                            <th scope="col">Email</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($alumni as $student)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td><span class="text-danger">#{{ $student->id }}</span></td>
                            <td><span class="text-primary">{{ $student->cgpa }}</span></td>
                            <td>{{ $student->applicant->lastname .' '. $student->applicant->othernames }}</td>
                            <td>{{ $student->academicLevel->level }} </td>
                            <td>{{ $student->passcode }} </td>
                            <td>{{ $student->matric_number }}</td>
                            <td>{{ $student->applicant->application_number }}</td>
                            <td>{{ $student->programme->name }}</td>
                            <td>{{ $student->email }} </td>
                            <td>
                                <a href="{{ url('staff/studentProfile/'.$student->slug) }}" class="btn btn-primary m-1"><i class="ri-user-6-fill"></i> View Student</a>
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
@endsection

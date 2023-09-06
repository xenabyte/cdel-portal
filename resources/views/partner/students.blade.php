@extends('partner.layout.dashboard')

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Admitted Students for {{ $pageGlobalData->sessionSetting->admission_session}} Admission Session</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">students-</li>
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
                <h4 class="card-title mb-0 flex-grow-1">Admitted Students for {{ $pageGlobalData->sessionSetting->admission_session}} Admission Session </h4>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Name</th>
                            <th scope="col">Programme</th>
                            <th scope="col">Email</th>
                            <th scope="col">Access Code</th>
                            <th scope="col">Phone Number</th>
                            <th scope="col">Academic Session</th>
                            <th scope="col">Admitted Date</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $student->applicant->lastname .' '. $student->applicant->othernames }}</td>
                            <td>{{ $student->applicant->programme->name }}</td>
                            <td>{{ $student->applicant->email }} </td>
                            <td>{{ $student->passcode }} </td>
                            <td>{{ $student->applicant->phone_number }} </td>
                            <td>{{ $student->academic_session }} </td>
                            <td>{{ $student->created_at }} </td>
                            <td>
                                <a href="{{ url('partner/student/'.$student->slug) }}" class="btn btn-primary m-1"><i class= "ri-user-6-fill"></i> View Student</a>
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
<!-- end row -


@endsection
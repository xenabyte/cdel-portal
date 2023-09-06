@extends('admin.layout.dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Applicants for {{ empty($session)?$pageGlobalData->sessionSetting->application_session : $session }} Application Session</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Applicants</li>
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
                <h4 class="card-title mb-0 flex-grow-1">Applicants for {{ empty($session)?$pageGlobalData->sessionSetting->application_session : $session }} Application Session </h4>
                <div class="flex-shrink-0">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#searchApplicant">Filter Applicants</button>
                </div>
            </div><!-- end card header -->

            <div class="card-body">
                <!-- Bordered Tables -->
                <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Name</th>
                            <th scope="col">Gender</th>
                            <th scope="col">Programme</th>
                            <th scope="col">Application Type</th>
                            <th scope="col">Email</th>
                            <th scope="col">Access Code</th>
                            <th scope="col">Phone Number</th>
                            <th scope="col">Academic Session</th>
                            <th scope="col">Application Status</th>
                            <th scope="col">Applied Date</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($applicants->where('status', '!=', 'Admitted') as $applicant)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $applicant->lastname .' '. $applicant->othernames }}</td>
                            <td>{{ $applicant->gender }}</td>
                            <td>{{ !empty($applicant->programme)?$applicant->programme->name:null }}</td>
                            <td>{{ $applicant->application_type }}</td>
                            <td>{{ $applicant->email }} </td>
                            <td>{{ $applicant->passcode }} </td>
                            <td>{{ $applicant->phone_number }} </td>
                            <td>{{ $applicant->academic_session }} </td>
                            <td>{{ ucwords($applicant->status) }} </td>
                            <td>{{ $applicant->created_at }} </td>
                            <td>
                                <a href="{{ url('admin/applicant/'.$applicant->slug) }}" class="btn btn-primary m-1"><i class= "ri-user-6-fill"></i> View Applicant</a>
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


<div id="searchApplicant" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Filter Applicants</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="{{ url('/admin/applicantWithSession') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="session" class="form-label">Session</label>
                        <input type="text" class="form-control" placeholder="2022/2023" name="session" id="session">
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@endsection

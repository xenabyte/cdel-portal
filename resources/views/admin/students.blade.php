@extends('admin.layout.dashboard')

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
                            <th scope="col">Application Number</th>
                            <th scope="col">Programme</th>
                            <th scope="col">Email</th>
                            <th scope="col">Access Code</th>
                            <th scope="col">Phone Number</th>
                            <th scope="col">Academic Session</th>
                            <th scope="col">Admitted Date</th>
                            <th scope="col">Admission Letter</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $student->applicant->lastname .' '. $student->applicant->othernames }}</td>
                            <td>{{ $student->applicant->application_number }}</td>
                            <td>{{ $student->programme->name }}</td>
                            <td>{{ $student->email }} </td>
                            <td>{{ $student->passcode }} </td>
                            <td>{{ $student->applicant->phone_number }} </td>
                            <td>{{ $student->academic_session }} </td>
                            <td>{{ $student->created_at }} </td>
                            <td>
                                <a href="{{ asset($student->admission_letter) }}" class="btn btn-danger m-1"> Download Admission Letter</a>
                            </td>
                            <td>
                                <a href="{{ url('admin/student/'.$student->slug) }}" class="btn btn-primary m-1"><i class= "ri-user-6-fill"></i> View Student</a>
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete{{$student->id}}" class="btn btn-danger"><i class="ri-delete-bin-5-line"></i> Reverse Admission</a>
                            </td>
                        </tr>

                        <div id="delete{{$student->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <div class="modal-body text-center p-5">
                                        <div class="text-end">
                                            <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="mt-2">
                                            <lord-icon src="https://cdn.lordicon.com/wwneckwc.json" trigger="hover" style="width:150px;height:150px">
                                            </lord-icon>
                                            <h4 class="mb-3 mt-4">Are you sure you want to reverse admission for <br/> {{ $student->applicant->lastname .' '. $student->applicant->othernames }}?</h4>
                                            <form action="{{ url('admin/manageAdmission') }}" method="POST">
                                                @csrf
                                                <input name="applicant_id" type="hidden" value="{{$student->user_id}}">
                                                <input name="status" type="hidden" value="reverse_admission" />
                                                <hr>
                                                <button type="submit" id="submit-button" class="btn btn-danger w-100">Yes, Reverse Admission</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light p-3 justify-content-center">

                                    </div>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->
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
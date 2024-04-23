@extends('admin.layout.dashboard')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Course Registrations</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Course Registrations</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="row">

    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="accordion" id="default-accordion-example">
                    <div class="accordion-item shadow">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed bg-info" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Students yet to do course registration
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#default-accordion-example">
                            <div class="accordion-body">
                                <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th scope="col">Id</th>
                                            <th scope="col">Student Name</th>
                                            <th scope="col">Matric Number</th>
                                            <th scope="col">Phone Number</th>
                                            <th scope="col">Programme</th>
                                            <th scope="col">Level</th>
                                            <th scope="col"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pendingStudents as $pendingStudent)
                                            <tr>
                                                <th scope="row">{{ $loop->iteration }}</th>
                                                <td>{{ $pendingStudent->applicant->lastname .' '. $pendingStudent->applicant->othernames}}</td>
                                                <td>{{ $pendingStudent->matric_number }}</td>
                                                <td>{{ $pendingStudent->applicant->phone_number }}</td>
                                                <td>{{ $pendingStudent->programme->name }}</td>
                                                <td>{{ $pendingStudent->academicLevel->level }} Level</td>
                                                <td>
                                                    <a href="{{ url('admin/studentProfile/'.$pendingStudent->slug) }}" class="btn btn-success m-1"><i class= "ri-user-6-fill"></i> View Student</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Course Registrations for {{ $pageGlobalData->sessionSetting->academic_session }} Academic session</h4>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Student Name</th>
                            <th scope="col">Matric Number</th>
                            <th scope="col">Phone Number</th>
                            <th scope="col">Programme</th>
                            <th scope="col">Level</th>
                            <th scope="col">Academic Session</th>
                            <th scope="col">Level Adviser Status</th>
                            <th scope="col">HOD Status</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($studentRegistrations as $studentRegistration)
                            @if($studentRegistration->student)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $studentRegistration->student->applicant->lastname .' '. $studentRegistration->student->applicant->othernames}}</td>
                                <td>{{ $studentRegistration->student->matric_number }}</td>
                                <td>{{ $studentRegistration->student->applicant->phone_number }}</td>
                                <td>{{ $studentRegistration->student->programme->name }}</td>
                                <td>{{ $studentRegistration->student->academicLevel->level }} Level</td>
                                <td>{{ $studentRegistration->academic_session }}</td>
                                <td><span class="badge badge-soft-{{ $studentRegistration->level_adviser_status == 1 ? 'success' : 'warning' }}">{{ $studentRegistration->level_adviser_status == 1 ? 'Approved' : 'Pending' }}</span></td>
                                <td><span class="badge badge-soft-{{ $studentRegistration->hod_status == 1 ? 'success' : 'warning' }}">{{ $studentRegistration->hod_status == 1 ? 'Approved' : 'Pending' }}</span></td>
                                <td>
                                    <a href="{{ url('admin/studentProfile/'.$studentRegistration->student->slug) }}" class="btn btn-success m-1"><i class= "ri-user-6-fill"></i> View Student</a>
                                    <a href="{{ asset($studentRegistration->file) }}" target="_blank" style="margin: 5px" class="btn btn-primary">View Registration</a>
                                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#approve{{$studentRegistration->id}}"> Approve</button>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#undo{{$studentRegistration->id}}"> Undo Registration</button>
                                    <form action="{{ url('/admin/genExamDocket') }}" method="POST">
                                        @csrf
                                        <input name="student_id" type="hidden" value="{{$studentRegistration->student->id}}">
                                        <hr>
                                        <button type="submit" id="submit-button" class="btn btn-warning w-100">Generate Exam Docket</button>
                                    </form>
                                </td>
                            </tr>

                            <div id="approve{{$studentRegistration->id}}" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-body text-center p-5">
                                            <div class="text-end">
                                                <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="mt-2">
                                                <lord-icon src="https://cdn.lordicon.com/tqywkdcz.json" trigger="hover" style="width:150px;height:150px">
                                                </lord-icon>
                                                <h4 class="mb-3 mt-4">Are you sure you want to approve registration for <br/> {{ $studentRegistration->student->applicant->lastname .' '. $studentRegistration->student->applicant->othernames }} <br> for {{  $studentRegistration->academic_session }} academic session?</h4>
                                                <form action="{{ url('/admin/approveReg') }}" method="POST">
                                                    @csrf
                                                    <input name="reg_id" type="hidden" value="{{$studentRegistration->id}}">
                                                    <input name="type" type="hidden" value="both">
                                                    <hr>
                                                    <button type="submit" id="submit-button" class="btn btn-primary w-100">Yes, Approve</button>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light p-3 justify-content-center">
                            
                                        </div>
                                    </div><!-- /.modal-content -->
                                </div><!-- /.modal-dialog -->
                            </div><!-- /.modal -->

                            <div id="undo{{$studentRegistration->id}}" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-body text-center p-5">
                                            <div class="text-end">
                                                <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="mt-2">
                                                <lord-icon src="https://cdn.lordicon.com/wwneckwc.json" trigger="hover" style="width:150px;height:150px">
                                                </lord-icon>
                                                <h4 class="mb-3 mt-4">Are you sure you want to undo course registration for <br/> {{ $studentRegistration->student->applicant->lastname .' '. $studentRegistration->student->applicant->othernames }} <br> for {{  $studentRegistration->academic_session }} academic session?</h4>
                                                <form action="{{ url('/admin/undoReg') }}" method="POST">
                                                    @csrf
                                                    <input name="reg_id" type="hidden" value="{{$studentRegistration->id}}">
                                                    <input name="type" type="hidden" value="both">
                                                    <hr>
                                                    <button type="submit" id="submit-button" class="btn btn-primary w-100">Yes, Undo Registration</button>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light p-3 justify-content-center">
                            
                                        </div>
                                    </div><!-- /.modal-content -->
                                </div><!-- /.modal-dialog -->
                            </div><!-- /.modal -->
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div><!-- end card -->
    </div>
</div>

@endsection
@extends('admin.layout.dashboard')
@php
use \App\Models\ProgrammeCategory;
@endphp
@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">{{ $programmeCategory->category }} Programme Course Registrations</h4>

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
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Select Academic Session </h4>
                </div><!-- end card header -->
                <form id="courseRegForm" action="{{ url('/admin/courseRegistrations/'.$programmeCategory->category) }}" method="get">
                    @csrf
                    <div class="input-group" style="display: flex; flex-wrap: nowrap;">
                        <select id="sessionSelect" class="form-select select2 selectWithSearch" aria-label="staff" required style="flex-grow: 1;">
                            <option value="" selected>Select Session</option>
                            @foreach($sessions as $session)
                                <option value="{{ str_replace('/', '-', $session->year) }}">{{ $session->year }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-outline-secondary shadow-none" style="white-space: nowrap;">Fetch</button>
                    </div>
                </form>

                <script>
                    document.getElementById('courseRegForm').addEventListener('submit', function(event) {
                        event.preventDefault(); // Prevent the form from submitting the usual way
                
                        var session = document.getElementById('sessionSelect').value;
                        if(session) {
                            // Append the selected session to the form's action URL
                            var formAction = this.action + '/' + session;
                            window.location.href = formAction; // Redirect to the new URL
                        } else {
                            alert('Please select a session');
                        }
                    });
                </script>
                
            </div>
        </div>
    </div><!-- end col -->


    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="accordion" id="default-accordion-example">
                    <div class="accordion-item shadow">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed bg-primary text-light" type="button" data-bs-toggle="collapse" data-bs-target="#downloadCourseRegs" aria-expanded="false" aria-controls="downloadCourseRegs">
                                Download Course Registrations
                            </button>
                        </h2>
                        <div id="downloadCourseRegs" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#default-accordion-example">
                            <div class="accordion-body">
                                <form action="{{ url('/admin/downloadStudentCourseRegistrations') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="programme_category_id" value="{{ $programmeCategory->id }}">
                                    <div class="row g-3">
                                        <div class="col-lg-12">
                                            <div class="form-floating">
                                                <select class="form-select" id="programme" name="programme_id" aria-label="programme">
                                                    <option value="" selected>--Select--</option>
                                                    @foreach($programmes as $programme)
                                                        <option value="{{ $programme->id }}">{{ $programme->name }}</option>
                                                    @endforeach
                                                </select>
                                                <label for="department">Programme</label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-lg-12">
                                            <div class="form-floating">
                                                <select class="form-select" id="level" name="level_id" aria-label="level">
                                                    <option value="" selected>--Select--</option>
                                                    @foreach($academicLevels as $academicLevel)
                                                        <option value="{{ $academicLevel->id }}">{{ $academicLevel->level }} Level</option>
                                                    @endforeach
                                                </select>
                                                <label for="level">Academic Level</label>
                                            </div>
                                        </div>
            
                                        <div class="col-lg-12">
                                            <div class="form-floating">
                                                <select class="form-select" id="session" name="academic_session" aria-label="Academic Session">
                                                    <option value="" selected>--Select--</option>
                                                    @foreach($sessions as $session)<option value="{{ $session->year }}">{{ $session->year }}</option>@endforeach
                                                </select>
                                                <label for="session">Academic Session</label>
                                            </div>
                                        </div>
            
                                        <button type="submit" class="btn btn-fill btn-primary btn-lg btn-block mb-5">Download Students Course Registrations</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="accordion" id="default-accordion-example">
                    <div class="accordion-item shadow">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed bg-info" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                {{ $programmeCategory->category }} Programme Students yet to do course registration
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
                                            <th scope="col">Faculty</th>
                                            <th scope="col">Level</th>
                                            <th scope="col"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pendingStudents as $pendingStudent)
                                            @if(!$pendingStudent->is_passed_out || !$pendingStudent->is_active)
                                            <tr>
                                                <th scope="row">{{ $loop->iteration }}</th>
                                                <td>{{ $pendingStudent->applicant->lastname .' '. $pendingStudent->applicant->othernames}}</td>
                                                <td>{{ $pendingStudent->matric_number }}</td>
                                                <td>{{ $pendingStudent->applicant->phone_number }}</td>
                                                <td>{{ $pendingStudent->programme->name }}</td>
                                                <td>{{ $pendingStudent->faculty->name }}
                                                <td>{{ $pendingStudent->academicLevel->level }} Level</td>
                                                <td>
                                                    <a href="{{ url('admin/studentProfile/'.$pendingStudent->slug) }}" class="btn btn-success m-1"><i class= "ri-user-6-fill"></i> View Student</a>
                                                </td>
                                            </tr>
                                            @endif
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
                <h4 class="card-title mb-0 flex-grow-1">{{ $programmeCategory->category }} Programme Course Registrations for {{ $academicSession }} Academic session</h4>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table id="buttons-datatables2" class="display table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Student Name</th>
                            <th scope="col">Matric Number</th>
                            <th scope="col">Phone Number</th>
                            <th scope="col">Faculty</th>
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
                                <td>{{ $studentRegistration->student->faculty->name }}</td>
                                <td>{{ $studentRegistration->student->programme->name }}</td>
                                <td>{{ $studentRegistration->level_id * 100 }} Level</td>
                                <td>{{ $studentRegistration->academic_session }}</td>
                                <td><span class="badge badge-soft-{{ $studentRegistration->level_adviser_status == 1 ? 'success' : 'warning' }}">{{ $studentRegistration->level_adviser_status == 1 ? 'Approved' : 'Pending' }}</span></td>
                                <td><span class="badge badge-soft-{{ $studentRegistration->hod_status == 1 ? 'success' : 'warning' }}">{{ $studentRegistration->hod_status == 1 ? 'Approved' : 'Pending' }}</span></td>
                                <td>
                                    <a href="{{ asset($studentRegistration->file) }}" target="_blank" style="margin: 5px" class="btn btn-primary">View Registration</a>
                                    <a href="{{ url('admin/studentProfile/'.$studentRegistration->student->slug) }}" class="btn btn-success m-1"><i class= "ri-user-6-fill"></i> View Student</a>

                                    {{-- <a href="{{ url('admin/studentProfile/'.$studentRegistration->student->slug) }}" class="btn btn-success m-1"><i class= "ri-user-6-fill"></i> View Student</a>
                                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#approve{{$studentRegistration->id}}"> Approve</button>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#undo{{$studentRegistration->id}}"> Undo Registration</button>
                                    <form action="{{ url('/admin/genExamDocket') }}" method="POST">
                                        @csrf
                                        <input name="student_id" type="hidden" value="{{$studentRegistration->student->id}}">
                                        <hr>
                                        <button type="submit" id="submit-button" class="btn btn-warning w-10">Generate Exam Docket</button>
                                    </form> --}}
                                </td>
                            </tr>

                            {{-- <div id="approve{{$studentRegistration->id}}" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
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
                                                <form action="{{ url('/admin/approveReg') }}" method="POST" style="display: inline-block">
                                                    @csrf
                                                    <input name="reg_id" type="hidden" value="{{$studentRegistration->id}}">
                                                    <input name="type" type="hidden" value="both">
                                                    <button type="submit" id="submit-button" class="btn btn-primary w-100">Yes, Approve</button>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light p-3 justify-content-center">
                            
                                        </div>
                                    </div><!-- /.modal-content -->
                                </div><!-- /.modal-dialog -->
                            </div><!-- /.modal --> --}}

                            {{-- <div id="undo{{$studentRegistration->id}}" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-body text-center p-5">
                                            <div class="text-end">
                                                <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="mt-2">
                                                <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="hover" style="width:150px;height:150px">
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
                            </div><!-- /.modal --> --}}
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div><!-- end card -->
    </div>
</div>

@endsection
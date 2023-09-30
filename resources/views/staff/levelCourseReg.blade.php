@extends('staff.layout.dashboard')
@php
    $staff = Auth::guard('staff')->user();
    $staffDeanRole = false;
    $staffSubDeanRole = false;
    $staffHODRole = false;
    $staffVCRole = false;
    $staffRegistrarRole = false;
    $staffHRRole = false;
    $staffLevelAdviserRole = false;
    $staffExamOfficerRole = false;
    $staffPublicRelationRole = false;
    $staffStudentCareRole = false;

    $notifications = $staff->notifications()->orderBy('created_at', 'desc')->get();
    
    
    foreach ($staff->staffRoles as $staffRole) {
        if (strtolower($staffRole->role->role) == 'dean') {
            $staffDeanRole = true;
        }
        if (strtolower($staffRole->role->role) == 'sub-dean') {
            $staffSubDeanRole = true;
        }
        if (strtolower($staffRole->role->role) == 'hod') {
            $staffHODRole = true;
        }
        if (strtolower($staffRole->role->role) == 'vice chancellor') {
            $staffVCRole = true;
        }
        if (strtolower($staffRole->role->role) == 'registrar') {
            $staffRegistrarRole = true;
        }
        if (strtolower($staffRole->role->role) == 'human resource') {
            $staffHRRole = true;
        }
        if (strtolower($staffRole->role->role) == 'level adviser') {
            $staffLevelAdviserRole = true;
        }
        if (strtolower($staffRole->role->role) == 'exam officer') {
            $staffExamOfficerRole = true;
        }
        if (strtolower($staffRole->role->role) == 'public relation') {
            $staffPublicRelationRole = true;
        }
        if (strtolower($staffRole->role->role) == 'student care') {
            $staffStudentCareRole = true;
        }
        
    }
@endphp
@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Course Registration</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Course Registration</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Course Registration </h4>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table class="display table table-stripped" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Student Name</th>
                            <th scope="col">Matric Number</th>
                            <th scope="col">Phone Number</th>
                            <th scope="col">Academic Session</th>
                            <th scope="col">Level Adviser Status</th>
                            <th scope="col">HOD Status</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($studentRegistrations as $studentRegistration)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $studentRegistration->student->applicant->lastname .' '. $studentRegistration->student->applicant->othernames}}</td>
                            <td>{{ $studentRegistration->student->matric_number }}</td>
                            <td>{{ $studentRegistration->student->applicant->phone_number }}</td>
                            <td>{{ $studentRegistration->academic_session }}</td>
                            <td><span class="badge badge-soft-{{ $studentRegistration->level_adviser_status == 1 ? 'success' : 'warning' }}">{{ $studentRegistration->level_adviser_status == 1 ? 'Approved' : 'Pending' }}</span></td>
                            <td><span class="badge badge-soft-{{ $studentRegistration->hod_status == 1 ? 'success' : 'warning' }}">{{ $studentRegistration->hod_status == 1 ? 'Approved' : 'Pending' }}</span></td>
                            <td>
                                <a href="{{ url('staff/studentProfile/'.$studentRegistration->student->slug) }}" class="btn btn-success m-1"><i class= "ri-user-6-fill"></i> View Student</a>
                                <a href="{{ asset($studentRegistration->file) }}" target="_blank" style="margin: 5px" class="btn btn-primary">View Registration</a>
                                @if((!$studentRegistration->level_adviser_status && $staffLevelAdviserRole) || (!$studentRegistration->hod_status && $staffHODRole))
                                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#approve{{$studentRegistration->id}}"> Approve</button>
                                @endif
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
                                            <form action="{{ url('/staff/approveReg') }}" method="POST">
                                                @csrf
                                                <input name="reg_id" type="hidden" value="{{$studentRegistration->id}}">
                                                <input name="staff_id" type="hidden" value="{{$staff->id}}">
                                                <input name="student_id" type="hidden" value="{{$studentRegistration->student_id}}">
                                                @if($staffLevelAdviserRole)
                                                <input name="type" type="hidden" value="level_adviser">
                                                @endif
                                                @if($staffHODRole)
                                                <input name="type" type="hidden" value="hod">
                                                @endif
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
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>

@endsection
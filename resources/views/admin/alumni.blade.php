@extends('admin.layout.dashboard')

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
                    <form action="{{ url('admin/alumni') }}" method="GET" class="d-flex">
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
                            <th scope="col">Final Clearance Status</th>
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
                                @if(strtolower($student->finalClearance) && $student->finalClearance->status == 'approved')
                                    <span class="badge bg-success p-2 rounded-pill">Cleared</span>
                                @else
                                    <span class="badge bg-danger p-2 rounded-pill">Not Yet Cleared</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ url('admin/studentProfile/'.$student->slug) }}" class="btn btn-primary m-1"><i class="ri-user-6-fill"></i> View Student</a>
                                @if(!empty($student->finalClearance))
                                    @if(!($student->finalClearance->file))
                                    <a href="{{ asset($student->finalClearance->file) }}" class="btn btn-info m-1"><i class="ri-download-cloud-2-fill"></i> Download Clearance</a>
                                    @endif

                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#manage{{$student->id}}" class="btn btn-info m-1"><i class="ri-edit-fill"></i> Manage Clearance</a>
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
<!-- end row -->

@foreach($alumni as $student)
    @if($student->finalClearance)
    <div id="manage{{$student->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 overflow-hidden">
                <div class="modal-header p-3">
                    <h4 class="card-title mb-0">View Student Clearance</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <hr>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 border-end">
                            <div class="card-body text-center">
                                <div class="avatar-md mb-3 mx-auto">
                                    <img src="{{empty($student->image)?asset('assets/images/users/user-dummy-img.jpg'): asset($student->image) }}" alt="" id="candidate-img" class="img-thumbnail rounded-circle shadow-none">
                                </div>
        
                                <h5 id="candidate-name" class="mb-0">{{ $student->applicant->lastname .' '. $student->applicant->othernames }}</h5>
                                <p id="candidate-position" class="text-muted">{{ $student->programme?$student->programme->name:null }}</p>
                                <p id="candidate-position" class="text-muted">Phone Number: {{ $student->applicant->phone_number }}</p>
                                <div class="vr"></div>
                                {!!  $student->finalClearance? $student->finalClearance->experience:null !!}
                            </div>
                        </div>
        
                        <div class="col-md-4 border-end ">
                            <div class="card-body p-0">
                                <div class="simplebar-mask">
                                    <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                                        <div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content">
                                            <div class="simplebar-content" style="padding: 16px;">
                                                <div class="acitivity-timeline acitivity-main">
                                                    <!-- HOD Activity -->
                                                    @if(!empty($student->finalClearance->hod_id) && $student->finalClearance->hod)
                                                    <div class="acitivity-item d-flex mb-3">
                                                        <div class="flex-shrink-0">
                                                            <img src="{{ $student->finalClearance->hod->image }}" alt="" class="avatar-xs rounded-circle acitivity-avatar shadow">
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h6 class="mb-1">{{ $student->finalClearance->hod->title.' '.$student->finalClearance->hod->lastname.' '.$student->finalClearance->hod->othernames }}</h6>
                                                            <p class="text-muted mb-2 fst-italic">{{ ucwords($student->finalClearance->hod_status) }}</p>
                                                            <small class="mb-0 text-muted">Comment: {!! strip_tags($student->finalClearance->hod_comment) !!}</small>
                                                        </div>
                                                    </div>
                                                    @endif
                    
                                                    <!-- Dean Activity -->
                                                    @if(!empty($student->finalClearance->dean_id) && $student->finalClearance->dean)
                                                    <div class="acitivity-item d-flex mb-3">
                                                        <div class="flex-shrink-0">
                                                            <img src="{{ $student->finalClearance->dean->image }}" alt="" class="avatar-xs rounded-circle acitivity-avatar shadow">
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h6 class="mb-1">{{ $student->finalClearance->dean->title.' '.$student->finalClearance->dean->lastname.' '.$student->finalClearance->dean->othernames }}</h6>
                                                            <p class="text-muted mb-2 fst-italic">{{ ucwords($student->finalClearance->dean_status) }}</p>
                                                            <small class="mb-0 text-muted">Comment: {!! strip_tags($student->finalClearance->dean_comment) !!}</small>
                                                        </div>
                                                    </div>
                                                    @endif
                    
                                                    <!-- Registrar Activity -->
                                                    @if(!empty($student->finalClearance->registrar_id) && $student->finalClearance->registrar)
                                                    <div class="acitivity-item d-flex mb-3">
                                                        <div class="flex-shrink-0">
                                                            <img src="{{ $student->finalClearance->registrar->image }}" alt="" class="avatar-xs rounded-circle acitivity-avatar shadow">
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h6 class="mb-1">{{ $student->finalClearance->registrar->title.' '.$student->finalClearance->registrar->lastname.' '.$student->finalClearance->registrar->othernames }}</h6>
                                                            <p class="text-muted mb-2 fst-italic">{{ ucwords($student->finalClearance->registrar_status) }}</p>
                                                            <small class="mb-0 text-muted">Comment: {!! strip_tags($student->finalClearance->registrar_comment) !!}</small>
                                                        </div>
                                                    </div>
                                                    @endif
                    
                                                    <!-- Bursary Activity -->
                                                    @if(!empty($student->finalClearance->bursary_id) && $student->finalClearance->bursary)
                                                    <div class="acitivity-item d-flex mb-3">
                                                        <div class="flex-shrink-0">
                                                            <img src="{{ $student->finalClearance->bursary->image }}" alt="" class="avatar-xs rounded-circle acitivity-avatar shadow">
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h6 class="mb-1">{{ $student->finalClearance->bursary->title.' '.$student->finalClearance->bursary->lastname.' '.$student->finalClearance->bursary->othernames }}</h6>
                                                            <p class="text-muted mb-2 fst-italic">{{ ucwords($student->finalClearance->bursary_status) }}</p>
                                                            <small class="mb-0 text-muted">Comment: {!! strip_tags($student->finalClearance->bursary_comment) !!}</small>
                                                        </div>
                                                    </div>
                                                    @endif
                    
                                                    <!-- Library Activity -->
                                                    @if(!empty($student->finalClearance->library_id) && $student->finalClearance->librarian)
                                                    <div class="acitivity-item d-flex mb-3">
                                                        <div class="flex-shrink-0">
                                                            <img src="{{ $student->finalClearance->librarian->image }}" alt="" class="avatar-xs rounded-circle acitivity-avatar shadow">
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h6 class="mb-1">{{ $student->finalClearance->librarian->title.' '.$student->finalClearance->librarian->lastname.' '.$student->finalClearance->librarian->othernames }}</h6>
                                                            <p class="text-muted mb-2 fst-italic">{{ ucwords($student->finalClearance->library_status) }}</p>
                                                            <small class="mb-0 text-muted">Comment: {!! strip_tags($student->finalClearance->library_comment) !!}</small>
                                                        </div>
                                                    </div>
                                                    @endif
                    
                                                    <!-- Student Care Dean Activity -->
                                                    @if(!empty($student->finalClearance->student_care_dean_id) && $student->finalClearance->student_care_dean)
                                                    <div class="acitivity-item d-flex mb-3">
                                                        <div class="flex-shrink-0">
                                                            <img src="{{ $student->finalClearance->student_care_dean->image }}" alt="" class="avatar-xs rounded-circle acitivity-avatar shadow">
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h6 class="mb-1">{{ $student->finalClearance->student_care_dean->title.' '.$student->finalClearance->student_care_dean->lastname.' '.$student->finalClearance->student_care_dean->othernames }}</h6>
                                                            <p class="text-muted mb-2 fst-italic">{{ ucwords($student->finalClearance->student_care_dean_status) }}</p>
                                                            <small class="mb-0 text-muted">Comment: {!! strip_tags($student->finalClearance->student_care_dean_comment) !!}</small>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
        

                        <div class="col-md-4">
                            <div class="card-body">

                                <form action="{{ url('admin/manageFinalYearStudentClearance') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="clearance_id" value="{{ $student->finalClearance->id }}">
                                    <button type="submit" id="submit-button" class="btn btn-lg btn-block btn-primary"> Clear Student</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    @endif
@endforeach
@endsection

@extends('staff.layout.dashboard')

@section('content')
 <!-- start page title -->
 <div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Pending Student Clearances</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Pending Student Clearances</li>
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
                <h4 class="card-title mb-0 flex-grow-1">Pending Student Clearances</h4>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Name</th>
                            <th scope="col">Matric Number</th>
                            <th scope="col">Programme</th>
                            <th scope="col">Email</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $studentsPendingClearance)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $studentsPendingClearance->student->applicant->lastname .' '. $studentsPendingClearance->student->applicant->othernames }}</td>
                            <td>{{ $studentsPendingClearance->student->matric_number }}</td>
                            <td>{{ $studentsPendingClearance->student->programme->name }}</td>
                            <td>{{ $studentsPendingClearance->student->email }} </td>
                            <td>
                                <a href="{{ url('staff/studentProfile/'.$studentsPendingClearance->student->slug) }}" class="btn btn-primary m-1"><i class="ri-user-6-fill"></i> View Student</a>
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#manage{{$studentsPendingClearance->id}}" class="btn btn-info m-1"><i class="ri-edit-fill"></i> Manage Clearance</a>
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

@foreach($students as $studentsPendingClearance)
<div id="manage{{$studentsPendingClearance->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
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
                                <img src="{{empty($studentsPendingClearance->student->image)?asset('assets/images/users/user-dummy-img.jpg'): asset($studentsPendingClearance->student->image) }}" alt="" id="candidate-img" class="img-thumbnail rounded-circle shadow-none">
                            </div>
    
                            <h5 id="candidate-name" class="mb-0">{{ $studentsPendingClearance->student->applicant->lastname .' '. $studentsPendingClearance->student->applicant->othernames }}</h5>
                            <p id="candidate-position" class="text-muted">{{ $studentsPendingClearance->student->programme?$studentsPendingClearance->student->programme->name:null }}</p>
                            <p id="candidate-position" class="text-muted">Phone Number: {{ $studentsPendingClearance->student->applicant->phone_number }}</p>
                            <div class="vr"></div>
                            {!! $studentsPendingClearance?$studentsPendingClearance->experience:null !!}
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
                                                @if(!empty($studentsPendingClearance->hod_id) && $studentsPendingClearance->hod)
                                                <div class="acitivity-item d-flex mb-3">
                                                    <div class="flex-shrink-0">
                                                        <img src="{{ $studentsPendingClearance->hod->image }}" alt="" class="avatar-xs rounded-circle acitivity-avatar shadow">
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h6 class="mb-1">{{ $studentsPendingClearance->hod->title.' '.$studentsPendingClearance->hod->lastname.' '.$studentsPendingClearance->hod->othernames }}</h6>
                                                        <p class="text-muted mb-2 fst-italic">{{ ucwords($studentsPendingClearance->hod_status) }}</p>
                                                        <small class="mb-0 text-muted">Comment: {!! strip_tags($studentsPendingClearance->hod_comment) !!}</small>
                                                    </div>
                                                </div>
                                                @endif
                
                                                <!-- Dean Activity -->
                                                @if(!empty($studentsPendingClearance->dean_id) && $studentsPendingClearance->dean)
                                                <div class="acitivity-item d-flex mb-3">
                                                    <div class="flex-shrink-0">
                                                        <img src="{{ $studentsPendingClearance->dean->image }}" alt="" class="avatar-xs rounded-circle acitivity-avatar shadow">
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h6 class="mb-1">{{ $studentsPendingClearance->dean->title.' '.$studentsPendingClearance->dean->lastname.' '.$studentsPendingClearance->dean->othernames }}</h6>
                                                        <p class="text-muted mb-2 fst-italic">{{ ucwords($studentsPendingClearance->dean_status) }}</p>
                                                        <small class="mb-0 text-muted">Comment: {!! strip_tags($studentsPendingClearance->dean_comment) !!}</small>
                                                    </div>
                                                </div>
                                                @endif
                
                                                <!-- Registrar Activity -->
                                                @if(!empty($studentsPendingClearance->registrar_id) && $studentsPendingClearance->registrar)
                                                <div class="acitivity-item d-flex mb-3">
                                                    <div class="flex-shrink-0">
                                                        <img src="{{ $studentsPendingClearance->registrar->image }}" alt="" class="avatar-xs rounded-circle acitivity-avatar shadow">
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h6 class="mb-1">{{ $studentsPendingClearance->registrar->title.' '.$studentsPendingClearance->registrar->lastname.' '.$studentsPendingClearance->registrar->othernames }}</h6>
                                                        <p class="text-muted mb-2 fst-italic">{{ ucwords($studentsPendingClearance->registrar_status) }}</p>
                                                        <small class="mb-0 text-muted">Comment: {!! strip_tags($studentsPendingClearance->registrar_comment) !!}</small>
                                                    </div>
                                                </div>
                                                @endif
                
                                                <!-- Bursary Activity -->
                                                @if(!empty($studentsPendingClearance->bursary_id) && $studentsPendingClearance->bursary)
                                                <div class="acitivity-item d-flex mb-3">
                                                    <div class="flex-shrink-0">
                                                        <img src="{{ $studentsPendingClearance->bursary->image }}" alt="" class="avatar-xs rounded-circle acitivity-avatar shadow">
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h6 class="mb-1">{{ $studentsPendingClearance->bursary->title.' '.$studentsPendingClearance->bursary->lastname.' '.$studentsPendingClearance->bursary->othernames }}</h6>
                                                        <p class="text-muted mb-2 fst-italic">{{ ucwords($studentsPendingClearance->bursary_status) }}</p>
                                                        <small class="mb-0 text-muted">Comment: {!! strip_tags($studentsPendingClearance->bursary_comment) !!}</small>
                                                    </div>
                                                </div>
                                                @endif
                
                                                <!-- Library Activity -->
                                                @if(!empty($studentsPendingClearance->library_id) && $studentsPendingClearance->librarian)
                                                <div class="acitivity-item d-flex mb-3">
                                                    <div class="flex-shrink-0">
                                                        <img src="{{ $studentsPendingClearance->librarian->image }}" alt="" class="avatar-xs rounded-circle acitivity-avatar shadow">
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h6 class="mb-1">{{ $studentsPendingClearance->librarian->title.' '.$studentsPendingClearance->librarian->lastname.' '.$studentsPendingClearance->librarian->othernames }}</h6>
                                                        <p class="text-muted mb-2 fst-italic">{{ ucwords($studentsPendingClearance->library_status) }}</p>
                                                        <small class="mb-0 text-muted">Comment: {!! strip_tags($studentsPendingClearance->library_comment) !!}</small>
                                                    </div>
                                                </div>
                                                @endif
                
                                                <!-- Student Care Dean Activity -->
                                                @if(!empty($studentsPendingClearance->student_care_dean_id) && $studentsPendingClearance->student_care_dean)
                                                <div class="acitivity-item d-flex mb-3">
                                                    <div class="flex-shrink-0">
                                                        <img src="{{ $studentsPendingClearance->student_care_dean->image }}" alt="" class="avatar-xs rounded-circle acitivity-avatar shadow">
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h6 class="mb-1">{{ $studentsPendingClearance->student_care_dean->title.' '.$studentsPendingClearance->student_care_dean->lastname.' '.$studentsPendingClearance->student_care_dean->othernames }}</h6>
                                                        <p class="text-muted mb-2 fst-italic">{{ ucwords($studentsPendingClearance->student_care_dean_status) }}</p>
                                                        <small class="mb-0 text-muted">Comment: {!! strip_tags($studentsPendingClearance->student_care_dean_comment) !!}</small>
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
                            @php
                                // Determine the role of the staff
                                $staff = Auth::guard('staff')->user();
                                $roleStatus = null;

                                if ($staff->id == $studentsPendingClearance->hod_id) {
                                    $roleStatus = $studentsPendingClearance->hod_status;
                                } elseif ($staff->id == $studentsPendingClearance->dean_id) {
                                    $roleStatus = $studentsPendingClearance->dean_status;
                                } elseif ($staff->id == $studentsPendingClearance->student_care_dean_id) {
                                    $roleStatus = $studentsPendingClearance->student_care_dean_status;
                                } elseif ($staff->id == $studentsPendingClearance->registrar_id) {
                                    $roleStatus = $studentsPendingClearance->registrar_status;
                                } elseif ($staff->id == $studentsPendingClearance->bursary_id) {
                                    $roleStatus = $studentsPendingClearance->bursary_status;
                                } elseif ($staff->id == $studentsPendingClearance->library_id) {
                                    $roleStatus = $studentsPendingClearance->library_status;
                                }
                            @endphp
                            @if($roleStatus === null || $roleStatus !== 'approved')
                            <form action="{{ url('staff/manageFinalYearStudentClearance') }}" method="POST">
                                @csrf
                                <input type="hidden" name="clearance_id" value="{{ $studentsPendingClearance->id }}">

                                <div class="mb-3">
                                    <label for="comment" class="form-label">Comment</label>
                                    <textarea class="form-control ckeditor" name="comment" id="comment"></textarea>
                                </div>
            
                                <div class="mb-3">
                                    <label for="role" class="form-label">Select Option</label>
                                    <select class="form-select" aria-label="role" name="status" required>
                                        <option selected value= "">Select Option </option>
                                        <option value="approved">Approve</option>
                                        <option value="declined">Decline</option>
                                    </select>
                                </div>
    
                                <hr>
                                <button type="submit" id="submit-button" class="btn btn-lg btn-primary"> Submit</button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endforeach


@endsection

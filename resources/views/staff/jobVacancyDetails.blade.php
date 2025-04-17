@extends('staff.layout.dashboard')
@php
    use \App\Models\ResultApprovalStatus;
@endphp

@section('content')
 <!-- start page title -->
 <div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Job Details</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Job Details</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card mt-n4 mx-n4">
            <div class="bg-soft-primary">
                <div class="card-body pb-0 px-4">
                    <div class="row mb-3">
                        <div class="col-md">
                            <div class="row align-items-center g-3">
                                <div class="col-md-auto">
                                    <div class="avatar-md">
                                        <div class="avatar-title bg-white rounded-circle">
                                            <img src="{{ asset('assets/images/career1.png') }}" alt="" class="img-thumbnail rounded-circle avatar-md">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md">
                                    <div>
                                        <h4 class="fw-bold">{{ $jobVacancy->title}}</h4>
                                        <div class="hstack gap-3 flex-wrap">
                                            <div><i class="ri-building-line align-bottom me-1"></i> {{ env('SCHOOL_NAME') }} | {{ $jobVacancy->type }} | CGPA: {{ $jobVacancy->cgpa }} | Deadline Date: {{ date('F j, Y', strtotime($jobVacancy->application_deadline)) }} </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-auto">
                            <div class="hstack gap-1 flex-wrap">
                            </div>
                        </div>
                    </div>

                    <ul class="nav nav-tabs-custom border-bottom-0" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active fw-semibold" data-bs-toggle="tab" href="#project-overview" role="tab">
                                Overview
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- end card body -->
            </div>
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->
</div>

<!-- end row -->
<div class="row">
    <div class="col-lg-12">
        <div class="tab-content text-muted">
            <div class="tab-pane fade show active" id="project-overview" role="tabpanel">
                <div class="row">
                    <div class="col-xl-5 col-lg-5">
                        <div class="card">
                            <div class="card-body">
                                <div class="text-muted">
                                    <h6 class="mb-3 fw-semibold text-uppercase">Overview</h6>
                                    <hr>
                                    {!! $jobVacancy->description  !!}
                                </div>
                                <div class="text-muted">
                                    <h6 class="mb-3 fw-semibold text-uppercase">Requirements</h6>
                                    <hr>
                                    {!! $jobVacancy->requirements  !!}
                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->

                    </div>
                    <!-- ene col -->
                    <div class="col-xl-7 col-lg-7">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-header align-items-center d-flex">
                                    <h4 class="card-title mb-0 flex-grow-1">Applicants</h4>
                                    <div class="flex-shrink-0">
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateStatusModal">Change Status for Selected</button>
                                    </div>
                                </div><!-- end card header -->
                                <div class="table-responsive mt-3">
                                   <!-- Form to select applicants and update their status -->
                                    <form action="{{ url('staff/updateApplicantStatus') }}" method="POST">
                                        @csrf
                                        <input name="job_id" type="hidden" value="{{$jobVacancy->id}}">

                                        <table id="fixed-header" class="table table-borderedless table-responsive nowrap table-striped align-middle" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th scope="col">
                                                        <input type="checkbox" id="select-all" /> <!-- Checkbox to select all -->
                                                    </th>
                                                    <th scope="col">Id</th>
                                                    <th scope="col">Name</th>
                                                    <th scope="col">Status</th>
                                                    <th scope="col">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($jobVacancy->applications as $applicant)
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" name="selected_applicants[]" value="{{ $applicant->id }}" class="applicant-checkbox">
                                                    </td>
                                                    <th scope="row">{{ $loop->iteration }}</th>
                                                    <td>
                                                        @if($applicant->workStudyApplicant)
                                                            {{ $applicant->workStudyApplicant->applicant->lastname.' '.$applicant->workStudyApplicant->applicant->othernames }} 
                                                        @else
                                                            {{ $applicant->jobApplicant->lastname.' '.$applicant->jobApplicant->othernames }} 
                                                        @endif
                                                    </td>
                                                    <td><span class="badge badge-soft-{{ $applicant->status == 'approved' ? 'success' : 'info' }}">{{ ucwords(str_replace('_', ' ', $applicant->status)) }}</span></td>
                                                    <td>
                                                        <div class="hstack gap-3 fs-15">
                                                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#viewApplicant{{$applicant->id}}" class="link-primary"><i class="ri-eye-fill"></i></a>
                                                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#manage{{$applicant->id}}" class="link-muted"><i class="ri-edit-circle-fill"></i></a>
                                                            @if(!empty($applicant->appointment_letter))
                                                            <a href="{{ asset($applicant->appointment_letter) }}" class="btn btn-danger m-1"> Download Appointment Letter</a>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                   
                                        <div id="updateStatusModal" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
                                            <!-- Fullscreen Modals -->
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content border-0 overflow-hidden">
                                                    <div class="modal-header p-3">
                                                        <h4 class="card-title mb-0">Update Status for Selected Applicants</h4>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                        
                                                    <div class="modal-body">
                                                        
                                                        <div class="mb-3">
                                                            <label for="status" class="form-label">Status</label>
                                                            <select class="form-select" aria-label="role" name="status" required>
                                                                <option value="">Select Status</option>
                                                                <option value="request_interview">Request Interview</option>
                                                                <option value="declined">Declined</option>
                                                                <option value="approved">Approved</option>
                                                            </select>
                                                        </div>
                                                    
                                                        <div class="mb-3">
                                                            <label for="message">Message for Applicants:</label>
                                                            <textarea name="message" id="message" class="form-control ckeditor" rows="4" placeholder="Enter message for applicants"></textarea>
                                                        </div>

                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary">Update Applicants</button>
                                                    </div>
                                                </div><!-- /.modal-content -->
                                            </div><!-- /.modal-dialog -->
                                        </div><!-- /.modal -->

                                    </form>    
                                    
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->

                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>
        </div>
    </div>
    <!-- end col -->
</div>
<!-- end row -->

@foreach($jobVacancy->applications as $applicant)
<!-- Modal Blur -->
<div id="viewApplicant{{ $applicant->id }}" class="modal fade zoomIn" tabindex="-1" aria-labelledby="zoomInModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered  modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="zoomInModalLabel">Applicant Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <hr>
            </div>
            <div class="modal-body border-top border-top-dashed">
                @if($applicant->workStudyApplicant)
                    @php
                        $student = $applicant->workStudyApplicant;
                        $failedCourses = $student->registeredCourses()->where('grade', 'F')->where('re_reg', null)->get();                        
                        $studentAdvisoryData = (object) $student->getAcademicAdvisory();
                    @endphp

                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-sm me-3 flex-shrink-0">
                            <div class="avatar-title bg-info-subtle rounded">
                                <img src="{{ asset($student->image) }}" alt="" class="avatar-xs">
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="fs-15 fw-semibold mb-0">{{ $student->applicant->lastname .' '. $student->applicant->othernames }}</h5>
                            <p class="text-muted mb-2">{{ $student->programme->name.' | '.($student->level_id * 100).' Level | '.$student->cgpa }}</p>
                        </div>
                    </div>
                    <div class="row border-top border-top-dashed border bottom border-bottom-dashed">
                        <div class="col-md-5">
                            <div class="mt-4">
                                <p class="text-muted">{{ $student->programme->name }} <br>
                                    <strong>Matric Number:</strong> {{ $student->matric_number }}<br>
                                    <strong>Wifi Username:</strong> {{ $student->bandwidth_username }}<br>
                                    <strong>Email:</strong> {{ $student->email }}<br>
                                    <strong>Phone Number:</strong> {{ $student->applicant->phone_number }}<br>
                                    <strong>Address:</strong> {{ $student->applicant->address }}<br>
                                    @if(env('WALLET_STATUS'))<a class="dropdown-item" href="#"><i class="mdi mdi-wallet text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Balance : <b>₦{{ number_format($student->amount_balance/100, 2) }}</b></span></a>@endif
                                </p>
                                <p class="text-muted border-top border-top-dashed pt-2">
                                    <strong>Programme Category:</strong> {{ $student->programmeCategory->category }} Programme<br>
                                    <strong>Department:</strong> {{ $student->department->name }}<br>
                                    <strong>Faculty:</strong> {{ $student->faculty->name }}<br>
                                    <strong>Jamb Reg. Number:</strong> {{ $student->applicant->jamb_reg_no }} <br>
                                    <strong>Academic Level:</strong> <span class="text-primary">{{ $student->level_id * 100 }} Level</span><br>
                                    <strong>Academic session:</strong> {{ $student->academic_session }}</span>
                                    <br>
                                    @if($student->level_id >= $student->programme->duration && !$student->is_passed_out)
                                    <span class="text-warning"><strong>Graduating Set</strong></span> <br>
                                    @endif
                                    <strong>Support Code:</strong> <span class="text-danger">{{ $student->applicant->id }}-ST{{ sprintf("%03d", $student->id) }}</span> 
                                </p>
                                <p class="text-muted border-top border-top-dashed pt-2">
                                    <strong>CGPA:</strong> {{ $student->cgpa }} <br>
                                    <strong>Class:</strong> {{ $student->degree_class }}<br>
                                </p>
                                <p class="text-muted border-top border-top-dashed pt-2 text-start">
                                    @if($failedCourses->count() > 0)<strong class="text-danger">Failed Courses:</strong> <span class="text-danger">@foreach($failedCourses as $failedCourse) {{ $failedCourse->course_code.',' }} @endforeach</span> @endif <br>
                                    <strong>Promotion Eligibility:</strong> {{ $studentAdvisoryData->promotion_eligible?'You are eligible to promote':'You are not eligible to promote' }}<br>
                                    <strong>Promotion Message:</strong> {{ $studentAdvisoryData->promotion_message }}<br>
                                    <strong>GPA Trend:</strong> {{ $studentAdvisoryData->trajectory_analysis['cgpa_trend'] }}<br>
                                    <strong>CGPA Trajectory Analysis:</strong> {{ $studentAdvisoryData->trajectory_analysis['academic_risk'] }}<br>
                                    <strong>Course Strength:</strong> @foreach($studentAdvisoryData->trajectory_analysis['strengths'] as $strength) {{ $strength.', ' }} @endforeach<br>
                                    <strong>Course Weakness:</strong> @foreach($studentAdvisoryData->trajectory_analysis['weaknesses'] as $weakness) {{ $weakness.', ' }} @endforeach<br>
                                    <strong>Tips:</strong> @foreach($studentAdvisoryData->trajectory_analysis['tips'] as $tips) {{ $tips }} @endforeach<br>
                                </p>
                            </div>
                        </div>
                    </div>                
                @else
                    @php
                        $worker = $applicant->jobApplicant;
                    @endphp

                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-sm me-3 flex-shrink-0">
                            <div class="avatar-title bg-info-subtle rounded">
                                <img src="{{ asset($worker->image) }}" alt="" class="avatar-xs">
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="fs-15 fw-semibold mb-0">{{ $worker->lastname .' '. $worker->othernames }}</h5>
                            <p class="text-muted mb-2">{{ $worker->email.' | '.$worker->phone_number }}</p>
                        </div>
                    </div>
                    <p class="text-muted pb-1 border-top border-top-dashed">{!! $worker->profile->biodata !!}</p>
                    <p class="text-muted pb-1 border-top border-top-dashed">{!! $worker->profile->education_history !!}</p>
                    <p class="text-muted pb-1 border-top border-top-dashed">{!! $worker->profile->professional_information !!}</p>
                    <p class="text-muted pb-1 border-top border-top-dashed">{!! $worker->profile->publications !!}</p>

                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="manage{{ $applicant->id }}" tabindex="-1" aria-labelledby="manage{{ $applicant->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Appointment Letter</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <hr>
                <form action="{{ url('staff/uploadApplicantAppointmentLetter') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input name="applicant_id" type="hidden" value="{{$applicant->id}}">

                    <div class="mb-3">
                        <label for="appointment_letter" class="form-label">Upload Appointment Letter (Optional):</label>
                        <input type="file" name="appointment_letter" id="appointment_letter" class="form-control" accept=".pdf,.doc,.docx">
                    </div>
                    
                    <button type="submit" name="response" value="approved" class="btn btn-success">
                       Upload
                    </button>

                </form>
            </div>
        </div>
    </div>
</div>

@endforeach
<script>
    // Select all checkboxes
    document.getElementById('select-all').addEventListener('click', function(event) {
        const checkboxes = document.querySelectorAll('.applicant-checkbox');
        checkboxes.forEach(checkbox => checkbox.checked = event.target.checked);
    });
</script>
@endsection

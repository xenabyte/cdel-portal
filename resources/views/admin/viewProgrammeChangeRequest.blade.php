@extends('admin.layout.dashboard')
@php
use \App\Models\ResultApprovalStatus;

$student = $programmeChangeRequest->student;

$qrcode = 'https://quickchart.io/chart?chs=300x300&cht=qr&chl='.env('APP_URL').'/studentDetails/'.$student->slug;
$name = $student->applicant->lastname.' '.$student->applicant->othernames;
$eligibleProgrammes = $student->getQualifiedTransferProgrammes();
$studentAdvisoryData = (object) $student->getAcademicAdvisory();
$failedCourses = $student->registeredCourses()->where('grade', 'F')->where('re_reg', null)->where('result_approval_id', ResultApprovalStatus::getApprovalStatusId(ResultApprovalStatus::SENATE_APPROVED))->get();


$stage = 0;


@endphp
@section('content')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Intra Transfer Application</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Intra Transfer Application</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-lg-12">
        <div class="card mt-n4 mx-n4">
            <div class="bg-soft-info">
                <div class="card-body pb-0 px-4">
                    <div class="row mb-3">
                        <div class="col-md">
                            <div class="row align-items-center g-3">
                                <div class="col-md-auto">
                                    <div class="avatar-md">
                                        <div class="avatar-title bg-white rounded-circle">
                                            <img src="{{ !empty($student->image) ? $student->image : asset('assets/images/users/user-dummy-img.jpg') }}" alt="" class="img-thumbnail rounded-circle avatar-md">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md">
                                    <div>
                                        <h4 class="fw-bold">{{ $student->applicant->lastname .' '. $student->applicant->othernames }}</h4>
                                        <div class="hstack gap-3 flex-wrap">
                                            <div><i class="ri-building-line align-bottom me-1"></i> {{  $student->academic_status }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-auto">
                            <div class="hstack gap-1 flex-wrap">
                                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#manageApplication"> Manage Application</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end card body -->
            </div>
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->
</div>

<div class="row">
    <div class="col-xl-4">
        <div class="card card-height-100">
            <div class="card-body">
                <div>
                    <div class="flex-shrink-0 avatar-md mx-auto">
                        <div class="avatar-title bg-light rounded">
                            <img src="{{empty($student->image)?asset('assets/images/users/user-dummy-img.jpg'):asset($student->image)}}" alt="" height="50" />
                        </div>
                    </div>
                    <div class="mt-4 text-center">
                        <h5 class="mb-1">{{$name}}</h5>
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
                            <strong>Promotion Eligibility:</strong> {{ is_null($student->cgpa) || $student->cgpa == 0 ? 'You are a fresh student; promotion eligibility will be determined after your first semester.' : ($studentAdvisoryData->promotion_eligible ? 'You are eligible to promote.' : 'You are not eligible to promote.') }} <br>
                            <strong>Promotion Message:</strong> {{ $studentAdvisoryData->promotion_message }}<br>
                            <strong>GPA Trend:</strong> {{ $studentAdvisoryData->trajectory_analysis['cgpa_trend'] }}<br>
                            <strong>CGPA Trajectory Analysis:</strong> {{ $studentAdvisoryData->trajectory_analysis['academic_risk'] }}<br>
                            <strong>Course Strength:</strong> @foreach($studentAdvisoryData->trajectory_analysis['strengths'] as $strength) {{ $strength.', ' }} @endforeach<br>
                            <strong>Course Weakness:</strong> @foreach($studentAdvisoryData->trajectory_analysis['weaknesses'] as $weakness) {{ $weakness.', ' }} @endforeach<br>
                            <strong>Tips:</strong> @foreach($studentAdvisoryData->trajectory_analysis['tips'] as $tips) {{ $tips }} @endforeach<br>
                        </p>
                    </div>

                    <div class="border-top border-top-dashed mb-3">
                        <div class="avatar-title bg-light rounded">
                            <img src="{{ $qrcode }}" style="border: 1px solid black;">
                        </div>
                    </div>
                    @if(!empty($student->applicant->guardian))
                    <div class="card-body border-top border-top-dashed p-4">
                        <div>
                            <h6 class="text-muted text-uppercase fw-semibold mb-4">Guardian Info</h6>
                            <div class="table-responsive">
                                <table class="table mb-0 table-borderless">
                                    <tbody>
                                        <tr>
                                            <th><span class="fw-medium">SN</span></th>
                                            <td class="text-danger">#{{ $student->applicant->guardian->id }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="fw-medium">Name</span></th>
                                            <td>{{ $student->applicant->guardian->name }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="fw-medium">Email</span></th>
                                            <td>{{ $student->applicant->guardian->email }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="fw-medium">Contact No.</span></th>
                                            <td>{{ $student->applicant->guardian->phone_number }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="fw-medium">Address</span></th>
                                            <td>{!! $student->applicant->guardian->address !!}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                </div>
            </div>
        </div>

        
    </div>
    <!--end col-->

    <div class="col-xl-8">
        <div class="card card-height-100">
            <div class="card-header border-bottom-dashed align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Programme Change Approval Activities</h4>
            </div><!-- end cardheader -->
    
            <div class="card-body p-3">
                @if(is_null($programmeChangeRequest->new_programme_id))
                    {{-- Button to trigger modal --}}
                    <div class="text-center">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignNewProgrammeModal">
                            Apply For New Programme
                        </button>
                    </div>
                @else
                    {{-- Approval Timeline --}}
                    <div data-simplebar style="max-height: 500px;">
                        <div class="acitivity-timeline acitivity-main">
                            @php $stage = 0; @endphp
    
                            <div class="acitivity-item d-flex mb-3">
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-2 ms-2">- Programme change request submitted</h6>
                                    <p class="px-3"><strong>Reason:</strong> {{ $programmeChangeRequest->reason }}</p>
                                </div>
                            </div>
                            @php $stage = 1; @endphp
                            
                            {{-- HOD (Old Programme) Approval --}}
                            @if($stage >= 1)
                            <div class="acitivity-item d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <img src="{{ optional($programmeChangeRequest->oldHod)->image }}" alt="" class="avatar-xs rounded-circle acitivity-avatar shadow">
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-2">
                                        - {{ $programmeChangeRequest->hod_old_approved_at ? 'HOD (Old Programme) Approved' : 'Waiting for HOD (Old Programme) approval' }}
                                    </h6>
                                    <p class="mb-0 text-muted">By: {{ $programmeChangeRequest->oldHod->title.' '.$programmeChangeRequest->oldHod->lastname.' '.$programmeChangeRequest->oldHod->othernames }}</p>
                                    @if($programmeChangeRequest->hod_old_approved_at)
                                        <p class="text-muted">Approved on: {{ \Carbon\Carbon::parse($programmeChangeRequest->hod_old_approved_at)->format('d M Y') }}</p>
                                    @endif
                                </div>
                            </div>
                            @php $stage = $programmeChangeRequest->hod_old_approved_at ? 2 : $stage; @endphp
                            @endif
            
                            {{-- Dean (Old Programme) Approval --}}
                            @if($stage >= 2)
                            <div class="acitivity-item d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <img src="{{ optional($programmeChangeRequest->oldDean)->image }}" alt="" class="avatar-xs rounded-circle acitivity-avatar shadow">
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-2">
                                        - {{ $programmeChangeRequest->dean_old_approved_at ? 'Dean (Old Programme) Approved' : 'Waiting for Dean (Old Programme) approval' }}
                                    </h6>
                                    <p class="mb-0 text-muted">By: {{ $programmeChangeRequest->oldDean->title.' '.$programmeChangeRequest->oldDean->lastname.' '.$programmeChangeRequest->oldDean->othernames }}</p>
                                    @if($programmeChangeRequest->dean_old_approved_at)
                                        <p class="text-muted">Approved on: {{ \Carbon\Carbon::parse($programmeChangeRequest->dean_old_approved_at)->format('d M Y') }}</p>
                                    @endif
                                </div>
                            </div>
                            @php $stage = $programmeChangeRequest->dean_old_approved_at ? 3 : $stage; @endphp
                            @endif
            
                            {{-- HOD (New Programme) Approval --}}
                            @if($stage >= 3)
                            <div class="acitivity-item d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <img src="{{ optional($programmeChangeRequest->newHod)->image }}" alt="" class="avatar-xs rounded-circle acitivity-avatar shadow">
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-2">
                                        - {{ $programmeChangeRequest->hod_new_approved_at ? 'HOD (New Programme) Approved' : 'Waiting for HOD (New Programme) approval' }}
                                    </h6>
                                    <p class="mb-0 text-muted">By: {{ $programmeChangeRequest->newHod->title.' '.$programmeChangeRequest->newHod->lastname.' '.$programmeChangeRequest->newHod->othernames }}</p>
                                    @if($programmeChangeRequest->hod_new_approved_at)
                                        <p class="text-muted">Approved on: {{ \Carbon\Carbon::parse($programmeChangeRequest->hod_new_approved_at)->format('d M Y') }}</p>
                                    @endif
                                </div>
                            </div>
                            @php $stage = $programmeChangeRequest->hod_new_approved_at ? 4 : $stage; @endphp
                            @endif
            
                            {{-- Dean (New Programme) Approval --}}
                            @if($stage >= 4)
                            <div class="acitivity-item d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <img src="{{ optional($programmeChangeRequest->newDean)->image }}" alt="" class="avatar-xs rounded-circle acitivity-avatar shadow">
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-2">
                                        - {{ $programmeChangeRequest->dean_new_approved_at ? 'Dean (New Programme) Approved' : 'Waiting for Dean (New Programme) approval' }}
                                    </h6>
                                    <p class="mb-0 text-muted">By: {{ $programmeChangeRequest->newDean->title.' '.$programmeChangeRequest->newDean->lastname.' '.$programmeChangeRequest->newDean->othernames }}</p>
                                    @if($programmeChangeRequest->dean_new_approved_at)
                                        <p class="text-muted">Approved on: {{ \Carbon\Carbon::parse($programmeChangeRequest->dean_new_approved_at)->format('d M Y') }}</p>
                                    @endif
                                </div>
                            </div>
                            @php $stage = $programmeChangeRequest->dean_new_approved_at ? 5 : $stage; @endphp
                            @endif

                            {{-- DAP (Director of Academic Planning) Approval --}}
                            @if($stage >= 5)
                            <div class="acitivity-item d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <img src="{{ optional($programmeChangeRequest->dap)->image }}" alt="" class="avatar-xs rounded-circle acitivity-avatar shadow">
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-2">
                                        - {{ $programmeChangeRequest->dap_approved_at ? 'DAP (Director of Academic Planning) Approved' : 'Waiting for DAP (Director of Academic Planning) approval' }}
                                    </h6>
                                    <p class="mb-0 text-muted">By: {{ $programmeChangeRequest->dap->title.' '.$programmeChangeRequest->dap->lastname.' '.$programmeChangeRequest->dap->othernames }}</p>
                                    @if($programmeChangeRequest->dap_approved_at)
                                        <p class="text-muted">Approved on: {{ \Carbon\Carbon::parse($programmeChangeRequest->dap_approved_at)->format('d M Y') }}</p>
                                    @endif
                                </div>
                            </div>
                            @php $stage = $programmeChangeRequest->dap_approved_at ? 6 : $stage; @endphp
                            @endif

                            {{-- Registrar (Registrar) Approval --}}
                            @if($stage >= 6)
                            <div class="acitivity-item d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <img src="{{ optional($programmeChangeRequest->registrar)->image }}" alt="" class="avatar-xs rounded-circle acitivity-avatar shadow">
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-2">
                                        - {{ $programmeChangeRequest->registrar_approved_at ? 'Registrar Approved' : 'Waiting for registrar approval' }}
                                    </h6>
                                    <p class="mb-0 text-muted">By: {{ $programmeChangeRequest->registrar->title.' '.$programmeChangeRequest->registrar->lastname.' '.$programmeChangeRequest->registrar->othernames }}</p>
                                    @if($programmeChangeRequest->registrar_approved_at)
                                        <p class="text-muted">Approved on: {{ \Carbon\Carbon::parse($programmeChangeRequest->registrar_approved_at)->format('d M Y') }}</p>
                                    @endif
                                </div>
                            </div>
                            @php $stage = $programmeChangeRequest->registrar_approved_at ? 7 : $stage; @endphp
                            @endif
            
                            {{-- Final Status --}}
                            @if($stage == 7)
                            <div class="acitivity-item d-flex mb-3">
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-2 ms-2">- All approvals completed</h6>
                                    <p><strong>Final Status:</strong> {{ ucfirst($programmeChangeRequest->status) }}</p>
                                </div>
                            </div>
                            @endif

                            @if(!empty($programmeChangeRequest->rejection_reason))
                            <div class="acitivity-item d-flex mb-3">
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-2 ms-2">- Rejection Status</h6>
                                    <p><strong>Rejection Reason:</strong> {{ ucfirst($programmeChangeRequest->rejection_reason) }}</p>
                                </div>
                            </div>
                            @endif
                                
                        </div>
                    </div>
                @endif
            </div><!-- end card-body -->
        </div>
    </div>
</div>
<!--end row-->

<div id="manageApplication" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
    <!-- Fullscreen Modals -->
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Manage Intra Transfer Application</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <div class="modal-body">
                <form action="{{ url('/staff/manageProgrammeChangeRequest') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="programme_change_request_id" value="{{ $programmeChangeRequest->id }}">

                    
                    <div class="mb-3">
                        <label for="role" class="form-label">Approval Role</label>
                        <select class="form-select" name="role" id="role" required>
                            <option value="">Select Option</option>
                            <option value="old_hod">Old Programme HOD</option>
                            <option value="new_hod">New Programme HOD</option>
                            <option value="old_dean">Old Programme Dean</option>
                            <option value="new_dean">New Programme Dean</option>
                            <option value="dap">DAP</option>
                            <option value="registrar">Registrar</option>
                        </select>


                    </div>

                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Comment(if Declining)</label>
                        <textarea class="form-control ckeditor" name="rejection_reason" id="rejection_reason"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Select Option</label>
                        <select class="form-select" aria-label="role" name="status" required>
                            <option selected value= "">Select Option </option>
                            <option value="approved">Confirm</option>
                            <option value="declined">Decline</option>
                        </select>
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    function setActionBy(role) {
      document.getElementById('actionBy').value = role;
      document.getElementById('reason').value = ''; // Reset textarea
    }
  </script>

@endsection

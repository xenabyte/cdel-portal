@extends('staff.layout.dashboard')
@php
$student = $suspension->student;
$qrcode = 'https://quickchart.io/chart?chs=300x300&cht=qr&chl='.env('APP_URL').'/studentDetails/'.$student->slug;
$name = $student->applicant->lastname.' '.$student->applicant->othernames;
$failedCourses = $student->registeredCourses()->where('grade', 'F')->where('re_reg', null)->get();
$studentAdvisoryData = (object) $student->getAcademicAdvisory();

$programmeCategory = new \App\Models\ProgrammeCategory;


    $staff = Auth::guard('staff')->user();
    $staffRegistrarRole = false;
    
    foreach ($staff->staffRoles as $staffRole) {
        if (strtolower($staffRole->role->role) == 'registrar') {
            $staffRegistrarRole = true;
        }  
    }

$stage = 0;
@endphp

@section('content')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Suspension Details</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Suspension Details</li>
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
                                @if($staffRegistrarRole)
                                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#manageSuspension"> Manage Suspension</button>
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#liftSuspension"> Lift Suspension</button>
                                @endif
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
    <div class="col-xl-3">
        <div class="card card-height-100">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-xl-12">
                        <h6 class="fs-14 mb-2">Suspension Details</h6>
                        <hr>
                        <p class="text-muted"><strong>Academic Session:</strong> {{ $suspension->academic_session }} Academic Session</p>
                        <p class="text-muted"><strong>Reason:</strong> {!! $suspension->reason !!}</p>
                        <p class="text-muted"><strong>Suspension Letter:</strong> {!! $suspension->file !!}</p>
                        <p class="text-muted"><strong>Start Date:</strong> {{ \Carbon\Carbon::parse($suspension->start_date)->format('jS \o\f F, Y') }}</p>
                        <p class="text-muted"><strong>End Date:</strong> {{ $suspension->end_date? \Carbon\Carbon::parse($suspension->end_date)->format('jS \o\f F, Y') : '' }}</p>
                    </div>
                    <!-- end col -->
                </div>    
                <hr>
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
                            <strong>Promotion Eligibility:</strong> {{ $studentAdvisoryData->promotion_eligible?'You are eligible to promote':'You are not eligible to promote' }}<br>
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
    <div class="col-xl-9">

        <div class="card card-height-100">
            <div class="card-header border-bottom-dashed align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Suspension Approval Activities</h4>
            </div><!-- end cardheader -->
            <div class="card-body p-0">
                <div data-simplebar="init" style="" class="p-3 simplebar-scrollable-y"><div class="simplebar-wrapper" style="margin: -16px;"><div class="simplebar-height-auto-observer-wrapper"><div class="simplebar-height-auto-observer"></div></div><div class="simplebar-mask"><div class="simplebar-offset" style="right: 0px; bottom: 0px;"><div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content" style="height: auto; overflow: hidden scroll;"><div class="simplebar-content" style="padding: 16px;">
                    <div class="acitivity-timeline acitivity-main">
                        @php $stage = 0; @endphp
                        
                        @if(empty($suspension->transaction_id))
                            <div class="acitivity-item d-flex mb-3">
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">Payment is pending, kindly proceed to pay for re-admission</h6>
                                </div>
                            </div>
                        @else
                            <div class="acitivity-item d-flex mb-3">
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-2 ms-2">- Payment received</h6>
                                </div>
                            </div>
                            @php $stage = 1; @endphp
                        @endif
                    
                        @if($stage >= 1)
                            <div class="acitivity-item d-flex mb-3">
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-2 ms-2">
                                        - {{ empty($suspension->court_affidavit) ? 'Court affidavit upload is pending' : 'Court affidavit uploaded' }}
                                        @if(!empty($suspension->court_affidavit))
                                            <br>
                                            <img class="img-thumbnail mt-3" width="50%" src="{{ url('uploads/'.$suspension->court_affidavit) }}">
                                            <hr style="width:30%">
                                            <a href="{{ url('uploads/'.$suspension->court_affidavit) }}" target="_blank" class="btn btn-sm btn-secondary">View</a>
                                        @endif
                                    </h6>
                                </div>
                            </div>
                            @php $stage = !empty($suspension->court_affidavit) ? 2 : $stage; @endphp
                        @endif
                    
                        @if($stage >= 2)
                            <div class="acitivity-item d-flex mb-3">
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-2 ms-2">
                                        - {{ empty($suspension->undertaking_letter) ? 'Guardian letter of undertaking upload is pending' : 'Guardian letter uploaded' }}
                                        @if(!empty($suspension->undertaking_letter))
                                            <br>
                                            <img class="img-thumbnail mt-3" width="50%" src="{{ url('uploads/'.$suspension->undertaking_letter) }}">
                                            <hr style="width:30%">
                                            <a href="{{ url('uploads/'.$suspension->undertaking_letter) }}" target="_blank" class="btn btn-sm btn-secondary">View</a>
                                        @endif
                                    </h6>
                                </div>
                            </div>
                            @php $stage = !empty($suspension->undertaking_letter) ? 3 : $stage; @endphp
                        @endif
                    
                        @if($stage >= 3)
                            <div class="acitivity-item d-flex mb-3">
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-2 ms-2">
                                        - {{ empty($suspension->traditional_ruler_reference) ? 'Traditional ruler reference upload is pending' : 'Reference uploaded' }}
                                        @if(!empty($suspension->traditional_ruler_reference))
                                            <br>
                                            <img class="img-thumbnail mt-3" width="50%" src="{{ url('uploads/'.$suspension->traditional_ruler_reference) }}">
                                            <hr style="width:30%">
                                            <a href="{{ url('uploads/'.$suspension->traditional_ruler_reference) }}" target="_blank" class="btn btn-sm btn-secondary">View</a>
                                        @endif
                                    </h6>
                                </div>
                            </div>
                            @php $stage = !empty($suspension->traditional_ruler_reference) ? 4 : $stage; @endphp
                        @endif
                    
                        @if($stage >= 4)
                            <div class="acitivity-item d-flex mb-3">
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-2 ms-2">
                                        - {{ empty($suspension->ps_reference) ? 'Public Servant reference upload is pending' : 'Reference uploaded' }}
                                        @if(!empty($suspension->ps_reference))
                                            <br>
                                            <img class="img-thumbnail mt-3" width="50%" src="{{ url('uploads/'.$suspension->ps_reference) }}">
                                            <hr style="width:30%">
                                            <a href="{{ url('uploads/'.$suspension->ps_reference) }}" target="_blank" class="btn btn-sm btn-secondary">View</a>
                                        @endif
                                    </h6>
                                </div>
                            </div>
                            @php $stage = !empty($suspension->ps_reference) ? 5 : $stage; @endphp
                        @endif
                    
                        @if($stage >= 5)
                            <div class="acitivity-item d-flex mb-3">
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-2 ms-2">- {{ empty($suspension->admin_comment) ? 'Admin review is pending' : 'Admin has reviewed your documents' }}</h6>
                                    {!! $suspension->admin_comment !!}
                                </div>
                            </div>
                            @php $stage = !empty($suspension->admin_comment) ? 6 : $stage; @endphp
                        @endif
                    
                        @if($stage == 6)
                            <div class="acitivity-item d-flex mb-3">
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-2 ms-2">-Your re-admission process has been completed successfully.</h6>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                </div></div></div></div><div class="simplebar-placeholder" style="width: 342px; height: 895px;"></div></div><div class="simplebar-track simplebar-horizontal" style="visibility: hidden;"><div class="simplebar-scrollbar" style="width: 0px; display: none;"></div></div><div class="simplebar-track simplebar-vertical" style="visibility: visible;"><div class="simplebar-scrollbar" style="height: 148px; transform: translate3d(0px, 0px, 0px); display: block;"></div></div></div>
            </div><!-- end card body -->
        </div>
    </div>
</div>
<!--end row-->


<!-- Upload Modals -->
<div id="liftSuspension" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Are you sure you want to lift suspension for {{ $name }}?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if(empty($suspension->end_date))
                <form action="{{ url('staff/recall') }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="suspension_id" value="{{ $suspension->id }}">
                    <input type="hidden" name="student_id" value="{{ $student->id}}">
                    <div class="col-lg-12 border-top border-top-dashed">
                        <div class="d-flex align-items-start gap-3 mt-3">
                            <button type="submit" id="submit-button" class="btn btn-primary btn-label right ms-auto nexttab" data-nexttab="pills-bill-address-tab"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i> Yes, Proceed</button>
                        </div>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>


<div id="manageSuspension" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
    <!-- Fullscreen Modals -->
    <div class="modal-dialog modal-md">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Manage Suspension</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <div class="modal-body">
                <form action="{{ url('/staff/manageSuspension') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="suspension_id" value="{{ $suspension->id }}">


                    <div class="mb-3">
                        <label for="comment" class="form-label">Comment</label>
                        <textarea class="form-control ckeditor" name="admin_comment" id="comment"></textarea>
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

@endsection

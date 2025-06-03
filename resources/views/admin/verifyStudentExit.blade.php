@extends('admin.layout.dashboard')

@section('content')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Student Exit Applicaiton</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Student Exit Applicaiton</li>
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
                <h4 class="card-title mb-0 flex-grow-1">Get Application Information</h4>
                <div class="flex-shrink-0">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#getApplication">Get Application</button>
                </div>
            </div><!-- end card header -->
        </div><!-- end card -->
    </div>
</div>
<!-- end row -->
@if(!empty($student))
<div class="row">
    <div class="col-xxl-4">
        <div class="card">
            <div class="card-body p-4">
                <div>
                    <div class="flex-shrink-0 avatar-md mx-auto">
                        <div class="avatar-title bg-light rounded">
                            <img src="{{empty($student->image)?asset('assets/images/users/user-dummy-img.jpg'):asset($student->image)}}" alt="" height="50" />
                        </div>
                    </div>
                    <div class="mt-4 text-center">
                        <h5 class="mb-1">{{$student->applicant->lastname.' '.$student->applicant->othernames}}</h5>
                        @php
                            $studentAdvisoryData = (object) $student->getAcademicAdvisory();
                            $failedCourses = $student->registeredCourses()->where('grade', 'F')->where('re_reg', null)->get();
                        @endphp
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
        <!--end card-->
    
    </div>
    <!--end col-->

    <div class="col-xxl-8">
       
        <div class="card">
            <div class="card-header border-0 align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Application Information</h4>
                <div class="text-end mb-5">
                    <a href="{{ asset($studentExit->file) }}" class="btn btn-outline-primary" target="_blank">View Document</a>
                    @if(strtolower($studentExit->status) == 'pending')
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#decline{{$studentExit->id}}" class="btn btn-danger"><i class="ri-close-circle-fill"></i> Decline</a>
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#approve{{$studentExit->id}}" class="btn btn-success"><i class="ri-checkbox-circle-fill"></i> Approve</a>
                    @else
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#exit{{$studentExit->id}}" class="btn btn-info"><i class="mdi mdi-logout"></i> Left Campus</a>
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#entry{{$studentExit->id}}" class="btn btn-primary"><i class="mdi mdi-login"></i> Enter Campus</a>
                    @endif
                </div>
            </div><!-- end card header -->

            <div class="card-body table-responsive pb-2 border-top border-top-dashed">
                <table style="width: 100%;">
                    <tbody>
                        <tr>
                            <td style="width: 70%; vertical-align: top; text-align: left; border: none; padding-right: 10px;">
                                <div><strong>Application Number:</strong> #{{ sprintf("%06d", $studentExit->id) }}</div>
                                <div><strong>Destination:</strong> {{ $studentExit->destination }}</div>
                                <div><strong>Purpose:</strong> {{ $studentExit->purpose }}</div>
                                <div><strong>Mode of Transportation:</strong> {{ $studentExit->transport_mode }}</div>
                                @if(!empty($studentExit->exit_date))<div><strong>Outing Date:</strong> {{ $studentExit->exit_date }}</div>@endif
                                @if(!empty($studentExit->return_date))<div><strong>Returning Date:</strong> {{ $studentExit->return_date }}</div>@endif
                                
                            </td>
                            <td style="width: 30%; border: none;">
                                @if($studentExit->status == 'approved')
                                <img src="{{asset('approved.png')}}" width="40%" style="float: right; border: 1px solid black;">
                                @elseif ($studentExit->status == 'declined')
                                <img src="{{asset('denied.png')}}" width="40%" style="float: right; border: 1px solid black;">
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>

                <table class="pb-2 mb-3 pt-2 border-top border-top-dashed" style="width: 100%; margin-top: 30px;">
                    <tbody>
                        <tr>
                            <!-- HOD Approval -->
                            <td style="width: 50%; vertical-align: top; text-align: left; border: none; padding-right: 10px;">
                                <h5 style="margin-bottom: 10px;">HOD Approval</h5>
                                <div>
                                    <strong>Name:</strong>
                                    @if($studentExit->hod)
                                        {{ $studentExit->hod->title }} {{ $studentExit->hod->lastname }}, {{ $studentExit->hod->othernames }}
                                    @else
                                        <em>Not Assigned</em>
                                    @endif
                                </div>
                                <div><strong>Approval Status:</strong> {{ $studentExit->is_hod_approved ? 'Approved' : 'Pending Approval' }}</div>
                                @if($studentExit->is_hod_approved_date)
                                    <div><strong>Approval Date:</strong> {{ date('F j, Y \a\t g:i A', strtotime($studentExit->is_hod_approved_date)) }}</div>
                                @endif
                            </td>

                            <!-- Final Approval -->
                            <td style="width: 50%; vertical-align: top; text-align: left; border: none; padding-left: 10px;">
                                @if($studentExit->managedBy)
                                    <h5 style="margin-bottom: 10px;">Final Approval by Staff</h5>
                                    <div>
                                        <strong>Name:</strong>
                                        @if($studentExit->managedBy)
                                            {{ $studentExit->managedBy->title }} {{ $studentExit->managedBy->lastname }}, {{ $studentExit->managedBy->othernames }}
                                        @else
                                            <em>Pending</em>
                                        @endif
                                    </div>
                                    <div>
                                        <strong>Approval Time:</strong>
                                        @if($studentExit->managedBy) {{ $studentExit->updated_at ? date('F j, Y \a\t g:i A', strtotime($studentExit->updated_at)) : 'Pending' }} @endif
                                    </div>
                                @else
                                    <h5 style="margin-bottom: 10px;">Pending Student Care Approval</h5>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div><!-- end card body -->
        </div><!-- end card -->
        
    </div>
    <!--end col-->
    
</div>
<!--end row-->

<div id="decline{{$studentExit->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="text-end">
                    <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="mt-2">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                    </lord-icon>
                    <h4 class="mb-3 mt-4">Are you sure you want to decline <br/> {{ $student->applicant->lastname .' ' . $student->applicant->othernames}} exit application?</h4>
                    <form action="{{ url('/admin/managestudentExit') }}" method="POST">
                        @csrf
                        <input name="exit_id" type="hidden" value="{{$studentExit->id}}">
                        <input name="action" type="hidden" value="declined">
                        <hr>
                        <button type="submit" id="submit-button" class="btn btn-danger w-100">Yes, Decline</button>
                    </form>
                </div>
            </div>
            <div class="modal-footer bg-light p-3 justify-content-center">

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="approve{{$studentExit->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="text-end">
                    <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="mt-2">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="hover" style="width:150px;height:150px">
                    </lord-icon>
                    <h4 class="mb-3 mt-4">Are you sure you want to approve <br/> {{ $student->applicant->lastname .' ' . $student->applicant->othernames}} exit application?</h4>
                    <form action="{{ url('/admin/managestudentExit') }}" method="POST">
                        @csrf
                        <input name="exit_id" type="hidden" value="{{$studentExit->id}}">
                        <input name="action" type="hidden" value="approved">
                        <hr>
                        <button type="submit" id="submit-button" class="btn btn-success w-100">Yes, Approve</button>
                    </form>
                </div>
            </div>
            <div class="modal-footer bg-light p-3 justify-content-center">

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div id="exit{{$studentExit->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="text-end">
                    <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="mt-2">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                    </lord-icon>
                    <h4 class="mb-3 mt-4">Are you sure  <br/> {{ $student->applicant->lastname .' ' . $student->applicant->othernames}} <br> is leaving Campus?</h4>
                    <form action="{{ url('/admin/leftSchool') }}" method="POST">
                        @csrf
                        <input name="exit_id" type="hidden" value="{{$studentExit->id}}">
                        <input name="action" type="hidden" value="declined">
                        <hr>
                        <button type="submit" id="submit-button" class="btn btn-danger w-100">Yes, student is leaving campus</button>
                    </form>
                </div>
            </div>
            <div class="modal-footer bg-light p-3 justify-content-center">

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="entry{{$studentExit->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="text-end">
                    <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="mt-2">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="hover" style="width:150px;height:150px">
                    </lord-icon>
                    <h4 class="mb-3 mt-4">Are you sure  <br/> {{ $student->applicant->lastname .' ' . $student->applicant->othernames}} <br> is entry Campus?</h4>
                    <form action="{{ url('/admin/enterSchool') }}" method="POST">
                        @csrf
                        <input name="exit_id" type="hidden" value="{{$studentExit->id}}">
                        <hr>
                        <button type="submit" id="submit-button" class="btn btn-danger w-100">Yes, student is entering campus</button>
                    </form>
                </div>
            </div>
            <div class="modal-footer bg-light p-3 justify-content-center">

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@endif


<div id="getApplication" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Get Application Information</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body border-top border-top-dashed">
                <form action="{{ url('/admin/verifyStudentExit') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="exit_id" class="form-label">Exit Application  Number</label>
                        <input type="text" class="form-control" name="exit_id" id="exit_id">
                    </div>
                    <div class="text-end border-top border-top-dashed p-3">
                        <br>
                        <button type="submit" id="submit-button" class="btn btn-primary">Get Application</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection
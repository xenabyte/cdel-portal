@extends('layouts.dashboard')

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12 card p-3">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Student Exit Application</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Student Exit Application</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

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
                    
                        <p class="text-muted">{{ $student->programme->name }} <br>
                            <strong>Matric Number:</strong> {{ $student->matric_number }}<br>
                            <strong>Wifi Username:</strong> {{ $student->bandwidth_username }}<br>
                            <strong>Email:</strong> {{ $student->email }}<br>
                            <strong>Phone Number:</strong> {{ $student->applicant->phone_number }}<br>
                            <strong>Address:</strong> {{ strip_tags($student->applicant->address) }}<br>
                        </p>
                        <p class="text-muted border-top border-top-dashed pt-2">
                            <strong>Programme Category:</strong> {{ $student->programmeCategory->category }} Programme<br>
                            <strong>Department:</strong> {{ $student->department->name }}<br>
                            <strong>Faculty:</strong> {{ $student->faculty->name }}<br>
                            <strong>Academic Level:</strong> <span class="text-primary">{{ $student->level_id * 100 }} Level</span><br>
                            <strong>Academic session:</strong> {{ $student->academic_session }}</span>
                            <br>
                            @if($student->level_id >= $student->programme->duration && !$student->is_passed_out)
                            <span class="text-warning"><strong>Graduating Set</strong></span> <br>
                            @endif
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
                    <tbody class="mt-2">
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
@endif

@endsection
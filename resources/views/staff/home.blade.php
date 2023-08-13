@extends('staff.layout.dashboard')
@php
$staff = Auth::guard('staff')->user();
$name = $staff->title.' '.$staff->lastname.' '.$staff->othernames;
$courses = $staff->courses;
@endphp
@section('content')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Staff</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Staff</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row mb-5">
    <div class="col-12">
        <div class="d-flex align-items-lg-center flex-lg-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-16 mb-1"><span id="greeting">Hello</span>, {{ $name }}</h4>
                <p class="text-muted mb-0">Here's what's happening with your dashboard today.</p>
            </div>
            <div class="mt-3 mt-lg-0">
                <form action="javascript:void(0);">
                    <div class="row g-3 mb-0 align-items-center">
                       
                        
                        <!--end col-->
                        <div class="col-auto">
                            <button type="button" class="btn btn-soft-info btn-icon waves-effect waves-light layout-rightside-btn shadow-none"><i class="mdi mdi-account"></i></button>
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                </form>
            </div>
        </div><!-- end card header -->
    </div>
</div>

<div class="row">
    <div class="col-xl-3">
        <div class="card">
            <div class="card-header">
                <div class="d-flex">
                    <h5 class="card-title flex-grow-1 mb-0"><i class="mdi mdi-account align-middle me-1 text-muted"></i>
                        Personal Details</h5>
                </div>
            </div>
            <div class="card-body">
                <div>
                    <div class="flex-shrink-0 avatar-md mx-auto">
                        <div class="avatar-title bg-light rounded">
                            <img src="{{empty($staff->image)?asset('assets/images/users/user-dummy-img.jpg'): $staff->image}}" alt="" height="50" />
                        </div>
                    </div>
                    <div class="mt-4 text-center">
                        <h5 class="mb-1">{{$name}}</h5>
                        <p class="text-muted">{{ !empty($staff->programme)?$staff->programme->name:null }} <br>
                            <strong>Staff ID:</strong> {{ $staff->staffId }} <br>
                            <strong>Referral Code:</strong> {{ $staff->referral_code }}
                        </p>
                    </div>
                    <div class="table-responsive border-top border-top-dashed">
                        <table class="table mb-0 table-borderless">
                            <tbody>
                                @if(!empty($staff->department))
                                <tr>
                                    <th><span class="fw-medium">Department:</span></th>
                                    <td>{{  !empty($staff->department)?$staff->department->name:null }}</td>
                                </tr>
                                @endif
                                @if(!empty($staff->faculty))
                                <tr>
                                    <th><span class="fw-medium">Faculty:</span></th>
                                    <td>{{  !empty($staff->faculty)?$staff->faculty->name:null }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th><span class="fw-medium">Email:</span></th>
                                    <td>{{ $staff->email }}</td>
                                </tr>
                                <tr>
                                    <th><span class="fw-medium">Contact No.:</span></th>
                                    <td>{{ $staff->phone_number }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--end card-->

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri-map-pin-line align-middle me-1 text-muted"></i> Address
                </h5>
            </div>
            <div class="card-body">
                <p class="list-unstyled vstack gap-2 mb-0">
                   {!! $staff->address !!}
                </p>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
    <div class="col-xl-9">
        <div class="card">
            <div class="card-header align-items-center">
                <h4 class="card-title mb-0 flex-grow-1">Course Allocated  for {{ $pageGlobalData->sessionSetting->academic_session }} academic session</h4>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <table class="table table-stripped table-bordered table-nowrap">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Course Code</th>
                            <th scope="col">Course Title</th>
                            <th scope="col">Course Unit</th>
                            <th scope="col">Status</th>
                            <th scope="col">Enrolled Student Count</th>
                            <th scope="col">Level</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($courses as $course)
                        <tr>
                            <td scope="row"> {{ $loop->iteration }}</td>
                            <td>{{$course->code}}</td>
                            <td>{{$course->name }}</td>
                            <td>{{$course->credit_unit}} </td>
                            <td>{{$course->status}}</td>
                            <td>{{$course->registrations_count  }}</td>
                            <td>{{$course->level->level}}</td>
                            <td>
                                <a href="{{ url('/staff/courseDetail/'.$course->id) }}" class="btn btn-lg btn-primary">Course Details</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                      
            </div>
        </div><!-- end card -->

        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Referred Student(s) </h4>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Name</th>
                            <th scope="col">Programme</th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone Number</th>
                            <th scope="col">Academic Session</th>
                            <th scope="col">Application Status</th>
                            <th scope="col">Applied Date</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($applicants as $applicant)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $applicant->lastname .' '. $applicant->othernames }}</td>
                            <td>{{ $applicant->programme->name }}</td>
                            <td>{{ $applicant->email }} </td>
                            <td>{{ $applicant->phone_number }} </td>
                            <td>{{ $applicant->academic_session }} </td>
                            <td>{{ ucwords($applicant->status) }} </td>
                            <td>{{ $applicant->created_at }} </td>
                            <td>
                                <a href="{{ !empty($applicant->student)? url('staff/student/'.$applicant->student->slug) : url('admin/applicant/'.$applicant->slug) }}" class="btn btn-primary m-1"><i class= "ri-user-6-fill"></i> View Applicant/Student</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div><!-- end card -->
    </div>
    <!--end col-->
</div>
<!--end row-->

@endsection

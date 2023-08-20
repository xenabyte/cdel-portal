@extends('guardian.layout.dashboard')
@php
$guardian = Auth::guard('guardian')->user();
$name = $guardian->name;
$applicants = $guardian->applicants->where('status', 'Admitted');
@endphp
@section('content')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Guardian</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Guardian</li>
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
                            <img src="{{asset('assets/images/users/user-dummy-img.jpg')}}" alt="" height="50" />
                        </div>
                    </div>
                    <div class="table-responsive border-top border-top-dashed mt-3">
                        <table class="table mb-0 table-borderless">
                            <tbody>
                                <tr>
                                    <th><span class="fw-medium">Email:</span></th>
                                    <td>{{ $guardian->email }}</td>
                                </tr>
                                <tr>
                                    <th><span class="fw-medium">Contact No.:</span></th>
                                    <td>{{ $guardian->phone_number }}</td>
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
                   {!! $guardian->address !!}
                </p>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
    
    <div class="col-xl-9">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Students </h4>
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
                            <th scope="col">Level</th>
                            <th scope="col">Phone Number</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($applicants as $applicant)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $applicant->lastname .' '. $applicant->othernames }}</td>
                            <td>{{ $applicant->student->matric_number }}</td>
                            <td>{{ $applicant->student->programme->name }}</td>
                            <td>{{ $applicant->student->email }} </td>
                            <td>{{ $applicant->student->academicLevel->level }} </td>
                            <td>{{ $applicant->student->phone_number }} </td>
                            <td>
                                <a href="{{ url('guardian/studentProfile/'.$applicant->student->slug) }}" class="btn btn-primary m-1"><i class= "ri-user-6-fill"></i> View Student</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div><!-- end card -->
    </div>
    <!-- end col -->
    <!--end col-->
</div>
<!--end row-->

@endsection

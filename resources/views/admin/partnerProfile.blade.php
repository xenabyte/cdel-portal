@extends('admin.layout.dashboard')

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Partner</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Partner</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-xl-5">
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
                                    <th><span class="fw-medium">Referral Code:</span></th>
                                    <td>{{ $partner->referral_code }}</td>
                                </tr>
                                <tr>
                                    <th><span class="fw-medium">Link:</span></th>
                                    <td><a href="{{env('ADMISSION_URL').'?ref='.$partner->referral_code}}" target="_blank" id="myLink">{{env('ADMISSION_URL').'?ref='.$partner->referral_code}}</a>  <button class="btn btn-sm btn-info" id="copyButton"><i class="ri-file-copy-fill"></i></button></td>
                                </tr>
                                <tr>
                                    <th><span class="fw-medium">Email:</span></th>
                                    <td>{{ $partner->email }}</td>
                                </tr>
                                <tr>
                                    <th><span class="fw-medium">Contact No.:</span></th>
                                    <td>{{ $partner->phone_number }}</td>
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
                   {!! $partner->address !!}
                </p>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
    
    <div class="col-xl-7">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Students referred for {{ $pageGlobalData->sessionSetting->application_session }} session</h4>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Name</th>
                            <th scope="col">Programme</th>
                            <th scope="col">Phone Number</th>
                            <th scope="col">Academic Session</th>
                            <th scope="col">Application Status</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($applicants as $applicant)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $applicant->lastname .' '. $applicant->othernames }}</td>
                            <td>{{ $applicant->programme->name }}</td>
                            <td>{{ $applicant->phone_number }} </td>
                            <td>{{ $applicant->academic_session }} </td>
                            <td>{{ ucwords($applicant->status) }} </td>
                            <td>
                                <a href="{{ url('admin/applicant/'.$applicant->slug) }}" class="btn btn-primary m-1"><i class= "ri-user-6-fill"></i> View Applicant</a>
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
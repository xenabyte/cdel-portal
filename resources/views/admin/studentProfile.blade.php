@extends('admin.layout.dashboard')
@php

$qrcode = 'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl='.env('APP_URL').'/studentDetails/'.$student->slug;
$name = $student->applicant->lastname.' '.$student->applicant->othernames;
$transactions = $student->transactions()->orderBy('created_at', 'desc')->get();
$studentRegistrations = $student->courseRegistrationDocument()->orderBy('created_at', 'desc')->take(10)->get();

@endphp
@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Student</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Student</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

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
                                        <img src="{{ !empty($student->image) ? asset($student->image) : asset('assets/images/users/user-dummy-img.jpg') }}" alt="" class="img-thumbnail rounded-circle avatar-md">
                                    </div>
                                </div>
                                <div class="col-md">
                                    <div>
                                        <h4 class="fw-bold">{{$name}}</h4>
                                        <div class="hstack gap-3 flex-wrap">
                                            <div><i class="ri-building-line align-bottom me-1"></i> {{ $student->programme->name }}</div>
                                            <div class="vr"></div>
                                            <div>CGPA: <span class="fw-medium">{{ $student->cgpa }}</span></div>
                                            <div class="vr"></div>
                                            <div>Level: <span class="fw-medium">{{ $student->academicLevel->level }} Level</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-auto">
                            
                        </div>
                    </div>

                    <ul class="nav nav-tabs-custom border-bottom-0" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active fw-semibold" data-bs-toggle="tab" href="#project-overview" role="tab">
                                Overview
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#settings" role="tab">
                                Settings
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
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <div class="text-muted">
                                    <h6 class="mb-3 fw-semibold text-uppercase">Transactions</h6>
                                    <div class="border-top border-top-dashed pt-3">
                                        <div class="table-responsive">
                                            <!-- Bordered Tables -->
                                            <table id="buttons-datatables" class="display table table-stripped" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Id</th>
                                                        <th scope="col">Reference</th>
                                                        <th scope="col">Amount(₦)</th>
                                                        <th scope="col">Payment For</th>
                                                        <th scope="col">Session</th>
                                                        <th scope="col">Payment Gateway</th>
                                                        <th scope="col">Status</th>
                                                        <th scope="col">Payment Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($transactions as $transaction)
                                                    <tr>
                                                        <th scope="row">{{ $loop->iteration }}</th>
                                                        <td>{{ $transaction->reference }}</td>
                                                        <td>₦{{ number_format($transaction->amount_payed/100, 2) }} </td>
                                                        <td>{{ $transaction->paymentType->type }} </td>
                                                        <td>{{ $transaction->session }}</td>
                                                        <td>{{ $transaction->payment_method }}</td>
                                                        <td><span class="badge badge-soft-{{ $transaction->status == 1 ? 'success' : 'warning' }}">{{ $transaction->status == 1 ? 'Paid' : 'Pending' }}</span></td>
                                                        <td>{{ $transaction->status == 1 ? $transaction->updated_at : null }} </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->

                        <div class="card">
                            <div class="card-body">
                                <div class="text-center">
                                    <div class="row justify-content-center">
                                        <div class="col-lg-9">
                                            <h4 class="mt-4 fw-semibold">Generate Examination result</h4>
                                            <p class="text-muted mt-3"></p>
                                    
                                            <div class="mt-4">
                                                <form action="{{ url('/admin/generateResult') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="examSetting_id" value="{{ !empty($pageGlobalData->examSetting)?$pageGlobalData->examSetting->id:null }}">
                                                    <input type="hidden" name="academic_session" value="{{ $pageGlobalData->sessionSetting->academic_session }}">
                                                    <input type="hidden" name="student_id" value="{{ $student->id }}">

                                                    <div class="row g-3">
                                                        
                                                        <div class="col-lg-12">
                                                            <div class="form-floating">
                                                                <select class="form-select" id="level" name="level_id" aria-label="level">
                                                                    <option value="" selected>--Select--</option>
                                                                    @foreach($academicLevels as $academicLevel)
                                                                        @if($academicLevel->id <= $student->level_id)
                                                                            <option value="{{ $academicLevel->id }}">{{ $academicLevel->level }} Level</option>
                                                                        @endif
                                                                    @endforeach
                                                                </select>
                                                                <label for="level">Academic Level</label>
                                                            </div>
                                                        </div>
                        
                                                        <div class="col-lg-12">
                                                            <div class="form-floating">
                                                                <select class="form-select" id="semester" name="semester" aria-label="semester">
                                                                    <option value="" selected>--Select--</option>
                                                                    <option value="1">First Semester</option>
                                                                    <option value="2">Second Semester</option>
                                                                </select>
                                                                <label for="semester">Semester</label>
                                                            </div>
                                                        </div>
                        
                        
                                                        <div class="col-lg-12">
                                                            <div class="form-floating">
                                                                <select class="form-select" id="session" name="session" aria-label="Academic Session">
                                                                    <option value="" selected>--Select--</option>
                                                                    @foreach($sessions as $session)<option value="{{ $session->year }}">{{ $session->year }}</option>@endforeach
                                                                </select>
                                                                <label for="session">Academic Session</label>
                                                            </div>
                                                        </div>
                
                                                        <button type="submit" class="btn btn-fill btn-primary btn-lg btn-block mb-5">Generate Result</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ene col -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body p-4">
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
                                            <strong>Jamb Reg. Number:</strong> {{ $student->applicant->jamb_reg_no }}
                                        </p>
                                        <p class="text-muted border-top border-top-dashed"><strong>CGPA:</strong> {{ $student->cgpa }} <br>
                                            <strong>Class:</strong> {{ $student->degree_class }}<br>
                                            <strong>Standing:</strong> {{ $student->standing }}<br>
                                        </p>
                                    </div>
                                    <div class="table-responsive border-top border-top-dashed">
                                        <table class="table mb-0 table-borderless">
                                            <tbody>
                                                <tr>
                                                    <th><span class="fw-medium">Department:</span></th>
                                                    <td>{{ $student->department->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Faculty:</span></th>
                                                    <td>{{ $student->faculty->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Email:</span></th>
                                                    <td>{{ $student->email }}</td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Contact No.:</span></th>
                                                    <td>{{ $student->applicant->phone_number }}</td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-medium">Address:</span></th>
                                                    <td>{!! $student->applicant->address !!}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!--end card-body-->
                            <div class="card-body p-4 border-top border-top-dashed">
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
                        <!-- end card -->
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end tab pane -->
            <div class="tab-pane fade" id="settings" role="tabpanel">
                <!-- Accordions with Icons -->
                <div class="accordion custom-accordionwithicon" id="accordionWithicon">
                    <div class="accordion-item shadow">
                        <h2 class="accordion-header" id="accordionwithiconExample1">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#accor_iconExamplecollapse1" aria-expanded="true" aria-controls="accor_iconExamplecollapse1">
                                <i class="ri-image-add-fill g-3"></i> Upload Student Image
                            </button>
                        </h2>
                        <div id="accor_iconExamplecollapse1" class="accordion-collapse collapse show" aria-labelledby="accordionwithiconExample1" data-bs-parent="#accordionWithicon">
                            <div class="accordion-body">
                                <div class="mt-4">
                                    <form action="{{ url('/admin/uploadStudentImage') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="student_id" value="{{ $student->id }}">

                                        <div class="row g-3">

                                            <div class="row">
                                                <div class="col-lg-12 text-center">
                                                    <div class="profile-user position-relative d-inline-block mx-auto mb-2">
                                                        <img src="{{asset(empty($student->image)?'assets/images/users/user-dummy-img.jpg':$student->image)}}" class="rounded-circle avatar-lg img-thumbnail user-profile-image" alt="user-profile-image">
                                                        <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                                                            <input id="profile-img-file-input" type="file" class="profile-img-file-input" accept="image/png, image/jpeg" name="image" required>
                                                            <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                                                                <span class="avatar-title rounded-circle bg-light text-body">
                                                                    <i class="ri-camera-fill"></i>
                                                                </span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <h5 class="fs-14">Add Passport Photograph</h5>
                                                </div>
                                                <hr>
                                            </div>
    
                                            <button type="submit" class="btn btn-fill btn-primary btn-lg mb-5">Upload Image</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item shadow">
                        <h2 class="accordion-header" id="accordionwithiconExample2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accor_iconExamplecollapse2" aria-expanded="false" aria-controls="accor_iconExamplecollapse2">
                                <i class="ri-lock-password-fill"></i> Update Student Password
                            </button>
                        </h2>
                        <div id="accor_iconExamplecollapse2" class="accordion-collapse collapse" aria-labelledby="accordionwithiconExample2" data-bs-parent="#accordionWithicon">
                            <div class="accordion-body">
                                <form action="{{ url('admin/changeStudentPassword') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="student_id" value="{{ $student->id }}">

                                    <div class="row g-2">
                                        <div class="col-lg-4">
                                            <div>
                                                <label for="newpasswordInput" class="form-label">New Password<span class="text-danger">*</span></label>
                                                <input type="password"  name="password"  class="form-control" id="newpasswordInput" placeholder="Enter new password">
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-lg-4">
                                            <div>
                                                <label for="confirmpasswordInput" class="form-label">Confirm Password<span class="text-danger">*</span></label>
                                                <input type="password"  name="confirm_password" class="form-control" id="confirmpasswordInput" placeholder="Confirm password">
                                            </div>
                                        </div>
        
                                        <!--end col-->
                                        <div class="col-lg-4">
                                            <div class="text-end">
                                                <br>
                                                <button type="submit" class="btn btn-primary btn-lg">Change Password</button>
                                            </div>
                                        </div>
                                        <!--end col-->
                                    </div>
                                    <!--end row-->
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item shadow">
                        <h2 class="accordion-header" id="accordionwithiconExample3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accor_iconExamplecollapse3" aria-expanded="false" aria-controls="accor_iconExamplecollapse3">
                                <i class="ri-contacts-book-upload-fill"></i> Update Student Credit Load
                            </button>
                        </h2>
                        <div id="accor_iconExamplecollapse3" class="accordion-collapse collapse" aria-labelledby="accordionwithiconExample2" data-bs-parent="#accordionWithicon">
                            <div class="accordion-body">
                                <form action="{{ url('admin/changeStudentCreditLoad') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="student_id" value="{{ $student->id }}">

                                    <div class="row g-2">
                                        <div class="col-lg-12">
                                            <div>
                                                <label for="creditLoad" class="form-label">Credit Load<span class="text-danger">*</span></label>
                                                <input type="number"  name="credit_load"  class="form-control" id="credit_load" value="{{ $student->credit_load }}">
                                            </div>
                                        </div>
        
                                        <!--end col-->
                                        <div class="col-lg-12">
                                            <div class="text-end">
                                                <br>
                                                <button type="submit" class="btn btn-primary btn-lg">Save Changes</button>
                                            </div>
                                        </div>
                                        <!--end col-->
                                    </div>
                                    <!--end row-->
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end tab pane -->
        </div>
    </div>
    <!-- end col -->
</div>
<!-- end row -->

@endsection
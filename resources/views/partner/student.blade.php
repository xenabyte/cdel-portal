@extends('partner.layout.dashboard')
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
                                                    @foreach($transactions->where('session', $student->applicant->academic_session) as $transaction)
                                                    <tr>
                                                        <th scope="row">{{ $loop->iteration }}</th>
                                                        <td>{{ $transaction->reference }}</td>
                                                        <td>₦{{ number_format($transaction->amount_payed/100, 2) }} </td>
                                                        <td>{{ !empty($transaction->paymentType) ? ($transaction->paymentType->type == 'General Fee' ? $transaction->paymentType->title : $transaction->paymentType->type) : 'Wallet Deposit' }} </td>
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
        </div>
    </div>
    <!-- end col -->
</div>
<!-- end row -->

@endsection
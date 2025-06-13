@extends('staff.layout.dashboard')

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
        <div class="card">
            <div class="card-body">
                <div class="row gx-lg-5">
                    <div class="col-xl-4 col-md-8 mx-auto">
                        <div class="product-img-slider">
                            <div class="swiper product-thumbnail-slider p-2 rounded bg-light">
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide">
                                        <img src="{{asset($student->applicant->image)}}" alt="" class="img-fluid d-block" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-5">
                                    <div class="flex-grow-1">
                                        <h5 class="card-title mb-0">Congratulations</h5>
                                    </div>
                                    <div class="flex-shrink-0">
                                    </div>
                                </div>
                                <div class="d-flex align-items-center text-center mb-5">
                                    <div class="flex-grow-1">
                                        <i class="fa fa-check fa-5x text-success"></i><br>
                                        <p class="muted">Student have been granted admission</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end col -->

                    <div class="col-xl-8">
                        <div class="mt-xl-0 mt-5">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <h4>{{ $student->applicant->lastname .' '. $student->applicant->othernames }}</h4>
                                    <div class="hstack gap-3 flex-wrap">
                                        <div><a href="#" class="text-primary d-block">Applied Programme: {{ $student->programme->name }}</a></div>
                                        <div class="vr"></div>
                                        <div class="text-muted">Admitted Programme : <span class="text-body fw-medium">{{ $student->programme->name }}</span></div>
                                        <div class="vr"></div>
                                        <div class="text-muted">Application ID : <span class="text-body fw-medium"> {{ $student->applicant->application_number }}</span></div>
                                        @if($student->applicant->application_type == 'UTME')
                                        <div class="vr"></div>
                                        <div class="text-muted">UTME Scores : <span class="text-body fw-medium"> {{ $student->applicant->utmes->sum('score') }}</span></div>
                                        @endif
                                        <div class="vr"></div>
                                        <div class="text-muted">Application Date : <span class="text-body fw-medium">{{ $student->applicant->updated_at }}</span></div>
                                        <div class="vr"></div>
                                        <div class="text-muted">Admitted Date : <span class="text-body fw-medium">{{ $student->created_at }}</span></div>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <div>
                                    </div>
                                </div>
                            </div>

                            <div class="product-content mt-5">
                                <h5 class="fs-14 mb-3"> Student Information</h5>
                                <nav>
                                    <ul class="nav nav-tabs nav-tabs-custom nav-info" id="nav-tab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="nav-speci-tab" data-bs-toggle="tab" href="#biodata" role="tab" aria-controls="nav-speci" aria-selected="true">Bio Data</a>
                                        </li>
                                        
                                        <li class="nav-item">
                                            <a class="nav-link" id="nav-detail-tab" data-bs-toggle="tab" href="#olevel" role="tab" aria-controls="nav-detail" aria-selected="false">Olevel Result</a>
                                        </li>
                                        @if($student->applicant->application_type == 'UTME')
                                        <li class="nav-item">
                                            <a class="nav-link" id="nav-detail-tab" data-bs-toggle="tab" href="#utme" role="tab" aria-controls="nav-detail" aria-selected="false">UTME Result</a>
                                        </li>
                                        @endif

                                        @if($student->applicant->application_type != 'UTME')
                                        <li class="nav-item">
                                            <a class="nav-link" id="nav-detail-tab" data-bs-toggle="tab" href="#de" role="tab" aria-controls="nav-detail" aria-selected="false">Direct Entry/Prev Institution Result</a>
                                        </li>
                                        @endif

                                        <li class="nav-item">
                                            <a class="nav-link" id="nav-detail-tab" data-bs-toggle="tab" href="#transactions" role="tab" aria-controls="nav-detail" aria-selected="false">Transactions</a>
                                        </li>

                                    </ul>
                                </nav>
                                <div class="tab-content border border-top-0 p-4" id="nav-tabContent">
                                    <div class="tab-pane fade show active" id="biodata" role="tabpanel" aria-labelledby="nav-speci-tab">
                                        <div class="table-responsive">
                                            <table class="table mb-0">
                                                <tbody>
                                                    <tr>
                                                        <th scope="row" style="width: 200px;">Fullname</th>
                                                        <td>{{ $student->applicant->lastname .' '. $student->applicant->othernames }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Email</th>
                                                        <td>{{ $student->applicant->email }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Phone Number</th>
                                                        <td>{{ $student->applicant->phone_number }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Gender</th>
                                                        <td>{{ $student->applicant->gender }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Date of Birth</th>
                                                        <td>{{ $student->applicant->dob }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Religion</th>
                                                        <td>{{ $student->applicant->religion }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Marital Status</th>
                                                        <td>{{ $student->applicant->marital_status }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Nationality</th>
                                                        <td>{{ $student->applicant->nationality }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">State of Origin</th>
                                                        <td>{{ $student->applicant->state }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Local Government Area</th>
                                                        <td>{{ $student->applicant->lga }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="olevel" role="tabpanel" aria-labelledby="nav-detail-tab">
                                        <div>
                                            @if(!empty($student->applicant->olevel_1))
                                            <h5 class="fs-14 mb-3"> Schools Attended</h5>
                                            {!! $student->applicant->schools_attended !!}
                                            <hr>
                                            <div class="row mb-2">
                                                <div class="col-sm-6 col-xl-12">
                                                    <!-- Simple card -->
                                                    <i class="bx bxs-file-jpg text-danger" style="font-size: 50px"></i><span style="font-size: 20px">Olevel Result</span>
                                                    <div class="text-end">
                                                        <a href="{{ asset($student->applicant->olevel_1) }}" target="blank" class="btn btn-success">View</a>
                                                    </div>
                                                    @if($student->applicant->sitting_no > 1)
                                                    <i class="bx bxs-file-jpg text-danger" style="font-size: 50px"></i><span style="font-size: 20px">Olevel Result (Second Sitting)</span>
                                                    <div class="text-end">
                                                        <a href="{{ asset($student->applicant->olevel_2) }}" target="blank"  class="btn btn-success">View</a>
                                                    </div>
                                                    @endif
                                                </div><!-- end col -->
                                            </div>
                                            @endif
                                            <hr>
                                            <table class="table table-borderedless table-nowrap">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Id</th>
                                                        <th scope="col">Subject</th>
                                                        <th scope="col">Grade</th>
                                                        <th scope="col">Registration Number</th>
                                                        <th scope="col">Year</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($student->applicant->olevels as $olevel)
                                                    <tr>
                                                        <th scope="row">{{ $loop->iteration }}</th>
                                                        <td>{{ $olevel->subject }}</td>
                                                        <td>{{ $olevel->grade }}</td>
                                                        <td>{{ $olevel->reg_no }}</td>
                                                        <td>{{ $olevel->year }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    @if($student->applicant->application_type == 'UTME')
                                    <div class="tab-pane fade" id="utme" role="tabpanel" aria-labelledby="nav-detail-tab">
                                        <div>
                                            @if(!empty($student->applicant->utme))
                                            <div class="row mb-2">
                                                <div class="col-sm-6 col-xl-12">
                                                    <!-- Simple card -->
                                                    <i class="bx bxs-file-jpg text-danger" style="font-size: 50px"></i><span style="font-size: 20px">UTME Result Printout</span>
                                                    <div class="text-end">
                                                        <a href="{{ asset($student->applicant->utme) }}"  target="blank" class="btn btn-success">View</a>
                                                    </div>
                                                </div><!-- end col -->
                                            </div>
                                            @endif
                                            <hr>
                                            <table class="table table-borderedless table-nowrap">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Id</th>
                                                        <th scope="col">Subject</th>
                                                        <th scope="col">Score</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($student->applicant->utmes as $utme)
                                                    <tr>
                                                        <th scope="row">{{ $loop->iteration }}</th>
                                                        <td>{{ $utme->subject }}</td>
                                                        <td>{{ $utme->score }}</td>
                                                    </tr>
                                                    @endforeach
                                                    <tr>
                                                        <th scope="row"></th>
                                                        <td>Total</td>
                                                        <td><strong>{{$student->applicant->utmes->sum('score')}}</strong></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    @endif

                                    @if($student->applicant->application_type != 'UTME')
                                    <div class="tab-pane fade" id="de" role="tabpanel" aria-labelledby="nav-detail-tab">
                                        <div>
                                            <h5 class="fs-14 mb-3"> Institution Attended</h5>
                                            {!! $student->applicant->de_school_attended !!}
                                            <hr>
                                            @if(!empty($student->applicant->de_result))
                                            <div class="row mb-2">
                                                <div class="col-sm-6 col-xl-12">
                                                    <!-- Simple card -->
                                                    <i class="bx bxs-file-jpg text-danger" style="font-size: 50px"></i><span style="font-size: 20px">Direct Entry/Prev Institution Result</span>
                                                    <div class="text-end">
                                                        <a href="{{ asset($student->applicant->de_result) }}"  target="blank" class="btn btn-success">View</a>
                                                    </div>
                                                </div><!-- end col -->
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                    
                                    <div class="tab-pane fade" id="transactions" role="tabpanel" aria-labelledby="nav-detail-tab">
                                        <div>
                                            <h5 class="fs-14 mb-3"> Transaction</h5>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="card">
                                                        <div class="card-body table-responsive">
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
                                                                        <th scope="col"></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($student->transactions->where('academic_session', $student->applicant->academic_session) as $transaction)
                                                                    <tr>
                                                                        <th scope="row">{{ $loop->iteration }}</th>
                                                                        <td>{{ $transaction->reference }}</td>
                                                                        <td>₦{{ number_format($transaction->amount_payed/100, 2) }} </td>
                                                                        <td>
                                                                            {{ !empty($transaction->paymentType) 
                                                                                ? (in_array($transaction->paymentType->type, ['General Fee', 'Other Fee']) 
                                                                                    ? $transaction->paymentType->title 
                                                                                    : $transaction->paymentType->type) 
                                                                                : 'Wallet Deposit' 
                                                                            }}
                                                                        </td>
                                                                        <td>{{ $transaction->session }}</td>
                                                                        <td>{{ $transaction->payment_method }}</td>
                                                                        <td><span class="badge badge-soft-{{ $transaction->status == 1 ? 'success' : 'warning' }}">{{ $transaction->status == 1 ? 'Paid' : 'Pending' }}</span></td>
                                                                        <td>{{ $transaction->status == 1 ? $transaction->updated_at : null }} </td>
                                                                        <td>
                                                                            @if($transaction->status == 0)
                                                                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#payNow{{$transaction->id}}" style="margin: 5px" class="btn btn-warning">Pay Now</a>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div><!-- end card -->
                                                </div>
                                                <!-- end col -->
                                            </div>

                                        </div>
                                    </div>
                                </div>  
                            </div>
                            <!-- product-content -->
                        </div>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end card body -->
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end row --> 


@endsection

@extends('student.layout.dashboard')
@php
    $student = Auth::guard('student')->user();
    $eligibleProgrammes = $student->getQualifiedTransferProgrammes();
@endphp
@section('content')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Student Intra Transfer Application</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Student Intra Transfer Application</li>
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
                <h4 class="card-title mb-0 flex-grow-1">Student Intra Transfer Application</h4>

                <div class="flex-shrink-0">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#changeProgramme">Pay for Change of Programme</button>
                </div>
            </div><!-- end card header -->

            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-6 col-xl-12">
                        
                        <div class="table-responsive">
                            <!-- Bordered Tables -->
                            @if($programmeChangeRequests->isNotEmpty())

                                <table id="buttons-datatables" class="table table-stripped">
                                    <thead>
                                        <tr>
                                            <th scope="col">S/N</th>
                                            <th scope="col">Present Programme</th>
                                            <th scope="col">New Programme Date</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Current Status</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($programmeChangeRequests as $programmeChangeRequest)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $student->programme->name }}</td>
                                            <td>{{ $programmeChangeRequest->newProgramme->name ?? '' }}</td>
                                            <td>
                                                {{ $programmeChangeRequest->status }}
                                            </td>
                                            <td>
                                                {{ str_replace('_', ' ', $programmeChangeRequest->current_stage) }}
                                            </td>
                                            <td>
                                                <a href="{{ url('student/viewProgrammeChangeRequest/'.$programmeChangeRequest->slug) }}" class="btn btn-sm btn-info">
                                                    <i class="ri-eye-line"></i> View Details
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted">No records found.</p>
                            @endif
                        </div>
                       
                    </div><!-- end col -->
                </div>
            </div>

        </div><!-- end card -->
    </div>
</div>


<div class="modal fade" id="changeProgramme" tabindex="-1" aria-labelledby="changeProgrammeLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header p-3">
                <h5 class="modal-title text-uppercase fw-bold" id="changeProgrammeLabel">Apply for Change of Programme</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body border-top border-top-dashed">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="pills-bill-info" role="tabpanel" aria-labelledby="pills-bill-info-tab">
                        
                        <form action="{{ url('/student/makePayment') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                    
                                <input type="hidden" name="payment_id" value="{{ $programmeChangePayment->id }}">
                                <input type="hidden" name="student_id" value="{{ $student->id }}">
                                <input type="hidden" name="amount" value="{{ $programmeChangePayment->structures->sum('amount') }}">

                                <div class="alert alert-warning text-center">
                                    <strong>Notice:</strong> You are about to make a payment of <strong>₦{{ number_format($programmeChangePayment->structures->sum('amount')/100, 2) }}</strong>.  
                                    This payment is <strong>non-refundable</strong>. Please ensure that all details are correct before proceeding.
                                </div>
                                
                                @if($eligibleProgrammes->isEmpty())
                                    <div class="alert alert-warning">
                                        No eligible programmes found. Please visit the DAP or Academic Office for further guidance.
                                    </div>
                                @else
                                <div class="col-lg-12">
                                    <div class="form-floating">
                                        <select class="form-select" aria-label="paymentGateway" name="paymentGateway" required>
                                            <option value= "" selected>Select Payment Gateway</option>
                                            @if(env('UPPERLINK_STATUS'))<option value="Upperlink">Upperlink</option>@endif
                                            @if(env('FLUTTERWAVE_STATUS'))<option value="Rave">Flutterwave</option>@endif
                                            @if(env('MONNIFY_STATUS'))<option value="Monnify">Monnify</option>@endif
                                            @if(env('PAYSTACK_STATUS'))<option value="Paystack">Paystack</option>@endif
                                            @if(env('WALLET_STATUS'))<option value="Wallet">Wallet</option>@endif
                                            {{-- @if(env('BANK_TRANSFER_STATUS'))<option value="BankTransfer">Transfer</option>@endif --}}
                                        </select>
                                        <label for="paymentGateway" class="form-label">Select Payment Gateway</label>
                                    </div>
                                </div>
                               @endif
                                <button type="submit" id="submit-button" class="btn btn-fill btn-primary btn-lg btn-block mb-5">Apply Now</button>
                            </div>
                        </form>
                        
                    </div>
                    <!-- end tab pane -->
                </div>
                <!-- end tab content -->
            </div>
        </div>
    </div>
</div>
<!--end modal-->
@endsection
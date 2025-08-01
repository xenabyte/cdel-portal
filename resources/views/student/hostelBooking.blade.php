@extends('student.layout.dashboard')

@section('content')
@php
    $student = Auth::guard('student')->user();
    $applicant = $student->applicant;
    $gender = $applicant->gender;
    $programmeCategory = $student->programmeCategory;
@endphp
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Hostel Booking</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Hostel Booking</li>
                </ol>
            </div>

        </div>
    </div>
</div>

@if(empty($gender))
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="mb-sm-0">Student Gender Update</h4>
                <div class="flex-shrink-0">
                </div>
            </div><!-- end card header -->

            <div class="card-body">
                <div class="row">
                    <div class="col-md-8 offset-md-2 ">
                        <div class="bg-soft-info p-2">
                            <p>Kindly pick your gender</p>
                        </div>
                        
                        <form action="{{ url('/student/profile/saveBioData') }}" method="post" enctype="multipart/form-data">
                            @csrf

                            <div class="row mt-3 g-3">        
                                <div class="mb-3">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select class="form-control" name="gender" id="gender" required>
                                        <option @if($applicant->gender == '') selected  @endif value="" selected>Select Gender</option>
                                        <option @if($applicant->gender == 'Male') selected  @endif value="Male">Male</option>
                                        <option @if($applicant->gender == 'Female') selected  @endif value="Female">Female</option>
                                    </select>
                                </div>
     
                                <!--end col-->
                                <div class="col-lg-12 border-top border-top-dashed">
                                    <div class="d-flex align-items-start gap-3 mt-3">
                                        <button type="submit" id="submit-button" class="btn btn-primary btn-label right ms-auto nexttab" data-nexttab="pills-bill-address-tab"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i> Submit</button>
                                    </div>
                                </div>
                                <!--end col-->
                            </div>    
                        </form>
                    </div>
                </div>

            </div>
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>
@elseif(
    !empty($programmeCategory->academicSessionSetting) &&
    strtolower($programmeCategory->academicSessionSetting->accommodation_booking_status) != 'start' &&
    $student->academic_session != $programmeCategory->academicSessionSetting->admission_session
)<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <div class="row justify-content-center">
                        <div class="col-lg-9">
                            <h4 class="mt-4 fw-semibold">Hostel Booking</h4>
                            <p class="text-muted mt-3">Hello! We're thrilled to have you with us. Please note that the booking of accommodation has not started yet. Keep an eye out for further announcements, and we'll let you know as soon as it begins. Thank you for your patience!</p>
                            <div class="mt-4">
                               
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-center mt-5 mb-2">
                        <div class="col-sm-7 col-8">
                            <img src="{{asset('assets/images/house_1.png')}}" alt="" class="img-fluid" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>
@else
    @if(empty($allocatedRoom))
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        <div class="row justify-content-center">
                            <div class="col-lg-9">
                                <div class="row justify-content-center mt-5 mb-2">
                                    <div class="col-sm-7 col-8">
                                        <img src="{{asset('assets/images/house_3.png')}}" alt="" class="img-fluid" />
                                    </div>
                                </div>

                                <h4 class="mt-4 fw-semibold">Hostel Booking</h4>
                                <p class="text-muted mt-3">Great news! The booking of accommodation has officially started. To secure your room, please click the button below and make your selection. We look forward to welcoming you soon!</p>
                                @if($hostelPaymentTx)
                                <h4 class="mt-4 text-warning">We have recognized a payment of ₦{{ number_format($hostelPaymentTx->amount_payed/100, 2) }}. Please proceed to select a room. <br>If you have already made a payment but are still being prompted to pay, kindly contact the bursary for assistance.</h4>
                                @endif

                                <h4 class="mt-4 text-danger">Accommodation bookings for Rev. James Abolarin Hostel are handled manually. Payments should be directed to the TAU Accommodation Account.</h4>
                                <div class="mt-4">
                                    <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                                        Click here to book a space
                                    </button>

                                    <div class="collapse hide mt-5" id="collapseExample">
                                        <form action="{{ url('/student/makePayment') }}" method="POST">
                                            @csrf
                                            <div class="row g-3">
                            
                                                <input type="hidden" name="payment_id" value="{{ $hostelPayment->id }}">
                                                <input type="hidden" name="hostel_payment_id" value="{{ !empty($hostelPaymentTx) ? $hostelPaymentTx->id:null }}">
                                                
                                                <div class="col-lg-12">
                                                    <div class="form-floating">
                                                        <select class="form-select" id="campus" aria-label="role" name="campus" onchange="handleCampusChange(event)" required>
                                                            <option selected value="">Select Option </option>
                                                            <option value="West">West Campus</option>
                                                            <option value="East">East Campus</option>
                                                        </select>
                                                        <label for="campus" class="form-label">Select Campus</label>
                                                    </div>
                                                </div>
                            
                                                <div class="col-lg-12">
                                                    <div class="form-floating">
                                                        <select class="form-select"  id="hostel" name="hostel_id" aria-label="hostel" onchange="handleHostelChange(event)" required>
                                                            <option value="" selected>--Select--</option>
                                                        </select>
                                                        <label for="hostel">Hostel</label>
                                                    </div>
                                                </div>
                                                
                            
                                                <div class="col-lg-12">
                                                    <div class="form-floating">
                                                        <select class="form-select" id="roomType" name="type_id" aria-label="roomType" onchange="handleRoomTypeChange(event)" required>
                                                            <option value="" selected>--Select--</option>
                                                        </select>
                                                        <label for="roomType">Room Type</label>
                                                    </div>
                                                </div>
                            
                            
                                                {{-- <div class="col-lg-12">
                                                    <div class="form-floating">
                                                        <select class="form-select selectRoom" id="room" name="room_id" aria-label="room" required>
                                                            <option value="" selected>--Select--</option>
                                                        </select>
                                                        <label for="roomType">Rooms</label>
                                                    </div>
                                                </div> --}}
                                                
                                                @if(empty($hostelPaymentTx))
                                                <div class="col-lg-12">
                                                    <div class="form-floating">
                                                        <select class="form-select" aria-label="paymentGateway" name="paymentGateway" required>
                                                            <option value= "" selected>Select Payment Gateway</option>
                                                            @if(env('UPPERLINK_STATUS'))<option value="Upperlink">Upperlink</option>@endif
                                                            @if(env('FLUTTERWAVE_STATUS'))<option value="Rave">Flutterwave</option>@endif
                                                            @if(env('MONNIFY_STATUS'))<option value="Monnify">Monnify</option>@endif
                                                            @if(env('PAYSTACK_STATUS'))<option value="Paystack">Paystack</option>@endif
                                                            @if(env('WALLET_STATUS'))<option value="Wallet">Wallet</option>@endif
                                                        </select>
                                                        <label for="paymentGateway" class="form-label">Select Payment Gateway</label>
                                                    </div>
                                                </div>
                                                @endif
                                               
                                                <button type="submit" id="submit-button" class="btn btn-fill btn-primary btn-lg btn-block mb-5">Book Now</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <!--end card-->
        </div>
        <!--end col-->
    </div>
    @else
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        <div class="row justify-content-center">
                            <div class="col-lg-9">
                                <div class="row justify-content-center mt-5 mb-2">
                                    <div class="col-sm-7 col-8">
                                        <img src="{{ asset('assets/images/house_2.png') }}" alt="" class="img-fluid" />
                                    </div>
                                </div>
    
                                <h4 class="mt-4 fw-semibold">Hostel Booking</h4>
    
                                @if($allocatedRoom)
                                    <p class="text-muted mt-3">
                                        Congratulations! You have been successfully allocated a room.
                                        Below are your accommodation details:
                                    </p>
                                    <hr>
                                    <div class="mt-4 text-start">
                                        <p><strong>Campus:</strong> {{ $allocatedRoom->room->type->campus }} Campus</p>
                                        <p><strong>Hostel:</strong> {{ $allocatedRoom->room->hostel->name }}</p>
                                        <p><strong>Room Number:</strong> {{ $allocatedRoom->room->number }}</p>
                                        <p><strong>Room Type:</strong> {{ $allocatedRoom->room->type->name }} -  {{ $allocatedRoom->room->type->capacity }} Bed Space(s)</p>
                                        <p><strong>Bed Space:</strong> {{ $allocatedRoom->bedSpace->space }}</p>
                                    </div>
                                @else
                                    <p class="text-muted mt-3">
                                        You have not been allocated a room yet. Please wait for further updates.
                                    </p>
                                @endif
    
                                <hr>
                                <div class="mt-4">
                                    <button class="btn btn-primary" onclick="location.href='{{ url('/student/home') }}'">
                                        Go to Dashboard
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end card-->
        </div>
        <!--end col-->
    </div>
    @endif                     
@endif

@endsection
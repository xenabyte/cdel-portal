@extends('student.layout.dashboard')

@section('content')
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

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <div class="row justify-content-center">
                        <div class="col-lg-9">
                            <h4 class="mt-4 fw-semibold">Hostel Booking</h4>
                            <p class="text-muted mt-3"></p>
                            <div class="mt-4">
                                <form action="{{ url('/student/makePayment') }}" method="POST">
                                    @csrf
                                    <div class="row g-3">

                                        <input type="hidden" name="payment_id" value="{{ $hostelPayment->id }}">
                                        
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


                                        <div class="col-lg-12">
                                            <div class="form-floating">
                                                <select class="form-select" id="room" name="room_id" aria-label="room" required>
                                                    <option value="" selected>--Select--</option>
                                                </select>
                                                <label for="roomType">Rooms</label>
                                            </div>
                                        </div>

                                        <div class="col-lg-12">
                                            <div class="form-floating">
                                                <select class="form-select" aria-label="paymentGateway" name="paymentGateway" required>
                                                    <option value= "" selected>Select Payment Gateway</option>
                                                    @if(env('UPPERLINK_STATUS'))<option value="Upperlink">Upperlink</option>@endif
                                                    @if(env('FLUTTERWAVE_STATUS'))<option value="Rave">Flutterwave</option>@endif
                                                    @if(env('MONNIFY_STATUS'))<option value="Monnify">Monnify</option>@endif
                                                    @if(env('PAYSTACK_STATUS'))<option value="Paystack">Paystack</option>@endif
                                                    @if(env('BANK_TRANSFER_STATUS'))<option value="BankTransfer">Transfer</option>@endif
                                                    @if(env('WALLET_STATUS'))<option value="Wallet">Wallet</option>@endif
                                                </select>
                                                <label for="paymentGateway" class="form-label">Select Payment Gateway</label>
                                            </div>
                                        </div>
                                       
                                        <button type="submit" id="submit-button" class="btn btn-fill btn-primary btn-lg btn-block mb-5">Book Now</button>
                                    </div>
                                </form>
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

@endsection
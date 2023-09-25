@extends('layouts.dashboard')

@section('content')


<div class="row justify-content-center">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <div class="row justify-content-center">
                        <div class="col-lg-9">
                            <h4 class="mt-4 fw-semibold">Student Onboarding</h4>
                            <p class="text-muted mt-3">
                                We are thrilled to welcome you to our brand new student portal! 🎉 Your academic journey just got a whole lot easier. Our portal is designed to simplify your student experience, providing you with easy access to essential resources and information. 
                                <br><br> We are committed to making your educational journey as smooth as possible, and this portal is a big step in that direction. Explore it, familiarize yourself with its features, and don't hesitate to reach out if you have any questions.

                                <br><br> Once again, welcome aboard! We look forward to seeing you excel in your studies.
                            </p>
                            <div class="mt-4">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                   Click here to onboard
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-center mt-5 mb-2">
                        <div class="col-sm-7 col-8">
                            <img src="assets/images/verification-img.png" alt="" class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header p-3">
                <h5 class="modal-title text-uppercase" id="exampleModalLabel">Student Onboarding</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('student/saveStudentDetails') }}" class="checkout-tab border-top border-top-dashed" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-lg-12">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="matricNumber" name="matric_number" placeholder="Enter your matric number">
                                <label for="matricNumber">Matric Number</label>
                            </div>
                        </div>

                        <div class="col-lg-12 border-top border-top-dashed">
                            <div class="d-flex align-items-start gap-3 mt-3">
                                <button type="button" class="btn btn-primary btn-label right ms-auto validate-button" data-nexttab="pills-bill-address-tab">
                                    <i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i> Validate
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="hidden-fields row mt-3 g-3">
                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Enter your lastname">
                                <label for="lastname">Lastname(Surname)</label>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="othernames" name="othernames" placeholder="Enter your othernames">
                                <label for="othernames">Othernames</label>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-floating">
                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email">
                                <label for="email">TAU Student Email</label>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your Passowrd">
                                <label for="password">Password</label>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="password" class="form-control" id="confirm-password" name="confirm_password" placeholder="Enter your email">
                                <label for="confirm-password">Confirm Password</label>
                            </div>
                        </div>

                        <input type="hidden" id="student_id" name="student_id">

                        <!--end col-->
                        <div class="col-lg-12 border-top border-top-dashed">
                            <div class="d-flex align-items-start gap-3 mt-3">
                                <button type="submit" class="btn btn-primary btn-label right ms-auto nexttab" data-nexttab="pills-bill-address-tab"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i> Submit</button>
                            </div>
                        </div>
                        <!--end col-->
                    </div>                        
                </div>
                <!--end modal-body-->
            </form>
        </div>
    </div>
</div>

@endsection
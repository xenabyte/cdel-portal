@extends('partner.layout.dashboard')
<?php 
    $partner = Auth::guard('partner')->user();
?>
@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Partner Approval</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Partner Approval</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <div class="row justify-content-center">
                        <div class="col-lg-9">
                            <h4 class="mt-4 fw-semibold">Partner Approval</h4>
                            <p class="text-muted mt-3">
                                We appreciate your interest in joining our affiliate marketing program to bring students to our school. Your enthusiasm and efforts are highly valued. <br><br>

                                We are truly excited to have you as part of our team and look forward to a successful partnership. Once your account is approved, you'll gain access to all the platform to kick-start your marketing journey. <br><br>

                                Thank you for choosing to work with us, and we can't wait to see the positive impact we can create together. <br><br>

                                If you have any questions or need further assistance, please don't hesitate to reach out to our affiliate program coordinator at {{ env('APP_EMAIL') }}.
                            </p>
                        </div>
                    </div>

                    <div class="row justify-content-center mt-5 mb-2">
                        <div class="col-sm-7 col-8">
                            <img src="{{asset('assets/images/approval.png')}}" alt="" class="img-fluid" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>
<!--end row-->

@endsection

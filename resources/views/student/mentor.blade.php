@extends('student.layout.dashboard')
@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Student Mentor Profile</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Student Mentor Profile</li>
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
                            <h4 class="mt-4 fw-semibold">Mentor Profile</h4>
                            <p class="text-muted mt-3">We encourage you to make the most of your mentorship journey by ensuring regular interactions with your mentor. Aim to connect at least once a week to maximize your learning and growth opportunities. Your mentor is here to support and guide you throughout this enriching experience.</p>
                            @if(!empty($mentor))
                            <div class="mt-4">
                                <div class="mx-auto avatar-md mb-3">
                                    <img src="{{ !empty($mentor->image) ? $mentor->image : asset('assets/images/users/user-dummy-img.jpg') }}" alt="" class="img-fluid rounded-circle">
                                </div>
                                <h5 class="card-title mb-1">{{ $mentor->title.' '.$mentor->lastname.' '. $mentor->othernames}}</h5>
                                <p class="text-muted mb-0">Email: {{$mentor->email}}</p>
                                <p class="text-muted mb-0">Phone Number: {{$mentor->phone_number}}</p>
                                @if(!empty($mentor->faculty))<p class="text-muted mb-0">Faculty: {{$mentor->faculty->name}}</p>@endif
                            </div>
                            @endif
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

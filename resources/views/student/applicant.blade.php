@extends('student.layout.dashboard')

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Applicant</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Applicant</li>
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
                                        <img src="{{empty($applicant->image)?asset('assets/images/users/user-dummy-img.jpg'): $applicant->image}}" alt="" class="img-fluid d-block" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h4 class="mt-3 alert alert-info">Admission Status: {{ empty($applicant->status)? 'Processing' : ucwords($applicant->status) }}</h4>
                        <br>
                    </div>
                    <!-- end col -->

                    <div class="col-xl-8">
                        <div class="mt-xl-0 mt-5">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <h4>{{ $applicant->lastname .' '. $applicant->othernames }}</h4>
                                    <div class="hstack gap-3 flex-wrap">
                                        <div><a href="#" class="text-primary d-block">{{ $applicant->programme->name }}</a></div>
                                        <div class="vr"></div>
                                        <div class="text-muted">Application ID : <span class="text-body fw-medium"> {{ $applicant->application_number }}</span></div>
                                        @if($applicant->application_type == 'UTME')
                                        <div class="vr"></div>
                                        <div class="text-muted">UTME Scores : <span class="text-body fw-medium"> {{ $applicant->utmes->sum('score') }}</span></div>
                                        @endif
                                        <div class="vr"></div>
                                        <div class="text-muted">Application Date : <span class="text-body fw-medium">{{ $applicant->updated_at }}</span></div>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <div>
                                    </div>
                                </div>
                            </div>

                            <div class="product-content mt-5">
                                <h5 class="fs-14 mb-3"> Applicant Information</h5>
                                <nav>
                                    <ul class="nav nav-tabs nav-tabs-custom nav-info" id="nav-tab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="nav-speci-tab" data-bs-toggle="tab" href="#biodata" role="tab" aria-controls="nav-speci" aria-selected="true">Bio Data</a>
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
                                                        <td>{{ $applicant->lastname .' '. $applicant->othernames }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Email</th>
                                                        <td>{{ $applicant->email }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Phone Number</th>
                                                        <td>{{ $applicant->phone_number }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Gender</th>
                                                        <td>{{ $applicant->gender }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Religion</th>
                                                        <td>{{ $applicant->religion }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Marital Status</th>
                                                        <td>{{ $applicant->marital_status }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Nationality</th>
                                                        <td>{{ $applicant->nationality }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">State of Origin</th>
                                                        <td>{{ $applicant->state }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Local Government Area</th>
                                                        <td>{{ $applicant->lga }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
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

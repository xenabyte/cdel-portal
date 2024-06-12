@php
    $staff = Auth::guard('staff')->user();

    $staffVCRole = false;
    $staffRegistrarRole = false;  
    $staffAdmissionOfficerRole = false;

    foreach ($staff->staffRoles as $staffRole) {
      
        if (strtolower($staffRole->role->role) == 'vice chancellor') {
            $staffVCRole = true;
        }
        if (strtolower($staffRole->role->role) == 'registrar') {
            $staffRegistrarRole = true;
        }
        if(strtolower($staffRole->role->role) == 'admission'){
            $staffAdmissionOfficerRole = true;
        }  
    }
@endphp
@extends('staff.layout.dashboard')

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

                        @if($staffAdmissionOfficerRole || $staffRegistrarRole || $staffVCRole)
                            @if($applicant->status == 'submitted')
                            <form action="{{ url('staff/manageAdmission') }}" method="POST">
                                @csrf
                                <input type="hidden" name="applicant_id" value="{{ $applicant->id }}">
                                <div class="mb-3">
                                    <label for="programme" class="form-label">Programmes</label>
                                    <select class="form-select" name="programme_id" id="programme" data-choices data-choices-search-false required>
                                        @foreach($programmes as $programme)<option @if($programme->id == $applicant->programme_id) selected  @endif value="{{ $programme->id }}">{{ $programme->name }}</option>@endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="level" class="form-label">Level</label>
                                    <select class="form-select" name="level_id" id="level" data-choices data-choices-search-false required>
                                        <option value="" selected>Choose...</option>
                                        @foreach($levels as $academicLevel)<option value="{{ $academicLevel->id }}">{{ $academicLevel->level }}</option>@endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="choices-batch-input" class="form-label">Batch</label>
                                    <select class="form-select" name="batch" id="choices-batch-input" data-choices data-choices-search-false required>
                                        <option value="" selected>Choose...</option>
                                        <option value="A">Batch A</option>
                                        <option value="B">Batch B</option>
                                        <option value="C">Batch C</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="choices-publish-status-input" class="form-label">Manage Application</label>
                                    <select class="form-select" name="status" id="choices-publish-status-input" data-choices data-choices-search-false required>
                                        <option value="" selected>Choose...</option>
                                        <option value="Admitted">Admitted</option>
                                        <option value="Declined">Declined</option>
                                    </select>
                                </div>

                                <br>
                                <button type="submit" id="submit-button" class="btn btn-lg btn-primary"> Submit</button>
                            </form>
                            @endif
                        @endif
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
                                        
                                        <li class="nav-item">
                                            <a class="nav-link" id="nav-detail-tab" data-bs-toggle="tab" href="#olevel" role="tab" aria-controls="nav-detail" aria-selected="false">Olevel Result</a>
                                        </li>
                                        @if($applicant->application_type == 'UTME')
                                        <li class="nav-item">
                                            <a class="nav-link" id="nav-detail-tab" data-bs-toggle="tab" href="#utme" role="tab" aria-controls="nav-detail" aria-selected="false">UTME Result</a>
                                        </li>
                                        @endif

                                        @if($applicant->application_type != 'UTME')
                                        <li class="nav-item">
                                            <a class="nav-link" id="nav-detail-tab" data-bs-toggle="tab" href="#de" role="tab" aria-controls="nav-detail" aria-selected="false">Direct Entry/Prev Institution Result</a>
                                        </li>
                                        @endif
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
                                                        <th scope="row">Date of Birth</th>
                                                        <td>{{date('F j, Y', strtotime($applicant->dob))}}</td>
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
                                    <div class="tab-pane fade" id="olevel" role="tabpanel" aria-labelledby="nav-detail-tab">
                                        <div>
                                            @if(!empty($applicant->olevel_1))
                                            <h5 class="fs-14 mb-3"> Schools Attended</h5>
                                            {!! $applicant->schools_attended !!}
                                            <hr>
                                            <div class="row mb-2">
                                                <div class="col-sm-6 col-xl-12">
                                                    <!-- Simple card -->
                                                    <i class="bx bxs-file-jpg text-danger" style="font-size: 50px"></i><span style="font-size: 20px">Olevel Result</span>
                                                    <div class="text-end">
                                                        <a href="{{ asset($applicant->olevel_1) }}" target="blank" class="btn btn-success">View</a>
                                                    </div>
                                                    @if($applicant->sitting_no > 1)
                                                    <i class="bx bxs-file-jpg text-danger" style="font-size: 50px"></i><span style="font-size: 20px">Olevel Result (Second Sitting)</span>
                                                    <div class="text-end">
                                                        <a href="{{ asset($applicant->olevel_2) }}" target="blank"  class="btn btn-success">View</a>
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
                                                    @foreach($applicant->olevels as $olevel)
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
                                    @if($applicant->application_type == 'UTME')
                                    <div class="tab-pane fade" id="utme" role="tabpanel" aria-labelledby="nav-detail-tab">
                                        <div>
                                            @if(!empty($applicant->utme))
                                            <div class="row mb-2">
                                                <div class="col-sm-6 col-xl-12">
                                                    <!-- Simple card -->
                                                    <i class="bx bxs-file-jpg text-danger" style="font-size: 50px"></i><span style="font-size: 20px">UTME Result Printout</span>
                                                    <div class="text-end">
                                                        <a href="{{ asset($applicant->utme) }}"  target="blank" class="btn btn-success">View</a>
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
                                                    @foreach($applicant->utmes as $utme)
                                                    <tr>
                                                        <th scope="row">{{ $loop->iteration }}</th>
                                                        <td>{{ $utme->subject }}</td>
                                                        <td>{{ $utme->score }}</td>
                                                    </tr>
                                                    @endforeach
                                                    <tr>
                                                        <th scope="row"></th>
                                                        <td>Total</td>
                                                        <td><strong>{{$applicant->utmes->sum('score')}}</strong></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    @endif
                                    @if($applicant->application_type != 'UTME')
                                    <div class="tab-pane fade" id="de" role="tabpanel" aria-labelledby="nav-detail-tab">
                                        <div>
                                            <h5 class="fs-14 mb-3"> Institution Attended</h5>
                                            {!! $applicant->de_school_attended !!}
                                            <hr>
                                            @if(!empty($applicant->de_result))
                                            <div class="row mb-2">
                                                <div class="col-sm-6 col-xl-12">
                                                    <!-- Simple card -->
                                                    <i class="bx bxs-file-jpg text-danger" style="font-size: 50px"></i><span style="font-size: 20px">Direct Entry/Prev Institution/Prev Institution Result</span>
                                                    <div class="text-end">
                                                        <a href="{{ asset($applicant->de_result) }}"  target="blank" class="btn btn-success">View</a>
                                                    </div>
                                                </div><!-- end col -->
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
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

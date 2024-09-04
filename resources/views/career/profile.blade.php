@php
    $formSession =  session('previous_section');
    $career = Auth::guard('career')->user();
    $percent = $career->calculateProfileCompletion();
@endphp
@extends('career.layout.dashboard')

@section('content')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Profile</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Profile</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="container-fluid mt-5">
    <div class="profile-foreground position-relative mx-n4 mt-n4">
        <div class="profile-wid-bg">
            <img src="{{ asset('assets/images/profile-bg.jpg') }}" alt="" class="profile-wid-img" />
        </div>
    </div>
    <div class="pt-4 mb-4 mb-lg-3 pb-lg-4">
        <div class="row g-4">
            <div class="col-auto">
                <div class="avatar-lg">
                    <img src="{{asset(empty($career->image)?'assets/images/users/user-dummy-img.jpg':$career->image)}}" alt="user-img" class="img-thumbnail rounded-circle" />
                </div>
            </div>
            <!--end col-->
            <div class="col">
                <div class="p-2">
                    <h3 class="text-white mb-1">{{ $career->lastname.' '. $career->othernames}}</h3>
                    <div class="hstack text-white-50 gap-1">
                    </div>
                </div>
            </div>
            <!--end col-->
            <div class="col-12 col-lg-auto order-last order-lg-0">
                <div class="row text text-white-50 text-center">
                    <div class="col-lg-6 col-4">
                        <div class="p-2">
                           
                        </div>
                    </div>
                    <div class="col-lg-6 col-4">
                        <div class="p-2">
                            
                        </div>
                    </div>
                </div>
            </div>
            <!--end col-->

        </div>
        <!--end row-->
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div>
                <div class="d-flex">
                    <!-- Nav tabs -->
                    <ul class="nav nav-pills animation-nav profile-nav gap-2 gap-lg-3 flex-grow-1" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link fs-14 {{ $formSession == 'bio-data'?'active':null }} {{ $formSession == ''?'active':null }}" data-bs-toggle="tab" href="#overview-tab" role="tab">
                                <i class="ri-airplay-fill d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Bio-Data</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link fs-14 {{ $formSession == 'educationHistory'?'active':null }}" data-bs-toggle="tab" href="#educationHistory" role="tab">
                                <i class="ri-price-tag-line d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Education History</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link fs-14 {{ $formSession == 'professionalInformation'?'active':null }}" data-bs-toggle="tab" href="#professionalInformation" role="tab">
                                <i class="ri-folder-4-line d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Professional Information</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link fs-14 {{ $formSession == 'publications'?'active':null }}" data-bs-toggle="tab" href="#publications" role="tab">
                                <i class="ri-folder-4-line d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Publications</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link fs-14 {{ $formSession == 'CV'?'active':null }}" data-bs-toggle="tab" href="#CV" role="tab">
                                <i class="ri-folder-4-line d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">CV</span>
                            </a>
                        </li>


                    </ul>
                   
                </div>
                <!-- Tab panes -->
                <div class="tab-content pt-4 text-muted">
                    <div class="tab-pane {{ $formSession == 'bio-data'?'active':null }} {{ $formSession == ''?'active':null }}" id="overview-tab" role="tabpanel">
                        <div class="row">
                            @include('career.layout.clearanceProgress')

                            <!--end col-->
                            <div class="col-xxl-8">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Bio-Data</h5>
                                        <form action="{{ url('career/manageProfile') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <div class="col-lg-12 text-center">
                                                    <div class="profile-user position-relative d-inline-block mx-auto mb-2">
                                                        @if(empty($career->image))
                                                        <img src="{{asset('assets/images/users/user-dummy-img.jpg')}}" class="rounded-circle avatar-lg img-thumbnail user-profile-image" alt="user-profile-image">
                                                        @else
                                                        <img src="{{asset($career->image)}}" class="rounded-circle avatar-lg img-thumbnail user-profile-image" alt="user-profile-image">
                                                        @endif
                                                        <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                                                            <input id="profile-img-file-input" type="file" class="profile-img-file-input" accept="image/png, image/jpeg" name="image" required>
                                                            <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                                                                <span class="avatar-title rounded-circle bg-light text-body">
                                                                    <i class="ri-camera-fill"></i>
                                                                </span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <h5 class="fs-14">Add Passport Photograph</h5>
                                                </div>
                                                <hr>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <div class="mb-3">
                                                        <label for="lastname" class="form-label">Last Name(Surname)</label>
                                                        <input type="text" class="form-control" id="lastname" name="lastname" value="{{ $career->lastname }}" disabled readonly>
                                                    </div>
                                                </div>
    
                                                <div class="col-lg-8">
                                                    <div class="mb-3">
                                                        <label for="othernames" class="form-label">Other Names</label>
                                                        <input type="text" class="form-control" id="othernames" name="othernames" value="{{ $career->othernames }}" disabled readonly>
                                                    </div>
                                                </div>
                                                <!--end col-->
    
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label for="phone_number" class="form-label">Phone Number</label>
                                                        <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ $career->phone_number }}" disabled  readonly>
                                                    </div>
                                                </div>
    
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label for="email" class="form-label">Email Address</label>
                                                        <input type="text" class="form-control" id="email" name="email" value="{{ $career->email }}" disabled  readonly>
                                                    </div>
                                                </div>
                                                <hr>
    
                                                <div class="col-lg-12">
                                                    <label for="biodata">Bio Data (Including above information, date of birth, address, etc.)</label>
                                                    <textarea class="ckeditor" id="biodata" name="biodata">{!! $career->profile? $career->profile->biodata: null !!} </textarea>
                                                </div><!--end col-->
    
                                                <hr>
                                                <div class="col-lg-12">
                                                    <div class="hstack gap-2 justify-content-end">
                                                        <button type="submit" id="submit-button" class="btn btn-primary">Save</button>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                            </div>
                                            <!--end row-->
                                        </form>
                                    </div>
                                    <!--end card-body-->
                                </div><!-- end card -->

                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </div>


                    <!--end tab-pane-->
                    <div class="tab-pane {{ $formSession == 'educationHistory' ? 'active' : '' }}" id="educationHistory" role="tabpanel">
                        <div class="row">
                            @include('career.layout.clearanceProgress')
                    
                            <div class="col-xxl-8">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Education History</h5>
                                        <form action="{{ url('career/manageProfile') }}" method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="mb-3">
                                                        <label for="education_history" class="form-label">Education History</label>
                                                        <textarea class="ckeditor" id="education_history" name="education_history">
                                                            {{ $career->profile->education_history ?? '' }}
                                                        </textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="col-lg-12">
                                                <div class="hstack gap-2 justify-content-end">
                                                    <button type="submit" class="btn btn-primary">Save</button>
                                                </div>
                                            </div>
                                        </form>
                    
                                        <!-- Display the saved Education History -->
                                        @if(!empty($career->profile->education_history))
                                        <div class="mt-4">
                                            <h5>Saved Information:</h5>
                                            {!! $career->profile->education_history !!}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    

                    <div class="tab-pane {{ $formSession == 'professionalInformation' ? 'active' : '' }}" id="professionalInformation" role="tabpanel">
                        <div class="row">
                            @include('career.layout.clearanceProgress')
                    
                            <div class="col-xxl-8">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Professional Information</h5>
                                        <form action="{{ url('career/manageProfile') }}" method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="mb-3">
                                                        <label for="professional_information" class="form-label">Professional Information</label>
                                                        <textarea class="ckeditor" id="professional_information" name="professional_information">
                                                            {{ $career->profile->professional_information ?? '' }}
                                                        </textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="col-lg-12">
                                                <div class="hstack gap-2 justify-content-end">
                                                    <button type="submit" class="btn btn-primary">Save</button>
                                                </div>
                                            </div>
                                        </form>
                    
                                        <!-- Display the saved Professional Information -->
                                        @if(!empty($career->profile->professional_information))
                                        <div class="mt-4">
                                            <h5>Saved Information:</h5>
                                            {!! $career->profile->professional_information !!}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    

                    

                    <div class="tab-pane {{ $formSession == 'publications' ? 'active' : '' }}" id="publications" role="tabpanel">
                        <div class="row">
                            @include('career.layout.clearanceProgress')
                    
                            <div class="col-xxl-8">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Publications</h5>
                                        <form action="{{ url('career/manageProfile') }}" method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="mb-3">
                                                        <label for="publications" class="form-label">Publications</label>
                                                        <textarea class="ckeditor" id="publications" name="publications">
                                                            {{ $career->profile->publications ?? '' }}
                                                        </textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="col-lg-12">
                                                <div class="hstack gap-2 justify-content-end">
                                                    <button type="submit" class="btn btn-primary">Save</button>
                                                </div>
                                            </div>
                                        </form>
                    
                                        <!-- Display the saved Publications -->
                                        @if(!empty($career->profile->publications))
                                        <div class="mt-4">
                                            <h5>Saved Information:</h5>
                                            {!! $career->profile->publications !!}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    

                    <div class="tab-pane {{ $formSession == 'CV' ? 'active' : '' }}" id="CV" role="tabpanel">
                        <div class="row">
                            @include('career.layout.clearanceProgress')
                    
                            <div class="col-xxl-8">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">CV Upload <small>File should be CV and your credentials in a single file</small></h5>
                                        <form action="{{ url('career/manageProfile') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="mb-3">
                                                        <label for="cv_path" class="form-label">Upload CV</label>
                                                        <input type="file" class="form-control" id="cv_path" name="cv_path" accept=".pdf,.doc,.docx" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="col-lg-12">
                                                <div class="hstack gap-2 justify-content-end">
                                                    <button type="submit" class="btn btn-primary">Save</button>
                                                </div>
                                            </div>
                                        </form>
                    
                                        <!-- Display the uploaded CV -->
                                        @if(!empty($career->profile->cv_path))
                                        <div class="mt-4">
                                            <h5>Uploaded CV:</h5>
                                            <a href="{{ asset($career->profile->cv_path) }}" target="_blank">Download CV</a>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                <!--end tab-content-->
            </div>
        </div>
        <!--end col-->
    </div>
    <!--end row-->

</div><!-- container-fluid -->
@endsection
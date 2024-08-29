@php
    $formSession =  session('previous_section');
    $career = Auth::guard('career')->user();

    $percent = 1;
        $total = 7;

        if(!empty($career->lastname)){
            $percent = $percent + 1;
        }
        if(!empty($career->profile)){
            $percent = $percent + 1;
        }
        if(!empty($career->profile->biodata)){
            $percent = $percent + 1;
        }
        if(!empty($career->profile->education_history)){
            $percent = $percent + 1;
        }
        if(!empty($career->profile->professional_information)){
            $percent = $percent + 1;
        }
        if(!empty($career->profile->publications)){
            $percent = $percent + 1;
        }
        if(!empty($career->profile->cv_path)){
            $percent = $percent + 1;
        }

        $percent = round(($percent/$total)*100);
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
                                <i class="ri-airplay-fill d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Overview</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link fs-14 {{ $formSession == 'olevel'?'active':null }}" data-bs-toggle="tab" href="#olevel" role="tab">
                                <i class="ri-price-tag-line d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Olevel Result</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link fs-14 {{ $formSession == 'utme'?'active':null }}" data-bs-toggle="tab" href="#jamb" role="tab">
                                <i class="ri-folder-4-line d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Jamb Result</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link fs-14 {{ $formSession == 'guardian'?'active':null }}" data-bs-toggle="tab" href="#guardian" role="tab">
                                <i class="ri-folder-4-line d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Guardian</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link fs-14 {{ $formSession == 'nok'?'active':null }}" data-bs-toggle="tab" href="#nok" role="tab">
                                <i class="ri-folder-4-line d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Next of Kin</span>
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
                                                    <label for="address">Address</label>
                                                    <textarea class="ckeditor" id="address" name="address" ></textarea>
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
                    <div class="tab-pane {{ $formSession == 'olevel'?'active':null }}" id="olevel" role="tabpanel">
                        <div class="row">
                            @include('career.layout.clearanceProgress')
    
                            <div class="col-xxl-8">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">O'Level Results:</h5>
                                            <hr>
                                          
                                    </div>
                                    <!--end card-body-->
                                </div>
                                <!--end card-->
                            </div>
                        </div>
                    </div>

                    <!--end tab-pane-->
                    <div class="tab-pane {{ $formSession == 'utme'?'active':null }}" id="jamb" role="tabpanel">
                        <div class="row">
                            @include('career.layout.clearanceProgress')
    
                            <div class="col-xxl-8">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="mb-4 pb-2">
                                            <h5 class="card-title mb-3">UTME Results:</h5>
                                            <hr>
                                           
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    

                    <div class="tab-pane {{ $formSession == 'guardian'?'active':null }}" id="guardian" role="tabpanel">
                        <div class="row">
                            @include('career.layout.clearanceProgress')
    
                            <div class="col-xxl-8">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Guardian Information</h5>
                                        <hr>
                                       
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane {{ $formSession == 'nok'?'active':null }}" id="nok" role="tabpanel">
                        <div class="row">
                            @include('career.layout.clearanceProgress')
    
                            <div class="col-xxl-8">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Next of Kin Information</h5>
                                        <hr>
                                        
                                    </div>
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
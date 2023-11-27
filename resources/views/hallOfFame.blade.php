@extends('layouts.dashboard')

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Hall of Fame</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Hall of Fame</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-lg-12">
        <div class="">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <ul class="list-inline categories-filter animation-nav" id="filter">
                                <li class="list-inline-item"><a class="categories active" data-filter="*">All</a></li>
                                <li class="list-inline-item"><a class="categories" data-filter=".100">100 Level</a></li>
                                <li class="list-inline-item"><a class="categories" data-filter=".200">200 Level</a></li>
                                <li class="list-inline-item"><a class="categories" data-filter=".300">300 Level</a></li>
                            </ul>
                        </div>

                        <div class="row gallery-wrapper">
                            @foreach($students as $hofStudent)
                            @if(!empty($hofStudent->image))
                            <div class="element-item col-xxl-3 col-xl-4 col-sm-6 {{ ($hofStudent->level_id - 1) * 100 }}" data-category="{{ ($hofStudent->level_id - 1) * 100 }}">
                                <div class="gallery-box card">
                                    <div class="gallery-container">
                                        <a class="image-popup" href="{{ !empty($hofStudent->image) ? asset($hofStudent->image) : asset('assets/images/users/user-dummy-img.jpg') }}" title="">
                                            <img class="gallery-img img-fluid mx-auto" src="{{ !empty($hofStudent->image) ? asset($hofStudent->image) : asset('assets/images/users/user-dummy-img.jpg') }}" alt="" />
                                            <div class="gallery-overlay">
                                                <h5 class="overlay-caption">{{ $hofStudent->applicant->lastname .' '. $hofStudent->applicant->othernames }}</h5>
                                            </div>
                                        </a>
                                    </div>

                                    <div class="box-content">
                                        <div class="d-flex align-items-center mt-1">
                                            <div class="flex-grow-1 text-muted"> <a href="{{ url('studentDetails/'.$hofStudent->slug) }}" class="text-body text-truncate">{{ $hofStudent->applicant->lastname .' '. $hofStudent->applicant->othernames }}</a></div>
                                            <div class="flex-shrink-0">
                                                <div class="d-flex gap-3">
                                                    <button type="button" class="btn btn-md fs-12 btn-link text-body text-decoration-none px-0 shadow-none">
                                                        <i class="ri-sort-asc text-muted align-bottom me-1"></i> {{ ($hofStudent->level_id - 1) * 100 }} Level
                                                    </button>
                                                    <button type="button" class="btn btn-md fs-12 btn-link text-primary text-body text-decoration-none px-0 shadow-none">
                                                        <i class="ri-medal-2-line text-muted align-bottom me-1"></i> {{ ($hofStudent->cgpa) }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end col -->
                            @endif
                            @endforeach
                        </div>
                        
                    </div>
                </div>
                <!-- end row -->
            </div>
            <!-- ene card body -->
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end row -->

@endsection
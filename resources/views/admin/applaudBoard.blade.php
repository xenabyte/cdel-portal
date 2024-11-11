@extends('admin.layout.dashboard')

@section('content')
<div class="profile-foreground position-relative mx-n4 mt-n4">
    <div class="profile-wid-bg">
        <img src="{{ asset($applaudBoard->image) }}" alt="" class="profile-wid-img" />
    </div>
</div>

<div class="pt-4 mb-4 mb-lg-3 pb-lg-4 profile-wrapper">
    <div class="row g-4">
        <div class="col-auto">
            <div class="avatar-lg">
                <!-- Responsive Images -->
                <img src="{{ asset($applaudBoard->image) }}" alt="user-img" class="img-thumbnail rounded-circle" />
            </div>
        </div>
        <!--end col-->
        <div class="col">
            <div class="p-2">
                <h3 class="text-white mb-1">{{ $applaudBoard->title }}</h3>
                <p class="text-white text-opacity-75">{{ $applaudBoard->email }}</p>
            </div>
        </div>
        <!--end col-->
    </div>
    <!--end row-->
</div>

<div class="row">
    <div class="col-lg-12">
        <div>
            <div class="d-flex profile-wrapper">
                <!-- Nav tabs -->
                <ul class="nav nav-pills animation-nav profile-nav gap-2 gap-lg-3 flex-grow-1" role="tablist">
                    <li class="nav-item">
                        
                    </li>
                </ul>
            </div>
            <!-- Tab panes -->
            <div class="tab-content pt-4 text-muted">
                <div class="tab-pane active" id="overview-tab" role="tabpanel">
                    <div class="row">
                        <div class="col-xxl-3">
                            <div class="card">
                                <div class="card-body">
                                    <img src="{{ asset($applaudBoard->image) }}" class="img-fluid" alt="Responsive image">                                    
                                </div><!-- end card body -->
                            </div><!-- end card -->
                        </div>
                        <!--end col-->
                        <div class="col-xxl-9">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">Achievements</h5>
                                    {!! $applaudBoard->description !!}
                                </div>
                                <!--end card-body-->
                            </div><!-- end card -->

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-header align-items-center d-flex">
                                            <h4 class="card-title mb-0  me-2">Recent Activity</h4>
                                            <div class="flex-shrink-0 ms-auto">
                                                <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                                                    <li class="nav-item">
                                                       
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="tab-content text-muted">
                                                <div class="tab-pane active" id="today" role="tabpanel">
                                                    <div class="profile-timeline">
                                                        <div class="accordion accordion-flush" id="todayExample"> 
                                                            @foreach($applaudBoard->board_messages as $message)
                                                            <div class="accordion-item border-0">
                                                                <div class="accordion-header" id="headingFour">
                                                                    <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapse{{ $loop->iteration }}" aria-expanded="true">
                                                                        <div class="d-flex">
                                                                            <div class="flex-shrink-0 avatar-xs">
                                                                                <div class="avatar-title bg-light text-muted rounded-circle shadow">
                                                                                    <i class="ri-user-3-fill"></i>
                                                                                </div>
                                                                            </div>
                                                                            <div class="flex-grow-1 ms-3">
                                                                                <h6 class="fs-14 mb-1">
                                                                                    {{ $message->board_user->name }}
                                                                                </h6>
                                                                                <small class="text-muted">Commented on {{ date('F j, Y \a\t g:i A', strtotime($message->created_at)) }}</small>
                                                                            </div>
                                                                        </div>
                                                                    </a>
                                                                </div>
                                                                <div id="collapse{{ $loop->iteration }}" class="accordion-collapse collapse show" aria-labelledby="headingFour" data-bs-parent="#accordionExample">
                                                                    <div class="accordion-body ms-2 ps-5 fst-italic">
                                                                        "{{ $message->message }}"
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @endforeach

                                                        </div>
                                                        <!--end accordion-->
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!-- end card body -->
                                    </div><!-- end card -->
                                </div><!-- end col -->
                            </div><!-- end row -->

                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                </div>
            </div>
            <!--end tab-content-->
        </div>
    </div>
    <!--end col-->
</div>
<!--end row-->

@endsection
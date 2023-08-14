@extends('staff.layout.dashboard')

@section('content')


<div class="row project-wrapper">
    <div class="col-xxl-8">
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header border-0 align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Programme Overview - {{ Str::title(strtolower($programme->name)) }}</h4>
                    </div><!-- end card header -->

                    <div class="card-header p-0 border-0 bg-soft-light">
                        <div class="row g-0 text-center">
                            <div class="col-6 col-sm-3">
                                <div class="p-3 border border-dashed border-start-0">

                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-6 col-sm-3">
                                <div class="p-3 border border-dashed border-start-0">

                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-6 col-sm-3">
                                <div class="p-3 border border-dashed border-start-0">

                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-6 col-sm-3">
                                <div class="p-3 border border-dashed border-start-0 border-end-0">

                                </div>
                            </div>
                            <!--end col-->
                        </div>
                    </div><!-- end card header -->
                    <div class="card-body">
                        <p><strong>Programme Name: </strong> {{ Str::title(strtolower($programme->name)) }} </p>
                        <p><strong>Programme Duration: </strong> {{ $programme->duration }} Years </p>
                        <p><strong>Programme Max Duration: </strong> {{ $programme->max_duration }} Years </p>
                        <p><strong>Programme Code: </strong> {{ $programme->code }} </p>
                        <p><strong>Programme Code Number: </strong> {{ $programme->code_number }} </p>
                        <p><strong>Programme Last Matric Number: </strong> {{ $programme->matric_last_number }} </p>
                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end col -->
        </div><!-- end row -->
    </div><!-- end col -->
    <div class="col-xxl-4">
        <div class="card">
            <div class="card-header border-0">
                <h4 class="card-title mb-0">Handbook</h4>
            </div><!-- end cardheader -->
            <div class="card-body pt-0">
                @if(!empty($programme->handbook->handbook))
                <div class="avatar-lg">
                    <div class="avatar-title bg-soft-danger text-danger rounded fs-20 shadow">
                        <a href="{{ asset($programme->handbook->handbook) }}" target="_blank"><i class="ri-file-pdf-fill"></i></a>
                    </div>
                </div>
                @endif
            </div><!-- end cardbody -->
        </div><!-- end card -->
    </div><!-- end col -->
</div><!-- end row -->


<div class="row">
    <div class="col-xxl-12 col-lg-12">
        <div class="card card-height-100">
            
            <div class="card-body mb-3">
                @if(!empty($programme->courses))
                @include('admin.courses.year1')
                <hr>
                @include('admin.courses.year2')
                <hr>
                @include('admin.courses.year3')
                <hr>
                @include('admin.courses.year4')
                <hr>
                @include('admin.courses.year5')
                <hr>
                @include('admin.courses.year6')

            @endif
            </div><!-- end cardbody -->
        </div><!-- end card -->
    </div><!-- end col -->
</div><!-- end row -->

@endsection
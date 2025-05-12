@extends('student.layout.dashboard')

@section('content')

@if(env('SUMMER_COURSE_REGISTRATION'))
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <div class="row justify-content-center">
                        <div class="col-lg-9">
                            <h4 class="mt-4 fw-semibold">Course Registration for {{ $pageGlobalData->sessionSetting->academic_session }} Academic Session - Summer Semester</h4>
                            <p class="text-muted mt-3"></p>
                            <div class="mt-4">
                                <strong>Summer Course Registration has not started yet.</strong> Please check back later for updates.
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-center mt-5 mb-2">
                        <div class="col-sm-7 col-8">
                            <img src="{{asset('assets/images/done_creg.png')}}" alt="" class="img-fluid" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>
@else
<style>
    /* Adjust the width of the ID column */
    .table th:nth-child(1),
    .table td:nth-child(1) {
        width: 10px; /* Adjust the width as needed */
    }
    .semester-heading {
        font-weight: bold;
        font-size: 1.2em;
        padding: 10px 0;
    }
</style>
@if($existingSummerRegistration->count() > 0)
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <div class="row justify-content-center">
                        <div class="col-lg-9">
                            <h4 class="mt-4 fw-semibold">Course Registration for {{ $pageGlobalData->sessionSetting->academic_session }} academic session </h4>
                            <p class="text-muted mt-3"></p>
                            <div class="mt-4">
                                <h4 class="mt-4 fw-semibold">Course Registration for {{ $pageGlobalData->sessionSetting->academic_session }} Academic Session - Summer Semester</h4>
                            </div>
                            <div class="mt-4">
                                <form action="{{ url('/student/printSummerCourseReg') }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <button type="submit" id="submit-button" class="btn btn-info">
                                        Click here to download
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-center mt-5 mb-2">
                        <div class="col-sm-7 col-8">
                            <img src="{{asset('assets/images/done_creg.png')}}" alt="" class="img-fluid" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>
{{-- @else --}}

@endif
@endif

@endsection
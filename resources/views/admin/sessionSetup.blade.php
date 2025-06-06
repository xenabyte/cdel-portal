@extends('admin.layout.dashboard')

@section('content')
 <!-- start page title -->
 <div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Session Setup for {{ $programmeCategory->category }} Programme</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Session Setup</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">

    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Session Settings for {{ $programmeCategory->category }}</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-6 col-xl-12">
                        <form action="{{ url('/admin/setSession') }}" method="POST">
                            @csrf
                            <input type="hidden" name="sessionSetting_id" value="{{ !empty($sessionSetting)? $sessionSetting->id : ''}}">
                            <input type="hidden" name="programme_category_id" value="{{ $programmeCategory->id }}">
                            <div class="row g-3">

                                <div class="col-lg-4">
                                    <h4 class="card-title mb-0 flex-grow-1">Active Academic Session: {{ !empty($programmeCategory->academicSessionSetting)?$programmeCategory->academicSessionSetting->academic_session:'Not Set' }}</h4>
                                    <br>
                                    <div class="form-floating">
                                        <select class="form-select" id="academic_session" name="academic_session" aria-label="academic session">
                                            <option value="" selected>--Select--</option>
                                            @foreach($sessions as $session)<option value="{{$session->year}}">{{ $session->year}}</option>@endforeach
                                        </select>
                                        <label for="academic_session">Academic Session</label>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <h4 class="card-title mb-0 flex-grow-1">Active Admission Session: {{ !empty($programmeCategory->academicSessionSetting)?$programmeCategory->academicSessionSetting->admission_session:'Not Set' }}</h4>
                                    <br>
                                    <div class="form-floating">
                                        <select class="form-select" id="admission_session" name="admission_session" aria-label="admission session">
                                            <option value="" selected>--Select--</option>
                                            @foreach($sessions as $session)<option value="{{$session->year}}">{{ $session->year}}</option>@endforeach
                                        </select>
                                        <label for="admission_session">Admission Session</label>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <h4 class="card-title mb-0 flex-grow-1">Active Application Session: {{ !empty($programmeCategory->academicSessionSetting)?$programmeCategory->academicSessionSetting->application_session:'Not Set' }}</h4>
                                    <br>
                                    <div class="form-floating">
                                        <select class="form-select" id="application_session" name="application_session" aria-label="application session">
                                            <option value="" selected>--Select--</option>
                                            @foreach($sessions as $session)<option value="{{$session->year}}">{{ $session->year}}</option>@endforeach
                                        </select>
                                        <label for="application_session">Application Session</label>
                                    </div>
                                </div>
                                <hr>
                                <div class="col-lg-12">
                                    <div class="text-end">
                                        <button type="submit" id="submit-button" class="btn btn-primary">Update Settings</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div><!-- end col -->
                </div>
            </div>
        </div><!-- end card -->
    </div>


    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Fees Settings</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-6 col-xl-12">
                        <form action="{{ url('/admin/setFeeStatus') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="sessionSetting_id" value="{{ !empty($sessionSetting)? $sessionSetting->id : ''}}">
                            <input type="hidden" name="programme_category_id" value="{{ $programmeCategory->id }}">
                            
                            <div class="row g-3">

                                <div class="col-lg-4">
                                    <h4 class="card-title mb-0 flex-grow-1">School Fee Payment Status: {{ !empty($programmeCategory->academicSessionSetting)?$programmeCategory->academicSessionSetting->school_fee_status:'Not Set' }}</h4>
                                    <br>
                                    <div class="form-floating">
                                        <select class="form-select" id="school_fee_status" name="school_fee_status" aria-label="school_fee_status">
                                            <option value="" selected>--Select--</option>
                                            <option value="start">Start</option>
                                            <option value="stop">Stop</option>
                                        </select>
                                        <label for="school_fee_status">School Fee Payment Status</label>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <h4 class="card-title mb-0 flex-grow-1">Accomondation Booking Status: {{ !empty($programmeCategory->academicSessionSetting)?$programmeCategory->academicSessionSetting->accomondation_booking_status:'Not Set' }} </h4>
                                        <br>
                                        <div class="form-floating">
                                            <select class="form-select" id="accomondation_booking_status" name="accomondation_booking_status" aria-label="accomondation_booking_status">
                                                <option value="" selected>--Select--</option>
                                                <option value="start">Start</option>
                                                <option value="stop">Stop</option>
                                            </select>
                                            <label for="accomondation_booking_status">Accomondation Booking Status</label>
                                        </div>
                                </div>

                                 <div class="col-lg-4">
                                    <h4 class="card-title mb-0 flex-grow-1">Resumption Date: {{ !empty($programmeCategory->academicSessionSetting) ? date('l, jS F, Y', strtotime($programmeCategory->academicSessionSetting->resumption_date)) :'Not Set' }}</h4>
                                    <br>
                                    <div class="form-floating">
                                        <input type="date" class="form-control" id="resumption_date" name="resumption_date">
                                        <label for="resumption_date">Resumption Date</label>
                                    </div>
                                </div>

                                <hr>
                                <div class="col-lg-12">
                                    <div class="text-end">
                                        <button type="submit" id="submit-button" class="btn btn-primary">Update Settings</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div><!-- end col -->
                </div>
            </div>
        </div><!-- end card -->
    </div>

    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Exam Settings for {{ $programmeCategory->category }} programme, Academic session: {{ $programmeCategory->academicSessionSetting->academic_session }}</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div class="row mb-2">
                     <div class="col-sm-6 col-xl-12">
                        <form action="{{ url('/admin/setExamSetting') }}" method="POST">
                            @csrf
                            <input type="hidden" name="examSetting_id" value="{{ !empty($examDocketMgt)? $examDocketMgt->id : ''}}">
                            <input type="hidden" name="academic_session" value="{{ !empty($programmeCategory->academicSessionSetting)?$programmeCategory->academicSessionSetting->academic_session:null }}">
                            <input type="hidden" name="programme_category_id" value="{{ $programmeCategory->id }}">
                            <div class="row g-3">

                                <div class="col-lg-6">
                                    <h4 class="card-title mb-0 flex-grow-1">Semester: {{ !empty($programmeCategory->examSetting) ? ($programmeCategory->examSetting->semester == 1 ? env('FIRST_SEMESTER') : env('SECOND_SEMESTER')) : 'Not Set' }}</h4>
                                    <br>
                                    <div class="form-floating">
                                        <select class="form-select" id="semester" name="semester" aria-label="semester">
                                            <option value="" selected>--Select--</option>
                                            <option value="1">Harmattan Semester</option>
                                            <option value="2">Rain Semester</option>
                                        </select>
                                        <label for="academic_session">Semester</label>
                                    </div>
                                </div>


                                <div class="col-lg-6">
                                    <h4 class="card-title mb-0 flex-grow-1">Exam Docket Status: {{ !empty($programmeCategory->examSetting)?$programmeCategory->examSetting->exam_docket_status:'Not Set' }}</h4>
                                    <br>
                                    <div class="form-floating">
                                        <select class="form-select" id="exam_docket_status" name="exam_docket_status" aria-label="Exam Docket Status">
                                            <option value="" selected>--Select--</option>
                                            <option value="Start">Start</option>
                                            <option value="Stop">Stop</option>
                                        </select>
                                        <label for="exam_docket_status">Exam Docket Status</label>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <h4 class="card-title mb-0 flex-grow-1">Test Processing Status: {{ !empty($programmeCategory->examSetting)?$programmeCategory->examSetting->test_processing_status:'Not Set' }}</h4>
                                    <br>
                                    <div class="form-floating">
                                        <select class="form-select" id="test_processing_status" name="test_processing_status" aria-label="Test Processing Status">
                                            <option value="" selected>--Select--</option>
                                            <option value="Start">Start</option>
                                            <option value="Stop">Stop</option>
                                        </select>
                                        <label for="test_processing_status">Test Processing Status</label>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <h4 class="card-title mb-0 flex-grow-1">Result Processing Status: {{ !empty($programmeCategory->examSetting)?$programmeCategory->examSetting->result_processing_status:'Not Set' }}</h4>
                                    <br>
                                    <div class="form-floating">
                                        <select class="form-select" id="result_processing_status" name="result_processing_status" aria-label="Result Processing Status">
                                            <option value="" selected>--Select--</option>
                                            <option value="Start">Start</option>
                                            <option value="Stop">Stop</option>
                                        </select>
                                        <label for="result_processing_status">Result Processing Status</label>
                                    </div>
                                </div>

                                <hr>
                                <div class="col-lg-12">
                                    <div class="text-end">
                                        <button type="submit" id="submit-button" class="btn btn-primary">Update Settings</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div><!-- end col -->
                </div>
            </div>
        </div><!-- end card -->
    </div>
</div>
<!-- end row -->


@endsection
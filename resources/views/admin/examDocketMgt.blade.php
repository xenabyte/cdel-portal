@extends('admin.layout.dashboard')

@section('content')
 <!-- start page title -->
 <div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Exam Setup</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Exam Setup</li>
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
                <h4 class="card-title mb-0 flex-grow-1">Exam Setup for applicationSession</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-6 col-xl-12">
                        <form action="{{ url('/admin/setExamSetting') }}" method="POST">
                            @csrf
                            <input type="hidden" name="examSetting_id" value="{{ !empty($pageGlobalData->examSetting)?$pageGlobalData->examSetting->id:null }}">
                            <input type="hidden" name="academic_session" value="{{ $pageGlobalData->sessionSetting->academic_session }}">
                            <div class="row g-3">

                                <div class="col-lg-6">
                                    <h4 class="card-title mb-0 flex-grow-1">Semester: {{ !empty($pageGlobalData->examSetting) ? ($pageGlobalData->examSetting->semester == 1 ? env('FIRST_SEMESTER') : env('SECOND_SEMESTER')) : 'Not Set' }}</h4>
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
                                    <h4 class="card-title mb-0 flex-grow-1">Exam Docket Status: {{ !empty($pageGlobalData->examSetting)?$pageGlobalData->examSetting->exam_docket_status:'Not Set' }}</h4>
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
                                    <h4 class="card-title mb-0 flex-grow-1">Test Processing Status: {{ !empty($pageGlobalData->examSetting)?$pageGlobalData->examSetting->test_processing_status:'Not Set' }}</h4>
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
                                    <h4 class="card-title mb-0 flex-grow-1">Result Processing Status: {{ !empty($pageGlobalData->examSetting)?$pageGlobalData->examSetting->result_processing_status:'Not Set' }}</h4>
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
@extends('student.layout.dashboard')
<?php 
    $student = Auth::guard('student')->user();
?>
@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Student Result</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Student Result</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <div class="row justify-content-center">
                        <div class="col-lg-9">
                            <h4 class="mt-4 fw-semibold">Generate Examination result</h4>
                            <p class="text-muted mt-3"></p>
                            <div class="mt-4">
                                You must have paid 100% of academic session school fee
                            </div>
                            <div class="mt-4">
                                <form action="{{ url('/student/generateResult') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="examSetting_id" value="{{ !empty($pageGlobalData->examSetting)?$pageGlobalData->examSetting->id:null }}">
                                    <input type="hidden" name="academic_session" value="{{ $pageGlobalData->sessionSetting->academic_session }}">
                                    <div class="row g-3">
                                        
                                        <div class="col-lg-12">
                                            <div class="form-floating">
                                                <select class="form-select" id="level" name="level_id" aria-label="level">
                                                    <option value="" selected>--Select--</option>
                                                    @foreach($academicLevels as $academicLevel)
                                                        @if($academicLevel->id <= $student->level_id)
                                                            <option value="{{ $academicLevel->id }}">{{ $academicLevel->level }} Level</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                <label for="level">Academic Level</label>
                                            </div>
                                        </div>
        
                                        <div class="col-lg-12">
                                            <div class="form-floating">
                                                <select class="form-select" id="semester" name="semester" aria-label="semester">
                                                    <option value="" selected>--Select--</option>
                                                    <option value="1">First Semester</option>
                                                    <option value="2">Second Semester</option>
                                                </select>
                                                <label for="semester">Semester</label>
                                            </div>
                                        </div>
        
        
                                        <div class="col-lg-12">
                                            <div class="form-floating">
                                                <select class="form-select" id="session" name="session" aria-label="Academic Session">
                                                    <option value="" selected>--Select--</option>
                                                    @foreach($sessions as $session)<option value="{{ $session->year }}">{{ $session->year }}</option>@endforeach
                                                </select>
                                                <label for="session">Academic Session</label>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-fill btn-primary btn-lg btn-block mb-5">Generate Result</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>

@endsection
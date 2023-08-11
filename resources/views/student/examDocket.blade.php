@extends('student.layout.dashboard')
<?php 
    $student = Auth::guard('student')->user();
?>
@section('content')
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
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Exam Card for {{ $pageGlobalData->sessionSetting->academic_session }} academic session</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Exam Card</li>
                </ol>
            </div>

        </div>
    </div>
</div>
@if($pageGlobalData->examSetting->exam_docket_status != 'Start')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        <div class="row justify-content-center">
                            <div class="col-lg-9">
                                <h4 class="mt-4 fw-semibold">Examination Card</h4>
                                <p class="text-muted mt-3"></p>
                                <div class="mt-4">
                                    Please be advised that generation of examination card(docket) has not yet begun. We will notify you as soon as the registration period becomes available.
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-center mt-5 mb-2">
                            <div class="col-sm-7 col-8">
                                <img src="{{asset('assets/images/exam.png')}}" alt="" class="img-fluid" />
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
    @if($pageGlobalData->examSetting->semester == 2 && !$passEightyTuition)
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center">
                            <div class="row justify-content-center">
                                <div class="col-lg-9">
                                    <h4 class="mt-4 fw-semibold">Examination Card</h4>
                                    <p class="text-muted mt-3"></p>
                                    <div class="mt-4">
                                        Kindly note that access to the examination card (docket) page requires payment 80% of school fees. 
                                    </div>
                                </div>
                            </div>

                            <div class="row justify-content-center mt-5 mb-2">
                                <div class="col-sm-7 col-8">
                                    <img src="{{asset('assets/images/payment.png')}}" alt="" class="img-fluid" />
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
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header align-items-center">
                        <h4 class="card-title mb-0 flex-grow-1">Course Registration {{ $pageGlobalData->sessionSetting->academic_session }} academic session</h4>
                        <br/>
                        <p class=""><strong>Programme:</strong> {{ $student->programme->name }}
                            <br/><strong>Academic Session:</strong> {{ $student->academic_session }}
                            <br/><strong>Level:</strong> {{ $student->academicLevel->level }} Level
                            <br/><strong>Semester:</strong> {{ $pageGlobalData->examSetting->semester == 1?'First' : 'Second' }} Semester
                        </p>


                    </div><!-- end card header -->

                    <div class="card-body table-responsive">
                        <!-- Bordered Tables -->
                        <table class="table table-borderless table-nowrap">
                            
                            <tbody class="first-semester">
                                <tr>
                                    <td colspan="6" class="semester-heading">
                                        <div class="card-header align-items-center">
                                            <h4 class="card-title mb-0 flex-grow-1">Registered Courses</h4>
                                            <div class="flex-shrink-0 text-end ">
                                                <form action="{{ url('/student/genExamDocket') }}" method="POST">

                                                    <button type="submit" class="btn btn-primary">Generate Examination Card</button>
                                                </form>
                                            </div>
                                        </div><!-- end card header -->
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Course Code</th>
                                    <th scope="col">Course Title</th>
                                    <th scope="col">Course Unit</th>
                                    <th scope="col">Status</th>
                                </tr>
                            
                            
                                @foreach($courseRegs as $courseReg)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $courseReg->course->code }}</td>
                                    <td>{{ $courseReg->course->name }}</td>
                                    <td>{{ $courseReg->course->credit_unit }}</td>
                                    <td>{{ $courseReg->course->status }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>             
                    </div>
                </div><!-- end card -->
            </div>
            <!-- end col -->
        </div>
    @endif
@endif


@endsection
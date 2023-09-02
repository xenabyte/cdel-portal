@extends('layouts.dashboard')

@section('content')

<div class="container-fluid">
    <div class="profile-foreground position-relative mx-n4 mt-n4">
        <div class="profile-wid-bg">
            <img src="{{asset('assets/images/profile-bg.jpg')}}" alt="" class="profile-wid-img" />
        </div>
    </div>
    <div class="pt-4 mb-4 mb-lg-3 pb-lg-4">
        <div class="row g-4">
            <div class="col-auto">
                <div class="avatar-lg">
                    <img src="{{asset(empty($student->image) ? 'assets/images/users/user-dummy-img.jpg' : $student->image )}}" alt="user-img" class="img-thumbnail rounded-circle" />
                </div>
            </div>
            <!--end col-->
            <div class="col">
                <div class="p-2">
                    <h3 class="text-white mb-1">{{ $student->applicant->lastname.' '. $student->applicant->othernames}}</h3>
                    <p class="text-white-75">{{ $student->applicant->matric_number }}</p>
                    <div class="hstack text-white-50 gap-1">
                        <div class="me-2">
                            <i class="ri-building-4-fill me-1 text-white-75 fs-16 align-middle"></i> @if(!empty($student->applicant->programme)){{ $student->applicant->programme->name }}@endif
                        </div>
                        <div class="me-2">
                            <i class="ri-mail-fill me-1 text-white-75 fs-16 align-middle"></i> {{ $student->email }}
                        </div>
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
            <div class="d-flex mb-5">
                <!-- Nav tabs -->
                <ul class="nav nav-pills animation-nav profile-nav gap-2 gap-lg-3 flex-grow-1" role="tablist">
                    
                </ul>
                <div class="flex-shrink-0">
                    @if(($pageGlobalData->examSetting->semester == 1 && $passTuition) || ($pageGlobalData->examSetting->semester == 2 && $passEightyTuition))
                        <span class="btn btn-success"><i class="ri-check-double-fill align-bottom"></i> Approved To Take Exam</span>
                    @else
                        <span class="btn btn-danger"><i class="ri-close-circle-fill align-bottom"></i> Not allowed to to take Exam</span>
                    @endif
                </div>
            </div>
            <div class="tab-pane active" id="overview-tab" role="tabpanel">
                <div class="row">
                    <div class="col-xxl-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Bio Data</h5>
                                <hr>
                                <div class="table-responsive">
                                    <table class="table table-borderless mb-0">
                                        <tbody>
                                            <tr>
                                                <th class="ps-0" scope="row">Full Name: </th>
                                                <td class="text-muted">{{ $student->applicant->lastname.' '. $student->applicant->othernames}}</td>
                                            </tr>
                                            <tr>
                                                <th class="ps-0" scope="row">Phone Number: </th>
                                                <td class="text-muted">{{ $student->applicant->phone_number }}</td>
                                            </tr>
                                            <tr>
                                                <th class="ps-0" scope="row">E-mail: </th>
                                                <td class="text-muted">{{ $student->email }}</td>
                                            </tr><tr>
                                                <th class="ps-0" scope="row">Gender: </th>
                                                <td class="text-muted">{{ $student->applicant->gender }}</td>
                                            </tr>
                                            <tr>
                                                <th class="ps-0" scope="row">Academic session: </th>
                                                <td class="text-muted">{{ $student->academic_session }}</td>
                                            </tr>
                                            <tr>
                                                <th class="ps-0" scope="row">Programmme: </th>
                                                <td class="text-muted">@if(!empty($student->programme)){{ $student->programme->name }}@endif</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div>


                    <!--end col-->
                    <div class="col-xxl-8">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Overview</h5>
                                    <div class="row">
                                        <div class="col-lg-12 text-center">
                                            <div class="profile-user position-relative d-inline-block mx-auto mb-2">
                                                <img src="{{asset(empty($student->image)?'assets/images/users/user-dummy-img.jpg':$student->image)}}" class="rounded-circle avatar-lg img-thumbnail user-profile-image" alt="user-profile-image">
                                                
                                            </div>
                                        </div>
                                        <hr>
                                    </div>
                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="text-center">
                                                <h1>{{ $pageGlobalData->examSetting->semester == 1?'First' : 'Second' }} Semester Examination Card</h1>
                                                <br>
                                            </div>
                                        </div>
                                    
                                        <div class="col-md-12 mb-3">
                                            <table style="width: 100%;">
                                                <tbody>
                                                    <tr>
                                                        <td style="width: 50%; vertical-align: top; text-align: left; border: none; padding-right: 10px;">
                                                            <div><strong>MATRIC NUMBER:</strong> {{ $student->matric_number }}</div>
                                                            <div><strong>APPLICATION NO:</strong> {{ $student->applicant->application_number }}</div>
                                                            <div><strong>FULL NAME:</strong> {{ $student->applicant->lastname.' '. $student->applicant->othernames }}</div>
                                                            <div><strong>LEVEL:</strong> {{ $student->academicLevel->level }} Level</div>
                                                        </td>
                                                        <td style="width: 50%; vertical-align: top; text-align: left; border: none; padding-left: 10px;">
                                                            <div><strong>FACULTY:</strong>  {{ $student->faculty->name }} </div>
                                                            <div><strong>DEPARTMENT:</strong> {{ $student->department->name }}</div>
                                                            <div><strong>PROGRAMME:</strong> {{ $student->programme->name }}</div>
                                                            <div><strong>SESSION:</strong> {{ $student->academic_session }}</div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th>SN</th>
                                                        <th>Code</th>
                                                        <th>Course Title</th>
                                                        <th>Unit</th>
                                                        <th>Status</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($registeredCourses as $registeredCourse)
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>{{ $registeredCourse->course->code }}</td>
                                                                <td>{{ $registeredCourse->course->name }}</td>
                                                                <td>{{ $registeredCourse->course_credit_unit }}</td>
                                                                <td>{{ strtoupper(substr($registeredCourse->course_status, 0, 1)) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
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
        </div>
        <!--end col-->
    </div>
    <!--end row-->

</div><!-- container-fluid -->


@endsection
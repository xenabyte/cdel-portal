@extends('admin.layout.dashboard')

@section('content')
 <!-- start page title -->
 <div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Job Details</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Job Details</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card mt-n4 mx-n4">
            <div class="bg-soft-primary">
                <div class="card-body pb-0 px-4">
                    <div class="row mb-3">
                        <div class="col-md">
                            <div class="row align-items-center g-3">
                                <div class="col-md-auto">
                                    <div class="avatar-md">
                                        <div class="avatar-title bg-white rounded-circle">
                                            <img src="{{ asset('assets/images/career1.png') }}" alt="" class="img-thumbnail rounded-circle avatar-md">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md">
                                    <div>
                                        <h4 class="fw-bold">{{ $jobVacancy->title}}</h4>
                                        <div class="hstack gap-3 flex-wrap">
                                            <div><i class="ri-building-line align-bottom me-1"></i> {{ env('SCHOOL_NAME') }} | {{ $jobVacancy->type }} | CGPA: {{ $jobVacancy->cgpa }} | Deadline Date: {{ date('F j, Y', strtotime($jobVacancy->application_deadline)) }} </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-auto">
                            <div class="hstack gap-1 flex-wrap">
                            </div>
                        </div>
                    </div>

                    <ul class="nav nav-tabs-custom border-bottom-0" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active fw-semibold" data-bs-toggle="tab" href="#project-overview" role="tab">
                                Overview
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- end card body -->
            </div>
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->
</div>

<!-- end row -->
<div class="row">
    <div class="col-lg-12">
        <div class="tab-content text-muted">
            <div class="tab-pane fade show active" id="project-overview" role="tabpanel">
                <div class="row">
                    <div class="col-xl-6 col-lg-7">
                        <div class="card">
                            <div class="card-body">
                                <div class="text-muted">
                                    <h6 class="mb-3 fw-semibold text-uppercase">Overview</h6>
                                    <hr>
                                    {!! $jobVacancy->description  !!}
                                </div>
                                <div class="text-muted">
                                    <h6 class="mb-3 fw-semibold text-uppercase">Requirements</h6>
                                    <hr>
                                    {!! $jobVacancy->requirements  !!}
                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->

                    </div>
                    <!-- ene col -->
                    <div class="col-xl-6 col-lg-5">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Applicants</h5>
                                <hr>
                                <div class="table-responsive">
                                    <table id="fixed-header" class="table table-borderedless dt-responsive nowrap table-striped align-middle" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th scope="col">Id</th>
                                                <th scope="col">Name</th>
                                                <th scope="col"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($jobVacancy->applications as $applicant)
                                            <tr>
                                                <th scope="row">{{ $loop->iteration }}</th>
                                                <td>
                                                    @if($applicant->workStudyApplicant)
                                                        {{ $applicant->workStudyApplicant->applicant->lastname.' '.$applicant->workStudyApplicant->applicant->othernames }} 
                                                    @else
                                                        {{ $applicant->jobApplicant->lastname.' '.$applicant->jobApplicant->othernames }} 
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="hstack gap-3 fs-15">
                                                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#viewApplicant{{$applicant->id}}" class="link-primary"><i class="ri-eye-fill"></i></a>
                                                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#manage{{$applicant->id}}" class="link-muted"><i class="ri-edit-circle-fill"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->

                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>
        </div>
    </div>
    <!-- end col -->
</div>
<!-- end row -->

@foreach($jobVacancy->applications as $applicant)
<!-- Modal Blur -->
<div id="viewApplicant{{ $applicant->id }}" class="modal fade zoomIn" tabindex="-1" aria-labelledby="zoomInModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered  modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="zoomInModalLabel">Applicant Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <hr>
            </div>
            <div class="modal-body border-top border-top-dashed">
                @if($applicant->workStudyApplicant)
                    @php
                        $student = $applicant->workStudyApplicant;
                    @endphp

                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-sm me-3 flex-shrink-0">
                            <div class="avatar-title bg-info-subtle rounded">
                                <img src="{{ asset($student->image) }}" alt="" class="avatar-xs">
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="fs-15 fw-semibold mb-0">{{ $student->applicant->lastname .' '. $student->applicant->othernames }}</h5>
                            <p class="text-muted mb-2">{{ $student->programme->name.' | '.($student->level_id * 100).' Level | '.$student->cgpa }}</p>
                        </div>
                    </div>
                    <div class="row border-top border-top-dashed border bottom border-bottom-dashed">
                        <div class="col-md-5">
                            <div class="mt-4">
                                <p class="text-muted">{{ $student->programme->name }} <br>
                                    <strong>Matric Number:</strong> {{ $student->matric_number }}<br>
                                    <strong>Jamb Reg. Number:</strong> {{ $student->applicant->jamb_reg_no }}<br> <br>
                                </p>
                                <p class="text-muted"><strong>CGPA:</strong> {{ $student->cgpa }} <br>
                                    <strong>Class:</strong> {{ $student->degree_class }}<br>
                                    <strong>Standing:</strong> {{ $student->standing }}<br>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="table-responsive border-start border-start-dashed">
                                <table class="table mb-0 table-borderless">
                                    <tbody>
                                        <tr>
                                            <th><span class="fw-medium">Department:</span></th>
                                            <td>{{ $student->department->name }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="fw-medium">Faculty:</span></th>
                                            <td>{{ $student->faculty->name }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="fw-medium">Email:</span></th>
                                            <td>{{ $student->email }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="fw-medium">Contact No.:</span></th>
                                            <td>{{ $student->applicant->phone_number }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="fw-medium">Address:</span></th>
                                            <td>{!! $student->applicant->address !!}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>                
                @else
                    @php
                        $worker = $applicant->jobApplicant;
                    @endphp

                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-sm me-3 flex-shrink-0">
                            <div class="avatar-title bg-info-subtle rounded">
                                <img src="{{ asset($worker->image) }}" alt="" class="avatar-xs">
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="fs-15 fw-semibold mb-0">{{ $worker->lastname .' '. $worker->othernames }}</h5>
                            <p class="text-muted mb-2">{{ $worker->email.' | '.$worker->phone_number }}</p>
                        </div>
                    </div>
                    <p class="text-muted pb-1 border-top border-top-dashed">{!! $worker->profile->biodata !!}</p>
                    <p class="text-muted pb-1 border-top border-top-dashed">{!! $worker->profile->education_history !!}</p>
                    <p class="text-muted pb-1 border-top border-top-dashed">{!! $worker->profile->professional_information !!}</p>
                    <p class="text-muted pb-1 border-top border-top-dashed">{!! $worker->profile->publications !!}</p>

                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endforeach
@endsection

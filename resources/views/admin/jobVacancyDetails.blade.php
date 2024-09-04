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
                                                    @if($jobVacancy->type == 'Work Study')
                                                        {{ $applicant->applicant->lastname.' '.$applicant->applicant->othernames }} 
                                                    @else
                                                        {{ $applicant->lastname.' '.$applicant->othernames }} 
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="hstack gap-3 fs-15">
                                                        @if($jobVacancy->type == 'Work Study')
                                                            <a target="_blank" href="{{ url('admin/studentProfile/'.$applicant->slug) }}" class="link-secondary m-1"><i class= "ri-eye-fill"></i>View Applicant</a>
                                                        @else
                                                            <a target="_blank" href="{{ url('admin/careerProfile/'.$applicant->slug) }}" class="link-secondary m-1"><i class= "ri-eye-fill"></i>View Applicant</a>
                                                        @endif
                                                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#manage{{$applicant->id}}" class="link-primary"><i class="ri-edit-circle-fill"></i></a>
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
@endsection
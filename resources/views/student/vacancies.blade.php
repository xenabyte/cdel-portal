@php
    $student = Auth::guard('student')->user();
@endphp
@extends('student.layout.dashboard')

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Job Vacancies</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Job Vacancies</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
   @if($jobVacancies->count() < 1) 
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        <div class="row justify-content-center">
                            <div class="col-lg-9">
                                <h4 class="mt-4 fw-semibold">Job Vacancies</h4>
                                <p class="text-muted mt-3">Thank you for your interest in joining our team. At this moment, there are no job vacancies or work-study opportunities available. Please check back later or follow our updates for future openings. We appreciate your understanding and look forward to connecting with you in the future.</p>
                            </div>
                        </div>

                        <div class="row justify-content-center mt-5 mb-2">
                            <div class="col-sm-7 col-8">
                                <img src="{{asset('assets/images/job.png')}}" alt="" class="img-fluid" />
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
        @foreach($jobVacancies as $jobVacancy)
        <div class="col-lg-4">
            <div class="card shadow-lg">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="avatar-sm">
                            <div class="avatar-title bg-warning-subtle rounded">
                                <img src="{{ asset('assets/images/career1.png') }}" alt="" class="avatar-xxs">
                            </div>
                        </div>
                        <div class="ms-3 flex-grow-1">
                            <a href="#!">
                                <h5>{{ $jobVacancy->title }}</h5>
                            </a>
                            <ul class="list-inline text-muted mb-3">
                                <li class="list-inline-item">
                                    <i class="ri-building-line align-bottom me-1"></i> {{ env('SCHOOL_NAME') }}
                                </li>
                            </ul>
                            <div class="hstack">
                                {{ \Str::limit(strip_tags($jobVacancy->description), 250) }}
                            </div>
                        </div>
                        <div>
                            <button type="button" class="btn btn-ghost-primary btn-icon custom-toggle" data-bs-toggle="modal" data-bs-target="#jobVacancyModal{{ $jobVacancy->id }}">
                                <span class="icon-on"><i class="ri-eye-line"></i></span>
                                <span class="icon-off"><i class="ri-eye-off-line"></i></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>   
        @endforeach
    @endif
</div>

@if(!empty($jobVacancies))
    @foreach($jobVacancies as $jobVacancy)
        <div class="modal fade" id="jobVacancyModal{{ $jobVacancy->id }}" tabindex="-1" aria-labelledby="jobVacancyModalLabel{{ $jobVacancy->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="jobVacancyModalLabel{{ $jobVacancy->id }}">{{ $jobVacancy->title }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Job Description:</strong></p>
                        <p>{!! $jobVacancy->description !!}</p>
                        
                        <p><strong>Requirements:</strong></p>
                        <p>{!! $jobVacancy->requirements !!}</p>

                        <p><strong>Application Deadline:</strong> {{ date('F j, Y', strtotime($jobVacancy->application_deadline)) }}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <form action="{{ url('/student/apply') }}" method="POST">
                            @csrf
                            <input type="hidden" name="job_vacancy_id" value="{{ $jobVacancy->id }}">
                            <button type="submit" class="btn btn-primary">Apply for this Job</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>  
    @endforeach   
@endif
@endsection

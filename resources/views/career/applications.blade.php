@extends('career.layout.dashboard')
@php
    $career = Auth::guard('career')->user();
    $applications = $career->applications;
@endphp
@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Job Applications</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Job Applications</li>
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
                <h4 class="card-title mb-0 flex-grow-1">Job Applications </h4>
                <div class="flex-shrink-0">
                </div>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table id="buttons-datatables" class="display table table-stripped" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Job Title</th>
                            <th scope="col">Status</th>
                            <th scope="col">Application Date</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($applications as $application)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $application->vacancy->title }}</td>
                                <td><span class="badge badge-soft-{{ $application->status == 'approved' ? 'success' : 'warning' }}">{{ ucwords($application->status) }}</span></td>
                                <td>{{ date('F j, Y \a\t g:i A', strtotime($application->created_at)) }} </td>
                                <td>
                                    <button type="button" class="btn btn-ghost-primary btn-icon custom-toggle" data-bs-toggle="modal" data-bs-target="#jobVacancyModal{{ $application->id }}">
                                        <span class="icon-on"><i class="ri-eye-line"></i></span>
                                        <span class="icon-off"><i class="ri-eye-off-line"></i></span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>

@if(!empty($applications))
    @foreach($applications as $application)
        <div class="modal fade" id="jobVacancyModal{{ $application->id }}" tabindex="-1" aria-labelledby="jobVacancyModalLabel{{ $application->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="jobVacancyModalLabel{{ $application->vacancy->id }}">{{ $application->vacancy->title }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Job Description:</strong></p>
                        <p>{!! $application->vacancy->description !!}</p>
                        
                        <p><strong>Requirements:</strong></p>
                        <p>{!! $application->vacancy->requirements !!}</p>

                        <p><strong>Application Deadline:</strong> {{ date('F j, Y', strtotime($application->vacancy->application_deadline)) }}</p>
                    </div>
                    <div class="modal-footer">
                       
                    </div>
                </div>
            </div>
        </div>  
    @endforeach   
@endif

@endsection
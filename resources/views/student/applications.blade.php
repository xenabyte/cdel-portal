@extends('student.layout.dashboard')
@php
    $student = Auth::guard('student')->user();
    $applications = $student->applications;
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
                                <td><span class="badge badge-soft-{{ $application->status == 'approved' ? 'success' : 'warning' }}">{{ ucwords(str_replace('_', ' ', $application->status)) }}</span></td>
                                <td>{{ date('F j, Y \a\t g:i A', strtotime($application->created_at)) }} </td>
                                <td>
                                    <div class="hstack gap-3 fs-15">
                                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#jobVacancyModal{{$application->id}}" class="link-primary"><i class="ri-eye-fill"></i></a>
                                        @if(!empty($application->appointment_letter))
                                        <a href="{{ asset($application->appointment_letter) }}" class="btn btn-danger m-1"> Download Appointment Letter</a>
                                        @endif
                                        @if($application->status == 'applied')
                                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete{{$application->id}}" class="link-danger"><i class="ri-delete-bin-5-line"></i></a>
                                        @endif
                                        @if($application->status == 'approved')
                                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#manage{{$application->id}}" class="btn btn-secondary"><i class="ri-user-settings-fill"></i> Manage</a>
                                        @endif
                                    </div>
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

        <div id="delete{{$application->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center p-5">
                        <div class="text-end">
                            <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="mt-2">
                            <lord-icon src="https://cdn.lordicon.com/wwneckwc.json" trigger="hover" style="width:150px;height:150px">
                            </lord-icon>
                            <h4 class="mb-3 mt-4">Are you sure you want to delete <br/> {{ $application->vacancy->title }}?</h4>
                            <form action="{{ url('/student/deleteApplication') }}" method="POST">
                                @csrf
                                <input name="application_id" type="hidden" value="{{$application->id}}">
                                <hr>
                                <button type="submit" id="submit-button" class="btn btn-danger w-100">Yes, Delete</button>
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer bg-light p-3 justify-content-center">

                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <div class="modal fade" id="manage{{ $application->id }}" tabindex="-1" aria-labelledby="manage{{ $application->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="jobVacancyModalLabel{{ $application->vacancy->id }}">{{ $application->vacancy->title }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Job Description:</strong></p>
                        <p>{!! $application->vacancy->description !!}</p>
                    </div>
                    <div class="modal-footer">
                        <!-- Form for accepting or rejecting offer -->
                        <form action="{{ url('student/manageApplication') }}" method="POST">
                            @csrf
                            <input type="hidden" name="vacancy_id" value="{{ $application->vacancy->id }}">
                            <input name="application_id" type="hidden" value="{{$application->id}}">
                            
                            <!-- Accept offer button -->
                            <button type="submit" name="response" value="accepted" class="btn btn-success">
                                Accept Offer
                            </button>
        
                            <!-- Reject offer button -->
                            <button type="submit" name="response" value="rejected" class="btn btn-danger">
                                Reject Offer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
    @endforeach   
@endif

@endsection
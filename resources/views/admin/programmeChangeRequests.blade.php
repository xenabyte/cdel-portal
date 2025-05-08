@extends('admin.layout.dashboard')
@section('content')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Student Intra Transfer Application</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Student Intra Transfer Application</li>
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
                <h4 class="card-title mb-0 flex-grow-1">Student Intra Transfer Application</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-6 col-xl-12">
                        
                        <div class="table-responsive">
                            <!-- Bordered Tables -->
                            @if($programmeChangeRequests->isNotEmpty())

                                <table id="buttons-datatables" class="table table-stripped">
                                    <thead>
                                        <tr>
                                            <th scope="col">S/N</th>
                                            <th scope="col">Student Name</th>
                                            <th scope="col">Present Programme</th>
                                            <th scope="col">New Programme Date</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Current Status</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($programmeChangeRequests as $programmeChangeRequest)
                                        @if(!empty($programmeChangeRequest->student->applicant))
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $programmeChangeRequest->student->applicant->lastname .' '. $programmeChangeRequest->student->applicant->othernames }}</td>
                                            <td>{{ $programmeChangeRequest->student->programme->name }}</td>
                                            <td>{{ $programmeChangeRequest->newProgramme->name ?? '' }}</td>
                                            <td>
                                                {{ $programmeChangeRequest->status }}
                                            </td>
                                            <td>
                                                {{ str_replace('_', ' ', $programmeChangeRequest->current_stage) }}
                                            </td>
                                            <td>
                                                <a href="{{ url('admin/viewProgrammeChangeRequest/'.$programmeChangeRequest->slug) }}" class="btn btn-sm btn-info">
                                                    <i class="ri-eye-line"></i> View Details
                                                </a>
                                            </td>
                                        </tr>
                                        @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted">No records found.</p>
                            @endif
                        </div>
                       
                    </div><!-- end col -->
                </div>
            </div>

        </div><!-- end card -->
    </div>
</div>

@endsection
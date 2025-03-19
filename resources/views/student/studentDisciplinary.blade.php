@extends('student.layout.dashboard')
@php
    $student = Auth::guard('student')->user();
@endphp
@section('content')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Student Disciplinary</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Student Disciplinary</li>
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
                <h4 class="card-title mb-0 flex-grow-1">Student Disciplinary</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-6 col-xl-12">
                        
                        <div class="table-responsive">
                            <!-- Bordered Tables -->
                            @if($student->suspensions->isNotEmpty())
                                <h4 class="mt-4">Suspension Record</h4>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col">Reason</th>
                                            <th scope="col">Start Date</th>
                                            <th scope="col">End Date</th>
                                            <th scope="col">File</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($student->suspensions as $suspension)
                                        <tr>
                                            <td>{!! $suspension->reason !!}</td>
                                            <td>{{ $suspension->start_date }}</td>
                                            <td>
                                                @if($suspension->end_date)
                                                    {{ $suspension->end_date }}
                                                @else
                                                    <span class="badge bg-warning">Ongoing</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($suspension->file)
                                                    <a href="{{ asset($suspension->file) }}" class="btn btn-sm btn-info" target="_blank">
                                                        <i class="ri-file-download-line"></i> View File
                                                    </a>
                                                @else
                                                    <span class="text-muted">No file</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ url('student/viewSuspension/'.$suspension->slug) }}" class="btn btn-sm btn-info">
                                                    <i class="ri-eye-line"></i> View Details
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted">No suspension records found.</p>
                            @endif
                        </div>
                       
                    </div><!-- end col -->
                </div>
            </div>

        </div><!-- end card -->
    </div>
</div>

@endsection
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
                                <td><span class="badge badge-soft-{{ $application->status == 'accepted' ? 'success' : 'warning' }}">{{ ucwords(str_replace('_', ' ', $application->status)) }}</span></td>
                                <td>{{ date('F j, Y \a\t g:i A', strtotime($application->created_at)) }} </td>
                                <td>
                                    
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

@endsection
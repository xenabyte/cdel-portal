@extends('student.layout.dashboard')

@section('content')
 <!-- start page title -->
 <div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Elections/Polls</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Elections/Polls</li>
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
                <h4 class="card-title mb-0 flex-grow-1">Elections/Polls</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-6 col-xl-12">

                        <table id="fixed-header" class="table table-bordered table-responsive nowrap table-striped align-middle" style="width:100%">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Title</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Start Time</th>
                                    <th scope="col">End Time</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($elections as $election)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $election->title }}</td>
                                    <td>{{ ucfirst($election->type) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($election->start_time)->format('d M Y h:i A') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($election->end_time)->format('d M Y h:i A') }}</td>
                                    <td>
                                        @php
                                            $now = \Carbon\Carbon::now();
                                            if ($now->lt($election->start_time)) {
                                                $status = 'Upcoming';
                                                $badgeClass = 'warning';
                                            } elseif ($now->between($election->start_time, $election->end_time)) {
                                                $status = 'Ongoing';
                                                $badgeClass = 'success';
                                            } else {
                                                $status = 'Ended';
                                                $badgeClass = 'danger';
                                            }
                                        @endphp
                                        <span class="badge bg-{{ $badgeClass }}">{{ $status }}</span>
                                    </td>
                                    <td>
                                        <div class="hstack gap-3">
                                            <a href="{{ url('student/election/'.$election->slug) }}" class="btn btn-primary btn-sm">
                                                Cast Your Vote
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div><!-- end card -->
    </div>
</div>
<!-- end row -->
@endsection
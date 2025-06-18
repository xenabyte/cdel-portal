@extends('admin.layout.dashboard')
@php
    // $applicationSession = $programmeCategory->academicSessionSetting->application_session;
@endphp
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Election/poll Details</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Election/poll Details</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row project-wrapper">
    <div class="col-xxl-8 card-height-100">
        <div class="row">
            <div class="col-xl-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-primary rounded-2 fs-2">
                                    <i data-feather="briefcase"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 overflow-hidden ms-3">
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-3">Positions</p>
                                <div class="d-flex align-items-center mb-3">
                                    <h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value" data-target="{{ $election->positions->count() }}">0</span></h4>
                                </div>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div>
            </div><!-- end col -->

            <div class="col-xl-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-warning rounded-2 fs-2">
                                    <i data-feather="award"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-uppercase fw-medium text-muted mb-3">Candidates</p>
                                <div class="d-flex align-items-center mb-3">
                                    <h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value" data-target="{{ $election->candidates->count() }}">0</span></h4>
                                </div>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div>
            </div><!-- end col -->
        </div><!-- end row -->

        <div class="row">
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header border-0 align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Election/Poll Overview - {{ $election->title }}</h4>
                    </div><!-- end card header -->

                    <div class="card-body border border-dashed border-start-0">

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <p><strong>Type:</strong> {{ ucfirst($election->type) }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Status:</strong> 
                                    @if(now() < $election->start_time)
                                        <span class="badge bg-warning">Upcoming</span>
                                    @elseif(now() >= $election->start_time && now() <= $election->end_time)
                                        <span class="badge bg-success">Ongoing</span>
                                    @else
                                        <span class="badge bg-secondary">Ended</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Start Time:</strong> {{ \Carbon\Carbon::parse($election->start_time)->format('D, M j, Y h:i A') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>End Time:</strong> {{ \Carbon\Carbon::parse($election->end_time)->format('D, M j, Y h:i A') }}</p>
                            </div>
                            <div class="col-md-12">
                                <p><strong>Description:</strong> {!! nl2br(e($election->description)) !!}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Eligible Group:</strong> {{ $election->eligible_group ?? 'All Students' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Show Result:</strong> 
                                    {!! $election->show_result ? '<span class="text-success">Yes</span>' : '<span class="text-danger">No</span>' !!}
                                </p>
                            </div>
                        </div>

                        <div class="align-items-center d-flex border-top border-top-dashed mt-3 pt-3">
                            <p class="mb-0 flex-grow-1">Add Positions</p>
                            <div class="flex-shrink-0">
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addPosition" class="btn btn-primary">Add Position</a>
                            </div>
                        </div>

                        <div class="align-items-center d-flex border-top border-top-dashed mt-3 pt-3">
                            <p class="mb-0 flex-grow-1">Add Candidates</p>
                            <div class="flex-shrink-0">
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addCandidate" class="btn btn-primary">Add Candidates</a>
                            </div>
                        </div>

                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end col -->


           {{-- list positions --}}
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header border-0">
                        <h4 class="card-title mb-0">Election Positions</h4>
                    </div><!-- end cardheader -->

                    <div class="card-body pt-0">
                        <h6 class="text-uppercase fw-semibold mt-4 mb-3 text-muted">Available Positions</h6>

                        @foreach($election->positions as $position)
                        <div class="mini-stats-wid d-flex align-items-center mt-3">
                            <div class="flex-shrink-0 avatar-sm">
                                <span class="mini-stat-icon avatar-title rounded-circle text-success bg-soft-success fs-4">
                                    <i class="mdi mdi-briefcase-account"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">{{ $position->title }}</h6>
                                <p class="fs-14 mb-0 text-muted">{{ $position->candidates->count() }} Candidate(s)</p>
                            </div>
                            <div class="flex-shrink-0">
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#deletePosition{{ $position->id }}" class="btn btn-danger"><i class="mdi mdi-trash-can-outline"></i></a>
                            </div>

                            {{-- Delete Modal --}}
                            <div id="deletePosition{{ $position->id }}" class="modal fade" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-body text-center p-5">
                                            <div class="text-end">
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="mt-2">
                                                <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="hover" style="width:150px;height:150px"></lord-icon>
                                                <h4 class="mb-3 mt-4">Are you sure you want to delete <br/> {{ $position->title }}?</h4>
                                                <form action="{{ url('/admin/deletePosition') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="position_id" value="{{ $position->id }}">
                                                    <hr>
                                                    <button type="submit" id="submit-button" class="btn btn-danger w-100">Yes, Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light p-3 justify-content-center"></div>
                                    </div><!-- /.modal-content -->
                                </div><!-- /.modal-dialog -->
                            </div><!-- /.modal -->
                        </div><!-- end -->
                        <br>
                        @endforeach

                        <div class="mt-3 text-center">
                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addPosition" class="btn btn-primary">
                                Add Position</a>
                        </div>
                    </div><!-- end cardbody -->
                </div><!-- end card -->
            </div>
        </div><!-- end row -->
    </div><!-- end col -->
    <div class="col-xxl-4">
        <div class="card card-height-100">
            <div class="card-header border-0">
                <h4 class="card-title mb-0">Election/Poll Image</h4>
            </div><!-- end cardheader -->
            @if(!empty($election->image))
            <div class="card-body pt-0">
                <img class="card-img-top img-fluid" src="{{ $election->image }}" alt="Card image cap">
                
                <div class="card-header p-0 border-0 bg-soft-light">
                    <div class="row g-0 text-center">
                        <div class="col-12 col-sm-12">
                            <div class="p-3 border border-dashed border-start-0">

                            </div>
                        </div>
                    </div>
                </div><!-- end card header -->
            </div><!-- end cardbody -->
            @endif
        </div><!-- end card -->
    </div><!-- end col -->
</div><!-- end row -->


{{-- list candidates with position and vote counts --}}
{{-- @if(!empty($election->positions) && $election->positions->count() > 0)
    @foreach($election->positions as $position)
        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Candidates for Position: {{ $position->title }}</h4>
                    </div><!-- end card header -->

                    <div class="card-body table-responsive">
                        @php
                            // Filter candidates for this position
                            $candidates = $election->candidates->where('position_id', $position->id);
                        @endphp

                        @if($candidates->count() > 0)
                        <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Candidate Name</th>
                                    <th>Matric Number</th>
                                    <th>Level</th>
                                    <th>Programme</th>
                                    <th>Votes</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($candidates as $candidate)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $candidate->student->applicant->lastname .' '. $candidate->student->applicant->othernames }}</td>
                                    <td>{{ $candidate->student->matric_number }}</td>
                                    <td>{{ $candidate->student->academicLevel->level }} Level</td>
                                    <td>{{ $candidate->student->programme->name }}</td>
                                    <td>{{ $candidate->votes->count() }}</td>
                                    <td>
                                        <a href="{{ url('admin/studentProfile/'.$candidate->student->slug) }}" class="btn btn-primary m-1">
                                            <i class="ri-user-6-fill"></i> View Student
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                            <p>No candidates found for this position.</p>
                        @endif
                    </div>
                </div><!-- end card -->
            </div>
        </div>
    @endforeach
@else
    <p>No positions found for this election.</p>
@endif --}}

@if($election->positions->count() > 0)
    <div class="row">
        @foreach($election->positions as $position)
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0 text-truncate">Position: {{ $position->title }}</h5>
                </div>

                <div class="card-body table-responsive">
                    @php
                        $positionCandidates = $election->candidates->where('position_id', $position->id);
                    @endphp

                    @if($positionCandidates->count() > 0)
                    <table class="table table-bordered table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Candidate</th>
                                <th>Votes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($positionCandidates as $candidate)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $candidate->student->applicant->lastname .' '. $candidate->student->applicant->othernames }}</strong><br>
                                    <small>{{ $candidate->student->matric_number }}</small><br>
                                    <small>{{ $candidate->student->academicLevel->level }}L • {{ $candidate->student->programme->name }}</small>
                                </td>
                                <td class="text-center">{{ $candidate->votes->count() }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                        <p class="text-muted mb-0">No candidates available.</p>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
@else
    <p class="text-muted">No positions have been added for this election.</p>
@endif

{{-- <div class="row">
    <div class="col-lg-7">
        <div class="card card-height-100">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Staff Members</h4>
            </div><!-- end card header -->

            <div class="card-body">

                <div class="table-responsive p-3">
                    <table id="buttons-datatables1" class="table table-borderless table-nowrap align-middle mb-3">
                        <thead class="table-light text-muted">
                            <tr>
                                <th scope="col">Staff</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($department->staffs as $staff)
                            <tr>
                                <td class="d-flex">
                                    <img src="{{ $staff->image }}" alt="" class="avatar-xs rounded-3 shadow me-2">
                                    <div>
                                        <h5 class="fs-13 mb-0">{{ ucwords(strtolower($staff->lastname.' '.$staff->othernames)) }}</h5>
                                        <p class="fs-12 mb-0 text-muted">{{ $staff->qualification }}</p>
                                    </div>
                                </td>
                            </tr><!-- end tr -->
                            @endforeach
                        </tbody><!-- end tbody -->
                    </table><!-- end table -->
                </div>
            </div><!-- end cardbody -->
        </div><!-- end card -->
    </div><!-- end col -->

    <div class="col-lg-5">
        <div class="card card-height-100">
            <div class="card-header border-0">
                <h4 class="card-title mb-0">Exam Officer's Profile</h4>
            </div><!-- end cardheader -->
            @if(!empty($department->examOfficer))
            <div class="card-body pt-0">
                <img class="card-img-top img-fluid" src="{{$department->examOfficer->image }}" width="50px" alt="Card image cap">
                <div class="card-body">
                    <p class="card-text text-center"><strong>{{ $department->examOfficer->lastname.' '. $department->examOfficer->othernames }}</strong> <br> Exam officer, {{ $department->name }} Department</p>
                </div>


                <div class="card-header p-0 border-0 bg-soft-light">
                    <div class="row g-0 text-center">
                        <div class="col-12 col-sm-12">
                            <div class="p-3 border border-dashed border-start-0">

                            </div>
                        </div>
                    </div>
                </div><!-- end card header -->
            </div><!-- end cardbody -->
            @endif
        </div><!-- end card -->
    </div>
</div><!-- end row --> --}}

{{-- Add Candidate to Position --}}
<div id="addCandidate" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Add Candidate</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body border-top border-top-dashed">
                <form action="{{ url('/admin/addCandidate') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" name="election_id" value="{{ $election->id }}">

                    <div class="mb-3">
                        <label for="position_id" class="form-label">Select Position</label>
                        <select class="form-select" name="position_id" required>
                            <option value="">-- Select Position --</option>
                            @foreach($election->positions as $position)
                                <option value="{{ $position->id }}">{{ $position->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="matric_number" class="form-label">Candidate Matric Number</label>
                        <input type="text" class="form-control" name="matric_number" placeholder="Enter Matric Number" required>
                    </div>

                    <div class="mb-3">
                        <label for="manifesto" class="form-label">Manifesto</label>
                        <textarea name="manifesto" class="form-control ckeditor" rows="4"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="photo" class="form-label">Photo</label>
                        <input type="file" class="form-control" name="photo" accept="image/*">
                    </div>

                    <div class="text-end border-top border-top-dashed p-3">
                        <button type="submit" class="btn btn-primary">Add Candidate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

 {{-- add positions --}}
<div id="addPosition" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Add Position</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="{{ url('/admin/addPosition') }}" method="post">
                    @csrf
                    <input type="hidden" name="election_id" value="{{ $election->id }}">

                    <div class="mb-3">
                        <label for="title" class="form-label">Position Title</label>
                        <input type="text" class="form-control" name="title" id="title" placeholder="e.g. President, Secretary" required>
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Add Position</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@endsection
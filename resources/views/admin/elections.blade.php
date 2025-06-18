@extends('admin.layout.dashboard')

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
                <div class="flex-shrink-0">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add">Create Election</button>
                </div>
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
                                        <div class="hstack gap-3 fs-15">
                                            <a href="{{ url('admin/election/'.$election->slug) }}" class="link-primary" title="View"><i class="ri-eye-fill"></i></a>
                                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit{{ $election->id }}" class="link-info" title="Edit"><i class="ri-edit-circle-fill"></i></a>
                                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete{{ $election->id }}" class="link-danger" title="Delete"><i class="ri-delete-bin-5-line"></i></a>
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


@foreach ($elections as $election)
<div id="delete{{ $election->id }}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="text-end">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="mt-2">
                    <lord-icon 
                        src="https://cdn.lordicon.com/gsqxdxog.json" 
                        trigger="hover" 
                        style="width:150px;height:150px">
                    </lord-icon>
                    <h4 class="mb-3 mt-4">
                        Are you sure you want to delete<br/> 
                        <strong>{{ $election->title }}</strong>?
                    </h4>
                    <form action="{{ url('/admin/deleteElection') }}" method="POST">
                        @csrf
                        <input type="hidden" name="election_id" value="{{ $election->id }}">
                        <hr>
                        <button type="submit" class="btn btn-danger w-100">Yes, Delete</button>
                    </form>
                </div>
            </div>
            <div class="modal-footer bg-light p-3 justify-content-center">
                <!-- Optional footer content -->
            </div>
        </div>
    </div>
</div>

<div id="edit{{ $election->id }}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Edit Election/Poll</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="{{ url('/admin/updateElection') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" name="election_id" value="{{ $election->id }}">

                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" id="title" value="{{ $election->title }}">
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Type</label>
                        <select name="type" id="type" class="form-control">
                            <option value="election" {{ $election->type == 'election' ? 'selected' : '' }}>Election</option>
                            <option value="poll" {{ $election->type == 'poll' ? 'selected' : '' }}>Poll</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="start_time" class="form-label">Start Time</label>
                        <input type="datetime-local" class="form-control" name="start_time" id="start_time"
                            value="{{ \Carbon\Carbon::parse($election->start_time)->format('Y-m-d\TH:i') }}">
                    </div>

                    <div class="mb-3">
                        <label for="end_time" class="form-label">End Time</label>
                        <input type="datetime-local" class="form-control" name="end_time" id="end_time"
                            value="{{ \Carbon\Carbon::parse($election->end_time)->format('Y-m-d\TH:i') }}">
                    </div>

                    <div class="mb-3">
                        <label for="eligible_group" class="form-label">Eligible Group</label>
                        <input type="text" class="form-control" name="eligible_group" id="eligible_group"
                            value="{{ $election->eligible_group }}">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" class="form-control" id="description" cols="30" rows="4">{{ $election->description }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="show_result" class="form-check-label">
                            <input type="checkbox" name="show_result" id="show_result" class="form-check-input"
                                {{ $election->show_result ? 'checked' : '' }}>
                            Show Result After Voting
                        </label>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Image (optional)</label>
                        <input type="file" class="form-control" name="image" id="image">
                        @if($election->image)
                            <small class="text-muted d-block mt-1">Current: <a href="{{ asset($election->image) }}" target="_blank">View Image</a></small>
                        @endif
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach

<div id="add" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Create Election or Poll</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="{{ url('/admin/createElection') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" id="title" required>
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Type</label>
                        <select name="type" id="type" class="form-select" required>
                            <option value="">-- Select Type --</option>
                            <option value="election">Election</option>
                            <option value="poll">Poll</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="start_time" class="form-label">Start Time</label>
                        <input type="datetime-local" class="form-control" name="start_time" id="start_time" required>
                    </div>

                    <div class="mb-3">
                        <label for="end_time" class="form-label">End Time</label>
                        <input type="datetime-local" class="form-control" name="end_time" id="end_time" required>
                    </div>

                    <div class="mb-3">
                        <label for="eligible_group" class="form-label">Eligibility Group</label>
                        <select name="eligible_group" id="eligible_group" class="form-select">
                            <option value="">-- Everyone --</option>
                            <option value="100L">100 Level</option>
                            <option value="200L">200 Level</option>
                            <option value="faculty">By Faculty</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description (optional)</label>
                        <textarea name="description" class="form-control" id="description" rows="4"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Election Image (optional)</label>
                        <input type="file" class="form-control" name="image" id="image" accept="image/*">
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="show_result" id="show_result" value="1">
                        <label class="form-check-label" for="show_result">Show Results to Students</label>
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Create Election/Poll</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
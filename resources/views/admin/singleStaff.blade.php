@extends('admin.layout.dashboard')
@php
$allStaffRoles = $singleStaff->staffRoles;

$singleStaffRoles = json_decode($allStaffRoles);

$deanRole = array_filter($singleStaffRoles, function ($staffRole) {
return strtolower($staffRole->role->role) === 'dean';
});

$subDeanRole = array_filter($singleStaffRoles, function ($staffRole) {
return strtolower($staffRole->role->role) === 'sub-dean';
});

$hodRole = array_filter($singleStaffRoles, function ($staffRole) {
return strtolower($staffRole->role->role) === 'hod';
});
@endphp

@section('content')

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
                                            <img src="{{ !empty($singleStaff->image) ? $singleStaff->image : asset('assets/images/users/user-dummy-img.jpg') }}"
                                                alt="" class="img-thumbnail rounded-circle avatar-md">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md">
                                    <div>
                                        <h4 class="fw-bold">
                                            {{ $singleStaff->title.' '.$singleStaff->lastname .' '. $singleStaff->othernames }}
                                        </h4>
                                        <div class="hstack gap-3 flex-wrap">
                                            <div><i class="ri-building-line align-bottom me-1"></i>
                                                {{ !empty($singleStaff->staffRoles->first()) ? $singleStaff->staffRoles->first()->role->role : $singleStaff->current_position }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-auto">
                            <div class="hstack gap-1 flex-wrap">
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                    data-bs-target="#assignProgramModal">Assign Program Role</button>
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                    data-bs-target="#editStaff">Update Staff</button>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#assignRole">Assign Role</button>
                                @if(empty($singleStaff->deleted_at))
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                    data-bs-target="#disableStaff">Disable Staff</button>
                                @else
                                <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                    data-bs-target="#enableStaff">Enable Staff</button>
                                @endif
                                @if(!empty($deanRole) && ($singleStaff->id != $singleStaff->faculty->dean_id))
                                <button type="button" class="btn btn-info" data-bs-toggle="modal"
                                    data-bs-target="#assignDeanToFaculty"> Assign Dean To Faculty</button>
                                @endif
                                @if(!empty($subDeanRole) && ($singleStaff->id != $singleStaff->faculty->sub_dean_id))
                                <button type="button" class="btn btn-info" data-bs-toggle="modal"
                                    data-bs-target="#assignSubDeanToFaculty"> Assign Sub Dean To Faculty</button>
                                @endif
                                @if(!empty($hodRole))
                                <button type="button" class="btn btn-info" data-bs-toggle="modal"
                                    data-bs-target="#assignHodToDepartment"> Assign HOD To Department</button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <ul class="nav nav-tabs-custom border-bottom-0" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active fw-semibold" data-bs-toggle="tab" href="#project-overview"
                                role="tab">
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
                    <div class="col-xl-9 col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <div class="text-muted">
                                    <h6 class="mb-3 fw-semibold text-uppercase">Profile Overview</h6>
                                    <hr>
                                    {!! $singleStaff->description !!}
                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->

                        <div class="card">
                            <div class="card-body">
                                <div class="text-muted">
                                    <h4 class="card-title mb-0 flex-grow-1">Referred Student(s) </h4>
                                    <div class="border-top border-top-dashed pt-3">
                                        <div class="table-responsive">
                                            <!-- Bordered Tables -->
                                            <table id="buttons-datatables" class="display table table-bordered"
                                                style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Id</th>
                                                        <th scope="col">Name</th>
                                                        <th scope="col">Programme</th>
                                                        <th scope="col">Email</th>
                                                        <th scope="col">Phone Number</th>
                                                        <th scope="col">Academic Session</th>
                                                        <th scope="col">Application Status</th>
                                                        <th scope="col">Applied Date</th>
                                                        <th scope="col"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($applicants as $applicant)
                                                    <tr>
                                                        <th scope="row">{{ $loop->iteration }}</th>
                                                        <td>{{ $applicant->lastname .' '. $applicant->othernames }}</td>
                                                        <td>{{ !empty($applicant->programme)? $applicant->programme->name:'Not Available' }}
                                                        </td>
                                                        <td>{{ $applicant->email }} </td>
                                                        <td>{{ $applicant->phone_number }} </td>
                                                        <td>{{ $applicant->academic_session }} </td>
                                                        <td>{{ ucwords($applicant->status) }} </td>
                                                        <td>{{ $applicant->created_at }} </td>
                                                        <td>
                                                            <a href="{{ !empty($applicant->student)? url('admin/student/'.$applicant->student->slug) : url('admin/applicant/'.$applicant->slug) }}"
                                                                class="btn btn-primary m-1"><i
                                                                    class="ri-user-6-fill"></i> View
                                                                Applicant/Student</a>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- ene col -->
                    <div class="col-xl-3 col-lg-4">

                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Bio Data</h5>
                                <hr>
                                <div class="table-responsive">
                                    <table class="table table-borderless mb-0">
                                        <tbody>
                                            <tr>
                                                <th class="ps-0" scope="row">Full Name: </th>
                                                <td class="text-muted">
                                                    {{ $singleStaff->title.' '.$singleStaff->lastname.' '. $singleStaff->othernames}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="ps-0" scope="row">Phone Number: </th>
                                                <td class="text-muted">{{ $singleStaff->phone_number }}</td>
                                            </tr>
                                            <tr>
                                                <th class="ps-0" scope="row">E-mail: </th>
                                                <td class="text-muted">{{ $singleStaff->email }}</td>
                                            </tr>
                                            <tr>
                                                <th class="ps-0" scope="row">Gender: </th>
                                                <td class="text-muted">{{ $singleStaff->gender }}</td>
                                            </tr>
                                            <tr>
                                                <th class="ps-0" scope="row">Faculty: </th>
                                                <td class="text-muted">
                                                    {{ !empty($singleStaff->faculty) ? $singleStaff->faculty->name : null }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="ps-0" scope="row">Department: </th>
                                                <td class="text-muted">
                                                    {{ !empty($singleStaff->acad_department)?$singleStaff->acad_department->name:null }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->

                        @if(!empty($singleStaff->faculty) && (($singleStaff->id == $singleStaff->faculty->dean_id) ||
                        ($singleStaff->id == $singleStaff->faculty->sub_dean_id)))
                        <div class="card-body">
                            <div class="card">
                                <div class="card-header">
                                    <button type="button" class="btn-close float-end fs-11" aria-label="Close"></button>
                                    <h6 class="card-title mb-0">Position</h6>
                                </div>
                                <div class="card-body p-4 text-center">
                                    <div class="mx-auto avatar-md mb-3">
                                        <img src="{{ !empty($singleStaff->image) ? $singleStaff->image : asset('assets/images/users/user-dummy-img.jpg') }}"
                                            alt="" class="img-fluid rounded-circle">
                                    </div>
                                    <h5 class="card-title mb-1">
                                        {{ $singleStaff->title.' '.$singleStaff->lastname.' '. $singleStaff->othernames}}
                                    </h5>
                                    @if($singleStaff->id == $singleStaff->faculty->dean_id)
                                    <p class="text-muted mb-0">Dean, {{$singleStaff->faculty->name}}</p>
                                    @endif
                                    @if($singleStaff->id == $singleStaff->faculty->sub_dean_id)
                                    <p class="text-muted mb-0">Sub Dean, {{$singleStaff->faculty->name}}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="card">
                            <div class="card-header align-items-center d-flex border-bottom-dashed">
                                <h4 class="card-title mb-0 flex-grow-1">Role(s)/Position(s)</h4>
                            </div>

                            <div class="card-body">
                                <div style="height: 235px;" class="mx-n3 px-3">
                                    @foreach($singleStaff->staffRoles as $singleStaffRole)
                                    <div class="vstack gap-3">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <h5 class="fs-13 mb-0"><a href="#"
                                                        class="text-body d-block">{{ $loop->iteration }}.
                                                        {{$singleStaffRole->role->role}}</a></h5>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <div class="d-flex align-items-center gap-1">
                                                    <a href="javascript:void(0);" data-bs-toggle="modal"
                                                        data-bs-target="#unAssignRole{{$singleStaffRole->id}}"
                                                        class="link-danger"><i class="ri-delete-bin-5-line"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="unAssignRole{{$singleStaffRole->id}}" class="modal fade" tabindex="-1"
                                        aria-hidden="true" data-bs-backdrop="static" style="display: none;">
                                        <!-- Fullscreen Modals -->
                                        <div class="modal-dialog modal-xl">
                                            <div class="modal-content border-0 overflow-hidden">
                                                <div class="modal-header p-3">
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>

                                                <div class="modal-body">
                                                    <h4 class="mb-3 mt-4">Are you sure you want to delete <br />
                                                        {{ $singleStaffRole->role->role }} role from
                                                        {{ $singleStaff->title.' '.$singleStaff->lastname.' '. $singleStaff->othernames}}?
                                                    </h4>
                                                    <form action="{{ url('/admin/unAssignRole') }}" method="POST">
                                                        @csrf
                                                        <input name="staff_role_id" type="hidden"
                                                            value="{{$singleStaffRole->id}}">
                                                        <hr>
                                                        <button type="submit" id="submit-button"
                                                            class="btn btn-danger w-100">Yes, Delete</button>
                                                    </form>
                                                </div>
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div><!-- /.modal -->
                                    @endforeach
                                    <!-- end list -->
                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->

                        <div class="card">
                            <div class="card-header align-items-center d-flex border-bottom-dashed">
                                <h4 class="card-title mb-0 flex-grow-1">Program Assignment(s)</h4>
                            </div>

                            <div class="card-body">
                                <div style="height: auto;" class="mx-n3 px-3">
                                    @forelse(optional($singleStaff)->programmeAssignments ?? [] as $assignment)

                                    <div class="vstack gap-3 mb-3 border-bottom pb-2">
                                        <div class="d-flex align-items-start justify-content-between">
                                            <div class="flex-grow-1">
                                                <h5 class="fs-13 mb-1">
                                                    <a href="#" class="text-body d-block">
                                                        @php
                                                        $category = $programmeCategories->firstWhere('id',
                                                        $assignment->programme_category_id);
                                                        @endphp
                                                        <p class="mb-0"><strong>Programme:</strong>
                                                            {{ $category ? $category->category : 'N/A' }}<br>

                                                    </a>
                                                </h5>
                                                <p class="mb-0"><strong>Role:</strong>
                                                    {{ $assignment->role_in_programme }}
                                                </p>
                                                <p class="mb-0"><strong>Status:</strong> {{ $assignment->status }}</p>

                                                <p class="mb-0"><strong>Assigned By:</strong>
                                                    {{ $assigner->name ?? 'N/A' }}
                                                </p>
                                                <p class="mb-0"><strong>Assigned At:</strong>
                                                    {{ \Carbon\Carbon::parse($assignment->assigned_at)->format('d M Y, h:i A') }}
                                                </p>
                                            </div>

                                            <div class="flex-shrink-0">
                                                <div class="d-flex align-items-center gap-1">

                                                    <a href="javascript:void(0);" data-bs-toggle="modal"
                                                        data-bs-target="#unAssignProgram{{ $assignment->id }}"
                                                        class="link-danger">
                                                        <i class="ri-delete-bin-5-line"></i>
                                                    </a>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <!-- Modal for unassigning -->
                                    <div id="unAssignProgram{{ $assignment->id }}" class="modal fade" tabindex="-1"
                                        aria-hidden="true" data-bs-backdrop="static">
                                        <div class="modal-dialog modal-xl">
                                            <div class="modal-content border-0 overflow-hidden">
                                                <div class="modal-header p-3">
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>

                                                <div class="modal-body">
                                                    <h4 class="mb-3 mt-4">
                                                        Are you sure you want to unassign the program
                                                        @php
    $assignmentToDelete = optional($singleStaff)->programmeAssignments
        ->firstWhere('id', $assignment->id);
@endphp

@if($assignmentToDelete)
    @php
        $category = $programmeCategories->firstWhere('id', $assignmentToDelete->programme_category_id);
    @endphp
    <strong>{{ $category?->category ?? 'Unknown Category' }}</strong>
@else
    <p>Program not found.</p>
@endif

                                                        from
                                                        {{ $singleStaff->title . ' ' . $singleStaff->lastname . ' ' . $singleStaff->othernames }}?
                                                    </h4>
                                                    <form action="{{ route('unassign.program') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="staff_program_assignment_id"
                                                            value="{{ $assignment->id }}">
                                                        <hr>
                                                        <button type="submit" class="btn btn-danger w-100">Yes,
                                                            Unassign</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="text-muted text-center my-4">No program assignment found for this staff.
                                    </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>


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


<div id="assignRole" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
    style="display: none;">
    <!-- Fullscreen Modals -->
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Add Staff Role</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <div class="modal-body">
                <form action="{{ url('/admin/assignRole') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="staff_id" value="{{ $singleStaff->id }}">

                    <div class="mb-3">
                        <label for="role" class="form-label">Select Role</label>
                        <select class="form-select" aria-label="role" name="role_id" required>
                            <option selected value="">Select Role </option>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->role }}</option>
                            @endforeach
                        </select>
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Assign Role</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@if(!empty($deanRole) && ($singleStaff->id != $singleStaff->faculty->dean_id))
<div id="assignDeanToFaculty" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
    style="display: none;">
    <!-- Fullscreen Modals -->
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Assign Dean To Faculty</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <div class="modal-body">
                <form action="{{ url('/admin/assignDeanToFaculty') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="staff_id" value="{{ $singleStaff->id }}">

                    <div class="mb-3">
                        <label for="role" class="form-label">Select Faculty</label>
                        <select class="form-select" aria-label="role" name="faculty_id" required>
                            <option selected value="">Select Faculty </option>
                            <option value="{{ $singleStaff->faculty->id }}">{{ $singleStaff->faculty->name }}</option>
                        </select>
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Assign Dean To Faculty</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endif

@if(!empty($subDeanRole) && ($singleStaff->id != $singleStaff->faculty->sub_dean_id))
<div id="assignSubDeanToFaculty" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
    style="display: none;">
    <!-- Fullscreen Modals -->
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Assign Dean To Faculty</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <div class="modal-body">
                <form action="{{ url('/admin/assignSubDeanToFaculty') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="staff_id" value="{{ $singleStaff->id }}">

                    <div class="mb-3">
                        <label for="role" class="form-label">Select Faculty</label>
                        <select class="form-select" aria-label="role" name="faculty_id" required>
                            <option selected value="">Select Faculty </option>
                            <option value="{{ $singleStaff->faculty->id }}">{{ $singleStaff->faculty->name }}</option>
                        </select>
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Assign Dean To Faculty</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endif

@if(!empty($hodRole))
<div id="assignHodToDepartment" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
    style="display: none;">
    <!-- Fullscreen Modals -->
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Assign HOD To Department</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <div class="modal-body">
                <form action="{{ url('/admin/assignHodToDepartment') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="staff_id" value="{{ $singleStaff->id }}">

                    <div class="mb-3">
                        <label for="role" class="form-label">Select Department</label>
                        <select class="form-select" aria-label="role" name="department_id" required>
                            <option selected value="">Select Department </option>
                            @foreach($departments as $department)<option value="{{ $department->id }}">
                                {{ $department->name }}
                            </option>@endforeach
                        </select>
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Assign HOD To
                            Department</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endif

<div id="disableStaff" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
    style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="text-end">
                    <button type="button" class="btn-close text-end" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="mt-2">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="hover"
                        style="width:150px;height:150px">
                    </lord-icon>
                    <h4 class="mb-3 mt-4">Are you sure you want to disable <br />
                        {{ $singleStaff->lastname.' '.$singleStaff->othernames }}?
                    </h4>
                    <form action="{{ url('/admin/disableStaff') }}" method="POST">
                        @csrf
                        <input name="staff_id" type="hidden" value="{{$singleStaff->id}}">
                        <hr>
                        <button type="submit" id="submit-button" class="btn btn-danger w-100">Yes, Disable</button>
                    </form>
                </div>
            </div>
            <div class="modal-footer bg-light p-3 justify-content-center">

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="enableStaff" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
    style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="text-end">
                    <button type="button" class="btn-close text-end" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="mt-2">
                    <lord-icon src="https://cdn.lordicon.com/tqywkdcz.json" trigger="hover"
                        style="width:150px;height:150px">
                    </lord-icon>
                    <h4 class="mb-3 mt-4">Are you sure you want to enable <br />
                        {{ $singleStaff->lastname.' '.$singleStaff->othernames }}?
                    </h4>
                    <form action="{{ url('/admin/enableStaff') }}" method="POST">
                        @csrf
                        <input name="staff_id" type="hidden" value="{{$singleStaff->id}}">
                        <hr>
                        <button type="submit" id="submit-button" class="btn btn-success w-100">Yes, Enable</button>
                    </form>
                </div>
            </div>
            <div class="modal-footer bg-light p-3 justify-content-center">

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="editStaff" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Update Staff</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <hr>d
                <form action="{{ url('/admin/updateStaff') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="row mt-3 g-3">
                        <input name="staff_id" type="hidden" value="{{$singleStaff->id}}">

                        <span class="text-muted"> Bio Data</span><br>
                        <div class="col-lg-3">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="title" name="title"
                                    value="{{ $singleStaff->title }}">
                                <label for="title">Title(Mr/Miss/Mrs/Dr/Prof)</label>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="lastname" name="lastname"
                                    value="{{ $singleStaff->lastname }}">
                                <label for="lastname">Lastname(Surname)</label>
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="othernames" name="othernames"
                                    value="{{ $singleStaff->othernames }}">
                                <label for="othernames">Othernames</label>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="email" class="form-control" id="email" name="email"
                                    value="{{ $singleStaff->email }}">
                                <label for="email">Staff Email</label>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="staffId" name="staffId"
                                    value="{{ $singleStaff->staffId }}">
                                <label for="staffId">Staff ID</label>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="phone_number" name="phone_number"
                                    value="{{ $singleStaff->phone_number }}">
                                <label for="phone_number">Staff Mobile Number</label>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="phone_number" name="phone_number"
                                    value="{{ $singleStaff->phone_number }}">
                                <label for="phone_number">Staff Mobile Number</label>
                            </div>
                        </div>

                        <span class="text-muted"> Authentication</span><br>
                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Enter your Passowrd">
                                <label for="password">Password</label>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="password" class="form-control" id="confirm-password"
                                    name="confirm_password" placeholder="Enter your email">
                                <label for="confirm-password">Confirm Password</label>
                            </div>
                        </div>

                        <span class="text-muted"> Academic Information</span><br>
                        <div class="mb-3">
                            <label for="category" class="form-label">Select Staff Category</label>
                            <select class="form-select" aria-label="category" name="category">
                                <option value="" selected>Select Staff Category </option>
                                <option @if($singleStaff->category == 'Academic') selected @endif
                                    value="Academic">Academic</option>
                                <option @if($singleStaff->category == 'Non Academic') selected @endif value="Non
                                    Academic">Non Academic</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="faculty" class="form-label">Select Staff Faculty</label>
                            <select class="form-select" aria-label="faculty" name="faculty_id">
                                <option value="" selected>Select Staff Faculty </option>
                                @foreach($faculties as $faculty)
                                <option @if($singleStaff->faculty_id == $faculty->id) selected @endif value="{{
                                    $faculty->id }}">{{ $faculty->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="department" class="form-label">Select Staff Department</label>
                            <select class="form-select" aria-label="department" name="department_id">
                                <option value="" selected>Select Staff Department </option>
                                @foreach($allDepartments as $allDepartment)
                                <option @if($singleStaff->department_id == $allDepartment->id) selected @endif value="{{
                                    $allDepartment->id }}">{{ $allDepartment->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Staff Qualifications</label>
                            <textarea type="text" class="form-control ckeditor" name="description"
                                id="description">{{ $singleStaff->description }}</textarea>
                        </div>

                        <!--end col-->
                        <div class="col-lg-12 border-top border-top-dashed">
                            <div class="d-flex align-items-start gap-3 mt-3">
                                <button type="submit" id="submit-button"
                                    class="btn btn-primary btn-label right ms-auto nexttab"
                                    data-nexttab="pills-bill-address-tab"><i
                                        class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>
                                    Submit</button>
                            </div>
                        </div>
                        <!--end col-->
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="assignProgramModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Assign Staff to Program Category</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="{{ route('assign.program') }}" method="POST">
                    @csrf
                    <!-- Hidden staff_id -->
                    <input type="hidden" name="staff_id" value="{{ $singleStaff->id }}">

                    <!-- Program Category -->
                    <div class="mb-3">
                        <label for="programme_category_id" class="form-label">Program Category</label>
                        <select class="form-select" name="programme_category_id" required>
                            <option value="">Select Program Category</option>
                            @foreach($programmeCategories as $category)
                            <option value="{{ $category->id }}">{{ $category->category }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Role -->
                    <div class="mb-3">
                        <label for="role_in_programme" class="form-label">Role in Program</label>
                        <select class="form-select" name="role_in_programme" required>
                            <option value="">Select Role</option>
                            <option value="Secretary">Secretary</option>
                            <option value="Coordinator">Coordinator</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label for="status" class="form-label">Assignment Status</label>
                        <select class="form-select" name="status" required>
                            <option value="">Select Status</option>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                            <option value="Suspended">Suspended</option>
                        </select>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Assign</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>


@endsection
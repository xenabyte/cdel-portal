@extends('staff.layout.dashboard')
@php
    $staff = Auth::guard('staff')->user();
    $staffDeanRole = false;
    $staffSubDeanRole = false;
    $staffHODRole = false;
    $staffVCRole = false;
    $staffRegistrarRole = false;
    $staffHRRole = false;
    $staffLevelAdvicerRole = false;
    $staffExamOfficerRole = false;

    // staffAccessLevel
    $staffAccessLevel = null;

    foreach ($staff->staffRoles as $staffRole) {
        $accessLevel = $staffRole['role']['access_level'];

        if ($staffAccessLevel == null || $accessLevel < $minimumAccessLevel) {
            $staffAccessLevel = $accessLevel;
        }
    }
        
    foreach ($staff->staffRoles as $staffRole) {
        if (strtolower($staffRole->role->role) == 'dean') {
            $staffDeanRole = true;
        }
        if (strtolower($staffRole->role->role) == 'sub-dean') {
            $staffSubDeanRole = true;
        }
        if (strtolower($staffRole->role->role) == 'hod') {
            $staffHODRole = true;
        }
        if (strtolower($staffRole->role->role) == 'vice chancellor') {
            $staffVCRole = true;
        }
        if (strtolower($staffRole->role->role) == 'registrar') {
            $staffRegistrarRole = true;
        }
        if (strtolower($staffRole->role->role) == 'human resource') {
            $staffHRRole = true;
        }
        if (strtolower($staffRole->role->role) == 'level adviser') {
            $staffLevelAdvicerRole = true;
        }
        if (strtolower($staffRole->role->role) == 'exam officer') {
            $staffExamOfficerRole = true;
        }
    }
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
                                            <img src="{{ !empty($singleStaff->image) ? $singleStaff->image : asset('assets/images/users/user-dummy-img.jpg') }}" alt="" class="img-thumbnail rounded-circle avatar-md">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md">
                                    <div>
                                        <h4 class="fw-bold">{{ $singleStaff->title.' '.$singleStaff->lastname .' '. $singleStaff->othernames }}</h4>
                                        <div class="hstack gap-3 flex-wrap">
                                            <div><i class="ri-building-line align-bottom me-1"></i> {{  $singleStaff->current_position }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if(!empty($staffAccessLevel) && $staffAccessLevel < 6)
                        <div class="col-md-auto">
                            <div class="hstack gap-1 flex-wrap">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignRole">Assign Role</button>
                                @if(!empty($staffAccessLevel) && $staffAccessLevel < 3)
                                    @if(empty($singleStaff->deleted_at))
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#disableStaff">Disable Staff</button>
                                    @else
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#enableStaff">Enable Staff</button>
                                    @endif
                                @endif

                                @if(!empty($singleStaff->faculty))
                                    @if(!empty($staffAccessLevel) && $staffAccessLevel < 2 && ($singleStaff->id != $singleStaff->faculty->dean_id))
                                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#assignDeanToFaculty"> Assign Dean To Faculty</button>
                                    @endif
                                @endif
                                @if(!empty($singleStaff->faculty))
                                    @if(!empty($staffAccessLevel) && $staffAccessLevel < 3 && ($singleStaff->id != $singleStaff->faculty->sub_dean_id) && ($singleStaff->id != $singleStaff->faculty->dean_id))
                                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#assignSubDeanToFaculty"> Assign Sub Dean To Faculty</button>
                                    @endif
                                @endif
                                @if(!empty($singleStaff->department))
                                    @if(!empty($staffAccessLevel) &&  $staffAccessLevel < 4 && ($singleStaff->id != $singleStaff->department->hod_id))
                                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#assignHodToDepartment"> Assign HOD To Department</button>
                                    @endif
                                @endif

                            </div>
                        </div>
                        @endif
                    </div>

                    <ul class="nav nav-tabs-custom border-bottom-0" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active fw-semibold" data-bs-toggle="tab" href="#project-overview" role="tab">
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
                                    {!! $singleStaff->description  !!}
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
                                                <td class="text-muted">{{ $singleStaff->title.' '.$singleStaff->lastname.' '. $singleStaff->othernames}}</td>
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
                                                <td class="text-muted">{{ !empty($singleStaff->faculty) ? $singleStaff->faculty->name : null }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->

                        @if(!empty($singleStaff->faculty) && (($singleStaff->id == $singleStaff->faculty->dean_id) || ($singleStaff->id == $singleStaff->faculty->sub_dean_id)))
                            <div class="card-body">
                                <div class="card">
                                    <div class="card-header">
                                        <button type="button" class="btn-close float-end fs-11" aria-label="Close"></button>
                                        <h6 class="card-title mb-0">Position</h6>
                                    </div>
                                    <div class="card-body p-4 text-center">
                                        <div class="mx-auto avatar-md mb-3">
                                            <img src="{{ !empty($singleStaff->image) ? $singleStaff->image : asset('assets/images/users/user-dummy-img.jpg') }}" alt="" class="img-fluid rounded-circle">
                                        </div>
                                        <h5 class="card-title mb-1">{{ $singleStaff->title.' '.$singleStaff->lastname.' '. $singleStaff->othernames}}</h5>
                                        @if($singleStaff->id == $singleStaff->faculty->dean_id)
                                        <p class="text-muted mb-0">Dean,  {{$singleStaff->faculty->name}}</p>
                                        @endif
                                        @if($singleStaff->id == $singleStaff->faculty->sub_dean_id)
                                        <p class="text-muted mb-0">Sub Dean,  {{$singleStaff->faculty->name}}</p>
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
                                                <h5 class="fs-13 mb-0"><a href="#" class="text-body d-block">{{ $loop->iteration }}. {{$singleStaffRole->role->role}}</a></h5>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <div class="d-flex align-items-center gap-1">
                                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#unAssignRole{{$singleStaffRole->id}}" class="link-danger"><i class="ri-delete-bin-5-line"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="unAssignRole{{$singleStaffRole->id}}" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
                                        <!-- Fullscreen Modals -->
                                        <div class="modal-dialog modal-md">
                                            <div class="modal-content border-0 overflow-hidden">
                                                <div class="modal-header p-3">
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                    
                                                <div class="modal-body">
                                                    <h4 class="mb-3 mt-4">Are you sure you want to delete <br/> {{ $singleStaffRole->role->role }} role from {{ $singleStaff->title.' '.$singleStaff->lastname.' '. $singleStaff->othernames}}?</h4>
                                                    <form action="{{ url('/admin/unAssignRole') }}" method="POST">
                                                        @csrf
                                                        <input name="staff_role_id" type="hidden" value="{{$singleStaffRole->id}}">
                                                        <hr>
                                                        <button type="submit" class="btn btn-danger w-100">Yes, Delete</button>
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

@if(!empty($staffAccessLevel))
<div id="assignRole" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
    <!-- Fullscreen Modals -->
    <div class="modal-dialog modal-md modal-dialog-centered">
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
                            <option selected value= "">Select Role </option>
                            @foreach($roles as $role)
                                @if($staffAccessLevel < $role->access_level)
                                    <option value="{{ $role->id }}">{{ $role->role }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Assign Role</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endif

@if(!empty($singleStaff->faculty))
    @if(!empty($staffAccessLevel) && $staffAccessLevel < 2 && ($singleStaff->id != $singleStaff->faculty->dean_id))    
        <div id="assignDeanToFaculty" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
            <!-- Fullscreen Modals -->
            <div class="modal-dialog modal-md modal-dialog-centered">
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
                                    <option selected value= "">Select Faculty </option>
                                    <option value="{{ $singleStaff->faculty->id }}">{{ $singleStaff->faculty->name }}</option>
                                </select>
                            </div>

                            <hr>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Assign Dean To Faculty</button>
                            </div>
                        </form>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    @endif
@endif

@if(!empty($singleStaff->faculty))
    @if(!empty($staffAccessLevel) && $staffAccessLevel < 3 && ($singleStaff->id != $singleStaff->faculty->sub_dean_id)) 
        <div id="assignSubDeanToFaculty" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
            <!-- Fullscreen Modals -->
            <div class="modal-dialog modal-md modal-dialog-centered">
                <div class="modal-content border-0 overflow-hidden">
                    <div class="modal-header p-3">
                        <h4 class="card-title mb-0">Assign Sub Dean To Faculty</h4>
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
                                    <option selected value= "">Select Faculty </option>
                                    <option value="{{ $singleStaff->faculty->id }}">{{ $singleStaff->faculty->name }}</option>
                                </select>
                            </div>

                            <hr>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Assign Dean To Faculty</button>
                            </div>
                        </form>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    @endif
@endif

@if(!empty($singleStaff->department))
    @if(!empty($staffAccessLevel) && $staffAccessLevel < 4 && ($singleStaff->id != $singleStaff->department->hod_id)) 
        <div id="assignHodToDepartment" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
            <!-- Fullscreen Modals -->
            <div class="modal-dialog modal-md modal-dialog-centered">
                <div class="modal-content border-0 overflow-hidden">
                    <div class="modal-header p-3">
                        <h4 class="card-title mb-0">Assign Dean To Faculty</h4>
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
                                    <option selected value= "">Select Department </option>
                                    @foreach($departments as $department)<option value="{{ $department->id }}">{{ $department->name }}</option>@endforeach
                                </select>
                            </div>

                            <hr>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Assign Dean To Faculty</button>
                            </div>
                        </form>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    @endif
@endif

<div id="disableStaff" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="text-end">
                    <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="mt-2">
                    <lord-icon src="https://cdn.lordicon.com/wwneckwc.json" trigger="hover" style="width:150px;height:150px">
                    </lord-icon>
                    <h4 class="mb-3 mt-4">Are you sure you want to disable <br/> {{ $singleStaff->lastname.' '.$singleStaff->othernames }}?</h4>
                    <form action="{{ url('/admin/disableStaff') }}" method="POST">
                        @csrf
                        <input name="staff_id" type="hidden" value="{{$singleStaff->id}}">
                        <hr>
                        <button type="submit" class="btn btn-danger w-100">Yes, Disable</button>
                    </form>
                </div>
            </div>
            <div class="modal-footer bg-light p-3 justify-content-center">

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="enableStaff" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="text-end">
                    <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="mt-2">
                    <lord-icon src="https://cdn.lordicon.com/tqywkdcz.json" trigger="hover" style="width:150px;height:150px">
                    </lord-icon>
                    <h4 class="mb-3 mt-4">Are you sure you want to enable <br/> {{ $singleStaff->lastname.' '.$singleStaff->othernames }}?</h4>
                    <form action="{{ url('/admin/enableStaff') }}" method="POST">
                        @csrf
                        <input name="staff_id" type="hidden" value="{{$singleStaff->id}}">
                        <hr>
                        <button type="submit" class="btn btn-success w-100">Yes, Enable</button>
                    </form>
                </div>
            </div>
            <div class="modal-footer bg-light p-3 justify-content-center">

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@endsection
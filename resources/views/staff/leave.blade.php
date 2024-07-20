@extends('staff.layout.dashboard')
@php

$staffId = Auth::guard('staff')->user()->id;

$staff = $leave->staff;
$name = $staff->title.' '.$staff->lastname.' '.$staff->othernames;

$assistingStaff = $leave->assistingStaff;
$assistingStaffId = $leave->assisting_staff_id;
$assistingstaffName = $assistingStaff->title.' '.$assistingStaff->lastname.' '.$assistingStaff->othernames;
@endphp
@section('content')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Leave Details</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Leave Details</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-lg-12">
        <div class="card mt-n4 mx-n4">
            <div class="bg-soft-info">
                <div class="card-body pb-0 px-4">
                    <div class="row mb-3">
                        <div class="col-md">
                            <div class="row align-items-center g-3">
                                <div class="col-md-auto">
                                    <div class="avatar-md">
                                        <div class="avatar-title bg-white rounded-circle">
                                            <img src="{{ !empty($staff->image) ? $staff->image : asset('assets/images/users/user-dummy-img.jpg') }}" alt="" class="img-thumbnail rounded-circle avatar-md">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md">
                                    <div>
                                        <h4 class="fw-bold">{{ $staff->title.' '.$staff->lastname .' '. $staff->othernames }}</h4>
                                        <div class="hstack gap-3 flex-wrap">
                                            <div><i class="ri-building-line align-bottom me-1"></i> {{  $staff->current_position }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-auto">
                            <div class="hstack gap-1 flex-wrap">
                                @if($assistingStaffId == $staffId)
                                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#assistingStaffMgt"> Manage Leave</button>
                                @endif
                                @if($leave->hod_id == $staffId)
                                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#hodMgt"> Manage Leave</button>
                                @endif
                                @if($leave->dean_id == $staffId)
                                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#deanMgt"> Manage Leave</button>
                                @endif
                                @if($leave->hr_id == $staffId)
                                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#hrMgt"> Manage Leave</button>
                                @endif
                                @if($leave->registrar_id == $staffId)
                                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#registrarMgt"> Manage Leave</button>
                                @endif
                                @if($leave->vc_id == $staffId)
                                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#vcMgt"> Manage Leave</button>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
                <!-- end card body -->
            </div>
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->
</div>

<div class="row">
    <div class="col-xl-3">
        <div class="card card-height-100">
            <div class="card-body">
                <div class="d-flex mb-4 align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="text-primary fs-18 mb-0"><strong>Leave Duration:</strong><span> {{ $leave->days }} Day(s)</span></h5>
                    </div>
                </div>
                <hr>   
                <div class="row mb-3">
                    <div class="col-xl-12">
                        <h6 class="fs-14 mb-2">Leave Details</h6>
                        <p class="text-muted"><strong>Purpose:</strong> {!! $leave->purpose !!}</p>
                        <p class="text-muted"><strong>Destination:</strong> {!! $leave->destination_address !!}</p>
                        <p class="text-muted"><strong>Start Date:</strong> {{ \Carbon\Carbon::parse($leave->start_date)->format('jS \o\f F, Y') }}</p>
                        <p class="text-muted"><strong>Resumption Date:</strong> {{ \Carbon\Carbon::parse($leave->end_date)->format('jS \o\f F, Y') }}</p>

                    </div>
                    <!-- end col -->
                </div>    
                <hr>
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-sm me-3 flex-shrink-0">
                        <div class="avatar-title bg-danger-subtle rounded">
                            <img src="{{ !empty($assistingstaff->image) ? $assistingstaff->image : asset('assets/images/users/user-dummy-img.jpg') }}" alt="" class="avatar-xs">
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted mb-2">Assisting Staff</p>
                        <h5 class="fs-15 fw-semibold mb-0">{{ $assistingstaffName }}</h5>
                    </div>
                </div>
                <a href="#!" class="btn {{ !empty($leave->assisting_staff_status && $leave->assisting_staff_statys=='approved')? 'btn-soft-success' : ' btn-soft-info' }} d-block">{{ !empty($leave->assisting_staff_status)? ucwords($leave->assisting_staff_status) : 'Pending' }}</a> 
            </div>
        </div>

        
    </div>
    <!--end col-->
    <div class="col-xl-9">

        <div class="card card-height-100">
            <div class="card-header border-bottom-dashed align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Leave Approval Activities</h4>
            </div><!-- end cardheader -->
            <div class="card-body p-0">
                <div data-simplebar="init" style="max-height: 364px;" class="p-3 simplebar-scrollable-y"><div class="simplebar-wrapper" style="margin: -16px;"><div class="simplebar-height-auto-observer-wrapper"><div class="simplebar-height-auto-observer"></div></div><div class="simplebar-mask"><div class="simplebar-offset" style="right: 0px; bottom: 0px;"><div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content" style="height: auto; overflow: hidden scroll;"><div class="simplebar-content" style="padding: 16px;">
                    <div class="acitivity-timeline acitivity-main">
                       
                        @if(!empty($leave->vc_id) && $leave->viceChancellor)
                        <div class="acitivity-item d-flex mb-3">
                            <div class="flex-shrink-0">
                                <img src="{{ $leave->viceChancellor->image }}" alt="" class="avatar-xs rounded-circle acitivity-avatar shadow">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">{{ $leave->viceChancellor->title.' '.$leave->viceChancellor->lastname.' '.$leave->viceChancellor->othernames }}</h6>
                                <p class="text-muted mb-2 fst-italic">{{ ucwords($leave->vc_status) }}</p>
                                <small class="mb-0 text-muted">{{ $leave->vc_comment }}</small>
                            </div>
                        </div>
                        @endif
                        @if(!empty($leave->registrar_id) && $leave->registrar)
                        <div class="acitivity-item d-flex mb-3">
                            <div class="flex-shrink-0">
                                <img src="{{ $leave->registrar->image }}" alt="" class="avatar-xs rounded-circle acitivity-avatar shadow">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">{{ $leave->registrar->title.' '.$leave->registrar->lastname.' '.$leave->registrar->othernames }}</h6>
                                <p class="text-muted mb-2 fst-italic">{{ ucwords($leave->registrar_status) }}</p>
                                <small class="mb-0 text-muted">{{ $leave->registrar_comment }}</small>
                            </div>
                        </div>
                        @endif
                        @if(!empty($leave->hr_id) && $leave->humanResource)
                        <div class="acitivity-item d-flex mb-3">
                            <div class="flex-shrink-0">
                                <img src="{{ $leave->humanResource->image }}" alt="" class="avatar-xs rounded-circle acitivity-avatar shadow">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">{{ $leave->humanResource->title.' '.$leave->humanResource->lastname.' '.$leave->humanResource->othernames }}</h6>
                                <p class="text-muted mb-2 fst-italic">{{ ucwords($leave->hr_status) }}</p>
                                <small class="mb-0 text-muted">{{ $leave->hr_comment }}</small>
                            </div>
                        </div>
                        @endif
                        @if(!empty($leave->dean_id) && $leave->dean)
                        <div class="acitivity-item d-flex mb-3">
                            <div class="flex-shrink-0">
                                <img src="{{ $leave->dean->image }}" alt="" class="avatar-xs rounded-circle acitivity-avatar shadow">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">{{ $leave->dean->title.' '.$leave->dean->lastname.' '.$leave->dean->othernames }}</h6>
                                <p class="text-muted mb-2 fst-italic">{{ ucwords($leave->dean_status) }}</p>
                                <small class="mb-0 text-muted">{{ $leave->dean_comment }}</small>
                            </div>
                        </div>
                        @endif
                        @if(!empty($leave->hod_id) && $leave->hod)
                        <div class="acitivity-item d-flex mb-3">
                            <div class="flex-shrink-0">
                                <img src="{{ $leave->hod->image }}" alt="" class="avatar-xs rounded-circle acitivity-avatar shadow">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">{{ $leave->hod->title.' '.$leave->hod->lastname.' '.$leave->hod->othernames }}</h6>
                                <p class="text-muted mb-2 fst-italic">{{ ucwords($leave->hod_status) }}</p>
                                <small class="mb-0 text-muted">{{ $leave->hod_comment }}</small>
                            </div>
                        </div>
                        @endif
                        @if(!empty($leave->assisting_staff_id) && $leave->assistingStaff)
                        <div class="acitivity-item d-flex mb-3">
                            <div class="flex-shrink-0">
                                <img src="{{ $leave->assistingStaff->image }}" alt="" class="avatar-xs rounded-circle acitivity-avatar shadow">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">{{ $leave->assistingStaff->title.' '.$leave->assistingStaff->lastname.' '.$leave->assistingStaff->othernames }}</h6>
                                <p class="text-muted mb-2 fst-italic">{{ ucwords($leave->assisting_staff_status) }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div></div></div></div><div class="simplebar-placeholder" style="width: 342px; height: 895px;"></div></div><div class="simplebar-track simplebar-horizontal" style="visibility: hidden;"><div class="simplebar-scrollbar" style="width: 0px; display: none;"></div></div><div class="simplebar-track simplebar-vertical" style="visibility: visible;"><div class="simplebar-scrollbar" style="height: 148px; transform: translate3d(0px, 0px, 0px); display: block;"></div></div></div>
            </div><!-- end card body -->
        </div>
    </div>
    <!--end col-->
</div>
<!--end row-->

<div id="assistingStaffMgt" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
    <!-- Fullscreen Modals -->
    <div class="modal-dialog modal-md">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Manage Leave as Assisting Staff</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <div class="modal-body">
                <form action="{{ url('/staff/manageLeave') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="leave_id" value="{{ $leave->id }}">
                    <input type="hidden" name="role" value="assisting_staff">

                    <div class="mb-3">
                        <label for="role" class="form-label">Select Option</label>
                        <select class="form-select" aria-label="role" name="status" required>
                            <option selected value= "">Select Option </option>
                            <option value="approved">Approve</option>
                            <option value="declined">Decline</option>
                        </select>
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div id="hodMgt" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
    <!-- Fullscreen Modals -->
    <div class="modal-dialog modal-md">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Manage Leave as HOD/HOU</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <div class="modal-body">
                <form action="{{ url('/staff/manageLeave') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="leave_id" value="{{ $leave->id }}">
                    <input type="hidden" name="role" value="hod">

                    <div class="mb-3">
                        <label for="comment" class="form-label">Comment</label>
                        <textarea class="form-control ckeditor" name="comment" id="comment"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Select Option</label>
                        <select class="form-select" aria-label="role" name="status" required>
                            <option selected value= "">Select Option </option>
                            <option value="approved">Approve</option>
                            <option value="declined">Decline</option>
                        </select>
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div id="deanMgt" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
    <!-- Fullscreen Modals -->
    <div class="modal-dialog modal-md">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Manage Leave as DEAN</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <div class="modal-body">
                <form action="{{ url('/staff/manageLeave') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="leave_id" value="{{ $leave->id }}">
                    <input type="hidden" name="role" value="dean">

                    <div class="mb-3">
                        <label for="comment" class="form-label">Comment</label>
                        <textarea class="form-control ckeditor" name="comment" id="comment"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Select Option</label>
                        <select class="form-select" aria-label="role" name="status" required>
                            <option selected value= "">Select Option </option>
                            <option value="approved">Approve</option>
                            <option value="declined">Decline</option>
                        </select>
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="hrMgt" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
    <!-- Fullscreen Modals -->
    <div class="modal-dialog modal-md">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Manage Leave as HR</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <div class="modal-body">
                <form action="{{ url('/staff/manageLeave') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="leave_id" value="{{ $leave->id }}">
                    <input type="hidden" name="role" value="hr">

                    <div class="mb-3">
                        <label for="comment" class="form-label">Comment</label>
                        <textarea class="form-control ckeditor" name="comment" id="comment"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Select Option</label>
                        <select class="form-select" aria-label="role" name="status" required>
                            <option selected value= "">Select Option </option>
                            <option value="approved">Approve</option>
                            <option value="declined">Decline</option>
                        </select>
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="registrarMgt" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
    <!-- Fullscreen Modals -->
    <div class="modal-dialog modal-md">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Manage Leave as Registrar</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <div class="modal-body">
                <form action="{{ url('/staff/manageLeave') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="leave_id" value="{{ $leave->id }}">
                    <input type="hidden" name="role" value="registrar">

                    <div class="mb-3">
                        <label for="comment" class="form-label">Comment</label>
                        <textarea class="form-control ckeditor" name="comment" id="comment"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Select Option</label>
                        <select class="form-select" aria-label="role" name="status" required>
                            <option selected value= "">Select Option </option>
                            <option value="approved">Approve</option>
                            <option value="declined">Decline</option>
                        </select>
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="vcMgt" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
    <!-- Fullscreen Modals -->
    <div class="modal-dialog modal-md">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Manage Leave as DEAN</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <div class="modal-body">
                <form action="{{ url('/staff/manageLeave') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="leave_id" value="{{ $leave->id }}">
                    <input type="hidden" name="role" value="vc">

                    <div class="mb-3">
                        <label for="comment" class="form-label">Comment</label>
                        <textarea class="form-control ckeditor" name="comment" id="comment"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Select Option</label>
                        <select class="form-select" aria-label="role" name="status" required>
                            <option selected value= "">Select Option </option>
                            <option value="approved">Approve</option>
                            <option value="declined">Decline</option>
                        </select>
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@endsection

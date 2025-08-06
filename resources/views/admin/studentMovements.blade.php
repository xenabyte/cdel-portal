@extends('admin.layout.dashboard')
@php
    $role = 'student care';
@endphp
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Student Movements</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Student Movements</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->
@if(empty($student))
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col-lg-9">
                        <h4 class="mt-4 fw-semibold">Get Student Movements</h4>
                        <p class="text-muted mt-3"></p>
                        <div class="mt-4">
                            <form action="{{ url('/admin/getStudentMovement') }}" method="POST">
                                @csrf
                                <div class="row g-3">

                                     <div class="form-floating mb-3">
                                        <select class="form-select select2 selectWithSearch" id="selectWithSearch" name="student_id" aria-label="username">
                                            <option value="" selected>-- Student Name --</option>
                                            @foreach($students as $student)<option value="{{$student->id}}">{{$student->applicant->lastname.' '.$student->applicant->othernames}}</option>@endforeach
                                        </select>
                                    </div>
    
                                    <button type="submit" id="submit-button" class="btn btn-fill btn-primary btn-lg btn-block mb-5">Fetch Records</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>

@else

<div class="row">
    <div class="col-xxl-4">
        <div class="card">
            <div class="card-body p-4">
                <div>
                    <div class="flex-shrink-0 avatar-md mx-auto">
                        <div class="avatar-title bg-light rounded">
                            <img src="{{empty($student->image)?asset('assets/images/users/user-dummy-img.jpg'):asset($student->image)}}" alt="" height="50" />
                        </div>
                    </div>
                    <div class="mt-4 text-center">
                        <h5 class="mb-1">{{$student->applicant->lastname.' '.$student->applicant->othernames}}</h5>
                        @php
                            $studentAdvisoryData = (object) $student->getAcademicAdvisory();
                            $failedCourses = $student->registeredCourses()->where('grade', 'F')->where('re_reg', null)->get();
                        @endphp
                        <p class="text-muted">{{ $student->programme->name }} <br>
                            <strong>Matric Number:</strong> {{ $student->matric_number }}<br>
                            <strong>Wifi Username:</strong> {{ $student->bandwidth_username }}<br>
                            <strong>Email:</strong> {{ $student->email }}<br>
                            <strong>Phone Number:</strong> {{ $student->applicant->phone_number }}<br>
                            <strong>Address:</strong> {!! preg_replace('/<\/?p[^>]*>/', '', $student->applicant->address) !!}<br>
                            @if(env('WALLET_STATUS'))<a class="dropdown-item" href="#"><i class="mdi mdi-wallet text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Balance : <b>₦{{ number_format($student->amount_balance/100, 2) }}</b></span></a>@endif
                        </p>
                        <p class="text-muted border-top border-top-dashed pt-2">
                            <strong>Programme Category:</strong> {{ $student->programmeCategory->category }} Programme<br>
                            <strong>Department:</strong> {{ $student->department->name }}<br>
                            <strong>Faculty:</strong> {{ $student->faculty->name }}<br>
                            <strong>Jamb Reg. Number:</strong> {{ $student->applicant->jamb_reg_no }} <br>
                            <strong>Academic Level:</strong> <span class="text-primary">{{ $student->level_id * 100 }} Level</span><br>
                            <strong>Academic session:</strong> {{ $student->academic_session }}</span>
                            <br>
                            @if($student->level_id >= $student->programme->duration && !$student->is_passed_out)
                            <span class="text-warning"><strong>Graduating Set</strong></span> <br>
                            @endif
                            <strong>Support Code:</strong> <span class="text-danger">{{ $student->applicant->id }}-ST{{ sprintf("%03d", $student->id) }}</span> 
                        </p>
                    </div>
                </div>
            </div>

            @if(!empty($student->applicant->guardian))
            <div class="card-body border-top border-top-dashed p-4">
                <div>
                    <h6 class="text-muted text-uppercase fw-semibold mb-4">Guardian Info</h6>
                    <div class="table-responsive">
                        <table class="table mb-0 table-borderless">
                            <tbody>
                                <tr>
                                    <th><span class="fw-medium">Name</span></th>
                                    <td>{{ $student->applicant->guardian->name }}</td>
                                </tr>
                                <tr>
                                    <th><span class="fw-medium">Email</span></th>
                                    <td>{{ $student->applicant->guardian->email }}</td>
                                </tr>
                                <tr>
                                    <th><span class="fw-medium">Contact No.</span></th>
                                    <td>{{ $student->applicant->guardian->phone_number }}</td>
                                </tr>
                                <tr>
                                    <th><span class="fw-medium">Address</span></th>
                                    <td>{!! $student->applicant->guardian->address !!}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
        <!--end card-->
    
    </div>
    <!--end col-->

    <div class="col-xxl-8">
        <div class="card">
            <div class="card-header border-0 align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Movements</h4>
                <div class="text-end">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createMovement">Create Movement</button>
                </div>
            </div><!-- end card header -->

            <div class="card-body pb-2 border-top border-top-dashed">
                <table id="fixed-header" class="table table-bordered table-responsive nowrap table-striped align-middle" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Movement ID</th>
                            <th scope="col">Type</th>
                            <th scope="col">Date & Time</th>
                            <th scope="col">Reason</th>
                            <th scope="col">Approved By</th>
                            <th scope="col">Recorded On</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($student->movements as $movement)
                            <tr>
                                <td>#{{ sprintf("%06d", $movement->id) }}</td>
                                <td>
                                    <span class="badge bg-{{ $movement->movement_type == 'entry' ? 'success' : 'danger' }}">
                                        {{ ucfirst($movement->movement_type) }}
                                    </span>
                                </td>
                                <td>{{ date('F j, Y \a\t g:i A', strtotime($movement->movement_time)) }}</td>
                                <td>{{ $movement->reason ?? '-' }}</td>
                                <td>{{ $movement->approved_by ?? '-' }}</td>
                                <td>{{ date('F j, Y \a\t g:i A', strtotime($movement->created_at)) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div><!-- end card body -->
        </div><!-- end card -->


         <div class="card">
            @if(!empty($exitApplications) && $exitApplications->count() > 0)
            <div class="card-body">
                <h6 class="mb-3 fw-semibold text-uppercase">Pending Student Exit Application(s)</h6>
                <form action="{{ url('/admin/bulkManageExitApplications') }}" method="POST">
                    @csrf
                    
                    <input type="hidden" name="role" value="{{ $role }}">
                    <div class="table-responsive">
                        <table id="fixed-header" class="table table-bordered table-responsive nowrap table-striped align-middle" style="width:100%">
                            <thead>
                                <tr>
                                    <th scope="col"><input type="checkbox" id="select-all"></th>
                                    <th scope="col">Application ID</th>
                                    <th scope="col">Purpose</th>
                                    <th scope="col">Destination</th>
                                    <th scope="col">Outing Date</th>
                                    <th scope="col">Returning Date</th>
                                    <th scope="col">Application Date
                                    {{-- <th scope="col">File</th> --}}
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($exitApplications as $exitApplication)
                                    <tr>
                                        <td><input type="checkbox" name="exit_ids[]" value="{{ $exitApplication->id }}"></td>
                                        <td>#{{ sprintf("%06d", $exitApplication->id) }}</td>
                                        <td>{{ $exitApplication->purpose }}</td>
                                        <td>{{ $exitApplication->destination }}</td>
                                        <td>{{ empty($exitApplication->exit_date) ? null : date('F j, Y', strtotime($exitApplication->exit_date)) }}</td>
                                        <td>{{ empty($exitApplication->return_date) ? null : date('F j, Y \a\t g:i A', strtotime($exitApplication->return_date)) }}</td>
                                        <td>{{ empty($exitApplication->created_at) ? null : date('F j, Y \a\t g:i A', strtotime($exitApplication->created_at)) }}</td>
                                        {{-- <td><a href="{{ asset($exitApplication->file) }}" class="btn btn-outline-primary" target="_blank">View Document</a></td> --}}
                                        <td>{{ ucwords($exitApplication->status) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" name="action" value="approved" class="btn btn-success me-2">Approve Selected</button>
                        <button type="submit" name="action" value="declined" class="btn btn-danger">Decline Selected</button>
                    </div>
                </form>
            </div>
            @endif
        </div>

        @foreach($exitApplications as $exitApplication)
        <div id="decline{{$exitApplication->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center p-5">
                        <div class="text-end">
                            <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="mt-2">
                            <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                            </lord-icon>
                            <h4 class="mb-3 mt-4">Are you sure you want to decline <br/> {{ isset($exitApplication->student->applicant)?$exitApplication->student->applicant->lastname .' ' . $exitApplication->student->applicant->othernames:null}} exit application?</h4>
                            <form action="{{ url('/admin/manageExitApplication') }}" method="POST">
                                @csrf
                                <input type="hidden" name="role" value="{{ $role }}">
                                <input name="exit_id" type="hidden" value="{{$exitApplication->id}}">
                                <input name="student_id" type="hidden" value="{{$exitApplication->student->id}}">
                                <input name="action" type="hidden" value="declined">
                                <hr>
                                <button type="submit" id="submit-button" class="btn btn-danger w-100">Yes, Decline</button>
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer bg-light p-3 justify-content-center">

                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <div id="approve{{$exitApplication->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center p-5">
                        <div class="text-end">
                            <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="mt-2">
                            <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="hover" style="width:150px;height:150px">
                            </lord-icon>
                            <h4 class="mb-3 mt-4">Are you sure you want to approve <br/> {{ isset($exitApplication->student->applicant)? $exitApplication->student->applicant->lastname .' ' . $exitApplication->student->applicant->othernames: null }} exit application?</h4>
                            <form action="{{ url('/admin/manageExitApplication') }}" method="POST">
                                @csrf
                                <input type="hidden" name="role" value="{{ $role }}">
                                <input name="exit_id" type="hidden" value="{{$exitApplication->id}}">
                                <input name="student_id" type="hidden" value="{{$exitApplication->student->id}}">
                                <input name="action" type="hidden" value="approved">
                                <hr>
                                <button type="submit" id="submit-button" class="btn btn-danger w-100">Yes, Approve</button>
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer bg-light p-3 justify-content-center">

                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        @endforeach
    </div>
    <!--end col-->
</div>
<!--end row-->

<div id="createMovement" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Create Movement</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body border-top border-top-dashed">
                <form action="{{ url('/admin/createMovement') }}" method="POST">
                    @csrf

                    <input type="hidden" name="student_id" value="{{ $student->id }}">

                    <!-- Movement Type -->
                    <div class="mb-3">
                        <label for="movement_type" class="form-label">Movement Type</label>
                        <select name="movement_type" id="movement_type" class="form-control" required>
                            <option value="" disabled selected>Select Type</option>
                            <option value="entry">Entry (Resumption)</option>
                            <option value="exit">Exit (Leaving Campus)</option>
                        </select>
                    </div>

                    <!-- Date and Time -->
                    <div class="mb-3">
                        <label for="movement_time" class="form-label">Date & Time</label>
                        <input type="datetime-local" name="movement_time" id="movement_time" class="form-control" required>
                    </div>

                    <!-- Reason -->
                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason (optional)</label>
                        <input type="text" name="reason" id="reason" class="form-control" placeholder="E.g. Resumption, Medical, Visit">
                    </div>

                    <!-- Approved By (Optional for Exit) -->
                    <div class="mb-3">
                        <label for="approved_by" class="form-label">Approved By (if applicable)</label>
                        <input type="text" name="approved_by" id="approved_by" class="form-control" placeholder="Dean, HOD, etc.">
                    </div>

                    <!-- Submit Button -->
                    <div class="text-end border-top border-top-dashed p-3">
                        <br>
                        <button type="submit" class="btn btn-primary">Record Movement</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@endif



@endSection
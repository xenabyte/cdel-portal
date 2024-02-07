@extends('admin.layout.dashboard')

@section('content')
 <!-- start page title -->
 <div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Student Demotion</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Student Demotion</li>
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
                <h4 class="card-title mb-0 flex-grow-1">Get Student Information</h4>
                <div class="flex-shrink-0">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#getStudent">Get Student</button>
                </div>
            </div><!-- end card header -->
        </div><!-- end card -->
    </div>
</div>
<!-- end row -->

<div id="getStudent" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Get Student</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">

                <form action="{{ url('/admin/getStudent') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="type" value="Student">
                    <input type="hidden" name="url" value="admin.demoteStudent">
                    <div class="mb-3">
                        <label for="reg" class="form-label">Matric Number</label>
                        <input type="text" class="form-control" name="reg_number" id="reg">
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Get student</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@if(!empty($student))
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
                        <p class="text-muted">{{ $student->programme->name }} <br>
                            <strong>Matric Number:</strong> {{ $student->matric_number }}<br>
                            <strong>Jamb Reg. Number:</strong> {{ $student->applicant->jamb_reg_no }}<br><br>
                            <strong>Support Code:</strong> <span class="text-danger">ST{{ sprintf("%06d", $student->id) }}</span> 
                            <hr>
                            @if(env('WALLET_STATUS'))<a class="dropdown-item" href=#"><i class="mdi mdi-wallet text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Balance : <b>₦{{ number_format($student->amount_balance/100, 2) }}</b></span></a>@endif
                        </p>
                    </div>
                    <div class="table-responsive border-top border-top-dashed">
                        <table class="table mb-0 table-borderless">
                            <tbody>
                                <tr>
                                    <th><span class="fw-medium">Department:</span></th>
                                    <td>{{ $student->department->name }}</td>
                                </tr>
                                <tr>
                                    <th><span class="fw-medium">Faculty:</span></th>
                                    <td>{{ $student->faculty->name }}</td>
                                </tr>
                                <tr>
                                    <th><span class="fw-medium">Email:</span></th>
                                    <td>{{ $student->email }}</td>
                                </tr>
                                <tr>
                                    <th><span class="fw-medium">Contact No.:</span></th>
                                    <td>{{ $student->applicant->phone_number }}</td>
                                </tr>
                                <tr>
                                    <th><span class="fw-medium">Address:</span></th>
                                    <td>{!! $student->applicant->address !!}</td>
                                </tr>
                            </tbody>
                        </table>
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
                                    <th><span class="fw-medium">SN</span></th>
                                    <td class="text-danger">#{{ $student->applicant->guardian->id }}</td>
                                </tr>
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
        {{-- Student Message --}}
        <div class="card">
            <div class="card-header border-0 align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Demote Student</h4>
                <div class="text-end mb-5">
                    
                </div>
            </div><!-- end card header -->

            <div class="card-body pb-2 border-top border-top-dashed">
                <form action="{{ url('/admin/makeDemoteStudent') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                    <input type="hidden" name="url" value="admin.demoteStudent">

                    <div class="row g-3">
                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="current" value="{{ $student->academicLevel->level }} Level" disabled readonly>
                                <label for="current">Current Level</label>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-floating">
                                <select class="form-select" id="new" aria-label="New Level" name="new_level" required>
                                  <option value="" selected>-- Select Level --</option>
                                  @foreach($levels as $level)<option value="{{ $level->id }}">{{ $level->level }} Level</option>@endforeach;
                                </select>
                                <label for="new">New Level</label>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="current" value="{{ $student->programme->name }}" disabled readonly>
                                <label for="current">Current Programme</label>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-floating">
                                <select class="form-select" id="new" aria-label="New Programme" name="new_programme">
                                  <option value="" selected>-- Select Programme --</option>
                                  @foreach($programmes as $programmes)<option value="{{ $programmes->id }}">{{ $programmes->name }}</option>@endforeach;
                                </select>
                                <label for="new">New Programme</label>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="reason" placeholder="Reason"  required>
                                <label for="reason">Reason</label>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Demote student</button>
                    </div>
                </form>
            </div><!-- end card body -->
        </div><!-- end card -->
    </div>
    <!--end col-->
    
</div>
<!--end row-->
@endif

@endsection

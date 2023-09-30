@extends('staff.layout.dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Department</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Department</li>
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
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-3">Staffs</p>
                                <div class="d-flex align-items-center mb-3">
                                    <h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value" data-target="{{ $department->staffs->count() }}">0</span></h4>
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
                                <p class="text-uppercase fw-medium text-muted mb-3">Programmes</p>
                                <div class="d-flex align-items-center mb-3">
                                    <h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value" data-target="{{ $department->programmes->count() }}">0</span></h4>
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
                        <h4 class="card-title mb-0 flex-grow-1">Department Overview - {{ $department->name }}</h4>
                    </div><!-- end card header -->

                    <div class="card-header p-0 border-0 bg-soft-light">
                        <div class="row g-0 text-center">
                            <div class="col-6 col-sm-3">
                                <div class="p-3 border border-dashed border-start-0">

                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-6 col-sm-3">
                                <div class="p-3 border border-dashed border-start-0">

                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-6 col-sm-3">
                                <div class="p-3 border border-dashed border-start-0">

                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-6 col-sm-3">
                                <div class="p-3 border border-dashed border-start-0 border-end-0">

                                </div>
                            </div>
                            <!--end col-->
                        </div>
                    </div><!-- end card header -->
                    <div class="card-body">
                        <div class="align-items-center d-flex">
                            <p class="mb-0 flex-grow-1">Kindly Appoint Level Adviser for levels </p>
                            <div class="flex-shrink-0">
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addAdviser" class="btn btn-success">Assign Level Adviser</a>
                            </div>
                        </div>
                       
                        <div class="align-items-center d-flex border-top border-top-dashed mt-3 pt-3">
                            <p class="mb-0 flex-grow-1">Kindly Appoint Exam Officer</p>
                            <div class="flex-shrink-0">
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addExamOfficer" class="btn btn-primary">Assign Exam Officer</a>
                            </div>
                        </div>
                        
                        <div class="align-items-center d-flex border-top border-top-dashed mt-3 pt-3">
                            <p class="mb-0 flex-grow-1">Get Students</p>
                            <div class="flex-shrink-0">
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#getStudents" class="btn btn-primary">Get Students</a>
                            </div>
                        </div>

                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end col -->
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header border-0">
                        <h4 class="card-title mb-0">Programmes</h4>
                    </div><!-- end cardheader -->
                    <div class="card-body pt-0">

                        <h6 class="text-uppercase fw-semibold mt-4 mb-3 text-muted">Available Programmes</h6>
                        @foreach($department->programmes as $programme)
                        <div class="mini-stats-wid d-flex align-items-center mt-3">
                            <div class="flex-shrink-0 avatar-sm">
                                <span class="mini-stat-icon avatar-title rounded-circle text-primary bg-soft-primary fs-4">
                                    <i class="mdi mdi-file-certificate"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">{{ $programme->name }}</h6>
                                <p class="fs-12 mb-0 text-muted">{{ $programme->programmeCategory->category }} Programme</p>
                            </div>
                            <div class="flex-shrink-0">
                                <a href="{{url('/staff/programme/'.$programme->slug)}}" class="btn btn-primary"><i class= "mdi mdi-folder-eye"></i> View</a>
                            </div>
                        </div><!-- end -->
                        <br>
                        @endforeach

                        <div class="card-header p-0 border-0 bg-soft-light">
                            <div class="row g-0 text-center">
                                <div class="col-12 col-sm-12">
                                    <div class="p-3 border border-dashed border-start-0">

                                    </div>
                                </div>
                            </div>
                        </div><!-- end card header -->

                    </div><!-- end cardbody -->
                </div><!-- end card -->
            </div>
        </div><!-- end row -->
    </div><!-- end col -->
    <div class="col-xxl-4">
        <div class="card card-height-100">
            <div class="card-header border-0">
                <h4 class="card-title mb-0">HOD's Profile</h4>
            </div><!-- end cardheader -->
            @if(!empty($department->hod))
            <div class="card-body pt-0">
                <img class="card-img-top img-fluid" src="{{ $department->hod->image }}" alt="Card image cap">
                <div class="card-body">
                    <p class="card-text text-center"><strong>{{ $department->hod->lastname.' '. $department->hod->othernames }}</strong> <br> HOD, {{ $department->name }}</p>
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
    </div><!-- end col -->
</div><!-- end row -->

@if(!empty($students))
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Students </h4>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Name</th>
                            <th scope="col">Matric Number</th>
                            <th scope="col">Level</th>
                            <th scope="col">Programme</th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone Number</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $student->applicant->lastname .' '. $student->applicant->othernames }}</td>
                            <td>{{ $student->matric_number }}</td>
                            <td>{{ $student->academicLevel->level }} Level</td>
                            <td>{{ $student->programme->name }}</td>
                            <td>{{ $student->email }} </td>
                            <td>{{ $student->applicant->phone_number }} </td>
                            <td>
                                <a href="{{ url('staff/studentProfile/'.$student->slug) }}" class="btn btn-primary m-1"><i class= "ri-user-6-fill"></i> View Student</a>
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
@endif

<div class="row">
    <div class="col-lg-5">
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
                                        <h5 class="fs-13 mb-0">{{ $staff->lastname.' '.$staff->othernames }}</h5>
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

    <div class="col-lg-4">
        <div class="card card-height-100">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Level Advisers</h4>
            </div><!-- end card header -->

            <div class="card-body">

                <div class="table-responsive p-3">
                    <table id="buttons-datatables2" class="table table-borderless table-nowrap align-middle mb-3">
                        <thead class="table-light text-muted">
                            <tr>
                                <th scope="col">Staff</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($department->programmes as $programmes)
                                @foreach($programmes->academicAdvisers as $academicAdviser)
                                <tr>
                                    <td class="d-flex">
                                        <img src="{{ $academicAdviser->staff->image }}" alt="" class="avatar-xs rounded-3 shadow me-2">
                                        <div>
                                            <h5 class="fs-13 mb-0">{{ $academicAdviser->staff->title.' '.$academicAdviser->staff->lastname.' '.$academicAdviser->staff->lastname.' '.$academicAdviser->staff->othernames }}</h5>
                                            <p class="fs-12 mb-0 text-muted"><strong>Programme:</strong> {{ $programmes->name }}</p>
                                            <p class="fs-12 mb-0 text-muted"><strong>Level:</strong> {{ $academicAdviser->level->level }} Level</p>
                                        </div>
                                    </td>
                                </tr><!-- end tr -->
                                @endforeach
                            @endforeach
                        </tbody><!-- end tbody -->
                    </table><!-- end table -->
                </div>
            </div><!-- end cardbody -->
        </div><!-- end card -->
    </div><!-- end col -->

    <div class="col-lg-3">
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

    <div class="col-lg-6">
        <div class="card card-height-100">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Fresh Student ({{ $pageGlobalData->sessionSetting->academic_session }}) - {{$department->students->where('level_id', 1)->count()}} Student(s)</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div class="table-responsive p-3">
                    <table id="buttons-datatables3" class="table table-borderless table-nowrap align-middle mb-0">
                        <thead class="table-light text-muted">
                            <tr>
                                <th scope="col">Student</th>
                                <th scope="col">Programme</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($department->students->where('level_id', 1) as $student)
                            <tr>
                                <td class="d-flex">
                                    <img src="{{ env('APP_URL').'/'.$student->image }}" alt="" class="avatar-xs rounded-3 shadow me-2">
                                    <div>
                                        <h5 class="fs-13 mb-0">{{ $student->applicant->lastname.' '.$student->applicant->othernames }}</h5>
                                        <p class="fs-12 mb-0 text-muted"><strong>Dept:</strong> {{ $student->programme->department->name }}</p>
                                    </div>
                                </td>

                                <td style="width:5%;">
                                    <p class="fs-12 mb-0 text-muted">{{ $student->programme->name }}</p>
                                </td>
                            </tr><!-- end tr -->
                            @endforeach
                        </tbody><!-- end tbody -->
                    </table><!-- end table -->
                </div>

            </div><!-- end cardbody -->
        </div><!-- end card -->
    </div><!-- end col -->

    <div class="col-lg-6">
        <div class="card card-height-100">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Department Capacity</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div class="table-responsive p-3">
                    <table id="buttons-datatables4" class="table table-borderless table-nowrap align-middle mb-0">
                        <thead class="table-light text-muted">
                            <tr>
                                <th scope="col">Programme</th>
                                <th scope="col">Student Number</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($department->programmes as $programme)
                            <tr>
                                <td class="d-flex">
                                    <p class="fs-12 mb-0 text-muted">{{ $programme->name }}</p>
                                </td>

                                <td style="width:5%;">
                                    <p class="fs-12 mb-0 text-muted">{{ $programme->students->count() }}</p>
                                </td>
                            </tr><!-- end tr -->
                            @endforeach
                        </tbody><!-- end tbody -->
                    </table><!-- end table -->
                </div>

            </div><!-- end cardbody -->
        </div><!-- end card -->
    </div><!-- end col -->
</div><!-- end row -->


<div id="addAdviser" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Add Level Advisers</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body border-top border-top-dashed">

                <form action="{{ url('/staff/addAdviser') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="category" class="form-label">Select Programme</label>
                        <select class="form-select" aria-label="category" name="programme_id">
                            <option selected value= "">Select Programme </option>
                            @foreach($department->programmes as $programme)
                            <option value="{{ $programme->id }}">{{ $programme->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="level" class="form-label">Select Level</label>
                        <select class="form-select" aria-label="level" name="level_id">
                            <option selected value= "">Select Level </option>
                            @foreach($levels as $acadlevel)
                            <option value="{{ $acadlevel->id }}">{{ $acadlevel->level }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="staff_id" class="form-label">Select Staff</label>
                        <select class="form-select" aria-label="staff_id" name="staff_id">
                            <option selected value= "">Select Staff </option>
                            @foreach($department->staffs as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->lastname.' '.$staff->othernames }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="text-end border-top border-top-dashed p-3 p-3">
                        <button type="submit" id="submit-button" class="btn btn-primary">Add Adviser</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="getStudents" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Get Students</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body border-top border-top-dashed">

                <form action="{{ url('/staff/getStudents') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" name="faculty_id" value="{{ $department->faculty_id }}">
                    <input type="hidden" name="department_id" value="{{ $department->id }}">
                    <div class="mb-3">
                        <label for="category" class="form-label">Select Programme</label>
                        <select class="form-select" aria-label="category" name="programme_id">
                            <option selected value= "">Select Programme </option>
                            @foreach($department->programmes as $programme)
                            <option value="{{ $programme->id }}">{{ $programme->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="level" class="form-label">Select Level</label>
                        <select class="form-select" aria-label="level" name="level_id">
                            <option selected value= "">Select Level </option>
                            @foreach($levels as $acadlevel)
                            <option value="{{ $acadlevel->id }}">{{ $acadlevel->level }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="text-end border-top border-top-dashed p-3 p-3">
                        <button type="submit" id="submit-button" class="btn btn-primary">Get Students</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="addExamOfficer" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Add Exam Officer</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body border-top border-top-dashed">

                <form action="{{ url('/staff/addExamOfficer') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name='department_id' value="{{ $department->id }}">

                    <div class="mb-3">
                        <label for="staff_id" class="form-label">Select Staff</label>
                        <select class="form-select" aria-label="staff_id" name="staff_id">
                            <option selected value= "">Select Staff </option>
                            @foreach($department->staffs as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->lastname.' '.$staff->othernames }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="text-end border-top border-top-dashed p-3 p-3">
                        <button type="submit" id="submit-button" class="btn btn-primary">Add Exam Officer</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@endsection
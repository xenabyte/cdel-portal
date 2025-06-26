@extends('admin.layout.dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Faculty</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Faculty</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row project-wrapper">
    <div class="col-xxl-8">
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
                                    <h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value" data-target="{{$faculty->staffs? $faculty->staffs->count():0 }}">0</span></h4>
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
                                <p class="text-uppercase fw-medium text-muted mb-3">Departments</p>
                                <div class="d-flex align-items-center mb-3">
                                    <h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value" data-target="{{ $faculty->departments?$faculty->departments->count():0 }}">0</span></h4>
                                </div>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div>
            </div><!-- end col -->
        </div><!-- end row -->

        <div class="row">
            <div class="col-xl-5">
                <div class="card">
                    <div class="card-header border-0 align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Faculty Overview - {{ $faculty->name }}</h4>
                    </div><!-- end card header -->

                    <div class="card-header p-0 border-0 bg-soft-light">
                        <div class="row g-0 text-center">
                            <div class="col-6 col-sm-12">
                                <div class="p-3 border border-dashed border-start-0">
                                    <strong>Faculty Code:</strong> {{ $faculty->code }}
                                </div>
                            </div>
                        </div>
                    </div><!-- end card header -->
                    <div class="card-body">
                        <div class="align-items-center d-flex border-top border-top-dashed mt-3 pt-3">
                            <p class="mb-0 flex-grow-1">Edit Faculty</p>
                            <div class="flex-shrink-0">
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#editFaculty" class="btn btn-primary">Edit Faculty</a>
                            </div>
                        </div>

                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end col -->
            <div class="col-xl-7">
                <div class="card">
                    <div class="card-header border-0">
                        <h4 class="card-title mb-0">Departments</h4>
                    </div><!-- end cardheader -->
                    <div class="card-body pt-0">

                        <h6 class="text-uppercase fw-semibold mt-4 mb-3 text-muted">Available Departments</h6>
                        @foreach($faculty->departments as $department)
                        <div class="mini-stats-wid d-flex align-items-center mt-3">
                            <div class="flex-shrink-0 avatar-sm">
                                <span class="mini-stat-icon avatar-title rounded-circle text-primary bg-soft-primary fs-4">
                                    <i class="mdi mdi-certificate"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">{{ $department->name }}</h6>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="flex-shrink-0">
                                    <a href="{{url('/admin/department/'.$department->slug)}}" class="btn btn-primary"> <i class= "mdi mdi-monitor-eye"></i></a>
                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#editDepartment{{$department->id}}" class="btn btn-info"><i class= "mdi mdi-application-edit"></i></a>
                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete{{$department->id}}" class="btn btn-danger"><i class= "mdi mdi-trash-can"></i></a>
                                </div>
                            </div>
                        </div><!-- end -->
                        <div id="editDepartment{{$department->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 overflow-hidden">
                                    <div class="modal-header p-3">
                                        <h4 class="card-title mb-0">Update Department</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>

                                    <div class="modal-body">
                                        <form action="{{ url('/admin/updateDepartment') }}" method="post" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name='department_id' value="{{ $department->id }}">
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Department Name</label>
                                                <input type="text" class="form-control" name="name" id="name" value="{{ $department->name }}">
                                            </div>

                                            <div class="mb-3">
                                                <label for="code" class="form-label">Department Code</label>
                                                <input type="text" class="form-control" name="code" id="code" value="{{ $department->code }}">
                                            </div>

                                            <hr>
                                            <div class="text-end">
                                                <button type="submit" id="submit-button" class="btn btn-primary">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->
                        <div id="delete{{$department->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-body text-center p-5">
                                        <div class="text-end">
                                            <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="mt-2">
                                            <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="hover" style="width:150px;height:150px">
                                            </lord-icon>
                                            <h4 class="mb-3 mt-4">Are you sure you want to delete <br/> {{ $department->name }}?</h4>
                                            <form action="{{ url('/admin/deleteDepartment') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name='department_id' value="{{ $department->id }}">
                                                <hr>
                                                <button type="submit" id="submit-button" class="btn btn-danger w-100">Yes, Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light p-3 justify-content-center">

                                    </div>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->
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
                        <div class="mt-3 text-center">
                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addDepartment" class="btn btn-primary">
                                Add Department</a>
                        </div>
                    </div><!-- end cardbody -->
                </div><!-- end card -->
            </div>
        </div><!-- end row -->
    </div><!-- end col -->
    <div class="col-xxl-4">
        <div class="card">
            <div class="card-header border-0">
                <h4 class="card-title mb-0">Dean's Profile</h4>
            </div><!-- end cardheader -->
            @if(!empty($faculty->dean))
            <div class="card-body pt-0">
                <img class="card-img-top img-fluid" src="{{ $faculty->dean->image }}" alt="Card image cap">
                <div class="card-body">
                    <p class="card-text text-center"><strong>{{ $faculty->dean->lastname.' '.$faculty->dean->othernames }}</strong> <br> Dean, {{ $faculty->name }}</p>
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

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Staff Members</h4>
            </div><!-- end card header -->

            <div class="card-body">

                <div class="table-responsive p-3">
                    <table id="buttons-datatables1" class="table table-borderless table-nowrap align-middle mb-3">
                        <thead class="table-light text-muted">
                            <tr>
                                <th scope="col">Staff</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($faculty->staffs as $staff)
                            <tr>
                                <td class="d-flex">
                                    <img src="{{ $staff->image }}" alt="" class="avatar-xs rounded-3 shadow me-2">
                                    <div>
                                        <h5 class="fs-13 mb-0">{{ ucwords(strtolower($staff->lastname.' '.$staff->othernames)) }}</h5>
                                        <p class="fs-12 mb-0 text-muted">{{ $staff->qualification }}</p>
                                    </div>
                                </td>

                                <td style="width:5%;">

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

<div id="editFaculty" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Edit Faculty</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="{{ url('/admin/updateFaculty') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="faculty_id" value="{{ $faculty->id }}">
                    <div class="mb-3">
                        <label for="name" class="form-label">Faculty Name</label>
                        <input type="text" class="form-control" name="name" id="name" value="{{ $faculty->name }}" disabled readonly>
                    </div>

                    <div class="mb-3">
                        <label for="code" class="form-label">Faculty Code</label>
                        <input type="text" class="form-control" name="code" id="code" value="{{ $faculty->code }}">
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="addDepartment" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Add Department</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="{{ url('/admin/addDepartment') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="faculty_id" value="{{ $faculty->id }}">
                    <div class="mb-3">
                        <label for="name" class="form-label">Department Name</label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Enter Department Name">
                    </div>

                    <div class="mb-3">
                        <label for="code" class="form-label">Department Code</label>
                        <input type="text" class="form-control" name="code" id="code">
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Add Department</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection

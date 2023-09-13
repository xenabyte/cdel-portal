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
                    <div class="mb-3">
                        <label for="reg" class="form-label">Matric Number</label>
                        <input type="text" class="form-control" name="reg_number" id="reg">
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Get student</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@if(!empty($student))
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Student Information</h4>
                <div class="flex-shrink-0">
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#demote">Demote Student</button>
                </div>
            </div><!-- end card header -->
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-6 col-xl-12">
                        <div class="row">
                            <div class="col-md-12">
                                <img class="rounded shadow" width="100%" src="{{asset($student->image)}}" alt="Student Logo">
                                <hr>
                            </div>
                        </div>
                        <p class="card-text"><strong>Student Name: </strong> {{ $student->applicant->lastname .' '. $student->applicant->othernames }} </p>
                        <hr>
                        <p class="card-text"><strong>Programme: </strong> {{ $student->programme->name }} </p>
                        <hr>
                        <p class="card-text"><strong>Student Level: </strong> {{ $student->academicLevel->level }} Level</p>
                        <hr>
                        <p class="card-text"><strong>Academic Session: </strong> {{ $student->academic_session }} Level</p>
                        <hr>
                        <p class="card-text"><strong>Email: </strong> {{ $student->applicant->email }}</p>
                        <hr>
                        <p class="card-text"><strong>Phone Number(s): </strong> {{ $student->applicant->phone_number }}</p>
                    </div><!-- end col -->
                </div>
            </div>
        </div><!-- end card -->
    </div>
</div>
<!-- end row -->

<div id="demote" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Demote Student</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <hr>
            <div class="modal-body">
                <form action="{{ url('/admin/makeDemoteStudent') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $student->id }}">
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
                        <button type="submit" class="btn btn-primary">Demote student</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endif
@endsection

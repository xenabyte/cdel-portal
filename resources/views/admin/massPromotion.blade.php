@extends('admin.layout.dashboard')

@section('content')
 <!-- start page title -->
 <div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Student Promotion</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Student Promotion</li>
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
                <h4 class="card-title mb-0 flex-grow-1">{{ $programmeCategory->category }} Programme (Student Promotion)</h4>
            </div><!-- end card header -->

            @if(!empty($programmes) && $programmes->count() > 0)
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-6 col-xl-12">
                        
                        <table id="fixed-header" class="table table-borderedless dt-responsive nowrap table-striped align-middle" style="width:100%">
                            <thead>
                                <tr>
                                    <th scope="col">Id</th>
                                    <th scope="col">Programmes</th>
                                    <th scope="col">Academic Session</th>
                                    <th scope="col">Category</th>
                                    <th scope="col">Student Strength</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($programmes as $programme)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $programme->name }} </td>
                                    <td>{{ $programme->academic_session }}</td>
                                    <td>{{ $programme->programmeCategory->category }}</td>
                                    <td>{{ $programme->students->count() }} </td>
                                    <td>
                                        <div class="hstack gap-3 fs-15">
                                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#promote{{$programme->id}}" class="btn btn-sm btn-primary">Promote Student</a>
        
                                            <div id="promote{{$programme->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                                <div class="modal-dialog  modal-dialog-scrollable modal-xl modal-dialog-centered">
                                                    <div class="modal-content border-0 overflow-hidden">
                                                        <div class="modal-header p-3">
                                                            <h4 class="card-title mb-0">Promote Student(s)</h4>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                
                                                        <div class="modal-body">
                                                            <form action="{{ url('/admin/promoteStudent') }}" method="post" enctype="multipart/form-data">
                                                                @csrf
                                                                <input name="programme_id" type="hidden" value="{{$programme->id}}">
                                                                <input name="programme_category_id" type="hidden" value="{{$programmeCategory->id}}" >
                                                                <hr>
                                                                <div class="text-end">
                                                                    <button type="submit" id="submit-button" class="btn btn-primary">Promote</button>
                                                                </div>
                                                            </form>

                                                            <hr>
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
                                                                                        <th scope="col">Programme</th>
                                                                                        <th scope="col">Email</th>
                                                                                        <th scope="col">Level</th>
                                                                                        <th scope="col">Phone Number</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @foreach($programme->students as $student)
                                                                                    <tr>
                                                                                        <th scope="row">{{ $loop->iteration }}</th>
                                                                                        <td>{{ $student->applicant->lastname .' '. $student->applicant->othernames }}</td>
                                                                                        <td>{{ $student->programme->name }}</td>
                                                                                        <td>{{ $student->email }} </td>
                                                                                        <td>{{ $student->academicLevel->level }} </td>
                                                                                        <td>{{ $student->applicant->phone_number }} </td>
                                                                                    </tr>
                                                                                    @endforeach
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div><!-- end card -->
                                                                </div>
                                                                <!-- end col -->
                                                            </div>

                                                        </div>
                                                    </div><!-- /.modal-content -->
                                                </div><!-- /.modal-dialog -->
                                            </div><!-- /.modal -->
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div><!-- end col -->
                </div>
            </div>
            @endif
        </div><!-- end card -->
    </div>
</div>
<!-- end row -->
@endsection

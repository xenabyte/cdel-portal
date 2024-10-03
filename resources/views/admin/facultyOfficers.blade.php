@extends('admin.layout.dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Faculty Officers</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Faculties</li>
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
                <h4 class="card-title mb-0 flex-grow-1">Faculties </h4>
                <div class="flex-shrink-0">
                </div>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Name</th>
                            <th scope="col">Faculty Officer</th>
                            <th scope="col"></th>
                       </tr>
                    </thead>
                    <tbody>
                        @foreach($faculties as $faculty)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $faculty->name }}</td>
                            <td>{{ !empty($faculty->facultyOfficer)?$faculty->facultyOfficer->title.' '.$faculty->facultyOfficer->lastname. ' '.$faculty->facultyOfficer->othernames : null }}</td>
                            <td>
                                {{-- <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#assign{{$faculty->id}}" class="btn btn-info">Assign facultyOfficer</a> --}}
                                <form action="{{ url('/admin/assignFacultyOfficerToFaculty') }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name='faculty_id' value="{{ $faculty->id }}">
                                
                                    <div class="input-group" style="display: flex; flex-wrap: nowrap;">
                                        <select class="form-select select2 selectWithSearch" aria-label="staff" name="staff_id" required style="flex-grow: 1;">
                                            <option value="" selected>Select Staff</option>
                                            @foreach($staffMembers as $staffMember)
                                                <option value="{{ $staffMember->id }}">{{ $staffMember->title.' '.$staffMember->lastname.' '.$staffMember->othernames }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-outline-secondary shadow-none" style="white-space: nowrap;">Assign</button>
                                    </div>
                                </form>                                
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
<!-- end row -->

<!-- Modal for Assign facultyOfficer -->
{{-- @foreach($faculties as $faculty)
    <div id="assign{{$faculty->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 overflow-hidden">
                <div class="modal-header p-3">
                    <h4 class="card-title mb-0">Assign Faculty Officer</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form action="{{ url('/admin/assignFacultyOfficerToFaculty') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name='faculty_id' value="{{ $faculty->id }}">
                        
                        <div class="input-group">
                            <select class="form-select select2 selectWithSearch" aria-label="staff" name="staff_id" required>
                                <option value= "" selected>Select Staff</option>
                                @foreach($staffMembers as $staffMember)<option value="{{ $staffMember->id }}">{{ $staffMember->title.' '.$staffMember->lastname.' '.$staffMember->othernames }}</option>@endforeach
                            </select>
                            <button type="submit" class="btn btn-outline-secondary shadow-none" type="button">Assign</button>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div id="editFaculty{{$faculty->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 overflow-hidden">
                <div class="modal-header p-3">
                    <h4 class="card-title mb-0">Update Faculty</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form action="{{ url('/admin/updateFaculty') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name='faculty_id' value="{{ $faculty->id }}">
                        <div class="mb-3">
                            <label for="name" class="form-label">Faculty Name</label>
                            <input type="text" class="form-control" name="name" id="name" value="{{ $faculty->name }}">
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
    <div id="delete{{$faculty->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-5">
                    <div class="text-end">
                        <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="mt-2">
                        <lord-icon src="https://cdn.lordicon.com/wwneckwc.json" trigger="hover" style="width:150px;height:150px">
                        </lord-icon>
                        <h4 class="mb-3 mt-4">Are you sure you want to delete <br/> {{ $faculty->name }}?</h4>
                        <form action="{{ url('/admin/deleteFaculty') }}" method="POST">
                            @csrf
                            <input type="hidden" name='faculty_id' value="{{ $faculty->id }}">
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
@endforeach --}}

@endsection

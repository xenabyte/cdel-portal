@extends('admin.layout.dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Units</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Units</li>
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
                <h4 class="card-title mb-0 flex-grow-1">Units </h4>
                <div class="flex-shrink-0">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUnit">Add Unit</button>
                </div>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Name</th>
                            <th scope="col">Head of Unit</th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($units as $unit)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $unit->name }}</td>
                            <td>{{ !empty($unit->unit_head)?$unit->unit_head->title.' '.$unit->unit_head->lastname. ' '.$unit->unit_head->othernames : null }}</td>
                            <td>
                                <form action="{{ url('/admin/updateUnit') }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name='unit_id' value="{{ $unit->id }}">
                                    
                                    <div class="input-group" style="display: flex; flex-wrap: nowrap;">
                                        <select class="form-select select2 selectWithSearch" aria-label="staff" name="unit_head_id" required>
                                            <option value= "" selected>Select Staff</option>
                                            @foreach($staffMembers as $staffMember)<option value="{{ $staffMember->id }}">{{ $staffMember->title.' '.$staffMember->lastname.' '.$staffMember->othernames }}</option>@endforeach
                                        </select>
                                        <button type="submit" class="btn btn-outline-secondary shadow-none" type="button">Assign</button>
                                    </div>
                                </form>
                            </td>
                            <td>
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#editUnit{{$unit->id}}" class="btn btn-info">Edit</a>
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete{{$unit->id}}" class="btn btn-danger">Delete</a>
                            </td>
                            <div id="editUnit{{$unit->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content border-0 overflow-hidden">
                                        <div class="modal-header p-3">
                                            <h4 class="card-title mb-0">Update Unit</h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body">
                                            <form action="{{ url('/admin/updateUnit') }}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name='unit_id' value="{{ $unit->id }}">
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Unit Name</label>
                                                    <input type="text" class="form-control" name="name" id="name" value="{{ $unit->name }}">
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
                            <div id="delete{{$unit->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-body text-center p-5">
                                            <div class="text-end">
                                                <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="mt-2">
                                                <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="hover" style="width:150px;height:150px">
                                                </lord-icon>
                                                <h4 class="mb-3 mt-4">Are you sure you want to delete <br/> {{ $unit->name }}?</h4>
                                                <form action="{{ url('/admin/deleteUnit') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name='unit_id' value="{{ $unit->id }}">
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

<div id="addUnit" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Add Unit</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="{{ url('/admin/addUnit') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Unit Name</label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Enter Unit Name" autofocus>
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Add Unit</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@endsection

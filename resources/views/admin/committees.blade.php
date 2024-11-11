@extends('admin.layout.dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Committees</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Committees</li>
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
                <h4 class="card-title mb-0 flex-grow-1">Committees </h4>
                <div class="flex-shrink-0">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCommittee">Add Committee</button>
                </div>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Name</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($committees as $committee)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $committee->name }}</td>
                            <td>
                                <a href="{{ url('admin/committee/'.$committee->slug) }}" class="btn btn-primary m-1"><i class= "mdi mdi-database-eye"></i> View Committee</a>
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#editCommittee{{$committee->id}}" class="btn btn-info">Edit</a>
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete{{$committee->id}}" class="btn btn-danger">Delete</a>
                            </td>
                            <div id="editCommittee{{$committee->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content border-0 overflow-hidden">
                                        <div class="modal-header p-3">
                                            <h4 class="card-title mb-0">Update Committee</h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body">
                                            <form action="{{ url('/admin/updateCommittee') }}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name='committee_id' value="{{ $committee->id }}">
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Committee Name</label>
                                                    <input type="text" class="form-control" name="name" id="name" value="{{ $committee->name }}">
                                                </div>

                                                <div class="mb-3">
                                                    <label for="duties" class="form-label">Committee Duties</label>
                                                    <textarea class="form-control ckeditor" name="duties" id="duties">{!! $committee->duties !!}</textarea>
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
                            <div id="delete{{$committee->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-body text-center p-5">
                                            <div class="text-end">
                                                <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="mt-2">
                                                <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="hover" style="width:150px;height:150px">
                                                </lord-icon>
                                                <h4 class="mb-3 mt-4">Are you sure you want to delete <br/> {{ $committee->name }}?</h4>
                                                <form action="{{ url('/admin/deleteCommittee') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name='committee_id' value="{{ $committee->id }}">
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

<div id="addCommittee" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Add Committee</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="{{ url('/admin/addCommittee') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Committee Name</label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Enter Committee Name" autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="duties" class="form-label">Committee Duties</label>
                        <textarea class="form-control ckeditor" name="duties" id="duties" placeholder="Enter Committee Duties"></textarea>
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Add Committee</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@endsection

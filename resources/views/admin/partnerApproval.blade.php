@extends('admin.layout.dashboard')

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Partners</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Partners-</li>
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
                <h4 class="card-title mb-0 flex-grow-1">Partners </h4>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone Number</th>
                            <th scope="col">Date Joined</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($partners as $partner)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $partner->name }}</td>
                            <td>{{ $partner->email }} </td>
                            <td>{{ $partner->phone_number }} </td>
                            <td>{{ $partner->created_at }} </td>
                            <td>
                                <div class="hstack gap-3 fs-15">
                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete{{$partner->id}}" class="link-danger"><i class="ri-delete-bin-5-line"></i></a>
                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit{{$partner->id}}" class="link-primary"><i class="ri-edit-circle-fill"></i></a>

                                    <div id="delete{{$partner->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-body text-center p-5">
                                                    <div class="text-end">
                                                        <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="mt-2">
                                                        <lord-icon src="https://cdn.lordicon.com/wwneckwc.json" trigger="hover" style="width:150px;height:150px">
                                                        </lord-icon>
                                                        <h4 class="mb-3 mt-4">Are you sure you want to delete <br/> {{ $partner->level }}?</h4>
                                                        <form action="{{ url('/admin/deletepartner') }}" method="POST">
                                                            @csrf
                                                            <input name="level_id" type="hidden" value="{{$partner->id}}">
                                                            <hr>
                                                            <button type="submit" class="btn btn-danger w-100">Yes, Delete</button>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light p-3 justify-content-center">

                                                </div>
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div><!-- /.modal -->

                                    <div id="edit{{$partner->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 overflow-hidden">
                                                <div class="modal-header p-3">
                                                    <h4 class="card-title mb-0">Edit Level</h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                        
                                                <div class="modal-body">
                                                    <form action="{{ url('/admin/updatepartner') }}" method="post" enctype="multipart/form-data">
                                                        @csrf

                                                        <input name="level_id" type="hidden" value="{{$partner->id}}">
                        
                                                        <div class="mb-3">
                                                            <label for="level" class="form-label">Level</label>
                                                            <input type="text" class="form-control" name="level"   id="level" value="{{ $partner->level }}">
                                                        </div>

                                                        <hr>
                                                        <div class="text-end">
                                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                                        </div>
                                                    </form>
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
            </div>
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end row -


@endsection
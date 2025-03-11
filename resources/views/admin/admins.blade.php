@extends('admin.layout.dashboard')

@section('content')

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">{{ Auth::guard('admin')->user()->name }}'s Dashboard</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                        <li class="breadcrumb-item active">Admin Records</li>
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
                    <h4 class="card-title mb-0 flex-grow-1">Administrator</h4>
                    <div class="flex-shrink-0">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAdmin">Add Admin</button>
                    </div>
                </div><!-- end card header -->
                @if(!empty($admins))
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-sm-6 col-xl-12">
                            
                            <table id="fixed-header" class="table table-borderedless table-responsive nowrap table-striped align-middle" style="width:100%">
                                <thead>
                                    <tr>
                                        <th scope="col">Id</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Email</th>
                                        <th scope="col"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($admins as $admin)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td>{{ $admin->name }} </td>
                                        <td>{{ $admin->email }}</td>
                                        <td>
                                            <div class="hstack gap-3 fs-15">
                                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#deleteAdmin{{$admin->id}}" class="link-danger"><i class="ri-delete-bin-5-line"></i></a>
                                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#editAdmin{{$admin->id}}" class="link-primary"><i class="ri-edit-circle-fill"></i></a>

                                                <div id="deleteAdmin{{$admin->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-body text-center p-5">
                                                                <div class="text-end">
                                                                    <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="mt-2">
                                                                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="hover" style="width:150px;height:150px">
                                                                    </lord-icon>
                                                                    <h4 class="mb-3 mt-4">Are you sure you want to delete <br/> {{ $admin->name }}?</h4>
                                                                    <form action="{{ url('/admin/deleteAdmin') }}" method="POST">
                                                                        @csrf
                                                                        <input name="admin_id" type="hidden" value="{{$admin->id}}">
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

                                                <div id="editAdmin{{$admin->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content border-0 overflow-hidden">
                                                            <div class="modal-header p-3">
                                                                <h4 class="card-title mb-0">Update Adminstrator</h4>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                    
                                                            <div class="modal-body">
                                                                <hr>
                                                                <form action="{{ url('/admin/updateAdmin') }}" method="post" enctype="multipart/form-data">
                                                                    @csrf

                                                                    <input name="admin_id" type="hidden" value="{{$admin->id}}">
                                    
                                                                    <div class="mb-3">
                                                                        <label for="name" class="form-label">Name</label>
                                                                        <input type="text" class="form-control" name="name" id="name" value="{{ $admin->name }}">
                                                                    </div>
                                    

                                                                    <div class="mb-3">
                                                                        <label for="email" class="form-label">Email</label>
                                                                        <input type="text" class="form-control" name="email" id="email"  value="{{ $admin->email }}">
                                                                    </div>
                                    
                                                                    <div class="mb-3">
                                                                        <label for="password" class="form-label">Password</label>
                                                                        <input type="text" class="form-control" name="password" id="password">
                                                                    </div>
                                    
                                                                    <hr>
                                                                    <div class="text-end">
                                                                        <button type="submit" class="btn btn-primary">Update Record</button>
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
                        </div><!-- end col -->
                    </div>
                </div>
                @endif
            </div><!-- end card -->
        </div>
    </div>
    <!-- end row -->

    <div id="addAdmin" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
        <!-- Fullscreen Modals -->
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 overflow-hidden">
                <div class="modal-header p-3">
                    <h4 class="card-title mb-0">Add Admin</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form action="{{ url('/admin/addAdmin') }}" method="post" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" id="name">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" class="form-control" name="email" id="email">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="text" class="form-control" name="password" id="password">
                        </div>

                        <hr>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Add Admin</button>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection

@extends('staff.layout.dashboard')

@section('content')
 <!-- start page title -->
 <div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Hostels</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Hostels</li>
                </ol>
            </div>

        </div>
    </div>

    <div class="row">

        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Hostels</h4>
                    <div class="flex-shrink-0">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add">Add Hostel</button>
                    </div>
                </div><!-- end card header -->

                @if(!empty($hostels) && $hostels->count() > 0)
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-sm-6 col-xl-12">
                            
                            <table id="fixed-header" class="table table-borderedless table-responsive nowrap table-striped align-middle" style="width:100%">
                                <thead>
                                    <tr>
                                        <th scope="col">Id</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Campus</th>
                                        <th scope="col">Gender</th>
                                        <th scope="col"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($hostels as $hostel)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td>{{ $hostel->name }} </td>
                                        <td>{{ $hostel->campus }} </td>
                                        <td>{{ $hostel->gender }} </td>
                                        <td>
                                            <div class="hstack gap-3 fs-15">
                                                <a href="{{ url('staff/viewHostel/'.$hostel->slug) }}" class="link-secondary m-1"><i class= "ri-eye-fill"></i></a>
                                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit{{$hostel->id}}" class="link-primary"><i class="ri-edit-circle-fill"></i></a>
                                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete{{$hostel->id}}" class="link-danger"><i class="ri-delete-bin-5-line"></i></a>

                                                <div id="delete{{$hostel->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-body text-center p-5">
                                                                <div class="text-end">
                                                                    <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="mt-2">
                                                                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="hover" style="width:150px;height:150px">
                                                                    </lord-icon>
                                                                    <h4 class="mb-3 mt-4">Are you sure you want to delete <br/> {{ $hostel->name }} at {{ $hostel->campus }} Campus?</h4>
                                                                    <form action="{{ url('/staff/deleteHostel') }}" method="POST">
                                                                        @csrf
                                                                        <input name="hostel_id" type="hidden" value="{{$hostel->id}}">
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

                                                <div id="edit{{$hostel->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content border-0 overflow-hidden">
                                                            <div class="modal-header p-3">
                                                                <h4 class="card-title mb-0">Edit Hostel</h4>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                    
                                                            <div class="modal-body">
                                                                <form action="{{ url('/staff/updateHostel') }}" method="post" enctype="multipart/form-data">
                                                                    @csrf

                                                                    <input name="hostel_id" type="hidden" value="{{$hostel->id}}">

                                                                    <div class="mb-3">
                                                                        <label for="name" class="form-label">Name</label>
                                                                        <input type="text" class="form-control" name="name" id="name" value="{{ $hostel->name }}">
                                                                    </div>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label for="role" class="form-label">Select Gender</label>
                                                                        <select class="form-select" aria-label="role" name="gender" required>
                                                                            <option {{ $hostel->gender =='Male'?'selected':'' }} value="Male">Male</option>
                                                                            <option {{ $hostel->gender =='Female'?'selected':'' }} value="Female">Female</option>
                                                                        </select>
                                                                    </div>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label for="role" class="form-label">Select Campus</label>
                                                                        <select class="form-select" aria-label="role" name="campus" required>
                                                                            <option {{ $hostel->campus =='West'?'selected':'' }} value="West">West Campus</option>
                                                                            <option {{ $hostel->campus =='East'?'selected':'' }} value="East">East Campus</option>
                                                                        </select>
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
</div>
<!-- end page title -->

<div id="add" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
    <!-- Fullscreen Modals -->
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Add Hostel</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="{{ url('/staff/addHostel') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" id="name">
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Select Gender</label>
                        <select class="form-select" aria-label="role" name="gender" required>
                            <option selected value= "">Select Option </option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="campus" class="form-label">Select Campus</label>
                        <select class="form-select" aria-label="role" name="campus" required>
                            <option selected value= "">Select Option </option>
                            <option value="West">West Campus</option>
                            <option value="East">East Campus</option>
                        </select>
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Create a Hostel</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection
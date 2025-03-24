@extends('admin.layout.dashboard')

@section('content')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Study Centers</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Study Centers</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<!-- end page title -->
<div class="row">

    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Study Centers</h4>
                <div class="flex-shrink-0">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add">Add Study Center</button>
                </div>
            </div><!-- end card header -->

            @if(!empty($studyCenters) && $studyCenters->count() > 0)
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-6 col-xl-12">
                        
                        <table id="fixed-header" class="table table-borderedless table-responsive nowrap table-striped align-middle" style="width:100%">
                            <thead>
                                <tr>
                                    <th scope="col">Id</th>
                                    <th scope="col">Center Name</th>
                                    <th scope="col">Center Admin Name</th>
                                    <th scope="col">Center Email</th>
                                    <th scope="col">Center Phone Number</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($studyCenters as $studyCenter)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $studyCenter->name }} </td>
                                    <td>{{ $studyCenter->center_name }} </td>
                                    <td>{{ $studyCenter->email }} </td>
                                    <td>{{ $studyCenter->phone_number }} </td>
                                    <td>
                                        <div class="hstack gap-3 fs-15">
                                            <a href="{{ url('admin/studyCenter/'.$studyCenter->slug)}}" class="link-primary"><i class="ri-eye-fill"></i></a>
                                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit{{$studyCenter->id}}" class="link-info"><i class="ri-edit-circle-fill"></i></a>
                                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete{{$studyCenter->id}}" class="link-danger"><i class="ri-delete-bin-5-line"></i></a>

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


@foreach ($studyCenters as $studyCenter)
<div id="delete{{$studyCenter->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="text-end">
                    <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="mt-2">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="hover" style="width:150px;height:150px">
                    </lord-icon>
                    <h4 class="mb-3 mt-4">Are you sure you want to delete <br/> {{ $studyCenter->name }}?</h4>
                    <form action="{{ url('/admin/deleteStudyCenter') }}" method="POST">
                        @csrf
                        <input name="center_id" type="hidden" value="{{$studyCenter->id}}">
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

<div id="edit{{$studyCenter->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Edit Study Center</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body border-top border-top-dashed">
                <form action="{{ url('/admin/updateStudyCenter') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" name="center_id" value="{{ $studyCenter->id }}">

                    <div class="mb-3">
                        <label for="center_name" class="form-label">Center Name</label>
                        <input type="text" class="form-control"  name="center_name" id="center_name" value="{{ $studyCenter->center_name }}">
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Center Admin Name</label>
                        <input type="text" class="form-control"  name="name" id="name" value="{{ $studyCenter->name }}">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Center Email </label>
                        <input type="email" class="form-control"  name="email" id="email" value="{{ $studyCenter->email }}">
                    </div>

                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Center Phone Number </label>
                        <input type="text" class="form-control"  name="phone_number" id="phone_number" value="{{ $studyCenter->phone_number }}">
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Center Password </label>
                        <input type="password" class="form-control"  name="password" id="password">
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea name="address" class="form-control" id="address" cols="30" rows="10">{{ $studyCenter->address }}</textarea>
                    </div>

                    <div class="text-end border-top border-top-dashed pt-2">
                        <button type="submit" id="submit-button" class="btn btn-primary">Update Changes</button>
                    </div>
                </form>
            </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endforeach

<div id="add" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
    <!-- Fullscreen Modals -->
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Add Study Center</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body border-top border-top-dashed">
                <form action="{{ url('/admin/addStudyCenter') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="center_name" class="form-label">Center Name</label>
                        <input type="text" class="form-control"  name="center_name" id="center_name">
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Center Admin Name</label>
                        <input type="text" class="form-control"  name="name" id="name">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Center Email </label>
                        <input type="email" class="form-control"  name="email" id="email">
                    </div>

                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Center Phone Number </label>
                        <input type="text" class="form-control"  name="phone_number" id="phone_number">
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea name="address" class="form-control" id="description" cols="30" rows="10"></textarea>
                    </div>

                    <div class="text-end border-top border-top-dashed pt-2">
                        <button type="submit" id="submit-button" class="btn btn-primary">Create Center</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection
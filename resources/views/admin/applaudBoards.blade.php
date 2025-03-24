@extends('admin.layout.dashboard')

@section('content')
 <!-- start page title -->
 <div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Applaud Boards</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Applaud Boards</li>
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
                <h4 class="card-title mb-0 flex-grow-1">Applaud Boards</h4>
                <div class="flex-shrink-0">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add">Add Applaud Board</button>
                </div>
            </div><!-- end card header -->

            @if(!empty($applaudBoards) && $applaudBoards->count() > 0)
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-6 col-xl-12">
                        
                        <table id="fixed-header" class="table table-borderedless table-responsive nowrap table-striped align-middle" style="width:100%">
                            <thead>
                                <tr>
                                    <th scope="col">Id</th>
                                    <th scope="col">Title</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($applaudBoards as $applaudBoard)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $applaudBoard->title }} </td>
                                    <td>
                                        <div class="hstack gap-3 fs-15">
                                            <a href="{{ url('admin/applaudBoard/'.$applaudBoard->slug)}}" class="link-primary"><i class="ri-eye-fill"></i></a>
                                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit{{$applaudBoard->id}}" class="link-info"><i class="ri-edit-circle-fill"></i></a>
                                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete{{$applaudBoard->id}}" class="link-danger"><i class="ri-delete-bin-5-line"></i></a>


                                           
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


@foreach ($applaudBoards as $applaudBoard)
<div id="delete{{$applaudBoard->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="text-end">
                    <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="mt-2">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="hover" style="width:150px;height:150px">
                    </lord-icon>
                    <h4 class="mb-3 mt-4">Are you sure you want to delete <br/> {{ $applaudBoard->title }}?</h4>
                    <form action="{{ url('/admin/deleteBoard') }}" method="POST">
                        @csrf
                        <input name="board_id" type="hidden" value="{{$applaudBoard->id}}">
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

<div id="edit{{$applaudBoard->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Edit Applaud Board</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="{{ url('/admin/updateBoard') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <input name="board_id" type="hidden" value="{{$applaudBoard->id}}">

                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control"  name="title" id="title" value="{{$applaudBoard->title}}">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Celebrant Email</label>
                        <input type="email" class="form-control"  name="email" id="email" value="{{$applaudBoard->email}}">
                    </div>


                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" class="form-control" id="description" cols="30" rows="10">{!! $applaudBoard->description !!}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" class="form-control"  name="image" id="image">
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
@endforeach

<div id="add" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
    <!-- Fullscreen Modals -->
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Add Applaud Board</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="{{ url('/admin/createBoard') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control"  name="title" id="title">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Celebrant Email</label>
                        <input type="email" class="form-control"  name="email" id="email">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" class="form-control" id="description" cols="30" rows="10"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" class="form-control"  name="image" id="image">
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Create Applaud Board</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection
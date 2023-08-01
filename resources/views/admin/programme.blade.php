@extends('admin.layout.dashboard')

@section('content')


<div class="row project-wrapper">
    <div class="col-xxl-8">
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header border-0 align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Programme Overview - {{ Str::title(strtolower($programme->name)) }}</h4>
                        <div class="card-header align-items-center d-flex">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProgramme">Edit Programme</button>
                        </div><!-- end card header -->
                    </div><!-- end card header -->

                    <div class="card-header p-0 border-0 bg-soft-light">
                        <div class="row g-0 text-center">
                            <div class="col-6 col-sm-3">
                                <div class="p-3 border border-dashed border-start-0">

                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-6 col-sm-3">
                                <div class="p-3 border border-dashed border-start-0">

                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-6 col-sm-3">
                                <div class="p-3 border border-dashed border-start-0">

                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-6 col-sm-3">
                                <div class="p-3 border border-dashed border-start-0 border-end-0">

                                </div>
                            </div>
                            <!--end col-->
                        </div>
                    </div><!-- end card header -->
                    <div class="card-body">
                        <p><strong>Programme Name: </strong> {{ Str::title(strtolower($programme->name)) }} </p>
                        <p><strong>Programme Duration: </strong> {{ $programme->duration }} Years </p>
                        <p><strong>Programme Max Duration: </strong> {{ $programme->max_duration }} Years </p>
                        <p><strong>Programme Code: </strong> {{ $programme->code }} </p>
                        <p><strong>Programme Code Number: </strong> {{ $programme->code_number }} </p>
                        <p><strong>Programme Last Matric Number: </strong> {{ $programme->matric_last_number }} </p>
                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end col -->
        </div><!-- end row -->
    </div><!-- end col -->
    <div class="col-xxl-4">
        <div class="card">
            <div class="card-header border-0">
                <h4 class="card-title mb-0">Handbook</h4>
            </div><!-- end cardheader -->
            <div class="card-body pt-0">
                @if(!empty($programme->handbook->handbook))
                <div class="avatar-lg">
                    <div class="avatar-title bg-soft-danger text-danger rounded fs-20 shadow">
                        <a href="{{ asset($programme->handbook->handbook) }}" target="_blank"><i class="ri-file-pdf-fill"></i></a>
                    </div>
                </div>
                @endif
                <form action="{{ url('/admin/uploadHandbook') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name='programme_id' value="{{ $programme->id }}">

                    <div class="mb-3">
                        <label for="image" class="form-label"></label>
                        <input type="file" required class="form-control" name='handbook' id="image">
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Upload Handbook</button>
                    </div>
                </form>
            </div><!-- end cardbody -->
        </div><!-- end card -->
    </div><!-- end col -->
</div><!-- end row -->


<div class="row">
    <div class="col-xxl-12 col-lg-12">
        <div class="card card-height-100">
            
            <div class="card-body mb-3">
                @if(!empty($programme->courses))
                @include('admin.courses.year1')
                <hr>
                @include('admin.courses.year2')
                <hr>
                @include('admin.courses.year3')
                <hr>
                @include('admin.courses.year4')
                <hr>
                @include('admin.courses.year5')
                <hr>
                @include('admin.courses.year6')

            @endif
            </div><!-- end cardbody -->
        </div><!-- end card -->
    </div><!-- end col -->
</div><!-- end row -->

<div id="editProgramme" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Add Programme</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="{{ url('/admin/saveProgramme') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="programme_id" value="{{ $programme->id }}">
                    <div class="mb-3">
                        <label for="name" class="form-label">Programme Name</label>
                        <input type="text" class="form-control" name="name" id="name" value="{{ $programme->name }}" disabled readonly>
                    </div>

                    <div class="mb-3">
                        <label for="code_number" class="form-label">Programme Code Number</label>
                        <input type="text" class="form-control" name="code_number" id="code_number" value="{{ $programme->code_number }}">
                    </div>

                    <div class="mb-3">
                        <label for="code" class="form-label">Programme Code</label>
                        <input type="text" class="form-control" name="code" id="code" value="{{ $programme->code }}">
                    </div>

                    <div class="mb-3">
                        <label for="matric_last_number" class="form-label">Programme Last Matric Number</label>
                        <input type="number" class="form-control" name="matric_last_number" id="matric_last_number" value="{{ $programme->matric_last_number }}">
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

@endsection
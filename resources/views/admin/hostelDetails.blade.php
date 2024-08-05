@extends('admin.layout.dashboard')

@section('content')


<div class="row project-wrapper">
    <div class="col-xxl-8">
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header border-0 align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Hostel Overview - {{ Str::title(strtolower($hostel->name)) }}</h4>
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
                        <p><strong>Hostel Name: </strong> {{ Str::title(strtolower($hostel->name)) }} </p>
                        <p><strong>Hostel Campus: </strong> {{ $hostel->campus }} Campus </p>
                        <p><strong>Hostel Gender: </strong> {{ $hostel->gender }} </p>
                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end col -->
        </div><!-- end row -->
    </div><!-- end col -->
    <div class="col-xxl-4">
        <div class="card">
            <div class="card-header border-0">
                <h4 class="card-title mb-0">Add Rooms</h4>
            </div><!-- end cardheader -->
            <div class="card-body pt-0">
                <form action="{{ url('/admin/addRoom') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name='hostel_id' value="{{ $hostel->id }}">

                    <div class="mb-3">
                        <label for="number" class="form-label">Room Name/Number</label>
                        <input type="text" class="form-control" name="number" id="number">
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Select Room Type</label>
                        <select class="form-select" aria-label="role" name="type_id" required>
                            @foreach($roomTypes as $roomType)<option value="{{ $roomType->id }}">{{ $roomType->name.' - '. $roomType->capacity.' Bed Space(s) - N'. number_format($roomType->amount/100, 2) }}</option>@endforeach
                        </select>
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Add Room</button>
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
               
            </div><!-- end cardbody -->
        </div><!-- end card -->
    </div><!-- end col -->
</div><!-- end row -->

<div id="editHostel" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Edit Hostel</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="{{ url('/admin/saveHostel') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="hostel_id" value="{{ $hostel->id }}">
                    <div class="mb-3">
                        <label for="name" class="form-label">Hostel Name</label>
                        <input type="text" class="form-control" name="name" id="name" value="{{ $hostel->name }}" disabled readonly>
                    </div>

                    <div class="mb-3">
                        <label for="code" class="form-label">Hostel Code</label>
                        <input type="text" class="form-control" name="code" id="code" value="{{ $hostel->code }}">
                    </div>

                    <div class="mb-3">
                        <label for="matric_last_number" class="form-label">Hostel Last Matric Number</label>
                        <input type="number" class="form-control" name="matric_last_number" id="matric_last_number" value="{{ $hostel->matric_last_number }}">
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

@endsection
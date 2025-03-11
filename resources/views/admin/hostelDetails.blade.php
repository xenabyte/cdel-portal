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
            <div class="col-xl-4 col-md-4">
                <div class="card card-height-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-warning text-white rounded-2 fs-2 shadow">
                                    <i class="mdi mdi-bed-double"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-uppercase fw-medium text-muted mb-3">Total Bed Space(s)</p>
                                <h4 class="fs-4 mb-3"><span class="counter-value" data-target="{{ $hostelBedSpaces }}">{{ $hostelBedSpaces }}</span></h4>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div>
            </div> <!-- end col-->
            
            <div class="col-xl-4 col-md-4">
                <div class="card card-height-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-success text-white rounded-2 fs-2 shadow">
                                    <i class="mdi mdi-bed-outline"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-uppercase fw-medium text-muted mb-3">Total Allocated Bed Space(s)</p>
                                <h4 class="fs-4 mb-3"><span class="counter-value" data-target="{{ $allocatedBedSpaces }}">{{ $allocatedBedSpaces }}</span></h4>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div>
            </div> <!-- end col-->
            
            <div class="col-xl-4 col-md-4">
                <div class="card  bg-success card-height-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-info text-white rounded-2 fs-2 shadow">
                                    <i class="mdi mdi-bed-empty"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-uppercase fw-medium text-white-50 mb-3">Total Available Bed Space(s)</p>
                                <h4 class="fs-4 mb-3 text-white"><span class="counter-value" data-target="{{ $hostelBedSpaces - $allocatedBedSpaces }}">{{ $hostelBedSpaces - $allocatedBedSpaces }}</span></h4>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div>
            </div> <!-- end col-->
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
                            <option value="">Select Option </option>
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
                <table id="fixed-header" class="table table-borderedless table-responsive nowrap table-striped align-middle" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Room Number</th>
                            <th scope="col">Room Type</th>
                            <th scope="col">Allocations</th>
                            <th scope="col">Status</th>
                            <th scope="col">No of Space Left</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                        @foreach($hostel->rooms as $room)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $room->number }}</td>
                                <td>{{ $room->type->name . ' - ' . $room->type->capacity }} Bedspaces</td>
                                <td>
                                    <ol>
                                    @foreach($room->allocations->where('academic_session', $pageGlobalData->sessionSetting->academic_session) as $allocation)
                                            <li>{{ $allocation->student->applicant->lastname.' '. $allocation->student->applicant->othernames }} 
                                            <br>Programme: {{ $allocation->student->programme->name }} 
                                            <br>Level: {{ $allocation->student->level_id * 100 }} Level
                                            </li>
                                    @endforeach
                                    </ol>
                                </td>
                                <td><span class="badge badge-pill {{ $room->is_reserved?'bg-warning':'bg-success' }}" data-key="t-hot">{{ $room->is_reserved?'Reserved':'Open' }} </span></td>
                                <td>
                                    {{ intval($room->type->capacity) - $room->allocations->where('academic_session', $pageGlobalData->sessionSetting->academic_session)->count() }}
                                </td>
                                <td>
                                    <div class="hstack gap-3 fs-15">
                                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#reserve{{$room->id}}" class="link-{{ $room->is_reserved?'success':'primary' }}"><i class="ri-list-settings-line"></i></a>
                                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete{{$room->id}}" class="link-danger"><i class="ri-delete-bin-5-line"></i></a>

                                        <div id="delete{{$room->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-body text-center p-5">
                                                        <div class="text-end">
                                                            <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="mt-2">
                                                            <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="hover" style="width:150px;height:150px">
                                                            </lord-icon>
                                                            <h4 class="mb-3 mt-4">Are you sure you want to delete <br/> {{ $room->number }} with {{ $room->type->capacity }} Capacity</h4>
                                                            <form action="{{ url('/admin/deleteRoom') }}" method="POST">
                                                                @csrf
                                                                <input name="room_id" type="hidden" value="{{$room->id}}">
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

                                        <div id="reserve{{$room->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-body text-center p-5">
                                                        <div class="text-end">
                                                            <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="mt-2">
                                                            <h4 class="mb-3 mt-4">Are you sure you want to {{ $room->is_reserved?'open':'reserve' }}  <br/> {{ $room->number }} with {{ $room->type->capacity }} Capacity</h4>
                                                            <form action="{{ url('/admin/reserveRoom') }}" method="POST">
                                                                @csrf
                                                                <input name="room_id" type="hidden" value="{{$room->id}}">
                                                                <hr>
                                                                <button type="submit" id="submit-button" class="btn btn-danger w-100">Yes, Proceed</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer bg-light p-3 justify-content-center">

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
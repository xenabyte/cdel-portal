@extends('admin.layout.dashboard')

@section('content')
 <!-- start page title -->
 <div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Students Allocations</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Students Allocations</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
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
                        <h4 class="fs-4 mb-3"><span class="counter-value" data-target="{{ $totalBedSpaces }}">{{ $totalBedSpaces }}</span></h4>
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
                        <h4 class="fs-4 mb-3"><span class="counter-value" data-target="{{ $allocations->count() }}">{{ $allocations->count() }}</span></h4>
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
                        <h4 class="fs-4 mb-3 text-white"><span class="counter-value" data-target="{{ $totalBedSpaces - $allocations->count() }}">{{ $totalBedSpaces - $allocations->count() }}</span></h4>
                    </div>
                </div>
            </div><!-- end card body -->
        </div>
    </div> <!-- end col-->
</div> <!-- end row-->

<div class="col-lg-12">
    <div class="card">
        <div class="card-body">
            <div class="accordion" id="default-accordion-example">
                <div class="accordion-item shadow">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed bg-info" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                           Manually Allocate room to a student
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#default-accordion-example">
                        <div class="accordion-body">
                            <form action="{{ url('/admin/allocateRoom') }}" method="POST">
                                @csrf
                                <div class="row g-3">

                                    <div class="col-lg-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="matricNumber" name="matric_number" placeholder="Enter your matric number">
                                            <label for="matricNumber">Matric Number</label>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 border-top border-top-dashed">
                                        <div class="d-flex align-items-start gap-3 mt-3">
                                            <button type="button" class="btn btn-primary btn-label right ms-auto validate-button" data-nexttab="pills-bill-address-tab">
                                                <i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i> Validate
                                            </button>
                                        </div>
                                    </div>


                                    <div class="hidden-fields row mt-3 g-3">
                                        <input type="hidden" id="student_id" name="student_id">
                                        <input type="hidden" id="studentGender" class="gender" name="gender">

                                        <div class="col-lg-12">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="reference" placeholder="Enter transaction reference" required>
                                                <label for="matricNumber">Transaction Reference</label>
                                            </div>
                                        </div>

                                        <div class="col-lg-12">
                                            <div class="form-floating">
                                                <select class="form-select" id="campus" aria-label="role" name="campus" onchange="handleCampusChange(event)" required>
                                                    <option selected value="">Select Option </option>
                                                    <option value="West">West Campus</option>
                                                    <option value="East">East Campus</option>
                                                </select>
                                                <label for="campus" class="form-label">Select Campus</label>
                                            </div>
                                        </div>
                    
                                        <div class="col-lg-12">
                                            <div class="form-floating">
                                                <select class="form-select"  id="hostel" name="hostel_id" aria-label="hostel" onchange="handleHostelChange(event)" required>
                                                    <option value="" selected>--Select--</option>
                                                </select>
                                                <label for="hostel">Hostel</label>
                                            </div>
                                        </div>
                                        
                    
                                        <div class="col-lg-12">
                                            <div class="form-floating">
                                                <select class="form-select" id="roomType" name="type_id" aria-label="roomType" onchange="handleRoomTypeChange(event)" required>
                                                    <option value="" selected>--Select--</option>
                                                </select>
                                                <label for="roomType">Room Type</label>
                                            </div>
                                        </div>
                    
                    
                                        <div class="col-lg-12">
                                            <div class="form-floating">
                                                <select class="form-select selectRoom" id="room" name="room_id" aria-label="room" required>
                                                    <option value="" selected>--Select--</option>
                                                </select>
                                                <label for="roomType">Rooms</label>
                                            </div>
                                        </div>

                                        <button type="submit" id="submit-button" class="btn btn-fill btn-primary btn-lg btn-block mb-5">Book Now</button>
                                    </div>       
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Students Allocations</h4>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Support Code</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Level</th>
                            <th scope="col">Matric Number</th>
                            <th scope="col">Programme</th>
                            <th scope="col">Room Allocation</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allocations as $allocation)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td><span class="text-danger">#{{ $allocation->student->id }}</span></td>
                            <td>{{ $allocation->student->applicant->lastname .' '. $allocation->student->applicant->othernames }}</td>
                            <td>{{ $allocation->student->email }} </td>
                            <td>{{ $allocation->student->academicLevel->level }} </td>
                            <td>{{ $allocation->student->matric_number }}</td>
                            <td>{{ $allocation->student->programme->name }}</td>
                            <td>
                                @if($allocation)
                                    <span class="text-primary">Campus: {{ $allocation->room->type->campus }}</span><br>
                                    <span class="text-success">Room: {{ $allocation->room->number }}</span><br>
                                    <span class="text-secondary">Hostel: {{ $allocation->room->hostel->name }}</span><br>
                                    <span class="text-info">Bed Space: {{ $allocation->bedSpace->space }} of {{ $allocation->room->type->capacity }} Bed Space(s)</span>
                                @else
                                    <span class="text-danger">Not Allocated</span>
                                @endif
                            </td>
                            <td>
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete{{$allocation->id}}" class="btn btn-danger m-1"><i class= "ri-delete-bin-5-line"></i> Reverse Application</a>
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
<!-- end row -->

@foreach($allocations as $allocation)
<div id="delete{{$allocation->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="text-end">
                    <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="mt-2">
                    <lord-icon src="https://cdn.lordicon.com/wwneckwc.json" trigger="hover" style="width:150px;height:150px">
                    </lord-icon>
                    <h4 class="mb-3 mt-4">Are you sure you want to reverse Allocation for  <br/> {{ $allocation->student->applicant->lastname .' '. $allocation->student->applicant->othernames }}?</h4>
                    <form action="{{ url('/admin/deleteAllocation') }}" method="POST">
                        @csrf
                        <input name="allocation_id" type="hidden" value="{{$allocation->id}}">
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
@endforeach
@endsection

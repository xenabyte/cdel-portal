@extends('staff.layout.dashboard')
<?php 
    $staff = Auth::guard('staff')->user();
?>
@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Update Staff Directoriate/Unit</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Update Staff Directoriate/Unit</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <div class="row justify-content-center">
                        <div class="col-lg-9">
                            <h4 class="mt-4 fw-semibold">Update Staff Directoriate/Unit</h4>
                            <p class="text-muted mt-3">Welcome! To allow proper staff classification, kindly update your Directoriate/Unit!</p>
                            <div class="mt-4">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                    Click here update
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-center mt-5 mb-2">
                        <div class="col-sm-7 col-8">
                            <img src="{{asset('assets/images/dd.png')}}" alt="" class="img-fluid" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header p-3">
                <h5 class="modal-title text-uppercase fw-bold" id="exampleModalLabel">Update Directoriate/Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <div class="modal-body">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="pills-bill-info" role="tabpanel" aria-labelledby="pills-bill-info-tab">
                        <form class="needs-validation" method="POST" novalidate action="{{ url('staff/updateStaffUnit') }}">
                            @csrf

                            <div class="mb-3" id="unit-container">
                                <label for="unit" class="form-label">Select Directoriate/Unit</label>
                                <select class="form-select" id="unit" name="unit_id" aria-label="unit" required>
                                    <option value="" selected>--Select--</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mt-4">
                                <button class="btn btn-success w-100" id='submit-button' type="submit">Update Changes</button>
                            </div>

                        </form>
                    </div>
                    <!-- end tab pane -->
                </div>
                <!-- end tab content -->
            </div>
        </div>
    </div>
</div>
<!--end modal-->

@endsection

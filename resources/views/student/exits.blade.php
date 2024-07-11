@extends('student.layout.dashboard')
@php
    $student = Auth::guard('student')->user();
    $exitApplications = $student->exitApplications()->orderBy('id', 'DESC')->get();
@endphp
@section('content')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Exit Application(s)</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Exit Application(s)</li>
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
                <h4 class="card-title mb-0 flex-grow-1">Exit Application(s)</h4>
                <div class="flex-shrink-0">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add">Apply For Exit</button>
                </div>
            </div><!-- end card header -->

            @if(!empty($exitApplications) && $exitApplications->count() > 0)
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-6 col-xl-12">
                        
                        <table id="fixed-header" class="table table-borderedless dt-responsive nowrap table-striped align-middle" style="width:100%">
                            <thead>
                                <tr>
                                    <th scope="col">Id</th>
                                    <th scope="col">Purpose</th>
                                    <th scope="col">Destination</th>
                                    <th scope="col">Outing Date</th>
                                    <th scope="col">Returning Date</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">File</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($exitApplications as $exitApplication)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $exitApplication->purpose }} </td>
                                    <td>{{ $exitApplication->destination }} </td>
                                    <td>{{ empty($exitApplication->exit_date)? null : date('F j, Y \a\t g:i A', strtotime($exitApplication->exit_date)) }} </td>
                                    <td>{{ empty($exitApplication->return_date)? null : date('F j, Y \a\t g:i A', strtotime($exitApplication->return_date)) }} </td>
                                    <td>{{ ucwords($exitApplication->status) }} </td>
                                    <td>@if($exitApplication->status != 'Pending') <a href="{{ asset($exitApplication->file) }}" class="btn btn-outline-primary" target="_blank" rel="noopener noreferrer">View Document</a>@endif
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

<div id="add" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
    <!-- Fullscreen Modals -->
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Exit Application</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="{{ url('student/exitApplication') }}" class="checkout-tab border-top border-top-dashed" method="POST">
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                    <div class="modal-body">
                        <div class="hidden-fields row mt-3 g-3">

                            <div class="col-lg-6">
                                <div class="form-floating">
                                    <select name="type" class="form-control" id="type" required>
                                        <option selected value="">Select Exit Type</option>
                                        <option value="Holiday">Holiday</option>
                                        <option value="Casual">Casual</option>
                                    </select>
                                    <label for="type">Exit Type</label>
                                </div>
                            </div>
    
                            <div class="col-lg-6" id="transportField">
                                <div class="form-floating">
                                    <select name="transport_mode" class="form-control" id="transport" required>
                                        <option selected value="">Select Mode of Transportation</option>
                                        <option value="Public">Public</option>
                                        <option value="Private">Private</option>
                                        <option value="School Transport">School Transport</option>
                                        <option value="Flight">Flight</option>
                                    </select>
                                    <label for="transport">Select Mode of Transportation</label>
                                </div>
                            </div>
    
                            <div class="col-lg-6" id="exitDateField">
                                <div class="form-floating">
                                    <input type="date" class="form-control" id="exit_date" name="exit_date">
                                    <label for="exit_date">Outing Date</label>
                                </div>
                            </div>
    
                            <div class="col-lg-6" id="returnDateField">
                                <div class="form-floating">
                                    <input type="datetime-local" class="form-control" id="return_date" name="return_date">
                                    <label for="return_date">Returning Date and time</label>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="destination" name="destination">
                                    <label for="destination">Destination</label>
                                </div>
                            </div>
    
                            <div class="col-lg-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="purpose" name="purpose">
                                    <label for="purpose">Purpose</label>
                                </div>
                            </div>
        
                            <!--end col-->
                            <div class="col-lg-12 border-top border-top-dashed">
                                <div class="d-flex align-items-start gap-3 mt-3">
                                    <button type="submit" id="submit-button" class="btn btn-primary btn-label right ms-auto nexttab" data-nexttab="pills-bill-address-tab"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i> Submit</button>
                                </div>
                            </div>
                            <!--end col-->
                        </div>                        
                    </div>
                    <!--end modal-body-->
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection
@extends('admin.layout.dashboard')

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Bills Setup for {{ $pageGlobalData->sessionSetting->academic_session }} Academic Session</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Bills</li>
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
                <h4 class="card-title mb-0 flex-grow-1">Bills</h4>
                <div class="flex-shrink-0">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPayment">Create a Bill</button>
                    <a href="{{ asset('BulkPaymentFormat.csv') }}" class="btn btn-info" download>Download Format</a>
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#uploadBulkPayment">Bulk upload Bills</button>
                </div>
            </div><!-- end card header -->

            <div class="card-body">
                <div class="row">
                    <div class="card-header align-items-center">
                        <h4 class="card-title mb-0 flex-grow-1">Other Bills</h4>
                    </div><!-- end card header -->
                    @foreach($payments->where('type', '!=', 'School Fee')->where('type', '!=', 'DE School Fee') as $payment)
                    <div class="col-sm-6 col-xl-4">
                        <!-- Simple card -->
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title mb-2">{{ $payment->title }}</h4>
                                <p class="card-text">Programme: {{ $payment->programme ? $payment->programme->name : null }}</p>
                                <p class="text-muted">Bill Type: {{ $payment->type }} </p>
                                <p class="text-muted">Bill Academic Session: {{ $payment->academic_session }}  Academic Session</p>
                                <p class="text-muted">Total Amount: ₦{{ number_format($payment->structures->sum('amount')/100, 2) }} </p>
                                <hr>
                                <div class="text-start">
                                    <a href="{{ url('admin/payment/'.$payment->slug) }}" class="btn btn-warning">View</a>
                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#editPayment{{$payment->id}}" style="margin: 5px" class="btn btn-primary">Edit</a>
                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#deletePayment{{$payment->id}}" style="margin: 5px" class="btn btn-danger btn-block">Delete</a>
                                </div>
                            </div>
                        </div><!-- end card -->
                        <div id="editPayment{{$payment->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content border-0 overflow-hidden">
                                    <div class="modal-header p-3">
                                        <h4 class="card-title mb-0">Update Bill</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <hr>
                                    <div class="modal-body">
                                        <form action="{{ url('/admin/updatePayment') }}" method="post" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name='payment_id' value="{{ $payment->id }}">
                                            
                                            <div class="mb-3">
                                                <label for="paymentTitle" class="form-label">Bill Name</label>
                                                <input type="text" class="form-control" name="title" id="paymentTitle" value="{{ $payment->title }}">
                                            </div>

                                            <div class="mb-3">
                                                <label for="type" class="form-label">Select Bill Type</label>
                                                <select class="form-select" aria-label="type" name="type">
                                                    <option selected value= "">Select type </option>
                                                    <option value="General Application Fee">General Application Fee</option>
                                                    <option value="Inter Transfer Application Fee">Inter Transfer Application Fee</option>
                                                    <option value="Acceptance Fee">Acceptance Fee</option>
                                                    <option value="School Fee">School Fee</option>
                                                    <option value="DE School Fee">Direct Entry School Fee</option>
                                                    <option value="General Fee">General Fee</option>
                                                   <option value="Other Fee">Other Fee</option>
                                                    <option value="Bandwidth Fee">Bandwidth Fee</option>
                                                    <option value="Course Reg">Modify Course Reg Fee</option>
                                                </select>
                                            </div>

                                            @if($payment->type == 'School Fee'  && $payment->type == 'General Fee')
                                            <div class="mb-3">
                                                <label for="category" class="form-label">Select Programme</label>
                                                <select class="form-select" aria-label="category" name="programme_id">
                                                    <option selected value= "">Select Programme </option>
                                                    @foreach($programmes as $programme)
                                                    <option value="{{ $programme->id }}">{{ $programme->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @endif

                                            <div class="mb-3">
                                                <label for="academic_session" class="form-label">Select Academic Session</label>
                                                <select class="form-select" aria-label="academic_session" name="academic_session">
                                                    <option selected value= "">Select Select Academic Session </option>
                                                    @foreach($sessions as $session)
                                                    <option value="{{ $session->year }}">{{ $session->year }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label for="level" class="form-label">Select Level</label>
                                                <select class="form-select" aria-label="level" name="level_id">
                                                    <option selected value= "">Select Level </option>
                                                    @foreach($levels as $acadlevel)
                                                    <option value="{{ $acadlevel->id }}">{{ $acadlevel->level }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
            
                                            <div class="mb-3">
                                                <label for="description" class="form-label">Description</label>
                                                <textarea class="form-control" name="description" id="description" >{!! $payment->description !!}</textarea>
                                            </div>
            
                                            <div class="text-end">
                                                <button type="submit" id="submit-button" class="btn btn-primary">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->

                        <div id="deletePayment{{$payment->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-body text-center p-5">
                                        <div class="text-end">
                                            <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="mt-2">
                                            <lord-icon src="https://cdn.lordicon.com/wwneckwc.json" trigger="hover" style="width:150px;height:150px">
                                            </lord-icon>
                                            <h4 class="mb-3 mt-4">Are you sure you want to delete <br>{{ $payment->title }}?</h4>
                                            <form action="{{ url('/admin/deletePayment') }}" method="POST">
                                                @csrf
                                                <input name="payment_id" type="hidden" value="{{$payment->id}}">

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
                    </div><!-- end col -->
                @endforeach
                </div>

                <div class="row">
                    <div class="card-header align-items-center">
                        <h4 class="card-title mb-0 flex-grow-1">School Fee Bill</h4>
                    </div><!-- end card header -->
                    <hr>
                    <!-- Accordions with Plus Icon -->
                    <div class="accordion custom-accordionwithicon-plus" id="accordionWithplusicon">
                        @foreach($levels as $level)
                            @if($level->id < 6)
                                <div class="accordion-item shadow">
                                    <h2 class="accordion-header" id="accordionwithplusExample{{$level->id}}">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#accor_plusExamplecollapse{{$level->id}}" aria-expanded="true" aria-controls="accor_plusExamplecollapse{{$level->id}}">
                                        {{ $level->level }} Level School Fee
                                        </button>
                                    </h2>
                                    <div id="accor_plusExamplecollapse{{$level->id}}" class="accordion-collapse collapse" aria-labelledby="accordionwithplusExample{{$level->id}}" data-bs-parent="#accordionWithplusicon">
                                        <div class="accordion-body">
                                            <table  id="buttons-datatables{{ $level->id }}" class="display table table-bordered" style="width:100%">
                                                <br>
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Id</th>
                                                        <th scope="col">Programme</th>
                                                        <th scope="col">Type</th>
                                                        <th scope="col">Total Amount</th>
                                                        <th scope="col">Level</th>
                                                        <th scope="col"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($payments->filter(function($payment) {
                                                        return $payment->type === 'School Fee' || $payment->type === 'DE School Fee';
                                                    }) as $schoolFeePayment)
                                                        @if($schoolFeePayment->level->id == $level->id)
                                                            <tr>
                                                                <th scope="row">{{ $loop->iteration }}</th>
                                                                <td>{{ $schoolFeePayment->programme->name  }} </td>
                                                                <td>{{ $schoolFeePayment->type  }} </td>
                                                                <td>₦{{ number_format($schoolFeePayment->structures->sum('amount')/100, 2) }}</td>
                                                                <td>{{ $schoolFeePayment->level->level }}</td>
                                                                <td>
                                                                    <div class="text-start">
                                                                        <a href="{{ url('admin/payment/'.$schoolFeePayment->slug) }}" class="btn btn-warning">View</a>
                                                                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#editSchoolPayment{{$schoolFeePayment->id}}" style="margin: 5px" class="btn btn-primary">Edit</a>
                                                                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#deleteSchoolPayment{{$schoolFeePayment->id}}" style="margin: 5px" class="btn btn-danger btn-block">Delete</a>
                                                                    </div>
                                                                    <div id="editSchoolPayment{{$schoolFeePayment->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                                                        <div class="modal-dialog modal-xl modal-dialog-centered">
                                                                            <div class="modal-content border-0 overflow-hidden">
                                                                                <div class="modal-header p-3">
                                                                                    <h4 class="card-title mb-0">Update Bill</h4>
                                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                                </div>
                                                                                <hr>
                                                                                <div class="modal-body">
                                                                                    <form action="{{ url('/admin/updatePayment') }}" method="post" enctype="multipart/form-data">
                                                                                        @csrf
                                                                                        <input type="hidden" name='payment_id' value="{{ $schoolFeePayment->id }}">
                                                                                        
                                                                                        <div class="mb-3">
                                                                                            <label for="paymentTitle" class="form-label">Bill Name</label>
                                                                                            <input type="text" class="form-control" name="title" id="paymentTitle" value="{{ $schoolFeePayment->title }}">
                                                                                        </div>
                                            
                                                                                        <div class="mb-3">
                                                                                            <label for="type" class="form-label">Select Bill Type</label>
                                                                                            <select class="form-select" aria-label="type" name="type">
                                                                                                <option selected value= "">Select type </option>
                                                                                                <option value="General Application Fee">General Application Fee</option>
                                                                                                <option value="Inter Transfer Application Fee">Inter Transfer Application Fee</option>
                                                                                                <option value="Acceptance Fee">Acceptance Fee</option>
                                                                                                <option value="School Fee">School Fee</option>
                                                                                                <option value="DE School Fee">Direct Entry School Fee</option>
                                                                                                <option value="General Fee">General Fee</option>
                                                                                               <option value="Other Fee">Other Fee</option>
                                                                                                <option value="Bandwidth Fee">Bandwidth Fee</option>
                                                                                                <option value="Course Reg">Modify Course Reg Fee</option>
                                                                                            </select>
                                                                                        </div>
                                            
                                                                                        <div class="mb-3">
                                                                                            <label for="category" class="form-label">Select Programme</label>
                                                                                            <select class="form-select" aria-label="category" name="programme_id">
                                                                                                <option selected value= "">Select Programme </option>
                                                                                                @foreach($programmes as $programme)
                                                                                                <option value="{{ $programme->id }}">{{ $programme->name }}</option>
                                                                                                @endforeach
                                                                                            </select>
                                                                                        </div>
                                
                                                                                        <div class="mb-3">
                                                                                            <label for="academic_session" class="form-label">Select Academic Session</label>
                                                                                            <select class="form-select" aria-label="academic_session" name="academic_session">
                                                                                                <option selected value= "">Select Select Academic Session </option>
                                                                                                @foreach($sessions as $session)
                                                                                                <option value="{{ $session->year }}">{{ $session->year }}</option>
                                                                                                @endforeach
                                                                                            </select>
                                                                                        </div>
                                
                                                                                        <div class="mb-3">
                                                                                            <label for="level" class="form-label">Select Level</label>
                                                                                            <select class="form-select" aria-label="level" name="level_id">
                                                                                                <option selected value= "">Select Level </option>
                                                                                                @foreach($levels as $acadlevel)
                                                                                                <option value="{{ $acadlevel->id }}">{{ $acadlevel->level }}</option>
                                                                                                @endforeach
                                                                                            </select>
                                                                                        </div>
                                                        
                                                                                        <div class="mb-3">
                                                                                            <label for="description" class="form-label">Description</label>
                                                                                            <textarea class="form-control" name="description" id="description" >{!! $schoolFeePayment->description !!}</textarea>
                                                                                        </div>
                                                        
                                                                                        <div class="text-end">
                                                                                            <button type="submit" id="submit-button" class="btn btn-primary">Save Changes</button>
                                                                                        </div>
                                                                                    </form>
                                                                                </div>
                                                                            </div><!-- /.modal-content -->
                                                                        </div><!-- /.modal-dialog -->
                                                                    </div><!-- /.modal -->
                                            
                                                                    <div id="deleteSchoolPayment{{$schoolFeePayment->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                                                        <div class="modal-dialog modal-dialog-centered">
                                                                            <div class="modal-content">
                                                                                <div class="modal-body text-center p-5">
                                                                                    <div class="text-end">
                                                                                        <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                                    </div>
                                                                                    <div class="mt-2">
                                                                                        <lord-icon src="https://cdn.lordicon.com/wwneckwc.json" trigger="hover" style="width:150px;height:150px">
                                                                                        </lord-icon>
                                                                                        <h4 class="mb-3 mt-4">Are you sure you want to delete <br>{{ $schoolFeePayment->title }}?</h4>
                                                                                        <form action="{{ url('/admin/deletePayment') }}" method="POST">
                                                                                            @csrf
                                                                                            <input name="payment_id" type="hidden" value="{{$schoolFeePayment->id}}">
                                            
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
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table> 
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

            </div>
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end row -->


<div id="addPayment" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Add Bill</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="{{ url('/admin/addPayment') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="paymentTitle" class="form-label">Bill Name</label>
                        <input type="text" class="form-control" name="title" id="paymentTitle">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="description" ></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Select Bill Type</label>
                        <select class="form-select" aria-label="type" name="type">
                            <option selected value= "">Select type </option>
                            <option value="General Application Fee">General Application Fee</option>
                            <option value="Inter Transfer Application Fee">Inter Transfer Application Fee</option>
                            <option value="Acceptance Fee">Acceptance Fee</option>
                            <option value="School Fee">School Fee</option>
                            <option value="DE School Fee">Direct Entry School Fee</option>
                            <option value="General Fee">General Fee</option>
                            <option value="Other Fee">Other Fee</option>
                            <option value="Bandwidth Fee">Bandwidth Fee</option>
                            <option value="Course Reg">Modify Course Reg Fee</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="category" class="form-label">Select Programme</label>
                        <select class="form-select" aria-label="category" name="programme_id">
                            <option selected value= "">Select Programme </option>
                            @foreach($programmes as $programme)
                            <option value="{{ $programme->id }}">{{ $programme->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="level" class="form-label">Select Academic Session</label>
                        <select class="form-select" aria-label="level" name="academic_session">
                            <option selected value= "">Select Select Academic Session </option>
                            @foreach($sessions as $session)
                            <option value="{{ $session->year }}">{{ $session->year }}</option>
                            @endforeach
                        </select>
                    </div>


                    <div class="mb-3">
                        <label for="level" class="form-label">Select Level</label>
                        <select class="form-select" aria-label="level" name="level_id">
                            <option selected value= "">Select Level </option>
                            @foreach($levels as $acadlevel)
                            <option value="{{ $acadlevel->id }}">{{ $acadlevel->level }}</option>
                            @endforeach
                        </select>
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Create Bill</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="uploadBulkPayment" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Upload Bulk Bills</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="{{ url('/admin/uploadBulkPayment') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="academic_session" value="{{ $pageGlobalData->sessionSetting->application_session}}">
                    <div class="mb-3">
                        <label for="file" class="form-label">File(CSV)</label>
                        <input type="file" class="form-control" name="file" id="type">
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Upload Bills</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection
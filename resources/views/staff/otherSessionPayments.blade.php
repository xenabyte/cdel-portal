@extends('staff.layout.dashboard')

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">{{ $programmeCategory->category }} Programme Bills Setup for {{ $academicSession }} Academic Session</h4>

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
                <h4 class="card-title mb-0 flex-grow-1">{{ $programmeCategory->category }} Programme Bills Setup for {{ $academicSession }} Academic Session</h4>
                <div class="flex-shrink-0">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPayment">Create a Bill</button>
                    <a href="{{ asset('BulkPaymentFormat.csv') }}" class="btn btn-info" download>Download Format</a>
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#uploadBulkPayment">Bulk upload Bills</button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#billsForSessions">Bills for other sessions</button>
                </div>
            </div><!-- end card header -->

            <div class="card-body">
                <div class="row">
                    <div class="card-header align-items-center">
                        <h4 class="card-title mb-0 flex-grow-1">Other Bills</h4>
                    </div><!-- end card header -->
                    <div class="table-responsive">
                        <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th scope="col">Id</th>
                                    <th scope="col">Title</th>
                                    <th scope="col">Programme</th>
                                    <th scope="col">Bill Type</th>
                                    <th scope="col">Academic Session</th>
                                    <th scope="col">Total Amount</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments->where('type', '!=', 'School Fee')->where('type', '!=', 'DE School Fee') as $payment)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td>{{ $payment->title }}</td>
                                        <td>{{ $payment->programme ? $payment->programme->name : null }}</td>
                                        <td>{{ $payment->type }}</td>
                                        <td>{{ $payment->academic_session }}</td>
                                        <td>₦{{ number_format($payment->structures->sum('amount')/100, 2) }}</td>
                                        <td>
                                            <div class="text-start">
                                                <a href="{{ url('staff/payment/'.$payment->slug) }}" class="btn btn-warning">View</a>
                                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#editPayment{{$payment->id}}" style="margin: 5px" class="btn btn-primary">Edit</a>
                                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#deletePayment{{$payment->id}}" style="margin: 5px" class="btn btn-danger btn-block">Delete</a>
                                            </div>
                        
                                            <!-- Edit Modal -->
                                            <div id="editPayment{{$payment->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                                <div class="modal-dialog modal-xl modal-dialog-centered">
                                                    <div class="modal-content border-0 overflow-hidden">
                                                        <div class="modal-header p-3">
                                                            <h4 class="card-title mb-0">Update Bill</h4>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <hr>
                                                        <div class="modal-body">
                                                            <form action="{{ url('/staff/updatePayment') }}" method="post" enctype="multipart/form-data">
                                                                @csrf
                                                                <input type="hidden" name='payment_id' value="{{ $payment->id }}">
                        
                                                                <div class="mb-3">
                                                                    <label for="paymentTitle" class="form-label">Bill Name</label>
                                                                    <input type="text" class="form-control" name="title" id="paymentTitle" value="{{ $payment->title }}">
                                                                </div>
                        
                                                                <div class="mb-3">
                                                                    <label for="type" class="form-label">Select Bill Type</label>
                                                                    <select class="form-select" aria-label="type" name="type">
                                                                        <option selected value="">Select type</option>
                                                                        @foreach($paymentTypes as $paymentType)
                                                                            <option value="{{ $paymentType->type }}">{{ $paymentType->type }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                        
                                                                @if($payment->type == 'School Fee' && $payment->type == 'General Fee')
                                                                <div class="mb-3">
                                                                    <label for="category" class="form-label">Select Programme</label>
                                                                    <select class="form-select" aria-label="category" name="programme_id">
                                                                        <option selected value="">Select Programme</option>
                                                                        @foreach($programmes as $programme)
                                                                        <option value="{{ $programme->id }}">{{ $programme->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                @endif
                        
                                                                <div class="mb-3">
                                                                    <label for="academic_session" class="form-label">Select Academic Session</label>
                                                                    <select class="form-select" aria-label="academic_session" name="academic_session">
                                                                        <option selected value="">Select Academic Session</option>
                                                                        @foreach($sessions as $session)
                                                                        <option value="{{ $session->year }}">{{ $session->year }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                        
                                                                <div class="mb-3">
                                                                    <label for="level" class="form-label">Select Level</label>
                                                                    <select class="form-select" aria-label="level" name="level_id">
                                                                        <option selected value="">Select Level</option>
                                                                        @foreach($levels as $acadlevel)
                                                                        <option value="{{ $acadlevel->id }}">{{ $acadlevel->level }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                        
                                                                <div class="mb-3">
                                                                    <label for="description" class="form-label">Description</label>
                                                                    <textarea class="form-control ckeditor" name="description" id="description">{!! $payment->description !!}</textarea>
                                                                </div>
                        
                                                                <div class="text-end">
                                                                    <button type="submit" id="submit-button" class="btn btn-primary">Save Changes</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div><!-- /.modal-content -->
                                                </div><!-- /.modal-dialog -->
                                            </div><!-- /.modal -->
                        
                                            <!-- Delete Modal -->
                                            <div id="deletePayment{{$payment->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-body text-center p-5">
                                                            <div class="text-end">
                                                                <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="mt-2">
                                                                <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="hover" style="width:150px;height:150px"></lord-icon>
                                                                <h4 class="mb-3 mt-4">Are you sure you want to delete <br>{{ $payment->title }}?</h4>
                                                                <form action="{{ url('/staff/deletePayment') }}" method="POST">
                                                                    @csrf
                                                                    <input name="payment_id" type="hidden" value="{{ $payment->id }}">
                                                                    <hr>
                                                                    <button type="submit" id="submit-button" class="btn btn-danger w-100">Yes, Delete</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light p-3 justify-content-center"></div>
                                                    </div><!-- /.modal-content -->
                                                </div><!-- /.modal-dialog -->
                                            </div><!-- /.modal -->
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
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
                                                                        <a href="{{ url('staff/payment/'.$schoolFeePayment->slug) }}" class="btn btn-warning">View</a>
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
                                                                                    <form action="{{ url('/staff/updatePayment') }}" method="post" enctype="multipart/form-data">
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
                                                                                                @foreach($paymentTypes as $paymentType)
                                                                                                    <option value="{{ $paymentType->type }}">{{ $paymentType->type }}</option>
                                                                                                @endforeach
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
                                                                                            <textarea class="form-control ckeditor" name="description" id="description" >{!! $schoolFeePayment->description !!}</textarea>
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
                                                                                        <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="hover" style="width:150px;height:150px">
                                                                                        </lord-icon>
                                                                                        <h4 class="mb-3 mt-4">Are you sure you want to delete <br>{{ $schoolFeePayment->title }}?</h4>
                                                                                        <form action="{{ url('/staff/deletePayment') }}" method="POST">
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
                <form action="{{ url('/staff/addPayment') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="paymentTitle" class="form-label">Bill Name</label>
                        <input type="text" class="form-control" name="title" id="paymentTitle">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control ckeditor2" name="description" id="description" ></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Select Bill Type</label>
                        <select class="form-select" aria-label="type" name="type">
                            <option selected value= "">Select type </option>
                            @foreach($paymentTypes as $paymentType)
                                <option value="{{ $paymentType->type }}">{{ $paymentType->type }}</option>
                            @endforeach
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


<div id="billsForSessions" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Bill for Other Sessions</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="{{ url('/staff/billsForSessions') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" name="programme_category_id" value="{{$programmeCategory->id}}">

                    <div class="col-lg-12">
                        <div class="form-floating">
                            <select class="form-select" id="academic_session" name="academic_session" aria-label="academic_session">
                                <option value="" selected>--Select--</option>
                                @foreach($sessions as $acadSession)
                                    <option value="{{ $acadSession->year }}">{{ $acadSession->year }}</option>
                                @endforeach
                            </select>
                            <label for="level">Academic Session</label>
                        </div>
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" id="submit-button" class="btn btn-primary">Fetch</button>
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
                <form action="{{ url('/staff/uploadBulkPayment') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="academic_session" value="{{ $academicSession}}">
                    <input type="hidden" name="programme_category_id" value="{{$programmeCategory->id}}">

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
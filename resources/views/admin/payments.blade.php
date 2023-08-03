@extends('admin.layout.dashboard')

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Payments</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Payments</li>
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
                <h4 class="card-title mb-0 flex-grow-1">Payments</h4>
                <div class="flex-shrink-0">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPayment">Create a Payment</button>
                </div>
            </div><!-- end card header -->

            <div class="card-body">
                <div class="row">
                    @foreach($payments as $payment)
                    <div class="col-sm-6 col-xl-4">
                        <!-- Simple card -->
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title mb-2">{{ $payment->title }}</h4>
                                <p class="card-text">Programme: {{ $payment->programme ? $payment->programme->name : null }}</p>
                                <p class="text-muted">Payment Type: {{ $payment->type }} </p>
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
                                        <h4 class="card-title mb-0">Update Payment</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>

                                    <div class="modal-body">
                                        <form action="{{ url('/admin/updatePayment') }}" method="post" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name='payment_id' value="{{ $payment->id }}">
                                            
                                            <div class="mb-3">
                                                <label for="paymentTitle" class="form-label">Payment Name</label>
                                                <input type="text" class="form-control" name="title" id="paymentTitle" value="{{ $payment->title }}">
                                            </div>

                                            <div class="mb-3">
                                                <label for="type" class="form-label">Select Payment Type</label>
                                                <select class="form-select" aria-label="type" name="type">
                                                    <option selected value= "">Select type </option>
                                                    <option value="Application Fee">Application Fee</option>
                                                    <option value="Acceptance Fee">Acceptance Fee</option>
                                                    <option value="School Fee">School Fee</option>
                                                    <option value="General Fee">General Fee</option>
                                                </select>
                                            </div>

                                            @if($payment->type == 'School Fee')
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
                                                <label for="description" class="form-label">Description</label>
                                                <textarea class="form-control" name="description" id="description" >{!! $payment->description !!}</textarea>
                                            </div>
            
                                            <div class="text-end">
                                                <button type="submit" class="btn btn-primary">Save Changes</button>
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
                                                <button type="submit" class="btn btn-danger w-100">Yes, Delete</button>
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
                <h4 class="card-title mb-0">Add Payment</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="{{ url('/admin/addPayment') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="paymentTitle" class="form-label">Payment Name</label>
                        <input type="text" class="form-control" name="title" id="paymentTitle">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="description" ></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Select Payment Type</label>
                        <select class="form-select" aria-label="type" name="type">
                            <option selected value= "">Select type </option>
                            <option value="Application Fee">Application Fee</option>
                            <option value="Acceptance Fee">Acceptance Fee</option>
                            <option value="School Fee">School Fee</option>
                            <option value="General Fee">General Fee</option>
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

                    <hr>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Create Payment</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection
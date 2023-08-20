@extends('staff.layout.dashboard')

@section('content')
<?php $total = $payment->structures->sum('amount'); ?>

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Payment</h4>

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

<div class="row project-wrapper">
    <div class="col-xxl-12">
        <div class="row">
            <div class="col-xl-7">
                <div class="card card-height-100">
                    <div class="card-header border-0 align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Payment Overview - {{ $payment->title }}</h4>
                        <div class="flex-shrink-0">
                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#editPayment{{$payment->id}}" style="margin: 5px" class="btn btn-success">Edit Payment</a>
                        </div>
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
                        <p class="text-muted">Payment Type: {{ $payment->type }} </p>
                        <hr>
                        <p class="text-muted">Level : {{ !empty($payment->level)? $payment->level->level : null }} </p>
                        <hr>
                        <p class="text-muted">Payment Academic Session: {{ $payment->academic_session }} </p>
                        <hr>
                        {!! $payment->description !!}
                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end col -->
            <div class="col-xl-5">
                <div class="card card-height-100">
                    <div class="card-header border-0 align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Payment Structure</h4>
                        <div class="flex-shrink-0">
                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addStructure" style="margin: 5px" class="btn btn-primary">Add Payment Structure</a>
                        </div>
                    </div><!-- end card header -->
                    <div class="card-body pt-0">
                        <div class="card-header p-0 border-0 bg-soft-light">
                            <div class="row g-0 text-center">
                                <div class="col-12 col-sm-12">
                                    <div class="p-3 border border-dashed border-start-0">

                                    </div>
                                </div>
                            </div>
                        </div><!-- end card header -->
                        <hr>
                        @if(!empty($payment->structures))
                            @foreach($payment->structures as $structure)
                            <div class="mini-stats-wid d-flex align-items-center mt-3">
                                {{ $structure->title }}
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1"></h6>
                                </div>
                                
                                <div class="form-check form-switch">
                                    ₦{{ number_format($structure->amount/100, 2) }}
                                </div>

                                <div class="hstack gap-3 fs-15 mx-3">
                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete{{$structure->id}}" class="link-danger"><i class="ri-delete-bin-5-line"></i></a>
                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit{{$structure->id}}" class="link-primary"><i class="ri-edit-circle-fill"></i></a>
                                </div>
                            </div><!-- end -->

                            <div id="edit{{$structure->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                <div class="modal-dialog modal-xl modal-dialog-centered">
                                    <div class="modal-content border-0 overflow-hidden">
                                        <div class="modal-header p-3">
                                            <h4 class="card-title mb-0">Update Structure</h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body border-top border-top-dashed">
                                            <form action="{{ url('/staff/updateStructure') }}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name='structure_id' value="{{ $structure->id }}">
                                                
                                                <div class="mb-3">
                                                    <label for="structureTitle" class="form-label">Structure Name</label>
                                                    <input type="text" class="form-control" name="title" id="structureTitle" value="{{ $structure->title }}">
                                                </div>
                
                                                <div class="mb-3">
                                                    <label for="amount" class="form-label">Amount</label>
                                                    <input type="text" class="form-control" name="amount" id="amount" value="{{ $structure->amount/100 }}">
                                                </div>
                                                
                                                <div class="text-end">
                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div><!-- /.modal-content -->
                                </div><!-- /.modal-dialog -->
                            </div><!-- /.modal -->

                            <div id="delete{{$structure->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-body text-center p-5">
                                            <div class="text-end">
                                                <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="mt-2">
                                                <lord-icon src="https://cdn.lordicon.com/wwneckwc.json" trigger="hover" style="width:150px;height:150px">
                                                </lord-icon>
                                                <h4 class="mb-3 mt-4">Are you sure you want to delete <br>{{ $structure->title }}?</h4>
                                                <form action="{{ url('/staff/deleteStructure') }}" method="POST">
                                                    @csrf
                                                    <input name="structure_id" type="hidden" value="{{$structure->id}}">

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
                            @endforeach
                            <hr>
                            <div class="mini-stats-wid d-flex align-items-center mt-3">
                                Total
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1"></h6>
                                </div>
                                
                                <div class="form-check form-switch">
                                    ₦{{ number_format($total/100, 2) }}
                                </div>

                                <div class="hstack gap-3 fs-15 mx-4">
                                </div>
                            </div><!-- end -->

                        @endif
                    </div><!-- end cardbody -->
                </div><!-- end card -->
            </div>
        </div><!-- end row -->
    </div><!-- end col -->
</div><!-- end row -->



<div id="editPayment{{$payment->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Update Payment</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body border-top border-top-dashed">
                <form action="{{ url('/staff/updatePayment') }}" method="post" enctype="multipart/form-data">
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
                        <select class="form-select" aria-label="level" name="level_id">
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
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



<div id="addStructure" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Add Payment</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body border-top border-top-dashed">

                <form action="{{ url('/staff/addStructure') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name='payment_id' value="{{ $payment->id }}">

                    <div class="mb-3">
                        <label for="paymentTitle" class="form-label">Structure Name</label>
                        <input type="text" class="form-control" name="title" id="paymentTitle">
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="text" class="form-control" name="amount" id="amount">
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
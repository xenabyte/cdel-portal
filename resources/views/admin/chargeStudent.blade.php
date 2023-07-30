@extends('admin.layout.dashboard')

@section('content')

   

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Charge 'a' student</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Charge 'a' student</li>
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
                <h4 class="card-title mb-0 flex-grow-1">Student Information</h4>
                <div class="flex-shrink-0">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#getStudent">Get Student</button>
                </div>
            </div><!-- end card header -->
            @if(!empty($applicant))
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-6 col-xl-4">
                        <div class="row">
                            <div class="col-md-12">
                                <img class="rounded shadow" width="100%" src="{{asset($applicant->image)}}" alt="Student Logo">
                                <hr>
                            </div>
                        </div>
                        <p class="card-text"><strong>Student Name: </strong> {{ $applicant->lastname .' '. $applicant->othernames }} </p>
                        <hr>
                        <p class="card-text"><strong>Programme: </strong> {{ $applicant->programme->title }} </p>
                        <hr>
                        <p class="card-text"><strong>email: </strong> {{ $applicant->email }}</p>
                        <hr>
                        <p class="card-text"><strong>Phone Number(s): </strong> {{ $applicant->phone_number }}</p>
                    </div><!-- end col -->

                    <div class="col-sm-6 col-xl-8">
                        <div class="text-end mb-5">
                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addTransaction" class="btn btn-primary">Charge Student</a>
                        </div>
                        <div class="card card-body table-responsive">
                            @if(!empty($applicant->transactions))
                            <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th scope="col">Id</th>
                                        <th scope="col">Reference</th>
                                        <th scope="col">Amount(₦)</th>
                                        <th scope="col">Payment For</th>
                                        <th scope="col">Session</th>
                                        <th scope="col">Payment Gateway</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Payment Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($applicant->transactions as $payment)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td>{{ $payment->reference }}</td>
                                        <td>₦{{ number_format($payment->amount_payed/100, 2) }} </td>
                                        <td>{{ $payment->paymentType->title }} </td>
                                        <td>{{ $payment->session }}</td>
                                        <td>{{ $payment->payment_method }}</td>
                                        <td><span class="badge badge-soft-{{ $payment->status == 1 ? 'success' : 'warning' }}">{{ $payment->status == 1 ? 'Paid' : 'Pending' }}</span></td>
                                        <td>{{ $payment->created_at }} </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div><!-- end card -->
    </div>
</div>
<!-- end row -->


<div id="getStudent" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Get Student</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">

                <form action="{{ url('/admin/getStudent') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="reg" class="form-label">Registration Number</label>
                        <input type="text" class="form-control" name="reg_number" id="reg">
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Get student</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@if(!empty($applicant))
<div id="addTransaction" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Create Payment</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="{{ url('/admin/chargeStudent') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $applicant->id }}">
                    <input type="hidden" name="paymentGateway" value="Manual/BankTransfer">

                    <div class="mb-3">
                        <label for="paymentType" class="form-label">Select Payment Type<span class="text-danger">*</span></label>
                        <select class="form-select" aria-label="paymentType" name="payment_id" required onchange="handlePaymentTypeChange(event)">
                            <option value= "" selected>Select Payment Type</option>
                            @foreach($programmePaymentTypes as $programmePaymentType)
                            <option value="{{ $programmePaymentType->id }}"> {{ $programmePaymentType->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3" id='payment-options-acceptance' style="display: none">
                        <label for="amount" class="form-label">Payment Amount<span class="text-danger">*</span></label>
                        <select class="form-select" aria-label="amount" name="amountApplication">
                            <option value= "" selected>Select Amount</option>
                            @foreach($programmePayments->where('type',  'Application Fee') as $programmePayment)
                            <option value="{{ $programmePayment->structures->sum('amount') }}">₦{{ number_format($programmePayment->structures->sum('amount')/100, 2) }} - {{ $programmePayment->title }}</option>
                            @endforeach
                        </select>
                    </div>


                    <div class="mb-3" id='payment-options-tuition' style="display: none">
                        <label for="amount" class="form-label">Payment Amount<span class="text-danger">*</span></label>
                        <select class="form-select" aria-label="amount" name="amountTuition">
                            <option value= "" selected>Select Amount</option>
                            <option value="{{ $programmeTuitionPayment->structures->sum('amount') }}">₦{{ number_format($programmeTuitionPayment->structures->sum('amount')/100, 2) }} - Tuition Fee - 100%</option>
                            <option value="{{ $programmeTuitionPayment->structures->sum('amount')*0.7 }}">₦{{ number_format($programmeTuitionPayment->structures->sum('amount')*0.7/100, 2) }} - Tuition Fee - 70%</option>
                            @if($passTuition && !$fullTuitionPayment)
                            <option value="{{ $programmeTuitionPayment->structures->sum('amount')*0.3 }}">₦{{ number_format($programmeTuitionPayment->structures->sum('amount')*0.3/100, 2) }} - Tuition Fee - 30%</option>
                            @endif
                        </select>
                    </div>

                    <div class="mb-3" id='payment-options-general' style="display: none">
                        <label for="amount" class="form-label">Payment Amount<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="amount" id="amountGeneral">
                    </div>

                    <div class="mb-3">
                        <label for="paymentStatus" class="form-label">Select Payment Status<span class="text-danger">*</span></label>
                        <select class="form-select" aria-label="paymentStatus" name="paymentStatus" required>
                            <option value= "" selected>Select Payment Status</option>
                            <option value="1">Paid</option>
                            <option value="0">Not Paid</option>
                        </select>
                    </div>

                    
                    <hr>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endif

<script>
    function handlePaymentTypeChange(event) {
        const selectedPaymentType = event.target.value;
        if(selectedPaymentType != ''){
            axios.get("{{ url('/admin/paymentById')  }}/"+selectedPaymentType)
            .then(response => {
                const data = response.data;
                const paymentType = data.type;
                console.log(paymentType);
                const amount = data.structures.reduce((total, structure) => total + parseInt(structure.amount), 0);                   

                if (amount > 0) {
                    document.getElementById('payment-options-general').style.display = 'none';
                    if(paymentType == 'Application Fee') {
                        document.getElementById('payment-options-acceptance').style.display = 'block';
                    }else{
                        document.getElementById('payment-options-acceptance').style.display = 'none';
                    }

                    if(paymentType == 'School Fee') {
                        document.getElementById('payment-options-tuition').style.display = 'block';
                    }else{
                        document.getElementById('payment-options-tuition').style.display = 'none';
                    }
                } else {
                    // Hide the select dropdown and show the simple input field
                    document.getElementById('payment-options-general').style.display = 'block';
                }
            })
            .catch(error => {
                console.error(error);
            });
        }

    }
</script>
@endsection
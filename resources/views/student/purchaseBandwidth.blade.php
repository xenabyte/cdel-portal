@extends('student.layout.dashboard')
@php
    $student = Auth::guard('student')->user();
@endphp
@section('content')
@if($student->id == 82)
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Bandwidth Transactions</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Bandwidth Transactions</li>
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
                <h4 class="card-title mb-0 flex-grow-1">Bandwidth Transactions </h4>
                <div class="flex-shrink-0">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTransaction">Create Transaction</button>
                </div>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table id="buttons-datatables" class="display table table-stripped" style="width:100%">
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
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                            @if(!empty($transaction->paymentType) && $transaction->paymentType->type == 'Bandwidth Fee')
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $transaction->reference }}</td>
                                    <td>₦{{ number_format($transaction->amount_payed/100, 2) }} </td>
                                    <td>{{ $transaction->paymentType->title}} </td>
                                    <td>{{ $transaction->session }}</td>
                                    <td>{{ $transaction->payment_method }}</td>
                                    <td><span class="badge badge-soft-{{ $transaction->status == 1 ? 'success' : 'warning' }}">{{ $transaction->status == 1 ? 'Paid' : 'Pending' }}</span></td>
                                    <td>{{ $transaction->status == 1 ? $transaction->updated_at : null }} </td>
                                    <td>
                                        @if($transaction->status == 0)
                                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#payNow{{$transaction->id}}" style="margin: 5px" class="btn btn-warning">Pay Now</a>
                                        @endif
                                    </td>
                                </tr>

                                <div id="payNow{{$transaction->id}}" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content border-0 overflow-hidden">
                                            <div class="modal-header p-3">
                                                <h4 class="card-title mb-0">Pay Now</h4>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>

                                            <div class="modal-body border-top border-top-dashed">
                                                <div class="mt-2 text-center">
                                                <lord-icon src="https://cdn.lordicon.com/ggihhudh.json" trigger="hover" style="width:150px;height:150px">
                                                </lord-icon>
                                                </div>
                                                @if(empty($transaction->checkout_url))
                                                    <form action="https://staging.gateway.paychoice.ng/upl/merchant/pay" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @php
                                                            $uniqueString = md5(uniqid('', true));
                                                            $reference = base64_encode("$transaction->reference:" . $uniqueString);
                                                            $narration = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $transaction->narration)));
                                                            $hash = env('UPPERLINK_MERCHANT_ID') . 'UPL002' . $narration . $transaction->amount_payed . env('UPPERLINK_NGN_CODE') . $reference . $student->email . $student->applicant->phone_number . $student->applicant->lastname . $student->applicant->lastname . env('UPPERLINK_REDIRECT_URL') . env('UPPERLINK_REDIRECT_URL') . env('UPPERLINK_MERCHANT_SECRET');
                                                            $hashedValue = hash('sha256', $hash);
                                                        @endphp
                                                        <input type="hidden" name="merchant_id" value="{{ env('UPPERLINK_MERCHANT_ID') }}">
                                                        <input type="hidden" name="transaction_id" value="{{ $reference }}">
                                                        <input type="hidden" name="product_id" value="UPL002">
                                                        <input type="hidden" name="product_description" value="{{ $narration }}">
                                                        <input type="hidden" name="amount" value="{{ $transaction->amount_payed }}">
                                                        <input type="hidden" name="email" value="{{ $student->email }}">
                                                        <input type="hidden" name="phone_number" value="{{ $student->applicant->phone_number }}">
                                                        <input type="hidden" name="first_name" value="{{ $student->applicant->lastname }}">
                                                        <input type="hidden" name="last_name" value="{{ $student->applicant->lastname }}">
                                                        <input type="hidden" name="response_url" value="{{ env('UPPERLINK_REDIRECT_URL') }}">
                                                        <input type="hidden" name="notify_url" value="{{ env('UPPERLINK_REDIRECT_URL') }}">
                                                        <input type="hidden" name="string2hash" value="{{ $hashedValue }}">
                                                        <input type="hidden" name="currency" value="{{ env('UPPERLINK_NGN_CODE') }}">
                                                    
                                                        <input type="hidden" name="hash" value="{{ hash('sha256', $hash) }}">

                                                        
                                                        <div>
                                                            <button type="submit" id="submit-button" id='submit-button' class="btn btn-primary">Make payment</button>
                                                        </div>
                                                    </form>
                                                @else
                                                    <a href="{{ $transaction->checkout_url }}" class="btn btn-primary">Make payment</a>
                                                @endif
                                            </div>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>

<div id="addTransaction" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Make 'a' Payment</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body border-top border-top-dashed">
                <form action="{{ url('/student/createBandwidthPayment') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="payment_id" value="{{ $bandwidthPayment->id }}">

                    <div class="mb-3">
                        <label for="plan_id" class="form-label">Payment Amount<span class="text-danger">*</span></label>
                        <select class="form-select" aria-label="plan_id" name="plan_id" required>
                            <option value= "" selected>Select Amount</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}">₦{{ number_format($plan->amount/100, 2) }} - {{ $plan->title }} @if(env('BANDWIDTH_BONUS')) + {{  \App\Models\Plan::formatBytes($plan->bonus) }} @endif</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <button type="submit" id='submit-button-main' class="btn btn-primary">Initialize Transaction</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    function handlePaymentTypeChange(event) {
        const selectedPaymentType = event.target.value;
        const academicSession = document.getElementById('academicSession').value;
        const programmeId = document.getElementById('programmeId').value;
        const paymentId = document.getElementById('paymentId');
        const studentId = document.getElementById('studentId').value;
        const level = document.getElementById('level').value;
        const userType = document.getElementById('userType').value;

        const paymentSelect = document.querySelector('select[name="payment_id"]');
        const paymentHidden = document.querySelector('input[name="payment_id"]');


        if(selectedPaymentType != ''){
            axios.post("{{ url('/student/getPayment') }}", {
                type: selectedPaymentType,
                academic_session: academicSession,
                programme_id: programmeId,
                student_id: studentId,
                level: level,
                userType: userType
            })
            .then(response => {
                const data = response.data.data;
                if (response.data.status != 'success') {
                    // Show a SweetAlert for record not found
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops!!',
                        text: response.data.status,
                    });
                }else{
                    if (selectedPaymentType === 'General Fee') {
                        document.getElementById('payment-options-general').style.display = 'block';
                        document.getElementById('payment-for').style.display = 'block';

                        paymentSelect.disabled = false;
                        paymentHidden.disabled = true; 

                        const selectElement = document.querySelector('select[name="payment_id"]');

                        data.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.id; 
                            option.textContent = item.title;
                            selectElement.appendChild(option);
                        });

                    } else {
                        paymentId.value = data.id;

                        document.getElementById('payment-options-general').style.display = 'none';
                        document.getElementById('payment-for').style.display = 'none';

                        paymentSelect.disabled = true;
                        paymentHidden.disabled = false;

                        
                        const selectElement = document.querySelector('[name="amountAcceptance"]');
                        selectElement.innerHTML = '<option value="">Select Amount</option>';

                        const option = document.createElement('option');
                        option.value = data.structures.reduce((total, structure) => total + structure.amount, 0);
                        option.text = '₦' + (option.value / 100).toFixed(2) + ' - ' + data.title;
                        selectElement.appendChild(option);
                    }
                }
            })
            
        }
    }
</script>
@endif
@endsection
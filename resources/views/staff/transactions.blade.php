@extends('staff.layout.dashboard')
@section('content')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Transactions</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Transactions</li>
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
                <h4 class="card-title mb-0 flex-grow-1">Transactions </h4>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table id="buttons-datatables" class="display table table-stripped" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Paid By</th>
                            <th scope="col">Payer Type</th>
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
                        @foreach($transactions as $transaction)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>
                                    @if (!empty($transaction->student_id) && !empty($transaction->student->applicant))
                                        {{ $transaction->student->applicant->lastname .' '. $transaction->student->applicant->othernames }}
                                    @elseif (!empty($transaction->applicant))
                                        {{ $transaction->applicant->lastname .' '. $transaction->applicant->othernames }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ !empty($transaction->student_id) ? 'Student' : 'Applicant' }}</td>
                                <td>{{ $transaction->reference }}</td>
                                <td>₦{{ number_format($transaction->amount_payed / 100, 2) }}</td>
                                <td>
                                    {{ !empty($transaction->paymentType) ? 
                                        ($transaction->paymentType->type == 'General Fee' ? 
                                        $transaction->paymentType->title : 
                                        $transaction->paymentType->type) : 
                                        'Wallet Deposit' 
                                    }} 
                                </td>
                                <td>{{ $transaction->session }}</td>
                                <td>{{ $transaction->payment_method }}</td>
                                <td>
                                    <span class="badge badge-soft-{{ $transaction->status == 1 ? 'success' : 'warning' }}">
                                        {{ $transaction->status == 1 ? 'Paid' : 'Pending' }}
                                    </span>
                                </td>
                                <td>{{ $transaction->status == 1 ? $transaction->updated_at : null }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>
@endsection
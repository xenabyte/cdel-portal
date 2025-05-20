@extends('admin.layout.dashboard')
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
                <div class="flex-shrink-0">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filterModal">Filter by Date</button>
                </div>
            </div><!-- end card header -->

            <div class="card-body table-responsive">
                <!-- Bordered Tables -->
                <table id="buttons-datatables" class="display table table-stripped" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Application/Matric Number</th>
                            <th scope="col">Paid By</th>
                            <th scope="col">Payer Type</th>
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
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>
                                @if (!empty($transaction->student_id) && !empty($transaction->student))
                                    {{ $transaction->student->matric_number }}
                                @elseif (!empty($transaction->applicant))
                                    {{ $transaction->applicant->application_number }}
                                @else
                                    N/A
                                @endif
                            </td>
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
                            <td>
                                @if($transaction->status == 0 && strtolower($transaction->payment_method) == 'upperlink')
                                <form action="{{ url('/staff/requeryUpperlinkPayment') }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">
                
                                    <div>
                                        <button type="submit" id='submit-button-main' class="btn btn-primary">Requery Transaction</button>
                                    </div>
                                </form>
                                @endif
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


<div id="filterModal" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
    <!-- Fullscreen Modals -->
    <div class="modal-dialog modal-md">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0">Filter Transactions</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form method="GET" action="{{ url('/staff/transactions') }}">
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" name="start_date" id="start_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" name="end_date" id="end_date" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Filter</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


@endsection
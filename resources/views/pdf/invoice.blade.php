<!DOCTYPE html>
<html>
<head>
    <title>Transaction Receipt</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-size: 12px;
        }
       
        .header-logo {
            text-align: right;
        }
        .header-logo img {
            width: 25%;
            margin-bottom: 5px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid;
            padding: 2px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .info-column {
            column-count: 2;
            column-gap: 5px;
        }
        @media print {
            .info-column {
                column-count: 2;
                column-gap: 5px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <table style="width: 100%;">
        <tbody>
            <tr>
                <td style="width: 100%; border: none;">
                    <img src="{{ env('SCHOOL_LOGO') }}" width="40%">
                </td>
            </tr>
        </tbody>
    </table>
    <div class="row" style="margin-top: 2%;">
        <div class="text-center">
            <h1>{{ $info->paymentType }} Transaction Receipt</h1>
            <br>
        </div>
    </div>

    <table style="width: 100%;">
        <tbody>
            <tr>
                <td style="width: 50%; vertical-align: top; text-align: left; border: none; padding-right: 10px;">
                    <div><strong>MATRIC NUMBER:</strong> {{ $info->matric_number }}</div>
                    <div><strong>APPLICATION NO:</strong> {{ $info->applicant->application_number }}</div>
                    <div><strong>FULL NAME:</strong> {{ $info->applicant->lastname.' '. $info->applicant->othernames }}</div>
                    <div><strong>LEVEL:</strong> {{ $info->level_id*100 }} Level</div>
                </td>
                <td style="width: 50%; vertical-align: top; text-align: left; border: none; padding-left: 10px;">
                    <div><strong>FACULTY:</strong>  {{ $info->faculty->name }} </div>
                    <div><strong>DEPARTMENT:</strong> {{ $info->department->name }}</div>
                    <div><strong>PROGRAMME:</strong> {{ $info->programme->name }}</div>
                    <div><strong>SESSION:</strong> {{ $info->session }}</div>
                </td>
            </tr>
        </tbody>
    </table>
    <br>
    <div class="row" style="margin-top: 10px;">
        <div class="col-md-12 text-center">
            <h2>Transactions</h2>
        </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>SN</th>
                        <th scope="col">Reference</th>
                        <th scope="col">Amount(NGN)</th>
                        <th scope="col">Payment Gateway</th>
                        <th scope="col">Payment Date</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $transaction->reference }}</td>
                                <td>NGN{{ number_format($transaction->amount_payed/100, 2) }} </td>
                                <td>{{ $transaction->payment_method }}</td>
                                <td>{{date('F j, Y', strtotime($transaction->updated_at))}}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <td><strong>Total</strong></td>
                            <td>
                                <strong>NGN{{ number_format($transactions->sum('amount_payed')/100, 2) }}</strong>
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-12">
            <h4>Amount Billed: NGN{{ number_format($info->amountBilled/100, 2) }}
            <br>
            <h4>Amount Paid:  NGN{{ number_format($transactions->sum('amount_payed')/100, 2) }}
            <br>
            <h4 class="text-danger">Balance: NGN{{ number_format(($info->amountBilled-$transactions->sum('amount_payed'))/100, 2) }}</h4>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <title>Wallet Settlement Report - {{ $group['payment_title'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
        }
        .watermark {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: url('{{ env('SCHOOL_LOGO') }}') center center no-repeat;
            background-size: 50%;
            opacity: 0.05;
            z-index: 0;
            pointer-events: none;
        }
        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        thead tr {
            background-color: #2980b9;
            color: white;
        }
        thead th, tbody td {
            padding: 10px 15px;
            border: 1px solid #ddd;
        }
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tbody tr:hover {
            background-color: #f1f7fc;
        }
        /* Payment group table */
        .payment-group {
            width: 60%;
            margin: 0 auto 40px auto;
            border: 1px solid #2980b9;
            border-radius: 6px;
            background-color: #ecf0f1;
        }
        .payment-group th, .payment-group td {
            border: none;
            padding: 12px 20px;
            text-align: left;
            font-weight: normal;
            color: #2c3e50;
        }
        .payment-group th {
            width: 40%;
            background-color: #3498db;
            color: white;
            font-weight: 600;
        }
        .text-right {
            text-align: right;
        }
        .total-row td {
            font-weight: 700;
            background-color: #d6eaf8;
        }
    </style>
</head>
<body>
<div class="watermark"></div>
<table style="width: 100%; text-align: center;">
    <tbody>
        <tr>
            <td style="width: 100%; border: none;">
                <img src="{{ env('SCHOOL_LOGO') }}" width="40%">
            </td>
        </tr>
    </tbody>
</table>

<h1>Wallet Settlement Report</h1>

<table class="payment-group" cellspacing="0" cellpadding="0">
    <tbody>
        <tr>
            <th>Payment</th>
            <td>{{ $group['payment_title'] }}</td>
        </tr>
        <tr>
            <th>Payment Date</th>
            <td>{{ $group['payment_date'] }}</td>
        </tr>
        <tr>
            <th>Total Amount</th>
            <td>NGN {{ number_format($group['total_amount'] / 100, 2) }}</td>
        </tr>
    </tbody>
</table>

<table cellspacing="0" cellpadding="0" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th style="width: 5%; border: 1px solid #ddd; padding: 8px;">SN</th>
            <th style="width: 30%; border: 1px solid #ddd; padding: 8px;">Student Name</th>
            <th style="width: 10%; border: 1px solid #ddd; padding: 8px;">Matric Number</th>
            <th style="width: 15%; border: 1px solid #ddd; padding: 8px;">Reference</th>
            <th style="width: 15%; border: 1px solid #ddd; padding: 8px; text-align: right;">Amount (NGN)</th>
            <th style="width: 15%; border: 1px solid #ddd; padding: 8px; text-align: right;">Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($group['transactions'] as $index => $transaction)
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ $transaction['student_name'] }}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ $transaction['matric_number'] ?? 'N/A' }}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ $transaction['reference'] }}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">NGN {{ number_format($transaction['amount'] / 100, 2) }}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">{{ $transaction['updated_at'] ?? 'N/A' }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="4" style="border: 1px solid #ddd; padding: 8px; text-align: right; font-weight: bold;">Total</td>
            <td style="border: 1px solid #ddd; padding: 8px; text-align: right; font-weight: bold;">NGN {{ number_format($group['total_amount'] / 100, 2) }}</td>
            <td style="border: 1px solid #ddd; padding: 8px;"></td>
        </tr>
    </tbody>
</table>

</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <title>Resumption Clearance</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-size: 12px;
        }
        .header-logo img {
            width: 25%;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid;
            padding: 5px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-6">
            <img src="{{ env('SCHOOL_LOGO') }}" width="50%">
        </div>
        @if(!empty($student->image))
            <div class="col-6 text-end">
                <img src="{{ asset($student->image) }}" width="30%" style="border:1px solid black;">
            </div>
        @endif
    </div>
    <br>
    <div class="text-center">
        <h2>{{ $semesterText }} Clearance</h2>
    </div>
    @php
        $transactions = $student->paymentCheck->schoolPaymentTransaction;
        $amountBilled = $student->paymentCheck->schoolPayment->structures->sum('amount') ?? 0;
        $amountPaid = $transactions->sum('amount_payed') ?? 0;
        $balance = $amountBilled - $amountPaid;
        $percentagePaid = $amountBilled > 0 ? round(($amountPaid / $amountBilled) * 100, 2) : 0;
        
        $requiredPercent = match((int)$semester) {
            1 => 40,
            2 => 100,
            3 => 80,
            default => 0
        };

        $clearanceText = $percentagePaid >= $requiredPercent ? 'Cleared' : 'Not Cleared';
        $clearanceClass = $clearanceText === 'Cleared' ? 'text-success' : 'text-danger';
        $isCleared = $percentagePaid >= $requiredPercent;
    @endphp
    <br>
      <table style="width: 100%;">
        <tbody>
            <tr>
                <td style="width: 50%; vertical-align: top; text-align: left; border: none; padding-right: 10px;">
                    <div><strong>MATRIC NUMBER:</strong> {{ $student->matric_number }}</div>
                    <div><strong>APPLICATION NO:</strong> {{ $student->applicant->application_number }}</div>
                    <div><strong>FULL NAME:</strong> {{ $student->applicant->lastname.' '. $student->applicant->othernames }}</div>
                    <div><strong>LEVEL:</strong> {{ $student->academicLevel->level }} Level</div>
                    <div><strong>FACULTY:</strong>  {{ $student->faculty->name }} </div>
                    <div><strong>DEPARTMENT:</strong> {{ $student->department->name }}</div>
                    <div><strong>PROGRAMME:</strong> {{ $student->programme->name }}</div>
                    <div><strong>SESSION:</strong> {{ $student->academic_session }}</div>
                </td>
                <td style="width: 50%; vertical-align: top; text-align: left; border: none; padding-left: 10px;">
                    <div><strong>GUARDIAN NAME:</strong> {{ $student->applicant->guardian->name ?? '-' }}</div>
                    <div><strong>RELATIONSHIP:</strong> Guardian </div>
                    <div><strong>PHONE:</strong> {{ $student->applicant->guardian->phone_number ?? '-' }}</div>
                    <div><strong>EMAIL:</strong> {{ $student->applicant->guardian->email ?? '-' }}</div>
                    <div><strong>ADDRESS:</strong> {{ strip_tags($student->applicant->guardian->address ?? '-') }}</div>
                </td>
            </tr>
        </tbody>
    </table>
    <br>
    <table width="100%" cellspacing="0" cellpadding="10" border="0" style="font-family: Arial, sans-serif; font-size: 14px;">
        <tr>
            <td width="25%" style="background-color: #f0f8ff; border: 1px solid #ccc; text-align: center; padding-top: 20px;">
                <div style="font-weight: bold; color: #666;">Amount Billed</div>
                <div style="font-size: 18px; color: #000;">N{{ number_format($amountBilled / 100, 2) }}</div>
            </td>

            <td width="25%" style="background-color: #e8fff3; border: 1px solid #ccc; text-align: center; padding-top: 20px;">
                <div style="font-weight: bold; color: #666;">Total Paid</div>
                <div style="font-size: 18px; color: #000;">N{{ number_format($amountPaid / 100, 2) }}</div>
            </td>

            <td width="25%" style="background-color: #fffbe6; border: 1px solid #ccc; text-align: center; padding-top: 20px;">
                <div style="font-weight: bold; color: #666;">Balance</div>
                <div style="font-size: 18px; color: #000;">N{{ number_format($balance / 100, 2) }}</div>
            </td>

            <td width="25%" style="background-color: #e6f7ff; border: 1px solid #ccc; text-align: center; padding-top: 20px;">
                <div style="font-weight: bold; color: #666;">Percentage Paid</div>
                <div style="font-size: 18px; color: #000;">{{ $percentagePaid }}%</div>
            </td>
        </tr>
    </table>

    <br>

    <table width="100%" cellspacing="0" cellpadding="10" border="0" style="font-family: Arial, sans-serif; font-size: 14px;">
        <tr>
            <td style="border: 2px solid {{ $isCleared ? '#28a745' : '#dc3545' }}; text-align: center; padding: 20px;">
                <h3 style="margin: 10px 0; color: {{ $isCleared ? '#28a745' : '#dc3545' }};">
                    {{ $isCleared ? 'Cleared for Entry' : 'Not Cleared for Entry' }}
                </h3>
                <p style="color: #666;">
                    {{ $percentagePaid }}% paid (required: {{ $requiredPercent }}%)<br>
                    @if (!$isCleared)
                        Outstanding Balance: N{{ number_format($balance / 100, 2) }}
                    @endif
                </p>
            </td>
        </tr>
    </table>
    <br>
    <div>
        <h5>Payment Summary</h5>
        <table>
            <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Payment Method</th>
                    <th class="bg bg-success text-light" scope="col">Amount Paid(N)</th>
                    <th scope="col">Payment Date</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $id = 1;
                @endphp
                @foreach($transactions as $transaction)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $transaction->payment_method }}</td>
                        <td class="bg bg-soft-success">N{{ number_format($transaction->amount_payed/100, 2) }}</td>
                        <td>{{date('l, jS F, Y', strtotime($transaction->updated_at))}}</td>
                    </tr>
                @endforeach
            </tbody> 
        </table>
    </div>
    <br>

    {{-- <div style="padding: 20px; border: 1px solid #ddd; background-color: #f8f9fa; margin-top: 20px;">
        <h3 style="margin-bottom: 15px; font-weight: bold;">Clearance Status</h3>
        <p style="font-size: 18px; margin-bottom: 10px;">
            <strong>Percentage Paid:</strong> {{ $percentagePaid }}%
        </p>
        <p style="font-size: 18px;">
            <strong>Status:</strong>
            <span style="font-weight: bold; color: 
                @if ($clearanceClass === 'text-success') green
                @elseif ($clearanceClass === 'text-warning') orange
                @else red
                @endif
            ;">
                {{ $clearanceText }}
            </span>
        </p>
    </div> --}}

    @if($student->allocatedRoom)
        @php
            $allocatedRoom = $student->allocatedRoom;
        @endphp
        <div style="padding: 20px; border: 1px solid #ccc; margin-top: 20px;">
            <h4 style="margin-bottom: 15px; font-weight: bold;">Hostel Allocation</h4>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;"><strong>Campus</strong></td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $allocatedRoom->room->type->campus }} Campus</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;"><strong>Hostel</strong></td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $allocatedRoom->room->hostel->name }}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;"><strong>Room Number</strong></td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $allocatedRoom->room->number }}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;"><strong>Room Type</strong></td>
                    <td style="border: 1px solid #ddd; padding: 8px;">
                        {{ $allocatedRoom->room->type->name }} - {{ $allocatedRoom->room->type->capacity }} Bed Space(s)
                    </td>
                </tr>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;"><strong>Bed Space</strong></td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $allocatedRoom->bedSpace->space }}</td>
                </tr>
            </table>
        </div>
    @else
        <div style="padding: 20px; border: 1px solid #ccc; margin-top: 20px;">
            <h4 style="margin-bottom: 15px; font-weight: bold;">Hostel Allocation</h4>
            <p style="color: #888;">You have not been allocated a room yet. Please wait for further updates.</p>
        </div>
    @endif

    <div class="mt-4">
        <strong>Date Generated:</strong> {{ now()->format('F j, Y') }}
    </div>
</div>
</body>
</html>
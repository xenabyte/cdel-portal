@php
$qrcode = 'https://quickchart.io/chart?chs=300x300&cht=qr&chl='.env('APP_URL').'/exit/'.$exitApplication->id;
@endphp
<!DOCTYPE html>
<html>
<head>
    <title>Exit Application</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-size: 12px;
        }
        .watermark {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('{{ env('SCHOOL_LOGO') }}') center center no-repeat;
            background-size: 50%;
            opacity: 0.05;
            z-index: 0;
            pointer-events: none;
        }
        th, td {
            border: 1px solid;
            padding: 5px;
            text-align: left;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        .section-title {
            background-color: #f8f9fa;
            padding: 5px;
            font-weight: bold;
            margin-top: 20px;
            border: 1px solid #dee2e6;
        }
        .approval-box {
            border: 1px solid #dee2e6;
            padding: 10px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
<div class="watermark"></div>
<div class="container">

    <!-- Logos -->
    <table>
        <tr>
            <td style="border: none; width: 50%;">
                <img src="{{ env('SCHOOL_LOGO') }}" width="70%">
            </td>
            <td style="border: none; text-align: right; width: 50%;">
                <img src="{{ asset($info->image) }}" width="40%" style="border: 1px solid black;">
            </td>
        </tr>
    </table>

    <!-- Title -->
    <div class="text-center mt-4">
        <h1 class="mb-0">Exit Application</h1>
        <small class="text-muted">Student Exit Permit Record</small>
        <hr>
    </div>

    <!-- Student Details -->
    <div class="row mt-3">
        <div class="col-md-6">
            <p><strong>Matric Number:</strong> {{ $info->matric_number }}</p>
            <p><strong>Application No:</strong> {{ $info->applicant->application_number }}</p>
            <p><strong>Full Name:</strong> {{ $info->applicant->lastname . ' ' . $info->applicant->othernames }}</p>
            <p><strong>Level:</strong> {{ $info->academicLevel->level }} Level</p>
        </div>
        <div class="col-md-6">
            <p><strong>Faculty:</strong> {{ $info->faculty->name }}</p>
            <p><strong>Department:</strong> {{ $info->department->name }}</p>
            <p><strong>Programme:</strong> {{ $info->programme->name }}</p>
            <p><strong>Session:</strong> {{ $info->academic_session }}</p>
        </div>
    </div>

    <!-- Exit Details -->
    <div class="section-title">Exit Information</div>
    <div class="row">
        <div class="col-md-9">
            <p><strong>Application Number:</strong> #{{ sprintf("%06d", $exitApplication->id) }}</p>
            <p><strong>Destination:</strong> {{ $exitApplication->destination }}</p>
            <p><strong>Purpose:</strong> {{ $exitApplication->purpose }}</p>
            <p><strong>Mode of Transportation:</strong> {{ $exitApplication->transport_mode }}</p>
            @if($exitApplication->exit_date)
                <p><strong>Outing Date:</strong> {{ date('F j, Y', strtotime($exitApplication->exit_date)) }}</p>
            @endif
            @if($exitApplication->return_date)
                <p><strong>Returning Date:</strong> {{ date('F j, Y \a\t g:i A', strtotime($exitApplication->return_date)) }}</p>
            @endif

            <hr>
            <p><strong>Student Email:</strong> {{ $info->email }}</p>
            <p><strong>Student Phone Number:</strong> {{ $info->applicant->phone_number }}</p>

            @if(!empty($info->applicant->guardian))
                <hr>
                <p><strong>Guardian Name:</strong> {{ $info->applicant->guardian->name }}</p>
                <p><strong>Guardian Phone:</strong> {{ $info->applicant->guardian->phone_number }}</p>
                <p><strong>Guardian Email:</strong> {{ $info->applicant->guardian->email }}</p>
                <p><strong>Guardian Address:</strong> {!! $info->applicant->guardian->address !!}</p>
            @endif
        </div>
        <div class="col-md-3 text-end">
            @if($exitApplication->status == 'approved')
                <img src="{{ asset('approved.png') }}" width="60%" style="border: 1px solid black;">
            @elseif($exitApplication->status == 'declined')
                <img src="{{ asset('denied.png') }}" width="60%" style="border: 1px solid black;">
            @endif
        </div>
    </div>

    <!-- HOD Approval -->
    <div class="approval-box">
        <h5>HOD Approval</h5>
        <p><strong>Name:</strong> 
            @if($exitApplication->hod) 
                {{ $exitApplication->hod->title }} {{ $exitApplication->hod->lastname }}, {{ $exitApplication->hod->firstname }} 
            @else 
                <em>Not Assigned</em> 
            @endif
        </p>
        <p><strong>Approved?</strong> {{ $exitApplication->is_hod_approved ? 'Yes' : 'No' }}</p>
        @if($exitApplication->is_hod_approved_date)
            <p><strong>Approval Date:</strong> {{ date('F j, Y \a\t g:i A', strtotime($exitApplication->is_hod_approved_date)) }}</p>
        @endif
    </div>

    <!-- Staff Final Approval -->
    <div class="approval-box">
        <h5>Final Approval by Staff</h5>
        <p><strong>Name:</strong> 
            @if($exitApplication->managedBy) 
                {{ $exitApplication->managedBy->title }} {{ $exitApplication->managedBy->lastname }}, {{ $exitApplication->managedBy->firstname }} 
            @else 
                <em>Pending</em> 
            @endif
        </p>
        <p><strong>Approval Time:</strong> 
            {{ $exitApplication->updated_at ? date('F j, Y \a\t g:i A', strtotime($exitApplication->updated_at)) : 'Pending' }}
        </p>
    </div>

    <!-- QR Code -->
    <div class="text-end mt-4">
        <img src="{{ $qrcode }}" width="25%" style="border: 1px solid black;">
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
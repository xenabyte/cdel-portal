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

    <div class="row" style="margin-top: 20%;">
        <div class="text-center">
            <h1>Exit Application</h1>
            <small class="text-muted">Student Exit Permit Record</small>
            <br>
        </div>
    </div>


    <!-- Student Details -->
     <table style="width: 100%;">
        <tbody>
            <tr>
                <td style="width: 50%; vertical-align: top; text-align: left; border: none; padding-right: 10px;">
                    <div><strong>MATRIC NUMBER:</strong> {{ $info->matric_number }}</div>
                    <div><strong>APPLICATION NO:</strong> {{ $info->applicant->application_number }}</div>
                    <div><strong>FULL NAME:</strong> {{ $info->applicant->lastname.' '. $info->applicant->othernames }}</div>
                    <div><strong>LEVEL:</strong> {{ $info->academicLevel->level }} Level</div>
                </td>
                <td style="width: 50%; vertical-align: top; text-align: left; border: none; padding-left: 10px;">
                    <div><strong>FACULTY:</strong>  {{ $info->faculty->name }} </div>
                    <div><strong>DEPARTMENT:</strong> {{ $info->department->name }}</div>
                    <div><strong>PROGRAMME:</strong> {{ $info->programme->name }}</div>
                    <div><strong>SESSION:</strong> {{ $info->academic_session }}</div>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Exit Details -->
    <div class="section-title text-center">Exit Information</div>
    <div class="row">
        <div class="col-md-9">
            <table style="width: 100%; margin-top: 10px;">
                <tbody>
                    <tr>
                        <td style="width: 50%; vertical-align: top; text-align: left; border: none; padding-right: 10px;">
                            <strong>Application Number:</strong> #{{ sprintf("%06d", $exitApplication->id) }}<br>
                            <strong>Destination:</strong> {{ $exitApplication->destination }}<br>
                            <strong>Purpose:</strong> {{ $exitApplication->purpose }}<br>
                            <strong>Mode of Transportation:</strong> {{ $exitApplication->transport_mode }}<br>
                            @if($exitApplication->exit_date)
                                <strong>Outing Date:</strong> {{ date('F j, Y', strtotime($exitApplication->exit_date)) }}<br>
                            @endif
                            @if($exitApplication->return_date)
                                <strong>Returning Date:</strong> {{ date('F j, Y \a\t g:i A', strtotime($exitApplication->return_date)) }}<br>
                            @endif
                        </td>
                        <td style="width: 50%; vertical-align: top; text-align: left; border: none; padding-left: 10px;">
                            <strong>Student Email:</strong> {{ $info->email }}<br>
                            <strong>Student Phone Number:</strong> {{ $info->applicant->phone_number }}<br>
                            @if(!empty($info->applicant->guardian))
                                <br>
                                <strong>Guardian Name:</strong> {{ $info->applicant->guardian->name }}<br>
                                <strong>Guardian Phone:</strong> {{ $info->applicant->guardian->phone_number }}<br>
                                <strong>Guardian Email:</strong> {{ $info->applicant->guardian->email }}<br>
                                <strong>Guardian Address:</strong> {!! $info->applicant->guardian->address !!}<br>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-3 text-end">
            @if($exitApplication->status == 'approved')
                <img src="{{ asset('approved.png') }}" width="60%" style="border: 1px solid black;">
            @elseif($exitApplication->status == 'declined')
                <img src="{{ asset('denied.png') }}" width="60%" style="border: 1px solid black;">
            @endif
        </div>
    </div>

   <table style="width: 100%; margin-top: 30px;">
        <tbody>
            <tr>
                <!-- HOD Approval -->
                <td style="width: 50%; vertical-align: top; text-align: left; border: none; padding-right: 10px;">
                    <h5 style="margin-bottom: 10px;">HOD Approval</h5>
                    <div>
                        <strong>Name:</strong>
                        @if($exitApplication->hod)
                            {{ $exitApplication->hod->title }} {{ $exitApplication->hod->lastname }}, {{ $exitApplication->hod->othernames }}
                        @else
                            <em>Not Assigned</em>
                        @endif
                    </div>
                    <div><strong>Approval Status:</strong> {{ $exitApplication->is_hod_approved ? 'Approved' : 'Pending Approval' }}</div>
                    @if($exitApplication->is_hod_approved_date)
                        <div><strong>Approval Date:</strong> {{ date('F j, Y \a\t g:i A', strtotime($exitApplication->is_hod_approved_date)) }}</div>
                    @endif
                </td>

                <!-- Final Approval -->
                <td style="width: 50%; vertical-align: top; text-align: left; border: none; padding-left: 10px;">
                    @if($exitApplication->managedBy)
                        <h5 style="margin-bottom: 10px;">Final Approval by Staff</h5>
                        <div>
                            <strong>Name:</strong>
                            @if($exitApplication->managedBy)
                                {{ $exitApplication->managedBy->title }} {{ $exitApplication->managedBy->lastname }}, {{ $exitApplication->managedBy->othernames }}
                            @else
                                <em>Pending</em>
                            @endif
                        </div>
                        <div>
                            <strong>Approval Time:</strong>
                            @if($exitApplication->managedBy) {{ $exitApplication->updated_at ? date('F j, Y \a\t g:i A', strtotime($exitApplication->updated_at)) : 'Pending' }} @endif
                        </div>
                    @else
                        <h5 style="margin-bottom: 10px;">Pending Student Care Approval</h5>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    <!-- QR Code -->
    <div class="text-end mt-4">
        <img src="{{ $qrcode }}" width="25%" style="border: 1px solid black;">
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
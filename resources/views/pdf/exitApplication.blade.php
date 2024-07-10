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
                <td style="width: 50%; border: none;">
                    <img src="{{env('SCHOOL_LOGO')}}" width="70%" style="float: left;">
                </td>
                <td style="width: 50%; border: none;">
                    <img src="{{ asset($info->image) }}" width="40%" style="float: right; border: 1px solid black;">
                </td>
            </tr>
        </tbody>
    </table>
    <div class="row" style="margin-top: 20%;">
        <div class="text-center">
            <h1>Exit Application</h1>
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
    <br>
    <div class="row">
        <div class="col-md-12">
            <h4>Exit Information</h4>
        </div>
        <div class="col-md-12">
            <table style="width: 100%;">
                <tbody>
                    <tr>
                        <td style="width: 70%; vertical-align: top; text-align: left; border: none; padding-right: 10px;">
                            <div><strong>Application Number:</strong> #{{ sprintf("%06d", $exitApplication->id) }}</div>
                            <div><strong>Destination:</strong> {{ $exitApplication->destination }}</div>
                            <div><strong>Purpose:</strong> {{ $exitApplication->purpose }}</div>
                            <div><strong>Mode of Transportation:</strong> {{ $exitApplication->transport_mode }}</div>
                            @if(!empty($exitApplication->exit_date))<div><strong>Outing Date:</strong> {{ empty($exitApplication->exit_date)? null : date('F j, Y', strtotime($exitApplication->exit_date)) }}</div>@endif
                            @if(!empty($exitApplication->return_date))<div><strong>Returning Date:</strong> {{ empty($exitApplication->return_date)? null : date('F j, Y \a\t g:i A', strtotime($exitApplication->return_date)) }}</div>@endif
                            <div><hr></div>
                            <div><strong>Student Email:</strong> {{ $info->email }}</div>
                            <div><strong>Student Phone Number:</strong> {{ $info->applicant->phone_number }}</div>
                            @if(!empty($info->applicant->guardian))
                            <div><hr></div>
                            <div><strong>Student Guardian Name:</strong> {{ $info->applicant->guardian->name }}</div>
                            <div><strong>Student Guardian Phone Number:</strong> {{ $info->applicant->guardian->phone_number }}</div>
                            <div><strong>Student Guardian Email:</strong> {{ $info->applicant->guardian->email }}</div>
                            <div><strong>Student Guardian Address:</strong> {{ $info->applicant->guardian->address }}</div>
                            @endif
                        </td>
                        <td style="width: 30%; border: none;">
                            @if($exitApplication->status == 'approved')
                            <img src="{{asset('approved.png')}}" width="40%" style="float: right; border: 1px solid black;">
                            @elseif ($exitApplication->status == 'declined')
                            <img src="{{asset('denied.png')}}" width="40%" style="float: right; border: 1px solid black;">
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <table style="width: 100%;">
        <tbody>
            <tr>
                <td style="width: 70%; border: none;">
                </td>
                <td style="width: 30%; border: none;">
                    <img src="{{ $qrcode }}" width="40%"  style="float: right; border: 1px solid black;">
                </td>
            </tr>
        </tbody>
    </table>
    
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

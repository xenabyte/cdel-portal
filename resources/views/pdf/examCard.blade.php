@php
$qrcode = 'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl='.env('APP_URL').'/examDocket/'.$info->slug;
@endphp
<!DOCTYPE html>
<html>
<head>
    <title>Examination Card</title>
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
                    <img src="https://tau.edu.ng/assets/images/logo.png" width="70%" style="float: left;">
                </td>
                <td style="width: 50%; border: none;">
                    <img src="{{ asset($info->image) }}" width="40%" style="float: right; border: 1px solid black;">
                </td>
            </tr>
        </tbody>
    </table>
    <div class="row" style="margin-top: 20%;">
        <div class="text-center">
            <h1>{{ $pageGlobalData->examSetting->semester == 1?'First' : 'Second' }} Semester Examination Card</h1>
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
        <div class="col-md-12 text-center">
            <h4>Registered Courses</h4>
        </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>SN</th>
                        <th>Code</th>
                        <th>Course Title</th>
                        <th>Unit</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($registeredCourses as $registeredCourse)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $registeredCourse->course->code }}</td>
                                <td>{{ $registeredCourse->course->name }}</td>
                                <td>{{ $registeredCourse->course->credit_unit }}</td>
                                <td>{{ strtoupper(substr($registeredCourse->course->status, 0, 1)) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <table style="width: 100%;">
        <tbody>
            <tr>
                <td style="width: 50%; border: none;">
                </td>
                <td style="width: 50%; border: none;">
                    <img src="{{ $qrcode }}" style="float: right; border: 1px solid black;">
                </td>
            </tr>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

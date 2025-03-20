@php
    $totalPoints = $registeredCourses->sum('points');
    $totalCreditUnits = $registeredCourses->sum('course_credit_unit')
@endphp
<!DOCTYPE html>
<html>
<head>
    <title>Result Card</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-size: 12px;
            position: relative;
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

        .watermark {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background-repeat: no-repeat;
            background-position: center;
            background-size: 40%;
            opacity: 0.1;
            background-image: url('{{ env('SCHOOL_LOGO') }}');
            page-break-before: always;

        }

        @media print {
            .watermark {
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                opacity: 0.1;
                width: 50%;
                height: auto;
                z-index: -1;
                page-break-before: always;
            }
        }

        .watermark-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 50px;
            font-weight: bold;
            color: rgba(0, 0, 0, 0.1);
            white-space: nowrap;
            z-index: -2;
            pointer-events: none; 
            page-break-before: always;
        }
    </style>
</head>
<body>

<div class="container">
    <table style="width: 100%;">
        <tbody>
            <tr>
                <td style="width: 100%; border: none; text-align: center; font-size: 12px; color: red; font-weight: bold;">
                    This is not a transcript
                </td>
            </tr>
            <tr>
                <td style="width: 100%; border: none;">
                    <img src="{{ env('SCHOOL_LOGO') }}" width="40%">
                </td>
            </tr>
        </tbody>
    </table>
    <div class="row" style="margin-top: 2%;">
        <div class="text-center">
            <h1>{{ $info->resultSemester == 1 ? env('FIRST_SEMESTER') : env('SECOND_SEMESTER') }} Examination Result</h1>
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
                    <div><strong>LEVEL:</strong> {{ $info->resultLevel }} Level</div>
                </td>
                <td style="width: 50%; vertical-align: top; text-align: left; border: none; padding-left: 10px;">
                    <div><strong>FACULTY:</strong>  {{ $info->faculty->name }} </div>
                    <div><strong>DEPARTMENT:</strong> {{ $info->department->name }}</div>
                    <div><strong>PROGRAMME:</strong> {{ $info->programme->name }}</div>
                    <div><strong>SESSION:</strong> {{ $info->resultSession }}</div>
                </td>
            </tr>
        </tbody>
    </table>
    <br>
    <div class="row" style="margin-top: 10px;">
        <div class="col-md-12 text-center">
            <h2>Registered Courses</h2>
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
                        <th>Total Score</th>
                        <th>Grade</th>
                        <th>Point</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($registeredCourses as $registeredCourse)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $registeredCourse->course->code }}</td>
                                <td>{{ ucwords(strtolower($registeredCourse->course->name)) }}</td>
                                <td>{{ $registeredCourse->course_credit_unit }}</td>
                                <td>{{ $registeredCourse->total }}</td>
                                <td>{{ $registeredCourse->grade }}</td>
                                <td>{{ $registeredCourse->points }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <td></td>
                            <td><strong>Total</strong></td>
                            <td>{{ $totalCreditUnits }}</td>
                            <td></td>
                            <td></td>
                            <td>{{ $totalPoints }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-12">
            <h4>G.P.A: {{ number_format($totalPoints / $totalCreditUnits, 2) }}</h4>
            <br>
            <h4>Total Credit Unit: {{ $cgpaData->levelTotalUnit }}</h4>
            <br>
            <h4>Total Credit Point: {{ $cgpaData->levelTotalPoint }}</h4>
            <br>
            <h4>C.G.P.A: {{ $cgpaData->levelCGPA }}</h4>
        </div>
    </div>
</div>
<div class="watermark"></div>
<div class="watermark-text">This is not a transcript</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

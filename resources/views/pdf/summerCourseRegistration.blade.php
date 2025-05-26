
<!DOCTYPE html>
<html>
<head>
    <title>Course Registration</title>
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
        /* @media print {
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
        } */
    </style>
</head>
<body>
<div class="watermark"></div>
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
            <h1>Summer Course Registration Form</h1>
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
            <h4>Summer Semester Courses</h4>
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
                    @php
                        $totalCreditUnits = 0;
                    @endphp
                    <tbody>
                        @foreach($registeredCourses as $registeredCourse)
                            @php
                                $totalCreditUnits += $registeredCourse->course_registration->course_credit_unit;
                            @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $registeredCourse->course_registration->course->code }}</td>
                                    <td>{{ ucwords(strtolower($registeredCourse->course_registration->course->name)) }}</td>
                                    <td>{{ $registeredCourse->course_registration->course_credit_unit }}</td>
                                    <td>{{ strtoupper(substr($registeredCourse->course_registration->course_status, 0, 1)) }}</td>
                                </tr>
                        @endforeach
                        <tr>
                            <td colspan="3"><strong>Total Credit Units</strong></td>
                            <td><strong>{{ $totalCreditUnits }}</strong></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <br>
   
    <div class="row text-justify">
        <p>By this CourseForm, I undertake that as a {{ $info->academicLevel->level }} Level student of {{ $info->department->name }} (Department) in the {{ $info->academic_session }} session, I shall remain in good standing, and maintain the minimum CGPA required by the laws and regulations of the Department/Faculty. If I do not meet the minimum required CGPA, I will be held solely responsible, and the department/Faculty shall take the required measures against me, as the case may be, in accordance with the University&apos;s Rules and regulations.</p>
    </div>
    <div class="row mt-4">
        <div class="col-md-6 text-left">
            <strong>Date Generated:</strong> {{ date('F j, Y') }}
        </div>
    </div>
    
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

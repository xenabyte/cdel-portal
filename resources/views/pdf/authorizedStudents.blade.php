<!DOCTYPE html>
<html>
<head>
    <title>Authorized Students Attendance List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-size: 12px;
            position: relative;
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
    </style>
</head>
<body>
<div class="watermark"></div>
<div class="container">
    <table style="width: 100%;">
        <tbody>
            <tr>
                <td style="width: 100%; border: none;">
                    <img src="{{env('SCHOOL_LOGO')}}" width="70%" style="float: left;">
                </td>
            </tr>
        </tbody>
    </table>
    <div class="row" style="margin-top: 20%;">
        <div class="text-center">
            <h1>{{ $programmeCategory->category }} Examination Attendance List</h1>
            <br>
        </div>
    </div>
    <table style="width: 100%;">
        <tbody>
            <tr>
                <td style="width: 50%; vertical-align: top; text-align: left; border: none; padding-right: 10px;">
                    <div><strong>Course Title:</strong> {{ $course->name }}</div>
                    <div><strong>Course Code:</strong> {{ $course->code }}</div>
                </td>
                <td style="width: 50%; vertical-align: top; text-align: left; border: none; padding-left: 10px;">
                    <div><strong>Academic Session:</strong> {{ $academicSession }}</div>
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
              dd($students);
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th class="col-sno">S/No</th>
                      <th>Passport</th>
                      <th>Matric No</th>
                      <th>Full Name</th>
                      <th class="col-sex">Sex</th>
                      <th class="col-level">Level</th>
                      <th>Faculty</th>
                      <th>Department</th>
                      <th>Programme</th>
                      <th>Sign In</th>
                      <th>Sign Out</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $entry)
                        <tr>
                          <td class="col-sno">{{ $loop->iteration }}</td>
                          <td>
                            <img
                              src="{{ !empty($entry['student']->image) ? $entry['student']->image : asset('assets/images/users/user-dummy-img.jpg') }}"
                              alt="Passport">
                          </td>
                          <td>{{ $entry['student']->matric_number ?? 'N/A' }}</td>
                          <td>
                            {{ optional($entry['student']->applicant)->lastname }}
                            {{ optional($entry['student']->applicant)->othernames }}
                          </td>
                          <td>{{ optional($entry['student']->academicLevel)->level ?? '' }}</td>
                          <td>{{ optional($entry['student']->faculty)->name ?? '' }}</td>
                          <td>{{ optional($entry['student']->department)->name ?? '' }}</td>
                          <td>{{ optional($entry['student']->programme)->award ?? '' }}</td>

                          <td></td>
                          <td></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                 <table style="width: 100%;">
                    <tbody>
                        <tr>
                            <td style="width: 70%; border: none;">
                            </td>
                            <td style="width: 30%; border: none;">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

   
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
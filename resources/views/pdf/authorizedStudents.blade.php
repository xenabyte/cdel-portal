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
        /* {{ env('SCHOOL_LOGO') }} */
        .watermark {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('https://tau.edu.ng/assets/images/logo.png') center center no-repeat;
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
                    <img src="https://tau.edu.ng/assets/images/logo.png" width="70%" style="float: left;">
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
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th class="col-sno">S/No</th>
                      <th>Passport</th>
                      <th>Matric No</th>
                      <th>Full Name</th>
                      <th>Sex</th>
                      <th>Level</th>
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
                          <td>{{ optional($entry['student']->applicant)->gender ?? '' }}</td>

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
                <br><br>

                <h3 style="color: black; font-weight: bold;">
                  For Official Use Only – Not to be filled by students
                </h3>
          
                <table
                  style="width: 100%; margin-top: 30px; font-size: 11px; text-align: center; border-collapse: separate; border-spacing: 20px 10px;">
                  <tr>
                    <td
                      style="border-bottom: 1px solid #000; height: 40px; border-left:none; border-right:none; border-top: none;">
                    </td>
                    <td style="border-bottom: 1px solid #000; border-left:none; border-right:none; border-top: none;"></td>
                    <td style="border-bottom: 1px solid #000; border-left:none; border-right:none; border-top: none;"></td>
                  </tr>
                  <tr>
                    <th style="border: none; background-color: transparent;">Chief Supervisor's Name</th>
                    <th style="border: none; background-color: transparent;">Signature</th>
                    <th style="border: none; background-color: transparent;">Date</th>
                  </tr>
                </table>
          
            </div>
        </div>
    </div>

   
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
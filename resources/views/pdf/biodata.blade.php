@php
    $pageWidth = 794;
    $pageHeight = 1123;

    // Watermark spacing
    $xSpacing = 395;
    $ySpacing = 20;

    $cols = intval($pageWidth / $xSpacing);
    $rows = intval($pageHeight / $ySpacing);

    $text = strtoupper(env('SCHOOL_NAME').' - ' . $student->applicant->lastname . ' ' . $student->applicant->othernames . ' - ' . $student->academic_session);
@endphp

<!DOCTYPE html>
<html>
<head>
    <title>Student Biodata</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-size: 14px;
        }
        .header-logo img {
            width: 25%;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        /* th, td {
            border: 1px solid;
            padding: 5px;
            text-align: center;
        } */
        th {
            background-color: #f2f2f2;
        }
        .watermark {
            position: fixed;
            top: 35%;
            left: 25%;
            width: 50%;
            opacity: 0.04;
            z-index: -1;
        }
        .watermark-text {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            pointer-events: none;
            z-index: -1;
            user-select: none;
            opacity: 0.15;
            font-size: 10px;
            color: #ccc;
            white-space: nowrap;
        }
        .watermark-text div {
            position: absolute;
            width: 400px;
            transform: rotate(-45deg);
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>

<div class="">
    <img src="{{ env('SCHOOL_LOGO') }}" class="watermark">
    <div class="watermark-text">
        @for ($row = 0; $row < $rows; $row++)
            @for ($col = 0; $col < $cols; $col++)
                <div style="top: {{ $row * $ySpacing }}px; left: {{ $col * $xSpacing }}px;">
                    {{ $text }}
                </div>
            @endfor
        @endfor
    </div>
    <!-- Important Note -->
    <p style="color: red; font-weight: bold; text-align: center; font-size: 12px; margin-top: 5px;">
        NOTE: Please come with <u>6 printed copies</u> of this clearance document at resumption.
    </p>

    <div class="row">
        <div class="col-6">
            <img src="{{ env('SCHOOL_LOGO') }}" width="40%">
        </div>
        @if(!empty($student->image))
            <div class="col-6 text-end">
                <img src="{{ asset($student->image) }}" width="30%" style="border:1px solid black;">
            </div>
        @endif
    </div>
    <br>
    <div class="text-center">
        <h1>Student Biodata</h1>
    </div>
    <br>
    <table style="width: 100%; border-collapse: collapse;">
        <tbody>
            <tr>
                <td style="width: 60%; vertical-align: top; padding: 5px 10px;">
                    <h3>Academic Information</h3>
                    <div><strong>Matric Number:</strong> {{ $student->matric_number }}</div>
                    <div><strong>Application No:</strong> {{ $student->applicant->application_number }}</div>
                    <div><strong>JAMB Registration No:</strong> {{ $student->applicant->jamb_reg_no }}</div>
                    <div><strong>Full Name:</strong> {{ $student->applicant->lastname . ' ' . $student->applicant->othernames }}</div>
                    <div><strong>Level:</strong> {{ $student->academicLevel->level }} Level</div>
                    <div><strong>Faculty:</strong> {{ $student->faculty->name }}</div>
                    <div><strong>Department:</strong> {{ $student->department->name }}</div>
                    <div><strong>Programme:</strong> {{ $student->programme->name }}</div>
                    <div><strong>Academic Session:</strong> {{ $student->academic_session }}</div>
                    <div><strong>Mode of Entry:</strong> {{ $student->applicant->application_type }}</div>
                    <div><strong>Entry Year:</strong> {{ $student->entry_year }}</div>
                    <div><strong>Entry Batch:</strong> {{ $student->batch }}</div>
                </td>

                <td style="width: 40%; vertical-align: top; padding: 5px 10px;">
                    <h3>Personal Information</h3>
                    <div><strong>Gender:</strong> {{ $student->applicant->gender }}</div>
                    <div><strong>Date of Birth:</strong>  {{ \Carbon\Carbon::parse($student->applicant->dob)->format('F j, Y') }}</div>
                    <div><strong>Marital Status:</strong> {{ $student->applicant->marital_status }}</div>
                    <div><strong>Religion:</strong> {{ $student->applicant->religion }}</div>
                    <div><strong>Nationality:</strong> {{ $student->applicant->nationality }}</div>
                    <div><strong>State of Origin:</strong> {{ $student->applicant->state }}</div>
                    <div><strong>LGA:</strong> {{ $student->applicant->lga }}</div>
                    <div><strong>Position in Family:</strong> {{ $student->applicant->family_position }}</div>
                    <div><strong>Number of Siblings:</strong> {{ $student->applicant->number_of_siblings }}</div>
                    <div><strong>Hobbies:</strong> {!! strip_tags($student->hobbies) !!}</div>
                </td>
            </tr>

            <tr>
                <td style="width: 50%; vertical-align: top; padding: 5px 10px;">
                    <h3>Contact Information</h3>
                    <div><strong>Residential Address:</strong> {!! strip_tags($student->applicant->address) !!}</div>
                    <div><strong>Phone Number:</strong> {{ $student->applicant->phone_number }}</div>
                    <div><strong>Email:</strong> {{ $student->email }}</div>
                </td>

                <td style="width: 50%; vertical-align: top; padding: 5px 10px;">
                    <h3>Social Media</h3>
                    <div><strong>Facebook:</strong> {{ $student->facebook }}</div>
                    <div><strong>Twitter:</strong> {{ $student->twitter }}</div>
                    <div><strong>Instagram:</strong> {{ $student->instagram }}</div>
                    <div><strong>LinkedIn:</strong> {{ $student->linkedIn }}</div>
                    <div><strong>TikTok:</strong> {{ $student->tiktok }}</div>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Guardian and Parent Information -->
    <table style="width: 100%; border-collapse: collapse; margin-top: 2px;">
        <tbody>
            <tr>
                <td style="width: 50%; vertical-align: top; padding: 5px 10px;">
                    <h3>Father's Information</h3>
                    <div><strong>Father's Name:</strong> {{ $student->applicant->guardian->father_name ?? '-' }}</div>
                    <div><strong>Father's Phone:</strong> {{ $student->applicant->guardian->father_phone ?? '-' }}</div>
                    <div><strong>Father's Email:</strong> {{ $student->applicant->guardian->father_email ?? '-' }}</div>
                    <div><strong>Father's Occupation:</strong> {{ $student->applicant->guardian->father_occupation ?? '-' }}</div>
                </td>
                <td style="width: 50%; vertical-align: top; padding: 5px 10px;">
                    <h3>Mother's Information</h3>
                    <div><strong>Mother's Name:</strong> {{ $student->applicant->guardian->mother_name ?? '-' }}</div>
                    <div><strong>Mother's Phone:</strong> {{ $student->applicant->guardian->mother_phone ?? '-' }}</div>
                    <div><strong>Mother's Email:</strong> {{ $student->applicant->guardian->mother_email ?? '-' }}</div>
                    <div><strong>Mother's Occupation:</strong> {{ $student->applicant->guardian->mother_occupation ?? '-' }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding: 5px 10px;">
                    <div><strong>Parent Residential Address:</strong> {{ strip_tags($student->applicant->guardian->parent_residential_address ?? '-') }}</div>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Guardian Info Table -->
    <table style="width: 100%; border-collapse: collapse; margin-top: 5px;">
        <thead>
            <tr>
                <th colspan="2" style="text-align: left; padding: 10px 10px 8px; border-bottom: 2px solid #333; text-transform: uppercase;">
                    Guardian Information
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 2px 10px; font-weight: bold; width: 35%;">Guardian Name:</td>
                <td style="padding: 2px 10px;">{{ $student->applicant->guardian->name ?? '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 2px 10px; font-weight: bold;">Phone:</td>
                <td style="padding: 2px 10px;">{{ $student->applicant->guardian->phone_number ?? '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 2px 10px; font-weight: bold;">Email:</td>
                <td style="padding: 2px 10px;">{{ $student->applicant->guardian->email ?? '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 2px 10px; font-weight: bold;">Address:</td>
                <td style="padding: 2px 10px;">{{ strip_tags($student->applicant->guardian->address ?? '-') }}</td>
            </tr>
        </tbody>
    </table>

    <table style="width: 100%; border-collapse: collapse; margin-top: 5px;">
        <tr>
            <td colspan="2" style="border: 1px solid #000; padding: 10px;">
                <strong>CONSENT AND DECLARATION</strong><br>
                I hereby declare that the information provided in this form is accurate to the best of my knowledge.
            </td>
        </tr>
        <tr>
            <td style="width: 50%; border: 1px solid #000; padding: 10px;">
                <strong>Signature:</strong><br>
                <img src="{{ asset($student->signature) }}" alt="Signature" style="max-height: 100px;">
            </td>
            <td style="width: 50%; border: 1px solid #000; padding: 10px;">
                <strong>Date:</strong>
                {{ date('F j, Y') }}
            </td>
        </tr>
    </table>
</div>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Letter</title>
    <style>
        body {
            font-family: "Calibri", sans-serif;
            margin: 0;
            padding: 0;
            font-size: 12px; /* Reduced font size */
            position: relative;
        }
        .container {
            width: 90%; /* Increased width */
            margin: 0 auto;
            padding: 10px; /* Reduced padding */
            border: none;
            position: relative;
            z-index: 1;
        }
        .watermark {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: url('{{ env('SCHOOL_LOGO') }}') center center no-repeat;
            background-size: 50%;
            opacity: 0.1; /* Adjust for visibility */
        }
        .header {
            text-align: center;
        }
        .header img {
            width: 30%; /* Reduced image width */
        }
        .header strong {
            font-size: 20px; /* Slightly reduced font size */
            color: #E25041;
        }
        .header .email {
            font-size: 12px; /* Reduced font size */
            color: #000;
        }
        .content {
            line-height: 1.4; /* Reduced line height */
            position: relative;
            z-index: 2;
        }
        .content p {
            margin: 8px 0; /* Reduced margin */
            text-align: justify;
        }
        .content .date {
            text-align: right;
            margin-bottom: 10px; /* Reduced margin */
        }
        .content .congratulations {
            text-align: center;
            font-size: 14px; /* Slightly reduced font size */
        }
        .content ul {
            list-style-type: disc;
            margin-left: 20px;
        }
        .content table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px; /* Reduced margin */
        }
        .content table, .content th, .content td {
            border: 1px solid #000;
        }
        .content th, .content td {
            padding: 4px; /* Reduced padding */
            text-align: left;
            font-size: 12px; /* Reduced font size */
        }
        .footer {
            margin-top: 20px; /* Reduced margin */
        }
        .footer img {
            width: 8%; /* Reduced image width */
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.1;
            z-index: 0;
        }
    </style>
</head>
<body>
    <div class="watermark"></div>
    <div class="container">
        <div class="header">
            <img src="{{ env('SCHOOL_LOGO') }}" alt="School Logo">
            <p><strong>Office of the Registrar</strong></p>
            <p class="email">Email: <a href="mailto:registrar@tau.edu.ng">registrar@tau.edu.ng</a></p>
        </div>

        <div class="content">
            <p class="date"><strong>Date:</strong> {{ date('F j, Y', strtotime(now())) }}</p>
            <table>
                <tr>
                    <th colspan="3">PART A: (Personal Details)</th>
                </tr>
                <tr>
                    <td><strong>Lastname:</strong> {{ $info->applicant->lastname }}</td>
                    <td  colspan="2"><strong>Othernames:</strong> {{ $info->applicant->othernames }}</td>
                </tr>
                <tr>
                    <td><strong>MATRIC No:</strong> <br> {{ $info->matric_number }}</td>
                    <td><strong>DEPARTMENT:</strong> <br> {{ $info->department->name }}</td>
                    <td><strong>PROGRAMME:</strong> <br> {{ $info->programme->name }}</td>
                </tr>
                <tr>
                    <td><strong>GENDER:</strong> {{ $info->applicant->gender }}</td>
                    <td><strong>GSM No:</strong> {{ $info->applicant->phone_number }}</td>
                    <td><strong>EMAIL ADDRESS:</strong> <br> {{ $info->email }}</td>
                </tr>
                <tr>
                    <td><strong>DATE OF BIRTH:</strong> <br>{{ date('F j, Y', strtotime($info->applicant->dob)) }}</td>
                    <td colspan="2"><strong>DATE OF GRADUATION:</strong> {{ $info->academic_session }}</td>
                </tr>
            </table>
            
            <table>
                <tr>
                    <th colspan="4">PART B: (Faculty & School Clearance)</th>
                </tr>
                <tr>
                    <td><strong>Clearing Officer Position</strong></td>
                    <td><strong>Clearing Officer's Name</strong></td>
                    <td><strong>Signature</strong></td>
                    <td><strong>Date</strong></td>
                </tr>
                @if(!empty($finalClearance->hod))
                <tr>
                    <td>HOD, {{ $info->department->name }}</td>
                    <td>{{ $finalClearance->hod->title.'. '.$finalClearance->hod->lastname.' '.$finalClearance->hod->othernames }}</td>
                    <td><img src="{{ asset($finalClearance->hod->signature ) }}" width="10%"></td>
                    <td {{ date('F j, Y \a\t g:i A', strtotime($finalClearance->hod_approval_date)) }}td>
                </tr>
                @endif
                @if(!empty($finalClearance->dean))
                <tr>
                    <td>DEAN, {{ $info->faculty->name }}</td>
                    <td>{{ $finalClearance->dean->title.'. '.$finalClearance->dean->lastname.' '.$finalClearance->dean->othernames }}</td>
                    <td><img src="{{ asset($finalClearance->dean->signature ) }}" width="10%"></td>
                    <td {{ date('F j, Y \a\t g:i A', strtotime($finalClearance->dean_approval_date)) }}td>
                </tr>
                @endif

                @if(!empty($finalClearance->student_care_dean))
                <tr>
                    <td>DEAN, Student Care Services</td>
                    <td>{{ $finalClearance->student_care_dean->title.'. '.$finalClearance->student_care_dean->lastname.' '.$finalClearance->student_care_dean->othernames }}</td>
                    <td><img src="{{ asset($finalClearance->student_care_dean->signature ) }}" width="10%"></td>
                    <td {{ date('F j, Y \a\t g:i A', strtotime($finalClearance->student_care_dean_approval_date)) }}td>
                </tr>
                @endif

                @if(!empty($finalClearance->librarian))
                <tr>
                    <td>LIBRARIAN</td>
                    <td>{{ $finalClearance->librarian->title.'. '.$finalClearance->librarian->lastname.' '.$finalClearance->librarian->othernames }}</td>
                    <td><img src="{{ asset($finalClearance->librarian->signature ) }}" width="10%"></td>
                    <td {{ date('F j, Y \a\t g:i A', strtotime($finalClearance->library_approval_date)) }}td>
                </tr>
                @endif

                @if(!empty($finalClearance->bursary))
                <tr>
                    <td>BURSARY</td>
                    <td>{{ $finalClearance->bursary->title.'. '.$finalClearance->bursary->lastname.' '.$finalClearance->bursary->othernames }}</td>
                    <td><img src="{{ asset($finalClearance->bursary->signature ) }}" width="10%"></td>
                    <td {{ date('F j, Y \a\t g:i A', strtotime($finalClearance->bursary_approval_date)) }}td>
                </tr>
                @endif
                
                @if(!empty($finalClearance->registrar))
                <tr>
                    <td>REGISTRY</td>
                    <td>{{ $finalClearance->registrar->title.'. '.$finalClearance->registrar->lastname.' '.$finalClearance->registrar->othernames }}</td>
                    <td><img src="{{ asset($finalClearance->registrar->signature ) }}" width="10%"></td>
                    <td {{ date('F j, Y \a\t g:i A', strtotime($finalClearance->registrar_approval_date)) }}td>
                </tr>
                @endif

            </table>

            <p>NB: CLEARANCE SIGNING MUST BE COMPLETED AND SUBMITTED BEFORE CERTIFICATE/PERSONAL TRANSCRIPT CAN BE ISSUED</p>
            
        </div>


        <div class="footer">
            <img src="{{ asset($pageGlobalData->sessionSetting->registrar_signature) }}" alt="Registrar Signature">
            <p>{{ $pageGlobalData->sessionSetting->registrar_name }}<br><strong>Registrar</strong></p>
        </div>
        
        <div class="watermark">
            <img src="{{ env('SCHOOL_LOGO') }}" alt="Watermark Logo">
        </div>
    </div>
</body>
</html>
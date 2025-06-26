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
        /* .watermark {
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
        } */

        .watermark {
            position: fixed;
            top: 35%;
            left: 25%;
            width: 50%;
            opacity: 0.04;
            z-index: -1;
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
        /* .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.1;
            z-index: 0;
        } */
    </style>
</head>
<body>
    <img src="{{ env('SCHOOL_LOGO') }}" class="watermark">
    <div class="container">
        <div class="header">
            <img src="{{ env('SCHOOL_LOGO') }}" alt="School Logo">
            <p><strong>Office of the Registrar</strong></p>
            <p class="email">Email: <a href="mailto:registrar@tau.edu.ng">registrar@tau.edu.ng</a></p>
        </div>
        
        <div class="content">
            <p class="date"><strong>Date:</strong> {{ date('F j, Y', strtotime($created_at)) }}</p>
            <p>Dear <strong>{{ $student_name }}</strong>,</p>
            
            <p class="congratulations"><strong>Congratulations and Welcome to the {{ $programmeCategory->academicSessionSetting->academic_session }}  {{ $programmeCategory->category }} Degree Programme at TAU</strong></p>
            
            <p>On behalf of Thomas Adewumi University, I am excited to offer you a Provisional Admission to the <strong>{{ $programmeCategory->category }} Degree Programme for {{ $programme_name }}</strong> in the {{ $faculty_name }} for the {{ $programmeCategory->academicSessionSetting->academic_session }} academic session. This admission is granted for a full-time study period of <strong>{{ $duration + 1 - $levelId }} Years</strong> and acknowledges your potential as an outstanding candidate. Please note that your JAMB admission letter will soon be available for your acceptance on the Central Admissions Processing System (CAPS).</p>
            
            <p>Kindly visit the university’s portal <a href="{{ env('STUDENT_URL') }}">{{ env('STUDENT_URL') }}</a> to:</p>
            <ul>
                <li>Pay the non-refundable acceptance fee of <strong>N{{ number_format($acceptance_amount/100, 2) }}</strong></li>
                <li>Pay your school fees <strong>(N{{ number_format($school_amount/100, 2) }})</strong> in full or at least a first installment of 40% before resumption</li>
                <li>Prepare for resumption. Please note that the resumption date for {{ $programmeCategory->academicSessionSetting->academic_session }} Academic Session is <strong>{{ date('l, jS F, Y', strtotime($programmeCategory->academicSessionSetting->resumption_date)) }}. Note that any Fees paid is non-refundable.</strong></li>
                <li>Book and pay for your accomondation. The following accomondation facilities are available:</li>
            </ul>
            
            <table>
                <thead>
                    <tr>
                        <th>Occupancy</th>
                        <th>West Campus</th>
                        <th>East Campus</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>10 bed spaces</td>
                        <td>N65,000 (Girls only)</td>
                        <td>N80,000 (Boys only)</td>
                    </tr>
                    <tr>
                        <td>8 bed spaces</td>
                        <td>N90,000 (Boys and Girls)</td>
                        <td>N100,000 (Boys only)</td>
                    </tr>
                    <tr>
                        <td>6 bed spaces</td>
                        <td>N150,000 (Boys and Girls)</td>
                        <td>N150,000 (Boys and Girls)</td>
                    </tr>
                    <tr>
                        <td>4 bed spaces</td>
                        <td>N200,000 (Girls only) TnB</td>
                        <td>N240,000 (Boys and Girls) TnB</td>
                    </tr>
                    <tr>
                        <td>3 bed spaces</td>
                        <td>N250,000 (Girls only) TnB</td>
                        <td>N270,000 (Boys and Girls) TnB</td>
                    </tr>
                    <tr>
                        <td>2 bed spaces</td>
                        <td>N150,000 (Boys only)</td>
                        <td>Not available</td>
                    </tr>
                    <tr>
                        <td>1 bed space</td>
                        <td>N250,000 (Boys only)</td>
                        <td>Not available</td>
                    </tr>
                </tbody>
            </table>
            
            <p>At Thomas Adewumi University, we guarantee you an internationally recognized degree by delivering a world-class education that prepares you for life-long excellence. You can contact the Admission Office at 09053929899 or <a href="mailto:admissions@tau.edu.ng">admissions@tau.edu.ng</a> if you have any questions about your next steps. Accept our congratulations, and we look forward to welcoming you to our beautiful campus!</p>
        </div>
        
        <div class="footer">
            <img src="{{ asset($pageGlobalData->appSetting->registrar_signature) }}" alt="Registrar Signature">
            <p>{{ $pageGlobalData->appSetting->registrar_name }}<br><strong>Registrar</strong></p>
        </div>
        
        {{-- <div class="watermark">
            <img src="{{ env('SCHOOL_LOGO') }}" alt="Watermark Logo">
        </div> --}}
    </div>
</body>
</html>
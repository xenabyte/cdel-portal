@php
    $years = floor($duration);
    $months = round(($duration - $years) * 12);
@endphp
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
            font-size: 12px;
            position: relative;
        }
        .container {
            width: 90%;
            margin: 0 auto;
            padding: 10px;
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
            opacity: 0.1; 
        }
        .header {
            text-align: center;
        }
        .header img {
            width: 30%;
        }
        .header strong {
            font-size: 20px;
            color: #E25041;
        }
        .header .email {
            font-size: 12px;
            color: #000;
        }
        .content {
            line-height: 1.4;
            position: relative;
            z-index: 2;
        }
        .content p {
            margin: 8px 0;
            text-align: justify;
        }
        .content .date {
            text-align: right;
            margin-bottom: 10px;
        }
        .content .congratulations {
            text-align: center;
            font-size: 14px;
            text-transform: uppercase;
        }
        .footer {
            margin-top: 20px;
        }
        .footer img {
            width: 8%;
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
            <p class="date"><strong>Date:</strong> {{ date('F j, Y', strtotime($created_at)) }}</p>
            <p><strong>{{ $student_name }}</strong></p>
            <p>Dear Asamu,</p>

            <p class="congratulations"><strong>OFFER OF PROVISIONAL ADMISSION INTO THE POSTGRADUATE PROGRAMME</strong></p>

            <p>
                I am pleased to inform you that you have been provisionally admitted into the postgraduate programme of {{ env('SCHOOL_NAME') }} for the {{ $programmeCategory->academicSessionSetting->academic_session }} Academic Session.
            </p>

            <p><strong>Programme Details:</strong></p>
            <ul>
                <li><strong>Faculty:</strong>{{ $faculty_name }}</li>
                <li><strong>Department:</strong>{{ $department_name }}</li>
                <li><strong>Degree Offered:</strong> {{ $programmeCategory->code }}in {{ $programme_name }}</li>
                <li><strong>Mode of Study:</strong> Full-time</li>
                <li><strong>Duration of Programme:</strong> {{ $years }} year{{ $years == 1 ? '' : 's' }}@if($months > 0), {{ $months }} month{{ $months == 1 ? '' : 's' }}@endif</li>
            </ul>

            <p>This admission is provisional and subject to the fulfillment of the following conditions:</p>
            <ol>
                <li>Submission of the original and photocopies of all academic credentials for verification.</li>
                <li>Payment of the prescribed acceptance fee of <strong>N{{ number_format($acceptance_amount/100, 2) }}</strong> and 50% school fees via the school portal.</li>
                <li>Meeting any other departmental or institutional requirements.</li>
            </ol>

            <p>
                You are expected to accept this offer within two (2) weeks from the date of this letter by paying the acceptance fee and registering your intention to enroll with the School of Postgraduate Studies.
            </p>

            <p>
                Failure to comply within the stipulated period may result in the withdrawal of this offer.
            </p>

            <p>
                We congratulate you on your successful admission and warmly welcome you to Thomas Adewumi University — a citadel of academic excellence, innovation, and character.
            </p>

            <p>
                For further inquiries or clarification, please contact the Secretary, School of Postgraduate Studies via: <a href="mailto:admissions@tau.edu.ng">admissions@tau.edu.ng</a>
            </p>

            <p>Yours faithfully,</p>
        </div>
        
        <div class="footer">
            <img src="{{ asset($pageGlobalData->appSetting->registrar_signature) }}" alt="Registrar Signature">
            <p>{{ $pageGlobalData->appSetting->registrar_name }}<br><strong>Registrar</strong></p>
        </div>
    </div>
</body>
</html>
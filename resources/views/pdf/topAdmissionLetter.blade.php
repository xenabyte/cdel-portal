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
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: url('{{ env('SCHOOL_LOGO') }}') center center no-repeat;
            background-size: 50%;
            opacity: 0.1; 
        } */
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
        .congratulations {
            text-transform: uppercase;
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
            <p>Dear <strong>{{ $student_name }}</strong>,</p>
            
            <p class="congratulations"><strong>CONGRATULATIONS ON YOUR ADMISSION TO THE {{ $academic_session }} {{ $programmeCategory }} DEGREE PROGRAMME AT THOMAS ADEWUMI UNIVERSITY</strong></p>

            <p>Congratulations! On behalf of {{ env('SCHOOL_NAME') }}, we are pleased to offer you a Provisional Admission into the {{ $programmeCategory }} Degree Programme for the {{ $programme_name }} under the {{ $faculty_name }} for the {{ $academic_session }} academic session.</p>

            <p>This admission is for a full-time study period of two (2) years, consisting of six (6) semesters. We recognize your potential and are excited to welcome you into our academic community.</p>
                        
            <p>Next Steps:</p>
            <ul>
                <ol> Payment of Fees
                    <ul>
                        <li>Acceptance Fee:  <strong>N{{ number_format($acceptance_amount/100, 2) }}</strong></li>
                        <li>School Fees:  <strong>N{{ number_format($school_amount/100, 2) }}</strong> (payable in full or at least a 40% first installment before resumption at any of our study centres)</li>
                        <li>Payments should be made via the University Portal:  <a href="{{ env('STUDENT_URL') }}">{{ env('STUDENT_URL') }}</a></li>
                    </ul>
                </ol>
                <ol> Preparation for Resumption
                    <ul>
                        <li>The official resumption date for the {{ $academic_session }} Academic Session is Monday, 10th March 2025</strong></li>
                        <li>Ensure you have all necessary documents and academic materials ready</strong></li>
                    </ul>
                </ol>
            </ul>   
            
            <p>Why Thomas Adewumi University?<br> At Thomas Adewumi University, we are committed to providing a world-class education that prepares you for a lifetime of excellence. Our internationally recognized degree will give you a competitive edge in the ever-evolving global workforce.</p>
            
            <p>If you have any questions or need further clarification, kindly reach out to the Admissions Office via: 📞 09053929899 📧 <a href="mailto:admissions@tau.edu.ng">admissions@tau.edu.ng</a></p>
            <p>Once again, congratulations! We look forward to welcoming you to any of our study centers and supporting you throughout your academic journey at Thomas Adewumi University.</p>
            <p>Best regards,</p>
                
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
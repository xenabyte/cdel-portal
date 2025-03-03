
<!DOCTYPE html>
<html>
<head>
    <title>Anti Drug Declaration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-size: 12px;
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
            <h1>Course Registration Form</h1>
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
            <h4>Anti-Drug Declaration Form</h4>
        </div>
        <div class="col-md-12">
            <div class="declaration">
                <p>
                    I, <strong>[Your Name]</strong>, having been admitted to the Physics Department of Thomas Adewumi University, 
                    hereby declare that I have been made aware that possessing, consuming, or dealing in narcotic and intoxicating 
                    drugs is an offense punishable by expulsion under Section 10.3, vii of the Student's Handbook of Information and Regulations.
                </p>
                
                <p>
                    In the event of such indulgence or suspicion, I am willing to undergo a medical examination, including blood tests 
                    and urine analysis, as required by the institution.
                </p>
                
                <p>
                    I also commit to reporting any irregular behavior that I observe in relation to the possession, use, sale, or distribution 
                    of alcohol, tobacco, or any psychoactive/psychotropic substances that may occur within the institution or during activities 
                    conducted by any student of the institution.
                </p>
            </div>
        </div>
    </div>
    <br>
    <div class="row text-justify">
        <p>
            <img src="{{ asset($info->signature ) }}" width="10%"> DATE: {{ date('F j, Y') }}<br><br>
        </p>
    </div>
    <div class="row mt-4">
        <div class="col-md-6 text-left">
            <strong>Date Generated:</strong> {{ date('F j, Y') }}
        </div>
    </div>
    
</div>
<div class="watermark"></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

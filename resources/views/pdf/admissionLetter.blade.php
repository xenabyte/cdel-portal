<table style="width: 40%;">
    <tbody>
        <tr>
            <td><img style="text-align:center;" src="{{env('SCHOOL_LOGO')}}" width="40%"></td>
        </tr>
    </tbody>
</table>

<p style='margin:0cm;font-size:14px;font-family:"Calibri",sans-serif;text-align:center;'><strong><span
            style="font-size: 22px; color: rgb(226, 80, 65);">Office of the Registrar</span></strong></p>
<p style='margin:0cm;font-size:14px;font-family:"Calibri",sans-serif;text-align:center;'><strong><span
            style="font-size: 14px; color: rgb(0, 0, 0);">Email: <a
                href="mailto:registrar@tau.edu.ng">registrar@tau.edu.ng</a></span></strong></p>
<p style="text-align: right;"><strong>Date:</strong> {{date('F j, Y', strtotime($created_at))}}</p>

<p>Our Ref:<strong>{{ $applicant_number }}</strong></p>
<p>Dear <strong>{{ $student_name }}</strong>,</p>

<p style="text-align: center;"><strong><span style="font-size: 16px;">Congratulations and Welcome to the {{
            $academic_session }} Undergraduate Degree Programme at TAU</span></strong></p>

<p style="font-family:'Calibri',sans-serif; text-align:justify"> On behalf of Thomas Adewumi University, I am excited to offer you a Provisional Admission to the <strong>{{ $programme_name }}</strong>, in the {{ $faculty_name }} for the {{ $academic_session }} academic session.  This admission is granted for a full-time study period of <strong>{{ $duration + 1 - $levelId  }} Years</strong> and   acknowledges your potential as an outstanding candidate. Please note that your JAMB admission letter will soon be available for your acceptance on the Central Admissions Processing System (CAPS).<br>
    

    Kindly visit the university&rsquo;s portal <a data-fr-linked="true"  href="{{ env('STUDENT_URL')  }}">{{ env('STUDENT_URL')  }}</a> to:<br>
    <ul>
        <li>	Pay the non-refundable acceptance fee of <strong>N{{ number_format($acceptance_amount/100, 2) }}</strong> </li>
        <li>	Pay your school fees <strong>(N{{ number_format($school_amount/100, 2) }})</strong> in full or at least a first installment of 40% before resumption </li>
        <li>	Prepare for resumption. Please note that the resumption date for {{ $pageGlobalData->sessionSetting->admission_session }} Academic Session is <strong>{{date('l, jS F, Y', strtotime($pageGlobalData->sessionSetting->resumption_date))}}</strong></li>
        <li>	Book and pay for your accommodation. The following accommodation facilities are available </li>
    </ul>
    <table style="width: 3.8e+2pt;margin-left:36.0pt;border-collapse:collapse;border:none;">
        <tbody>
            <tr>
                <td style="width: 135pt;border: 1pt solid black;padding: 5pt;vertical-align: top;">
                    <p style='margin:0cm;font-size:16px;font-family:"Calibri",sans-serif;text-align:justify;border:none;'><strong><span style='font-size:15px;font-family:"Times New Roman",serif;'>Occupancy</span></strong></p>
                </td>
                <td style="width: 130.5pt;border-top: 1pt solid black;border-right: 1pt solid black;border-bottom: 1pt solid black;border-image: initial;border-left: none;padding: 5pt;vertical-align: top;">
                    <p style='margin:0cm;font-size:16px;font-family:"Calibri",sans-serif;text-align:justify;border:none;'><strong><span style='font-size:15px;font-family:"Times New Roman",serif;'>West Campus</span></strong></p>
                </td>
                <td style="width: 117pt;border-top: 1pt solid black;border-right: 1pt solid black;border-bottom: 1pt solid black;border-image: initial;border-left: none;padding: 5pt;vertical-align: top;">
                    <p style='margin:0cm;font-size:16px;font-family:"Calibri",sans-serif;text-align:justify;border:none;'><strong><span style='font-size:15px;font-family:"Times New Roman",serif;'>East Campus</span></strong></p>
                </td>
            </tr>
            <tr>
                <td style="width: 135pt;border-right: 1pt solid black;border-bottom: 1pt solid black;border-left: 1pt solid black;border-image: initial;border-top: none;padding: 5pt;vertical-align: top;">
                    <p style='margin:0cm;font-size:16px;font-family:"Calibri",sans-serif;text-align:justify;border:none;'><span style='font-size:15px;font-family:"Times New Roman",serif;'>12 bed spaces</span></p>
                </td>
                <td style="width: 130.5pt;border-top: none;border-left: none;border-bottom: 1pt solid black;border-right: 1pt solid black;padding: 5pt;vertical-align: top;">
                    <p style='margin:0cm;font-size:16px;font-family:"Calibri",sans-serif;text-align:justify;border:none;'><span style='font-size:15px;font-family:"Times New Roman",serif;'>N60,000(Silver)</span></p>
                </td>
                <td style="width: 117pt;border-top: none;border-left: none;border-bottom: 1pt solid black;border-right: 1pt solid black;padding: 5pt;vertical-align: top;">
                    <p style='margin:0cm;font-size:16px;font-family:"Calibri",sans-serif;text-align:justify;'><span style='font-size:15px;font-family:"Times New Roman",serif;'>Not available</span></p>
                </td>
            </tr>
            <tr>
                <td style="width: 135pt;border-right: 1pt solid black;border-bottom: 1pt solid black;border-left: 1pt solid black;border-image: initial;border-top: none;padding: 5pt;vertical-align: top;">
                    <p style='margin:0cm;font-size:16px;font-family:"Calibri",sans-serif;text-align:justify;border:none;'><span style='font-size:15px;font-family:"Times New Roman",serif;'>6 bed spaces</span></p>
                </td>
                <td style="width: 130.5pt;border-top: none;border-left: none;border-bottom: 1pt solid black;border-right: 1pt solid black;padding: 5pt;vertical-align: top;">
                    <p style='margin:0cm;font-size:16px;font-family:"Calibri",sans-serif;text-align:justify;border:none;'><span style='font-size:15px;font-family:"Times New Roman",serif;'>N100,000(Silver)</span></p>
                </td>
                <td style="width: 117pt;border-top: none;border-left: none;border-bottom: 1pt solid black;border-right: 1pt solid black;padding: 5pt;vertical-align: top;">
                    <p style='margin:0cm;font-size:16px;font-family:"Calibri",sans-serif;text-align:justify;'><span style='font-size:15px;font-family:"Times New Roman",serif;'>N150,000(Silver)</span></p>
                </td>
            </tr>
            <tr>
                <td style="width: 135pt;border-right: 1pt solid black;border-bottom: 1pt solid black;border-left: 1pt solid black;border-image: initial;border-top: none;padding: 5pt;vertical-align: top;">
                    <p style='margin:0cm;font-size:16px;font-family:"Calibri",sans-serif;text-align:justify;border:none;'><span style='font-size:15px;font-family:"Times New Roman",serif;'>4 bed spaces</span></p>
                </td>
                <td style="width: 130.5pt;border-top: none;border-left: none;border-bottom: 1pt solid black;border-right: 1pt solid black;padding: 5pt;vertical-align: top;">
                    <p style='margin:0cm;font-size:16px;font-family:"Calibri",sans-serif;text-align:justify;'><span style='font-size:15px;font-family:"Times New Roman",serif;'>N200,000(Gold)</span></p>
                </td>
                <td style="width: 117pt;border-top: none;border-left: none;border-bottom: 1pt solid black;border-right: 1pt solid black;padding: 5pt;vertical-align: top;">
                    <p style='margin:0cm;font-size:16px;font-family:"Calibri",sans-serif;text-align:justify;'><span style='font-size:15px;font-family:"Times New Roman",serif;'>N200,000(Silver)</span></p>
                </td>
            </tr>
            <tr>
                <td style="width: 135pt;border-right: 1pt solid black;border-bottom: 1pt solid black;border-left: 1pt solid black;border-image: initial;border-top: none;padding: 5pt;vertical-align: top;">
                    <p style='margin:0cm;font-size:16px;font-family:"Calibri",sans-serif;text-align:justify;'><span style='font-size:15px;font-family:"Times New Roman",serif;'>3 bed spaces</span></p>
                </td>
                <td style="width: 130.5pt;border-top: none;border-left: none;border-bottom: 1pt solid black;border-right: 1pt solid black;padding: 5pt;vertical-align: top;">
                    <p style='margin:0cm;font-size:16px;font-family:"Calibri",sans-serif;text-align:justify;'><span style='font-size:15px;font-family:"Times New Roman",serif;'>N300,000(Gold)</span></p>
                </td>
                <td style="width: 117pt;border-top: none;border-left: none;border-bottom: 1pt solid black;border-right: 1pt solid black;padding: 5pt;vertical-align: top;">
                    <p style='margin:0cm;font-size:16px;font-family:"Calibri",sans-serif;text-align:justify;'><span style='font-size:15px;font-family:"Times New Roman",serif;'>N500,000(Gold)</span></p>
                </td>
            </tr>
            <tr>
                <td style="width: 135pt;border-right: 1pt solid black;border-bottom: 1pt solid black;border-left: 1pt solid black;border-image: initial;border-top: none;padding: 5pt;vertical-align: top;">
                    <p style='margin:0cm;font-size:16px;font-family:"Calibri",sans-serif;text-align:justify;'><span style='font-size:15px;font-family:"Times New Roman",serif;'>4 bed spaces</span></p>
                </td>
                <td style="width: 130.5pt;border-top: none;border-left: none;border-bottom: 1pt solid black;border-right: 1pt solid black;padding: 5pt;vertical-align: top;">
                    <p style='margin:0cm;font-size:16px;font-family:"Calibri",sans-serif;text-align:justify;'><span style='font-size:15px;font-family:"Times New Roman",serif;'>N150,000(Silver)</span></p>
                </td>
                <td style="width: 117pt;border-top: none;border-left: none;border-bottom: 1pt solid black;border-right: 1pt solid black;padding: 5pt;vertical-align: top;">
                    <p style='margin:0cm;font-size:16px;font-family:"Calibri",sans-serif;text-align:justify;'><span style='font-size:15px;font-family:"Times New Roman",serif;'>N375,000</span></p>
                </td>
            </tr>
            <tr>
                <td style="width: 135pt;border-right: 1pt solid black;border-bottom: 1pt solid black;border-left: 1pt solid black;border-image: initial;border-top: none;padding: 5pt;vertical-align: top;">
                    <p style='margin:0cm;font-size:16px;font-family:"Calibri",sans-serif;text-align:justify;'><span style='font-size:15px;font-family:"Times New Roman",serif;'>2 bed spaces</span></p>
                </td>
                <td style="width: 130.5pt;border-top: none;border-left: none;border-bottom: 1pt solid black;border-right: 1pt solid black;padding: 5pt;vertical-align: top;">
                    <p style='margin:0cm;font-size:16px;font-family:"Calibri",sans-serif;text-align:justify;'><span style='font-size:15px;font-family:"Times New Roman",serif;'>Not available</span></p>
                </td>
                <td style="width: 117pt;border-top: none;border-left: none;border-bottom: 1pt solid black;border-right: 1pt solid black;padding: 5pt;vertical-align: top;">
                    <p style='margin:0cm;font-size:16px;font-family:"Calibri",sans-serif;text-align:justify;'><span style='font-size:15px;font-family:"Times New Roman",serif;'>N750,000 (Gold) <br> N350,000 (Silver)</span></p>
                </td>
            </tr>
        </tbody>
    </table>
</p>

<p style="font-family:'Calibri',sans-serif; text-align:justify">At Thomas Adewumi University, we guarantee you an internationally recognized degree by delivering a world-class education that prepares you for life-long excellence. You can contact the following if you have any questions about your next steps by reaching out to the<strong>Admission Office:</strong> 09053929899, <a data-fr-linked="true" href="mailto:admissions@tau.edu.ng">admissions@tau.edu.ng</a> <br>Accept our congratulations and we look forward to welcoming you to our beautiful campus!</p>
<p style="font-family:'Calibri',sans-serif;">Yours Faithfully,</p>
<p><img src="{{ asset($pageGlobalData->sessionSetting->registrar_signature ) }}" width="10%"></p>
<p style="font-family:'Calibri',sans-serif;">{{ $pageGlobalData->sessionSetting->registrar_name }} <br>
<strong>Registrar</strong></p>
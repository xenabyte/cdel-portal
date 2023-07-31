@extends('mail.layout.mail')

@section('content')
 <!-- Start single column section -->
 <table align="center" style="text-align: center; vertical-align: top; width: 600px; max-width: 600px; background-color: #ffffff;" width="600">
  <tbody>
    <tr>
      <td style="width: 596px; vertical-align: top; padding-left: 30px; padding-right: 30px; padding-top: 30px; padding-bottom: 40px;" width="596">

        <p style="font-size: 15px; line-height: 24px; font-family: 'Helvetica', Arial, sans-serif; font-weight: 400; text-decoration: none; color: #919293; text-align: start;">Dear {{ $applicationData->lastname .' '. $applicationData->othernames }},</p>              

        <p style="font-size: 15px; line-height: 24px; font-family: 'Helvetica', Arial, sans-serif; font-weight: 400; text-decoration: none; color: #919293; text-align: start;">Thank you for applying to {{ env('SCHOOL_NAME') }}! We are thrilled to have you as a potential candidate for the {{ $applicationData->programme->name }} Programme and appreciate your interest in joining our institution.</p> 
        <h1 style="font-size: 20px; line-height: 24px; font-family: 'Helvetica', Arial, sans-serif; font-weight: 600; text-decoration: none; color: #000000; text-align: start;">Application Details</h1>
        <p style="font-size: 15px; line-height: 24px; font-family: 'Helvetica', Arial, sans-serif; font-weight: 400; text-decoration: none; color: #919293; text-align: start;">
          Full Name: {{ $applicationData->lastname .' '. $applicationData->othernames }}<br/>
          Application ID: {{ $applicationData->user_id }}
        </p>              

        <h1 style="font-size: 20px; line-height: 24px; font-family: 'Helvetica', Arial, sans-serif; font-weight: 600; text-decoration: none; color: #000000;">{{ $applicationData->passcode }}</h1>

        <p style="font-size: 15px; line-height: 24px; font-family: 'Helvetica', Arial, sans-serif; font-weight: 400; text-decoration: none; color: #919293; text-align: start;">To access the application portal, please follow these steps:</p> 
        <p style="font-size: 15px; line-height: 24px; font-family: 'Helvetica', Arial, sans-serif; font-weight: 400; text-decoration: none; color: #919293; text-align: start;">
          1. Click the button below, select Complete Application<br/>
          2. Enter your provided email address and the password mentioned above. <br/>
          3. Complete the application form with accurate and relevant information. <br/>
          4. Upload any necessary documents as per the application requirements. <br/>
          5. Review your application thoroughly before submitting.
        </p> 

        <p style="font-size: 15px; line-height: 24px; font-family: 'Helvetica', Arial, sans-serif; font-weight: 400; text-decoration: none; color: #919293;">
          Please remember to keep your password confidential and do not share it with anyone. If you have any concerns about the security of your password or encounter any technical difficulties during the application process, don't hesitate to reach out to our Admissions Office at {{ env('APP_EMAIL') }}

        <p style="font-size: 15px; line-height: 24px; font-family: 'Helvetica', Arial, sans-serif; font-weight: 400; text-decoration: none; color: #919293;">
        </p> 

        <!-- Start button (You can change the background colour by the hex code below) -->
        <a href="{{ env('APP_URL').'/applicant' }}" target="_blank" style="background-color: #000000; font-size: 15px; line-height: 22px; font-family: 'Helvetica', Arial, sans-serif; font-weight: normal; text-decoration: none; padding: 12px 15px; color: #ffffff; border-radius: 5px; display: inline-block; mso-padding-alt: 0;">
            <span style="mso-text-raise: 15pt; color: #ffffff;">Application Portal</span>
        </a>
        <!-- End button here -->

      </td>
    </tr>
  </tbody>
</table>
<!-- End single column section -->

@endsection
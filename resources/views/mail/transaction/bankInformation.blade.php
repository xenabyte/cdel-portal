@extends('mail.layout.mail')

@section('content')
 <!-- Start single column section -->
 <table align="center" style="text-align: center; vertical-align: top; width: 600px; max-width: 600px; background-color: #ffffff;" width="600">
  <tbody>
    <tr>
      <td style="width: 596px; vertical-align: top; padding-left: 30px; padding-right: 30px; padding-top: 30px; padding-bottom: 40px;" width="596">

        <p style="font-size: 15px; line-height: 24px; font-family: 'Helvetica', Arial, sans-serif; font-weight: 400; text-decoration: none; color: #919293; text-align: start;">Dear {{ $userData->lastname .' '. $userData->othernames }},</p>              

        <p style="font-size: 15px; line-height: 24px; font-family: 'Helvetica', Arial, sans-serif; font-weight: 400; text-decoration: none; color: #919293; text-align: start;">Please make Payment to the bank information below</p> 
        <h1 style="font-size: 20px; line-height: 24px; font-family: 'Helvetica', Arial, sans-serif; font-weight: 600; text-decoration: none; color: #000000; text-align: start;">Your Details</h1>
        <p style="font-size: 15px; line-height: 24px; font-family: 'Helvetica', Arial, sans-serif; font-weight: 400; text-decoration: none; color: #919293; text-align: start;">
          Full Name: {{ $userData->lastname .' '. $userData->othernames }}<br/>
          Application ID: {{ $userData->application_id }}
        </p>              

        <h1 style="font-size: 20px; line-height: 24px; font-family: 'Helvetica', Arial, sans-serif; font-weight: 600; text-decoration: none; color: #000000;">₦{{ number_format($userData->amount/100, 2) }}</h1>

        <h1 style="font-size: 20px; line-height: 24px; font-family: 'Helvetica', Arial, sans-serif; font-weight: 600; text-decoration: none; color: #000000; text-align: start;">Bank Details</h1>
        <p style="font-size: 15px; line-height: 24px; font-family: 'Helvetica', Arial, sans-serif; font-weight: 400; text-decoration: none; color: #919293; text-align: start;">
            Bank Name: {{ env('BANK_NAME') }}<br/>
            Bank Account Name: {{ env('BANK_ACCOUNT_NAME') }}<br/>
            Bank Account Number: {{ env('BANK_ACCOUNT_NUMBER') }}
        </p> 

        <p style="font-size: 15px; line-height: 24px; font-family: 'Helvetica', Arial, sans-serif; font-weight: 400; text-decoration: none; color: #919293;">
            Please send proof of payment as an attachment to {{ env('ACCOUNT_EMAIL') }}, including your name, Application ID, and purpose of payment. For any inquiries, you can also call {{ env('ACCOUNT_PHONE') }}.
        </p> 

        <p style="font-size: 15px; line-height: 24px; font-family: 'Helvetica', Arial, sans-serif; font-weight: 400; text-decoration: none; color: #919293;">
            NOTE: PLEASE ENSURE TO VERIFY THE TRANSACTION DETAILS PROPERLY. TRANSFER ONLY TO THE ACCOUNT ABOVE. STUDENTS TAKE RESPONSIBILITY FOR ANY MISPLACEMENT OF FUNDS.
        </p>

      </td>
    </tr>
  </tbody>
</table>
<!-- End single column section -->

@endsection
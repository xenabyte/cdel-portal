@extends('mail.layout.mail')

@section('content')

<tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
  <td class="content-block" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 20px; line-height: 1.5; font-weight: 500; vertical-align: top; margin: 0; padding: 0 0 10px;" valign="top">
      Dear {{ $userData->lastname .' '. $userData->othernames }},
  </td>
</tr>

<tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
  <td class="content-block" style="font-family: 'Roboto', sans-serif; color: #878a99; box-sizing: border-box; line-height: 1.5; font-size: 15px; vertical-align: top; margin: 0; padding: 0 0 10px;" valign="top">
    Please make Payment to the bank information below:
  </td>
</tr>

<tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
  <td class="content-block" style="font-family: 'Roboto', sans-serif; color: #878a99; box-sizing: border-box; line-height: 1.5; font-size: 15px; vertical-align: top; margin: 0; padding: 0 0 24px;" valign="top">
      <strong>Your Details:</strong> 
      <p><strong>Full Name: </strong>  {{ $userData->lastname .' '. $userData->othernames }}<br/>
          <strong>Application ID:</strong> {{ $userData->application_id }}</p>
  </td>
</tr>

<tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
  <td class="content-block" style="text-align:center; font-family: 'Roboto', sans-serif; color: #878a99; box-sizing: border-box; line-height: 1.5; font-size: 15px; vertical-align: top; margin: 0; padding: 0 0 10px;" valign="top">
      <strong>₦{{ number_format($userData->amount/100, 2) }}</strong>
  </td>
</tr>

<tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
  <td class="content-block" style="font-family: 'Roboto', sans-serif; color: #878a99; box-sizing: border-box; line-height: 1.5; font-size: 15px; vertical-align: top; margin: 0; padding: 0 0 10px;" valign="top">
    Bank Details:
  </td>
</tr>

<tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
  <td class="content-block" style="font-family: 'Roboto', sans-serif; color: #878a99; box-sizing: border-box; line-height: 1.5; font-size: 15px; vertical-align: top; margin: 0; padding: 0 0 24px;" valign="top">
    Bank Name: {{ env('BANK_NAME') }}<br/>
    Bank Account Name: {{ env('BANK_ACCOUNT_NAME') }}<br/>
    Bank Account Number: {{ env('BANK_ACCOUNT_NUMBER') }}
  </td>
</tr>

<tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
  <td class="content-block" style="font-family: 'Roboto', sans-serif; color: #878a99; box-sizing: border-box; line-height: 1.5; font-size: 15px; vertical-align: top; margin: 0; padding: 0 0 10px;" valign="top">
    Please send proof of payment as an attachment to {{ env('ACCOUNT_EMAIL') }}, including your name, Application ID, and purpose of payment. For any inquiries, you can also call {{ env('ACCOUNT_PHONE') }}.
  </td>
</tr>

<tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
  <td class="content-block" style="font-family: 'Roboto', sans-serif; color: #878a99; box-sizing: border-box; line-height: 1.5; font-size: 15px; vertical-align: top; margin: 0; padding: 0 0 10px;" valign="top">
    NOTE: PLEASE ENSURE TO VERIFY THE TRANSACTION DETAILS PROPERLY. TRANSFER ONLY TO THE ACCOUNT ABOVE. STUDENTS TAKE RESPONSIBILITY FOR ANY MISPLACEMENT OF FUNDS.
  </td>
</tr>
@endsection
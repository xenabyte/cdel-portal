@extends('mail.layout.mail')

@section('content')
 <!-- Start single column section -->
 <table align="center" style="text-align: center; vertical-align: top; width: 600px; max-width: 600px; background-color: #ffffff;" width="600">
  <tbody>
    <tr>
      <td style="width: 596px; vertical-align: top; padding-left: 30px; padding-right: 30px; padding-top: 30px; padding-bottom: 40px;" width="596">

        <p style="font-size: 15px; line-height: 24px; font-family: 'Helvetica', Arial, sans-serif; font-weight: 400; text-decoration: none; color: #919293; text-align: start;">Dear {{ $receiverName}},</p>              

        <p style="font-size: 15px; line-height: 24px; font-family: 'Helvetica', Arial, sans-serif; font-weight: 400; text-decoration: none; color: #919293; text-align: start;">You have a message from {{ $senderName }}</p>
        
        <p style="font-size: 15px; line-height: 24px; font-family: 'Helvetica', Arial, sans-serif; font-weight: 400; text-decoration: none; color: #919293; text-align: start;">
        
           @php
                echo $messageBody;
           @endphp
        
        </p>

      </td>
    </tr>
  </tbody>
</table>
<!-- End single column section -->

@endsection
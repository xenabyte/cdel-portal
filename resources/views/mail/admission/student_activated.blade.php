@extends('mail.layout.mail')

@section('content')

<tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
  <td class="content-block" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 20px; line-height: 1.5; font-weight: 500; vertical-align: top; margin: 0; padding: 0 0 10px;" valign="top">
      Dear {{ $applicationData->applicant->lastname .' '. $applicationData->applicant->->othernames }},
  </td>
</tr>
<tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
  <td class="content-block" style="font-family: 'Roboto', sans-serif; color: #878a99; box-sizing: border-box; line-height: 1.5; font-size: 15px; vertical-align: top; margin: 0; padding: 0 0 10px;" valign="top">
    We are excited to announce that you now have access to your official institutional email address and your unique matriculation number. These essential tools will play a pivotal role in your academic journey at {{ env('SCHOOL_NAME') }}.
  </td>
</tr>

<tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
  <td class="content-block" style="font-family: 'Roboto', sans-serif; color: #878a99; box-sizing: border-box; line-height: 1.5; font-size: 15px; vertical-align: top; margin: 0; padding: 0 0 24px;" valign="top">
      <p><strong>Your Institutional Email Address: </strong> {{ $applicationData->email }}<br/>
        <strong>Your Institutional Email Password: </strong> {{ $applicationData->applicant->passcode }}<br/>
          <strong>Your Matriculation Number:</strong> {{ $applicationData->matric_number }}</p>
  </td>
</tr>


<tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
    <td class="content-block" style="font-family: 'Roboto', sans-serif; color: #878a99; box-sizing: border-box; line-height: 1.5; font-size: 15px; vertical-align: top; margin: 0; padding: 0 0 24px;" valign="top">
        <strong>Institutional Email: </strong> Your {{ $applicationData->email }} email address will serve as your primary mode of communication with the university. Please use it for all official correspondence with faculty, staff, and fellow students. It's your key to accessing important updates, class announcements, and academic resources.
    </td>
</tr>

<tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
    <td class="content-block" style="font-family: 'Roboto', sans-serif; color: #878a99; box-sizing: border-box; line-height: 1.5; font-size: 15px; vertical-align: top; margin: 0; padding: 0 0 24px;" valign="top">
        <strong>Matriculation Number: </strong>  Your unique matriculation number is your identification within our institution. It will be used for various administrative purposes, including course registration, examinations, and student records. Please keep it safe and provide it whenever requested.
    </td>
</tr>

<tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
    <td class="content-block" style="font-family: 'Roboto', sans-serif; color: #878a99; box-sizing: border-box; line-height: 1.5; font-size: 15px; vertical-align: top; margin: 0; padding: 0 0 24px;" valign="top">
        Furthermore, starting today, you will use your institutional email address to access your student portal. This secure portal provides you with access to course materials, schedules, grades, and various student services. You can log in with the following credentials:
        <p><strong>Portal Username (Email): </strong> {{ $applicationData->email }}<br/>
            <p><strong>Portal Password:</strong> Your existing password. If you have forgotten your password, you can easily reset it by using the "Forgot Password" option on the login page of the portal.</p>
    </td>
  </tr>

<tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; border-top: 1px solid #e9ebec;">
  <td class="content-block" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0; padding-top: 15px" valign="top">
      <div style="display: flex; align-items: center;">
      </div>
  </td>
</tr>
@endsection
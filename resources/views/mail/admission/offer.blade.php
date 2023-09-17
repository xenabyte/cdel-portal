@extends('mail.layout.mail')

@section('content')

<tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
  <td class="content-block" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 20px; line-height: 1.5; font-weight: 500; vertical-align: top; margin: 0; padding: 0 0 10px;" valign="top">
      Dear {{ $applicationData->applicant->lastname .' '. $applicationData->applicant->othernames }},
  </td>
</tr>
<tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
  <td class="content-block" style="font-family: 'Roboto', sans-serif; color: #878a99; box-sizing: border-box; line-height: 1.5; font-size: 15px; vertical-align: top; margin: 0; padding: 0 0 10px;" valign="top">
        We are thrilled to extend our warmest congratulations on your successful admission to <strong>{{ $applicationData->programme->name }}</strong>. Your dedication and commitment have earned you a place in our esteemed institution, and we are excited to have you as a part of our academic community.
  </td>
</tr>

<tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
    <td class="content-block" style="font-family: 'Roboto', sans-serif; color: #878a99; box-sizing: border-box; line-height: 1.5; font-size: 15px; vertical-align: top; margin: 0; padding: 0 0 10px;" valign="top">
        Your official admission letter is attached, providing crucial enrollment details. Please take a moment to review it:Your official admission letter is attached, providing crucial enrollment details. Please take a moment to review it    </td>
  </tr>

<tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
  <td class="content-block" style="font-family: 'Roboto', sans-serif; color: #878a99; box-sizing: border-box; line-height: 1.5; font-size: 15px; vertical-align: top; margin: 0; padding: 0 0 24px;" valign="top">
      <strong>Login Credentials:</strong> 
      <p><strong>Email: </strong> {{ $applicationData->email }}<br/>
          <strong>Password:</strong> {{ $applicationData->passcode }}</p>
  </td>
</tr>


<tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
    <td class="content-block" style="font-family: 'Roboto', sans-serif; color: #878a99; box-sizing: border-box; line-height: 1.5; font-size: 15px; vertical-align: top; margin: 0; padding: 0 0 24px;" valign="top">
        Upon making a payment of at least 50% of your school fees, you will become eligible to receive your official institutional email address. This email address will serve as a vital communication tool throughout your time at {{ env('SCHOOL_NAME')}}.Upon making a payment of at least 50% of your school fees, you will become eligible to receive your official institutional email address. This email address will serve as a vital communication tool throughout your time at {{ env('SCHOOL_NAME') }} and will be used to login to your portal.
    </td>
</tr>

<tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
  <td class="content-block" style="font-family: 'Roboto', sans-serif; color: #878a99; box-sizing: border-box; line-height: 1.5; font-size: 15px; vertical-align: top; margin: 0; padding: 0 0 10px;" valign="top">
    Now, it's time to access your Student Portal. Click the button below to get started:  </td>
</tr>

<tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
  <td class="content-block" itemprop="handler" itemscope="" itemtype="http://schema.org/HttpActionHandler" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 24px;" valign="top">
      <a href="{{ env('STUDENT_URL') }}/login" itemprop="url" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: .8125rem;font-weight: 400; color: #FFF; text-decoration: none; text-align: center; cursor: pointer; display: inline-block; border-radius: .25rem; text-transform: capitalize; background-color: #3bad71; margin: 0; border-color: #3bad71; border-style: solid; border-width: 1px; padding: .5rem .9rem;box-shadow: 0 3px 3px rgba(56,65,74,0.1);" onmouseover="this.style.background='#099885'" onmouseout="this.style.background='#3bad71'">Assess Student Portal →</a>
  </td>
</tr>

<tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; border-top: 1px solid #e9ebec;">
  <td class="content-block" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0; padding-top: 15px" valign="top">
      <div style="display: flex; align-items: center;">
      </div>
  </td>
</tr>
@endsection
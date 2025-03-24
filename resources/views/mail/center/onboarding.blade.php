@extends('mail.layout.mail')

@section('content')


<tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
    <td class="content-block" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 20px; line-height: 1.5; font-weight: 500; vertical-align: top; margin: 0; padding: 0 0 10px;" valign="top">
        Dear {{ $studyCenter->name }},
    </td>
  </tr>
  
  <tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
    <td class="content-block" style="font-family: 'Roboto', sans-serif; color: #878a99; box-sizing: border-box; line-height: 1.5; font-size: 15px; vertical-align: top; margin: 0; padding: 0 0 10px;" valign="top">
        A new study center has been successfully registered on the TAU platform. Below are the details:
    </td>
  </tr>
  
  <tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
    <td class="content-block" style="font-family: 'Roboto', sans-serif; color: #878a99; box-sizing: border-box; line-height: 1.5; font-size: 15px; vertical-align: top; margin: 0; padding: 0 0 24px;" valign="top">
        <strong>Study Center Details:</strong> 
        <p><strong>Center Name: </strong> {{ $studyCenter->center_name }}<br/>
        <strong>Address: </strong> {{ $studyCenter->address }}<br/>
        <strong>Email: </strong> {{ $studyCenter->email }}<br/>
        <strong>Phone Number: </strong> {{ $studyCenter->phone_number }}<br/></p>
    </td>
  </tr>

  <tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
    <td class="content-block" style="font-family: 'Roboto', sans-serif; color: #878a99; box-sizing: border-box; line-height: 1.5; font-size: 15px; vertical-align: top; margin: 0; padding: 0 0 24px;" valign="top">
        <strong>Admin Login Details:</strong> 
        <p>
        <strong>Email: </strong> {{ $studyCenter->email }}<br/>
        <strong>Password: </strong> {{ $studyCenter->view_password }}</p>
    </td>
  </tr>

  <tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
    <td class="content-block" itemprop="handler" itemscope="" itemtype="http://schema.org/HttpActionHandler" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 24px;" valign="top">
        <a href="{{ env('STUDY_CENTER_URL') }}" itemprop="url" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: .8125rem;font-weight: 400; color: #FFF; text-decoration: none; text-align: center; cursor: pointer; display: inline-block; border-radius: .25rem; text-transform: capitalize; background-color: #3bad71; margin: 0; border-color: #3bad71; border-style: solid; border-width: 1px; padding: .5rem .9rem;box-shadow: 0 3px 3px rgba(56,65,74,0.1);" onmouseover="this.style.background='#099885'" onmouseout="this.style.background='#3bad71'">Study Center Admin Login →</a>
    </td>
  </tr>


  <tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
    <td class="content-block" style="font-family: 'Roboto', sans-serif; color: #878a99; box-sizing: border-box; line-height: 1.5; font-size: 15px; vertical-align: top; margin: 0; padding: 0 0 10px;" valign="top">
        Please log in to the admin dashboard to review the study center’s details and take necessary actions.
  </tr>

@endsection
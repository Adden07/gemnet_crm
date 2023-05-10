@extends('emails.master')
@section('email_title', 'Reset Your Password')
@section('content')
<table style="width: 700px; margin: 0 auto; border-collapse: collapse;">
    <tr>
        <td bgcolor="#ffffff" align="left" style="color: #666666; font-family: 'Roboto', Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 400; line-height: 1.6;">Hi {{ $email_data['name'] }},</td>
    </tr>
    <tr>
        <td>
            <x-emails.simple_paragraph>We have received a password reset request for your account.</x-emails.simple_paragraph>
        </td>
    </tr>
    <tr>
        <td align="center" style="padding: 10px">
            <x-emails.button margin="0" link="{!! route('password.reset',  $email_data['token']) !!}" title="Click Here To Reset"></x-emails.button>
        </td>
    </tr>
    <tr>
        <td>
            <x-emails.simple_paragraph>If you did not request a password reset, no further action is required.</x-emails.simple_paragraph>
            <x-emails.simple_paragraph>If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:</x-emails.simple_paragraph>
            <x-emails.a_tag link="{!! route('password.reset', $email_data['token']) !!}" title="{!! route('password.reset', $email_data['token']) !!}" />
        </td>
    </tr>
</table>
@endsection
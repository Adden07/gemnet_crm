@extends('emails.master')
@section('email_title', 'Account Blocked')
@section('content')
<table style="width: 700px; margin: 0 auto; border-collapse: collapse;">
    <tr>
        <td align="left" style="color: #666666; font-family: 'Roboto', Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 400; line-height: 1.6;">Hi {{ $email_data['name'] }},</td>
    </tr>
    <tr>
        <td>
            <x-emails.simple_paragraph>Your account have been blocked due to your violations against our marketplace.</x-emails.simple_paragraph>
        </td>
    </tr>
</table>
@endsection
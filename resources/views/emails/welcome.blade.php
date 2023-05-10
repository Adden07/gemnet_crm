@extends('emails.master')
@section('content')
<tr>
    <td bgcolor="#142440" align="center" style="padding: 0px 10px 0px 10px;">
        <table border="0" cellpadding="0" cellspacing="0" width="750" >
            <tr>
                <td bgcolor="#ffffff" align="center" valign="top" style="padding: 40px 20px 20px 20px; border-radius: 4px 4px 0px 0px; color: #111111; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 48px; font-weight: 400; letter-spacing: 3px; line-height: 48px;">
                    <h1 style="font-size: 32px; font-weight: 400; margin: 0;">Welcome to Transport Toolkit</h1>
                </td>
            </tr>
        </table>
    </td>
</tr>
<!-- COPY BLOCK -->
<tr>
    <td bgcolor="#eeeeee" align="center" style="padding: 0px 10px 0px 10px;">
        <table border="0" cellpadding="0" cellspacing="0" width="750" >
            <!-- COPY -->
            <tr>
                <td bgcolor="#ffffff" align="left" style="padding: 20px 30px 0px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;" >
                    <p style="margin: 0;"><strong>Hello <span style="color: #142440">{!! ucwords($email_data['first_name'].' '.$email_data['last_name']) !!}</span>, </strong></p>
                    <p style="margin: 15px 0;">Welcome to our site we are very happy to see you sign up. Now you can access all of our tool from the system.</p>
                </td>
            </tr>
            <!-- BULLETPROOF BUTTON -->
            <tr>
                <td bgcolor="#ffffff" align="left">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td bgcolor="#ffffff" align="center" style="padding: 10px 30px 40px 30px;">
                        <table border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td align="center" style="border-radius: 3px;" bgcolor="#34ccff"><a href="{!! url('/') !!}" target="_blank" style="font-size: 20px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; text-decoration: none; color: #ffffff; text-decoration: none; padding: 15px 25px; border-radius: 2px; border: 1px solid #34ccff; display: inline-block;">Click Here To Go To Site</a></td>
                            </tr>
                        </table>
                        </td>
                    </tr>
                    </table>
                </td>
            </tr>
        </table>
    </td>
</tr>
@endsection

@section('browser-view')
<p style="margin: 0;">You received this email because you just signed up for a new account. If it looks weird, <a href="{!! route('sent_email', ['email_id' => @$email_data['email_id']]) !!}" target="_blank" style="color: #111111; font-weight: 700;">view it in your browser</a>.</p>
@endsection
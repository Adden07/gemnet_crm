@extends('emails.master')
@section('email_title', 'Violation Report')
@section('content')
<table style="width: 700px; margin: 0 auto; border-collapse: collapse;">
    <tr>
        <td bgcolor="#ffffff" align="left" style="color: #666666; font-family: 'Roboto', Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 400; line-height: 1.6;">Hi {{ $email_data['name'] }},</td>
    </tr>
    <tr>
        <td>
            <x-emails.simple_paragraph>A new complaint has been filed. Please review the issue below and make sure to check the status of the complaint.</x-emails.simple_paragraph>
        </td>
    </tr>
    <tr>
        <td align="center" style="padding: 0">
            <x-emails.button margin="0 0 20px" link="{{ route('admin.violations') }}" title="Check Status of Violation"></x-emails.button>
        </td>
    </tr>
    <tr>
        <td style="border-top: 1px solid #aaa;">
            <table cellspacing="0" cellpadding="0" style="margin: 20px auto 20px; border-collapse: collapse;" align="left">
                <tr>
                    <th align="left" style="width: 250px;">
                        <x-emails.simple_paragraph margin="0 0 10px" color="#999">COMPLAIN BY:</x-emails.simple_paragraph>
                    </th>
                    <td>
                        <x-emails.simple_paragraph margin="0 0 10px">{{$email_data['complainer']}}</x-emails.simple_paragraph>
                    </td>
                </tr>
                <tr>
                    <th align="left" style="width: 250px;">
                        <x-emails.simple_paragraph margin="0 0 10px" color="#999">COMPLAIN AGAINST:</x-emails.simple_paragraph>
                    </th>
                    <td>
                        <x-emails.simple_paragraph margin="0 0 10px">{{$email_data['complaint_against']}}</x-emails.simple_paragraph>
                    </td>
                </tr>
                <tr>
                    <th align="left" style="width: 250px;">
                        <x-emails.simple_paragraph margin="0 0 10px" color="#999">REPORTED AT:</x-emails.simple_paragraph>
                    </th>
                    <td>
                        <x-emails.simple_paragraph margin="0 0 10px">{{$email_data['complained_at']}}</x-emails.simple_paragraph>
                    </td>
                </tr>
                <tr>
                    <th align="left" style="width: 250px;">
                        <x-emails.simple_paragraph margin="0 0 10px" color="#999">TYPE OF COMPLAIN:</x-emails.simple_paragraph>
                    </th>
                    <td>
                        <x-emails.simple_paragraph margin="0 0 10px">{{$email_data['complained_type']}}</x-emails.simple_paragraph>
                    </td>
                </tr>
                <tr>
                    <th align="left" style="width: 250px;">
                        <x-emails.simple_paragraph margin="0 0 10px" color="#999">COMPLAIN ISSUES:</x-emails.simple_paragraph>
                    </th>
                    <td>
                        <x-emails.simple_paragraph margin="0 0 10px">{{ implode(', ', $email_data['complaint']) }}</x-emails.simple_paragraph>
                    </td>
                </tr>
                <tr>
                    <th align="left" style="width: 250px;">
                        <x-emails.simple_paragraph margin="0 0 10px" color="#999">DETAILS:</x-emails.simple_paragraph>
                    </th>
                    <td>
                        <x-emails.simple_paragraph margin="0 0 10px">{{$email_data['complain']}}</x-emails.simple_paragraph>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
@endsection
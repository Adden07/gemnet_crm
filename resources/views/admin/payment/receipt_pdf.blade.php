
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print</title>
    <style>
        *{
            font-family: sans-serif;
            
        }
    </style>
</head>
<body>
    <div style="width: 80mm; border: 1px solid #000; margin: 0 auto; padding: 0 10px;">
        <img src="{{ asset('admin_assets/images/receipt_logo.png') }}" alt="Logo" style="width: 50%; margin: 0 auto; display: block;">
        <p style="text-align: center; font-family: sans-serif; font-weight: bold;margin-top: 5px;">
            {{ @$edit_setting->address }} <br>
           {{ @$edit_setting->mobile }} {{ @$edit_setting->email }}
        </p>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td>Date {{ date('d/m/Y H:i:s') }}</td>
            </tr>
            <tr>
                <td>Receiver  ID: {{ auth()->user()->username }}</td>
            </tr>
            <tr>
                <td colspan="2">RCPT# GES/REC2305-0013</td>
            </tr>
        </table>
        <hr style="border: 1px dashed #000; margin-bottom: 0;">
        <hr style="border: 1px dashed #000; margin-top: 2px;">
        <div style="text-align: center;">
            <h3>Cash Payment Receipt</h3>
        </div>

        <p>
            Customer ID: {{ $data->receiver->c_id }} <br>
            Customer Name: {{ $data->receiver->name }}<br>
            Username: {{ $data->receiver->username }}<br>
            Contact: {{ $data->receiver->mobile }}
        </p>

        <hr style="border: 1px dotted #000; margin-bottom: 0;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="text-align: right; max-width: 150px;width: 150px;">Recieved</td>
                <td style="text-align: right;">{{ $data->amount }}</td>
            </tr>
            <tr>
                <td style="text-align: right;">Current Balance</td>
                <td style="text-align: right;">{{ $data->new_balance }}</td>
            </tr>
        </table>
        <hr style="border: 2px solid #000; margin-bottom: 0;">
        <br>
        <p style="text-align: center; font-style: italic;">Thank you for choosing us</p>
    </div>
</body>
<script>
        window.onload = function() {
            window.print();
        };
</script>
</html>
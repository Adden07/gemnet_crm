<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>
    <div class="row">
        <div class="col-lg-12">
            <h4>Username : {{ $user_data->username }} -- Name : {{ $user_data->name }} -- Current Package : {{ $user_data->packages->name }} -- Expiration : {{ date('d-M-Y', strtotime($user_data->current_expiration_date)) }}</h4>
            <table class="table table-bordered" id="online_users">
                <thead>
                    <tr>
                        <th width="20">#</th>
                        <th>DateTime</th>
                        <th>Payment</th>
                        <th>Online Transaction</th>
                        <th>Type</th>
                        <th>Invoice</th>
                        <th>Total</th>
                </thead>
                <tbody>
                    @php
                        $invoice = 0;
                        $payment = 0;
                        $total   = 0;
                    @endphp
                        @foreach($payments->concat($invoices)->sortBy('created_at') AS $key=>$data)
                            <tr>
                                <td>
                                    {{ $loop->iteration }}
                                </td>
                                <td>{{ date('d-M-Y H:i:s', strtotime($data->created_at)) }}</td>
                                <td>@isset($data->amount) {{ 'RS:'.$data->amount }} @endisset</td>
                                <td>{{ @$data->online_transaction }}</td>
                                <td>@if(isset($data->amount)) {{ $data->type }} @endif</td>
                                <td>@isset($data->total) {{ 'RS:'.$data->total }} @endisset</td>
                                @php $total += (isset($data->total)) ? -$data->total : $data->amount  @endphp
                                <td>{{ 'Rs:'.$total }}</td>
                            </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
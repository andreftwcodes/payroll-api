<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        * {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 15px;
        }
        .print-area {
            margin: 0 50px;
        }

        table, td, th {  
            border: 1px solid #000000;
            text-align: left;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        td {
            padding: 3px;
            padding-left: 10px;
            padding-right: 10px;
        }

        table tr td:nth-child(n+2) {
            text-align: right;
        }

        .less-child > td {
            padding-left: 30px;
        }

        .column {
            float: left;
            width: 50%;
        }

        /* Clear floats after the columns */
        .row:after {
            content: "";
            display: table;
            clear: both;
        }
        .heading {
            text-align: center;
        }
        .heading p {
            margin-top: 0em;
            margin-bottom: 0em;
        }
    </style>
    <title>Payslip</title>
</head>
<body>
    <div class="print-area">
        <div class="heading">
            <p>{{ $payslip['locale'] }}</p>
            <p><strong>PAY SLIP</strong></p>
        </div>
        <div class="row">
            <div class="column">
                <p><strong>Name:</strong> {{ $payslip['fullname'] }}</p>
                <p><strong>Period:</strong> {{ $payslip['period'] }}</p>
            </div>
            <div class="column">
                <p align="right"><strong>Date:</strong> {{ $payslip['date_issued'] }}</p>
            </div>
        </div>
        <table>
            <tr>
                <td><strong>Basic Rate</strong></td>
                <td></td>
                <td><strong>{{ $payslip['basic_rate'] }}</strong></td>
                <td></td>
            </tr>
            <tr>
                <td>Overtime | Hours</td>
                <td>{{ $payslip['overtime']['hours'] }}</td>
                <td>{{ $payslip['overtime']['amount'] }}</td>
                <td></td>
            </tr>
            <tr>
                <td>Undertime | Hours</td>
                <td>- {{ $payslip['undertime']['hours'] }}</td>
                <td>- {{ $payslip['undertime']['amount'] }}</td>
                <td></td>
            </tr>
            <tr>
                <td><strong>GROSS SALARY</strong></td>
                <td></td>
                <td></td>
                <td><strong>{{ $payslip['gross_pay'] }}</strong></td>
            </tr>
            <tr>
                <td>Less:</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @foreach ($payslip['less'] as $item)
                <tr class="less-child">
                    <td>{{ $item['name'] }}</td>
                    <td></td>
                    <td>{{ $item['amount'] }}</td>
                    <td></td>
                </tr>
            @endforeach
            <tr>
                <td><strong>Total Deductions</strong></td>
                <td></td>
                <td></td>
                <td>{{ $payslip['total_deductions'] }}</td>
            </tr>
            <tr>
                <td><strong>Take Home Pay</strong></td>
                <td></td>
                <td></td>
                <td><strong>{{ $payslip['net_pay'] }}</strong></td>
            </tr>
        </table>
    </div>
</body>
</html>
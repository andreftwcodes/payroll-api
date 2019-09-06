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

        td, th {
            padding: 3px;
            padding-left: 10px;
            padding-right: 10px;
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
    <title>Timesheet</title>
</head>
<body>
    <div class="print-area">
        <div class="heading">
            <p><strong>Timesheet</strong></p>
        </div>
        <div class="row">
            <div class="column">
            <p><strong>Name:</strong> {{ $timesheet->fullname }}</p>
            <p><strong>Period:</strong> {{ $timesheet->period }}</p>
            </div>
            <div class="column">
                <p align="right"><strong>Total Hours:</strong> {{ $timesheet->total_hours }}</p>
            </div>
        </div>
        <table>
            <tbody>
                <tr>
                    <th>Date</th>
                    <th>Locale</th>
                    <th>Hours</th>
                    <th>Remarks</th>
                </tr>
                @foreach ($timesheet->attendances as $attendance)
                <tr>
                    <td>{{ $attendance->date }}</td>
                    <td>{{ $attendance->locale->name }}</td>
                    <td>{{ $attendance->hours_dsp }}</td>
                    <td>{{ $attendance->remarks }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
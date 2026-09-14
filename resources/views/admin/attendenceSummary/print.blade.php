@extends('admin.layouts.masterPrint')

@section('content')
<table id="report-header">
    <tr>
        <td>{{ $title }}</td>
    </tr>
    <tr>
        <td style="text-transform: uppercase">Month : {{ date('F Y', strtotime($monthYear)) }} </td>
    </tr>
</table>

<div id="pad-bottom"></div>
<table id="report-table">
    <thead>
        <tr>
            <th width="20px">SL</th>
            <th>Employee Code</th>
            <th style="white-space: nowrap">Employee Name</th>
            <th>Present In Time</th>
            <th>Present In Late</th>
            <th>Absent</th>
            <th>Leave</th>
            <th>Holiday</th>
            <th>Total</th>
        </tr>
    </thead>

    <tbody>
        @foreach($monthly_attendance as $key => $value)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $value['staff_code'] }}</td>
            <td>{{ $value['staff_name'] }}</td>
            <?php
            $leaves = 0;
            $attends_in_time = 0;
            $attends_late_time = 0;
            $absents = 0;
            $holidays = 0;
            ?>
            @foreach($value['monthly_staff'] as $key => $attendence)
            <?php
            if (date('l', strtotime($attendence['date'])) != 'Friday') {
                if ($attendence['attendence'] == '') {
                    if ($attendence['leave'] != '') {
//                        echo '<td style="color: yellow">V</td>';
                        $leaves += 1;
                    } else {
                        $absents += 1;
                    }
                } elseif ($attendence['attendence'] != '') {
                    if ($attendence['attendence']->in_time <= "09:30:00") {
                        $attends_in_time += 1;
                    } elseif ($attendence['attendence']->in_time > "09:30:00") {
                        $attends_late_time += 1;
                    }
                }
            } else {
                $holidays += 1;
            }
            ?>
            @endforeach
            <td>{{ $attends_in_time }}</td>
            <td>{{ $attends_late_time }}</td>
            <td>{{ $absents }}</td>
            <td>{{ $leaves }}</td>
            <td>{{ $holidays }}</td>
            <td>{{ $attends_in_time + $attends_late_time + $leaves + $absents + $holidays }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>

    </tfoot>
</table>

<div id="pad-bottom"></div>

<div class="row">
    <div class="col-md-12 text-right">
        <?php
        date_default_timezone_set("Asia/Dhaka");
        ?>
        <p>Print Date & Time : <?php echo date("d-m-Y h:i:sa"); ?></p>
    </div>
</div>

@endsection

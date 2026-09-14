@extends('admin.layouts.masterPrint')

@section('content')
<table id="report-header">
    <tr>
        <td>Monthly Attendence Sheet</td>
    </tr>
    <tr>
        <td style="text-transform: uppercase">Month : {{ date('F Y', strtotime($monthYear)) }} </td>
    </tr>
</table>

<div id="pad-bottom"></div>
<table class="attendence_status" width='100%'>
    <tr>
        <td>Attend: P</td>
        <td>Late: L</td>
        <td>Leave: V</td>
        <td>Absent: A</td>
        <td>Holiday: H</td>
    </tr>
</table>
<table id="report-table">
    <thead>
        <tr>
            <th width="20px">SL</th>
            <th>Employee Code</th>
            <th style="white-space: nowrap">Employee Name</th>
            <?php
            $num_of_days = cal_days_in_month(CAL_GREGORIAN, @$month, @$year);
            for ($i = 1; $i <= $num_of_days; $i++) {
                $date = str_pad($i, 2, '0', STR_PAD_LEFT);
                ?>
                <th>{{ $date }}</th>
            <?php } ?>
        </tr>
    </thead>

    <tbody>
        @foreach($monthly_attendance as $key => $value)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $value['staff_code'] }}</td>
            <td>{{ $value['staff_name'] }}</td>
            @foreach($value['monthly_staff'] as $key => $attendence)

            <?php
            if (date('l', strtotime($attendence['date'])) != 'Friday') {
                if ($attendence['attendence'] == '') {
                    if ($attendence['leave'] != '') {
                        echo '<td style="color: yellow">V</td>';
                    } else {
                        echo '<td style="color: red">A</td>';
                    }
                } elseif ($attendence['attendence'] != '') {
                    if ($attendence['attendence']->in_time <= "09:30:00") {
                        echo '<td style="color: green">P</td>';
                    } elseif ($attendence['attendence']->in_time > "09:30:00") {
                        echo '<td style="color: blue">L</td>';
                    }
                }
            } else {
                echo '<td style="color: purple;">H</td>';
            }
            ?>

            @endforeach
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

<style>
    .attendence_status{
        font-size: 11px; 
        background-color: green;
        margin-bottom: 5px;
    }
    .attendence_status td{
        color: white !important;
        font-weight: bold;
        text-align: center;
    }
</style>
@endsection

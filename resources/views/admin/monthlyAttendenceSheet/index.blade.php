
@extends('admin.layouts.masterReport')

@section('search_card_body')
<input type="hidden" name="print" value="print">
<div class="row d-flex justify-content-center">
    <div class="col-md-4">
        <label for="date">Date</label>
        <div class="form-group">
            <input type="month" class="form-control" name="month" value="{{ !empty($monthYear) ? $monthYear : date('Y-m') }}">
        </div>
    </div>

</div>
@endsection

@section('print_card_header')

<input type="hidden" id="month" name="month" value="{{ @$monthYear }}">

<input type="hidden" id="print_value" name="print" value="{{ @$print }}">
@endsection

@section('print_card_body')
<table class="attendence_status" width='100%'>
    <tr>
        <td>Attend: P</td>
        <td>Late: L</td>
        <td>Leave: V</td>
        <td>Absent: A</td>
        <td>Holiday: H</td>
    </tr>
</table>
@if(!empty($monthYear))
<table id="" name="productList" class="table table-bordered table-sm">
    <thead>
        <tr class="bg-success">
            <th width="20px">SL</th>
            <th width="20px" class="text-nowrap">Employee Code</th>
            <th class="text-nowrap">Employee Name</th>
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
</table>
@endif
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

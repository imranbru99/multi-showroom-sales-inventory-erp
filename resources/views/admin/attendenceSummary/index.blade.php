
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

@if(!empty($monthYear))
<table id="" name="productList" class="table table-bordered table-sm">
    <thead>
        <tr class="bg-success">
            <th width="20px">SL</th>
            <th width="20px" class="text-nowrap">Employee Code</th>
            <th class="text-nowrap">Employee Name</th>
            <th class="text-nowrap">Present In Time</th>
            <th class="text-nowrap">Present In Late</th>
            <th class="text-nowrap">Absent</th>
            <th class="text-nowrap">Leave</th>
            <th class="text-nowrap">Holiday</th>
            <th class="text-nowrap">Total</th>
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
            <td>
                <form action="{{ route('attendenceSummary.inTime') }}" class="text-center" method="post" enctype="multipart/form-data" target="_blank">
                    {{ csrf_field() }}
                    <input type="hidden" name="month" value="{{ @$monthYear }}">
                    <input type="hidden" name="staff" value="{{ $value['staff_id'] }}">
                    <button type="submit" class="detaisBtn border-0"> {{ $attends_in_time }}</button>
                </form>
            </td>
            <td>
                <form action="{{ route('attendenceSummary.lateTime') }}" class="text-center" method="post" enctype="multipart/form-data" target="_blank">
                    {{ csrf_field() }}
                    <input type="hidden" name="month" value="{{ @$monthYear }}">
                    <input type="hidden" name="staff" value="{{ $value['staff_id'] }}">
                    <button type="submit" class="detaisBtn border-0"> {{ $attends_late_time }}</button>
                </form>
            </td>
            <td>
                <form action="{{ route('attendenceSummary.absent') }}" class="text-center" method="post" enctype="multipart/form-data" target="_blank">
                    {{ csrf_field() }}
                    <input type="hidden" name="month" value="{{ @$monthYear }}">
                    <input type="hidden" name="staff" value="{{ $value['staff_id'] }}">
                    <button type="submit" class="detaisBtn border-0"> {{ $absents }}</button>
                </form>
            </td>
            <td>
                <form action="{{ route('attendenceSummary.leave') }}" class="text-center" method="post" enctype="multipart/form-data" target="_blank">
                    {{ csrf_field() }}
                    <input type="hidden" name="month" value="{{ @$monthYear }}">
                    <input type="hidden" name="staff" value="{{ $value['staff_id'] }}">
                    <button type="submit" class="detaisBtn border-0"> {{ $leaves }}</button>
                </form>
            </td>
            <td class="text-center">{{ $holidays }}</td>
            <td class="text-center">{{ $attends_in_time + $attends_late_time + $leaves + $absents + $holidays }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<style>
    .detaisBtn{
        cursor: pointer;
        text-align:center
    }

</style>
@endsection


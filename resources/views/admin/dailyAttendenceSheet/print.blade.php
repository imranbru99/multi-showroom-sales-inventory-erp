@extends('admin.layouts.masterPrint')

@section('content')
<table id="report-header">
    <tr>
        <td>Daily Attendence Sheet</td>
    </tr>
    <tr>
        <td>Date : {{ $date }} </td>
    </tr>
</table>

<div id="pad-bottom"></div>

<table id="report-table">
    <thead>
        <tr>
            <th width="20px">Sl</th>
            <th>Employee Name</th>
            <th>Code</th>
            <th>Date</th>
            <th>Day</th>
            <th>In Time</th>
            <th>Attend Status</th>
        </tr>
    </thead>

    <tbody>
        @php $sl = 1; @endphp
        @if(!empty($attendences->attendenceList))
        @foreach($attendences->attendenceList as $key => $attendence)
        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ $attendence->staff->name }}</td>
            <td>{{ $attendence->staff->code }}</td>
            <td>{{ $attendence->date }}</td>
            <td>{{ date('l', strtotime($attendence->date)) }}</td>
            <td>{{ date('h:i A', strtotime($attendence->in_time)) }}</td>
            <td align="center">
                @if($attendence->in_time <= "09:30:00")
                Attend
                @else
                Late
                @endif
            </td>
        </tr>
        @endforeach
        @endif
        @foreach($leaves as $leave)
        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ $leave->staff->name }}</td>
            <td>{{ $leave->staff->code }}</td>
            <td>{{ $date }}</td>
            <td>{{ date('l', strtotime($date)) }}</td>
            <td>Leave</td>
            <td align="center">{{ $leave->leaveType->name }}</td>
        </tr>
        @endforeach
        @foreach($absents as $absent)
        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ $absent->name }}</td>
            <td>{{ $absent->code }}</td>
            <td>{{ $date }}</td>
            <td>{{ date('l', strtotime($date)) }}</td>
            <td>Absent</td>
            <td align="center">Absent</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="bg-secondary">
            <td style="font-weight: bold; text-align: right" colspan="6">Total Present : </td>
            <td style="font-weight: bold; text-align: center">{{ !empty($attendences->attendenceList) ? count($attendences->attendenceList) : 0 }}</td>
        </tr>
        <tr class="bg-secondary">
            <td style="font-weight: bold; text-align: right" colspan="6">Total Leaves : </td>
            <td style="font-weight: bold; text-align: center">{{ count($leaves) }}</td>
        </tr>
        <tr class="bg-secondary">
            <td style="font-weight: bold; text-align: right" colspan="6">Total Absent : </td>
            <td style="font-weight: bold; text-align: center">{{ count($absents) }}</td>
        </tr>
        <tr class="bg-secondary">
            <td style="font-weight: bold; text-align: right" colspan="6">Total Employee : </td>
            <td style="font-weight: bold; text-align: center">{{ (!empty($attendences->attendenceList) ? count($attendences->attendenceList) : 0) + count($leaves) + count($absents) }}</td>
        </tr>
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

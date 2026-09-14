@extends('admin.layouts.masterPrint')

@section('custome-css')
    <style type="text/css">
            #rep-tab {
                font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
                border: 1px solid #4CAF50;
                border-collapse: collapse;
                width: 100%;
            }

            #rep-tab td, #report-table th {
                border: 0px solid #ddd;
            }

            #rep-tab td {
                font-size: 11px;
                padding: 5px;
            }

            #rep-tab th {
                text-align: center;
                background-color: #4CAF50;
                color: white;
                font-size: 13px;
                vertical-align: middle;
                padding: 3px;
            }

            #rep-tab tr:nth-child(even){background-color: #f2f2f2;}

            #rep-tab tr:hover {background-color: #ddd;}
    </style>
@endsection

@php
    use App\ManualAttendance;

    $totalHours = 0;
    $totalMinutes = 0;

    $totalPresentDay = ManualAttendance::where('employee_id',$employee->id)->whereYear('date',$year)->whereMonth('date',$month)->count('in_hour');
    $times = ManualAttendance::where('employee_id',$employee->id)->whereYear('date',$year)->whereMonth('date',$month)->get();

    foreach ($times as $time)
    {
        if (@$time->in_hour)
        {
            $inTime = @$time->in_hour.":".@$time->in_minute;
        }
        else
        {
            $inTime = "";
        }

        if (@$time->out_hour)
        {
            $outTime = @$time->out_hour.":".@$time->out_minute;
        }
        else
        {
            $outTime = "";
        }

        if (@$inTime != "" && @$outTime != "")
        {
            $hour = round((strtotime(@$outTime) - strtotime(@$inTime)) / 3600);
            $minute = round(((strtotime(@$outTime) - strtotime(@$inTime)) % 3600)/60);

            $totalHours = $totalHours + $hour;
            $totalMinutes = $totalMinutes + $minute;

            if ($totalMinutes >= 60)
            {
                $totalHours = $totalHours + 1;
                $totalMinutes = $totalMinutes - 60;
            }
        }
    }

    $totalTime = $totalHours." hrs ".$totalMinutes." min";
@endphp

@section('content')
    <table id="report-header">
        <tr>
            <td>Employee Attendance For {{ date('F', mktime(0, 0, 0,$month, 10)) }} Of {{ $year }}</td>
        </tr>
    </table>

    <div id="pad-bottom"></div>

    @php
        $totalDays = 0;
        if ($year == date('Y') && $month == date('m'))
        {
            $fromDate = strtotime(date('d-m-Y', strtotime('first day of this month')));
            $toDate = strtotime(date('d-m-Y'));

            $days = round(($toDate - $fromDate) / 86400) + 1;
        }
        
        for ($i = 1; $i <= $days; $i++)
        {
            $date = $year.'/'.$month.'/'.$i; //format date
            $dayName = date('l', strtotime($date)); //get week day

            //if not a weekend add day to array
            if($dayName != 'Friday')
            {
                $totalDays = $totalDays + 1;
            }
        }

        $totalWorkingTime = $totalDays * 8;
    @endphp

    <table id="rep-tab">
        <thead class="thead-green">
            <tr>
                <th colspan="6"><h3 style="text-align: center; margin: 0px;">Employee Information</h3></th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td width="95px">Employee No</td>
                <td width="5px" style="border: 0px solid #ddd;">:</td>
                <td>{{ $employee->employee_no }}</td>
                <td width="135px">Total Working Day & Hrs</td>
                <td width="5px" style="border: 0px solid #ddd;">:</td>
                <td>{{ $totalDays }} & {{ $totalWorkingTime }} hrs</td>
            </tr>

            <tr>
                <td width="95px">Employee Name</td>
                <td width="5px">:</td>
                <td>{{ $employee->name }}</td>
                <td width="135px">Total Present Day & Hrs</td>
                <td width="5px">:</td>
                <td>{{ $totalPresentDay }} & {{ $totalTime }}</td>
            </tr>

            <tr>
                <td width="95px">Designation</td>
                <td width="5px">:</td>
                <td>{{ $employee->designationName }}</td>
                <td width="135px">Total Absent Days & Hrs</td>
                <td width="5px">:</td>
                @php
                    $absentHour = $totalWorkingTime - $totalHours;
                    $absentMinute = $totalMinutes;

                    if ($absentHour < 0)
                    {
                        $absentHour = 0;
                        $absentMinute = 0;
                    }

                    $absentTime = $absentHour." hrs ".$totalMinutes." min";
                @endphp
                <td>{{ $totalDays - $totalPresentDay }} & {{ $absentTime }}</td>
            </tr>
        </tbody>
    </table>

    <div id="pad-bottom"></div>

    <table id="report-table">
        <thead class="thead-dark">
            <tr>
                <th width="15%">Date</th>
                <th width="10%">In Time</th>
                <th width="10%">Out Time</th>
                <th width="15%">Duration</th>
                <th width="15%">Date</th>
                <th width="10%">In Time</th>
                <th width="10%">Out Time</th>
                <th width="15%">Duration</th>
            </tr>
        </thead>
        <tbody>
            @php
                $sl = 0;
                $row = 1;
                $totalHours = 0;
                $totalMinutes = 0;
            @endphp
            @for ($i = 1; $i <= $days; $i++)
                @php
                    $date = $year.'/'.$month.'/'.$i; //format date
                    $dayName = date('l', strtotime($date)); //get week day
                @endphp

                @if ($dayName != 'Friday')
                    @php
                        $date = $year."-".$month."-".$i;
                        $manualAttendance = ManualAttendance::where('date',$date)
                            ->where('employee_id',$employee->id)
                            ->first();
                        if (@$manualAttendance->in_hour)
                        {
                            $inTime = @$manualAttendance->in_hour.":".@$manualAttendance->in_minute;
                        }
                        else
                        {
                            $inTime = "";
                        }

                        if (@$manualAttendance->out_hour)
                        {
                            $outTime = @$manualAttendance->out_hour.":".@$manualAttendance->out_minute;
                        }
                        else
                        {
                            $outTime = "";
                        }

                        if (@$inTime == "" || @$outTime == "")
                        {
                            $time = "";
                        }
                        else
                        {
                            $hour = round((strtotime(@$outTime) - strtotime(@$inTime)) / 3600);
                            $minute = round(((strtotime(@$outTime) - strtotime(@$inTime)) % 3600)/60);

                            $time = $hour." hrs ".$minute." min";
                        }
                    @endphp

                    @if ($row == 1)
                        <tr>
                    @endif
                        <td style="font-weight: bold;">{{ $date }}</td>
                        <td align="center">{{ @$inTime == "" ? "-" : @$inTime }}</td>
                        <td align="center">{{ @$outTime == "" ? "-" : @$outTime }}</td>
                        <td align="center">{{ @$time == "" ? "-" : @$time}}</td>
                    @if ($row == 2)
                        </tr>
                        @php
                            $row = 1;
                        @endphp
                    @else
                        @php
                            $row++;
                        @endphp
                    @endif
                @endif
            @endfor
        </tbody>
    </table>
@endsection

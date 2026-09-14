@extends('admin.layouts.master')

@php
    use App\ManualAttendance;
@endphp

@section('content')
    <div class="card">            
        <div class="card-header">
            <div class="row">
                <div class="col-md-6"><h4 class="card-title">{{ $title }}</div>
                <div class="col-md-6 text-right">
                    <a class="btn btn-outline-info btn-lg" href="{{ route('manualAttendance.index') }}">
                        <i class="fa fa-arrow-circle-left"></i> Go Back
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless table-striped" style="border: 1px solid green;">
                        <thead class="thead-green">
                            <tr>
                                <th colspan="3"><h3 style="text-align: center; margin: 0px;">Employee Information</h3></th>
                            </tr>
                        </thead>

                        <tbody style="font-weight: bold;">
                            <tr>
                                <td width="150px">Employee No</td>
                                <td width="5px">:</td>
                                <td>{{ $employee->employee_no }}</td>
                            </tr>

                            <tr>
                                <td width="150px">Employee Name</td>
                                <td width="5px">:</td>
                                <td>{{ $employee->name }}</td>
                            </tr>

                            <tr>
                                <td width="150px">Designation</td>
                                <td width="5px">:</td>
                                <td>{{ $employee->designationName }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="col-md-6">
                    <form class="form-horizontal" action="{{ route('manualAttendance.showAttendancedetails') }}" method="POST" enctype="multipart/form-data" name="form">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="month">Month</label>
                                    <select class="form-control chosen-select month" id="month" name="month">
                                        @foreach ($months as $key => $value)
                                            @php
                                                if ($key == $month)
                                                {
                                                    $select = "selected";
                                                }
                                                else
                                                {
                                                    $select = "";
                                                }
                                            @endphp
                                            <option value="{{ $key }}" {{ $select }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <input class="form-control" type="hidden" name="employeeId" value="{{ $employee->id }}">
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="year">Year</label>
                                    <select class="form-control chosen-select year" id="year" name="year">
                                        <option value="">Select Year</option>
                                        @for ($i = date('Y'); $i >= 1950; $i--)
                                            @php
                                                if ($i == $year)
                                                {
                                                    $select = "selected";
                                                }
                                                else
                                                {
                                                    $select = "";
                                                }
                                            @endphp
                                            <option value="{{ $i }}" {{ $select }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <button class="btn btn-outline-success btn-md actionButton" name="actionButton" value="Show" style="width: 100%">Show</button>
                            </div>

                            <div class="col-md-6">
                                <button class="btn btn-outline-success btn-md actionButton" name="actionButton" value="Print" style="width: 100%">Print</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @php
                $sl = 0;
            @endphp

            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th width="20%">Date</th>
                        <th width="10%">In Time</th>
                        <th width="10%">Out Time</th>
                        <th width="10%">Duration</th>
                        <th width="20%">Date</th>
                        <th width="10%">In Time</th>
                        <th width="10%">Out Time</th>
                        <th width="10%">Duration</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sl = 0;
                        $row = 1;
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
                                <td align="center">{{ @$time == "" ? "-" : @$time }}</td>
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
        </div>
    </div>
@endsection
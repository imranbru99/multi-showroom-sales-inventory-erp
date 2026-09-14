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
                        <td>
                            {{ $employee->employee_no }}
                            <input class="form-control" type="hidden" id="employeeId" name="employeeId" value="{{ $employee->id }}">
                        </td>
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

            @php
                $sl = 0;
            @endphp

            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th width="32%">Date</th>
                        <th width="32%">Hour</th>
                        <th width="31%">Minute</th>
                        <th width="5%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sl = 0;
                        $days = cal_days_in_month(CAL_GREGORIAN,date('m'),date('Y'));
                    @endphp
                    @for ($i = 1; $i <= $days; $i++)
                        @php
                            $date = date('Y').'/'.date('m').'/'.$i; //format date
                            $dayName = date('l', strtotime($date)); //get week day
                        @endphp

                        @if ($dayName != 'Friday')
                            @php
                                $date = date('Y')."-".date('m')."-".$i;
                                $manualAttendance = ManualAttendance::where('date',$date)->where('employee_id',$employee->id)->first();
                            @endphp

                            <tr>
                                <td>
                                    <input class="form-control" type="text" id="date_{{ $i }}" name="date" value="{{ $date }}" readonly>
                                    <input class="form-control" type="hidden" id="attendanceId_{{ $i }}" name="attendanceId" value="{{ @$manualAttendance->id }}" readonly>
                                </td>
                                <td>
                                    <select class="form-control chosen-select hour" id="hour_{{ $i }}" name="hour">
                                        <option value="">Select Hour</option>
                                        @for ($hour = 1; $hour <= 24; $hour++)
                                            @php
                                                if ($hour == @$manualAttendance->out_hour)
                                                {
                                                    $select = "selected";
                                                } 
                                                else
                                                {
                                                    $select = "";
                                                }                                                
                                            @endphp
                                            <option value="{{ str_pad($hour, 2, "0", STR_PAD_LEFT) }}" {{ $select }}>{{ str_pad($hour, 2, "0", STR_PAD_LEFT) }}</option>
                                        @endfor
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control chosen-select minute" id="minute_{{ $i }}" name="minute">
                                        <option value="">Select minute</option>
                                        @for ($minute = 0; $minute < 60; $minute++)
                                            @php
                                                if (@$manualAttendance->out_minute)
                                                {                                                
                                                    if ($minute == @$manualAttendance->out_minute)
                                                    {
                                                        $select = "selected";
                                                    } 
                                                    else
                                                    {
                                                        $select = "";
                                                    } 
                                                }
                                                else
                                                {
                                                    $select = "";
                                                }                                               
                                            @endphp
                                            <option value="{{ str_pad($minute, 2, "0", STR_PAD_LEFT) }}" {{ $select }}>{{ str_pad($minute, 2, "0", STR_PAD_LEFT) }}</option>
                                        @endfor
                                    </select>
                                </td>
                                <td>
                                    @if (@$manualAttendance->out_hour)
                                        <button class="btn btn-outline-success" onclick="updateInTime({{ $i }})">Update</button>
                                    @else
                                        <button class="btn btn-outline-info" onclick="saveInTime({{ $i }})">Save</button>
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('custom-js')
    <script>
        function saveInTime(i)
        {
            var employeeId = $('#employeeId').val();
            var attendanceId = $('#attendanceId_'+i).val();
            var date = $('#date_'+i).val();
            var hour = $('#hour_'+i).val();
            var minute = $('#minute_'+i).val();

            if (attendanceId == "")
            {
                swal('Please Add In Time Fist For This Date: '+date,'','warning')
            }
            else
            {
                if (hour == "")
                {
                    swal('Please Select Hour','','warning')
                }
                else
                {
                    if (minute == "")
                    {
                        swal('Please Select Minute','','warning')
                    }
                    else
                    {
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: "post",
                            url: "{{ route('manualAttendance.updateOutTime') }}",
                            data: {attendanceId:attendanceId,employeeId:employeeId,date:date,hour:hour,minute:minute},
                            success: function(response) {
                                swal('Out Time Successfully Added','','warning');
                            },
                            error: function(response) {
                                swal('Something Went Wrong, Please Try Again','','warning');
                            }
                        });
                    }
                }
            }
        }

        function updateInTime(i)
        {
            var employeeId = $('#employeeId').val();
            var attendanceId = $('#attendanceId_'+i).val();
            var date = $('#date_'+i).val();
            var hour = $('#hour_'+i).val();
            var minute = $('#minute_'+i).val();

            if (hour == "")
            {
                swal('Please Select Hour','','warning')
            }
            else
            {
                if (minute == "")
                {
                    swal('Please Select Minute','','warning')
                }
                else
                {

                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "post",
                        url: "{{ route('manualAttendance.updateOutTime') }}",
                        data: {attendanceId:attendanceId,employeeId:employeeId,date:date,hour:hour,minute:minute},
                        success: function(response) {
                            swal({
                                title: "<small class='text-success'>Success!</small>", 
                                type: "success",
                                text: "Out Time Successfully Added!",
                                timer: 1000,
                                html: true,
                            });
                        },
                        error: function(response) {
                            error = "Failed.";
                            swal({
                                title: "<small class='text-danger'>Error!</small>", 
                                type: "error",
                                text: "Something Went Worng, Please Try Again",
                                timer: 2000,
                                html: true,
                                confirmButtonText: "Yes, delete it!",
                            });
                        }
                    });
                }
            }
        }
    </script>
@endsection
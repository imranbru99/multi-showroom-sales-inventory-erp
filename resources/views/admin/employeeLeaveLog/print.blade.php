@extends('admin.layouts.masterPrint')

@section('content')
<table id="report-header">
    <tr>
        <td>Employee Name : {{ $staffDetails->name }}</td>
    </tr>
</table>

<div id="pad-bottom"></div>

<table id="report-table">
    <thead>
        <tr>
            <th width="20px">Sl</th>
            <th>Employee Name</th>
            <th>Leave Type</th>
            <th>Leave Details</th>
        </tr>
    </thead>

    <tbody>
        @php 
        $grand_total = 0;  
        @endphp

        @foreach($leaves as $leave)

        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>@if($loop->first) {{ $leave->staff->name }} @endif</td>
            <td>{{ $leave->leaveType->name }} ({{ $leave->leaveType->days }})</td>
            <td>
                <table width="100%" id="leaveDetails">
                    <thead>
                        <tr class="bg-success">
                            <th>Leave From</th>
                            <th>Leave To</th>
                            <th>Leave Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_leaves = 0;
                        $leaves_types = App\LeaveRequest::where('leave_type', $leave->leave_type)
                                ->where('employee_id', $leave->employee_id)
                                ->get();
                        ?>
                        @foreach($leaves_types as $leaves_type)
                        @php 
                        $total_leaves += $leaves_type->leave_day; 
                        @endphp
                        <tr>
                            <td>{{ $leaves_type->leave_from }}</td>
                            <td>{{ $leaves_type->leave_to }}</td>
                            <td>{{ $leaves_type->leave_day }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-secondary">
                            <td colspan="2" class="font-weight-bold">Total Leave</td>
                            <td>{{ $total_leaves }}</td>
                        </tr>
                    </tfoot>
                </table>
            </td>
        </tr>
        @php $grand_total += $total_leaves; @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr class="bg-secondary">
            <td colspan="3"></td>
            <td style="font-weight: bold; text-align: center">Total Leaves : {{ $grand_total }}</td>
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

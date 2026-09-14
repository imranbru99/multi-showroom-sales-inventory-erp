@extends('admin.layouts.masterPrint')

@section('content')
<table id="report-header">
    <tr>
        <td>{{ $title }}</td>
    </tr>
    <tr>
        <td style="text-transform: uppercase">
            Month : {{ date('F Y', strtotime($monthYear)) }} 
            <br>
            Of {{ $staff->name }} ({{ $staff->code }})
        </td>
    </tr>
</table>

<div id="pad-bottom"></div>
<table id="report-table">
    <thead>
        <tr>
            <th width='20px'>SL</th>
            <th>Date</th>
            <th>Attendence Time</th>
            <th>Remarks</th>
        </tr>
    </thead>

    <tbody>
        @foreach($monthly_attendance as $key => $value)
        @php $sl=1 ; @endphp
        @foreach($value['monthly_staff'] as $key => $attendence)
        <?php
        if ($attendence['attendence'] != '') {
            if ($attendence['attendence']->in_time > "09:30:00") {
                ?>  

                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ $attendence['attendence']->date }}</td>
                    <td>{{ date('h:i A', strtotime($attendence['attendence']->in_time)) }}</td>
                    <td>Late Time</td>
                </tr>

                <?php
            }
        }
        ?>

        @endforeach
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
    #report-table{
        text-align: center;
    }
</style>

@endsection

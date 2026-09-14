@extends('admin.layouts.masterPrint')

@section('content')
    <?php $total = 0; ?>
    <div id="pad-bottom"></div>

    <table id="report-table">
        <thead>
            <tr>
                <th width="20px">SL</th>
                <th width="100px">Name</th>
                <th width="100px">Type</th>
                <th width="100px">Date From</th>
                <th width="100px">Date To</th>
                <th width="50px">Duration</td>
            </tr>
        </thead>

        <tbody>
            @foreach ($holidays as $holiday).
                <?php
                $to = now()->createFromFormat('Y-m-d', $holiday->from_date);
                $from = now()->createFromFormat('Y-m-d', $holiday->to_date);

                $diff_in_days = $to->diffInDays($from) + 1;

                $total += $diff_in_days;
                ?>

                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $holiday->name }}</td>
                    <td align="center">{{ $holiday->type }}</td>
                    <td align="center">{{ $holiday->from_date }}</td>
                    <td align="center">{{ $holiday->to_date }}</td>
                    <td align="center">{{ $diff_in_days }} Days</td>
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td align="right" colspan="5">
                    <h3>Total Holidays</h3>
                </td>
                <td align="center">{{ $total }} Days</td>
            </tr>
        </tfoot>
    </table>
    <div class="row">
        <div class="col-md-12 text-right">
            <?php date_default_timezone_set('Asia/Dhaka'); ?>
            <p>Print Date & Time : <?php echo date('d-m-Y h:i:sa'); ?></p>
        </div>
    </div>
@endsection

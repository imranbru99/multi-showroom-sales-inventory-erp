@extends('admin.layouts.masterPrint')

@section('content')
<table id="report-header">
    <tr>
        <td>Salary Payment From {{ date('d-m-Y', strtotime($fromDate)) }} To {{ date('d-m-Y', strtotime($toDate)) }}</td>
    </tr>
</table>

<div id="pad-bottom"></div>

<table id="report-table">
    <thead>
        <tr>
            <th>SL</th>
            <th>Employee Code</th>
            <th>Employee Name</th>
            <th>Company Name</th>
            <th>Salary Month</th>
            <th>Salary Year</th>
            <th>Payment Date</th>
            <th>Payable</th>
        </tr>
    </thead>
    <tbody>
        @php
        $total_payable = 0;
        @endphp
        @foreach($payrollPayments as $payrollPayment)
        @php
        $total_payable += $payrollPayment->total_payable;
        @endphp
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $payrollPayment->staff->code }}</td>
            <td>{{ $payrollPayment->staff->name }}</td>
            <td>{{ $payrollPayment->salaryProcess->showroom->name }}</td>
            <td>{{ date('F', strtotime($payrollPayment->month_year)) }}</td>
            <td>{{ date('Y', strtotime($payrollPayment->month_year)) }}</td>
            <td>{{ date('d-m-Y', strtotime($payrollPayment->payment_date)) }}</td>
            <td>{{ $payrollPayment->total_payable }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="7" align="right"><b>Total Payable</b></td>
            <td  align="right"><b>{{number_format($total_payable, 2, '.', '')}}</b></td>
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

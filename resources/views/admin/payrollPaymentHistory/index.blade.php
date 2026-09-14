<?php
$months = [
    1 => 'January',
    2 => 'February',
    3 => 'March',
    4 => 'April',
    5 => 'May',
    6 => 'June',
    7 => 'July ',
    8 => 'August',
    9 => 'September',
    10 => 'October',
    11 => 'November',
    12 => 'December',
];
?>
@extends('admin.layouts.masterReport')

@section('search_card_body')
<input type="hidden" name="print" value="print">
<div class="row d-flex justify-content-center">
    <div class="col-md-3">
        <label for="from-date">From Date</label>
        <input type="text" class="form-control {{ $print == 'print' ? 'datepicker' : 'add_datepicker' }}"
               name="fromDate" placeholder="Select Date From"
               value="{{ Date('d-m-Y', strtotime(@$fromDate)) }}" readonly>
    </div>

    <div class="col-md-3">
        <label for="to-date">To Date</label>
        <input type="text" class="form-control {{ $print == 'print' ? 'datepicker' : 'add_datepicker' }}"
               name="toDate" placeholder="Select Date To" value="{{ Date('d-m-Y', strtotime(@$toDate)) }}"
               readonly>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Company</label>
            <select class="form-control" name="company" required>
                <?php
                foreach ($companies as $comp) {
                    $select = '';
                    if ($comp->id == $company) {
                        $select = 'selected';
                    }
                    echo '<option value="' . $comp->id . '"' . $select . '>' . $comp->name . '</option>';
                }
                ?>
            </select>
        </div>
    </div>
</div>
@endsection

@section('print_card_header')
@if (@$fromDate)
<input type="hidden" name="fromDate" value="{{ @$fromDate }}">
@endif

@if (@$toDate)
<input type="hidden" name="toDate" value="{{ @$toDate }}">
@endif

@if ($company)
<input type="hidden" name="company" value="{{ $company }}">
@endif

<input type="hidden" id="print_value" name="print" value="{{ $print }}">
@endsection

@section('print_card_body')
<table id="dataTable" name="paymentHistory" class="table table-bordered table-sm">
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
        @foreach($payrollPayments as $payrollPayment)
        <tr>
            <td></td>
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
</table>
@endsection

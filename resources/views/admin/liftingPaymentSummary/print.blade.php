@extends('admin.layouts.masterPrint')
@php
error_reporting(0);
@endphp
@section('content')
<table id="report-header">
    <tr>
        <td>Lifting Payment Summary For The Month Of {{ date('F', mktime(0, 0, 0,$month, 10)) }} Of {{ $year }}</td>
    </tr>
</table>

<div id="pad-bottom"></div>

<table id="report-table">
    <caption></caption>
    <thead>
        <tr>
            <th width="20px" rowspan="2">Sl</th>
            <th rowspan="2">Vendor Name</th>
            <th rowspan="2" width="70px">Previous Years</th>
            <th colspan="4" style="text-align: center;">For The Year {{ $year }}</th>
            <th colspan="4" style="text-align: center;">For The Month {{ date('F', mktime(0, 0, 0,$month, 10)) }}</th>
            <th rowspan="2" width="70px">Vendor Outstanding</th>
        </tr>
        <tr>
            <th width="60px">Lifting</th>
            <th width="80px">Payments</th>
            <th width="70px">Return</th>
            <th width="70px">Balance</th>
            <th width="60px">Lifting</th>
            <th width="80px">Payments</th>
            <th width="70px">Return</th>
            <th width="70px">Balance</th>
        </tr>
    </thead>

    <tbody>
        @php
        $sl = 1;
        $balance = 0;
        $currentId = 0;


        $totalPreviousBalance = 0;

        $totalYearlyLifting = 0;
        $totalYearlyPayment = 0;
        $totalYearlyReturn = 0;
        $totalYearlyBalance = 0;

        $totalMonthlyLifting = 0;
        $totalMonthlyPayment = 0;
        $totalMonthlyReturn = 0;
        $totalMonthlyBalance = 0;

        $totalMarketOutstanding = 0;
        @endphp

        @php
        $sl = 0;
        @endphp
        @foreach ($data as $row)
        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ $row['vendorName'] }}</td>
            <td align="right">{{ number_format($row['previousBalance'], 2, '.', '') }}</td>
            <td align="right">{{ number_format($row['year']['lifting'], 2, '.', '') }}</td>
            <td align="right">{{ number_format($row['year']['payment'], 2, '.', '') }}</td>
            <td align="right">{{ number_format($row['year']['return'], 2, '.', '') }}</td>
            <td align="right">{{ number_format($row['year']['balance'], 2, '.', '') }}</td>
            <td align="right">{{ number_format($row['month']['lifting'], 2, '.', '') }}</td>
            <td align="right">{{ number_format($row['month']['payment'], 2, '.', '') }}</td>
            <td align="right">{{ number_format($row['month']['return'], 2, '.', '') }}</td>
            <td align="right">{{ number_format($row['month']['balance'], 2, '.', '') }}</td>
            <td align="right">{{ number_format($row['marketOutstanding'], 2, '.', '') }}</td>
        </tr>
        @php
        $totalPreviousBalance += $row['previousBalance'];

        $totalYearlyLifting += $row['year']['lifting'];
        $totalYearlyPayment += $row['year']['payment'];
        $totalYearlyReturn += $row['year']['return'];
        $totalYearlyBalance += $row['year']['balance'];

        $totalMonthlyLifting += $row['month']['lifting'];
        $totalMonthlyPayment += $row['month']['payment'];
        $totalMonthlyReturn += $row['month']['return'];
        $totalMonthlyBalance += $row['month']['balance'];

        $totalMarketOutstanding += $row['marketOutstanding'];
        @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2" style="text-align: right;font-weight:bold">Total</th>
            <th align="right">{{ number_format($totalPreviousBalance, 2, '.', '') }}</th>
            <th align="right">{{ number_format($totalYearlyLifting, 2, '.', '') }}</th>
            <th align="right">{{ number_format($totalYearlyPayment, 2, '.', '') }}</th>
            <th align="right">{{ number_format($totalYearlyReturn, 2, '.', '') }}</th>
            <th align="right">{{ number_format($totalYearlyBalance, 2, '.', '') }}</th>
            <th align="right">{{ number_format($totalMonthlyLifting, 2, '.', '') }}</th>
            <th align="right">{{ number_format($totalMonthlyPayment, 2, '.', '') }}</th>
            <th align="right">{{ number_format($totalMonthlyReturn, 2, '.', '') }}</th>
            <th align="right">{{ number_format($totalMonthlyBalance, 2, '.', '') }}</th>
            <th align="right">{{ number_format($totalMarketOutstanding, 2, '.', '') }}</th>
        </tr>
    </tfoot>
</table>
@endsection
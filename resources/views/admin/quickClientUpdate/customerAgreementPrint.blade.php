@extends('admin.layouts.masterPrint')

@section('content')
<table id="report-header">
    <tr>
        <td>Customer Agreement</td>
    </tr>
</table>

<div id="pad-bottom"></div>

<table id="report-table">
    <thead>
        <tr>
            <th style="width:50px;">SL#</th>
            <th style="width:105px;">Date</th>
            <th style="width:105px;">A/C No.</th>
            <th style="width:120px;">Customer Name</th>
            <th style="width:105px;">Reference</th>
            <th align="center" style="width:105px;">Memo No / MR. No</th>
            <th style="width:92px;">Agreement Amount</th>
        </tr>
    </thead>

    <tbody>
        @php
        $i = 1;
        $totalCollection = 0;
        @endphp
        @foreach ($agreements as $agreement)
        <tr>
            <td>{{ $i++ }}</td>
            <td>{{ date('d-m-Y', strtotime($agreement->date)) }}</td>
            <td align="center">{{ @$agreement->customer->code }}</td>
            <td>{{ @$agreement->customer->name }}</td>
            <td>{{ @$agreement->staff->name }}</td>
            <td align="center">{{ @$agreement->money_r_no }}</td>
            <td align="right">{{ @$agreement->agreement_amount }}</td>
        </tr>
        @php
        $totalCollection += $agreement->agreement_amount;
        @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="6">Total</th>
            <th>{{ $totalCollection }}</th>
        </tr>

    </tfoot>
</table>
@endsection
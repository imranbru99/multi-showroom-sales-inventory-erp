@extends('admin.layouts.masterPrint')

@section('content')
<table id="report-header">
    <tr>
        <td>Customer Outstanding</td>
    </tr>
</table>

<div id="pad-bottom"></div>

<table id="report-table">
    <thead>
        <tr>
            <th width="20px">Sl</th>
            <th>A/C No.</th>
            <th>Account Name</th>
            <th width="80px">Mobile No</th>
            <th width="65px">Savings</th>
        </tr>
    </thead>

    <tbody>
        @php
        $sl = 1;
        $totalCollection = 0;
        @endphp
        @foreach ($customerOutstandings as $customerOutstanding)
        @php
        $totalCollection += $customerOutstanding['totalCollectionAmount'];
        @endphp
        <tr>
            <td>{{ $customerOutstanding['sl'] }}</td>
            <td>{{ $customerOutstanding['customerAccountCode'] }}</td>
            <td>{{ $customerOutstanding['customerName'] }}</td>
            <td>{{ $customerOutstanding['customerMobile'] }}</td>
            <td>
                {{$customerOutstanding['totalCollectionAmount']}}
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4">Total</th>
            <th>{{ $totalCollection }}</th>
        </tr>

    </tfoot>
</table>
@endsection
@extends('admin.layouts.masterPrint')

@section('content')
    <table id="report-header">
        <tr>
            <td>Print Previous Retail Sales Report ON {{ $fromDate }} To {{ $toDate }}</td>
        </tr>
    </table>

    <div id="pad-bottom"></div>

    <table id="report-table">
        <thead>
            <tr>
                <th width="20px">Sl</th>
                <th width="80px">Date</th>
                <th>Customer Name</th>
                <th width="70px">A/C No</th>
                <th width="90px">Mobile No</th>
                <th width="70px">Group</th>
                <th width="90px">Pro Name</th>
                <th width="80px">Model No</th>
                <th width="125px">Money Receipt No</th>
                <th width="90px">Sales Price</th>
                <th width="70px">Collection</th>
                <th width="100px">Total Balance</th>
            </tr>
        </thead>

        <tbody>
            @php
                $sl = 1;
                $totalSalesAmount = 0;
                $totalCollection = 0;
                $totalTotalBalance = 0;
                $colSpan = 9;
            @endphp

            @foreach ($retailSalesReports as $retailSalesReport)
                <tr>
                    @php
                        $totalSalesAmount = $totalSalesAmount + (int) $retailSalesReport->sales_amount;
                        $totalCollection = $totalCollection + (int) $retailSalesReport->collection;
                        $totalTotalBalance = $totalTotalBalance + (int) $retailSalesReport->total_balance;
                    @endphp
                    <td>{{ $sl++ }}</td>
                    <td>{{ date('d-m-Y',strtotime($retailSalesReport->date)) }}</td>
                    <td>{{ $retailSalesReport->customer_name }}</td>
                    <td>{{ $retailSalesReport->account_no }}</td>
                    <td>{{ $retailSalesReport->mobile_no }}</td>
                    <td>{{ $retailSalesReport->groups }}</td>
                    <td>{{ $retailSalesReport->product_name }}</td>
                    <td>{{ $retailSalesReport->model_no }}</td>
                    <td>{{ $retailSalesReport->money_receipt_no }}</td>
                    <td align="right">{{ $retailSalesReport->sales_amount }}</td>
                    <td align="right">{{ $retailSalesReport->collection }}</td>
                    <td align="right">{{ $retailSalesReport->total_balance }}</td>
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td align="right">{{ $totalSalesAmount }}</td>
                <td align="right">{{ $totalCollection }}</td>
                <td align="right">{{ $totalTotalBalance }}</td>
                {{-- <td colspan="1">{{ $totalSalesAmount }}</td> --}}
                {{-- <td colspan="1">{{ $totalCollection }}</td> --}}
                {{-- <td colspan="1">{{ $totalTotalBalance }}</td> --}}
            </tr>
        </tfoot>
    </table>
@endsection

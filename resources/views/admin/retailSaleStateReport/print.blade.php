@extends('admin.layouts.masterPrint')


@section('content')
    <table id="report-header">
        <tr>
            <td>Employee Sales State ON
                @if (date('d-m-Y', strtotime($fromDate)) == '01-01-1970')
                    01-07-2019
                @else
                    {{ date('d-m-Y', strtotime($fromDate)) }}
                @endif
                To
                {{ date('d-m-Y', strtotime($toDate)) }}
            </td>
        </tr>
    </table>

    <div id="pad-bottom"></div>



    <table id="report-table" name="paymentRecordTable" class="table table-bordered table-sm">
        <thead>
            <tr>
                <th width="20px">Sl</th>
                <th width="100px">Employee Name</th>
                <th width="50px">Sales Amount</th>
                <th width="50px">Sales Return Amount</th>
                <th width="50px">Actual Sales Amount</th>
                <th width="50px">Collection Amount</th>
                <th width="50px">Current Balance</th>
                <th width="50px">Sales Qty</th>
                <th width="50px">Sales Return Qty</th>
                <th width="50px">Actual Sales Qty</th>
            </tr>
        </thead>

        <tbody>
            @php
                $sl = 1;
                $tota_sales = 0;
                $total_collection = 0;
                $total_return = 0;
                $total_balance = 0;
                $total_sale_qty = 0;
                $total_current_balance = 0;
                $total_return_qty = 0;
                $total_actual_amount = 0;
                $total_actual_qty = 0;
            @endphp
            @foreach ($data as $d)
                @php
                    if ($d['total_collections'] != 0 && $d['actual_sales'] != 0) {
                        $average = ($d['total_collections'] / $d['actual_sales']) * 100;
                        $average = number_format($average, 2, '.', '');
                    } else {
                        $average = 0;
                    }
                    
                    if ($average == 0) {
                        continue;
                    }
                    
                    $tota_sales += $d['total_sales'];
                    $total_collection += $d['total_collections'];
                    $total_return += $d['total_returns'];
                    $total_sale_qty += $d['sales_qty'];
                    $total_current_balance += $d['current_balance'];
                    $total_return_qty += $d['returns_qty'];
                    $total_actual_amount += $d['actual_sales'];
                    $total_actual_qty += $d['actual_sales_qty'];
                    
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $d['name'] }}</td>
                    <td align="right">{{ $d['total_sales'] }}</td>
                    <td align="right">{{ $d['total_returns'] }}</td>
                    <td align="right">{{ number_format($d['actual_sales'], 2, '.', '') }}</td>
                    <td align="right">{{ number_format($d['total_collections'], 2, '.', '') }}</td>
                    <td align="right">{{ number_format($d['current_balance'], 2, '.', '') }}</td>
                    <td align="right">{{ $d['sales_qty'] }}</td>
                    <td align="right">{{ $d['returns_qty'] }}</td>
                    <td align="right">{{ $d['actual_sales_qty'] }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th>Total</th>
                <th></th>
                <th>{{ $tota_sales }}</th>
                <th>{{ $total_return }}</th>
                <th>{{ $total_actual_amount }}</th>
                <th>{{ $total_collection }}</th>
                <th>{{ round($total_current_balance, 2) }}</th>
                <th>{{ $total_sale_qty }}</th>
                <th>{{ $total_return_qty }}</th>
                <th>{{ $total_actual_qty }}</th>
            </tr>
        </tfoot>
    </table>



    <div class="row">
        <div class="col-md-12 text-right">
            <p>Print Date & Time : {{ date('d-m-Y h:i:sa', strtotime(now())) }}</p>
        </div>
    </div>

@endsection

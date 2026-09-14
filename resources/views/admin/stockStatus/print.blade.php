
@extends('admin.layouts.masterPrint')

@section('content')
<table id="report-table">
    <caption>Stock Status Report On {{ date('Y-m-d', strtotime($fromDate)) }} To {{ date('Y-m-d', strtotime($toDate)) }}</caption>
    <thead>
        <tr>
            <th width="20px">Sl</th>
            <th  width="120px">Product</th>
            <th width="120px">Model</th>
            <th width="80px">Opening</th>
            <th width="95px">Lifting Qty</th>
            <th width="95px">Lifting Return</th>
            <th width="90px">Sales Qty</th>
            <th width="90px">Sales Return</th>
            <th width="80px">Stock Balance</th>
           <!--  <th width="80px">Stock Valuation</th> -->
        </tr>
    </thead>

    <tbody>

        @php
        $sl = 1;
        $totalOpening = 0;
        $totalLifting = 0;
        $totalSalesQty = 0;
        $totalBalanceQty = 0;
        $totalBalanceValue = 0;
        $totalLiftingReturnQty = 0;
        $totalSalesReturnQty = 0;
        @endphp
        @foreach ($stockStatusReports as $stockStatusReport)
        <?php
        if ($stockStatusReport['balanceQty'] <= 0) {
            continue;
        }
        $totalOpening += $stockStatusReport['opening'];
        $totalLifting += $stockStatusReport['liftingQty'];
        $totalLiftingReturnQty += $stockStatusReport['liftingReturnQty'];
        $totalSalesQty += $stockStatusReport['salesQty'];
        $totalSalesReturnQty += $stockStatusReport['salesReturnQty'];
        $totalBalanceQty += $stockStatusReport['balanceQty'];
        ?>
        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ $stockStatusReport['product']->name }}</td>
            <td>{{ $stockStatusReport['product']->model_no }}</td>
            <td style="text-align: right;">
                {{ $stockStatusReport['opening'] }}
            </td>
            <td style="text-align: right;">{{ $stockStatusReport['liftingQty'] }}</td>
            <td style="text-align: right;">{{ $stockStatusReport['liftingReturnQty'] }}</td>
            <td style="text-align: right;">{{ $stockStatusReport['salesQty'] }}</td>
            <td style="text-align: right;">{{ $stockStatusReport['salesReturnQty'] }}</td>
            <td style="text-align: right;">{{ $stockStatusReport['balanceQty'] }}</td>
        </tr>
        @endforeach

    </tbody>
    <tfoot>
        <tr>
            <th colspan="3">Total:</th>
            <th style="text-align:right;">{{ $totalOpening }}</th>
            <th style="text-align:right;">{{ $totalLifting }}</th>
            <th style="text-align:right;">{{ $totalLiftingReturnQty }}</th>
            <th style="text-align:right;">{{ $totalSalesQty }}</th>
            <th style="text-align:right;">{{ $totalSalesReturnQty }}</th>
            <th style="text-align:right;">{{ $totalBalanceQty }}</th>
            <!-- <th style="text-align:right;">{{ $totalBalanceValue }}</th> -->
        </tr>
    </tfoot>


</table>
<div class="row">
    <div class="col-md-12 text-right">
        <?php
        date_default_timezone_set("Asia/Dhaka");
        ?>
        <p>Print Date & Time : <?php echo date("d-m-Y h:i:sa"); ?></p>
    </div>
</div>
@endsection

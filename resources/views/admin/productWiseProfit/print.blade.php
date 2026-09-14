@extends('admin.layouts.masterPrint')

@section('content')
<table id="report-header">
    <tr>
        <td>{{ $title }} ON {{ date('d-m-Y', strtotime($fromDate)) }} To {{date('d-m-Y', strtotime($toDate))  }}</td>
    </tr>
</table>

<div id="pad-bottom"></div>

<table id="report-table">
    <thead>
        <tr>
            <th width="20px">SL</th>
            <th>Category</th>
            <th>Name</th>
            <th>Model</th>
            <th width="80px">Qty</th>
            <th width="130px">Sales Amount</th>
            <th width="130px">Lifting Amount</th>
            <th width="130px">Profit Amount</th>
            <th width="110px">Profit(%)</th>
        </tr>
    </thead>

    <tbody>
        <?php
        $totalProfit = 0;
        $totalQty = 0;
        $totalSales = 0;
        $totalLifting = 0;
        $totalProfitPercent = 0;
        $count = 0;
        ?>
        @foreach($sales as $sale)
        <?php
        if ($type == 'warranty_product') {
            $lifting = $sale->liftingPrice;
            $netProfit = $sale->salePrice - $sale->liftingPrice;
            $profitPercent = 0;
            if ($netProfit > 0) {
                if ($sale->liftingPrice <= 0) {
                    $profitPercent = 100;
                } else {
                    $profitPercent = ($netProfit / $sale->liftingPrice) * 100;
                }
            }
        } else {
            $lifting = $sale->consumerPrice * $sale->qty;
            $netProfit = $sale->salePrice - $lifting;
            $profitPercent = 0;
            if ($netProfit > 0) {
                if ($lifting <= 0) {
                    $profitPercent = 100;
                } else {
                    $profitPercent = ($netProfit / $lifting) * 100;
                }
            }
        }
        $count += 1;
        $totalQty += $sale->qty;
        $totalProfit += $netProfit;
        $totalSales += $sale->salePrice;
        $totalLifting += $lifting;
        $totalProfitPercent += $profitPercent;
        ?>
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $sale->productCategory }}</td>
            <td>{{ $sale->productName }}</td>
            <td>{{ $sale->productModel }}</td>
            <td align="center">{{ $sale->qty }}</td>
            <td align="right">{{ number_format($sale->salePrice, 2, '.', '') }}</td>
            <td align="right">{{ number_format($lifting, 2, '.', '') }}</td>
            <td align="right">
                {{ number_format($netProfit, 2, '.', '') }}
            </td>
            <td align="right">
                {{ number_format($profitPercent, 2, '.', '') }} %
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot id="report-footer">
        <tr style="background-color: #50af51;">
            <td style="text-align: right; color: white" colspan="4"><b>Total Summary : </b></td>
            <td style="text-align: center; color: white"><b>{{ $totalQty }}</b></td>
            <td style="text-align: right; color: white"><b>{{ number_format($totalSales, 2, '.', '') }}</b></td>
            <td style="text-align: right; color: white"><b>{{ number_format($totalLifting, 2, '.', '') }}</b></td>
            <td style="text-align: right; color: white"><b>{{ number_format($totalProfit, 2, '.', '') }}</b></td>
            <td style="text-align: right; color: white">
                <b>
                    <?php
                    $grand = 0;
                    if($count > 0){
                        $grand = $totalProfitPercent / $count;
                    }
                    ?>
                    {{ number_format($grand, 2, '.', '') }} %
                </b>
            </td>
        </tr>
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
@endsection

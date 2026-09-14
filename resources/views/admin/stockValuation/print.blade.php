@extends('admin.layouts.masterPrint')

@section('content')
<table id="report-header">
    <tr>
        <td>{{ $title }}</td>
    </tr>
</table>

<div id="pad-bottom"></div>

<table id="report-table">
    <thead>
        <tr>
            <th width="20px" rowspan="2" >SL#</th>
            <th rowspan="2" width="120px">Category</th>
            <th rowspan="2" width="70px">Product Name</th>
            <th rowspan="2" width="90px">Model No</th>
            <th rowspan="2"  width="60px">Available Quantity</th>
            <th colspan="2" class="text-center">Purchase value</th>
            <th colspan="2"  class="text-center">Sales value</th>
        </tr>
        <tr>
            <th class="text-center">Cost</th>
            <th class="text-center">Value</th>
            <th class="text-center">Rate</th>
            <th class="text-center">Value</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sl = 1;
        $currentCategoryId = 0;
        $totalQty = 0;
        $totalPurchaseValue = 0;
        $totalSaleValue = 0;
        ?>
        @foreach ($stockOutReports as $stockOutReport)
        <?php
        $product = DB::table('tbl_products')->where('id', $stockOutReport['productId'])->first();
        if ($stockOutReport['InStock'] <= 0) {
            continue;
        }
        ?>

        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ $stockOutReport['categoryName'] }}</td>
            <td>{{ $stockOutReport['productName'] }}</td>
            <td>{{ $stockOutReport['productModel'] }}</td>
            <td style="text-align: right;">{{ $stockOutReport['InStock'] }}</td>
            <td style="text-align: right;">{{ $product->price }}</td>
            <td style="text-align: right;">{{ number_format($stockOutReport['InStock'] * $product->price, 2, '.', '')}}</td>
            <td style="text-align: right;">{{ $product->mrp_price}}</td>
            <td style="text-align: right;">{{ number_format($stockOutReport['InStock'] * $product->mrp_price, 2, '.', '')}}</td>
        </tr>

        <?php
        $totalQty += $stockOutReport['InStock'];
        $totalPurchaseValue += $stockOutReport['InStock'] * $product->price;
        $totalSaleValue += $stockOutReport['InStock'] * $product->mrp_price;
        ?>
        @endforeach
        <tr>
            <td colspan="4" style="font-weight: bold;" align="right">Total:</td>
            <td style="text-align: right; font-weight: bold;">{{ $totalQty }}</td>
            <td ></td>
            <td style="text-align: right; font-weight: bold;">{{ number_format($totalPurchaseValue, 2, '.', '') }}</td>
            <td ></td>
            <td style="text-align: right; font-weight: bold;">{{ number_format($totalSaleValue, 2, '.', '') }}</td>
        </tr>
    </tbody>
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

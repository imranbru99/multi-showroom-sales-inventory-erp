
@extends('admin.layouts.masterPrint')

<?php

use App\LiftingProduct;
use App\ProductIssueList;
use App\LiftingReturnProduct;
use App\SalesReturn;
use App\TransferProduct;
?>


@section('content')
<table id="report-header">
    <tr>
        <td>{{ $title }}
    </tr>
</table>

<div id="pad-bottom"></div>

@if($type == 'warranty_product' || $type == 'spare_parts')
<table id="report-table">
    <thead>
        <tr>
            <th width="20px">Sl</th>
            <th width="130px">Category</th>
            <th>Product</th>
            <th width="70px">Model</th>
            <th>Serial No</th>
            <th width="100px">Available Qty</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sl = 1;
        $totalRemainingQty = 0;
        ?>

        @foreach ($stockOutReports as $stockOutReport)
        <?php
        if ($stockOutReport['InStock'] <= 0) {
            continue;
        }
        $totalRemainingQty += $stockOutReport['InStock'];
        ?>
        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ $stockOutReport['categoryName'] }}</td>
            <td>{{ $stockOutReport['productName'] }}</td>
            <td>{{ $stockOutReport['productModel'] }}</td>
            <td>
                @foreach ($stockOutReport['serialNo'] as $serial)
                {{ $serial }},
                @endforeach
            </td>
            <td style="text-align: right;">
                {{ $stockOutReport['InStock'] }}
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" align="right"><b>Total Available Qty</b></td>
            <td  align="right"><b>{{$totalRemainingQty}}</b></td>
        </tr>
    </tfoot>
</table>
@else

<table id="report-table">
    <thead>
        <tr>
            <th width="20px">Sl</th>
            <th width="130px">Category</th>
            <th>Product</th>
            <th width="70px">Model</th>
            <th width="100px">Available Qty</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sl = 1;
        $totalRemainingQty = 0;
        ?>

        @foreach ($stockOutReports as $stockOutReport)
        <?php
        if ($stockOutReport['InStock'] <= 0) {
            continue;
        }
        $totalRemainingQty += $stockOutReport['InStock'];
        ?>
        <tr>
            <td>{{ $sl++ }}</td>
            <<td>{{ $stockOutReport['categoryName'] }}</td>
            <td>{{ $stockOutReport['productName'] }}</td>
            <td>{{ $stockOutReport['productModel'] }}</td>
            <td style="text-align: right;">
                {{ $stockOutReport['InStock'] }}
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" align="right"><b>Total Available Qty</b></td>
            <td  align="right"><b>{{$totalRemainingQty}}</b></td>
        </tr>
    </tfoot>
</table>

@endif

<div class="row">
    <div class="col-md-12 text-right">
        <?php date_default_timezone_set('Asia/Dhaka'); ?>
        <p>Print Date & Time : <?php echo date('d-m-Y h:i:sa'); ?></p>
    </div>
</div>
@endsection

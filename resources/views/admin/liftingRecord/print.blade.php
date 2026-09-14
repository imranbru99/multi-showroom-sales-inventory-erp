@extends('admin.layouts.masterPrint')
@php
error_reporting(0);
@endphp
@section('content')
<table id="report-header">
    @if ($btnPrintSummary == "Print Summary")
    <tr><td>Lifting Summary On {{ date('d-m-Y',strtotime($fromDate)) }} To {{ date('d-m-Y',strtotime($toDate)) }}</td></tr>
    @if (@$vendorName->name)
    <tr><td>Suppler Name: {{ @$vendorName->name }}</td></tr>
    @endif                                
    @endif

    @if ($btnPrintRecord == "Print Record")
    <tr><td>Lifting History On {{ date('d-m-Y',strtotime($fromDate)) }} To {{ date('d-m-Y',strtotime($toDate)) }}</td></tr>                
    @endif
</table>

<div id="pad-bottom"></div>

@if ($btnPrintSummary == "Print Summary")
<table id="report-table">
    <thead>
        <tr>
            <th width="20px">Sl</th>
            <th>Vendor Name</th>
            <th>Product Information</th>
        </tr>
    </thead>

    <tbody>
        @php
        $sl = 1;
        $totalQty = 0;
        $totalPrice = 0;
        @endphp
        @foreach ($liftingSummaries as $liftingSummary)
        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ $liftingSummary->vendorName }}</td>
            <td>
                <table style="width: 100%">
                    <thead class="bg-success">
                        <tr>
                            <th>Category Name</th>
                            <th>Product Name</th>
                            <th>Store</th>
                            <th width="100px">Total Lifting</th>
                            <th width="110px">Total Amount</th>
                        </tr>
                    </thead>
                    <?php
                    $qty = 0;
                    $amount = 0;
                    $liftingSum = DB::table('view_lifting_record')
                            ->select('productName',
                                    'productModelNo',
                                    'vendorName',
                                    'categoryName',
                                    'tbl_stores.name as storeName',
                                    DB::raw('SUM(productQty) as totalLifting'),
                                    DB::raw('SUM(amount) as totalLiftingPrice')
                            )
                            ->leftjoin('tbl_stores', 'tbl_stores.id', '=', 'view_lifting_record.storeOrShowroomId')
                            ->orWhere(function ($query) use ($fromDate, $toDate, $vendor, $category, $product, $type, $store, $liftingSummary) {
                                if (!empty($fromDate)) {
                                    $query->whereBetween('liftingDate', array($fromDate, $toDate));
                                }

                                if ($category) {
                                    $query->whereIn('categoryId', $category);
                                }

                                if ($product) {
                                    $query->whereIn('productId', $product);
                                }

                                if ($type) {
                                    $query->where('productType', $type);
                                }

                                if ($store) {
                                    $query->where('storeOrShowroomType', 'store')
                                    ->where('storeOrShowroomId', $liftingSummary->storeOrShowroomId);
                                }
                            })
                            ->where('vendorId', $liftingSummary->vendorId)
                            ->groupBy('productId')
                            ->groupBy('vendorId')
                            ->orderBy('productName', 'asc')
                            ->get();
                    ?>
                    @foreach ($liftingSum as $liftingS)
                    <?php
                    $qty += $liftingS->totalLifting;
                    $amount += $liftingS->totalLiftingPrice;
                    ?>
                    <tr>
                        <td>{{ $liftingS->categoryName }}</td>
                        <td>{{ $liftingS->productName }} - ({{ $liftingS->productModelNo }})</td>
                        <td>{{ $liftingS->storeName }}</td>
                        <td align="right">{{ $liftingS->totalLifting }}</td>
                        <td align="right">{{ number_format($liftingS->totalLiftingPrice, 2, '.', '') }}</td>
                    </tr>
                    @endforeach
                    <tfoot>
                        <tr>
                            <th colspan="3">Total</th>
                            <th align="right">{{$qty}}</th>
                            <th align="right">{{ number_format($amount, 2, '.', '') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </td>
        </tr>
        <?php
        $totalQty += $qty;
        $totalPrice += $amount;
        ?>
        @endforeach
    </tbody>
</table>
@endif

@if ($btnPrintRecord == "Print Record")
<table id="report-table">
    <thead class="thead-light">
        <tr>
            <th width="20px">Sl</th>
            <th>Date</th>
            <th>Lifting No</th>
            <th>Vendor</th>
            <th>Store</th>
            <th>Category</th>
            <th>Product</th>
            <th>Serial</th>
            <th>Model</th>
            <th>Color</th>
            <th>Qty</th>
            <th>Price</th>
        </tr>
    </thead>

    <tbody>
        @php
        $sl = 1;
        $totalQty = 0;
        $totalPrice = 0;
        @endphp
        @foreach ($liftingRecords as $liftingRecord)
        @php
        $totalQty = $totalQty + $liftingRecord->productQty;
        $totalPrice = $totalPrice + $liftingRecord->price;
        @endphp
        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ date('d-m-Y',strtotime($liftingRecord->liftingDate )) }}</td>
            <td>{{ $liftingRecord->liftingNo }}</td>
            <td>{{ $liftingRecord->vendorName }}</td>
            <td>{{ $liftingRecord->storeOrShowroomName }}</td>
            <td>{{ $liftingRecord->categoryName }}</td>
            <td>{{ $liftingRecord->productName }}</td>
            <td>{{ $liftingRecord->productSerialNo }}</td>
            <td>{{ $liftingRecord->productModelNo }}</td>
            <td>{{ $liftingRecord->productColor }}</td>
            <td>{{ $liftingRecord->productQty }}</td>
            <td>{{ number_format($liftingRecord->amount, 2, '.', '') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<div id="pad-bottom"></div>

<table  id="report-table">
    <tfoot>
        <tr>
            <th style="text-align: right;"><b>Total Qty : </b></th>
            <td style="text-align: right;">{{ $totalQty }}</td>
        </tr>

        <tr>
            <th style="text-align: right;"><b>Total Price : </b></th>
            <td style="text-align: right;">{{ number_format($totalPrice, 2, '.', '') }}</td>
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

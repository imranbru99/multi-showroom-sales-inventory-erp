@extends('admin.layouts.masterPrint')
@php
error_reporting(0);
@endphp
@section('content')
<table id="report-header">
    @if ($btnPrintSummary == "Print Summary")
    <tr><td>Lifting Return Summary On {{ date('d-m-Y',strtotime($fromDate)) }} To {{ date('d-m-Y',strtotime($toDate)) }}</td></tr>
    @if (@$vendorName->name)
    <tr><td>Suppler Name: {{ @$vendorName->name }}</td></tr>
    @endif                                
    @endif

    @if ($btnPrintRecord == "Print Record")
    <tr><td>Lifting Return History On {{ date('d-m-Y',strtotime($fromDate)) }} To {{ date('d-m-Y',strtotime($toDate)) }}</td></tr>                
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
        @foreach ($liftingReturnSummaries as $liftingReturnSummary)
        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ $liftingReturnSummary->vendorName }}</td>
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
                    $liftingSum = DB::table('view_lifting_return_record')
                            ->select('productName',
                                    'productModelNo',
                                    'vendorName',
                                    'categoryName',
                                    'tbl_stores.name as storeName',
                                    DB::raw('SUM(productQty) as totalLifting'),
                                    DB::raw('SUM(amount) as totalLiftingPrice')
                            )
                            ->leftjoin('tbl_stores', 'tbl_stores.id', '=', 'view_lifting_return_record.storeOrShowroomId')
                            ->orWhere(function ($query) use ($fromDate, $toDate, $vendor, $category, $product, $type, $store, $liftingReturnSummary) {
                                if (!empty($fromDate)) {
                                    $query->whereBetween('liftingReturnDate', array($fromDate, $toDate));
                                }

                                if ($category) {
                                    $query->whereIn('categoryId', $category);
                                }

                                if ($product) {
                                    $query->whereIn('productId', $product);
                                }

                                if ($store) {
                                    $query->where('storeOrShowroomType', 'store')
                                    ->where('storeOrShowroomId', $liftingReturnSummary->storeOrShowroomId);
                                }
                            })
                            ->where('vendorId', $liftingReturnSummary->vendorId)
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
<table  id="report-table">
    <thead class="thead-light">
        <tr>
            <th>SL</th>
            <th>Date</th>
            <th>Return No</th>
            <th>Vendor</th>
            <th>Company Name</th>
            <th>Category</th>
            <th>Product</th>
            <th>Serial</th>
            <th>Model</th>
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
        @foreach ($liftingReturnRecords as $liftingReturnRecord)
        @php
        $totalQty = $totalQty + $liftingReturnRecord->productQty;
        $totalPrice = $totalPrice + $liftingReturnRecord->price;
        @endphp
        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ date('d-m-Y',strtotime($liftingReturnRecord->liftingReturnDate))}}</td>
            <td>{{ $liftingReturnRecord->liftingReturnNo }}</td>
            <td>{{ $liftingReturnRecord->vendorName }}</td>
            <td>{{ $liftingReturnRecord->storeOrShowroomName }}</td>
            <td>{{ $liftingReturnRecord->categoryName }}</td>
            <td>{{ $liftingReturnRecord->productName }}</td>
            <td>{{ $liftingReturnRecord->productSerialNo }}</td>
            <td>{{ $liftingReturnRecord->productModelNo }}</td>
            <td>{{ $liftingReturnRecord->productQty }}</td>
            <td>{{ $liftingReturnRecord->price }}</td>
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
            <td style="text-align: right;">{{ $totalPrice }}</td>
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

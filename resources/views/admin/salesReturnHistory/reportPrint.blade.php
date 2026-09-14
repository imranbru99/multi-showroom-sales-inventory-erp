@extends('admin.layouts.masterPrint')
@php
error_reporting(0);
@endphp
@section('custome-css')
<style type="text/css">
    #lifting-info {
        font-family: Times, "Times New Roman", serif;
        width: 100%;
        border-collapse: collapse;
        border-style: dotted;
    }

    #lifting-info td {
        padding: 5px;
        border-bottom: 1px solid #ddd;
    }
</style>
@endsection

@php
$storeOrShowroom = DB::table('view_store_and_showroom')
->select('name as storeOrShowroomName')
->where('type', $lifting->store_or_showroom_type)
->where('id', $lifting->store_or_showroom_id)
->first();
@endphp

@section('content')
<table id="report-header">
    <tr>
        <td>Dealer Sales Return History</td>
    </tr>
</table>

<div id="pad-bottom"></div>

<table id="report-table">
    <thead class="thead-light">
        <tr>
            <th width="15px">SL#</th>
            <th width="80px">Date</th>
            <th width="130px">Dealer Name</th>
            <th width="30px">Category Name</th>
            <th width="80px">Product Name</th>
            @if($type == 'warranty_product')
            <th width="90px">Model</th>
            <th width="90px">Serial No</th>
            @endif
            <th width="80px">Qty</th>
            <th width="80px">Amount</th>
        </tr>
    </thead>

    <tbody>
        @php
        $sl = 1;
        $totalQty = 0;
        $totalAmount = 0;
        @endphp

        @foreach ($productReturnHistories as $productReturnHistory)

        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ date('d-m-Y', strtotime($productReturnHistory->return_date)) }}</td>
            <td>{{ @$productReturnHistory->dealer->name }}</td>
            <td>{{ @$productReturnHistory->product->category->name }}</td>
            <td>{{ @$productReturnHistory->product->name }}</td>
            @if($type == 'warranty_product')
            <td>{{ @$productReturnHistory->product->model_no }}</td>
            <td>{{ @$productReturnHistory->serial_no }}</td>
            @endif
            <td align="right">{{ $productReturnHistory->qty }}</td>
            <td align="right">{{ number_format($productReturnHistory->amount, 2, '.', '') }}</td>
        </tr>

        @php
        $totalQty += $productReturnHistory->qty;
        $totalAmount += $productReturnHistory->amount;
        @endphp

        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <td colspan="@if($type == 'warranty_product') 7 @else 5 @endif" align="right"><b>Total Quantity</b></td>

            <td style="text-align: right;"><b>{{ $totalQty }}</b></td>
            <td style="text-align: right;"><b>{{ number_format($totalAmount, 2, '.', '') }}</b></td>
        </tr>
    </tfoot>
</table>
<div class="row">
    <div class="col-md-12 text-right">
        <?php date_default_timezone_set('Asia/Dhaka'); ?>
        <p>Print Date & Time : <?php echo date('d-m-Y h:i:sa'); ?></p>
    </div>
</div>
@endsection
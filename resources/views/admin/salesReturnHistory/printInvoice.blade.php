@extends('admin.layouts.masterPrint')

@section('custome-css')
<style>
    #report-table td,
    #report-table th {
        border: 1px solid #ddd;
    }

    #report-table tbody td {
        vertical-align: top;
    }

    #invoice-table {
        width: 100%;
        border-collapse: collapse;
    }

    #invoice-table td {
        width: 50%;
        border: 0px solid black;
    }

    #invoice-header {
        background-color: lightgray;
        width: 100%;
        padding: 5px;
        text-align: center;
        font-weight: bold;
        font-size: 20px;
    }

    #invoice-footer {
        background-color: lightgray;
        width: 100%;
    }

    #invoice-footer th {
        border: 0px solid white;
        padding: 5px;
        text-align: right;
        font-size: 14px;
        width: 180px;
    }

    #invoice-footer td {
        border: 1px solid black;
        padding: 5px;
    }

</style>
@endsection

@section('content')
<table id="invoice-header">
    <tr>
        <td>Sales Return Invoice</td>
    </tr>
</table>

<div id="pad-bottom"></div>

<table width="100%">
    <tbody>
        <tr>
            <td width="105px"><b>Issue No</b></td>
            <td width="10px"><b>:</b></td>
            <td>#{{ @$returnInfo->issue_no }}</td>

            <td align="right" width="60px"><b>Date</b></td>
            <td align="right" width="10px"><b>:</b></td>
            <td align="right">{{ date('d-m-Y', strtotime($returnInfo->return_date)) }}</td>
        </tr>
        <tr>
            <td width="105px"><b>Dealer Name</b></td>
            <td width="10px"><b>:</b></td>
            <td>{{ $returnInfo->dealerName }}</td>

            <td align="right" width="60px"><b>Dealer Code</b></td>
            <td align="right" width="10px"><b>:</b></td>
            <td align="right">{{ $returnInfo->dealerCode }}</td>
        </tr>
        <tr>
            <td width="105px"><b>Upazila</b></td>
            <td width="10px"><b>:</b></td>
            <td>{{ $returnInfo->upazilaEnglishName }}</td>

            <td align="right" width="105px"><b>Phone</b></td>
            <td align="right" width="10px"><b>:</b></td>
            <td align="right">{{ $returnInfo->dealerMobile }}</td>
        </tr>
        <tr>
            <td width="105px"><b>Address</b></td>
            <td width="10px"><b>:</b></td>
            <td>{{ $returnInfo->dealerAddress }}</td>

            <td align="right" width="105px"><b>Sale By</b></td>
            <td align="right" width="10px"><b>:</b></td>
            <td align="right">{{ $returnInfo->staffName }}</td>
        </tr>
    </tbody>
</table>

<div id="pad-bottom"></div>

@if($returnInfo->product_type == 'warranty_product')
<table id="report-table">
    <thead>
        <tr>
            <th width="20px">SL#</th>
            <th>Name</th>
            <th width="110px">Model</th>
            <th width="115px">Serial No</th>
            <th width="40px">Qty</th>
            <th width="60px">MRP</th>
            <th width="60px">Commission</th>
            <th width="60px">Amount</th>
        </tr>
    </thead>

    <tbody>
        @php
        $sl = 1;
        $totalQty = 0;
        $totalAmount = 0;
        @endphp
        @foreach($groupByProductIds as $key => $groupByProduct)
        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{$groupByProduct[0]->productName }}</td>
            <td>{{ $groupByProduct[0]->modelName }}</td>
            <td>
                @foreach ($groupByProduct as $uniqueProduct)
                {{ $uniqueProduct->serial_no }},
                @endforeach
            </td>
            <td align="right">{{ $groupByProduct->sum('qty') }}</td>
            <td align="right">{{ number_format($groupByProduct[0]->price, 2, '.', '') }}</td>
            <td align="right">{{ $groupByProduct[0]->commission_rate }}</td>
            <td align="right">{{ number_format($groupByProduct->sum('amount'), 2, '.', '') }}</td>
        </tr>
        @php
        $totalQty += $groupByProduct->sum('qty');
        $totalAmount += $groupByProduct->sum('amount');
        @endphp
        @endforeach
        <tr>
            <td colspan="4">Total:</td>
            <td align="right">{{ $totalQty}}</td>
            <td></td>
            <td></td>
            <td align="right">{{ number_format($totalAmount, 2, '.', '') }}</td>
        </tr>

    </tbody>
</table>
@else
<table id="report-table">
    <thead>
        <tr>
            <th width="20px">SL#</th>
            <th>Name</th>
            <th width="40px">Qty</th>
            <th width="60px">MRP</th>
            <th width="60px">Commission</th>
            <th width="60px">Amount</th>
        </tr>
    </thead>

    <tbody>
        @php
        $sl = 1;
        $totalQty = 0;
        $totalAmount = 0;
        @endphp
        @foreach($groupByProductIds as $key => $groupByProduct)
        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{$groupByProduct[0]->productName }}</td>
            <td align="right">{{ $groupByProduct->sum('qty') }}</td>
            <td align="right">{{ number_format($groupByProduct[0]->price, 2, '.', '') }}</td>
            <td align="right">{{ $groupByProduct[0]->commission_rate }}</td>
            <td align="right">{{ number_format($groupByProduct->sum('amount'), 2, '.', '') }}</td>
        </tr>
        @php
        $totalQty += $groupByProduct->sum('qty');
        $totalAmount += $groupByProduct->sum('amount');
        @endphp
        @endforeach
        <tr>
            <td colspan="2">Total:</td>
            <td align="right">{{ $totalQty}}</td>
            <td></td>
            <td></td>
            <td align="right">{{ number_format($totalAmount, 2, '.', '') }}</td>
        </tr>

    </tbody>
</table>
@endif

<div id="pad-bottom"></div>

<table id="invoice-footer">
    <tbody>
        <tr>
            <th>Total Amount : </th>
            <td align="right">{{ number_format($totalAmount, 2, '.', '') }}</td>
        </tr>

        <tr>
            <th>In Words : </th>
            @php
            $inWords = \App\HelperClass::numberToWords($totalAmount);
            @endphp
            <td>{{ $inWords }} Taka Only.</td>
        </tr>
    </tbody>
</table>

<div style="padding-bottom: 60px;"></div>

<table id="invoice-table">
    <tr>
        <td>
            <span>
                <h3 class="overline">Received By</h3>
            </span>
        </td>
        <td align="right">
            <span>
                <span>
                    <h3 class="overline">Authorized By</h3>
                </span>
            </span>
        </td>
    </tr>
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

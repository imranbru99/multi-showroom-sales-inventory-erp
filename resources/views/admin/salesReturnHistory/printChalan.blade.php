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
        <td>Sales Return Challan</td>
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

<table id="report-table">
    <thead>
        <tr>
            <th width="20px">SL#</th>
            <th>Name</th>
            @if($returnInfo->product_type == 'warranty_product')
            <th width="110px">Model</th>
            <th width="115px">Serial No</th>
            @endif
            <th width="40px">Qty</th>
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
            @if($returnInfo->product_type == 'warranty_product')
            <td>{{ $groupByProduct[0]->modelName }}</td>
            <td>
                @foreach ($groupByProduct as $uniqueProduct)
                {{ $uniqueProduct->serial_no }},
                @endforeach
            </td>
            @endif
            <td align="right">{{ $groupByProduct->sum('qty') }}</td>

        </tr>

        @endforeach
    </tbody>
</table>

<div id="pad-bottom"></div>

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

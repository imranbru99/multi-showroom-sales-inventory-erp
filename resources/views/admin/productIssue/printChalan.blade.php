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

    #chalan-table {
        width: 100%;
        border-collapse: collapse;
    }

    #chalan-table td {
        width: 50%;
        border: 0px solid black;
    }

    #chalan-header {
        background-color: lightgray;
        width: 100%;
        padding: 5px;
        text-align: center;
        font-weight: bold;
        font-size: 20px;
    }

    #chalan-footer {
        background-color: lightgray;
        width: 100%;
    }

    #chalan-footer th {
        border: 0px solid white;
        padding: 5px;
        text-align: right;
        font-size: 14px;
        width: 180px;
    }

    #chalan-footer td {
        border: 1px solid black;
        padding: 5px;
    }

</style>
@endsection

@section('content')
<table id="chalan-header">
    <tr>
        <td>Challan</td>
    </tr>
</table>

<div id="pad-bottom"></div>

<table width="100%">
    <tbody>
        <tr>
            <td width="105px"><b>Issue No</b></td>
            <td width="10px"><b>:</b></td>
            <td>#{{ @$productIssue->issue_no }}</td>

            <td align="right" width="105px"><b>Date</b></td>
            <td align="right" width="10px"><b>:</b></td>
            <td align="right">{{ date('d-m-Y', strtotime($productIssue->date)) }}</td>
        </tr>
        <tr>
            <td width="105px"><b>Dealer Name</b></td>
            <td width="10px"><b>:</b></td>
            <td>{{ $productIssue->dealerName }}</td>

            <td align="right" width="105px"><b>Dealer Code</b></td>
            <td align="right" width="10px"><b>:</b></td>
            <td align="right">{{ $productIssue->dealerCode }}</td>

        </tr>
        <tr>
            <td width="105px"><b>Territory</b></td>
            <td width="10px"><b>:</b></td>
            <td>{{ @$productIssue->territoryName }}</td>

            <td align="right" width="105px"><b>Phone</b></td>
            <td align="right" width="10px"><b>:</b></td>
            <td align="right">{{ $productIssue->dealerMobile }}</td>
        </tr>
        <tr>
            <td width="105px"><b>Address</b></td>
            <td width="10px"><b>:</b></td>
            <td>{{ $productIssue->dealerAddress }}</td>

            <td align="right" width="105px"><b>Sales By</b></td>
            <td align="right" width="10px"><b>:</b></td>
            <td align="right">{{ $productIssue->staffName }}</td>
        </tr>
    </tbody>
</table>

<div id="pad-bottom"></div>

@if($productIssue->product_type == 'warranty_product')
<table id="report-table">
    <thead>
        <tr>
            <th width="20px">SL#</th>
            <th>Name</th>
            <th width="120px">Model</th>
            <th width="115px">Serial No</th>
            <th width="40px">Qty</th>
        </tr>
    </thead>

    <tbody>
        @php
        $sl = 1;
        $totalQty = 0;
        @endphp


        @foreach ($groupByProductIds as $key => $groupByProduct)
        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ $groupByProduct[0]->productName }}</td>
            <td>{{ $groupByProduct[0]->model_no }}</td>
            <td>
                @foreach ($groupByProduct as $uniqueProduct)
                {{ $uniqueProduct->serial_no }},
                @endforeach
            </td>
            <td align="right">{{ $groupByProduct->sum('qty') }}</td>
        </tr>

        @php
        $totalQty += $groupByProduct->sum('qty');
        @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" align="right" style="font-weight: bold"> Total:</td>
            <td align="right">{{ $totalQty ?? '0' }}</td>
        </tr>
    </tfoot>
</table>
@else
<table id="report-table">
    <thead>
        <tr>
            <th width="20px">SL#</th>
            <th>Name</th>
            <th width="40px">Qty</th>
        </tr>
    </thead>

    <tbody>
        @php
        $sl = 1;
        $totalQty = 0;
        @endphp

        @foreach ($groupByProductIds as $key => $groupByProduct)
        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ $groupByProduct[0]->productName }}</td>
            <td align="right">{{ $groupByProduct->sum('qty') }}</td>
        </tr>

        @php
        $totalQty += $groupByProduct->sum('qty');
        @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2" align="right" style="font-weight: bold"> Total:</td>
            <td align="right">{{ $totalQty ?? '0' }}</td>
        </tr>
    </tfoot>
</table>
@endif


<div style="padding-bottom: 60px;"></div>

<table id="chalan-table">
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

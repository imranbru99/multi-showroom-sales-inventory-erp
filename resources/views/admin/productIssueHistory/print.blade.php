@extends('admin.layouts.masterPrint')

@section('content')
<table id="report-header">
    <tr>
        <td>Dealer Sales History ON {{ date('d-m-Y', strtotime($fromDate)) }} To {{ date('d-m-Y', strtotime($toDate)) }}
        </td>
    </tr>
</table>

<div id="pad-bottom"></div>

<table id="report-table">
    <thead>
        <tr>
            <th width="15px">SL#</th>
            <th width="80px">Date</th>
            <th width="130px">Dealer Name</th>
            <th width="30px">Category Name</th>
            <th width="80px">Product Name</th>
            <th width="90px">Model</th>
            <th width="90px">Serial No</th>
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

        @foreach ($productIssueHistories as $groupByProduct)
        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ date('d-m-Y',strtotime($groupByProduct->date))}}</td>
            <td>{{ $groupByProduct->dealerName}}</td>
            <td>{{ $groupByProduct->categoryName }}</td>
            <td>{{ $groupByProduct->productName }}</td>
            <td>{{ $groupByProduct->modelNo }}</td>
            <td>
                @php
                $uniqueProducts = explode(',', $groupByProduct->totalProductSerialNO);
                @endphp
                @foreach ($uniqueProducts as $uniqueProduct)
                {{ $uniqueProduct }},
                @endforeach
            </td>
            <td align="right">{{ $groupByProduct->totalIssueQty }}</td>
            <td align="right">{{ number_format($groupByProduct->totalIssueAmount, 2, '.', '') }}</td>
        </tr>

        @php
        $totalQty += $groupByProduct->totalIssueQty;
        $totalAmount += $groupByProduct->totalIssueAmount;
        @endphp
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <td colspan="7" align="right">
                <h3>Total</h3>
            </td>
            <td align="right"><b>{{ $totalQty }}</b></td>
            <td align="right"><b>{{ number_format($totalAmount, 2, '.', '') }}</b></td>
        </tr>
    </tfoot>
</table>
<div class="row">
    <div class="col-md-12 text-right">
        <?php 
                date_default_timezone_set("Asia/Dhaka");
            ?>
        <p>Print Date & Time : <?php echo  date("d-m-Y h:i:sa");?></p>
    </div>
</div>

@endsection
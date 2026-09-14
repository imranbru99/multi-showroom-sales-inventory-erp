@extends('admin.layouts.masterPrint')

@section('content')
<table id="report-header">
    <tr>
        <td>Production Sales History ON {{ date('d-m-Y', strtotime($fromDate)) }} To {{ date('d-m-Y', strtotime($toDate)) }}
        </td>
    </tr>
</table>

<div id="pad-bottom"></div>

<table id="report-table">
    <thead>
        <tr>
            <th width="15px">SL#</th>
            <th width="130px">Rqusion Name</th>
            <th width="80px">Date</th>
            <th width="30px">Category Name</th>
            <th width="80px">Product Name</th>
            <th width="90px">Model</th>
            <th width="90px">Serial No</th>
            <th width="80px">Qty</th>
            
        </tr>
    </thead>

    <tbody>

        @php
        $sl = 1;
        $totalQty = 0;
      
        @endphp

        @foreach ($productIssueHistories as $groupByProduct)
        {{-- @foreach ($step1 as $step2)
                   @foreach ($step2 as $step3)
                    @foreach ($step3 as $groupByProduct) --}}
        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ $groupByProduct->staffName}}</td>
            <td>{{ date('d-m-Y',strtotime($groupByProduct->date))}}</td>
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
        </tr>

        @php
        $totalQty += $groupByProduct->totalIssueQty;
        @endphp
        {{-- @endforeach 
                @endforeach 
                     @endforeach  --}}
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <td colspan="7" align="right">
                <h3>Total</h3>
            </td>
            <td align="right"><b>{{ $totalQty }}</b></td>
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
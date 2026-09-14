@extends('admin.layouts.masterPrint')

@section('content')
<table id="report-header">
    <tr>
        <td>
            {{ $title }}

            @if ($staffName)
                - {{ $staffName->name }}
            @endif
            
            @if ($distributionName)
                - {{ $distributionName }}
            @endif
        </td>
    </tr>
</table>

<div id="pad-bottom"></div>


<table id="report-table" class="table table-bordered table-sm">
    <thead>
        <tr>
            <th>SL</th>
            <th>Dealer Name</th>
            <th>Contact No</th>
            <th class="text-center">Sales</th>
            <th class="text-center">Collection</th>
            <th class="text-center">Outstanding</th>
            <th class="text-center">Last Collection</th>
            <th class="text-center">Collection Duration</th>
            <th class="text-center">Last Sales</th>
            <th class="text-center">Sales Duration</th>
            <th class="text-center">Sales By</th>
            <th>Due Ratio</th>
        </tr>
    </thead>

    <tbody>
        <?php
        $sl = 1;
        $totalSales = 0;
        $totalCollection = 0;
        $totalDue = 0;
        ?>

        @foreach ($outstanding as $dealer)
        <?php
        if ($dealer['outstanding'] <= 0) {
            continue;
        }

        $totalSales += $dealer['purchase'];
        $totalCollection += $dealer['collection'];
        $totalDue += $dealer['outstanding'];
        ?>
        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ $dealer['dealer_name'] }}</td>
            <td>{{ $dealer['contact_no'] }}</td>
            <td align="right">{{ number_format($dealer['purchase'], 2, '.', '') }}</td>
            <td align="right">{{ number_format($dealer['collection'], 2, '.', '') }}</td>
            <td align="right">{{ number_format($dealer['outstanding'], 2, '.', '') }}</td>
            <td align="center">{{ $dealer['last_collection'] }}</td>
            <td align="center">{{ $dealer['collection_duration'] }} Day(s)</td>
            <td align="center">{{ $dealer['last_sales'] }}</td>
            <td align="center">{{ $dealer['sales_duration'] }} Day(s)</td>
            <td>{{ $dealer['sale_by'] }}</td>
            <td>
                <div class="progress">
                    <div class="progress-bar progress-bar-success" role="progressbar"
                         style="width:{{ round($dealer['averagePercent']) }}%; height:5px;"
                         aria-valuenow="{{ round($dealer['averagePercent']) }}" aria-valuemin="0"
                         aria-valuemax="100">
                    </div>
                </div>
                <span class="progress-parcent">{{ $dealer['averagePercent'] }}%</span>
            </td>
        </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <th align="right" colspan="3">Total</th>
            <th align="right">{{ number_format($totalSales, 2, '.', '') }}</th>
            <th align="right">{{ number_format($totalCollection, 2, '.', '') }}</th>
            <th align="right">{{ number_format($totalDue, 2, '.', '') }}</th>
            <th colspan="6"></th>
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

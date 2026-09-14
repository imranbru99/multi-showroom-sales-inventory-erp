@extends('admin.layouts.masterPrint')

@section('content')
@php
use App\InstallmentCollection;
use App\CustomerRegistrationSetup;
use App\Product;
@endphp
<table id="report-table">
    <caption>
        {{ @$title }}
        <br>
        Of {{ date('M Y', strtotime($fromDate)) }}
    </caption>
    <thead>
        <tr>
            <th width="20px">Sl</th>
            <th width="100px">Group</th>
            <th width="200px">Group Members</th>
            <th width="100px">Month-Year</th>
            <th width="100px">Target</th>
            <th width="100px">Achivement</th>
            <th width="100px">Difference</th>
            <th width="50px">Achive % </th>
            <th width="50px">Salary Eligible</th>
        </tr>
    </thead>

    <tbody>
        <?php
        $sl = 1;
        $totalTarget = 0;
        $totalAchivement = 0;
        $totalDifference = 0;
        $totalsalaryAchieve = 0;
        $totalAverage = 0;
        $total = 0;
        if (!empty($targets) && count($targets) > 0) {
            $total = count($targets);
        }
        ?>
        @foreach ($targets as $target)
        <?php
        $totalTarget += $target['target'];
        $totalAchivement += $target['collection'];
        $totalDifference += $target['difference'];
        $totalsalaryAchieve += $target['salaryAchieve'];
        $totalAverage += $target['average'];

        $comma = '';
        if (!empty($target['members']) && count($target['members']) > 1) {
            $comma = ',';
        }
        $monthcomma = '';
        if (!empty($target['month']) && count($target['month']) > 1) {
            $monthcomma = ',';
        }
        ?>
        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ $target['group'] }}</td>
            <td>
                @foreach ($target['members'] as $key => $value)
                {{ $value }} {{ $comma }}
                @endforeach
            </td>
            <td>
                @foreach ($target['month'] as $key => $value)
                {{ $value }} {{ $comma }}
                @endforeach
            </td>
            <td style="text-align: right;">{{ $target['target'] }}</td>
            <td style="text-align: right;">{{ $target['collection'] }}</td>
            <td style="text-align: right;">{{ $target['difference'] }}</td>
            <td style="text-align: right;">{{ $target['average'] }}%</td>
            <td style="text-align: right;">{{ $target['salaryAchieve'] }}</td>
        </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <th colspan="4">Total</th>
            <th style="text-align: right;">{{ @$totalTarget }}</th>
            <th style="text-align: right;">{{ @$totalAchivement }}</th>
            <th style="text-align: right;">{{ @$totalDifference }}</th>
            <th style="text-align: right;">{{ round($totalAverage / $total, 2) }}%</th>
            <th style="text-align: right;">{{ @$totalsalaryAchieve }}</th>
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

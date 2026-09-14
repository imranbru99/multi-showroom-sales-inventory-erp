@php
use App\SalesReturn;
use App\AdvanceCollection;
@endphp
@extends('admin.layouts.masterPrint')

@section('content')
<table id="report-header">
    <tr>
        <td>Dealer Realization For The Month Of {{ date('F', mktime(0, 0, 0, $month, 10)) }} - {{ $year }}
        </td>
    </tr>
</table>

<div id="pad-bottom"></div>

<table id="report-table">
    <thead>
        <tr>
            <th width="20px" rowspan="2" style="vertical-align: middle;">Sl</th>
            <th width="150px" rowspan="2" style="vertical-align: middle;">Dealer Name</th>
            <th width="50px" rowspan="2" style="vertical-align: middle;">Previous Year</th>
            <th colspan="4" style="text-align: center;"><b>For The Year Of {{ $year == '' ? '' : $year }}</b></th>
            <th colspan="4" style="text-align: center;"><b>For The Month Of
                    {{ $month == '' ? '' : date('F', mktime(0, 0, 0, $month, 10)) }}</b></th>
                    <th width="100px" rowspan="2" style="vertical-align: middle;">Current Outstanding</th>
            <th width="120px" rowspan="2" style="vertical-align: middle;">Credit %</th>
        </tr>
        <tr>
            <th width="70px">Sales</th>
            <th width="80px">Collection</th>
            <th width="80px">Return</th>
            <th width="70px">Due</th>
            <th width="7px">Sales</th>
            <th width="80px">Collection</th>
            <th width="80px">Return</th>
            <th width="70px">Due</th>
        </tr>
    </thead>

    <tbody>
        @php
        $sl = 1;

        $totalPreviousrealization = 0;
        $totalyearlySales = 0;
        $totalYearlyCollection = 0;
        $yearlyReturn = 0;
        $totalYearlyRealization = 0;
        $totalMonthlySales = 0;
        $totalMonthlyCollection = 0;
        $monthlyReturn = 0;
        $totalMonthlyRealization = 0;
        $totalCurrentRealization = 0;
        @endphp

        @foreach ($data as $row)
	@php
           if( $row['previousBalance'] == 0 && $row['year']['sales'] == 0 && $row['year']['collection'] == 0 &&
           $row['year']['return'] == 0 && $row['year']['balance'] == 0 && $row['month']['sales'] == 0 && 
           $row['month']['collection'] == 0 && $row['month']['return'] == 0 && $row['month']['balance'] == 0){
               continue;
           }
        @endphp
        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ $row['dealerName'] }}</td>
            <td align="right">{{ round($row['previousBalance'], 2) }}</td>
            <td align="right">{{ round($row['year']['sales'], 2) }}</td>
            <td align="right">{{ round($row['year']['collection'], 2) }}</td>
            <td align="right">{{ round($row['year']['return'], 2) }}</td>
            <td align="right">
                {{ round($row['year']['balance'], 2) }}
            </td>
            <td align="right">{{ round($row['month']['sales'], 2) }}</td>
            <td align="right">{{ round($row['month']['collection'], 2) }}</td>
            <td align="right">{{ round($row['month']['return'], 2) }}</td>
            <td align="right">
                {{ round($row['month']['balance'], 2) }}
            </td>
            <td align="right">
                {{ round($row['currentOutstanding'], 2) }}
            </td>
            <td align="right">
                {{ round($row['creditP'], 2) }} %
            </td>
        </tr>

        @php
        $totalPreviousrealization += $row['previousBalance'];
        $totalyearlySales += $row['year']['sales'];
        $totalYearlyCollection += $row['year']['collection'];
        $yearlyReturn += $row['year']['return'];
        $totalYearlyRealization += $row['year']['balance'];
        $totalMonthlySales += $row['month']['sales'];
        $totalMonthlyCollection += $row['month']['collection'];
        $monthlyReturn += $row['month']['return'];
        $totalMonthlyRealization += $row['month']['balance'];
        $totalCurrentRealization += $row['currentOutstanding'];
        @endphp

        @endforeach
    </tbody>

    <tfoot>

        @php
            $totalDue = $totalPreviousrealization + $totalYearlyRealization + $totalMonthlySales; //ex: 10000

            if($totalDue ==0 ){
                $totalDue = 1;
            }

            $collection = $totalMonthlyCollection + $monthlyReturn; // ex: 2500
            $dueMinusCollection = $totalDue - $collection; //ex: 7500
            $creditP = ($dueMinusCollection / $totalDue) * 100; //ex: 75%;
        @endphp

        <tr>
            <th style="text-align: right;" colspan="2"><b>Total</b></th>
            <td style="text-align: right;"><b>{{ round($totalPreviousrealization, 2) }}</b></td>
            <td style="text-align: right;"><b>{{ round($totalyearlySales, 2) }}</b></td>
            <td style="text-align: right;"><b>{{ round($totalYearlyCollection, 2) }}</b></td>
            <td style="text-align: right;"><b>{{ round($yearlyReturn, 2) }}</b></td>
            <td style="text-align: right;"><b>{{ round($totalYearlyRealization, 2) }}</b></td>
            <td style="text-align: right;"><b>{{ round($totalMonthlySales, 2) }}</b></td>
            <td style="text-align: right;"><b>{{ round($totalMonthlyCollection, 2) }}</b></td>
            <td style="text-align: right;"><b>{{ round($monthlyReturn, 2) }}</b></td>
            <td style="text-align: right;"><b>{{ round($totalMonthlyRealization, 2) }}</b></td>
            <td style="text-align: right;"><b>{{ round($totalCurrentRealization, 2) }}</b></td>
            <td style="text-align: right;"><b>{{ round($creditP, 2) }} %</b></td>
        </tr>
    </tfoot>
</table>

<div id="pad-bottom"></div>

<table id="report-table">
</table>

<div class="row">
    <div class="col-md-12 text-right">
        <?php date_default_timezone_set('Asia/Dhaka'); ?>
        <p>Print Date & Time : <?php echo date('d-m-Y h:i:sa'); ?></p>
    </div>
</div>
@endsection
@extends('admin.layouts.masterPrint')

@section('content')
<table id="report-header">
    <tr>
        <td>Dealer Name : {{ $delearName->name }}</td>
    </tr>
    <tr>
        <td>Dealer Statement ON {{ date('d-m-Y', strtotime($fromDate)) }} To {{ date('d-m-Y', strtotime($toDate)) }}
        </td>
    </tr>
</table>

<div id="pad-bottom"></div>

<table id="report-table">
    <thead>
        <tr>
            <th colspan="8" style="text-align: right;"><b>Previous Balance</b></th>
            <th style="text-align: right;">{{ $previousBalance }}</th>
        </tr>
        <tr>
            <th width="20px">Date</th>
            <th width="80px">Invoice</th>
            <th width="80px">Particulars</th>
            <th width="80px">Advance</th>
            <th width="80px">Sales</th>
            <th width="90px">Collection</th>
            <th width="90px">Return</th>
            <th width="90px">Discount</th>
            <th width="80px">Balance</th>
        </tr>
    </thead>

    <tbody>
        @php
        $sl = 1;
        $totalSales = 0;
        $totalCollection = 0;
        $totalReturn = 0;
        $totalDiscount = 0;
        $balance = 0;
        @endphp
        @foreach ($data as $d)
        <?php
        if ($loop->iteration == 1) {
            if ($d['remark'] == 'Collection against advance') {
                $balance += $previousBalance + $d['sale'] - ($d['advance'] + $d['return'] + $d['discount']);
            } else {
                $balance += $previousBalance + $d['sale'] - ($d['advance'] + $d['collection'] + $d['return'] + $d['discount']);
            }
        } else {
            if ($d['remark'] == 'Collection against advance') {
                $balance += $d['sale'] - ($d['advance'] + $d['return'] + $d['discount']);
            } else {
                $balance += $d['sale'] - ($d['advance'] + $d['collection'] + $d['return'] + $d['discount']);
            }
        }
        $totalDiscount += $d['discount'];
        ?>
        <tr>
            <td>{{ date('d-m-Y', strtotime($d['date'])) }}</td>
            <td>{{ $d['invoice_no'] }}</td>
            <td>{{ $d['remark'] }}</td>
            <td align="right">{{ number_format($d['advance'], 2, '.', '') }}</td>
            <td align="right">{{ number_format($d['sale'], 2, '.', '') }}</td>
            <td align="right">{{ number_format($d['collection'], 2, '.', '') }}</td>
            <td align="right">{{ number_format($d['return'], 2, '.', '') }}</td>
            <td align="right">{{ number_format($d['discount'], 2, '.', '') }}</td>
            <td align="right">{{ number_format($balance, 2, '.', '') }}</td>
        </tr>
        @php
        $totalSales += $d['sale'];

        if ($d['remark'] != 'Collection against advance') {
        $totalCollection += $d['advance'];
        $totalCollection += $d['collection'];
        $totalReturn += $d['return'];
        }
        @endphp
        @endforeach
    </tbody>
</table>

<div id="pad-bottom"></div>

<table id="report-table">
    <tfoot>
        <tr>
            <th style="text-align: right;"><b>Total Sales : </b></th>
            <td style="text-align: right;">{{ number_format($totalSales, 2, '.', '') }}</td>
        </tr>

        <tr>
            <th style="text-align: right;"><b>Total Return : </b></th>
            <td style="text-align: right;">{{ number_format($totalReturn, 2, '.', '') }}</td>
        </tr>

        <tr>
            <th style="text-align: right;"><b>Total Collection : </b></th>
            <td style="text-align: right;">{{ number_format($totalCollection, 2, '.', '') }}</td>
        </tr>
        <tr>
            <th style="text-align: right;"><b>Total Discount : </b></th>
            <td style="text-align: right;">{{ number_format($totalDiscount, 2, '.', '') }}</td>
        </tr>

        <tr>
            <th style="text-align: right;"><b>Total Balance : </b></th>
            <td style="text-align: right;">
                {{ number_format($totalSales + $previousBalance - ($totalCollection + $totalReturn + $totalDiscount), 2, '.', '') }}
            </td>
        </tr>
    </tfoot>
</table>
<div class="row">
    <div class="col-md-12 text-right">
        <?php
        date_default_timezone_set('Asia/Dhaka');
        ?>
        <p>Print Date & Time : <?php echo date('d-m-Y h:i:sa'); ?></p>
    </div>
</div>
@endsection

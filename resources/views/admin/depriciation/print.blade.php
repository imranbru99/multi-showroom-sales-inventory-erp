@extends('admin.layouts.masterPrint')
@section('content')
<table id="report-header">
    <tr>
        <td>{{ $title }} On {{ date('d-m-Y', strtotime($fromDate)) }} To
            {{ date('d-m-Y', strtotime($toDate)) }}</td>
    </tr>
</table>

<div id="pad-bottom"></div>
<table id="report-table">
    <thead>
        <tr>
            <th>SL</th>
            <th class="text-center">Name of Assets</th>
            <th class="text-center">Opening Balance</th>
            <th class="text-center">Addition during the Year</th>
            <th class="text-center">Cost of Assets</th>
            <th class="text-center">Rate of Depreciation</th>
            <th class="text-center">Accumulated Depreciation</th>
            <th class="text-right">Net Value</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $totalPrevious = 0;
        $totalAddition = 0;
        $totalCost = 0;
        $totalNet = 0;
        $totalAccu = 0;
        ?>
        @foreach ($fixAssets as $fix)
        <?php
        $totalAmount = $fix['previous'] + $fix['amount'];
        if ($fix['name'] == 'Furniture & Fixtures') {
            $accu = $totalAmount * 0.15;
        } else {
            $accu = $totalAmount * 0.20;
        }

        $totalPrevious +=$fix['previous'];
        $totalAddition += $fix['amount'];
        $totalCost += $totalAmount;
        $totalAccu += $accu;
        $totalNet += ($totalAmount - $accu);
        ?>
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $fix['name'] }}</td>
            <td align='right'>{{ number_format($fix['previous'], 2, '.', '') }}</td>
            <td align='right'>{{ number_format($fix['amount'], 2, '.', '') }}</td>
            <td align='right'>{{ number_format($totalAmount , 2, '.', '') }}</td>
            <td align='right'>
                @if($fix['name'] == 'Furniture & Fixtures')
                15%
                @else
                20%
                @endif
            </td>
            <td align='right'>
                {{ number_format($accu, 2, '.', '') }}
            </td>
            <td align='right'>{{ number_format(($totalAmount - $accu), 2, '.', '') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background-color: gray;">
            <td colspan="2" align='left'><b>Total </b></td>
            <td align='right'>{{ number_format($totalPrevious, 2, '.', '') }}</td>
            <td align='right'>{{ number_format($totalAddition, 2, '.', '') }}</td>
            <td align='right'>{{ number_format($totalCost, 2, '.', '') }}</td>
            <td align='right'></td>
            <td align='right'>{{ number_format($totalAccu, 2, '.', '') }}</td>
            <td align='right'>{{ number_format($totalNet, 2, '.', '') }}</td>
        </tr>
    </tfoot>
</table>
<div id="pad-bottom"></div>

<style>
    tfoot td{
        color: #fff;
        font-weight: bold;
    }
</style>

@endsection

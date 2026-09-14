@extends('admin.layouts.masterPrint')
@section('content')
<style>
    .row {
        display: flex;
        flex-wrap: wrap;
        margin-right: -10px;
        margin-left: -10px;
    }
    .col-md-6 {
        /*flex: 0 0 50%;*/
        max-width: 50%;
        position: relative;
        width: 48%;
        min-height: 1px;
        padding-right: 10px;
        padding-left: 10px;
        float: left;
    }
</style>

<table id="report-header">
    <tr>
        <td>{{ $title }}</td>
    </tr>
</table>

<div id="pad-bottom"></div>
<div class="row">
    <div class="col-md-6">
        <table id="report-table">
            <thead>
                <tr>
                    <th colspan="2" align="center">ASSETS</th>
                    <th align="right">AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $cAsset = 0;
                $fAsset = 0;
                ?>
                @foreach ($currentFix as $current)
                <tr>
                    <td colspan="3" style="font-size: 18px;"><u>{{ $current['head'] }}:</u></td>
                </tr>
                @foreach ($current['childs'] as $key => $child)
                <tr>
                    <td>{{ $child['name'] }}</td>
                    <td align="center">:</td>
                    <td align="right">{{ number_format($child['amount'], 2, '.', '') }}</td>
                </tr>

                @if ($key == 3 && $current['head'] == 'Current Asset')
                <tr>
                    <td>Closing Stock</td>
                    <td align="center">:</td>
                    <td align="right">{{ number_format($closingStock, 2, '.', '') }}</td>
                </tr>
                @endif
                <?php
                if ($current['head'] == 'Current Asset') {
                    $cAsset += $child['amount'];
                } elseif ($current['head'] == 'Fixed Assets') {
                    $fAsset += $child['amount'];
                }
                ?>
                @endforeach
                @if ($current['head'] == 'Current Asset')
                <tr>
                    <th colspan="2">Total Current Asset</th>
                    <th align="right">
                        {{ number_format($cAsset + $closingStock, 2, '.', '') }}
                    </th>
                </tr>
                @elseif ($current['head'] == 'Fixed Assets')
                <tr>
                    <th colspan="2">Total Fixed Asset</th>
                    <th align="right">
                        {{ number_format($fAsset, 2, '.', '') }}
                    </th>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="col-md-6" style="padding-right: 0px !important">
        <table id="report-table">
            <thead>
                <tr>
                    <th colspan="2" align="center">LIABILITIES</th>
                    <th align="right">AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="3" style="font-size: 18px;"><u>Current Liabilities:</u></td>
                </tr>

                <?php $totalL = 0; ?>
                @foreach ($liabilities as $liability)
                <?php
                if ($liability['head'] == 'Current Liabilities' || $liability['head'] == 'Share Capital') {
                    continue;
                }
                $totalChildL = 0;
                ?>
                @foreach ($liability['childs'] as $child)
                <?php $totalChildL += $child['amount']; ?>
                @endforeach
                <?php $totalL += $totalChildL; ?>

                <tr>
                    <td>{{ $liability['head'] }}</td>
                    <td align="center">:</td>
                    <td align="right">{{ number_format(abs($totalChildL), 2, '.', '') }}</td>
                </tr>
                @endforeach
                {{-- Total Amount --}}
                <tr>
                    <th colspan="2">Total Liabilities</th>
                    <th align="right">
                        {{ number_format(abs($totalL), 2, '.', '') }}
                    </th>
                </tr>

                <?php $totalC = 0; ?>
                @foreach ($liabilities as $liability)
                <?php
                if ($liability['head'] != 'Share Capital') {
                    continue;
                }
                ?>
                <tr>
                    <td colspan="3" style="font-size: 18px;"><u>{{ $liability['head'] }}:</u></td>
                </tr>
                @foreach ($liability['childs'] as $child)
                <?php $totalC += abs($child['amount']); ?>
                <tr>
                    <td>{{ $child['name'] }}</td>
                    <td align="center">:</td>
                    <td align="right">{{ number_format(abs($child['amount']), 2, '.', '') }}</td>
                </tr>
                @endforeach
                @endforeach
                <tr>
                    <th colspan="2">Total Liabilities</th>
                    <th align="right">
                        {{ number_format(abs($totalC), 2, '.', '') }}
                    </th>
                </tr>


                <tr>
                    <td colspan="3" style="font-size: 18px;"><u>Profit & Loss:</u></td>
                </tr>
                <tr>
                    <td>Opening Balance</td>
                    <td align="center">:</td>
                    <td align="right">
                        @if($profitLoss['opening'] > 0)
                        {{ number_format($profitLoss['opening'], 2, '.', '') }}
                        @else
                        ({{ number_format(abs($profitLoss['opening']), 2, '.', '') }})
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Current Period Profit/Loss</td>
                    <td align="center">:</td>
                    <td align="right">
                        @if($profitLoss['current'] > 0)
                        {{ number_format($profitLoss['current'], 2, '.', '') }}
                        @else
                        ({{ number_format(abs($profitLoss['current']), 2, '.', '') }})
                        @endif
                    </td>
                </tr>
                <tr>
                    <th colspan="2">Total Profit/Loss</th>
                    <th align="right">
                        @if($profitLoss['opening'] > 0)
                        {{ number_format(($profitLoss['opening'] + $profitLoss['current']), 2, '.', '') }}
                        @else
                        ({{ number_format(abs(($profitLoss['opening'] + $profitLoss['current'])), 2, '.', '') }})
                        @endif
                    </th>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div id="pad-bottom"></div>

<div class="row">
    <div class="col-md-6">
        <table id="report-table">
            <thead>
                <tr>
                    <th colspan="2">Total Assets</th>
                    <th align="right">
                        {{ number_format($cAsset + $fAsset + $closingStock, 2, '.', '') }}</th>
                </tr>
            </thead>
        </table>
    </div>
    <div class="col-md-6">
        <table id="report-table">
            <thead>
                <tr>
                    <th colspan="2">Total Liabilities</th>
                    <th align="right">{{ number_format(abs($totalL) + abs($totalC) + $profitLoss['opening'] + $profitLoss['current'], 2, '.', '') }}</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

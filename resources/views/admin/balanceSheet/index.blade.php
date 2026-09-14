@extends('admin.layouts.master')


@section('content')
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <h4 class="card-title">{{ $title }}</h4>
            </div>
            <div class="col-md-6 text-right">
                <a class="btn btn-outline-info btn-lg" href="{{ route($printFormLink) }}" target="_blank">
                    <i class="fa fa-print"></i> Print
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr class="bg-success">
                            <th colspan="2" class="text-center">ASSETS</th>
                            <th class="text-right">AMOUNT</th>
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
                            <td class="text-center">:</td>
                            <td class="text-right">{{ number_format($child['amount'], 2, '.', '') }}</td>
                        </tr>

                        @if ($key == 3 && $current['head'] == 'Current Asset')
                        <tr>
                            <td>Closing Stock</td>
                            <td class="text-center">:</td>
                            <td class="text-right">{{ number_format($closingStock, 2, '.', '') }}</td>
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
                        <tr class="bg-success text-white font-weight-bold">
                            <td colspan="2">Total Current Asset</td>
                            <td class="text-right">
                                {{ number_format($cAsset + $closingStock, 2, '.', '') }}
                            </td>
                        </tr>
                        @elseif ($current['head'] == 'Fixed Assets')
                        <tr class="bg-success text-white font-weight-bold">
                            <td colspan="2">Total Fixed Asset</td>
                            <td class="text-right">
                                {{ number_format($fAsset, 2, '.', '') }}
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr class="bg-success">
                            <th colspan="2" class="text-center">LIABILITIES</th>
                            <th class="text-right">AMOUNT</th>
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
                            <td class="text-center">:</td>
                            <td class="text-right">{{ number_format(abs($totalChildL), 2, '.', '') }}</td>
                        </tr>
                        @endforeach
                        {{-- Total Amount --}}
                        <tr class="bg-success text-white font-weight-bold">
                            <td colspan="2">Total Liabilities</td>
                            <td class="text-right">
                                {{ number_format(abs($totalL), 2, '.', '') }}
                            </td>
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
                            <td class="text-center">:</td>
                            <td class="text-right">{{ number_format(abs($child['amount']), 2, '.', '') }}</td>
                        </tr>
                        @endforeach
                        @endforeach
                        <tr class="bg-success text-white font-weight-bold">
                            <td colspan="2">Total Liabilities</td>
                            <td class="text-right">
                                {{ number_format(abs($totalC), 2, '.', '') }}
                            </td>
                        </tr>


                        <tr>
                            <td colspan="3" style="font-size: 18px;"><u>Profit & Loss:</u></td>
                        </tr>
                        <tr>
                            <td>Opening Balance</td>
                            <td class="text-center">:</td>
                            <td class="text-right">{{ number_format($profitLoss['opening'], 2, '.', '') }}</td>
                        </tr>
                        <tr>
                            <td>Current Period Profit/Loss</td>
                            <td class="text-center">:</td>
                            <td class="text-right">{{ number_format($profitLoss['current'], 2, '.', '') }}</td>
                        </tr>
                        <tr class="bg-success text-white font-weight-bold">
                            <td colspan="2">Total Profit/Loss</td>
                            <td class="text-right">
                                {{ number_format($profitLoss['opening'] + $profitLoss['current'], 2, '.', '') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr class="bg-success">
                                    <th colspan="2">Total Assets</th>
                                    <th class="text-right">
                                        {{ number_format($cAsset + $fAsset + $closingStock, 2, '.', '') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr class="bg-success">
                                    <th colspan="2">Total Liabilities</th>
                                    <th class="text-right">{{ number_format(abs($totalL) + abs($totalC) + $profitLoss['opening'] + $profitLoss['current'], 2, '.', '') }}
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<style>
    table tr td,
    th {
        padding: 0px !important;
    }

    th {
        font-weight: bold !important;
        color: #fff !important;
    }

</style>
@endsection

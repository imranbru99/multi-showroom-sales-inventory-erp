@php

use Illuminate\Support\Carbon;
@endphp
@extends('admin.layouts.masterPrint')

<style>
    .retail td {
        text-align: right;
    }

    .title {
        text-align: center;
        margin-top: 20px;
        text-align: center;
        background-color: #000;
        border-radius: 5px;
        color: #ffffff;
        width: 100%;
        display: flex !important;
    }

    .showroom {
        text-align: center;
        background-color: #000;
        border-radius: 5px;
        padding: 0px !important;
        font-size: 10px;
        color: #ffffff;
    }
    .h2 {
        text-align: center;
        background-color: green;
        border-radius: 5px;
        padding: 5px;
        color: #ffffff;
    }

</style>

@section('content')
    <table id="report-header">
        <tr>
            <td>
                {{ $title }}
                ON {{ date('d-m-Y', strtotime($fromDate)) }} To
                {{ date('d-m-Y', strtotime($toDate)) }}
            </td>
        </tr>
    </table>

    <div id="pad-bottom"></div>
    @if (!empty($print))
        <?php
        
        $netPrevious = 0;
        $netSales = 0;
        $netReturns = 0;
        $netActualSales = 0;
        $netCollections = 0;
        $netOutstanding = 0;
        $netAgree = 0;
        ?>
        @foreach ($retails as $retail)
            <div class="title" style="display: flex">
                <h3 class="showroom">Showroom - {{ $retail['showroom'] }} (Type - Retail)</h3>
            </div>
            <div id="pad-bottom"></div>
            <table id="report-table" class="retail">
                <thead>
                    <tr style="text-alight: right">
                        <th>Project Name</th>
                        <th>Previous Outstanding</th>
                        <th>Sales</th>
                        <th>Returns</th>
                        <th>Actual Sales</th>
                        <th>Collections</th>
                        <th>Current Outstanding</th>
                        <th>Agreement Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totalPrevious = 0;
                    $totalSales = 0;
                    $totalReturns = 0;
                    $totalActualSales = 0;
                    $totalCollections = 0;
                    $totalOutstanding = 0;
                    $totalAgree = 0;
                    ?>
                    @foreach ($retail['info'] as $info)
                        <?php
                        $totalPrevious += $info['previous'];
                        $totalSales += $info['sales'];
                        $totalReturns += $info['returns'];
                        $totalActualSales += $info['actualSales'];
                        $totalCollections += $info['collections'];
                        $totalOutstanding += $info['previous'] + $info['outstanding'];
                        $totalAgree += $info['agreement'];
                        ?>
                        <tr>
                            <td style="text-align: left !important;" width="26%">{{ $info['project'] }}</td>
                            <td width="14%">
                                @if ($info['previous'] > 0)
                                    {{ $info['previous'] }}
                                @else
                                    ({{ number_format(abs($info['previous']), 2, '.', '') }})
                                @endif
                            </td>
                            <td width="12%">{{ $info['sales'] }}</td>
                            <td width="12%">{{ $info['returns'] }}</td>
                            <td width="12%">{{ $info['actualSales'] }}</td>
                            <td width="12%">{{ $info['collections'] }}</td>
                            <td width="14%">
                                @if ($info['previous'] + $info['outstanding'] > 0)
                                    {{ $info['previous'] + $info['outstanding'] }}
                                @else
                                    ({{ number_format(abs($info['previous'] + $info['outstanding']), 2, '.', '') }})
                                @endif
                            </td>
                            <td width="12%">{{ $info['agreement'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="text-right bg-success">
                        <td width="26%"><b>Total</b></td>
                        <td width="14%"><b>{{ number_format($totalPrevious, 2, '.', '') }}</b></td>
                        <td width="12%"><b>{{ number_format($totalSales, 2, '.', '') }}</b></td>
                        <td width="12%"><b>{{ number_format($totalReturns, 2, '.', '') }}</b></td>
                        <td width="12%">
                            <b>
                                @if ($totalActualSales > 0)
                                    {{ number_format($totalActualSales, 2, '.', '') }}
                                @else
                                    ({{ number_format(abs($totalActualSales), 2, '.', '') }})
                                @endif
                            </b>
                        </td>
                        <td class="text-white" width="12%"><b>{{ number_format($totalCollections, 2, '.', '') }}</b>
                        </td>
                        <td class="text-white" width="14%">
                            <b>
                                @if ($totalOutstanding > 0)
                                    {{ number_format($totalOutstanding, 2, '.', '') }}
                                @else
                                    ({{ number_format(abs($totalOutstanding), 2, '.', '') }})
                                @endif
                            </b>
                        </td>
                        <td class="text-white" width="12%">
                            <b>{{ number_format($totalAgree, 2, '.', '') }}</b>
                        </td>
                    </tr>
                </tfoot>
            </table>
            <?php
            $netPrevious += $totalPrevious;
            $netSales += $totalSales;
            $netReturns += $totalReturns;
            $netActualSales += $totalActualSales;
            $netCollections += $totalCollections;
            $netOutstanding += $totalOutstanding;
            $netAgree += $totalAgree;
            ?>
        @endforeach


        <h2 class="h2" style="margin-top: 80px">Dealer</h2>
        <table id="report-table" class="retail">
            <thead>
                <tr>
                    <th>Previous Outstanding</th>
                    <th>Sales</th>
                    <th>Returns</th>
                    <th>Actual Sales</th>
                    <th>Collections</th>
                    <th>Current Outstanding</th>
                </tr>
            </thead>
            <tbody>
                <tr class="text-right">
                    <td>
                        @if ($dealerDetails['previous'] > 0)
                            {{ $dealerDetails['previous'] }}
                        @else
                            ({{ number_format(abs($dealerDetails['previous']), 2, '.', '') }})
                        @endif
                    </td>
                    <td>{{ $dealerDetails['sales'] }}</td>
                    <td>{{ $dealerDetails['returns'] }}</td>
                    <td>{{ $dealerDetails['actualSales'] }}</td>
                    <td>{{ $dealerDetails['collections'] }}</td>
                    <td>
                        @if ($dealerDetails['previous'] + $dealerDetails['outstanding'] > 0)
                            {{ $dealerDetails['previous'] + $dealerDetails['outstanding'] }}
                        @else
                            ({{ number_format(abs($dealerDetails['previous'] + $dealerDetails['outstanding']), 2, '.', '') }})
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        <h2 class="h2" style="margin-top: 80px">Net Total</h2>
        <table id="report-table" class="retail">
            <thead>
                <tr>
                    <th width="18%">Net Total</th>
                    <th width="12%">
                        @if ($netPrevious + $dealerDetails['previous'] > 0)
                            {{ number_format($netPrevious + $dealerDetails['previous'], 2, '.', '') }}
                        @else
                            ({{ number_format(abs($netPrevious + $dealerDetails['previous']), 2, '.', '') }})
                        @endif
                    </th>
                    <th width="12%">{{ number_format($netSales + $dealerDetails['sales'], 2, '.', '') }}</th>
                    <th width="12%">{{ number_format($netReturns + $dealerDetails['returns'], 2, '.', '') }}</th>
                    <th width="12%">
                        @if ($netActualSales + $dealerDetails['actualSales'] > 0)
                            {{ number_format($netActualSales + $dealerDetails['actualSales'], 2, '.', '') }}
                        @else
                            ({{ number_format(abs($netActualSales + $dealerDetails['actualSales']), 2, '.', '') }})
                        @endif
                    </th>
                    <th width="12%">{{ number_format($netCollections + $dealerDetails['collections'], 2, '.', '') }}
                    </th>
                    <th width="12%">
                        @if ($netOutstanding + $dealerDetails['previous'] + $dealerDetails['outstanding'] > 0)
                            {{ number_format($netOutstanding + $dealerDetails['previous'] + $dealerDetails['outstanding'], 2, '.', '') }}
                        @else
                            ({{ number_format(abs($netOutstanding + $dealerDetails['previous'] + $dealerDetails['outstanding']), 2, '.', '') }})
                        @endif
                    </th>
                    <th width="12%">{{ number_format($netAgree, 2, '.', '') }}</th>
                </tr>
            </thead>
        </table>
        </div>
    @endif



    <div class="row">
        <div class="col-md-12 text-right">
            <?php date_default_timezone_set('Asia/Dhaka'); ?>
            <p>Print Date & Time : <?php echo date('d-m-Y h:i:sa'); ?></p>
        </div>
    </div>
@endsection

@extends('admin.layouts.masterPrint')

@section('content')
    <table id="report-header">
        <tr>
            @if ($btnPrintSummary == "Print Summary")
                <tr><td>Lifting Summary On {{ $fromDate }} To {{ $toDate }}</td></tr>
                @if (@$vendorName->name)
                    <tr><td>Suppler Name: {{ @$vendorName->name }}</td></tr>
                @endif                                
            @endif

            @if ($btnPrintRecord == "Print Record")
                <tr><td>Lifting History On {{ $fromDate }} To {{ $toDate }}</td></tr>                
            @endif
        </tr>
        <tr>
            <td>Dealer Commission Statements</td>
        </tr>
    </table>

    <div id="pad-bottom"></div>

    @if ($btnPrintSummary == "Print Summary")
        <table  id="report-table">
            <thead>
                <tr>
                    <th width="20px">Sl</th>
                    <th>Dealer Name</th>
                    <th width="80px">Phone</th>
                    <th>Address</th>
                    <th width="90px">Commission</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $sl = 1;
                    $currentDealerId = 0;
                    $totalCommissionAmount = 0;
                @endphp

                <tbody>
                    @foreach ($dealerCommissionSummaries as $dealerCommissionSummarie)
                        @php
                            $totalCommissionAmount = $totalCommissionAmount + $dealerCommissionSummarie->totalCommissionAmount;
                        @endphp
                        <tr>
                            <td>{{ $sl++ }}</td>
                            <td>{{ $dealerCommissionSummarie->dealerName }}</td>
                            <td>{{ $dealerCommissionSummarie->dealerPhone }}</td>
                            <td>{{ $dealerCommissionSummarie->dealerAddress }}</td>
                            <td align="right">{{ number_format($dealerCommissionSummarie->totalCommissionAmount, 2, '.', '') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </tbody>
        </table>
    @endif

    @if ($btnPrintRecord == "Print Record")
        <table  id="report-table">
            <thead>
                <tr>
                    <th width="20px">Sl</th>
                    <th>Dealer Name</th>
                    <th>Category</th>
                    <th width="110px">Sale Amount</th>
                    <th width="130px">Commission Rate</th>
                    <th width="100px">Commission</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $sl = 1;
                    $currentDealerId = 0;
                    $totalCommissionAmount = 0;
                @endphp

                @foreach ($dealerCommissionStatements as $dealerCommissionStatement)
                    @php
                        $totalCommissionAmount = $totalCommissionAmount + $dealerCommissionStatement->commissionAmount;
                    @endphp
                    
                    @if ($dealerCommissionStatement->dealerId != $currentDealerId)
                        @php
                            $currentDealerId = $dealerCommissionStatement->dealerId;
                            $rowSpan = DB::table('view_dealer_commission_statement')
                                ->whereBetween('view_dealer_commission_statement.date', array($fromDate,$toDate))
                                ->where('dealerId',$dealerCommissionStatement->dealerId)
                                ->count('dealerId');
                        @endphp
                        <tr>
                            <td>{{ $sl++ }}</td>
                            <td rowspan="{{ $rowSpan + 1 }}">
                                <p><b>Name:</b> {{ $dealerCommissionStatement->dealerName }}</p>
                                <p><b>Phone:</b> {{ $dealerCommissionStatement->dealerPhone }}</p>
                                <p><b>Address:</b> {{ $dealerCommissionStatement->dealerAddress }}</p>
                            </td>
                            <td>{{ $dealerCommissionStatement->categoryName }}</td>
                            <td align="right">{{ number_format($dealerCommissionStatement->saleAmount, 2, '.', '') }}</td>
                            <td align="right">{{ $dealerCommissionStatement->commissionRate }}</td>
                            <td align="right">{{ number_format($dealerCommissionStatement->commissionAmount, 2, '.', '') }}</td>
                        </tr>
                    @else
                        <tr>
                            <td>{{ $sl++ }}</td>
                            <td>{{ $dealerCommissionStatement->categoryName }}</td>
                            <td align="right">{{ number_format($dealerCommissionStatement->saleAmount, 2, '.', '') }}</td>
                            <td align="right">{{ $dealerCommissionStatement->commissionRate }}</td>
                            <td align="right">{{ number_format($dealerCommissionStatement->commissionAmount, 2, '.', '') }}</td>
                        </tr>
                        @php
                            $rowSpan--;
                        @endphp
                        @if ($rowSpan == 1)
                            @php
                                $summary = DB::table('view_dealer_commission_statement')
                                    ->whereBetween('view_dealer_commission_statement.date', array($fromDate,$toDate))
                                    ->where('dealerId',$dealerCommissionStatement->dealerId)
                                    ->sum('commissionAmount');
                            @endphp
                            <tr>
                                <td>{{ $sl++ }}</td>
                                <td align="center" colspan="3"><b>Total</b></td>
                                <td align="right">{{ number_format($summary, 2, '.', '') }}</td>
                            </tr>
                        @endif
                    @endif
                @endforeach
            </tbody>
        </table>
    @endif

    <div id="pad-bottom"></div>

    <table  id="report-table">
        <tfoot>
            <tr>
                <th style="text-align: right;"><b>Total Commission Amount : </b></th>
                <td style="text-align: right;">{{ number_format($totalCommissionAmount, 2, '.', '') }}</td>
            </tr>
        </tfoot>
    </table>
@endsection

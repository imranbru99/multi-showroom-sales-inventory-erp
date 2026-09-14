@extends('admin.layouts.masterPrint')

@section('content')
    <table id="report-header">
        <tr>
            <td>Dealer Wise Categories/Products Contribution ON {{ $fromDate }} To {{ $toDate }}</td>
        </tr>
    </table>

    <div id="pad-bottom"></div>

    <table  id="report-table">
        <thead>
            <tr>
                <th width="20px">Sl</th>
                <th>Dealers Name</th>
                <th class="byCategoryOrProduct">{{ $searchType == '' ? 'Categories' : $searchType }} Name</th>
                <th width="90px">By Qty</th>
                <th width="85px">Qty (%)</th>
                <th width="105px">By Value</th>
                <th width="85px">Value (%)</th>
            </tr>
        </thead>

        <tbody>
            @php
                $sl = 1;
                $currentDealerId = 0;
            @endphp

            @foreach ($contributions as $contribution)
                @if ($contribution->dealerId != $currentDealerId)
                    @php
                        $currentDealerId = $contribution->dealerId;
                        $rowSpan = DB::table('view_dealer_wise_product_contribution')
                            ->whereBetween('date', array($fromDate,$toDate))
                            ->where('dealerId',$contribution->dealerId)
                            ->groupBY($searchType == 'Categories' ? 'categoryId' : 'productId')
                            ->get();
                    @endphp
                    <tr>
                        <td>{{ $sl++ }}</td>
                        <td rowspan="{{ count($rowSpan) }}">{{ $contribution->dealerName }}</td>
                        <td>{{ $searchType == 'Categories' ? $contribution->categoryName : $contribution->productName }}</td>
                        <td align="right">{{ $contribution->totalIssueQty }}</td>
                        <td align="right">{{ number_format($contribution->percentageQty, 2, '.', '') }}</td>
                        <td align="right">{{ $contribution->totalIssueAmount }}</td>
                        <td align="right">{{ number_format($contribution->percentageAmount, 2, '.', '') }}</td>
                    </tr>
                @else
                    <tr>
                        <td>{{ $sl++ }}</td>
                        <td>{{ $searchType == 'Categories' ? $contribution->categoryName : $contribution->productName }}</td>
                        <td align="right">{{ $contribution->totalIssueQty }}</td>
                        <td align="right">{{ number_format($contribution->percentageQty, 2, '.', '') }}</td>
                        <td align="right">{{ $contribution->totalIssueAmount }}</td>
                        <td align="right">{{ number_format($contribution->percentageAmount, 2, '.', '') }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
@endsection

@extends('admin.layouts.masterPrint')

@section('content')
    <table id="report-header">
        <tr>
            <td>Product Statement Report On {{ $fromDate }} To {{ $toDate }}</td>
        </tr>
    </table>

    <div id="pad-bottom"></div>

    <table align="center">
        <tr>
            <td colspan="2"><h3>Product Name :</h3></td>
            <td colspan="4"><h3>{{ $productName->name }}</h3></td>            
        </tr>
    </table>

    <div id="pad-bottom"></div>

    <table  id="report-table">
        <thead>
            <tr>
                <td colspan="8" align="right"><h3>Opening Balance</h3></td>
                <td align="right"><b>{{ $openingBalance->opening }}</b></td>
            </tr>
            <tr>
                <th width="20px">Sl</th>
                <th width="60px">date</th>
                <th width="70px" align="right">Lifting</th>
                <th width="100px" align="right">Lifting Return</th>
                <th width="100px" align="right">Product Issue</th>
                <th width="110px" align="right">Product Return</th>
                <th width="70px" align="right">Sales</th>
                <th width="95px" align="right">Sales Return</th>
                <th width="70px" align="right">Balance</th>
            </tr>
        </thead>

        <tbody>
            @php
                $sl = 1;
                $totalLifting = 0;                                       
                $totalLiftingReturn = 0;
                $totalProductIssue = 0;                                       
                $totalProductReturn = 0;                                       
                $totalSales = 0;
                $totalSalesReturn = 0;
                $balance = 0;
                $totalBalance = 0;
            @endphp
            @foreach ($productStatements as $productStatement)
                @php
                    $totalLifting = $totalLifting + $productStatement->liftingPrice;                                       
                    $totalLiftingReturn = $totalLiftingReturn + $productStatement->liftingReturnPrice;
                    $totalProductIssue = $totalProductIssue + $productStatement->productIssuePrice;
                    $totalProductReturn = $totalProductReturn + $productStatement->productReturnPrice;
                    $totalSales = $totalSales + $productStatement->salesPrice;
                    $totalSalesReturn = $totalSalesReturn + $productStatement->slaesReturnPrice;
                    if ($sl == 1)
                    {
                        $balance = $openingBalance->opening + $productStatement->liftingPrice + $productStatement->productReturnPrice + $productStatement->slaesReturnPrice - $productStatement->liftingReturnPrice - $productStatement->productIssuePrice - $productStatement->salesPrice;
                    }
                    else
                    {
                        $balance = $balance + $productStatement->liftingPrice + $productStatement->productReturnPrice + $productStatement->slaesReturnPrice - $productStatement->liftingReturnPrice - $productStatement->productIssuePrice - $productStatement->salesPrice; 
                    }
                    $totalBalance = $totalBalance + $balance; 
                @endphp
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ date('d-m-Y',strtotime($productStatement->date)) }}</td>
                    <td align="right">{{ $productStatement->liftingPrice }}</td>
                    <td align="right">{{ $productStatement->liftingReturnPrice }}</td>
                    <td align="right">{{ $productStatement->productIssuePrice }}</td>
                    <td align="right">{{ $productStatement->productReturnPrice }}</td>
                    <td align="right">{{ $productStatement->salesPrice }}</td>
                    <td align="right">{{ $productStatement->slaesReturnPrice }}</td>
                    <td align="right">{{ $balance }}</td>
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <th colspan="2">Total</th>
                <td align="right"><b>{{ $totalLifting }}</b></td>
                <td align="right"><b>{{ $totalLiftingReturn }}</b></td>
                <td align="right"><b>{{ $totalProductIssue }}</b></td>
                <td align="right"><b>{{ $totalProductReturn }}</b></td>
                <td align="right"><b>{{ $totalSales }}</b></td>
                <td align="right"><b>{{ $totalSalesReturn }}</b></td>
                <td align="right"><b>{{ $totalBalance }}</b></td>
            </tr>
        </tfoot>
    </table>
@endsection

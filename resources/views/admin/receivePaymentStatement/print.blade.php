@extends('admin.layouts.masterPrint')

@section('content')
    <table id="report-header">
        <tr>
            <td>Receive And Payment Statement On {{ date('d-m-Y',strtotime($fromDate)) }} To {{ date('d-m-Y',strtotime($toDate)) }}</td>
        </tr>
    </table>

    <div id="pad-bottom"></div>
    
    <table id="report-table">
        <thead class="thead-green">
            <tr>
                <td colspan="4" style="font-size: 20px; font-weight: bold; text-align: center;">Assets</td>
            </tr>

            <tr>
                <th width="20px">Sl</th>
                <th width="100px">Head Code</th>
                <th>Head Name</th>
                <th width="80px">Balance</th>
            </tr>
        </thead>

        <tbody>
            @php
                $sl = 1;
                $totalAssets = 0;
            @endphp
            @foreach ($assetsLists as $assetsList)
                @php
                    $totalAssets = $totalAssets + $assetsList->amount;
                @endphp
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ $assetsList->headCode }}</td>
                    <td>{{ $assetsList->headName }}</td>
                    <td align="right">{{ $assetsList->amount > 0 ? $assetsList->amount : '('.abs($assetsList->amount).')' }}</td>
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td colspan="3" align="right"><b>Total</b></td>
                <td align="right" style="font-weight: bold;">{{ $totalAssets >= 0 ? $totalAssets : '('.abs($totalAssets).')' }}</td>
            </tr>
        </tfoot>
    </table>

    <div id="pad-bottom"></div>

    <table id="report-table">
        <thead class="thead-green">
            <tr>
                <td colspan="4" style="font-size: 20px; font-weight: bold; text-align: center;">Libilities</td>
            </tr>

            <tr>
                <th width="20px">Sl</th>
                <th width="100px">Head Code</th>
                <th>Head Name</th>
                <th width="80px">Balance</th>
            </tr>
        </thead>

        <tbody>
            @php
                $sl = 1;
                $totalLibilities = 0;
            @endphp
            @foreach ($libilityLists as $libilityList)
                @php
                    $totalLibilities = $totalLibilities + $libilityList->amount;
                @endphp
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ $libilityList->headCode }}</td>
                    <td>{{ $libilityList->headName }}</td>
                    <td align="right">{{ $libilityList->amount > 0 ? $libilityList->amount : '('.abs($libilityList->amount).')' }}</td>
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td colspan="3" align="right"><b>Total</b></td>
                <td align="right" style="font-weight: bold;">{{ $totalLibilities >= 0 ? $totalLibilities : '('.abs($totalLibilities).')' }}</td>
            </tr>
        </tfoot>
    </table>

    <div id="pad-bottom"></div>

    <table id="report-table">
        <tr>
            @if ($totalAssets > $totalLibilities)
                <td style="background: green; font-size: 20px; font-weight: bold; text-align: center;">Net Profit: {{ $totalAssets - $totalLibilities }}</td>
            @endif

            @if ($totalAssets < $totalLibilities)
                <td style="background: red; font-size: 20px; font-weight: bold; color: white; text-align: center;">Net Lose: {{ $totalLibilities - $totalAssets }}</td>
            @endif
        </tr>
    </table>
@endsection

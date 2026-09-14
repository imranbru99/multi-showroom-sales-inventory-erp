@extends('admin.layouts.masterPrint')

@section('content')
    <table id="report-header">
        <tr>
            <td>Income Statement On {{ date('d-m-Y', strtotime($fromDate)) }} To {{ date('d-m-Y', strtotime($toDate)) }}
            </td>
        </tr>
    </table>

    <div id="pad-bottom"></div>

    <table id="report-table">
        <thead class="thead-green">
            <tr>
                <td colspan="4" style="font-size: 20px; font-weight: bold; text-align: center;">Income</td>
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
                $totalIncome = 0;
            @endphp
            @foreach ($incomeLists as $incomeList)
                @php
                    $totalIncome = $totalIncome + abs($incomeList->amount);
                @endphp
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ $incomeList->headCode }}</td>
                    <td>{{ $incomeList->headName }}</td>
                    <td align="right">{{ abs($incomeList->amount) }}</td>
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td colspan="3" align="right"><b>Total</b></td>
                <td align="right" style="font-weight: bold;">{{ $totalIncome }}</td>
            </tr>
        </tfoot>
    </table>

    <div id="pad-bottom"></div>

    <table id="report-table">
        <thead class="thead-green">
            <tr>
                <td colspan="3" style="font-size: 20px; font-weight: bold; text-align: center;">Closing Stock</td>
            </tr>

            <tr>
                <th width="20px">Sl</th>
                <th>Head Name</th>
                <th width="80px">Balance</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td>1</td>
                <td>Closing Stock</td>
                <td align="right">{{ $closingStockValue }}</td>
            </tr>
        </tbody>

        <tfoot>
            <tr>
                <td colspan="2" align="right"><b>Total</b></td>
                <td align="right" style="font-weight: bold;">{{ $closingStockValue }}</td>
            </tr>
        </tfoot>
    </table>

    <div id="pad-bottom"></div>

    <table id="report-table">
        <thead class="thead-green">
            <tr>
                <td colspan="4" style="font-size: 20px; font-weight: bold; text-align: center;">Expense</td>
            </tr>

            <tr>
                <th width="20px">Sl</th>
                <th width="100px">Head Code</th>
                <th>Head Name</th>
                <th width="80px">Balance</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td>1</td>
                <td></td>
                <td>Opening Balance</td>
                <td align="right">{{ abs($openingBalance) }}</td>
            </tr>
            @php
                $sl = 2;
                $totalExpanse = 0;
            @endphp
            @foreach ($data as $d)
                @php
                    $totalExpanse = $totalExpanse + $d['amount'];
                @endphp
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ $d['headCode'] }}</td>
                    <td>{{ $d['headName'] }}</td>
                    <td align="right">{{ $d['amount'] > 0 ? $d['amount'] : abs($d['amount']) }}</td>
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td colspan="3" align="right"><b>Total</b></td>
                <td align="right" style="font-weight: bold;">
                    {{ $totalExpanse >= 0 ? ($totalExpanse + $openingBalance) : abs($totalExpanse) }}</td>
            </tr>
        </tfoot>
    </table>

    <div id="pad-bottom"></div>

    <table id="report-table">
        <tr>
            @php
                $netIncome = $totalIncome + $closingStockValue;
            @endphp
            @if ($netIncome > $totalExpanse)
                <td style="background: green; font-size: 20px; font-weight: bold; color: white; text-align: center;">Net
                    Profit: {{ $netIncome - ($totalExpanse + $openingBalance) }}</td>
            @endif

            @if ($netIncome < $totalExpanse)
                <td style="background: red; font-size: 20px; font-weight: bold; color: white; text-align: center;">Net Lose:
                    {{ ($totalExpanse +  $openingBalance) - $netIncome }}</td>
            @endif
        </tr>
    </table>

    <div class="row">
        <div class="col-md-12 text-right">
            <?php date_default_timezone_set('Asia/Dhaka'); ?>
            <p>Print Date & Time : <?php echo date('d-m-Y h:i:sa'); ?></p>
        </div>
    </div>
@endsection

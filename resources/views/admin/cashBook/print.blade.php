@extends('admin.layouts.masterPrint')

@section('content')
    <table id="report-header">
        <tr>
            <td>Cash Book On {{ date('d-m-Y',strtotime($fromDate)) }} To {{ date('d-m-Y',strtotime($toDate)) }}</td>
        </tr>
    </table>

    <div id="pad-bottom"></div>

    <table id="report-table">
        <thead>
            <tr>
                <td colspan="6" style="text-align: right; font-weight: bold;"><h2>Previous Balance</h2></td>
                <td style="text-align: right; font-weight: bold;"><b>{{ $previousBalance->previousBalance == "" ? 0 : $previousBalance->previousBalance }}</b></td>
            </tr>
            <tr>
                <th width="20px">Sl</th>
                <th width="80px">Date</th>
                <th width="130px">Voucher No</th>
                <th>Prticular</th>
                <th width="60px">Debit</th>
                <th width="60px">Credit</th>
                <th width="60px">Balance</th>
            </tr>
        </thead>

        <tbody>
            @php
                $sl = 1;
                $balance = 0;
                $totalDebit = 0;
                $totalCredit = 0;
            @endphp
            @foreach ($cashBookReports as $cashBookReports)
                @php
                    $totalDebit = $totalDebit + $cashBookReports->debit_amount;
                    $totalCredit = $totalCredit + $cashBookReports->credit_amount;
                    if ($sl == 1)
                    {
                        $balance = $previousBalance->previousBalance + $balance + $cashBookReports->debit_amount - $cashBookReports->credit_amount;
                    }
                    else
                    {
                        $balance = $balance + $cashBookReports->debit_amount - $cashBookReports->credit_amount;
                    }
                @endphp
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ date('d-m-Y',strtotime($cashBookReports->voucher_date)) }}</td>
                    <td>{{ $cashBookReports->voucher_no }}</td>
                    <td>{{ $cashBookReports->narration }}</td>
                    <td align="right">{{ $cashBookReports->debit_amount }}</td>
                    <td align="right">{{ $cashBookReports->credit_amount }}</td>
                    <td align="right">{{ $balance > 0 ? $balance : '('.abs($balance).')' }}</td>
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td colspan="4" align="right"><b>Total</b></td>
                <td align="right">{{ $totalDebit }}</td>
                <td align="right">{{ $totalCredit }}</td>
                <td align="right">{{ $balance > 0 ? $balance : '('.abs($balance).')' }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="row">
        <div class="col-md-12 text-right">
            <?php date_default_timezone_set('Asia/Dhaka'); ?>
            <p>Print Date & Time : <?php echo date('d-m-Y h:i:sa'); ?></p>
        </div>
    </div>

@endsection

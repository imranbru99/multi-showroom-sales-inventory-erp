@extends('admin.layouts.masterPrint')

@section('content')
    <table id="report-header">
        <tr>
            <td>General Ledger On {{ date('d-m-Y',strtotime($fromDate)) }} To {{ date('d-m-Y',strtotime($toDate)) }}</td>
        </tr>

        @if (@$generalLedgerHeadName->head_name)
            <tr>
                <td>Accounts Head : {{ @$generalLedgerHeadName->head_name }}</td>
            </tr>
        @endif
    </table>

    <div id="pad-bottom"></div>

    <table id="report-table">
        <thead>
            <tr>
                <td colspan="6" align="right"><h2>Previous Balance</h2></td>
                <td align="right"><b>{{ $previousBalance->previousBalance == "" ? 0 : $previousBalance->previousBalance }}</b></td>
            </tr>
            <tr>
                <th width="20px">Sl</th>
                <th width="130px">Voucher No</th>
                <th width="80px">Date</th>
                <th>Prticular</th>
                <th width="80px">Debit</th>
                <th width="80px">Credit</th>
                <th width="80px">Balance</th>
            </tr>
        </thead>

        <tbody>
            @php
                $sl = 1;
                $balance = 0;
                $totalDebit = 0;
                $totalCredit = 0;
            @endphp
            @foreach ($generalLedgerReports as $generalLedgerReport)
                @php
                    $totalDebit = $totalDebit + $generalLedgerReport->debit_amount;
                    $totalCredit = $totalCredit + $generalLedgerReport->credit_amount;
                    
                    if($generalLedgerReport->head_level == 2 || $generalLedgerReport->head_level == 4 ){
                        if ($sl == 1)
                        {
                            $balance = $previousBalance->previousBalance + $balance + $generalLedgerReport->credit_amount - $generalLedgerReport->debit_amount;
                        }
                        else
                        {
                            $balance = $balance + $generalLedgerReport->credit_amount - $generalLedgerReport->debit_amount;
                        }
                    }else{
                        if ($sl == 1)
                        {
                            $balance = $previousBalance->previousBalance + $balance + $generalLedgerReport->debit_amount - $generalLedgerReport->credit_amount;
                        }
                        else
                        {
                            $balance = $balance + $generalLedgerReport->debit_amount - $generalLedgerReport->credit_amount;
                        }
                    }
                @endphp
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ $generalLedgerReport->voucher_no }}</td>
                    <td>{{ date('d-m-Y',strtotime($generalLedgerReport->voucher_date)) }}</td>
                    <td>{{ $generalLedgerReport->narration }}</td>
                    <td align="right">{{ $generalLedgerReport->debit_amount }}</td>
                    <td align="right">{{ $generalLedgerReport->credit_amount }}</td>
                    <td align="right">{{ $balance > 0 ? $balance : '('.abs($balance).')' }}</td>
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td colspan="4" align="right"><b>Total</b></td>
                <td align="right"><b>{{ $totalDebit }}</b></td>
                <td align="right"><b>{{ $totalCredit }}</b></td>
                <td align="right"><b>{{ $balance > 0 ? $balance : '('.abs($balance).')' }}</b></td>
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

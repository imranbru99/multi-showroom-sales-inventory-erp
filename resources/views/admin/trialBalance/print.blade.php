@extends('admin.layouts.masterPrint')

@section('content')
    <table id="report-header">
        <tr>
            <td>Trial Balance From {{ date('d-m-Y',strtotime($fromDate)) }} to {{ date('d-m-Y',strtotime($toDate)) }}</td>
        </tr>
    </table>

    <div id="pad-bottom"></div>

    <table id="report-table">
        <thead>
            <tr>
                <th width="20px">Sl</th>
                <th>General Ledger Head</th>
                <th width="80px">Debit</th>
                <th width="80px">Credit</th>
            </tr>
        </thead>

        <tbody>
            @php
                $sl = 1;
                $totalDebit = 0;
                $totalCredit = 0;
            @endphp
            @foreach ($coaLists as $coaList)
                @php
                    $amount = DB::table('tbl_account_transactions')
                        ->select(DB::raw('(SUM(debit_amount) - SUM(credit_amount)) as amount'))
                        ->where('coa_head_code','LIKE',$coaList->head_code.'%')
                        ->where('voucher_date', '>=', $fromDate)
                        ->where('voucher_date', '<=', $toDate)
                        ->where('approve',1)
                        ->first();

                    if ($amount->amount > 0)
                    {
                        $debitAmount = $amount->amount;
                    }
                    else
                    {
                        $debitAmount = 0;
                    } 

                    if ($amount->amount < 0)
                    {
                        $creditAmount = abs($amount->amount);
                    }
                    else
                    {
                        $creditAmount= 0;
                    }                            

                    $totalDebit = $totalDebit + $debitAmount;
                    $totalCredit = $totalCredit + $creditAmount;
                @endphp

                @if ($debitAmount != $creditAmount)
                    <tr>
                        <td>{{ $sl++ }}</td>
                        <td>{{ $coaList->head_name }}</td>
                        <td align="right">{{ round($debitAmount, 2) }}</td>
                        <td align="right">{{ round($creditAmount, 2) }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td colspan="2" align="right"><b>Total</b></td>
                <td align="right">{{ $totalDebit }}</td>
                <td align="right">{{ $totalCredit }}</td>
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

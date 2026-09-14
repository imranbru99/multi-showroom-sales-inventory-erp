@extends('admin.layouts.masterPrint')

@section('content')
    <table id="report-header">
        <tr>
            @if ($fromDate == "" && $toDate == "")
                <td>Opening Balance List From</td>
            @else
                <td>Opening Balance List From {{ date('d-m-Y',strtotime($fromDate)) }} Month  Of {{ date('d-m-Y',strtotime($toDate)) }}</td>
            @endif
        </tr>
    </table>

    <div id="pad-bottom"></div>

    <table  id="report-table">
        <thead>
            <tr>
                <th width="20px">SL</th>
                <th width="100px">Vouchar No</th>
                <th width="70px">Date</th>
                <th width="150px">Debit Head</th>
                <th width="150px">Credit Head</th>
                <th width="105px">Account Status</th>
                <th width="60px">Debit</th>
                <th width="60px">Credit</th>
            </tr>
        </thead>

        <tbody>            
            @php
                $sl = 1;
                $totalDebit = 0;
                $totalCredit = 0;
            @endphp
            @foreach ($transactionLists as $transactionList)
                @php
                    $accountHeadName = DB::table('view_account')
                        ->select('view_account.*')
                        ->where('voucherNo',$transactionList->voucher_no)
                        ->first();

                    $totalDebit = $totalDebit + $transactionList->totalDebitAmount;
                    $totalCredit = $totalCredit + $transactionList->totalCreditAmount;
                @endphp
                <tr class="row_{{ $transactionList->id }}">
                    <td>{{ $sl++ }}</td>
                    <td>{{ $transactionList->voucher_no }}</td>
                    <td>{{ date('d-m-Y',strtotime($transactionList->voucher_date)) }}</td>
                    <td>{{ $accountHeadName->debitHeadname }}</td>
                    <td>{{ $accountHeadName->creditHeadName }}</td>
                    <td align="center">
                        @php
                            if ($transactionList->approve == 1)
                            {
                                echo "Approve";
                            }
                            else
                            {
                                echo "Pending";
                            }
                        @endphp
                    </td>
                    <td align="right">{{ $transactionList->totalDebitAmount }}</td>
                    <td align="right">{{ $transactionList->totalCreditAmount }}</td>
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td style="text-align: center;" colspan="6"><b>Total</b></td>
                <td style="text-align: right;"><b>{{ $totalDebit }}</b></td>
                <td style="text-align: right;"><b>{{ $totalCredit }}</b></td>
            </tr>
        </tfoot>
    </table>

    <div id="pad-bottom"></div>

    <table  id="report-table">
    </table>
@endsection

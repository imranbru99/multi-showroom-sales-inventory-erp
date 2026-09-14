@extends('admin.layouts.masterReport')

@section('search_card_body')
    <div class="row">
        <div class="col-md-12 form-group">
            <input class="form-control" type="hidden" name="print" value="print">
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <label for="credit-account-head">Transaction Ledger Head</label>
            <div class="form-group">
                <select class="chosen-select" id="transactionLedgerHead" name="transactionLedgerHead">
                    <option value="">Select Account Name</option>
                    @foreach ($transactionLedgerLists as $transactionLedgerList)
                        @php
                            $select = "";
                            if ($transactionLedgerHead)
                            {
                                if ($transactionLedgerList->head_code == $transactionLedgerHead)
                                {
                                    $select = "selected";
                                }
                                else
                                {
                                    $select = "";
                                }
                            }
                        @endphp
                        <option value="{{ $transactionLedgerList->head_code }}" {{ $select }}>{{ $transactionLedgerList->head_name }}</option>
                    @endforeach
                </select>
            </div>  
        </div>

        <div class="col-md-4 form-group">
            <label for="from-date">From Date</label>
            <input  type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'from_date' }}" name="fromDate" value="{{ date('d-m-Y',strtotime($fromDate)) }}" placeholder="Select Date From">
        </div>
        <div class="col-md-4 form-group">
            <label for="to-date">To Date</label>
            <input  type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'to_date' }}" name="toDate" value="{{ date('d-m-Y',strtotime($toDate)) }}" placeholder="Select Date To">
        </div>
    </div>
@endsection

@section('print_card_header')
    <input type="hidden" name="fromDate" value="{{ $fromDate }}">
    <input type="hidden" name="toDate" value="{{ $toDate }}">
    <input type="hidden" name="transactionLedgerHead" value="{{ $transactionLedgerHead }}">
    
    <input type="hidden" id="print_value" name="print" value="{{ $print }}">
@endsection

@section('print_card_body')
    <table id="dataTable" name="productList" class="table table-bordered table-sm">
        <thead>
            <tr>
                <th colspan="6" style="text-align: right; font-weight: bold;">Previous Balance</th>
                <th style="text-align: right; font-weight: bold;">{{ $previousBalance->previousBalance == "" ? 0 : $previousBalance->previousBalance }}</th>
            </tr>
            <tr>
                <th width="20px">Sl</th>
                <th width="80px">Date</th>
                <th width="130px">Voucher No</th>
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
            @foreach ($transactionLedgerReports as $transactionLedgerReports)
                @php
                    $totalDebit = $totalDebit + $transactionLedgerReports->debit_amount;
                    $totalCredit = $totalCredit + $transactionLedgerReports->credit_amount;
                    if ($sl == 1)
                    {
                        $balance = $previousBalance->previousBalance + $balance + $transactionLedgerReports->debit_amount - $transactionLedgerReports->credit_amount;
                    }
                    else
                    {
                        $balance = $balance + $transactionLedgerReports->debit_amount - $transactionLedgerReports->credit_amount;
                    }
                @endphp
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ date('d-m-Y',strtotime($transactionLedgerReports->voucher_date)) }}</td>
                    <td>{{ $transactionLedgerReports->voucher_no }}</td>
                    <td>{{ $transactionLedgerReports->narration }}</td>
                    <td align="right">{{ $transactionLedgerReports->debit_amount }}</td>
                    <td align="right">{{ $transactionLedgerReports->credit_amount }}</td>
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
@endsection
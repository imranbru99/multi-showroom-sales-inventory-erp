@extends('admin.layouts.master')

@section('content')
    <div style="padding-bottom: 10px;"></div>

    <form class="form-horizontal" id="search" action="{{ route($searchFormLink) }}" method="POST"
        enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="card">
            <div class="card-header">
                <input type="hidden" name="print" value="print">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="card-title">{{ $title }}</h4>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-5 form-group">
                        <label for="from-date">From Date</label>
                        <input type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'from_date' }}"
                            name="fromDate" placeholder="Select Date From"
                            value="{{ date('d-m-Y', strtotime($fromDate)) }}" readonly>
                    </div>

                    <div class="col-md-5 form-group">
                        <label for="to-date">To Date</label>
                        <input type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'to_date' }}"
                            name="toDate" placeholder="Select Date To" value="{{ date('d-m-Y', strtotime($toDate)) }}"
                            readonly>
                    </div>

                    <div class="col-md-2">
                        <label for=""></label>
                        <div class="form-group">
                            <button type="submit" id="search" name="searchButton"
                                class="btn btn-outline-info btn-md waves-effect" style="width: 100%;"><i
                                    class="fa fa-search"></i> Search</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title">Searched Report</h4>
                </div>
                <div class="col-md-6 text-right">
                    <form class="form-horizontal" id="print" action="{{ route($printFormLink) }}" target="_blank"
                        method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="toDate" value="{{ $toDate }}">
                        <input type="hidden" name="fromDate" value="{{ $fromDate }}">

                        <input type="hidden" id="print_value" name="print" value="{{ $print }}">
                        <button type="submit" class="btn btn-outline-info btn-lg waves-effect"><i class="fa fa-print"></i>
                            Print</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body">
            <table id="dataTable" name="productList" class="table table-bordered table-sm">
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
                            if ($coaList->head_level == 2 || $coaList->head_level == 4) {
                                $amount = DB::table('tbl_account_transactions')
                                    ->select(DB::raw('(SUM(credit_amount) - SUM(debit_amount)) as amount'))
                                    ->where('coa_head_code', 'LIKE', $coaList->head_code . '%')
                                    ->where('voucher_date', '>=', $fromDate)
                                    ->where('voucher_date', '<=', $toDate)
                                    ->where('approve', 1)
                                    ->first();
                            } else {
                                $amount = DB::table('tbl_account_transactions')
                                    ->select(DB::raw('(SUM(debit_amount) - SUM(credit_amount)) as amount'))
                                    ->where('coa_head_code', 'LIKE', $coaList->head_code . '%')
                                    ->where('voucher_date', '>=', $fromDate)
                                    ->where('voucher_date', '<=', $toDate)
                                    ->where('approve', 1)
                                    ->first();
                            }
                            
                            if ($coaList->head_level == 2 || $coaList->head_level == 4) {
                                if ($amount->amount < 0) {
                                    $creditAmount = $amount->amount;
                                } else {
                                    $creditAmount = 0;
                                }
                            
                                if ($amount->amount > 0) {
                                    $debitAmount = abs($amount->amount);
                                } else {
                                    $debitAmount = 0;
                                }
                            } else {
                                if ($amount->amount > 0) {
                                    $debitAmount = $amount->amount;
                                } else {
                                    $debitAmount = 0;
                                }
                            
                                if ($amount->amount < 0) {
                                    $creditAmount = abs($amount->amount);
                                } else {
                                    $creditAmount = 0;
                                }
                            }
                            
                            $totalDebit = $totalDebit + $debitAmount;
                            $totalCredit = $totalCredit + $creditAmount;
                        @endphp

                        @if ($debitAmount != $creditAmount)
                            <tr>
                                <td>{{ $sl++ }}</td>
                                <td>{{ $coaList->head_name }}</td>
                                <td align="right">{{ $debitAmount }}</td>
                                <td align="right">
                                    {{ $creditAmount > 0 ? round($creditAmount, 2) : '(' . abs($creditAmount) . ')' }}
                                </td>
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
        </div>
    </div>
@endsection

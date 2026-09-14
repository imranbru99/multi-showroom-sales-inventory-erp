@extends('admin.layouts.masterPrint')

@section('content')
    <table id="report-header">
        <tr>
            @if ($fromDate == "" && $toDate == "")
                <td>Voucher List</td>
            @else
                <td>Voucher List From {{ date('d-m-Y',strtotime($fromDate)) }} Month  Of {{ date('d-m-Y',strtotime($toDate)) }}</td>
            @endif
        </tr>
    </table>

    <div id="pad-bottom"></div>

    <table id="report-table" class="table table-bordered table-sm">
        <thead>
            <tr>
                <th width="20px">Sl</th>
                <th width="80px">Date</th>
                <th>Narration</th>
                <th width="100px">Voucher No</th>
                <th width="120px">Debit Head</th>
                <th width="120px">Credit Head</th>
                <th width="60px">Amount</th>
            </tr>
        </thead>

        <tbody>
            @php
                $sl = 1;
                $totalAmount = 0;
            @endphp
            @foreach ($voucherLists as $voucherList)
                @php
                    if ($voucherList->voucherType == "CV" || $voucherList->voucherType == "JV")
                    {
                        $fld_debit = "tbl_account_transactions.credit_amount";
                        $fld_credit = "tbl_account_transactions.debit_amount";
                    }

                    if ($voucherList->voucherType == "DV")
                    {
                        $fld_debit = "tbl_account_transactions.debit_amount";
                        $fld_credit = "tbl_account_transactions.credit_amount";
                    }

                    $debitHeadName = DB::table('tbl_account_transactions')
                        ->select('tbl_coa.head_name as headName')
                        ->leftJoin('tbl_coa','tbl_coa.head_code','=','tbl_account_transactions.coa_head_code')
                        ->where('tbl_account_transactions.voucher_no',$voucherList->voucherNo)
                        ->where($fld_debit,0)
                        ->first();

                    $creditHeadName = DB::table('tbl_account_transactions')
                        ->select('tbl_coa.head_name as headName')
                        ->leftJoin('tbl_coa','tbl_coa.head_code','=','tbl_account_transactions.coa_head_code')
                        ->where('tbl_account_transactions.voucher_no',$voucherList->voucherNo)
                        ->where($fld_credit,0)
                        ->first();

                    $totalAmount = $totalAmount + $voucherList->amount;
                @endphp
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ date('d-m-Y',strtotime($voucherList->date)) }}</td>
                    <td>{{ $voucherList->narration }}</td>
                    <td>{{ $voucherList->voucherNo }}</td>
                    <td>{{ $debitHeadName->headName }}</td>
                    <td>{{ $creditHeadName->headName }}</td>
                    <td align="right">{{ $voucherList->amount }}</td>
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td colspan="6" align="right"><b>Total</b></td>
                <td align="right"><b>{{ $totalAmount }}</b></td>
            </tr>
            <tr>
                <td style="text-align: left; padding: 10px" colspan="7">                    
                    @php
                        $inWords = \App\HelperClass::numberToWords($totalAmount);
                    @endphp
                    In Words : {{ $inWords }} Taka Only.
                    <b></b>
                </td>
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

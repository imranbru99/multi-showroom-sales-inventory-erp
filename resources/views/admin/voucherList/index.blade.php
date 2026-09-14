@extends('admin.layouts.master')

@section('content')
    <div style="padding-bottom: 10px;"></div>

    <form class="form-horizontal" id="searchForm" action="{{ route($searchFormLink) }}" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}

        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6"><h4 class="card-title">{{ $title }}</h4></div>
                    <div class="col-md-6 text-right">
                        <button type="submit" class="btn btn-outline-info btn-lg waves-effect"><i class="fa fa-search"></i> Search</button>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 form-group">
                        <input class="form-control" type="hidden" name="print" value="print">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="from-date">From Date</label>
                        <input  type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'from_date' }}" name="fromDate" value="{{ date('d-m-Y',strtotime($fromDate)) }}" placeholder="Select Date From">
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="to-date">To Date</label>
                        <input  type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'to_date' }}" name="toDate" value="{{ date('d-m-Y',strtotime($toDate)) }}" placeholder="Select Date To">
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="submit" id="search" class="btn btn-outline-info btn-lg waves-effect search"><i class="fa fa-search"></i> Search</button>
                    </div>
                </div>              
            </div>
        </div>
    </form>

    <div class="card" style="margin-bottom: 0px;">              
        <div class="card-header">
            <div class="row">
                <div class="col-md-6"><h4 class="card-title">Searched Report</h4></div>
                <div class="col-md-6 text-right">
                    <form class="form-horizontal" id="print" action="{{ route($printFormLink) }}" target="_blank" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}

                        <input type="hidden" name="fromDate" value="{{ $fromDate }}">
                        <input type="hidden" name="toDate" value="{{ $toDate }}">
                        
                        <input type="hidden" id="print_value" name="print" value="{{ $print }}">

                        <button type="submit" class="btn btn-outline-info btn-lg waves-effect"><i class="fa fa-print"></i> Print</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body">
            <table id="dataTable" name="productList" class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th width="20px">Sl</th>
                        <th width="80px">Date</th>
                        <th width="150px">Voucher No</th>
                        <th width="180px">Debit Head</th>
                        <th width="180px">Credit Head</th>
                        <th>Narration</th>
                        <th width="80px">Amount</th>
                    </tr>
                </thead>

                <tbody>
                    @php
                        $sl = 1;
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
                        @endphp
                        <tr>
                            <td>{{ $sl++ }}</td>
                            <td>{{ date('d-m-Y',strtotime($voucherList->date)) }}</td>
                            <td>{{ $voucherList->voucherNo }}</td>
                            <td>{{ $debitHeadName->headName }}</td>
                            <td>{{ $creditHeadName->headName }}</td>
                            <td>{{ $voucherList->narration }}</td>
                            <td align="right">{{ $voucherList->amount }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

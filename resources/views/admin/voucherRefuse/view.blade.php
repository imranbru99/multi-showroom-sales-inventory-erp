@extends('admin.layouts.master')

@section('custom_css')
    <style type="text/css">
        .voucher-head{
            border: 1px solid #ddd; text-align: center; height: 0px; line-height: 0px; padding: 25px;
        }

        .voucher-head p{
            font-size: 20px; font-weight: bold;
        }
    </style>
@endsection

@section('content')
    <div style="padding-bottom: 10px;"></div>

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6"><h4 class="card-title">{{ $title }}</h4></div>

                <div class="col-md-6 text-right">
                    <a class="btn btn-outline-info btn-lg" href="{{ route('voucherRefuse.index') }}">
                        <i class="fa fa-arrow-circle-left"></i> Go Back
                    </a>

                    <form id="print" action="{{ route('voucherRefuse.approve') }}" method="post" enctype="multipart/form-data" style="float: right; margin-left: 5px;">
                        {{ csrf_field() }}

                        <input type="hidden" name="voucherApproveId" value="{{ $accountTransaction->id }}">
                        <input type="hidden" name="view" value="view">

                        <button type="submit" class="btn btn-outline-danger btn-lg waves-effect">
                            <i class="{{ $accountTransaction->approve == 0 ? 'fa fa-thumbs-up' : 'fa fa-thumbs-down' }}"></i> {{ $accountTransaction->approve == 0 ? 'Apporve' : 'Refuse' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="voucher-head">
                <p>
                    Status: 
                    @if ($accountTransaction->approve == 0)
                        <font color="red">Pending</font>
                    @else
                        <font color="green">Approve</font>
                    @endif
                </p>
            </div>

            <div style="padding-bottom: 10px;"></div>

            @if ($accountTransaction->voucher_type == "DV")
                <table class="table table-borderless table-striped table-responsive-sm">
                    <tbody>
                        <tr>
                            <th style="width: 200px; font-weight: bold;">Company Name</th>
                            <th style="width: 10px; font-weight: bold;">:</th>
                            <td>{{ $accountTransaction->showroomName }}</td>
                        </tr>

                        <tr>
                            <th style="width: 200px; font-weight: bold;">Credit Account Head Name</th>
                            <th style="width: 10px; font-weight: bold;">:</th>
                            <td>{{ $accountTransaction->accountHeadName }}</td>
                        </tr>
                        
                        <tr>
                            <th style="width: 200px; font-weight: bold;">Voucher No.</th>
                            <th style="width: 10px; font-weight: bold;">:</th>
                            <td>{{ $accountTransaction->voucher_no }}</td>
                        </tr>
                        
                        <tr>
                            <th style="width: 200px; font-weight: bold;">Transaction Date</th>
                            <th style="width: 10px; font-weight: bold;">:</th>
                            <td>{{ date('d-m-Y', strtotime($accountTransaction->voucher_date)) }}</td>
                        </tr>
                        
                        <tr>
                            <th style="width: 200px; font-weight: bold;">Remarks</th>
                            <th style="width: 10px; font-weight: bold;">:</th>
                            <td>{{ $accountTransaction->narration }}</td>
                        </tr>
                    </tbody>
                </table>

                <table class="table table-bordered table-striped table-responsive-sm">
                    <thead class="thead-green">
                        <tr>
                            <th style="font-weight: bold;">Account Name</th>
                            <th style="font-weight: bold; width: 120px;">Debit</th>
                        </tr>
                    </thead>
                    <tbody id="tbody">
                        @php
                            $i = 0;
                        @endphp

                        @foreach ($accountTransactionLists as $accountTransactionList)
                            @php
                                $i++;
                            @endphp
                            <tr>
                                <td style="font-weight: bold;">{{ $accountTransactionList->accountHeadName }}</td>

                                <td align="right">{{ $accountTransactionList->debit_amount }}</td>
                            </tr>
                        @endforeach
                    </tbody>

                    <tfoot>
                        <tr>
                            <th style="text-align: center; font-weight: bold;">Total Amount</th>
                            <th style="text-align: right; font-weight: bold;">{{ $accountTransaction->credit_amount }}</th>
                        </tr>
                    </tfoot>
                </table>
            @endif

            @if ($accountTransaction->voucher_type == "CV")
                <table class="table table-borderless table-striped table-responsive-sm">
                    <tbody>
                        <tr>
                            <th style="width: 200px; font-weight: bold;">Company Name</th>
                            <th style="width: 10px; font-weight: bold;">:</th>
                            <td>{{ $accountTransaction->showroomName }}</td>
                        </tr>

                        <tr>
                            <th style="width: 200px; font-weight: bold;">Debit Account Head Name</th>
                            <th style="width: 10px; font-weight: bold;">:</th>
                            <td>{{ $accountTransaction->accountHeadName }}</td>
                        </tr>
                        
                        <tr>
                            <th style="width: 200px; font-weight: bold;">Voucher No.</th>
                            <th style="width: 10px; font-weight: bold;">:</th>
                            <td>{{ $accountTransaction->voucher_no }}</td>
                        </tr>
                        
                        <tr>
                            <th style="width: 200px; font-weight: bold;">Transaction Date</th>
                            <th style="width: 10px; font-weight: bold;">:</th>
                            <td>{{ date('d-m-Y', strtotime($accountTransaction->voucher_date)) }}</td>
                        </tr>
                        
                        <tr>
                            <th style="width: 200px; font-weight: bold;">Remarks</th>
                            <th style="width: 10px; font-weight: bold;">:</th>
                            <td>{{ $accountTransaction->narration }}</td>
                        </tr>
                    </tbody>
                </table>

                <table class="table table-bordered table-striped table-responsive-sm">
                    <thead class="thead-green">
                        <tr>
                            <th style="font-weight: bold;">Account Name</th>
                            <th style="font-weight: bold; width: 120px;">Credit</th>
                        </tr>
                    </thead>
                    <tbody id="tbody">
                        @php
                            $i = 0;
                        @endphp

                        @foreach ($accountTransactionLists as $accountTransactionList)
                            @php
                                $i++;
                            @endphp
                            <tr>
                                <td style="font-weight: bold;">{{ $accountTransactionList->accountHeadName }}</td>

                                <td align="right">{{ $accountTransactionList->credit_amount }}</td>
                            </tr>
                        @endforeach
                    </tbody>

                    <tfoot>
                        <tr>
                            <th style="text-align: center; font-weight: bold;">Total Amount</th>
                            <th style="text-align: right; font-weight: bold;">{{ $accountTransaction->debit_amount }}</th>
                        </tr>
                    </tfoot>
                </table>
            @endif

            @if ($accountTransaction->voucher_type == "JV" || $accountTransaction->voucher_type == "OB")
                <table class="table table-borderless table-striped table-responsive-sm">
                    <tbody>
                        <tr>
                            <th style="width: 200px; font-weight: bold;">Company Name</th>
                            <th style="width: 10px; font-weight: bold;">:</th>
                            <td>{{ $accountTransaction->showroomName }}</td>
                        </tr>
                        
                        <tr>
                            <th style="width: 200px; font-weight: bold;">Voucher No.</th>
                            <th style="width: 10px; font-weight: bold;">:</th>
                            <td>{{ $accountTransaction->voucher_no }}</td>
                        </tr>
                        
                        <tr>
                            <th style="width: 200px; font-weight: bold;">Transaction Date</th>
                            <th style="width: 10px; font-weight: bold;">:</th>
                            <td>{{ date('d-m-Y', strtotime($accountTransaction->voucher_date)) }}</td>
                        </tr>
                        
                        <tr>
                            <th style="width: 200px; font-weight: bold;">Remarks</th>
                            <th style="width: 10px; font-weight: bold;">:</th>
                            <td>{{ $accountTransaction->narration }}</td>
                        </tr>
                    </tbody>
                </table>

                <table class="table table-bordered table-striped table-responsive-sm">
                    <thead class="thead-green">
                        <tr>
                            <th style="font-weight: bold;">Account Name</th>
                            <th style="font-weight: bold; width: 120px;">Debit</th>
                            <th style="font-weight: bold; width: 120px;">Credit</th>
                        </tr>
                    </thead>
                    <tbody id="tbody">
                        @php
                            $totalDebit = 0;
                            $totalCredit = 0;
                        @endphp

                        @foreach ($accountTransactionLists as $accountTransactionList)
                            @php
                                $totalDebit = $totalDebit + $accountTransactionList->debit_amount;
                                $totalCredit = $totalCredit + $accountTransactionList->credit_amount;
                            @endphp
                            <tr>
                                <td style="font-weight: bold;">{{ $accountTransactionList->accountHeadName }}</td>

                                <td align="right">{{ $accountTransactionList->debit_amount }}</td>
                                <td align="right">{{ $accountTransactionList->credit_amount }}</td>
                            </tr>
                        @endforeach
                    </tbody>

                    <tfoot>
                        <tr>
                            <th style="text-align: center; font-weight: bold;">Total Amount</th>
                            <th style="text-align: right; font-weight: bold;">{{ $totalDebit }}</th>
                            <th style="text-align: right; font-weight: bold;">{{ $totalCredit }}</th>
                        </tr>
                    </tfoot>
                </table>
            @endif
        </div>
    </div>
@endsection
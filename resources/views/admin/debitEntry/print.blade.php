@extends('admin.layouts.masterPrint')

@section('content')
    <table id="report-header">
        <tr>
            @if ($fromDate == "" && $toDate == "")
                <td>Debit Voucher List</td>
            @else
                <td>Debit Voucher List From {{ date('d-m-Y',strtotime($fromDate)) }} Month  Of {{ date('d-m-Y',strtotime($toDate)) }}</td>
            @endif
        </tr>
    </table>

    <div id="pad-bottom"></div>

    <table id="report-table">
        <thead>
            <tr>
                <th width="20px">SL</th>
                <th width="100px">Vouchar No</th>
                <th width="60px">Date</th>
                <th width="150px">Debit Head</th>
                <th width="150px">Credit Head</th>
                <th width="90px">Account Status</th>
                <th width="50px">Amount</th>
            </tr>
        </thead>

        <tbody>            
            @php
                $i = 0;
                $total = 0;
            @endphp
            @foreach ($transactionLists as $transactionList)
                @php
                    $i++;
                    $total = $total + $transactionList->credit_amount;
                @endphp
                <tr>
                    <td>{{ $i }}</td>
                    <td>{{ $transactionList->voucher_no }}</td>
                    <td>{{ date('d-m-Y',strtotime($transactionList->voucher_date)) }}</td>
                    <td>{{ $transactionList->debitHeadname }}</td>
                    <td>{{ $transactionList->creditHeadName }}</td>
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
                    <td align="right">{{ $transactionList->credit_amount }}</td>
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td style="text-align: center;" colspan="6"><b>Total</b></td>
                <td style="text-align: right;"><b>{{ $total }}</b></td>
            </tr>
        </tfoot>
    </table>

    <div id="pad-bottom"></div>

    <table  id="report-table">
    </table>
@endsection

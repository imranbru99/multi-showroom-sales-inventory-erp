@extends('admin.layouts.masterPrint')

@section('content')
    <table id="report-header">
        <tr>
        	<td>Opening Balance</td>
        </tr>
    </table>

    <div id="pad-bottom"></div>

    <table id="account-voucher-header">
        <thead>
        	<tr>
        		<th align="left">
        			Voucher No: {{ $openingBalance->voucher_no }}
        		</th>
        		<th align="right">
        			Date: {{ date('d-m-Y',strtotime($openingBalance->voucher_date)) }}    			
        		</th>
        	</tr>
        </thead>
    </table>

    <div id="pad-bottom"></div>

    <table  id="account-voucher-table">
        <thead>
            <tr>
                <th width="20px">SL</th>
                <th>Account Name</th>
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
            @foreach ($openingBalances as $openingBalanceInfo)
                @php
                    $totalDebit = $totalDebit + $openingBalanceInfo->debit_amount;
                    $totalCredit = $totalCredit + $openingBalanceInfo->credit_amount;
                @endphp
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ $openingBalanceInfo->accountHeadName }}</td>
                    <td align="right">{{ $openingBalanceInfo->debit_amount }}</td>
                    <td align="right">{{ $openingBalanceInfo->credit_amount }}</td>
                </tr>
            @endforeach
        </tbody>

        <tfoot>
        	<tr>
        		<td colspan="4" style="padding: 20px 10px 20px 10px;">{{ $openingBalanceInfo->narration }}</td>
        	</tr>

            <tr>
                <td style="text-align: center;" colspan="2"><b>Total</b></td>
                <td style="text-align: right;"><b>{{ $totalDebit }}</b></td>
                <td style="text-align: right;"><b>{{ $totalCredit }}</b></td>
            </tr>

            <tr>
                <th style="text-align: left; padding: 10px" colspan="4">	                
	                @php
	                    $inWords = \App\HelperClass::numberToWords($totalDebit);
	                @endphp
	                In Words : {{ $inWords }} Taka Only.
                	<b></b>
                </th>
            </tr>
        </tfoot>
    </table>

    <div style="padding: 60px"></div>

    <table id="voucher-sign-table">
        <tr>
            <td>
                <span><h3 class="overline">Receive From</h3></span>
            </td>
            <td align="right">
                <span>
                    <span><h3 class="overline">Accountant</h3></span>
                </span>
            </td>
        </tr>
    </table>
@endsection

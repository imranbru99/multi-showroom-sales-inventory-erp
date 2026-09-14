@extends('admin.layouts.masterPrint')

@section('content')
    <table id="report-header">
        <tr>
        	<td>Debit Voucher</td>
        </tr>
    </table>

    <div id="pad-bottom"></div>

    <table id="account-voucher-header">
        <thead>
        	<tr>
        		<th align="left">
        			Voucher No: {{ $debitEntry->voucher_no }}
        		</th>
        		<th align="right">
        			Date: {{ date('d-m-Y',strtotime($debitEntry->voucher_date)) }}    			
        		</th>
        	</tr>
        </thead>
    </table>

    <div id="pad-bottom"></div>

    <table  id="account-voucher-table">
        <thead>
            <tr>
                <th colspan="2" align="left" style="padding: 10px 10px">Account Head Of: {{ $debitEntry->narration }}</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td style="padding: 100px 10px">{{ $debitEntry->narration }}</td>
                <td align="right" style="padding: 100px 10px">{{ $debitEntry->credit_amount }}</td>
            </tr>
        </tbody>

        <tfoot>
            <tr>
                <th style="text-align: left; padding: 10px" colspan="2">	                
	                @php
	                    $inWords = \App\HelperClass::numberToWords($debitEntry->credit_amount);
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

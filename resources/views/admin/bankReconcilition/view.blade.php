@extends('admin.layouts.master')

@section('content')
    <div style="padding-bottom: 10px;"></div>

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6"><h4 class="card-title">{{ $title }}</h4></div>
                <div class="col-md-6 text-right">
                	<a class="btn btn-outline-info btn-lg" href="{{ route($goBackLink) }}">
                		<i class="fa fa-arrow-circle-left"></i> Go Back
                	</a>
                </div>
            </div>
        </div>

        <div class="card-body">
        	<table class="table table-borderless table-striped table-responsive-sm">
        		<tbody>
        			<tr>
        				<th style="width: 200px; font-weight: bold;">Company Name</th>
        				<th style="width: 10px; font-weight: bold;">:</th>
        				<td>{{ $journalEntry->showroomName }}</td>
        			</tr>
        			
        			<tr>
        				<th style="width: 200px; font-weight: bold;">Voucher No.</th>
        				<th style="width: 10px; font-weight: bold;">:</th>
        				<td>{{ $journalEntry->voucher_no }}</td>
        			</tr>
        			
        			<tr>
        				<th style="width: 200px; font-weight: bold;">Transaction Date</th>
        				<th style="width: 10px; font-weight: bold;">:</th>
        				<td>{{ date('d-m-Y', strtotime($journalEntry->voucher_date)) }}</td>
        			</tr>
        			
        			<tr>
        				<th style="width: 200px; font-weight: bold;">Remarks</th>
        				<th style="width: 10px; font-weight: bold;">:</th>
        				<td>{{ $journalEntry->narration }}</td>
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

                	@foreach ($journalEntries as $journalEntryInfo)
                		@php
                            $totalDebit = $totalDebit + $journalEntryInfo->debit_amount;
                            $totalCredit = $totalCredit + $journalEntryInfo->credit_amount;
                		@endphp
                        <tr>
                            <td style="font-weight: bold;">{{ $journalEntryInfo->accountHeadName }}</td>

                            <td align="right">{{ $journalEntryInfo->debit_amount }}</td>
                            <td align="right">{{ $journalEntryInfo->credit_amount }}</td>
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
        </div>
    </div>
@endsection
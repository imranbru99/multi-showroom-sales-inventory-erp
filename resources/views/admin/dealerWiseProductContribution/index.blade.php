@extends('admin.layouts.masterReport')

@section('search_card_body')
    <input type="hidden" name="print" value="print">
    <div class="row">
        <div class="col-md-6">
            <label for=""></label>
             <div class="form-group">
                 <div class="form-check-inline">
                    <label class="form-check-label">
                        <input type="radio" value="Categories" name="searchType" class="searchType" {{ $searchType == "Categories" ? "checked" : "" }}> By Categories
                    </label>
                </div>

                <div class="form-check-inline">
                    <label class="form-check-label">
                        <input type="radio" value="Products" name="searchType" class="searchType" {{ $searchType == "Products" ? "checked" : "" }}> By Products
                    </label>
                </div>
            </div>
        </div>

        <div class="col-md-3 form-group">
            <label for="from-date">From Date</label>
            <input  type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'from_date' }}" name="fromDate" value="{{ date('d-m-Y',strtotime($fromDate)) }}" placeholder="Select Date From">
        </div>

        <div class="col-md-3 form-group">
            <label for="to-date">To Date</label>
            <input  type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'to_date' }}" name="toDate" value="{{ date('d-m-Y',strtotime($toDate)) }}" placeholder="Select Date To">
        </div>
    </div>
@endsection

@section('print_card_header')
    <input type="hidden" name="searchType" value="{{ $searchType }}">
    <input type="hidden" name="fromDate" value="{{ $fromDate }}">
    <input type="hidden" name="toDate" value="{{ $toDate }}">
    <input type="hidden" id="print_value" name="print" value="{{ $print }}">
@endsection

@section('print_card_body')
	<table id="dataTable" name="paymentRecordTable" class="table table-bordered table-sm">
		<thead>
			<tr>
                <th width="20px">Sl</th>
                <th>Dealers Name</th>
                <th class="byCategoryOrProduct">{{ $searchType == '' ? 'Categories' : $searchType }} Name</th>
                <th width="90px">By Qty</th>
                <th width="85px">Qty (%)</th>
                <th width="105px">By Value</th>
                <th width="85px">Value (%)</th>
			</tr>
		</thead>

		<tbody>
            @php
                $sl = 1;
            @endphp

            @foreach ($contributions as $contribution)
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ $contribution->dealerName }}</td>
                    <td>{{ $searchType == 'Categories' ? $contribution->categoryName : $contribution->productName }}</td>
                    <td align="right">{{ $contribution->totalIssueQty }}</td>
                    <td align="right">{{ number_format($contribution->percentageQty, 2, '.', '') }}</td>
                    <td align="right">{{ $contribution->totalIssueAmount }}</td>
                    <td align="right">{{ number_format($contribution->percentageAmount, 2, '.', '') }}</td>
                </tr>
            @endforeach
		</tbody>
	</table>
@endsection
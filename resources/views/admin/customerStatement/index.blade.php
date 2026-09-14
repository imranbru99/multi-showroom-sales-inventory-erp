@extends('admin.layouts.masterReport')

@section('search_card_body')
    <input type="hidden" name="print" value="print">
    <div class="row">
        <div class="col-md-6">
            <label for="customer">Customer</label>
            <div class="form-group">
                <select class="form-control chosen-select" id="customer" name="customer" >
                    @foreach ($customers as $customerInfo)
                        <option value="{{ $customerInfo->id }}" 
                            @if ($customerInfo->id == $customerId)
                                selected
                            @endif
                            >{{ $customerInfo->name }} - ({{ $customerInfo->code }})</option>
                    @endforeach
                </select>
            </div>  
        </div>

        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="from-date">From Date</label>
                    <input  type="text" class="form-control datepicker" name="fromDate" value="{{ date('d-m-Y', strtotime($fromDate)) }}" placeholder="Select Date From">
                </div>
                <div class="col-md-6 form-group">
                    <label for="to-date">To Date</label>
                    <input  type="text" class="form-control datepicker" name="toDate" value="{{ date('d-m-Y', strtotime($toDate)) }}" placeholder="Select Date To">
                </div>
            </div>                                  
        </div>
    </div>
@endsection

@section('print_card_header')
    @if ($customerId)
            <input type="hidden" name="customer" value="{{ $customerId }}">
    @endif

    <input type="hidden" name="fromDate" value="{{ $fromDate }}">
    <input type="hidden" name="toDate" value="{{ $toDate }}">
    <input type="hidden" id="print_value" name="print" value="{{ $print }}">
@endsection

@section('print_card_body')
	<table id="dataTable" name="paymentRecordTable" class="table table-bordered table-sm">
		<thead>
            <tr>
                <th colspan="11" style="text-align: right; font-weight: bold;">Previous Balance = </th>
                <th style="text-align: left;">
            @if ($customer)
                {{ $previousBalance }}
            @endif

            </th>
            </tr>
			<tr>
                <th width="20px">Sl</th>
				<th width="100px">Date</th>
				<th>Customer Name</th>
				<th width="200px">Inv/MR No</th>
				<th width="200px">Sales</th>
				<th width="200px">Return</th>
                <th width="70px">Discount</th>
                <th width="70px">Gift Voucher</th>
                <th width="70px">Exchange CRT</th>
                <th width="200px">Collection</th>
                <th width="200px">Balance</th>
                <th width="70px">Agreement</th>

			</tr>
		</thead>

		<tbody>
            @if ($customer)
            @php

                $sl = 0;
            @endphp
            @foreach ($customerStatements as $customerStatement)
                <tr>
                    <td>{{ $customerStatement['sl'] }}</td>
                    <td>{{ $customerStatement['date'] }}</td>
                    <td>{{ $customerStatement['customer_name'] }}</td>
                    <td>{{ $customerStatement['invoice_no'] }}</td>
                    <td>{{ $customerStatement['sale'] }}</td>
                    <td>{{ $customerStatement['return'] }}</td>
                    <td>{{ $customerStatement['discount'] }}</td>
                    <td>{{ $customerStatement['giftVoucher'] }}</td>
                    <td>{{ $customerStatement['exchangeCRT'] }}</td>
                    <td>{{ $customerStatement['collection'] }}</td>
                    <td>{{ $customerStatement['due'] }}</td>
                    <td>{{ $customerStatement['agreement'] }}</td>
                </tr>
            @endforeach
            @endif
		</tbody>
	</table>
@endsection
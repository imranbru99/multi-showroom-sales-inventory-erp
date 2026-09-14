@extends('admin.layouts.masterReport')
@section('search_card_body')
<input type="hidden" value="true" name="searched">
    <div class="row">

        <div class="col-md-4">
            <label for="customer">Customers</label>
            <div class="form-group">
                <select class="form-control chosen-select" id="customer" name="customer[]" multiple>
                    @foreach ($customers as $customerInfo)
                        <option value="{{ $customerInfo->id }}">{{ $customerInfo->name }}  ({{ $customerInfo->code }})</option>
                    @endforeach
                </select>
            </div>  
        </div>

        <div class="col-md-4">
            <label for="customer">Invoice Duration (Days)</label>
            <div class="form-group">
                <input type="number" class="form-control" name="dayBefore" value="{{ $dayBefore }}">
            </div>  
        </div>

        <div class="col-md-4">
            <label for="customer">Collector</label>
            <div class="form-group">
                <select name="staff" class="form-control chosen-select">
                    <option value="">Select a collector</option>
                    @foreach ($staffs as $staff)
                    <option value="{{ $staff->id }}" @if ($staffId==$staff->id)
                        selected
                        @endif
                        >{{ $staff->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

    </div>
@endsection

@section('print_card_header')
    @if ($customer)
        @foreach ($customer as $customerInfo)
            <input type="hidden" name="customer[]" value="{{ $customerInfo }}">
        @endforeach
    @endif
    <input type="hidden" class="form-control" name="dayBefore" value="{{ $dayBefore }}">
    <input type="hidden" class="form-control" name="staffId" value="{{ $staffId }}">
    <input type="hidden" id="print_value" name="print" value="Print">
@endsection

@section('print_card_body')
	<table id="dataTable" name="paymentRecordTable" class="table table-bordered table-sm">
		<thead>
			<tr>
                <th width="20px">Sl</th>
				<th>A/C No.</th>
				<th>Account Name</th>
				<th width="100px">Mobile No</th>
				<th width="110px">Sales Amount</th>
				<th width="110px">Discount</th>
				<th width="110px">Gift Voucher</th>
				<th width="110px">Exchange Crt</th>
                <th width="100px">Collection</th>
                <th width="100px">Outstanding</th>
			</tr>
		</thead>

		<tbody>
            @foreach ($customerOutstandings as $customerOutstanding)
                <tr>
                    <td>{{ $customerOutstanding['sl'] }}</td>
                    <td>{{ $customerOutstanding['customerAccountCode'] }}</td>
                    <td>{{ $customerOutstanding['customerName'] }}</td>
                    <td>{{ $customerOutstanding['customerMobile'] }}</td>
                    <td>{{ $customerOutstanding['totalsaleAmount'] }}</td>
                    <td>{{ $customerOutstanding['totalsaleDiscount'] }}</td>
                    <td>{{ $customerOutstanding['totalsaleGiftVoucher'] }}</td>
                    <td>{{ $customerOutstanding['totalsaleExchangeCrt'] }}</td>
                    <td>
                        {{$customerOutstanding['totalCollectionAmount']}}
                    </td>
                    <td>
                        {{ $customerOutstanding['outstanding'] }}
                    </td>
                </tr>
            @endforeach
		</tbody>
	</table>
@endsection
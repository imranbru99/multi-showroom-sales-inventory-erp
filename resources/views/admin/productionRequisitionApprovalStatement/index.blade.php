@extends('admin.layouts.masterReport')

@section('search_card_body')
    <input type="hidden" name="print" value="print">
    <div class="row">
        <div class="col-md-6">
            <label for="staff">Requisition By</label>
            <div class="form-group">
                <select class="form-control chosen-select" id="staff" name="staff[]" multiple>
                    @foreach ($staffs as $dealerInfo)
                        @php
                            $select = "";
                            if ($staff)
                            {
                                if (in_array($dealerInfo->id, $staff))
                                {
                                    $select = "selected";
                                }
                                else
                                {
                                    $select = "";
                                }
                            }
                        @endphp
                        <option value="{{ $dealerInfo->id }}" {{ $select }}>{{ $dealerInfo->name }}</option>
                    @endforeach
                </select>
            </div>  
        </div>

        <div class="col-md-6">
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
    </div>
@endsection

@section('print_card_header')
    @if ($staff)
        @foreach ($staff as $dealerInfo)
            <input type="hidden" name="staff[]" value="{{ $dealerInfo }}">
        @endforeach
    @endif

    <input type="hidden" name="fromDate" value="{{ $fromDate }}">
    <input type="hidden" name="toDate" value="{{ $toDate }}">
    <input type="hidden" id="print_value" name="print" value="{{ $print }}">
@endsection

@section('print_card_body')
	<table id="dataTable" name="paymentRecordTable" class="table table-bordered table-sm">
		<thead>
			<tr>
                <th width="20px">Sl</th>
                <th>Requisition Name</th>
                <th>Product Name</th>
                <th width="200px">Model</th>
                <th width="80px">Qty</th>
                <th width="100px">Approve Qty</th>
			</tr>
		</thead>

		<tbody>
            @php
                $sl = 1;
            @endphp

            @foreach ($requisitionApprovalStatements as $requisitionApprovalStatement)
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ $requisitionApprovalStatement->staffName }}</td>
                    <td>{{ $requisitionApprovalStatement->productName }}</td>
                    <td>{{ $requisitionApprovalStatement->productModelNo }}</td>
                    <td>{{ $requisitionApprovalStatement->requisitionQty }}</td>
                    <td>{{ $requisitionApprovalStatement->approvedQty }}</td>
                </tr>
            @endforeach
		</tbody>
	</table>
@endsection
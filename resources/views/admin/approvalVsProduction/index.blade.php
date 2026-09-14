@extends('admin.layouts.masterReport')

@section('search_card_body')
<input type="hidden" name="print" value="print">
<div class="row">

    <div class="col-md-3 form-group">
        <label for="from-date">From Date</label>
        <input type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'from_date' }}"
               name="fromDate" value="{{ date('d-m-Y', strtotime($fromDate)) }}" placeholder=" Select Date From"
               autocomplete="off">
    </div>

    <div class="col-md-3 form-group">
        <label for="to-date">To Date</label>
        <input type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'to_date' }}" name="toDate"
               value="{{ date('d-m-Y', strtotime($toDate)) }}" placeholder="Select Date To" autocomplete="off">
    </div>
</div>
@endsection

@section('print_card_header')
<input type="hidden" name="fromDate" value="{{ @$fromDate }}">
<input type="hidden" name="toDate" value="{{ @$toDate }}">
<input type="hidden" id="print_value" name="print" value="{{ @$print }}">
@endsection

@section('print_card_body')

<table id="dataTable" name="paymentRecordTable" class="table table-bordered table-sm">
    <thead>
        <tr>
            <th width="20px">Sl</th>
            <th>Requisition Date</th>
            <th>Requisition No</th>
            <th>Requisition By</th>
            <th>Product Name</th>
            <th>Model Number</th>
            <th>Requisition Qty</th>
            <th>Production Qty</th>
        </tr>
    </thead>

    <tbody>
        @foreach($approvalVsProduction as $approvalVsProduct)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ date('d-m-Y', strtotime($approvalVsProduct->date)) }}</td>
            <td>{{ $approvalVsProduct->requisition_no }}</td>
            <td>{{ $approvalVsProduct->requisition->staff->name }}</td>
            <td>{{ $approvalVsProduct->product_name }}</td>
            <td>{{ $approvalVsProduct->model_no }}</td>
            <td>{{ $approvalVsProduct->qty }}</td>
            <td>{{ $approvalVsProduct->approved_qty }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection



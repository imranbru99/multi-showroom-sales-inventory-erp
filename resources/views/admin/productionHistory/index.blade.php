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
            <th>Production Date</th>
            <th>Batch Number</th>
            <th>Product Name</th>
            <th>Model Number</th>
            <th>Product Qty</th>
            <th width="300px">Serial No</th>
        </tr>
    </thead>

    <tbody>
        @foreach($productionHistories as $productionHistory)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ date('d-m-Y', strtotime($productionHistory->date)) }}</td>
            <td>{{ $productionHistory->serial_no }}</td>
            <td>{{ $productionHistory->productName->product_name }}</td>
            <td>{{ $productionHistory->productName->model_no }}</td>
            <td>{{ $productionHistory->total_qty }}</td>
            <td>
                @foreach($productionHistory->producttionList as $serial)

                {{ $serial->serial_no }},

                @endforeach
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection



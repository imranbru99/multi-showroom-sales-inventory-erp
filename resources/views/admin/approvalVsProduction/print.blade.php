@extends('admin.layouts.masterPrint')

@section('content')
<table id="report-header">
    <tr>
        <td>{{ $title }} ON
            @if (date('d-m-Y', strtotime($fromDate)) == '01-01-1970')
            01-01-2020
            @else
            {{ date('d-m-Y', strtotime($fromDate)) }}
            @endif
            To
            {{ date('d-m-Y', strtotime($toDate)) }}
        </td>
    </tr>
</table>

<div id="pad-bottom"></div>
<table id="report-table" name="paymentRecordTable" class="table table-bordered table-sm">
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
    <tfoot>
        <tr>

        </tr>
    </tfoot>
</table>



<div class="row">
    <div class="col-md-12 text-right">
        <p>Print Date & Time : {{ date('d-m-Y h:i:sa', strtotime(now())) }}</p>
    </div>
</div>

<style>
    #report-table td{
        text-align: center
    }
</style>

@endsection

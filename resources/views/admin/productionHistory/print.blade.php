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
            <th>Batch Number</th>
            <th>Production Date</th>
            <th>Product Name</th>
            <th>Model Number</th>
            <th>Product Qty</th>
            <th>Serial No</th>
        </tr>
    </thead>

    <tbody>

        <tr>
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
        </tr>

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

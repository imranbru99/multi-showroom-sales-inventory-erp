@extends('admin.layouts.masterPrint')
@php
error_reporting(0);
@endphp
@section('content')
<table id="report-header">
    <tr>
        <td>{{ $title }} For {{ $customer->name }} ({{$customer->code}})</td>
    </tr>
</table>

<div id="pad-bottom"></div>

<table id="report-table">
    <thead class="thead-light">
        <tr>
            <th width="20px">SL</th>
            <th>Sale Date</th>
            <th>Product Name</th>
            <th>Product Model</th>
            <th>Product Serial</th>
            <th>Warranty Time</th>
            <th>Warranty Status</th>
        </tr>
    </thead>

    <tbody>
        @foreach($warrantyProducts as $warrantyProduct)
        <?php
            $warrantyStatus = 'None';
            if($warrantyProduct->warranty){
                $date = $warrantyProduct->sale->sale_date;
                $todayDate = now();
                $totalDuration = $todayDate->diffInDays($date);
                $duartionMonth = $totalDuration / 30;
                $totalWarranty = 12 * 30;
                $warrantyStatus = 'End';
                if($duartionMonth < 12 ){
                    $warrantyStatus = $totalWarranty - $totalDuration;
                }
            }
            ?>
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ date('d-m-Y', strtotime($warrantyProduct->sale->sale_date)) }}</td>
            <td>{{ $warrantyProduct->product->name }}</td>
            <td>{{ $warrantyProduct->product_model }}</td>
            <td>{{ $warrantyProduct->product_serial }}</td>
            <td>{{ $warrantyProduct->warranty }}</td>
            <td>{{ $warrantyStatus }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div id="pad-bottom"></div>


<div class="row">
    <div class="col-md-12 text-right">
        <?php
            date_default_timezone_set('Asia/Dhaka');
            ?>
        <p>Print Date & Time : <?php echo date('d-m-Y h:i:sa'); ?></p>
    </div>
</div>


@endsection

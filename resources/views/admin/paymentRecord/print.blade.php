@extends('admin.layouts.masterPrint')

@section('content')
<table id="report-header">
    @if ($btnPrintSummary == "Print Summary")
    <tr><td>Payment Summary On {{ date('d-m-Y',strtotime($fromDate)) }} To {{ date('d-m-Y',strtotime($toDate)) }}</td></tr>                               
    @endif

    @if ($btnPrintRecord == "Print Record")
    <tr><td>Payment History On {{ date('d-m-Y',strtotime($fromDate)) }} To {{ date('d-m-Y',strtotime($toDate)) }}</td></tr>                
    @endif
</table>

<div id="pad-bottom"></div>

@if ($btnPrintSummary == "Print Summary")
<table id="report-table">
    <thead>
        <tr>
            <th width="20px">SL#</th>
            <th>Name</th>
            <th width="200px">Payment</th>
        </tr>
    </thead>

    <tbody>
        <?php
        $total = 0;
        ?>
        @foreach ($paymentSummaries as $paymentSummary)
        <?php
        $total += $paymentSummary->price;
        ?>
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $paymentSummary->vendorName }}</td>
            <td align="right">{{ number_format($paymentSummary->price, 2, '.', '') }}</td>
        </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <td align="right" colspan="2"><h3>Total Payment</h3></td>
            <td align="right">{{ number_format($total, 2, '.', '') }}</td>
        </tr>
    </tfoot>
</tbody>
</table>
@endif


@if ($btnPrintRecord == "Print Record")
<table id="report-table">
    <thead>
        <tr>
            <th width="20px">SL#</th>
            <th width="80px">Date</th>
            <th width="100px">Payment No</th>
            <th>Name</th>
            <th>Pay Mode</th>
            <th>Remarks</th>
            <th width="110px">Payment</th>
        </tr>
    </thead>

    <tbody>
        @php
        $sl = 1;
        $total = 0;
        @endphp
        @foreach ($productRecords as $productRecord)
        @php
        $total = $total + $productRecord->price;
        @endphp
        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ date('d-m-Y', strtotime($productRecord->paymentDate)) }}</td>
            <td>{{ $productRecord->paymentNo }}</td>
            <td>{{ $productRecord->vendorName }}</td>
            <td>{{ $productRecord->paymentType }}</td>
            <td>{{ $productRecord->remarks }}</td>
            <td align="right">{{ number_format($productRecord->price, 2, '.', '') }}</td>
        </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <td align="right" colspan="6"><h3>Total Payment</h3></td>
            <td align="right">{{ number_format($total, 2, '.', '') }}</td>
        </tr>
    </tfoot>
</table>
@endif
<div class="row">
    <div class="col-md-12 text-right">
        <?php
        date_default_timezone_set("Asia/Dhaka");
        ?>
        <p>Print Date & Time : <?php echo date("d-m-Y h:i:sa"); ?></p>
    </div>
</div>
@endsection

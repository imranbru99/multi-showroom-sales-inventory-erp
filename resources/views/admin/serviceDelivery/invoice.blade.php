@extends('admin.layouts.masterPrint')

@section('custome-css')
<style>
    #report-table td,
    #report-table th {
        border: 1px solid #ddd;
    }

    #report-table tbody td {
        vertical-align: top;
    }

    #invoice-table {
        width: 100%;
        border-collapse: collapse;
    }

    #invoice-table td {
        width: 50%;
        border: 0px solid black;
    }

    #invoice-header {
        background-color: lightgray;
        width: 100%;
        padding: 5px;
        text-align: center;
        font-weight: bold;
        font-size: 20px;
    }

    #invoice-footer {
        background-color: lightgray;
        width: 100%;
    }

    #invoice-footer th {
        border: 0px solid white;
        padding: 5px;
        text-align: right;
        font-size: 14px;
        width: 180px;
    }

    #invoice-footer td {
        border: 1px solid black;
        padding: 5px;
    }

</style>
@endsection

@section('content')
<table id="invoice-header">
    <tr>
        <td>{{ $title }}</td>
    </tr>
</table>

<div id="pad-bottom"></div>

<table width="100%">
    <tbody>
        <tr>
            <td width="150px"><b>Delivery Date</b></td>
            <td width="10px"><b>:</b></td>
            <td>{{ date('d-m-Y', strtotime($serviceDelivery->delivery_date)) }}</td>

            <td align="right" width="60px"><b>Job No</b></td>
            <td align="right" width="10px"><b>:</b></td>
            <td align="right" width="200px">{{ $serviceDelivery->invoice_no }}</td>
        </tr>
        <tr>
            <td width="105px"><b>Receive Inv No</b></td>
            <td width="10px"><b>:</b></td>
            <td>{{ $serviceDelivery->serviceAllocation->serviceProduct->invoice_no }}</td>

            <td align="right" width="60px"><b>Dealer</b></td>
            <td align="right" width="10px"><b>:</b></td>
            <td align="right">{{ @$serviceDelivery->serviceAllocation->serviceProduct->dealer->name }}</td>
        </tr>
        <tr>
            <td width="105px"><b>Staff</b></td>
            <td width="10px"><b>:</b></td>
            <td>{{ $serviceDelivery->serviceAllocation->staff->name }}</td>

            <td align="right" width="60px"><b>Pay-Type</b></td>
            <td align="right" width="10px"><b>:</b></td>
            <td align="right" style="text-transform: capitalize;">{{ $serviceDelivery->service_payment_type }}</td>
        </tr>
    </tbody>
</table>

<div id="pad-bottom"></div>

<table id="report-table">
    <thead>
        <tr>
            <th width="20px">SL#</th>
            <th>Name</th>
            <th width="115px">Serial No</th>
            <th width="40px">Qty</th>
            <th width="60px">Amount</th>
        </tr>
    </thead>

    <tbody>
        <?php
        $totalQty = 0;
        $totalAmount = 0;
        $spareAllProducts = \App\ServiceSpareAllocation::with('product')
                ->where('service_allocation_id', $serviceDelivery->service_allocation_id)
                ->groupBy('product_id')
                ->get();
        ?>
        @foreach($spareAllProducts as $product)
        <?php
        $spareProducts = \App\ServiceSpareAllocation::select(
                'service_allocation_id', 'product_id'
                )
                ->where('service_allocation_id', $product->service_allocation_id)
                ->where('product_id', $product->product_id)
                ->get();
        
        $totalQty += count($spareProducts);
        ?>
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td align="center">{{ $product->product->name }} - {{ $product->product->model_no }}</td>
            <td align="center">
                @foreach($spareProducts as $serial)
                    {{ $product->serial_no }}
                @endforeach
            </td>
            <td align="center">{{ count($spareProducts) }}</td>
            <td align="right">
                @if ($serviceDelivery->service_payment_type == 'free')
                Free
                @else
                {{ $product->sale_price }}
                @endif
            </td>
        </tr>
        @endforeach

        <tr>
            <td colspan="3">Total:</td>
            <td align="center">{{ $totalQty }}</td>
            <td align="right">
                @if ($serviceDelivery->service_payment_type == 'free')
                Free
                @else
                {{ $serviceDelivery->service_amount }}
                @endif
            </td>
        </tr>

    </tbody>
</table>

<div id="pad-bottom"></div>

<table id="invoice-footer">
    <tbody>
        <tr>
            <th>Total Amount : </th>
            <td align="right">
                @if ($serviceDelivery->service_payment_type == 'free')
                Free
                @else
                {{ $serviceDelivery->service_amount }}
                @endif
            </td>
        </tr>

        <tr>
            <th>In Words : </th>
            <?php
            // $inWords = \App\HelperClass::numberToWords($productIssue->total_amount);
            if ($serviceDelivery->service_payment_type == 'free') {
                $inWords = 'Free';
            } else {
                $inWords = \App\HelperClass::numberToWords($serviceDelivery->service_amount) . ' Taka Only.';
            }
            ?>
            <td>{{ @$inWords }} Taka Only.</td>
        </tr>
    </tbody>
</table>

<div style="padding-bottom: 60px;"></div>

<table id="invoice-table">
    <tr>
        <td>
            <span>
                <h3 class="overline">Received By</h3>
            </span>
        </td>
        <td align="right">
            <span>
                <span>
                    <h3 class="overline">Authorized By</h3>
                </span>
            </span>
        </td>
    </tr>
</table>

<div class="row">
    <div class="col-md-12 text-right">
        <?php
        date_default_timezone_set("Asia/Dhaka");
        ?>
        <p>Print Date & Time : <?php echo date("d-m-Y h:i:sa"); ?></p>
    </div>
</div>
@endsection

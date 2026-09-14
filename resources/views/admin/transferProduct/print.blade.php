@extends('admin.layouts.masterPrint')

@section('custome-css')
    <style type="text/css">
        #lifting-return-info {
            font-family: Times, "Times New Roman", serif;
            width: 100%;
            border-collapse: collapse;
            border-style: dotted;
        }

        #lifting-return-info td {
            padding: 5px;
            border-bottom: 1px solid #ddd;
        }

    </style>
@endsection

@section('content')
    <table id="report-header">
        <tr>
            <td align="left">Transfer Invoice #{{ $transfer->transfer_no }}</td>
            <td align="right">Date: {{ date('d-m-Y', strtotime($transfer->date)) }}</td>
        </tr>
    </table>

    <div id="pad-bottom"></div>

    <table id="lifting-return-info">
        <tbody>
            <tr>
                <td width="90px"><b>Issue No.</b></td>
                <td width="5px">:</td>
                <td>{{ $transfer->transfer_no }}</td>
                <td width="60px"><b></b></td>
                <td width="5px"></td>
                <td></td>
            </tr>

            <tr>
                <td width="60px"><b>From</b></td>
                <td width="5px">:</td>
                <td>{{ $host->hostName }}</td>
                <td width="60px"><b>To</b></td>
                <td width="15px">:</td>
                <td>{{ $destination->destinationName }}</td>
            </tr>
            <tr>
                <td width="90px"><b>Send By</b></td>
                <td width="15px">:</td>
                <td>{{ @$transfer->sendBy->name }}</td>
                <td width="100px"><b>Received By</b></td>
                <td width="15px">:</td>
                <td>{{ @$transferProducts[0]->receivedBy->name }}</td>
            </tr>
        </tbody>
    </table>

    <div id="pad-bottom"></div>

    
    @if($transfer->product_type == 'warranty_product' || $transfer->product_type == 'spare_parts')
    <table id="report-table">
        <thead class="thead-light">
            <tr>
                <th width="20px">Sl</th>
                <th width="100px">Product Name & Code</th>
                <th width="200px">Model</th>
                <th>Serial No</th>
                <th width="30px">Qty</th>
                <th width="80px">Price</th>
            </tr>
        </thead>

        <tbody>
            @php
                $sl = 1;
                $totalQty = 0;
                $totalAmount = 0;
            @endphp
            @foreach ($transferProducts as $transferProduct)
                @php
                    $products = \App\TransferProduct::with('lifting')
                        ->where('transfer_id', $transferProduct->transfer_id)
                        ->where('product_id', $transferProduct->product_id)
                        ->get();
                @endphp
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ @$transferProduct->product->name }}</td>
                    <td>{{ @$transferProduct->product->model_no }}</td>
                    <td>
                        <?php $amount = 0; ?>
                        @foreach ($products as $product)
                            @php
                                $totalQty += $product->lifting->qty;
                                $totalAmount += $product->lifting->price;
                                $amount += $product->lifting->price;
                            @endphp
                            {{ $product->serial_no }},
                        @endforeach
                    </td>
                    <td style="text-align: right;">{{ count($products) }}</td>
                    <td style="text-align: right;">{{ number_format($amount, 2, '.', '') }}</td>
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <th colspan="4">Total Quantity</th>
                <th style="text-align: right;"><b>{{ $totalQty }}</b></th>
                <th style="text-align: right;"><b>{{ number_format($totalAmount, 2, '.', '') }}</b></th>
            </tr>
        </tfoot>
    </table>
    @elseif($transfer->product_type == 'consumer_product' || $transfer->product_type == 'raw_product')
    <table id="report-table">
        <thead>
            <tr>
                <th width="20px">Sl</th>
                <th width="100px">Product Name & Code</th>
                <th width="200px">Model</th>
                <th width="30px">Qty</th>
            </tr>
        </thead>

        <tbody>
            @php
                $sl = 1;
                $totalQty = 0;
            @endphp
            @foreach ($transferProducts as $transferProduct)
            @php
                $totalQty += $transferProduct->qty;
            @endphp
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ $transferProduct->product->name }}</td>
                    <td>{{ $transferProduct->product->model_no }}</td>
                    <td align="center">{{ $transferProduct->qty }}</td>
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <th colspan="3">Total Quantity</th>
                <th style="text-align: center;"><b>{{ $totalQty }}</b></th>
            </tr>
        </tfoot>
    </table>
    @endif
    
    
    
    <div class="row">
        <div class="col-md-12 text-right">
            <?php 
                date_default_timezone_set("Asia/Dhaka");
            ?>
            <p>Print Date & Time : <?php echo  date("d-m-Y h:i:sa");?></p>
        </div>
    </div>
@endsection

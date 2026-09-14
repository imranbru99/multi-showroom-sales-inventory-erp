@extends('admin.layouts.masterPrint')

@section('content')
    <table id="report-header">
        <tr>
            <td>Transfer Receive History On {{ $fromDate }} To {{ $toDate }}</td>
        </tr>
    </table>

    <div id="pad-bottom"></div>

    <table id="report-table">
        <thead class="thead-light">
            <tr>
                <th width="20px">Sl</th>
                <th>Date</th>
                <th>Host</th>
                <th>Destination</th>
                <th>Transfer No</th>
                <th>Product Info</th>
            </tr>
        </thead>

        <tbody>
            <?php
                $grandTotalQty = 0;
                $grandTotalAmount = 0;
            ?>
            @foreach ($transferReports as $transferReport)
                <tr>
                    <td width="5%">{{ $loop->iteration }}</td>
                    <td width="15%">{{ date('d-m-Y', strtotime($transferReport->date)) }}</td>
                    <td width="20%">{{ @$transferReport->host->name }}</td>
                    <td width="20%">{{ @$transferReport->destination->name }}</td>
                    <td width="20%">{{ @$transferReport->transfer_no }}</td>
                    <?php
                    $transfers = \App\TransferProduct::with('product')
                        ->where('transfer_id', $transferReport->id)
                        ->whereNotNull('approve_by')
                        ->groupBy('product_id')
                        ->get();
                    ?>
                    <td width="40%">
                        <table id="dataTable" class="table table-bordered table-sm">
                            <thead>
                                <tr class="bg-success">
                                    <th width="80px">Name</th>
                                    <th width="150px">Model</th>
                                    <th width="200px">Serial No</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $totalQty = 0;
                                $totalAmount = 0;
                                ?>
                                @foreach ($transfers as $transfer)
                                    <?php
                                    $products = \App\TransferProduct::with('lifting')
                                        ->where('transfer_id', $transfer->transfer_id)
                                        ->where('product_id', $transfer->product_id)
                                        ->get();
                                    ?>
                                    <tr>
                                        <td width="10%">{{ @$transfer->product->name }}</td>
                                        <td width="30%">{{ @$transfer->product->model_no }}</td>
                                        <td width="30%">
                                            <?php $amount = 0; ?>
                                            @foreach ($products as $product)
                                                @php
                                                    $totalQty += $product->qty;
                                                    $totalAmount += $product->price;
                                                @endphp
                                                {{ $product->serial_no }},
                                            @endforeach
                                        </td>
                                        <td width="10%">{{ $products->sum('qty') }}</td>
                                        <td align="right" width="20%">{{ number_format($products->sum('price'), 2, '.', '') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="background-color: #4caf50; color: white">
                                    <td colspan="3" style="color: white"><b>Total</b></td>
                                    <td align="right" style="color: white"><b>{{ $totalQty }}</b></td>
                                    <td align="right" style="color: white"><b>{{ number_format($totalAmount, 2, '.', '') }}</b></td>
                                </tr>
                            </tfoot>
                        </table>
                    </td>
                </tr>
                <?php
                $grandTotalQty += $totalQty;
                $grandTotalAmount += $totalAmount;
            ?>
            @endforeach
        </tbody>
    </table>

    <div id="pad-bottom"></div>

    <table id="report-table">
        <tfoot>
            <tr>
                <th style="text-align: right;"><b>Total Qty : </b></th>
                <td style="text-align: right;">{{ $grandTotalQty }}</td>
            </tr>
            <tr>
                <th style="text-align: right;"><b>Total Amount : </b></th>
                <td style="text-align: right;">{{ number_format($grandTotalAmount, 2, '.', '') }}</td>
            </tr>
        </tfoot>
    </table>
@endsection

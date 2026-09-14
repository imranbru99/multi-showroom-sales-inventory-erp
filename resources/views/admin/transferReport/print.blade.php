@extends('admin.layouts.masterPrint')

@section('content')
    <table id="report-header">
        <tr>
            <td>Transfer History On {{ $fromDate }} To {{ $toDate }}</td>
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
            ?>
            @foreach ($transferReports as $transferReport)
                <tr>
                    <td width="5%">{{ $loop->iteration }}</td>
                    <td width="7%">{{ date('d-m-Y', strtotime($transferReport->date)) }}</td>
                    <td width="15%">{{ @$transferReport->host->name }}</td>
                    <td width="15%">{{ @$transferReport->destination->name }}</td>
                    <td width="7%">{{ @$transferReport->transfer_no }}</td>
                    @if($transferReport->product_type == 'warranty_product' || $transferReport->product_type == 'spare_parts')
                    <?php
                    $transfers = \App\TransferProduct::with('product')
                        ->where('transfer_id', $transferReport->id)
                        ->whereNotNull('approve_by')
                        ->groupBy('product_id')
                        ->get();
                    ?>
                    <td width="51%">
                        <table id="dataTable" class="table table-bordered table-sm">
                            <thead>
                                <tr class="bg-success">
                                    <th width="80px">Name</th>
                                    <th width="150px">Model</th>
                                    <th width="200px">Serial No</th>
                                    <th>Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $totalQty = 0;
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
                                                @endphp
                                                {{ $product->serial_no }},
                                            @endforeach
                                        </td>
                                        <td align='center' width="10%">{{ $products->sum('qty') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="background-color: #4caf50; color: white">
                                    <td colspan="3" style="color: white"><b>Total</b></td>
                                    <td align="center" style="color: white"><b>{{ $totalQty }}</b></td>
                                </tr>
                            </tfoot>
                        </table>
                    </td>
                    @else
                    <?php
                    $transfers = \App\TransferProduct::with('product')
                        ->where('transfer_id', $transferReport->id)
                        ->whereNotNull('approve_by')
                        ->get();
                    ?>
                    <td width="51%">
                        <table id="dataTable" style="width: 100%">
                            <thead>
                                <tr class="bg-success">
                                    <th width="40%">Name</th>
                                    <th width="53%">Model</th>
                                    <th width="8%">Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $totalQty = 0;
                                ?>
                                @foreach ($transfers as $transfer)
                                <?php
                                $totalQty += @$transfer->qty;
                                ?>
                                    <tr>
                                        <td width="40%">{{ @$transfer->product->name }}</td>
                                        <td width="53%">{{ @$transfer->product->model_no }}</td>
                                        <td align='center' width="8%">{{ @$transfer->qty }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="background-color: #4caf50; color: white">
                                    <td colspan="2" style="color: white"><b>Total</b></td>
                                    <td align="center" style="color: white"><b>{{ $totalQty }}</b></td>
                                </tr>
                            </tfoot>
                        </table>
                    </td>
                    @endif
                </tr>
                <?php
                $grandTotalQty += $totalQty;
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
        </tfoot>
    </table>
@endsection

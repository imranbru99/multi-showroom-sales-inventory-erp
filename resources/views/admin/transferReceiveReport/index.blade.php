@extends('admin.layouts.masterReport')

@section('search_card_body')
    <input type="hidden" name="print" value="print">
    <div class="row">
        
        <div class="col-md-4">
            <label for="host">Host</label>
            <div class="form-group">
                <select class="form-control chosen-select host" id="host" name="host">
                    <option value="">Select Host</option>
                    @foreach ($storeAndShowrooms as $storeAndShowroom)
                        @php
                            if ($storeAndShowroom->id == $hostId) {
                                $select = 'selected';
                            } else {
                                $select = '';
                            }
                        @endphp
                        <option value="{{ $storeAndShowroom->id }}" {{ $select }}>{{ $storeAndShowroom->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-4">
            <label for="destination">Destination</label>
            <div class="form-group" id="destination-select-menu">
                <select class="form-control chosen-select destination" id="destination" name="destination">
                    <option value="">Select Destination</option>
                    @foreach ($destinations as $destination)
                        @php
                            if ($destination->id == $destinationId) {
                                $select = 'selected';
                            } else {
                                $select = '';
                            }
                        @endphp
                        <option value="{{ $destination->id }}" {{ $select }}>{{ $destination->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="col-md-2 form-group">
            <label for="from-date">From Date</label>
            <input type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'from_date' }}"
                name="fromDate" placeholder="Select Date From" value="{{ date('d-m-Y', strtotime($fromDate)) }}" readonly>
        </div>
        <div class="col-md-2 form-group">
            <label for="to-date">To Date</label>
            <input type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'to_date' }}" name="toDate"
                placeholder="Select Date To" value="{{ date('d-m-Y', strtotime($toDate)) }}" readonly>
        </div>
    </div>
@endsection

@section('print_card_header')
    <input type="hidden" name="fromDate" value="{{ $fromDate }}">
    <input type="hidden" name="toDate" value="{{ $toDate }}">

    <input type="hidden" name="hostId" value="{{ $hostId }}">
    <input type="hidden" name="destinationId" value="{{ $destinationId }}">
    <input type="hidden" id="print_value" name="print" value="{{ $print }}">
@endsection

@section('print_card_body')
    <table id="dataTable" class="table table-bordered table-sm">
        <thead>
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
            @php
                $sl = 1;
            @endphp

            @foreach ($transferReports as $transferReport)
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ date('d-m-Y', strtotime($transferReport->date)) }}</td>
                    <td>{{ @$transferReport->host->name }}</td>
                    <td>{{ @$transferReport->destination->name }}</td>
                    <td>{{ @$transferReport->transfer_no }}</td>
                    <?php
                    $transfers = \App\TransferProduct::with('product')
                        ->where('transfer_id', $transferReport->id)
                        ->whereNotNull('approve_by')
                        ->groupBy('product_id')
                        ->get();
                    ?>
                    <td>
                        <table id="dataTable" class="table table-bordered table-sm">
                            <thead>
                                <tr class="bg-success">
                                    <th class="font-weight-bold">Name</th>
                                    <th class="font-weight-bold">Model</th>
                                    <th class="font-weight-bold">Serial No</th>
                                    <th class="font-weight-bold">Qty</th>
                                    <th class="font-weight-bold">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $totalQty = 0;
                                $totalAmount = 0;
                                ?>
                                @foreach ($transfers as $transfer)
                                    <?php
                                    $products = \App\TransferProduct::where('transfer_id', $transfer->transfer_id)
                                        ->where('product_id', $transfer->product_id)
                                        ->get();
                                    ?>
                                    <tr>
                                        <td>{{ @$transfer->product->name }}</td>
                                        <td>{{ @$transfer->product->model_no }}</td>
                                        <td>
                                            <?php $amount = 0; ?>
                                            @foreach ($products as $product)
                                                @php
                                                    $totalQty += $product->qty;
                                                    $totalAmount += $product->price;
                                                    $amount += $product->price;
                                                @endphp
                                                {{ $product->serial_no }},
                                            @endforeach
                                        </td>
                                        <td>{{ count($products) }}</td>
                                        <td>{{ $amount }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-info text-white font-weight-bold">
                                    <td colspan="3">Total</td>
                                    <td>{{ $totalQty }}</td>
                                    <td>{{ $totalAmount }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

@extends('admin.layouts.masterReport')

@section('search_card_body')
    <div class="row">
        <div class="col-md-12">
            <input type="hidden" name="print" value="print">
        </div>
    </div>


    <div class="row">
        <div class="col-md-4">
            <label for="search-type">Type</label>
            <div class="form-group">
                @php
                    $allSerachType = ['receive' => 'Receive', 'running_allocation' => 'Running Allocation', 'stock' => 'In Stock', 'delivery' => 'Delivery', 'damage' => 'Damage'];
                @endphp
                <select class="form-control chosen-select" id="type" name="type">

                    @foreach ($allSerachType as $key => $value)
                        <option value="{{ $key }}" @if ($type == $key) selected @endif>{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4 form-group">
            <label for="from-date">From Date</label>
            <input type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'from_date' }}"
                name="fromDate" value="{{ date('d-m-Y', strtotime($fromDate)) ?? '' }}" placeholder="Select Date From">
        </div>
        <div class="col-md-4 form-group">
            <label for="to-date">To Date</label>
            <input type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'to_date' }}" name="toDate"
                value="{{ date('d-m-Y', strtotime($toDate)) ?? '' }}" placeholder="Select Date To">
        </div>
    </div>

@endsection

@section('print_card_header')
    <input type="hidden" name="fromDate" value="{{ $fromDate }}">
    <input type="hidden" name="toDate" value="{{ $toDate }}">

    <input type="hidden" id="type" name="type" value="{{ $type }}">
    <input type="hidden" id="print_value" name="print" value="{{ $print }}">
@endsection



@section('print_card_body')
    <div class="card-body">
        <div class="table-responsive">
            @if ($type == 'receive')
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="20px">SL</th>
                            <th>Receive Date</th>
                            <th>Invoice No</th>
                            <th>Product Name</th>
                            <th>Product Model</th>
                            <th>Product Serial</th>
                            <th>Dealer</th>
                            <th>Problem List</th>
                            <th>Product Condition</th>
                            <th>Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($serviceProductReceives as $serviceProductReceive)
                            <tr class="row_{{ $serviceProductReceive->id }}">
                                <td></td>
                                <td>{{ date('d-m-Y', strtotime($serviceProductReceive->receive_date)) }}</td>
                                <td>{{ $serviceProductReceive->invoice_no }}</td>
                                <td>{{ $serviceProductReceive->product->name }}</td>
                                <td>{{ $serviceProductReceive->product_model }}</td>
                                <td>{{ $serviceProductReceive->product_serial }}</td>
                                <td>{{ @$serviceProductReceive->dealer->name }}
                                    ({{ @$serviceProductReceive->dealer->code }})</td>
                                <td>{{ $serviceProductReceive->problem_list }}</td>
                                <td>{{ $serviceProductReceive->product_condition }}</td>
                                <td>{{ $serviceProductReceive->qty }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            @if ($type == 'running_allocation')
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="20px">SL</th>
                            <th>Issue Date</th>
                            <th>Job No</th>
                            <th>Product Name</th>
                            <th>Product Model</th>
                            <th>Product Serial</th>
                            <th>Customer Name</th>
                            <th>Engineer</th>
                            <th>Problem List</th>
                            <th>Product Condition</th>
                            <th>Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($serviceRunningAllocation as $running)
                            <tr class="row_{{ $running->id }}">
                                <td></td>
                                <td>{{ date('d-m-Y', strtotime($running->issue_date)) }}</td>
                                <td>{{ $running->invoice_no }}</td>
                                <td>{{ $running->product->name }}</td>
                                <td>{{ $running->product_model }}</td>
                                <td>{{ $running->product_serial }}</td>
                                <td>{{ @$running->serviceProduct->dealer->name }}
                                    ({{ @$running->serviceProduct->dealer->code }})</td>
                                <td>{{ $running->staff->name }} ({{ $initial->staff->code }})</td>
                                <td>{{ $running->serviceProduct->problem_list }}</td>
                                <td>{{ $running->serviceProduct->product_condition }}</td>
                                <td>1</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            @if ($type == 'stock')
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="20px">SL</th>
                            <th>Issue Date</th>
                            <th>Finish Date</th>
                            <th>Job No</th>
                            <th>Product Name</th>
                            <th>Product Model</th>
                            <th>Product Serial</th>
                            <th>Dealer</th>
                            <th>Engineer</th>
                            <th>Problem List</th>
                            <th>Product Condition</th>
                            <th>Qty</th>
                            <th>Spare Products</th>
                        </tr>
                    </thead>
                    <tbody id="">
                        @foreach ($inStock as $stock)

                            <tr class="row_{{ $stock->id }}">
                                <td></td>
                                <td>{{ date('d-m-Y', strtotime($stock->issue_date)) }}</td>
                                <td>{{ date('d-m-Y', strtotime($stock->finish_date)) }}</td>
                                <td>{{ $stock->invoice_no }}</td>
                                <td>{{ $stock->product->name }}</td>
                                <td>{{ $stock->product_model }}</td>
                                <td>{{ $stock->product_serial }}</td>
                                <td>{{ @$stock->serviceProduct->dealer->name }}
                                    ({{ @$stock->serviceProduct->dealer->code }})</td>
                                <td>{{ @$stock->staff->name }} ({{ @$stock->staff->code }})</td>
                                <td>{{ @$stock->serviceProduct->problem_list }}</td>
                                <td>{{ @$stock->serviceProduct->product_condition }}</td>
                                <td>1</td>
                                <td>
                                    <table id="dataTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr class="bg-success text-white">
                                                <th>Product Name</th>
                                                <th>Model No</th>
                                                <th>Serial No</th>
                                                <th>Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($stock->spareProducts as $spareProduct)
                                                <tr>
                                                    <td>{{ @$spareProduct->product->name }}</td>
                                                    <td>{{ $spareProduct->model_no }}</td>
                                                    <td>{{ $spareProduct->serial_no }}</td>
                                                    <td>1</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="bg-cyan text-white font-weight-bold">
                                                <td colspan="3">Total Qty</td>
                                                <td>{{ count($stock->spareProducts) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            @if ($type == 'delivery')
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="20px">SL</th>
                            <th>Delivery Issue To</th>
                            <th>Delivery Date</th>
                            <th width="100px">Product Receive Inv No</th>
                            <th>Job No</th>
                            <th>Product Name</th>
                            <th>Product Model</th>
                            <th>Product Serial</th>
                            <th>Dealer</th>
                            <th>Delivery Condition</th>
                            <th>Service Amount</th>
                        </tr>
                    </thead>
                    <tbody id="">
                        @foreach ($serviceDeliveries as $serviceDelivery)
                            <tr class="row_{{ $serviceDelivery->id }}">
                                <td></td>
                                <td>{{ date('d-m-Y', strtotime($serviceDelivery->delivery_issue_to)) }}</td>
                                <td>{{ date('d-m-Y', strtotime($serviceDelivery->delivery_date)) }}</td>
                                <td>{{ $serviceDelivery->serviceAllocation->serviceProduct->invoice_no }}</td>
                                <td>{{ $serviceDelivery->invoice_no }}</td>
                                <td>{{ @$serviceDelivery->serviceAllocation->product->name }}</td>
                                <td>{{ @$serviceDelivery->serviceAllocation->product_model }}</td>
                                <td>{{ @$serviceDelivery->serviceAllocation->product_serial }}</td>
                                <td>{{ @$serviceDelivery->serviceAllocation->serviceProduct->dealer->name }}
                                    ({{ @$serviceDelivery->serviceAllocation->serviceProduct->customer->code }})</td>
                                <td>{{ $serviceDelivery->delivery_condition }}</td>
                                <td>{{ $serviceDelivery->service_amount }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @if ($type == 'damage')
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="20px">SL</th>
                            <th>Receive Date</th>
                            <th>Invoice No</th>
                            <th>Product Name</th>
                            <th>Product Model</th>
                            <th>Product Serial</th>
                            <th>Dealer</th>
                            <th>Problem List</th>
                            <th>Product Condition</th>
                            <th>Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($serviceDamages as $serviceDamage)
                            <tr class="row_{{ $serviceDamage->id }}">
                                <td></td>
                                <td>{{ date('d-m-Y', strtotime($serviceDamage->receive_date)) }}</td>
                                <td>{{ $serviceDamage->invoice_no }}</td>
                                <td>{{ $serviceDamage->product->name }}</td>
                                <td>{{ $serviceDamage->product_model }}</td>
                                <td>{{ $serviceDamage->product_serial }}</td>
                                <td>{{ @$serviceDamage->dealer->name }}
                                    ({{ @$serviceDamage->dealer->code }})</td>
                                <td>{{ $serviceDamage->problem_list }}</td>
                                <td>{{ $serviceDamage->product_condition }}</td>
                                <td>{{ $serviceDamage->qty }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection

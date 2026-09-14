@extends('admin.layouts.masterPrint')
@php
error_reporting(0);
@endphp
@section('content')
    <table id="report-header">
        <tr>
            <td>{{ $title }} From {{ date('d-m-Y', strtotime($fromDate)) }} To
                {{ date('d-m-Y', strtotime($toDate)) }}</td>
        </tr>
    </table>

    <div id="pad-bottom"></div>

    @if ($type == 'receive')
        <table id="report-table">
            <thead class="thead-light">
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
                <?php
                $totalQty = 0;
                ?>
                @foreach ($serviceProductReceives as $serviceProductReceive)
                    <?php
                    $totalQty += $serviceProductReceive->qty;
                    ?>
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

        <div id="pad-bottom"></div>

        <table id="report-table">
            <tfoot>
                <tr>
                    <th style="text-align: right;"><b>Total Qty : </b></th>
                    <td style="text-align: right;">{{ $totalQty }}</td>
                </tr>
            </tfoot>
        </table>

    @endif
    @if ($type == 'running_allocation')
        <table id="report-table">
            <thead class="thead-light">
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
                <?php
                $totalQty += 0;
                ?>
                @foreach ($serviceRunningAllocation as $running)
                    <?php
                    $totalQty += 1;
                    ?>
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

        <div id="pad-bottom"></div>

        <table id="report-table">
            <tfoot>
                <tr>
                    <th style="text-align: right;"><b>Total Qty : </b></th>
                    <td style="text-align: right;">{{ $totalQty }}</td>
                </tr>
            </tfoot>
        </table>

    @endif
    @if ($type == 'stock')
        <table id="report-table">
            <thead class="thead-light">
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
                <?php
                $totalQty = 0;
                ?>
                @foreach ($inStock as $stock)
                    <?php
                    $totalQty += 1;
                    ?>
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

        <div id="pad-bottom"></div>

        <table id="report-table">
            <tfoot>
                <tr>
                    <th style="text-align: right;"><b>Total Qty : </b></th>
                    <td style="text-align: right;">{{ $totalQty }}</td>
                </tr>
            </tfoot>
        </table>

    @endif
    @if ($type == 'delivery')
        <table id="report-table">
            <thead class="thead-light">
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
                <?php
                $totalQty = 0;
                $totalAmount = 0;
                ?>
                @foreach ($serviceDeliveries as $serviceDelivery)
                    <?php
                    $totalQty += 1;
                    $totalAmount += $serviceDelivery->service_amount;
                    ?>

                    <tr class="row_{{ $serviceDelivery->id }}">
                        <td>{{ $loop->iteration }}</td>
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

        <div id="pad-bottom"></div>

        <table id="report-table">
            <tfoot>
                <tr>
                    <th style="text-align: right;"><b>Total Qty : </b></th>
                    <td style="text-align: right;">{{ $totalQty }}</td>
                </tr>

                <tr>
                    <th style="text-align: right;"><b>Total Amount : </b></th>
                    <td style="text-align: right;">{{ $totalAmount }}</td>
                </tr>
            </tfoot>
        </table>

    @endif

    @if ($type == 'damage')
        <table id="report-table">
            <thead class="thead-light">
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
                <?php
                $totalQty = 0;
                ?>
                @foreach ($serviceDamages as $serviceDamage)
                    <?php
                    $totalQty += $serviceDamage->qty;
                    ?>
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

        <div id="pad-bottom"></div>

        <table id="report-table">
            <tfoot>
                <tr>
                    <th style="text-align: right;"><b>Total Qty : </b></th>
                    <td style="text-align: right;">{{ $totalQty }}</td>
                </tr>
            </tfoot>
        </table>

    @endif

    <div class="row">
        <div class="col-md-12 text-right">
            <?php
            date_default_timezone_set('Asia/Dhaka');
            ?>
            <p>Print Date & Time : <?php echo date('d-m-Y h:i:sa'); ?></p>
        </div>
    </div>


@endsection

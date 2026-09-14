 @extends('admin.layouts.masterPrint')

@section('content')
    <table id="report-header">
        <tr>
            <td>{{ $title }}</td>
        </tr>
    </table>

    <div id="pad-bottom"></div>

    <table id="report-table">
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
        <tbody>
            @php
                $sl = 1;
                $currentCategoryId = 0;
                $totalQty = 0;
                $totalPurchaseValue = 0;
                $totalSaleValue = 0;
            @endphp
            @foreach ($completes as $complete)

            <tr class="row_{{ $complete->id }}">
                <td></td>
                <td>{{ date('d-m-Y', strtotime($complete->issue_date)) }}</td>
                <td>{{ date('d-m-Y', strtotime($complete->finish_date)) }}</td>
                <td>{{ $complete->invoice_no }}</td>
                <td>{{ $complete->product->name }}</td>
                <td>{{ $complete->product_model }}</td>
                <td>{{ $complete->product_serial }}</td>
                <td>{{ @$complete->serviceProduct->dealer->name }} ({{ @$complete->serviceProduct->dealer->code }})</td>
                <td>{{ @$complete->staff->name }} ({{ @$complete->staff->code }})</td>
                <td>{{ @$complete->serviceProduct->problem_list }}</td>
                <td>{{ @$complete->serviceProduct->product_condition }}</td>
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
                            @foreach($complete->spareProducts as $spareProduct)
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
                                <td>{{ count($complete->spareProducts) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="row">
        <div class="col-md-12 text-right">
            <?php 
                date_default_timezone_set("Asia/Dhaka");
            ?>
            <p>Print Date & Time : <?php echo  date("d-m-Y h:i:sa");?></p>
        </div>
    </div>
@endsection


@extends('admin.layouts.masterPrint')
<?php
use App\ProductIssueList;
?>

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

        #invoice-total {
            background-color: green;
            width: 100%;
        }

        #invoice-total th {
            border: 0px solid white;
            padding: 5px;
            text-align: right;
            font-size: 14px;
            width: 180px;
            color: white;
        }

        #invoice-total td {
            border: 1px solid white;
            padding: 5px;
            color: white;
            font-weight: bold;
        }

    </style>
@endsection

@section('content')
    <table id="invoice-header">
        <tr>
            <td align="left">Gate Pass #{{ $gatePass->invoice_no }}</td>
            <td align="right">Date: {{ date('d-m-Y', strtotime($gatePass->date)) }}</td>
        </tr>
    </table>

    <div id="pad-bottom"></div>


    <div id="pad-bottom"></div>

    <table id="report-table">
        <thead>
            <tr>
                <th width="20px">SL#</th>
                <th width="20px">Invoice No</th>
                <th width="80px">Name</th>
                <th width="110px">Model</th>
                <th width="30px">Qty</th>
                <th width="200px">Serial No</th>
            </tr>
        </thead>

        <tbody>

            <?php $netTotal = 0; ?>
            @foreach ($productIssues as $issue)
                @php
                    $sl = 1;
                    $totalQty = 0;
                @endphp


                <?php
                $productIssueLists = ProductIssueList::select('tbl_product_issue_lists.*', 'tbl_product_issue.issue_no', 'tbl_products.name as productName')
                    ->leftJoin('tbl_product_issue', 'tbl_product_issue.id', '=', 'tbl_product_issue_lists.issue_id')
                    ->leftJoin('tbl_products', 'tbl_products.id', '=', 'tbl_product_issue_lists.product_id')
                    ->where('tbl_product_issue_lists.issue_id', $issue->id)
                    ->get();
                
                $groupByProductIds = $productIssueLists->groupBy('product_id');
                ?>

                @foreach ($groupByProductIds as $key => $groupByProduct)
                    <tr>
                        <td>{{ $sl++ }}</td>
                        <td>{{ $groupByProduct[0]->issue_no }}</td>
                        <td>{{ $groupByProduct[0]->productName }}</td>
                        <td>{{ $groupByProduct[0]->model_no }}</td>
                        <td align="center">{{ $groupByProduct->sum('qty') }}</td>
                        <td>
                            @foreach ($groupByProduct as $uniqueProduct)
                                {{ $uniqueProduct->serial_no }},
                            @endforeach
                        </td>
                    </tr>

                    @php
                        $totalQty += $groupByProduct->sum('qty');
                    @endphp
                @endforeach
                @php
                    $netTotal += $totalQty;
                @endphp
            @endforeach
        </tbody>
    </table>


    <div id="pad-bottom"></div>

    <table id="report-table">
        <thead>
            <tr>
                <th>Grand Total - {{ $netTotal }}</th>
            </tr>
        </thead>
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
            date_default_timezone_set('Asia/Dhaka');
            ?>
            <p>Print Date & Time : <?php echo date('d-m-Y h:i:sa'); ?></p>
        </div>
    </div>
@endsection

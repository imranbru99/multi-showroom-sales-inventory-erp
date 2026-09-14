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

        #chalan-table {
            width: 100%;
            border-collapse: collapse;
        }

        #chalan-table td {
            width: 50%;
            border: 0px solid black;
        }

        #chalan-header {
            background-color: lightgray;
            width: 100%;
            padding: 5px;
            text-align: center;
            font-weight: bold;
            font-size: 20px;
        }

        #chalan-footer {
            background-color: lightgray;
            width: 100%;
        }

        #chalan-footer th {
            border: 0px solid white;
            padding: 5px;
            text-align: right;
            font-size: 14px;
            width: 180px;
        }

        #chalan-footer td {
            border: 1px solid black;
            padding: 5px;
        }

    </style>
@endsection

@section('content')

@php
$chalanNo = str_replace('inv', 'chalan', $saleData->invoice_no);
@endphp
    <table id="chalan-header">
        <tr>
            <td>Challan</td>
        </tr>
    </table>

    <div id="pad-bottom"></div>

    <table width="100%">
        <tbody>
            <tr>
                <td width="105px"><b>Inv/MR. No</b></td>
                <td width="10px"><b>:</b></td>
                <td>#{{ @$chalanNo }}</td>

                <td align="right" width="105px"><b>Date</b></td>
                <td align="right" width="10px"><b>:</b></td>
                <td align="right">{{ date('d-m-Y', strtotime(@$saleData->sale_date)) }}</td>
            </tr>
            <tr>
                <td width="105px"><b>Customer Name</b></td>
                <td width="10px"><b>:</b></td>
                <td>{{ @$saleData->customer->name }}</td>

                <td align="right" width="105px"><b>Phone No</b></td>
                <td align="right" width="10px"><b>:</b></td>
                <td align="right">{{ @$saleData->customer->phone_no }}</td>

            </tr>
            <tr>
                <td width="105px"><b>Address</b></td>
                <td width="10px"><b>:</b></td>
                <td>{{ @$saleData->customer->present_address }}</td>

                <td align="right" width="105px"><b>Sales By</b></td>
                <td align="right" width="10px"><b>:</b></td>
                <td align="right">{{ @$saleData->saleBy->name }}</td>
            </tr>
        </tbody>
    </table>

    <div id="pad-bottom"></div>

    <table id="report-table">
        <thead>
            <tr>
                <th width="20px">SL#</th>
                <th>Name</th>
                <th width="80px">Model</th>
                <th width="80px">Serial No</th>
                <th width="40px">Qty</th>
            </tr>
        </thead>

        <tbody>
            @php
                $i = 1;
                $totalQty = 0;
            @endphp
            @foreach ($saleData->products as $p)
                
                @php
                    $totalQty += $p->qty;
                @endphp

                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ $p->product->name }}</td>
                    <td>{{ $p->product->model_no }}</td>
                    <td>{{ $p->product_serial }}</td>
                    <td align="right">{{ $p->qty }}</td>
                </tr>
            @endforeach
           
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4">Total Qty</th>
                <th>{{ $totalQty }}</th>
            </tr>
        </tfoot>
    </table>


    <div style="padding-bottom: 60px;"></div>

    <table id="chalan-table">
        <tr>
            <td>
                <span>
                    <h3 class="overline">Received By</h3>
                </span>
            </td>
            <td align="right">
                <span>
                    <span>
                        <h3 class="overline">Prepared By</h3>
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
            <p>Print Date & Time : <?php echo  date("d-m-Y h:i:sa");?></p>
        </div>
    </div>

@endsection



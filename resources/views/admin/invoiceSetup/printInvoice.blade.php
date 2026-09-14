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

    @php
    $chalanNo = str_replace('inv', 'chalan', $saleData->invoice_no);
    @endphp
    <table id="chalan-header">
        <tr>
            <td>{{ $title }}</td>
        </tr>
    </table>

    <div id="pad-bottom"></div>

    <table width="100%">
        <tbody>
            <tr>
                <td width="105px"><b>Inv/MR. No</b></td>
                <td width="10px"><b>:</b></td>
                <td>#{{ $chalanNo }}</td>

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
                <th style="text-align: left;">Product Name</th>
                <th width="80px">Model</th>
                <th width="80px">Serial No</th>
                <th width="40px">Qty</th>
                @if ($saleData->sale_type == 'Cash')
                    <th width="70px">Cash</th>
                @endif

                @if ($saleData->sale_type == 'Short Installment')
                    <th width="70px">Short.Install</th>
                @endif
                @if ($saleData->sale_type == 'Long Installment')
                    <th width="70px">Long.Install</th>
                @endif

                @if ($saleData->sale_type == 'Cash')
                    <th width="70px">Discount</th>
                    <th width="70px">Gift Voucher</th>
                    <th width="70px">Value</th>
                @endif
            </tr>
        </thead>

        <tbody>
            @php
                $i = 1;
                $totalPrice = 0;
                $totalDis = 0;
                $totalVou = 0;
            @endphp
            @foreach ($saleData->products as $p)
                @php
                    $price = 0;
                    
                    $cprice = $p->cash_price;
                    $sprice = $cprice + ($cprice * 8) / 100;
                    $lprice = $sprice + ($sprice * 12) / 100;
                    
                    if ($saleData->sale_type == 'Cash') {
                        $price = $p->cash_price;
                    }
                    
                    if ($saleData->sale_type == 'Short Installment') {
                        $cprice = $p->cash_price;
                        $price = $cprice + ($cprice * 8) / 100;
                    
                        // $price = $p->mrp_price;
                    }
                    
                    if ($saleData->sale_type == 'Long Installment') {
                        $cprice = $p->cash_price;
                        $sprice = $cprice + ($cprice * 8) / 100;
                        $price = $sprice + ($sprice * 12) / 100;
                    
                        // $price = $p->hire_price;
                    }
                    
                    // $price = $price - ($p->discount + $p->gift_voucher);
                    
                    $totalDis += $p->discount;
                    $totalVou += $p->gift_voucher;
                    
                    $totalPrice += $price;
                    
                @endphp
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ $p->product->name }}</td>
                    <td>{{ $p->product->model_no }}</td>
                    <td>{{ $p->product_serial }}</td>
                    <td align="right">{{ $p->qty }}</td>

                    @if ($saleData->sale_type == 'Cash')
                        <td align="right">
                            {{ $p->cash_price }}
                        </td>
                    @endif

                    @if ($saleData->sale_type == 'Short Installment')
                        <td align="right">
                            {{ round(abs($sprice)) }}
                        </td>
                    @endif
                    @if ($saleData->sale_type == 'Long Installment')
                        <td align="right">
                            {{ round(abs($lprice)) }}
                        </td>
                    @endif

                    @if ($saleData->sale_type == 'Cash')
                        <td align="right">{{ $p->discount }}</td>
                        <td align="right">{{ $p->gift_voucher }}</td>
                        <td align="right">
                            {{ $price }}
                        </td>
                    @endif
                </tr>
            @endforeach
            {{-- <tr>
                <td>1</td>
                <td>{{ $invoice->productName }}</td>
                <td>{{ $invoice->customer_product_model }}</td>
                <td>{{ $invoice->product_serial_no }}</td>
                <td align="right">{{ $invoice->qty }}</td>
                <td align="right">{{ $invoice->customer_product_price }}</td>
                <td align="right">{{ $invoice->qty * $invoice->customer_product_price }}</td>
            </tr> --}}
        </tbody>
    </table>


    {{-- <div style="padding-bottom: 60px;"></div> --}}


    <div id="pad-bottom"></div>

    <table id="invoice-footer">
        <tbody>
            <tr>
                <th>Total Price Amount : </th>
                <td align="right">{{ round(abs($totalPrice)) }}</td>
            </tr>
            <tr>
                <th>Down Payment : </th>
                <td align="right">{{ $saleData->deposite }}</td>
            </tr>
            <tr>
                <th>Gift Voucher : </th>
                <td align="right">{{ $totalVou }}</td>
            </tr>
            <tr>
                <th>Discount : </th>
                <td align="right">{{ $totalDis }}</td>
            </tr>
            <tr>
                <th>Balance : </th>
                @php
                    $balance = round(abs($totalPrice - $saleData->deposite - $totalVou - $totalDis));
                @endphp
                <td align="right">{{ $balance }}</td>
            </tr>
            <tr>
                <th>In Words : </th>
                <td align="right">{!! \App\HelperClass::numberToWords($balance) !!} Taka Only.</td>
            </tr>
        </tbody>
    </table>
    <div class="row">
        <div class="col-md-12 text-right">
            <?php date_default_timezone_set('Asia/Dhaka'); ?>
            <p>Print Date & Time : <?php echo date('d-m-Y h:i:sa'); ?></p>
        </div>
    </div>

@endsection

@extends('admin.layouts.masterPrint')

@section('content')
    <style type="text/css">
        #report-table td {
            font-size: 8px;
        }

    </style>
    <table id="report-header">
        {{-- <tr> --}}
        @if ($btnPrintSummary == 'Print Summary')
            <tr>
                <td>
                    Sales Summary On {{ date('d-m-Y', strtotime($fromDate)) }} To {{ date('d-m-Y', strtotime($toDate)) }}

                    @if ($employee)
                        Sale By {{ $employee->name }}
                    @endif
                
                </td>
            </tr>
        @endif

        @if ($btnPrintHistory == 'Print History')
            <tr>
                <td>Sales History On {{ date('d-m-Y', strtotime($fromDate)) }} To {{ date('d-m-Y', strtotime($toDate)) }} 
                
                    @if ($employee)
                        Sale By {{ $employee->name }}
                    @endif
                
                </td>
            </tr>
        @endif

        @if ($btnPrintGroupSalesHistory == 'Print Group Sales History')
            <tr>
                <td>Group Wise Sales History On {{ date('d-m-Y', strtotime($fromDate)) }} To {{ date('d-m-Y', strtotime($toDate)) }}</td>
            </tr>
        @endif
        {{-- </tr> --}}
    </table>

    <div id="pad-bottom"></div>

    @if ($btnPrintSummary == 'Print Summary')
        <table id="report-table">
            <thead class="thead-light">
                <tr>
                    <th width="20px">Sl</th>
                    <th>Account No</th>
                    <th>Client</th>
                    <th>Phone</th>
                    <th width="80px" style="text-align: center;">Qty</th>
                    <th width="110px">Total Amount</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $sl = 1;
                    $totalQty = 0;
                    $totalPrice = 0;
                @endphp
                @foreach ($salesSummary as $summary)
                    @php
                        $totalQty += $summary['qty'];
                        $totalPrice += $summary['total_amount'];
                    @endphp
                    <tr>
                        <td>{{ $summary['sl'] }}</td>
                        <td>{{ $summary['account_no'] }}</td>
                        <td>{{ $summary['customer_name'] }}</td>
                        <td>{{ $summary['customer_phone'] }}</td>
                        <td>{{ $summary['qty'] }}</td>
                        <td>{{ $summary['total_amount'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if ($btnPrintHistory == 'Print History')
        <table id="report-table">
            <thead class="thead-light">
                <tr>
                    <th width="20px">Sl</th>
                    <th>Date</th>
                    <th>A/C No</th>
                    <th>Client</th>
                    <th>Phone</th>
                    <th>Memo No</th>
                    <th>Product</th>
                    <th>Model</th>
                    <th>Serial</th>
                    <th>Sales By</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Discount</th>
                    <th>Gift Voucher</th>
                    <th>Exchange CRT</th>
                    <th>Total Value</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $sl = 1;
                    $totalQty = 0;
                    $totalPrice = 0;
                @endphp
                @foreach ($salesHistory as $salesRecord)
                    @php
                        $totalQty += $salesRecord['product_qty'];
                        $totalPrice += $salesRecord['total_product_price'];
                    @endphp
                    <tr>
                        <td>{{ $salesRecord['sl'] }}</td>
                        <td>{{ $salesRecord['date'] }}</td>
                        <td>{{ $salesRecord['account_no'] }}</td>
                        <td>{{ $salesRecord['customer_name'] }}</td>
                        <td>{{ $salesRecord['customer_phone'] }}</td>
                        <td>{{ $salesRecord['memo_no'] }}</td>
                        <td>{{ $salesRecord['product_name'] }}</td>
                        <td>{{ $salesRecord['product_model'] }}</td>
                        <td>{{ $salesRecord['product_serial'] }}</td>
                        <td>{{ $salesRecord['seller'] }}</td>
                        <td>{{ $salesRecord['product_qty'] }}</td>
                        <td>{{ $salesRecord['product_price'] }}</td>
                        <td>{{ $salesRecord['product_discount'] }}</td>
                        <td>{{ $salesRecord['product_gift_voucher'] }}</td>
                        <td>{{ $salesRecord['product_exchange_crt'] }}</td>
                        <td>{{ $salesRecord['total_product_price'] }}</td>
                    </tr>
                @endforeach


            </tbody>
        </table>
    @endif

    @if ($btnPrintGroupSalesHistory == 'Print Group Sales History')
        <table id="report-table">
            <thead class="thead-light">
                <tr>
                    <th width="20px">Sl</th>
                    <th>Date</th>
                    <th>Group</th>
                    <th>Client</th>
                    <th>Phone</th>
                    <th>Showroom</th>
                    <th>Category</th>
                    <th>Product</th>
                    <th>Serial</th>
                    <th>Model</th>
                    <th>Color</th>
                    <th>Price</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $sl = 1;
                    $totalQty = 0;
                    $totalPrice = 0;
                @endphp
                @foreach ($groupSalesHistory as $salesRecord)
                    @php
                        $purchaseDate = date('d-m-Y', strtotime($salesRecord->purchaseDate));
                        $totalPrice = $totalPrice + $salesRecord->productCashPrice;
                    @endphp
                    <tr>
                        <td>{{ $sl++ }}</td>
                        <td>{{ $purchaseDate }}</td>
                        <td>{{ @$salesRecord->groupName }}</td>
                        <td>{{ $salesRecord->customerName }}</td>
                        <td>{{ $salesRecord->customerPhoneNo }}</td>
                        <td>{{ $salesRecord->showroomName }}</td>
                        <td>{{ $salesRecord->categoryName }}</td>
                        <td>{{ $salesRecord->productName }}</td>
                        <td>{{ $salesRecord->productSerialNo }}</td>
                        <td>{{ $salesRecord->productModelNo }}</td>
                        <td>{{ $salesRecord->productColor }}</td>
                        <td align="right">{{ $salesRecord->productCashPrice }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div id="pad-bottom"></div>
    @if ($btnPrintSummary == 'Print Summary')
        <table id="report-table">
            <tfoot>
                <tr>
                    <th style="text-align: right;"><b>Total Qty : </b></th>
                    <td style="text-align: right;">{{ @$totalQty }}</td>
                </tr>

                <tr>
                    <th style="text-align: right;"><b>Total Amount : </b></th>
                    <td style="text-align: right;">{{ $totalPrice }}</td>
                </tr>
            </tfoot>
        </table>
    @endif

    @if ($btnPrintHistory == 'Print History')
        <table id="report-table">
            <tfoot>
                <tr>
                    <th style="text-align: right;"><b>Total Qty : </b></th>
                    <td style="text-align: right;">{{ $totalQty }}</td>
                </tr>
                <tr>
                    <th style="text-align: right;"><b>Total Sale Amount : </b></th>
                    <td style="text-align: right;">{{ $totalPrice }}</td>
                </tr>
            </tfoot>
        </table>
    @endif

    @if ($btnPrintGroupSalesHistory == 'Print Group Sales History')
        <table id="report-table">
            <tfoot>
                <tr>
                    <th style="text-align: right;"><b>Total Price : </b></th>
                    <td style="text-align: right;">{{ $totalPrice }}</td>
                </tr>
            </tfoot>
        </table>
    @endif
@endsection

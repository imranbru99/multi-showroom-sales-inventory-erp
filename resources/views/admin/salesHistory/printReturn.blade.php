@extends('admin.layouts.masterPrint')

@section('content')
    <style type="text/css">
        #report-table td {
            font-size: 8px;
        }

    </style>
    <table id="report-header">

        <tr>
            <td>Sales History On {{ date('d-m-Y', strtotime($fromDate)) }} To {{ date('d-m-Y', strtotime($toDate)) }}
            </td>
        </tr>

    </table>

    <div id="pad-bottom"></div>

    <table id="report-table">
        <thead class="thead-light">
            <tr>
                <th width="20px">Sl</th>
                <th width="80px">Date</th>
                <th>Account No</th>
                <th>Customer</th>
                <th>Phone</th>
                <th>Memo No</th>
                <th>Product</th>
                <th>Model</th>
                <th>Serial</th>
                <th>Qty</th>
                <th>Price</th>
            </tr>
        </thead>

        <tbody>
            @php
                $sl = 1;
                $totalQty = 0;
                $totalPrice = 0;
            @endphp
           @foreach ($salesHistory as $salesRecord)
           @foreach ($salesRecord->products as $product)
           @php
                $totalQty += 1;
                $totalPrice += $product->cash_price;
           @endphp
               <tr>
                   <td></td>
                   <td>{{ date('d-m-Y', strtotime($salesRecord->date)) }}</td>
                   <td>{{ $salesRecord->customer->code }}</td>
                   <td>{{ $salesRecord->customer->name }}</td>
                   <td>{{ $salesRecord->customer->phone_no }}</td>
                   <td>{{ $salesRecord->invoice_no }}</td>
                   <td>{{ $product->product->name }}</td>
                   <td>{{ $product->product->model_no }}</td>
                   <td>{{ $product->product_serial }}</td>
                   <td>1</td>
                   <td>{{ $product->cash_price }}</td>
               </tr>
           @endforeach
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
                <th style="text-align: right;"><b>Total Sale Amount : </b></th>
                <td style="text-align: right;">{{ $totalPrice }}</td>
            </tr>
        </tfoot>
    </table>

@endsection

<?php

use App\Installment;
use Illuminate\Support\Carbon;
use App\CustomerRegistrationSetup;
use App\InstallmentCollectionList;
?>

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
            <th width="20px">Sl</th>
            <th>A/C No.</th>
            <th>Account Name</th>
            <th>Mobile#</th>
            <th>Invoice Date</th>
            <th>Invoice#</th>
            <th>Invoice Amount</th>
            <th>Collection</th>
            <th>Due Amount</th>
            <th>Duration</th>
            <th>Over Date</th>
            <th>Over Month</th>
        </tr>
    </thead>

    <tbody>
        @php
        $i = 1;
        $totalSale = 0;
        $totalCollection = 0;
        @endphp

        @foreach ($retailSales as $retailSale)

        @php
        $saleAmount = 0;

        foreach ($retailSale->products as $product) {
        $qty = $product->qty;
        $price = null;

        if ($retailSale->sale_type == 'cash') {
        $price = $product->cash_price;
        }
        if ($retailSale->sale_type == 'Short Installment') {
        $price = $product->mrp_price;
        }
        if ($retailSale->sale_type == 'Long Installment') {
        $price = $product->hire_price;
        }

        $salePrice = $qty * $price;

        $salePrice = $salePrice - $product->discount - $product->exchange_crt - $product->mobile_gift;

        $saleAmount += $salePrice;

        }

        if (!$retailSale->installment) {
        continue;
        }

        $collectionAmount = 0;

        if ($retailSale->customer) {
        $customer = CustomerRegistrationSetup::findOrFail($retailSale->customer->id);

        $collectionAmount = $customer->totalCollectionAmount();
        }

        $invoiceDate = Carbon::parse($retailSale->sale_date);
        $invoiceDatePlusOneYear = Carbon::parse($retailSale->sale_date)->addDays(365);

        // Date Diff
        $todayDate = Carbon::now();

        $duration = $todayDate->diffInDays($invoiceDate);
        $overDate = $todayDate->diffInDays($invoiceDatePlusOneYear);
        $overMonths = $todayDate->diffInMonths($invoiceDatePlusOneYear);

        $totalSale += $saleAmount;
        $totalCollection += $collectionAmount;

        @endphp

        <tr>

            <td>{{ $i++ }}</td>
            <td>{{ @$retailSale->customer->code }}</td>
            <td>{{ @$retailSale->customer->name }}</td>
            <td>{{ @$retailSale->customer->phone_no }}</td>
            <td>{{ date('d-m-Y', strtotime($retailSale->sale_date)) }}</td>
            <td>{{ $retailSale->invoice_no }}</td>
            <td>{{ $saleAmount }}</td>
            <td>{{ $collectionAmount }}</td>
            <td>{{ $saleAmount - $collectionAmount }}</td>
            <td>{{ $duration }}</td>
            <td>
                @if ($invoiceDatePlusOneYear->lt($todayDate))
                {{ $overDate }}
                @endif
            </td>
            <td>
                @if ($invoiceDatePlusOneYear->lt($todayDate))
                {{ $overMonths }}
                @endif
            </td>

        </tr>


        @endforeach

    </tbody>
    <tfoot>
        <tr>
            <th colspan="6">Total</th>
            <th>{{ $totalSale }}</th>
            <th>{{ $totalCollection }}</th>
            <th>{{ $totalSale - $totalCollection }}</th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
    </tfoot>
</table>
@endsection
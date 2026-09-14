<?php

use App\Installment;
use Illuminate\Support\Carbon;
use App\InstallmentCollectionList;
?>

@extends('admin.layouts.masterPrint')

@section('content')
    <table id="report-header">
        <tr>
            <td>
                Over Due (30 Days) Report
                @if ($staff)
                    - {{ $staff->name }}
                @endif
            </td>
        </tr>
    </table>

    <div id="pad-bottom"></div>

    <table id="report-table">
        <thead>
            <tr>
                <th width="20px">Sl</th>
                <th>Invoice Date</th>
                <th>A/C No.</th>
                <th>Account Name</th>
                <th>Mobile#</th>
                <th>Address</th>
                <th>Sales By</th>
                <th>Invoice#</th>
                <th>Invoice Amount</th>
                <th>Collection</th>
                <th>Due Amount</th>
                <th>Duration</th>
                <th>Over Date</th>
            </tr>
        </thead>

        <tbody>
            @php
                $i = 1;
                $total_sales = 0;
                $total_collections = 0;
                $total_due = 0;
            @endphp

            @foreach ($retailSales as $retailSale)

                @php
                    $invoices = \App\RetailSale::where('customer_id', $retailSale->customer_id)->count();
                    
                    $saleAmount = @$retailSale->products->sum('sales_price');
                    
                    if (!$retailSale->installment) {
                        continue;
                    }
                    
                    $collectionAmount = 0;
                    foreach ($retailSale->collection as $values) {
                        $collectionAmount += @$values->collections->sum('installment_schedule_amount') / $invoices;
                    }
                    
                    $invoiceDate = Carbon::parse($retailSale->sale_date);
                    $invoiceDatePlusOneYear = Carbon::parse($retailSale->sale_date)->addDays(30);
                    
                    // Date Diff
                    $todayDate = Carbon::now();
                    
                    $duration = $todayDate->diffInDays($invoiceDate);
                    $overDate = $todayDate->diffInDays($invoiceDatePlusOneYear);
                    
                    $due_amount = $saleAmount - $collectionAmount;
                    
                    if ($due_amount < 0 || $due_amount == 0) {
                        continue;
                    }
                    
                    $total_sales += $saleAmount;
                    $total_collections += $collectionAmount;
                    $total_due += $saleAmount - $collectionAmount;
                    
                @endphp

                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ date('d-m-Y', strtotime($retailSale->sale_date)) }}</td>
                    <td>{{ @$retailSale->customer->code }}</td>
                    <td>{{ @$retailSale->customer->name }}</td>
                    <td>{{ @$retailSale->customer->phone_no }}</td>
                    <td>{{ @$retailSale->customer->present_address }}</td>
                    <td>{{ @$retailSale->seller->name }}</td>
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
                </tr>
            @endforeach

        </tbody>

        <tfoot>
            <tr>
                <th colspan="8" class="text-right">Total</th>
                <th>{{ round($total_sales) }}</th>
                <th>{{ round($total_collections) }}</th>
                <th>{{ round($total_due) }}</th>
                <th colspan="2"></th>
            </tr>
        </tfoot>
    </table>

    <div class="row">
        <div class="col-md-12 text-right">
            <?php date_default_timezone_set('Asia/Dhaka'); ?>
            <p>Print Date & Time : <?php echo date('d-m-Y h:i:sa'); ?></p>
        </div>
    </div>
@endsection

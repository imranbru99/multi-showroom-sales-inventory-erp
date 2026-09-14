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
                Runnig Account
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
                <th>Last Collection Date</th>
                <th>A/C No.</th>
                <th>Account Name</th>
                <th>Mobile#</th>
                <th>Address</th>
                <th>Sales By</th>
                <th>Invoice#</th>
                <th>Invoice Amount</th>
                <th>Collection</th>
                <th>Due Amount</th>
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
                    
                    $due_amount = $saleAmount - $collectionAmount;
                    
                    if ($due_amount <= 0 || @$retailSale->customer->is_close == 1) {
                        continue;
                    }
                    
                    $startDate = date('Y-m-d', strtotime(now()->subDays(90)));
                    $endDate = date('Y-m-d');
                    
                    $customer = \App\InstallmentCollectionList::with('collection')
                        ->whereHas('collection', function ($q) use ($retailSale) {
                            $q->where('customer_id', $retailSale->customer_id);
                        })
                        ->whereBetween('installment_collection_date', [$startDate, $endDate])
                        ->latest('installment_collection_date')
                        ->first();
                    
                    if ($collectionAmount == 0 && !$customer) {
                        $lastdate = 'Waiting';
                    } elseif ($collectionAmount > 0 && !$customer) {
                        continue;
                    } else {
                        $lastdate = date('d-m-Y', strtotime($customer->installment_collection_date));
                    }
                    
                    $total_sales += $saleAmount;
                    $total_collections += $collectionAmount;
                    $total_due += $saleAmount - $collectionAmount;
                    
                @endphp

                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ date('d-m-Y', strtotime($retailSale->sale_date)) }}</td>
                    <td>{{ $lastdate }}</td>
                    <td>{{ @$retailSale->customer->code }}</td>
                    <td>{{ @$retailSale->customer->name }}</td>
                    <td>{{ @$retailSale->customer->phone_no }}</td>
                    <td>{{ @$retailSale->customer->present_address }}</td>
                    <td>{{ @$retailSale->seller->name }}</td>
                    <td>{{ $retailSale->invoice_no }}</td>
                    <td align="right">{{ round($saleAmount) }}</td>
                    <td align="right">{{ round($collectionAmount) }}</td>
                    <td align="right">{{ round($due_amount) }}</td>
                </tr>
            @endforeach

        </tbody>

        <tfoot>
            <tr>
                <th colspan="9" class="text-right">Total</th>
                <th>{{ round($total_sales) }}</th>
                <th>{{ round($total_collections) }}</th>
                <th>{{ round($total_due) }}</th>
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

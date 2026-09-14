<?php

use App\Installment;
use Illuminate\Support\Carbon;
use App\InstallmentCollectionList;
?>

@extends('admin.layouts.masterPrint')

@section('content')
    <table id="report-header">
        <tr>
            <td>Over Due Report</td>
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
            </tr>
        </thead>

        <tbody>
            <?php $sl = 1; ?>
            @foreach ($overDues as $overDue)

                <?php
                $collection_amount = 0;
                foreach ($overDue->collection as $collection) {
                    $collect = 0;
                    foreach ($collection->collections as $indcollection) {
                        $collect += floatval($indcollection->installment_schedule_amount);
                    }
                    $collection_amount += $collect;
                }
                
                $due_amount = $overDue->products->sum('sales_price') - $collection_amount;
                
                if ($due_amount <= 0) {
                    continue;
                }
                
                ?>
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ date('d-m-Y', strtotime($overDue->sale_date)) }}</td>
                    <td>{{ @$overDue->customer->code }}</td>
                    <td>{{ @$overDue->customer->name }}</td>
                    <td>{{ @$overDue->customer->mobile_no }}</td>
                    <td>{{ @$overDue->customer->present_address }}</td>
                    <td>{{ @$overDue->seller->name }}</td>
                    <td>{{ $overDue->invoice_no }}</td>
                    <td>{{ $overDue->products->sum('sales_price') }}</td>
                    <td>{{ $collection_amount }}</td>
                    <td>{{ $due_amount }}</td>
                </tr>
            @endforeach

        </tbody>
    </table>

    <div class="row">
        <div class="col-md-12 text-right">
            <?php date_default_timezone_set('Asia/Dhaka'); ?>
            <p>Print Date & Time : <?php echo date('d-m-Y h:i:sa'); ?></p>
        </div>
    </div>
@endsection

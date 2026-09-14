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
                {{ $title }}
                @if ($days)
                    ({{ $days }} Days)
                @endif
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
                <th>Schedule Amount</th>
                <th>Collection Amount</th>
                <th>Due Amount</th>
                <th>Last Schedule Duration</th>
                {{-- <th>Duration</th>
                <th>Day Over</th> --}}
            </tr>
        </thead>

        <tbody>

            @php
                $i = 1;
                $startDate = date('Y-m-d');
                $duration = date('Y-m-d', strtotime(now()->subDays($days)));
                $total_sales = 0;
                $total_schedule_amount = 0;
                $total_collectionAmount = 0;
                $total_over_due = 0;
            @endphp

            @foreach ($retailSales as $retailSale)
                @php
                    $total_sales += $retailSale['sale_amount'];
                    $total_schedule_amount += $retailSale['schedule_amount'];
                    $total_collectionAmount += $retailSale['collection_amount'];
                    $total_over_due += $retailSale['over_due'];
                @endphp

                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ date('d-m-Y', strtotime($retailSale['sale_date'])) }}</td>
                    <td>{{ $retailSale['customer_code'] }}</td>
                    <td>{{ $retailSale['customer_name'] }}</td>
                    <td>{{ $retailSale['phone_no'] }}</td>
                    <td>{{ $retailSale['address'] }}</td>
                    <td>{{ $retailSale['seller'] }}</td>
                    <td>{{ $retailSale['invoice_no'] }}</td>
                    <td align="right">{{ $retailSale['sale_amount'] }}</td>
                    <td align="right">{{ $retailSale['schedule_amount'] }}</td>
                    <td align="right">{{ $retailSale['collection_amount'] }}</td>
                    <td align="right">{{ $retailSale['over_due'] }}</td>
                    <td align="center">{{ $retailSale['last_date_duration'] }}</td>
                    {{-- <td>{{ $duration }}</td>
                    <td>{{ $over_due_days }}</td> --}}
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="8">Total</th>
                <th align="right">{{ $total_sales }}</th>
                <th align="right">{{ $total_schedule_amount }}</th>
                <th align="right">{{ $total_collectionAmount }}</th>
                <th align="right">{{ $total_over_due }}</th>
                <th></th>
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

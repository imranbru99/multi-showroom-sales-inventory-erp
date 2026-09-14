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
                Close Account Report
                @if ($staff)
                    - {{ $staff->name }}
                @endif
                @if ($staff)
                    - ({{ $type }})
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
                <th>Last Collec. Date</th>
                <th>Late Days</th>
                <th>A/C No.</th>
                <th>Account Name</th>
                <th>Mobile</th>
                <th>Address</th>
                <th>Sales By</th>
                <th>Invoice</th>
                <th>Inv. Amnt.</th>
                <th>Inter. Rec.</th>
                <th>Late Fee</th>
                <th>Mis. Amnt.</th>
                <th>Collec.</th>
                <th>Close Dis.</th>
                <th>Bal.</th>
                <th>Closed By</th>
            </tr>
        </thead>

        <tbody>
            @php
                $i = 1;
                $total_sales = 0;
                $total_collections = 0;
                $total_interest_recv = 0;
                $total_late_fee = 0;
                $total_missing = 0;
                $total_close_disc = 0;
                $total_balance = 0;
            @endphp

            @foreach ($closes as $close)

                @php
                    $total_sales += $close['sales_amount'];
                    $total_collections += $close['collection_amount'];
                    $total_interest_recv += @$close['interest_receive'];
                    $total_late_fee += @$close['late_fee'];
                    $total_missing += @$close['missing_amount'];
                    $total_close_disc += @$close['discount_amount'];
                    $total_balance += $close['due_amount'];
                @endphp

                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ date('d-m-Y', strtotime(@$close['sale_date'])) }}</td>
                    <td>{{ date('d-m-Y', strtotime(@$close['last_col_date'])) }}</td>
                    <td>{{ $close['late_days'] }}</td>
                    <td>{{ @$close['customer_code'] }}</td>
                    <td>{{ @$close['customer_name'] }}</td>
                    <td>{{ @$close['phone_no'] }}</td>
                    <td>{{ @$close['address'] }}</td>
                    <td>{{ @$close['sales_by'] }}</td>
                    <td>{{ $close['invoice_no'] }}</td>
                    <td align="right">{{ $close['sales_amount'] }}</td>
                    <td align="right">{{ @$close['interest_receive'] }}</td>
                    <td align="right">{{ $close['late_fee'] }}</td>
                    <td align="right">{{ @$close['missing_amount'] }}</td>
                    <td align="right">{{ $close['collection_amount'] }}</td>
                    <td align="right">{{ @$close['discount_amount'] }}</td>
                    <td align="right">{{ $close['due_amount'] }}</td>
                    <td>{{ @$close['closed_by'] }}</td>
                </tr>
            @endforeach

        </tbody>
        <tfoot>
            <tr>
                <th colspan="10" class="text-right">Total</th>
                <th>{{ round($total_sales) }}</th>
                <th>{{ round($total_interest_recv) }}</th>
                <th>{{ round($total_late_fee) }}</th>
                <th>{{ round($total_missing) }}</th>
                <th>{{ round($total_collections) }}</th>
                <th>{{ round($total_close_disc) }}</th>
                <th>{{ round($total_balance) }}</th>
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

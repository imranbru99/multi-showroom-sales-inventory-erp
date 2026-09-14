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
                Missing Amount Report
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

            @foreach ($missings as $missing)

                @php
                    $total_sales += $missing['sales_amount'];
                    $total_collections += $missing['collection_amount'];
                    $total_interest_recv += @$missing['interest_receive'];
                    $total_late_fee += @$missing['late_fee'];
                    $total_missing += @$missing['missing_amount'];
                    $total_close_disc += @$missing['discount_amount'];
                    $total_balance += $missing['due_amount'];
                @endphp

                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ date('d-m-Y', strtotime(@$missing['sale_date'])) }}</td>
                    <td>{{ date('d-m-Y', strtotime(@$missing['last_col_date'])) }}</td>
                    <td>{{ $missing['late_days'] }}</td>
                    <td>{{ @$missing['customer_code'] }}</td>
                    <td>{{ @$missing['customer_name'] }}</td>
                    <td>{{ @$missing['phone_no'] }}</td>
                    <td>{{ @$missing['address'] }}</td>
                    <td>{{ @$missing['sales_by'] }}</td>
                    <td>{{ $missing['invoice_no'] }}</td>
                    <td align="right">{{ $missing['sales_amount'] }}</td>
                    <td align="right">{{ @$missing['interest_receive'] }}</td>
                    <td align="right">{{ $missing['late_fee'] }}</td>
                    <td align="right">{{ @$missing['missing_amount'] }}</td>
                    <td align="right">{{ $missing['collection_amount'] }}</td>
                    <td align="right">{{ @$missing['discount_amount'] }}</td>
                    <td align="right">{{ $missing['due_amount'] }}</td>
                    <td>{{ @$missing['closed_by'] }}</td>
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

@php

use Illuminate\Support\Carbon;
@endphp
@extends('admin.layouts.masterPrint')

@section('content')
    <table id="report-header">
        <tr>
            <td>
                Calssify Accounts
                @if ($sales_by)
                    Of {{ $staff->name }}
                @endif
            </td>
        </tr>
    </table>

    <div id="pad-bottom"></div>

    <table id="report-table">
        <thead>
            <tr>
                <th>SL</th>
                <th>Last Collection Date</th>
                <th>Account No</th>
                <th>Name</th>
                <th>Contact No</th>
                <th>Address</th>
                <th>Collector By</th>
                <th>Outstanding</th>
                <th>Duration</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total_accounts = 0; 
            $sl =1;
            ?>
            @foreach ($customers as $customer)

            @php
            if($customer->outstanding < 1){ 
                continue; 
            } 
            @endphp 

            <?php
            if ($customer->duration < 91) {
                continue;
            }
            $total_accounts += 1;
            ?>
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ date('d-m-Y', strtotime($customer->date)) }}</td>
                    <td>{{ @$customer->customerCode }}</td>
                    <td>{{ @$customer->customerName }}</td>
                    <td>{{ @$customer->customerPhone }}</td>
                    <td>{{ @$customer->customerAddress }}</td>
                    <td>{{ @$customer->staffName }}</td>
                    <td>{{ $customer->outstanding }}</td>
                    <td>{{ $customer->duration }}</td>
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

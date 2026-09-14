@php

use Illuminate\Support\Carbon;
@endphp
@extends('admin.layouts.masterPrint')

@section('content')
    <table id="report-header">
        <tr>
            <td>
                Unpaid Customers From {{ date('d-m-Y', strtotime($fromDate)) }} To
                {{ date('d-m-Y', strtotime($toDate)) }}
                @if ($sales_by)
                    Collected By {{ $staff->name }}
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
                <th>Duration</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($customers as $customer)
                <?php
                $collectionDate = Carbon::parse($customer->installment_collection_date);
                $todayDate = Carbon::parse($toDate);
                
                $duration = $todayDate->diffInDays($collectionDate);
                ?>


                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ date('d-m-Y', strtotime($customer->installment_collection_date)) }}</td>
                    <td>{{ $customer->code }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->phone_no }}</td>
                    <td>{{ $customer->present_address }}</td>
                    <td>{{ $customer->collector_by }}</td>
                    <td>{{ $customer->duration }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" align="right"><b>Total Customers</b></td>
                <td align="right"><b>{{ count($customers) }}</b></td>
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

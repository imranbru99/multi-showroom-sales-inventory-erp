<?php

use App\Installment;
use Illuminate\Support\Carbon;
use App\InstallmentCollectionList;
?>

@extends('admin.layouts.masterPrint')

@section('content')
    <table id="report-header">
        <tr>
            <td> {{ $title }} </td>
        </tr>
    </table>

    <div id="pad-bottom"></div>

    <table id="report-table">
        <thead>
            <tr>
                <th width="20px">Sl</th>
                <th>Dealer Name</th>
                <th>Code</th>
                <th>Region</th>
                <th>Area</th>
                <th>Type</th>
                <th>Mobile</th>
                <th>Address</th>
                <th>Documents</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($dealers as $dealer)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $dealer->name }}</td>
                    <td>{{ $dealer->code }}</td>
                    <td>{{ @$dealer->region->name }}</td>
                    <td>{{ @$dealer->area->name }}</td>
                    <td>{{ $dealer->type }}</td>
                    <td>{{ $dealer->mobile }}</td>
                    <td>{{ $dealer->address }}</td>
                    <td>
                        @if(!empty($dealer->documents) && count($dealer->documents) > 0)
                        @foreach($dealer->documents as $document)
                        {{ $document->title }},
                        @endforeach
                        @endif
                    </td>
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

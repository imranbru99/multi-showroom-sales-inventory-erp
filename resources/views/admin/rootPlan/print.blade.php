@extends('admin.layouts.masterPrint')
<?php
use App\ProductIssueList;
?>
@section('custome-css')
<style>
    #report-table td,
    #report-table th {
        border: 1px solid #ddd;
    }

    #report-table tbody td {
        vertical-align: top;
    }

    #chalan-table {
        width: 100%;
        border-collapse: collapse;
    }

    #chalan-table td {
        width: 50%;
        border: 0px solid black;
    }

    #chalan-header {
        background-color: lightgray;
        width: 100%;
        padding: 5px;
        text-align: center;
        font-weight: bold;
        font-size: 20px;
    }

    #chalan-footer {
        background-color: lightgray;
        width: 100%;
    }

    #chalan-footer th {
        border: 0px solid white;
        padding: 5px;
        text-align: right;
        font-size: 14px;
        width: 180px;
    }

    #chalan-footer td {
        border: 1px solid black;
        padding: 5px;
    }

    #chalan-total {
        background-color: green;
        width: 100%;
    }

    #chalan-total th {
        border: 0px solid white;
        padding: 5px;
        text-align: right;
        font-size: 14px;
        width: 180px;
        color: white;
    }

    #chalan-total td {
        padding: 5px;
        color: white;
        font-weight: bold;
    }


    


    #root-table {
        width: 100%;
    }

</style>
@endsection

@section('content')
<table id="chalan-header">
    <tr>
        <td>{{ $title }} No #{{ $rootPlan->root_plan_no }} Of Date {{ date('d-m-Y', strtotime($rootPlan->date)) }}</td>
    </tr>
</table>

<div id="pad-bottom"></div>

<table id="root-table">
    <thead>
        <tr>
            <td width="150px"><b>Date</b></td>
            <td width="10px"><b>:</b></td>
            <td>{{ date('d-m-Y', strtotime($rootPlan->date)) }}</td>

            <td align="right" width="150px"><b>Vehicle No</b></td>
            <td align="right" width="10px"><b>:</b></td>
            <td align="right" width="100px">{{ $rootPlan->vehicle->registration_no }}</td>
        </tr>
        <tr>
            <td width="150px"><b>Vehicle Capacity</b></td>
            <td width="10px"><b>:</b></td>
            <td>{{ $rootPlan->vehicle_capacity }}</td>

            <td align="right" width="150px"><b>Product Capacity</b></td>
            <td align="right" width="10px"><b>:</b></td>
            <td align="right" width="100px">{{ @$rootPlan->product_capacity }}</td>

        </tr>
        <tr>
            <td width="150px"><b>Driver Name</b></td>
            <td width="10px"><b>:</b></td>
            <td>{{ @$rootPlan->driver_name }}</td>

            <td align="right" width="150px"><b>Contact No</b></td>
            <td align="right" width="10px"><b>:</b></td>
            <td align="right" width="100px">{{ @$rootPlan->contact_no }}</td>
        </tr>
        <tr>
            <td width="150px" colspan="6"><b>Roote:</b></td>
        </tr>
        @foreach($roads as $road)
        <tr>
            <td colspan="6">{{$loop->iteration}}. {{ $road->name }} > {{ @$road->area->name }} > {{ @$road->area->region->name }}</td>
        </tr>
        @endforeach
    </thead>
</table>
<div id="pad-bottom"></div>
<div id="pad-bottom"></div>
<div id="pad-bottom"></div>

<div id="pad-bottom"></div>

<table id="report-table">
    <thead>
        <tr>
            <th width="20px">SL#</th>
            <th>Dealer Name</th>
            <th width="120px">Phone No</th>
            <th>Address</th>
        </tr>
    </thead>

    <tbody>
        @foreach($rootPlanDealers as $rootPlanDealer)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ @$rootPlanDealer->dealer->name }}</td>
                <td align="center">{{ @$rootPlanDealer->dealer->mobile }}</td>
                <td>{{ @$rootPlanDealer->dealer->address }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div style="padding-bottom: 40px;"></div>


<div class="row">
    <div class="col-md-12 text-right">
        <?php 
                date_default_timezone_set("Asia/Dhaka");
            ?>
        <p>Print Date & Time : <?php echo  date("d-m-Y h:i:sa");?></p>
    </div>
</div>

@endsection

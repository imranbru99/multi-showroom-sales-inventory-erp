@extends('admin.layouts.masterPrint')
@php
error_reporting(0);
@endphp
@section('content')
<table id="report-header">
    <tr>
        <td align="center" colspan="2">
            <u>{{ $title }}</u>
        </td>
    </tr>
    <tr>
        <td align="left">
            Region : 
            @if($regionName)
            {{ $regionName->name }}
            @else
            All
            @endif
        </td>
        <td align="right">
            Territory : 
            @if($territoryName)
            {{ $territoryName->name }}
            @else
            All
            @endif
        </td>
    </tr>
    <tr>
        <td align="left">
            Area : 
            @if($areaName)
            {{ $areaName->name }}
            @else
            All
            @endif
        </td>
        <td align="right">
            Reference : 
            @if($staffName)
            {{ $staffName->name }}
            @else
            All
            @endif
        </td>
    </tr>
</table>

<div id="pad-bottom"></div>
<table id="report-table">
    <thead class="thead-light">
        <tr>
            <th width="20px">Sl</th>
            <th>Code</th>
            <th>Dealer Name</th>
            <th>Mobile</th>
            <th>Address</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($dealers as $dealer)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $dealer->code }}</td>
            <td>{{ $dealer->name }}</td>
            <td>{{ $dealer->mobile }}</td>
            <td>{{ $dealer->address }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div id="pad-bottom"></div>

<div class="row">
    <div class="col-md-12 text-right">
        <?php
        date_default_timezone_set("Asia/Dhaka");
        ?>
        <p>Print Date & Time : <?php echo date("d-m-Y h:i:sa"); ?></p>
    </div>
</div>


@endsection

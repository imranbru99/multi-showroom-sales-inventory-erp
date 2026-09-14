@extends('admin.layouts.masterPrint')
@php
error_reporting(0);
@endphp
@section('custome-css')
    <style type="text/css">
        #lifting-return-info{
            font-family: Times, "Times New Roman", serif;
            width: 100%;
            border-collapse: collapse;
            border-style: dotted;
        }

        #lifting-return-info td{
            padding: 5px;
            border-bottom: 1px solid #ddd;
        }
    </style>
@endsection

@php    
    $storeOrShowroom = DB::table('view_store_and_showroom')
        ->select('name as storeOrShowroomName')
        ->where('type',$liftingReturn->store_or_showroom_type)
        ->where('id',$liftingReturn->store_or_showroom_id)
        ->first();
@endphp

@section('content')
    <table id="report-header">
        <tr>
            <td>Lifting Return Vouchar</td>
        </tr>
    </table>

    <div id="pad-bottom"></div>

    <table id="lifting-return-info">
        <tbody>
            <tr>
                <td width="115px"><b>Vouchar No.</b></td>
                <td width="5px">:</td>
                <td>{{ $liftingReturn->serial_no }}</td>
                <td width="150px"><b>Supplier</b></td>
                <td width="5px">:</td>
                <td>{{ $liftingReturn->vendorName }}</td>
            </tr>

            <tr>
                <td width="115px"><b>Vouchar Date</b></td>
                <td width="15px">:</td>
                <td>{{ date('d-m-Y', strtotime($liftingReturn->date))  }}</td>
                <td width="150px"><b>Store / Showrooms</b></td>
                <td width="15px">:</td>
                <td>{{ $storeOrShowroom->storeOrShowroomName }}</td>
            </tr>
        </tbody>
    </table>

    <div id="pad-bottom"></div>

    <table  id="report-table">
        <thead class="thead-light">
            <tr>
                <th width="20px">Sl</th>
                <th width="80px">Product Name & Code</th>
                <th width="80px">Model</th>
                <th width="70px">Color</th>
                <th width="80px">Serial No</th>
                <th width="30px">Quanty</th>
            </tr>
        </thead>

        <tbody>
            @php
                $sl = 1;
                $totalQty = 0;
            @endphp
            @foreach ($liftingReturnProducts as $liftingReturnProduct)
                @php
                    $totalQty = $totalQty + $liftingReturnProduct->qty;                                       
                @endphp
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ $liftingReturnProduct->product_name }} ( {{ $liftingReturnProduct->productCode }} )</td>
                    <td>{{ $liftingReturnProduct->model_no }}</td>
                    <td>{{ $liftingReturnProduct->color }}</td>
                    <td>{{ $liftingReturnProduct->serial_no }}</td>
                    <td style="text-align: right;">{{ $liftingReturnProduct->qty }}</td>
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td colspan="5" style="text-align: right;"><b>Total Quantity</b></td>
                <td style="text-align: right;"><b>{{ $totalQty }}</b></td>
            </tr>
        </tfoot>
    </table>
@endsection

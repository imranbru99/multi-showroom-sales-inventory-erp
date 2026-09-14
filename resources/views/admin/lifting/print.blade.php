@extends('admin.layouts.masterPrint')
@php
error_reporting(0);
@endphp
@section('custome-css')
	<style type="text/css">
		#lifting-info{
			font-family: Times, "Times New Roman", serif;
			width: 100%;
			border-collapse: collapse;
			border-style: dotted;
		}

		#lifting-info td{
			padding: 5px;
			border-bottom: 1px solid #ddd;
		}
	</style>
@endsection

@php    
    $storeOrShowroom = DB::table('view_store_and_showroom')
        ->select('name as storeOrShowroomName')
        ->where('type',$lifting->store_or_showroom_type)
        ->where('id',$lifting->store_or_showroom_id)
        ->first();
@endphp

@section('content')
    <table id="report-header">
        <tr>
            <td>Lifting Vouchar</td>
        </tr>
    </table>

    <div id="pad-bottom"></div>

    <table id="lifting-info">
        <tbody>
        	<tr>
        		<td width="115px"><b>Vouchar No.</b></td>
        		<td width="5px">:</td>
        		<td>{{ $lifting->vaouchar_no }}</td>
                <td width="150px"><b>Supplier</b></td>
                <td width="5px">:</td>
                <td>{{ $lifting->vendorName }}</td>
        	</tr>

        	<tr>
                <td width="115px"><b>Vouchar Date</b></td>
                <td width="15px">:</td>
                <td>{{ date('d-m-Y', strtotime($lifting->vouchar_date)) }}</td>
                <td width="150px"><b>Company Name</b></td>
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
                <th>Product Name & Code</th>
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
            @foreach ($liftingProducts as $liftingProduct)
                @php
                    $totalQty = $totalQty + $liftingProduct->qty;                                       
                @endphp
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ $liftingProduct->productName }} ( {{ $liftingProduct->productCode }} )</td>
                    <td>{{ $liftingProduct->model_no }}</td>
                    <td>{{ $liftingProduct->color }}</td>
                    <td>{{ $liftingProduct->serial_no }}</td>
                    <td style="text-align: right;">{{ $liftingProduct->qty }}</td>
                </tr>


            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td colspan="5" align="right"><b>Total Quantity</b></td>
                
                <td style="text-align: right;"><b>{{ $totalQty }}</b></td>
            </tr>
        </tfoot>
    </table>
    <div class="row">
        <div class="col-md-12 text-right">
            <?php 
                date_default_timezone_set("Asia/Dhaka");
            ?>
            <p>Print Date & Time : <?php echo  date("d-m-Y h:i:sa");?></p>
        </div>
    </div>
@endsection

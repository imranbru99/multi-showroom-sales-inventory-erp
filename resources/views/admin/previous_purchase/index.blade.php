@extends('admin.layouts.masterReport')

<style>
    .card-body{
        padding: 0 !important;
    }
</style>

@section('search_card_body')
    <div class="row">
        <div class="col-md-12">
            <input type="hidden" name="print" value="print">
        </div>
    </div> 


    <div class="row">
        <div class="col-md-6 form-group">
            <label for="from-date">From Date</label>
            <input  type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'from_date' }}" name="fromDate" value="{{$fromDate ?? ''}}" placeholder="Select Date From">
        </div>
        <div class="col-md-6 form-group">
            <label for="to-date">To Date</label> 
            <input  type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'to_date' }}" name="toDate" value="{{$toDate ?? ''}}" placeholder="Select Date To">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <label for="search-type">Search Type</label>
            <div class="form-group">
                @php
                    $allSerachType = array('vendor_name' => 'Vendor Name','invoice_no' => 'Invoice No', 'product_name' => 'Product Name','model_no' => 'Model Name');
                @endphp
                <select class="form-control chosen-select" id="searchType" name="searchType[]" multiple>
                   
                    @foreach ($allSerachType as $key => $value)
                        <option value="{{ $key }}" 
                        @if ($searchType)
                            
                        @foreach ($searchType as $searchkey)
                        @if ($key == $searchkey)
                        selected
                        @endif
                        @endforeach
                        @endif
                        >{{ $value }}</option>
                    @endforeach
                </select>
            </div>  
        </div>

        <div class="col-md-6">
            <label for="search-text">Search Text</label>
            <div class="form-group {{ $errors->has('searchText') ? ' has-danger' : '' }}">
                <input type="text" class="form-control" name="searchText" value="{{ $searchText }}">
                @if ($errors->has('searchText'))
                    @foreach($errors->get('searchText') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>


    <div class="row">
   
           <div class="col-md-6">
            <label for="search-type">Search Vendor</label>
            <div class="form-group">

                <select class="form-control chosen-select" id="vendors" name="vendors[]">
                   
                    @foreach($allVendor as $singlevendor)
                        <option value="{{$singlevendor->vendor_name}}">{{$singlevendor->vendor_name}}</option>
                    @endforeach
                  
                </select>
            </div>  
        </div>

    
    </div>
@endsection

@section('print_card_header')
    <input type="hidden" name="fromDate" value="{{ $fromDate }}">
    <input type="hidden" name="toDate" value="{{ $toDate }}">
    @if ($searchType)
    @foreach ($searchType as $searchKey)
    <input type="hidden" name="searchType[]" value="{{ $searchKey }}">
    @endforeach
    @endif

    <input type="hidden" name="searchText" value="{{ $searchText }}">
    <input type="hidden" id="print_value" name="print" value="{{ $print }}">
@endsection

{{-- class="bg-success text-dark font-weight-bold" style="font-size: 11px;" --}}

@section('print_card_body')

	<table id="dataTable" name="paymentRecordTable" class="table table-bordered table-sm">
		<thead>
			<tr>
                <th rowspan="2" >Sl</th>
                <th rowspan="2" >Date</th>
                <th rowspan="2" >Invoice No</th>
                <th rowspan="2">Vendor Name</th>
                <th rowspan="2" >Pro Name</th>
                <th rowspan="2" style="width: 40px">Model No</th>
                <th rowspan="1"  class="bg-success text-dark font-weight-bold" style="font-size: 11px;">Total: {{$data->total_quantity ?? ""}} Unit</th>
                <th rowspan="1"  class="bg-success text-dark font-weight-bold" style="font-size: 11px;">Total: {{$data->total_mrp_tk ?? ""}} BDT</th>
                <th rowspan="1"  class="bg-success text-dark font-weight-bold" style="font-size: 11px;">Total: {{$data->total_cp_tk ?? ""}} BDT</th>
                <th rowspan="1"  class="bg-success text-dark font-weight-bold" style="font-size: 11px;">Total: {{$data->total_discount_receipt ?? ""}} BDT</th>

                <th rowspan="1"  class="bg-success text-dark font-weight-bold" style="font-size: 11px;">Total: {{$data->total_total_amount ?? ""}} BDT</th>
            </tr>
            <tr>
                <th>Qty</th>
                <th>MRP Price</th>
                <th>Product Cost</th>
                <th>Discount</th>
                <th>Total Amount</th>
            </tr>
		</thead>

		<tbody>
            @php
                $sl = 1;
                $preTotalAmt = 0;
            @endphp

            @foreach ($retailSalesReports as $retailSalesReport)
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ date('d-m-Y',strtotime($retailSalesReport->date))}}</td>
                    <td>{{ $retailSalesReport->invoice_no }}</td>
                    <td>{{ $retailSalesReport->vendor_name }}</td>
                    <td>{{ $retailSalesReport->product_name }}</td>
                    <td>{{ $retailSalesReport->model_no }}</td>
                    <td>{{ $retailSalesReport->qty }}</td>
                    <td align="right">{{ $retailSalesReport->mrp_tk }}</td>
                    <td align="right">{{ $retailSalesReport->cp_tk }}</td>
                    <td align="right">{{ $retailSalesReport->discount_receipt }}</td>
                    <td align="right">{{ $retailSalesReport->total_amount + $preTotalAmt}}</td>

                </tr>
                @php
                   
                   $preTotalAmt = $preTotalAmt + $retailSalesReport->total_amount;
                   
                @endphp
            @endforeach
		</tbody>
	</table>
@endsection
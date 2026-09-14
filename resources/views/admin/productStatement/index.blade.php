@extends('admin.layouts.masterReport')

@section('search_card_body')
    <input type="hidden" name="print" value="print">
    <div class="row">
        <div class="col-md-6">
            <label for="product">Product</label>
            <div class="form-group">
                <select class="form-control chosen-select" id="product" name="product">
                    <option value="">Select A Product</option>
                    @foreach ($products as $productInfo)
                        @php
                            $select = "";
                            if ($product)
                            {
                                if ($productInfo->id == $product)
                                {
                                    $select = "selected";
                                }
                                else
                                {
                                    $select = "";
                                }
                            }
                        @endphp
                        <option value="{{ $productInfo->id }}" {{ $select }}>{{ $productInfo->name }}</option>
                    @endforeach
                </select>
            </div>                                  
        </div>

        <div class="col-md-3 form-group">
            <label for="from-date">From Date</label>
            <input  type="text" class="form-control datepicker" id="{{ $print == "print" ? "" : "from_date" }}" name="fromDate" placeholder="Select Date From" value="{{ date('d-m-Y',strtotime($fromDate)) }}" readonly>
        </div>
        <div class="col-md-3 form-group">
            <label for="to-date">To Date</label>
            <input  type="text" class="form-control datepicker" id="{{ $print == "print" ? "" : "to_date" }}" name="toDate" placeholder="Select Date To" value="{{ date('d-m-Y',strtotime($toDate)) }}" readonly>
        </div>
    </div>
@endsection

@section('print_card_header')
    <input type="hidden" name="product" value="{{ $product }}">

    <input type="hidden" name="fromDate" value="{{ $fromDate }}">
    <input type="hidden" name="toDate" value="{{ $toDate }}">
    <input type="hidden" id="print_value" name="print" value="{{ $print }}">
@endsection

@section('print_card_body')
	<table id="dataTable" name="liftingRecord" class="table table-bordered table-sm">
		<thead>
            <tr>
                <td colspan="8" align="right"><b>Opening Balance</b></td>
                <td align="right">{{ $openingBalance->opening }}</td>
            </tr>
			<tr>
                <th width="20px">Sl</th>
				<th width="80px">Date</th>
                <th width="100px" style="text-align: right;">Lifting</th>
				<th width="100px" style="text-align: right;">Lifting Return</th>
                <th width="100px" style="text-align: right;">Product Issue</th>
                <th width="100px" style="text-align: right;">Product Return</th>
                <th width="105px" style="text-align: right;">Sales</th>
                <th width="100px" style="text-align: right;">Sales Return</th>
                <th width="100px" style="text-align: right;">Balance</th>
			</tr>
		</thead>

		<tbody>
            @php
                $sl = 1;
            @endphp
            @foreach ($productStatements as $productStatement)
                @php
                    if ($sl == 1)
                    {
                        $balance = $openingBalance->opening + $productStatement->liftingPrice + $productStatement->productReturnPrice + $productStatement->slaesReturnPrice - $productStatement->liftingReturnPrice - $productStatement->productIssuePrice - $productStatement->salesPrice;
                    }
                    else
                    {
                        $balance = $balance + $productStatement->liftingPrice + $productStatement->productReturnPrice + $productStatement->slaesReturnPrice - $productStatement->liftingReturnPrice - $productStatement->productIssuePrice - $productStatement->salesPrice; 
                    } 
                @endphp
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ date('d-m-Y',strtotime($productStatement->date)) }}</td>
                    <td align="right">{{ $productStatement->liftingPrice }}</td>
                    <td align="right">{{ $productStatement->liftingReturnPrice }}</td>
                    <td align="right">{{ $productStatement->productIssuePrice }}</td>
                    <td align="right">{{ $productStatement->productReturnPrice }}</td>
                    <td align="right">{{ $productStatement->salesPrice }}</td>
                    <td align="right">{{ $productStatement->slaesReturnPrice }}</td>
                    <td align="right">{{ $balance }}</td>
                </tr>
            @endforeach
		</tbody>
	</table>
@endsection

@section('custom-js')
    <script>
        $('#searchForm').submit(function(){
            if ($('#product').val() == "")
            {
                swal("Please! Select A product", "", "warning");
                return false;
            }
        });
    </script>
@endsection
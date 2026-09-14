@extends('admin.layouts.masterReport')

@section('search_card_body')
@php
    use App\Product;
    use App\InstallmentSchedule;
    use App\InstallmentCollectionList;
@endphp
    <input type="hidden" name="print" value="print">
    <div class="row">
        <div class="col-md-6">
            <label for="from-date">Upto Date</label>
            <input  type="text" class="form-control {{$print=='print' ? 'datepicker' : 'add_datepicker'}}" name="tillCollectionDate" placeholder="Select Date From" value="{{Date('d-m-Y',strtotime(@$tillCollectionDate))}}" readonly>                                     
        </div>

        <div class="col-md-6">
            <label for="collector">Collector</label>
            <div class="form-group">
                <select class="form-control chosen-select" id="collector" name="collector[]" data-placeholder="Select Collector" multiple>
                    @foreach ($collectorList as $collector)
                        @php
                            $select = "";
                            if (@$collectorParameter)
                            {
                                if (in_array($collector->id, $collectorParameter))
                                {
                                    $select = "selected";
                                }
                                else
                                {
                                    $select = "";
                                }
                            }
                        @endphp
                        <option value="{{ $collector->id }}" {{ $select }}>{{ $collector->name }}</option>
                    @endforeach
                </select>
            </div>  
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <label for="collector">Company</label>
            <div class="form-group">
                <select class="form-control chosen-select" id="company" data-placeholder="Select Company" name="company[]" multiple>
                    @foreach ($collectorList as $company)
                        @php
                            $select = "";
                            if (@$companyParameter)
                            {
                                if (in_array($company->id, $companyParameter))
                                {
                                    $select = "selected";
                                }
                                else
                                {
                                    $select = "";
                                }
                            }
                        @endphp
                        <option value="{{ $company->id }}" {{ $select }}>{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>  
        </div>

        <div class="col-md-6">
            <label for="product">Product</label>
            <div class="form-group">
                <select class="form-control chosen-select" id="collector" name="installmentProduct[]" data-placeholder="Select Product" multiple>
                    @foreach ($installmentProductList as $installmentProduct)
                        @php
                            $select = "";
                            if (@$installmentProductParameter)
                            {
                                if (in_array($installmentProduct->product_id, $installmentProductParameter))
                                {
                                    $select = "selected";
                                }
                                else
                                {
                                    $select = "";
                                }
                            }

                            $product = Product::where('id',$installmentProduct->product_id)->first();
                        @endphp
                        <option value="{{ $product->id }}" {{ $select }}>{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>  
        </div>
    </div>

@endsection

@section('print_card_header')
   @if (@$tillCollectionDate)
    <input type="hidden" name="tillCollectionDate" value="{{ @$tillCollectionDate }}">
@endif
@if (@$collectorParameter)
    @foreach ($collectorParameter as $collector)
        <input type="hidden" name="collector[]" value="{{ $collector }}">
    @endforeach
@endif

@if (@$installmentProductParameter)
    @foreach ($installmentProductParameter as $product)
        <input type="hidden" name="installmentProduct[]" value="{{ $product }}">
    @endforeach
@endif
@endsection

@section('print_card_body')
	<table id="dataTable" name="liftingRecord" class="table table-bordered table-sm">
		<thead>
			<tr>
                <th width="20px">Sl</th>
                <th width="100px">Invoice No</th>
				<th width="180px">Client Name</th>
                <th width="90px">Phone No</th>
				<th width="30px" class="text-center">Qty</th>
                <th width="120px" class="text-center">Prospective Collection</th>
                <th width="115px">Collector</th>
			</tr>
		</thead>

		<tbody>
            @php
                $sl = 0;
            @endphp
            @foreach ($prospectiveCollectionList as $prospectiveCollection)
            @php
                $totalInstallmentAmount = InstallmentSchedule::where('installment_id',$prospectiveCollection->installmentId)->where('installment_schedule_date','<=',$tillCollectionDate)->sum('installment_schedule_amount');
                $totalInstallmentCollectionAmount = InstallmentCollectionList::where('installment_id',$prospectiveCollection->installmentId)->sum('installment_schedule_amount');
                $prospectiveCollectionAmount = $totalInstallmentAmount-$totalInstallmentCollectionAmount ;
            @endphp
                <tr>
                    <td>{{ $sl }}</td>
                    <td>{{ $prospectiveCollection->invoiceNo }}</td>
                    <td>{{ $prospectiveCollection->customerName }}</td>
                    <td>{{ $prospectiveCollection->phoneNo }}</td>
                    <td style="text-align: center;">{{ $prospectiveCollection->totalDueInstallment }}</td>
                    <td style="text-align: center;">{{$prospectiveCollectionAmount}}</td>
                    <td>{{ $prospectiveCollection->installmentCollectorName }}</td>
                </tr>
            @endforeach
		</tbody>
	</table>

<style type="text/css">
    td .container_table {
        z-index:10;display:none; padding:10px 10px;
        margin-top:15px; margin-left:-380px;
        line-height:16px;
        min-height: 100px;
        max-height: 200px;
        overflow-y: scroll;
    }

    td:hover .container_table{
        display:inline; position:absolute; color:#111;
        border:1px solid #DCA; background:#fffAF0;}

    .container_table td{
        padding: 8px !important;
        font-size: 13px !important;
        border-bottom: 1px solid #333 !important;
    }
    .container_table tr{
        line-height: 8px !important;
    }

    .container_table tbody tr:nth-child(even) {
        background: #fff;
    }
</style>
@endsection
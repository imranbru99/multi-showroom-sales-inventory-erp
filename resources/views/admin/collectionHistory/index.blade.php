@extends('admin.layouts.masterReport')

@section('search_card_body')
@php
    use App\InstallmentCollection;
    use App\CustomerRegistrationSetup;
    use App\Product;
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
                <select class="form-control chosen-select" id="collector" name="collector[]" multiple>
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
@endsection

@section('print_card_body')
	<table id="dataTable" name="liftingRecord" class="table table-bordered table-sm">
		<thead>
			<tr>
                <th width="20px">Sl</th>
                <th width="100px">Invoice No</th>
				<th width="180px">Client Name</th>
				<th width="150px">Phone No</th>
                <th>Product</th>
                <th width="80px">Collector</th>
                <th width="115px" style="text-align: center;">Collection Date</th>
                <th width="130px" style="text-align: right;">Collection Amount</th>
			</tr>
		</thead>

		<tbody>
            @php
                $sl = 1;
            @endphp
            @foreach ($collectionHistoryList as $collectionHistory)
            @php
                $installmentCollection = InstallmentCollection::where('id',$collectionHistory->installment_collection_id)->first();
                $customer = CustomerRegistrationSetup::where('id',$installmentCollection->customer_id)->first();
                $product = Product::where('id',$installmentCollection->product_id)->first();
                $collectionDate = Date('d-m-Y', strtotime($collectionHistory->installment_collection_date));
            @endphp
                <tr>
                    <td>{{ $sl }}</td>
                    <td>{{ $collectionHistory->invoice_no }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->phone_no }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $collectionHistory->installmentCollectorName }}</td>
                    <td style="text-align: center;">{{ $collectionDate }}</td>
                    <td style="text-align: right;">{{ $collectionHistory->installment_schedule_amount }}</td>
                </tr>
            @endforeach
		</tbody>
	</table>
@endsection
@extends('admin.layouts.masterReport')

@section('search_card_body')
@php
    use App\InstallmentSchedule;
@endphp
    <input type="hidden" name="print" value="print">
    <div class="row">
        <div class="col-md-6">
            <label for="from-date">Upto Date</label>
            <input  type="text" class="form-control" name="tillCollectionDate" placeholder="Select Date From" value="{{Date('d-m-Y')}}" readonly>                                     
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
                <th width="80px">Due Qty</th>
                <th width="115px">Total Amount</th>
			</tr>
		</thead>

		<tbody>
            @php
                $sl = 0;
            @endphp
            @foreach ($dropCollectionList as $dropCollection)
            @php
                $scheduleList = InstallmentSchedule::where('installment_id',$dropCollection->installmentId)->where('status',1)->where('installment_schedule_date','<',$tillCollectionDate)->get();
            @endphp
                <tr>
                    <td>{{ $sl }}</td>
                    <td>{{ $dropCollection->invoiceNo }}</td>
                    <td>{{ $dropCollection->customerName }}</td>
                    <td>{{ $dropCollection->phoneNo }}</td>
                    <td>{{ $dropCollection->productName }}</td>
                    <td style="text-align: center;">
                        {{ $dropCollection->totalDueInstallment }}
                        <table class="container_table" border="1">
                            <tbody>
                                 <tr style="background-color: #b0efc9;">
                                    <td>Invoice No</td>
                                    <td>Schedule Date</td>
                                    <td>Installment Amount</td>
                                </tr>
                            </tbody>
                            <tbody>
                                @foreach ($scheduleList as $schedule)
                                    @php
                                        $scheduleDate = Date('d-m-Y',strtotime($schedule->installment_schedule_date));
                                    @endphp
                                   <tr>
                                        <td>{{$schedule->invoice_no}}</td>
                                        <td>{{$scheduleDate}}</td>
                                        <td>{{@$schedule->installment_schedule_amount}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                    <td style="text-align: right;">{{ $dropCollection->totalInstallmentAmount }}</td>
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
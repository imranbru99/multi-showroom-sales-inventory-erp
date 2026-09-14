<?php
use App\Installment;
use Illuminate\Support\Carbon;
use App\InstallmentCollectionList;
use Illuminate\Support\Facades\DB;
?>
@extends('admin.layouts.masterReport')
@section('search_card_body')
    <input type="hidden" value="true" name="searched">
    <div class="row d-flex justify-content-center">
        <div class="col-md-4">
            <label for="customer">Project</label>
            <div class="form-group">
                <select name="project" class="form-control">
                    <option value="">Select Project</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}" @if ($project->id == $project_id) selected @endif>{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-4">
            <label for="staff">Employee</label>
            <select name="staff" class="form-control chosen-select">
                <option value="">Select Employee</option>
                @foreach ($staffs as $staff)
                    <option value="{{ $staff->id }}" @if ($staff->id == $staffId) selected @endif>{{ $staff->name }}</option>
                @endforeach
            </select>
        </div>

    </div>
@endsection

@section('print_card_header')
    <input type="hidden" class="form-control" name="project" value="{{ $project_id }}">
    <input type="hidden" class="form-control" name="staff" value="{{ $staffId }}">
    <input type="hidden" id="print_value" name="print" value="Print">
@endsection

@section('print_card_body')
    <table id="dataTable" name="paymentRecordTable" class="table table-bordered table-sm">
        <thead>
            <tr>
                <th width="20px">Sl</th>
                <th>Invoice Date</th>
                <th>Last Collection Date</th>
                <th>A/C No.</th>
                <th>Account Name</th>
                <th>Mobile#</th>
                <th>Address</th>
                <th>Sales By</th>
                <th>Invoice#</th>
                <th>Invoice Amount</th>
                <th>Collection</th>
                <th>Due Amount</th>
            </tr>
        </thead>

        <tbody>
            @php
                $i = 1;
            @endphp

            @foreach ($retailSales as $retailSale)

                @php
                    
                    $invoices = \App\RetailSale::where('customer_id', $retailSale->customer_id)->count();
                    
                    $saleAmount = @$retailSale->products->sum('sales_price');
                    
                    if (!$retailSale->installment) {
                        continue;
                    }
                    
                    $collectionAmount = 0;
                    foreach ($retailSale->collection as $values) {
                        $collectionAmount += @$values->collections->sum('installment_schedule_amount') / $invoices;
                    }
                    
                    $due_amount = $saleAmount - $collectionAmount;
                    
                    if ($due_amount <= 0 || @$retailSale->customer->is_close == 1) {
                        continue;
                    }
                    
                    $startDate = date('Y-m-d', strtotime(now()->subDays(90)));
                    $endDate = date('Y-m-d');
                    
                    $customer = \App\InstallmentCollectionList::with('collection')
                        ->whereHas('collection', function ($q) use ($retailSale) {
                            $q->where('customer_id', $retailSale->customer_id);
                        })
                        ->whereBetween('installment_collection_date', [$startDate, $endDate])
                        ->latest('installment_collection_date')
                        ->first();
                    
                    if ($collectionAmount == 0 && !$customer) {
                        $lastdate = 'Waiting';
                    } elseif ($collectionAmount > 0 && !$customer) {
                        continue;
                    }else{
                        $lastdate = date('d-m-Y', strtotime($customer->installment_collection_date));
                    }
                @endphp

                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ date('d-m-Y', strtotime($retailSale->sale_date)) }}</td>
                    <td>{{ $lastdate }}</td>
                    <td>{{ @$retailSale->customer->code }}</td>
                    <td>{{ @$retailSale->customer->name }}</td>
                    <td>{{ @$retailSale->customer->phone_no }}</td>
                    <td>{{ @$retailSale->customer->present_address }}</td>
                    <td>{{ @$retailSale->seller->name }}</td>
                    <td>{{ $retailSale->invoice_no }}</td>
                    <td>{{ round($saleAmount) }}</td>
                    <td>{{ round($collectionAmount) }}</td>
                    <td>{{ round($due_amount) }}</td>
                </tr>
            @endforeach

        </tbody>
    </table>
@endsection


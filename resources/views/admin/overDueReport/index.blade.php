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
    <table id="dataTableUpcomingCollection" name="paymentRecordTable" class="table table-bordered table-sm">
        <thead>
            <tr>
                <th width="20px">Sl</th>
                <th>Invoice Date</th>
                <th>A/C No.</th>
                <th>Account Name</th>
                <th>Mobile#</th>
                <th>Address</th>
                <th>Sales By</th>
                <th>Invoice#</th>
                <th>Invoice Amount</th>
                <th>Collection</th>
                <th>Due Amount</th>
                <th>Duration</th>
                <th>Over Date</th>
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
                    
                    $invoiceDate = Carbon::parse($retailSale->sale_date);
                    $invoiceDatePlusOneYear = Carbon::parse($retailSale->sale_date)->addDays(365);
                    
                    // Date Diff
                    $todayDate = Carbon::now();
                    
                    $duration = $todayDate->diffInDays($invoiceDate);
                    $overDate = $todayDate->diffInDays($invoiceDatePlusOneYear);
                    
                    $due_amount = $saleAmount - $collectionAmount;
                    
                    if ($due_amount < 0 || $due_amount == 0) {
                        continue;
                    }
                    
                @endphp

                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ date('d-m-Y', strtotime($retailSale->sale_date)) }}</td>
                    <td>{{ @$retailSale->customer->code }}</td>
                    <td>{{ @$retailSale->customer->name }}</td>
                    <td>{{ @$retailSale->customer->phone_no }}</td>
                    <td>{{ @$retailSale->customer->present_address }}</td>
                    <td>{{ @$retailSale->seller->name }}</td>
                    <td>{{ $retailSale->invoice_no }}</td>
                    <td>{{ $saleAmount }}</td>
                    <td>{{ $collectionAmount }}</td>
                    <td>{{ $due_amount }}</td>
                    <td>{{ $duration }}</td>
                    <td>
                        @if ($invoiceDatePlusOneYear->lt($todayDate))
                            {{ $overDate }}
                        @endif
                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>
@endsection

@section('custom-js')
    <script>
        $(document).ready(function() {
            var table = $('#dataTableUpcomingCollection').DataTable({
                "order": [
                    [6, "desc"]
                ],
            });
        });
    </script>
@endsection

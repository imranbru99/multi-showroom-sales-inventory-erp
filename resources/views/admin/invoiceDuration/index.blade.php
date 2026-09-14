<?php
use App\Installment;
use Illuminate\Support\Carbon;
use App\CustomerRegistrationSetup;
use App\InstallmentCollectionList;
use Illuminate\Support\Facades\DB;
?>
@extends('admin.layouts.masterReport')

@section('search_card_body')

    <input type="hidden" value="true" name="searched">

    <div class="row">

        <div class="col-md-4">
            <label for="customer">Start Date</label>
            <div class="form-group">
                <input type="text" class="form-control datepicker" name="start_date" value="{{ $start_date }}">
            </div>
        </div>

        <div class="col-md-4">
            <label for="customer">End Date</label>
            <div class="form-group">
                <input type="text" class="form-control datepicker" name="end_date" value="{{ $end_date }}">
            </div>
        </div>

        <div class="col-md-4">
            <label for="customer">Sales By</label>
            <div class="form-group">
                <select name="staff" class="form-control chosen-select">
                    <option value=""></option>
                    @foreach ($staffs as $staff)
                        <option value="{{ $staff->id }}"
                            @if ($staffId == $staff->id)
                                selected
                            @endif
                            >{{ $staff->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

    </div>

@endsection

@section('print_card_header')

    <input type="hidden" class="form-control" name="start_date" value="{{ $start_date }}">
    <input type="hidden" class="form-control" name="end_date" value="{{ $end_date }}">
    <input type="hidden" class="form-control" name="staffId" value="{{ $staffId }}">
    <input type="hidden" id="print_value" name="print" value="Print">

@endsection

@section('print_card_body')

    <table id="dataTableUpcomingCollection" name="paymentRecordTable" class="table table-bordered table-sm">

        <thead>
            <tr>
                <th width="20px">Sl</th>
                <th>A/C No.</th>
                <th>Account Name</th>
                <th>Mobile#</th>
                <th>Invoice Date</th>
                <th>Invoice#</th>
                <th>Invoice Amount</th>
                <th>Collection</th>
                <th>Due Amount</th>
                <th>Duration</th>
                <th>Over Date</th>
                <th>Over Month</th>
            </tr>
        </thead>

        <tbody>

            @php
                $i = 1;
            @endphp

            @foreach ($retailSales as $retailSale)

                @php
                    $saleAmount = 0;
                    
                    foreach ($retailSale->products as $product) {
                        $qty = $product->qty;
                        $price = null;
                    
                        if ($retailSale->sale_type == 'cash') {
                            $price = $product->cash_price;
                        }
                        if ($retailSale->sale_type == 'Short Installment') {
                            $price = $product->mrp_price;
                        }
                        if ($retailSale->sale_type == 'Long Installment') {
                            $price = $product->hire_price;
                        }
                    
                        $salePrice = $qty * $price;

                        $salePrice = $salePrice - $product->discount - $product->exchange_crt - $product->mobile_gift;
                    
                        $saleAmount += $salePrice;
                    }
                    
                    if (!$retailSale->installment) {
                        continue;
                    }
                    
                    $collectionAmount = 0;
                    
                    if ($retailSale->customer) {
                        $customer = CustomerRegistrationSetup::findOrFail($retailSale->customer->id);
                    
                        $collectionAmount = $customer->totalCollectionAmount();
                    }
                    
                    $invoiceDate = Carbon::parse($retailSale->sale_date);
                    $invoiceDatePlusOneYear = Carbon::parse($retailSale->sale_date)->addDays(365);
                    
                    // Date Diff
                    $todayDate = Carbon::now();
                    
                    $duration = $todayDate->diffInDays($invoiceDate);
                    $overDate = $todayDate->diffInDays($invoiceDatePlusOneYear);
                    $overMonths = $todayDate->diffInMonths($invoiceDatePlusOneYear);
                    
                @endphp

                <tr>

                    <td>{{ $i++ }}</td>
                    <td>{{ @$retailSale->customer->code }}</td>
                    <td>{{ @$retailSale->customer->name }}</td>
                    <td>{{ @$retailSale->customer->phone_no }}</td>
                    <td>{{ date('d-m-Y', strtotime($retailSale->sale_date)) }}</td>
                    <td>{{ $retailSale->invoice_no }}</td>
                    <td>{{ $saleAmount }}</td>
                    <td>{{ $collectionAmount }}</td>
                    <td>{{ $saleAmount - $collectionAmount }}</td>
                    <td>{{ $duration }}</td>
                    <td>
                        @if ($invoiceDatePlusOneYear->lt($todayDate))
                            {{ $overDate }}
                        @endif
                    </td>
                    <td>
                        @if ($invoiceDatePlusOneYear->lt($todayDate))
                            {{ $overMonths }}
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

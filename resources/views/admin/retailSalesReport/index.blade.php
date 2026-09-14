@extends('admin.layouts.masterReport')

@section('search_card_body')
    <div class="row">
        <div class="col-md-12">
            <input type="hidden" name="print" value="print">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 form-group">
            <label for="from-date">From Date</label>
            <input type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'from_date' }}"
                name="fromDate" value="{{ date('d-m-Y', strtotime($fromDateToform)) ?? '' }}"
                placeholder="Select Date From">
        </div>
        <div class="col-md-6 form-group">
            <label for="to-date">To Date</label>
            <input type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'to_date' }}" name="toDate"
                value="{{ date('d-m-Y', strtotime($toDateToform)) ?? '' }}" placeholder="Select Date To">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <label for="search-type">Search Type</label>
            <div class="form-group">
                @php
                    $allSerachType = ['customer_name' => 'Customer Name', 'account_no' => 'Account No', 'mobile_no' => 'Mobile No', 'employee_name' => 'Employee Name', 'groups' => 'Group', 'product_name' => 'Product Name', 'model_no' => 'Model Name', 'money_receipt_no' => 'Money Receipt No', 'memo_no' => 'Memo No'];
                @endphp
                <select class="form-control chosen-select" id="searchType" name="searchType[]" multiple>

                    @foreach ($allSerachType as $key => $value)
                        <option value="{{ $key }}" @if ($searchType)  @foreach ($searchType as $searchkey)
                            @if ($key == $searchkey)
                                selected @endif
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
                    @foreach ($errors->get('searchText') as $error)
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

                <select class="form-control chosen-select" id="vendors" name="vendors[]" multiple>

                    @foreach ($allVendor as $singlevendor)
                        <option value="{{ $singlevendor->id }}">{{ $singlevendor->name }}</option>
                    @endforeach

                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="col-md-6">
                <label for="search-type">Search Dealer</label>
                <div class="form-group">

                    <select class="form-control chosen-select" id="dealers" name="dealers[]" multiple>

                        @foreach ($allShowroomProject as $val)
                            <option value="{{ $val->id }}">{{ $val->name }}</option>
                        @endforeach

                    </select>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('print_card_header')
    <input type="hidden" name="fromDate" value="{{ $fromDateToform }}">
    <input type="hidden" name="toDate" value="{{ $toDateToform }}">

    @if ($searchType)
        @foreach ($searchType as $searchKey)
            <input type="hidden" name="searchType[]" value="{{ $searchKey }}">
        @endforeach
    @endif
    <input type="hidden" name="searchText" value="{{ $searchText }}">
    <input type="hidden" id="print_value" name="print" value="{{ $print }}">
@endsection



@section('print_card_body')

    <table id="dataTable" name="paymentRecordTable" class="table table-bordered table-sm table-responsive">
        <thead>
            <tr>
                <th rowspan="2">Sl</th>
                <th rowspan="2">Date</th>
                <th rowspan="2">Customer Name</th>
                <th rowspan="2">Mobile No</th>
                <th rowspan="2">A/C No</th>
                <th rowspan="2">Memo No</th>
                <th rowspan="2">Pro Name</th>
                <th rowspan="2" style="width: 40px">Model No</th>
                <th rowspan="1" class="bg-success text-dark font-weight-bold" style="font-size: 11px;">Total:
                    {{ $data->total_quantity ?? '' }} Unit</th>
                <th rowspan="2">Money Receipt No</th>
                <th rowspan="2">Product Price</th>
                <th rowspan="1" class="bg-success text-dark font-weight-bold" style="font-size: 11px;">Total:
                    {{ $data->total_sales_price ?? '' }} BDT</th>
                <th rowspan="1" class="bg-success text-dark font-weight-bold" style="font-size: 11px;">Total:
                    {{ $data->total_collection ?? '' }} BDT</th>
                <th rowspan="1" class="bg-success text-dark font-weight-bold" style="font-size: 11px;">Total:
                    {{ $data->total_discount ?? '' }} BDT</th>
                <th rowspan="1" class="bg-success text-dark font-weight-bold" style="font-size: 11px;">Total:
                    {{ $data->total_gift_voucher ?? '' }} BDT </th>
                <th rowspan="1" class="bg-success text-dark font-weight-bold" style="font-size: 11px;">Total:
                    {{ $data->total_exchange_crt ?? '' }} BDT</th>
                <th rowspan="1" class="bg-success text-dark font-weight-bold" style="font-size: 11px;">Total:
                    {{ $data->total_balance ?? '' }} BDT</th>
                <th rowspan="1" class="bg-success text-dark font-weight-bold" style="font-size: 11px;">Total:
                    {{ $data->total_agreement_tk ?? '' }} BDT </th>
            </tr>
            <tr>
                <th>Qty</th>
                <th>Sales Price</th>
                <th>Collection</th>
                <th>Discount</th>
                <th>Gift Voucher</th>
                <th>Exchange CRT</th>
                <th>Balance</th>
                <th>Agreement</th>
            </tr>
        </thead>

        <tbody>
            @php
                $sl = 1;
                $prevBalance = 0;
                
            @endphp
            @foreach ($retailSalesReports as $retailSalesReport)
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ date('d-m-Y', strtotime($retailSalesReport->date)) }}</td>
                    <td>{{ $retailSalesReport->customer_name }}</td>
                    <td>{{ $retailSalesReport->mobile_no }}</td>
                    <td>{{ $retailSalesReport->account_no }}</td>
                    <td>{{ $retailSalesReport->memo_no }}</td>
                    <td>{{ $retailSalesReport->product_name }}</td>
                    <td>{{ $retailSalesReport->model_no }}</td>
                    <td>{{ $retailSalesReport->qty }}</td>
                    <td>{{ $retailSalesReport->money_receipt_no }}</td>
                    <td align="right">
                       
                    </td>
                    <td align="right">{{ $retailSalesReport->sales_amount }}</td>
                    <td align="right">{{ $retailSalesReport->collection }}</td>
                    <td align="right">{{ $retailSalesReport->discount }}</td>
                    <td align="right">{{ $retailSalesReport->gift_voucher }}</td>
                    <td align="right">{{ $retailSalesReport->exchange_crt }}</td>
                    <td align="right">{{ $prevBalance + $retailSalesReport->total_balance }}</td>
                    <td align="right">{{ $retailSalesReport->agreement_tk }}</td>

                    <?php $prevBalance = $prevBalance + $retailSalesReport->total_balance; ?>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

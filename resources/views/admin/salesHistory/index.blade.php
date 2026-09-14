@extends('admin.layouts.master')

@section('content')
    <form class="form-horizontal" id="search" action="{{ route($searchFormLink) }}" method="POST"
        enctype="multipart/form-data">
        {{ csrf_field() }}

        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-5">
                        <h4 class="card-title">{{ $title }}</h4>
                    </div>
                    <div class="col-md-7 text-right">
                        {{-- <button type="submit" class="btn btn-outline-info btn-lg waves-effect" name="btnSummary"
                        value="Summary"><i class="fa fa-search"></i> Sales Summary</button>
                    <button type="submit" class="btn btn-outline-info btn-lg waves-effect" name="btnHistory"
                        value="History"><i class="fa fa-search"></i> Sales History</button> --}}
                        {{-- <button type="submit" class="btn btn-outline-info btn-lg waves-effect" name="btnGroupSalesHistory"
                        value="Group Sales History"><i class="fa fa-search"></i> Group Sales History</button> --}}
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <input type="hidden" name="print" value="print">
                    </div>
                </div>

                <div class="row">

                    <div class="col-md-4 form-group">
                        <label for="from-date">From Date</label>
                        <input type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'from_date' }}"
                            name="fromDate" placeholder="Select Date From"
                            value="{{ date('d-m-Y', strtotime($fromDate)) }}" readonly>
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="to-date">To Date</label>
                        <input type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'to_date' }}"
                            name="toDate" placeholder="Select Date To" value="{{ date('d-m-Y', strtotime($toDate)) }}"
                            readonly>
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="to-date">Sales By</label>
                        <select name="employee" class="form-control chosen-select">
                            <option value="">Select Employee</option>
                            @foreach ($staffList as $staff)
                                <option value="{{ $staff->id }}" @if ($staff->id == $employee) selected @endif>{{ $staff->name }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                {{-- <div class="row">
        <div class="col-md-4">
            <label for="reference">Reference</label>
            <div class="form-group">
                <select class="form-control chosen-select" name="reference[]" data-placeholder="Select Refences"
                    multiple>
                    @foreach ($staffList as $staff)
                    @php
                    $select = "";
                    if ($reference)
                    {
                    if (in_array($staff->id, $reference))
                    {
                    $select = "selected";
                    }
                    else
                    {
                    $select = "";
                    }
                    }
                    @endphp
                    <option value="{{ $staff->id }}" {{ $select }}>{{ $staff->name }}</option>
    @endforeach
    </select>
    </div>
    </div>
    <div class="col-md-4">
        <label for="category">Category</label>
        <div class="form-group">
            <select class="form-control chosen-select" id="category" name="category[]"
                data-placeholder="Select Categories" multiple>
                @foreach ($categories as $categoryInfo)
                @php
                $select = "";
                if ($category)
                {
                if (in_array($categoryInfo->id, $category))
                {
                $select = "selected";
                }
                else
                {
                $select = "";
                }
                }
                @endphp
                <option value="{{ $categoryInfo->id }}" {{ $select }}>{{ $categoryInfo->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-4">
        <label for="product">Product</label>
        <div class="form-group">
            <select class="form-control chosen-select" id="product" name="product[]" data-placeholder="Select Products"
                multiple>
                @foreach ($products as $productInfo)
                @php
                $select = "";
                if ($product)
                {
                if (in_array($productInfo->id, $product))
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
    </div>
    </div> --}}

                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="submit" class="btn btn-outline-info btn-lg waves-effect" name="btnSummary"
                                value="Summary"><i class="fa fa-search"></i> Sales Summary</button>
                            <button type="submit" class="btn btn-outline-info btn-lg waves-effect" name="btnHistory"
                                value="History"><i class="fa fa-search"></i> Sales History</button>
                            {{-- <button type="submit" class="btn btn-outline-info btn-lg waves-effect" name="btnGroupSalesHistory"
                    value="Group Sales History"><i class="fa fa-search"></i> Group Sales History</button> --}}
                        </div>
                    </div>
                </div>
            </div>
    </form>

    @if ($btnSummary != '' || $btnHistory != '' || $btnGroupSalesHistory != '')
        @php
            if ($btnSummary == 'Summary') {
                $searchResultTitle = 'Sales Summary';
            }
            if ($btnHistory == 'History') {
                $searchResultTitle = 'Sales History';
            }
            if ($btnGroupSalesHistory == 'Group Sales History') {
                $searchResultTitle = 'Group Wise Sales History';
            }
        @endphp
        <div class="card" style="margin-bottom: 0px;">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="card-title">Searched Report ( {{ @$searchResultTitle }} )</h4>
                    </div>
                    <div class="col-md-6 text-right">
                        <form class="form-horizontal" id="print" action="{{ route($printFormLink) }}" target="_blank"
                            method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            @if ($showroomParam)
                                @foreach ($showroomParam as $showroomInfo)
                                    <input type="hidden" name="showRoom[]" value="{{ $showroomInfo }}">
                                @endforeach
                            @endif
                            @if ($groupParam)
                                @foreach ($groupParam as $groupInfo)
                                    <input type="hidden" name="group[]" value="{{ $groupInfo }}">
                                @endforeach
                            @endif
                            <input type="hidden" name="fromDate" value="{{ $fromDate }}">
                            <input type="hidden" name="toDate" value="{{ $toDate }}">
                            {{-- @if ($employee) --}}
                            {{-- @foreach ($employee as $referenceInfo) --}}
                            <input type="hidden" name="employee" value="{{ $employee }}">
                            {{-- @endforeach --}}
                            {{-- @endif --}}
                            @if ($category)
                                @foreach ($category as $categoryInfo)
                                    <input type="hidden" name="category[]" value="{{ $categoryInfo }}">
                                @endforeach
                            @endif

                            @if ($product)
                                @foreach ($product as $productInfo)
                                    <input type="hidden" name="product[]" value="{{ $productInfo }}">
                                @endforeach
                            @endif

                            <input type="hidden" id="print_value" name="print" value="{{ $print }}">

                            @if ($btnSummary == 'Summary')
                                <button type="submit" class="btn btn-outline-info btn-lg waves-effect"
                                    name="btnPrintSummary" value="Print Summary"><i class="fa fa-print"></i> Print Sales
                                    Summary</button>
                            @endif

                            @if ($btnHistory == 'History')
                                <button type="submit" class="btn btn-outline-info btn-lg waves-effect"
                                    name="btnPrintHistory" value="Print History"><i class="fa fa-print"></i> Print Sales
                                    History</button>
                            @endif

                            @if ($btnGroupSalesHistory == 'Group Sales History')
                                <button type="submit" class="btn btn-outline-info btn-lg waves-effect"
                                    name="btnPrintGroupSalesHistory" value="Print Group Sales History"><i
                                        class="fa fa-print"></i>
                                    Print Group Sales History</button>
                            @endif
                    </div>
                </div>
            </div>

            <div class="card-body">
                @if ($btnSummary == 'Summary')
                    <table id="dataTable" name="salesSummary" class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th width="20px">Sl</th>
                                <th>Account No</th>
                                <th>Client</th>
                                <th>Phone</th>
                                <th width="80px" style="text-align: center;">Qty</th>
                                <th width="110px">Total Amount</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($salesSummary as $summary)
                                <tr>
                                    <td>{{ $summary['sl'] }}</td>
                                    <td>{{ $summary['account_no'] }}</td>
                                    <td>{{ $summary['customer_name'] }}</td>
                                    <td>{{ $summary['customer_phone'] }}</td>
                                    <td>{{ $summary['qty'] }}</td>
                                    <td>{{ $summary['total_amount'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                @if ($btnHistory == 'History')
                    <table id="dataTable" name="salesHistory" class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th width="20px">Sl</th>
                                <th width="80px">Date</th>
                                <th>Account No</th>
                                <th>Client</th>
                                <th>Phone</th>
                                {{-- <th>Nid</th> --}}
                                <th>Memo No</th>
                                {{-- <th>Category</th> --}}
                                <th>Product</th>
                                <th>Model</th>
                                <th>Serial</th>
                                <th>Seller</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Total Price</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php
                                $sl = 1;
                            @endphp
                            @foreach ($salesHistory as $salesRecord)
                                <tr>
                                    <td>{{ $salesRecord['sl'] }}</td>
                                    <td>{{ $salesRecord['date'] }}</td>
                                    <td>{{ $salesRecord['account_no'] }}</td>
                                    <td>{{ $salesRecord['customer_name'] }}</td>
                                    <td>{{ $salesRecord['customer_phone'] }}</td>
                                    {{-- <td>{{ $salesRecord['customer_nid'] }}</td> --}}
                                    <td>{{ $salesRecord['memo_no'] }}</td>
                                    {{-- <td>{{ $salesRecord['category'] }}</td> --}}
                                    <td>{{ $salesRecord['product_name'] }}</td>
                                    <td>{{ $salesRecord['product_model'] }}</td>
                                    <td>{{ $salesRecord['product_serial'] }}</td>
                                    <td>{{ $salesRecord['seller'] }}</td>
                                    <td>{{ $salesRecord['product_qty'] }}</td>
                                    <td>{{ $salesRecord['product_price'] }}</td>
                                    <td>{{ $salesRecord['total_product_price'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                @if ($btnGroupSalesHistory == 'Group Sales History')
                    <table id="dataTable" name="salesHistory" class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th width="20px">Sl</th>
                                <th width="80px">Date</th>
                                <th>Group</th>
                                <th>Client</th>
                                <th>Phone</th>
                                <th>Showroom</th>
                                <th>Category</th>
                                <th>Product</th>
                                <th>Serial</th>
                                <th>Model</th>
                                <th>Color</th>
                                <th>Price</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php
                                $sl = 1;
                            @endphp
                            @foreach ($groupSalesHistory as $salesRecord)
                                @php
                                    $purchaseDate = date('d-m-Y', strtotime($salesRecord->purchaseDate));
                                @endphp
                                <tr>
                                    <td>{{ $sl++ }}</td>
                                    <td>{{ $purchaseDate }}</td>
                                    <td>{{ @$salesRecord->groupName }}</td>
                                    <td>{{ $salesRecord->customerName }}</td>
                                    <td>{{ $salesRecord->customerPhoneNo }}</td>
                                    <td>{{ $salesRecord->showroomName }}</td>
                                    <td>{{ $salesRecord->categoryName }}</td>
                                    <td>{{ $salesRecord->productName }}</td>
                                    <td>{{ $salesRecord->productSerialNo }}</td>
                                    <td>{{ $salesRecord->productModelNo }}</td>
                                    <td>{{ $salesRecord->productColor }}</td>
                                    <td align="right">{{ $salesRecord->productCashPrice }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    @endif
@endsection

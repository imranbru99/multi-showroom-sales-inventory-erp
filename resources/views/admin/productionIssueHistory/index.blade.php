@extends('admin.layouts.masterReport')

@section('search_card_body')
    <input type="hidden" name="print" value="print">
    <div class="row">

        <div class="col-md-4 form-group">
            <label for="from-date">From Date</label>
            <input type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'from_date' }}"
                name="fromDate" value="{{ date('d-m-Y', strtotime($fromDate)) }}" placeholder="Select Date From">
        </div>

        <div class="col-md-4 form-group">
            <label for="to-date">To Date</label>
            <input type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'to_date' }}" name="toDate"
                value="{{ date('d-m-Y', strtotime($toDate)) }}" placeholder="Select Date To">
        </div>

        <div class="col-md-4">
            <label for="category">Category</label>
            <div class="form-group">
                <select class="form-control chosen-select" id="category" name="category[]" multiple>
                    @foreach ($categories as $categoryInfo)
                        @php
                            $select = '';
                            if ($category) {
                                if (in_array($categoryInfo->id, $category)) {
                                    $select = 'selected';
                                } else {
                                    $select = '';
                                }
                            }
                        @endphp
                        <option value="{{ $categoryInfo->id }}" {{ $select }}>{{ $categoryInfo->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

    </div>

    <div class="row">

       

        <div class="col-md-4">
            <label for="staff">Requisition By </label>
            <div class="form-group">
                <select class="form-control chosen-select" id="staff" name="staff[]" multiple>
                    @foreach ($staffs as $dealerInfo)
                        <option value="{{ $dealerInfo->id }}">{{ $dealerInfo->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

    </div>
@endsection

@section('print_card_header')
    @if ($staff)
        @foreach ($staff as $dealerInfo)
            <input type="hidden" name="staff[]" value="{{ $dealerInfo }}">
        @endforeach
    @endif

    @if ($category)
        @foreach ($category as $categoryInfo)
            <input type="hidden" name="category[]" value="{{ $categoryInfo }}">
        @endforeach
    @endif

    <input type="hidden" name="fromDate" value="{{ $fromDate }}">
    <input type="hidden" name="toDate" value="{{ $toDate }}">
    <input type="hidden" id="print_value" name="print" value="{{ $print }}">
@endsection

@section('print_card_body')
    <table id="dataTable" name="paymentRecordTable" class="table table-bordered table-sm">
        <thead>
            <tr>
                <th width="15px">SL#</th>
                <th width="130px">Requsition Name</th>
                <th width="80px">Date</th>
                <th width="30px">Category Name</th>
                <th width="80px">Product Name</th>
                <th width="90px">Model</th>
                <th width="90px">Serial No</th>
                <th width="80px">Qty</th>
            </tr>
        </thead>

        <tbody>
            @php
                $sl = 1;
            @endphp

            @foreach ($productIssueHistories as $groupByProduct)

                {{-- @foreach ($step1 as $step2)
                    @foreach ($step2 as $step3)
                        @foreach ($step3 as $groupByProduct) --}}
                            <tr>
                                <td>{{ $sl++ }}</td>
                                <td>{{ $groupByProduct->staffName }}</td>
                                <td>{{ date('d-m-Y', strtotime($groupByProduct->date)) }}</td>
                                <td>{{ $groupByProduct->categoryName }}</td>
                                <td>{{ $groupByProduct->productName }}</td>
                                <td>{{ $groupByProduct->modelNo }}</td>
                                <td>
                                    @php
                                        $uniqueProducts = explode(',', $groupByProduct->totalProductSerialNO);
                                    @endphp
                                    @foreach ($uniqueProducts as $uniqueProduct)
                                        {{ $uniqueProduct }},
                                    @endforeach
                                </td>
                                <td align="right">{{ $groupByProduct->totalIssueQty }}</td>
                            </tr>
                        {{-- @endforeach
                    @endforeach
                @endforeach --}}
            @endforeach
        </tbody>
    </table>
@endsection



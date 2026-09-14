@extends('admin.layouts.masterReport')

@section('search_card_body')
<input type="hidden" name="print" value="print">
<div class="row">
    <div class="col-md-6">
        <label for="vendor">Vendor</label>
        <div class="form-group">
            <select class="form-control chosen-select" id="vendor" name="vendor[]">
                <option value="">Select Vendor</option>
                @foreach ($vendors as $vendorInfo)
                @php
                $select = '';
                if ($vendor) {
                if (in_array($vendorInfo->id, $vendor)) {
                $select = 'selected';
                } else {
                $select = '';
                }
                }
                @endphp
                <option value="{{ $vendorInfo->id }}" {{ $select }}>{{ $vendorInfo->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-6">
        <div class="row">
            <div class="col-md-6 form-group">
                <label for="from-date">From Date</label>
                <input type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'from_date' }}"
                       name="fromDate" placeholder="Select Date From" value="{{ date('d-m-Y', strtotime($fromDate)) }}"
                       readonly>
            </div>
            <div class="col-md-6 form-group">
                <label for="to-date">To Date</label>
                <input type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'to_date' }}"
                       name="toDate" placeholder="Select Date To" value="{{ date('d-m-Y', strtotime($toDate)) }}"
                       readonly>
            </div>
        </div>
    </div>
</div>
@endsection

@section('print_card_header')
@if ($vendor)
@foreach ($vendor as $vendorInfo)
<input type="hidden" name="vendor[]" value="{{ $vendorInfo }}">
@endforeach
@endif

<input type="hidden" name="fromDate" value="{{ $fromDate }}">
<input type="hidden" name="toDate" value="{{ $toDate }}">
<input type="hidden" id="print_value" name="print" value="{{ $print }}">
@endsection

@section('print_card_body')

<div class="table-responsive">
    @if (count($data) > 0)

    <table id="dataTable" name="paymentRecordTable" class="table table-bordered table-sm">
        <thead>
            <tr>
                <th colspan="6" style="text-align: right; font-weight: bold;">Previous Balance</th>
                <th style="text-align: right;">{{ $data['previousBalance'] }}</th>
            </tr>
            <tr>
                <th width="20px">Sl</th>
                <th width="100px">Date</th>
                <th width="100px">Remarks</th>
                <th width="100px">Purchase</th>
                <th width="100px">Payment</th>
                <th width="100px">Returns</th>
                <th width="100px">Balance</th>
            </tr>
        </thead>

        <tbody>
            @php
            $sl = 0;
            @endphp
            @foreach ($data['statements'] as $statement)
            <tr>
                <td>{{ $sl++ }}</td>
                <td>{{ $statement['date'] }}</td>
                <td>
                    @foreach ($statement['remarks'] as $value)
                    {{ $value['purchase'] }}
                    @if ($value['purchase'] && $value['payment'])
                    ,<br>
                    @endif
                    {{ $value['payment'] }}
                    @endforeach
                </td>
                <td>{{ $statement['lifting'] }}</td>
                <td>{{ $statement['payment'] }}</td>
                <td>{{ $statement['return'] }}</td>
                <td>{{ $statement['balance'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @endif
</div>
@endsection

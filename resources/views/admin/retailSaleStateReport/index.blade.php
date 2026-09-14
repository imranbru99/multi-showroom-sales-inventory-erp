@extends('admin.layouts.masterReport')

@section('search_card_body')
    <input type="hidden" name="print" value="print">
    <div class="row">
        <div class="col-md-3 form-group">
            <label for="from-date">Criteria</label>
            <select name="criteria" class="form-control chosen-select" id="criteria">
                <option value="employee">Employee</option>
            </select>
        </div>

        <div class="col-md-3 form-group">
            <label for="from-date">From Date</label>
            <input type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'from_date' }}"
                name="fromDate" value="{{ date('d-m-Y', strtotime($fromDate)) }}" placeholder=" Select Date From"
                autocomplete="off">
        </div>

        <div class="col-md-3 form-group">
            <label for="to-date">To Date</label>
            <input type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'to_date' }}" name="toDate"
                value="{{ date('d-m-Y', strtotime($toDate)) }}" placeholder="Select Date To" autocomplete="off">
        </div>
    </div>
@endsection

@section('print_card_header')
    <input type="hidden" name="fromDate" value="{{ $fromDate }}">
    <input type="hidden" name="toDate" value="{{ $toDate }}">
    <input type="hidden" id="print_value" name="print" value="{{ $print }}">
@endsection

@section('print_card_body')

    <table id="dataTable" name="paymentRecordTable" class="table table-bordered table-sm">
        <thead>
            <tr>
                <th width="20px">Sl</th>
                <th>Employee Name</th>
                <th width="90px">Sales Amount</th>
                <th width="105px">Sales Return Amount</th>
                <th width="105px">Actual Sales Amount</th>
                <th width="85px">Collection Amount</th>
                <th width="85px">Current Balance</th>
                <th width="85px">Sales Qty</th>
                <th width="85px">Sales Return Qty</th>
                <th width="85px">Actual Sales Qty</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($data as $d)
                @php
                    if ($d['total_collections'] != 0 && $d['actual_sales'] != 0) {
                        $average = ($d['total_collections'] / $d['actual_sales']) * 100;
                        $average = number_format($average, 2, '.', '');
                    } else {
                        $average = 0;
                    }
                    
                    if ($average == 0) {
                        continue;
                    }
                    
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $d['name'] }}</td>
                    <td align="right">{{ $d['total_sales'] }}</td>
                    <td align="right">{{ $d['total_returns'] }}</td>
                    <td align="right">{{ number_format($d['actual_sales'], 2, '.', '') }}</td>
                    <td align="right">{{ number_format($d['total_collections'], 2, '.', '') }}</td>
                    <td align="right">{{ number_format($d['current_balance'], 2, '.', '') }}</td>
                    <td align="right">{{ $d['sales_qty'] }}</td>
                    <td align="right">{{ $d['returns_qty'] }}</td>
                    <td align="right">{{ $d['actual_sales_qty'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

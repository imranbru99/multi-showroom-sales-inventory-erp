@extends('admin.layouts.masterReport')

@section('search_card_body')
<input type="hidden" name="print" value="print">
<div class="row">

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
    <div class="col-md-3 form-group">
        <label for="from-date">Criteria</label>
        <select name="criteria" class="form-control chosen-select" id="criteria">
            <option value="dealer" @if ($criteria == 'dealer') selected @endif>Dealer</option>
            <option value="d_internal" @if ($criteria == 'd_internal') selected @endif>Dealer (Internal)</option>
            <option value="d_external" @if ($criteria == 'd_external') selected @endif>Dealer (External)</option>
            <option value="employee" @if ($criteria == 'employee') selected @endif>Employee</option>
            <option value="employee_dealer" @if ($criteria == 'employee_dealer') selected @endif>Employee Dealer</option>
            <option value="dealer_type" @if ($criteria == 'dealer_type') selected @endif>Dealer Type</option>
        </select>
    </div>

    <div class="col-md-3 form-group" id="staffs_col">
        <label for="from-date">Staff</label>
        <select name="staff" class="form-control chosen-select">
            <option value=" "></option>
            @foreach ($staffs as $staff)

            @php
            $select = '';
            if ($staff->id == $staffId) {
            $select = 'selected';
            } else {
            $select = '';
            }
            @endphp
            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
            @endforeach
        </select>
    </div>
</div>
@endsection

@section('print_card_header')
<input type="hidden" name="fromDate" value="{{ $fromDate }}">
<input type="hidden" name="toDate" value="{{ $toDate }}">
<input type="hidden" id="print_value" name="print" value="{{ $print }}">
<input type="hidden" name="criteria" value="{{ $criteria }}">
<input type="hidden" name="staff" value="{{ $staffId }}">
@endsection

@section('print_card_body')

@php
$dealerCriterias = ['dealer', 'd_internal', 'd_external', 'employee_dealer'];
@endphp

@if (in_array($criteria, $dealerCriterias))

<table id="dataTable" name="paymentRecordTable" class="table table-bordered table-sm">
    <thead>
        <tr>
            <th width="20px">Sl</th>
            <th>Dealer Name</th>
            <th width="90px">Previous Balance</th>
            <th width="90px">Sales Amount</th>
            <th width="105px">Sales Return Amount</th>
            <th width="105px">Actual Sales Amount</th>
            <th width="85px">Collection Amount</th>
            <th width="85px">Current Balance</th>
            <th width="85px">Sales Qty</th>
            <th width="85px"> Sales Return Qty</th>
            <th width="85px">Actual Sales Qty</th>
            <th width="85px">Periodical Collec Ratio %</th>
            <th width="85px">Average Collec Ratio %</th>
        </tr>
    </thead>

    <tbody>
        @php
        $sl = 1;
        @endphp

        @foreach ($data as $d)

        @php
        if ($d['collection'] != 0 && $d['actualSale'] != 0) {

        $periodPercent = ($d['collection'] / ($d['prev_balance'] + $d['actualSale'])) * 100;
        $periodPercent = number_format($periodPercent, 2, '.', '');

        } else {
        $periodPercent = 0;
        }

        if($d['total_collection'] != 0 && $d['total_sales'] != 0){
        $averagePercent = ($d['total_collection'] / ($d['total_sales'] - $d['total_return'])) * 100;
        $averagePercent =  number_format($averagePercent, 2, '.', '');
        }else{
        $averagePercent = 0;
        }
	if($d['prev_balance'] == 0 && $d['balance'] == 0 && $d['collection'] == 0){
            continue;
        }        @endphp
        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ $d['dealerName'] }}</td>
            <td align="right">{{ $d['prev_balance'] }}</td>
            <td align="right">{{ $d['purchase'] }}</td>
            <td align="right">{{ $d['return'] }}</td>
            <td align="right">{{ $d['actualSale'] }}</td>
            <td align="right">{{ number_format($d['collection'], 2, '.', '') }}</td>
            <td align="right"> {{ round($d['balance'], 2) }}</td>
            <td align="right">{{ $d['qty'] }}</td>
            <td align="right">{{ $d['returnQty'] }}</td>
            <td align="right">{{ $d['actualQty'] }}</td>
            {{-- <td align="right">{{ $d['creditP'] }}%</td> --}}
            <td align="right"> {{ $periodPercent }}% </td>
            <td align="right"> {{ $averagePercent }}% </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endif

@if ($criteria == 'employee')

<table id="dataTable" name="paymentRecordTable" class="table table-bordered table-sm">
    <thead>
        <tr>
            <th width="20px">Sl</th>
            <th>Employee Name</th>
            <th width="90px">Previous Balance</th>
            <th width="90px">Sales Amount</th>
            <th width="105px">Sales Return Amount</th>
            <th width="105px">Actual Sales Amount</th>
            <th width="85px">Collection Amount</th>
            <th width="85px">Current Balance</th>
            <th width="85px">Sales Qty</th>
            <th width="85px">Sales Return Qty</th>
            <th width="85px">Actual Sales Qty</th>
            <th width="85px">Periodical Collec Ratio %</th>
            <th width="85px">Average Collec Ratio %</th>
        </tr>
    </thead>

    <tbody>
        @php
        $sl = 1;
        @endphp

        @foreach ($data as $d)
        @php
        $actualSale = $d['purchase'] - $d['return'];

        if ($d['collection'] != 0 && $d['actualSale'] != 0) {

        $periodPercent = ($d['collection'] / ($d['prev_balance'] + $d['actualSale'])) * 100;
        $periodPercent = number_format($periodPercent, 2, '.', '');

        } else {
        $periodPercent = 0;
        }

        if($d['total_collection'] != 0 && $d['total_purchase'] != 0){
        $averagePercent = ($d['total_collection'] / ($d['total_purchase'] - $d['total_return'])) * 100;
        $averagePercent =  number_format($averagePercent, 2, '.', '');
        }else{
        $averagePercent = 0;
        }

	$balance =$d['prev_balance'] + $actualSale - $d['collection'];

        if($d['prev_balance'] == 0 && $balance == 0 && $d['collection'] ==0){
            continue;
        }
        @endphp
        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ $d['employeeName'] }}</td>
            <td align="right">{{ $d['prev_balance'] }}</td>
            <td align="right">{{ $d['purchase'] }}</td>
            <td align="right">{{ $d['return'] }}</td>
            <td align="right">{{ $d['actualSale'] }}</td>
            <td align="right">{{ number_format($d['collection'], 2, '.', '') }}</td>
            <td align="right"> {{ round($d['prev_balance'] + $actualSale - $d['collection'], 2) }}</td>
            <td align="right">{{ $d['qty'] }}</td>
            <td align="right">{{ $d['returnQty'] }}</td>
            <td align="right">{{ $d['actualQty'] }}</td>
            {{-- <td align="right">{{ $d['creditP'] }}%</td> --}}
            <td align="right"> {{ $periodPercent }}% </td>
            <td align="right"> {{ $averagePercent }}% </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endif

@if ($criteria == 'dealer_type')

<table id="dataTable" name="paymentRecordTable" class="table table-bordered table-sm">
    <thead>
        <tr>
            <th width="20px">Sl</th>
            <th>Dealer Type</th>
            <th width="90px">Sales Amount</th>
            <th width="105px">Sales Return Amount</th>
            <th width="105px">Actual Sales Amount</th>
            <th width="85px">Collection Amount</th>
            <th width="85px">Outstanding</th>
            <th width="85px">Sales Qty</th>
            <th width="85px">Sales Return Qty</th>
            <th width="85px">Actual Sales Qty</th>
        </tr>
    </thead>

    <tbody>
        @php
        $sl = 1;
        @endphp

        @foreach ($data as $d)
        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ $d['employeeName'] }}</td>
            <td align="right">{{ $d['purchase'] }}</td>
            <td align="right">{{ $d['return'] }}</td>
            <td align="right">{{ $d['purchase'] - $d['return'] }}</td>
            <td align="right">{{ number_format($d['collection'], 2, '.', '') }}</td>
            <td align="right">{{ number_format($d['outstanding'], 2, '.', '') }}</td>
            <td align="right">{{ $d['qty'] }}</td>
            <td align="right">{{ $d['returnQty'] }}</td>
            <td align="right">{{ $d['qty'] - $d['returnQty'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

@endif

@endsection


@section('custom-js')
<script>
    $(function () {

        $('#staffs_col').hide();

        let selectedCriteria = $('#criteria').val();

        if (selectedCriteria == "employee_dealer") {
            $('#staffs_col').show();
        } else {
            $('#staffs_col').hide();
        }

        $('#criteria').change(function (e) {
            e.preventDefault();

            let selectedCriteria = $(this).val();

            if (selectedCriteria == "employee_dealer") {
                $('#staffs_col').show();
            } else {
                $('#staffs_col').hide();
            }

        });


    });
</script>
@endsection

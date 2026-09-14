@extends('admin.layouts.masterReport')

@section('custom_css')
<style>
    th {
        background: #00c292;
        font-weight: bold !important;
        padding: 5px;
        font-size: 11px;
    }

</style>
@endsection

@section('search_card_body')
<input type="hidden" name="print" value="print">
<div class="row">
    <div class="col-md-6">
        <label for="dealer">Dealer</label>
        <div class="form-group">
            <select class="form-control chosen-select" id="dealer" name="dealer[]" required>
                @foreach ($dealers as $dealerInfo)
                <?php
                $select = '';
                if ($dealer) {
                    if (in_array($dealerInfo->id, $dealer)) {
                        $select = 'selected';
                    } else {
                        $select = '';
                    }
                }
                ?>
                <option value="{{ $dealerInfo->id }}" {{ $select }}>{{ $dealerInfo->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-6">
        <div class="row">
            <div class="col-md-6 form-group">
                <label for="from-date">From Date</label>
                <input type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'from_date' }}"
                       name="fromDate" value="{{ $fromDatee }}" placeholder="Select Date From">
            </div>
            <div class="col-md-6 form-group">
                <label for="to-date">To Date</label>
                <input type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'to_date' }}"
                       name="toDate" value="{{ $toDatee }}" placeholder="Select Date To">
            </div>
        </div>
    </div>
</div>
@endsection

@section('print_card_header')
@if ($dealer)
@foreach ($dealer as $dealerInfo)
<input type="hidden" name="dealer[]" value="{{ $dealerInfo }}">
@endforeach
@endif

<input type="hidden" name="fromDate" value="{{ $fromDate }}">
<input type="hidden" name="toDate" value="{{ $toDate }}">
<input type="hidden" id="print_value" name="print" value="{{ $print }}">
@endsection

@section('print_card_body')
<p class="text-right">Previous Amount: {{ $previousBalance }}</p>
<table id="dataTable"name="paymentRecordTable" class="table table-bordered table-sm">
    <thead>
        <tr>
            <th width="10px">SL</th>
            <th width="60px">Date</th>
            <th width="20px">Invoice</th>
            <th width="80px">Particulars</th>
            <th width="80px">Advance</th>
            <th width="80px">Sales</th>
            <th width="90px">Collection</th>
            <th width="90px">Return</th>
            <th width="90px">Discount</th>
            <th width="80px">Balance</th>
        </tr>
    </thead>

    <tbody>
        @php
        $sl = 1;
        $balance = 0;
        @endphp
        @foreach ($data as $d)
        <?php
        if ($loop->iteration == 1) {
            if ($d['remark'] == 'Collection against advance') {
                $balance += $previousBalance + $d['sale'] - ($d['advance'] + $d['return'] + $d['discount']);
            } else {
                $balance += $previousBalance + $d['sale'] - ($d['advance'] + $d['collection'] + $d['return'] + $d['discount']);
            }
        } else {
            if ($d['remark'] == 'Collection against advance') {
                $balance += $d['sale'] - ($d['advance'] + $d['return'] + $d['discount']);
            } else {
                $balance += $d['sale'] - ($d['advance'] + $d['collection'] + $d['return'] + $d['discount']);
            }
        }
        ?>
        <tr>
            <td></td>
            <td>{{ date('d-m-Y', strtotime($d['date'])) }}</td>
            <td>{{ $d['invoice_no'] }}</td>
            <td>{{ $d['remark'] }}</td>
            <td align='right'>{{ number_format($d['advance'], 2, '.', '') }}</td>
            <td align='right'>{{ number_format($d['sale'], 2, '.', '') }}</td>
            <td align='right'>{{ number_format($d['collection'], 2, '.', '') }}</td>
            <td align='right'>{{ number_format($d['return'], 2, '.', '') }}</td>
            <td align='right'>{{ number_format($d['discount'], 2, '.', '') }}</td>
            <td align='right'>{{ number_format($balance, 2, '.', '') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection

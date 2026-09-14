@extends('admin.layouts.masterReport')

@section('search_card_body')
<div class="row">
    <div class="col-md-12 form-group">
        <input class="form-control" type="hidden" name="print" value="print">
    </div>
</div>

<div class="row">
    <div class="col-md-6 form-group">
        <label for="from-date">From Date</label>
        <input type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'from_date' }}"
               name="fromDate" value="{{ date('d-m-Y', strtotime($fromDate)) }}" placeholder="Select Date From">
    </div>
    <div class="col-md-6 form-group">
        <label for="to-date">To Date</label>
        <input type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'to_date' }}" name="toDate"
               value="{{ date('d-m-Y', strtotime($toDate)) }}" placeholder="Select Date To">
    </div>
</div>
@endsection

@section('print_card_header')
<input type="hidden" name="fromDate" value="{{ $fromDate }}">
<input type="hidden" name="toDate" value="{{ $toDate }}">

<input type="hidden" id="print_value" name="print" value="{{ $print }}">
@endsection

@section('print_card_body')
<table id="dataTable" class="table table-bordered table-sm">
    <thead>
        <tr>
            <th>SL</th>
            <th class="text-center">Name of Assets</th>
            <th class="text-center">Opening Balance</th>
            <th class="text-center">Addition during the Year</th>
            <th class="text-center">Cost of Assets</th>
            <th class="text-center">Rate of Depreciation</th>
            <th class="text-center">Accumulated Depreciation</th>
            <th class="text-right">Net Value</th>
        </tr>
    </thead>
    <tbody>
        <?php
        ?>
        @foreach ($fixAssets as $fix)
        <?php
        $totalAmount = $fix['previous'] + $fix['amount'];
        ?>
        <tr>
            <td></td>
            <td>{{ $fix['name'] }}</td>
            <td class="text-right">{{ number_format($fix['previous'], 2, '.', '') }}</td>
            <td class="text-right">{{ number_format($fix['amount'], 2, '.', '') }}</td>
            <td class="text-right">{{ number_format($totalAmount , 2, '.', '') }}</td>
            <td>
                @if($fix['name'] == 'Furniture & Fixtures')
                15%
                @else
                20%
                @endif
            </td>
            <td>
                <?php
                if ($fix['name'] == 'Furniture & Fixtures') {
                    $accu = $totalAmount * 0.15;
                } else {
                    $accu = $totalAmount * 0.20;
                }
                ?>
                {{ number_format($accu, 2, '.', '') }}
            </td>
            <td>{{ number_format(($totalAmount - $accu), 2, '.', '') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection

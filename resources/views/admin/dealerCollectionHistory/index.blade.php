@extends('admin.layouts.masterReport')

@section('search_card_body')
<input type="hidden" name="print" value="print">
<div class="row">

    <div class="col-md-3">
        <label for="employee">Employee</label>
        <div class="form-group">
            <select class="form-control chosen-select" id="employee" name="employee[]" multiple>
                @foreach ($employees as $e)
                <option value="{{ $e->id }}" 
                        @if ($employee) 
                        @if (in_array($e->id, $employee))
                    selected
                    @endif
                    @endif
                    >{{ $e->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-3">
        <label for="dealer">Dealer</label>
        <div class="form-group">
            <select class="form-control chosen-select" id="dealer" name="dealer[]" multiple>
                @foreach ($dealers as $dealerInfo)
                <option value="{{ $dealerInfo->id }}" @if ($dealer) @if (in_array($dealerInfo->id, $dealer))
                    selected
                    @endif
                    @endif
                    >{{ $dealerInfo->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-3 form-group">
        <label for="from-date">From Date</label>
        <input type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'from_date' }}"
               name="fromDate" value="{{ date('d-m-Y', strtotime($fromDate)) }}" placeholder="Select Date From">
    </div>
    <div class="col-md-3 form-group">
        <label for="to-date">To Date</label>
        <input type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'to_date' }}" name="toDate"
               value="{{ date('d-m-Y', strtotime($toDate)) }}" placeholder="Select Date To">
    </div>
</div>
@endsection

@section('print_card_header')
@if ($dealer)
@foreach ($dealer as $dealerInfo)
<input type="hidden" name="dealer[]" value="{{ $dealerInfo }}">
@endforeach
@endif


@if ($employee)
@foreach ($employee as $eInfo)
<input type="hidden" name="employee[]" value="{{ $eInfo }}">
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
            <th width="20px">Sl</th>
            <th width="80px">Date</th>
            <th>Dealer Name</th>
            <th>Employee Name</th>
            <th>Area</th>
            <th width="150px">Payment No.</th>
            <th width="90px">Pay Mode</th>
            <th width="80px">Amount</th>
        </tr>
    </thead>

    <tbody>
        @php
        $sl = 1;
        @endphp

        @foreach ($data as $d)
        <tr>
            <td>{{ $sl++ }}</td>
            <td> {{ $d['date'] }}</td>
            <td>{{ $d['dealerName'] }}</td>
            <td>{{ $d['sale_by'] }}</td>
            <td></td>
            <td>{{ $d['paymentNo'] }}</td>
            <td>
                <?php
                if (!empty($d['bankId'])) {
                    $bankname = \App\CoaSetup::where('id', $d['bankId'])->first();
                    $type = $bankname->head_name;
                } else {
                    $type = $d['paymentType'];
                }
                ?>
                
                {{ $type }}

            </td>
            <td align="right">{{ $d['amount'] }}</td>
        </tr>
        @endforeach

    </tbody>
</table>
@endsection

{{-- @section('custom-js')
    <script>
        $(document).ready(function() {
            var table = $('#tdb').DataTable({
                "order": [
                    [2, "asc"]
                ],
            });
        });

    </script>
@endsection --}}

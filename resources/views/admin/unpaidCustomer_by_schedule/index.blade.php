@extends('admin.layouts.masterReport')

@section('search_card_body')
<input type="hidden" name="print" value="print">
<div class="row d-flex justify-content-center">
    <div class="col-md-4">
        <label for="days">Days</label>
        <input type="number" class="form-control" name="days" value="{{ @$days }}" placeholder="Days" required>
    </div>
    <div class="col-md-4">
        <label for="days">Sales By</label>
        <select name="sales_by" class="form-control chosen-select">
            <option value="">Select Employee</option>
            @foreach($staffs as $staff)
            <option value="{{ $staff->id }}" @if($staff->id == $sales_by) selected @endif>{{ $staff->name }}</option>
            @endforeach
        </select>
    </div>
</div>
@endsection

@section('print_card_header')
@if (@$days)
<input type="hidden" name="days" value="{{ @$days }}">
@endif

@if (@$days)
<input type="hidden" name="sales_by" value="{{ @$sales_by }}">
@endif

<input type="hidden" id="print_value" name="print" value="{{ $print }}">
@endsection

@section('print_card_body')
<table id="dataTable" name="unpaidCustomers" class="table table-bordered table-sm">
    <thead>
        <tr>
            <th>SL</th>
            <th>Customer Name</th>
            <th>Account No</th>
            <th>Contact No</th>
            <th>Sales By</th>
        </tr>
    </thead>

    <tbody>
        @foreach($customers as $customer)
        <tr>
            <td></td>
            <td>{{ $customer->name }}</td>
            <td>{{ $customer->code }}</td>
            <td>{{ $customer->phone_no }}</td>
            <td>{{ @$customer->agreement->staff->name }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection

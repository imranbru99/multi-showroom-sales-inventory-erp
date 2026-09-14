@extends('admin.layouts.masterReport')

@section('search_card_body')
    <input type="hidden" name="print" value="print">
    <div class="row">
        <div class="col-md-4">
            <label for="group">Commission Type</label>
            <div class="form-group">
                <select class="form-control chosen-select" name="type">
                    <option value="1" @if ($type == 1) selected @endif>With Discount</option>
                    <option value="2" @if ($type == 2) selected @endif>Without Discount</option>
                </select>
            </div>
        </div>
        <div class="col-md-5">
            <label for="group">Employee</label>
            <div class="form-group">
                <select class="form-control chosen-select" name="employee">
                    <option value="">Select Employee</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}" @if ($employee->id == $employeeId) selected @endif>
                            {{ $employee->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3 form-group">
            <label for="month">Month</label>
            <input type="month" class="form-control" name="month" placeholder="Select Date From"
                value="{{ !empty($month) ? @$month : date('Y-m') }}">
        </div>
    </div>
@endsection

@section('print_card_header')
    <input type="hidden" name="print" value="{{ $print }}">
    <input type="hidden" name="month" value="{{ $month }}">
    <input type="hidden" name="employee" value="{{ $employeeId }}">
    <input type="hidden" name="type" value="{{ $type }}">
@endsection

@section('print_card_body')
    <table id="dataTable" class="table table-bordered table-sm">
        <thead>
            <tr>
                <th width="20px">Sl</th>
                <th>Invoice No</th>
                <th>Date</th>
                <th>A/C No</th>
                <th>Client Name</th>
                <th>Phone No</th>
                <th>Address</th>
                <th>Refference By</th>
                <th>Purchase Commission</th>
                <th>Cash Price</th>
                <th>Sales Price</th>
                <th>Profit/Loss (%)</th>
                @if ($type == 1)
                    <th>With Discount /Commission (%)</th>
                    <th>With Discount /Commission</th>
                @else
                    <th>Without Discount /Commission (%)</th>
                    <th>Without Discount /Commission</th>
                @endif
                <th>Employee Commission (2%)</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($cashData as $cash)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $cash['invoice_no'] }}</td>
                    <td>{{ $cash['date'] }}</td>
                    <td>{{ $cash['ac_no'] }}</td>
                    <td>{{ $cash['client_name'] }}</td>
                    <td>{{ $cash['phone_no'] }}</td>
                    <td>{{ $cash['address'] }}</td>
                    <td>{{ $cash['reference_by'] }}</td>
                    <td>{{ $cash['purchase_com'] }}</td>
                    <td>{{ $cash['cash_price'] }}</td>
                    <td>{{ $cash['sales_price'] }}</td>
                    <td>{{ $cash['profit_loss'] }}%</td>
                    <td>{{ $cash['commission_percent'] }}%</td>
                    <td>{{ $cash['commission_amount'] }}</td>
                    <td>{{ $cash['employee_commission'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

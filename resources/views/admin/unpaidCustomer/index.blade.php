@extends('admin.layouts.masterReport')

@section('search_card_body')
    <input type="hidden" name="print" value="print">
    <div class="row d-flex justify-content-center">
        <div class="col-md-3 form-group">
            <label for="from-date">From Date</label>
            <input type="text" class="form-control datepicker" name="fromDate"
                value="{{ date('d-m-Y', strtotime($fromDate)) }}" autocomplete="off">
        </div>

        <div class="col-md-3 form-group">
            <label for="to-date">To Date</label>
            <input type="text" class="form-control datepicker" value="{{ date('d-m-Y', strtotime($toDate)) }}"
                autocomplete="off">
        </div>
        <div class="col-md-3">
            <label for="project">Project</label>
            <div class="form-group">
                <select name="project" class="form-control" required>
                    <option value="">Select Project</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}" @if($project->id == $project_id) selected @endif>{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <label for="days">Sales By</label>
            <select name="sales_by" class="form-control chosen-select">
                <option value="">Select Employee</option>
                @foreach ($staffs as $staff)
                    <option value="{{ $staff->id }}" @if ($staff->id == $sales_by) selected @endif>{{ $staff->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
@endsection

@section('print_card_header')
    @if (@$fromDate)
        <input type="hidden" name="fromDate" value="{{ @$fromDate }}">
    @endif

    @if (@$toDate)
        <input type="hidden" name="toDate" value="{{ @$toDate }}">
    @endif

    @if (@$sales_by)
        <input type="hidden" name="sales_by" value="{{ @$sales_by }}">
    @endif

    <input type="hidden" id="project" name="project" value="{{ $project_id }}">
    <input type="hidden" id="print_value" name="print" value="{{ $print }}">
@endsection

@section('print_card_body')
    <table id="dataTable" name="unpaidCustomers" class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>SL</th>
                <th>Last Collection Date</th>
                <th>Account No</th>
                <th>Name</th>
                <th>Contact No</th>
                <th>Address</th>
                <th>Collector By</th>
                <th>Duration</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($customers as $customer)
                <tr>
                    <td></td>
                    <td>{{ date('d-m-Y', strtotime($customer->installment_collection_date)) }}</td>
                    <td>{{ $customer->code }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->phone_no }}</td>
                    <td>{{ $customer->present_address }}</td>
                    <td>{{ $customer->collector_by }}</td>
                    <td>{{ $customer->duration }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

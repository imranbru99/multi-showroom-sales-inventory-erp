@extends('admin.layouts.masterReport')

@section('search_card_body')
<input type="hidden" name="print" value="print">
<div class="row d-flex justify-content-center">
    <div class="col-md-4">
        <label for="project">Project</label>
        <select name="project" class="form-control">
            <option value="">Select Project</option>
            @foreach ($projects as $project)
            <option value="{{ $project->id }}" @if ($project->id == $project_id) selected @endif>{{ $project->name }}
            </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
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

@if (@$project_id)
<input type="hidden" name="project" value="{{ @$project_id }}">
@endif

@if (@$sales_by)
<input type="hidden" name="sales_by" value="{{ @$sales_by }}">
@endif

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
            <th>Outstanding</th>
            <th>Duration</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($customers as $customer)

        @php
        if($customer->outstanding < 1){ 
            continue; 
        } 
        @endphp 
        <tr>
            <td></td>
            <td>{{ date('d-m-Y', strtotime($customer->date)) }}</td>
            <td>{{ @$customer->customerCode }}</td>
            <td>{{ @$customer->customerName }}</td>
            <td>{{ @$customer->customerPhone }}</td>
            <td>{{ @$customer->customerAddress }}</td>
            <td>{{ @$customer->staffName }}</td>
            <td>{{ $customer->outstanding }}</td>
            <td>{{ $customer->duration }}</td>
            </tr>
            @endforeach
    </tbody>
</table>
@endsection

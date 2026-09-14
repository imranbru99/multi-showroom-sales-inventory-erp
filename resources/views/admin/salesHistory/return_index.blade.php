@extends('admin.layouts.master')

@section('content')
    <form class="form-horizontal" id="search" action="{{ route($searchFormLink) }}" method="POST"
        enctype="multipart/form-data">
        {{ csrf_field() }}

        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-5">
                        <h4 class="card-title">{{ $title }}</h4>
                    </div>
                    <div class="col-md-7 text-right">

                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <input type="hidden" name="print" value="print">
                    </div>
                </div>

                <div class="row">

                    <div class="col-md-3 form-group">
                        <label for="from-date">From Date</label>
                        <input type="text" class="form-control datepicker" id="datepicker" name="fromDate"
                            placeholder="Select Date From" value="{{ date('d-m-Y', strtotime($fromDate)) }}" readonly>
                    </div>

                    <div class="col-md-3 form-group">
                        <label for="to-date">To Date</label>
                        <input type="text" class="form-control datepicker" id="datepicker" name="toDate"
                            placeholder="Select Date To" value="{{ date('d-m-Y', strtotime($toDate)) }}" readonly>
                    </div>

                    <div class="col-md-3 form-group">
                        <label for="to-date">Customer</label>
                        <select name="customer" class="form-control chosen-select">
                            <option value="">Select Customer</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}"
                                    @if ($customerId == $customer->id)
                                        selected
                                    @endif
                                    >{{ $customer->name }} - {{ $customer->code }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 form-group">
                        <label for="to-date">Sales By</label>
                        <select name="staffId" class="form-control chosen-select">
                            <option value="">Select Sale By</option>
                            @foreach ($staffs as $staff)
                                <option value="{{ $staff->id }}"
                                    @if ($staffId == $staff->id)
                                        selected
                                    @endif
                                    >{{ $staff->name }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="submit" class="btn btn-outline-info btn-lg waves-effect" name="submit"
                                value="search"><i class="fa fa-search"></i> Search</button>
                            <button type="submit" class="btn btn-outline-info btn-lg waves-effect" name="submit"
                                value="print"><i class="fa fa-search"></i> Print</button>
                        </div>
                    </div>
                </div>
            </div>
    </form>


    <div class="card" style="margin-bottom: 0px;">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title">Searched Report Sales Return History</h4>
                </div>
                <div class="col-md-6 text-right">
                    <form class="form-horizontal" id="print" action="{{ route($printFormLink) }}" target="_blank"
                        method="post" enctype="multipart/form-data">

                        {{ csrf_field() }}

                        <input type="hidden" name="fromDate" value="{{ $fromDate }}">
                        <input type="hidden" name="toDate" value="{{ $toDate }}">


                </div>
            </div>
        </div>

        <div class="card-body">
            <table id="dataTable" name="salesHistory" class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th width="20px">Sl</th>
                        <th width="80px">Date</th>
                        <th>Account No</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Memo No</th>
                        <th>Product</th>
                        <th>Model</th>
                        <th>Serial</th>
                        <th>Qty</th>
                        <th>Price</th>
                    </tr>
                </thead>

                <tbody>
                    @php
                        $sl = 1;
                    @endphp
                    @foreach ($salesHistory as $salesRecord)
                    @foreach ($salesRecord->products as $product)
                        <tr>
                            <td></td>
                            <td>{{ date('d-m-Y', strtotime($salesRecord->date)) }}</td>
                            <td>{{ $salesRecord->customer->code }}</td>
                            <td>{{ $salesRecord->customer->name }}</td>
                            <td>{{ $salesRecord->customer->phone_no }}</td>
                            <td>{{ $salesRecord->invoice_no }}</td>
                            <td>{{ $product->product->name }}</td>
                            <td>{{ $product->product->model_no }}</td>
                            <td>{{ $product->product_serial }}</td>
                            <td>1</td>
                            <td>{{ $product->cash_price }}</td>
                        </tr>
                    @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@extends('admin.layouts.masterPrint')

@section('custome-css')
    <style>
        #report-table td,
        #report-table th {
            border: 1px solid #ddd;
        }

        #report-table tbody td {
            /*height: 500px;*/
            vertical-align: top;
        }

        #invoice-table {
            width: 100%;
            border-collapse: collapse;
        }

        #invoice-table td {
            width: 50%;
            border: 0px solid black;
        }

        #invoice-header {
            background-color: lightgray;
            width: 100%;
            padding: 5px;
            text-align: center;
            font-weight: bold;
            font-size: 20px;
        }

        #invoice-footer {
            background-color: lightgray;
            width: 100%;
        }

        #invoice-footer th {
            border: 0px solid white;
            padding: 5px;
            text-align: right;
            font-size: 14px;
            width: 180px;
        }

        #invoice-footer td {
            border: 1px solid black;
            padding: 5px;
        }

    </style>
@endsection

@section('content')
    <table id="invoice-header">
        <tr>
            <td>Customer Not Audit List</td>
        </tr>
    </table>

    <div id="pad-bottom"></div>

    <div id="pad-bottom"></div>

    <table id="report-table">
        <thead>
            <tr>
                <th width="20px">SL#</th>
                <th style="text-align: left;">Name</th>
                <th width="80px">Code</th>
                <th width="80px">Phone</th>
                <th width="70px">Address</th>
                <th width="100px">Remarks</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($customers as $customer)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->code }}</td>
                    <td>{{ $customer->phone_no }}</td>
                    <td>{{ $customer->present_address }}</td>
                    <td>{{ $customer->remarks }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div id="pad-bottom"></div>

@endsection

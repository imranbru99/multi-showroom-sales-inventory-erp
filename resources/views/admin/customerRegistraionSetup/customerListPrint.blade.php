@extends('admin.layouts.masterPrint')

@section('content')
    <table id="report-table">
    	<caption>Customer List</caption>
        <thead>
            <tr>
                <th width="20px">SL</th>
                <th width="120px">Customer Code</th>
                <th>Customer Name</th>
                <th width="100px">Phone No</th>
                <th>Address</th>
                <th>Reference</th>

            </tr>
        </thead>
        <tbody>
            @php
                $i = 0;
            @endphp
            @foreach ($customerLists as $customerList)
            @php
                $i++;
            @endphp
                <tr>
                    <td>{{$i}}</td>
                    <td>{{$customerList->code}}</td>
                    <td>{{$customerList->name}}</td>
                    <td>{{$customerList->phone_no}}</td>
                    <td>{{$customerList->present_address}}</td>
                    <td>{{@$customerList->referenceName}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

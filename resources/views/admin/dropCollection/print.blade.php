@extends('admin.layouts.masterPrint')

@section('content')
    <table  id="report-table">
        <caption>{{@$title}}</caption>
        <thead>
            <tr>
                <th width="20px">Sl</th>
                <th width="100px">Invoice No</th>
                <th width="180px">Client Name</th>
                <th width="150px">Phone No</th>
                <th>Product</th>
                <th width="80px">Due Qty</th>
                <th width="115px">Total Amount</th>
            </tr>
        </thead>

        <tbody>
            @php
                $sl = 1;
                $totalDueInstallment = 0;                                       
                $totalInstallmentAmount = 0;  
            @endphp
             @foreach ($dropCollectionList as $dropCollection)
             @php
                 $totalDueInstallment = $totalDueInstallment + $dropCollection->totalDueInstallment;
                $totalInstallmentAmount = $totalInstallmentAmount + $dropCollection->totalInstallmentAmount;
             @endphp
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ $dropCollection->invoiceNo }}</td>
                    <td>{{ $dropCollection->customerName }}</td>
                    <td>{{ $dropCollection->phoneNo }}</td>
                    <td>{{ $dropCollection->productName }}</td>
                    <td style="text-align: center;">{{ $dropCollection->totalDueInstallment }}</td>
                    <td style="text-align: right;">{{ $dropCollection->totalInstallmentAmount }}</td>
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <th colspan="5">Total</th>
                <th style="text-align: center;">{{ $totalDueInstallment }}</th>
                <th style="text-align: right;">{{ $totalInstallmentAmount }}</th>
            </tr>
        </tfoot>
    </table>
@endsection

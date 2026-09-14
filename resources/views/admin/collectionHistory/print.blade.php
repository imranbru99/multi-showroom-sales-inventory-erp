@extends('admin.layouts.masterPrint')

@section('content')
@php
    use App\InstallmentCollection;
    use App\CustomerRegistrationSetup;
    use App\Product;
@endphp
    <table  id="report-table">
        <caption>{{@$title}}</caption>
        <thead>
            <tr>
                <th width="20px">Sl</th>
                <th width="100px">Invoice No</th>
                <th width="150px">Client Name</th>
                <th width="90px">Phone No</th>
                <th width="130px">Product</th>
                <th width="80px">Collector</th>
                <th width="115px" style="text-align: center;">Collection Date</th>
                <th width="130px" style="text-align: right;">Collection Amount</th>
            </tr>
        </thead>

        <tbody>
            @php
                $sl = 1;                                     
                $totalCollectionAmount = 0;  
            @endphp
             @foreach ($collectionHistoryList as $collectionHistory)
            @php
                $totalCollectionAmount = $totalCollectionAmount + $collectionHistory->installment_schedule_amount;
                $installmentCollection = InstallmentCollection::where('id',$collectionHistory->installment_collection_id)->first();
                $customer = CustomerRegistrationSetup::where('id',$installmentCollection->customer_id)->first();
                $product = Product::where('id',$installmentCollection->product_id)->first();
                $collectionDate = Date('d-m-Y', strtotime($collectionHistory->installment_collection_date));
            @endphp
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ $collectionHistory->invoice_no }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->phone_no }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $collectionHistory->installmentCollectorName }}</td>
                    <td style="text-align: center;">{{ $collectionDate }}</td>
                    <td style="text-align: right;">{{ $collectionHistory->installment_schedule_amount }}</td>
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <th colspan="7">Total</th>
                <th style="text-align: right;">{{ $totalCollectionAmount }}</th>
            </tr>
        </tfoot>
    </table>
@endsection

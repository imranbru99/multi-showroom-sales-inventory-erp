@extends('admin.layouts.masterPrint')

@section('content')
<table id="report-header">
    <tr>
        <td>Product List Report</td>
    </tr>
</table>

<div id="pad-bottom"></div>

<table id="report-table">
    <thead>
        <tr>
            <th>Category Name</th>
            <th>Product Name</th>
            <th>Model</th>
            <th>Price</th>
            <th>MRP Price</th>
            <th>Higher Price</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($productLists as $productList)
        <tr>
            <td>{{ $productList->categoryName }}</td>
            <td>{{ $productList->productName }}</td>
            <td>{{ $productList->productModel }}</td>
            <td align="right">{{ number_format($productList->price, 2, '.', '') }}</td>
            <td align="right">{{ number_format($productList->mrpPrice, 2, '.', '') }}</td>
            <td align="right">{{ number_format($productList->hairePrice, 2, '.', '') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection

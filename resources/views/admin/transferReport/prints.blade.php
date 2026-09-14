@extends('admin.layouts.masterPrint')

@section('content')
    <div id="pad-bottom"></div>

    <table id="report-table">
        <thead class="thead-light">
            <tr>
                <th width="20px">Sl</th>
                <th>Model No</th>
                <th>Lifting</th>
                <th>Transfer</th>
                <th>In Stock</th>
            </tr>
        </thead>

        <tbody>
            <?php $sl = 1; ?>
            @foreach ($products as $product)
                @php
                    // $qty = $product['lifting'] - $product['transfer'];
                    // if ($qty >= 0) {
                    //     continue;
                    // }
                @endphp
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ $product['productModel'] }}</td>
                    <td>{{ $product['lifting'] }}</td>
                    <td>{{ $product['transfer'] }}</td>
                    <td>{{ $product['lifting'] - $product['transfer'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

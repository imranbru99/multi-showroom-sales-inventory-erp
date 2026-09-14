@extends('admin.layouts.masterReport')
@section('search_card_body')
<input type="hidden" name="print" value="print">
<div class="row">
    <div class="col-md-3">
        <label for="productType">Product Type</label>
        <div class="form-group">
            <select class="form-control chosen-select" id="productType" name="productType" required>
                <option value="">Select Product Type</option>
                @foreach ($productTypes as $key => $value)
                <option value="{{ $key }}" @if($key == $type) selected @endif>{{ $value }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <label for="store">Store</label>
        <div class="form-group">
            <select class="form-control" id="store" name="store">
                <option value="">Select Store</option>
                @foreach ($stores as $store)
                <option value="{{ $store->id }}" @if($store->id == $storeId) selected @endif>{{ $store->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <label for="product-category">Category</label>
        <div class="form-group">
            <select class="form-control chosen-select" id="productCategory" name="productCategory[]" multiple>

                <?php
                $select = '';
                if ($productCategory) {
                    if ($productCategory[0] == 'All') {
                        $select = 'selected';
                    } else {
                        $select = '';
                    }
                }
                ?>

                <option value="All" {{ $select }}>All </option>
                @foreach ($categories as $category)
                <?php
                $select = '';
                if ($productCategory) {
                    if (in_array($category->id, $productCategory)) {
                        $select = 'selected';
                    } else {
                        $select = '';
                    }
                }
                ?>
                <option value="{{ $category->id }}" {{ $select }}>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-12">
        <label for="product">Product</label>
        <div class="form-group">
            <select class="form-control chosen-select" id="product" name="product[]" multiple>

                <?php
                $select = '';
                if ($product) {
                    if ($product[0] == 'All') {
                        $select = 'selected';
                    } else {
                        $select = '';
                    }
                }
                ?>

                <option value="All" {{ $select }}>All</option>
                @foreach ($products as $productInfo)
                <?php
                $select = '';
                if ($product) {
                    if (in_array($productInfo->id, $product)) {
                        $select = 'selected';
                    } else {
                        $select = '';
                    }
                }
                ?>
                <option value="{{ $productInfo->id }}" {{ $select }}>{{ $productInfo->name }}
                    ({{ $productInfo->model_no }})</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
@endsection

@section('print_card_header')
@if ($productCategory)
@foreach ($productCategory as $productCategoryInfo)
<input type="hidden" name="productCategory[]" value="{{ $productCategoryInfo }}">
@endforeach
@endif

@if ($product)
@foreach ($product as $productInfo)
<input type="hidden" name="product[]" value="{{ $productInfo }}">
@endforeach
@endif
<input type="hidden" id="print_value" name="productType" value="{{ $type }}">
<input type="hidden" id="print_value" name="store" value="{{ $storeId }}">
<input type="hidden" id="print_value" name="print" value="{{ $print }}">
@endsection

@section('print_card_body')
<table id="dataTable" name="productList" class="table table-bordered table-sm">
    <thead>
        <tr>
            <th width="20px" rowspan="2" >SL#</th>
            <th rowspan="2" width="100px">Category</th>
            <th rowspan="2" width="100px">Product Name</th>
            <th rowspan="2" width="120px">Model No</th>
            <th rowspan="2"  width="80px">Available Quantity</th>
            <th colspan="2" class="text-center">Purchase value</th>
            <th colspan="2"  class="text-center">Sales value</th>
        </tr>
        <tr>
            <th class="text-center" width="100px">Cost</th>
            <th class="text-center" width="100px">Value</th>
            <th class="text-center" width="100px">Rate</th>
            <th class="text-center" width="100px">Value</th>
        </tr>
    </thead>
    <tbody>
        @php
        $sl = 1;
        @endphp
        @foreach ($stockOutReports as $stockOutReport)
        <?php
        $product = DB::table('tbl_products')->where('id', $stockOutReport['productId'])->first();
        if ($stockOutReport['InStock'] <= 0) {
            continue;
        }
        ?>
        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ $stockOutReport['categoryName'] }}</td>
            <td>{{ $stockOutReport['productName'] }}</td>
            <td>{{ $stockOutReport['productModel'] }}</td>
            <td style="text-align: right;">{{ $stockOutReport['InStock'] }}</td>
            <td style="text-align: right;">{{ $product->price }}</td>
            <td style="text-align: right;">{{ number_format($stockOutReport['InStock'] * $product->price, 2, '.', '')}}</td>
            <td style="text-align: right;">{{ $product->mrp_price}}</td>
            <td style="text-align: right;">{{ number_format($stockOutReport['InStock'] * $product->mrp_price, 2, '.', '')}}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection

@section('custom-js')
<script>
    $('#productType').change(function () {
        var type = $(this).val();
        $('#product option').remove();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            url: "{{ route('product.type') }}",
            data: {
                type: type,
            },
            success: function (response) {
                response.forEach(function (item, index) {
                    var option = `<option value="${item.id}">${item.name} - (${item.model_no})</option>`;
                    $('#product').append(option);
                    $('.chosen-select').chosen();
                    $('.chosen-select').trigger("chosen:updated");
                });
            }
        });
        $('.chosen-select').chosen();
        $('.chosen-select').trigger("chosen:updated");
    });
</script>

@endsection

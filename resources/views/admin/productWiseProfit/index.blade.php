@extends('admin.layouts.masterReport')

@section('custom_css')
<style>
    th {
        background: #00c292;
        font-weight: bold !important;
        padding: 5px;
        font-size: 11px;
    }

</style>
@endsection

@section('search_card_body')
<input type="hidden" name="print" value="print">
<div class="row">
    <div class="col-md-4">
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
    <div class="col-md-4 form-group">
        <label for="from-date">From Date</label>
        <input type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'from_date' }}"
               name="fromDate" value="{{ date('d-m-Y', strtotime($fromDate)) }}" placeholder="Select Date From">
    </div>
    <div class="col-md-4 form-group">
        <label for="to-date">To Date</label>
        <input type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'to_date' }}"
               name="toDate" value="{{ date('d-m-Y', strtotime($toDate)) }}" placeholder="Select Date To">
    </div>
    <div class="col-md-6">
        <label for="dealer">Categories</label>
        <div class="form-group">
            <select class="form-control chosen-select" id="category_id" name="category_id[]" multiple>
                @foreach ($categories as $category)
                <?php
                $select = '';
                if ($category_id) {
                    if (in_array($category->id, $category_id)) {
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
    <div class="col-md-6">
        <label for="dealer">Products</label>
        <div class="form-group">
            <select class="form-control chosen-select" id="product_id" name="product_id[]" multiple>
                @foreach ($products as $product)
                <?php
                $select = '';
                if ($product_id) {
                    if (in_array($product->id, $product_id)) {
                        $select = 'selected';
                    } else {
                        $select = '';
                    }
                }
                ?>
                <option value="{{ $product->id }}" {{ $select }}>{{ $product->name }} {{ $product->model_no }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
@endsection

@section('print_card_header')
@if ($product_id)
@foreach ($product_id as $product_i)
<input type="hidden" name="product_id[]" value="{{ $product_i }}">
@endforeach
@endif

@if ($category_id)
@foreach ($category_id as $category_i)
<input type="hidden" name="category_id[]" value="{{ $category_i }}">
@endforeach
@endif

<input type="hidden" name="fromDate" value="{{ $fromDate }}">
<input type="hidden" name="toDate" value="{{ $toDate }}">
<input type="hidden" name="productType" value="{{ $type }}">
<input type="hidden" id="print_value" name="print" value="{{ $print }}">
@endsection

@section('print_card_body')
<table id="dataTable" class="table table-bordered table-sm">
    <thead>
        <tr>
            <th width="20px">SL</th>
            <th>Category</th>
            <th>Name</th>
            <th>Model</th>
            <th width="80px">Qty</th>
            <th width="130px">Sales Amount</th>
            <th width="130px">Lifting Amount</th>
            <th width="130px">Profit Amount</th>
            <th width="110px">Profit(%)</th>
        </tr>
    </thead>

    <tbody>
        @foreach($sales as $sale)
        <?php
        if ($type == 'warranty_product') {
            $lifting = $sale->liftingPrice;
            $netProfit = $sale->salePrice - $sale->liftingPrice;
            $profitPercent = 0;
            if ($netProfit > 0) {
                if ($sale->liftingPrice <= 0) {
                    $profitPercent = 100;
                } else {
                    // $profitPercent = ($netProfit / $sale->liftingPrice) * 100;
                    $profitPercent = ($netProfit / $sale->salePrice) * 100;
                }
            }
        } else {
            $lifting = $sale->consumerPrice * $sale->qty;
            $netProfit = $sale->salePrice - $lifting;
            $profitPercent = 0;
            if ($netProfit > 0) {
                if ($lifting <= 0) {
                    $profitPercent = 100;
                } else {
                    // $profitPercent = ($netProfit / $lifting) * 100;
                    $profitPercent = ($netProfit / $sale->salePrice) * 100;
                }
            }
        }
        ?>
        <tr>
            <td></td>
            <td>{{ $sale->productCategory }}</td>
            <td>{{ $sale->productName }}</td>
            <td>{{ $sale->productModel }}</td>
            <td>{{ $sale->qty }}</td>
            <td>{{ number_format($sale->salePrice, 2, '.', '') }}</td>
            <td>{{ number_format($lifting, 2, '.', '') }}</td>
            <td>{{ number_format($netProfit, 2, '.', '') }}</td>
            <td>
                {{ number_format($profitPercent, 2, '.', '') }} %
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
@section('custom-js')
<script>
    $('#productType').change(function () {
        var type = $(this).val();
        $('#product_id option').remove();
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
                    $('#product_id').append(option);
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

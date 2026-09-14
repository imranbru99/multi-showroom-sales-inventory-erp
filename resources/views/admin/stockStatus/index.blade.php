@extends('admin.layouts.masterReport')

@section('search_card_body')
<input type="hidden" name="print" value="print">
<div class="row">

    <div class="col-md-3 form-group">
        <label for="from-date">From Date</label>
        <input type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'from_date' }}"
               name="fromDate" placeholder="Select Date From" value="{{ date('d-m-Y', strtotime($fromDate)) }}" readonly>
    </div>
    <div class="col-md-3 form-group">
        <label for="to-date">To Date</label>
        <input type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'to_date' }}" name="toDate"
               placeholder="Select Date To" value="{{ date('d-m-Y', strtotime($toDate)) }}" readonly>
        <input type="hidden" name="stockSearch" value="stockStatus">
    </div>
    
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
    
    <div class="col-md-4">
        <label for="product-category">Category</label>
        <div class="form-group">
            <select class="form-control chosen-select" id="category" name="category[]" multiple>
                @foreach ($categories as $cat)
                <?php
                $select = '';
                if ($category) {
                    if (in_array($cat->id, $category)) {
                        $select = 'selected';
                    } else {
                        $select = '';
                    }
                }
                ?>
                <option value="{{ $cat->id }}" {{ $select }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-8">
        <label for="product">Product</label>
        <div class="form-group">
            <select class="form-control chosen-select" id="product" name="product[]" multiple>
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
                <option value="{{ $productInfo->id }}" {{ $select }}>{{ $productInfo->name }} ({{ $productInfo->model_no }})</option>
                @endforeach
            </select>
        </div>
    </div>


</div>
@endsection

@section('print_card_header')
@if ($vendor)
@foreach ($vendor as $vendorInfo)
<input type="hidden" name="vendor[]" value="{{ $vendorInfo }}">
@endforeach
@endif

@if ($category)
@foreach ($category as $categoryInfo)
<input type="hidden" name="category[]" value="{{ $categoryInfo }}">
@endforeach
@endif

@if ($product)
@foreach ($product as $productInfo)
<input type="hidden" name="product[]" value="{{ $productInfo }}">
@endforeach
@endif
<input type="hidden" name="productType" value="{{ $type }}">
<input type="hidden" name="store" value="{{ $storeId }}">
<input type="hidden" name="fromDate" value="{{ $fromDate }}">
<input type="hidden" name="toDate" value="{{ $toDate }}">
<input type="hidden" id="print_value" name="print" value="{{ $print }}">
@endsection

@section('print_card_body')
<div class="table-responsive">
    <table id="dataTable" name="liftingRecord" class="table table-bordered table-sm">
        <thead>
            <tr>
                <th width="20px">SL#</th>
                <th>Product</th>
                <th width="200px">Model</th>
                <th width="80px">Opening</th>
                <th width="95px">Lifting Qty</th>
                <th width="95px">Lifting Return</th>
                {{-- <th width="95px">LiftingReturnQty</th> --}}
                <th width="90px">Sales Qty</th>
                <th width="90px">Sales Return</th>
                <th width="80px">Stock Balance</th>
               <!--  <th width="80px">Stock Valuation</th> -->
            </tr>
        </thead>

        <tbody>

            @if ($stockSearch == 'stockStatus')

            <?php
            $sl = 0;
            ?>

            @foreach ($stockStatusReports as $stockStatusReport)
            <?php
            if ($stockStatusReport['balanceQty'] <= 0) {
                continue;
            }
            ?>
            <tr>
                <td>{{ $sl }}</td>
                <td>{{ $stockStatusReport['product']->name }}</td>
                <td>{{ $stockStatusReport['product']->model_no }}</td>
                <td style="text-align: right;">
                    {{ $stockStatusReport['opening'] }}
                </td>
                <td style="text-align: right;">{{ $stockStatusReport['liftingQty'] }}</td>
                <td style="text-align: right;">{{ $stockStatusReport['liftingReturnQty'] }}</td>
                <td style="text-align: right;">{{ $stockStatusReport['salesQty'] }}</td>
                <td style="text-align: right;">{{ $stockStatusReport['salesReturnQty'] }}</td>
                <td style="text-align: right;">{{ $stockStatusReport['balanceQty'] }}</td>
            </tr>

            @endforeach

            @endif
        </tbody>

    </table>
</div>
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


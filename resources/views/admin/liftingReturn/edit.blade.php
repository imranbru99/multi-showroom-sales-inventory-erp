@extends('admin.layouts.masterAddEdit')

@section('card_body')
<style type="text/css">
    .chosen-single{
        height: 35px !important;
    }
</style>

<div class="card-body">
    <div class="row">
        <div class="col-md-12">
            <input type="hidden" name="liftingReturnId" value="{{ $liftingReturn->id }}">
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <label for="productType">Product Type</label>
            <div class="form-group">
                <select class="form-control chosen-select" id="productType" name="productType">
                    <option value="">Select Product Type</option>
                    @foreach ($productTypes as $key => $value)
                    <option value="{{ $key }}" @if($key == $liftingReturn->product_type) selected @endif>{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-4">
            <label for="transfer-no">Serial No</label>
            <div class="form-group {{ $errors->has('serialNo') ? ' has-danger' : '' }}">
                <input type="text" class="form-control" name="serialNo" value="{{ $liftingReturn->serial_no }}" required readonly/>
                @if ($errors->has('serialNo'))
                @foreach($errors->get('serialNo') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <label for="transfer-date">Date</label>
            <div class="form-group {{ $errors->has('liftingReturnDate') ? ' has-danger' : '' }}">
                <input type="text" class="form-control add_datepicker" name="liftingReturnDate" value="{{ date('d-m-Y', strtotime($liftingReturn->date)) }}" readonly>
                @if ($errors->has('liftingReturnDate'))
                @foreach($errors->get('liftingReturnDate') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12">
                    <label for="supplier">Supplier</label>
                    <div class="form-group">
                        <select class="form-control chosen-select destination" id="supplier" name="supplier" required="">
                            <option value="">Select Supplier</option>
                            @foreach ($vendors as $vendor)
                            <?php
                            if ($vendor->id == $liftingReturn->vendor_id) {
                                $select = "selected";
                            } else {
                                $select = "";
                            }
                            ?>
                            <option value="{{ $vendor->id }}" {{ $select }}>{{ $vendor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <label for="store-or-showroom">Store Name</label>
                    <div class="form-group">
                        <select class="form-control chosen-select storeOrShowroom" id="storeOrShowroom" name="storeOrShowroom" required="">
                            <option value="">Select Store Name</option>
                            @foreach ($storeAndShowrooms as $storeAndShowroom)
                            <?php
                            if ($storeAndShowroom->type == $liftingReturn->store_or_showroom_type AND $storeAndShowroom->id == $liftingReturn->store_or_showroom_id) {
                                $select = "selected";
                            } else {
                                $select = "";
                            }
                            ?>
                            <option value="{{ $storeAndShowroom->id }},{{ $storeAndShowroom->type }}" {{ $select }}>{{ $storeAndShowroom->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            @if($liftingReturn->product_type == 'consumer_product' || $liftingReturn->product_type == 'raw_product')
            <div class="row">
                <div class="col-md-12" id="liftingNoColumn">
                    <label for="lifting-no">Lifting No</label>
                    <div class="form-group">
                        <select class="form-control chosen-select" id="liftingNo" name="liftingNo">
                            <option value="">Select Lifting No</option>
                            @foreach($liftingNo as $lif)
                            <option value="{{ $lif->id }}" @if($lif->id == $liftingReturnProducts[0]->lifting_id) selected @endif>{{ $lif->serial_no }} - {{ $lif->vaouchar_no }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            @else

            <div class="row">
                <div class="col-md-12">
                    <label for="products">Products</label>
                    <div class="form-group">
                        <select class="form-control chosen-select product" id="product" name="product">
                            <option value="">Select Product</option>
                            @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->model_no }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            @endif

        </div>

        <div class="col-md-6">
            <label for="remarks">Remarks</label>
            <div class="form-group {{ $errors->has('remarks') ? ' has-remarks' : '' }}">
                <textarea class="form-control" name="remarks" rows="9">{{ $liftingReturn->remarks }}</textarea>
                @if ($errors->has('remarks'))
                @foreach($errors->get('remarks') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card-body">
    <div class="row">
        <div class="col-md-6"><h4>Lifting Products</h4></div>
        <div class="col-md-6"><h4>Return Products</h4></div>
    </div>

    @if($liftingReturn->product_type == 'warranty_product' || $liftingReturn->product_type == 'spare_parts')
    <div class="row">
        <div class="col-md-6">
            <label for=""></label>
            <div class="form-group">
                <table class="table table-striped gridTable liftingProductTable" id="wLTable">
                    <thead>
                        <tr>
                            <th class="text-center" width="160px">Name</th>
                            <th class="text-center" width="120px">Model No</th>
                            <th class="text-center" width="100px">Serial No</th>
                            <th class="text-center" width="70px">Action</th>
                        </tr>
                    </thead>

                    <tbody id="tbody">
                       
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-6">
            <label for=""></label>
            <div class="form-group">
                <table class="table table-striped gridTable liftingReturnProductTable" id="wLRTable">
                    <thead>
                        <tr>
                            <th class="text-center" width="160px">Name</th>
                            <th class="text-center" width="120px">Model No</th>
                            <th class="text-center" width="100px">Serial No</th>
                            <th class="text-center" width="70px">Action</th>
                        </tr>
                    </thead>

                    <tbody id="tbody">
                        @foreach ($liftingReturnProducts as $liftingReturnProduct)
                        <tr class="liftingReturnProductRow" id="liftingReturnProductRow_{{ $liftingReturnProduct->lifting_product_id }}">
                            <td>
                                <input class="form-control liftingReturnProductName_{{ $liftingReturnProduct->lifting_product_id }}" name="productName[]" type="text" value="{{ $liftingReturnProduct->product_name }}" data-toggle="tooltip" title="{{ $liftingReturnProduct->product_name }}" readonly>
                                <input class="form-control liftingReturnId_{{ $liftingReturnProduct->lifting_product_id }}" name="liftingProductId[]" type="hidden" value="{{ $liftingReturnProduct->lifting_product_id }}" readonly>
                                <input class="form-control liftingId_{{ $liftingReturnProduct->lifting_product_id }}" name="liftingId[]" type="hidden" value="{{ $liftingReturnProduct->lifting_id }}" readonly>
                                <input class="form-control liftingReturnProductId_{{ $liftingReturnProduct->lifting_product_id }}" name="productId[]" type="hidden" value="{{ $liftingReturnProduct->product_id }}" readonly>
                                <input class="form-control liftingReturnProductPrice_{{ $liftingReturnProduct->lifting_product_id }}" name="productPrice[]" type="hidden" value="{{ $liftingReturnProduct->price }}" readonly>
                            </td>

                            <td>
                                <input class="form-control liftingReturnProductModelNo_{{ $liftingReturnProduct->lifting_product_id }}" name="productModelNo[]" type="text" value="{{ $liftingReturnProduct->model_no }}" data-toggle="tooltip" title="{{ $liftingReturnProduct->model_no }}" readonly>
                                <input class="form-control liftingReturnProductColor_{{ $liftingReturnProduct->lifting_product_id }}" name="productColor[]" type="hidden" value="{{ $liftingReturnProduct->color }}" readonly>
                                <input class="form-control liftingReturnProductMrpPrice_{{ $liftingReturnProduct->lifting_product_id }}" name="productMrpPrice[]" type="hidden" value="{{ $liftingReturnProduct->mrp_price }}" readonly>
                            </td>

                            <td>
                                <input class="form-control liftingReturnProductSerialNo_{{ $liftingReturnProduct->lifting_product_id }}" name="productSerialNo[]" type="text" value="{{ $liftingReturnProduct->serial_no }}" data-toggle="tooltip" title="{{ $liftingReturnProduct->serial_no }}" readonly>
                                <input class="form-control liftingReturnProductQty_{{ $liftingReturnProduct->lifting_product_id }}" name="productQty[]" type="hidden" value="{{ $liftingReturnProduct->qty }}" readonly>
                                <input class="form-control liftingReturnProductHigherPrice_{{ $liftingReturnProduct->lifting_product_id }}" name="productHigherPrice[]" type="hidden" value="{{ $liftingReturnProduct->haire_price }}" readonly>
                            </td>

                            <td align="center">
                                <span class="btn btn-danger item_remove" onclick="liftingReturnProductRemove({{ $liftingReturnProduct->lifting_product_id }})">Remove</span>
                            </td>
                        </tr>
                        @endforeach                            
                    </tbody>

                    <tfoot>
                        <tr>
                            <th style="text-align: center;">Total Quanity</th>
                            <td colspan="3"><input class="form-control totalQty" id="totalQty" type="text" name="totalQty" value="{{ $liftingReturn->total_qty }}" readonly></td>
                        </tr>
                        <tr>
                            <th style="text-align: center;">Total Price</th>
                            <td colspan="3"><input class="form-control totalPrice" id="totalPrice" type="text" name="totalPrice" value="{{ $liftingReturn->total_price }}" readonly></td>
                        </tr>
                        <tr>
                            <th style="text-align: center;">Total MRP Price</th>
                            <td colspan="3"><input class="form-control totalMrpPrice" id="totalMrpPrice" type="text" name="totalMrpPrice" value="{{ $liftingReturn->total_mrp_price }}" readonly></td>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    @else



    <!--Consumer Products-->
    <!--/stable-->
    <div class="row" id="spareProduct">
        <div class="col-md-6">
            <label for=""></label>
            <div class="form-group">
                <table class="table table-striped gridTable" id="sLTable">
                    <thead>
                        <tr>
                            <th class="text-center" width="160px">Name</th>
                            <th class="text-center" width="120px">Model No</th>
                            <th class="text-center" width="100px">L Qty</th>
                            <th class="text-center" width="100px">R Qty</th>
                            <th class="text-center" width="70px">Action</th>
                        </tr>
                    </thead>

                    <tbody id="tbody">
                        @foreach($lproducts as $lproduct)
                        <?php
                        $price = $lproduct->price;
                        $amount = $lproduct->amount
                        ?>
                            <tr class="liftingProductRow" id="liftingProductRow_{{$lproduct->id}}">
                                <td>
                                    <input class="form-control liftingId_{{$lproduct->id}}" type="hidden" value="{{$lproduct->lifting_id}}" readonly>
                                    <input class="form-control liftingProductId_{{$lproduct->id}}" type="hidden" value="{{$lproduct->product_id}}" readonly>
                                    <input class="form-control liftingProductName_{{$lproduct->id}}" type="text" value="{{$lproduct->product->name}}" data-toggle="tooltip" title="{{$lproduct->product->name}}" readonly>
                                    <input class="form-control liftingProductPrice_{{$lproduct->id}}" type="hidden" value="{{$lproduct->price}}" readonly>
                                    <input class="form-control liftingProductAmount_{{$lproduct->id}}" type="hidden" value="{{$amount}}" readonly>
                                </td>
                                <td>
                                    <input class="form-control liftingProductModelNo_{{$lproduct->id}}" type="text" value="{{$lproduct->model_no}}" data-toggle="tooltip" title="{{$lproduct->model_no}}" readonly>
                                    <input class="form-control liftingProductMrpPrice_{{$lproduct->id}}" type="hidden" value="{{$amount}}" readonly>
                                </td>
                                <td>
                                    <input class="form-control liftingProductQty_{{$lproduct->id}}" type="text" value="{{$lproduct->qty}}" readonly>
                                    <input class="form-control liftingProductHigherPrice_{{$lproduct->id}}" type="hidden" value="{{ $amount }}" readonly>
                                </td>
                                <td>
                                    <input class="form-control liftingProductRQty_{{$lproduct->id}}" type="text" value="{{$lproduct->qty}}">
                                </td>
                                <td align="center">
                                    <span class="btn btn-success item_remove" onclick="sLProductTransfer({{$lproduct->id}})">Return</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-6">
            <label for=""></label>
            <div class="form-group">
                <table class="table table-striped gridTable" id="sLRTable">
                    <thead>
                        <tr>
                            <th class="text-center" width="160px">Name</th>
                            <th class="text-center" width="120px">Model No</th>
                            <th class="text-center" width="100px">Qty</th>
                            <th class="text-center" width="70px">Action</th>
                        </tr>
                    </thead>

                    <tbody id="tbody">
                        @foreach ($liftingReturnProducts as $liftingReturnProduct)
                        
                        <tr class="liftingReturnProductRow" id="liftingReturnProductRow_{{ $liftingReturnProduct->lifting_product_id }}">
                            <td>
                                <input type="hidden" id="rowVal_{{ $liftingReturnProduct->lifting_product_id }}" value ="{{ $liftingReturnProduct->lifting_product_id }}">
                                <input class="form-control liftingReturnId_{{ $liftingReturnProduct->lifting_product_id }}" name="liftingProductId[]" type="hidden" value="{{ $liftingReturnProduct->lifting_product_id }}" readonly>
                                <input class="form-control liftingId_{{ $liftingReturnProduct->lifting_product_id }}" name="liftingId[]" type="hidden" value="{{ $liftingReturnProduct->lifting_id }}" readonly>
                                <input class="form-control liftingReturnProductId_{{ $liftingReturnProduct->lifting_product_id }}" name="productId[]" type="hidden" value="{{ $liftingReturnProduct->product_id }}" readonly>
                                <input class="form-control liftingReturnProductName_{{ $liftingReturnProduct->lifting_product_id }}" name="productName[]" type="text" value="{{ $liftingReturnProduct->product_name }}" data-toggle="tooltip" title="{{ $liftingReturnProduct->product_name }}" readonly>
                                <input class="form-control liftingReturnProductPrice_{{ $liftingReturnProduct->lifting_product_id }}" name="productPrice[]" type="hidden" value="{{ $liftingReturnProduct->amount }}" readonly>
                            </td>
                            <td>
                                <input class="form-control liftingReturnProductModelNo_{{ $liftingReturnProduct->lifting_product_id }}" name="productModelNo[]" type="text" value="{{ $liftingReturnProduct->model_no }}" data-toggle="tooltip" title="{{ $liftingReturnProduct->model_no }}" readonly>
                                <input class="form-control liftingReturnProductMrpPrice_{{ $liftingReturnProduct->lifting_product_id }}" name="productMrpPrice[]" type="hidden" value="{{ $liftingReturnProduct->amount }}" readonly>
                            </td>
                            <td>
                                <input class="form-control liftingReturnProductQty_{{ $liftingReturnProduct->lifting_product_id }}" name="productQty[]" type="text" oninput="findReturnAmnt({{ $liftingReturnProduct->lifting_product_id }});" value="{{ $liftingReturnProduct->qty }}">
                                <input class="form-control liftingReturnMProductQty_{{ $liftingReturnProduct->lifting_product_id }}" type="hidden" value="{{ $liftingReturnProduct->qty }}" readonly>
                                <input class="form-control liftingReturnProductHigherPrice_{{ $liftingReturnProduct->lifting_product_id }}" name="productHigherPrice" type="hidden" value="{{ $liftingReturnProduct->amount }}" readonly>
                            </td>
                            <td align="center">
                                <span class="btn btn-danger item_remove" onclick="sLReturnProductRemove({{ $liftingReturnProduct->lifting_product_id }})">Remove</span>
                            </td>
                        </tr>
                        @endforeach 
                    </tbody>

                    <tfoot>
                        <tr>
                            <th style="text-align: center;">Total Quanity</th>
                            <td colspan="3"><input class="form-control stotalQty" id="stotalQty" type="text" name="stotalQty" value="{{ $liftingReturn->total_qty }}" readonly></td>
                        </tr>
                        <tr>
                            <th style="text-align: center;">Total Price</th>
                            <td colspan="3"><input class="form-control stotalPrice" id="stotalPrice" type="text" name="stotalPrice" value="{{ $liftingReturn->total_price }}" readonly></td>
                        </tr>
                        <tr>
                            <th style="text-align: center;">Total MRP Price</th>
                            <td colspan="3"><input class="form-control stotalMrpPrice" id="stotalMrpPrice" type="text" name="stotalMrpPrice" value="{{ $liftingReturn->total_mrp_price }}" readonly></td>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection

@section('custom-js')
<script type="text/javascript">
    $(document).on('change', '#storeOrShowroom', function () {
        $('.liftingProductRow').remove();
        $('.liftingReturnProductRow').remove();

        var store = $(this).val();
        var pType = $('#productType').val();
        var supplier = $('#supplier').val();
        $('#liftingNo option, #product option').remove();

        if (pType == 'consumer_product' || pType == 'raw_product') {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "{{ route('liftingReturn.variant') }}",
                data: {
                    type: pType,
                    store: store,
                    supplier: supplier,
                },
                success: function (response) {
                    $('#liftingNo').append(`<option> Select Lifting No </option>`);
                    response.forEach(function (item, index) {
                        var option = `<option value="${item.id}">${item.serial_no} - (${item.vaouchar_no})</option>`;
                        $('#liftingNo').append(option);
                        $('.chosen-select').chosen();
                        $('.chosen-select').trigger("chosen:updated");
                    });
                }
            });
        }else{
          
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "{{ route('liftingReturn.variant') }}",
                data: {
                    type: pType,
                },
                success: function (response) {
                    $('#product').append(`<option> Select Product </option>`);
                    response.forEach(function (item, index) {
                        var option = `<option value="${item.id}">${item.name} - (${item.model_no})</option>`;
                        $('#product').append(option);
                        $('.chosen-select').chosen();
                        $('.chosen-select').trigger("chosen:updated");
                    });
                }
            });
        }
        $('.chosen-select').chosen();
        $('.chosen-select').trigger("chosen:updated");
        
    });
    $('#productType').change(function () {
        var productType = $(this).val();
        if (productType == 'warranty_product' || productType == 'spare_parts') {
            $('#liftingNoColumn, #spareProduct').hide();
            $('#productColumn, #warrantyProduct').show();
        } else {
            $('#liftingNoColumn, #spareProduct').show();
            $('#productColumn, #warrantyProduct').hide();
        }

    });
    $(document).on('change', '#supplier', function () {
        $('.liftingProductRow').remove();
        $('.liftingReturnProductRow').remove();
        $('#product').val("").trigger('chosen:updated');
    });
    $(document).on('change', '#product', function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        if ($('#supplier').val() == "")
        {
            swal('Please Select Supplier', '', 'warning');
            $('#product').val("").trigger('chosen:updated');
        } else
        {
            if ($('#storeOrShowroom').val() == "")
            {
                swal('Please Select Store Or Showroom', '', 'warning');
                $('#product').val("").trigger('chosen:updated');
            } else
            {
                $('.liftingProductRow').remove();
                var productId = $('#product').val();
                var storeOrShowroom = $('#storeOrShowroom').val().split(',');
                var vendorId = $('#supplier').val();
                var storeOrShowroomId = storeOrShowroom[0];
                var storeOrShowroomType = storeOrShowroom[1];
                $.ajax({
                    type: 'post',
                    url: '{{ route('liftingReturn.liftingProductInfo') }}',
                    data: {productId: productId, storeOrShowroomType: storeOrShowroomType, storeOrShowroomId: storeOrShowroomId, vendorId: vendorId},
                    success: function (data) {
                        var liftingProducts = data.liftingProducts;

                        for (var liftingProduct of liftingProducts)
                        {
                            if (liftingProduct.id != parseInt($('.liftingReturnId_' + liftingProduct.id).val()))
                            {
                                $("#wLTable tbody").append(
                                        '<tr class="liftingProductRow" id="liftingProductRow_' + liftingProduct.id + '">' +
                                        '<td>' +
                                        '<input class="form-control liftingId_' + liftingProduct.id + '" type="hidden" value="' + liftingProduct.lifting_id + '" readonly>' +
                                        '<input class="form-control liftingProductId_' + liftingProduct.id + '" type="hidden" value="' + liftingProduct.product_id + '" readonly>' +
                                        '<input class="form-control liftingProductName_' + liftingProduct.id + '" type="text" value="' + liftingProduct.productName + '" data-toggle="tooltip" title="' + liftingProduct.productName + '" readonly>' +
                                        '<input class="form-control liftingProductPrice_' + liftingProduct.id + '" type="hidden" value="' + liftingProduct.price + '" readonly>' +
                                        '</td>' +
                                        '<td>' +
                                        '<input class="form-control liftingProductModelNo_' + liftingProduct.id + '" type="text" value="' + liftingProduct.model_no + '" data-toggle="tooltip" title="' + liftingProduct.model_no + '" readonly>' +
                                        '<input class="form-control liftingProductColor_' + liftingProduct.id + '" type="hidden" value="' + liftingProduct.color + '" readonly>' +
                                        '<input class="form-control liftingProductMrpPrice_' + liftingProduct.id + '" type="hidden" value="' + liftingProduct.mrp_price + '" readonly>' +
                                        '</td>' +
                                        '<td>' +
                                        '<input class="form-control liftingProductSerialNo_' + liftingProduct.id + '" type="text" value="' + liftingProduct.serial_no + '" data-toggle="tooltip" title="' + liftingProduct.serial_no + '" readonly>' +
                                        '<input class="form-control liftingProductQty_' + liftingProduct.id + '" type="hidden" value="' + liftingProduct.qty + '" readonly>' +
                                        '<input class="form-control liftingProductHigherPrice_' + liftingProduct.id + '" type="hidden" value="' + liftingProduct.haire_price + '" readonly>' +
                                        '</td>' +
                                        '<td align="center">' +
                                        '<span class="btn btn-success item_remove" onclick="liftingProductTransfer(' + liftingProduct.id + ')">Return</span>' +
                                        '</td>' +
                                        '</tr>'
                                        );
                            }
                        }
                    }
                });
            }
        }
    });
    function liftingProductTransfer(liftingProductId)
    {
        var liftingId = $('.liftingId_' + liftingProductId).val();
        var productId = $('.liftingProductId_' + liftingProductId).val();
        var productName = $('.liftingProductName_' + liftingProductId).val();
        var modelNo = $('.liftingProductModelNo_' + liftingProductId).val();
        var serialNo = $('.liftingProductSerialNo_' + liftingProductId).val();
        var color = $('.liftingProductColor_' + liftingProductId).val();
        var qty = $('.liftingProductQty_' + liftingProductId).val();
        var price = $('.liftingProductPrice_' + liftingProductId).val();
        var mrpPrice = $('.liftingProductMrpPrice_' + liftingProductId).val();
        var higherPrice = $('.liftingProductHigherPrice_' + liftingProductId).val();
        $("#wLRTable tbody").append(
                '<tr class="liftingReturnProductRow" id="liftingReturnProductRow_' + liftingProductId + '">' +
                '<td>' +
                '<input class="form-control liftingReturnId_' + liftingProductId + '" name="liftingProductId[]" type="hidden" value="' + liftingProductId + '" readonly>' +
                '<input class="form-control liftingId_' + liftingProductId + '" name="liftingId[]" type="hidden" value="' + liftingId + '" readonly>' +
                '<input class="form-control liftingReturnProductId_' + liftingProductId + '" name="productId[]" type="hidden" value="' + productId + '" readonly>' +
                '<input class="form-control liftingReturnProductName_' + liftingProductId + '" name="productName[]" type="text" value="' + productName + '" data-toggle="tooltip" title="' + productName + '" readonly>' +
                '<input class="form-control liftingReturnProductPrice_' + liftingProductId + '" name="productPrice[]" type="hidden" value="' + price + '" readonly>' +
                '</td>' +
                '<td>' +
                '<input class="form-control liftingReturnProductModelNo_' + liftingProductId + '" name="productModelNo[]" type="text" value="' + modelNo + '" data-toggle="tooltip" title="' + modelNo + '" readonly>' +
                '<input class="form-control liftingReturnProductColor_' + liftingProductId + '" name="productColor[]" type="hidden" value="' + color + '" readonly>' +
                '<input class="form-control liftingReturnProductMrpPrice_' + liftingProductId + '" name="productMrpPrice[]" type="hidden" value="' + mrpPrice + '" readonly>' +
                '</td>' +
                '<td>' +
                '<input class="form-control liftingReturnProductSerialNo_' + liftingProductId + '" name="productSerialNo[]" type="text" value="' + serialNo + '" data-toggle="tooltip" title="' + serialNo + '" readonly>' +
                '<input class="form-control liftingReturnProductQty_' + liftingProductId + '" name="productQty[]" type="hidden" value="' + qty + '" readonly>' +
                '<input class="form-control liftingReturnProductHigherPrice_' + liftingProductId + '" name="productHigherPrice" type="hidden" value="' + higherPrice + '" readonly>' +
                '</td>' +
                '<td align="center">' +
                '<span class="btn btn-danger item_remove" onclick="liftingReturnProductRemove(' + liftingProductId + ')">Remove</span>' +
                '</td>' +
                '</tr>'
                );
        var totalQty = parseInt($('#totalQty').val());
        totalQty = totalQty + parseInt(qty);
        $('#totalQty').val(totalQty);
        var totalPrice = parseInt($('#totalPrice').val());
        totalPrice = totalPrice + parseInt(price);
        $('#totalPrice').val(totalPrice);
        var totalMrpPrice = parseInt($('#totalMrpPrice').val());
        totalMrpPrice = totalMrpPrice + parseInt(mrpPrice);
        $('#totalMrpPrice').val(totalMrpPrice);
        var totalHigherPrice = parseInt($('#totalHigherPrice').val());
        totalHigherPrice = totalHigherPrice + parseInt(higherPrice);
        $('#totalHigherPrice').val(totalHigherPrice);
        $('#liftingProductRow_' + liftingProductId).remove();
    }

    function liftingReturnProductRemove(liftingProductId)
    {
        var liftingId = $('.liftingId_' + liftingProductId).val();
        var productId = $('.liftingReturnProductId_' + liftingProductId).val();
        var productName = $('.liftingReturnProductName_' + liftingProductId).val();
        var modelNo = $('.liftingReturnProductModelNo_' + liftingProductId).val();
        var serialNo = $('.liftingReturnProductSerialNo_' + liftingProductId).val();
        var color = $('.liftingReturnProductColor_' + liftingProductId).val();
        var qty = $('.liftingReturnProductQty_' + liftingProductId).val();
        var price = $('.liftingReturnProductPrice_' + liftingProductId).val();
        var mrpPrice = $('.liftingReturnProductMrpPrice_' + liftingProductId).val();
        var higherPrice = $('.liftingReturnProductHigherPrice_' + liftingProductId).val();
        $("#wLTable tbody").append(
                '<tr class="liftingProductRow" id="liftingProductRow_' + liftingProductId + '">' +
                '<td>' +
                '<input class="form-control liftingId_' + liftingProductId + '" type="hidden" value="' + liftingId + '" readonly>' +
                '<input class="form-control liftingProductId_' + liftingProductId + '" type="hidden" value="' + productId + '" readonly>' +
                '<input class="form-control liftingProductName_' + liftingProductId + '" type="text" value="' + productName + '" data-toggle="tooltip" title="' + productName + '" readonly>' +
                '<input class="form-control liftingProductPrice_' + liftingProductId + '" type="hidden" value="' + price + '" readonly>' +
                '</td>' +
                '<td>' +
                '<input class="form-control liftingProductModelNo_' + liftingProductId + '" type="text" value="' + modelNo + '" data-toggle="tooltip" title="' + modelNo + '" readonly>' +
                '<input class="form-control liftingProductColor_' + liftingProductId + '" type="hidden" value="' + color + '" readonly>' +
                '<input class="form-control liftingProductMrpPrice_' + liftingProductId + '" type="hidden" value="' + mrpPrice + '" readonly>' +
                '</td>' +
                '<td>' +
                '<input class="form-control liftingProductSerialNo_' + liftingProductId + '" type="text" value="' + serialNo + '" data-toggle="tooltip" title="' + serialNo + '" readonly>' +
                '<input class="form-control liftingProductQty_' + liftingProductId + '" type="hidden" value="' + qty + '" readonly>' +
                '<input class="form-control liftingProductHigherPrice_' + liftingProductId + '" type="hidden" value="' + higherPrice + '" readonly>' +
                '</td>' +
                '<td align="center">' +
                '<span class="btn btn-success item_remove" onclick="liftingProductTransfer(' + liftingProductId + ')">Return</span>' +
                '</td>' +
                '</tr>'
                );
        var totalQty = parseInt($('#totalQty').val());
        totalQty = totalQty - parseInt(qty);
        $('#totalQty').val(totalQty);
        var totalPrice = parseInt($('#totalPrice').val());
        totalPrice = totalPrice - parseInt(price);
        $('#totalPrice').val(totalPrice);
        var totalMrpPrice = parseInt($('#totalMrpPrice').val());
        totalMrpPrice = totalMrpPrice - parseInt(mrpPrice);
        $('#totalMrpPrice').val(totalMrpPrice);
        var totalHigherPrice = parseInt($('#totalHigherPrice').val());
        totalHigherPrice = totalHigherPrice - parseInt(higherPrice);
        $('#totalHigherPrice').val(totalHigherPrice);
        $('#liftingReturnProductRow_' + liftingProductId).remove();
        
        console.log($('#liftingReturnProductRow_' + liftingProductId).html());
    }

</script>




<script>
    $('#liftingNo').change(function () {
        var liftingNo = $(this).val();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            url: "{{ route('liftingReturn.liftingNoProducts') }}",
            data: {
                liftingNo: liftingNo
            },
            success: function (response) {
                for (var liftingProduct of response)
                {
                    if (liftingProduct.id != parseInt($('.liftingReturnId_' + liftingProduct.id).val()))
                    {
                        $("#sLTable tbody").append(
                                '<tr class="liftingProductRow" id="liftingProductRow_' + liftingProduct.id + '">' +
                                '<td>' +
                                '<input class="form-control liftingId_' + liftingProduct.id + '" type="hidden" value="' + liftingProduct.lifting_id + '" readonly>' +
                                '<input class="form-control liftingProductId_' + liftingProduct.id + '" type="hidden" value="' + liftingProduct.product_id + '" readonly>' +
                                '<input class="form-control liftingProductName_' + liftingProduct.id + '" type="text" value="' + liftingProduct.product_name + '" data-toggle="tooltip" title="' + liftingProduct.product_name + '" readonly>' +
                                '<input class="form-control liftingProductPrice_' + liftingProduct.id + '" type="hidden" value="' + liftingProduct.price + '" readonly>' +
                                '<input class="form-control liftingProductAmount_' + liftingProduct.id + '" type="hidden" value="' + liftingProduct.amount + '" readonly>' +
                                '</td>' +
                                '<td>' +
                                '<input class="form-control liftingProductModelNo_' + liftingProduct.id + '" type="text" value="' + liftingProduct.model_no + '" data-toggle="tooltip" title="' + liftingProduct.model_no + '" readonly>' +
                                '<input class="form-control liftingProductMrpPrice_' + liftingProduct.id + '" type="hidden" value="' + liftingProduct.mrp_price + '" readonly>' +
                                '</td>' +
                                '<td>' +
                                '<input class="form-control liftingProductQty_' + liftingProduct.id + '" type="text" value="' + liftingProduct.qty + '" readonly>' +
                                '<input class="form-control liftingProductHigherPrice_' + liftingProduct.id + '" type="hidden" value="' + liftingProduct.mrp_price + '" readonly>' +
                                '</td>' +
                                '<td>' +
                                '<input class="form-control liftingProductRQty_' + liftingProduct.id + '" type="text" value="' + liftingProduct.qty + '">' +
                                '</td>' +
                                '<td align="center">' +
                                '<span class="btn btn-success item_remove" onclick="sLProductTransfer(' + liftingProduct.id + ')">Return</span>' +
                                '</td>' +
                                '</tr>'
                                );
                    }
                }
            }
        });
    });




    ////return spare

    function sLProductTransfer(liftingProductId)
    {
        var lrow = $('#rowVal_' + liftingProductId).val();
       
        if (lrow) {
            sLReturnProductRemove(liftingProductId);
            sLProductTransfer(liftingProductId);
        } else {
            var liftingId = $('.liftingId_' + liftingProductId).val();
            var productId = $('.liftingProductId_' + liftingProductId).val();
            var productName = $('.liftingProductName_' + liftingProductId).val();
            var modelNo = $('.liftingProductModelNo_' + liftingProductId).val();
            var qty = $('.liftingProductRQty_' + liftingProductId).val();
            var price = $('.liftingProductPrice_' + liftingProductId).val();

            var amount = price * qty;

            var mrpPrice = amount;
            var higherPrice = amount;
            $("#sLRTable tbody").append(
                    '<tr class="liftingReturnProductRow" id="liftingReturnProductRow_' + liftingProductId + '">' +
                    '<td>' +
                    '<input type="hidden" id="rowVal_' + liftingProductId +'" value ="' + liftingProductId + '">' +
                    '<input class="form-control liftingReturnId_' + liftingProductId + '" name="liftingProductId[]" type="hidden" value="' + liftingProductId + '" readonly>' +
                    '<input class="form-control liftingId_' + liftingProductId + '" name="liftingId[]" type="hidden" value="' + liftingId + '" readonly>' +
                    '<input class="form-control liftingReturnProductId_' + liftingProductId + '" name="productId[]" type="hidden" value="' + productId + '" readonly>' +
                    '<input class="form-control liftingReturnProductName_' + liftingProductId + '" name="productName[]" type="text" value="' + productName + '" data-toggle="tooltip" title="' + productName + '" readonly>' +
                    '<input class="form-control liftingReturnProductPrice_' + liftingProductId + '" name="productPrice[]" type="hidden" value="' + amount + '" readonly>' +
                    '</td>' +
                    '<td>' +
                    '<input class="form-control liftingReturnProductModelNo_' + liftingProductId + '" name="productModelNo[]" type="text" value="' + modelNo + '" data-toggle="tooltip" title="' + modelNo + '" readonly>' +
                    '<input class="form-control liftingReturnProductMrpPrice_' + liftingProductId + '" name="productMrpPrice[]" type="hidden" value="' + mrpPrice + '" readonly>' +
                    '</td>' +
                    '<td>' +
                    '<input class="form-control liftingReturnProductQty_' + liftingProductId + '" name="productQty[]" type="text" value="' + qty + '" readonly>' +
                    '<input class="form-control liftingReturnMProductQty_' + liftingProductId + '" type="hidden" value="' + qty + '" readonly>' +
                    '<input class="form-control liftingReturnProductHigherPrice_' + liftingProductId + '" name="productHigherPrice" type="hidden" value="' + higherPrice + '" readonly>' +
                    '</td>' +
                    '<td align="center">' +
                    '<span class="btn btn-danger item_remove" onclick="sLReturnProductRemove(' + liftingProductId + ')">Remove</span>' +
                    '</td>' +
                    '</tr>'
                    );
            var totalQty = parseInt($('#stotalQty').val());
            totalQty = totalQty + parseInt(qty);
            $('#stotalQty').val(totalQty);
            var totalPrice = parseInt($('#stotalPrice').val());
            totalPrice = totalPrice + parseInt(amount);
            $('#stotalPrice').val(totalPrice);
            var totalMrpPrice = parseInt($('#stotalMrpPrice').val());
            totalMrpPrice = totalMrpPrice + parseInt(amount);
            $('#stotalMrpPrice').val(totalMrpPrice);
            var totalHigherPrice = parseInt($('#stotalHigherPrice').val());
            totalHigherPrice = totalHigherPrice + parseInt(higherPrice);
            $('#stotalHigherPrice').val(totalHigherPrice);
//        $('#sliftingProductRow_' + liftingProductId).remove();   
        }
    }

    function sLReturnProductRemove(liftingProductId)
    {
        var qty = $('.liftingReturnProductQty_' + liftingProductId).val();
        var price = $('.liftingReturnProductPrice_' + liftingProductId).val();
        

        var totalQty = parseInt($('#stotalQty').val());
        totalQty = totalQty - parseInt(qty);
        $('#stotalQty').val(totalQty);
        var totalPrice = parseInt($('#stotalPrice').val());
        totalPrice = totalPrice - parseInt(price);
        $('#stotalPrice').val(totalPrice);
        var totalMrpPrice = parseInt($('#stotalMrpPrice').val());
        totalMrpPrice = totalMrpPrice - parseInt(price);
        $('#stotalMrpPrice').val(totalMrpPrice);
        var totalHigherPrice = parseInt($('#stotalHigherPrice').val());
        totalHigherPrice = totalHigherPrice - parseInt(price);
        $('#stotalHigherPrice').val(totalHigherPrice);
        $('#liftingReturnProductRow_' + liftingProductId).remove();
    }
    
    
    function findReturnAmnt(id){
        
        var mainQty = parseInt($('.liftingReturnMProductQty_' + id).val());
        
        var qty = parseInt($('.liftingReturnProductQty_' + id).val());
        var price = parseInt($('.liftingProductPrice_' + id).val());
        
        var Rprice = $('.liftingReturnProductPrice_' + id).val();
        
        var amount = qty * price;
        

        var totalQty = parseInt($('#stotalQty').val());
        totalQty = totalQty - parseInt(mainQty);
        $('#stotalQty').val(totalQty);
        var totalPrice = parseInt($('#stotalPrice').val());
        totalPrice = totalPrice - parseInt(Rprice);
        $('#stotalPrice').val(totalPrice);
        var totalMrpPrice = parseInt($('#stotalMrpPrice').val());
        totalMrpPrice = totalMrpPrice - parseInt(Rprice);
        $('#stotalMrpPrice').val(totalMrpPrice);
        var totalHigherPrice = parseInt($('#stotalHigherPrice').val());
        totalHigherPrice = totalHigherPrice - parseInt(Rprice);
        $('#stotalHigherPrice').val(totalHigherPrice);
        
        
        
        var totalQty = parseInt($('#stotalQty').val());
        totalQty = totalQty + parseInt(qty);
        $('#stotalQty').val(totalQty);
        var totalPrice = parseInt($('#stotalPrice').val());
        totalPrice = totalPrice + parseInt(amount);
        $('#stotalPrice').val(totalPrice);
        var totalMrpPrice = parseInt($('#stotalMrpPrice').val());
        totalMrpPrice = totalMrpPrice + parseInt(amount);
        $('#stotalMrpPrice').val(totalMrpPrice);
        var totalHigherPrice = parseInt($('#stotalHigherPrice').val());
        totalHigherPrice = totalHigherPrice + parseInt(amount);
        $('#stotalHigherPrice').val(totalHigherPrice);
        
        $('.liftingReturnMProductQty_' + id).val(qty);
        
    }
</script>

@endsection
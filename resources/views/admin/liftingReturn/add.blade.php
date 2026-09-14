@extends('admin.layouts.masterAddEdit')

@section('card_body')
<style type="text/css">
    .chosen-single {
        height: 35px !important;
    }
</style>
<?php
$transferNo = "";

use App\LiftingReturn;

$maxLiftingReturn = LiftingReturn::max('id');
if (@$maxLiftingReturn) {
    $serialNo = 1000000 + $maxLiftingReturn + 1;
} else {
    $serialNo = 1000000 + 1;
}
$type = array('store' => 'Warehouse Transfer', 'showrooms' => 'Showroom');
?>

<div class="card-body">
    <div class="row">
        <div class="col-md-4">
            <label for="productType">Product Type</label>
            <div class="form-group">
                <select class="form-control chosen-select" id="productType" name="productType">
                    <option value="">Select Product Type</option>
                    @foreach ($productTypes as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-4">
            <label for="transfer-no">Serial No</label>
            <div class="form-group {{ $errors->has('serialNo') ? ' has-danger' : '' }}">
                <input type="text" class="form-control" name="serialNo" value="{{ $serialNo }}" required readonly />
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
                <input type="text" class="form-control add_datepicker" name="liftingReturnDate" value="" readonly>
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
                        <select class="form-control chosen-select destination" id="supplier" name="supplier"
                            required="">
                            <option value="">Select Supplier</option>
                            @foreach ($vendors as $vendor)
                            <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label for="store-or-showroom">Store Name</label>
                    <div class="form-group">
                        <select class="form-control chosen-select storeOrShowroom" id="storeOrShowroom"
                            name="storeOrShowroom" required="">
                            <option value="">Select Store Name</option>
                            @foreach ($storeAndShowrooms as $storeAndShowroom)
                            <option value="{{ $storeAndShowroom->id }},{{ $storeAndShowroom->type }}">{{
                                $storeAndShowroom->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12" id="liftingNoColumn" style="display: none">
                    <label for="lifting-no">Lifting No</label>
                    <div class="form-group">
                        <select class="form-control chosen-select" id="liftingNo" name="liftingNo">
                            <option value="">Select Lifting No</option>

                        </select>
                    </div>
                </div>
            </div>
            <div class="row" id="productColumn">
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
        </div>
        <div class="col-md-6">
            <label for="remarks">Remarks</label>
            <div class="form-group {{ $errors->has('remarks') ? ' has-remarks' : '' }}">
                <textarea class="form-control" name="remarks" rows="9"></textarea>
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
        <div class="col-md-6">
            <h4>Lifting Products</h4>
        </div>
        <div class="col-md-6">
            <h4>Return Products</h4>
        </div>
    </div>

    <div class="row" id="warrantyProduct">
        <div class="col-md-6">
            <label for=""></label>
            <div class="form-group">
                <table class="table table-striped gridTable" id="wLTable">
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
                <table class="table table-striped gridTable" id="wLRTable">
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

                    <tfoot>
                        <tr>
                            <th style="text-align: center;">Total Quanity</th>
                            <td colspan="3"><input class="form-control" id="totalQty" type="text" name="totalQty"
                                    value="0" readonly></td>
                        </tr>
                        <tr>
                            <th style="text-align: center;">Total Price</th>
                            <td colspan="3"><input class="form-control" id="totalPrice" type="text" name="totalPrice"
                                    value="0" readonly></td>
                        </tr>
                        <tr>
                            <th style="text-align: center;">Total MRP Price</th>
                            <td colspan="3"><input class="form-control" id="totalMrpPrice" type="text"
                                    name="totalMrpPrice" value="0" readonly></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>



    <!--/stable-->
    <div class="row" id="spareProduct" style="display: none">
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
                    </tbody>

                    <tfoot>
                        <tr>
                            <th style="text-align: center;">Total Quanity</th>
                            <td colspan="3"><input class="form-control stotalQty" id="stotalQty" type="text"
                                    name="stotalQty" value="0" readonly></td>
                        </tr>
                        <tr>
                            <th style="text-align: center;">Total Price</th>
                            <td colspan="3"><input class="form-control stotalPrice" id="stotalPrice" type="text"
                                    name="stotalPrice" value="0" readonly></td>
                        </tr>
                        <tr>
                            <th style="text-align: center;">Total MRP Price</th>
                            <td colspan="3"><input class="form-control stotalMrpPrice" id="stotalMrpPrice" type="text"
                                    name="stotalMrpPrice" value="0" readonly></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom-js')

<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.2.2/axios.min.js"></script>
<script>
    // product type change event

    $('#productType').change(function (e) { 
        var pType = $('#productType').val();

        // console.log(pType);

        // fetch product by type using axios
        axios.post("{!! route('product.type') !!}", {
            type: pType
        })
        .then(function (response) {
            $('#product').empty();
            $('#product').append(`<option> Select Product </option>`);
            response.data.forEach(function (item, index) {
                var option = `<option value="${item.id}">${item.name} - (${item.model_no})</option>`;
                $('#product').append(option);


            window.setInterval(function() {
            $('.chosen-select').trigger('chosen:updated');
            }, 1000);
            
            });
        })

        
    });

</script>


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
                    // $('#product options').empty();
                    $('#product').append(`<option> Select Product </option>`);
                    response.forEach(function (item, index) {
                        var option = `<option value="${item.id}">${item.name} - (${item.model_no})</option>`;
                        // console.log(option);
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
</script>

@endsection

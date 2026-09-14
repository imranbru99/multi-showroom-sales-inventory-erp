@extends('admin.layouts.masterAddEdit')

@section('card_body')
<style type="text/css">
    .chosen-single {
        height: 35px !important;
    }

</style>
@php
$serialNo = '';
$productSerialNo = '';
use App\Lifting;
use App\LiftingProduct;

$purchaseBy = Auth::user()->name;

$maxLifting = Lifting::where('showroom_id', $showroom)->max('serial_no');

if (@$maxLifting) {
$serialNo = 1000000 + 1;
} else {
$serialNo = 1000000 + 1;
}

$maxLiftingProduct = LiftingProduct::where('showroom_id', $showroom)->max('serial_no');

if (@$maxLiftingProduct) {
$productSerialNo = $maxLiftingProduct;
} else {
$productSerialNo = 100000;
}
@endphp

<div class="card-body">
    <div class="row">
        <div class="col-md-12">
            <input class="form-control" type="hidden" name="liftingId" value="{{ $lifting->id }}">
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <input class="form-control" type="hidden" id="productSerialNo" name="productSerialNo"
                   value="{{ $productSerialNo }}">
        </div>
    </div>



    <div class="row d-flex justify-content-between mb-3" style="min-height: 85px;">
        <div class="col-md-3">
            <label for="product-type">Product Type</label>
            <select class="form-control chosen-select" name="productType" id="productType">
                @foreach($productTypes as $key => $value)
                <option value="{{ $key }}" @if($lifting->product_type == $key) selected @endif>{{ $value }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3" id="serialOption" style="display:  @if($lifting->product_type == 'warranty_product' || $lifting->product_type == 'spare_parts') block @else none @endif ;">
            <div class="form-group mt-4">
                <select class="form-control chosen-select serialType" name="serialType">
                    <option value="0">Auto Serial No</option>
                    <option value="1">Manual Serial No</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-3">
                    <label for="sl-no">lifting No</label>
                    <div class="form-group {{ $errors->has('serialNo') ? ' has-danger' : '' }}">
                        <input type="text" class="form-control" name="serialNo" value="{{ $lifting->serial_no }}"
                               required readonly />
                        @if ($errors->has('serialNo'))
                        @foreach ($errors->get('serialNo') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>

                <div class="col-md-3">
                    <label for="submission-date">Lifting Date</label>
                    <div class="form-group {{ $errors->has('submissionDate') ? ' has-danger' : '' }}">
                        <input type="text" class="form-control add_datepicker" name="submissionDate"
                               value="{{ old('submissionDate') }}" readonly>
                        @if ($errors->has('submissionDate'))
                        @foreach ($errors->get('submissionDate') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>

                <div class="col-md-3">
                    <label for="vouchar-no">Voucher No</label>
                    <div class="form-group {{ $errors->has('voucharNo') ? ' has-danger' : '' }}">
                        <input type="text" class="form-control" name="voucharNo" value="{{ $lifting->vaouchar_no }}"
                               required>
                        @if ($errors->has('voucharNo'))
                        @foreach ($errors->get('voucharNo') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>

                

                <div class="col-md-3">
                    <label for="purchase-by">Purchase By</label>
                    <div class="form-group {{ $errors->has('purchaseBy') ? ' has-danger' : '' }}">
                        <input type="text" class="form-control" name="purchaseBy" value="{{ $purchaseBy }}"
                               required readonly>
                        @if ($errors->has('purchaseBy'))
                        @foreach ($errors->get('purchaseBy') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>


            </div>

            <div class="row">
                <div class="col-md-6">
                    <label for="supplier">Supplier</label>
                    <div class="form-group {{ $errors->has('vendorId') ? ' has-danger' : '' }}">
                        <select class="form-control chosen-select" name="vendorId" required="">
                            <option value=" ">Select Supplier</option>
                            @foreach ($vendors as $vendor)
                            @php
                            if ($vendor->id == $lifting->vendor_id) {
                            $select = 'selected';
                            } else {
                            $select = '';
                            }
                            @endphp
                            <option value="{{ $vendor->id }}" {{ $select }}>{{ $vendor->name }}
                            </option>
                            @endforeach
                        </select>

                        @if ($errors->has('vendorId'))
                        @foreach ($errors->get('vendorId') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="store-showroom">Company Name</label>
                    <div class="form-group {{ $errors->has('storeOrShowroom') ? ' has-danger' : '' }}">
                        <select class="form-control chosen-select" name="storeOrShowroom">
                            <option value=" ">Select Stores Or Showrooms</option>
                            @foreach ($storesAndShowrooms as $storeAndShowroom)
                            @php
                            if ($storeAndShowroom->type == $lifting->store_or_showroom_type and $storeAndShowroom->id == $lifting->store_or_showroom_id) {
                            $select = 'selected';
                            } else {
                            $select = '';
                            }
                            @endphp
                            <option value="{{ $storeAndShowroom->id }},{{ $storeAndShowroom->type }}"
                                    {{ $select }}>{{ $storeAndShowroom->name }}</option>
                            @endforeach
                        </select>

                        @if ($errors->has('storeOrShowroom'))
                        @foreach ($errors->get('storeOrShowroom') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="@if($lifting->product_type == 'warranty_product' || $lifting->product_type == 'spare_parts') col-md-9 @else col-md-6 @endif productColumn">
                    <label for="supplier">Products</label>
                    <div class="form-group {{ $errors->has('product_d') ? ' has-danger' : '' }}">
                        <select class="form-control chosen-select" id="product" name="product_d">
                            <option value="">Select Product</option>
                            @foreach ($products as $product)
                            @php
                            if ($product->id == $lifting->id) {
                            $select = 'selected';
                            } else {
                            $select = '';
                            }
                            @endphp


                            <option value="{{ $product->id }}">{{ $product->name }} ( {{ $product->code }}
                                -
                                {{ $product->color }} - {{ $product->model_no }} )</option>
                            @endforeach
                        </select>

                        @if ($errors->has('product_d'))
                        @foreach ($errors->get('product_d') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>


                <div class="col-md-3 serialNo" style="display: none;">
                    <label for="product-serial-no">Serial No.</label>
                    <div class="form-group">
                        <input type="text" class="form-control" id="serialNo" value="">
                    </div>
                </div>

                <div class="col-md-3" style="display: none;">
                    <label for="product-serial-no">Products Serial No.</label>
                    <div class="form-group {{ $errors->has('product_serial_no') ? ' has-danger' : '' }}">
                        <input type="text" class="form-control" id="product_serial_no" name="product_serial_no"
                               value="">
                        @if ($errors->has('product_serial_no'))
                        @foreach ($errors->get('product_serial_no') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>
                
                <div class="col-md-3" id="rateColumn" style="display: @if($lifting->product_type == 'warranty_product' || $lifting->product_type == 'spare_parts') none @else block @endif;">
                    <label for="rate">Rate</label>
                    <div class="form-group">
                        <input type="number" class="form-control" id="rate" value="0" min="0">
                    </div>
                </div>

                <div class="col-md-3">
                    <label for="qty">Products QTY <span id="uom"></span></label>
                    <div class="form-group {{ $errors->has('qty') ? ' has-danger' : '' }}">
                        <input type="number" class="form-control" id="qty" name="qty" value="1" min="1">
                        @if ($errors->has('qty'))
                        @foreach ($errors->get('qty') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-md-6">
                    <label for="remarks">Remarks</label>
                    <div class="form-group">
                        <input class="form-control" type="text" name="remarks" value="{{ $lifting->remarks }}" >
                    </div>
                </div>
                
                <div class="col-md-2">
                    <label for="supplier">Total Quantity</label>
                    <div class="form-group">
                        <input style="text-align: right;" class="form-control totalQty" type="number" name="totalQty"
                               value="{{ $lifting->total_qty }}" readonly>
                    </div>
                </div>

                <div class="col-md-2">
                    <label for="supplier">Total Price</label>
                    <div class="form-group">
                        <input style="text-align: right;" class="form-control totalPrice" type="number"
                               name="totalPrice" value="{{ $lifting->total_price }}" readonly>
                    </div>
                </div>

                <div class="col-md-2">
                    <label for=""></label>
                    <div class="form-group">
                        <input type="hidden" class="row_count" value="{{ count($liftingProducts) + 1 }}">
                        <span class="btn btn-outline-success add_item" style="width: 100%;">
                            <i class="fa fa-arrow-down"></i> Listing
                        </span>
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col-md-3" style="display: none">
                    <label for="supplier">Total MRP Price</label>
                    <div class="form-group">
                        <input style="text-align: right;" class="form-control totalMrpPrice" type="number"
                               name="totalMrpPrice" value="0" readonly>
                    </div>
                </div>

                <div class="col-md-3" style="display: none">
                    <label for="supplier">Total Higher Price</label>
                    <div class="form-group">
                        <input style="text-align: right;" class="form-control totalHairePrice" type="number"
                               name="totalHairePrice" value="0" readonly>
                    </div>
                </div>
            </div>

            @if($lifting->product_type == 'warranty_product' || $lifting->product_type == 'spare_parts')
            <div class="row" id="warrantyPTable">
                <div class="col-md-12">
                    <label for=""></label>
                    <div class="form-group">
                        <table class="table table-bordered table-striped gridTable" id="wTable">
                            <thead>
                                <tr>
                                    <th>Product Name & Code</th>
                                    <th width="160px">Model</th>
                                    <th width="160px">Serial No</th>
                                    <th width="80px">Price</th>
                                    <th width="10px"><i class="fa fa-trash" style="color: white;"></i></th>
                                </tr>
                            </thead>
                            <tbody id="tbody">
                                @php
                                $i = 0;
                                @endphp
                                @foreach ($liftingProducts as $liftingProduct)
                                @php
                                $i++;
                                @endphp
                                <tr id="itemRow_{{ $i }}">
                                    <td>
                                        <input class="productId_{{ $i }}" type="hidden"
                                               name="productId[]" value="{{ $liftingProduct->product_id }}">
                                        <input class="productName_{{ $i }}" type="text"
                                               name="productName[]" value="{{ $liftingProduct->productName }}"
                                               required readonly>
                                    </td>
                                    <td>
                                        <input class="productModel_{{ $i }}" type="text"
                                               name="productModel[]" value="{{ $liftingProduct->model_no }}"
                                               readonly>
                                    </td>

                            <input class="productColor_{{ $i }}" type="hidden"
                                   name="productColor[]" value="{{ $liftingProduct->color }}" readonly>

                            <td>
                                <input class="productSerialNo_{{ $i }}" type="text"
                                       name="productSerialNo[]" value="{{ $liftingProduct->serial_no }}"
                                       required readonly>
                            </td>

                            <td>
                                <input class="productQty productQty_{{ $i }}" type="hidden"
                                       name="productQty[]" value="{{ $liftingProduct->qty }}" required>
                                <input style="text-align: right;"
                                       class="productPrice productPrice_{{ $i }}" type="number"
                                       name="productPrice[]" value="{{ $liftingProduct->price }}"
                                       oninput="findMrpHairePrice({{ $i }})" required>
                            </td>


                            <input style="text-align: right;"
                                   class="productMrpPrice productMrpPrice_{{ $i }}" type="hidden"
                                   name="productMrpPrice[]" value="{{ $liftingProduct->mrp_price }}"
                                   readonly>


                            <input style="text-align: right;"
                                   class="productHairePrice productHairePrice_{{ $i }}"
                                   type="hidden" name="productHairePrice[]"
                                   value="{{ $liftingProduct->haire_price }}" readonly>

                            <td align="center">
                                <span class="btn btn-outline-danger btn-sm item_remove"
                                      onclick="itemRemove({{ $i }})" style="width: 100%;">
                                    <i class="fa fa-trash"></i>
                                </span>
                            </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @else
            <div class="row" id="partsPTable">
                <div class="col-md-12">
                    <label for=""></label>
                    <div class="form-group">
                        <table class="table table-bordered table-striped gridTable" id="sTable">
                            <thead>
                                <tr>
                                    <th>Product Name & Code</th>
                                    <th width="160px">Batch No</th>
                                    <th width="160px">Qty</th>
                                    <th width="80px">Rate</th>
                                    <th width="80px">Amount</th>
                                    <th width="10px"><i class="fa fa-trash" style="color: white;"></i></th>
                                </tr>
                            </thead>
                            <tbody id="tbody">
                                @php
                                $i = 0;
                                @endphp
                                @foreach ($liftingProducts as $liftingProduct)
                                @php
                                    $i++;
                                @endphp
                                <tr id="itemRow_{{ $i }}">
                                    <td>
                                        <input class="productId_{{ $i }}" type="hidden"
                                               name="productId[]" value="{{ $liftingProduct->product_id }}">
                                        <input class="productName_{{ $i }}" type="text"
                                               name="productName[]" value="{{ $liftingProduct->productName }}"
                                               required readonly>
                                    </td>
                                    <td>
                                        <input class="productModel_{{ $i }}" type="text"
                                               name="productModel[]" value="{{ $liftingProduct->model_no }}"
                                               readonly>
                                    </td>

                            <input class="productColor_{{ $i }}" type="hidden"
                                   name="productColor[]" value="{{ $liftingProduct->color }}" readonly>

                            <td>
                                <input class="productQty productQty_{{ $i }}" type="number"
                                       name="productQty[]" value="{{ $liftingProduct->qty }}" readonly>
                            </td>
                            <td>
                                <input style="text-align: right;"
                                       class="productPrice productPrice_{{ $i }}" type="number"
                                       name="productPrice[]" value="{{ $liftingProduct->price }}"
                                       oninput="findMrpHairePrice({{ $i }})" required>
                            </td>
                            
                            <td>
                                <input style="text-align: right;"
                                       class="productAmount productAmount_{{ $i }}" type="text"
                                       name="productAmount[]" value="{{ $liftingProduct->amount }}"
                                       oninput="findProductAmount_({{ $i }})" required>
                            </td>


                            <input style="text-align: right;"
                                   class="productMrpPrice productMrpPrice_{{ $i }}" type="hidden"
                                   name="productMrpPrice[]" value="{{ $liftingProduct->mrp_price }}"
                                   readonly>


                            <input style="text-align: right;"
                                   class="productHairePrice productHairePrice_{{ $i }}"
                                   type="hidden" name="productHairePrice[]"
                                   value="{{ $liftingProduct->haire_price }}" readonly>

                            <td align="center">
                                <span class="btn btn-outline-danger btn-sm item_remove"
                                      onclick="itemRemove({{ $i }})" style="width: 100%;">
                                    <i class="fa fa-trash"></i>
                                </span>
                            </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection

@section('custom-js')
<script type="text/javascript">
    $('#formAddEdit').submit(function () {
        if ($('#storeOrShowroom').val() == "") {
            swal("Please! Select Store Or Showroom", "", "warning");
            $(".buttonAddEdit").attr("disabled", false);
            return false;
        }

        if ($('.gridTable tbody tr').length <= 0) {
            swal("Please! Lifting Product", "", "warning");
            $(".buttonAddEdit").attr("disabled", false);
            return false;
        }
    });





    $(".add_item").click(function () {

        var productType = $('#productType').val();

        if (productType == 'warranty_product' || productType == 'spare_parts') {


            var productId = $("#product option:selected").val();
            var AddProductQty = parseInt($('#qty').val());
            // $('#qty').val(1);


            if (productId == "") {
                swal("Please! Select A Product", "", "warning");
                $(".buttonAddEdit").attr("disabled", false);
            } else {



                for (var c = 0; c < AddProductQty; c++) {

                    var row_count = $('.row_count').val();

                    var total = parseInt(row_count);

                    if (total > 400) {
                        swal("You Can't Lifting Product More Than 400", "", "warning");
                        $(".buttonAddEdit").attr("disabled", false);
                    } else {




                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: "POST",
                            url: "{{ route('lifting.productInfo') }}",
                            data: {
                                productId: productId
                            },
                            success: function (response) {
                                var serialType = $("input[name='serialType']:checked").val();
                                if (serialType == 1) {
                                    var serialNo = $('#serialNo').val();
                                    if (serialNo == '') {
                                        swal("Enter serial No", "", "warning");
                                        $(".buttonAddEdit").attr("disabled", false);
                                        return;
                                    }

                                } else {
                                    if ($("#product_serial_no").val() == "") {
                                        var productSerialNo = parseInt($('#productSerialNo').val()) + 1;
                                        var serialNo = productSerialNo;
                                        $('#productSerialNo').val(productSerialNo);
                                    } else {
                                        var serialNo = $("#product_serial_no").val();
                                    }
                                }

                                $("#wTable tbody").append(
                                        '<tr id="itemRow_' + total + '">' +
                                        '<td>' +
                                        '<input class="productId_' + total +
                                        '" type="hidden" name="productId[]" value="">' +
                                        '<input class="productName_' + total +
                                        '" type="text" name="productName[]" value="" readonly>' +
                                        '</td>' +
                                        '<td>' +
                                        '<input class="productModel_' + total +
                                        '" type="text" name="productModel[]" value="" readonly>' +
                                        '</td>' +
                                        '<td style="display: none">' +
                                        '<input class="productColor_' + total +
                                        '" type="hi" name="productColor[]" value="">' +
                                        '</td>' +
                                        '<td>' +
                                        '<input class="productSerialNo_' + total +
                                        '" type="text" name="productSerialNo[]" value="" required>' +
                                        '</td>' +
                                        '<td>' +
                                        '<input class="productQty_' + total +
                                        '" type="hidden" name="productQty[]" value="1">' +
                                        '<input style="text-align: right;" class="productPrice productPrice_' +
                                        total +
                                        '" type="number" name="productPrice[]" value="" oninput="findMrpHairePrice(' +
                                        total + ')" required>' +
                                        '</td>' +
                                        '<td style="display: none">' +
                                        '<input style="text-align: right;" class="productMrpPrice productMrpPrice_' +
                                        total + '" type="number" name="productMrpPrice[]" value="" >' +
                                        '</td>' +
                                        '<td style="display: none">' +
                                        '<input style="text-align: right;" class="productHairePrice productHairePrice_' +
                                        total +
                                        '" type="number" name="productHairePrice[]" value="" >' +
                                        '</td>' +
                                        '<td align="center">' +
                                        '<span class="btn btn-outline-danger btn-sm item_remove" onclick="itemRemove(' +
                                        total + ')" style="width: 100%;">' +
                                        '<i class="fa fa-trash"></i>' +
                                        '</span>' +
                                        '</td>' +
                                        '</tr>'
                                        );

                                var product = response.product;

                                // console.log("in ajax " + serialNo);

                                $('.productId_' + total).val(product.id);
                                $('.productName_' + total).val(product.name);
                                $('.productModel_' + total).val(product.model_no);
                                $('.productColor_' + total).val(product.color);
                                $('.productSerialNo_' + total).val(serialNo);
                                $('.price_' + total).val(product.price);
                                $('.productPrice_' + total).val(product.price);
                                $('.productMrpPrice_' + total).val(product.mrp_price);
                                $('.productHairePrice_' + total).val(product.haire_price);

                                var totalQty = parseFloat($('.totalQty').val()) + parseFloat($(
                                        '.productQty_' + total).val());
                                var totalPrice = parseFloat($('.totalPrice').val()) + parseFloat($(
                                        '.productPrice_' + total).val());
                                var totalMrpPrice = parseFloat($('.totalMrpPrice').val()) + parseFloat(
                                        $('.productMrpPrice_' + total).val());
                                var totalHairePrice = parseFloat($('.totalHairePrice').val()) +
                                        parseFloat($('.productHairePrice_' + total).val());
                                $('.totalQty').val(totalQty);
                                $('.totalPrice').val(Math.round(totalPrice));
                                $('.totalMrpPrice').val(Math.round(totalMrpPrice));
                                $('.totalHairePrice').val(Math.round(totalHairePrice));

                                total++;
                                $('.row_count').val(total);

                            },
                            error: function (response) {

                            }
                        });

                        $('#product_serial_no').val('').focus();
                    }
                }

            }
        } else {
            var productId = $("#product option:selected").val();
            var AddProductQty = parseInt($('#qty').val());

            var row_count = $('.row_count').val();
            var total = parseInt(row_count);
            

            if (!productId) {
                swal("Please! Select A Product", "", "warning");
                return;
            }
            if (!AddProductQty) {
                swal("Please! Enter Qty", "", "warning");
                return;
            }


            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "{{ route('lifting.productInfo') }}",
                data: {
                    productId: productId
                },
                success: function (response) {

                    $("#sTable tbody").append(
                            '<tr id="itemRow_' + total + '">' +
                            '<td>' +
                            '<input class="productId_' + total +
                            '" type="hidden" name="productId[]" value="">' +
                            '<input class="productName_' + total +
                            '" type="text" name="productName[]" value="" readonly>' +
                            '</td>' +
                            '<td>' +
                            '<input class="productModel_' + total +
                            '" type="text" name="productModel[]" value="" readonly>' +
                            '</td>' +
                            '<td style="display: none">' +
                            '<input class="productColor_' + total +
                            '" type="hi" name="productColor[]" value="">' +
                            '</td>' +
//                            '<td>' +
//                            '<input class="productSerialNo_' + total +
//                            '" type="text" name="productSerialNo[]" value="" required>' +
//                            '</td>' +
                            '<td>' +
                            '<input class="productQty_' + total +
                            '" type="number" name="productQty[]" value="1" readonly>' +
                            '</td>' +
                            '<td>' +
                            '<input style="text-align: right;" class="productPrice productPrice_' +
                            total +
                            '" type="number" name="productPrice[]" value="" oninput="findMrpHairePrice(' +
                            total + ')" required>' +
                            '</td>' +
                            '<td>' +
                            '<input style="text-align: right;" class="productAmount productAmount_' +
                            total +
                            '" type="number" name="productAmount[]" value="" oninput="findProductAmount(' +
                            total + ')" required>' +
                            '</td>' +
                            '<td style="display: none">' +
                            '<input style="text-align: right;" class="productMrpPrice productMrpPrice_' +
                            total + '" type="number" name="productMrpPrice[]" value="" >' +
                            '</td>' +
                            '<td style="display: none">' +
                            '<input style="text-align: right;" class="productHairePrice productHairePrice_' +
                            total +
                            '" type="number" name="productHairePrice[]" value="" >' +
                            '</td>' +
                            '<td align="center">' +
                            '<span class="btn btn-outline-danger btn-sm item_remove" onclick="itemRemove(' +
                            total + ')" style="width: 100%;">' +
                            '<i class="fa fa-trash"></i>' +
                            '</span>' +
                            '</td>' +
                            '</tr>'
                            );

                    var product = response.product;

                    // console.log("in ajax " + serialNo);
                    
                    var sQty = AddProductQty;
                    var sPrice = parseInt($('#rate').val());
                    var sTPrice = AddProductQty * sPrice;
                    
                    

                    $('.productId_' + total).val(product.id);
                    $('.productName_' + total).val(product.name);
                    $('.productModel_' + total).val(product.model_no);
                    $('.productColor_' + total).val(product.color);
//                    $('.productSerialNo_' + total).val();
                    $('.price_' + total).val(product.price);
                    $('.productPrice_' + total).val(sPrice);
                    $('.productMrpPrice_' + total).val(sTPrice);
                    $('.productHairePrice_' + total).val(sTPrice);
                    $('.productAmount_' + total).val(sTPrice);
                    $('.productQty_' + total).val(sQty);

                    var totalQty = parseFloat($('.totalQty').val()) + sQty;
                    var totalPrice = parseFloat($('.totalPrice').val()) + sTPrice;
                    var totalMrpPrice = parseFloat($('.totalMrpPrice').val()) + sTPrice;
                    var totalHairePrice = parseFloat($('.totalHairePrice').val()) + sTPrice;
                    
                    
                    
                    $('.totalQty').val(totalQty);
                    $('.totalPrice').val(Math.round(totalPrice));
                    $('.totalMrpPrice').val(Math.round(totalMrpPrice));
                    $('.totalHairePrice').val(Math.round(totalHairePrice));


                    total++;
                    $('.row_count').val(total);
                }
            });

        }
    });

    function findMrpHairePrice(i) {
        if ($(".productPrice_" + i).val() == "") {
            var price = 0
        } else {
            var price = parseFloat($(".productPrice_" + i).val());
        }

        var mrpPrice = price + (price * 8) / 100;
        var hairePrice = mrpPrice + (mrpPrice * 12) / 100;
        $(".productMrpPrice_" + i).val(Math.round(mrpPrice));
        $(".productHairePrice_" + i).val(Math.round(hairePrice));

        rowSum();
    }

    function rowSum() {
        var totalPrice = 0;
        var totalMrpPrice = 0;
        var totalHairePrice = 0;
        $(".productPrice").each(function () {
            var price = parseFloat($(this).val());
            totalPrice += isNaN(price) ? 0 : price;
        });

        $(".productMrpPrice").each(function () {
            var mrpPrice = parseFloat($(this).val());
            totalMrpPrice += isNaN(mrpPrice) ? 0 : mrpPrice;
        });

        $(".productHairePrice").each(function () {
            var hairePrice = parseFloat($(this).val());
            totalHairePrice += isNaN(hairePrice) ? 0 : hairePrice;
        });

        $('.totalPrice').val(totalPrice);
        $('.totalMrpPrice').val(Math.round(totalMrpPrice));
        $('.totalHairePrice').val(Math.round(totalHairePrice));
    }

    function itemRemove(i) {
        var totalQty = parseFloat($('.totalQty').val());
        var totalPrice = parseFloat($('.totalPrice').val());
        var totalMrpPrice = parseFloat($('.totalMrpPrice').val());
        var totalHairePrice = parseFloat($('.totalHairePrice').val());

        var quantity = parseFloat($('.productQty_' + i).val());
        var productPrice = parseFloat($('.productPrice_' + i).val());
        var productMrpPrice = parseFloat($('.productMrpPrice_' + i).val());
        var productHairePrice = parseFloat($('.productHairePrice_' + i).val());

        totalQty = totalQty - quantity;
        totalPrice = totalPrice - productPrice;
        totalMrpPrice = totalMrpPrice - productMrpPrice;
        totalHairePrice = totalHairePrice - productHairePrice;

        $('.totalQty').val(totalQty);
        $('.totalPrice').val(totalPrice.toFixed(2));
        $('.totalMrpPrice').val(totalMrpPrice.toFixed(2));
        $('.totalHairePrice').val(totalHairePrice.toFixed(2));

        $("#itemRow_" + i).remove();
        $('#product_serial_no').val('').focus();
    }
</script>

<script>
    $('.serialType').change(function (event) {
//        var serialType = $("input[name='serialType']:checked").val();
        var serialType = $(this).val();


        if (serialType == 0) {
            $('.serialNo').hide();
            $('.productColumn').removeClass('col-md-6');
            $('.productColumn').addClass('col-md-9');
            $("#qty").prop("readonly", false);
        }

        if (serialType == 1) {
            $('.serialNo').show();
            $('.productColumn').removeClass('col-md-9');
            $('.productColumn').addClass('col-md-6');
            $("#qty").prop("readonly", true);
        }

    });

//    productType

    $('#productType').change(function () {
        var type = $(this).val();

        if (type == 'warranty_product' || type == 'spare_parts') {
            $('#serialOption').show();
            $('.productColumn').removeClass('col-md-6').addClass('col-md-9');
            $('#rateColumn').hide();
            $('#warrantyPTable').show();
            $('#partsPTable').hide();
        } else {
            $('#serialOption').hide();
            $('#warrantyPTable').hide();
            $('.productColumn').removeClass('col-md-9').addClass('col-md-6');
            $('#rateColumn').show();
            $('#partsPTable').show();
        }


        $('#product option').remove();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            url: "{{ route('lifting.variantProduct') }}",
            data: {
                type: type
            },
            success: function (response) {
                $('#product').append(`<option value="">Select Product</option>`);
                response.forEach(function (item, index) {
                    var option = `<option value="${item.id}">${item.name}</option>`;
                    $('#product').append(option);

                    $('.chosen-select').chosen();
                    $('.chosen-select').trigger("chosen:updated");
                });


            }
        });
        $('.chosen-select').chosen();
        $('.chosen-select').trigger("chosen:updated");
    });


    //Product
    //    productType

    $('#product').change(function () {
        var product = $(this).val();

        $('#uom').html('');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            url: "{{ route('lifting.variantProduct') }}",
            data: {
                product: product
            },
            success: function (response) {
                if (response.uom) {
                    $('#uom').html('/ ' + response.uom);
                }

            }
        });
    });

</script>

@endsection

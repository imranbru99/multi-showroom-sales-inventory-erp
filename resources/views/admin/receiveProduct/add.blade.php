@extends('admin.layouts.master')

@section('content')
<style type="text/css">
    .chosen-single {
        height: 35px !important;
    }

</style>

<form action="{{ route($formLink) }}" method="post">
    {{ csrf_field() }}
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title">{{ $title }}</h4>
                </div>
                <div class="col-md-6 text-right">
                    <a class="btn btn-outline-info btn-lg" href="{{ route('receiveProduct.index') }}">
                        <i class="fa fa-arrow-circle-left"></i> Go Back
                    </a>
                    <button type="submit" class="btn btn-outline-info btn-lg waves-effect buttonAddEdit" name="buttonAddEdit" value="Save"><i class="fa fa-save"></i>
                        {{$buttonName}}</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label for="host">Source Store</label>
                    <div class="form-group">
                        <input type="hidden" name="transfer_id" class="form-control" value="{{ $transfer_info->id }}">
                        <input type="hidden" name="host_id" class="form-control" value="{{ $transfer_info->host_id }}">
                        <input type="text" class="form-control" value="{{ $transfer_info->host_name }}" readonly>
                    </div>
                </div>

                <div class="col-md-3">
                    <label for="destination">Destination Store</label>
                    <div class="form-group" id="destination-select-menu">
                        <input type="hidden" name="destination_id" class="form-control" value="{{ $transfer_info->destination_id }}">
                        <input type="text" class="form-control" value="{{ $transfer_info->destination_name }}" readonly>
                    </div>
                </div>

                <div class="col-md-3">
                    <label for="transfer-no">Transfer No</label>
                    <div class="form-group">
                        <input type="hidden" name="transfer_no" class="form-control" value="{{ $transfer_info->transfer_no }}">
                        <input type="text" class="form-control" value="{{ $transfer_info->transfer_no }}" readonly>

                    </div>
                </div>

                <div class="col-md-3">
                    <label for="transfer-date">Transfer Date</label>
                    <div class="form-group">
                        <input type="hidden" name="transfer_no" class="form-control" value="{{ $transfer_info->date }}">
                        <input type="text" class="form-control" value="{{ date('d-m-Y', strtotime($transfer_info->date)) }}" readonly>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6 d-flex justify-content-between">
                    <h4>Transfer Products</h4>
                    <span class="btn btn-outline-info" onclick="receiveAll()">Receive</span>
                </div>
                <div class="col-md-6">
                    <h4>Receive Products</h4>
                </div>
            </div>

            @if($transfer_info->product_type == 'warranty_product' || $transfer_info->product_type == 'spare_parts')
            <div class="row">
                <div class="col-md-6">
                    <label for=""></label>
                    <div class="form-group">
                        <table class="table table-striped gridTable availableProductTable">
                            <thead>
                                <tr>
                                    <th class="text-center" width="160px">Name</th>
                                    <th class="text-center" width="120px">Model No</th>
                                    <th class="text-center" width="100px">Serial No</th>
                                    <th class="text-center" width="70px">
                                        <input type="checkbox" class="form_control" id="select_all" onclick="return SelectAll()">
                                    </th>
                                </tr>
                            </thead>

                            <tbody id="tbody">
                                @foreach ($products as $product)
                                <tr class="availableProductRow" id="availableProductRow_{{ $product->id }}">
                                    <td>
                                        <input class="form-control availableProductId_{{ $product->id }}" type="hidden" value="{{ $product->id }}" readonly>
                                        <input class="form-control availableProductName_{{ $product->id }}" type="text" value="{{ $product->productName }}" data-toggle="tooltip" title="{{ $product->productName }}" readonly>
                                    </td>
                                    <td>
                                        <input class="form-control availableProductModelNo_{{ $product->id }}" type="text" value="{{ $product->model_no }} " data-toggle="tooltip" title="{{ $product->model_no }}" readonly>
                                        <input class="form-control availableProductColor_{{ $product->id }}" type="hidden" value="{{ $product->color }}" readonly>
                                    </td>
                                    <td>
                                        <input class="form-control availableProductSerialNo_{{ $product->id }}" type="text" value="{{ $product->serial_no }}" data-toggle="tooltip" title="{{ $product->serial_no }}" readonly>
                                        <input class="form-control availableProductQty_{{ $product->id }}" type="hidden" value="{{ $product->qty }}" readonly>
                                    </td>
                                    <td align="center">
                                        <input type="checkbox" class="form_control checkbox" value="{{ $product->id }}" id="tr_product_{{ $product->id }}">
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
                        <table class="table table-striped gridTable transferedProductTable">
                            <thead>
                                <tr>
                                    <th class="text-center" width="160px">Name</th>
                                    <th class="text-center" width="120px">Model No</th>
                                    <th class="text-center" width="100px">Serial No</th>
                                    <th class="text-center" width="70px">Action</th>
                                </tr>
                            </thead>

                            <tbody id="tbody">
                                @foreach ($rec_products as $rec_product)
                                <tr class="transferedProductRow" id="transferedProductRow_{{ $rec_product->id }}">
                                    <td>
                                        <input class="form-control transferedId_{{ $rec_product->id }}" name="liftingProductId[]" type="hidden" value="{{ $rec_product->id }}" readonly>

                                        <input class="form-control transferedProductId_{{ $rec_product->id }}" name="productId[]" type="hidden" value="{{ $rec_product->id }}" readonly>
                                        <input class="form-control transferedProductName_{{ $rec_product->id }}" name="productName[]" type="text" value="{{ $rec_product->productName }}" readonly>
                                    </td>
                                    <td>
                                        <input class="form-control transferedProductModelNo_{{ $rec_product->id }}" name="productModelNo[]" type="text" value="{{ $rec_product->model_no }}" data-toggle="tooltip" title="{{ $rec_product->model_no }}" readonly>
                                        <input class="form-control transferedProductColor_{{ $rec_product->id }}" name="productColor[]" type="hidden" value="{{ $rec_product->color }}" readonly>
                                    </td>
                                    <td>
                                        <input class="form-control transferedProductSerialNo_{{ $rec_product->id }}" name="productSerialNo[]" type="text" value="{{ $rec_product->serial_no }}" data-toggle="tooltip" title="{{ $rec_product->serial_no }}" readonly>
                                        <input class="form-control transferedProductQty_{{ $rec_product->id }}" name="productQty[]" type="hidden" value="{{ $rec_product->qty }}" readonly>
                                    </td>
                                    <td align="center">
                                        <span class="btn btn-danger item_remove" onclick="transferProductRemove({{ $rec_product->id }})">Remove</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>

                            <tfoot>
                                <tr>
                                    <th style="text-align: center;">Total Quanity</th>
                                    <td colspan="3">
                                        <input class="form-control totalQty text-center font-weight-bold" id="totalQty" type="text" name="totalQty" value="{{ count($rec_products) }}" readonly>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            @else
            <div class="row">
                <div class="col-md-6">
                    <label for=""></label>
                    <div class="form-group">
                        <table class="table table-striped gridTable availableProductTable">
                            <thead>
                                <tr>
                                    <th class="text-center" width="160px">Name</th>
                                    <th class="text-center" width="120px">Model No</th>
                                    <th class="text-center" width="100px">Qty</th>
                                    <th class="text-center" width="70px">
                                        <input type="checkbox" class="form_control" id="select_all" onclick="return SelectAll()">
                                    </th>
                                </tr>
                            </thead>

                            <tbody id="tbody">
                                @foreach ($products as $product)
                                <tr class="availableProductRow" id="availableProductRow_{{ $product->id }}">
                                    <td>
                                        <input class="form-control availableProductId_{{ $product->id }}" type="hidden" value="{{ $product->id }}" readonly>
                                        <input class="form-control availableProductName_{{ $product->id }}" type="text" value="{{ $product->productName }}" data-toggle="tooltip" title="{{ $product->productName }}" readonly>
                                    </td>
                                    <td>
                                        <input class="form-control availableProductModelNo_{{ $product->id }}" type="text" value="{{ $product->model_no }} " data-toggle="tooltip" title="{{ $product->model_no }}" readonly>
                                        <input class="form-control availableProductColor_{{ $product->id }}" type="hidden" value="{{ $product->color }}" readonly>
                                    </td>
                                    <td>
                                        <input class="form-control availableProductSerialNo_{{ $product->id }}" type="hidden" value="{{ $product->serial_no }}" data-toggle="tooltip" title="{{ $product->serial_no }}" readonly>
                                        <input class="form-control text-center availableProductQty_{{ $product->id }}" type="text" value="{{ $product->qty }}" readonly>
                                    </td>
                                    <td align="center">
                                        <input type="checkbox" class="form_control checkbox" value="{{ $product->id }}" id="tr_product_{{ $product->id }}">
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
                        <table class="table table-striped gridTable transferedProductTable">
                            <thead>
                                <tr>
                                    <th class="text-center" width="160px">Name</th>
                                    <th class="text-center" width="120px">Model No</th>
                                    <th class="text-center" width="100px">Qty</th>
                                    <th class="text-center" width="70px">Action</th>
                                </tr>
                            </thead>

                            <tbody id="tbody">
                                @foreach ($rec_products as $rec_product)
                                <tr class="transferedProductRow" id="transferedProductRow_{{ $rec_product->id }}">
                                    <td>
                                        <input class="form-control transferedId_{{ $rec_product->id }}" name="liftingProductId[]" type="hidden" value="{{ $rec_product->id }}" readonly>

                                        <input class="form-control transferedProductId_{{ $rec_product->id }}" name="productId[]" type="hidden" value="{{ $rec_product->id }}" readonly>
                                        <input class="form-control transferedProductName_{{ $rec_product->id }}" name="productName[]" type="text" value="{{ $rec_product->productName }}" readonly>
                                    </td>
                                    <td>
                                        <input class="form-control transferedProductModelNo_{{ $rec_product->id }}" name="productModelNo[]" type="text" value="{{ $rec_product->model_no }}" data-toggle="tooltip" title="{{ $rec_product->model_no }}" readonly>
                                        <input class="form-control transferedProductColor_{{ $rec_product->id }}" name="productColor[]" type="hidden" value="{{ $rec_product->color }}" readonly>
                                    </td>
                                    <td>
                                        <input class="form-control transferedProductSerialNo_{{ $rec_product->id }}" name="productSerialNo[]" type="hidden" value="{{ $rec_product->serial_no }}" data-toggle="tooltip" title="{{ $rec_product->serial_no }}" readonly>
                                        <input class="form-control text-center transferedProductQty_{{ $rec_product->id }}" name="productQty[]" type="text" value="{{ $rec_product->qty }}" readonly>
                                    </td>
                                    <td align="center">
                                        <span class="btn btn-danger item_remove" onclick="transferProductRemove({{ $rec_product->id }})">Remove</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>

                            <tfoot>
                                <tr>
                                    <th style="text-align: center;">Total Quanity</th>
                                    <td colspan="3">
                                        <input class="form-control totalQty text-center font-weight-bold" id="totalQty" type="text" name="totalQty" value="{{ count($rec_products) }}" readonly>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            
            @endif
            
            
            
            
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-md-12 text-right">
                    <button type="submit" class="btn btn-outline-info btn-lg waves-effect buttonAddEdit" name="buttonAddEdit" value="Save"><i class="fa fa-save"></i>
                        {{$buttonName}}</button>
                </div>
            </div>
        </div>
    </div>

</form>
@endsection

@section('custom-js')
@if($transfer_info->product_type == 'warranty_product' || $transfer_info->product_type == 'spare_parts')
<script type="text/javascript">
    function availableProductTransfer(liftingProductId) {
        var productId = $('.availableProductId_' + liftingProductId).val();
        var productName = $('.availableProductName_' + liftingProductId).val();
        var modelNo = $('.availableProductModelNo_' + liftingProductId).val();
        var serialNo = $('.availableProductSerialNo_' + liftingProductId).val();
        var color = $('.availableProductColor_' + liftingProductId).val();
        var qty = $('.availableProductQty_' + liftingProductId).val();

        $(".transferedProductTable tbody").append(
            '<tr class="transferedProductRow" id="transferedProductRow_' + liftingProductId + '">' +
            '<td>' +
            '<input class="form-control transferedId_' + liftingProductId +
            '" name="liftingProductId[]" type="hidden" value="' + liftingProductId + '" readonly>' +
            '<input class="form-control transferedProductId_' + liftingProductId +
            '" name="productId[]" type="hidden" value="' + productId + '" readonly>' +
            '<input class="form-control transferedProductName_' + liftingProductId +
            '" name="productName[]" type="text" value="' + productName + '" data-toggle="tooltip" title="' +
            productName + '" readonly>' +
            '</td>' +
            '<td>' +
            '<input class="form-control transferedProductModelNo_' + liftingProductId +
            '" name="productModelNo[]" type="text" value="' + modelNo + '" data-toggle="tooltip" title="' +
            modelNo + '" readonly>' +
            '<input class="form-control transferedProductColor_' + liftingProductId +
            '" name="productColor[]" type="hidden" value="' + color + '" readonly>' +
            '</td>' +
            '<td>' +
            '<input class="form-control transferedProductSerialNo_' + liftingProductId +
            '" name="productSerialNo[]" type="text" value="' + serialNo +
            '" data-toggle="tooltip" title="' +
            serialNo + '" readonly>' +
            '<input class="form-control transferedProductQty_' + liftingProductId +
            '" name="productQty[]" type="hidden" value="' + qty + '" readonly>' +
            '</td>' +
            '<td align="center">' +
            '<span class="btn btn-danger item_remove" onclick="transferProductRemove(' + liftingProductId +
            ')">Remove</span>' +
            '</td>' +
            '</tr>'
        );

        var totalQty = parseInt($('#totalQty').val());

        totalQty = totalQty + parseInt(qty);
        $('#totalQty').val(totalQty);

        $('#availableProductRow_' + liftingProductId).remove();
    }

    function transferProductRemove(liftingProductId) {
        var productId = $('.transferedProductId_' + liftingProductId).val();
        var productName = $('.transferedProductName_' + liftingProductId).val();
        var modelNo = $('.transferedProductModelNo_' + liftingProductId).val();
        var serialNo = $('.transferedProductSerialNo_' + liftingProductId).val();
        var color = $('.transferedProductColor_' + liftingProductId).val();
        var qty = $('.transferedProductQty_' + liftingProductId).val();

        $(".availableProductTable tbody").append(
            '<tr class="availableProductRow" id="availableProductRow_' + liftingProductId + '">' +
            '<td>' +
            '<input class="form-control availableProductId_' + liftingProductId +
            '" type="hidden" value="' +
            productId + '" readonly>' +
            '<input class="form-control availableProductName_' + liftingProductId +
            '" type="text" value="' +
            productName + '" data-toggle="tooltip" title="' + productName + '" readonly>' +
            '</td>' +
            '<td>' +
            '<input class="form-control availableProductModelNo_' + liftingProductId +
            '" type="text" value="' +
            modelNo + '" data-toggle="tooltip" title="' + modelNo + '" readonly>' +
            '<input class="form-control availableProductColor_' + liftingProductId +
            '" type="hidden" value="' +
            color + '" readonly>' +
            '</td>' +
            '<td>' +
            '<input class="form-control availableProductSerialNo_' + liftingProductId +
            '" type="text" value="' +
            serialNo + '" data-toggle="tooltip" title="' + serialNo + '" readonly>' +
            '<input class="form-control availableProductQty_' + liftingProductId +
            '" type="hidden" value="' + qty +
            '" readonly>' +
            '</td>' +
            '<td align="center">' +
            '<input type="checkbox" class="form_control checkbox" value="' +
            liftingProductId +
            '" id="tr_product_' + liftingProductId + '">' +
            '</td>' +
            '</tr>'
        );

        var totalQty = parseInt($('#totalQty').val());

        totalQty = totalQty - parseInt(qty);
        $('#totalQty').val(totalQty);

        $('#transferedProductRow_' + liftingProductId).remove();
    }

</script>
@else
<script type="text/javascript">
    function availableProductTransfer(liftingProductId) {
        var productId = $('.availableProductId_' + liftingProductId).val();
        var productName = $('.availableProductName_' + liftingProductId).val();
        var modelNo = $('.availableProductModelNo_' + liftingProductId).val();
        var serialNo = $('.availableProductSerialNo_' + liftingProductId).val();
        var color = $('.availableProductColor_' + liftingProductId).val();
        var qty = $('.availableProductQty_' + liftingProductId).val();

        $(".transferedProductTable tbody").append(
            '<tr class="transferedProductRow" id="transferedProductRow_' + liftingProductId + '">' +
            '<td>' +
            '<input class="form-control transferedId_' + liftingProductId +
            '" name="liftingProductId[]" type="hidden" value="' + liftingProductId + '" readonly>' +
            '<input class="form-control transferedProductId_' + liftingProductId +
            '" name="productId[]" type="hidden" value="' + productId + '" readonly>' +
            '<input class="form-control transferedProductName_' + liftingProductId +
            '" name="productName[]" type="text" value="' + productName + '" data-toggle="tooltip" title="' +
            productName + '" readonly>' +
            '</td>' +
            '<td>' +
            '<input class="form-control transferedProductModelNo_' + liftingProductId +
            '" name="productModelNo[]" type="text" value="' + modelNo + '" data-toggle="tooltip" title="' +
            modelNo + '" readonly>' +
            '<input class="form-control transferedProductColor_' + liftingProductId +
            '" name="productColor[]" type="hidden" value="' + color + '" readonly>' +
            '</td>' +
            '<td>' +
            '<input class="form-control transferedProductSerialNo_' + liftingProductId +
            '" name="productSerialNo[]" type="hidden" value="' + serialNo +
            '" data-toggle="tooltip" title="' +
            serialNo + '" readonly>' +
            '<input class="form-control text-center transferedProductQty_' + liftingProductId +
            '" name="productQty[]" type="text" value="' + qty + '" readonly>' +
            '</td>' +
            '<td align="center">' +
            '<span class="btn btn-danger item_remove" onclick="transferProductRemove(' + liftingProductId +
            ')">Remove</span>' +
            '</td>' +
            '</tr>'
        );

        var totalQty = parseInt($('#totalQty').val());

        totalQty = totalQty + parseInt(qty);
        $('#totalQty').val(totalQty);

        $('#availableProductRow_' + liftingProductId).remove();
    }

    function transferProductRemove(liftingProductId) {
        var productId = $('.transferedProductId_' + liftingProductId).val();
        var productName = $('.transferedProductName_' + liftingProductId).val();
        var modelNo = $('.transferedProductModelNo_' + liftingProductId).val();
        var serialNo = $('.transferedProductSerialNo_' + liftingProductId).val();
        var color = $('.transferedProductColor_' + liftingProductId).val();
        var qty = $('.transferedProductQty_' + liftingProductId).val();

        $(".availableProductTable tbody").append(
            '<tr class="availableProductRow" id="availableProductRow_' + liftingProductId + '">' +
            '<td>' +
            '<input class="form-control availableProductId_' + liftingProductId +
            '" type="hidden" value="' +
            productId + '" readonly>' +
            '<input class="form-control availableProductName_' + liftingProductId +
            '" type="text" value="' +
            productName + '" data-toggle="tooltip" title="' + productName + '" readonly>' +
            '</td>' +
            '<td>' +
            '<input class="form-control availableProductModelNo_' + liftingProductId +
            '" type="text" value="' +
            modelNo + '" data-toggle="tooltip" title="' + modelNo + '" readonly>' +
            '<input class="form-control availableProductColor_' + liftingProductId +
            '" type="hidden" value="' +
            color + '" readonly>' +
            '</td>' +
            '<td>' +
            '<input class="form-control availableProductSerialNo_' + liftingProductId +
            '" type="hidden" value="' +
            serialNo + '" data-toggle="tooltip" title="' + serialNo + '" readonly>' +
            '<input class="form-control text-center availableProductQty_' + liftingProductId +
            '" type="text" value="' + qty +
            '" readonly>' +
            '</td>' +
            '<td align="center">' +
            '<input type="checkbox" class="form_control checkbox" value="' +
            liftingProductId +
            '" id="tr_product_' + liftingProductId + '">' +
            '</td>' +
            '</tr>'
        );

        var totalQty = parseInt($('#totalQty').val());

        totalQty = totalQty - parseInt(qty);
        $('#totalQty').val(totalQty);

        $('#transferedProductRow_' + liftingProductId).remove();
    }

</script>
@endif
<script>
    function SelectAll() {

        $("#select_all").change(function() { //"select all" change 
            var status = this.checked; // "select all" checked status
            $('.checkbox').each(function() { //iterate all listed checkbox items
                this.checked = status; //change ".checkbox" checked status
                // $(".checkbox").attr('checked', true);
            });
        });

        $('.checkbox').change(function() { //".checkbox" change 
            //uncheck "select all", if one of the listed checkbox item is unchecked
            if (this.checked == false) { //if this item is unchecked
                $("#select_all")[0].checked = false; //change "select all" checked status to false

            }

            //check "select all" if all checkbox items are checked
            if ($('.checkbox:checked').length == $('.checkbox').length) {
                $("#select_all")[0].checked = true; //change "select all" checked status to true
            }
        });


    }

    function receiveAll() {
        $(".availableProductTable tbody tr .checkbox").each(function() {
            if (this.checked == true) {
                var id = this.value;
                availableProductTransfer(id);
            }
            // console.log('dfsdfs', this.value);
        });
    }

</script>
@endsection

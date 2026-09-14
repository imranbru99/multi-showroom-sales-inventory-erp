@extends('admin.layouts.masterAddEditBercode')

@section('custom_css')
<style type="text/css">
    .table th {
        background: #00c292;
        text-align: center;
    }

</style>
@endsection

@section('card_body')
<style type="text/css">
    .chosen-single {
        height: 35px !important;
    }

</style>
@php
$requisitionNo = '';
use App\ProductIssue;

$maxProductIssueId = ProductIssue::max('issue_no');

if (@$maxProductIssueId) {
$productIssueNo = $maxProductIssueId + 1;
} else {
$productIssueNo = 100000000 + 1;
}
@endphp

<div class="card-body">

    <div class="row">

        <div id="productQtyStore"></div>

        <div class="col-md-4">
            <div class="form-check-inline @if ($requisitionRequired) d-none @endif ">
                <label class=" form-check-label">
                    <input type="checkbox" id="requisition_checkbox" onclick="toggleRequitionDropdown()"
                           class="form-check-input" checked> Requisition
                </label>
            </div>
        </div>

        <div class="col-md-4">
            <div id="requisition">
                <div class="form-group">
                    <select class="form-control chosen-select requisition" id="requisitionDD" name="dealerRequisitionId"
                            @if ($requisitionRequired) required="" @endif>
                        <option value="">Select Requisition</option>

                        @foreach ($requisitons as $requisiton)
                        <option value="{{ $requisiton->id }}">{{ $requisiton->requisition_no }} -
                            {{ $requisiton->dealer->name }}</option>
                        @endforeach

                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <select class="form-control chosen-select" id="productType" name="productType">
                    <option value="">Select Product Type</option>
                    @foreach ($productTypes as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-2 saleTypeColumn">
            <div class="form-group">
                <select class="form-control chosen-select salesType" id="productType" name="salesType">
                    <option value="salesBarcode">By Barcode</option>
                    <option value="salesManual">By Manual</option>
                </select>
            </div>
        </div>
    </div>

    <br>


    <div class="row">

        <div class="col-md-4">
            <label for="dealer">Dealer</label>
            <div class="form-group">
                <select class="form-control chosen-select dealer" id="dealer" name="dealer" required="">
                    <option value="">Select Dealer</option>
                    @foreach ($dealers as $dealer)
                    <option value="{{ $dealer->id }}">{{ $dealer->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-4">
            <label for="sales_by">Sales By</label>
            <div class="form-group">
                <select class="form-control chosen-select" id="sales_by" name="sales_by" required>
                    <option value="">Select Staff</option>
                    @foreach ($staffs as $staff)
                    <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-2">
            <label for="issue-no">Issue No</label>
            <div class="form-group {{ $errors->has('productIssueNo') ? ' has-danger' : '' }}">
                <input type="text" class="form-control" name="productIssueNo" value="{{ $productIssueNo }}" required
                       readonly />
                @if ($errors->has('productIssueNo'))
                @foreach ($errors->get('productIssueNo') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-2">
            <label for="issue-date">Sales Date</label>
            <div class="form-group {{ $errors->has('issueDate') ? ' has-danger' : '' }}">
                <input type="text" class="form-control add_datepicker" name="issueDate"
                       value="{{ old('issueDate') }}" readonly>
                @if ($errors->has('issueDate'))
                @foreach ($errors->get('issueDate') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <input type="hidden" class="form-control" id="dealerId" name="dealerId" value="" readonly>
        </div>
        <div class="col-md-6">
            <input type="hidden" class="form-control row_count" value="0">
        </div>
    </div>



    <div id="withoutAprrovalSection">
        <div class="row">

            <div class="col-md-4" id="baecodeSection">
                <label for="barcode">Scan Barcode</label>
                <div class="form-group">
                    <input type="text" class="form-control" id="barcode" name="barcode" autofocus=""
                           onchange="salesProduct(this.value)">
                </div>
            </div>

            <div class="col-md-4">
                <label for="product">Products</label>
                <div class="form-group" id="product-select-menu">
                    <select class="form-control chosen-select product" id="product" name="product" required="">
                        <option value="">Select Product</option>
                        @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} ( {{ $product->code }} -
                            {{ $product->color }} - {{ $product->model_no }} )</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-4" id="modelSection">
                <label for="model-no">Model No</label>
                <div class="form-group">
                    <input type="text" class="form-control" id="modelNo" name="modelNo" readonly>
                </div>
            </div>

            <div class="col-md-4" style="display: none;" id="serialNoSection">
                <label for="serial-no">Serial No</label>
                <div class="form-group" id="serial-no-select-menu">
                    <select class="form-control chosen-select serialNo" id="serialNo" name="serialNo">
                        <option value="">Select Serial No</option>
                    </select>
                </div>
            </div>

            <div class="col-md-4" style="display: none;" id="NoserialNo">
                <label for="serial-no">Serial No</label>
                <div class="form-group" id="serial-no-select-menu">
                    <input type="text" class="form-control" id="noSerialNo" name="NoserialNo" value="XXXXXX">
                </div>
            </div>

            <div class="col-md-4 consumerColumn" style="display: none;">
                <label for="serial-no">Qty</label>
                <div class="form-group">
                    <input type="number" class="form-control consumerRQty" value="1">
                </div>
            </div>

            <div class="col-md-4" style="display: none;" id="addProductSection">
                <label></label>
                <span class="btn btn-outline-success addItem" style="width: 100%;">
                    <i class="fa fa-arrow-down"></i> Add Product
                </span>
            </div>

            <div class="col-md-4" style="display: none;" id="addNoserialNo">
                <label></label>
                <span class="btn btn-outline-success addItemNoSerial" style="width: 100%;">
                    <i class="fa fa-arrow-down"></i> Add Product
                </span>
            </div>

            <div class="col-md-4 consumerColumn" style="display: none;">
                <label></label>
                <span class="btn btn-outline-success addConsumerItem" style="width: 100%;">
                    <i class="fa fa-arrow-down"></i> Add Product
                </span>
            </div>

            <div class="col-md-4" style="display: none;" id="serialNoSection">
                <label for="serial-no">Serial No</label>
                <div class="form-group" id="serial-no-select-menu">
                    <select class="form-control chosen-select serialNo" id="serialNo" name="serialNo">
                        <option value="">Select Serial No</option>
                    </select>
                </div>
            </div>

        </div>

    </div>

    <div class="row">
        <div class="col-md-12" id="warrantyProduct">
            <label></label>
            <div class="form-group">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped issueProductList">
                        <thead>
                            <tr>
                                <th width="15%">Product Name</th>
                                <th width="20%">Model</th>
                                <th width="15%">Serial</th>
                                <th width="5%">Qty</th>
                                <th width="10%">MRP</th>
                                <th width="10%">Commision(%)</th>
                                <th width="15%">Amount</th>

                                <th width="10px"><i class="fa fa-trash" style="color: white;"></i></th>
                            </tr>
                        </thead>
                        <tbody id="tbody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-12" id="consumerProduct" style="display: none">
            <label></label>
            <div class="form-group">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="consumerTable">
                        <thead>
                            <tr>
                                <th width="20%">Product Name</th>
                                <!--<th width="25%">Model</th>-->
                                <th width="10%">Qty</th>
                                <th width="10%">MRP</th>
                                <th width="10%">Commision(%)</th>
                                <th width="15%">Amount</th>

                                <th width="10px"><i class="fa fa-trash" style="color: white;"></i></th>
                            </tr>
                        </thead>
                        <tbody id="tbody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <label for="total-qty">Total Quantity</label>
            <div class="form-group">
                <input style="text-align: right;" class="form-control totalQty" type="number" name="totalQty" value="0"
                       readonly>
            </div>
        </div>

        <div class="col-md-6">
            <label for="taotal-amount">Total Amount</label>
            <div class="form-group">
                <input style="text-align: right;" class="form-control totalAmount" type="number" name="totalAmount"
                       value="0" readonly>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom-js')
<script type="text/javascript">
    function updateAmount(e) {
        const thisParent = $(e.target).parent().parent();
        const productPriceVal = thisParent.find('.productPrice').val();
        const productQty = thisParent.find('.productQty').val();
        const amountLine = thisParent.find('.amount');

        let percentAmount = (productPriceVal / 100) * $(e.target).val();

        let newAmount = productPriceVal - percentAmount;

        amountLine.val(newAmount * productQty);

        // update total amount
        updateTotalAmount();
    }

    function updateTotalAmount() {

        let totalAmount = 0;

        $('.amount').each(function (index) {
            totalAmount += +$(this).val();
        });

        $('.totalAmount').val(totalAmount);
    }


    $('.buttonAddEdit').prop("disabled", true);

    $('#withAprrovalSection').hide();
    // $('#withoutAprrovalSection').hide();

    $('.issueType').click(function (event) {
        var issueType = $("input[name='issueType']:checked").val();
        $('.totalQty').val(0);
        $('.totalAmount').val(0);
        $('.issueProductRow').remove();
        $('#dealerId').val("");

        if (issueType == "With Approval") {
            $('#withAprrovalSection').show();
            $('#withoutAprrovalSection').hide();
            $('#dealer').val("").trigger('chosen:updated');
            $('#product').val("").trigger('chosen:updated');
            $('#serialNo').val("").trigger('chosen:updated');
        }

        if (issueType == "Without Approval") {
            $('#withAprrovalSection').hide();
            $('#withoutAprrovalSection').show();
            $('.approveProductRow').remove();
            $('#dealerRequisitionId').val("").trigger('chosen:updated');
            $('#dealerCode').val("");
            $('#dealerName').val("");
            $('#dealerAddress').val("");
        }
    })


    $('.salesType').change(function (event) {
        var salesType = $(this).val();

        if (salesType == "salesBarcode") {

            $('#baecodeSection').show();
            $('#modelSection').show();
            $('#serialNoSection').hide();
            $('#NoserialNo').hide();
            $('#addProductSection').hide();
            $('#addNoserialNo').hide();


        }

        if (salesType == "salesManual") {
            $('#baecodeSection').hide();
            $('#modelSection').hide();
            $('#serialNoSection').show();
            $('#NoserialNo').hide();
            $('#addProductSection').show();
            $('#addNoserialNo').hide();

        }

        if (salesType == "salesBlank") {
            $('#baecodeSection').hide();
            $('#modelSection').hide();
            $('#serialNoSection').hide();
            $('#NoserialNo').show();
            $('#addProductSection').hide();
            $('#addNoserialNo').show();

        }
    })

    $('#checkButton').click(function (event) {
        $('.buttonAddEdit').prop("disabled", false);
        $('.buttonAddEdit').click();
    })


    $(document).on('change', '#product', function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var productId = $('#product').val();

        $.ajax({
            type: 'post',
            url: '{{ route('productIssue.productSerialInfo') }}',
            data: {
                productId: productId
            },
            success: function (data) {
                var productSerials = data.productSerials;

                var productSerialOption = '';
                if (productSerials) {
                    productSerialOption +=
                            '<select class="form-control chosen-select serialNo" id="serialNo" name="serialNo">';
                    productSerialOption += '<option value="">Select Serial No</option>';


                    for (let productSerial of Object.keys(productSerials)) {
                        var ps = productSerials[productSerial];
                        if (ps != $('.serialNo_' + ps)
                                .val()) {
                            productSerialOption += '<option id="serialNo_' + ps + '" value="' + ps +
                                    '">' +
                                    ps + '</option>';
                        }
                    }


                    productSerialOption += '</select>';
                } else {
                    productSerialOption +=
                            '<select class="form-control chosen-select serialNo" id="serialNo" name="serialNo">';
                    productSerialOption += '<option value="">Select Serial No</option>';
                    productSerialOption += '</select>';
                }
                $('#serial-no-select-menu').html(productSerialOption);
                $(".chosen-select").chosen();
            }
        });
    });

    $(document).on('change', '#dealerRequisitionId', function () {
        var dealerRequisitionId = $('#dealerRequisitionId').val();

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            url: "{{ route('productIssue.dealerRequisitionInfo') }}",
            data: {
                dealerRequisitionId: dealerRequisitionId
            },
            success: function (response) {
                $('.issueProductRow').remove();
                $('.approveProductRow').remove();

                var product = response.dealerRequisition;
                var dealer = response.dealer;
                var dealerRequisitionProducts = response.dealerRequisitionProducts;
                var productSerials = response.productSerials;
                var productIssueLists = response.productIssueLists;
                var allCommissions = response.allCommissions;

                $('#dealerId').val(dealer.id);
                $('#dealerCode').val(dealer.code);
                $('#dealerName').val(dealer.name);
                $('#dealerAddress').val(dealer.address);


                for (var dealerRequisitionProduct of dealerRequisitionProducts) {
                    var productSerialOption = '';
                    var serialCount = 1;
                    for (var productSerial of productSerials) {
                        if (productSerial.product_id == dealerRequisitionProduct.product_id) {
                            productSerialOption += '<option id="serialNo_' + productSerial
                                    .serial_no + '" value="' + productSerial.serial_no + '">' +
                                    productSerial.serial_no + '</option>';
                            serialCount = serialCount + 1;
                        }
                    }

                    var totalIssueQty = 0;
                    for (var productIssueList of productIssueLists) {
                        if (productIssueList.product_id == dealerRequisitionProduct.product_id) {
                            totalIssueQty = totalIssueQty + parseInt(productIssueList.qty);
                        }
                    }

                    var commissionRate = 0;
                    for (var commission of allCommissions) {
                        if (commission.category_id == dealerRequisitionProduct.categoryId) {
                            commissionRate = commission.commission_rate;
                        }
                    }

                    $(".approveProducts tbody").append(
                            '<tr class="approveProductRow" id="approveProductRow_' +
                            dealerRequisitionProduct.id + '">' +
                            '<td>' +
                            '<input class="form-control productName_' + dealerRequisitionProduct
                            .id + '" type="text" value="' + dealerRequisitionProduct.productName +
                            '" readonly>' +
                            '<input class="form-control productId_' + dealerRequisitionProduct.id +
                            '" type="hidden" value="' + dealerRequisitionProduct.product_id +
                            '" readonly>' +
                            '</td>' +
                            '<td>' +
                            '<input class="form-control productModelNo_' + dealerRequisitionProduct
                            .id + '" type="text" value="' + dealerRequisitionProduct.model_no +
                            '" readonly>' +
                            '</td>' +
                            '<td>' +
                            '<input style="text-align: right;" class="form-control approveQty approveQty_' +
                            dealerRequisitionProduct.id + '" type="number" value="' +
                            dealerRequisitionProduct.approved_qty + '" readonly>' +
                            '<input style="text-align: right;" class="form-control price_' +
                            dealerRequisitionProduct.id + '" type="hidden" value="' +
                            dealerRequisitionProduct.price + '" readonly>' +
                            '</td>' +
                            '<td>' +
                            '<input style="text-align: right;" class="form-control issueQty issueQty_' +
                            dealerRequisitionProduct.id +
                            '" type="number" name="issueQty[]" value="' + totalIssueQty +
                            '" readonly>' +
                            '<input style="text-align: right;" class="form-control commissionRate_' +
                            dealerRequisitionProduct.id + '" type="hidden" value="' +
                            commissionRate + '" readonly>' +
                            '</td>' +
                            '<td>' +
                            '<select class="form-control chosen-select productSerialNo_' +
                            dealerRequisitionProduct.id + '" id="productSerialNo_' +
                            dealerRequisitionProduct.id + '" name="serialNo[]">' +
                            '<option value="">Select Serial No</option>' + productSerialOption +
                            '</select>' +
                            '</td>' +
                            '<td align="center">' +
                            '<span class="btn btn-info btn-sm item_remove" onclick="issueProductAdd(' +
                            dealerRequisitionProduct.id + ')" style="width: 100%;">' +
                            '<i class="fa fa-plus"></i>' +
                            '</span>' +
                            '</td>' +
                            '</tr>'
                            );
                }
            },
            error: function (response) {

            }
        });
    });

    function issueProductAdd(dealerRequisitionProductId) {
        var approveQty = parseInt($('.approveQty_' + dealerRequisitionProductId).val());
        var issueQty = parseInt($('.issueQty_' + dealerRequisitionProductId).val());

        if ($('.productSerialNo_' + dealerRequisitionProductId).val() == "") {
            swal("Select Serial Number", "", "warning");
        } else {
            if (approveQty == issueQty) {
                swal("You Can't Issue More Than Approve Quantity", "", "warning");
            } else {
                issueQty = issueQty + 1;
                $('.issueQty_' + dealerRequisitionProductId).val(issueQty);

                var productId = $('.productId_' + dealerRequisitionProductId).val();
                var productName = $('.productName_' + dealerRequisitionProductId).val();
                var productModelNo = $('.productModelNo_' + dealerRequisitionProductId).val();
                var productSerialNo = $('.productSerialNo_' + dealerRequisitionProductId).val();
                var productPrice = $('.price_' + dealerRequisitionProductId).val();
                var commissionRate = $('.commissionRate_' + dealerRequisitionProductId).val();

                var totalQty = parseInt($('.totalQty').val());
                totalQty = totalQty + 1;
                $('.totalQty').val(totalQty);

                var totalAmount = parseInt($('.totalAmount').val());
                totalAmount = totalAmount + parseFloat(productPrice);
                $('.totalAmount').val(totalAmount);

                var row_count = $('.row_count').val();
                var total = parseInt(row_count) + 1;

                $(".issueProductList tbody").append(
                        '<tr class="issueProductRow" id="issueProductRow_' + total + '">' +
                        '<td>' +
                        '<input class="productId_' + total + '" type="hidden" name="productId[]" value="' + productId +
                        '">' +
                        '<input class="productName_' + total + '" type="text" name="productName[]" value="' +
                        productName + '" readonly>' +
                        '</td>' +
                        '<td>' +
                        '<input class="productModel_' + total + '" type="text" name="productModel[]" value="' +
                        productModelNo + '" readonly>' +
                        '</td>' +
                        '<td>' +
                        '<input class="productSerial_' + total + '" type="text" name="productSerial[]" value="' +
                        productSerialNo + '" readonly>' +
                        '</td>' +
                        '<td>' +
                        '<input style="text-align: right;" class="productQty productQty_' + total +
                        '" type="number" name="productQty[]" value="1" readonly>' +
                        '</td>' +
                        '<td>' +
                        '<input style="text-align: right;" class="productPrice productPrice_' + total +
                        '" type="text" name="productPrice[]" value="' + productPrice + '" required readonly>' +
                        '</td>' +
                        '<td>' +
                        '<input style="text-align: right;" class="productCommision_' + total +
                        '" type="number" name="commission[]" value="' + commissionRate + '" readonly>' +
                        '</td>' +
                        '<td>' +
                        '<input style="text-align: right;" class="amount amount_' + total +
                        '" type="number" name="amount[]" value="' + productPrice + '" readonly>' +
                        '</td>' +
                        '<td align="center">' +
                        '<span class="btn btn-outline-danger btn-sm item_remove" onclick="withApprovalRowRemove(' +
                        total + ',' + dealerRequisitionProductId + ')" style="width: 100%;">' +
                        '<i class="fa fa-trash"></i>' +
                        '</span>' +
                        '</td>' +
                        '</tr>'
                        );
                $('#serialNo_' + productSerialNo).remove();
                $('.row_count').val(total);
            }
        }
    }

    $(document).on('change', '#dealer', function () {
        var dealerId = $("#dealer option:selected").val();
        $("#dealerId").val(dealerId);
        $('.issueProductRow').remove();
        $('#product').val("").trigger('chosen:updated');
        $('#serialNo').val("").trigger('chosen:updated');
    });

    $(".addItem").click(function () {
        if ($("#dealer option:selected").val() == "") {
            swal("Please! Select A Dealer", "", "warning");
        } else {
            if ($("#product option:selected").val() == "") {
                swal("Please! Select A Product", "", "warning");
            } else {
                if ($("#serialNo option:selected").val() == "") {
                    swal("Please! Select A Serial", "", "warning");
                } else {
                    var productId = $("#product option:selected").val();
                    var dealerId = $("#dealerId").val();


                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: "{{ route('productIssue.productInfo') }}",
                        data: {
                            productId: productId,
                            dealerId: dealerId
                        },
                        success: function (response) {
                            var credit_limit = parseFloat(response.credit_limit);
                            var totalAmount = parseFloat($('.totalAmount').val());

                            var product = response.product;
                            var commissionRate = response.commissionRate;
                            var offerRate = response.offerRate;
                            var netAmount = response.netAmount;


                            totalAmount = totalAmount + parseFloat(netAmount);
                            if (totalAmount > credit_limit) {
                                swal("Credit Limit Over", "", "warning");
                                return;
                            }


                            var row_count = $('.row_count').val();
                            var total = parseInt(row_count) + 1;
                            var serialNo = $(".serialNo").val();

                            // check if product is more than requsitioned start


                            let isrequisition = $('#' + productId).length == 0 ? false : true;

                            if (isrequisition) {
                                let reqsitionCount = $('#' + productId).val();

                                let addedCurrentProduct = $('.productId_' + productId).length;

                                console.log(reqsitionCount);
                                console.log(addedCurrentProduct);

                                if (reqsitionCount == addedCurrentProduct) {
                                    return false;
                                }

                            }

                            // check if product is more than requsitioned end

                            $(".issueProductList tbody").append(
                                    '<tr class="issueProductRow" id="issueProductRow_' + total +
                                    '">' +
                                    '<td>' +
                                    '<input class="productId_' + productId + ' productId_' + total +
                                    '" type="hidden" name="productId[]" value="">' +
                                    '<input class="productName_' + total +
                                    '" type="text" name="productName[]" value="" size="15" readonly>' +
                                    '</td>' +
                                    '<td>' +
                                    '<input class="productModel_' + total +
                                    '" type="text" name="productModel[]" value="" readonly>' +
                                    '</td>' +
                                    '<td>' +
                                    '<input class="productSerial_' + total +
                                    '" type="text" size="15" name="productSerial[]" value="" readonly>' +
                                    // '<input class="serialNo_'+serialNo+'" type="hidden" name="productSerial[]" value="'+serialNo+'" readonly>'+
                                    '</td>' +
                                    '<td>' +
                                    '<input style="text-align: right;" class="productQty productQty_' +
                                    total +
                                    '" type="text" size="15" name="productQty[]" value="1" readonly>' +
                                    '</td>' +
                                    '<td>' +
                                    '<input style="text-align: right;" class="productPrice productPrice_' +
                                    total +
                                    '" type="number" size="15" name="productPrice[]" value="" required readonly>' +
                                    '</td>' +
                                    '<td>' +
                                    '<input style="text-align: right;" class="productCommision_' +
                                    total +
                                    '" onkeyup="updateAmount(event)" size="15" type="number" name="commission[]" value="">' +
                                    '</td>' +
                                    '<td>' +
                                    '<input style="text-align: right;" size="15" class="amount amount_' +
                                    total +
                                    '" type="number" name="amount[]" value="" readonly>' +
                                    '</td>' +
                                    '<td align="center">' +
                                    '<span class="btn btn-outline-danger btn-sm item_remove" onclick="withoutApprovalRowRemove(' +
                                    total + ')" style="width: 100%;">' +
                                    '<i class="fa fa-trash"></i>' +
                                    '</span>' +
                                    '</td>' +
                                    '</tr>'
                                    );
                            $('.serialNo option[value=' + serialNo + ']').remove();
                            $('.serialNo').trigger('chosen:updated');
                            $('.row_count').val(total);



                            $('.productId_' + total).val(product.id);
                            $('.productName_' + total).val(product.name);
                            $('.productModel_' + total).val(product.model_no);
                            $('.productSerial_' + total).val(serialNo);
                            $('.productCommision_' + total).val(commissionRate);
                            $('.productOffer_' + total).val(offerRate);
                            $('.productPrice_' + total).val(product.mrp_price);
                            $('.amount_' + total).val(netAmount);

                            var totalQty = parseInt($('.totalQty').val());
                            totalQty = totalQty + 1;
                            $('.totalQty').val(totalQty);


                            $('.totalAmount').val(totalAmount.toFixed(2));
                        },
                        error: function (response) {

                        }
                    });


                }
            }
        }
    });

    function withApprovalRowRemove(i, id) {
        var issueQty = parseInt($('.issueQty_' + id).val());
        issueQty = issueQty - 1;
        $('.issueQty_' + id).val(issueQty);

        var totalQty = parseInt($('.totalQty').val());
        totalQty = totalQty - 1;
        $('.totalQty').val(totalQty);

        var productPrice = parseFloat($('.productPrice_' + i).val());
        var totalAmount = parseInt($('.totalAmount').val());
        totalAmount = totalAmount - productPrice;
        $('.totalAmount').val(totalAmount);

        var productSerialNo = $('.productSerial_' + i).val();

        $('.productSerialNo_' + id).append(
                '<option id="serialNo_' + productSerialNo + '" value="' + productSerialNo + '">' + productSerialNo +
                '</option>'
                );

        $('#issueProductRow_' + i).remove();
    }

    function withoutApprovalRowRemove(i) {
        var totalQty = parseFloat($('.totalQty').val());
        totalQty = totalQty - 1;
        $('.totalQty').val(totalQty);

        var productPrice = parseFloat($('.amount_' + i).val());
        var totalAmount = parseFloat($('.totalAmount').val());

        totalAmount = totalAmount - productPrice;
        $('.totalAmount').val(totalAmount);

        var serialNo = $('.productSerial_' + i).val();

        $('.serialNo').append(
                '<option id="serialNo_' + serialNo + '" value="' + serialNo + '">' + serialNo + '</option>'
                );

        $('.serialNo').trigger('chosen:updated');

        $('#issueProductRow_' + i).remove();
    }

    //scan barcode

    function salesProduct(serial) {
        var serial = serial;
        var serialNo = serial.split('-').pop();
        var serialNo = '1' + serialNo;

        $.ajax({

            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            url: "{{ route('productIssue.getProduct') }}",
            data: {
                serialNo: serialNo
            },
            success: function (response) {

                var pdt = response.productDetail;
                var productOption = '';
                var productSerialOption = '';

                $('#modelNo').val(pdt.model_no);


                if (pdt) {
                    productSerialOption +=
                            '<select class="form-control chosen-select serialNo" id="serialNo" name="serialNo">';
                    productSerialOption += '<option value="">Select Serial No</option>';

                    productSerialOption += '<option  value="' + pdt.serial_no + '" selected="selected">' +
                            pdt.serial_no + '</option>';

                    productSerialOption += '</select>';



                    productOption +=
                            '<select class="form-control chosen-select product" id="product" name="product">';
                    productOption += '<option value="">Select Product</option>';

                    productOption += '<option  value="' + pdt.product_id + '" selected="selected">' + pdt
                            .product_name + '</option>';

                    productOption += '</select>';



                }

                $('#serial-no-select-menu').html(productSerialOption);
                $('#product-select-menu').html(productOption);

                $(".chosen-select").chosen();

                $('.addItem').click();
            },

            error: function (response) {

            }

        });
    }

    // $('form').bind("keypress", function(e) {

    //   if (e.keyCode == 13) {               
    //     e.preventDefault();
    //     return false;
    //   }
    // });


    function toggleRequitionDropdown() {

        let showReqDropDown = $('#requisition_checkbox').is(":checked");

        if (showReqDropDown) {

            $('#requisition').show();

        } else {

            $('#requisition').hide();

        }

    }

    toggleRequitionDropdown();

    // requisition code

    $('#requisitionDD').change(function (e) {
        e.preventDefault();

        let requisitionNo = $('#requisitionDD').val();

        if (requisitionNo == '') {
            return true;
        }

        $.ajax({

            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            url: "{{ route('productIssue.getRequisitionProduct') }}",
            data: {
                requisitionNo: requisitionNo
            },
            success: function (response) {

                // products dropdown start

                $('#product').html('');

                $('#product').append(`<option value="">Select Product</option>`);

                let products = response.products;

                for (const product in products) {
                    if (Object.hasOwnProperty.call(products, product)) {
                        const p = products[product];

                        let option = `<option value="${p.id}">${p.name} - ${p.model_no}</option>`;

                        $('#product').append(option);

                        $('#product').trigger("chosen:updated");

                    }
                }
                // products dropdown end


                // Dealer dropdown start

                $('#dealer').html('');

                let dealer = response.dealer;

                let option = `<option value="${dealer.id}" selected>${dealer.name}</option>`;

                $('#dealer').append(option);

                $('#dealer').trigger("chosen:updated");

                // Dealer dropdown end

                // requisition count store start

                $('#productQtyStore').html('');

                let reqp = response.requisition.requisitions;

                for (const prod in reqp) {
                    if (Object.hasOwnProperty.call(reqp, prod)) {
                        const p = reqp[prod];

                        let input =
                                `<input type="hidden" id="${p.product_id}" value="${p.approved_qty}">`;

                        $('#productQtyStore').append(input);

                    }
                }

                // requisition count store end

            },

            error: function (response) {

            }

        });

    });
</script>


<script>
    $(".addItemNoSerial").click(function () {
        var salesType = $(".salesType").val();
        if ($("#dealer option:selected").val() == "") {
            swal("Please! Select A Dealer", "", "warning");
            return;
        }
        if ($("#product option:selected").val() == "") {
            swal("Please! Select A Product", "", "warning");
            return;
        }
        if (salesType == "salesBlank") {

            var productId = $("#product option:selected").val();
            var dealerId = $("#dealerId").val();


            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "{{ route('productIssue.productInfo') }}",
                data: {
                    productId: productId,
                    dealerId: dealerId
                },
                success: function (response) {
                    var credit_limit = parseFloat(response.credit_limit);
                    var totalAmount = parseFloat($('.totalAmount').val());

                    var product = response.product;
                    var commissionRate = response.commissionRate;
                    var offerRate = response.offerRate;
                    var netAmount = response.netAmount;


                    totalAmount = totalAmount + parseFloat(netAmount);
                    if (totalAmount > credit_limit) {
                        swal("Credit Limit Over", "", "warning");
                        return;
                    }


                    var row_count = $('.row_count').val();
                    var total = parseInt(row_count) + 1;
                    var serialNo = $("#noSerialNo").val();

                    // check if product is more than requsitioned start


                    let isrequisition = $('#' + productId).length == 0 ? false : true;

                    if (isrequisition) {
                        let reqsitionCount = $('#' + productId).val();

                        let addedCurrentProduct = $('.productId_' + productId).length;

                        if (reqsitionCount == addedCurrentProduct) {
                            return false;
                        }

                    }

                    // check if product is more than requsitioned end

                    $(".issueProductList tbody").append(
                            '<tr class="issueProductRow" id="issueProductRow_' + total +
                            '">' +
                            '<td>' +
                            '<input class="productId_' + productId + ' productId_' + total +
                            '" type="hidden" name="productId[]" value="">' +
                            '<input class="productName_' + total +
                            '" type="text" name="productName[]" value="" readonly>' +
                            '</td>' +
                            '<td>' +
                            '<input class="productModel_' + total +
                            '" type="text" name="productModel[]" value="" readonly>' +
                            '</td>' +
                            '<td>' +
                            '<input class="productSerial_' + total +
                            '" type="text" name="productSerial[]" value="" readonly>' +
                            // '<input class="serialNo_'+serialNo+'" type="hidden" name="productSerial[]" value="'+serialNo+'" readonly>'+
                            '</td>' +
                            '<td>' +
                            '<input style="text-align: right;" class="productQty productQty_' +
                            total +
                            '" type="number" name="productQty[]" value="1" readonly>' +
                            '</td>' +
                            '<td>' +
                            '<input style="text-align: right;" class="productPrice productPrice_' +
                            total +
                            '" type="number" name="productPrice[]" value="" required readonly>' +
                            '</td>' +
                            '<td>' +
                            '<input style="text-align: right;" class="productCommision_' +
                            total +
                            '" onkeyup="updateAmount(event)" type="number" name="commission[]" value="">' +
                            '</td>' +
                            '<td>' +
                            '<input style="text-align: right;" class="amount amount_' +
                            total +
                            '" type="number" name="amount[]" value="" readonly>' +
                            '</td>' +
                            '<td align="center">' +
                            '<span class="btn btn-outline-danger btn-sm item_remove" onclick="withoutApprovalRowRemove(' +
                            total + ')" style="width: 100%;">' +
                            '<i class="fa fa-trash"></i>' +
                            '</span>' +
                            '</td>' +
                            '</tr>'
                            );

                    $('.row_count').val(total);



                    $('.productId_' + total).val(product.id);
                    $('.productName_' + total).val(product.name);
                    $('.productModel_' + total).val(product.model_no);
                    $('.productSerial_' + total).val(serialNo);
                    $('.productCommision_' + total).val(commissionRate);
                    $('.productOffer_' + total).val(offerRate);
                    $('.productPrice_' + total).val(product.mrp_price);
                    $('.amount_' + total).val(netAmount);

                    var totalQty = parseInt($('.totalQty').val());
                    totalQty = totalQty + 1;
                    $('.totalQty').val(totalQty);


                    $('.totalAmount').val(totalAmount.toFixed(2));
                },
                error: function (response) {

                }
            });


        }
    });
</script>


<script>
    $('#productType').change(function () {
        var type = $(this).val();

        if (type == 'consumer_product') {
            $('.saleTypeColumn,#baecodeSection,#modelSection,#serialNoSection,#NoserialNo,#addProductSection,#addNoserialNo, #warrantyProduct').hide();
            $('.consumerColumn, #consumerProduct, #consumerTable').show();

        } else {
            $('.saleTypeColumn').show();
            var salesType = $(".salesType").val();

            if (salesType == "salesBarcode") {
                $('#baecodeSection,#modelSection, #warrantyProduct').show();
                $('#serialNoSection,#NoserialNo,#addProductSection,#addNoserialNo,.consumerColumn, #consumerProduct, #consumerTable').hide();
            }

            if (salesType == "salesManual") {
                $('#baecodeSection,#modelSection,#NoserialNo,#addNoserialNo,.consumerColumn, #consumerProduct, #consumerTable').hide();
                $('#serialNoSection').show();
                $('#addProductSection, #warrantyProduct').show();
            }
        }



        $('#product option').remove();
        $('#product').append(`<option value="">Select Product</option>`);
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




    $(".addConsumerItem").click(function () {
        if ($("#dealer option:selected").val() == "") {
            swal("Please! Select A Dealer", "", "warning");
        } else {
            if ($("#product option:selected").val() == "") {
                swal("Please! Select A Product", "", "warning");
            } else {
                var productId = $("#product option:selected").val();
                var dealerId = $("#dealerId").val();
                var consumerRQty = $(".consumerRQty").val();

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: "{{ route('productIssue.consumerProductInfo') }}",
                    data: {
                        productId: productId,
                        dealerId: dealerId,
                        qty: consumerRQty
                    },
                    success: function (response) {
                        var credit_limit = parseFloat(response.credit_limit);
                        var totalAmount = parseFloat($('.totalAmount').val());

                        var product = response.product;
                        var commissionRate = response.commissionRate;
                        var offerRate = response.offerRate;
                        var netAmount = response.netAmount;


                        totalAmount = totalAmount + parseFloat(netAmount);
                        if (totalAmount > credit_limit) {
                            swal("Credit Limit Over", "", "warning");
                            return;
                        }


                        var row_count = $('.row_count').val();
                        var total = parseInt(row_count) + 1;


                        // check if product is more than requsitioned end

                        $("#consumerTable tbody").append(
                                '<tr class="issueProductRow" id="issueProductRow_' + total +
                                '">' +
                                '<td>' +
                                '<input class="productId_' + productId + ' productId_' + total +
                                '" type="hidden" name="productId[]" value="">' +
                                '<input class="productName_' + total +
                                '" type="text" name="productName[]" value="" readonly>' +
                                '</td>' +
                                '<td>' +
                                '<input class="productModel_' + total +
                                '" type="hidden" name="productModel[]" value="" readonly>' +
                                '<input style="text-align: right;" class="productQty productQty_' +
                                total +
                                '" type="number" name="productQty[]" value="1" readonly>' +
                                '</td>' +
                                '<td>' +
                                '<input style="text-align: right;" class="productPrice productPrice_' +
                                total +
                                '" type="number" name="productPrice[]" value="" required readonly>' +
                                '</td>' +
                                '<td>' +
                                '<input style="text-align: right;" class="productCommision_' +
                                total +
                                '" onkeyup="updateAmount(event)" type="number" name="commission[]" value="">' +
                                '</td>' +
                                '<td>' +
                                '<input style="text-align: right;" class="amount amount_' +
                                total +
                                '" type="number" name="amount[]" value="" readonly>' +
                                '</td>' +
                                '<td align="center">' +
                                '<span class="btn btn-outline-danger btn-sm item_remove" onclick="withoutApprovalRowRemove(' +
                                total + ')" style="width: 100%;">' +
                                '<i class="fa fa-trash"></i>' +
                                '</span>' +
                                '</td>' +
                                '</tr>'
                                );
                        $('.row_count').val(total);



                        $('.productId_' + total).val(product.id);
                        $('.productName_' + total).val(product.name);
                        $('.productModel_' + total).val(product.model_no);
                        $('.productCommision_' + total).val(commissionRate);
                        $('.productOffer_' + total).val(offerRate);
                        $('.productPrice_' + total).val(product.mrp_price);
                        $('.amount_' + total).val(netAmount);
                        $('.productQty_' + total).val(response.qty);


                        var totalQty = parseInt($('.totalQty').val());
                        totalQty = totalQty + parseInt(response.qty);
                        $('.totalQty').val(totalQty);


                        $('.totalAmount').val(totalAmount.toFixed(2));
                    },
                    error: function (response) {

                    }
                });

            }
        }
    });
</script>

@endsection

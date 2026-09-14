@extends('admin.layouts.masterAddEdit')

@section('card_body')
    <style type="text/css">
        .chosen-single {
            height: 35px !important;
        }

    </style>
    @php
    $transferNo = '';
    use App\Transfer;

    $maxTransfer = Transfer::where('company_id', auth()->user()->company_id)->max('id');

    if (@$maxTransfer) {
        $transferNo = 1000000 + $maxTransfer + 1;
    } else {
        $transferNo = 1000000 + 1;
    }
    @endphp

    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-4">
                        <label for="transfer-no">Transfer No</label>
                        <div class="form-group {{ $errors->has('transferNo') ? ' has-danger' : '' }}">
                            <input type="text" class="form-control" name="transferNo" value="{{ $transferNo }}" required
                                readonly />
                            @if ($errors->has('transferNo'))
                                @foreach ($errors->get('transferNo') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="col-md-8">
                        <label for="transfer-date">Transfer Date</label>
                        <div class="form-group {{ $errors->has('transferDate') ? ' has-danger' : '' }}">
                            <input type="text" class="form-control add_datepicker" name="transferDate" id="" value=""
                                readonly>
                            @if ($errors->has('transferDate'))
                                @foreach ($errors->get('transferDate') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <label for="host">Source Store</label>
                <div class="form-group">
                    <select class="form-control chosen-select host" id="host" name="host" required="">
                        <option value="">Select Host</option>
                        @foreach ($storeAndShowrooms as $storeAndShowroom)
                            <option value="{{ $storeAndShowroom->id }},{{ $storeAndShowroom->type }}">
                                {{ $storeAndShowroom->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <label for="destination">Destination Store</label>
                <div class="form-group" id="destination-select-menu">
                    <select class="form-control chosen-select destination" id="destination" name="destination">
                        <option value="">Select Destination</option>
                    </select>
                </div>
            </div>
            

            <div class="col-md-2">
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
                <label for="products">Products</label>
                <div class="form-group">
                    <select class="form-control chosen-select product" id="product" name="product" required="">
                        <option value="">Select Product</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} --
                                ({{ $product->model_no }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6" id="serialNoColumn">
                        <label for="serial_no">Serial No</label>
                        <div class="form-group">
                            <select class="form-control chosen-select serial_no" id="serial_no">
                                <option value="">Select Serial</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4" id="consumerRemainQtyColumn" style="display: none">
                        <label for="">Remain Qty</label>
                        <div class="form-group">
                            <input type="number" class="form-control" id="consumerRemainQty" value="0" min="0" readonly>
                        </div>
                    </div>
                    <div class="col-md-4" id="consumerQtyColumn" style="display: none">
                        <label for="qty">Issue Qty</label>
                        <div class="form-group">
                            <input type="number" class="form-control Qty" id="consumerQty" value="1" min="1">
                        </div>
                    </div>
                    <div class="col-md-6 addIC">
                        <div class="form-group">
                            <label for=""></label>
                            <span class="btn btn-outline-success" style="width: 100%;" onclick="addItem()">
                                <i class="fa fa-arrow-down"></i> Add to list
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <h4>Transfered Products</h4>
                </div>
            </div>

            <div class="row">

                <div class="col-md-12" id="wTable">
                    <label for=""></label>
                    <div class="form-group">
                        <table class="table table-striped productList" id="dataTable">
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
                                    <th style="text-align: center;">Total Quantity</th>
                                    <td colspan="3"><input class="form-control totalQty text-center font-weight-bold"
                                            id="totalQty" type="text" name="totalQty" value="0" readonly></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                
                
                <div class="col-md-12" id="sTable" style="display: none">
                    <label for=""></label>
                    <div class="form-group">
                        <table class="table table-striped sTable">
                            <thead class="bg-success">
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
                                    <th style="text-align: center;">Total Quantity</th>
                                    <td colspan="3"><input class="form-control stotalQty text-center font-weight-bold"
                                            id="stotalQty" type="text" name="stotalQty" value="0" readonly></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                
                
            </div>
        </div>
    @endsection

    @section('custom-js')
        <script type="text/javascript">
            $(document).on('change', '#host', function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $('.productList tbody tr').remove();
                $('#product').val("").trigger('chosen:updated');

                var host = $('#host').val().split(',');

                var hostId = host[0];
                var hostType = host[1];

                $.ajax({
                    type: 'post',
                    url: '{{ route('transferProduct.storeAndShowroomInfo') }}',
                    data: {
                        hostId: hostId,
                        hostType: hostType
                    },
                    success: function(data) {
                        $('#destination-select-menu').html(data);
                        $(".chosen-select").chosen();
                    }
                });
            });


            $(document).on('change', '#product', function() {
                $('#serial_no option').remove();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                
                var type = $('#productType').val();
                
                if (type == 'warranty_product' || type == 'spare_parts') {
                    if ($('#host').val() == "") {
                        swal('Please Select Host', '', 'warning');
                        $('#product').val("").trigger('chosen:updated');
                        $('.chosen-select').chosen();
                        $('.chosen-select').trigger("chosen:updated");
                    } else {
                        // if ($('#supplier').val() == "") {
                        //     swal('Please Select Supplier', '', 'warning');
                        //     $('#product').val("").trigger('chosen:updated');
                        // } else {
                        $('.availableProductRow').remove();

                        var productId = $('#product').val();
                        var host = $('#host').val().split(',');
                        var vendorId = $('#supplier').val();
                        var hostId = host[0];
                        var hostType = host[1];

                        $.ajax({
                            type: 'get',
                            url: '{{ route('transferProduct.liftingProductInfo') }}',
                            data: {
                                productId: productId,
                                hostType: hostType,
                                hostId: hostId,
                            },
                            success: function(data) {
                                var liftingProducts = data.liftingProducts;
                                $('#serial_no option').remove();
                                if (liftingProducts.length > 0) {
                                    liftingProducts.forEach(function(item, index) {
                                        var option = '<option id="serial_' + item.serial_no +
                                            '" value="' + item.serial_no +
                                            '">' + item.serial_no + '</option>';
                                        $('#serial_no').append(option);
                                    });


                                    $('.chosen-select').chosen();
                                    $('.chosen-select').trigger("chosen:updated");
                                }
                            }
                        });
                     }
                    } else {
                    var productId = $('#product').val();
                    var host = $('#host').val().split(',');
                    var vendorId = $('#supplier').val();
                    var hostId = host[0];
                    var hostType = host[1];
                    
                    $.ajax({
                        type: 'get',
                        url: '{{ route('transferProduct.getConsumerProduct') }}',
                        data: {
                            product: productId, 
                            host: hostId,
                            reqQty: 0
                        },
                        success: function(data) {
                            $('#consumerRemainQty').val(data.remainQty);
                        }
                    });
                    
                } 
            });

            function addItem() {
                var type = $('#productType').val();
                
                if (type == 'warranty_product' || type == 'spare_parts') {
                    var serial_no = $('#serial_no').val();
                    var totalQty = parseInt($('#totalQty').val());

                    if (serial_no == '') {
                        swal('Please Select Serial No', '', 'warning');
                        return;
                    }

                    $.ajax({
                        type: 'post',
                        url: '{{ route('transferProduct.getSerialProduct') }}',
                        data: {
                            serial_no: serial_no,
                        },
                        success: function(data) {

                            if (totalQty == 0) {
                                $('.productList tbody tr').remove();
                            }

                            var product = data.liftingProduct;

                            var tr = `
                                <tr id="${product.serial_no}">
                                    <td>
                                        <input type="hidden" name="liftingProductId[]" value="${product.id}">
                                        <input type="text" class="form-control" value="${product.product.name}"
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" value="${product.product.model_no}"
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" value="${product.serial_no}"
                                    </td>
                                    <td>
                                        <span class="btn btn-outline-danger w-100" onclick="removeItem(${product.serial_no})"><i class="fa fa-remove"></i> Remove</span>
                                    </td>
                                </tr>
                            `;

                            $('.productList').append(tr);
                            $('#serial_' + serial_no).remove();


                            totalQty = totalQty + 1;
                            $('#totalQty').val(totalQty);

                            $('.chosen-select').chosen();
                            $('.chosen-select').trigger("chosen:updated");

                        }
                    });
                } else {
                    var productId = $('#product').val();
                    var totalQty = parseInt($('#stotalQty').val());
                    var host = $('#host').val().split(',');
                    var qty = $('#consumerQty').val();
                    
                    if(productId == ''){
                        swal('Please Select Product', '', 'warning');
                        return;
                    }
                    if (totalQty == 0) {
                        $('.sTable tbody tr').remove();
                    }
                    
                    var productTr = $('#cQty_'+productId).val();
                    if(productTr){
                        var Rqty = $('#cQty_' + productId).val();
                        var RtotalQty = parseInt($('#stotalQty').val());
                        RtotalQty = RtotalQty - parseInt(Rqty);
                        $('#stotalQty').val(RtotalQty);

                        $('#' + productId).remove();
                    }
                    
                    $.ajax({
                        type: 'get',
                        url: '{{ route('transferProduct.getConsumerProduct') }}',
                        data: {
                            product: productId, 
                            host: host[0],
                            reqQty: qty
                        },
                        success: function(data) {
                            if(data.remainQty >= data.reqQty){
                                var product = data.product;
                                var tr = `
                                <tr id="${product.id}">
                                    <td>
                                        <input type="hidden" class="form-control" name="productId[]" value="${product.id}">
                                        <input type="text" class="form-control" value="${product.name}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" value="${product.model_no}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control text-center" id="cQty_${product.id}" name="qty[]" value="${data.reqQty}">
                                    </td>
                                    <td>
                                        <span class="btn btn-outline-danger w-100" onclick="removeCItem(${product.id})"><i class="fa fa-remove"></i> Remove</span>
                                    </td>
                                </tr>
                            `;

                            $('.sTable').append(tr);
                            totalQty = totalQty + parseInt(data.reqQty);
                            $('#stotalQty').val(totalQty);
                            }else{
                               swal("Don't have enough Qty! ", "warning");
                                return; 
                            }
                        }
                    });
                            
                }
                
            }

            function removeItem(tr) {
                $('#' + tr).remove();
                $('#serial_no').append('<option id="serial_' + tr +
                    '" value="' + tr +
                    '">' + tr + '</option>');

                var totalQty = parseInt($('#totalQty').val());
                totalQty = totalQty - 1;
                $('#totalQty').val(totalQty);


                $('.chosen-select').chosen();
                $('.chosen-select').trigger("chosen:updated");
            }
            
            function removeCItem(tr) {
                var qty = $('#cQty_' + tr).val();
                var totalQty = parseInt($('#stotalQty').val());
                totalQty = totalQty - parseInt(qty);
                $('#stotalQty').val(totalQty);
                
                $('#' + tr).remove();
            }
        </script>
        
        <script>
            $('#productType').change(function () {
                var type = $(this).val();
                if (type == 'warranty_product' || type == 'spare_parts') {
                    $('#wTable, #serialNoColumn').show();
                    $('#sTable, #consumerQtyColumn, #consumerRemainQtyColumn').hide();
                    $('.addIC').removeClass('col-md-4').addClass('col-md-6');
                } else {
                    $('#sTable, #consumerQtyColumn, #consumerRemainQtyColumn').show();
                    $('#wTable, #serialNoColumn').hide();
                    $('.addIC').removeClass('col-md-6').addClass('col-md-4');
                }
                
                
                
                
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
                        $('#product').append(`<option value="">Select Product</option>`);
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

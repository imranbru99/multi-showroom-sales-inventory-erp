@extends('admin.layouts.masterAddEdit')

@section('custom_css')
    <style type="text/css">
        thead {
            background: #00c292;
            font-weight: bold !important;
            padding: 5px;
            font-size: 11px;
        }

    </style>
@endsection

@section('card_body')
    <div class="card-body">

        <div class="row mb-4">
            <div class="col-md-12">
                <h4 class="text-center py-3" style="font-weight: bold;font-family: tahoma; background-color: #ddd;">Return</h4>
            </div>
        </div>


        <div class="row">
            <div class="col-md-5">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="return-date">Invoice No</label>
                            <input type="text" class="form-control" name="invoice_no"
                                value="{{ $retailSalesReturn->invoice_no }}" id="invoice_no">
                                <input type="hidden" class="form-control" name="saleType" value="{{ $exchangeProducts->sale_type }}" id="saleType">
                                <input type="hidden" class="form-control" name="saleId" value="{{ $productExchange->sale_id }}" id="saleId">
                                <input type="hidden" class="form-control" name="exchangeId" value="{{ $productExchange->id }}" id="saleId">
                        </div>
                    </div>


                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="return-date">Date</label>
                            <input type="text" class="form-control datepicker" name="date"
                                value="{{ date('d-m-Y', strtotime($retailSalesReturn->date)) }}">
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-md-3">
                <div class="form-group">
                    <label for="project_id">Project</label>
                    <select name="project_id" id="project_id" class="form-control" required>
                        <option value="">Select Project</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}" @if ($retailSalesReturn->project_id == $project->id) selected @endif>{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="customerId">Customer</label>
                    <select name="customerId" id="customerId" class="form-control chosen-select">
                        <option value="">Select a customer</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" @if ($customer->id == $retailSalesReturn->customer_id)
                                selected
                        @endif
                        >{{ $customer->name }} ({{ $customer->code }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

        </div>


        <table class="table table-bordered mt-5" id="return">
            <thead>
                <tr>
                    <th>Sale Date</th>
                    <th>Product Name</th>
                    <th>Product Price</th>
                    <th>Product Model</th>
                    <th>Return</th>
                </tr>
            </thead>
            <tbody>
                <?php $totalReturn = 0; ?>
                @foreach ($retailSalesReturn->products as $product)
                <?php $totalReturn += $product->sales_price; ?>
                    <tr>
                        <td>{{ date('d-m-Y', strtotime($retailSalesReturn->date)) }}</td>
                        <td>{{ $product->product->name }}</td>
                        <td>{{ $product->sales_price }}</td>
                        <td>{{ $product->product->model_no }}</td>
                        <td>
                            {{-- <input type="checkbox" name="ids[]" value="{{ $product->sales_id }}" checked> --}}
                            <input type="checkbox" id="checked_{{ $product->sales_id }}" name = "ids[]" value = "{{ $product->sales_id }}" onclick="totalReurn({{ $product->sales_id }})" checked>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>



    <div class="card-body">

    <div class="row mb-4">
        <div class="col-md-12">
            <h4 class="text-center py-3" style="font-weight: bold;font-family: tahoma; background-color: #ddd;">Add</h4>
        </div>
    </div>
    <?php $totalSales = 0; ?>
    @foreach($exchangeProducts->products as $exchangeProduct)
    <?php $totalSales += $exchangeProduct->sales_price; ?>
    <div class="product-block short_long_installment" id="{{ $loop->iteration }}">

        <div class="row p-3 mt-3" style="border: 1px solid black">

            <div class="cls" style="display:none;">
                <div class="row">
                    <div class="col-md-12">
                        <span class="closeBtn text-danger" onclick="deleteProduct(event)">X</span>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group {{ $errors->has('productId') ? ' has-danger' : '' }}">
                    <label for="productId">Product Name</label>
                    <select class="form-control asd product chosen-select" name="productId[]" data-placeholder="Select Product">
                        <option value="">Select Product</option>
                        @foreach ($products as $product)
                        <option value="{{ $product->id }}" @if($exchangeProduct->product_id == $product->id) selected @endif>{{ $product->name }} ({{ $product->model_no }})
                        </option>
                        @endforeach
                    </select>
                    @if ($errors->has('productId'))
                    @foreach ($errors->get('productId') as $error)
                    <div class="form-control-feedback">{{ $error }}</div>
                    @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group {{ $errors->has('discount') ? ' has-danger' : '' }}">
                    <label for="discount">Discount</label>
                    <input type="number" min="0" name="discount[]" class="form-control discount" value="0" oninput="calculateInstallmentDueAmount(null, event)">
                    @if ($errors->has('discount'))
                    @foreach ($errors->get('discount') as $error)
                    <div class="form-control-feedback">{{ $error }}</div>
                    @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group {{ $errors->has('giftVoucher') ? ' has-danger' : '' }}">
                    <label for="gift-voucher">Gift Voucher</label>
                    <input type="number" min="0" name="giftVoucher[]" class="form-control giftVoucher" value="0" oninput="calculateInstallmentDueAmount(null, event)">
                    @if ($errors->has('giftVoucher'))
                    @foreach ($errors->get('giftVoucher') as $error)
                    <div class="form-control-feedback">{{ $error }}</div>
                    @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group {{ $errors->has('exchangeCrt') ? ' has-danger' : '' }}">
                    <label for="exchange-crt">Exchange CRT</label>
                    <input type="number" min="0" name="exchangeCrt[]" class="form-control exchangeCrt" value="0" oninput="calculateInstallmentDueAmount(null, event)">
                    @if ($errors->has('exchangeCrt'))
                    @foreach ($errors->get('exchangeCrt') as $error)
                    <div class="form-control-feedback">{{ $error }}</div>
                    @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group {{ $errors->has('cashPrice') ? ' has-danger' : '' }}">
                    <label for="cash-price">Cash Price</label>
                    <input type="number" min="0" name="cashPrice[]" class="form-control cashPrice cashPrice_{{ $loop->iteration }}" value="{{ $exchangeProduct->cash_price }}" step=".1">
                    @if ($errors->has('cashPrice'))
                    @foreach ($errors->get('cashPrice') as $error)
                    <div class="form-control-feedback">{{ $error }}</div>
                    @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group {{ $errors->has('salesPrice') ? ' has-danger' : '' }}">
                    <label for="cash-price">Sales Price</label>
                    <input type="number" min="0" name="salesPrice[]" class="form-control salesPrice salesPrice_{{ $loop->iteration }}" value="{{ $exchangeProduct->sales_price }}">
                    @if ($errors->has('salesPrice'))
                    @foreach ($errors->get('salesPrice') as $error)
                    <div class="form-control-feedback">{{ $error }}</div>
                    @endforeach
                    @endif
                </div>
            </div>

        </div>

    </div>
    @endforeach

    <div class="more-products"></div>

    <div class="row mt-3 mb-3 text-right">
        <div class="col-md-3 offset-md-9">
            <button type="button" id="addMoreProductBtn" class="btn btn-outline-primary">
                <i class="fa fa-plus-circle" aria-hidden="true"></i>
                Add More Products
            </button>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-md-4">
            <label for="cash-price">Return</label>
            <input type="text" class="form-control" value="{{ $totalReturn }}" id="totalReturn">
        </div>
        <div class="form-group col-md-4">
            <label for="cash-price">Total Add</label>
            <input type="text" class="form-control" value="{{ $totalSales }}" id="totalPrice">
        </div>
        <div class="form-group col-md-4">
            <label for="cash-price">Net Amount</label>
            <input type="text" class="form-control" value="{{ $totalReturn - $totalSales }}" id="netPrice">
        </div>
    </div>
</div>
@endsection

@section('custom-js')
<script>
    $('#customerId').change(function(e) {
        e.preventDefault();

        let customerId = $(this).val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "GET"
            , url: "{{ route('productExchange.customerProducts') }}"
            , data: {
                customerId: customerId
            },

            success: function(response) {


                $('#return tbody tr').remove();
                if (response.length > 0) {
                    response.forEach(function(item, index) {
                        const d = new Date(item.sale.sale_date);

                        month = '' + (d.getMonth() + 1)
                            , day = '' + d.getDate()
                            , year = d.getFullYear();

                        if (month.length < 2) month = '0' + month;
                        if (day.length < 2) day = '0' + day;

                        var tr = '<tr>' +
                            '<td>' + day + '-' + month + '-' + year + '</td>' +
                            '<td>' + item.product.name + '</td>' +
                            '<td id="return_price_'+ item.id +'">' + item.sales_price * item.qty + '</td>' +
                            '<td>' + item.product.model_no + '</td>' +
                            '<td><input type = "checkbox" id="checked_'+item.id+'" name = "ids[]" value = "' + item.id + '" onclick="totalReurn(' + item.id + ')"> </td>' +
                            '</tr>';
                        $('#return tbody').append(tr);

                        $('#invoice_no').val(item.sale.invoice_no);
                        $('#saleType').val(item.sale.sale_type);
                        $('#saleId').val(item.sale.id);
                        $('#totalReturn').val(0);
                    });
                }
            }
            , error: function(response) {
                console.log(response);
            }
        });

    });


    function totalReurn(id){

      var checked = $('#checked_' +id).is(":checked");

        var price = parseInt($('#return_price_' + id).html());
        var totalReturn = parseInt($('#totalReturn').val());
            if (checked == true) {
                $('#totalReturn').val(price + totalReturn);
            }
            if (checked != true) {
                $('#totalReturn').val(price - totalReturn);
            }
    }


</script>

<script>
    var href = $('.go_back').attr('href');
    $('.go_back').attr('href', href + '?project={{ @$project_id }}');

    $('#project_id').change(function() {
        var selected = $('#project_id').val();
        $('.go_back').attr('href', href + '?project=' + selected);
    });

</script>







<script>
    $(document).on('change', '.product', function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let parent = $(this).parent().parent().parent();

        var productId = parent.find('.product option:selected').val();

        if (productId != '') {
            $.ajax({
                type: 'get'
                , url: "{{ route('productExchange.getProductInfo') }}"
                , data: {
                    id: productId
                }
                , success: function(data) {

                    var saleType = $("#saleType").val();
                    var product = data.product;


                    var price = product.price;
                    var short_total_p = (price * 8) / 100;
                    var short_total = parseInt(price) + parseInt(short_total_p);
                    var long_total_p = (short_total * 12) / 100;
                    var long_total = parseInt(short_total) + parseInt(long_total_p);
                    parent.find('.cashPrice').val(price);
                    if (saleType == 'Cash') {
                        parent.find('.salesPrice').val(price);
                    }

                    if (saleType == 'Short Installment') {
                        parent.find('.salesPrice').val(short_total);
                    }

                    if (saleType == 'Long Installment') {
                        parent.find('.salesPrice').val(long_total);
                    }

                    total();
                }
            });
        }
    });


    function total() {
        // short_long_installment
        var total = 0;
        var discount = 0;
        var giftVoucher = 0;
        var exchangeCrt = 0;

        var saleType = $("#saleType").val();

        $('.salesPrice').each(function() {
            total = total + parseFloat($(this).val());
        });
        $('.discount').each(function() {
            discount = discount + parseFloat($(this).val());
        });
        $('.giftVoucher').each(function() {
            giftVoucher = giftVoucher + parseFloat($(this).val());
        });
        $('.exchangeCrt').each(function() {
            exchangeCrt = exchangeCrt + parseFloat($(this).val());
        });

        var grand = total - (discount + giftVoucher + exchangeCrt);

        $('#totalPrice').val(grand);

        var totalReturn = $("#totalReturn").val();
        var totalPrice = $("#totalPrice").val();

        $('#netPrice').val(totalReturn - totalPrice);

    }




    $('#addMoreProductBtn').click(function(e) {
            e.preventDefault();

            var row_id = $('.product-block:last').attr('id');
            var inc_row_id = parseInt(row_id) + 1;

            let row = `
            <div class="product-block short_long_installment" id="${inc_row_id}">
                <div class="row p-3 mt-3" style="border: 1px solid black">

                    <div class="cls">
                        <div class="row">
                            <div class="col-md-12">
                                <span class="closeBtn text-danger" onclick="deleteProduct(event)">X</span>
                            </div>
                        </div>
                    </div>  
                        <div class="col-md-6">
                            <div class="form-group {{ $errors->has('productId') ? ' has-danger' : '' }}">
                                <label for="productId">Product Name</label>
                                <select class="form-control asd product chosen-select" name="productId[]" data-placeholder="Select Product">
                                    <option value="">Select Product</option>
                                    @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->model_no }})
                                    </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('productId'))
                                @foreach ($errors->get('productId') as $error)
                                <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                                @endif
                            </div>
                        </div>


                        <div class="col-md-3">
                            <div class="form-group {{ $errors->has('discount') ? ' has-danger' : '' }}">
                                <label for="discount">Discount</label>
                                <input type="number" min="0" name="discount[]" class="form-control discount" value="0"
                                    oninput="calculateInstallmentDueAmount(null, event)">
                                @if ($errors->has('discount'))
                                    @foreach ($errors->get('discount') as $error)
                                        <div class="form-control-feedback">{{ $error }}</div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group {{ $errors->has('giftVoucher') ? ' has-danger' : '' }}">
                                <label for="gift-voucher">Gift Voucher</label>
                                <input type="number" min="0" name="giftVoucher[]" class="form-control giftVoucher" value="0"
                                    oninput="calculateInstallmentDueAmount(null, event)">
                                @if ($errors->has('giftVoucher'))
                                    @foreach ($errors->get('giftVoucher') as $error)
                                        <div class="form-control-feedback">{{ $error }}</div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group {{ $errors->has('exchangeCrt') ? ' has-danger' : '' }}">
                                <label for="exchange-crt">Exchange CRT</label>
                                <input type="number" min="0" name="exchangeCrt[]" class="form-control exchangeCrt" value="0"
                                    oninput="calculateInstallmentDueAmount(null, event)">
                                @if ($errors->has('exchangeCrt'))
                                    @foreach ($errors->get('exchangeCrt') as $error)
                                        <div class="form-control-feedback">{{ $error }}</div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group {{ $errors->has('cashPrice') ? ' has-danger' : '' }}">
                                <label for="cash-price">Cash Price</label>
                                <input type="number" min="0" name="cashPrice[]" class="form-control cashPrice cashPrice_${inc_row_id}" oninput="cash_row_sum(${inc_row_id})" value="0" step=".1">
                                @if ($errors->has('cashPrice'))
                                    @foreach ($errors->get('cashPrice') as $error)
                                        <div class="form-control-feedback">{{ $error }}</div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group {{ $errors->has('salesPrice') ? ' has-danger' : '' }}">
                                <label for="cash-price">Sales Price</label>
                                <input type="number" min="0" name="salesPrice[]" class="form-control salesPrice salesPrice_${inc_row_id}" value="0">
                                @if ($errors->has('salesPrice'))
                                    @foreach ($errors->get('salesPrice') as $error)
                                        <div class="form-control-feedback">{{ $error }}</div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                </div>
            </div>
            `;
            $('.more-products').append(row);
            $('.chosen-select').chosen();
            $('.chosen-select').trigger("chosen:updated");
        });


        function deleteProduct(e) {
            e.preventDefault();
            let parent = $(e.target).parent().parent().parent().parent();
            parent.remove();
            total();
        }
    </script>

@endsection


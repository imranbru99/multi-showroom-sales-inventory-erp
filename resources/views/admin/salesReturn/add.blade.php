@extends('admin.layouts.masterAddEdit')

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

    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <label for="dealer-address">Return Date</label>
                <div class="form-group">
                    <input type="text" class="form-control add_datepicker" name="return_date">
                </div>
            </div>

            <div class="col-md-6">
                <label for="issue-no">Return No</label>
                <div class="form-group {{ $errors->has('productIssueNo') ? ' has-danger' : '' }}">
                    <input type="text" class="form-control" name="productIssueNo" value="{{ $returnNo }}" required>
                    @if ($errors->has('productIssueNo'))
                        @foreach ($errors->get('productIssueNo') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-6">
                <label for="dealer-name">Dealer</label>
                <div class="form-group">
                    <select class="form-control chosen-select dealer_id" name="dealer_id">
                        <option value=" ">Select Dealer</option>
                        @foreach ($dealers as $dealer)
                            <option value="{{ $dealer->id }}">{{ $dealer->name }} ({{ $dealer->code }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <label for="dealer-address">Return Reason</label>
                <div class="form-group">
                    <textarea class="form-control" id="reason" name="reason" rows="2"></textarea>
                </div>
            </div>

            <div class="col-md-6">
                <label for="dealer-name">Product</label>
                <div class="form-group">
                    <select class="form-control chosen-select product_id" name="product_id">
                        <option value=" ">Select Product</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->model_no }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <label></label>
                <span class="btn btn-outline-success addItem" style="width: 100%;">
                    <i class="fa fa-arrow-down"></i> Add Product
                </span>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <label></label>
                <div class="form-group">
                    <table class="table table-bordered table-striped table-sm gridTable" id="products">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th width="200px">Model</th>
                                <th width="150px">Serial</th>
                                <th width="40px">Qty</th>
                                <th width="80px">Price</th>
                                <th width="110px">Commision (%)</th>
                                <th width="80px">Promo</th>
                                <th width="80px">Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6"><input class="form-control text-right" value="Total"> </td>
                                <td colspan="2"><input class="form-control text-right totalCalAmount" value="" readonly>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <br>

    </div>
@endsection

@section('custom-js')
    <script type="text/javascript">
        function updateAmount(e) {
            const thisParent = $(e.target).parent().parent();
            const productPriceVal = thisParent.find('.productPrice').val();
            const amountLine = thisParent.find('.amountCalculate');

            let percentAmount = (productPriceVal / 100) * $(e.target).val();

            let newAmount = productPriceVal - percentAmount;

            amountLine.val(newAmount);

            // update total amount
            calculate();
        }


        function calculate() {
            var totalAmount = 0;
            $('.amountCalculate').each(function() {
                totalAmount += parseFloat($(this).val());
            });
            $('.totalCalAmount').val(totalAmount);
        }
    </script>

    <script>
        $(".addItem").click(function() {
            if ($(".dealer_id option:selected").val() == "") {
                swal("Please! Select A Dealer", "", "warning");
                return;
            }
            if ($(".product option:selected").val() == "") {
                swal("Please! Select A Product", "", "warning");
                return;
            }

            var issueListIds = [];
            $('.issueListId').each(function() {
                issueListIds.push(parseFloat($(this).val()));
            });

            console.log(issueListIds);

            var product_id = $(".product_id option:selected").val();
            var dealer_id = $(".dealer_id").val();


            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "{{ route('salesReturn.product') }}",
                data: {
                    product_id: product_id,
                    dealer_id: dealer_id,
                    issueListIds: issueListIds
                },
                success: function(response) {
                    var product = response.info;

                    if (response.status == true) {
                        var last_row_id = parseInt($('#products tbody tr:last').attr('id'));
                        var row_id = parseInt(last_row_id) + 1;
                        if (isNaN(row_id)) {
                            var row_id = 1;
                        }

                        // check if product is more than requsitioned end
                        $("#products tbody").append(
                            `
                        <tr id="${row_id}">
                                <td>
                                    <input type="text" value="${product.productName}" readonly>
                                    <input type="hidden" name="product_id" value="${product.product_id}" readonly>
                                    <input type="hidden" class="issueListId" name="ids[]" value="${product.issue_list_id}" readonly>
                                </td>
                                <td>
                                    <input type="text" name="productModel" value="${product.productModel}" readonly>
                                </td>

                                <td>
                                    <input style="text-align: right;" type="text" name="productQty"
                                        value="${product.sale_serial}" readonly>
                                </td>

                                <td>
                                    <input style="text-align: right;" type="number" name="productQty" value="${product.saleQty}"
                                        readonly>
                                </td>

                                <td>
                                    <input style="text-align: right;" type="number" name="productPrice"
                                        oninput="updateAmount(event)" class="productPrice" value="${product.salePrice}">
                                </td>

                                <td>
                                    <input style="text-align: right;" type="number" name="commission"
                                        oninput="updateAmount(event)" value="${product.saleCommission}">
                                </td>

                                <td>
                                    <input style="text-align: right;" type="number" name="offer" value="${product.saleOffer}"
                                        readonly>
                                </td>

                                <td>
                                    <input style="text-align: right;" type="number" name="amount" class="amountCalculate"
                                        oninput="calculate()" value="${product.saleAmount}" readonly>
                                </td>
                                <td class="text-center">
                                    <span class="btn btn-outline-danger btn-sm" onclick="remove(${row_id})"><i class="fa fa-trash"></i></span>
                                </td>

                            </tr>
                        `
                        );

                        calculate();
                    }


                    if (response.status == false) {
                        swal("No Product Found", "", "warning");
                        return;
                    }

                },
                error: function(response) {

                }
            });
        });


        function remove(id) {
            $('#' + id).remove();
            calculate();
        }
    </script>

@endsection

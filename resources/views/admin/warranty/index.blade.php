@extends('admin.layouts.master')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <h4 class="card-title">{{ $title }}</h4>
            </div>
            <div class="col-md-6 text-right">

            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row d-flex justify-content-center">
            <div class="col-md-3 form-group">
                <label for="serial_no">Serial No</label>
                <input type="text" name="serial_no" class="form-control" value="" id="sSerialNo">
            </div>
            <div class="col-md-2 form-group">
                <label for="serial_no"></label>
                <p><span class="btn btn-outline-info mt-2" onclick="serialNo();"><i class="fa fa-search"></i> Search</span></p>
            </div>
        </div>
        <hr>
        <div class="product-info">
            <div class="col-md-12 d-flex justify-content-center bg-dark">
                <h3 class="m-0 p-2 text-white">Warranty Status : <span id="warranty_status">None</span></h3>
            </div>
            <hr>

            <div class="row" id="all_information">

            </div>
        </div>
    </div>
</div>
@endsection
@section('custom-js')
<script>
    function serialNo() {
        var type = $('#type').val();
        var serial_no = $('#sSerialNo').val();
        $.ajax({
            type: "get"
            , url: "{{ route('warranty.getProductInfo') }}"
            , data: {
                type: type
                , serial_no: serial_no
            },

            success: function(response) {
                $('#all_information div').remove();
                $('#warranty_status').html('');

                if (response.status == 'true') {
                    if (response.type == 'dealer') {
                        var dealer = response.product.issue.dealer;
                        var product = response.product.product;
                        var sale = response.product.issue;
                        var saleProduct = response.product;

                        var formattedDate = new Date(sale.date);
                        var d = formattedDate.getDate();
                        var m = formattedDate.getMonth();
                        m += 1; // JavaScript months are 0-11
                        var y = formattedDate.getFullYear();

                        var saleDate = d + "-" + m + "-" + y;

                        var data = `
                        <div class="col-md-6">
                            <div class="table-responsive">
                                <h3>Sale Information (Dealer)</h3>
                                <table class="table table-bordered table-striped">
                                    <tbody>
                                        <tr>
                                            <th width="30%">Dealer Name</th>
                                            <td width="5%">:</td>
                                            <td width="60%">${dealer.name}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Contact No</th>
                                            <td width="5%">:</td>
                                            <td width="60%">${dealer.mobile}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Address</th>
                                            <td width="5%">:</td>
                                            <td width="60%">${dealer.address}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Sale Date</th>
                                            <td width="5%">:</td>
                                            <td width="60%">${saleDate}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Sales By</th>
                                            <td width="5%">:</td>
                                            <td width="60%">${response.product.issue.sales_by.name}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="table-responsive">
                            <h3>Product Information</h3>
                                <table class="table table-bordered table-striped">
                                    <tbody>
                                        <tr>
                                            <th width="30%">Product Name</th>
                                            <td width="5%">:</td>
                                            <td width="60%">${product.name}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Product Model</th>
                                            <td width="5%">:</td>
                                            <td width="60%">${saleProduct.model_no}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Serial No</th>
                                            <td width="5%">:</td>
                                            <td width="60%">${saleProduct.serial_no}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Warranty Month</th>
                                            <td width="5%">:</td>
                                            <td width="60%">${product.warranty}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Price</th>
                                            <td width="5%">:</td>
                                            <td width="60%">${saleProduct.price}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Promo</th>
                                            <td width="5%">:</td>
                                            <td width="60%">${saleProduct.offer}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Commission</th>
                                            <td width="5%">:</td>
                                            <td width="60%">${saleProduct.commission_rate}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Sales Price</th>
                                            <td width="5%">:</td>
                                            <td width="60%">${saleProduct.amount}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                `;

                        $('#all_information').append(data);
                    } else {
                        var customer = response.product.sale.customer;
                        var product = response.product.product;
                        var sale = response.product.sale;
                        var saleProduct = response.product;

                        var productPrice = saleProduct.cash_price;

                        var mrpPrice = productPrice + (productPrice * 0.08);
                        var higherPrice = mrpPrice + (mrpPrice * 0.12);
                        if (sale.sale_type == 'Short Installment') {
                            productPrice = mrpPrice;
                        }
                        if (sale.sale_type == 'Long Installment') {
                            productPrice = higherPrice;
                        }

                        var formattedDate = new Date(sale.sale_date);
                        var d = formattedDate.getDate();
                        var m = formattedDate.getMonth();
                        m += 1; // JavaScript months are 0-11
                        var y = formattedDate.getFullYear();

                        var saleDate = d + "-" + m + "-" + y;

                        var data = `
                        <div class="col-md-6">
                            <div class="table-responsive">
                                <h3>Sale Information (Retail)</h3>
                                <table class="table table-bordered table-striped">
                                    <tbody>
                                        <tr>
                                            <th width="30%">Customer Name</th>
                                            <td width="5%">:</td>
                                            <td width="60%">${customer.name}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Account No</th>
                                            <td width="5%">:</td>
                                            <td width="60%">${customer.code}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Contact No</th>
                                            <td width="5%">:</td>
                                            <td width="60%">${customer.phone_no}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Address</th>
                                            <td width="5%">:</td>
                                            <td width="60%">${customer.present_address}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Sale Date</th>
                                            <td width="5%">:</td>
                                            <td width="60%">${saleDate}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Sales By</th>
                                            <td width="5%">:</td>
                                            <td width="60%">${response.product.sale.seller.name}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="table-responsive">
                            <h3>Product Information</h3>
                                <table class="table table-bordered table-striped">
                                    <tbody>
                                        <tr>
                                            <th width="30%">Product Name</th>
                                            <td width="5%">:</td>
                                            <td width="60%">${product.name}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Product Model</th>
                                            <td width="5%">:</td>
                                            <td width="60%">${saleProduct.product_model}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Serial No</th>
                                            <td width="5%">:</td>
                                            <td width="60%">${saleProduct.product_serial}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Warranty Month</th>
                                            <td width="5%">:</td>
                                            <td width="60%">${product.warranty}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Price</th>
                                            <td width="5%">:</td>
                                            <td width="60%">${saleProduct.mrp_price}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Discount</th>
                                            <td width="5%">:</td>
                                            <td width="60%">${saleProduct.discount}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Gift Voucher</th>
                                            <td width="5%">:</td>
                                            <td width="60%">${saleProduct.gift_voucher}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Exchange Crt</th>
                                            <td width="5%">:</td>
                                            <td width="60%">${saleProduct.exchange_crt}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Sales Price</th>
                                            <td width="5%">:</td>
                                            <td width="60%">${saleProduct.sales_price}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                `;

                        $('#all_information').append(data);
                    }

                    if (response.warranty_status == "None" || response.warranty_status == 'End') {
                        $('#warranty_status').html(response.warranty_status).css("color", "red");
                    } else {
                        $('#warranty_status').html(response.warranty_status).css("color", "#00c292 !important");
                    }
                } else {
                    swal("No Product Found!", "", "warning");
                    $('#warranty_status').html('None');
                    return;
                }
            }
        });
    }

</script>
@endsection

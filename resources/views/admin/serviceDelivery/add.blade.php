@extends('admin.layouts.masterAddEdit')

@section('card_body')
<style type="text/css">
    .chosen-single{
        height: 35px !important;
    }
</style>

<div class="card-body">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group {{ $errors->has('invoice_no') ? ' has-danger' : '' }}">
                <label for="invoice_no">Job NO</label>
                <select name="job_id" class="form-control chosen-select job_no">
                    <option value="">Select Job NO</option>
                    @foreach($completeAllocations as $completeAllocation)
                    <option value="{{ $completeAllocation->id }}">{{ $completeAllocation->invoice_no }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group {{ $errors->has('delivery_issue_to') ? ' has-danger' : '' }}">
                <label for="delivery_issue_to">Delivery Issue To</label>
                <input type="text" class="form-control datepicker" name="delivery_issue_to" value="{{ date('d-m-Y') }}" required>
                @if ($errors->has('delivery_issue_to'))
                @foreach($errors->get('delivery_issue_to') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group {{ $errors->has('delivery_date') ? ' has-danger' : '' }}">
                <label for="delivery_date">Delivery Date</label>
                <input type="text" class="form-control datepicker" name="delivery_date" value="{{ date('d-m-Y') }}" required>
                @if ($errors->has('delivery_date'))
                @foreach($errors->get('delivery_date') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label for="dealer">Dealer</label>
                <input type="text" class="form-control dealer" value="" placeholder="Dealer" readonly>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label for="engineer">Engineer</label>
                <input type="text" class="form-control engineer" value="" placeholder="Engineer Name" readonly>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label for="rec_invoice">Product Receive Invoice No</label>
                <input type="text" class="form-control rec_invoice" value="" placeholder="Product Receive Invoice No" readonly>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group {{ $errors->has('product_name') ? ' has-danger' : '' }}">
                <label for="product_name">Product Name</label>
                <input type="text" class="form-control product_name" value="" required placeholder="Product Name" readonly>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group {{ $errors->has('product_model') ? ' has-danger' : '' }}">
                <label for="product_model">Product Model</label>
                <input type="text" class="form-control product_model" value="" required placeholder="Product Model" readonly>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group {{ $errors->has('product_serial') ? ' has-danger' : '' }}">
                <label for="product_serial">Product Serial</label>
                <input type="text" class="form-control product_serial" value="" required placeholder="Product Serial" readonly>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group {{ $errors->has('service_payment_type') ? ' has-danger' : '' }}">
                <label for="service_payment_type">Payment Type</label>
                <select class="form-control chosen-select" id="service_payment_type" name="service_payment_type">
                    <option value="cash">Cash</option>
                    <option value="credit">Credit</option>
                    <option value="free">Free</option>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group {{ $errors->has('service_amount') ? ' has-danger' : '' }}">
                <label for="service_amount">Service Amount</label>
                <input type="text" class="form-control" id="service_amount" name="service_amount" value="" required placeholder="Service Amount">
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group {{ $errors->has('product_serial') ? ' has-danger' : '' }}">
                <label for="spare_products">Spare Products</label>
                <div class="col-md-12">
                    <div class="row spare_products">
                        <input type="text" class="form-control spare_product_list spare_product_name col-md-6" value="" required placeholder="Product Name" readonly>
                        <input type="text" class="form-control spare_product_list spare_product_model col-md-3" value="" placeholder="Product Model" required readonly>
                        <input type="text" class="form-control spare_product_list spare_product_name col-md-3" value="" placeholder="Product Serial" required readonly>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group {{ $errors->has('delivery_condition') ? ' has-danger' : '' }}">
                <label for="delivery_condition">Delivery Condition</label>
                <textarea class="form-control" rows="5" name="delivery_condition" placeholder="Delivery Condition"></textarea>
            </div>
        </div>
    </div>
</div>
@endsection
@section('custom-js')
<script>
    $(document).on('change', '.job_no', function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var job_no = $('.job_no').val();
        if (job_no != '') {
            $.ajax({
                type: 'post',
                url: '{{ route('serviceDelivey.getInfo') }}',
                data: {
                    id: job_no
                },
                success: function (data) {
                    var info = data.info;
                    var service_product = data.service_product;
                    var dealer = data.dealer;
                    var product = data.product;
                    var spare_products = data.spare_products;
                    var staff = data.staff;

                    $('.dealer').val(dealer.name + ' (' + dealer.code + ')');
                    $('.engineer').val(staff.name);
                    $('.rec_invoice').val(service_product.invoice_no);
                    $('.product_name').val(product.name);
                    $('.product_model').val(info.product_model);
                    $('.product_serial').val(info.product_serial);
                    
                    var serviceProduct = 0;
                    if (data.spare_products.length > 0) {
                        $('.spare_product_list').remove();
                        data.spare_products.forEach(function (item, index) {
                            serviceProduct += parseInt(item.sale_price);
                            var products = `
                                        <input type="text" class="form-control spare_product_list spare_product_name col-md-6" value="${item.product_name}" required placeholder="Product Name" readonly>
                                        <input type="text" class="form-control spare_product_list spare_product_model col-md-3" value="${item.model_no}" placeholder="Product Model" required readonly>
                                        <input type="text" class="form-control spare_product_list spare_product_name col-md-3" value="${item.serial_no}" placeholder="Product Serial" required readonly>
                                        `;
                            $('.spare_products').append(products);
                        });
                        $('#service_amount').val(serviceProduct);
                    }


                }
            });
        }
    });
    
    $('#service_payment_type').change(function(){
        var type = $(this).val();
        if(type == 'free'){
            $('#service_amount').val(0);
        }else{
           $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

        var job_no = $('.job_no').val();
        if (job_no != '') {
            $.ajax({
                type: 'post',
                url: '{{ route('serviceDelivey.getInfo') }}',
                data: {
                    id: job_no
                },
                success: function (data) {
                    
                    var serviceProduct = 0;
                    if (data.spare_products.length > 0) {
                        data.spare_products.forEach(function (item, index) {
                            serviceProduct += parseInt(item.sale_price);
                        });
                        $('#service_amount').val(serviceProduct);
                    }


                }
            });
        } 
        }
    });
</script>
@endsection
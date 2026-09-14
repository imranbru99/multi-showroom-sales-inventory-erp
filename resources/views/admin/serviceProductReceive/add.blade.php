@extends('admin.layouts.masterAddEdit')

@section('card_body')
<style type="text/css">
    .chosen-single {
        height: 35px !important;
    }

</style>

<div class="card-body">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group {{ $errors->has('date') ? ' has-danger' : '' }}">
                <label for="date">Date</label>
                <input type="text" class="form-control datepicker" name="date" value="{{ date('d-m-Y') }}" required>
                @if ($errors->has('date'))
                @foreach($errors->get('date') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group {{ $errors->has('invoice_no') ? ' has-danger' : '' }}">
                <label for="invoice_no">Invoice No</label>
                <input type="number" class="form-control" name="invoice_no" value="{{ $invoiceNo }}" required>
                @if ($errors->has('invoice_no'))
                @foreach($errors->get('invoice_no') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group {{ $errors->has('product_serial') ? ' has-danger' : '' }}">
                <label for="serial_no">Serial No</label>
                <input type="text" class="form-control" id="product_serial" name="product_serial" value="">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group {{ $errors->has('product_id') ? ' has-danger' : '' }}">
                <label for="product_id">Product Name</label>
                <input type="text" id="productName" class="form-control" value="" readonly>
                <input type="hidden" id="productId" name="product_id" value="">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group {{ $errors->has('model_no') ? ' has-danger' : '' }}">
                <label for="model_no">Model No</label>
                <input type="text" class="form-control" id="model_no" name="model_no" value=""  readonly>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group {{ $errors->has('dealer_id') ? ' has-danger' : '' }}">
                <label for="dealer_id">Dealer</label>
                <input type="text" id="dealerName" class="form-control" value="" readonly>
                <input type="hidden" id="dealerId" name="dealer_id" value="">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="lifting_vendor">Lifting Vendor</label>
                <input type="text" id="vendorName" class="form-control" value="" readonly>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="sale_date">Sale Date</label>
                <input type="text" class="form-control datepicker" name="sale_date" value="">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="type">Type</label>
                <select class="form-control" name="type">
                    <option value="">Select Type</option>
                    <option value="dealer">Dealer</option>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="cn_no">CN No</label>
                <input type="text" class="form-control" name="cn_no" value="">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group {{ $errors->has('product_condition') ? ' has-danger' : '' }}">
                <label for="product_condition">Product Condition <span class="text-danger text-lowercase ml-3">* Don't use only 'Damage' Word *</span></label>
                <input type="text" class="form-control product_condition" name="product_condition" value="{{ old('product_condition') }}" required>
                @if ($errors->has('product_condition'))
                @foreach($errors->get('product_condition') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mt-4 mx-5 {{ $errors->has('damage') ? ' has-danger' : '' }}">
                <label for="damage">Damage</label>
                <input type="checkbox" name="damage" value="Damage" class="damage ml-3 mr-1" onclick="damageCheck()"> Yes
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group {{ $errors->has('qty') ? ' has-danger' : '' }}">
                <label for="qty">Qty</label>
                <input type="number" class="form-control" id="qty" name="qty" value="1" readonly>
                @if ($errors->has('qty'))
                @foreach($errors->get('qty') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group {{ $errors->has('problem_list') ? ' has-danger' : '' }}">
                <label for="problem_list">Problem List</label>
                <textarea class="form-control" name="problem_list" rows="5">{{ old('problem_list') }}</textarea>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group {{ $errors->has('remarks') ? ' has-danger' : '' }}">
                <label for="remarks">Remarks</label>
                <textarea class="form-control" name="remarks" rows="5">{{ old('remarks') }}</textarea>
            </div>
        </div>
    </div>
</div>
@endsection
@section('custom-js')
<script>
    $.fn.onEnterKey =
            function (closure) {
                $(this).keypress(
                        function (event) {
                            var code = event.keyCode ? event.keyCode : event.which;

                            if (code == 13) {
                                closure();
                                return false;
                            }
                        });
            }

    $('#product_serial').onEnterKey(
            function () {
                productInfo();
            });

    function productInfo() {
        var serialNo = $('#product_serial').val();
        
        if (serialNo == '') {
            swal({
                title: "Enter Serial Number",
                type: "warning",
                // timer: 1000,
            });
            return;
        }


        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "post",
            url: "{{ route('serviceProductReceive.productInfo') }}",
            data: {
                serialNo: serialNo
            },
            success: function (response) {
                $('#productId').val(response.productId);
                $('#productName').val(response.productName);
                $('#model_no').val(response.modelNo);
                $('#dealerId').val(response.dealerId);
                $('#dealerName').val(response.dealerName);
                $('#vendorName').val(response.liftingVendor);
                
            },
            error: function () {
                swal({
                    title: "No data found against " + serialNo,
                    type: "warning",
                    // timer: 1000,
                });
                $('#product_serial').val('');
                $('#productId').val('');
                $('#productName').val('');
                $('#model_no').val('');
                $('#dealerId').val('');
                $('#dealerName').val('');
                $('#vendorName').val('');
            }
        });
    }


    $(".buttonAddEdit").click(function (e) {

        e.preventDefault();

        var dealer = $('.dealer_id').val();
        var product = $('.product_id').val();

        if (dealer == '') {
            swal({
                title: "Select Dealer"
                , type: "warning",
                // timer: 1000,
            });
        }

        if (product == '') {
            swal({
                title: "Select Product"
                , type: "warning",
                // timer: 1000,
            });
        }

        if (dealer != '' && product != '') {
            $('#formAddEdit').submit();
        }
    });


    function damageCheck() {
        var checked = $('.damage').is(":checked");

        if (checked == true) {
            $('.product_condition').val('Damage');
            $('.product_condition').attr('readonly', true)
        }

        if (checked != true) {
            $('.product_condition').val('');
            $('.product_condition').attr('readonly', false)
        }
    }

</script>
@endsection

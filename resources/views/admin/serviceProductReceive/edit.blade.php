@extends('admin.layouts.masterAddEdit')

@section('card_body')
<style type="text/css">
    .chosen-single {
        height: 35px !important;
    }

</style>
<input type="hidden" class="form-control" name="id" value="{{ $serviceProductReceive->id }}">
<div class="card-body">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group {{ $errors->has('date') ? ' has-danger' : '' }}">
                <label for="date">Date</label>
                <input type="text" class="form-control datepicker" name="date" value="{{ date('d-m-Y', strtotime(@$serviceProductReceive->receive_date)) }}" required>
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
                <input type="number" class="form-control" name="invoice_no" value="{{ $serviceProductReceive->invoice_no }}" required>
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
                <input type="text" class="form-control" name="product_serial" value="{{ $serviceProductReceive->product_serial }}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group {{ $errors->has('product_id') ? ' has-danger' : '' }}">
                <label for="product_id">Product Name</label>
                <input type="text" class="form-control" value="{{ @$serviceProductReceive->product->name }}" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group {{ $errors->has('model_no') ? ' has-danger' : '' }}">
                <label for="model_no">Model No</label>
                <input type="text" class="form-control" value="{{ $serviceProductReceive->product_model }}" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group {{ $errors->has('dealer_id') ? ' has-danger' : '' }}">
                <label for="dealer_id">Dealer</label>
                <input type="text" class="form-control" value="{{ @$serviceProductReceive->dealer->name }}" readonly>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="lifting_vendor">Lifting Vendor</label>
                <input type="text" id="vendorName" class="form-control" value="{{ @$serviceProductReceive->lifting->lifting->vendor->name }}" readonly>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="sale_date">Sale Date</label>
                <input type="text" class="form-control datepicker" name="sale_date" value="{{ $serviceProductReceive->sale_date ? date('d-m-Y', strtotime($serviceProductReceive->sale_date)) : '' }}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="type">Type</label>
                <select class="form-control" name="type">
                    <option value="">Select Type</option>
                    <option value="dealer" @if($serviceProductReceive->type) selected @endif>Dealer</option>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="cn_no">CN No</label>
                <input type="text" class="form-control" name="cn_no" value="{{ $serviceProductReceive->cn_no }}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group {{ $errors->has('product_condition') ? ' has-danger' : '' }}">
                <label for="product_condition">Product Condition</label>
                <input type="text" class="form-control product_condition" name="product_condition" value="{{ $serviceProductReceive->product_condition }}" required>
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
                <input type="checkbox" name="damage" value="Damage" class="damage ml-3 mr-1" onclick="damageCheck()" @if($serviceProductReceive->product_condition == "Damage") checked @endif> Yes
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group {{ $errors->has('qty') ? ' has-danger' : '' }}">
                <label for="qty">Qty</label>
                <input type="number" class="form-control" value="{{ $serviceProductReceive->qty }}" readonly>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group {{ $errors->has('problem_list') ? ' has-danger' : '' }}">
                <label for="problem_list">Problem List</label>
                <textarea class="form-control" name="problem_list" rows="5">{{ $serviceProductReceive->problem_list }}</textarea>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group {{ $errors->has('remarks') ? ' has-danger' : '' }}">
                <label for="remarks">Remarks</label>
                <textarea class="form-control" name="remarks" rows="5">{{ @$serviceProductReceive->remarks->remarks }}</textarea>
            </div>
        </div>
    </div>
</div>
@endsection
@section('custom-js')
<script>
//    window.onload = function () {
//        damageCheck();
//    }

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

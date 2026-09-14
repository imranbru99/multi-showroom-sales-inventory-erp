@extends('admin.layouts.masterAddEdit')

@section('card_body')
<style type="text/css">
    .chosen-single{
        height: 35px !important;
    }
</style>

@php
use App\PaymentToCompany;
$supplierPayment = PaymentToCompany::max('id');

if(!$supplierPayment)
{
$paymentNo = 1000000+1;
}
else
{
$paymentNo = 1000000+$supplierPayment+1;
}
@endphp

<div class="card-body">
    <div class="row">
        <div class="col-md-4">
            <label for="vendor">Vendors</label>
            <div class="form-group {{ $errors->has('vendorId') ? ' has-danger' : '' }}">
                <select class="form-control chosen-select vendor" id="vendorId" name="vendorId">
                    <option value="">Select Vendors</option>
                    @foreach ($vendors as $vendor)
                    <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                    @endforeach
                </select>
                @if ($errors->has('vendorId'))
                @foreach($errors->get('vendorId') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <label for="payment-no">Payment No</label>
            <div class="form-group {{ $errors->has('paymentNo') ? ' has-danger' : '' }}">
                <input type="text" class="form-control" name="paymentNo" value="{{$paymentNo}}" required readonly>
                @if ($errors->has('paymentNo'))
                @foreach($errors->get('paymentNo') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
        
        <div class="col-md-4">
            <label for="payment-date">Payment Date</label>
            <div class="form-group {{ $errors->has('paymentDate') ? ' has-danger' : '' }}">
                <input type="text" class="form-control add_datepicker" name="paymentDate" required readonly>
                @if ($errors->has('paymentDate'))
                @foreach($errors->get('paymentDate') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <label for="current-due">Current Due</label>
            <div class="form-group {{ $errors->has('currentDue') ? ' has-danger' : '' }}">
                <input type="text" class="form-control currentDue" name="currentDue" value="0" required readonly>
                @if ($errors->has('currentDue'))
                @foreach($errors->get('currentDue') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
        
        <div class="col-md-4">
            <label for="payment-now">Payment Now</label>
            <div class="form-group {{ $errors->has('paymentNow') ? ' has-danger' : '' }}">
                <input type="number" class="form-control paymentNow" name="paymentNow" oninput="Balance()" value="0" required>
                @if ($errors->has('paymentNow'))
                @foreach($errors->get('paymentNow') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <label for="balance">Balance</label>
            <div class="form-group {{ $errors->has('balance') ? ' has-danger' : '' }}">
                <input type="text" class="form-control balance" name="balance" value="0" required readonly>
                @if ($errors->has('balance'))
                @foreach($errors->get('balance') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
        
        <div class="col-md-4">
            <label for="money-receipt">Money Receipt</label>
            <div class="form-group {{ $errors->has('moneyReceipt') ? ' has-danger' : '' }}">
                <input type="text" class="form-control" name="moneyReceipt">
                @if ($errors->has('moneyReceipt'))
                @foreach($errors->get('moneyReceipt') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <label for="payment-method">Payment Types</label>
            <div class="form-group {{ $errors->has('paymentType') ? ' has-danger' : '' }}">
                @php
                $paymentTypes = array('Cash'=>'Cash','Check'=>'Cheque','Others'=>'Others')
                @endphp
                <select class="form-control chosen-select paymentType" id="paymentType" name="paymentType">
                    <option value="">Select Payment Type</option>
                    @foreach ($paymentTypes as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
                @if ($errors->has('paymentType'))
                @foreach($errors->get('paymentType') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
        
        <div class="col-md-4">
            <label for="remarks">Rmarks</label>
            <div class="form-group {{ $errors->has('remarks') ? ' has-danger' : '' }}">
                <textarea class="form-control" name="remarks" rows="2"></textarea>
                @if ($errors->has('remarks'))
                @foreach($errors->get('remarks') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom-js')
<script type="text/javascript">
    $("form").submit(function () {
        var vendor = $(".vendor option:selected").val();
        var paymentType = $(".paymentType option:selected").val();

        if (vendor == '')
        {
            swal("Please! Select A Supplier", "", "warning");
            return false;
        }

        if (paymentType == '')
        {
            swal("Please! Select A Payment Method", "", "warning");
            return false;
        }
    });

    $(document).ready(function () {
        // Get Purchase order id
        $(".vendor").change(function () {
            var vendorId = $('.vendor option:selected').val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: "{{route('getVendorInfo')}}",
                data: {vendorId: vendorId},
                success: function (data) {
                    var lifting = parseFloat(data.lifting);
                    var liftingReturn = parseFloat(data.liftingReturn);
                    var currentDue = parseFloat(data.currentDue);

                    var currentDue = lifting - liftingReturn - currentDue;

                    $(".currentDue").val(currentDue);
                    Balance();
                }
            })
        });
    });
</script>

<script type="text/javascript">
    function Balance() {
        var currentDue = $(".currentDue").val();
        var paymentNow;

        if ($(".paymentNow").val() == "")
        {
            paymentNow = 0;
        } else
        {
            paymentNow = $('.paymentNow').val();
        }

        var balance = parseFloat(currentDue) - parseFloat(paymentNow);

        if (balance < 0)
        {
            // swal('Invalid Payment!','','warning');
            // $('.paymentNow').val(currentDue);
            // paymentNow = currentDue;
            balance = parseFloat(currentDue) - parseFloat(paymentNow);
        }

        if (balance == currentDue)
        {
            $(".balance").val("0");
        } else
        {
            $(".balance").val(balance.toFixed(2));
        }
    }
</script>
@endsection
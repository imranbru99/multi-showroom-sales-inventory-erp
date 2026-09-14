@extends('admin.layouts.masterAddEdit')

@php
use App\DealerSetup;
use App\LiftingProduct;

$dealerInfo = DealerSetup::where('id', $issuedProduct->dealer_id)->first();
@endphp

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

        <div class="col-md-4">
            <label for="dealer">Dealer</label>
            <div class="form-group">
                <select class="form-control chosen-select dealer" id="dealer" name="dealer" required>

                    <option value="">Select Dealer</option>
                    @foreach ($dealers as $dealer)
                    <option value="{{ $dealer->id }}" @if($dealer->id == $issuedProduct->dealer_id) selected @endif>{{ $dealer->name }}</option>
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
                    <option value="{{ $staff->id }}" @if($staff->id == $issuedProduct->sales_by) selected @endif>{{ $staff->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-2">
            <label for="issue-no">Issue No</label>
            <div class="form-group {{ $errors->has('productIssueNo') ? ' has-danger' : '' }}">
                <input type="text" class="form-control" name="productIssueNo" value="{{ $issuedProduct->issue_no }}"
                       required readonly>
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
                <input type="text" class="form-control datepicker" name="issueDate"
                       value="{{ date('d-m-Y', strtotime($issuedProduct->date)) }}" readonly>
                @if ($errors->has('issueDate'))
                @foreach ($errors->get('issueDate') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <input type="hidden" class="form-control" id="dealerId" name="dealerId"
                       value="{{ $issuedProduct->dealer_id }}" readonly>
            </div>
            <div class="col-md-4">
                <input type="hidden" class="form-control" id="issueId" name="issueId"
                       value="{{ $issuedProduct->id }}" readonly>
            </div>
            <div class="col-md-4">
                <input type="hidden" class="form-control" id="dealerRequisitionId" name="dealerRequisitionId"
                       value="{{ $issuedProduct->requisition_id }}" readonly>
            </div>
        </div>
    </div>

    @if($issuedProduct->product_type == 'warranty_product')
    <div class="row">
        <div class="col-md-12">
            <label></label>
            <div class="form-group">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm issueProductList">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th width="200px">Model</th>
                                <th width="150px">Serial</th>
                                <th width="40px">Qty</th>
                                <th width="80px">Price</th>
                                <th width="110px">Commision (%)</th>
                                <th width="80px">Amount</th>

                            </tr>
                        </thead>
                        <tbody id="tbody">
                            @foreach ($issuedProductLists as $issuedProductList)
                            <?php
                            $productSerials = LiftingProduct::where('product_id', $issuedProductList->product_id)
                                    ->where('status', '1')
                                    ->get();
                            ?>
                            <tr class="issueProductRow_{{ $issuedProductList->product_id }}">
                                <td>
                                    <input class="productId_{{ $issuedProductList->product_id }}" type="hidden"
                                           name="productId[]" value="{{ $issuedProductList->product_id }}">
                                    <input class="productName_{{ $issuedProductList->product_id }}" type="text" size='15'
                                           name="productName[]" value="{{ $issuedProductList->productName }}" readonly>
                                </td>
                                <td>
                                    <input class="productModel_{{ $issuedProductList->product_id }}" type="text"
                                           name="productModel[]" value="{{ $issuedProductList->model_no }}" readonly>
                                </td>
                                <td>
                                    @if($issuedProduct->type == 2)
                                    <input type="text"  class="form-control productSerialNo_{{ $issuedProductList->product_id }}"
                                           id="productSerialNo_{{ $issuedProductList->product_id }}" value="{{ $issuedProductList->serial_no}}"
                                           name="productSerial[]">
                                    @else
                                    <select
                                        class="form-control chosen-select productSerialNo_{{ $issuedProductList->product_id }}"
                                        id="productSerialNo_{{ $issuedProductList->product_id }}"
                                        name="productSerial[]">
                                        <option value="">Select Serial No</option>
                                        @foreach ($productSerials as $productSerial)
                                        <?php
                                        if ($productSerial->serial_no == $issuedProductList->serial_no) {
                                            $select = 'selected';
                                        } else {
                                            $select = '';
                                        }
                                        ?>
                                        <option value="{{ $productSerial->serial_no }}" {{ $select }}>
                                            {{ $productSerial->serial_no }}</option>
                                        @endforeach
                                    </select>
                                    @endif
                                </td>

                                <td>
                                    <input style="text-align: right;"
                                           class="productQty productQty_{{ $issuedProductList->product_id }}"
                                           type="text" name="productQty[]" size="15" value="{{ $issuedProductList->qty }}"
                                           readonly>
                                </td>

                                <td>
                                    <input style="text-align: right;"
                                           class="productPrice productPrice_{{ $issuedProductList->product_id }}"
                                           type="number" name="productPrice[]" value="{{ $issuedProductList->price }}"
                                           required readonly>
                                </td>

                                <td>
                                    <input style="text-align: right;"
                                           class="productCommision_{{ $issuedProductList->product_id }}" type="number"
                                           name="commission[]" value="{{ $issuedProductList->commission_rate }}"
                                           onkeyup="updateAmount(event)">
                                </td>

                                <td>
                                    <input style="text-align: right;"
                                           class="amount amount_{{ $issuedProductList->product_id }}" type="number"
                                           name="amount[]" value="{{ $issuedProductList->amount }}" readonly>
                                </td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-md-12">
            <label></label>
            <div class="form-group">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm issueProductList">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th width="40px">Qty</th>
                                <th width="80px">Price</th>
                                <th width="110px">Commision (%)</th>
                                <th width="80px">Amount</th>

                            </tr>
                        </thead>
                        <tbody id="tbody">
                            @foreach ($issuedProductLists as $issuedProductList)
                            <tr class="issueProductRow_{{ $issuedProductList->product_id }}">
                                <td>
                                    <input class="productId_{{ $issuedProductList->product_id }}" type="hidden"
                                           name="productId[]" value="{{ $issuedProductList->product_id }}">
                                    <input class="w-100 productName_{{ $issuedProductList->product_id }}" type="text"
                                           name="productName[]" value="{{ $issuedProductList->productName }}" readonly>
                                </td>
                                <td>
                                    <input class="productModel_{{ $issuedProductList->product_id }}" type="hidden"
                                           name="productModel[]" value="{{ $issuedProductList->model_no }}" readonly>
                                    
                                    
                                    <input style="text-align: right;"
                                           class="productQty productQty_{{ $issuedProductList->product_id }}"
                                           type="text" name="productQty[]" value="{{ $issuedProductList->qty }}"
                                           readonly>
                                </td>

                                <td>
                                    <input style="text-align: right;"
                                           class="productPrice productPrice_{{ $issuedProductList->product_id }}"
                                           type="number" name="productPrice[]" value="{{ $issuedProductList->price }}"
                                           required readonly>
                                </td>

                                <td>
                                    <input style="text-align: right;"
                                           class="productCommision_{{ $issuedProductList->product_id }}" type="number"
                                           name="commission[]" value="{{ $issuedProductList->commission_rate }}"
                                           onkeyup="updateAmount(event)">
                                </td>

                                <td>
                                    <input style="text-align: right;"
                                           class="amount amount_{{ $issuedProductList->product_id }}" type="number"
                                           name="amount[]" value="{{ $issuedProductList->amount }}" readonly>
                                </td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <label for="total-qty">Total Quantity</label>
            <div class="form-group">
                <input style="text-align: right;" class="form-control totalQty" type="number" name="totalQty"
                       value="{{ $issuedProduct->total_qty }}" readonly>
            </div>
        </div>

        <div class="col-md-6">
            <label for="taotal-amount">Total Amount</label>
            <div class="form-group">
                <input style="text-align: right;" class="form-control totalAmount" type="number" name="totalAmount"
                       value="{{ $issuedProduct->total_amount }}" readonly>
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
        const qty = thisParent.find('.productQty').val();
        const amountLine = thisParent.find('.amount');

        let percentAmount = (productPriceVal / 100) * $(e.target).val();

        let newAmount = productPriceVal - percentAmount;

        amountLine.val(newAmount * qty);

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

    function itemRemove(i) {
        var issueQty = parseInt($('.issueQty_' + i).val());
        issueQty = issueQty - 1;
        $('.issueQty_' + i).val(issueQty);

        var productPrice = parseFloat($('.productPrice_' + i).val());

        var totalQty = parseInt($('.totalQty').val());
        totalQty = totalQty - 1;
        $('.totalQty').val(totalQty);

        var totalAmount = parseInt($('.totalAmount').val());
        totalAmount = totalAmount - productPrice;
        $('.totalAmount').val(totalAmount);

        $('.issueProductRow_' + i).remove();
    }

    function findTotalAmount(i) {
        var rate = parseFloat($('.productPrice_' + i).val());
        var qty = parseFloat($('.productQty_' + i).val());
        var amount = rate * qty;

        $('.amount_' + i).val(Math.round(amount));

        rowSum();
    }

    function rowSum() {
        var totalQty = 0;
        var totalAmount = 0;
        $(".productQty").each(function () {
            var stvalTotal = parseFloat($(this).val());
            // console.log(stval);
            totalQty += isNaN(stvalTotal) ? 0 : stvalTotal;
        });

        $(".amount").each(function () {
            var stvalAmount = parseFloat($(this).val());
            // console.log(stval);
            totalAmount += isNaN(stvalAmount) ? 0 : stvalAmount;
        });

        $('.totalQty').val(totalQty);
        $('.totalAmount').val(Math.round(totalAmount));
    }

</script>

@endsection

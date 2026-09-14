@extends('admin.layouts.masterAddEdit')

@section('custom_css')
    <style type="text/css">
        .blockTitle {
            color: #333;
            font-family: tahoma;
            border-bottom: 1px solid #a2a2a2;
            display: inline-block;
            padding-bottom: 6px;
        }

        .guarantorInfo {
            border: 1px solid #000;
            margin-top: 14px;
        }

        .cls {
            position: absolute;
            right: 22px;
        }

        .closeBtn:hover {
            cursor: pointer;
        }

    </style>
@endsection

@section('card_body')
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <h4 class="text-center py-3" style="font-weight: bold;font-family: tahoma; background-color: #ddd;">Sales
                    Information</h4>
            </div>
        </div>

        <div class="row">

            <div class="col-md-3">
                <label for="purchase-type">Sales Type</label>
                <div class="form-group {{ $errors->has('purchaseType') ? ' has-danger' : '' }}">
                    <select name="purchaseType" id="purchaseType" class="form-control purchaseType chosen-select" readonly>
                        <option value="Cash">Cash</option>
                        <option value="Short Installment">Short Installment</option>
                        <option value="Long Installment">Long Installment</option>
                    </select>
                </div>
            </div>

            <input type="hidden" value="{{ $retailSales->id }}" name="retailSalesId">

            <div class="col-md-3">
                <div class="form-group {{ $errors->has('dealerId') ? ' has-danger' : '' }}">
                    <label for="showroom-id">Project Name</label><span style="color: red; font-size: 18px;">*</span>
                    <select class="form-control chosen-select select_project" name="dealerId" @if ($showroomId == 1) required="" @endif>
                        <option value="">Select Showroom Project Name</option>
                        @foreach ($showroomProjects as $showroomProject)
                            <option value="{{ $showroomProject->id }}" @if ($retailSales->dealer_id == $showroomProject->id) selected @endif>
                                {{ $showroomProject->name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('dealerId'))
                        @foreach ($errors->get('dealerId') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group {{ $errors->has('purchaseDate') ? ' has-danger' : '' }}">
                    <label for="purchase-date">Sale Date</label>
                    <input type="text" name="purchaseDate" value="{{ date('d-m-Y', strtotime($retailSales->sale_date)) }}"
                        class="form-control datepicker" readonly="">
                    @if ($errors->has('purchaseDate'))
                        @foreach ($errors->get('purchaseDate') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="nick-name">Invoice No</label>
                    <input type="text" class="form-control" name="invoice_no" value="{{ $retailSales->invoice_no }}">
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-3">
                <div class="form-group {{ $errors->has('customerId') ? ' has-danger' : '' }}">
                    <label for="customer-ac-no">Customer A/C No.</label><span style="color: red; font-size: 18px;">*</span>
                    <select class="form-control chosen-select customer" name="customerId"
                        data-placeholder="Select Customer A/C">
                        <option value="">Select Customer A/C</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" @if ($customer->id == $retailSales->customer_id) selected @endif>{{ $customer->code }}
                                ({{ $customer->name }})
                            </option>
                        @endforeach
                    </select>
                    @if ($errors->has('customerId'))
                        @foreach ($errors->get('customerId') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group {{ $errors->has('customerName') ? ' has-danger' : '' }}">
                    <label for="nick-name">Customer Name</label>
                    <input type="text" class="form-control customerName" name="customerName"
                        value="{{ @$retailSales->customer->name }}" readonly>
                    @if ($errors->has('customerName'))
                        @foreach ($errors->get('customerName') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group {{ $errors->has('customerPhone') ? ' has-danger' : '' }}">
                    <label for="nick-name">Customer Phone</label>
                    <input type="text" class="form-control customerPhone" name="customerPhone"
                        value="{{ @$retailSales->customer->phone_no }}" readonly>
                    @if ($errors->has('customerPhone'))
                        @foreach ($errors->get('customerPhone') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group {{ $errors->has('referenceId') ? ' has-danger' : '' }}">
                    <label for="showroom-id">Reference</label>
                    <select class="form-control chosen-select" name="referenceId" required>
                        <option value="">Select Reference Staff</option>
                        @foreach ($staffs as $staff)
                            <option value="{{ $staff->id }}" @if ($retailSales->reference_id == $staff->id) selected @endif>
                                {{ $staff->name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('referenceId'))
                        @foreach ($errors->get('referenceId') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group {{ $errors->has('productUsageAddress') ? ' has-danger' : '' }}">
                    <label for="product-usage-address">Product Usage Address</label>
                    <textarea name="productUsageAddress" class="form-control"
                        rows="2">{{ $retailSales->product_usage_address }}</textarea>
                    @if ($errors->has('productUsageAddress'))
                        @foreach ($errors->get('productUsageAddress') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <h4 class="text-center py-3" style="font-weight: bold;font-family: tahoma; background-color: #ddd;">
                    Product Information
                </h4>
            </div>
        </div>

        <div class="pb">

            @foreach ($retailSales->products as $p)

                @php
                    $mpro = \App\Product::where('id', $p->product_id)->first();
                    $models = \App\Product::where('name', @$mpro->name)->get();
                @endphp

                <div class="row p-3 mt-3 short_long_installment" style="border: 1px solid black" id="1">

                    <div class="cls">
                        <div class="row">
                            <div class="col-md-12">
                                <span class="closeBtn text-danger" onclick="deleteProduct(event)">X</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('productId') ? ' has-danger' : '' }}">
                            <label for="productId">Product Name</label>
                            <select class="form-control asd product chosen-select" name="productId[]"
                                data-placeholder="Select Product">
                                <option value="">Select Product</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" @if ($product->name == @$mpro->name) selected @endif>
                                        {{ $product->name }}
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
                        <div class="form-group {{ $errors->has('prmodel') ? ' has-danger' : '' }}">
                            <label for="prmodel">Product Model</label>
                            <select class="form-control prmodel chosen-select" id="prmodel" name="prmodel[]"
                                data-placeholder="Select Model">
                                <option value="">Select Product</option>
                                @foreach ($models as $model)
                                    <option value="{{ $model->model_no }}" @if ($model->id == $p->product_id) selected @endif>
                                        {{ $model->model_no }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('prmodel'))
                                @foreach ($errors->get('prmodel') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <input type="hidden" name="sip" class="sip">
                    <input type="hidden" name="lip" class="lip">

                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('productSerials') ? ' has-danger' : '' }}">
                            <label for="productSerials">Product Serials</label>
                            <select class="form-control productSerials" name="productSerials[]"
                                data-placeholder="Select Product Serial">
                                <option value="{{ $p->product_serial }}">{{ $p->product_serial }}</option>
                            </select>
                            @if ($errors->has('productSerials'))
                                @foreach ($errors->get('productSerials') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('productModel') ? ' has-danger' : '' }}">
                            <label for="nick-name">STOCK</label>
                            <input type="text" class="form-control productModel" name="productModel[]"
                                value="{{ old('productModel') }}" required readonly>
                            @if ($errors->has('productModel'))
                                @foreach ($errors->get('productModel') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('discount') ? ' has-danger' : '' }}">
                            <label for="discount">Discount</label>
                            <input type="number" min="0" name="discount[]" class="form-control discount"
                                value="{{ $p->discount }}" oninput="calculateInstallmentDueAmount(null, event)">
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
                            <input type="number" min="0" name="giftVoucher[]" class="form-control giftVoucher"
                                value="{{ $p->gift_voucher }}" oninput="calculateInstallmentDueAmount(null, event)">
                            @if ($errors->has('giftVoucher'))
                                @foreach ($errors->get('giftVoucher') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('exchangeCrt') ? ' has-danger' : '' }}">
                            <label for="exchange-crt">Exchange CRT</label>
                            <input type="number" min="0" name="exchangeCrt[]" class="form-control exchangeCrt"
                                value="{{ $p->exchange_crt }}" oninput="calculateInstallmentDueAmount(null, event)">
                            @if ($errors->has('exchangeCrt'))
                                @foreach ($errors->get('exchangeCrt') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('remarks') ? ' has-danger' : '' }}">
                            <label for="remarks">Remarks</label>
                            <input type="text" name="remarks[]" class="form-control" value="{{ $p->remarks }}">
                            @if ($errors->has('remarks'))
                                @foreach ($errors->get('remarks') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('mobileGift') ? ' has-danger' : '' }}">
                            <label for="mobile-gift">Gift Name</label>
                            <input type="text" min="0" name="GiftName[]" class="form-control mobileGift"
                                value="{{ $p->gift_name }}">
                            @if ($errors->has('mobileGift'))
                                @foreach ($errors->get('mobileGift') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group {{ $errors->has('mobileGift') ? ' has-danger' : '' }}">
                            <label for="mobile-gift">Gift Amount</label>
                            <input type="number" min="0" name="mobileGift[]" class="form-control mobileGift"
                                value="{{ $p->mobile_gift }}">
                            @if ($errors->has('mobileGift'))
                                @foreach ($errors->get('mobileGift') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group {{ $errors->has('warranty') ? ' has-danger' : '' }}">
                            <label for="warranty">Warranty</label>
                            <input type="number" min="0" name="warranty[]" class="form-control warranty"
                                value="{{ $p->warranty }}" readonly>
                            @if ($errors->has('warranty'))
                                @foreach ($errors->get('warranty') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group {{ $errors->has('cashPrice') ? ' has-danger' : '' }}">
                            <label for="cash-price">Cash Price</label>
                            <input type="number" min="0" name="cashPrice[]"
                                class="form-control cashPrice cashPrice_{{ $loop->iteration }}"
                                oninput="cash_row_sum({{ $loop->iteration }})" value="{{ $p->cash_price }}">
                            @if ($errors->has('cashPrice'))
                                @foreach ($errors->get('cashPrice') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('salesPrice') ? ' has-danger' : '' }}">
                            <label for="cash-price">Sales Price</label>
                            <input type="number" min="0" name="salesPrice[]"
                                class="form-control salesPrice salesPrice_{{ $loop->iteration }}"
                                value="{{ $p->sales_price }}">
                            @if ($errors->has('salesPrice'))
                                @foreach ($errors->get('salesPrice') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                </div>

            @endforeach

        </div>

        <div class="more-products"></div>

        <div class="row mt-3 mb-3 text-right">
            <div class="col-md-3 offset-md-9">
                <button id="addMoreProductBtn" class="btn btn-outline-primary">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
                    Add More Products
                </button>
            </div>
        </div>

        <div class="row">

            <div class="col-md-2 installmentType">
                <div class="form-group {{ $errors->has('installmentType') ? ' has-danger' : '' }}">
                    <label for="installment-type">Installment Type</label>
                    @php
                        $installmentTypes = ['Daily' => 'Daily', 'Weekly' => 'Weekly', 'Bi-Monthlyt' => 'Bi-Monthly', 'Monthly' => 'Monthly'];
                    @endphp
                    <select class="form-control" name="installmentType">
                        <option value="">Select Installment Type</option>
                        @foreach ($installmentTypes as $key => $value)
                            <option value="{{ $key }}" @if ($key == $retailSales->installment_type) selected @endif>
                                {{ $value }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('installmentType'))
                        @foreach ($errors->get('installmentType') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-2 shortInstallmentRow">
                <div class="form-group {{ $errors->has('shortInstallmentPrice') ? ' has-danger' : '' }}">
                    <label for="mrp-price">MRP Price</label>

                    <input type="number" min="0" class="form-control shortInstallmentPrice" name="shortInstallmentPrice"
                        value="{{ $retailSales->products->sum('mrp_price') }}">
                    @if ($errors->has('shortInstallmentPrice'))
                        @foreach ($errors->get('shortInstallmentPrice') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-2 shortInstallmentRow">
                <div class="form-group {{ $errors->has('shortInstallmentDeposite') ? ' has-danger' : '' }}">
                    <label for="deposite">Deposite</label>
                    <input type="number" min="0" class="form-control shortInstallmentDeposite"
                        name="shortInstallmentDeposite" value="{{ $retailSales->deposite }}"
                        oninput="calculateInstallmentDueAmount(null, event)">
                    @if ($errors->has('shortInstallmentDeposite'))
                        @foreach ($errors->get('shortInstallmentDeposite') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-2 shortInstallmentRow">
                <div class="form-group {{ $errors->has('shortInstallmentDueAmount') ? ' has-danger' : '' }}">
                    <label for="deposite">Due Amount</label>
                    <input type="number" class="form-control shortInstallmentDueAmount" name="shortInstallmentDueAmount"
                        value="{{ $retailSales->products->sum('mrp_price') - $retailSales->deposite }}">
                    @if ($errors->has('shortInstallmentDueAmount'))
                        @foreach ($errors->get('shortInstallmentDueAmount') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-2 shortInstallmentRow">
                <div class="form-group {{ $errors->has('shortTotalInstallment') ? ' has-danger' : '' }}">
                    <label for="total-installment">Total Installment</label>
                    <input type="number" min="0" class="form-control shortTotalInstallment" name="shortTotalInstallment"
                        value="{{ $retailSales->total_installment }}"
                        oninput="calculateShortInstallmentAmount(null, event)">
                    @if ($errors->has('shortTotalInstallment'))
                        @foreach ($errors->get('shortTotalInstallment') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-2 shortInstallmentRow">
                <div class="form-group {{ $errors->has('shortInstallmentAmount') ? ' has-danger' : '' }}">
                    <label for="monthly-installment-amount">Installment Amount</label>
                    <input type="number" min="0" class="form-control shortInstallmentAmount" name="shortInstallmentAmount"
                        value="{{ $retailSales->monthly_installment_amount }}">
                    @if ($errors->has('shortInstallmentAmount'))
                        @foreach ($errors->get('shortInstallmentAmount') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-2 longInstallmentRow">
                <div class="form-group {{ $errors->has('longInstallmentPrice') ? ' has-danger' : '' }}">
                    <label for="higher-price">Hire Price</label>
                    <input type="number" min="0" class="form-control longInstallmentPrice" name="longInstallmentPrice"
                        value="{{ $retailSales->products->sum('hire_price') }}">
                    @if ($errors->has('longInstallmentPrice'))
                        @foreach ($errors->get('longInstallmentPrice') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-2 longInstallmentRow">
                <div class="form-group {{ $errors->has('longInstallmentDeposite') ? ' has-danger' : '' }}">
                    <label for="deposite">Deposite</label>
                    <input type="number" min="0" class="form-control longInstallmentDeposite"
                        name="longInstallmentDeposite" value="{{ $retailSales->deposite }}"
                        oninput="calculateInstallmentDueAmount(null, event)">
                    @if ($errors->has('longInstallmentDeposite'))
                        @foreach ($errors->get('longInstallmentDeposite') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-2 longInstallmentRow">
                <div class="form-group {{ $errors->has('longInstallmentDueAmount') ? ' has-danger' : '' }}">
                    <label for="deposite">Due Amount</label>
                    <input type="number" min="0" class="form-control longInstallmentDueAmount"
                        name="longInstallmentDueAmount" value="0">
                    @if ($errors->has('longInstallmentDueAmount'))
                        @foreach ($errors->get('longInstallmentDueAmount') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-2 longInstallmentRow">
                <div class="form-group {{ $errors->has('longTotalInstallment') ? ' has-danger' : '' }}">
                    <label for="total-installment">Total Installment</label>
                    <input type="number" min="0" class="form-control longTotalInstallment" name="longTotalInstallment"
                        value="{{ $retailSales->total_installment }}"
                        oninput="calculateLongInstallmentAmount(null, event)">
                    @if ($errors->has('longTotalInstallment'))
                        @foreach ($errors->get('longTotalInstallment') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-2 longInstallmentRow">
                <div class="form-group {{ $errors->has('longInstallmentAmount') ? ' has-danger' : '' }}">
                    <label for="monthly-installment-amount">Installment Amount</label>
                    <input type="number" min="0" class="form-control longInstallmentAmount" name="longInstallmentAmount"
                        value="{{ $retailSales->monthly_installment_amount }}">
                    @if ($errors->has('longInstallmentAmount'))
                        @foreach ($errors->get('longInstallmentAmount') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

        </div>

        @if ($retailSales->sale_type != 'Cash')

            <div class="guarantorInfo p-3">

                <div class="row">
                    <div class="col-md-12">
                        <h4 class="text-center" style="font-weight: bold;font-family: tahoma">Guarantor Information 1
                        </h4>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('gurantorName') ? ' has-danger' : '' }}">
                            <label for="gurantor-name">Guarantor Name</label>
                            <input type="text" class="form-control" name="gurantorName[]"
                                value="{{ @$retailSales->guarantor[0]->gurantor_name }}">
                            @if ($errors->has('gurantorName'))
                                @foreach ($errors->get('gurantorName') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('gurantorPhoneNo') ? ' has-danger' : '' }}">
                            <label for="gurantor-phone-no">Guarantor Phone No</label>
                            <input type="text" class="form-control" name="gurantorPhoneNo[]"
                                value="{{ @$retailSales->guarantor[0]->gurantor_phone_no }}">
                            @if ($errors->has('gurantorPhoneNo'))
                                @foreach ($errors->get('gurantorPhoneNo') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('guarantorFatherName') ? ' has-danger' : '' }}">
                            <label for="garantor-spouse-name">Father Name</label>
                            <input type="text" class="form-control" name="guarantorFatherName[]"
                                value="{{ @$retailSales->guarantor[0]->guarantor_father_name }}">
                            @if ($errors->has('guarantorFatherName'))
                                @foreach ($errors->get('guarantorFatherName') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('gurantorAge') ? ' has-danger' : '' }}">
                            <label for="guarantor-age">Guarantor Age</label>
                            <input type="number" min="0" class="form-control" name="gurantorAge[]"
                                value="{{ @$retailSales->guarantor[0]->gurantor_age }}">
                            @if ($errors->has('gurantorAge'))
                                @foreach ($errors->get('gurantorAge') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        @php
                            $maritalStatus = ['Unmarried' => 'Unmarried', 'Married' => 'Married'];
                        @endphp
                        <div class="form-group {{ $errors->has('guarantorMaritalStatus') ? ' has-danger' : '' }}">
                            <label for="guarantor-mrital-status">Marital Status</label>
                            <select name="guarantorMaritalStatus[]" class="form-control firstGuarantorMaritalStatus"
                                style="height: 41%">
                                <option value="">Select Marital Status</option>
                                @foreach ($maritalStatus as $key => $value)
                                    <option value="{{ $value }}" @if ($value == @$retailSales->guarantor[0]->guarantor_marital_status) selected @endif>{{ $value }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('guarantorMaritalStatus'))
                                @foreach ($errors->get('guarantorMaritalStatus') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('guarantorSpouseName') ? ' has-danger' : '' }}">
                            <label for="guarantor-spouse-name">Spouse Name</label>
                            <input type="text" class="form-control guarantorSpouce" name="guarantorSpouseName[]"
                                value="{{ @$retailSales->guarantor[0]->guarantor_spouse_name }}">
                            @if ($errors->has('guarantorSpouseName'))
                                @foreach ($errors->get('guarantorSpouseName') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('guarantorProfessionName') ? ' has-danger' : '' }}">
                            <label for="guarantor-profession-name">Profession's Name</label>
                            <input type="text" class="form-control" name="guarantorProfessionName[]"
                                value="{{ @$retailSales->guarantor[0]->guarantor_profession_name }}">
                            @if ($errors->has('guarantorProfessionName'))
                                @foreach ($errors->get('guarantorProfessionName') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('guarantorDesignation') ? ' has-danger' : '' }}">
                            <label for="guarantor-designation">Designation</label>
                            <input type="text" class="form-control" name="guarantorDesignation[]"
                                value="{{ @$retailSales->guarantor[0]->guarantor_designation }}">
                            @if ($errors->has('guarantorDesignation'))
                                @foreach ($errors->get('guarantorDesignation') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('guarantorWorkplacePhoneNo') ? ' has-danger' : '' }}">
                            <label for="guarantor-workplace-phoneNo">Professional Phone No</label>
                            <input type="text" class="form-control" name="guarantorWorkplacePhoneNo[]"
                                value="{{ @$retailSales->guarantor[0]->guarantor_workplace_phone_no }}">
                            @if ($errors->has('guarantorWorkplacePhoneNo'))
                                @foreach ($errors->get('guarantorWorkplacePhoneNo') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('guarantorMonthlyIncome') ? ' has-danger' : '' }}">
                            <label for="guarantor-monthly-income">Monthly Income</label>
                            <input type="number" min="0" class="form-control" name="guarantorMonthlyIncome[]"
                                value="{{ @$retailSales->guarantor[0]->guarantor_monthly_income }}">
                            @if ($errors->has('guarantorMonthlyIncome'))
                                @foreach ($errors->get('guarantorMonthlyIncome') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('guarantorPresentAddress') ? ' has-danger' : '' }}">
                            <label for="guarantor-present-address">Present Address</label>
                            <textarea name="guarantorPresentAddress[]" class="form-control"
                                rows="2">{{ @$retailSales->guarantor[0]->guarantor_present_address }}</textarea>
                            @if ($errors->has('guarantorPresentAddress'))
                                @foreach ($errors->get('guarantorPresentAddress') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('guarantorPermanentAddress') ? ' has-danger' : '' }}">
                            <label for="guarantor-permanent-address">Permanent Address</label>
                            <textarea name="guarantorPermanentAddress[]" class="form-control"
                                rows="2">{{ @$retailSales->guarantor[0]->guarantor_permanent_address }}</textarea>
                            @if ($errors->has('guarantorPermanentAddress'))
                                @foreach ($errors->get('guarantorPermanentAddress') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('guarantorWorkPlaceAddress') ? ' has-danger' : '' }}">
                            <label for="monthly-income">Work Place address</label>
                            <textarea name="guarantorWorkPlaceAddress[]" class="form-control"
                                rows="2">{{ @$retailSales->guarantor[0]->guarantor_work_place_address }}</textarea>
                            @if ($errors->has('guarantorWorkPlaceAddress'))
                                @foreach ($errors->get('guarantorWorkPlaceAddress') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                </div>
            </div>

            <div class="guarantorInfo p-3">
                <div class="row">
                    <div class="col-md-12">
                        <h4 class="text-center" style="font-weight: bold;font-family: tahoma">Guarantor Information 2
                        </h4>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('gurantorName') ? ' has-danger' : '' }}">
                            <label for="gurantor-name">Guarantor Name</label>
                            <input type="text" class="form-control" name="gurantorName[]"
                                value="{{ @$retailSales->guarantor[1]->gurantor_name }}">
                            @if ($errors->has('gurantorName'))
                                @foreach ($errors->get('gurantorName') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('gurantorPhoneNo') ? ' has-danger' : '' }}">
                            <label for="gurantor-phone-no">Guarantor Phone No</label>
                            <input type="text" class="form-control" name="gurantorPhoneNo[]"
                                value="{{ @$retailSales->guarantor[1]->gurantor_phone_no }}">
                            @if ($errors->has('gurantorPhoneNo'))
                                @foreach ($errors->get('gurantorPhoneNo') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('guarantorFatherName') ? ' has-danger' : '' }}">
                            <label for="garantor-spouse-name">Father Name</label>
                            <input type="text" class="form-control" name="guarantorFatherName[]"
                                value="{{ @$retailSales->guarantor[1]->guarantor_father_name }}">
                            @if ($errors->has('guarantorFatherName'))
                                @foreach ($errors->get('guarantorFatherName') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('gurantorAge') ? ' has-danger' : '' }}">
                            <label for="guarantor-age">Guarantor Age</label>
                            <input type="number" min="0" class="form-control" name="gurantorAge[]"
                                value="{{ @$retailSales->guarantor[1]->gurantor_age }}">
                            @if ($errors->has('gurantorAge'))
                                @foreach ($errors->get('gurantorAge') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        @php
                            $maritalStatus = ['Unmarried' => 'Unmarried', 'Married' => 'Married'];
                        @endphp
                        <div class="form-group {{ $errors->has('guarantorMaritalStatus') ? ' has-danger' : '' }}">
                            <label for="guarantor-mrital-status">Marital Status</label>
                            <select name="guarantorMaritalStatus[]" class="form-control firstGuarantorMaritalStatus"
                                style="height: 41%">
                                <option value="">Select Marital Status</option>
                                @foreach ($maritalStatus as $key => $value)
                                    <option value="{{ $value }}" @if ($value == @$retailSales->guarantor[1]->guarantor_marital_status) selected @endif>{{ $value }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('guarantorMaritalStatus'))
                                @foreach ($errors->get('guarantorMaritalStatus') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('guarantorSpouseName') ? ' has-danger' : '' }}">
                            <label for="guarantor-spouse-name">Spouse Name</label>
                            <input type="text" class="form-control guarantorSpouce" name="guarantorSpouseName[]"
                                value="{{ @$retailSales->guarantor[1]->guarantor_spouse_name }}">
                            @if ($errors->has('guarantorSpouseName'))
                                @foreach ($errors->get('guarantorSpouseName') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('guarantorProfessionName') ? ' has-danger' : '' }}">
                            <label for="guarantor-profession-name">Profession's Name</label>
                            <input type="text" class="form-control" name="guarantorProfessionName[]"
                                value="{{ @$retailSales->guarantor[1]->guarantor_profession_name }}">
                            @if ($errors->has('guarantorProfessionName'))
                                @foreach ($errors->get('guarantorProfessionName') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('guarantorDesignation') ? ' has-danger' : '' }}">
                            <label for="guarantor-designation">Designation</label>
                            <input type="text" class="form-control" name="guarantorDesignation[]"
                                value="{{ @$retailSales->guarantor[1]->guarantor_designation }}">
                            @if ($errors->has('guarantorDesignation'))
                                @foreach ($errors->get('guarantorDesignation') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('guarantorWorkplacePhoneNo') ? ' has-danger' : '' }}">
                            <label for="guarantor-workplace-phoneNo">Professional Phone No</label>
                            <input type="text" class="form-control" name="guarantorWorkplacePhoneNo[]"
                                value="{{ @$retailSales->guarantor[1]->guarantor_workplace_phone_no }}">
                            @if ($errors->has('guarantorWorkplacePhoneNo'))
                                @foreach ($errors->get('guarantorWorkplacePhoneNo') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('guarantorMonthlyIncome') ? ' has-danger' : '' }}">
                            <label for="guarantor-monthly-income">Monthly Income</label>
                            <input type="number" min="0" class="form-control" name="guarantorMonthlyIncome[]"
                                value="{{ @$retailSales->guarantor[1]->guarantor_monthly_income }}">
                            @if ($errors->has('guarantorMonthlyIncome'))
                                @foreach ($errors->get('guarantorMonthlyIncome') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('guarantorPresentAddress') ? ' has-danger' : '' }}">
                            <label for="guarantor-present-address">Present Address</label>
                            <textarea name="guarantorPresentAddress[]" class="form-control"
                                rows="2">{{ @$retailSales->guarantor[1]->guarantor_present_address }}</textarea>
                            @if ($errors->has('guarantorPresentAddress'))
                                @foreach ($errors->get('guarantorPresentAddress') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('guarantorPermanentAddress') ? ' has-danger' : '' }}">
                            <label for="guarantor-permanent-address">Permanent Address</label>
                            <textarea name="guarantorPermanentAddress[]" class="form-control"
                                rows="2">{{ @$retailSales->guarantor[1]->guarantor_permanent_address }}</textarea>
                            @if ($errors->has('guarantorPermanentAddress'))
                                @foreach ($errors->get('guarantorPermanentAddress') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('guarantorWorkPlaceAddress') ? ' has-danger' : '' }}">
                            <label for="monthly-income">Work Place address</label>
                            <textarea name="guarantorWorkPlaceAddress[]" class="form-control"
                                rows="2">{{ @$retailSales->guarantor[1]->guarantor_work_place_address }}</textarea>
                            @if ($errors->has('guarantorWorkPlaceAddress'))
                                @foreach ($errors->get('guarantorWorkPlaceAddress') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                </div>
            </div>

        @endif

    </div>
@endsection

@section('custom-js')

    <script>
        function deleteProduct(e) {
            e.preventDefault();

            var purchaseType = $(".purchaseType").val();
            let parent = $(e.target).parent().parent().parent().parent();

            let sip = parent.find('.sip').val();
            let lip = parent.find('.lip').val();

            let totalMrp = $('.shortInstallmentPrice').val();
            let totalHire = $('.longInstallmentPrice').val();

            if (purchaseType == 'Short Installment') {
                console.log(sip);
                totalMrp -= sip;
                $(".shortInstallmentPrice").val(totalMrp);
            }

            if (purchaseType == 'Long Installment') {
                totalHire -= lip;
                $(".longInstallmentPrice").val(totalHire);
            }

            parent.remove();
            short_long_installment();

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

                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('productId') ? ' has-danger' : '' }}">
                            <label for="productId">Product Name</label>
                            <select class="form-control asd product chosen-select" name="productId[]"
                                data-placeholder="Select Product">
                                <option value="">Select Product</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}
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
                        <div class="form-group {{ $errors->has('prmodel') ? ' has-danger' : '' }}">
                            <label for="prmodel">Product Model</label>
                            <select class="form-control prmodel chosen-select" id="prmodel" name="prmodel[]"
                                data-placeholder="Select Model">
                                <option value="">Select Model</option>
                            </select>
                            @if ($errors->has('prmodel'))
                                @foreach ($errors->get('prmodel') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <input type="hidden" name="sip[]" class="sip">
                    <input type="hidden" name="lip[]" class="lip">

                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('productSerials') ? ' has-danger' : '' }}">
                            <label for="productSerials">Product Serials</label>
                            <select class="form-control productSerials" name="productSerials[]"
                                data-placeholder="Select Product Serial">
                                <option value="">Select Serial</option>
                            </select>
                            @if ($errors->has('productSerials'))
                                @foreach ($errors->get('productSerials') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('productModel') ? ' has-danger' : '' }}">
                            <label for="nick-name">STOCK</label>
                            <input type="text" class="form-control productModel" name="productModel[]"
                                value="{{ old('productModel') }}" required readonly>
                            @if ($errors->has('productModel'))
                                @foreach ($errors->get('productModel') as $error)
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

                    <div class="col-md-3">
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

                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('remarks') ? ' has-danger' : '' }}">
                            <label for="remarks">Remarks</label>
                            <input type="text" name="remarks[]" class="form-control" value="{{ old('remarks') }}">
                            @if ($errors->has('remarks'))
                                @foreach ($errors->get('remarks') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('mobileGift') ? ' has-danger' : '' }}">
                            <label for="mobile-gift">Gift Name</label>
                            <input type="text" min="0" name="GiftName[]" class="form-control mobileGift" value="0">
                            @if ($errors->has('mobileGift'))
                                @foreach ($errors->get('mobileGift') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group {{ $errors->has('mobileGift') ? ' has-danger' : '' }}">
                            <label for="mobile-gift">Gift Amount</label>
                            <input type="number" min="0" name="mobileGift[]" class="form-control mobileGift" value="0">
                            @if ($errors->has('mobileGift'))
                                @foreach ($errors->get('mobileGift') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group {{ $errors->has('warranty') ? ' has-danger' : '' }}">
                            <label for="warranty">Warranty</label>
                            <input type="number" min="0" name="warranty[]" class="form-control warranty" readonly>
                            @if ($errors->has('warranty'))
                                @foreach ($errors->get('warranty') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-2">
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

                    <div class="col-md-3">
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
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            $(".spouceName").prop('disabled', true);
            $('.maritalStatus').click(function(event) {
                var maritalStatus = $('.maritalStatus').val();
                if (maritalStatus == "Married") {
                    $(".spouceName").prop('disabled', false);
                } else {
                    $(".spouceName").prop('disabled', true);
                }
            })
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {


            // deposit change update due 

            $('.shortInstallmentDeposite').keyup(function(e) {
                let thisParent = $(this).parent().parent().parent();
                let thisDue = thisParent.find('.shortInstallmentDueAmount');

                let depositAmount = $(this).val();
                let thisMRP = thisParent.find('.shortInstallmentPrice').val();

                let due = thisMRP - depositAmount;

                thisDue.val(due);

            });

            $('.longInstallmentDeposite').keyup(function(e) {
                let thisParent = $(this).parent().parent().parent();
                let thisDue = thisParent.find('.longInstallmentDueAmount');

                let depositAmount = $(this).val();
                let thisHaire = thisParent.find('.longInstallmentPrice').val();

                let due = thisHaire - depositAmount;

                thisDue.val(due);

            });

            /*code for guarantor info*/
            $(".guarantorSpouce").prop('disabled', true);
            // $(".guarantorSpouce").prop('disabled', true);

            $('.firstGuarantorMaritalStatus').change(function(event) {
                var firstGuarantorMaritalStatus = $(this).val();

                let thisGurantorSpouse = $(this).parent().parent().parent().find(".guarantorSpouce");
                console.log(thisGurantorSpouse);

                if (firstGuarantorMaritalStatus == "Married") {
                    thisGurantorSpouse.prop('disabled', false);
                } else {
                    thisGurantorSpouse.prop('disabled', true);
                    thisGurantorSpouse.val('');
                }
            })

            /*end code for guarantor info*/

            // start code installment

            let installType = '{{ $retailSales->sale_type }}';

            if (installType != "Short Installment") {
                $('.shortInstallmentRow').hide();
            }

            if (installType != "Long Installment") {
                $('.longInstallmentRow').hide();
            }

            if (installType == 'Cash') {
                $('.installmentType').hide();
                $('.guarantorInfo').hide();
            }

            $('.purchaseType').change(function(event) {
                var purchaseType = $(".purchaseType").val();

                if (purchaseType == "Cash") {
                    $('.installmentType').hide();
                    $("select[name='installmentType']").prop('required', false);
                    $('.guarantorInfo').hide();
                } else {
                    $('.guarantorInfo').show();
                }

                if (purchaseType == "Short Installment") {
                    $('.shortInstallmentRow').show();
                    $('.installmentType').show();
                    $("input[name='shortInstallmentDeposite']").prop('required', true);
                    $("input[name='shortInstallmentPrice']").prop('required', true);
                    $("input[name='shortTotalInstallment']").prop('required', true);
                    $("input[name='shortInstallmentAmount']").prop('required', true);
                    $("select[name='installmentType']").prop('required', true);
                } else {
                    $('.shortInstallmentRow').hide();
                    $("input[name='shortInstallmentDeposite']").prop('required', false);
                    $("input[name='shortInstallmentPrice']").prop('required', false);
                    $("input[name='shortTotalInstallment']").prop('required', false);
                    $("input[name='shortInstallmentAmount']").prop('required', false);
                }

                if (purchaseType == "Long Installment") {
                    $('.longInstallmentRow').show();
                    $('.installmentType').show();
                    $("input[name='longInstallmentDeposite']").prop('required', true);
                    $("input[name='longInstallmentPrice']").prop('required', true);
                    $("input[name='longTotalInstallment']").prop('required', true);
                    $("input[name='longInstallmentAmount']").prop('required', true);
                    $("select[name='installmentType']").prop('required', true);
                } else {
                    $('.longInstallmentRow').hide();
                    $("input[name='longInstallmentDeposite']").prop('required', false);
                    $("input[name='longInstallmentPrice']").prop('required', false);
                    $("input[name='longTotalInstallment']").prop('required', false);
                    $("input[name='longInstallmentAmount']").prop('required', false);
                }
            })

            /*end code for installment*/
        });
    </script>


    <script type="text/javascript">
        /*code for product info*/
        $(document).on('change', '.customer', function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var customerId = $('.customer option:selected').val();
            if (customerId != '') {
                $.ajax({
                    type: 'post',
                    url: '{{ route('retailSales.getCustomerInfo') }}',
                    data: {
                        customerId: customerId
                    },
                    success: function(data) {
                        var customer = data.customer;
                        $('.customerName').val(customer.name);
                        $('.customerPhone').val(customer.phone_no);
                    }
                });
            } else {
                $('.customerName').val("");
                $('.customerPhone').val("");
            }
        });

        let totalMrp = 0;
        let totalHire = 0;

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
                    type: 'post',
                    url: '{{ route('retailSales.getAllProduct') }}',
                    data: {
                        productId: productId
                    },
                    success: function(data) {
                        var products = data.products;
                        var purchaseType = $(".purchaseType").val();


                        parent.find('.prmodel option').remove();
                        parent.find('.prmodel').append('<option value="">Select Model</option>');

                        if (data.products.length > 0) {
                            data.products.forEach(function(item, index) {
                                var option = '<option value="' + item.model_no +
                                    '">' + item.model_no + '</option>';
                                parent.find('.prmodel').append(option);
                            });


                            $('.chosen-select').chosen();
                            $('.chosen-select').trigger("chosen:updated");
                        }


                    }
                });
            }
        });

        function calculateInstallmentDueAmount(parent, e) {

            if (parent == null) {
                parent = $(e.target).parent().parent().parent();
            }

            var purchaseType = parent.find(".purchaseType").val();

            var discount = parseInt(parent.find('.discount').val());
            var giftVoucher = parseInt(parent.find('.giftVoucher').val());
            var exchangeCrt = parseInt(parent.find('.exchangeCrt').val());

            if (purchaseType == "Cash") {
                calculateSalesPrice(discount, giftVoucher, exchangeCrt, parent);
            } else if (purchaseType == "Short Installment") {
                var price = parseInt(parent.find('.shortInstallmentPrice').val());
                var deposite = parseInt(parent.find('.shortInstallmentDeposite').val());
                var dueAmount = price - deposite - discount - giftVoucher - exchangeCrt;

                parent.find('.shortInstallmentDueAmount').val(Math.round(dueAmount));
                calculateShortInstallmentAmount(parent, null);
                calculateSalesPrice(discount, giftVoucher, exchangeCrt, parent);
            } else {
                var price = parseInt(parent.find('.longInstallmentPrice').val());
                var deposite = parseInt(parent.find('.longInstallmentDeposite').val());
                var dueAmount = price - deposite - discount - giftVoucher - exchangeCrt;

                parent.find('.longInstallmentDueAmount').val(Math.round(dueAmount));
                calculateLongInstallmentAmount(parent, null);
                calculateSalesPrice(discount, giftVoucher, exchangeCrt, parent);
            }
        }

        function calculateSalesPrice(discount, giftVoucher, exchangeCrt, parent) {
            var price = parseInt(parent.find('.cashPrice').val());

            var short_total_p = (price * 8) / 100;
            var short_total = parseInt(price) + parseInt(short_total_p);
            var long_total_p = (short_total * 12) / 100;
            var long_total = parseInt(short_total) + parseInt(long_total_p);


            var salesPrice = price - discount - giftVoucher - exchangeCrt;

            // parent.find('.salesPrice').val(Math.round(salesPrice));

            var sale_type = $('.purchaseType').val();
            if (sale_type == 'Short Installment') {
                var salesPrice = short_total - discount - giftVoucher - exchangeCrt;
                parent.find('.salesPrice').val(Math.round(salesPrice));
            }
            if (sale_type == 'Long Installment') {
                var salesPrice = long_total - discount - giftVoucher - exchangeCrt;
                parent.find('.salesPrice').val(Math.round(salesPrice));
            }
            if (sale_type == 'Cash') {
                parent.find('.salesPrice').val(Math.round(salesPrice));
            }
        }

        function calculateShortInstallmentAmount(parent, e) {

            if (parent == null) {
                parent = $(e.target).parent().parent().parent();
            }

            var dueAmount = parseFloat(parent.find('.shortInstallmentDueAmount').val());
            var shortTotalInstallment = parseFloat(parent.find('.shortTotalInstallment').val());

            if (shortTotalInstallment == 0 || parent.find('.shortTotalInstallment').val() == "") {
                var shortInstallmentAmount = dueAmount;
            } else {
                var shortInstallmentAmount = dueAmount / shortTotalInstallment;
            }

            parent.find('.shortInstallmentAmount').val(Math.round(shortInstallmentAmount));

            short_long_installment();
        }

        function calculateLongInstallmentAmount(parent, e) {

            if (parent == null) {
                parent = $(e.target).parent().parent().parent();
            }

            var dueAmount = parseFloat(parent.find('.longInstallmentDueAmount').val());
            var longTotalInstallment = parseFloat(parent.find('.longTotalInstallment').val());

            if (longTotalInstallment == 0 || parent.find('.longTotalInstallment').val() == "") {
                var longInstallmentAmount = dueAmount;
            } else {
                var longInstallmentAmount = dueAmount / longTotalInstallment;
            }

            parent.find('.longInstallmentAmount').val(Math.round(longInstallmentAmount));

            short_long_installment();
        }
    </script>

    <script>
        $(document).on('change', '.prmodel', function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let parent = $(this).parent().parent().parent();

            var prmodel = parent.find('.prmodel option:selected').val();

            if (prmodel != '') {
                $.ajax({
                    type: 'post',
                    url: '{{ route('retailSales.getProductInfo') }}',
                    data: {
                        model_no: prmodel
                    },
                    success: function(data) {

                        var purchaseType = $(".purchaseType").val();
                        var product = data.product;


                        parent.find(".productModel").val(data.stock);
                        parent.find(".cashPrice").val(product.price);
                        parent.find(".warranty").val(product.warranty);


                        var price = product.price;
                        var short_total_p = (price * 8) / 100;
                        var short_total = parseInt(price) + parseInt(short_total_p);
                        var long_total_p = (short_total * 12) / 100;
                        var long_total = parseInt(short_total) + parseInt(long_total_p);

                        if (purchaseType == 'Cash') {
                            parent.find('.salesPrice').val(price);
                        }

                        if (purchaseType == 'Short Installment') {
                            parent.find('.salesPrice').val(short_total);
                        }

                        if (purchaseType == 'Long Installment') {
                            parent.find('.salesPrice').val(long_total);
                        }

                        short_long_installment();
                    }
                });
            } else {
                // totalMrp -= parent.find(".sip").val();
                // $(".shortInstallmentPrice").val(totalMrp);
                // parent.find(".sip").val('');

                // totalHire -= parent.find(".lip").val();
                // $(".longInstallmentPrice").val(totalHire);
                // parent.find('.lip').val('');

                // parent.find('.productModel').val('');
                // parent.find('.cashPrice').val('');
                // parent.find('.warranty').val('');
                // parent.find('.longInstallmentPrice').val('');
                // parent.find('.shortInstallmentPrice').val('');
                // parent.find('.salesPrice').val('');
            }
        });

        function cash_row_sum(id) {
            var cash_price = $('.cashPrice_' + id).val();

            var purchaseType = $(".purchaseType").val();

            var price = cash_price;
            var short_total_p = (price * 8) / 100;
            var short_total = parseInt(price) + parseInt(short_total_p);
            var long_total_p = (short_total * 12) / 100;
            var long_total = parseInt(short_total) + parseInt(long_total_p);

            if (purchaseType == 'Short Installment') {
                $('.salesPrice_' + id).val(short_total);
            }

            if (purchaseType == 'Long Installment') {
                $('.salesPrice_' + id).val(long_total);
            }

            if (purchaseType == 'Cash') {
                $('.salesPrice_' + id).val(price);
            }

            short_long_installment();
        }

        function short_long_installment() {
            // short_long_installment
            var total = 0;
            var discount = 0;
            var giftVoucher = 0;
            var exchangeCrt = 0;

            var purchaseType = $(".purchaseType").val();

            $('.short_long_installment .cashPrice').each(function() {
                // total = total + parseFloat($(this).val());


                var price = parseFloat($(this).val());
                var short_total_p = (price * 8) / 100;
                var short_total = parseInt(price) + parseInt(short_total_p);
                var long_total_p = (short_total * 12) / 100;
                var long_total = parseInt(short_total) + parseInt(long_total_p);

                if (purchaseType == 'Cash') {
                    total += parseFloat(price);
                }

                if (purchaseType == 'Short Installment') {
                    total += parseFloat(short_total);
                }

                if (purchaseType == 'Long Installment') {
                    total += parseFloat(long_total);
                }
            });
            $('.short_long_installment .discount').each(function() {
                discount = discount + parseFloat($(this).val());
            });
            $('.short_long_installment .giftVoucher').each(function() {
                giftVoucher = giftVoucher + parseFloat($(this).val());
            });
            $('.short_long_installment .exchangeCrt').each(function() {
                exchangeCrt = exchangeCrt + parseFloat($(this).val());
            });

            var grand = total - (discount + giftVoucher + exchangeCrt);

            $('.shortInstallmentPrice, .longInstallmentPrice').val(grand);

            var savings = parseInt($('.longInstallmentSavings').val());

            $('.shortInstallmentDueAmount, .longInstallmentDueAmount').val(grand - savings);
        }




        $(".buttonAddEdit").click(function(e) {

            e.preventDefault();

            var dealerId = $('.dealerId').val();
            var referenceId = $('.referenceId').val();
            var product = $('.product').val();
            var customerId = $('.customer').val();

            if (dealerId == '') {
                swal({
                    title: "Select Project",
                    type: "warning",
                    // timer: 1000,
                });
            }

            if (customerId == '') {
                swal({
                    title: "Select Customer",
                    type: "warning",
                    // timer: 1000,
                });
            }

            if (referenceId == '') {
                swal({
                    title: "Select Reference",
                    type: "warning",
                    // timer: 1000,
                });
            }

            if (product == '') {
                swal({
                    title: "Select Product",
                    type: "warning",
                    // timer: 1000,
                });
            }

            if (dealerId != '' && referenceId != '' && product != '') {
                $('#formAddEdit').submit();
            }
        });
    </script>
    <script type="text/javascript">
        document.forms['formAddEdit'].elements['purchaseType'].value = "{{ $retailSales->sale_type }}";
        $('.chosen-select').chosen();
        $('.chosen-select').trigger("chosen:updated");
    </script>



    <script>
        var href = $('.go_back').attr('href');
        $('.go_back').attr('href', href + '?project={{ @$retailSales->dealer_id }}');

        $('.select_project').change(function() {
            var selected = $('.select_project').val();
            $('.go_back').attr('href', href + '?project=' + selected);
        });
    </script>
@endsection

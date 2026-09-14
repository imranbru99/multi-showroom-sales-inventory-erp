<div class="card-body">
    <div class="row">
        <div class="col-md-12">
            <h4 class="text-center" style="font-weight: bold;font-family: tahoma">Product Description</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('productId') ? ' has-danger' : '' }}">
                <label for="productId">Product Name</label>
                <select class="form-control chosen-select product" name="productId" data-placeholder="Select Product">
                    <option value="">Select Product</option>
                    @foreach ($products as $product)
                      <option value="{{$product->id}}">{{$product->name}} ({{$product->code}})</option>
                    @endforeach
                </select>
                @if ($errors->has('productId'))
                    @foreach($errors->get('productId') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group {{ $errors->has('showroomName') ? ' has-danger' : '' }}">
                <label for="showroom">Showroom</label>
                <input type="text" class="form-control" name="showroomName" value="{{ $showroomName }}" required readonly="">
                @if ($errors->has('showroomName'))
                    @foreach($errors->get('showroomName') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="form-group {{ $errors->has('productModel') ? ' has-danger' : '' }}">
                <label for="nick-name">Product Model</label>
                <input type="text" class="form-control productModel" name="productModel" value="{{ old('productModel') }}" required readonly="">
                @if ($errors->has('productModel'))
                    @foreach($errors->get('productModel') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-1">
            <div class="form-group {{ $errors->has('warranty') ? ' has-danger' : '' }}">
                <label for="warranty">Warranty</label>
                <input type="number" name="warranty" class="form-control warranty" readonly>
                @if ($errors->has('warranty'))
                    @foreach($errors->get('warranty') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group {{ $errors->has('purchaseDate') ? ' has-danger' : '' }}">
                <label for="purchase-date">Purchase Date</label>
                <input type="text" name="purchaseDate" class="form-control add_datepicker" readonly="">
                @if ($errors->has('purchaseDate'))
                    @foreach($errors->get('purchaseDate') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                    @endforeach
                @endif
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="form-group {{ $errors->has('remarks') ? ' has-danger' : '' }}">
                <label for="remarks">Remarks</label>
                <input type="text" name="remarks" class="form-control" value="{{ old('remarks') }}">
                @if ($errors->has('remarks'))
                    @foreach($errors->get('remarks') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group {{ $errors->has('referenceId') ? ' has-danger' : '' }}">
                <label for="showroom-id">Reference</label>
                <select class="form-control chosen-select" name="referenceId">
                    <option value="">Select Reference Staff</option>
                    @foreach ($staffs as $staff)
                        <option value="{{$staff->id}}">{{$staff->name}}</option>
                    @endforeach
                </select>
                @if ($errors->has('referenceId'))
                    @foreach($errors->get('referenceId') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group {{ $errors->has('cashPrice') ? ' has-danger' : '' }}">
                <label for="cash-price">Cash Price</label>
                <input type="number" name="cashPrice" class="form-control cashPrice">
                @if ($errors->has('cashPrice'))
                    @foreach($errors->get('cashPrice') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-5">
            <label for="purchase-type">Purchase Type</label>
             <div class="form-group {{ $errors->has('purchaseType') ? ' has-danger' : '' }}">
                 <div class="form-check-inline">
                    <label class="form-check-label">
                        <input type="radio" value="Cash" name="purchaseType" class="product purchaseType" required> Cash
                    </label>
                </div>

                <div class="form-check-inline">
                    <label class="form-check-label">
                        <input type="radio" value="Short Installment" name="purchaseType" class="product purchaseType"> Short Installment
                    </label>
                </div>

                <div class="form-check-inline">
                    <label class="form-check-label">
                        <input type="radio" value="Long Installment" name="purchaseType" class="product purchaseType"> Long Installment
                    </label>
                </div>
                @if ($errors->has('purchaseType'))
                    @foreach($errors->get('purchaseType') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-3 installmentType">
            <div class="form-group {{ $errors->has('installmentType') ? ' has-danger' : '' }}">
                <label for="installment-type">Installment Type</label>
                @php
                    $installmentTypes = array('Daily'=>'Daily','Weekly'=>'Weekly','Bi-Monthlyt'=>'Bi-Monthly','Monthly'=>'Monthly')
                @endphp
                <select class="form-control" name="installmentType">
                    <option value="">Select Installment Type</option>
                    @foreach ($installmentTypes as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
                @if ($errors->has('installmentType'))
                    @foreach($errors->get('installmentType') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <div class="row shortInstallmentRow">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group {{ $errors->has('shortInstallmentDeposite') ? ' has-danger' : '' }}">
                        <label for="deposite">Deposite</label>
                        <input type="number" class="form-control shortInstallmentDeposite" name="shortInstallmentDeposite" value="{{ old('shortInstallmentDeposite') }}">
                        @if ($errors->has('shortInstallmentDeposite'))
                            @foreach($errors->get('shortInstallmentDeposite') as $error)
                                <div class="form-control-feedback">{{ $error }}</div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group {{ $errors->has('shortInstallmentPrice') ? ' has-danger' : '' }}">
                        <label for="mrp-price">MRP Price</label>
                        <input type="number" class="form-control shortInstallmentPrice" name="shortInstallmentPrice" value="{{ old('shortInstallmentPrice') }}">
                        @if ($errors->has('shortInstallmentPrice'))
                            @foreach($errors->get('shortInstallmentPrice') as $error)
                                <div class="form-control-feedback">{{ $error }}</div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="row">
               <div class="col-md-6">
                    <div class="form-group {{ $errors->has('shortTotalInstallment') ? ' has-danger' : '' }}">
                        <label for="total-installment">Total Installment</label>
                        <input type="text" class="form-control shortTotalInstallment" name="shortTotalInstallment" value="{{ old('shortTotalInstallment') }}" oninput="calculateShortInstallmentAmount()">
                        @if ($errors->has('shortTotalInstallment'))
                            @foreach($errors->get('shortTotalInstallment') as $error)
                                <div class="form-control-feedback">{{ $error }}</div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group {{ $errors->has('shortInstallmentAmount') ? ' has-danger' : '' }}">
                        <label for="monthly-installment-amount">Installment Amount</label>
                        <input type="number" class="form-control shortInstallmentAmount" name="shortInstallmentAmount" value="{{ old('shortInstallmentAmount') }}">
                        @if ($errors->has('shortInstallmentAmount'))
                            @foreach($errors->get('shortInstallmentAmount') as $error)
                                <div class="form-control-feedback">{{ $error }}</div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row longInstallmentRow">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group {{ $errors->has('longInstallmentDeposite') ? ' has-danger' : '' }}">
                        <label for="deposite">Deposite</label>
                        <input type="number" class="form-control longInstallmentDeposite" name="longInstallmentDeposite" value="{{ old('longInstallmentDeposite') }}">
                        @if ($errors->has('longInstallmentDeposite'))
                            @foreach($errors->get('longInstallmentDeposite') as $error)
                                <div class="form-control-feedback">{{ $error }}</div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group {{ $errors->has('longInstallmentPrice') ? ' has-danger' : '' }}">
                        <label for="higher-price">Higher Price</label>
                        <input type="number" class="form-control longInstallmentPrice" name="longInstallmentPrice" value="{{ old('longInstallmentPrice') }}">
                        @if ($errors->has('longInstallmentPrice'))
                            @foreach($errors->get('longInstallmentPrice') as $error)
                                <div class="form-control-feedback">{{ $error }}</div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="row">
               <div class="col-md-6">
                    <div class="form-group {{ $errors->has('longTotalInstallment') ? ' has-danger' : '' }}">
                        <label for="total-installment">Total Installment</label>
                        <input type="text" class="form-control longTotalInstallment" name="longTotalInstallment" value="{{ old('longTotalInstallment') }}" oninput="calculateLongInstallmentAmount()">
                        @if ($errors->has('longTotalInstallment'))
                            @foreach($errors->get('longTotalInstallment') as $error)
                                <div class="form-control-feedback">{{ $error }}</div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group {{ $errors->has('longInstallmentAmount') ? ' has-danger' : '' }}">
                        <label for="monthly-installment-amount">Installment Amount</label>
                        <input type="number" class="form-control longInstallmentAmount" name="longInstallmentAmount" value="{{ old('longInstallmentAmount') }}">
                        @if ($errors->has('longInstallmentAmount'))
                            @foreach($errors->get('longInstallmentAmount') as $error)
                                <div class="form-control-feedback">{{ $error }}</div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group {{ $errors->has('productUsageAddress') ? ' has-danger' : '' }}">
                <label for="product-usage-address">Product Usage Address</label>
                <textarea name="productUsageAddress" class="form-control" rows="5"></textarea>
                @if ($errors->has('productUsageAddress'))
                    @foreach($errors->get('productUsageAddress') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
@extends('admin.layouts.masterAddEdit')

@section('custom_css')
    <style type="text/css">
        .blockTitle{
            color: #333;
            font-family: tahoma;
            border-bottom: 1px solid #a2a2a2;
            display: inline-block;
            padding-bottom: 6px;
        }
    </style>
@endsection

@section('card_body')
	<div class="card-body">
	    <div class="row">
	        <div class="col-md-12">
	            <h4 class="text-center" style="font-weight: bold;font-family: tahoma">Product Description</h4>
	        </div>
	    </div>

	    <div class="row">
	        <div class="col-md-3">
	            <div class="form-group {{ $errors->has('customerId') ? ' has-danger' : '' }}">
	                <label for="customer-ac-no">Customer A/C No.</label><span style="color: red; font-size: 18px;">*</span>
	                <select class="form-control chosen-select customer" name="customerId" data-placeholder="Select Customer A/C">
	                    <option value="">Select Customer A/C</option>
	                    @foreach ($customers as $customer)
	                      <option value="{{$customer->id}}">{{$customer->code}} ({{$customer->name}})</option>
	                    @endforeach
	                </select>
	                @if ($errors->has('customerId'))
	                    @foreach($errors->get('customerId') as $error)
	                        <div class="form-control-feedback">{{ $error }}</div>
	                    @endforeach
	                @endif
	            </div>
	        </div>

	        <div class="col-md-3">
	            <div class="form-group {{ $errors->has('customerName') ? ' has-danger' : '' }}">
	                <label for="nick-name">Customer Name</label>
	                <input type="text" class="form-control customerName" name="customerName" value="{{ old('customerName') }}" readonly>
	                @if ($errors->has('customerName'))
	                    @foreach($errors->get('customerName') as $error)
	                        <div class="form-control-feedback">{{ $error }}</div>
	                    @endforeach
	                @endif
	            </div>
	        </div>

	        <div class="col-md-3">
	            <div class="form-group {{ $errors->has('customerPhone') ? ' has-danger' : '' }}">
	                <label for="nick-name">Customer Phone</label>
	                <input type="text" class="form-control customerPhone" name="customerPhone" value="{{ old('customerPhone') }}" readonly>
	                @if ($errors->has('customerPhone'))
	                    @foreach($errors->get('customerPhone') as $error)
	                        <div class="form-control-feedback">{{ $error }}</div>
	                    @endforeach
	                @endif
	            </div>
	        </div>

			<div class="col-md-3">
	            <div class="form-group">
	                <label for="nick-name">Invoice No</label>
	                <input type="text" class="form-control customerPhone" name="invoice_no" value="{{ $invoiceNo }}" readonly>
	            </div>
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
	            <div class="form-group {{ $errors->has('productModel') ? ' has-danger' : '' }}">
	                <label for="nick-name">Product Model</label>
	                <input type="text" class="form-control productModel" name="productModel" value="{{ old('productModel') }}" required readonly>
	                @if ($errors->has('productModel'))
	                    @foreach($errors->get('productModel') as $error)
	                        <div class="form-control-feedback">{{ $error }}</div>
	                    @endforeach
	                @endif
	            </div>
	        </div>
	    </div>

	    <div class="row">
	        <div class="col-md-3">
	            <div class="form-group {{ $errors->has('discount') ? ' has-danger' : '' }}">
	                <label for="discount">Discount</label>
	                <input type="number" min="0" name="discount" class="form-control discount" value="0" oninput="calculateInstallmentDueAmount()">
	                @if ($errors->has('discount'))
	                    @foreach($errors->get('discount') as $error)
	                        <div class="form-control-feedback">{{ $error }}</div>
	                    @endforeach
	                @endif
	            </div>
	        </div>

	        <div class="col-md-3">
	            <div class="form-group {{ $errors->has('giftVoucher') ? ' has-danger' : '' }}">
	                <label for="gift-voucher">Gift Voucher</label>
	                <input type="number" min="0" name="giftVoucher" class="form-control giftVoucher" value="0" oninput="calculateInstallmentDueAmount()">
	                @if ($errors->has('giftVoucher'))
	                    @foreach($errors->get('giftVoucher') as $error)
	                        <div class="form-control-feedback">{{ $error }}</div>
	                    @endforeach
	                @endif
	            </div>
	        </div>
	        
	        <div class="col-md-3">
	            <div class="form-group {{ $errors->has('exchangeCrt') ? ' has-danger' : '' }}">
	                <label for="exchange-crt">Exchange CRT</label>
	                <input type="number" min="0" name="exchangeCrt" class="form-control exchangeCrt" value="0" oninput="calculateInstallmentDueAmount()">
	                @if ($errors->has('exchangeCrt'))
	                    @foreach($errors->get('exchangeCrt') as $error)
	                        <div class="form-control-feedback">{{ $error }}</div>
	                    @endforeach
	                @endif
	            </div>
	        </div>

	        <div class="col-md-3">
	            <div class="form-group {{ $errors->has('mobileGift') ? ' has-danger' : '' }}">
	                <label for="mobile-gift">Mobile Gift</label>
	                <input type="number" min="0" name="mobileGift" class="form-control mobileGift" value="0">
	                @if ($errors->has('mobileGift'))
	                    @foreach($errors->get('mobileGift') as $error)
	                        <div class="form-control-feedback">{{ $error }}</div>
	                    @endforeach
	                @endif
	            </div>
	        </div>
	    </div>

	    <div class="row">
	        <div class="col-md-3">
	            <div class="form-group {{ $errors->has('warranty') ? ' has-danger' : '' }}">
	                <label for="warranty">Warranty</label>
	                <input type="number" min="0" name="warranty" class="form-control warranty" readonly>
	                @if ($errors->has('warranty'))
	                    @foreach($errors->get('warranty') as $error)
	                        <div class="form-control-feedback">{{ $error }}</div>
	                    @endforeach
	                @endif
	            </div>
	        </div>

	        <div class="col-md-3">
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
	                <select class="form-control chosen-select" name="referenceId" required>
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
	        <div class="col-md-2">
	            <div class="form-group {{ $errors->has('cashPrice') ? ' has-danger' : '' }}">
	                <label for="cash-price">Cash Price</label>
	                <input type="number" min="0" name="cashPrice" class="form-control cashPrice" value="0">
	                @if ($errors->has('cashPrice'))
	                    @foreach($errors->get('cashPrice') as $error)
	                        <div class="form-control-feedback">{{ $error }}</div>
	                    @endforeach
	                @endif
	            </div>
	        </div>

	        <div class="col-md-2">
	            <div class="form-group {{ $errors->has('salesPrice') ? ' has-danger' : '' }}">
	                <label for="cash-price">Sales Price</label>
	                <input type="number" min="0" name="salesPrice" class="form-control salesPrice" value="0">
	                @if ($errors->has('salesPrice'))
	                    @foreach($errors->get('salesPrice') as $error)
	                        <div class="form-control-feedback">{{ $error }}</div>
	                    @endforeach
	                @endif
	            </div>
	        </div>

	        <div class="col-md-3">
	            <div class="form-group {{ $errors->has('dealerId') ? ' has-danger' : '' }}">
	                <label for="showroom-id">Showroom Project Name</label><span style="color: red; font-size: 18px;">*</span>
	                <select class="form-control chosen-select" name="dealerId" <?php if( $showroomId == 1) { ?> required="" <?php } ?>>
	                    <option value="">Select Showroom Project Name</option>
	                    @foreach ($showroomProjects as $showroomProject)
	                        <option value="{{$showroomProject->id}}">{{$showroomProject->name}}</option>
	                    @endforeach
	                </select>
	                @if ($errors->has('dealerId'))
	                    @foreach($errors->get('dealerId') as $error)
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
	        <div class="col-md-2">
                <div class="form-group {{ $errors->has('shortInstallmentPrice') ? ' has-danger' : '' }}">
                    <label for="mrp-price">MRP Price</label>
                    <input type="number" min="0" class="form-control shortInstallmentPrice" name="shortInstallmentPrice" value="{{ old('shortInstallmentPrice') }}">
                    @if ($errors->has('shortInstallmentPrice'))
                        @foreach($errors->get('shortInstallmentPrice') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group {{ $errors->has('shortInstallmentDeposite') ? ' has-danger' : '' }}">
                    <label for="deposite">Deposite</label>
                    <input type="number" min="0" class="form-control shortInstallmentDeposite" name="shortInstallmentDeposite" value="0" oninput="calculateInstallmentDueAmount()">
                    @if ($errors->has('shortInstallmentDeposite'))
                        @foreach($errors->get('shortInstallmentDeposite') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group {{ $errors->has('shortInstallmentDueAmount') ? ' has-danger' : '' }}">
                    <label for="deposite">Due Amount</label>
                    <input type="number" min="0" class="form-control shortInstallmentDueAmount" name="shortInstallmentDueAmount" value="0">
                    @if ($errors->has('shortInstallmentDueAmount'))
                        @foreach($errors->get('shortInstallmentDueAmount') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

	        <div class="col-md-3">
                <div class="form-group {{ $errors->has('shortTotalInstallment') ? ' has-danger' : '' }}">
                    <label for="total-installment">Total Installment</label>
                    <input type="number" min="0" class="form-control shortTotalInstallment" name="shortTotalInstallment" value="0" oninput="calculateShortInstallmentAmount()">
                    @if ($errors->has('shortTotalInstallment'))
                        @foreach($errors->get('shortTotalInstallment') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group {{ $errors->has('shortInstallmentAmount') ? ' has-danger' : '' }}">
                    <label for="monthly-installment-amount">Installment Amount</label>
                    <input type="number" min="0" class="form-control shortInstallmentAmount" name="shortInstallmentAmount" value="0">
                    @if ($errors->has('shortInstallmentAmount'))
                        @foreach($errors->get('shortInstallmentAmount') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>
	    </div>

	    <div class="row longInstallmentRow">
            <div class="col-md-2">
                <div class="form-group {{ $errors->has('longInstallmentPrice') ? ' has-danger' : '' }}">
                    <label for="higher-price">Hire Price</label>
                    <input type="number" min="0" class="form-control longInstallmentPrice" name="longInstallmentPrice" value="0">
                    @if ($errors->has('longInstallmentPrice'))
                        @foreach($errors->get('longInstallmentPrice') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group {{ $errors->has('longInstallmentDeposite') ? ' has-danger' : '' }}">
                    <label for="deposite">Deposite</label>
                    <input type="number" min="0" class="form-control longInstallmentDeposite" name="longInstallmentDeposite" value="0" oninput="calculateInstallmentDueAmount()">
                    @if ($errors->has('longInstallmentDeposite'))
                        @foreach($errors->get('longInstallmentDeposite') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group {{ $errors->has('longInstallmentDueAmount') ? ' has-danger' : '' }}">
                    <label for="deposite">Due Amount</label>
                    <input type="number" min="0" class="form-control longInstallmentDueAmount" name="longInstallmentDueAmount" value="0">
                    @if ($errors->has('longInstallmentDueAmount'))
                        @foreach($errors->get('longInstallmentDueAmount') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group {{ $errors->has('longTotalInstallment') ? ' has-danger' : '' }}">
                    <label for="total-installment">Total Installment</label>
                    <input type="number" min="0" class="form-control longTotalInstallment" name="longTotalInstallment" value="0" oninput="calculateLongInstallmentAmount()">
                    @if ($errors->has('longTotalInstallment'))
                        @foreach($errors->get('longTotalInstallment') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group {{ $errors->has('longInstallmentAmount') ? ' has-danger' : '' }}">
                    <label for="monthly-installment-amount">Installment Amount</label>
                    <input type="number" min="0" class="form-control longInstallmentAmount" name="longInstallmentAmount" value="0">
                    @if ($errors->has('longInstallmentAmount'))
                        @foreach($errors->get('longInstallmentAmount') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>
	    </div>

	    <div class="guarantorInfo">	    	
		    <div class="row">
		        <div class="col-md-12">
		            <h4 class="text-center" style="font-weight: bold;font-family: tahoma">Guarantor Information</h4>
		        </div>
		    </div>

		    <div class="row">
		        <div class="col-md-6">
		            <div class="form-group {{ $errors->has('gurantorName') ? ' has-danger' : '' }}">
		                <label for="gurantor-name">Guarantor Name</label>
		                <input type="text" class="form-control" name="gurantorName" value="{{ old('gurantorName') }}">
		                @if ($errors->has('gurantorName'))
		                    @foreach($errors->get('gurantorName') as $error)
		                        <div class="form-control-feedback">{{ $error }}</div>
		                    @endforeach
		                @endif
		            </div>
		        </div>

		         <div class="col-md-6">
		            <div class="form-group {{ $errors->has('gurantorPhoneNo') ? ' has-danger' : '' }}">
		                <label for="gurantor-phone-no">Guarantor Phone No</label>
		                <input type="text" class="form-control" name="gurantorPhoneNo" value="{{ old('gurantorPhoneNo') }}">
		                @if ($errors->has('gurantorPhoneNo'))
		                    @foreach($errors->get('gurantorPhoneNo') as $error)
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
                        <input type="text" class="form-control" name="guarantorFatherName">
                        @if ($errors->has('guarantorFatherName'))
                            @foreach($errors->get('guarantorFatherName') as $error)
                                <div class="form-control-feedback">{{ $error }}</div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group {{ $errors->has('gurantorAge') ? ' has-danger' : '' }}">
                        <label for="guarantor-age">Guarantor Age</label>
                        <input type="number" min="0" class="form-control" name="gurantorAge">
                        @if ($errors->has('gurantorAge'))
                            @foreach($errors->get('gurantorAge') as $error)
                                <div class="form-control-feedback">{{ $error }}</div>
                            @endforeach
                        @endif
                    </div>
                </div>

		        <div class="col-md-3">
				    @php
				        $maritalStatus = array('Unmarried' => 'Unmarried', 'Married' => 'Married');
				    @endphp
                    <div class="form-group {{ $errors->has('guarantorMaritalStatus') ? ' has-danger' : '' }}">
                        <label for="guarantor-mrital-status">Marital Status</label>
                        <select name="guarantorMaritalStatus" class="form-control firstGuarantorMaritalStatus" style="height: 41%">
                            <option value="">Select Marital Status</option>
                            @foreach ($maritalStatus as $key => $value)
                                <option value="{{$value}}" {{@$selected}}>{{$value}}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('guarantorMaritalStatus'))
                            @foreach($errors->get('guarantorMaritalStatus') as $error)
                                <div class="form-control-feedback">{{ $error }}</div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group {{ $errors->has('guarantorSpouseName') ? ' has-danger' : '' }}">
                        <label for="guarantor-spouse-name">Spouse Name</label>
                        <input type="text" class="form-control guarantorSpouce" name="guarantorSpouseName">
                        @if ($errors->has('guarantorSpouseName'))
                            @foreach($errors->get('guarantorSpouseName') as $error)
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
		                <input type="text" class="form-control" name="guarantorProfessionName" value="{{ old('guarantorProfessionName') }}">
		                @if ($errors->has('guarantorProfessionName'))
		                    @foreach($errors->get('guarantorProfessionName') as $error)
		                        <div class="form-control-feedback">{{ $error }}</div>
		                    @endforeach
		                @endif
		            </div>
		        </div>

		        <div class="col-md-3">
		            <div class="form-group {{ $errors->has('guarantorDesignation') ? ' has-danger' : '' }}">
		                <label for="guarantor-designation">Designation</label>
		                <input type="text" class="form-control" name="guarantorDesignation" value="{{ old('guarantorDesignation') }}">
		                @if ($errors->has('guarantorDesignation'))
		                    @foreach($errors->get('guarantorDesignation') as $error)
		                        <div class="form-control-feedback">{{ $error }}</div>
		                    @endforeach
		                @endif
		            </div>
		        </div>

                <div class="col-md-3">
                    <div class="form-group {{ $errors->has('guarantorWorkplacePhoneNo') ? ' has-danger' : '' }}">
                        <label for="guarantor-workplace-phoneNo">Professional Phone No</label>
                        <input type="text" class="form-control" name="guarantorWorkplacePhoneNo" value="{{ old('guarantorDesignation') }}" >
                        @if ($errors->has('guarantorWorkplacePhoneNo'))
                            @foreach($errors->get('guarantorWorkplacePhoneNo') as $error)
                                <div class="form-control-feedback">{{ $error }}</div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group {{ $errors->has('guarantorMonthlyIncome') ? ' has-danger' : '' }}">
                        <label for="guarantor-monthly-income">Monthly Income</label>
                        <input type="number" min="0" class="form-control" name="guarantorMonthlyIncome" value="{{ old('guarantorDesignation') }}">
                        @if ($errors->has('guarantorMonthlyIncome'))
                            @foreach($errors->get('guarantorMonthlyIncome') as $error)
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
		                <textarea name="guarantorPresentAddress" class="form-control" rows="2"></textarea>
		                @if ($errors->has('guarantorPresentAddress'))
		                    @foreach($errors->get('guarantorPresentAddress') as $error)
		                        <div class="form-control-feedback">{{ $error }}</div>
		                    @endforeach
		                @endif
		            </div>
		        </div>

		        <div class="col-md-4">
		            <div class="form-group {{ $errors->has('guarantorPermanentAddress') ? ' has-danger' : '' }}">
		                <label for="guarantor-permanent-address">Permanent Address</label>
		                <textarea name="guarantorPermanentAddress" class="form-control" rows="2"></textarea>
		                @if ($errors->has('guarantorPermanentAddress'))
		                    @foreach($errors->get('guarantorPermanentAddress') as $error)
		                        <div class="form-control-feedback">{{ $error }}</div>
		                    @endforeach
		                @endif
		            </div>
		        </div>

		        <div class="col-md-4">
	                <div class="form-group {{ $errors->has('guarantorWorkPlaceAddress') ? ' has-danger' : '' }}">
		            	<label for="monthly-income">Work Place address</label>
		            	<textarea name="guarantorWorkPlaceAddress" class="form-control" rows="2"></textarea>
	                    @if ($errors->has('guarantorWorkPlaceAddress'))
	                        @foreach($errors->get('guarantorWorkPlaceAddress') as $error)
	                            <div class="form-control-feedback">{{ $error }}</div>
	                        @endforeach
	                    @endif
	                </div>
		        </div>
		    </div>
	    </div>

	    <div class="row">
	        <div class="col-md-12">
	            <div class="form-group {{ $errors->has('productUsageAddress') ? ' has-danger' : '' }}">
	                <label for="product-usage-address">Product Usage Address</label>
	                <textarea name="productUsageAddress" class="form-control" rows="2"></textarea>
	                @if ($errors->has('productUsageAddress'))
	                    @foreach($errors->get('productUsageAddress') as $error)
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
        $(document).ready(function() {
            /*code for guarantor info*/
            $(".guarantorSpouce").prop('disabled', true);
            $(".guarantorSpouce").prop('disabled', true);
                $('.firstGuarantorMaritalStatus').click(function(event) {
                    var firstGuarantorMaritalStatus = $('.firstGuarantorMaritalStatus').val();
                    if(firstGuarantorMaritalStatus == "Married"){
                         $(".guarantorSpouce").prop('disabled', false);
                    }else{
                         $(".guarantorSpouce").prop('disabled', true);
                        $(".guarantorSpouce").val('');
                    }
                })

                $('.secondGuarantorMaritalStatus').click(function(event) {
                    var secondGuarantorMaritalStatus = $('.secondGuarantorMaritalStatus').val();
                    if(secondGuarantorMaritalStatus == "Married"){
                         $(".guarantorSpouce").prop('disabled', false);
                    }else{
                         $(".guarantorSpouce").prop('disabled', true);
                        $(".guarantorSpouce").val('');
                    }
                })

            /*end code for guarantor info*/

            // start code installment

            $('.installmentType').hide();
            $('.longInstallmentRow').hide();
            $('.shortInstallmentRow').hide();
            $('.guarantorInfo').hide();

             $('.purchaseType').click(function(event) {
                var purchaseType =  $("input[name='purchaseType']:checked").val();

                if(purchaseType == "Cash")
                {
                    $('.installmentType').hide();
                    $("select[name='installmentType']").prop('required',false);
                	$('.guarantorInfo').hide();  
                }
                else
                {
                	$('.guarantorInfo').show();                	
                }

                if(purchaseType == "Short Installment")
                {
                    $('.shortInstallmentRow').show();
                    $('.installmentType').show();
                    $("input[name='shortInstallmentDeposite']").prop('required',true);
                    $("input[name='shortInstallmentPrice']").prop('required',true);
                    $("input[name='shortTotalInstallment']").prop('required',true);
                    $("input[name='shortInstallmentAmount']").prop('required',true);
                    $("select[name='installmentType']").prop('required',true);
                }
                else
                {
                    $('.shortInstallmentRow').hide();
                    $("input[name='shortInstallmentDeposite']").prop('required',false);
                    $("input[name='shortInstallmentPrice']").prop('required',false);
                    $("input[name='shortTotalInstallment']").prop('required',false);
                    $("input[name='shortInstallmentAmount']").prop('required',false);
                }

                if(purchaseType == "Long Installment")
                {
                    $('.longInstallmentRow').show();
                    $('.installmentType').show();
                    $("input[name='longInstallmentDeposite']").prop('required',true);
                    $("input[name='longInstallmentPrice']").prop('required',true);
                    $("input[name='longTotalInstallment']").prop('required',true);
                    $("input[name='longInstallmentAmount']").prop('required',true);
                    $("select[name='installmentType']").prop('required',true);
                }
                else
                {
                    $('.longInstallmentRow').hide();
                    $("input[name='longInstallmentDeposite']").prop('required',false);
                    $("input[name='longInstallmentPrice']").prop('required',false);
                    $("input[name='longTotalInstallment']").prop('required',false);
                    $("input[name='longInstallmentAmount']").prop('required',false);
                }
            })

            /*end code for installment*/            
        });
    </script>


    <script type="text/javascript">
    /*code for product info*/
        $(document).on('change', '.customer', function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            var customerId = $('.customer option:selected').val();
            if(customerId != '')
            {
                $.ajax({
                    type:'post',
                    url:'{{ route('retailSales.getCustomerInfo') }}',
                    data:{customerId:customerId},
                    success:function(data){
                        var customer = data.customer;
                        $('.customerName').val(customer.name);
                        $('.customerPhone').val(customer.phone_no);
                    }
                });
            }
            else
            {
                $('.customerName').val("");
                $('.customerPhone').val("");
            }
        });

        $(document).on('change', '.product', function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            var productId = $('.product option:selected').val();
            if(productId != '')
            {
                $.ajax({
                    type:'post',
                    url:'{{ route('retailSales.getProductInfo') }}',
                    data:{productId:productId},
                    success:function(data){
                        var product = data.product;
                        var purchaseType =  $("input[name='purchaseType']:checked").val();
                        $('.productModel').val(product.model_no);
                        $('.cashPrice').val(product.price);
                        $('.warranty').val(product.warranty);

                        if (purchaseType == 'Short Installment')
                        {
                            $('.shortInstallmentPrice').val(product.mrp_price);
                        }
                        else
                        {
                            $('.shortInstallmentPrice').val('');                             
                        }

                        if (purchaseType == 'Long Installment')
                        {
                            $('.longInstallmentPrice').val(product.haire_price);
                        }
                        else
                        {
                            $('.longInstallmentPrice').val('');                             
                        }
                        calculateInstallmentDueAmount()
                        calculateShortInstallmentAmount();
                        calculateLongInstallmentAmount();
                    }
                });
            }
            else
            {
               $('.productModel').val('');
               $('.cashPrice').val('');  
               $('.warranty').val(''); 
               $('.longInstallmentPrice').val(''); 
               $('.shortInstallmentPrice').val(''); 
            }
        });

        function calculateInstallmentDueAmount()
        {
        	var purchaseType =  $("input[name='purchaseType']:checked").val();

            var discount = parseInt($('.discount').val());
            var giftVoucher = parseInt($('.giftVoucher').val());
            var exchangeCrt = parseInt($('.exchangeCrt').val());

            if (purchaseType == "Cash")
            {
        		calculateSalesPrice(discount,giftVoucher,exchangeCrt);
            }
        	else if (purchaseType == "Short Installment")
        	{
        		var price = parseInt($('.shortInstallmentPrice').val());
        		var deposite = parseInt($('.shortInstallmentDeposite').val());
        		var dueAmount = price - deposite - discount - giftVoucher - exchangeCrt;

        		$('.shortInstallmentDueAmount').val(Math.round(dueAmount));
        		calculateShortInstallmentAmount();
        		calculateSalesPrice(discount,giftVoucher,exchangeCrt);
        	}
        	else
        	{
        		var price = parseInt($('.longInstallmentPrice').val());
        		var deposite = parseInt($('.longInstallmentDeposite').val());
        		var dueAmount = price - deposite - discount - giftVoucher - exchangeCrt;

        		$('.longInstallmentDueAmount').val(Math.round(dueAmount));
        		calculateLongInstallmentAmount();
        		calculateSalesPrice(discount,giftVoucher,exchangeCrt);
        	}
        }

        function calculateSalesPrice(discount,giftVoucher,exchangeCrt)
        {
    		var price = parseInt($('.cashPrice').val());
    		var salesPrice = price - discount - giftVoucher - exchangeCrt;

    		$('.salesPrice').val(Math.round(salesPrice));        	
        }

        function calculateShortInstallmentAmount()
        {
            var dueAmount = parseFloat($('.shortInstallmentDueAmount').val());
            var shortTotalInstallment = parseFloat($('.shortTotalInstallment').val());

            if (shortTotalInstallment == 0 || $('.shortTotalInstallment').val() == "")
            {
                var shortInstallmentAmount = dueAmount;
            }
            else
            {
                var shortInstallmentAmount = dueAmount/shortTotalInstallment;                
            }

            $('.shortInstallmentAmount').val(Math.round(shortInstallmentAmount));
        }

        function calculateLongInstallmentAmount()
        {
            var dueAmount = parseFloat($('.longInstallmentDueAmount').val());
            var longTotalInstallment = parseFloat($('.longTotalInstallment').val());

            if (longTotalInstallment == 0 || $('.longTotalInstallment').val() == "")
            {
                var longInstallmentAmount = dueAmount;
            }
            else
            {
                var longInstallmentAmount = dueAmount/longTotalInstallment;                
            }

            $('.longInstallmentAmount').val(Math.round(longInstallmentAmount));
        }

  

    </script>
@endsection
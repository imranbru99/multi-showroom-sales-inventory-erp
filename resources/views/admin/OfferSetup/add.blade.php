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
	        <div class="col-md-4">
	            <div class="form-group {{ $errors->has('offerName') ? ' has-danger' : '' }}">
	                <label for="offerName">Offer Name</label>
	                <input type="text" class="form-control offerName" name="offerName" value="{{ old('offerName') }}" required="">
	                @if ($errors->has('offerName'))
	                    @foreach($errors->get('offerName') as $error)
	                        <div class="form-control-feedback">{{ $error }}</div>
	                    @endforeach
	                @endif
	            </div>
	        </div>

	        <div class="col-md-4">
	            <div class="form-group {{ $errors->has('start_date') ? ' has-danger' : '' }}">
	                <label for="start_date">Start Date</label>
	                <input type="text" name="start_date" class="form-control add_datepicker" readonly="">
	                @if ($errors->has('start_date'))
	                    @foreach($errors->get('start_date') as $error)
	                        <div class="form-control-feedback">{{ $error }}</div>
	                    @endforeach
	                @endif
	            </div>
	        </div>

	        <div class="col-md-4">
	            <div class="form-group {{ $errors->has('end_date') ? ' has-danger' : '' }}">
	                <label for="end_date">End Date</label>
	                <input type="text" name="end_date" class="form-control datepicker" id="from_date_null" readonly="">
	                @if ($errors->has('end_date'))
	                    @foreach($errors->get('end_date') as $error)
	                        <div class="form-control-feedback">{{ $error }}</div>
	                    @endforeach
	                @endif
	            </div>
	        </div>
	    </div>

	    <div class="row">
	        <div class="col-md-4">
	            <div class="form-group {{ $errors->has('product') ? ' has-danger' : '' }}">
		            <label for="product">Product</label>
	                <select class="form-control chosen-select" name="product_id" required="">
	                    <option value="">Select Product</option>
	                    @foreach($products as $product)
	                        <option value="{{ $product->id }}">{{ $product->name }}-{{ $product->model_no}}</option>
	                    @endforeach
	                </select>
	                @if ($errors->has('product'))
	                    @foreach($errors->get('product') as $error)
	                        <div class="form-control-feedback">{{ $error }}</div>
	                    @endforeach
	                @endif
		        </div>
	        </div>
	        <div class="col-md-4">
	            <div class="form-group {{ $errors->has('offer_type') ? ' has-danger' : '' }}">
		            <label for="offer_type">Offer Type</label>
	                <select class="form-control chosen-select" name="offer_type" id="offer_type" required="">
	                    <option value="">Select Offer Type</option>
	                    <option value="fixed">Fixed</option>
	                    <option value="percentage">Percentage</option>
	                </select>
	                @if ($errors->has('offer_type'))
	                    @foreach($errors->get('offer_type') as $error)
	                        <div class="form-control-feedback">{{ $error }}</div>
	                    @endforeach
	                @endif
		        </div>
	        </div>

	        <div class="col-md-4 offerAmountSection">
	            <div class="form-group {{ $errors->has('offerAmount') ? ' has-danger' : '' }}">
	                <label for="offerAmount">Amount</label>
	                <input type="number" class="form-control offerAmount" name="offerAmount" value="{{ old('offerAmount') }}">
	                @if ($errors->has('offerAmount'))
	                    @foreach($errors->get('offerAmount') as $error)
	                        <div class="form-control-feedback">{{ $error }}</div>
	                    @endforeach
	                @endif
	            </div>
	        </div>

	        <div class="col-md-4 percAmountSection">
	            <div class="form-group {{ $errors->has('percAmount') ? ' has-danger' : '' }}">
	                <label for="percAmount">Amount(In Percentage)</label>
	                <input type="number" class="form-control percAmount" name="percAmount" value="{{ old('percAmount') }}">
	                @if ($errors->has('percAmount'))
	                    @foreach($errors->get('percAmount') as $error)
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

        	$('.offerAmountSection').hide();
            $('.percAmountSection').hide();
            
             $('#offer_type').on('change', function() {
             
                var offer_type =  $('#offer_type').val();
              

                if(offer_type == "fixed")
                {
                    $('.offerAmountSection').show();
                    $('.percAmountSection').hide();
                    $("input[name='offerAmount']").prop('required',true);
                    $("input[name='percAmount']").prop('required',false);
                   
                }
                else
                {
                    $('.offerAmountSection').hide();
                    $('.percAmountSection').show();
                    $("input[name='offerAmount']").prop('required',false);
                    $("input[name='percAmount']").prop('required',true);
                }

            });
             
        });
    </script>
@endsection
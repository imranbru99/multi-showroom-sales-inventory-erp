@extends('admin.layouts.masterAddEdit')

@section('card_body')
    <style type="text/css">
        .chosen-single{
            height: 35px !important;
        }
    </style>

    <div class="card-body">
    	<div class="row">
    		<div class="col-md-6">
    			<div class="row">
    				<div class="col-md-12">
		            	<div class="form-group {{ $errors->has('code') ? ' has-danger' : '' }}">
		            		<label for="code-prefix">Code / Prefix</label>
		        			<input type="text" class="form-control" name="code" value="{{ old('code') }}" required>
		        			@if ($errors->has('code'))
		            			@foreach($errors->get('code') as $error)
		            				<div class="form-control-feedback">{{ $error }}</div>
		            			@endforeach
		        			@endif
		            	</div>
    				</div>
    			</div>
    			<div class="row">
    				<div class="col-md-12">
		            	<div class="form-group {{ $errors->has('bankName') ? ' has-danger' : '' }}">
		            		<label for="bank-name">Bank / Brunch Name</label>
		        			<input type="text" class="form-control" name="bankName" value="{{ old('bankName') }}" required>
		        			@if ($errors->has('bankName'))
		            			@foreach($errors->get('bankName') as $error)
		            				<div class="form-control-feedback">{{ $error }}</div>
		            			@endforeach
		        			@endif
		            	</div>
    				</div>
    			</div>
    			<div class="row">
    				<div class="col-md-12">
		            	<div class="form-group {{ $errors->has('phone') ? ' has-danger' : '' }}">
		            		<label for="phone-no">Phone No.</label>
		        			<input type="text" class="form-control" name="phone" value="{{ old('phone') }}" required>
		        			@if ($errors->has('phone'))
		            			@foreach($errors->get('phone') as $error)
		            				<div class="form-control-feedback">{{ $error }}</div>
		            			@endforeach
		        			@endif
		            	</div>
    				</div>
    			</div>
    		</div>

    		<div class="col-md-6">
            	<div class="form-group {{ $errors->has('address') ? ' has-danger' : '' }}">
            		<label for="address">Address</label>
            		<textarea class="form-control" name="address"rows="9">{{ old('address') }}</textarea>
        			@if ($errors->has('address'))
            			@foreach($errors->get('address') as $error)
            				<div class="form-control-feedback">{{ $error }}</div>
            			@endforeach
        			@endif
            	</div>
    		</div>
    	</div>
    </div>
@endsection
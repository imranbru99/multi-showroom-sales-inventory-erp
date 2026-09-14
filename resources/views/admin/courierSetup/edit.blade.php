@extends('admin.layouts.masterAddEdit')

@section('card_body')
    <style type="text/css">
        .chosen-single{
            height: 35px !important;
        }
    </style>

    <div class="card-body">
    	<input type="hidden" name="courierId" value="{{ $courier->id }}">
    	<div class="row">
    		<div class="col-md-6">
    			<div class="row">
    				<div class="col-md-12">
		            	<div class="form-group {{ $errors->has('code') ? ' has-danger' : '' }}">
		            		<label for="code-prefix">Code / Prefix</label>
		        			<input type="text" class="form-control" name="code" value="{{ $courier->code }}" required>
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
		            	<div class="form-group {{ $errors->has('courierName') ? ' has-danger' : '' }}">
		            		<label for="courier-name">Courier Name</label>
		        			<input type="text" class="form-control" name="courierName" value="{{ $courier->name }}" required>
		        			@if ($errors->has('courierName'))
		            			@foreach($errors->get('courierName') as $error)
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
		        			<input type="text" class="form-control" name="phone" value="{{ $courier->phone }}" required>
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
            		<textarea class="form-control" name="address"rows="9">{{ $courier->address }}</textarea>
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
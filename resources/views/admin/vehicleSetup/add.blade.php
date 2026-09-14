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
            	<div class="form-group {{ $errors->has('registrationNo') ? ' has-danger' : '' }}">
            		<label for="registration-no">Registration No</label>
            		<input type="text" class="form-control" name="registrationNo" value="{{ old('registrationNo') }}" required>
        			@if ($errors->has('registrationNo'))
            			@foreach($errors->get('registrationNo') as $error)
            				<div class="form-control-feedback">{{ $error }}</div>
            			@endforeach
        			@endif
            	</div>
    		</div>

    		<div class="col-md-6">
    			<div class="row">
    				<div class="col-md-6">
    					@php
    						$types = array('Heavy'=>'Heavy','Medium'=>'Medium','Light'=>'Light','Pickup Van'=>'Pickup Van')
    					@endphp
		            	<div class="form-group {{ $errors->has('type') ? ' has-danger' : '' }}">
		            		<label for="type">Type</label>
		            		<select class="form-control" name="type">
		            			<option value="">Select Vehicle Type</option>
		            			@foreach ($types as $key => $value)
		            				<option value="{{ $key }}">{{ $value }}</option>
		            			@endforeach
		            		</select>
		        			@if ($errors->has('type'))
		            			@foreach($errors->get('type') as $error)
		            				<div class="form-control-feedback">{{ $error }}</div>
		            			@endforeach
		        			@endif
		            	</div>
    				</div>

    				<div class="col-md-6">
		            	<div class="form-group {{ $errors->has('capacity') ? ' has-danger' : '' }}">
		            		<label for="capacity">Capacity</label>
		        			<input type="text" class="form-control" name="capacity" value="{{ old('capacity') }}" required>
		        			@if ($errors->has('capacity'))
		            			@foreach($errors->get('capacity') as $error)
		            				<div class="form-control-feedback">{{ $error }}</div>
		            			@endforeach
		        			@endif
		            	</div>
    				</div>
    			</div>
    		</div>
    	</div>
    </div>
@endsection
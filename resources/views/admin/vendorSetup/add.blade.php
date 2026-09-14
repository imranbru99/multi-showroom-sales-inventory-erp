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
                <label for="Code" >Code</label>
                <div class="form-group {{ $errors->has('code') ? ' has-danger' : '' }}">
                    <input type="text" class="form-control form-control-danger" name="code" value="{{ old('code') }}" required="">
                    @if ($errors->has('code'))
                        @foreach($errors->get('code') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>
            <div class="col-md-6">
                <label for="vendor-name" >Vendor Name</label>
                <div class="form-group {{ $errors->has('vendorName') ? ' has-danger' : '' }}">
                    <input type="text" class="form-control form-control-danger" name="vendorName" value="{{ old('vendorName') }}" required>
                    @if ($errors->has('vendorName'))
                        @foreach($errors->get('vendorName') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <label for="contact-person">Contact Person</label>
                <div class="form-group {{ $errors->has('contactPerson') ? ' has-danger' : '' }}">
                    <input type="text" class="form-control form-control-danger" placeholder="contact person" name="contactPerson" value="{{ old('contactPerson') }}">
                    @if ($errors->has('contactPerson'))
                        @foreach($errors->get('contactPerson') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>
            <div class="col-md-6">
		        <div class="row">
		            <div class="col-md-6">
		                <label for="contact-number" >Contact Number</label>
		                <div class="form-group {{ $errors->has('contact') ? ' has-danger' : '' }}">
		                    <input type="text" class="form-control form-control-danger" name="contact" value="{{ old('contact') }}" required>
		                    @if ($errors->has('contact'))
		                        @foreach($errors->get('contact') as $error)
		                            <div class="form-control-feedback">{{ $error }}</div>
		                        @endforeach
		                    @endif
		                </div>
		            </div>
		            <div class="col-md-6">
		                <label for="email">Email</label>
		                <div class="form-group {{ $errors->has('email') ? ' has-danger' : '' }}">
		                    <input type="email" class="form-control form-control-danger" name="email" value="{{ old('email') }}">
		                    @if ($errors->has('email'))
		                        @foreach($errors->get('email') as $error)
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
                <label for="address" >Address</label>
                <div class="form-group {{ $errors->has('address') ? ' has-danger' : '' }}">
                    <textarea class="form-control form-control-danger" name="address" rows="5">{{ old('address') }}</textarea>
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
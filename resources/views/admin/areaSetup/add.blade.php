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
                <div class="col-md-6">
                    <div class="form-group {{ $errors->has('code') ? ' has-danger' : '' }}">
                        <label for="prefix">Prefix</label>
                        <input type="text" class="form-control" name="code" value="{{ old('code') }}" required>
                        @if ($errors->has('code'))
                        @foreach($errors->get('code') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group {{ $errors->has('regionId') ? ' has-danger' : '' }}">
                        <label for="region-name">Region Name</label>
                        <select class="form-control chosen-select" name="regionId" required>
                            <option value="">Select Region</option>
                            @foreach ($allArea as $region)
                            <option value="{{ $region->id }}">{{ $region->name }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('regionId'))
                        @foreach($errors->get('regionId') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group {{ $errors->has('inchargeName') ? ' has-danger' : '' }}">
                <label for="incharge-name">Incharge Name</label>
                <input type="text" class="form-control" name="inchargeName" value="{{ old('inchargeName') }}" required>
                @if ($errors->has('inchargeName'))
                @foreach($errors->get('inchargeName') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group {{ $errors->has('areaName') ? ' has-danger' : '' }}">
                        <label for="area-name">Area Name</label>
                        <input type="text" class="form-control" name="areaName" value="{{ old('areaName') }}" required>
                        @if ($errors->has('areaName'))
                        @foreach($errors->get('areaName') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group {{ $errors->has('contact') ? ' has-danger' : '' }}">
                        <label for="contact-number">Contact Number</label>
                        <input type="text" class="form-control" name="contact" value="{{ old('contact') }}" required>
                        @if ($errors->has('contact'))
                        @foreach($errors->get('contact') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group {{ $errors->has('email') ? ' has-danger' : '' }}">
                        <label for="email">Email</label>
                        <input type="text" class="form-control" name="email" value="{{ old('email') }}" required>
                        @if ($errors->has('email'))
                        @foreach($errors->get('email') as $error)
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
                <textarea class="form-control" name="address" rows="9">{{ old('address') }}</textarea>
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

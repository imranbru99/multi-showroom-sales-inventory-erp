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
                    <div class="form-group {{ $errors->has('areaId') ? ' has-danger' : '' }}">
                        <label for="area-name">Area Name</label>
                        <select class="form-control chosen-select" name="areaId" required>
                            <option value="">Select Area</option>
                            @foreach ($allArea as $area)
                            <option value="{{ $area->id }}">{{ $area->name }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('areaId'))
                        @foreach($errors->get('areaId') as $error)
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
                    <div class="form-group {{ $errors->has('territoryName') ? ' has-danger' : '' }}">
                        <label for="territory-name">Territory Name</label>
                        <input type="text" class="form-control" name="territoryName" value="{{ old('territoryName') }}" required>
                        @if ($errors->has('territoryName'))
                        @foreach($errors->get('territoryName') as $error)
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
        </div>

        <div class="col-md-6">
            <div class="form-group {{ $errors->has('address') ? ' has-danger' : '' }}">
                <label for="address">Address</label>
                <textarea class="form-control" name="address" rows="5">{{ old('address') }}</textarea>
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

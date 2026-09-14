@extends('admin.layouts.masterAddEdit')

@section('card_body')
<style type="text/css">
    .chosen-single{
        height: 35px !important;
    }
</style>

<div class="card-body">
    <input type="hidden" name="territoryId" value="{{ $territory->id }}">
    <div class="row">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group {{ $errors->has('code') ? ' has-danger' : '' }}">
                        <label for="prefix">Prefix</label>
                        <input type="text" class="form-control" name="code" value="{{ $territory->code }}" required>
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
                        <select class="form-control" name="areaId" required>
                            <option value="">Select Area</option>
                            @foreach ($allArea as $area)
                            @php
                            $select = '';
                            if ($area->id == $territory->area_id)
                            {
                            $select = 'selected';
                            }
                            else
                            {
                            $select = '';
                            }                                        
                            @endphp
                            <option value="{{ $area->id }}" {{ $select }}>{{ $area->name }}</option>
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
                <input type="text" class="form-control" name="inchargeName" value="{{ $territory->incharge_name }}" required>
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
                        <input type="text" class="form-control" name="territoryName" value="{{ $territory->name }}" required>
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
                        <input type="text" class="form-control" name="contact" value="{{ $territory->contact }}" required>
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
                <textarea class="form-control" name="address" rows="5">{{ $territory->address }}</textarea>
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
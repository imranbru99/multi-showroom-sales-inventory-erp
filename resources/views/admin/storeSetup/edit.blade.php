@extends('admin.layouts.masterAddEdit')

@section('card_body')
<style type="text/css">
    .chosen-single{
        height: 35px !important;
    }
</style>

<div class="card-body">
    <input type="hidden" name="storeId" value="{{ $store->id }}">

    <div class="row">
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('code') ? ' has-danger' : '' }}">
                <label for="code-prefix">Code/Prefix</label>
                <input type="text" class="form-control" name="code" value="{{ $store->code }}" required>
                @if ($errors->has('code'))
                @foreach($errors->get('code') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group {{ $errors->has('storeName') ? ' has-danger' : '' }}">
                <label for="store-name">Store Name</label>
                <input type="text" class="form-control" name="storeName" value="{{ $store->name }}" required>
                @if ($errors->has('storeName'))
                @foreach($errors->get('storeName') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('address') ? ' has-danger' : '' }}">
                <label for="address">Address</label>
                <textarea class="form-control" name="address"rows="5">{{ $store->address }}</textarea>
                @if ($errors->has('address'))
                @foreach($errors->get('address') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group {{ $errors->has('remarks') ? ' has-danger' : '' }}">
                <label for="remarks">Remarks</label>
                <textarea class="form-control" name="remarks"rows="5">{{ $store->remarks }}</textarea>
                @if ($errors->has('remarks'))
                @foreach($errors->get('remarks') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <label for="store-types">Store Types</label>
            <div class="form-group {{ $errors->has('type') ? ' has-danger' : '' }}">
                <?php
                $type1Status = "";
                $type2Status = "";
                $type3Status = "";
                $type4Status = "";
                $type5Status = "";
                $types = "";

                $types = explode(',', $store->type);

                foreach ($types as $type) {
                    if ($type == "Purchase Item Stock") {
                        $type1Status = 'checked';
                    }

                    if ($type == "Production Floor (Raw/Finish)") {
                        $type2Status = 'checked';
                    }

                    if ($type == "Finish Stock (Warehouse)") {
                        $type3Status = 'checked';
                    }

                    if ($type == "Damage Stock (Warehouse)") {
                        $type4Status = 'checked';
                    }
                    
                    if ($type == "Branch Store") {
                        $type5Status = 'checked';
                    }
                }
                ?>
                <div class="form-check-inline">
                    <label class="form-check-label" for="">
                        <input type="checkbox" class="form-check-input" id="type1" name="type[]" value="Purchase Item Stock" {{ $type1Status }}>Purchase Item Stock
                    </label>
                </div>

                <div class="form-check-inline">
                    <label class="form-check-label" for="">
                        <input type="checkbox" class="form-check-input" id="type2" name="type[]" value="Production Floor (Raw/Finish)" {{ $type2Status }}>Production Floor (Raw/Finish)
                    </label>
                </div>

                <div class="form-check-inline">
                    <label class="form-check-label" for="">
                        <input type="checkbox" class="form-check-input" id="type3" name="type[]" value="Finish Stock (Warehouse)" {{ $type3Status }}>Finish Stock (Warehouse)
                    </label>
                </div>

                <div class="form-check-inline">
                    <label class="form-check-label" for="">
                        <input type="checkbox" class="form-check-input" id="type4" name="type[]" value="Damage Stock (Warehouse)" {{ $type4Status }}>Damage Stock (Warehouse)
                    </label>
                </div>

                <div class="form-check-inline">
                    <label class="form-check-label" for="">
                        <input type="checkbox" class="form-check-input" id="type4" name="type[]" value="Branch Store" {{ $type5Status }}>Branch Store
                    </label>
                </div>


                @if ($errors->has('type'))
                @foreach($errors->get('type') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
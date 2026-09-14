@extends('admin.layouts.masterAddEdit')

@section('card_body')
<style type="text/css">
    .chosen-single{
        height: 35px !important;
    }
</style>

<div class="card-body">
    <div class="row">
        <div class="col-md-12">
            <input class="form-control" type="hidden" name="allowanceId" value="{{ $allowance->id }}">
        </div>
    </div>

    <div class="row">

        <div class="col-md-4">
            <div class="form-group {{ $errors->has('code') ? ' has-danger' : '' }}">
                <label for="leave-name">Allownce Code</label>
                <input type="text" class="form-control" name="code" value="{{ $allowance->code }}" required>
                @if ($errors->has('code'))
                @foreach($errors->get('code') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group {{ $errors->has('title') ? ' has-danger' : '' }}">
                <label for="leave-name">Allowance Title</label>
                <input type="text" class="form-control" name="title" value="{{ $allowance->title }}" required>
                @if ($errors->has('title'))
                @foreach($errors->get('title') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group {{ $errors->has('type') ? ' has-danger' : '' }}">
                <label for="leave-type">Allowance Type</label>
                <select class="form-control" name="type">
                    <option value="">Select Leave Type</option>
                    @foreach ($allownaceTypes as $key => $value)
                    @php
                    if ($key == $allowance->type)
                    {
                    $select = "selected";
                    }
                    else
                    {
                    $select = "";
                    }                                
                    @endphp
                    <option value="{{ $key }}" {{ $select }}>{{ $value }}</option>
                    @endforeach
                </select>
                @if ($errors->has('type'))
                @foreach($errors->get('type') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group {{ $errors->has('remarks') ? ' has-danger' : '' }}">
                <label for="leave-type"> Remarks </label>
                <textarea class="form-control" name="remarks" rows="5" required>{{ $allowance->remarks}}</textarea>
                @if ($errors->has('remarks'))
                @foreach($errors->get('remarks') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

    </div>

</div>
@endsection
@extends('admin.layouts.masterAddEdit')

@section('card_body')
    <style type="text/css">
        .chosen-single{
            height: 35px !important;
        }
    </style>

    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group {{ $errors->has('code') ? ' has-danger' : '' }}">
                    <label for="leave-name">Code</label>
                    <input type="number" class="form-control" name="code" value="{{ old('code') }}" required>
                    @if ($errors->has('code'))
                        @foreach($errors->get('code') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="form-group {{ $errors->has('title') ? ' has-danger' : '' }}">
                    <label for="leave-name">Allowance Name</label>
                    <input type="text" class="form-control" name="title" value="{{ old('title') }}" required>
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

            <div  class="col-md-12">
                <div class="form-group {{ $errors->has('remarks') ? ' has-danger' : '' }}">
                    <label for="leave-type">Remarks</label>
                    <textarea class="form-control" name="remarks" rows="5" required>{{ old('remarks') }}</textarea>
                                      
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
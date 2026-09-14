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
                        <div class="form-group {{ $errors->has('designationName') ? ' has-danger' : '' }}">
                            <label for="designation-name">Designation Name</label>
                            <input type="text" class="form-control" name="designationName" placeholder="Designation" value="{{ old('designationName') }}">
                            @if ($errors->has('designationName'))
                                @foreach($errors->get('designationName') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group {{ $errors->has('officialTitle') ? ' has-danger' : '' }}">
                            <label for="officila-title">Official Title</label>
                            <input type="text" class="form-control" name="officialTitle" placeholder="Official Title" value="{{ old('officialTitle') }}">
                            @if ($errors->has('officialTitle'))
                                @foreach($errors->get('officialTitle') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group {{ $errors->has('description') ? ' has-danger' : '' }}">
                    <label for="description">Description</label>
                    <textarea class="form-control" name="description" rows="5" placeholder="Description">{{ old('description') }}</textarea>
                    @if ($errors->has('description'))
                        @foreach($errors->get('description') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
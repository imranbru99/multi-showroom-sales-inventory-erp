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
                <div class="form-group {{ $errors->has('leaveName') ? ' has-danger' : '' }}">
                    <label for="leave-name">Leave Name</label>
                    <input type="text" class="form-control" name="leaveName" value="{{ old('leaveName') }}" required>
                    @if ($errors->has('leaveName'))
                        @foreach($errors->get('leaveName') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

           

            <div class="col-md-4">
                <div class="form-group {{ $errors->has('numberOfDay') ? ' has-danger' : '' }}">
                    <label for="number-of-days">Yearlly Allocation</label>
                    <input type="number" class="form-control" name="numberOfDay" value="{{ old('numberOfDay') }}" required>
                    @if ($errors->has('numberOfDay'))
                        @foreach($errors->get('numberOfDay') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@extends('admin.layouts.masterAddEdit')

@section('card_body')
    <style type="text/css">
        .chosen-single{
            height: 35px !important;
        }
    </style>

    <div class="card-body">
        <input type="hidden" name="showroomProjectId" value="{{ $ShowroomProject->id }}">

        <div class="row">
            <div class="col-md-6">
                <div class="form-group {{ $errors->has('code') ? ' has-danger' : '' }}">
                    <label for="code-prefix">Code/Prefix</label>
                    <input type="text" class="form-control" name="code" value="{{ $ShowroomProject->code }}" required>
                    @if ($errors->has('code'))
                        @foreach($errors->get('code') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group {{ $errors->has('showroomProjectName') ? ' has-danger' : '' }}">
                    <label for="showroomProject-name">Project Name</label>
                    <input type="text" class="form-control" name="showroomProjectName" value="{{ $ShowroomProject->name }}" required>
                    @if ($errors->has('showroomProjectName'))
                        @foreach($errors->get('showroomProjectName') as $error)
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
                    <textarea class="form-control" name="address"rows="5">{{ $ShowroomProject->address }}</textarea>
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
                    <textarea class="form-control" name="remarks"rows="5">{{ $ShowroomProject->remarks }}</textarea>
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
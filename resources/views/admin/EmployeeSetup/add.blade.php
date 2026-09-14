@extends('admin.layouts.masterAddEdit')

@section('card_body')
    <style type="text/css">
        .chosen-single{
            height: 35px !important;
        }
    </style>

    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group {{ $errors->has('employeeNo') ? ' has-danger' : '' }}">
                    <label for="employee-no">Employee No.</label>
                    <input type="text" class="form-control" name="employeeNo" value="{{ old('employeeNo') }}" required>
                    @if ($errors->has('employeeNo'))
                        @foreach($errors->get('employeeNo') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-3 form-group">
                <label for="joining-date">Joining Date</label>
                <input  type="text" class="form-control datepicker" id="joiningDate" name="joiningDate" value="" placeholder="Joining Date" readonly>
            </div>

            <div class="col-md-6">
                <div class="form-group {{ $errors->has('employeeName') ? ' has-danger' : '' }}">
                    <label for="employee-name">Employee Name</label>
                    <input type="text" class="form-control" name="employeeName" value="{{ old('employeeName') }}" required>
                    @if ($errors->has('employeeName'))
                        @foreach($errors->get('employeeName') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group {{ $errors->has('designation') ? ' has-danger' : '' }}">
                    <label for="designation">Designation</label>
                    <select class="form-control" name="designation">
                        <option value="">Select Leave Type</option>
                        @foreach ($designations as $designation)
                            <option value="{{ $designation->id }}">{{ $designation->name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('designation'))
                        @foreach($errors->get('designation') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group {{ $errors->has('image') ? ' has-danger' : '' }}">
                    <label for="image">Image</label> <span style="color: red; font-weight: bold;">( Width : 300px, Height : 300px )</span>
                    <input type="file" class="form-control" name="image" value="{{ old('image') }}">
                    @if ($errors->has('image'))
                        @foreach($errors->get('image') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group {{ $errors->has('mobile') ? ' has-danger' : '' }}">
                    <label for="mobile-no">Mobile No</label>
                    <input type="number" class="form-control" name="mobile" placeholder="Mobile Number" value="{{ old('mobile') }}" required>
                    @if ($errors->has('mobile'))
                        @foreach($errors->get('mobile') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group {{ $errors->has('email') ? ' has-danger' : '' }}">
                    <label for="email">Email</label>
                    <input type="text" class="form-control" name="email" placeholder="Email" value="{{ old('email') }}" required>
                    @if ($errors->has('email'))
                        @foreach($errors->get('email') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
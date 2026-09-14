@extends('admin.layouts.masterAddEdit')

@section('card_body')
    <style type="text/css">
        .chosen-single {
            height: 35px !important;
        }

    </style>
    <input type="hidden" name="id" value="{{ $commission->id }}">
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <div class="form-group {{ $errors->has('date') ? ' has-danger' : '' }}">
                    <label for="code-prefix">Employee Name</label>
                    <select class="form-control chosen-select" name="employee_id" required>
                        <option value="">Select Employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" @if($employee->id == $commission->employee_id) selected @endif>{{ $employee->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group {{ $errors->has('commission') ? ' has-danger' : '' }}">
                    <label for="code-prefix">Commission</label>
                    <input type="text" class="form-control" name="commission" value="{{ $commission->commission }}" required>
                    @if ($errors->has('commission'))
                        @foreach ($errors->get('commission') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

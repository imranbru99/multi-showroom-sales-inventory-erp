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
                <input class="form-control" type="hidden" name="employeeId" value="{{ $employee->id }}">
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="form-group {{ $errors->has('employeeNo') ? ' has-danger' : '' }}">
                    <label for="employee-no">Employee No.</label>
                    <input type="text" class="form-control" name="employeeNo" value="{{ $employee->employee_no }}" required>
                    @if ($errors->has('employeeNo'))
                        @foreach($errors->get('employeeNo') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-3 form-group">
                <label for="joining-date">Joining Date</label>
                <input  type="text" class="form-control datepicker" id="joiningDate" name="joiningDate" value="{{ $employee->joining_date }}" placeholder="Joining Date" readonly>
            </div>

            <div class="col-md-6">
                <div class="form-group {{ $errors->has('employeeName') ? ' has-danger' : '' }}">
                    <label for="employee-name">Employee Name</label>
                    <input type="text" class="form-control" name="employeeName" value="{{ $employee->name }}" required>
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
                        @php
                            if ($designation->id == $employee->designation_id)
                            {
                                $select = "selected";
                            }
                            else
                            {
                                $select = "";
                            }                            
                        @endphp
                            <option value="{{ $designation->id }}" {{ $select }}>{{ $designation->name }}</option>
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
                    <input type="hidden" class="form-control" name="previousImage" value="{{ $employee->image }}">
                    <img src="{{ asset($employee->image) }}" style="padding: 10px 10px 0px 0px; width: 100px; height: 100px;">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="form-group {{ $errors->has('mobile') ? ' has-danger' : '' }}">
                    <label for="mobile-no">Mobile No</label>
                    <input type="number" class="form-control" name="mobile" placeholder="Mobile Number" value="{{ $employee->mobile }}" required>
                    @if ($errors->has('mobile'))
                        @foreach($errors->get('mobile') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group {{ $errors->has('email') ? ' has-danger' : '' }}">
                    <label for="email">Email</label>
                    <input type="text" class="form-control" name="email" placeholder="Email" value="{{ $employee->email }}" required>
                    @if ($errors->has('email'))
                        @foreach($errors->get('email') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <label for="mobile-no">Resigned</label>
                 <div class="form-group {{ $errors->has('resigned') ? ' has-danger' : '' }}">
                    <div class="form-check-inline">
                        <label class="form-check-label">
                            <input type="radio" value="1" name="resigned" class="resigned" {{ $employee->resigned == 1 ? 'checked' : '' }}> Yes
                        </label>
                    </div>

                    <div class="form-check-inline">
                        <label class="form-check-label">
                            <input type="radio" value="0" name="resigned" class="resigned" {{ $employee->resigned == 0 ? 'checked' : '' }}> No
                        </label>
                    </div>
                </div>
            </div>

            <div class="col-md-3 form-group resigningDate">
                <label for="resigning-date">Resigning Date</label>
                <input  type="text" class="form-control datepicker" id="resigningDate" name="resigningDate" value="{{ $employee->resigning_date }}" placeholder="Joining Date" readonly>
            </div>
        </div>

        <div class="row resigningReason">
            <div class="col-md-12">
                <div class="form-group {{ $errors->has('resigningReason') ? ' has-danger' : '' }}">
                    <label for="reasigning-reason">Reasigning Reason</label>
                    <textarea class="form-control" id="resigningReason" name="resigningReason" placeholder="Resigning Reason" rows="3">{{ $employee->resigning_reason }}</textarea>
                    @if ($errors->has('resigningReason'))
                        @foreach($errors->get('resigningReason') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom-js')
    <script type="text/javascript">
        var resigned =  $("input[name='resigned']:checked").val();

        if(resigned == "1")
        {
            $('.resigningDate').show();
            $('.resigningReason').show();
        }

        if(resigned == "0")
        {
            $('.resigningDate').hide();
            $('.resigningReason').hide();
        }

        $('.resigned').click(function(event) {
            var resigned =  $("input[name='resigned']:checked").val();

            if(resigned == "1")
            {
                $('.resigningDate').show();
                $('.resigningReason').show();
            }

            if(resigned == "0")
            {
                $('#resigningDate').val("");
                $('#resigningReason').val("");
                $('.resigningDate').hide();
                $('.resigningReason').hide();
            }
        })
    </script>
@endsection
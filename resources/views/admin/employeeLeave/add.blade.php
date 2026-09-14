@extends('admin.layouts.masterAddEdit')

@section('card_body')
    <style type="text/css">
        .chosen-single{
            height: 35px !important;
        }
    </style>

    <div class="card-body">
        <div class="row">
            <div class="col-md-5">
                <div class="form-group {{ $errors->has('employeeId') ? ' has-danger' : '' }}">
                    <label for="employee-id">Employee</label>
                    <select class="form-control chosen-select employee" name="employeeId">
                        <option value="">Select Employee</option>
                        @foreach ($employies as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }} ({{ $employee->employee_no }})</option>
                        @endforeach
                    </select>
                    @if ($errors->has('employeeId'))
                        @foreach($errors->get('employeeId') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group {{ $errors->has('leaveId') ? ' has-danger' : '' }}">
                    <label for="leave-name">Leave Name</label>
                    <select class="form-control chosen-select leave" name="leaveId">
                        <option value="">Select Leave Name</option>
                        @foreach ($leaves as $leave)
                            <option value="{{ $leave->id }}">{{ $leave->name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('leaveId'))
                        @foreach($errors->get('leaveId') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-2 form-group">
                <label for="from-date">From Date</label>
                <input  type="text" class="form-control datepicker" id="fromDate" name="fromDate" value="" placeholder="Select From Date" onchange="totalDays()" readonly>
            </div>

            <div class="col-md-2 form-group">
                <label for="to-date">To Date</label>
                <input  type="text" class="form-control datepicker" id="toDate" name="toDate" value="" placeholder="Select To Date" onchange="totalDays()" readonly>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('previousLeaveDays') ? ' has-danger' : '' }}">
                            <label for="previous-leave-days">Previous Leave Days</label>
                            <input type="number" class="form-control" id="previousLeaveDays" name="previousLeaveDays" placeholder="Previous Leave Days" value="{{ old('previousLeaveDays') }}" readonly>
                            @if ($errors->has('previousLeaveDays'))
                                @foreach($errors->get('previousLeaveDays') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('leaveDays') ? ' has-danger' : '' }}">
                            <label for="leave-days">Leave Days</label>
                            <input type="number" class="form-control" id="leaveDays" name="leaveDays" placeholder="Leave Days" value="{{ old('leaveDays') }}" readonly>
                            @if ($errors->has('leaveDays'))
                                @foreach($errors->get('leaveDays') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('observedDays') ? ' has-danger' : '' }}">
                            <label for="observed-days">Total Observed Leave Days</label>
                            <input type="number" class="form-control" id="observedDays" name="observedDays" placeholder="Total Observed Leave Days" value="{{ old('observedDays') }}" readonly>
                            @if ($errors->has('observedDays'))
                                @foreach($errors->get('observedDays') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('remainingDays') ? ' has-danger' : '' }}">
                            <label for="remaining-days">Total Remaining Leave Days</label>
                            <input type="number" class="form-control" id="remainingDays" name="remainingDays" placeholder="Total Remaining Leave Days" value="{{ old('remainingDays') }}" readonly>
                            @if ($errors->has('remainingDays'))
                                @foreach($errors->get('remainingDays') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group {{ $errors->has('remarks') ? ' has-danger' : '' }}">
                    <label for="remarks">Remarks</label>
                    <textarea class="form-control" name="remarks" placeholder="Remarks" rows="5">{{ old('remarks') }}</textarea>
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

@section('custom-js')
    <script>
        $(document).on('change', '.employee', function(){
            $('.leave').val("").trigger('chosen:updated');

            $('#previousLeaveDays').val(0);
            $('#observedDays').val(0);
            $('#leaveDays').val(Math.round(0));
            $('#remainingDays').val(Math.round(0));
        });

        $(document).on('change', '.leave', function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            var employeeId = $('.employee option:selected').val();
            var leaveId = $('.leave option:selected').val();
            var previousLeaveDays;
            var year = new Date().getFullYear();
            // var year = 2019;

            $.ajax({
                type:'post',
                url:'{{ route('employeeLeave.getLeaveInfo') }}',
                data:{employeeId:employeeId,leaveId:leaveId,year:year},
                success:function(data){
                    var leave = data.leave;
                    var employeeLeave = data.employeeLeave;

                    if (employeeLeave.totalDays == null)
                    {
                        previousLeaveDays = 0;
                    }
                    else
                    {
                        previousLeaveDays = employeeLeave.totalDays;
                    }

                    $('#previousLeaveDays').val(previousLeaveDays);
                    $('#observedDays').val(leave.days);
                }
            });
        });

        function totalDays()
        {
            var fromDate = $('#fromDate').datepicker('getDate');
            var toDate = $('#toDate').datepicker('getDate');
            var observedDay = parseInt($('#observedDays').val());
            var previousLeaveDays = parseInt($('#previousLeaveDays').val());

            if (fromDate != null && toDate != null)
            {
                if (fromDate > toDate)
                {
                    swal("From Date Can't Be Exceed To Date", "", "warning");
                    fromDate = $.datepicker.formatDate("dd-mm-yy", fromDate);
                    $('#toDate').val(fromDate);
                    $('#fromDate').val("");
                }
                else
                {
                    if (fromDate.getFullYear() != toDate.getFullYear())
                    {
                        swal("Pleas Select Date From Curren Year", "", "warning");
                        lastDateOfCurrentYear = $.datepicker.formatDate("dd-mm-yy", new Date(new Date().getFullYear(), 11, 31));
                        $('#toDate').val(lastDateOfCurrentYear);
                    }
                    else
                    {
                        var totalDays = ((toDate - fromDate)/(1000 * 60 * 60 * 24)) + 1;
                        var remainingDay = observedDay - (totalDays + previousLeaveDays);

                        // alert(previousLeaveDays);

                        if (remainingDay < 0)
                        {
                            swal("Employee Don't Have Sufficient Leave Days", "", "warning");
                        }
                        else
                        {
                            $('#leaveDays').val(Math.round(totalDays));
                            $('#remainingDays').val(Math.round(remainingDay));
                        }
                    }
                }
            }            
        }
    </script>
@endsection
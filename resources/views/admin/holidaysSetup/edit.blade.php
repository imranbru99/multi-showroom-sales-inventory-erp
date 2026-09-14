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
                <input class="form-control" type="hidden" name="holidayId" value="{{ $holiday->id }}">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('holidayName') ? ' has-danger' : '' }}">
                            <label for="holiday-name">Holiday Name</label>
                            <input type="text" class="form-control" name="holidayName" placeholder="Holiday Name" value="{{ $holiday->name }}">
                            @if ($errors->has('holidayName'))
                                @foreach($errors->get('holidayName') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('holidayType') ? ' has-danger' : '' }}">
                            <label for="holiday-type">Holiday Type</label>
                            <select class="form-control" name="holidayType">
                                <option value="">Select Holiday Type</option>
                                @foreach ($holidayTypes as $key => $value)
                                    @php
                                        if ($key == $holiday->type)
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
                            @if ($errors->has('holidayType'))
                                @foreach($errors->get('holidayType') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

           

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="from-date">From Date</label>
                        <input  type="text" class="form-control datepicker" id="fromDate" name="fromDate" value="{{ $holiday->from_date == '' ? '' : date('d-m-Y', strtotime($holiday->from_date)) }}" placeholder="Select Date From" {{ $holiday->holiday_for == "One Day" ? 'disabled' : '' }} readonly>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="to-date">To Date </label>
                        <input  type="text" class="form-control datepicker" id="toDate" name="toDate" value="{{ $holiday->to_date == '' ? '' : date('d-m-Y', strtotime($holiday->to_date)) }}" placeholder="Select Date To" onchange="totalDays()" readonly>
                    </div>
                   
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom-js')
    <script>
        $(document).on('change', '#holidayFor', function(){
            var holidayFor = $('#holidayFor').val();

            if (holidayFor == 'One Day')
            {
                $('#toDate').prop('disabled',false);
                $('#fromDate').val('');


                // $('#numberOfDay').val(1);
            }
            else
            {
                $('#fromDate').prop('disabled',false);
                $('#toDate').prop('disabled',false);
            }
        });

        function totalDays()
        {
            var holidayFor = $('#holidayFor').val();

            if (holidayFor == "One Day")
            {
                $('#numberOfDay').val(1);
            }
            else
            {
                var fromDate = $('#fromDate').datepicker('getDate');
                var toDate = $('#toDate').datepicker('getDate');

                var totalDays = ((toDate - fromDate)/(1000 * 60 * 60 * 24)) + 1;
                
                $('#numberOfDay').val(Math.round(totalDays));
            }
        }
    </script>
@endsection
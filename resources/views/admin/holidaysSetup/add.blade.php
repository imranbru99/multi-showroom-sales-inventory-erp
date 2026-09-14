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
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('holidayName') ? ' has-danger' : '' }}">
                            <label for="holiday-name">Holiday Name</label>
                            <input type="text" class="form-control" name="holidayName" placeholder="Holiday Name" value="{{ old('holidayName') }}">
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
                                    <option value="{{ $key }}">{{ $value }}</option>
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
                        <label for="from-date">From Date </label>
                        <input  type="text" class="form-control" id="from_date" name="fromDate" value="" placeholder="Select Date From" readonly>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="to-date">To Date </label>
                        <input  type="text" class="form-control" id="to_date" name="toDate" value="" placeholder="Select Date To" onchange="totalDays()" readonly>
                    </div>
                   
                </div>
            </div>

           
        </div>
    </div>
@endsection

@section('custom-js')
    <script>
        $(document).ready(function() {
            $('#from_date').prop('',true);
            $('#to_date').prop('',true);
        });

        $(document).on('change', '#holidayFor', function(){
            var holidayFor = $('#holidayFor').val();

            if (holidayFor == 'One Day')
            {
                $('#to_date').prop('disabled',false);
                $('#from_date').val('');


                // $('#numberOfDay').val(1);
            }
            else
            {
                $('#from_date').prop('disabled',false);
                $('#to_date').prop('disabled',false);
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
                var fromDate = $('#from_date').datepicker('getDate');
                var toDate = $('#to_date').datepicker('getDate');

                var totalDays = ((toDate - fromDate)/(1000 * 60 * 60 * 24)) + 1;
                
                $('#numberOfDay').val(Math.round(totalDays));
            }
        }
    </script>
@endsection
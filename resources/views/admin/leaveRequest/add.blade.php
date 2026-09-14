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
            <div class="form-group {{ $errors->has('employee_id') ? ' has-danger' : '' }}">
                <label for="employee-name">Employee Name</label>
                <select name="employee_id" class="form-control chosen-select employee_id">
                    <option value="">Select Employee</option>
                    @foreach ($staffs as $staff)
                    <option value="{{ $staff->id }}">{{ $staff->name }} -- ({{ $staff->code }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group {{ $errors->has('leaveType') ? ' has-danger' : '' }}">
                <label for="leave-type">Leave Type</label>
                <select class="form-control chosen-select leaveType" name="leaveType">
                    <option value="">Select Leave Type</option>
                    @foreach ($leaveTypes as $leaveType)
                    <option value="{{ $leaveType->id }}">{{ $leaveType->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group {{ $errors->has('available_leave') ? ' has-danger' : '' }}">
                <label for="available_leave">Available Leave</label>
                <input type="number" name="available_leave" class="form-control available_leave" value="">
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label for="from-date">Leave From</label>
                <input type="text" class="form-control datepicker leave_from" id="leave_from" name="leave_from" value="{{ date('d-m-Y') }}" placeholder="Select Date From">
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label for="from-date">Leave To</label>
                <input type="text" class="form-control datepicker leave_to" id="leave_to" name="leave_to" value="{{ date('d-m-Y') }}" placeholder="Select Date From">
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label for="from-date">Leave Day</label>
                <input type="text" class="form-control leave_day" id="leave_day" name="leave_day" value="" placeholder="Leave Duration">
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label for="from-date">Remarks</label>
                <textarea class="form-control" id="remarks" name="remarks" rows="5"></textarea>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom-js')
<script>

    $(".datepicker").datepicker({
        format: 'dd-mm-yyyy',
        changeMonth: true,
        changeYear: true,
        orientation: "bottom left"
    });
    $(document).on('change', '.leaveType', function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var leave_type = $('.leaveType').val();
        var employee_id = $('.employee_id').val();
        if (employee_id == '') {
            swal({
                title: "Please Select Employee"
            });
            return;
        }

        console.log(leave_type);
        $.ajax({
            type: 'post',
            url: '{{ route('leaveRequest.leaveInfo') }}',
            data: {
                leave_type: leave_type,
                employee_id: employee_id,
            }
            ,
            success: function (response) {
                $('.available_leave').val(response);
            }
        });
    });

    $('.datepicker').change(function () {
        var start = $(".leave_from").datepicker("getDate");
        var end = $(".leave_to").datepicker("getDate");
        days = (end - start) / (1000 * 60 * 60 * 24);

        $('.leave_day').val(Math.round(days + 1));
    });
</script>

@endsection
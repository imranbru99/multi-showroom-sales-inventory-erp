@extends('admin.layouts.masterAddEdit')

@section('card_body')
<style type="text/css">
    .chosen-single{
        height: 35px !important;
    }
</style>

<div class="card-body">
    <div class="row">
        <div class="col-md-3 form-group">
            <label for="date">Date</label>
            <input  type="text" class="form-control datepicker" id="date" name="date" value="{{ date('d-m-Y') }}" placeholder="Select Date" readonly>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <table id="dataTable" width="100%" class="table-hovered manualAttendence">
            <thead>
                <tr>
                    <th width="20px">SL</th>
                    <th>Employee Name</th>
                    <th>In Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr id="row_1">
                    <td></td>
                    <td>
                        <div class="form-group {{ $errors->has('employeeId') ? ' has-danger' : '' }}">
                            <select class="form-control chosen-select employee_id_1" name="employeeId[]">
                                <option value="">Select Employee</option>
                                @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }} ({{ $employee->code }})</option>
                                @endforeach
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="form-group {{ $errors->has('hour') ? ' has-danger' : '' }}">
                            <input type="time" name="in_time[]" id="in_time" class="form-control timepicker" placeholder="Selected time">
                        </div>
                    </td>
                    <td>
                        <div class="form-group" id="add_remove_1">
                            <input type="hidden" class="row_count" value="1">
                            <span class="btn btn-outline-success add_item" style="width: 100%;" onclick="addRow(1)">
                                <i class="fa fa-arrow-down"></i> Add
                            </span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('custom-js')
<script>
    function itemRemove(i) {

        $("#row_" + i).remove();
    }

    function addRow(i) {
//        console.log(i);
//    $(".add_item").click(function () {

//        var row_count = $('.row_count').val();
//        var total = parseInt(row_count) + 1;
//
//        var rowCount = $('#manualAttendence tbody tr').length;
//
//        var no_row = rowCount + 1;
        var total = parseInt(i) + 1;

        $('.add_item').remove();
        $('#add_remove_' + i).append(
                `
                <span class="btn btn-outline-danger" style="width: 100%;" onclick="itemRemove(${i})">
                    <i class="fa fa-trash"></i> Remove
                </span>
            `
                );

        $(".manualAttendence tbody").append(
                `
                <tr id="row_${total}">
                    <td>${total}</td>
                    <td>
                        <div class="form-group {{ $errors->has('employeeId') ? ' has-danger' : '' }}">
                            <select class="form-control chosen-select employee_id_${total}" name="employeeId[]">
                                <option value="">Select Employee</option>
                                @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }} ({{ $employee->code }})</option>
                                @endforeach
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="form-group {{ $errors->has('in_time') ? ' has-danger' : '' }}">
                            <input type="time" name="in_time[]" id="in_time" class="form-control in_time${total}" placeholder="Selected time">
                        </div>
                    </td>
                    <td>
                        <div class="form-group" id="add_remove_${total}">
                            <input type="hidden" class="row_count" value="${total}">
                            <span class="btn btn-outline-success add_item" style="width: 100%;" onclick="addRow(${total})">
                                <i class="fa fa-arrow-down"></i> Add
                            </span>
                        </div>
                    </td>
                </tr>

            `

                );

        $('.chosen-select').chosen();
        $('.chosen-select').trigger("chosen:updated");
//    });
    }
</script>
@endsection
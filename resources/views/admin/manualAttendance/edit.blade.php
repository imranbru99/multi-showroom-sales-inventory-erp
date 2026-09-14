@extends('admin.layouts.masterAddEdit')

@section('card_body')
<style type="text/css">
    .chosen-single {
        height: 35px !important;
    }

</style>

<div class="card-body">
    <div class="row d-flex justify-content-center">
        <div class="col-md-4">
            <label for="transfer-date">Attendence Date</label>
            <div class="form-group">
                <input type="text" name="date" class="form-control datepicker" value="{{ date('d-m-Y', strtotime($attendEmployees->date)) }}">
                <input type="hidden" name="id" class="form-control" value="{{ $attendEmployees->id }}">
            </div>
        </div>
    </div>
</div>

<div class="card-body">
    <div class="row">
        <div class="col-md-6 d-flex justify-content-between">
            <h4>Employees</h4>
            <span class="btn btn-outline-info" onclick="AddAll()">Add</span>
        </div>
        <div class="col-md-6">
            <h4>Attendence Employee</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <label for=""></label>
            <div class="form-group">
                <table class="table table-striped gridTable Employees">
                    <thead>
                        <tr>
                            <th class="text-center">Employee Name</th>
                            <th class="text-center" width="100px">Code</th>
                            <th class="text-center" width="100px">Time</th>
                            <th class="text-center" width="70px">
                                <input type="checkbox" class="form_control" id="select_all"
                                       onclick="return SelectAll()">
                            </th>
                        </tr>
                    </thead>

                    <tbody id="tbody">
                        @foreach ($employees as $employee)
                        <tr class="employeeList" id="employeeListRow_{{ $employee->id }}">
                            <td>
                                <input class="form-control employee_id_{{ $employee->id }}" type="hidden"
                                       value="{{ $employee->id }}">
                                <input class="form-control employee_name_{{ $employee->id }}" type="text"
                                       value="{{ $employee->name }}" readonly>
                            </td>
                            <td>
                                <input class="form-control employee_code_{{ $employee->id }}" type="text"
                                       value="{{ $employee->code }}" readonly>
                            </td>
                            <td>
                                <input class="form-control employee_time_{{ $employee->id }}"
                                       type="time" value="">
                            </td>
                            <td align="center">
                                <input type="checkbox" class="form_control checkbox" value="{{ $employee->id }}"
                                       id="tr_rmployee_{{ $employee->id }}">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-6">
            <label for=""></label>
            <div class="form-group">
                <table class="table table-striped gridTable Attendence">
                    <thead>
                        <tr>
                            <th class="text-center">Employee Name</th>
                            <th class="text-center" width="100px">Code</th>
                            <th class="text-center" width="100px">Time</th>
                            <th class="text-center" width="70px">Action</th>
                        </tr>
                    </thead>

                    <tbody id="tbody">
                        @foreach ($attendEmployees->attendenceList as $key => $attendEmployee)
                        @if(!empty($attendEmployee->staff))
                        <tr class="attendenceList" id="attendenceListRow_{{ $attendEmployee->id }}">
                            <td>
                                <input class="form-control employee_id_{{ $attendEmployee->id }}" type="hidden"
                                       name="employeeId[]" value="{{ $attendEmployee->employee_id }}">
                                <input class="form-control employee_name_{{ $attendEmployee->id }}" type="text"
                                       value="{{ @$attendEmployee->staff->name }}" readonly>
                            </td>
                            <td>
                                <input class="form-control employee_code_{{ $attendEmployee->id }}" type="text"
                                       value="{{ @$attendEmployee->staff->code }}" readonly>
                            </td>
                            <td>
                                <input class="form-control employee_time_{{ $attendEmployee->id }}"
                                       name="in_time[]"  type="time" value="{{ $attendEmployee->in_time }}">
                            </td>
                            <td align="center">
                                <span class="btn btn-danger item_remove" onclick="AttendenceRemove({{ $attendEmployee->id }})">Remove</span>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom-js')
<script type="text/javascript">
    function addToAttendence(employeeId) {
        var employeeName = $('.employee_name_' + employeeId).val();
        var employeeCode = $('.employee_code_' + employeeId).val();
        var employeeTime = $('.employee_time_' + employeeId).val();
        $(".Attendence tbody").append(
                `
                        <tr class="attendenceList" id="attendenceListRow_${employeeId}">
                            <td>
                                <input class="form-control employee_id_${employeeId}" type="hidden"
                                      name="employeeId[]" value="${employeeId}">
                                <input class="form-control employee_name_${employeeId}" type="text"
                                     value="${employeeName}" readonly>
                            </td>
                            <td>
                                <input class="form-control employee_code_${employeeId}" type="text"
                                      value="${employeeCode}" readonly>
                            </td>
                            <td>
                                <input class="form-control employee_time_${employeeId}"
                                     name="in_time[]"  type="time" value="${employeeTime}">
                            </td>
                            <td align="center">
                                <span class="btn btn-danger item_remove" onclick="AttendenceRemove(${employeeId})">Remove</span>
                            </td>
                        </tr>
                `


                );
//        var totalQty = parseInt($('#totalQty').val());
//        totalQty = totalQty + parseInt(qty);
//        $('#totalQty').val(totalQty);
        $('#employeeListRow_' + employeeId).remove();
    }

    function AttendenceRemove(employeeId) {
        var employeeName = $('.employee_name_' + employeeId).val();
        var employeeCode = $('.employee_code_' + employeeId).val();
        $(".Employees tbody").append(
                `
                <tr class="employeeList" id="employeeListRow_${employeeId}">
                            <td>
                                <input class="form-control employee_id_${employeeId}" type="hidden" value="${employeeId}">
                                <input class="form-control employee_name_${employeeId}" type="text"
                                       value="${employeeName}" readonly>
                            </td>
                            <td>
                                <input class="form-control employee_code_${employeeId}" type="text"
                                      value="${employeeCode}" readonly>
                            </td>
                            <td>
                                <input class="form-control employee_time_${employeeId}"
                                       type="time" value="">
                            </td>
                            <td align="center">
                                <input type="checkbox" class="form_control checkbox" value="${employeeId}" id="tr_employee_${employeeId}"></td>
                        </tr>
                `
                );
        $('#attendenceListRow_' + employeeId).remove();
    }
</script>

<script>
    function SelectAll() {

        $("#select_all").change(function () { //"select all" change 
            var status = this.checked; // "select all" checked status
            $('.checkbox').each(function () { //iterate all listed checkbox items
                this.checked = status; //change ".checkbox" checked status
                // $(".checkbox").attr('checked', true);
            });
        });
        $('.checkbox').change(function () { //".checkbox" change 
            //uncheck "select all", if one of the listed checkbox item is unchecked
            if (this.checked == false) { //if this item is unchecked
                $("#select_all")[0].checked = false; //change "select all" checked status to false

            }

            //check "select all" if all checkbox items are checked
            if ($('.checkbox:checked').length == $('.checkbox').length) {
                $("#select_all")[0].checked = true; //change "select all" checked status to true
            }
        });
    }

    function AddAll() {
        $(".Employees tbody tr .checkbox").each(function () {
            if (this.checked == true) {
                var id = this.value;

                var time = $('.employee_time_' + id).val();
                if (time == '') {
                    swal({
                        title: 'Select Time'
                    });
                    return;
                }
                addToAttendence(id);
            }
        });
    }
</script>
@endsection
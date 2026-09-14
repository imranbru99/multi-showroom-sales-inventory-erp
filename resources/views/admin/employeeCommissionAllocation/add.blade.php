@extends('admin.layouts.masterAddEdit')

@section('custom_css')
<style type="text/css">
    #total-target {
        vertical-align: middle;
        text-align: center;
        font-weight: bold;
        font-size: 16px;
    }

</style>
@endsection

@section('card_body')
<div class="card-body">
    <div class="row d-flex justify-content-center">
        <div class="col-md-3 form-group">
            <label for="month">Month</label>
            <input type="month" class="form-control" name="month" value="{{ date('Y-m') }}">
        </div>
    </div>
    <div class="row d-flex justify-content-center mt-5">
        <div class="col-md-6 row">
            <div class="col-md-9">
                <div class="form-group">
                    <label for="staff">Staff</label>
                    <select class="form-control chosen-select" id="staff">
                        <option value="">Select Employee</option>
                        @foreach($staffs as $staff)
                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mt-3">
                    <a href="javascript:;" onclick="addRow()" class="btn btn-outline-info">
                        <i class="fa fa-plus m-0"></i> Add
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="staff_add">
            <thead>
                <tr class="text-center bg-success">
                    <th>EmployeeName</th>
                    {{-- <th>DealerCollection Target</th> --}}
                    <th>DealerCollection Commission</th>
                    {{-- <th>Retail CashCollection Target</th> --}}
                    <th>Retail CashCollection Commission</th>
                    {{-- <th>Retail HireCollection Target</th> --}}
                    <th>Retail HireCollection Commission</th>
                    {{-- <th>Recovery CashCollection Target</th> --}}
                    <th>Recovery CashCollection Commission</th>
                    {{-- <th>Recovery HireCollection Target</th> --}}
                    <th>Recovery HireCollection Commission</th>
                    {{-- <th>MSPCHTarget</th> --}}
                    <th>MSP Commission</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>
@endsection

@section('custom-js')
<script>
    function addRow() {
        var max = 0;
        $('.staff').each(function() {
            max = Math.max(this.id, max);
        });

        let next_row = max + 1;

        var staff = $('#staff').val();

        if (staff == '') {
            swal({
                title: "Select An Employee"
                , type: "warning",
                // timer: 1000,
            });
            return true;
        }
        if ($('.staff_' + staff).val() == staff) {
            swal({
                title: "Employee Already Exist on the list"
                , type: "warning",
                // timer: 1000,
            });
            return true;
        }



        $.ajax({
            type: "get"
            , url: "{{ route('employeecommissionallocation.staffInfo') }}"
            , data: {
                staff: staff
            },

            success: function(response) {
                let tr = `
                <tr class="staffs text-center" id="${next_row}">
                    <td>
                        <input type="hidden" class="form-control staff_${staff}" name="staff[]" value="${staff}">
                        <input type="text" class="form-control" value="${response.name}">
                    </td>
                    <td><input type="text" class="form-control text-right" name="dealer_collection[]" value="0"></td>
                    <td><input type="text" class="form-control text-right" name="retail_cash_collection[]" value="0"></td>
                    <td><input type="text" class="form-control text-right" name="retail_hire_collection[]" value="0"></td>
                    <td><input type="text" class="form-control text-right" name="recovery_cash_collection[]" value="0"></td>
                    <td><input type="text" class="form-control text-right" name="recovery_hire_collection[]" value="0"></td>
                    <td><input type="text" class="form-control text-right" name="msp[]" value="0"></td>
                    <td>
                        <a href="javascript:;" onclick="removeRow(${next_row})" class="btn btn-outline-danger">
                            <i class="fa fa-trash m-0"></i>
                        </a>
                    </td>
                </tr>
        `;

                $('#staff_add').append(tr);
            }
        });


    }

    function removeRow(id) {
        $('#' + id).remove();
    }

</script>
@endsection

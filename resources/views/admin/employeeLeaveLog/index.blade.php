
@extends('admin.layouts.masterReport')

@section('search_card_body')
<input type="hidden" name="print" value="print">
<div class="row d-flex justify-content-center">
    <div class="col-md-4">
        <label for="staff">Employee Name</label>
        <div class="form-group">
            <select class="form-control chosen-select" id="staff" name="staff">

                <option value="">Select Employee</option>
                @foreach ($staffs as $staff)
                <option value="{{ $staff->id }}" @if(@$staffId == $staff->id) selected @endif>{{ $staff->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

</div>
@endsection

@section('print_card_header')

<input type="hidden" id="store" name="staff" value="{{ @$staffId }}">

<input type="hidden" id="print_value" name="print" value="{{ @$print }}">
@endsection

@section('print_card_body')
<table id="dataTable" name="productList" class="table table-bordered table-sm">
    <thead>
        <tr>
            <th width="20px">Sl</th>
            <th>Employee Name</th>
            <th>Leave Type</th>
            <th>Leave Details</th>
        </tr>
    </thead>

    <tbody>
        @php 
        $grand_total = 0;  
        @endphp

        @foreach($leaves as $leave)

        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>@if($loop->first) {{ $leave->staff->name }} @endif</td>
            <td>{{ $leave->leaveType->name }} ({{ $leave->leaveType->days }})</td>
            <td>
                <table width="100%" id="leaveDetails">
                    <thead>
                        <tr class="bg-success">
                            <th>Leave From</th>
                            <th>Leave To</th>
                            <th>Leave Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_leaves = 0;
                        $leaves_types = App\LeaveRequest::where('leave_type', $leave->leave_type)
                                ->where('employee_id', $leave->employee_id)
                                ->get();
                        ?>
                        @foreach($leaves_types as $leaves_type)
                        @php 
                        $total_leaves += $leaves_type->leave_day; 
                        @endphp
                        <tr>
                            <td>{{ $leaves_type->leave_from }}</td>
                            <td>{{ $leaves_type->leave_to }}</td>
                            <td>{{ $leaves_type->leave_day }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-secondary">
                            <td colspan="2" class="font-weight-bold">Total Leave</td>
                            <td>{{ $total_leaves }}</td>
                        </tr>
                    </tfoot>
                </table>
            </td>
        </tr>
        @php $grand_total += $total_leaves; @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr class="bg-secondary">
            <td colspan="3"></td>
            <td class="font-weight-bold text-center">Total Leaves : {{ $grand_total }}</td>
        </tr>
    </tfoot>
</table>
@endsection

@section('custom-js')
<script>


</script>

@endsection

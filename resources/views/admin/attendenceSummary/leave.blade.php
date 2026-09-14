@extends('admin.layouts.masterAddEditBlank')
@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h4>{{ $staff->name }} ({{ $staff->code }})</h4>
            </div>
            <div class="col-md-6">
                <form action="{{ route('attendenceSummary.leavePrint') }}" class="float-right" method="post" enctype="multipart/form-data" target="_blank">
                    {{ csrf_field() }}
                    <input type="hidden" name="month" value="{{ @$monthYear }}">
                    <input type="hidden" name="staff" value="{{ @$staff->id }}">
                    <button type="submit" class="btn btn-outline-info btn-lg waves-effect"><i class="fa fa-print"></i> Print</button>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body">
        <table id="dataTable" class="table table-bordered table-sm" width='100%'>
            <thead>
                <tr>
                    <th width='20px'>SL</th>
                    <th>Date</th>
                    <th>Leave From</th>
                    <th>Leave To</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthly_attendance as $key => $value)
                @php $sl=1 ; @endphp
                @foreach($value['monthly_staff'] as $key => $attendence)
                <?php
                if ($attendence['attendence'] == '') {
                    if ($attendence['leave'] != '') {
                        ?>

                        <tr>
                            <td>{{ $sl++ }}</td>
                            <td>{{ $attendence['date'] }}</td>
                            <td>{{ $attendence['leave']->leave_from_a }}</td>
                            <td>{{ $attendence['leave']->leave_to_a }}</td>
                            <td>{{ $attendence['leave']->leaveType->name }}</td>
                        </tr>

                        <?php
                    }
                }
                ?>

                @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<style>
    #dataTable{
        text-align: center;
    }
</style>
@endsectionÏÏ
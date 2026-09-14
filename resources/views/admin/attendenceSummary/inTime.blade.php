@extends('admin.layouts.masterAddEditBlank')
@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h4>{{ $staff->name }} ({{ $staff->code }})</h4>
            </div>
            <div class="col-md-6">
                <form action="{{ route('attendenceSummary.inTimePrint') }}" class="float-right" method="post" enctype="multipart/form-data" target="_blank">
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
                    <th>Attendence Time</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthly_attendance as $key => $value)
                @php $sl=1 ; @endphp
                @foreach($value['monthly_staff'] as $key => $attendence)
                <?php
                if ($attendence['attendence'] != '') {
                    if ($attendence['attendence']->in_time <= "09:30:00") {
                        ?>  
                        <tr>
                            <td>{{ $sl++ }}</td>
                            <td>{{ $attendence['attendence']->date }}</td>
                            <td>{{ date('h:i A', strtotime($attendence['attendence']->in_time)) }}</td>
                            <td>In Time</td>
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
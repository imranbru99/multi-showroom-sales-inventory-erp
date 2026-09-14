
@extends('admin.layouts.masterReport')

@section('search_card_body')
<input type="hidden" name="print" value="print">
<div class="row d-flex justify-content-center">
    <div class="col-md-4">
        <label for="date">Date</label>
        <div class="form-group">
            <input type="text" class="form-control datepicker" name="date" value="{{ !empty($date) ? date('d-m-Y', strtotime($date)) : date('d-m-Y') }}">
        </div>
    </div>

</div>
@endsection

@section('print_card_header')

<input type="hidden" id="date" name="date" value="{{ @$date }}">

<input type="hidden" id="print_value" name="print" value="{{ @$print }}">
@endsection

@section('print_card_body')
<table id="dataTable" name="productList" class="table table-bordered table-sm">
    <thead>
        <tr>
            <th width="20px">Sl</th>
            <th>Employee Name</th>
            <th>Code</th>
            <th>Date</th>
            <th>Day</th>
            <th>In Time</th>
            <th>Attend Status</th>
        </tr>
    </thead>

    <tbody>
        @if(!empty($date))
        @php $sl = 1; @endphp
        @if(!empty($attendences->attendenceList))
        @foreach($attendences->attendenceList as $key => $attendence)
        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ $attendence->staff->name }}</td>
            <td>{{ $attendence->staff->code }}</td>
            <td>{{ $attendence->date }}</td>
            <td>{{ date('l', strtotime($attendence->date)) }}</td>
            <td>{{ date('h:i A', strtotime($attendence->in_time)) }}</td>
            <td>
                @if($attendence->in_time <= "09:30:00")
                Attend
                @else
                Late
                @endif
            </td>
        </tr>
        @endforeach
        @endif
        @foreach($leaves as $leave)
        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ $leave->staff->name }}</td>
            <td>{{ $leave->staff->code }}</td>
            <td>{{ $date }}</td>
            <td>{{ date('l', strtotime($date)) }}</td>
            <td>Leave</td>
            <td>{{ $leave->leaveType->name }}</td>
        </tr>
        @endforeach
        @foreach($absents as $absent)
        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ $absent->name }}</td>
            <td>{{ $absent->code }}</td>
            <td>{{ $date }}</td>
            <td>{{ date('l', strtotime($date)) }}</td>
            <td>Absent</td>
            <td>Absent</td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>
@endsection

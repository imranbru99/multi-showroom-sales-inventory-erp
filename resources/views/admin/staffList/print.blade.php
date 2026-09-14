@extends('admin.layouts.masterPrint')

@section('content')
<table id="report-header">
    <tr>
        <td>{{ $title }}</td>
    </tr>
</table>

<div id="pad-bottom"></div>

<table id="report-table">
    <thead>
        <tr>
            <th width="20px">Sl</th>
            <th>Code</th>
            <th>Name</th>
            <th>Designation</th>
            <th>Contact</th>
            <th>Address</th>
        </tr>
    </thead>

    <tbody>
        @foreach($staffs as $staff)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $staff->code }}</td>
            <td>{{ $staff->name }}</td>
            <td>{{ $staff->designation }}</td>
            <td>{{ $staff->contact }}</td>
            <td>{{ $staff->address }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="row">
    <div class="col-md-12 text-right">
        <?php
        date_default_timezone_set("Asia/Dhaka");
        ?>
        <p>Print Date & Time : <?php echo date("d-m-Y h:i:sa"); ?></p>
    </div>
</div>
@endsection

@extends('admin.layouts.masterPrint')

@section('content')
    <table id="report-table">
    	<caption>Staff Report</caption>
        <thead>
            <tr>
                <th width="20px">SL#</th>
                <th>Code</th>
                <th>Designation</th>
                <th>Name</th>
                <th>Joining Date</th>
                <th>National Id</th>
                <th>Contact</th>
                <th>Email</th>
                <th>Address</th>
                <th>A/C No</th>
                <th>A/C Branch</th>
            </tr>
        </thead>
        <tbody id="">
            @php
                $sl = 1;
            @endphp
            @foreach ($staffs as $staff)
                <tr class="row_{{ $staff->id }}">
                    <td>{{ $sl++ }}</td>
                    <td>{{ $staff->code }}</td>
                    <td>{{ $staff->designation }}</td>
                    <td>{{ $staff->name }}</td>
                    <td> {{ date('d-m-Y',strtotime($staff->joining_date))}}</td>
                    <td>{{ $staff->national_id }}</td>
                    <td>{{ $staff->contact }}</td>
                    <td>{{ $staff->email }}</td>
                    <td>{{ $staff->address }}</td>
                    <td>{{ $staff->ac_no }}</td>
                    <td>{{ $staff->ac_branch }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
     <div class="row">
        <div class="col-md-12 text-right">
            <?php 
                date_default_timezone_set("Asia/Dhaka");
            ?>
            <p>Print Date & Time : <?php echo  date("d-m-Y h:i:sa");?></p>
        </div>
    </div>
@endsection

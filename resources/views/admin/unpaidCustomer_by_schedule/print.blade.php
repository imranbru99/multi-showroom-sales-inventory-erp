@extends('admin.layouts.masterPrint')

@section('content')
<table id="report-header">
    <tr>
        <td>
            Unpaid Customers From {{ date('d-m-Y', strtotime($fromDate)) }} To {{ date('d-m-Y', strtotime($toDate)) }}
            @if ($sales_by)
            Sale By {{ $staff->name }}
            @endif
        </td>
    </tr>
</table>

<div id="pad-bottom"></div>

<table id="report-table">
    <thead>
        <tr>
            <th>SL</th>
            <th>Customer Name</th>
            <th>Account No</th>
            <th>Contact No</th>
            <th>Sales By</th>
        </tr>
    </thead>
    <tbody>
        @foreach($customers as $customer)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $customer->name }}</td>
            <td>{{ $customer->code }}</td>
            <td>{{ $customer->phone_no }}</td>
            <td>{{ @$customer->agreement->staff->name }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" align="right"><b>Total Customers</b></td>
            <td  align="right"><b>{{ count($customers) }}</b></td>
        </tr>
    </tfoot>
</table>

<div class="row">
    <div class="col-md-12 text-right">
        <?php date_default_timezone_set('Asia/Dhaka'); ?>
        <p>Print Date & Time : <?php echo date('d-m-Y h:i:sa'); ?></p>
    </div>
</div>
@endsection

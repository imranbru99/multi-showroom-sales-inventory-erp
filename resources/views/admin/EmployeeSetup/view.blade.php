@extends('admin.layouts.masterView')

@section('custom_css')
	<style type="text/css">
		td{
			font-weight: bold;
		}
		.employee-image{
			width: 210px;
			height: 210px;
			border: 1px solid #198206;
			border-radius: 3px;
		}
	</style>
@endsection

@section('card_body')
    <div class="card-body">
    	<div class="table-responsive">
	    	<table class="table table-borderless table-striped">
	    		<thead class="thead-green">
	    			<tr>
	    				<th colspan="4" style="text-align: center;"><font size="5px">{{ $employee->name }}</font></th>
	    			</tr>
	    		</thead>

	    		<tbody>
	    			<tr>
	    				<td rowspan="{{ $employee->resigning_date == "" ? 5 : 7 }}" width="215px" align="center"><img src="{{ asset($employee->image) }}" class="employee-image"></td>
	    				<td width="140px">Employee No.</td>
	    				<td width="10px">:</td>
	    				<td>{{ $employee->employee_no }}</td>
	    			</tr>

	    			<tr>
	    				<td width="140px">Designation</td>
	    				<td width="10px">:</td>
	    				<td>{{ $employee->designationName }}</td>
	    			</tr>

	    			<tr>
	    				<td width="140px">Joining Date</td>
	    				<td width="10px">:</td>
	    				<td>{{ $employee->joining_date }}</td>
	    			</tr>

	    			@if ($employee->resigning_date)
		    			<tr>
		    				<td width="140px">Resigning Date</td>
		    				<td width="10px">:</td>
		    				<td>{{ $employee->resigning_date }}</td>
		    			</tr>

		    			<tr>
		    				<td width="140px">Resigning Reason</td>
		    				<td width="10px">:</td>
		    				<td>{{ $employee->resigning_reason }}</td>
		    			</tr>
	    			@endif

	    			<tr>
	    				<td width="140px">Mobile No.</td>
	    				<td width="10px">:</td>
	    				<td>{{ $employee->mobile }}</td>
	    			</tr>

	    			<tr>
	    				<td width="140px">Email</td>
	    				<td width="10px">:</td>
	    				<td>{{ $employee->email }}</td>
	    			</tr>
	    		</tbody>
	    	</table>
    	</div>
    </div>
@endsection
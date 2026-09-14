<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\EmployeeLeave;
use App\EmployeeSetup;
use App\LeaveSetup;

use DB;

class EmployeeLeaveController extends Controller
{
    public function index()
    {
    	$title = "Employee Leave Setup";

    	$employeeLeaves = EmployeeLeave::select('tbl_employee_leave.*','tbl_employee.name as employeeName','tbl_leaves.name as leaveName')
    		->leftJoin('tbl_employee','tbl_employee.id','=','tbl_employee_leave.employee_id')
    		->leftJoin('tbl_leaves','tbl_leaves.id','=','tbl_employee_leave.leave_id')
    		->where('tbl_employee_leave.showroom_id',$this->showroomId)
    		->orderBy('tbl_employee_leave.id','dsc')
    		->get();

    	return view('admin.employeeLeave.index')->with(compact('title','employeeLeaves'));
    }

    public function add()
    {
    	$title = "Add Employee Leave";
    	$formLink = "employeeLeave.save";
    	$buttonName = "Save";

    	$employies = EmployeeSetup::where('resigned','0')->orderBy('id','asc')->get();
    	$leaves = LeaveSetup::where('status','1')->orderBy('id','asc')->get();

    	return view('admin.employeeLeave.add')->with(compact('title','formLink','buttonName','employies','leaves'));
    }

    public function save(Request $request)
    {
    	// dd($request->all());
        if ($request->fromDate == "")
        {
            $fromDate = $request->fromDate;
        }
        else
        {
            $fromDate = date('Y-m-d', strtotime($request->fromDate));
        }

        if ($request->toDate == "")
        {
            $toDate = $request->toDate;
        }
        else
        {
            $toDate = date('Y-m-d', strtotime($request->toDate));
        }

        EmployeeLeave::create([
            'showroom_id' => $this->showroomId,
            'employee_id' => $request->employeeId,
            'leave_id' => $request->leaveId,
            'from_date' => $fromDate,
            'to_date' => $toDate,
        	'days' => $request->leaveDays,
        	'total_observe_leave_days' => $request->observedDays,
        	'total_remain_leave_days' => $request->remainingDays,
            'remarks' => $request->remarks,
            'created_by' => $this->userId
        ]);

        return redirect(route('employeeLeave.index'))->with('msg','Employee Leaves Added Successfully');
    }

    public function edit($employeeLeaveId)
    {
    	$title = "Edit Employee Leave";
    	$formLink = "employeeLeave.update";
    	$buttonName = "Update";

    	$employies = EmployeeSetup::where('resigned','0')->orderBy('id','asc')->get();
    	$leaves = LeaveSetup::where('status','1')->orderBy('id','asc')->get();

    	$employeeLeave = EmployeeLeave::select('tbl_employee_leave.*','tbl_employee.name as employeeName','tbl_employee.employee_no as employeeNo')
    		->leftJoin('tbl_employee','tbl_employee.id','=','tbl_employee_leave.employee_id')
    		->where('tbl_employee_leave.id',$employeeLeaveId)
    		->first();

        $previousLeaveDays = EmployeeLeave::select(DB::raw('SUM(days) as totalDays'))
        	->where('employee_id',$employeeLeave->employee_id)
        	->where('leave_id',$employeeLeave->leave_id)
        	->whereYear('to_date',date('Y'))
        	->where('showroom_id',$this->showroomId)
        	->first();

    	return view('admin.employeeLeave.edit')->with(compact('title','formLink','buttonName','employies','leaves','employeeLeave','previousLeaveDays'));
    }

    public function update(Request $request)
    {
    	// dd($request->all());
        if ($request->fromDate == "")
        {
            $fromDate = $request->fromDate;
        }
        else
        {
            $fromDate = date('Y-m-d', strtotime($request->fromDate));
        }

        if ($request->toDate == "")
        {
            $toDate = $request->toDate;
        }
        else
        {
            $toDate = date('Y-m-d', strtotime($request->toDate));
        }

        $employeeLeave = EmployeeLeave::find($request->employeeLeaveId);

        $employeeLeave->update([
            'showroom_id' => $this->showroomId,
            'employee_id' => $request->employeeId,
            'leave_id' => $request->leaveId,
            'from_date' => $fromDate,
            'to_date' => $toDate,
        	'days' => $request->leaveDays,
        	'total_observe_leave_days' => $request->observedDays,
        	'total_remain_leave_days' => $request->remainingDays,
            'remarks' => $request->remarks,
            'updated_by' => $this->userId
        ]);

        return redirect(route('employeeLeave.index'))->with('msg','Employee Leaves Updated Successfully');
    }

    public function getLeaveInfo(Request $request)
    {
        $leave = LeaveSetup::where('id',$request->leaveId)->where('showroom_id',$this->showroomId)->first();
        $employeeLeave = EmployeeLeave::select(DB::raw('SUM(days) as totalDays'))
        	->where('employee_id',$request->employeeId)
        	->where('leave_id',$request->leaveId)
        	->whereYear('to_date',$request->year)
        	->where('showroom_id',$this->showroomId)
        	->first();

        if($request->ajax()){
	        return response()->json([
                'employeeLeave'=>$employeeLeave,
                'leave'=>$leave
            ]);
        }
    }

    public function delete(Request $request)
    {    	
        EmployeeLeave::where('id',$request->employeeLeaveId)->delete();
    }

    public function status(Request $request)
    {
        $employeeLeave = EmployeeLeave::find($request->employeeLeaveId);

        if ($employeeLeave->status == 1)
        {
            $employeeLeave->update([               
                'status' => 0                
            ]);
        }
        else
        {
            $employeeLeave->update([               
                'status' => 1                
            ]);
        }
    }
}
